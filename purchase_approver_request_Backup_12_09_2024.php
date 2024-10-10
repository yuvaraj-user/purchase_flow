<?php
include('../auto_load.php');
include('adition.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
if(!isset($_SESSION['EmpID']))
{
    ?>
<script type="text/javascript">
    window.location = "../pages/indexAdmin.php";
</script>
<?php
}
$request_id = $_GET['id'];

$Employee_Id = $_SESSION['EmpID'];

$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
WHERE Approver = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$Approver_Code = $selector_arr1['Approver'];
$verifier_name = $selector_arr1['Finance_Verfier'];
$verifier_code = $selector_arr1['Finance_Verfier'];
$Purchaser_Code = $selector_arr1['Purchaser'];
// print_r($Purchaser_Code);
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];

if (isset($_POST["save"])) {
	// echo "<pre>";print_r($_POST);exit;

	 //approval mapping id insertion
   $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set approval_mapping_id = '".$_POST['mapping_id']."' WHERE Request_id = '$request_id' ");

	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);

    $status       = 'Approved'; 
    $Requested_to = NULL;
    if($_POST['approver2_id'] != '' && $_POST['approver2_id'] != null) {
	    $status = 'Waiting_for_approval2';
	    $Requested_to = $_POST['approver2_id'];
    }

    if($updated_query['Request_id'] == ''){

		for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$query = "INSERT INTO Tb_Approver
			(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
			Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
			Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,
			Approver_Selection,EMP_ID,Requested_to)VALUES
			('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
			'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
			'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks ',
	  		'$fil',GETDATE(),'$status','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$emp_id','$Requested_to')";

			// $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approved' WHERE Request_Id = '$request_id' ");
			// $query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approved' WHERE Request_Id = '$request_id' ");

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set status = '$status' WHERE Request_id = '$request_id' ");

            $rs = sqlsrv_query($conn, $query);
        }


	}else{
		for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			 $query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = '$status',Finance_Remarks = '$Finance_Remarks',Total_Budget = '$Total_Budget',Approver_Remarks = '$Approver_Remarks'
            ,Approver_Selection = '$Approver_Selection'  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

            // $query2 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approved' WHERE Request_Id = '$request_id' ");
            // $query3 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approved' WHERE Request_Id = '$request_id' ");

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set status = '$status' WHERE Request_id = '$request_id' ");


        }

    }

	if($updated_query['Request_id'] == ''){
		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];

			$meterial = "INSERT INTO Tb_Approver_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID) VALUES 
			('$request_id','$Meterial_Name','$Quantity_Details','$status','$Price','$Total','$V_id','$emp_id')";

			$rs_material = sqlsrv_query($conn, $meterial);

		}
	}else{

		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {
			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = '$status'  WHERE V_id = '$V_id' and Request_id = '$request_id'");


		}
	}
	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items 
	ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id WHERE Tb_Request.Request_ID = '$request_id'");
	$updated_query = sqlsrv_fetch_array($update_qry);
	$PERSION =  $updated_query['Persion_In_Workflow'];
	// print_r($PERSION);exit;
	$HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
	$idss = array();
	while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
		$idss[] = $ids['Office_Email_Address'];
	}
	$implode = implode(',', $idss);
	// print_r($implode);exit;

	$update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$verifier_code'");
	$updated_query1 = sqlsrv_fetch_array($update_qry1);
	$To =  $updated_query1['Office_Email_Address'];
	// print_r($To);exit;

	$update_qry12 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Recommender_Code'");
	$updated_query12 = sqlsrv_fetch_array($update_qry12);
	$Cc =  $updated_query12['Office_Email_Address'];
	//  print_r($Cc);exit;

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

	// $to = explode(',', $To);
    $to = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

	foreach ($to as $address) {
		// $mail->AddAddress($address);
		$mail->AddAddress(trim($address));
	}
	// $array = "$Cc,$implode";

	// $cc = explode(',', $array);
	// // print_r($cc);exit;
	// foreach ($cc as $ccc) {
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
	// }
	// $bcc = explode(',', $Bcc);

	// foreach ($bcc as $bccc) {
	//   // $mail->AddAddress($address);
	//   $mail->addBCC(trim($bccc));
	// }

	// $mail->addAttachment($fil);         // Add attachments
	// $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $updated_query['Request_Category'];
			
	$mail->Body = '
	<html>
	<head>
		<style>
		table, td, th {
		border: 1px solid;
		}
		
		table {
		width: 100%;
		border-collapse: collapse;
		}
		</style>
		</head>
			<body>
				<table >
					<thead>
						<tr>
							<th class="text-center">S.No</th>
							<th class="text-center">Request ID</th>
							<th class="text-center">Department</th>
							<th class="text-center">Category</th>
							<th class="text-center">Plant</th>
							<th class="text-center">Meterial</th>
							<th class="text-center">Quantity</th>                          
							<th class="text-center">Status</th>                          
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								1
							</td>
							<td>
								' . $request_id . '
							</td>
							<td>
								' . $updated_query['Department'] . '
							</td>
							<td>
								' . $updated_query['Request_Type'] . '
							</td>
							<td>
								' . $updated_query['Plant'] . '
							</td>
							<td>
								' . $updated_query['Item_Code'] . '
							</td>
							<td>
								' . $updated_query['Quantity'] . '
							</td>
							<td>
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>Approved</span></h4>
							</td>
						</tr>
					</tbody>
				</table>
			</body>
	</html>';

	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
	if (!$mail->send()) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}else{
		?>
	<script type="text/javascript">
		alert("Approved successsfully");
		window.location = "show_approver.php";
	</script>
	<?php
	}
}
// echo "<script>window.location.href ='show_approver.php'</script>";
///EXIT;
// send 
if (isset($_POST["send"])) {
	// echo '<pre>';
	// print_r($_POST);die();
	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
    if($updated_query['Request_id'] == ''){

		for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;
	
            for ($j = 0; $j < count($_POST['Approver_back_remark']); $j++) {
                $Approver_back_remark = $_POST['Approver_back_remark'][$j];

				$query = "INSERT INTO Tb_Approver
				(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
				Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
				Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,Approver_Selection,Approver_back_remark,EMP_ID)VALUES
				('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
				'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
				'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks',
				  '$fil',GETDATE(),'Approver Send Back','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Approver_back_remark','$emp_id')";
		
				$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approver Send Back', Approver_back_remark = '$Approver_back_remark' WHERE Request_Id = '$request_id' ");
				$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approver Send Back', Approver_back_remark = '$Approver_back_remark' WHERE Request_Id = '$request_id' ");

                $rs = sqlsrv_query($conn, $query);
        }
    }

	}else{
		for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
				$fil="";
				}else{
				$fil=$_POST['Attachment'][$i];
				}
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

				for ($j = 0; $j < count($_POST['Approver_back_remark']); $j++) {
					$Approver_back_remark = $_POST['Approver_back_remark'][$j];

				$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = 'Approver Send Back',Finance_Remarks = '$Finance_Remarks',
				Total_Budget = '$Total_Budget',Finance_Remarks = '$Finance_Remarks',Approver_Remarks = '$Approver_Remarks',
				Approver_Selection = '$Approver_Selection', Approver_back_remark = '$Approver_back_remark'  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

				$query2 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approver Send Back', Approver_back_remark = '$Approver_back_remark' WHERE Request_Id = '$request_id' ");
				$query3 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approver Send Back', Approver_back_remark = '$Approver_back_remark' WHERE Request_Id = '$request_id' ");

			}
		}

		}

	if($updated_query['Request_id'] == ''){
		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];

			for ($j = 0; $j < count($_POST['Approver_back_remark']); $j++) {
				$Approver_back_remark = $_POST['Approver_back_remark'][$j];

				$meterial = "INSERT INTO Tb_Approver_Meterial(Request_Id, Meterial_Name, Quantity, status, Price, Total,  V_id,
				Approver_back_remark,EMP_ID) VALUES 
				('$request_id','$Meterial_Name','$Quantity_Details','Approver Send Back','$Price','$Total','$V_id','$Approver_back_remark','$emp_id')";

			$rs_material = sqlsrv_query($conn, $meterial);
		
		}
	}
	}else{

		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];

			for ($j = 0; $j < count($_POST['Approver_back_remark']); $j++) {
				$Approver_back_remark = $_POST['Approver_back_remark'][$j];

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = 'Approver Send Back',Approver_back_remark = '$Approver_back_remark' WHERE V_id = '$V_id' and Request_id = '$request_id' ");


		}
	}
	}
	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items 
	ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id WHERE Tb_Request.Request_ID = '$request_id'");
	$updated_query = sqlsrv_fetch_array($update_qry);
	$PERSION =  $updated_query['Persion_In_Workflow'];
	// print_r($PERSION);exit;
	$HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
	$idss = array();
	while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
		$idss[] = $ids['Office_Email_Address'];
	}
	$implode = implode(',', $idss);
	// print_r($implode);exit;

	$update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$verifier_code'");
	$updated_query1 = sqlsrv_fetch_array($update_qry1);
	$To =  $updated_query1['Office_Email_Address'];
	// print_r($To);exit;

	$update_qry12 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Recommender_Code'");
	$updated_query12 = sqlsrv_fetch_array($update_qry12);
	$Cc =  $updated_query12['Office_Email_Address'];
	//  print_r($Cc);exit;

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

	// $to = explode(',', $To);
    $to = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

	foreach ($to as $address) {
		// $mail->AddAddress($address);
		$mail->AddAddress(trim($address));
	}
	// $array = "$Cc,$implode";

	// $cc = explode(',', $array);
	// // print_r($cc);exit;
	// foreach ($cc as $ccc) {
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
	// }
	// $bcc = explode(',', $Bcc);

	// foreach ($bcc as $bccc) {
	//   // $mail->AddAddress($address);
	//   $mail->addBCC(trim($bccc));
	// }

	// $mail->addAttachment($fil);         // Add attachments
	// $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $updated_query['Request_Category'];
			
	$mail->Body = '
	<html>
	<head>
		<style>
		table, td, th {
		border: 1px solid;
		}
		
		table {
		width: 100%;
		border-collapse: collapse;
		}
		</style>
		</head>
			<body>
				<table >
					<thead>
						<tr>
							<th class="text-center">S.No</th>
							<th class="text-center">Request ID</th>
							<th class="text-center">Department</th>
							<th class="text-center">Category</th>
							<th class="text-center">Plant</th>
							<th class="text-center">Meterial</th>
							<th class="text-center">Quantity</th>                          
							<th class="text-center">Status</th>                          
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								1
							</td>
							<td>
								' . $request_id . '
							</td>
							<td>
								' . $updated_query['Department'] . '
							</td>
							<td>
								' . $updated_query['Request_Type'] . '
							</td>
							<td>
								' . $updated_query['Plant'] . '
							</td>
							<td>
								' . $updated_query['Item_Code'] . '
							</td>
							<td>
								' . $updated_query['Quantity'] . '
							</td>
							<td>
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>Send Back</span></h4>
							</td>
						</tr>
					</tbody>
				</table>
			</body>
	</html>';

	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
	if (!$mail->send()) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}else{
		?>
	<script type="text/javascript">
		alert("Send Back successsfully");
		window.location = "show_approver.php";
	</script>
	<?php
	}
	}
