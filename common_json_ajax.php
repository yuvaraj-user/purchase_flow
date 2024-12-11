<?php
include '../auto_load.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

require 'Send_Mail.php';

$mail = new Send_Mail();

// // Get the JSON data
$json = file_get_contents('php://input');
$_POST = json_decode($json, true);

function auto_increment_request_id($conn)
{
	$previous_request_id_sql = "SELECT TOP 1 Request_ID from Tb_Request order by Id desc";
	$previous_request_id_sql_exec = sqlsrv_query($conn, $previous_request_id_sql);
	$previous_request_id = sqlsrv_fetch_array($previous_request_id_sql_exec)['Request_ID'];
	$previous_request_id_number = filter_var($previous_request_id, FILTER_SANITIZE_NUMBER_INT);

	$total_request_id_no_count = strlen($previous_request_id_number);
	$number_request_id = ltrim($previous_request_id_number,'0');

	$leading_zero_count = $total_request_id_no_count - strlen($number_request_id);
	$number_request_id++;

	$incremented_request_id = $number_request_id;
	if($leading_zero_count > 0) {
		$leading_zeros = '';
		for ($i=0; $i < $leading_zero_count; $i++) { 
			$leading_zeros = $leading_zeros.'0';
		}

		$incremented_request_id = "PV".$leading_zeros.$number_request_id;
	}
	return $incremented_request_id; 
}


function filter_request($request)
{
	$data = array_map(function($value) { 
         if(is_array($value)){
                 $mArr = array_map(function($value1) { 
                     return str_replace("'","''", $value1); 
                 },$value);
                 return $mArr;
         }else{
             return str_replace("'","''", $value); 
         }
  	}, $request);

  	return $data;	
}

