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
$request_id = $_GET['request_id'];

$Employee_Id = $_SESSION['EmpID'];

$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
WHERE Purchaser = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$verifier_name = $selector_arr1['Finance_Verfier'];
$Purchaser_Code = $selector_arr1['Purchaser'];
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];

if (isset($_POST["save"])) {
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


	$data_count = 0;
	foreach ($_POST['Vendor_SAP'] as $key => $sap_val) {
		if($sap_val != 'Select Vendor SAP') {
			$data_count++;
		}
	}

	//approval mapping id insertion
    $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set approval_mapping_id = '".$_POST['mapping_id']."' WHERE Request_id = '$request_id' ");

	// for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {

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
		// // $fil = $_FILES["Attachment"]["name"][$i];
		// $filename = basename($_FILES["Attachment"]["name"][$i]);
		// $extension = pathinfo($filename, PATHINFO_EXTENSION);
		// $fil = $filename.date('h:i:s').'.'.$extension;

		$V_id = $_POST['V1_id'][$i];
		$emp_id = $Employee_Id;
		// $Requested_to = $Recommender_Code;
		$Requested_to = $_POST['recommendor_id'];


		$total_amount    = $_POST['amt_tot'][$i];
		$discount_amount = $_POST['discount_amount'][$i];
		$package_amount = $_POST['package_amount'][$i];
		$package_percentage = $_POST['package_percentage'][$i];
		

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
		} 

		$vendor ="INSERT INTO Tb_Vendor_Selection(Request_Id, Vendor_SAP, Vendor_Name, Vendor_City,vendor_Active_SAP,
			Last_Purchase, Delivery_Time, Value_Of, Fright_Charges, Insurance_Details, GST_Component, Warrenty,
			Payment_Terms, Requester_Selection, Requester_Remarks, Attachment, Time_Log, status, V_id, EMP_ID,Requested_to,total_amount,discount_amount,package_amount,package_percentage) VALUES 
			('$request_id','$Vendor_SAP','$Vendor_Name','$Vendor_City','$vendor_Active_SAP','$Last_Purchase',
			'$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component','$Warrenty','$Payment_Terms','$Requester_Selection',
			'$Requester_Remarks','$fil',GETDATE(),'Added','$V_id','$emp_id','$Requested_to','$total_amount','$discount_amount','$package_amount','$package_percentage')";

	    $rs_vendor = sqlsrv_query($conn, $vendor);

		$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Added',Recommender = '$Requested_to',is_saved = '0' WHERE Request_Id = '$request_id' ");
		$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Added',Recommender = '$Requested_to' WHERE Request_Id = '$request_id' ");


		// Request move to approver level if the recommendor and verifier is empty  AND 
		// Request move to approver level if the recommendor and approver is same employee  
		if(($_POST['recommendor_id'] == '' && $_POST['finance_verifier_id'] == '') || ($_POST['recommendor_id'] == $_POST['approver_id'] && $_POST['finance_verifier_id'] == '')) {
			$Requested_to = $_POST['approver_id'];

		    $query = "INSERT INTO Tb_Recommender (Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time1,
	        Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
	        Recommender_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,Finance_Verification,EMP_ID,Requested_to,total_amount,discount_amount,package_amount,package_percentage)  
	        VALUES('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
	        '$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
	        '$Warrenty','$Payment_Terms','$Requester_Remarks','$Requester_Remarks',
	        '$fil',GETDATE(),'Recommended','$Requester_Selection','$Requester_Selection','$value','$emp_id','$Requested_to','$total_amount','$discount_amount','$package_amount','$package_percentage')";

	        $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Recommended',Approver = '".$_POST['approver_id']."' WHERE Request_Id = '$request_id'");
	        $query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Recommended',Approver = '".$_POST['approver_id']."' WHERE Request_Id = '$request_id'");

	        $rs = sqlsrv_query($conn, $query);
		}

			
	}


	

	$material_data_count = 0;
	foreach ($_POST['Price'] as $key => $Price_val) {
		if($Price_val != '') {
			$material_data_count++;
		}
	}

	// for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {
	for ($i = 0; $i < $material_data_count; $i++) {


		$Quantity_Details = $_POST['Quantity_Details'][$i];
		$Meterial_Name = $_POST['Meterial_Name'][$i];
		$Price = $_POST['Price'][$i];
		$Total = $_POST['Total'][$i];
		$V_id = $_POST['V_id'][$i];
		$Requested_to = $_POST['recommendor_id'];
		$gst_percentage      = $_POST['gst_percent'][$i];
		$discount_percentage = $_POST['discount_percent'][$i];

		$meterial = "INSERT INTO Tb_Vendor_Quantity(Request_Id, Meterial_Name, Quantity, status, Price, Total,  V_id, EMP_ID,Requested_to,gst_percentage,discount_percentage) VALUES 
 			('$request_id','$Meterial_Name','$Quantity_Details','Added','$Price','$Total','$V_id','$emp_id','$Requested_to','$gst_percentage','$discount_percentage')";

		$rs_material = sqlsrv_query($conn, $meterial);


		// Request move to approver level if the recommendor and verifier is empty  
		if(($_POST['recommendor_id'] == '' && $_POST['finance_verifier_id'] == '') || ($_POST['recommendor_id'] == $_POST['approver_id'] && $_POST['finance_verifier_id'] == '')) {
			$Requested_to = $_POST['approver_id'];

			$meterial = "INSERT INTO Tb_Recommender_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID,Requested_to,gst_percentage,discount_percentage) VALUES 
	 		('$request_id','$Meterial_Name','$Quantity_Details','$status','$Price','$Total','$V_id','$emp_id','$Requested_to','$gst_percentage','$discount_percentage')";

			$rs_material = sqlsrv_query($conn, $meterial);
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

		$update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$recommender_to'");
		$updated_query1 = sqlsrv_fetch_array($update_qry1);
		$To =  $updated_query1['Office_Email_Address'];
		// print_r($To);exit;

		$update_qry12 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'");
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
								<h4><span class="badge badge-success"><i class="fa fa-check"></i>Quotaion Added </span></h4>
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
				alert("Quotation added successsfully");
				window.location = "show_vendor_request.php";
			</script>
			<?php
		  }
}
?>
<!doctype html>
<html lang="en">

    <head>
        
        
        <meta charset="utf-8" />
        <title><?php echo $Title ?></title>
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
            overflow-x:scroll;
            overflow-y:visible;
            /* width:200px; */
            margin-left: 305px;
        }


        td, th {
            padding: 5px 20px;
            /* width: 100px; */
        }
        tbody tr {
        
        }
        th:first-child {
            position: absolute;
            left: 5px
        }
		input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

		.file_remove,.file_view {
			cursor: pointer;
			display: none;
		}
		.error_msg {
			display: none;
			font-size: 13px;
		}

		.preview_image,.preview_pdf {
			display: none;
		}

		.spinner-border {
			display: none;
		}
        </style>

    </head>

    
    <body data-keep-enlarged="true" class="vertical-collpsed" >
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
                                             <li class="breadcrumb-item"><a href="show_vendor_request.php">Show Vendor Request</a></li>
                                             <li class="breadcrumb-item active">Quotation For Request</li>
                                         </ol>
                                 </div>
                             </div>
                             <div class="col-sm-6">
                                <div class="float-end d-none d-sm-block">
								     <h4 class="">Request ID : <?php echo $request_id ?></h4>
								     <?php
								     $po_creator_sql = sqlsrv_query($conn, "SELECT TOP 1 Tb_Request.EMP_ID,Tb_Request.Plant,Tb_Request_Items.MaterialGroup from Tb_Request 
										left join Tb_Request_Items ON Tb_Request_Items.Request_ID = Tb_Request.Request_ID
										where Tb_Request.Request_ID = '$request_id'");
									  $po_creator = sqlsrv_fetch_array($po_creator_sql);

								     ?>
								     <input type="hidden" id="r_id" value="<?php echo $request_id; ?>">

                                    <button class="btn btn-primary btn-sm"  id="addColumn" >Add More</button>
                                    <button class="btn btn-danger btn-sm" id="remove" onclick="myFunction()" >Remove</button>
                                </div>
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
                                            <form method="POST" enctype="multipart/form-data" id="quotation_form">
			                                     <input type="hidden" id="mapping_id" name="mapping_id">
			                                     <input type="hidden" id="po_creator_id" value="<?php echo $po_creator['EMP_ID']; ?>">
											     <input type="hidden" name="recommendor_id" id="recommendor_id">
											     <input type="hidden" name="finance_verifier_id" id="finance_verifier_id">
											     <input type="hidden" name="approver_id" id="approver_id">
											     <input type="hidden" name="approver2_id" id="approver2_id">

			                                     <input type="hidden" id="po_plant" value="<?php echo $po_creator['Plant']; ?>">
			                                     <input type="hidden" id="po_mat_group" value="<?php echo $po_creator['MaterialGroup']; ?>">


											     <input type="hidden" id="req_id" name="req_id" value="<?php echo $request_id; ?>">

                                                <div class="table-wrapper">
                                                    <table id="busDataTable" class="data">
                                                        <tbody class="results" id="rone">
														<tr id="head">
															<th>Particulars</th>
															<td>Vendor 1<input type="hidden" class="form-control"
																	name="V1_id[]" value="Vendor 1"></td>
															<td>Vendor 2<input type="hidden" class="form-control"
																	name="V1_id[]" value="Vendor 2"></td>
															<td>Vendor 3<input type="hidden" class="form-control"
																	name="V1_id[]" value="Vendor 3"></td>
														</tr>
														<tr id="one">
															<th>Vendor SAP Code, if available</th>
														
															<td>
																<div class="d-flex">
																	<select class="form-control  s vendors" 
																		name="Vendor_SAP[]" id="vendor-dropdown" data-id="1" style="width: 100%;">
																
																		
																	</select>
																	<div class="spinner-border text-primary ms-3 vendor_spinner_loader" role="status">
																	  <span class="visually-hidden">Loading...</span>
																	</div>
																</div>
															</td>
															<td>
																<div class="d-flex">
																	<select class="form-control  s vendors"
																		name="Vendor_SAP[]" id="vendor-dropdown1" data-id="2"  style="width: 100%;">
																		
																	</select>
																	<div class="spinner-border text-primary ms-3 vendor_spinner_loader" role="status">
																	  <span class="visually-hidden">Loading...</span>
																	</div>
																</div>
															</td>
															<td>
																<div class="d-flex">
																	<select class="form-control  s vendors"
																		name="Vendor_SAP[]" id="vendor-dropdown2" data-id="3"  style="width: 100%;">
																	
																	</select>
																	<div class="spinner-border text-primary ms-3 vendor_spinner_loader" role="status">
																	  <span class="visually-hidden">Loading...</span>
																	</div>
																</div>
															</td>
														</tr>
														<tr id="two">
															<th>Name of vendor</th>
															<td><input type="text" class="form-control  disabled vendorname1" 
																	id="vendorname" name="Vendor_Name[]"
																	placeholder="Enter Name of vendor"></td>
															<td><input type="text" class="form-control  disabled1 vendorname2" 
																	id="vendorname1" name="Vendor_Name[]"
																	placeholder="Enter Name of vendor"></td>
															<td><input type="text" class="form-control  disabled2 vendorname3" 
																	id="vendorname2" name="Vendor_Name[]"
																	placeholder="Enter Name of vendor"></td>
														</tr>
														<tr id="three">
															<th>City of vendor</th>
															<td><input type="text" class="form-control  disabled city1"  
																	id="city" name="Vendor_City[]"
																	placeholder="Enter City of vendor"></td>
															<td><input type="text" class="form-control  disabled1 city2" 
																	id="city1" name="Vendor_City[]"
																	placeholder="Enter City of vendor"></td>
															<td><input type="text" class="form-control  disabled2 city3" 
																	id="city2" name="Vendor_City[]"
																	placeholder="Enter City of vendor"></td>
														</tr>
														<tr id="four">
															<th>Whether vendor is active in SAP?</th>
															<td><input type="text" class="form-control  disabled dis Active1" id="Active"  
																	name="vendor_Active_SAP[]"
																	placeholder="Enter vendor is active in SAP"></td>
															<td><input type="text" class="form-control  disabled1 dis1 Active2" id="Active1" 
																	name="vendor_Active_SAP[]"
																	placeholder="Enter vendor is active in SAP"></td>
															<td><input type="text" class="form-control  disabled2 dis2 Active3" id="Active2" 
																	name="vendor_Active_SAP[]"
																	placeholder="Enter vendor is active in SAP"></td>
														</tr>
														<tr id="five">
															<th>Last purchase made on</th>
															<td><input type="text" class="form-control  disabled dis Last_purchase1" id="Last_purchase"  
																	name="Last_Purchase[]"
																	placeholder="Enter Last purchase"></td>
															<td><input type="text" class="form-control  disabled1 dis1 Last_purchase2" id="Last_purchase1" 
																	name="Last_Purchase[]"
																	placeholder="Enter Last purchase"></td>
															<td><input type="text" class="form-control  disabled2 dis2 Last_purchase3" id="Last_purchase2" 
																	name="Last_Purchase[]"
																	placeholder="Enter Last purchase"></td>
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
																				<input type="hidden" id="mat_count" name="mat_count" value="<?php echo $item_count; ?>">
																				<td><input type="text" class="form-control-plaintext" readonly value="<?php echo trim($row['Description']) ?>-<?php echo $row['UOM'] ?>"
                                                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center<?php echo $ID ?>">
																						<!-- Modal -->
																						<div class="modal fade bs-example-modal-center<?php echo $ID ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                                            <div class="modal-dialog modal-dialog-centered">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header">
                                                                                                        <h5 class="modal-title mt-0">Last Purchace Date For Vendor</h5>
                                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                                                            
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div class="modal-body">
                                                                                                        <table class="table table-bordered" >
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <td>Vendor Code</td>
                                                                                                                    <th>Material Name</th>
                                                                                                                    <th>Price</th>
                                                                                                                    <th>Purchace Date</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                            <?php
                                                                                                                $result1 = sqlsrv_query($conn, "SELECT TOP 3 * FROM MIGO_DET WHERE  MATNR = '$ItemCode' ORDER BY LINE_ID DESC ");
                                                                                                                while ($row1 = sqlsrv_fetch_array($result1)) {
                                                                                                            ?>
                                                                                                                <tr>
                                                                                                                    <td><?php echo $row1['LIFNR'] ?></td>
                                                                                                                    <td><?php echo $row['Description'] ?></td>
                                                                                                                    <td><?php echo $row1['MENGE'] ?></td>
                                                                                                                    <td><?php echo $row1['BUDAT_MKPF']->format('Y-m-d') ?></td>
                                                                                                                </tr>
                                                                                                                <?php
                                                                                                                        }
                                                                                                                    ?>
                                                                                                            </tbody>
                                                                                                        </table>
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                        <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
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
																			<th>Discount(%)</th>
																			<th>GST(%)</th>
																			<th>Total</th>
																		</thead>
																	</tr>
																	<tbody>
																		<?php
																		$i = 1;
																		$result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$request_id'");
																		while ($row = sqlsrv_fetch_array($result)) {
																			?>
																			<tr>
																				<td>
																					<input type="text"
																						class="form-control  qty vendor1_qty_material<?php echo $i;?>" style="width: 75px; "
																						readonly name="Quantity_Details[]" 
																						value="<?php echo $row['Quantity'] ?>"
																						placeholder="Enter Quantity Details">
																					<input type="hidden"
																						class="form-control"
																						name="Meterial_Name[]"
																						value="<?php echo $row['Item_Code'] ?>">
																					<input type="hidden"
																						class="form-control" name="V_id[]"
																						value="Vendor 1">
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_price_1 price required_for_valid material_price vendor1_price_material<?php echo $i;?>" style="width: 75px;"
																						name="Price[]" data-rowid="1" value=""
																						placeholder="Enter Price" step=".01" readonly required error-msg='Price is mandatory.' data-materialindex="<?php echo $i;?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_discount_percent_1 discount_percent" style="width: 75px;"
																						name="discount_percent[]" 
																						placeholder="Enter Discount" step=".01"  required error-msg='Discount is mandatory.' data-rowid="1" readonly id="vendor1_discount_percentage_material<?php echo $i;?>" data-materialindex="<?php echo $i;?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_gst_percent_1 gst_percent " style="width: 75px;"
																						name="gst_percent[]" 
																						placeholder="Enter GST" step=".01"  required error-msg='GST is mandatory.' data-rowid="1" readonly id="vendor1_gst_percentage_material<?php echo $i;?>" data-materialindex="<?php echo $i;?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="hidden" class="vendor1_discount_reduced_total_material<?php echo $i;?> reduced_total">
																					<input type="text"
																						class="form-control  amount vendor1_total_material<?php echo $i;?>" style="width: 75px; " 
																						readonly name="Total[]" value="">
																				</td>
																			</tr>
																		<?php
																		$i++;}
																		?>
																	</tbody>
																</table>
															</td>
															<td>

																<table id="myTable2" class="table table-bordered"
																	style="width: 345px !important;">
																	<tr>
																		<thead>
																			<td>Quantity</td>
																			<th>Price</th>
																			<th>Discount(%)</th>
																			<th>GST(%)</th>
																			<th>Total</th>
																		</thead>
																	</tr>
																	<tbody>
																		<?php
																		$i = 1;
																		$material_count = 0;
																		$result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$request_id'");
																		while ($row = sqlsrv_fetch_array($result)) {
																			?>
																			<tr>
																				<td>
																					<input type="text"
																						class="form-control  qty1 vendor2_qty_material<?php echo $i;?>" style="width: 75px; "
																						readonly name="Quantity_Details[]"
																						value="<?php echo $row['Quantity'] ?>"
																						placeholder="Enter Quantity Details">
																					<input type="hidden"
																						class="form-control"
																						name="Meterial_Name[]"
																						value="<?php echo $row['Item_Code'] ?>">
																					<input type="hidden"
																						class="form-control" name="V_id[]"
																						value="Vendor 2">
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_price_2 price1 material_price vendor2_price_material<?php echo $i;?>" style="width: 75px;"
																						name="Price[]" data-rowid="2" value=""
																						placeholder="Enter price" step=".01" readonly required error-msg='Price is mandatory.' data-materialindex="<?php echo $i;?>">
																					<span class="text-danger"></span>
																				</td>

																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_discount_percent_2 discount_percent" style="width: 75px;"
																						name="discount_percent[]" 
																						placeholder="Enter Discount" step=".01"  required error-msg='Discount is mandatory.' data-rowid="2" readonly id="vendor2_discount_percentage_material<?php echo $i;?>" data-materialindex="<?php echo $i;?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_gst_percent_2 gst_percent" style="width: 75px;"
																						name="gst_percent[]" 
																						placeholder="Enter GST" step=".01"  required error-msg='Price is mandatory.' data-rowid="2" readonly id="vendor2_gst_percentage_material<?php echo $i;?>" data-materialindex="<?php echo $i;?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="hidden" class="vendor2_discount_reduced_total_material<?php echo $i;?> reduced_total">
																					<input type="text"
																						class="form-control  amount1 vendor2_total_material<?php echo $i;?>" style="width: 75px; " 
																						readonly name="Total[]" value="">
																				</td>
																			</tr>
																		<?php
																		$i++; $material_count++;}
																		?>
																	</tbody>
																</table>
															</td>
															<input type="hidden" id="material_request_count" value="<?php echo $material_count; ?>">
															<td>

																<table id="myTable3" class="table table-bordered"
																	style="width: 345px !important;">
																	<tr>
																		<thead>
																			<td>Quantity</td>
																			<th>Price</th>
																			<th>Discount(%)</th>
																			<th>GST(%)</th>
																			<th>Total</th>
																		</thead>
																	</tr>
																	<tbody>
																		<?php
																		$i = 1;
																		$result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$request_id'");
																		while ($row = sqlsrv_fetch_array($result)) {
																			?>
																			<tr>
																				<td>
																					<input type="text"
																						class="form-control  qty2 vendor3_qty_material<?php echo $i;?>"
																						readonly name="Quantity_Details[]" style="width: 75px; " 
																						value="<?php echo $row['Quantity'] ?>"
																						placeholder="Enter Quantity Details">
																					<input type="hidden"
																						class="form-control"
																						name="Meterial_Name[]"
																						value="<?php echo $row['Item_Code'] ?>">
																					<input type="hidden"
																						class="form-control" name="V_id[]"
																						value="Vendor 3">
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_price_3 price2 material_price vendor3_price_material<?php echo $i;?>" style="width: 75px;"
																						name="Price[]" data-rowid="3" data-materialindex="<?php echo $i;?>" value=""
																						placeholder="Enter price" step=".01" readonly required error-msg='Price is mandatory.'>
																					<span class="text-danger"></span>
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_discount_percent_3 discount_percent" style="width: 75px;"
																						name="discount_percent[]"
																						placeholder="Enter Discount" step=".01"  required error-msg='Discount is mandatory.' data-rowid="3" readonly id="vendor3_discount_percentage_material<?php echo $i;?>" data-materialindex="<?php echo $i;?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_gst_percent_3 gst_percent" style="width: 75px;"
																						name="gst_percent[]" 
																						placeholder="Enter GST" step=".01"  required error-msg='Price is mandatory.' data-rowid="3" readonly id="vendor3_gst_percentage_material<?php echo $i;?>" data-materialindex="<?php echo $i;?>">
																					<span class="error_msg text-danger"></span>
																				</td>

																				<td>
																					<input type="hidden" class="vendor3_discount_reduced_total_material<?php echo $i;?> reduced_total">
																					<input type="text"
																						class="form-control  amount2 vendor3_total_material<?php echo $i;?>" style="width: 75px; " 
																						readonly name="Total[]" value="">
																				</td>
																			</tr>
																		<?php
																		$i++;}
																		?>
																	</tbody>
																</table>
															</td>
														</tr>
														<tr id="seven1">
															<th>Total Price</th>
															<td>
																<input type="text" readonly class="form-control amt_tot1" 
																	name="amt_tot[]" id="amt_tot1" title="Total" readonly>
															</td>
															<td>
																<input type="text" readonly class="form-control amt_tot2" 
																	name="amt_tot[]" id="amt_tot2" title="Total" readonly>
															</td>
															<td>
																<input type="text" readonly class="form-control amt_tot3" 
																	name="amt_tot[]" id="amt_tot3" title="Total" readonly>
															</td>
														</tr>
														<tr id="nine">
															<th>Freight Charges</th>
															<td><input type="number" min="0" class="form-control  txtCal additonal_charges" 
																	name="Fright_Charges[]"
																	placeholder="Enter Freight Charges" id="freight_charge_1" data-id="1" readonly></td>
															<td><input type="number" min="0" class="form-control  txtCal1 additonal_charges" 
																	name="Fright_Charges[]"
																	placeholder="Enter Freight Charges"  id="freight_charge_2" data-id="2" readonly></td>
															<td><input type="number" min="0" class="form-control  txtCal2 additonal_charges"
																	name="Fright_Charges[]"
																	placeholder="Enter Freight Charges"  id="freight_charge_3" data-id="3" readonly></td>
														</tr>
														<tr id="ten">
															<th>Insurance Details</th>
															<td><input type="number" min="0" class="form-control  txtCal additonal_charges" 
																	name="Insurance_Details[]"
																	placeholder="Enter Insurance Details" id="insurance_amount_1" data-id="1" readonly></td>
															<td><input type="number" min="0" class="form-control  txtCal1 additonal_charges"
																	name="Insurance_Details[]"
																	placeholder="Enter Insurance Details" id="insurance_amount_2" data-id="2" readonly></td>
															<td><input type="number" min="0" class="form-control  txtCal2 additonal_charges" 
																	name="Insurance_Details[]"
																	placeholder="Enter Insurance Details" id="insurance_amount_3" data-id="3" readonly></td>
														</tr>
														<tr id="ten2">
															<th>Package Forwarding Percentage</th>
															<td><input type="number" min="0" class="form-control package_calc" 
																	name="package_percentage[]"
																	placeholder="Enter Packaging percentage" id="package_percentage_1" data-id="1" data-type="percent" readonly></td>
															<td><input type="number" min="0" class="form-control package_calc"
																	name="package_percentage[]"
																	placeholder="Enter Packaging percentage" id="package_percentage_2" data-id="2" data-type="percent" readonly></td>
															<td><input type="number" min="0" class="form-control package_calc" 
																	name="package_percentage[]"
																	placeholder="Enter Packaging percentage" id="package_percentage_3" data-id="3" data-type="percent" readonly></td>
														</tr>
														<tr id="ten1">
															<th>Package Forwarding Amount</th>
															<td><input type="number" min="0" class="form-control package_calc" 
																	name="package_amount[]"
																	placeholder="Enter Packaging Charges" id="package_amount_1" data-id="1" data-type="amount" readonly></td>
															<td><input type="number" min="0" class="form-control package_calc"
																	name="package_amount[]"
																	placeholder="Enter Packaging Charges" id="package_amount_2" data-id="2" data-type="amount" readonly></td>
															<td><input type="number" min="0" class="form-control package_calc" 
																	name="package_amount[]"
																	placeholder="Enter Packaging Charges" id="package_amount_3" data-id="3" data-type="amount" readonly></td>
														</tr>
														<tr id="eleven">
															<th>GST Component</th>
															<td><input type="number" min="0" class="form-control  txtCal"
																	name="GST_Component[]"
																	placeholder="Enter GST Component" id="gst_amount_1" readonly></td>
															<td><input type="number" min="0" class="form-control  txtCal1"
																	name="GST_Component[]"
																	placeholder="Enter GST Component" id="gst_amount_2" readonly></td>
															<td><input type="number" min="0" class="form-control  txtCal2"
																	name="GST_Component[]"
																	placeholder="Enter GST Component" id="gst_amount_3" readonly></td>
														</tr>
														<tr id="eleven1">
															<th>Discount Amount</th>
															<td><input type="number" min="0" class="form-control discount_charges"
																	name="discount_amount[]"
																	placeholder="Enter Discount Amount" id="discount_amount_1" readonly data-id="1"></td>
															<td><input type="number" min="0" class="form-control discount_charges"
																	name="discount_amount[]"
																	placeholder="Enter Discount Amount" id="discount_amount_2" readonly data-id="2"></td>
															<td><input type="number" min="0" class="form-control discount_charges"
																	name="discount_amount[]"
																	placeholder="Enter Discount Amount" id="discount_amount_3" readonly data-id="3"></td>
														</tr>
														<tr id="seven">
															<th>Net Amount</th>
															<td><input type="hidden" id="totCal"   onBlur="reSum();">
																<input type="hidden" id="old_totale2">
																<input type="text" readonly class="form-control  totale2 valueof1" 
																	name="Value_Of[]" id="totale2" title="Total + AdditionalCharges"
																	placeholder="Enter Value of">
															</td>
															<td><input type="hidden" id="totCal1"   onBlur="reSum();">
																<input type="hidden" id="old_total1">
																<input type="text" readonly class="form-control  total1 valueof2" 
																	name="Value_Of[]" id="total1" title="Total + AdditionalCharges"
																	placeholder="Enter Value of">
															</td>
															<td><input type="hidden" id="totCal2"   onBlur="reSum();">
																<input type="hidden" id="old_total2">
																<input type="text" readonly class="form-control  total2 valueof3" 
																	name="Value_Of[]" id="total2" title="Total + AdditionalCharges"
																	placeholder="Enter Value of">
															</td>
														</tr>
														<tr id="eight">
															<th>Delivery Time</th>
															<td><input type="date" class="form-control " min="<?php echo date("Y-m-d"); ?>"
																	name="Delivery_Time[]" value="<?php echo date('Y-m-d'); ?>"
																	placeholder="Enter Delivery Time"></td>
															<td><input type="date" class="form-control " min="<?php echo date("Y-m-d"); ?>"
																	name="Delivery_Time[]" value="<?php echo date('Y-m-d'); ?>"
																	placeholder="Enter Delivery Time"></td>
															<td><input type="date" class="form-control " min="<?php echo date("Y-m-d"); ?>"
																	name="Delivery_Time[]" value="<?php echo date('Y-m-d'); ?>"
																	placeholder="Enter Delivery Time"></td>
														</tr>

														<tr id="twelve">
															<th>Warrenty</th>
															<td><input type="text" class="form-control "
																	name="Warrenty[]" placeholder="Enter Warrenty"></td>
															<td><input type="text" class="form-control "
																	name="Warrenty[]" placeholder="Enter Warrenty"></td>
															<td><input type="text" class="form-control "
																	name="Warrenty[]" placeholder="Enter Warrenty"></td>
														</tr>
														<tr id="thirteen">
															<th>Payment Terms</th>
															<td>
																<!-- <input type="text" class="form-control "
																	name="Payment_Terms[]" placeholder="Enter Payment Terms"> -->
																<div>
																	<select class="form-control required_for_valid payment_terms_1 pay_terms" 
																		name="Payment_Terms[]" data-id="1" error-msg='Payment terms is mandatory.'>
																	</select>
																	<span class="error_msg text-danger"></span>
																	<div class="spinner-border text-primary ms-3 pay_term_loader" role="status">
																	  <span class="visually-hidden">Loading...</span>
																	</div>
																</div>
															</td>
															<td>
																<!-- <input type="text" class="form-control "
																	name="Payment_Terms[]" placeholder="Enter Payment Terms"> -->
																<div>
																	<select class="form-control  payment_terms_2 pay_terms" 
																		name="Payment_Terms[]" data-id="2" error-msg='Payment terms is mandatory.'>
																	
																	</select>
																	<span class="text-danger"></span>
																	<div class="spinner-border text-primary ms-3 pay_term_loader" role="status">
																	  <span class="visually-hidden">Loading...</span>
																	</div>
																</div>
															</td>
															<td>
																<div>
																	<select class="form-control  payment_terms_3 pay_terms" 
																		name="Payment_Terms[]" data-id="3" error-msg='Payment terms is mandatory.'>
																		
																	</select>
																	<span class="text-danger"></span>
																	<div class="spinner-border text-primary ms-3 pay_term_loader" role="status">
																	  <span class="visually-hidden">Loading...</span>
																	</div>
																</div>
																<!-- <input type="text" class="form-control "
																	name="Payment_Terms[]" placeholder="Enter Payment Terms"> -->
															</td>
														</tr>
													</tbody>
													<div class="separator-solid"></div>

													<tbody id="eone">
														<tr id="checkbox">
															<th>Requester's selection<span style="color:red">*</span></th>
															<td>
																<select class="form-control task request_selection required_for_valid request_selection_1"
																	name="Requester_Selection[]" required data-id="1" error-msg='Selection is mandatory.'>
																	<option value="">Select Requester Selection</option>
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																</select>
																<span class="error_msg text-danger"></span>
															</td>
															<td>
															<select class="form-control task request_selection request_selection_2"
																	name="Requester_Selection[]" required data-id="2" error-msg='Selection is mandatory.'>
																	<option value="">Select Requester Selection</option>
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																</select>
																<span class="text-danger"></span>
															</td>
															<td>
															<select class="form-control task request_selection request_selection_3"
																	name="Requester_Selection[]" id="diio" required data-id="3" error-msg='Selection is mandatory.'>
																	<option value="">Select Requester Selection</option>
																	<option value="1">1</option>
																	<option value="2">2</option>
																	<option value="3">3</option>
																</select>
																<span class="text-danger"></span>
															</td>
														</tr>
														<tr id="text">
														<?php 
															$requester_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Purchaser_Code' ");
															$requester_names = sqlsrv_fetch_array($requester_name);
															$name = $requester_names['Employee_Name'];
															?>
                                                            <th>Requester's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span></th>
															
															<td><input type="text" class="form-control "
																	name="Requester_Remarks[]"
																	placeholder="Enter Remark"></td>
															<td><input type="text" class="form-control "
																	name="Requester_Remarks[]"
																	placeholder="Enter Remark"></td>
															<td><input type="text" class="form-control "
																	name="Requester_Remarks[]"
																	placeholder="Enter Remark"></td>
														</tr>
														<tr id="pdf">
															<th>PDF / JPG attachment
															</th>
															<td>
																<div class="d-flex align-items-center">
																	<input class="form-control file-upload-input" type="file" name="Attachment[]" placeholder="" id="formFile" onchange="readURL(this)" data-id="1" accept="image/*,application/pdf">
																	<span class="ms-2 file_view" data-id="1"><i class="fa fa-eye text-primary"></i></span>
																	<span class="ms-2 file_remove"><i class="fa fa-window-close text-danger"></i></span>
																</div>


																<!-- file preview modal -->
																<div class="modal fade" id="file_preview_modal_1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
																  <div class="modal-dialog modal-lg">
																    <div class="modal-content">
																      <div class="modal-header">
																        <h1 class="modal-title fs-5" id="exampleModalLabel">File Preview</h1>
																        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																      </div>
																      <div class="modal-body">
																			<img class="preview_file_img_1 preview_image" src="#" alt="your image" width="100%"/>
																			 
																			 <iframe class="preview_file_pdf_1 preview_pdf" src="#"
                                                                                    style="width: 100%;height: 500px;"
                                                                                    frameborder="0">
                                                                              </iframe>
																      </div>
																      <div class="modal-footer">
																        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
																      </div>
																    </div>
																  </div>
																</div>
																<!-- file preview modal end -->

															</td>
															<td>
																<div class="d-flex align-items-center">
																	<input class="form-control file-upload-input" type="file" name="Attachment[]" placeholder="" id="formFile"  data-id="2" onchange="readURL(this)" accept="image/*,application/pdf">
																	<span class="ms-2 file_view" data-id="2"><i class="fa fa-eye text-primary"></i></span>
																	<span class="ms-2 file_remove"><i class="fa fa-window-close text-danger"></i><span>
																</div>


																<!-- file preview modal -->
																<div class="modal fade" id="file_preview_modal_2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
																  <div class="modal-dialog modal-lg">
																    <div class="modal-content">
																      <div class="modal-header">
																        <h1 class="modal-title fs-5" id="exampleModalLabel">File Preview</h1>
																        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																      </div>
																      <div class="modal-body">
																			<img class="preview_file_img_2 preview_image" src="#" alt="your image" width="100%"/>
																			 
																			 <iframe class="preview_file_pdf_2 preview_pdf" src="#"
                                                                                    style="width: 100%;height: 500px;"
                                                                                    frameborder="0">
                                                                              </iframe>
																      </div>
																      <div class="modal-footer">
																        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
																      </div>
																    </div>
																  </div>
																</div>
																<!-- file preview modal end -->
															</td>
															<td>
																<div class="d-flex align-items-center">
																	<input class="form-control file-upload-input" type="file" name="Attachment[]" placeholder="" id="formFile"  data-id="3" onchange="readURL(this)" accept="image/*,application/pdf">
																	<span class="ms-2 file_view" data-id="3"><i class="fa fa-eye text-primary"></i></span>
																	<span class="ms-2 file_remove"><i class="fa fa-window-close text-danger"></i><span>
																</div>


																<!-- file preview modal -->
																<div class="modal fade" id="file_preview_modal_3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
																  <div class="modal-dialog modal-lg">
																    <div class="modal-content">
																      <div class="modal-header">
																        <h1 class="modal-title fs-5" id="exampleModalLabel">File Preview</h1>
																        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																      </div>
																      <div class="modal-body">
																			<img class="preview_file_img_3 preview_image" src="#" alt="your image" width="100%"/>
																			 
																			 <iframe class="preview_file_pdf_3 preview_pdf" src="#"
                                                                                    style="width: 100%;height: 500px;"
                                                                                    frameborder="0">
                                                                              </iframe>
																      </div>
																      <div class="modal-footer">
																        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
																      </div>
																    </div>
																  </div>
																</div>
																<!-- file preview modal end -->
															</td>
														</tr>
													</tbody>
                                            
                                                    </table>
                                                </div>


                                                <div class="row" id="involved_persons_div" style="display:none;">
                                                    <div class="col-md-5">
                                                        <h4>Involved Persons</h4>
                                                        <table class="table table-striped table-bordered table-hover" >
                                                          <thead>
                                                            <tr>
                                                              <th>Purchaser</th>
                                                              <th>Recommender</th>
                                                              <th>Approver</th>
                                                            </tr>
                                                          </thead>
                                                          <tbody id="involved_persons_tbody">

                                                          </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-2 col-2 mx-auto" style="padding: 35px 0px 0px 0px;">
                                                	<input type="hidden" name="save">
                                                    <button class="btn btn-success mb-4 btn-sm" id="quotation_save" type="button" tabindex="-1" name="save_btn" >
                                                        <span class="btn-label">
                                                            <i class="fa fa-bookmark"></i>
                                                        </span>
                                                        Submit
                                                    </button>
                                                      <button class="btn btn-info mb-4 btn-sm" id="save" type="button" tabindex="-1" name="save_btn" >
                                                        <span class="btn-label">
                                                            <i class="fa fa-save"></i>
                                                        </span>
                                                        Save
                                                    </button>
                                                </div>
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

              	<!-- add more vendor count start from the 4 -->
                <input type="hidden" id="vendor_count" value="4">

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
       <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        
        <script>

		    function get_vendors_list() 
		    {
	    	     $.ajax({
	                  url: 'common_ajax.php',
	                  type: 'POST',
	                  data: { Action : 'get_vendors_list' },
	                  dataType: 'json',
                  	  beforeSend: function(){
                          $('.vendor_spinner_loader').show();
                      },
	                  success: function(response) {
	                  	let option = `<option selected >Select Vendor SAP</option>
									 <option value="New">New Vendor</option>`;
	                  	if(response.status == 200) {
	                      	for(i in response.data) {
	                      		option += `<option value="${response.data[i].VendorCode}">${response.data[i].VendorName} - ${response.data[i].VendorCode}</option>`;
	                      	}
	                  	}
	                  	$('.vendors').html(option);
	                  	$('.s').select2()
	                  },
	                  complete:function(){
                          $('.vendor_spinner_loader').hide();
	                  }
	              });	
		    }

		   	function get_payment_terms() 
		    {
	    	     $.ajax({
	                  url: 'common_ajax.php',
	                  type: 'POST',
	                  data: { Action : 'get_payment_terms' },
	                  dataType: 'json',
                  	  beforeSend: function(){
                          $('.pay_term_loader').show();
                      },
	                  success: function(response) {
	                  	let option = `<option selected >Select Payment Terms</option>`;
	                  	if(response.status == 200) {
	                      	for(i in response.data) {
	                      		option += `<option value="${response.data[i].Payment_Code}">${response.data[i].Payment_Code} - ${response.data[i].Payment_Name}</option>`;
	                      	}
	                  	}
	                  	$('.pay_terms').html(option);
	                  	$('.pay_terms').select2()
	                  },
	                  complete:function(){
                          $('.pay_term_loader').hide();
	                  }
	              });	
		    } 

            var i = 1;
            //VENDOR
            $(document).ready(function() {            	
				  $("input[type=number]").on("focus", function() {
				    $(this).on("keydown", function(event) {
				      if (event.keyCode === 38 || event.keyCode === 40) {
				        event.preventDefault();
				      }
				    });
				  });

				$('.preview_file_pdf_1').on('load', function() {
        			// vendor list load in select box
        			get_vendors_list();

        			// payment terms load in select box
        			get_payment_terms();
    			});  
			});

            $(document).ready(function () {
                $(".s").select2()
                $(".pay_terms").select2()

                $('#vendor-dropdown').on('change', function () {
                    var VendorCode = $(this).val();
                    var VendorCode_closet = $(this);
                    $.ajax({
                        url: "getvendor.php",
                        dataType: 'json',
                        type: 'POST',
                        async: true,
                        data: { VendorCode: VendorCode },
                        success: function (data) {
                            $("#vendorname").val(data.VendorName);
                            $("#city").val(data.City);
                            if(data.Active == '1' ){
                                $('#Active').val('Yes');
                            } else {
                                $('#Active').val('No');
                            };
                        }
                    });
                    $.ajax({
                        url: "getvendor1.php",
                        dataType: 'json',
                        type: 'POST',
                        async: true,
                        data: { VendorCode: VendorCode },
                        success: function (data) {
                            $("#Last_purchase").val(data.BUDAT_MKPF);
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
                        data: { VendorCode: VendorCode },
                        success: function (data) {
                            $("#vendorname1").val(data.VendorName);
                            $("#city1").val(data.City);
                            if(data.Active == '1' ){
                                $('#Active1').val('Yes');
                            } else {
                                $('#Active1').val('No');
                            };
                        }
                    });
                    $.ajax({
                        url: "getvendor1.php",
                        dataType: 'json',
                        type: 'POST',
                        async: true,
                        data: { VendorCode: VendorCode },
                        success: function (data) {
                            $("#Last_purchase1").val(data.BUDAT_MKPF);
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
                        data: { VendorCode: VendorCode },
                        success: function (data) {
                            $("#vendorname2").val(data.VendorName);
                            $("#city2").val(data.City);
                            if(data.Active == '1' ){
                                $('#Active2').val('Yes');
                            } else {
                                $('#Active2').val('No');
                            };
                        }
                    });
                    $.ajax({
                        url: "getvendor1.php",
                        dataType: 'json',
                        type: 'POST',
                        async: true,
                        data: { VendorCode: VendorCode },
                        success: function (data) {
                            $("#Last_purchase2").val(data.BUDAT_MKPF);
                        }
                    });
                });

                $('#one').on('change', '.s', function () {
                    // Get the selected options of all positions
                    var allSelected = $(".s").map(function () {
                        return $(this).val();
                    }).get();


                    // set all enabled
                    $(".s option").removeAttr("disabled");

                    // Disable selected options in other positions
                    $(".s option:not(:selected):not([value='New'])").each(function () {
                    	if($(this).val() != 'Select Vendor SAP') {
	                        if ($.inArray($(this).val(), allSelected) != -1) {
	                            $(this).attr('disabled', true);
	                        }
                    	}
                    });
                });
            });

		//END//
        $('#checkbox').on('change', '.request_selection', function () {
            var allSelected = $(".request_selection").map(function () {
                return $(this).val();
            }).get();

			// set all enabled
				$(".request_selection option").removeAttr("disabled");

			// Disable selected options in other positions
				$(".request_selection option:not(:selected):not([value='New'])").each(function () {
					if ($.inArray($(this).val(), allSelected) != -1) {
						$(this).attr('disabled', true);
					}
				});
		});

        //disabled//

		$(document).on('change', '#vendor-dropdown', function () {
			var stateID = $(this).val();
			if (stateID == 'New')
				$("input.disabled").prop("readonly", false);
			else
				$("input.disabled").prop("readonly", true);

			if (stateID == 'New')
				$("input.dis").prop("readonly", true);
			else
				$("input.dis").prop("readonly", true);
		});

		$(document).on('change', '#vendor-dropdown1', function () {
			var stateID = $(this).val();
			if (stateID == 'New')
				$("input.disabled1").prop("readonly", false);
			else
				$("input.disabled1").prop("readonly", true);

			if (stateID == 'New')
				$("input.dis1").prop("readonly", true);
			else
				$("input.dis2").prop("readonly", true);
		});

		$(document).on('change', '#vendor-dropdown2', function () {
			var stateID = $(this).val();
			if (stateID == 'New')
				$("input.disabled2").prop("readonly", false);
			else
				$("input.disabled2").prop("readonly", true);

			if (stateID == 'New')
				$("input.dis1").prop("readonly", true);
			else
				$("input.dis2").prop("readonly", true);
		});

		//disabled end//

        //SUM //

        $(document).on('keyup','.additonal_charges',function(){
        	var rowid = $(this).data('id');        	
        	var total_amount =$('.amt_tot'+rowid).val();
        	var gst_amount = $('#gst_amount_'+rowid).val() ||  0;
        	var freight_amount = $('#freight_charge_'+rowid).val() || 0;
        	var insurance_amount = $('#insurance_amount_'+rowid).val() || 0;
        	var package_amount = $('#package_amount_'+rowid).val() || 0;

        	var discount_amount = $('#discount_amount_'+rowid).val() || 0;

        	var net_amount = parseFloat(total_amount) + parseFloat(gst_amount) + parseFloat(freight_amount) + parseFloat(insurance_amount) + parseFloat(package_amount) - parseFloat(discount_amount);


			$('.valueof'+rowid).val(net_amount.toFixed(2))

		});

		$(document).on('keyup','.discount_charges',function(){
        	var rowid = $(this).data('id');        	
        	var total_amount =$('.amt_tot'+rowid).val();
        	var gst_amount = $('#gst_amount_'+rowid).val() ||  0;
        	var freight_amount = $('#freight_charge_'+rowid).val() || 0;
        	var insurance_amount = $('#insurance_amount_'+rowid).val() || 0;
        	var package_amount = $('#package_amount_'+rowid).val() || 0;

        	var discount_amount = $('#discount_amount_'+rowid).val() || 0;

        	if(parseFloat(discount_amount) > parseFloat(total_amount)) {
        		swal({
		          title: "Warning!",
		          text: "Dicount amount cannot be greater than total price amount.",
		          icon: "warning",
		        });	
        		$('#discount_amount_'+rowid).val(0)

        	} else if(parseFloat(discount_amount) > 0 && parseFloat(discount_amount) < parseFloat(total_amount)) {
	        	var net_amount = parseFloat(total_amount) + parseFloat(gst_amount) + parseFloat(freight_amount) + parseFloat(insurance_amount) + parseFloat(package_amount) - parseFloat(discount_amount);

				$('.valueof'+rowid).val(net_amount.toFixed(2));

				$('.vendor_discount_percent_'+rowid).each(function(){
					$(this).attr('readonly', true);
				});	


        	} else {
        		var net_amount = parseFloat(total_amount) + parseFloat(gst_amount) + parseFloat(freight_amount) + parseFloat(insurance_amount) + parseFloat(package_amount) - parseFloat(discount_amount);

				$('.valueof'+rowid).val(net_amount.toFixed(2));
        	}


		});


        $(document).on('keyup','.package_calc',function(){
        	var rowid = $(this).data('id');        	
        	var package_type = $(this).data('type');
        	var total_amount =$('.amt_tot'+rowid).val();
        	var gst_amount = $('#gst_amount_'+rowid).val() ||  0;
        	var freight_amount = $('#freight_charge_'+rowid).val() || 0;
        	var insurance_amount = $('#insurance_amount_'+rowid).val() || 0;
        	var package_amount = package_percent = 0;
        	

        	if(package_type == 'percent') {
        		package_percent = $(this).val() || 0;
        		package_amount = (package_percent > 0 && total_amount > 0) ? total_amount * $(this).val()/100 : 0;
        		package_amount = package_amount.toFixed(2);
        	} else if(package_type == 'amount') {
        		package_amount = $(this).val() || 0;
        		package_percent = (package_amount > 0 && total_amount > 0) ? (package_amount/total_amount) * 100 : 0;
        	}


        	if(parseInt(package_amount) > parseInt(total_amount) || (package_type == 'percent' && parseInt(package_percent) > parseInt(100))) {
        		$('#package_amount_'+rowid).val(0);
				$('#package_percentage_'+rowid).val(0);

				var message = "Package Amount cannot be greater than total amount.";
        		if(package_type == 'percent') {
        			message = "Package percent cannot be greater than 100";
        		}

			    update_net_amount(rowid);

        	  	swal({
		          title: "Warning!",
		          text: message,
		          icon: "warning",
		        });	

        	} else {
		       	var discount_amount = $('#discount_amount_'+rowid).val() || 0;

	        	var net_amount = parseFloat(total_amount) + parseFloat(gst_amount) + parseFloat(freight_amount) + parseFloat(insurance_amount) + parseFloat(package_amount) - parseFloat(discount_amount);

				$('.valueof'+rowid).val(net_amount.toFixed(2));
				console.log(package_percent);
				
				package_amount = (package_amount > 0) ? parseFloat(package_amount) : ''; 
				package_percent = (package_percent > 0) ? package_percent : ''; 	

	        	if(package_type == 'percent') {
					$('#package_amount_'+rowid).val(package_amount);
				} else if(package_type == 'amount') {
					$('#package_percentage_'+rowid).val(package_percent);
				}
        	}

		});

        // $(document).on('keyup','.txtCal',function(){
		// 	var  mat_count = $('#mat_count').val();
		// 	var tot = 0;
		// 	$(".txtCal").each(function () {
		// 		if($(this).val() != '') {
		// 			tot += parseFloat($(this).val()); 
		// 		}
		// 	});
		// 	var avg_value = (tot > 0) ? tot : 0;
		// 	$('#totCal').val(avg_value);
		// 	var mat_tot_value = $('#old_totale2').val();



		// 	var final_total = parseFloat(mat_tot_value) + parseFloat(avg_value); 

		// 	$('#totale2').val(final_total.toFixed(2))
        // });
        // $(document).on('keyup','.txtCal1',function(){
        //     var  mat_count = $('#mat_count').val();
        //     var tot = 0;
        //     $(".txtCal1").each(function () {
        //         if($(this).val() != '') {
        //             tot += parseFloat($(this).val()); 
        //         }
        //     });
        //     var avg_value = (tot > 0) ? tot : 0;
        //     $('#totCal1').val(avg_value);
        //     var mat_tot_value = $('#old_total1').val();
        //     var final_total = parseFloat(mat_tot_value) + parseFloat(avg_value); 
        //     $('#total1').val(final_total.toFixed(2))
        // });
        // $(document).on('keyup','.txtCal2',function(){
        //     var  mat_count = $('#mat_count').val();
        //     var tot = 0;
        //     $(".txtCal2").each(function () {
        //         if($(this).val() != '') {
        //             tot += parseFloat($(this).val()); 
        //         }
        //     });
        //     var avg_value = (tot > 0) ? tot : 0;
        //     $('#totCal2').val(avg_value);
        //     var mat_tot_value = $('#old_total2').val();
        //     var final_total = parseFloat(mat_tot_value) + parseFloat(avg_value); 
        //     $('#total2').val(final_total.toFixed(2))
        // });

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


					// update total value if the discount or gst having that row  
					var discount = $(this).find('.discount_percent').val();
					var gst      = $(this).find('.gst_percent').val();
					var vendor_row_id = $(this).find('.discount_percent').data('rowid');
					var material_row_id = $(this).find('.discount_percent').data('materialindex');


					if((discount != '' || discount != 0) || (gst != '' || gst != 0)) {
						update_material_tot_amount(vendor_row_id,material_row_id,discount,gst);
					}

				});
				$('input.totale2').val(sum);
				$('#old_totale2').val(sum);
				$('.amt_tot1').val(sum);
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

					// update total value if the discount or gst having that row  
					var discount = $(this).find('.discount_percent').val();
					var gst      = $(this).find('.gst_percent').val();
					var vendor_row_id = $(this).find('.discount_percent').data('rowid');
					var material_row_id = $(this).find('.discount_percent').data('materialindex');


					if((discount != '' || discount != 0) || (gst != '' || gst != 0)) {
						update_material_tot_amount(vendor_row_id,material_row_id,discount,gst);
					}
				});
				$('input.total1').val(sum);
				$('#old_total1').val(sum);
				$('.amt_tot2').val(sum);
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


					// update total value if the discount or gst having that row  
					var discount = $(this).find('.discount_percent').val();
					var gst      = $(this).find('.gst_percent').val();
					var vendor_row_id = $(this).find('.discount_percent').data('rowid');
					var material_row_id = $(this).find('.discount_percent').data('materialindex');


					if((discount != '' || discount != 0) || (gst != '' || gst != 0)) {
						update_material_tot_amount(vendor_row_id,material_row_id,discount,gst);
					}
				});
				$('input.total2').val(sum);
				$('#old_total2').val(sum);
				$('.amt_tot3').val(sum);
			}
		});

		//SUM end//
        </script>

        <script type="text/javascript">

                function myFunction() {
                    var allRows = document.getElementById('busDataTable').rows;
                    for (var i = 0; i < allRows.length; i++) {
                        allRows[i].deleteCell(4);
                    }

                    var old_vendor_count = $('#vendor_count').val();
                    var new_vendor_count = parseInt(old_vendor_count) - parseInt(1);
                    $('#vendor_count').val(new_vendor_count)
                }

                var i = 4;
                let vendor_count = $('#vendor_count').val();

                // add column
                $('#remove').attr('disabled', true);

                $("#addColumn").click(function () {

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
                                    var qty = parseFloat($(this).find('#qty3' + index).val() || 0, 10);
                                    var price = parseFloat($(this).find('#price3' + index).val() || 0, 10);
                                    var amount = (qty * price)
                                    sum += amount;
                                    $(this).find('#amount3' + index).val('' + amount);

                                    // update total value if the discount or gst having that row  
									var discount = $(this).find('.discount_percent').val();
									var gst      = $(this).find('.gst_percent').val();
									var vendor_row_id = $(this).find('.discount_percent').data('rowid');
									var material_row_id = $(this).find('.discount_percent').data('materialindex');


									if((discount != '' || discount != 0) || (gst != '' || gst != 0)) {
										update_material_tot_amount(vendor_row_id,material_row_id,discount,gst);
									}
                                });
                                $('input#total3' + index).val(sum);
                                $('#old_total3' + index).val(sum);
								$('.amt_tot'+ index).val(sum);
                            }
                        });                        
                    });

                    // $("tbody").on('keyup', '.form-control', function () {
                        
                    //     var index = $(this).data('id');
                    //     var  mat_count = $('#mat_count').val();
                    //     var tot = 0;
                    //     $(".txtCali"+ index).each(function () {
                    //         if($(this).val() != '') {
                    //             tot += parseInt($(this).val()); 
                    //         }
                    //     });
                    //     var avg_value = (tot > 0) ? tot : 0;
                    //     $('#totCal4'+ index).val(avg_value);
                    //     var mat_tot_value = $('#old_total3' + index ).val();
                    //     var final_total = parseInt(mat_tot_value) + parseInt(avg_value); 
                    //     $('#total3'+ index).val(final_total)

                    // });
                    //SUM end//
	
                    $('div').find('#head').append("<td>Vendor " + vendor_count + " <input type='hidden' class='form-control' value='Vendor " + i + " ' name='V1_id[]' > </td>");
                    $('div').find('#one').append(`<td><select class="form-control vendors vendors-dropdown datas_3 s" name="Vendor_SAP[]" data-id="${vendor_count}"  id="vendors-dropdown${vendor_count}">
                                <option selected>Select Vendor SAP</option>
                                <option value="New">New Vendor</option>
                                <?php
                                $VendorCode = sqlsrv_query($conn, "SELECT DISTINCT VendorCode,VendorName FROM vendor_master where VendorCode like 'ST%'");
                                while ($c = sqlsrv_fetch_array($VendorCode)) {
                                    ?>
                                        <option value='<?php echo $c['VendorCode'] ?>'><?php echo $c['VendorName'] ?>&nbsp;-&nbsp;<?php echo $c['VendorCode'] ?></option>
                                        <?php
                                }
                                ?>
                            </select></td>`);

                    $(document).ready(function () {
                        $(".s").select2()

            
                        $('.vendors-dropdown').on('change', function () {
                            var VendorCode = $(this).val();
                            var VendorCode_closet = $(this);
                            var index = $(this).data('id');
                            $.ajax({
                                url: "getvendor.php",
                                dataType: 'json',
                                type: 'POST',
                                async: true,
                                data: { VendorCode: VendorCode },
                                success: function (data) {
                                    VendorCode_closet.closest('tbody').find("#vendornames" + index).val(data.VendorName);
                                    VendorCode_closet.closest('tbody').find('#citys' + index).val(data.City);
                                    if(data.Active == '1' ){
                                        VendorCode_closet.closest('tbody').find('#Active' + index).val('Yes')
                                    } else {
                                        VendorCode_closet.closest('tbody').find('#Active' + index).val('No');
                                    };
                                }

                            });
                            $.ajax({
                                url: "getvendor1.php",
                                dataType: 'json',
                                type: 'POST',
                                async: true,
                                data: { VendorCode: VendorCode },
                                success: function (data) {
                                    VendorCode_closet.closest('tbody').find('#Last' + index).val(data.BUDAT_MKPF);
                                }
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
                                $("input.dis" + state).prop("readonly", true);
                        });



                    });
					$(document).ready(function() {
					  $("input[type=number]").on("focus", function() {
					    $(this).on("keydown", function(event) {
					      if (event.keyCode === 38 || event.keyCode === 40) {
					        event.preventDefault();
					      }
					    });
					  });
					});

                    $('div').find('#two').append(`<td><input type='text' class='form-control  disabled${vendor_count} vendorname${vendor_count}'   name='Vendor_Name[]'  id='vendornames${vendor_count}'     placeholder='Enter Name of vendor'></td>`);
                    $('div').find('#three').append(`<td><input type='text' class='form-control  disabled${vendor_count} city${vendor_count}'   name='Vendor_City[]'  id='citys${vendor_count}'      placeholder='Enter City of vendor'></td>`);
                    $('div').find('#four').append(`<td><input type='text' class='form-control  disabled${vendor_count}  dis${vendor_count} Active${vendor_count}' id="Active${vendor_count}"  name='vendor_Active_SAP[]' placeholder='Enter vendor is active in SAP'></td>`);
                    $('div').find('#five').append(`<td><input type='text' class='form-control  disabled${vendor_count}  dis${vendor_count} Last_purchase${vendor_count}' id='Last${vendor_count}' name='Last_Purchase[]' 	 placeholder='Enter Last Purchase'></td>`);
                    $('div').find('#six').append(`<td><table id='myTable4'  class="table table-bordered" style="width: 345px !important;">
                                                                    <tr>
                                                                        <thead>
                                                                            <td>Quantity</td>
                                                                            <th>Price</th>
                                                                            <th>GST(%)</th>
																			<th>Discount(%)</th>
                                                                            <th>Total</th>
                                                                        </thead>
                                                                    </tr>
                                                                    <tbody>
                                                                    <?php
                                                                    $mat_index = 1;
                                                                    $result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$request_id'");
                                                                    while ($row = sqlsrv_fetch_array($result)) {
                                                                        ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <input type="text" style="width: 75px; " class="form-control  qty3 vendor${vendor_count}_qty_material<?php echo $mat_index;?>"  readonly data-id="${vendor_count}" id='qty3${vendor_count}' name="Quantity_Details[]-<?php echo $row['ID'] ?>${vendor_count}" value="<?php echo $row['Quantity'] ?>" placeholder="Enter Quantity Details"> <input type="hidden" class="form-control" name="Meterial_Name[]" value="<?php echo $row['Item_Code'] ?>" >
                                                                                    <input type="hidden" class="form-control" name="V_id[]" value="Vendor ${vendor_count}" >
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" style="width: 75px;" min="0" class="form-control vendor_price_${vendor_count} price3 material_price vendor${vendor_count}_price_material<?php echo $mat_index;?>" data-id="${vendor_count}" id='price3${vendor_count}' name="Price[]-<?php echo $row['ID'] ?>${vendor_count}" value="" placeholder="Enter Price" step=".01" readonly error-msg='Price is mandatory.' data-rowid="${vendor_count}" data-materialindex="<?php echo $mat_index;?>">
																					<span class="text-danger"></span>
                                                                                </td>
                                                                                <td>
																					<input type="number" min="0"
																						class="form-control vendor_discount_percent_${vendor_count} discount_percent" style="width: 75px;"
																						name="discount_percent[]" 
																						placeholder="Enter Discount" step=".01"  required error-msg='Discount is mandatory.' data-rowid="${vendor_count}" readonly id="vendor${vendor_count}_discount_percentage_material<?php echo $mat_index;?>" data-materialindex="<?php echo $mat_index;?>">
																					<span class="error_msg text-danger"></span>
																				</td>
                                                                                <td>
																					<input type="number" min="0"
																						class="form-control vendor_gst_percent_${vendor_count} gst_percent" style="width: 75px;"
																						name="gst_percent[]" 
																						placeholder="Enter GST" step=".01"  required error-msg='Price is mandatory.' data-rowid="${vendor_count}" readonly id="vendor${vendor_count}_gst_percentage_material<?php echo $mat_index;?>" data-materialindex="<?php echo $mat_index;?>">
																					<span class="error_msg text-danger"></span>
																				</td>
                                                                                <td>
                                                                                	<input type="hidden" class="vendor${vendor_count}_discount_reduced_total_material<?php echo $mat_index;?> reduced_total">
                                                                                    <input type="text" style="width: 75px; " class="form-control  amount3 vendor${vendor_count}_total_material<?php echo $mat_index;?>"  readonly data-id="${vendor_count}" id='amount3${vendor_count}' name="Total[]-<?php echo $row['ID'] ?>${vendor_count}" value="" >
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                    $mat_index++;}
                                                                    ?>
                                                                    </tbody>
                                                                </table></td>`);
                    															

				   $('div').find('#seven1').append(`<td>
						<input type="text" readonly class="form-control amt_tot${vendor_count}" min='0' value="0" name="amt_tot[]" id="amt_tot${vendor_count}" title="Total"></td>`);

                    $('div').find('#eight').append(`<td><input type='date' class='form-control'  min="<?php echo date("Y-m-d"); ?>" name='Delivery_Time[]' id='' data-id="${vendor_count}" value="<?php echo date('Y-m-d'); ?>" 	 placeholder='Enter Delivery Time'></td>`);
                    $('div').find('#nine').append(`<td><input type='number' min='0' class='form-control  txtCali${vendor_count} additonal_charges'	data-id="${vendor_count}"  name='Fright_Charges[]'    placeholder='Enter Fright Charges' readonly id="freight_charge_${vendor_count}"></td>`);
                    $('div').find('#ten').append(`<td><input type='number' min='0' class='form-control  txtCali${vendor_count} additonal_charges' 	data-id="${vendor_count}"  name='Insurance_Details[]' placeholder='Enter Insurance Details' readonly id="insurance_amount_${vendor_count}"></td>`);

                    $('div').find('#ten1').append(`	<td><input type="number" min="0" class="form-control package_calc" 
						name="package_amount[]" placeholder="Enter Packaging Charges" readonly id="package_amount_${vendor_count}" data-id="${vendor_count}" data-type="amount"></td>`);
														
                    $('div').find('#ten2').append(`<td><input type="number" min="0" class="form-control package_calc" 
					name="package_percentage[]" placeholder="Enter Packaging percentage" readonly id="package_percentage_${vendor_count}" data-id="${vendor_count}" data-type="percent"></td>`);														
															
                    $('div').find('#eleven').append(`<td><input type='number' min='0' class='form-control  txtCali${vendor_count}' data-id="${vendor_count}"  name='GST_Component[]' id="gst_amount_${vendor_count}" readonly placeholder='Enter GST Component'></td>`);
													
														
					$('div').find('#eleven1').append(`<td><input type="number" min="0" class="form-control discount_charges" name="discount_amount[]"
							placeholder="Enter Discount Amount" id="discount_amount_${vendor_count}" readonly data-id="${vendor_count}"></td>`);

                    $('div').find('#seven').append(`<td><input type="hidden" id="totCal4${vendor_count}" data-id="${vendor_count}"  onBlur="reSum();">
                                                    <input type="hidden" id="old_total3${vendor_count}" >
                                                    <input type='number' min='0'  readonly class='form-control  total3${vendor_count} valueof${vendor_count}' name='Value_Of[]' id='total3${vendor_count}' data-id="${vendor_count}" placeholder='Enter Value of' ></td>`);
                    $('div').find('#twelve').append("<td><input type='text' class='form-control '  name='Warrenty[]'     	 placeholder='Enter Warrenty'></td>");
                    // $('div').find('#thirteen').append("<td><input type='text' class='form-control '  name='Payment_Terms[]'  	 placeholder='Enter Payment Terms'></td>");

                    $('div').find('#thirteen').append(`<td><select class="form-control pay_terms payment_terms_${vendor_count}" name="Payment_Terms[]" data-id="${vendor_count}" error-msg='Payment terms is mandatory.'>
																	<option  value="">Select Payment Terms</option>
																	<?php
																	$payment_terms = sqlsrv_query($conn, "select * from Payment_master_PO");
																	while ($payment_terms_row = sqlsrv_fetch_array($payment_terms)) {
																		?>
																		<option value="<?php echo $payment_terms_row['Payment_Code'] ?>">
																		<?php echo $payment_terms_row['Payment_Code'] ?>&nbsp;-&nbsp;<?php echo $payment_terms_row['Payment_Name'] ?>
																		</option>
																		<?php
																	}
																	?>
																</select>
																<span class="text-danger"></span>
																</td>`);


                    
                    $('div').find('#checkbox').append(`<td><select class="form-control request_selection request_selection_${vendor_count} task${vendor_count}"  
                                                                            name="Requester_Selection[]" required data-id="${vendor_count}" error-msg='Selection is mandatory.'>
                                                                            
                                                                        </select>
																<span class="text-danger"></span>
                                                                        </td>`);
                    $('div').find('#text').append("<td><input type='text' class='form-control '  name='Requester_Remarks[]' placeholder='Enter Remark'></td>");
                    // $('div').find('#pdf').append("<td><input class='form-control file-upload-input' type='file' name='Attachment[]' id='formFile'></td>");
            
                    $('div').find('#pdf').append(`<td><div class="d-flex align-items-center">
																	<input class="form-control file-upload-input" type="file" name="Attachment[]" placeholder="" id="formFile" data-id="${vendor_count}" onchange="readURL(this)"  accept="image/*,application/pdf">
																	<span class="ms-2 file_view" data-id="${vendor_count}"><i class="fa fa-eye text-primary"></i></span>
																	<span class="ms-2 file_remove"><i class="fa fa-window-close text-danger"></i><span>
																</div>
																<!-- file preview modal -->
																<div class="modal fade" id="file_preview_modal_${vendor_count}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
																  <div class="modal-dialog modal-lg">
																    <div class="modal-content">
																      <div class="modal-header">
																        <h1 class="modal-title fs-5" id="exampleModalLabel">File Preview</h1>
																        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																      </div>
																      <div class="modal-body">
																			<img class="preview_file_img_${vendor_count} preview_image" src="#" alt="your image" width="100%"/>
																			 
																			 <iframe class="preview_file_pdf_${vendor_count} preview_pdf" src="#"
                                                                                    style="width: 100%;height: 500px;"
                                                                                    frameborder="0">
                                                                              </iframe>
																      </div>
																      <div class="modal-footer">
																        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
																      </div>
																    </div>
																  </div>
																</div>
																<!-- file preview modal end -->
																</td>`);




                    var allSelected = $(".s").map(function () {
                            return $(this).val();
                        }).get();

                        // set all enabled
                        $(".s option").removeAttr("disabled");

                        // Disable selected options in other positions
                        $(".s option:not(:selected):not([value='New'])").each(function () {
                            if ($.inArray($(this).val(), allSelected) != -1) {
                                $(this).attr('disabled', true);
                            }
                        });



                    var option = '';
                    for(start = 0;start <= vendor_count;start++) {
                        text  = start;
                        if(start == 0) {
                            start = '';
                            text  = 'Select Requester Selection';
                        }
                        option += "<option value=" + start +">"+text+"</option>";
                    } 
                    $(".task"+vendor_count).append(option);
                    var option1 = '';
                    for(start = vendor_count;start <= vendor_count;start++) {
                        text  = start;
                        if(start == 0) {
                            start = '';
                            text  = 'Select Requester Selection';
                        }
                        option1 += "<option value=" + start +">"+text+"</option>";
                    } 
                    $(".task").append(option1);
                    var allSelected1 = $(".request_selection").map(function () {
                            return $(this).val();
                        }).get();

                        // set all enabled
                        $(".request_selection option").removeAttr("disabled");

                        // Disable selected options in other positions
                        $(".request_selection option:not(:selected):not([value='New'])").each(function () {
                            if ($.inArray($(this).val(), allSelected1) != -1) {
                                $(this).attr('disabled', true);
                            }
                        });
                    i = parseInt(i) + parseInt(1);
                    vendor_count = parseInt(vendor_count) + parseInt(1);
                    $("input[type='file']").on("change", function () {
                        if (this.files[0].size > 2000000) {
                            alert("Please upload file less than 2MB. Thanks!!");
                            $(this).val('');
                        }
                    });

                    if (i > 0) {
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
                	var request_preference = $(this).val();
                	var row_id = $(this).data('id');
                	var request_id    = $('#r_id').val();
                	var po_creator_id = $('#po_creator_id').val();

                	var quotation_value = $('.valueof'+row_id).val();

                	if(request_preference == 1) {
					     $.ajax({
							url: 'common_ajax.php',
							type: 'POST',
							data: { Action : 'get_emp_mapping_details',request_id : request_id,quotation_value : quotation_value,po_creator_id : po_creator_id },
							dataType: "json",
							success: function(response) {
								$('#recommendor_id').val(response[0].Recommender);
								$('#finance_verifier_id').val(response[0].Finance_Verfier);
								$('#approver_id').val(response[0].Approver);
								$('#approver2_id').val(response[0].Approver_2);
                                $('#mapping_id').val(response[0].id);
							}
						});	

						if(quotation_value > 0) {
							get_involved_persons(quotation_value);
						}	
                	}
                });


		        function get_involved_persons(value)
		        {
		        	var po_creator_id = $('#po_creator_id').val();
		        	var Plant = $('#po_plant').val();
		        	var MaterialGroup = $('#po_mat_group').val();
		        	var value = value;

		            $.ajax({
		                url: "common_ajax.php",
		                type: "POST",
		                data: {
		                    Plant : Plant,
		                    MaterialGroup : MaterialGroup,
		                    Action : 'get_involved_persons',
		                    po_creator_id : po_creator_id,
		                    value : value
		                },
		                cache: false,
		                dataType: 'json',                        
		                beforeSend:function(){
		                    // $('#ajax-preloader').show();
		                },
		                success: function (result) {
		                    var table_data = '';
		                    if(result.length > 0) {
		                        table_data = `<tr>
	                            <td>${ (result[0].purchaser_name != null) ? result[0].purchaser_name : '-' }</td>
	                            <td>${ (result[0].recommendor_name != null) ? result[0].recommendor_name : '-' }</td>
	                            <td>${ (result[0].approver_name != null) ? result[0].approver_name : '-' }</td>
		                        </tr>`; 

		                    }
		                    $('#involved_persons_tbody').html(table_data);  
		                    $('#involved_persons_div').show();                                  
		                },
		                complete:function(){
		                    // $('#ajax-preloader').hide();
		                }
		            });
		        }


		        $(document).on('change','.file-upload-input',function(){
		        	var image_value = $(this).val();
		        	if(image_value != '') {
		        		$(this).closest('div').find('.file_remove').show();
		        		$(this).closest('div').find('.file_view').show();
		        		
		        	} else {
		        		$(this).closest('div').find('.file_remove').hide();
		        		$(this).closest('div').find('.file_view').hide();
		        	}
		        });	

		        $(document).on('click','.file_remove',function(){
		        	$(this).closest('div').find('.file-upload-input').val('');
		        	$(this).hide();
		        });




		        $(document).on('change','.vendors',function(){
		        	var value = $(this).val();
		        	var row_id = $(this).data('id');

		        	if(value != '' && value != 'Select Vendor SAP') {
		        		$('.vendor_price_'+row_id).removeAttr('readonly');
		        		// $('.vendor_gst_percent_'+row_id).removeAttr('readonly');
		        		// $('.vendor_discount_percent_'+row_id).removeAttr('readonly');
 
		        		//price validation message add
		       			$('.vendor_price_'+row_id).addClass('required_for_valid');
		       			$('.vendor_price_'+row_id).closest('td').find('span').addClass('error_msg');

		       			// request selection validation 
		       			$('.request_selection_'+row_id).addClass('required_for_valid');
		       			$('.request_selection_'+row_id).closest('td').find('span').addClass('error_msg');

		       			// payment terms validation 
		       			// $('.payment_terms_'+row_id).addClass('required_for_valid');
		       			// $('.payment_terms_'+row_id).closest('td').find('span').addClass('error_msg');
		       			

		        	} else {
		        		$('.vendorname'+row_id).val("");
		        		$('.city'+row_id).val("");
		        		$('.Active'+row_id).val("");
		        		$('.Last_purchase'+row_id).val("");

		        		$('.vendor_price_'+row_id).attr('readonly',true);
		        		$('.vendor_gst_percent_'+row_id).attr('readonly',true);
		        		$('.vendor_discount_percent_'+row_id).attr('readonly',true);


		        		//price validation message remove
		       			$('.vendor_price_'+row_id).removeClass('required_for_valid');
		       			$('.vendor_price_'+row_id).closest('td').find('span').hide();
		       			$('.vendor_price_'+row_id).closest('td').find('span').removeClass('error_msg');

		       			// request selection validation 
		       			$('.request_selection_'+row_id).removeClass('required_for_valid');
		       			$('.request_selection_'+row_id).closest('td').find('span').hide();
		       			$('.request_selection_'+row_id).closest('td').find('span').removeClass('error_msg');


		       			// payment terms validation 
		       			// $('.payment_terms_'+row_id).removeClass('required_for_valid');
		       			// $('.payment_terms_'+row_id).closest('td').find('span').hide('error_msg');
		       			// $('.payment_terms_'+row_id).closest('td').find('span').removeClass('error_msg');


		        	}
		        });	



			    function readURL(input) {
			    	var row_id = $(input).data('id');
			    	
			        if (input.files && input.files[0]) {
			            var reader = new FileReader();
			            var extension = input.files[0].name.split('.').pop().toLowerCase();
			            
			            reader.onload = function (e) {
			                // $('#preview_image').attr('src', e.target.result);
			                if(extension == 'pdf') {
			                	$('.preview_file_pdf_'+row_id).attr('src', e.target.result+'#toolbar=0');
			                	$('.preview_file_img_'+row_id).hide();
			                	$('.preview_file_pdf_'+row_id).show();
			                } else {
			                	$('.preview_file_img_'+row_id).attr('src', e.target.result);
			                	$('.preview_file_pdf_'+row_id).hide();
			                	$('.preview_file_img_'+row_id).show();

			                }    

			            }

			            reader.readAsDataURL(input.files[0]);
			        }
			    }


				 function validation(){
				    var error_count=0;
				    $(".required_for_valid").each(function(){
				      var current_val=$(this).val();

				    var error_msg=$(this).attr("error-msg");
				    // var error_msg= "Field is mandatory.";
				    if(current_val == ''){
				      error_count++;
				      $(this).closest('td').find(".error_msg").html(error_msg).show();

				      // $(".error_msg").html(error_msg).show();

				    }else{
				      $(this).closest('td').find(".error_msg").html('').hide();

				      // $(".error_msg").html('').hide();
				    }
				    });
				    return error_count;
  				}


		        $(document).on('click','#quotation_save',function(e){
		        	e.preventDefault();
    				var error_count=validation();

					 if(error_count == 0) {
						$.ajax({
			                url: "common_ajax.php",
			                type: "POST",
			                data: {
			                    Action : 'checksession',
			                },
			                cache: false,
			                dataType: 'json',                        
			                success: function (result) {
			                	if(result.status == 200) {
						        	swal({
									  title: "Are you sure?",
									  text: "You want to add the quotation!",
									  icon: "warning",
									  buttons: true,
									  dangerMode: true,
									}).then((accept) => {
									  if (accept) {
						        		$('#quotation_form').submit();
					                    $('#ajax-preloader').show();
									  }
									});                             
			                	} else if(result.status == 419) {
						            swal({
			                          title: "Error!",
			                          text: result.message,
			                          icon: "warning",
			                        });
			                	}
			                }
			            });

					}
		        });


		        $(document).on('click','.file_view',function(e){
		        	var row = $(this).data('id'); 
		        	$('#file_preview_modal_'+row).modal('show');
		        });
		        

		        $(document).on('click','#save',function(){
                  var error_count = validation();

					if(error_count == 0) {
					  	$.ajax({
			                url: "common_ajax.php",
			                type: "POST",
			                data: {
			                    Action : 'checksession',
			                },
			                cache: false,
			                dataType: 'json',                        
			                success: function (result) {
			                	if(result.status == 200) {
						        	swal({
									  title: "Are you sure?",
									  text: "You want to save this quotation!",
									  icon: "warning",
									  buttons: true,
									  dangerMode: true,
									}).then((accept) => {
									  if (accept) {
									  	  //get form data
					                      var formdata = document.querySelector('#quotation_form');
					                      var form = new FormData(formdata);    
					                      form.append('Action', 'save_quotation');

					                      $('.file-upload-input').each(function(index){
					                        index++;
					                        form.append('Attachment'+index , $(this)[0].files[0]);
					                      });

					                      //save form data
					                      quotation_save(form);
									  }
									});                             
			                	} else if(result.status == 419) {
						            swal({
			                          title: "Error!",
			                          text: result.message,
			                          icon: "warning",
			                        });
			                	}
			                }
			            });

                  	}
            });

			    function quotation_save(form) 
			    {
		    	     $.ajax({
	                      url: 'common_ajax.php',
	                      type: 'POST',
	                      data: form,
	                      processData: false,  // Prevent jQuery from automatically transforming the data into a query string
	                      contentType: false,  // Prevent jQuery from setting the content type
	                      dataType: 'json',
	                      beforeSend: function(){
	                          $('#ajax-preloader').show();
	                      },
	                      success: function(response) {
	                          if(response.status == 200) {
	                              // alert(response.message);
		                      		swal({
									  title: "Success",
									  text: response.message,
									  icon: "success",
									  timer: 2000,
									  buttons: false,
									}).then(() => {
				                         window.location.href = 'show_vendor_request.php';
									});
	                          } else {
	                              // alert(response.message);
		                      		swal({
			                          title: "Error!",
			                          text: response.message,
			                          icon: "warning",
			                        });
	                          }
	                      },
	                      complete:function() {
	                            $('#ajax-preloader').hide();
	                      }
	                  });
			    } 


		        $(document).on('keyup','.gst_percent',function(){
			    	var rowid = $(this).data('rowid');
			    	var material_row_id = $(this).data('materialindex');
			    	var current_material_dicount_percent = $('#vendor'+rowid+'_discount_percentage_material'+material_row_id).val();
			    	var current_material_gst_percent = $(this).val();

					update_material_tot_amount(rowid,material_row_id,current_material_dicount_percent,current_material_gst_percent);

					update_gst_amount(rowid);

			    	update_net_amount(rowid);

			    });

			   	$(document).on('keyup','.discount_percent',function(){
			    	var rowid = $(this).data('rowid');
			    	var material_row_id = $(this).data('materialindex');

			    	var current_material_dicount_percent = $(this).val();
			    	var current_material_gst_percent     = $('#vendor'+rowid+'_gst_percentage_material'+material_row_id).val();

					update_material_tot_amount(rowid,material_row_id,current_material_dicount_percent,current_material_gst_percent);

			    	update_discount_amount(rowid);

			    	update_net_amount(rowid);


	        		var discount_percent = 0;
	        		$('.vendor_discount_percent_'+rowid).each(function(){
	        			discount_percent += $(this).val();
	        		});

	        		$('#discount_amount_'+rowid).attr('readonly',true);
	        		if(discount_percent <= 0) {
		        		$('#discount_amount_'+rowid).removeAttr('readonly');
	        		}


	        		// enable gst field after enter discount
					// if(current_material_dicount_percent > 0) {
						
				    // 	$('#vendor'+rowid+'_gst_percentage_material'+material_row_id).removeAttr('readonly');

					// }

			    });

		        
		        $(document).on('keyup','.material_price',function(){
			    	var rowid = $(this).data('rowid');
			    	var material_price =  $(this).val();
			    	var material_index = $(this).data('materialindex');

			    	var total_price = $('#amt_tot'+rowid).val();
			    	update_net_amount(rowid);

		    		$('#vendor'+rowid+'_gst_percentage_material'+material_index).attr('readonly',true);
	        		$('#vendor'+rowid+'_discount_percentage_material'+material_index).attr('readonly',true);


	        		$('#freight_charge_'+rowid).attr('readonly',true);
	        		$('#insurance_amount_'+rowid).attr('readonly',true);
	        		$('#package_percentage_'+rowid).attr('readonly',true);
	        		$('#package_amount_'+rowid).attr('readonly',true);
	        		$('#discount_amount_'+rowid).attr('readonly',true);

			    	if(total_price > 0) {
				    	$('#vendor'+rowid+'_gst_percentage_material'+material_index).removeAttr('readonly');

				    	if($('#discount_amount_'+rowid).val() == '' || $('#discount_amount_'+rowid).val() == 0) {
	        				$('#vendor'+rowid+'_discount_percentage_material'+material_index).removeAttr('readonly');
				    	} else {
	        				$('#discount_amount_'+rowid).removeAttr('readonly');
				    	}


	        			$('#freight_charge_'+rowid).removeAttr('readonly');
		        		$('#insurance_amount_'+rowid).removeAttr('readonly');
		        		$('#package_percentage_'+rowid).removeAttr('readonly');
		        		$('#package_amount_'+rowid).removeAttr('readonly');
			        	
		        		//discount entered percentage check
			        	var discount_percent = 0;
		        		$('.vendor_discount_percent_'+rowid).each(function(){
		        			discount_percent += $(this).val();
		        		});

		        		if(discount_percent <= 0) {
			        		$('#discount_amount_'+rowid).removeAttr('readonly');
		        		}
			    	}
		        });


			   	function update_gst_amount(rowid)
			    {
			    	var material_count = $('#material_request_count').val();

			    	var qty = gst_amount = price = gst_percent = 0;
			    	for(i=1 ; i <= material_count ; i++) {
			    		qty    = $(".vendor"+rowid+"_qty_material"+i).val();

			    		// console.log(qty);
			    		
			    		if(qty > 0) {
							// price  = qty * $(".vendor"+rowid+"_price_material"+i).val();
							price  = $(".vendor"+rowid+"_discount_reduced_total_material"+i).val();
			    		}

			    		// console.log(price);


						gst_percent  = $("#vendor"+rowid+"_gst_percentage_material"+i).val();

			    		// console.log(gst_percent);


						if(price > 0 && gst_percent > 0) {
			    			gst_amount += parseFloat(price) * (parseFloat(gst_percent)/100);
						}
			    	}

			    	$('#gst_amount_'+rowid).val(gst_amount);
			    }


			    function update_discount_amount(rowid)
			    {
			    	var material_count = $('#material_request_count').val();

			    	var qty = discount_amount = price = discount_percent = 0;
			    	for(i=1 ; i <= material_count ; i++) {
			    		qty    = $(".vendor"+rowid+"_qty_material"+i).val();

			    		if(qty > 0) {
							price  = qty * $(".vendor"+rowid+"_price_material"+i).val();
			    		}

						discount_percent  = $("#vendor"+rowid+"_discount_percentage_material"+i).val();

						if(price > 0 && discount_percent > 0) {
			    			discount_amount += parseFloat(price) * (parseFloat(discount_percent)/100);
						}
			    	}

			    	$('#discount_amount_'+rowid).val(discount_amount);
			    }


		        function update_net_amount(rowid)
		        {
		        	// var material_count = $('#material_request_count').val();

		        	// var total_amount = 0;
		        	// for(var mi=1;mi <= material_count;mi++) {
		        	// 	var reduced_total = $('.vendor'+rowid+'_total_material'+mi).val();
		        	// 	total_amount = parseInt(total_amount) + parseInt(reduced_total);
		        	// }

		        	// // console.log(total_amount);


		        	var total_amount =$('.amt_tot'+rowid).val();
		        	var gst_amount = $('#gst_amount_'+rowid).val() ||  0;
		        	var freight_amount = $('#freight_charge_'+rowid).val() || 0;
		        	var insurance_amount = $('#insurance_amount_'+rowid).val() || 0;
		        	var discount_amount = $('#discount_amount_'+rowid).val() || 0;
        			var package_amount = $('#package_amount_'+rowid).val() || 0;


		        	var net_amount = parseFloat(total_amount) + parseFloat(gst_amount) + parseFloat(freight_amount) + parseFloat(insurance_amount) + parseFloat(package_amount) - parseFloat(discount_amount);
		        	$('.valueof'+rowid).val(net_amount.toFixed(2));

		        }

		      	function update_material_tot_amount(vendor_rowid,material_row_id,discount_percentage,gst_percentage = '')
		        {
		        	var qty =$('.vendor'+vendor_rowid+'_qty_material'+material_row_id).val();
		        	var price =$('.vendor'+vendor_rowid+'_price_material'+material_row_id).val();
		        	var total_base_amount = parseInt(qty) * parseFloat(price);

		        	if(discount_percentage > 0) {
			        	var discount_amount   = parseFloat(total_base_amount) * parseFloat(discount_percentage)/100; 
			        	var final_total = parseFloat(total_base_amount) - parseFloat(discount_amount);
	
			        	// discount reduced total update 
			        	$('.vendor'+vendor_rowid+'_discount_reduced_total_material'+material_row_id).val(final_total);

			        	if(gst_percentage > 0) {
			        		var price      = $('.vendor'+vendor_rowid+'_discount_reduced_total_material'+material_row_id).val(); 

							var gst_amount = parseFloat(price) * parseFloat(gst_percentage)/100;  
							final_total    = parseFloat(final_total) + parseFloat(gst_amount);		

			        	}
			        	$('.vendor'+vendor_rowid+'_total_material'+material_row_id).val(final_total);

		        	} else if((discount_percentage == '' || discount_percentage == 0) && gst_percentage > 0) {
						var gst_amount = parseFloat(total_base_amount) * parseFloat(gst_percentage)/100;  
			        	var final_total = parseFloat(total_base_amount) + parseFloat(gst_amount);


			        	$('.vendor'+vendor_rowid+'_discount_reduced_total_material'+material_row_id).val(total_base_amount);

			        	$('.vendor'+vendor_rowid+'_total_material'+material_row_id).val(final_total);

		        	} else {
			        	$('.vendor'+vendor_rowid+'_discount_reduced_total_material'+material_row_id).val(total_base_amount);

			        	$('.vendor'+vendor_rowid+'_total_material'+material_row_id).val(total_base_amount);		        		
		        	}

		        }

		        // function isNumberKey(evt,element) {
				//     var charCode = (evt.which) ? evt.which : event.keyCode
				//     if (            
				//         (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
				//         (charCode < 48 || charCode > 57))
				//         return false;
				//         return true;
        		// }

	  			$('input[type="number"]').on('keydown', function(event) {
	  				// return isNumberKey(evt,this); 
	                   // this.value = this.value.replace(/[^0-9.]/g, ''); // Allow digits and decimal points
	  				if (event.key === 'E' || event.key === 'e' || event.key === '-') {
       				 event.preventDefault(); // Prevent these keys
    				}
    			});
				
        </script>
        <!-- CUSTOM SCRIPT END -->

    </body>
</html>