//reject
if (isset($_POST["reject"])) {

	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
    if($updated_query['Request_id'] == ''){

	    for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

            for ($j = 0; $j < count($_POST['Approver_Reject']); $j++) {
                $Approver_Reject = $_POST['Approver_Reject'][$j];

				$query = "INSERT INTO Tb_Approver
				(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
				Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
				Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,Approver_Selection,Approver_Reject,EMP_ID)VALUES
				('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
				'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
				'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks',
				  '$fil',GETDATE(),'Approver_Reject','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Approver_Reject','$emp_id')";
		
		$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approver_Reject', Approver_reject = '$Approver_Reject' WHERE Request_Id = '$request_id' ");
		$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approver_Reject', Approver_reject = '$Approver_Reject' WHERE Request_Id = '$request_id' ");

                $rs = sqlsrv_query($conn, $query);
        }
    }

	}else{
		for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
				$fil="";
				}else{
				$fil=$_POST['Attachment'][$i];
				}
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			for ($j = 0; $j < count($_POST['Approver_Reject']); $j++) {
				$Approver_Reject = $_POST['Approver_Reject'][$j];

				$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = 'Approver_Reject',Finance_Remarks = '$Finance_Remarks',
				Total_Budget = '$Total_Budget',Finance_Remarks = '$Finance_Remarks',Recommender_Remarks = '$Recommender_Remarks',
				Approver_Selection = '$Approver_Selection', Approver_Reject = '$Approver_Reject'  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

			$query2 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approver_Reject', Approver_reject = '$Approver_Reject' WHERE Request_Id = '$request_id' ");
			$query3 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approver_Reject', Approver_reject = '$Approver_Reject' WHERE Request_Id = '$request_id' ");

			}
		}

		}

	if($updated_query['Request_id'] == ''){
		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];

			for ($j = 0; $j < count($_POST['Approver_Reject']); $j++) {
				$Approver_Reject = $_POST['Approver_Reject'][$j];

				$meterial = "INSERT INTO Tb_Approver_Meterial(Request_Id, Meterial_Name, Quantity, status, Price, Total,  V_id,
				Approver_Reject,EMP_ID) VALUES 
				('$request_id','$Meterial_Name','$Quantity_Details','Approver_Reject','$Price','$Total','$V_id','$Approver_Reject','$emp_id')";


			$rs_material = sqlsrv_query($conn, $meterial);

		}
	}
	}else{

		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {
			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];
			for ($j = 0; $j < count($_POST['Approver_Reject']); $j++) {
				$Approver_Reject = $_POST['Approver_Reject'][$j];

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = 'Approver_Reject',Approver_Reject = '$Approver_Reject' WHERE V_id = '$V_id' and Request_id = '$request_id' ");


		}
	}
	}
	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items 
	ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id WHERE Tb_Request.Request_ID = '$request_id'");
	$updated_query = sqlsrv_fetch_array($update_qry);
	$PERSION =  $updated_query['Persion_In_Workflow'];
	// print_r($PERSION);exit;
	$HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
	$idss = array();
	while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
		$idss[] = $ids['Office_Email_Address'];
	}
	$implode = implode(',', $idss);
	// print_r($implode);exit;

	$update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$verifier_code'");
	$updated_query1 = sqlsrv_fetch_array($update_qry1);
	$To =  $updated_query1['Office_Email_Address'];
	// print_r($To);exit;

	$update_qry12 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Recommender_Code'");
	$updated_query12 = sqlsrv_fetch_array($update_qry12);
	$Cc =  $updated_query12['Office_Email_Address'];
	//  print_r($Cc);exit;

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

	// $to = explode(',', $To);
    $to = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

	// foreach ($to as $address) {
	// 	// $mail->AddAddress($address);
	// 	$mail->AddAddress(trim($address));
	// }
	// $array = "$Cc,$implode";

	// $cc = explode(',', $array);
	// // print_r($cc);exit;
	// foreach ($cc as $ccc) {
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
	// }
	// $bcc = explode(',', $Bcc);

	// foreach ($bcc as $bccc) {
	//   // $mail->AddAddress($address);
	//   $mail->addBCC(trim($bccc));
	// }

	// $mail->addAttachment($fil);         // Add attachments
	// $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $updated_query['Request_Category'];
			
	$mail->Body = '
	<html>
	<head>
		<style>
		table, td, th {
		border: 1px solid;
		}
		
		table {
		width: 100%;
		border-collapse: collapse;
		}
		</style>
		</head>
			<body>
				<table >
					<thead>
						<tr>
							<th class="text-center">S.No</th>
							<th class="text-center">Request ID</th>
							<th class="text-center">Department</th>
							<th class="text-center">Category</th>
							<th class="text-center">Plant</th>
							<th class="text-center">Meterial</th>
							<th class="text-center">Quantity</th>                          
							<th class="text-center">Status</th>                          
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								1
							</td>
							<td>
								' . $request_id . '
							</td>
							<td>
								' . $updated_query['Department'] . '
							</td>
							<td>
								' . $updated_query['Request_Type'] . '
							</td>
							<td>
								' . $updated_query['Plant'] . '
							</td>
							<td>
								' . $updated_query['Item_Code'] . '
							</td>
							<td>
								' . $updated_query['Quantity'] . '
							</td>
							<td>
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>Rejected</span></h4>
							</td>
						</tr>
					</tbody>
				</table>
			</body>
	</html>';

	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
	if (!$mail->send()) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}else{
		?>
	<script type="text/javascript">
		alert("Reject successsfully");
		window.location = "show_approver.php";
	</script>
	<?php
	}
}
if (isset($_POST["Verification"])) {

	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);

	if($updated_query['Request_id'] == ''){

		for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$status = 'Review';
            $Verification_Type = '2';

			$query = "INSERT INTO Tb_Approver
			(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
			Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
			Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,
			Approver_Selection,Verification_Type,EMP_ID)VALUES
			('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
			'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
			'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks ',
	  		'$fil',GETDATE(),'$status','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Verification_Type','$emp_id')";

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set Verification_Type = '$Verification_Type' WHERE Request_id = '$request_id' ");

            $rs = sqlsrv_query($conn, $query);
        }
	}else{
		for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$status = 'Review';
            $Verification_Type = '2';

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = '$status',Verification_Type = '$Verification_Type'
			  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

			// $query = "INSERT INTO Tb_Approver
			// (Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
			// Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
			// Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,
			// Approver_Selection,Verification_Type,EMP_ID)VALUES
			// ('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
			// '$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
			// '$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks ',
	  		// '$fil',GETDATE(),'$status','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Verification_Type','$emp_id')";

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set Verification_Type = '$Verification_Type' WHERE Request_id = '$request_id' ");

            // $rs = sqlsrv_query($conn, $query);
        }
	}
	if($updated_query['Request_id'] == ''){

		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];

			$meterial = "INSERT INTO Tb_Approver_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID) VALUES 
			('$request_id','$Meterial_Name','$Quantity_Details','$status','$Price','$Total','$V_id','$emp_id')";

			$rs_material = sqlsrv_query($conn, $meterial);

		}
	}else{
		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];
	
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = '$status'  WHERE V_id = '$V_id' and Request_id = '$request_id'");
	
	
		}
	}
	// exit;
	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items 
	ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id WHERE Tb_Request.Request_ID = '$request_id'");
	$updated_query = sqlsrv_fetch_array($update_qry);
	$PERSION =  $updated_query['Persion_In_Workflow'];
	// print_r($PERSION);exit;
	$HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
	$idss = array();
	while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
		$idss[] = $ids['Office_Email_Address'];
	}
	$implode = implode(',', $idss);
	// print_r($implode);exit;

	$update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$verifier_code'");
	$updated_query1 = sqlsrv_fetch_array($update_qry1);
	$To =  $updated_query1['Office_Email_Address'];
	// print_r($To);exit;

	$update_qry12 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Recommender_Code'");
	$updated_query12 = sqlsrv_fetch_array($update_qry12);
	$Cc =  $updated_query12['Office_Email_Address'];
	//  print_r($Cc);exit;

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

	// $to = explode(',', $To);
    $to = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

	foreach ($to as $address) {
		// $mail->AddAddress($address);
		$mail->AddAddress(trim($address));
	}
	// $array = "$Cc,$implode";

	// $cc = explode(',', $array);
	// // print_r($cc);exit;
	// foreach ($cc as $ccc) {
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
	// }
	// $bcc = explode(',', $Bcc);

	// foreach ($bcc as $bccc) {
	//   // $mail->AddAddress($address);
	//   $mail->addBCC(trim($bccc));
	// }

	// $mail->addAttachment($fil);         // Add attachments
	// $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $updated_query['Request_Category'];
			
	$mail->Body = '
	<html>
	<head>
		<style>
		table, td, th {
		border: 1px solid;
		}
		
		table {
		width: 100%;
		border-collapse: collapse;
		}
		</style>
		</head>
			<body>
				<table >
					<thead>
						<tr>
							<th class="text-center">S.No</th>
							<th class="text-center">Request ID</th>
							<th class="text-center">Department</th>
							<th class="text-center">Category</th>
							<th class="text-center">Plant</th>
							<th class="text-center">Meterial</th>
							<th class="text-center">Quantity</th>                          
							<th class="text-center">Status</th>                          
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								1
							</td>
							<td>
								' . $request_id . '
							</td>
							<td>
								' . $updated_query['Department'] . '
							</td>
							<td>
								' . $updated_query['Request_Type'] . '
							</td>
							<td>
								' . $updated_query['Plant'] . '
							</td>
							<td>
								' . $updated_query['Item_Code'] . '
							</td>
							<td>
								' . $updated_query['Quantity'] . '
							</td>
							<td>
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>'.$status.'</span></h4>
							</td>
						</tr>
					</tbody>
				</table>
			</body>
	</html>';

	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
	if (!$mail->send()) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}else{
		?>
	<script type="text/javascript">
		alert("Review successsfully");
		window.location = "show_approver.php";
	</script>
	<?php
	}
}