if(isset($_POST['Action'])) {
	if ($_POST['Action'] == 'save_purchase_request_div') {
		if(isset($_SESSION['EmpID']) && $_SESSION['EmpID'] != '') {

			$_POST = filter_request($_POST);
		
			$response['status']  = 422;
			$response['message'] = "Something Went Wrong.";

	 		$sql = "SELECT TOP 1 * FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."' and Purchase_Type = 'Purchase & Vendor selection (R&VS)' AND Plant = '".$_POST['plant_id']."' AND Material_Group = '".$_POST['mat_group']."'";

		    $sql_exec = sqlsrv_query($conn, $sql);

		    $purchaser_code = sqlsrv_fetch_array($sql_exec,SQLSRV_FETCH_ASSOC)['Purchaser'];

		 	$emp_id = $_SESSION['EmpID'];
		    $Requested_to = $purchaser_code;
		    // $request_id = $_POST['request_id'];
		    $request_id = auto_increment_request_id($conn);
		    $request_type = $_POST['request_type'];
		    $plant_id = $_POST['plant_id'];
		    $storage_location = $_POST['storage_location'];
		    $request_type1 = $_POST['request_type1'];
		    $Department = $_POST['department'];
		    // $Finance_Verification = $_POST['Finance_Verification'];
	    	$Reference = $_POST['reference'];
	    	$CreatedAt = date('Y-m-d h:i:s A');
		    $status = 'Requested';

		   	$Persion= '';
			if(isset($_POST['persionp']) && (COUNT($_POST['persionp']) > 0)){
		        $Persion= (COUNT($_POST['persionp']) > 1) ? implode(',',$_POST['persionp']) : $_POST['persionp'];
		    }

		    // additional cc add
		    if($emp_id == 'RS3552' || $emp_id == 'RS4308' || $Requested_to == 'RS3552' || $Requested_to == 'RS4308') {
		    	$Persion = ($Persion != '') ? $Persion.','.'RS6064' : 'RS6064';			    	
		    }

		   	// sales & marketing department only save season,activity,year
		    $season    = isset($_POST['season']) ? $_POST['season'] : '';
		    $activity  = isset($_POST['activity']) ? $_POST['activity'] : '';
		    $crop_year = isset($_POST['crop_year']) ? $_POST['crop_year'] : '';

 		    $query = "INSERT INTO Tb_Request (Request_ID, Request_Type, Plant, Storage_Location,Time_Log,Request_Category,Reference,
		     status, Department,Persion_In_Workflow,EMP_ID,Requested_to,CreatedAt,Season,Activity,Crop_Year) VALUES 
			  ('$request_id','$request_type','$plant_id','$storage_location',GETDATE(),'$request_type1','$Reference','$status','$Department','$Persion','$emp_id','$Requested_to','$CreatedAt','$season','$activity','$crop_year')";
		    $rs = sqlsrv_query($conn, $query);

		    $file_index = 1;
		    for ($i = 0; $i < count($_POST['item_code']); $i++) {
		    	if(count($_POST['item_code']) > 1) {
			        $item_code = $_POST['item_code'][$i];
			        $uom = $_POST['uom'][$i];
			        $description = $_POST['description'][$i];
			        $quantity = $_POST['quantity'][$i];
			        $Expected_Date = $_POST['Expected_Date'][$i];
			        $Specification = $_POST['Specification'][$i];
			        $budgest = $_POST['budget'][$i];
			        $budget_remark = $_POST['budget_remark'][$i];
			        $MaterialGroup = $_POST['MaterialGroup'][$i];

			        //Divya
			        $replace = isset($_POST['replace']) ? $_POST['replace'][$i] : '';
			        $rdateofpurchase = (isset($_POST['replace']) && $_POST['rdateofpurchase'][$i] !='') ? $_POST['rdateofpurchase'][$i] : '';
			        $rqty = (isset($_POST['replace']) && $_POST['rqty'][$i] !='') ? $_POST['rqty'][$i] : 0;
			        $rremarks = (isset($_POST['replace']) && $_POST['rremarks'][$i] !='') ? $_POST['rremarks'][$i] : '';
			        $rcost = (isset($_POST['replace']) && $_POST['rcost'][$i] !='') ? $_POST['rcost'][$i] : 0;
		    	} else {
		  			$item_code = $_POST['item_code'];
			        $uom = $_POST['uom'];
			        $description = $_POST['description'];
			        $quantity = $_POST['quantity'];
			        $Expected_Date = $_POST['Expected_Date'];
			        $Specification = $_POST['Specification'];
			        $budgest = $_POST['budget'];
			        $budget_remark = $_POST['budget_remark'];
			        $MaterialGroup = $_POST['MaterialGroup'];

			        //Divya
			        $replace = isset($_POST['replace']) ? $_POST['replace'] : '';
			        $rdateofpurchase = (isset($_POST['replace']) && $_POST['rdateofpurchase'] !='') ? $_POST['rdateofpurchase'] : '';
			        $rqty = (isset($_POST['replace']) && $_POST['rqty'] !='') ? $_POST['rqty'] : 0;
			        $rremarks = (isset($_POST['replace']) && $_POST['rremarks'] !='') ? $_POST['rremarks'] : '';
			        $rcost = (isset($_POST['replace']) && $_POST['rcost'] !='') ? $_POST['rcost'] : 0;
		    	}


				$size = (int) (strlen(rtrim($_POST['Attachment'][$i], '=')) * 3 / 4);
				$size_in_kb    = $size / 1024;
        		$size_in_mb    = $size_in_kb / 1024;

				$newFilePath = '';
				$newfilename = '';
		      	// Decode the Base64 file
            	if (preg_match('/^data:(.*);base64,(.*)$/', $_POST['Attachment'][$i], $matches)) {
	                $mimeType = $matches[1];
	                $base64Data = $matches[2];
	                if($base64Data != '') {
		                $decodedData = base64_decode($base64Data);
					    $createdons      = date("dmYHisA");

		                // Generate a unique filename or use the original file name
		                $temp = explode("/", $matches[1]); // Get extension from mime type if needed
		                $extension = end($temp); // Assuming you get the extension from the mime type or original filename
		                $newfilename = 'PR_'.$createdons . '.' . $extension; // Unique filename
		                $newFilePath = "file/" . $newfilename;
	                }
	        	}

		        $fil = $newfilename;


		        $sql = "INSERT INTO Tb_Request_Items (Request_ID, Item_Code, UOM, Description, Quantity, Budget, Time_Log,Reference, status, Expected_Date, Specification, Attachment, Budject_Remark,
		         MaterialGroup, EMP_ID,Requested_to,Replace_Type,Date_of_Purchase,Replace_Qty,Replace_Remarks,Replace_Cost) VALUES 
				('$request_id','$item_code','$uom','$description','$quantity','$budgest',GETDATE(),'$Reference','$status','$Expected_Date','$Specification','$fil','$budget_remark',
		        '$MaterialGroup','$emp_id','$Requested_to','$replace','".$rdateofpurchase."','$rqty','$rremarks','$rcost')"; 
		        // exit;

		        $params = array("updated data", 1);

		        $stmt = sqlsrv_query($conn, $sql, $params);

		        $rows_affected = sqlsrv_rows_affected($stmt);
		        if ($rows_affected === false) {
		            die(print_r(sqlsrv_errors(), true));
		        } elseif ($rows_affected == -1) {
		            echo "No information available.<br />";
		        }else{
		            // Save the file
		            file_put_contents($newFilePath, $decodedData);
		    	}

		        $file_index++;

		    }


		   	//informer mail id get for cc mail sending 
	        $implode='';
			if($Persion != ''){
		        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$Persion',','))");

		        $idss = array();
		        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
		            $idss[] = $ids['Office_Email_Address'];
		        }
		        $implode = implode(',', $idss);
		    }

		    // purchase mail id get for mail sending 
	        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Requested_to'  ");
	        $HR = sqlsrv_fetch_array($HR_Master_Table1);
	        $To = $HR['Office_Email_Address'];

		    
		    // $to = array('jr_developer4@mazenetsolution.com');
		    $to = explode(',', $To);

		    // informer mail cc
		    $cc = ($implode != '') ? explode(',', $implode) : array();

		    $bcc = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

        	$message_template = "<table width='100%' border='1' style='margin-top:15px;' align='left class='table table-striped'>". 
		                          "<thead><tr>".
		                          "<th>SN</th>".
		                          "<th nowrap='nowrap'>Request ID</th>".
		                          "<th nowrap='nowrap'>Department</th>".
		                          "<th nowrap='nowrap'>Category</th>".
		                          "<th nowrap='nowrap'>Plant</th>".
		                          "<th nowrap='nowrap'>Meterial</th>".
		                          "<th nowrap='nowrap'>Quantity</th>".
		                          "</tr></thead><tbody>";
              $qq = sqlsrv_query($conn,"SELECT Tb_Request.Request_ID,Tb_Request.Department,Tb_Request.Request_Type,Tb_Request.Plant,
              Tb_Request_Items.Item_Code,Tb_Request_Items.Quantity,Tb_Request_Items.Description as item_name FROM Tb_Request  INNER JOIN Tb_Request_Items 
              ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID  WHERE Tb_Request.Request_ID = '$request_id'");
              $d =0;
              while($c = sqlsrv_fetch_array($qq)){ $d++;
		      $message_template .= "<tr>".
                  "<td nowrap='nowrap'>".$d."</td>".
                  "<td nowrap='nowrap'> ".$c['Request_ID']."</td>".
                  "<td nowrap='nowrap'> ".$c['Department']."</td>".
                  "<td nowrap='nowrap'> ".$c['Request_Type']."</td>".
                  "<td nowrap='nowrap'> ".$c['Plant']."</td>".
                  "<td nowrap='nowrap'> ".$c['item_name']."(".$c['Item_Code'].")</td>".
                  "<td nowrap='nowrap'> ".$c['Quantity']."</td></tr>";
		       }
		    $message_template .= "</tbody></table>";
		    $subject = $emp_id.' - Purchase Request';

			$process_mail = $mail->Send_Mail_Details($subject,'','',$message_template,$to,$cc,$bcc);		    
 
		    if (!$process_mail) {
		   		$response['status']  = 500;
				$response['message'] = "Mail could not be sent.";
		    } else{
		   		$response['status']  = 200;
				$response['message'] = "Request added successsfully.";
		    }
		} else {
			$response['status'] = 419;
			$response['message'] = 'Your Login Session closed.';
		}

	    echo json_encode($response);exit;
	}

	elseif ($_POST['Action'] == 'update_purchase_request_div') {

		$_POST = filter_request($_POST);

		$response['status']  = 422;
		$response['message'] = "Something Went Wrong.";


		$sql = "SELECT TOP 1 * FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."' and Purchase_Type = 'Purchase & Vendor selection (R&VS)' AND Plant = '".$_POST['plant_id']."' AND Material_Group = '".$_POST['mat_group']."'";

	    $sql_exec = sqlsrv_query($conn, $sql);

	    $purchaser_code = sqlsrv_fetch_array($sql_exec,SQLSRV_FETCH_ASSOC)['Purchaser'];

	    $emp_id = $_SESSION['EmpID'];
	    $Requested_to = $purchaser_code;
	    $request_id = $_POST['request_id'];
	    $request_type = $_POST['request_type'];
	    $plant_id = $_POST['plant_id'];
	    $storage_location = $_POST['storage_location'];
	    $request_type1 = $_POST['request_type1'];
	    $Department = $_POST['department'];
	    $Finance_Verification = $_POST['Finance_Verification'];
	    $req_id = $_POST['request_id'];
    	$Reference = $_POST['reference'];
	    $UpdatedAt = date('Y-m-d h:i:s A');

	   	// sales & marketing department only save season,activity,year
	    $season    = isset($_POST['season']) ? $_POST['season'] : '';
	    $activity  = isset($_POST['activity']) ? $_POST['activity'] : '';
	    $crop_year = isset($_POST['crop_year']) ? $_POST['crop_year'] : '';

	    if (isset($_POST['persion']))
	    {
	        $Persion= implode(',',$_POST['persion']);
	        $status = 'Requested';
	        foreach ($_POST['item_code'] as $key => $value) {
	          if($value == 'Yes') {
	            $status = 'Review';
	            $Requested_to = $verifier_code;
	          }
	        }

	        $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set Request_Type = '$request_type',Plant ='$plant_id',Storage_Location ='$storage_location',
	        Request_Category ='$request_type1',status ='$status',Department ='$Department',Finance_Verification = '$Finance_Verification',Persion_In_Workflow ='$Persion',Reference = '$Reference',Requested_to = '$Requested_to',UpdatedAt = '$UpdatedAt',Season='$season',Activity='$activity',Crop_Year='$crop_year' WHERE Request_ID = '$req_id'");

		    sqlsrv_query($conn, $query1);


		    sqlsrv_query($conn, "DELETE from Tb_Request_Items WHERE Request_ID = '".$request_id."'");
		    
		    for ($i = 0; $i < count($_POST['item_code']); $i++) {

		    	if(count($_POST['item_code']) > 1) {
			        $item_code = $_POST['item_code'][$i];
			        $uom = $_POST['uom'][$i];
			        $description = $_POST['description'][$i];
			        $quantity = $_POST['quantity'][$i];
			        $Expected_Date = $_POST['Expected_Date'][$i];
			        $Specification = $_POST['Specification'][$i];
			        $budgest = $_POST['budget'][$i];
			        $ID = $_POST['id'][$i];
			        $MaterialGroup = $_POST['MaterialGroup'][$i];

			        $replace = $_POST['replace'][$i];
			        $rdateofpurchase = ($_POST['rdateofpurchase'][$i])!='' ? $_POST['rdateofpurchase'][$i] : '';
			        $rqty = ($_POST['rqty'][$i])!='' ? $_POST['rqty'][$i] : 0;
			        $rremarks = ($_POST['rremarks'][$i])!='' ? $_POST['rremarks'][$i] : '';
			        $rcost = ($_POST['rcost'][$i])!='' ? $_POST['rcost'][$i] : 0;
			     } else {
			        $item_code = $_POST['item_code'];
			        $uom = $_POST['uom'];
			        $description = $_POST['description'];
			        $quantity = $_POST['quantity'];
			        $Expected_Date = $_POST['Expected_Date'];
			        $Specification = $_POST['Specification'];
			        $budgest = $_POST['budget'];
			        $ID = $_POST['id'];
			        $MaterialGroup = $_POST['MaterialGroup'];

			        $replace = $_POST['replace'];
			        $rdateofpurchase = ($_POST['rdateofpurchase'])!='' ? $_POST['rdateofpurchase'] : '';
			        $rqty = ($_POST['rqty'])!='' ? $_POST['rqty'] : 0;
			        $rremarks = ($_POST['rremarks'])!='' ? $_POST['rremarks'] : '';
			        $rcost = ($_POST['rcost'])!='' ? $_POST['rcost'] : 0;
			     }

		        //file size
		        $size = (int) (strlen(rtrim($_POST['Attachment'][$i], '=')) * 3 / 4);
				$size_in_kb    = $size / 1024;
        		$size_in_mb    = $size_in_kb / 1024;

				$newFilePath = '';
				$newfilename = '';

		      	// Decode the Base64 file
            	if (preg_match('/^data:(.*);base64,(.*)$/', $_POST['Attachment'][$i], $matches)) {
	                $mimeType = $matches[1];
	                $base64Data = $matches[2];
	                if($base64Data != '') {
		                $decodedData = base64_decode($base64Data);
					    $createdons      = date("dmYHisA");

		                // Generate a unique filename or use the original file name
		                $temp = explode("/", $matches[1]); // Get extension from mime type if needed
		                $extension = end($temp); // Assuming you get the extension from the mime type or original filename
		                $newfilename = 'PR_'.$createdons . '.' . $extension; // Unique filename
		                $newFilePath = "file/" . $newfilename;
	                }
	        	}

		        // $fil = $_FILES["Attachment"]["name"][$i];
		        $fil = ($newfilename != '') ? $newfilename : ((isset($_POST['Saved_Attachment'][$i]) && $_POST['Saved_Attachment'][$i] != '') ? $_POST['Saved_Attachment'][$i] : '');

		        // $fil = ($_FILES["Attachment"]["name"][$i] != '') ? $_FILES["Attachment"]["name"][$i] : ((isset($_POST['Saved_Attachment'][$i]) && $_POST['Saved_Attachment'][$i] != '') ? $_POST['Saved_Attachment'][$i] : '') ;


		        

		        // $qry = sqlsrv_query($conn, "SELECT * from Tb_Request_Items ORDER BY ID desc") or die('error');
		        // $nextID = sqlsrv_fetch_array($qry)['ID'] + 1;
		        // $_paymentID =  $nextID;



		        $check_item_exist = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '".$request_id."' and Item_Code = '".$item_code."'",array(),array('Scrollable' => 'static'));
		        $check_item_exist_count = sqlsrv_num_rows($check_item_exist);


		        if($check_item_exist_count == 0){
		            $sql = "INSERT INTO Tb_Request_Items (Request_ID, Item_Code, UOM, Description, Quantity, Budget, Time_Log, status, Expected_Date, Specification, Attachment,
		            MaterialGroup, EMP_ID,Requested_to,Reference,Replace_Type,Date_of_Purchase,Replace_Qty,Replace_Remarks,Replace_Cost) VALUES 
		            ('$request_id','$item_code','$uom','$description','$quantity','$budgest',GETDATE(),'$status','$Expected_Date','$Specification','$fil',
		            '$MaterialGroup','$emp_id','$Requested_to','$Reference','$replace','$rdateofpurchase','$rqty','$rremarks','$rcost')";
		        }else{
		            $sql = "UPDATE Tb_Request_Items SET Request_ID = '$request_id',Item_Code = '$item_code',UOM = '$uom',Description = '$description',Quantity = '$quantity',
		            Budget = '$budgest',Time_Log = GETDATE(),status = '$status',Expected_Date = '$Expected_Date',Specification = '$Specification',Attachment = '$fil',
		            MaterialGroup ='$MaterialGroup',EMP_ID = '$emp_id',Requested_to ='$Requested_to',Reference = '$Reference',Replace_Type='$replace', Date_of_Purchase= '$rdateofpurchase', Replace_Qty='$rqty',Replace_Remarks='$rremarks',Replace_Cost='$rcost'
		            WHERE  Request_ID = '$req_id' AND ID = '$ID'";
		        }

		        $params = array("updated data", 1);

		        $stmt = sqlsrv_query($conn, $sql, $params);

		        $rows_affected = sqlsrv_rows_affected($stmt);
		        if ($rows_affected === false) {
		            // die(print_r(sqlsrv_errors(), true));
		           	$response['status']  = 500;
					$response['message'] = "Query execution error.";
		        } elseif ($rows_affected == -1) {
		            // echo "No information available.<br />";
		          	$response['status']  = 200;
					$response['message'] = "No data to update.";
		        } else {
		        	if($fil != '') {
		        		// Save the file
		            	file_put_contents($newFilePath, $decodedData);
		        	}
		           	
		           	$response['status']  = 200;
					$response['message'] = "Purchase request updated successsfully.";
		        }

			}
		}
        echo json_encode($response);exit;
    }

}



?>