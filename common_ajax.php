<?php
include '../auto_load.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

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

if(isset($_POST['Action'])) {
	if($_POST['Action'] == 'get_plant_code') {
		$plant_sql = "SELECT DISTINCT Plant,Plant_Master_PO.Plant_Name  from Tb_Master_Emp
		left join Plant_Master_PO on Plant_Master_PO.Plant_Code = Tb_Master_Emp.Plant
		 where Document_type = '".$_POST['request_type']."' AND PO_creator_Release_Codes = '".$_SESSION['EmpID']."'";
		$plant_sql_exec = sqlsrv_query($conn,$plant_sql);
		$result = array();
		while ($row = sqlsrv_fetch_array($plant_sql_exec,SQLSRV_FETCH_ASSOC)) {
			$result[] = $row; 
		}

		echo json_encode($result);exit;
	} elseif($_POST['Action'] == 'get_emp_mapping_details') {
		$mapping_sql = "SELECT Tb_Master_Emp.*,approver_details.Designation as approver_Department,approver_details.Employee_Name as approver_name,approver2_details.Designation as approver2_Department,approver2_details.Employee_Name as approver2_name from Tb_Master_Emp 
		inner join Tb_Request on Tb_Request.Plant = Tb_Master_Emp.Plant AND Tb_Request.Request_ID = '".$_POST['request_id']."' AND Tb_Master_Emp.Status ='Active' 
		inner join (SELECT TOP 1 * FROM Tb_Request_Items WHERE Request_ID = '".$_POST['request_id']."') as Tb_Request_Items on Tb_Request_Items.MaterialGroup =  Tb_Master_Emp.Material_Group
		left join HR_Master_Table as approver_details on approver_details.Employee_Code = Tb_Master_Emp.Approver
		left join HR_Master_Table as approver2_details on approver2_details.Employee_Code = Tb_Master_Emp.Approver_2
		where Tb_Master_Emp.PO_creator_Release_Codes = '".$_POST['po_creator_id']."'"; 

		$mapping_sql_exec = sqlsrv_query($conn,$mapping_sql);
		$result = array();
		while ($row = sqlsrv_fetch_array($mapping_sql_exec,SQLSRV_FETCH_ASSOC)) {
			$result[] = $row; 
		}

		$mapping_arr = array();
		$lowest_val_arr = array();
		if(COUNT($result) > 0) {
			foreach ($result as $key => $value) {
				$status = ($value['Value'] != '') ? 1 : 0;
				if($status == 1) {
					if($value['Value'] < $_POST['quotation_value']) {
						array_push($mapping_arr, $value);
						break;
					} 
				} elseif ($status == 0) {
					array_push($lowest_val_arr, $value);
				}


				if(COUNT($result) == $key+1 && (COUNT($mapping_arr) == 0)) {
					array_push($mapping_arr, $lowest_val_arr[0]);
					break;
				}						
			}

		}

		// echo "<pre>";print_r($mapping_arr);exit; 

		echo json_encode($mapping_arr);exit;
	} elseif ($_POST['Action'] == 'get_material_group') {
		$material_group_sql  = "SELECT MaterialMaster.MaterialGroup FROM MaterialMaster 
		INNER JOIN (SELECT DISTINCT Material_Group,PO_creator_Release_Codes,Document_type FROM Tb_Master_Emp) as Tb_Master_Emp ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group 
		AND Tb_Master_Emp.PO_creator_Release_Codes = '".$_SESSION['EmpID']."' 
		WHERE MaterialMaster.StorageLocation = '".$_POST['Storage_Location']."' AND MaterialMaster.Plant = '".$_POST['Plant']."'  
		AND Tb_Master_Emp.Document_type = '".$_POST['request_type']."'
		GROUP BY MaterialMaster.MaterialGroup"; 

		if($_POST['request_type'] == 'ZSER') {
			$material_group_sql  = "SELECT DISTINCT Material_Group as MaterialGroup from Tb_Master_Emp where Document_type = '".$_POST['request_type']."' and Plant = '".$_POST['Plant']."' and PO_creator_Release_Codes = '".$_SESSION['EmpID']."'";
		}

		$material_group_exec = sqlsrv_query($conn,$material_group_sql);

		$result = array();
		while ($row = sqlsrv_fetch_array($material_group_exec,SQLSRV_FETCH_ASSOC)) {
			$result[] = $row; 
		}

		echo json_encode($result);exit;

	} elseif ($_POST['Action'] == 'save_purchase_request') {
		if(isset($_SESSION['EmpID']) && $_SESSION['EmpID'] != '') {
			$_POST = array_map(function($value) { 
		         if(is_array($value)){
		                 $mArr = array_map(function($value1) { 
		                     return str_replace("'","''", $value1); 
		                 },$value);
		                 return $mArr;
		         }else{
		             return str_replace("'","''", $value); 
		         }
		  	}, $_POST);

			// echo "<pre>";print_r($_POST);exit;
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

		    if(!isset($_POST['persionp'])){
		        $Subject123="0";
		        }else{
		          $Subject123=$_POST['persionp'];
		        }
		    if($Subject123>0){ 
		    
		        $Persion= implode(',',$_POST['persionp']);
		        $status = 'Requested';

		        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$Persion',','))  ");
		        $idss = array();
		        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
		            $idss[] = $ids['Office_Email_Address'];
		        }
		        $implode = implode(',', $idss);
		        // print_r($implode);exit;
		        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
		        $HR = sqlsrv_fetch_array($HR_Master_Table1);
		        $To = $HR['Office_Email_Address'];
		    }else{

		        $Persion= '';
		        $implode='';
		        $status = 'Requested';
		        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
		        $HR = sqlsrv_fetch_array($HR_Master_Table1);
		        $To = $HR['Office_Email_Address'];
		    }

		        
		        
		    $query = "INSERT INTO Tb_Request (Request_ID, Request_Type, Plant, Storage_Location,Time_Log,Request_Category,Reference,
		     status, Department,Persion_In_Workflow,EMP_ID,Requested_to) VALUES 
			  ('$request_id','$request_type','$plant_id','$storage_location',GETDATE(),'$request_type1','$Reference','$status','$Department','$Persion','$emp_id','$Requested_to')";
			  // exit;
		    $rs = sqlsrv_query($conn, $query);

		    for ($i = 0; $i < count($_POST['item_code']); $i++) {

		        $item_code = $_POST['item_code'][$i];
		        $uom = $_POST['uom'][$i];
		        $description = $_POST['description'][$i];
		        $quantity = $_POST['quantity'][$i];
		        $Expected_Date = $_POST['Expected_Date'][$i];
		        $Specification = $_POST['Specification'][$i];
		        $fil = $_FILES["Attachment"]["name"][$i];
		        $budgest = $_POST['budget'][$i];
		        $budget_remark = $_POST['budget_remark'][$i];
		        $MaterialGroup = $_POST['MaterialGroup'][$i];


		        $sql = "INSERT INTO Tb_Request_Items (Request_ID, Item_Code, UOM, Description, Quantity, Budget, Time_Log,Reference, status, Expected_Date, Specification, Attachment, Budject_Remark,
		         MaterialGroup, EMP_ID,Requested_to) VALUES 
				('$request_id','$item_code','$uom','$description','$quantity','$budgest',GETDATE(),'$Reference','$status','$Expected_Date','$Specification','$fil','$budget_remark',
		        '$MaterialGroup','$emp_id','$Requested_to')"; 
		        // exit;

		        $params = array("updated data", 1);

		        $stmt = sqlsrv_query($conn, $sql, $params);

		        $rows_affected = sqlsrv_rows_affected($stmt);
		        if ($rows_affected === false) {
		            die(print_r(sqlsrv_errors(), true));
		        } elseif ($rows_affected == -1) {
		            echo "No information available.<br />";
		        }else{
		            move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);

		        }

		    }
		    $mail = new PHPMailer;

		    //$mail->SMTPDebug = 3;                               // Enable verbose debug output
		    
		    $mail->isSMTP();                                      // Set mailer to use SMTP
		    
		    $mail->Host = "rasiseeds-com.mail.protection.outlook.com";
		    $mail->SMTPAuth = false;
		    $mail->Port = 25;
		    $mail->From = "desk@rasiseeds.com";
		    $mail->FromName = "desk@rasiseeds.com";
		    //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
		    // $mail->addAddress($To_Address);               // Name is optional
		    
		    // Add cc or bcc 
		    
		    $to = explode(',', $To);
		    $to = array('jr_developer4@mazenetsolution.com');
		    
		    foreach ($to as $address) {
		        // $mail->AddAddress($address);
		        $mail->AddAddress(trim($address));
		    }

		    
		    $mail->addAttachment($fil);         // Add attachments
		    // $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
		    $mail->isHTML(true);                                  // Set email format to HTML
		    
		    $mail->Subject = $request_type1;
		    $mail->Body    =   "<table width='100%' border='1' style='margin-top:15px;' align='left class='table table-striped'>". 
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
		                          Tb_Request_Items.Item_Code,Tb_Request_Items.Quantity FROM Tb_Request  INNER JOIN Tb_Request_Items 
		                          ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID  WHERE Tb_Request.Request_ID = '$request_id'");
		                          $d =0;
		                          while($c = sqlsrv_fetch_array($qq)){ $d++;
		      $mail->Body.=       "<tr>".
		                          "<td nowrap='nowrap'>".$d."</td>".
		                          "<td nowrap='nowrap'> ".$c['Request_ID']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Department']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Request_Type']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Plant']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Item_Code']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Quantity']."</td></tr>";
		                          }
		    $mail->Body.=       "</tbody></table>";
		    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
		    if (!$mail->send()) {
		   		$response['status']  = 500;
				$response['message'] = "Mail could not be sent.";
		    }else{
		   		$response['status']  = 200;
				$response['message'] = "Request added successsfully.";
		    }
		}else {
			$response['status'] = 419;
			$response['message'] = 'Your Login Session closed.';
		}

	    echo json_encode($response);exit;
	} 
	elseif ($_POST['Action'] == 'save_purchase_request_div') {
		if(isset($_SESSION['EmpID']) && $_SESSION['EmpID'] != '') {
			$_POST = array_map(function($value) { 
		         if(is_array($value)){
		                 $mArr = array_map(function($value1) { 
		                     return str_replace("'","''", $value1); 
		                 },$value);
		                 return $mArr;
		         }else{
		             return str_replace("'","''", $value); 
		         }
		  	}, $_POST);

			// echo "<pre>";print_r($_POST);exit;
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

		    if(!isset($_POST['persionp'])){
		        $Subject123="0";
		        }else{
		          $Subject123=$_POST['persionp'];
		        }
		    if($Subject123>0){ 
		    
		        $Persion= implode(',',$_POST['persionp']);
		        $status = 'Requested';

		        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$Persion',','))  ");
		        $idss = array();
		        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
		            $idss[] = $ids['Office_Email_Address'];
		        }
		        $implode = implode(',', $idss);
		        // print_r($implode);exit;
		        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
		        $HR = sqlsrv_fetch_array($HR_Master_Table1);
		        $To = $HR['Office_Email_Address'];
		    }else{

		        $Persion= '';
		        $implode='';
		        $status = 'Requested';
		        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
		        $HR = sqlsrv_fetch_array($HR_Master_Table1);
		        $To = $HR['Office_Email_Address'];
		    }

		        
		        
		    $query = "INSERT INTO Tb_Request (Request_ID, Request_Type, Plant, Storage_Location,Time_Log,Request_Category,Reference,
		     status, Department,Persion_In_Workflow,EMP_ID,Requested_to) VALUES 
			  ('$request_id','$request_type','$plant_id','$storage_location',GETDATE(),'$request_type1','$Reference','$status','$Department','$Persion','$emp_id','$Requested_to')";
			  // exit;
		    $rs = sqlsrv_query($conn, $query);

		    for ($i = 0; $i < count($_POST['item_code']); $i++) {

		        $item_code = $_POST['item_code'][$i];
		        $uom = $_POST['uom'][$i];
		        $description = $_POST['description'][$i];
		        $quantity = $_POST['quantity'][$i];
		        $Expected_Date = $_POST['Expected_Date'][$i];
		        $Specification = $_POST['Specification'][$i];
		        $fil = $_FILES["Attachment"]["name"][$i];
		        $budgest = $_POST['budget'][$i];
		        $budget_remark = $_POST['budget_remark'][$i];
		        $MaterialGroup = $_POST['MaterialGroup'][$i];

		        //Divya
		        $replace = isset($_POST['replace']) ? $_POST['replace'][$i] : '';
		        $rdateofpurchase = (isset($_POST['replace']) && $_POST['rdateofpurchase'][$i] !='') ? $_POST['rdateofpurchase'][$i] : '';
		        $rqty = (isset($_POST['replace']) && $_POST['rqty'][$i] !='') ? $_POST['rqty'][$i] : 0;
		        $rremarks = (isset($_POST['replace']) && $_POST['rremarks'][$i] !='') ? $_POST['rremarks'][$i] : '';
		        $rcost = (isset($_POST['replace']) && $_POST['rcost'][$i] !='') ? $_POST['rcost'][$i] : 0;



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
		            move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);

		        }

		    }
		    $mail = new PHPMailer;

		    //$mail->SMTPDebug = 3;                               // Enable verbose debug output
		    
		    $mail->isSMTP();                                      // Set mailer to use SMTP
		    
		    $mail->Host = "rasiseeds-com.mail.protection.outlook.com";
		    $mail->SMTPAuth = false;
		    $mail->Port = 25;
		    $mail->From = "desk@rasiseeds.com";
		    $mail->FromName = "desk@rasiseeds.com";
		    //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
		    // $mail->addAddress($To_Address);               // Name is optional
		    
		    // Add cc or bcc 
		    
		    $to = explode(',', $To);
		    $to = array('jr_developer4@mazenetsolution.com');
		    
		    foreach ($to as $address) {
		        // $mail->AddAddress($address);
		        $mail->AddAddress(trim($address));
		    }

		    
		    $mail->addAttachment($fil);         // Add attachments
		    // $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
		    $mail->isHTML(true);                                  // Set email format to HTML
		    
		    $mail->Subject = $request_type1;
		    $mail->Body    =   "<table width='100%' border='1' style='margin-top:15px;' align='left class='table table-striped'>". 
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
		                          Tb_Request_Items.Item_Code,Tb_Request_Items.Quantity FROM Tb_Request  INNER JOIN Tb_Request_Items 
		                          ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID  WHERE Tb_Request.Request_ID = '$request_id'");
		                          $d =0;
		                          while($c = sqlsrv_fetch_array($qq)){ $d++;
		      $mail->Body.=       "<tr>".
		                          "<td nowrap='nowrap'>".$d."</td>".
		                          "<td nowrap='nowrap'> ".$c['Request_ID']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Department']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Request_Type']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Plant']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Item_Code']."</td>".
		                          "<td nowrap='nowrap'> ".$c['Quantity']."</td></tr>";
		                          }
		    $mail->Body.=       "</tbody></table>";
		    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
		    if (!$mail->send()) {
		   		$response['status']  = 500;
				$response['message'] = "Mail could not be sent.";
		    }else{
		   		$response['status']  = 200;
				$response['message'] = "Request added successsfully.";
		    }
		}else {
			$response['status'] = 419;
			$response['message'] = 'Your Login Session closed.';
		}

	    echo json_encode($response);exit;
	}
	elseif ($_POST['Action'] == 'get_involved_persons') {
		$po_creator_code = (isset($_POST['po_creator_id']) && $_POST['po_creator_id'] != '') ? $_POST['po_creator_id'] : $_SESSION['EmpID'];

		$amount =  (isset($_POST['value']) && $_POST['value'] != '') ? $_POST['value'] : '';

 		$sql = " SELECT TOP 1 purchaser_table.Employee_Name as purchaser_name,recommendor_table.Employee_Name as recommendor_name,
		 verifier_table.Employee_Name as verifier_name,approver_table.Employee_Name as approver_name
		 FROM Tb_Master_Emp 
		 LEFT JOIN HR_Master_Table as purchaser_table ON purchaser_table.Employee_Code = Tb_Master_Emp.Purchaser
		 LEFT JOIN HR_Master_Table as recommendor_table ON recommendor_table.Employee_Code = Tb_Master_Emp.Recommender
		 LEFT JOIN HR_Master_Table as verifier_table ON verifier_table.Employee_Code = Tb_Master_Emp.Finance_Verfier
		 LEFT JOIN HR_Master_Table as approver_table ON approver_table.Employee_Code = Tb_Master_Emp.Approver
		 WHERE PO_creator_Release_Codes = '".$po_creator_code."' and Purchase_Type = 'Purchase & Vendor selection (R&VS)' AND Plant = '".$_POST['Plant']."' AND Material_Group = '".$_POST['MaterialGroup']."' AND Tb_Master_Emp.Status = 'Active'";

		 if($amount == '') {
		 	$sql .= " AND Value = ''";
		 }

		 if($amount != '' && $amount > 50000) {
		 	$sql .= " AND Value != ''";
		 }

	    $sql_exec = sqlsrv_query($conn, $sql);

		$result = array();
		while ($row = sqlsrv_fetch_array($sql_exec,SQLSRV_FETCH_ASSOC)) {
			$result[] = $row; 
		}

		echo json_encode($result);exit;

	} elseif ($_POST['Action'] == 'update_purchase_request') {
		$_POST = array_map(function($value) { 
	         if(is_array($value)){
	                 $mArr = array_map(function($value1) { 
	                     return str_replace("'","''", $value1); 
	                 },$value);
	                 return $mArr;
	         }else{
	             return str_replace("'","''", $value); 
	         }
	  	}, $_POST);

		// echo "<pre>";print_r($_POST);exit;
		$response['status']  = 422;
		$response['message'] = "Something Went Wrong.";

	    $emp_id = $_SESSION['EmpID'];
	    $Requested_to = $Purchaser_Code;
	    $request_id = $_POST['request_id'];
	    $request_type = $_POST['request_type'];
	    $plant_id = $_POST['plant_id'];
	    $storage_location = $_POST['storage_location'];
	    $request_type1 = $_POST['request_type1'];
	    $Department = $_POST['department'];
	    $Finance_Verification = $_POST['Finance_Verification'];
	    $req_id = $_POST['request_id'];
    	$Reference = $_POST['reference'];

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

	        $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set Request_ID = '$request_id',Request_Type = '$request_type',Plant ='$plant_id',Storage_Location ='$storage_location',
	        Request_Category ='$request_type1',status ='$status',Department ='$Department',Finance_Verification = '$Finance_Verification',Persion_In_Workflow ='$Persion',Reference = '$Reference' WHERE Request_ID = '$req_id' ");


		    for ($i = 0; $i < count($_POST['item_code']); $i++) {

		        $item_code = $_POST['item_code'][$i];
		        $uom = $_POST['uom'][$i];
		        $description = $_POST['description'][$i];
		        $quantity = $_POST['quantity'][$i];
		        $Expected_Date = $_POST['Expected_Date'][$i];
		        $Specification = $_POST['Specification'][$i];
		        $fil = ($_FILES["Attachment"]["name"][$i] != '') ? $_FILES["Attachment"]["name"][$i] : ((isset($_POST['Saved_Attachment'][$i]) && $_POST['Saved_Attachment'][$i] != '') ? $_POST['Saved_Attachment'][$i] : '') ;
		        $budgest = $_POST['budget'][$i];
		        $ID = $_POST['id'][$i];
		        $MaterialGroup = $_POST['MaterialGroup'][$i];
		        

		        // $qry = sqlsrv_query($conn, "SELECT * from Tb_Request_Items ORDER BY ID desc") or die('error');
		        // $nextID = sqlsrv_fetch_array($qry)['ID'] + 1;
		        // $_paymentID =  $nextID;

		        if(!isset($_POST['id'][$i])){
		            $sql = "INSERT INTO Tb_Request_Items (Request_ID, Item_Code, UOM, Description, Quantity, Budget, Time_Log, status, Expected_Date, Specification, Attachment,
		            MaterialGroup, EMP_ID,Requested_to,Reference) VALUES 
		            ('$request_id','$item_code','$uom','$description','$quantity','$budgest',GETDATE(),'$status','$Expected_Date','$Specification','$fil',
		            '$MaterialGroup','$emp_id','$Requested_to','$Reference')";
		        }else{
		            $sql = "UPDATE Tb_Request_Items SET Request_ID = '$request_id',Item_Code = '$item_code',UOM = '$uom',Description = '$description',Quantity = '$quantity',
		            Budget = '$budgest',Time_Log = GETDATE(),status = '$status',Expected_Date = '$Expected_Date',Specification = '$Specification',Attachment = '$fil',
		            MaterialGroup ='$MaterialGroup',EMP_ID = '$emp_id',Requested_to ='$Requested_to',Reference = '$Reference'
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
		        	if($_FILES["Attachment"]["tmp_name"][$i] != '') {
		            	move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);
		        	}
		           	$response['status']  = 200;
					$response['message'] = "Purchase request updated successsfully.";
		        }

			}
		}
        echo json_encode($response);exit;
    }

    elseif ($_POST['Action'] == 'update_purchase_request_div') {
		$_POST = array_map(function($value) { 
	         if(is_array($value)){
	                 $mArr = array_map(function($value1) { 
	                     return str_replace("'","''", $value1); 
	                 },$value);
	                 return $mArr;
	         }else{
	             return str_replace("'","''", $value); 
	         }
	  	}, $_POST);

		// echo "<pre>";print_r($_POST);exit;
		$response['status']  = 422;
		$response['message'] = "Something Went Wrong.";

	    $emp_id = $_SESSION['EmpID'];
	    $Requested_to = $Purchaser_Code;
	    $request_id = $_POST['request_id'];
	    $request_type = $_POST['request_type'];
	    $plant_id = $_POST['plant_id'];
	    $storage_location = $_POST['storage_location'];
	    $request_type1 = $_POST['request_type1'];
	    $Department = $_POST['department'];
	    $Finance_Verification = $_POST['Finance_Verification'];
	    $req_id = $_POST['request_id'];
    	$Reference = $_POST['reference'];

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

	        $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set Request_ID = '$request_id',Request_Type = '$request_type',Plant ='$plant_id',Storage_Location ='$storage_location',
	        Request_Category ='$request_type1',status ='$status',Department ='$Department',Finance_Verification = '$Finance_Verification',Persion_In_Workflow ='$Persion',Reference = '$Reference' WHERE Request_ID = '$req_id' ");


		    for ($i = 0; $i < count($_POST['item_code']); $i++) {

		        $item_code = $_POST['item_code'][$i];
		        $uom = $_POST['uom'][$i];
		        $description = $_POST['description'][$i];
		        $quantity = $_POST['quantity'][$i];
		        $Expected_Date = $_POST['Expected_Date'][$i];
		        $Specification = $_POST['Specification'][$i];
		        $fil = ($_FILES["Attachment"]["name"][$i] != '') ? $_FILES["Attachment"]["name"][$i] : ((isset($_POST['Saved_Attachment'][$i]) && $_POST['Saved_Attachment'][$i] != '') ? $_POST['Saved_Attachment'][$i] : '') ;
		        $budgest = $_POST['budget'][$i];
		        $ID = $_POST['id'][$i];
		        $MaterialGroup = $_POST['MaterialGroup'][$i];

		        $replace = $_POST['replace'][$i];
		        $rdateofpurchase = ($_POST['rdateofpurchase'][$i])!='' ? $_POST['rdateofpurchase'][$i] : '';
		        $rqty = ($_POST['rqty'][$i])!='' ? $_POST['rqty'][$i] : 0;
		        $rremarks = ($_POST['rremarks'][$i])!='' ? $_POST['rremarks'][$i] : '';
		        $rcost = ($_POST['rcost'][$i])!='' ? $_POST['rcost'][$i] : 0;
		        

		        // $qry = sqlsrv_query($conn, "SELECT * from Tb_Request_Items ORDER BY ID desc") or die('error');
		        // $nextID = sqlsrv_fetch_array($qry)['ID'] + 1;
		        // $_paymentID =  $nextID;


		        if(!isset($_POST['id'][$i])){
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
		        	if($_FILES["Attachment"]["tmp_name"][$i] != '') {
		            	move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);
		        	}
		           	$response['status']  = 200;
					$response['message'] = "Purchase request updated successsfully.";
		        }

			}
		}
        echo json_encode($response);exit;
    }

    if($_POST['Action'] == 'checksession') {
		if(isset($_SESSION['EmpID']) && $_SESSION['EmpID'] != '') {
			$response['status'] = 200;
			$response['message'] = 'Your Login Session running.';
    	}
    	else {
			$response['status'] = 419;
			$response['message'] = 'Your Login Session closed.';
		}

        echo json_encode($response);exit;
    }


	if ($_POST['Action'] == 'save_quotation') {
		// echo "<pre>";print_r($_POST);exit;
		$response['status']  = 422;
		$response['message'] = "Something Went Wrong.";

		$_POST = array_map(function($value) { 
	         if(is_array($value)){
	                 $mArr = array_map(function($value1) { 
	                     return str_replace("'","''", $value1); 
	                 },$value);
	                 return $mArr;
	         }else{
	             return str_replace("'","''", $value); 
	         }
	  	}, $_POST);


		$request_id = $_POST['req_id'];
		
		$saved_quotation_sql = "SELECT * FROM Tb_Vendor_Selection WHERE Request_ID = '".$request_id."'";
		$saved_quotation_exec = sqlsrv_query($conn, $saved_quotation_sql, array(), array("Scrollable" => 'static'));
		$saved_count = sqlsrv_num_rows($saved_quotation_exec);
		$saved_data = array();
		while($saved_quotation_res = sqlsrv_fetch_array($saved_quotation_exec,SQLSRV_FETCH_ASSOC)) {
			$saved_data[] = $saved_quotation_res;
		}

		// save only how many vendors filled by customer  
		$data_count = 0;
		foreach ($_POST['Vendor_SAP'] as $key => $sap_val) {
			if($sap_val != 'Select Vendor SAP') {
				$data_count++;
			}
		}


		//approval mapping id insertion
	    $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set approval_mapping_id = '".$_POST['mapping_id']."' WHERE Request_id = '$request_id' ");

		// for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
		$file_index = 1;
		for ($i = 0; $i < $data_count; $i++) {
			
			$Vendor_SAP = $_POST['Vendor_SAP'][$i];
			$Vendor_Name = $_POST['Vendor_Name'][$i];
			$Vendor_City = $_POST['Vendor_City'][$i];
			$vendor_Active_SAP = $_POST['vendor_Active_SAP'][$i];
			$Last_Purchase = $_POST['Last_Purchase'][$i];
			$Delivery_Time = $_POST['Delivery_Time'][$i];
			$Value_Of = $_POST['Value_Of'][$i];
			$Fright_Charges = $_POST['Fright_Charges'][$i];
			$Insurance_Details = $_POST['Insurance_Details'][$i];
			$GST_Component = $_POST['GST_Component'][$i];
			$Warrenty = $_POST['Warrenty'][$i];
			$Payment_Terms = $_POST['Payment_Terms'][$i];
			$Requester_Selection = $_POST['Requester_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			
			$total_amount    = $_POST['amt_tot'][$i];
			$discount_amount = $_POST['discount_amount'][$i];
			$package_amount = $_POST['package_amount'][$i];
			$package_percentage = $_POST['package_percentage'][$i];



			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;
			// $Requested_to = $Recommender_Code;
			$Requested_to = $_POST['recommendor_id'];
			
			$fil = '';
			if($_FILES["Attachment"]["name"][$i] != '') {
				$filename = $request_id.'_Vendor'.$file_index.'_';
				$extension = pathinfo($_FILES["Attachment"]["name"][$i], PATHINFO_EXTENSION);
				$fil = $filename.strtotime(date('h:i:s')).'.'.$extension;

				$tmp_name = $_FILES["Attachment"]["tmp_name"][$i];
				$path = "file/" . $fil;
				$file1 = explode(".", $fil);
				if( !isset($file1[1]) ){
		            $file1[1]=0;
		        }
				$ext = $file1[1];
				$allowed = array("jpg", "png", "gif", "pdf", "wmv", "pdf", "zip");
				if (in_array($ext, $allowed)) {
					move_uploaded_file($tmp_name, $path);
				}
			} elseif(COUNT($saved_data) > 0) {
				$fil = $saved_data[$i]['Attachment']; 
			}


			$vendor_exist_query ="SELECT * from Tb_Vendor_Selection where Request_Id = '".$request_id."' and V_id = '".$V_id."'";
            $vendor_exist_query_exec = sqlsrv_query($conn, $vendor_exist_query,array(),array("Scrollable" => 'static'));
			$vendor_exist_count = sqlsrv_num_rows($vendor_exist_query_exec);


			if($vendor_exist_count > 0) {
				$vendor = "UPDATE Tb_Vendor_Selection SET Vendor_SAP='$Vendor_SAP', Vendor_Name='$Vendor_Name', Vendor_City='$Vendor_City', vendor_Active_SAP='$vendor_Active_SAP', Last_Purchase='$Last_Purchase', Delivery_Time='$Delivery_Time', Value_Of='$Value_Of', Fright_Charges='$Fright_Charges', Insurance_Details='$Insurance_Details', GST_Component='$GST_Component', Warrenty='$Warrenty', Payment_Terms='$Payment_Terms', Requester_Selection='$Requester_Selection', Requester_Remarks='$Requester_Remarks', Attachment='$fil', Time_Log=GETDATE(), status='Requested', V_id='$V_id', EMP_ID='$emp_id', Requested_to='$Requested_to',total_amount='$total_amount',discount_amount='$discount_amount',package_amount='$package_amount',package_percentage = '$package_percentage' WHERE Request_Id='$request_id' and V_id = '".$V_id."'";
			} else {
				$vendor ="INSERT INTO Tb_Vendor_Selection(Request_Id, Vendor_SAP, Vendor_Name, Vendor_City,vendor_Active_SAP,
					Last_Purchase, Delivery_Time, Value_Of, Fright_Charges, Insurance_Details, GST_Component, Warrenty,
					Payment_Terms, Requester_Selection, Requester_Remarks, Attachment, Time_Log, status, V_id, EMP_ID,Requested_to,total_amount,discount_amount,package_amount,package_percentage) VALUES 
					('$request_id','$Vendor_SAP','$Vendor_Name','$Vendor_City','$vendor_Active_SAP','$Last_Purchase',
					'$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component','$Warrenty','$Payment_Terms','$Requester_Selection',
					'$Requester_Remarks','$fil',GETDATE(),'Requested','$V_id','$emp_id','$Requested_to','$total_amount','$discount_amount','$package_amount','$package_percentage')";
			}


            $rs_vendor = sqlsrv_query($conn, $vendor);

            // is saved - 1 means saved 
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set is_saved = '1' WHERE Request_Id = '$request_id' ");

			$file_index++;
		}


		
		// save only how many vendors filled by customer  
		$material_data_count = 0;
		foreach ($_POST['Price'] as $key => $Price_val) {
			if($Price_val != '') {
				$material_data_count++;
			}
		}

		for ($i = 0; $i < $material_data_count; $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];
			$Requested_to = $_POST['recommendor_id'];

			$gst_percentage      = $_POST['gst_percent'][$i];
			$discount_percentage = $_POST['discount_percent'][$i];

			$vendor_quantity_exist_query ="SELECT * from Tb_Vendor_Quantity where Request_Id = '".$request_id."' and V_id = '".$V_id."' and Meterial_Name = '".$Meterial_Name."'";
            $vendor_quantity_exist_query_exec = sqlsrv_query($conn, $vendor_quantity_exist_query,array(),array("Scrollable" => 'static'));
			$vendor_quantity_exist_count = sqlsrv_num_rows($vendor_quantity_exist_query_exec);


			if($vendor_quantity_exist_count > 0) {
				$meterial = "UPDATE Tb_Vendor_Quantity SET Meterial_Name='$Meterial_Name', Quantity='$Quantity_Details', status='Requested', Price='$Price', Total='$Total', V_id='$V_id', EMP_ID='$emp_id', Requested_to='$Requested_to',gst_percentage='$gst_percentage',discount_percentage='$discount_percentage' WHERE Request_Id='$request_id' and V_id = '".$V_id."' and Meterial_Name = '".$Meterial_Name."'";

			} else {
				$meterial = "INSERT INTO Tb_Vendor_Quantity(Request_Id, Meterial_Name, Quantity, status, Price, Total,  V_id, EMP_ID,Requested_to,gst_percentage,discount_percentage) VALUES 
	 			('$request_id','$Meterial_Name','$Quantity_Details','Requested','$Price','$Total','$V_id','$emp_id','$Requested_to','$gst_percentage','$discount_percentage')";
			}

			$rs_material = sqlsrv_query($conn, $meterial);			
		}

		$response['status']  = 200;
		$response['message'] = "Quotation saved successsfully.";
        echo json_encode($response);exit;
	}

	if ($_POST['Action'] == 'get_vendors_list') {
		$vendor_detail_array = array();
		$vendor_detail = sqlsrv_query($conn, "Select DISTINCT VendorCode,VendorName from vendor_master where VendorCode like 'ST%' ");
		while ($c = sqlsrv_fetch_array($vendor_detail,SQLSRV_FETCH_ASSOC)) {
			$vendor_detail_array[] = $c; 
		}

		$response['status']  = 200;
		$response['data'] 	 = $vendor_detail_array;
        echo json_encode($response);exit;
	}

	if ($_POST['Action'] == 'get_payment_terms') {
		$payment_terms_array = array();
		$payment_terms_sql = sqlsrv_query($conn, "select * from Payment_master_PO");
		while ($row = sqlsrv_fetch_array($payment_terms_sql,SQLSRV_FETCH_ASSOC)) {
			$payment_terms_array[] = $row; 
		}

		$response['status']  = 200;
		$response['data'] 	 = $payment_terms_array;
        echo json_encode($response);exit;
	}
}

?>