if (isset($_POST["Reference"])) {

	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);

	if($updated_query['Request_id'] == ''){

		for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$status = 'Reference';
			$Reference_emil = $_POST['Reference_emil'];
			$Remark_To_Reference = $_POST['Remark_To_Reference'];
            // $Verification_Type = '2';

			$query = "INSERT INTO Tb_Approver
			(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
			Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
			Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,
			Approver_Selection,Reference_Empid,Remark_To_Reference,EMP_ID)VALUES
			('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
			'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
			'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks ',
	  		'$fil',GETDATE(),'$status','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Reference_emil','$Remark_To_Reference','$emp_id')";

			
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");

            $rs = sqlsrv_query($conn, $query);
        //    exit; 
        }

			// exit;
	}else{
		for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
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
			$Recommender_Selection = $_POST['Recommender_Selection'][$i];
			$Requester_Remarks = $_POST['Requester_Remarks'][$i];
			$Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$status = 'Reference';
			$Reference_emil = $_POST['Reference_emil'];
			$Remark_To_Reference = $_POST['Remark_To_Reference'];
            // $Verification_Type = '2';

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = '$status',Reference_Empid = '$Reference_emil',Remark_To_Reference = '$Remark_To_Reference'
			  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");

            // $rs = sqlsrv_query($conn, $query);
        }

	}
			
	if($updated_query['Request_id'] == ''){

		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];
	
			$meterial = "INSERT INTO Tb_Approver_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID) VALUES 
			 ('$request_id','$Meterial_Name','$Quantity_Details','$status','$Price','$Total','$V_id','$emp_id')";
	
			$rs_material = sqlsrv_query($conn, $meterial);

		}
	}else{
		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];
	
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = '$status'  WHERE V_id = '$V_id' and Request_id = '$request_id'");
	
	
		}
	}

	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items 
	ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id WHERE Tb_Request.Request_ID = '$request_id'");
	$updated_query = sqlsrv_fetch_array($update_qry);
	$PERSION =  $updated_query['Persion_In_Workflow'];
	// print_r($PERSION);exit;
	$HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
	$idss = array();
	while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
		$idss[] = $ids['Office_Email_Address'];
	}
	$implode = implode(',', $idss);
	// print_r($implode);exit;
	$ecode =  $_POST['Reference_emil'];
	$update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$ecode'");
	$updated_query1 = sqlsrv_fetch_array($update_qry1);
	$To =  $updated_query1['Office_Email_Address'];
	// print_r($To);exit;

	$update_qry12 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Recommender_Code'");
	$updated_query12 = sqlsrv_fetch_array($update_qry12);
	$Cc =  $updated_query12['Office_Email_Address'];
	//  print_r($Cc);exit;

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

	// $to = explode(',', $To);
    $to = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

	foreach ($to as $address) {
		// $mail->AddAddress($address);
		$mail->AddAddress(trim($address));
	}
	// $array = "$Cc,$implode";

	// $cc = explode(',', $array);
	// // print_r($cc);exit;
	// foreach ($cc as $ccc) {
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
	// }
	// $bcc = explode(',', $Bcc);

	// foreach ($bcc as $bccc) {
	//   // $mail->AddAddress($address);
	//   $mail->addBCC(trim($bccc));
	// }

	// $mail->addAttachment($fil);         // Add attachments
	// $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $updated_query['Request_Category'];
			
	$mail->Body = '
	<html>
	<head>
		<style>
		table, td, th {
		border: 1px solid;
		}
		
		table {
		width: 100%;
		border-collapse: collapse;
		}
		</style>
		</head>
			<body>
				<table >
					<thead>
						<tr>
							<th class="text-center">S.No</th>
							<th class="text-center">Request ID</th>
							<th class="text-center">Department</th>
							<th class="text-center">Category</th>
							<th class="text-center">Plant</th>
							<th class="text-center">Meterial</th>
							<th class="text-center">Quantity</th>                          
							<th class="text-center">Status</th>                          
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								1
							</td>
							<td>
								' . $request_id . '
							</td>
							<td>
								' . $updated_query['Department'] . '
							</td>
							<td>
								' . $updated_query['Request_Type'] . '
							</td>
							<td>
								' . $updated_query['Plant'] . '
							</td>
							<td>
								' . $updated_query['Item_Code'] . '
							</td>
							<td>
								' . $updated_query['Quantity'] . '
							</td>
							<td>
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>'.$status.'</span></h4>
							</td>
						</tr>
					</tbody>
				</table>
			</body>
	</html>';

	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
	if (!$mail->send()) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}else{
		?>
	<script type="text/javascript">
		alert("Reference send successsfully");
		window.location = "show_approver.php";
	</script>
	<?php
	}
}
?>
<!doctype html>
<html lang="en">

<head>


    <meta charset="utf-8" />
    <title>
        <?php echo $Title ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="../global/photos/favicon.ico">

    <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

    <style>
        /* body {
            position: relative;
        } */
        .table-wrapper {
            overflow-x: scroll;
            overflow-y: visible;
            /* width:200px; */
            margin-left: 305px;
        }


        td,
        th {
            padding: 5px 20px;
            /* width: 100px; */
        }

        tbody tr {}

        th:first-child {
            position: absolute;
            left: 5px
        }

        .heading-table {
            border-collapse: collapse;
            border-spacing: 0;
            width: 100%;
            max-width: 100ch;
        }

        .heading-table caption {
            font-size: 1.1em;
            text-align: left;
            font-weight: bold;
            margin-bottom: 0.5em;
        }

        .heading-table .highlight {
            background: rgba(2 130 12 / 27%);
        }
    </style>

</head>


<body data-keep-enlarged="true" class="vertical-collpsed">
    <!-- Loader -->
    <div id="preloader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>
    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include('navbar.php') ?>

        <!-- ========== Left Sidebar Start ========== -->
        <?php include('sidebar.php') ?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">

                <!-- start page title -->
                <div class="page-title-box">
                    <div class="container-fluid">
                        <div class="row align-items-center">
                            <div class="col-sm-6">
                                <div class="page-title">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                        <li class="breadcrumb-item"><a href="show_approver.php">Show Approver Purchase
                                                Request</a></li>
                                        <li class="breadcrumb-item active">Approver Purchase Request</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-end d-none d-sm-block">
                                    <h4 class="">Request ID :
                                        <?php echo $request_id ?>
                                    </h4>
		                                <input type="hidden" id="r_id" value="<?php echo $request_id; ?>">
                                </div>

                                <?php
                                 $po_creator_sql = sqlsrv_query($conn, "SELECT EMP_ID from Tb_Request where Request_ID = '$request_id'");
                                  $po_creator_id = sqlsrv_fetch_array($po_creator_sql)['EMP_ID']; 
                                 ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end page title -->


                <div class="container-fluid">

                    <div class="page-content-wrapper">

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <form method="POST" enctype="multipart/form-data">
					                                <input type="hidden" id="mapping_id" name="mapping_id">
                                  	       <input type="hidden" id="po_creator_id" value="<?php echo $po_creator_id; ?>">
                                           <input type="hidden" name="recommendor_id" id="recommendor_id">
                                           <input type="hidden" name="finance_verifier_id" id="finance_verifier_id">
                                           <input type="hidden" name="approver_id" id="approver_id">
                                           <input type="hidden" name="approver2_id" id="approver2_id">

                                            <div class="table-wrapper">
                                                <table id="busDataTable" class="data heading-table">
                                                    <?php
                                                        $selector = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.V_id,Tb_Recommender.Vendor_SAP,Tb_Recommender.Vendor_Name,Tb_Recommender.Vendor_City,Tb_Recommender.vendor_Active_SAP,
                                                        Tb_Recommender.Last_Purchase,Tb_Recommender.Delivery_Time1,Tb_Recommender.Value_Of,Tb_Recommender.Fright_Charges,Tb_Recommender.Insurance_Details,Tb_Recommender.GST_Component,
                                                        Tb_Recommender.Warrenty,Tb_Recommender.Payment_Terms,Tb_Recommender.Requester_Remarks,Tb_Recommender.Recommender_Remarks,Tb_Recommender.Attachment,
                                                        Tb_Recommender.Status,Tb_Recommender.Requester_Selection,Tb_Recommender.Recommender_Selection,Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                                        Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total
                                                        FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id
                                                        WHERE Tb_Recommender.Request_id = '$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id  AND Tb_Recommender.Recommender_Selection = '1'
                                                        GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,Tb_Recommender.Vendor_SAP,Tb_Recommender.Vendor_Name,Tb_Recommender.Vendor_City,Tb_Recommender.vendor_Active_SAP,
                                                        Tb_Recommender.Last_Purchase,Tb_Recommender.Delivery_Time1,Tb_Recommender.Value_Of,Tb_Recommender.Fright_Charges,Tb_Recommender.Insurance_Details,Tb_Recommender.GST_Component,
                                                        Tb_Recommender.Warrenty,Tb_Recommender.Payment_Terms,Tb_Recommender.Requester_Remarks,Tb_Recommender.Recommender_Remarks,Tb_Recommender.Attachment,
                                                        Tb_Recommender.Status,Tb_Recommender.Requester_Selection,Tb_Recommender.Recommender_Selection,Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                                        Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total");
                                                        $selector_arr = sqlsrv_fetch_array($selector);
                                                        ?>
                                                    <colgroup>
                                                        <col>
                                                        <col class="highlight">
                                                        <col>
                                                    </colgroup>
                                                    <tbody class="results" id="rone">
                                                        <tr id="head">
                                                            <th>Particulars</th>
                                                            <td>
                                                                <?php echo $selector_arr['V_id'] ?><input type="hidden"
                                                                    class="form-control" name="V1_id[]"
                                                                    value="<?php echo $selector_arr['V_id'] ?>">
                                                            </td>
                                                            <?php
															$array_Details1 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.V_id,Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id
															WHERE Tb_Recommender.Request_id = '$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id  AND Tb_Recommender.Recommender_Selection ! = '1'
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details1 = sqlsrv_fetch_array($array_Details1)) {

																?>
                                                            <th>
                                                                <?php echo $array_dy_Details1['V_id'] ?><input
                                                                    type="hidden" class="form-control" name="V1_id[]"
                                                                    value="<?php echo $array_dy_Details1['V_id'] ?>">
                                                            </th>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="one">
                                                            <th>Vendor SAP Code, if available</th>
                                                            <td><input type="text" class="form-control" readonly
                                                                    name="Vendor_SAP[]"
                                                                    value="<?php echo $selector_arr['Vendor_SAP'] ?>">
                                                            </td>
                                                            <?php
															$array_Details1 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Vendor_SAP
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Vendor_SAP ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details1 = sqlsrv_fetch_array($array_Details1)) {

																?>
                                                            <td><input type="text" class="form-control" readonly
                                                                    name="Vendor_SAP[]"
                                                                    value="<?php echo $array_dy_Details1['Vendor_SAP'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="two">
                                                            <th>Name of vendor</th>
                                                            <td><input type="text" class="form-control  disabled"
                                                                    readonly id="vendorname" name="Vendor_Name[]"
                                                                    value="<?php echo $selector_arr['Vendor_Name'] ?>">
                                                            </td>
                                                            <?php
															$array_Details2 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Vendor_Name
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Vendor_Name ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details2 = sqlsrv_fetch_array($array_Details2)) {

																?>
                                                            <td><input type="text" class="form-control  disabled"
                                                                    readonly id="vendorname" name="Vendor_Name[]"
                                                                    value="<?php echo $array_dy_Details2['Vendor_Name'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="three">
                                                            <th>City of vendor</th>
                                                            <td><input type="text" class="form-control  disabled"
                                                                    readonly id="city" name="Vendor_City[]"
                                                                    value="<?php echo $selector_arr['Vendor_City'] ?>">
                                                            </td>
                                                            <?php
															$array_Details3 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Vendor_City
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Vendor_City ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details3 = sqlsrv_fetch_array($array_Details3)) {

																?>
                                                            <td><input type="text" class="form-control  disabled"
                                                                    readonly id="city" name="Vendor_City[]"
                                                                    value="<?php echo $array_dy_Details3['Vendor_City'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="four">
                                                            <th>Whether vendor is active in SAP?</th>
                                                            <td><input type="text" class="form-control  dis" readonly
                                                                    name="vendor_Active_SAP[]"
                                                                    value="<?php echo $selector_arr['vendor_Active_SAP'] ?>">
                                                            </td>
                                                            <?php
															$array_Details4 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.vendor_Active_SAP
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.vendor_Active_SAP ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details4 = sqlsrv_fetch_array($array_Details4)) {

																?>
                                                            <td><input type="text" class="form-control  dis" readonly
                                                                    name="vendor_Active_SAP[]"
                                                                    value="<?php echo $array_dy_Details4['vendor_Active_SAP'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="five">
                                                            <th>Last purchase made on</th>
                                                            <td><input type="text" class="form-control  dis" readonly
                                                                    name="Last_Purchase[]"
                                                                    value="<?php echo $selector_arr['Last_Purchase'] ?>">
                                                            </td>
                                                            <?php
															$array_Details5 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Last_Purchase
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Last_Purchase ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details5 = sqlsrv_fetch_array($array_Details5)) {

																?>
                                                            <td><input type="text" class="form-control  dis" readonly
                                                                    name="Last_Purchase[]"
                                                                    value="<?php echo $array_dy_Details5['Last_Purchase'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="six">
                                                            <th>
                                                                <table class="table table-bordered"
                                                                    style="width: 300px !important;">
                                                                    <thead>
                                                                        <tr>
                                                                            <td>
                                                                                <b>Material Wise Quantity Details</b>
                                                                            </td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
																		$result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$request_id'",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
																		$item_count = sqlsrv_num_rows($result);
																		while ($row = sqlsrv_fetch_array($result)) {
																			$ID = $row['ID'];
																			$ItemCode = $row['Item_Code'];
																			// print_r($ID);
																			?>
                                                                        <tr>
                                                                            <td><input type="text"
                                                                                    class="form-control-plaintext"
                                                                                    readonly
                                                                                    value="<?php echo trim($row['Description']) ?>-<?php echo $row['UOM'] ?>"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target=".bs-example-modal-center<?php echo $ID ?>">
                                                                                <!-- Modal -->
                                                                                <div class="modal fade bs-example-modal-center<?php echo $ID ?>"
                                                                                    tabindex="-1" role="dialog"
                                                                                    aria-labelledby="mySmallModalLabel"
                                                                                    aria-hidden="true">
                                                                                    <div
                                                                                        class="modal-dialog modal-dialog-centered">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h5
                                                                                                    class="modal-title mt-0">
                                                                                                    Last Purchace Date
                                                                                                    For Vendor</h5>
                                                                                                <button type="button"
                                                                                                    class="btn-close"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close">

                                                                                                </button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <table
                                                                                                    class="table table-bordered">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <td>Vendor
                                                                                                                Code
                                                                                                            </td>
                                                                                                            <th>Material
                                                                                                                Name
                                                                                                            </th>
                                                                                                            <th>Price
                                                                                                            </th>
                                                                                                            <th>Purchace
                                                                                                                Date
                                                                                                            </th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        <?php
                                                                                                                $result1 = sqlsrv_query($conn, "SELECT TOP 3 * FROM MIGO_DET WHERE  MATNR = '$ItemCode' ORDER BY LINE_ID DESC ");
                                                                                                                while ($row1 = sqlsrv_fetch_array($result1)) {
                                                                                                            ?>
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <?php echo $row1['LIFNR'] ?>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <?php echo $row['Description'] ?>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <?php echo $row1['MENGE'] ?>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <?php echo $row1['BUDAT_MKPF']->format('Y-m-d') ?>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <?php
                                                                                                                        }
                                                                                                                    ?>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button"
                                                                                                    class="btn btn-danger waves-effect waves-light btn-sm"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close">Close</button>
                                                                                            </div>
                                                                                        </div><!-- /.modal-content -->
                                                                                    </div><!-- /.modal-dialog -->
                                                                                </div><!-- /.modal -->
                                                                            </td>
                                                                        </tr>
                                                                        <?php
																		}
																		?>
                                                                    </tbody>
                                                                </table>

                                                            </th>
                                                            <td>
                                                                <table id="myTable1" class="table table-bordered"
                                                                    style="width: 345px !important;">
                                                                    <tr>
                                                                        <thead>
                                                                            <td>Quantity</td>
                                                                            <th>Price</th>
                                                                            <th>Total</th>
                                                                        </thead>
                                                                    </tr>
                                                                    <tbody>
                                                                        <?php
																		$i = 1;
																		$result = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
																		Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
																		Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total
																		FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
																		WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection  = '1' 
																		GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
																		Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
																		Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total ORDER BY Tb_Recommender.V_id DESC");
																		while ($row = sqlsrv_fetch_array($result)) {
																			?>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control qty" readonly
                                                                                    name="Quantity_Details[]"
                                                                                    value="<?php echo $row['Quantity'] ?>"
                                                                                    placeholder="Enter Quantity Details">
                                                                                <input type="hidden"
                                                                                    class="form-control"
                                                                                    name="Meterial_Name[]"
                                                                                    value="<?php echo $row['Meterial_Name'] ?>">
                                                                                <input type="hidden"
                                                                                    class="form-control" name="V_id[]"
                                                                                    value="<?php echo $row['V_id'] ?>">
                                                                            </td>

                                                                            <td>
                                                                                <input type="text" readonly
                                                                                    class="form-control price"
                                                                                    name="Price[]"
                                                                                    value="<?php echo $row['Price'] ?>">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control amount" readonly
                                                                                    name="Total[]"
                                                                                    value="<?php echo $row['Total'] ?>">
                                                                            </td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                            <?php
															$array_Details111 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id 
															AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details111 = sqlsrv_fetch_array($array_Details111)) {
																$arr = $array_dy_Details111['V_id'];
																?>
                                                            <td>
                                                                <table id="myTable1" class="table table-bordered"
                                                                    style="width: 345px !important;">
                                                                    <tr>
                                                                        <thead>
                                                                            <td>Quantity</td>
                                                                            <th>Price</th>
                                                                            <th>Total</th>
                                                                        </thead>
                                                                    </tr>
                                                                    <tbody>
                                                                        <?php
																			$array_Details1111 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
																			Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
																			Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total
																			FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
																			WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = '$arr' AND Tb_Recommender_Meterial.V_id = '$arr'
																			AND Tb_Recommender.Recommender_Selection ! = '1' 
																			GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
																			Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
																			Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total ORDER BY Tb_Recommender.V_id DESC");
																			while ($array_dy_Details1111 = sqlsrv_fetch_array($array_Details1111)) {

																				?>
                                                                        <tr>
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control qty" readonly
                                                                                    name="Quantity_Details[]"
                                                                                    value="<?php echo $array_dy_Details1111['Quantity'] ?>"
                                                                                    placeholder="Enter Quantity Details">
                                                                                <input type="hidden"
                                                                                    class="form-control"
                                                                                    name="Meterial_Name[]"
                                                                                    value="<?php echo $array_dy_Details1111['Meterial_Name'] ?>">
                                                                                <input type="hidden"
                                                                                    class="form-control" name="V_id[]"
                                                                                    value="<?php echo $arr ?>">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text" readonly
                                                                                    class="form-control price"
                                                                                    name="Price[]"
                                                                                    value="<?php echo $array_dy_Details1111['Price'] ?>">
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control amount" readonly
                                                                                    name="Total[]"
                                                                                    value="<?php echo $array_dy_Details1111['Total'] ?>">
                                                                            </td>
                                                                        </tr>
                                                                        <?php } ?>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="seven">
                                                            <th>Value of</th>
                                                            <td><input type="text" readonly class="form-control valueof1"
                                                                    name="Value_Of[]" id="totale2"
                                                                    title="Total + AdditionalCharges"
                                                                    value="<?php echo $selector_arr['Value_Of'] ?>">
                                                            </td>
                                                            <?php
																														$array_Details7 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
																														Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Value_Of
																														FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
																														WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
																														GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
																														Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Value_Of ORDER BY Tb_Recommender.V_id DESC");

																														$tot_index = 2;
																														while ($array_dy_Details7 = sqlsrv_fetch_array($array_Details7)) {

																															?>
                                                            <td><input type="text" readonly class="form-control valueof<?php echo $tot_index; ?>"
                                                                    name="Value_Of[]" id="totale2"
                                                                    title="Total + AdditionalCharges"
                                                                    value="<?php echo $array_dy_Details7['Value_Of'] ?>">
                                                            </td>
                                                            <?php $tot_index++; } ?>
                                                        </tr>
                                                        <tr id="eight">
                                                            <th>Delivery Time</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Delivery_Time[]"
                                                                    value="<?php echo $selector_arr['Delivery_Time1'] ?>">
                                                            </td>
                                                            <?php
															$array_Details8 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Delivery_Time1
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Delivery_Time1 ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details8 = sqlsrv_fetch_array($array_Details8)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Delivery_Time[]"
                                                                    value="<?php echo $array_dy_Details8['Delivery_Time1'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="nine">
                                                            <th>Fright Charges</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Fright_Charges[]"
                                                                    value="<?php echo $selector_arr['Fright_Charges'] ?>">
                                                            </td>
                                                            <?php
															$array_Details9 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Fright_Charges
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Fright_Charges ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details9 = sqlsrv_fetch_array($array_Details9)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Fright_Charges[]"
                                                                    value="<?php echo $array_dy_Details9['Fright_Charges'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="ten">
                                                            <th>Insurance Details</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Insurance_Details[]"
                                                                    value="<?php echo $selector_arr['Insurance_Details'] ?>">
                                                            </td>
                                                            <?php
															$array_Details10 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Insurance_Details
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Insurance_Details ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details10 = sqlsrv_fetch_array($array_Details10)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Insurance_Details[]"
                                                                    value="<?php echo $array_dy_Details10['Insurance_Details'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="eleven">
                                                            <th>GST Component</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="GST_Component[]"
                                                                    value="<?php echo $selector_arr['GST_Component'] ?>">
                                                            </td>
                                                            <?php
															$array_Details11 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.GST_Component
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.GST_Component ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details11 = sqlsrv_fetch_array($array_Details11)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="GST_Component[]"
                                                                    value="<?php echo $array_dy_Details11['GST_Component'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="twelve">
                                                            <th>Warrenty</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Warrenty[]"
                                                                    value="<?php echo $selector_arr['Warrenty'] ?>">
                                                            </td>
                                                            <?php
															$array_Details12 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Warrenty
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Warrenty ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details12 = sqlsrv_fetch_array($array_Details12)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Warrenty[]"
                                                                    value="<?php echo $array_dy_Details12['Warrenty'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="thirteen">
                                                            <th>Payment Terms</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Payment_Terms[]"
                                                                    value="<?php echo $selector_arr['Payment_Terms'] ?>">
                                                            </td>
                                                            <?php
															$array_Details13 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Payment_Terms
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Payment_Terms ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details13 = sqlsrv_fetch_array($array_Details13)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Payment_Terms[]"
                                                                    value="<?php echo $array_dy_Details13['Payment_Terms'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                    </tbody>
                                                    <div class="separator-solid"></div>

                                                    <tbody id="eone">
                                                        <tr id="tara" class="src-table">
                                                            <th>Requester's Selection
                                                            </th>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Requester_Selection[]"
                                                                    value="<?php echo $selector_arr['Requester_Selection'] ?>">
                                                            </td>
                                                            <?php
															$array_Details15 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Requester_Selection
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Requester_Selection ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details15 = sqlsrv_fetch_array($array_Details15)) {

																?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Requester_Selection[]"
                                                                    value="<?php echo $array_dy_Details15['Requester_Selection'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="text">
															<?php 
															$requester_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Purchaser_Code' ");
															$requester_names = sqlsrv_fetch_array($requester_name);
															$name = $requester_names['Employee_Name'];
															?>
                                                            <th>Requester's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span>
                                                            </th>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Requester_Remarks[]"
                                                                    value="<?php echo $selector_arr['Requester_Remarks'] ?>">
                                                            </td>
                                                            <?php
															$array_Details15 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Requester_Remarks
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Requester_Remarks ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details15 = sqlsrv_fetch_array($array_Details15)) {

																?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Requester_Remarks[]"
                                                                    value="<?php echo $array_dy_Details15['Requester_Remarks'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="tara" class="src-table">
                                                            <th>Recommender's Selection
                                                            </th>
                                                            <td class="woo">
                                                                <input type="text" class="form-control " readonly
                                                                    name="Recommender_Selection[]"
                                                                    value="<?php echo $selector_arr['Recommender_Selection'] ?>">
                                                            </td>
                                                            <?php
															$array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

																?>
                                                            <td class="woo">
                                                                <input type="text" class="form-control " readonly
                                                                    name="Recommender_Selection[]"
                                                                    value="<?php echo $array_dy_Details14['Recommender_Selection'] ?>">
                                                            </td>
                                                            <?php } ?>

                                                        </tr>
                                                        <tr>
														<?php 
															$recommender_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Recommender_Code' ");
															$recommender_names = sqlsrv_fetch_array($recommender_name);
															$name1 = $recommender_names['Employee_Name'];
															?>
                                                            <th>Recommender's Remarks<span
                                                                    class="required-label">&nbsp;&nbsp;(<?php echo $name1 ?>)</span>
                                                            </th>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Recommender_Remarks[]"
                                                                    value="<?php echo $selector_arr['Recommender_Remarks'] ?>">
                                                            </td>
                                                            <?php
															$array_Details121 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Remarks
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Remarks ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Recommender_Remarks[]"
                                                                    value="<?php echo $array_dy_Details121['Recommender_Remarks'] ?>">
                                                            </td>
                                                            <?php } ?>

                                                        </tr>



                                                        <?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Finance_verifier INNER JOIN Tb_Recommender ON Tb_Finance_verifier.Request_Id = Tb_Recommender.Request_id 
                                                        WHERE Tb_Finance_verifier.Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Remark'] == '' ){

                                                        }else{
                                                            ?>
                                                        <tr>
                                                            <th>Total Budget (in INR)<span
                                                                    class="required-label"></span>
                                                            </th>
                                                            <?php
															$array_Deta = sqlsrv_query($conn, "SELECT  * 
															FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'");
															$array_dy_Deta = sqlsrv_fetch_array($array_Deta)
															?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Total_Budget[]"
                                                                    value="<?php echo $array_dy_Deta['Total_Budget'] ?>">
                                                            </td>
                                                            <?php 
															$array_Details1212 = sqlsrv_query($conn, "SELECT  *
															FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1' 
															");
															while ($array_dy_Details1212 = sqlsrv_fetch_array($array_Details1212)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Total_Budget[]"
                                                                    value="<?php echo $array_dy_Details1212['Total_Budget'] ?>">
                                                            </td>
                                                            <?php } ?>

                                                        </tr>
                                                        <tr>
                                                            <th>Available Budget (in INR)<span
                                                                    class="required-label"></span>
                                                            </th>
                                                            <?php
															$array_Deta = sqlsrv_query($conn, "SELECT  * 
															FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'");
															$array_dy_Deta = sqlsrv_fetch_array($array_Deta)
															?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Available_Budget[]"
                                                                    value="<?php echo $array_dy_Deta['Available_Budget'] ?>">
                                                            </td>
                                                            <?php 
															$array_Details1212 = sqlsrv_query($conn, "SELECT  *
															FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1' 
															");
															while ($array_dy_Details1212 = sqlsrv_fetch_array($array_Details1212)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Available_Budget[]"
                                                                    value="<?php echo $array_dy_Details1212['Available_Budget'] ?>">
                                                            </td>
                                                            <?php } ?>

                                                        </tr>
                                                        <tr>
														<?php 
															$finance_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$verifier_code' ");
															$finance_names = sqlsrv_fetch_array($finance_name);
															$name2 = $finance_names['Employee_Name'];
															?>
                                                            <th>Financer's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name2 ?>)</span>
                                                            </th>
                                                            <?php
															$array_Deta = sqlsrv_query($conn, "SELECT  * 
															FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'");
															$array_dy_Deta = sqlsrv_fetch_array($array_Deta)
															?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Finance_Remarks[]"
                                                                    value="<?php echo $array_dy_Deta['Remark'] ?>">
                                                            </td>
                                                            <?php 
															$array_Details1212 = sqlsrv_query($conn, "SELECT  *
															FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1' 
															");
															while ($array_dy_Details1212 = sqlsrv_fetch_array($array_Details1212)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Finance_Remarks[]"
                                                                    value="<?php echo $array_dy_Details1212['Remark'] ?>">
                                                            </td>
                                                            <?php } ?>

                                                        </tr>
                                                        <?php
														}
														?>
                                                        <tr id="tara" class="src-table">
                                                            <th>Approver's Selection
                                                            </th>
                                                            <?php
															$array_Details142 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id  
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection ORDER BY Tb_Recommender.V_id DESC",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
															$vendor_count = sqlsrv_num_rows($array_Details142);

															$array_dy_Details142 = sqlsrv_fetch_array($array_Details142);
																?>

                                                            <td class="woo">
                                                                <select class="form-control request_selection"
                                                                    name="Approver_Selection[]" data-id="1">
                                                                    <option value="">Select Approver Selection
                                                                    </option>
                                                                    <?php
															$array_Details1421 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id  AND Tb_Recommender.Recommender_Selection = '1'
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection");

															$array_dy_Details1421 = sqlsrv_fetch_array($array_Details1421);
																?>
                                                                    <?php for($i = 1;$i<= $vendor_count;$i++) { ?>
                                                                    <option <?php if
                                                                        ($array_dy_Details1421['Recommender_Selection']==$i)
                                                                        { ?> selected="selected"
                                                                        <?php } ?> value="<?php echo $i; ?>">
                                                                        <?php echo $i; ?>
                                                                    </option>
                                                                    <?php } ?>

                                                                </select>
                                                            </td>
                                                            <?php
															$array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection ORDER BY Tb_Recommender.V_id DESC");
                             	$resindex = 2;
															while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

																?>

                                                            <td class="answer">
                                                                <?php
															$array_Details142 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id  
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection ORDER BY Tb_Recommender.V_id DESC",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
															$vendor_count = sqlsrv_num_rows($array_Details142);

															$array_dy_Details142 = sqlsrv_fetch_array($array_Details142);

																?>
                                                                <select class="form-control request_selection"
                                                                    name="Approver_Selection[]" data-id="<?php echo $resindex; ?>">
                                                                    <option value="">Select Approver Selection
                                                                    </option>
                                                                    <?php for($i = 1;$i<= $vendor_count;$i++) { ?>
                                                                    <option <?php if
                                                                        ($array_dy_Details14['Recommender_Selection']==$i)
                                                                        { ?> selected="selected"
                                                                        <?php } ?>value="<?php echo $i; ?>">
                                                                        <?php echo $i; ?>
                                                                    </option>
                                                                    <?php } ?>

                                                                </select>
                                                            </td>

                                                            <?php $resindex++; } ?>

                                                        </tr>
                                                        <tr class="remark">
                                                            <?php
															$array_Details11 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks ORDER BY Tb_Approver.V_id DESC");
															$array_dy_Details11 = sqlsrv_fetch_array($array_Details11);
															?>
                                                            <?php
															if($array_dy_Details11['Approver_Remarks'] == ''){
																?>
																<?php 
															$approver_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Approver_Code' ");
															$approver_names = sqlsrv_fetch_array($approver_name);
															$name3 = $approver_names['Employee_Name'];
															?>
                                                            <th>Approver's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name3 ?>)</span>
                                                            </th>
                                                            <?php
																	$array_Details11 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
																	Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Remarks
																	FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
																	WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id 
																	GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
																	Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Remarks ORDER BY Tb_Recommender.V_id DESC");
																	while ($array_dy_Details11 = sqlsrv_fetch_array($array_Details11)) {

																		?>
                                                            <td><input type="text" class="form-control "
                                                                    name="Approver_Remarks[]"
                                                                    placeholder="Enter Approver Remarks"></td>
                                                            <?php } ?>
                                                            <?php
															}else{
																?>
                                                            <?php 
															$approver_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Approver_Code' ");
															$approver_names = sqlsrv_fetch_array($approver_name);
															$name3 = $approver_names['Employee_Name'];
															?>
                                                            <th>Approver's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name3 ?>)</span>
                                                            </th>
                                                            <?php
															$array_Details11 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection  = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks ORDER BY Tb_Approver.V_id DESC");
															$array_dy_Details11 = sqlsrv_fetch_array($array_Details11);
															?>
                                                            <td><input type="text" class="form-control "
                                                                    name="Approver_Remarks[]"
                                                                    value="<?php echo $array_dy_Details11['Approver_Remarks'] ?>">
                                                            </td>
                                                            <?php
																$array_Details121 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
																Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks
																FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
																WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection  ! = '1' 
																GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
																Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks ORDER BY Tb_Approver.V_id DESC");
																while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

																	?>
                                                            <td><input type="text" class="form-control "
                                                                    name="Approver_Remarks[]"
                                                                    value="<?php echo $array_dy_Details121['Approver_Remarks'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                            <?php
															}
															?>

                                                        </tr>
                                                        <tr id="pdf">
                                                            <?php
																	$view16 = sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id' ");
																	$view_query16 = sqlsrv_fetch_array($view16);
															?>
                                                            <?php 
															if($view_query16['Attachment'] == ''){

															}else{
																?>
                                                            <th>PDF / JPG attachment
                                                            </th>
                                                            <?php
																	$view18 = sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id' ");
																	while ($view_query18 = sqlsrv_fetch_array($view18)) {
															?>
                                                            <td><input type="text" class="form-control" readonly
                                                                    value="<?php echo $view_query18['Attachment'] ?>"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target=".bs-example-modal-center1?php echo $view_query18['id'] ?>">
                                                                <div class="modal fade bs-example-modal-center1<?php echo $view_query18['id'] ?>"
                                                                    tabindex="-1" role="dialog"
                                                                    aria-labelledby="mySmallModalLabel"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title mt-0">Attachment
                                                                                </h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close">

                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <iframe
                                                                                    src="file/<?php echo $view_query18['Attachment'] ?>"
                                                                                    style="width: 100%;height: auto;"
                                                                                    frameborder="0"></iframe>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="btn btn-danger waves-effect waves-light btn-sm"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close">Close</button>
                                                                            </div>
                                                                        </div><!-- /.modal-content -->
                                                                    </div><!-- /.modal-dialog -->
                                                                </div><!-- /.modal -->
                                                            </td>
                                                            <?php 
																	}?>
                                                            <?php } ?>
                                                        </tr>

                                                    </tbody>

                                                </table>
                                                <table>
                                                    <tbody>
                                                        <tr>
														<?php
															$array_Details11 = sqlsrv_query($conn, "SELECT Reference_Empid,Remark_To_Reference,Reference_Remark
															FROM Tb_Approver 
															WHERE Request_id ='$request_id' 
															GROUP BY Reference_Empid,Remark_To_Reference,Reference_Remark");
															$array_dy_Details11 = sqlsrv_fetch_array($array_Details11);
															?>
                                                            <?php
															if($array_dy_Details11['Reference_Empid'] == ''){
																?>
																	<th>Additional Reference&nbsp;&nbsp;<input type="checkbox"
                                                                    	id="submit"></th>
																	<td class="Reference">
																		<select class="form-control kim select2"
																			name="Reference_emil">
																			<option selected disabled>Select Reference</option>
																			<?php
																				$plant = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table ");
																				while ($c = sqlsrv_fetch_array($plant)) {
																					?>
																			<option
																				value="<?php echo $c['Employee_Code'] ?>">
																				<?php echo $c['Employee_Name'] ?>&nbsp;-&nbsp;
																				<?php echo $c['Employee_Code'] ?>
																			</option>
																			<?php
																				}
																				?>
																		</select>
																	</td>
																	<td class="Reference">
																		<textarea rows="1" cols="40" class="form-control kim"
																			placeholder="Remarks For Reference"
																			name="Remark_To_Reference"></textarea>
																	</td>
																<?php
															}else{
																?>
																<th>Additional Reference</th>
															<td>Reference Employee
															<?php 
															$emp = $array_dy_Details11['Reference_Empid'];
															$aditional_emp = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$emp' ");
															$aditional_emps = sqlsrv_fetch_array($aditional_emp);
															$name4 = $aditional_emps['Employee_Name'];
															$name4_dep = $aditional_emps['Department'];
															?>
                                                            
                                                                <textarea rows="3" cols="40" class="form-control " 
                                                                    readonly=""
                                                                    name=""><?php echo $name4 ?>&nbsp;-&nbsp;<?php echo $name4_dep ?></textarea>
                                                            </td>
                                                            <td>Remark To Reference&nbsp;&nbsp;(<?php echo $name3 ?>)
                                                                <textarea rows="3" cols="40" class="form-control " 
                                                                    readonly=""
                                                                    name=""><?php echo $array_dy_Details11['Remark_To_Reference'] ?></textarea>
                                                            </td>
                                                            <td>Reference Remark&nbsp;&nbsp;(<?php echo $name4 ?>)
                                                                <textarea rows="3" cols="40" class="form-control "
																	readonly=""
                                                                    name=""><?php echo $array_dy_Details11['Reference_Remark'] ?></textarea>
                                                            </td>
																<?php
															}
															?>
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <center style="padding: 35px 0px 0px 0px;">
                                                <!-- Default -->
                                                <button class="btn btn-success mb-2 me-4 btn-sm one" type="submit"
                                                    name="save">
                                                    Submit
                                                </button>
												<button class="btn btn-info mb-2 me-4 btn-sm two" type="submit"
                                                    name="Reference">
                                                    Submit
                                                </button>
                                                <button class="btn btn-warning mb-2 me-4 btn-sm three" type="button"
                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-centered">
                                                    Send Back
                                                </button>
                                                <button class="btn btn-danger mb-2 me-4 btn-sm four" type="button"
                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-cente">
                                                    Reject
                                                </button>
                                                <?php 
														
														$view18 = sqlsrv_query($conn, "SELECT * FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial 
														ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id  WHERE Tb_Recommender.Request_id = '$request_id' ");
														$view_query18 = sqlsrv_fetch_array($view18);
												          if($view_query18['Finance_Verification'] == 'No' ){
                                                            ?>
                                                <button class="btn btn-danger mb-2 me-4 btn-sm five d-none" type="submit"
                                                    name="Verification">
                                                    Finance Verification
                                                </button <?php }else{ } ?>
                                            </center>
                                            <div class="modal fade bs-example-modal-centered" tabindex="-1"
                                                role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title mt-0">Remarks For SendBack</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close">

                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <textarea class="form-control" name="Approver_back_remark[]"
                                                                aria-label="With textarea"></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                class="btn btn-danger waves-effect waves-light btn-sm"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-success waves-effect waves-light btn-sm"
                                                                name="send" data-bs-dismiss="modal">Submit</button>
                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->
                                            <div class="modal fade bs-example-modal-cente" tabindex="-1" role="dialog"
                                                aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title mt-0">Remarks For Reject</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close">

                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <textarea class="form-control" name="Approver_Reject[]"
                                                                aria-label="With textarea"></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                class="btn btn-danger waves-effect waves-light btn-sm"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-success waves-effect waves-light btn-sm"
                                                                name="reject" data-bs-dismiss="modal">Submit</button>
                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->
                                        </form>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>


                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->



            <?php include('footer.php') ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>

    <script src="assets/libs/select2/js/select2.min.js"></script>
    <script src="assets/js/pages/form-advanced.init.js"></script>

    <script src="assets/js/app.js"></script>
    <!-- CUSTOM SCRIPT -->

    <script>
        $(document).ready(function () {
            $('.request_selection').change(function () {
                var myOpt = [];
                $(".request_selection").each(function () {
                    myOpt.push($(this).val());
                });
                $(".request_selection").each(function () {
                    $(this).find("option").attr('disabled', false);
                    var sel = $(this);
                    $.each(myOpt, function (key, value) {
                        if ((value != "New") && (value != sel.val())) {
                            sel.find("option").filter('[value="' + value + '"]').attr('disabled', 'disabled');
                        }
                    });
                });
            });

                var request_id    = $('#r_id').val();
                var po_creator_id = $('#po_creator_id').val();
                var quotation_value = $('.valueof1').val();

                $.ajax({
                    url: 'common_ajax.php',
                    type: 'POST',
                    data: { Action : 'get_emp_mapping_details',request_id : request_id,quotation_value : quotation_value,po_creator_id : po_creator_id },
                    dataType: "json",
                    success: function(response) {
                        $('#mapping_id').val(response[0].id);
                        $('#approver_id').val(response[0].Approver)
                        $('#approver2_id').val(response[0].Approver_2)

                    }
                });
        });
        // Recommender selection copy as and show hide//

        // $(".root").hide();
        // $(".answer").find('.request_selection').attr('disabled',false).attr("required", true);
        // $(document).on("click", "#submit", function () {
        //     if (this.checked) {

        //         $(".answer").hide();
        //         $(".remark").hide();
        //         $(".root").show();
        //         $(".answer").find('.request_selection').attr('disabled',true).attr("required", false);        

        //     } else {
        //         $(".answer").show();
        //         $(".remark").show();
        //         $(".root").hide();
        //         $(".answer").find('.request_selection').attr('disabled',false).attr("required", true);
        //         // $("input:not(:checked)").closest('.target-table').find(".woo").remove();
        //     }
        // }).change();


        $(".Reference").hide();
		$(".one").show();
		$(".two").hide();
		$(".three").show();
		$(".four").show();
        $(document).on("click", "#submit", function () {
            if (this.checked) {

                $(".Reference").show();
                $(".one").hide();
                $(".two").show();
                $(".three").hide();
                $(".four").hide();
                $(".five").hide();
                $(".Reference").find('.kim').attr("required", true);

            } else {
                $(".Reference").hide();
				$(".one").show();
                $(".two").hide();
                $(".three").show();
                $(".four").show();
                $(".Reference").find('.kim').attr("required", false);
                // $("input:not(:checked)").closest('.target-table').find(".woo").remove();
            }
        }).change();
        // vendor selection//disable Select//


        $('input[type=checkbox]').on("change", function () {
            var target = $(this).parent().find('input[type=hidden]').val();
            if (target == 0) {
                target = 1;
            } else {
                target = 0;
            }
            $(this).parent().find('input[type=hidden]').val(target);
        });

        var i = 1;
        $(document).ready(function () {
            $('#vendor-dropdown').on('change', function () {
                var VendorCode = $(this).val();
                var VendorCode_closet = $(this);
                $.ajax({
                    url: "getvendor.php",
                    dataType: 'json',
                    type: 'POST',
                    async: true,
                    data: {
                        VendorCode: VendorCode
                    },
                    success: function (data) {
                        $("#vendorname").val(data.VendorName);
                        $("#city").val(data.City);
                    }
                });
            });

            $('#vendor-dropdown1').on('change', function () {
                var VendorCode = $(this).val();
                var VendorCode_closet = $(this);
                $.ajax({
                    url: "getvendor.php",
                    dataType: 'json',
                    type: 'POST',
                    async: true,
                    data: {
                        VendorCode: VendorCode
                    },
                    success: function (data) {
                        $("#vendorname1").val(data.VendorName);
                        $("#city1").val(data.City);

                    }
                });
            });

            $('#vendor-dropdown2').on('change', function () {
                var VendorCode = $(this).val();
                var VendorCode_closet = $(this);
                $.ajax({
                    url: "getvendor.php",
                    dataType: 'json',
                    type: 'POST',
                    async: true,
                    data: {
                        VendorCode: VendorCode
                    },
                    success: function (data) {
                        $("#vendorname2").val(data.VendorName);
                        $("#city2").val(data.City);

                    }
                });
            });
            $('.vendors-dropdown').change(function () {
                var myOpt = [];
                $(".vendors-dropdown").each(function () {
                    myOpt.push($(this).val());
                });
                $(".vendors-dropdown").each(function () {
                    $(this).find("option").prop('hidden', false);
                    var sel = $(this);
                    $.each(myOpt, function (key, value) {
                        if ((value != "New") && (value != sel.val())) {
                            sel.find("option").filter('[value="' + value + '"]').prop(
                                'hidden', true);
                        }
                    });
                });
            });
        });

        //END//

        //disabled//

        $(document).on('change', '#vendor-dropdown', function () {
            // debugger
            var stateID = $(this).val();
            if (stateID == 'New')
                $("input.disabled").prop("readonly", false);
            else
                $("input.disabled").prop("readonly", true);

            if (stateID == 'New')
                $("input.dis").prop("readonly", true);
            else
                $("input.dis").prop("readonly", false);
        });

        $(document).on('change', '#vendor-dropdown1', function () {
            // debugger
            var stateID = $(this).val();
            if (stateID == 'New')
                $("input.disabled1").prop("readonly", false);
            else
                $("input.disabled1").prop("readonly", true);

            if (stateID == 'New')
                $("input.dis1").prop("readonly", true);
            else
                $("input.dis2").prop("readonly", false);
        });

        $(document).on('change', '#vendor-dropdown2', function () {
            // debugger
            var stateID = $(this).val();
            if (stateID == 'New')
                $("input.disabled2").prop("readonly", false);
            else
                $("input.disabled2").prop("readonly", true);

            if (stateID == 'New')
                $("input.dis1").prop("readonly", true);
            else
                $("input.dis2").prop("readonly", false);
        });

        //disabled end//

        //##########################//

        //SUM //

        $(document).ready(function () {
            update_amounts();
            $('.price').keyup(function () {
                update_amounts();
            });

            function update_amounts() {
                var sum = 0.0;
                $('#myTable1 > tbody  > tr').each(function () {
                    var qty = parseFloat($(this).find('.qty').val() || 0, 10);
                    var price = parseFloat($(this).find('.price').val() || 0, 10);
                    var amount = (qty * price)
                    sum += amount;
                    $(this).find('.amount').val('' + amount);
                });
                $('input.totale2').val(sum);
            }
        });

        $(document).ready(function () {
            update_amounts();
            $('.price1').keyup(function () {
                update_amounts();
            });

            function update_amounts() {
                var sum = 0.0;
                $('#myTable2 > tbody  > tr').each(function () {
                    var qty = parseFloat($(this).find('.qty1').val() || 0, 10);
                    var price = parseFloat($(this).find('.price1').val() || 0, 10);
                    var amount = (qty * price)
                    sum += amount;
                    $(this).find('.amount1').val('' + amount);
                });
                $('input.total1').val(sum);
            }
        });

        $(document).ready(function () {
            update_amounts();
            $('.price2').keyup(function () {
                update_amounts();
            });

            function update_amounts() {
                var sum = 0.0;
                $('#myTable3 > tbody  > tr').each(function () {
                    var qty = parseFloat($(this).find('.qty2').val() || 0, 10);
                    var price = parseFloat($(this).find('.price2').val() || 0, 10);
                    var amount = (qty * price)
                    sum += amount;
                    $(this).find('.amount2').val('' + amount);
                });
                $('input.total2').val(sum);
            }
        });

        //SUM end//
    </script>

    <script type="text/javascript">
        function myFunction() {
            var allRows = document.getElementById('busDataTable').rows;
            for (var i = 0; i < allRows.length; i++) {
                allRows[i].deleteCell(-1);
            }
        }

        var i = 4;

        // add column
        $('#remove').attr('disabled', true);

        $("#addColumn").click(function () {

            var myOpt = [];
            $(".vendors-dropdown").each(function () {
                myOpt.push($(this).val());
            });

            //SUM //
            $(document).ready(function () {
                $("tbody").on("input", ".form-control", function () {
                    const $parent = $(this).closest("#six")
                    var index = $(this).data('id');
                    update_amounts();
                    $('#price3' + index).keyup(function () {
                        update_amounts();
                    });

                    function update_amounts() {
                        var sum = 0.0;

                        $('#myTable4 > tbody  > tr').each(function () {
                            var qty = parseFloat($(this).find('#qty3' + index).val() || 0,
                                10);
                            var price = parseFloat($(this).find('#price3' + index).val() ||
                                0, 10);
                            var amount = (qty * price)
                            sum += amount;
                            $(this).find('#amount3' + index).val('' + amount);
                        });
                        $('input#total3' + index).val(sum);
                    }
                });
            });

            //SUM end//

            $('tbody').find('#head').append("<td>Vendor" + i +
                " <input type='hidden' class='form-control' value=Vendor " + i + "  name='V1_id[]' > </td>");
            $('tbody').find('#one').append(`<td><select class="form-control vendors-dropdown" name="Vendor_SAP[]" data-id="${i}"  id="vendors-dropdown${i}">
                            <option value="">Select Vendor SAP</option>
                            <option value="New">New Vendor</option>
                            <?php
                            $VendorCode = sqlsrv_query($conn, 'SELECT * FROM vendor_master');
                            while ($c = sqlsrv_fetch_array($VendorCode)) {
                                ?>
                                                <option value='<?php echo $c['VendorCode'] ?>'><?php echo $c['VendorCode'] ?></option>
                                                <?php
                            }
                            ?>
                        </select></td>`);

            $(document).ready(function () {
                $('.vendors-dropdown').on('change', function () {
                    var VendorCode = $(this).val();
                    var VendorCode_closet = $(this);
                    var index = $(this).data('id');
                    $.ajax({
                        url: "getvendor.php",
                        dataType: 'json',
                        type: 'POST',
                        async: true,
                        data: {
                            VendorCode: VendorCode
                        },
                        success: function (data) {
                            VendorCode_closet.closest('tbody').find("#vendornames" +
                                index).val(data.VendorName);
                            VendorCode_closet.closest('tbody').find('#citys' + index)
                                .val(data.City);
                        }

                    });
                });
                $('.vendors-dropdown').change(function () {
                    var myOpt = [];
                    $(".vendors-dropdown").each(function () {
                        myOpt.push($(this).val());
                    });
                    $(".vendors-dropdown").each(function () {
                        // debugger
                        var index = $(this).data('id');
                        $(this).find("option").prop('hidden', false);
                        var sel = $(this);
                        $.each(myOpt, function (key, value) {
                            if ((value != "New" + index) && (value != sel.val())) {
                                sel.find("option").filter('[value="' + value + '"]')
                                    .prop('hidden', true);
                            }
                        });
                    });
                });
                $(document).on('change', '.vendors-dropdown', function () {
                    var stateID = $(this).val();
                    var state = $(this).data('id');
                    if (stateID == 'New')
                        $("input.disabled" + state).prop("readonly", false);
                    else
                        $("input.disabled" + state).prop("readonly", true);

                    if (stateID == 'New')
                        $("input.dis" + state).prop("readonly", true);
                    else
                        $("input.dis" + state).prop("readonly", false);
                });

            });
            $('tbody').find('#two').append(
                `<td><input type='text' class='form-control disabled${i}'  name='Vendor_Name[]'  id='vendornames${i}'     placeholder='Enter Name of vendor'></td>`
            );
            $('tbody').find('#three').append(
                `<td><input type='text' class='form-control disabled${i}'  name='Vendor_City[]'  id='citys${i}'      placeholder='Enter City of vendor'></td>`
            );
            $('tbody').find('#four').append(
                `<td><input type='text' class='form-control dis${i}'  name='vendor_Active_SAP[]' placeholder='Enter vendor is active in SAP'></td>`
            );
            $('tbody').find('#five').append(
                `<td><input type='text' class='form-control dis${i}'  name='Last_Purchase[]' 	 placeholder='Enter Last Purchase'></td>`
            );
            $('tbody').find('#six').append(`<td><table id='myTable4'  class="table table-bordered" style="width: 235px !important;">
                                                                <tr>
                                                                    <thead>
                                                                        <th>Quantity</th>
                                                                        <th>Price</th>
                                                                        <th>Total</th>
                                                                    </thead>
                                                                </tr>
                                                                <tbody>
                                                                <?php
                                                                $result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$request_id'");
                                                                while ($row = sqlsrv_fetch_array($result)) {
                                                                    ?>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input type="text" class="form-control  qty3" readonly data-id="${i}" id='qty3${i}' name="Quantity_Details[]-<?php echo $row['ID'] ?>${i}" value="<?php echo $row['Quantity'] ?>" placeholder="Enter Quantity Details" > <input type="hidden" class="form-control" name="Meterial_Name[]" value="<?php echo $row['Item_Code'] ?>" >
                                                                                            <input type="hidden" class="form-control" name="V_id[]" value="Vendor ${i}" >
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" class="form-control  price3" data-id="${i}" id='price3${i}' name="Price[]-<?php echo $row['ID'] ?>${i}" value="" placeholder="Enter Price" >
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" class="form-control  amount3" readonly data-id="${i}" id='amount3${i}' name="Total[]-<?php echo $row['ID'] ?>${i}" value="" >
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table></td>`);
            $('tbody').find('#seven').append(
                `<td><input type='text' readonly class='form-control total3' name='Value_Of[]' id='total3${i}' data-id="${i}" placeholder='Enter Value of' ></td>`
            );
            $('tbody').find('#eight').append(
                "<td><input type='date' class='form-control'  name='Delivery_Time[]'  	 placeholder='Enter Quantity Details'></td>"
            );
            $('tbody').find('#nine').append(
                "<td><input type='text' class='form-control'  name='Fright_Charges[]'    placeholder='Enter Value of'></td>"
            );
            $('tbody').find('#ten').append(
                "<td><input type='text' class='form-control'  name='Insurance_Details[]' placeholder='Enter Fright Charges'></td>"
            );
            $('tbody').find('#eleven').append(
                "<td><input type='text' class='form-control'  name='GST_Component[]' 	 placeholder='Enter Insurance Details'></td>"
            );
            $('tbody').find('#twelve').append(
                "<td><input type='text' class='form-control'  name='Warrenty[]'     	 placeholder='Enter GST Component'></td>"
            );
            $('tbody').find('#thirteen').append(
                "<td><input type='text' class='form-control'  name='Payment_Terms[]'  	 placeholder='Enter Warrenty'></td>"
            );
            $('tbody').find('#checkbox').append(
                "<td><center><input type='checkbox' /><input type='hidden' name='Requester_Selection[]' value='0' /></center></td>"
            );
            $('tbody').find('#text').append(
                "<td><input type='text' class='form-control'  name='Requester_Remarks[]' placeholder='Enter Terms'></td>"
            );
            $('tbody').find('#pdf').append(
                "<td><input type='file' class='form-control'  name='Attachment[]'></td>");

            $('input[type=checkbox]').on("change", function () {
                var target = $(this).parent().find('input[type=hidden]').val();
                if (target == 0) {
                    target = 1;
                } else {
                    target = 0;
                }
                $(this).parent().find('input[type=hidden]').val(target);
            });
            // disable select 

            $(".vendors-dropdown").each(function () {
                var index = $(this).data('id');
                $(this).find("option").prop('hidden', false);
                var sel = $(this);
                $.each(myOpt, function (key, value) {
                    if ((value != "New") && (value != sel.val())) {
                        sel.find("option").filter('[value="' + value + '"]').prop('hidden', true);
                    }
                });
            });

            i = i + 1;
            $("input[type='file']").on("change", function () {
                if (this.files[0].size > 2000000) {
                    alert("Please upload file less than 2MB. Thanks!!");
                    $(this).val('');
                }
            });

            if (i > 1) {
                $('#remove').attr('disabled', false);
            }
        });

        // file size 

        $("input[type='file']").on("change", function () {
            if (this.files[0].size > 2000000) {
                alert("Please upload file less than 2MB. Thanks!!");
                $(this).val('');
            }
        });



        $(".Requester_Selection").change(function () {
            if (this.checked) {

                $('.Requester_Selection').val(1);
                //Do stuff
            } else {
                $('.Requester_Selection').val(0);

            }
        });


         $(document).on('change','.request_selection',function(){
                    var recommend_preference = $(this).val();
                    var row_id = $(this).data('id');
                    var requester_selection =  $('.requester_selection'+row_id).val();
                    var request_id    = $('#r_id').val();
                    var po_creator_id = $('#po_creator_id').val();
                    var quotation_value = $('.valueof'+row_id).val();
                    var current_mapping_id = $('#mapping_id').val();

                    if(recommend_preference == 1) {

                         $.ajax({
                            url: 'common_ajax.php',
                            type: 'POST',
                            data: { Action : 'get_emp_mapping_details',request_id : request_id,quotation_value : quotation_value,po_creator_id : po_creator_id },
                            dataType: "json",
                            success: function(response) {
                                if(recommend_preference != requester_selection) {
                                    var last_approver = (response[0].Approver_2 != '' && response[0].Approver_2 != null) ? response[0].Approver_2+' - '+response[0].approver2_name+' - '+response[0].approver2_Department : response[0].Approver+' - '+response[0].approver_name+' - '+response[0].approver_Department;

                                    if(current_mapping_id != response[0].id) {
                                        var msg = '';
                                        if(response[0].Value == '') {
                                            msg += "You have changed the request selection to within limit range.It will approved by ";
                                        } else if(response[0].Value != '') {
                                            msg += "You have changed the request selection to limit exceed range.It will approved by ";
                                        }
                                        alert(`${msg}`+last_approver);
                                        $('#mapping_id').val(response[0].id);
                                    }
                                }

                                $('#recommendor_id').val(response[0].Recommender);
                                $('#finance_verifier_id').val(response[0].Finance_Verfier);
                                $('#approver_id').val(response[0].Approver);
                                $('#approver2_id').val(response[0].Approver_2);
                            }
                        });     
                    }
            });
    </script>

    <!-- CUSTOM SCRIPT END -->

</body>

</html>