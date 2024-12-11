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

$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Approver
WHERE Reference_Empid	 = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);
$ARRY =  $selector_arr1['EMP_ID'];

$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
WHERE Approver = '$ARRY' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$Approver_Code = $selector_arr1['Approver'];
$verifier_name = $selector_arr1['Finance_Verfier'];
$verifier_code = $selector_arr1['Finance_Verfier'];
$Purchaser_Code = $selector_arr1['Purchaser'];
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];



$vendor_data_count_sql =  "SELECT COUNT(*) as data_count from Tb_Vendor_Selection where Request_Id = '" . $request_id . "'";

$vendor_data_count_exec = sqlsrv_query($conn, $vendor_data_count_sql);
$vendor_data_count = sqlsrv_fetch_array($vendor_data_count_exec)['data_count'];

$table_width = '100%';
if ($vendor_data_count == 1) {
	$table_width = "100px";
}


if (isset($_POST["Reference"])) {


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
			// $Approver_Remarks = $_POST['Approver_Remarks'][$i];
			// $Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$status = 'Recommended';
			// $Reference_emil = $_POST['Reference_emil'];
			$Reference_Remark = $_POST['Reference_Remark'];
            // $Verification_Type = '2';

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = '$status',Reference_Remark = '$Reference_Remark'
			  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");

            // $rs = sqlsrv_query($conn, $query);
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
// $ecode =  $_POST['Reference_emil'];
$update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Approver_Code'");
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

$to = explode(',', $To);
$to = array('jr_developer4@mazenetsolution.com', 'sathish.r@rasiseeds.com');

foreach ($to as $address) {
	// $mail->AddAddress($address);
	$mail->AddAddress(trim($address));
}
$array = "$Cc,$implode";

$cc = explode(',', $array);
// print_r($cc);exit;
foreach ($cc as $ccc) {
	// $mail->AddAddress($address);
	$mail->addCC(trim($ccc));
}
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
						<h4><span class="badge badge-success"><i class="fa fa-check"></i>Recommended</span></h4>
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
    // exit;
	?>
<script type="text/javascript">
    alert("Reference send successsfully");
    window.location = "show_reference_purchase_request.php";
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

       <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


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

		.file_view {
			cursor: pointer;
		}

		.display_section {
			cursor: pointer;
		}

		.preview_icon {
			display: none;
			position: absolute;
		    top: 23%;
		    left: 42%;
		    font-size: 20px;
		}

		.material-tr {
			height: 83px;
		}

		.material-tr > td {
			height: auto;
    		vertical-align: middle;
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
										<input type="hidden" id="r_id" value="<?php echo $request_id; ?>">
									</h4>

									<?php
									$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Request
									WHERE Request_ID = '$request_id' ");
									$selector_arr1 = sqlsrv_fetch_array($selector1);

									$po_creator_sql = sqlsrv_query($conn, "SELECT TOP 1 Tb_Request.EMP_ID,Tb_Request.Plant,Tb_Request_Items.MaterialGroup from Tb_Request 
                      left join Tb_Request_Items ON Tb_Request_Items.Request_ID = Tb_Request.Request_ID
                      where Tb_Request.Request_ID = '$request_id'");
									$po_creator = sqlsrv_fetch_array($po_creator_sql);
									?>
									<?php
									if ($selector_arr1['Reference'] == '') {
									?>

									<?php
									} else {
									?>
										<!-- <h4 class="">Reference ID : <?php //echo $selector_arr1['Reference'] 
																			?></h4> -->
										<button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl" data-reference-id="<?php echo $selector_arr1['Reference'] ?>">
											View Reference
										</button>

										<!-- Modal HTML -->
										<div class="modal fade bs-example-modal-xl" tabindex="-1" aria-labelledby="myLargeModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-xl">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title">Table Reference</h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
													</div>
													<div class="modal-body">
														<div class="table-responsive">
															<table id="datatable" class="items table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
																<thead>
																	<tr>
																		<th>#</th>
																		<th>Request ID</th>
																		<th>Vendor Name</th>
																		<th>Vendor SAP</th>
																		<th>Vendor Active SAP</th>
																		<th>Last Purchase</th>
																		<th>Delivery Time</th>
																		<th>Quantity</th>
																		<th>Price</th>
																		<th>Total</th>
																		<th>Total Amount</th>
																		<th>Freight Charges</th>
																		<th>Insurance Details</th>
																		<th>GST Component</th>
																		<th>Warranty</th>
																		<th>Payment Terms</th>
																		<th>Total Budget Finance</th>
																		<th>Available Budget Finance</th>
																		<th>Verification Type Finance</th>
																		<th>Finance Remarks</th>
																		<th>Requester Remarks</th>
																		<th>Recommender Remarks</th>
																		<th>Approver Remarks</th>
																	</tr>
																</thead>
																<tbody class='item-row'>
																	<!-- Table rows go here -->
																</tbody>
															</table>
														</div>
													</div>
													<div class="modal-footer">
														<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
													</div>
												</div>
											</div>
										</div>


									<?php
									}
									?>
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
                                            <?php 
                                                $plant_sql = "SELECT Plant_Name FROM Plant_Master_PO WHERE Plant_Code = '".$po_creator['Plant']."'";
                                                $plant_sql_exec =  sqlsrv_query($conn,$plant_sql);
                                                $plant_detail = sqlsrv_fetch_array($plant_sql_exec);

                                            ?>
                                            <h1 class="badge bg-success" style="font-size: 15px;">Plant Details - <span><?php echo $po_creator['Plant']; ?> (<?php echo $plant_detail['Plant_Name']; ?>)</span></h1>
                                            

										<form method="POST" enctype="multipart/form-data">
											<input type="hidden" id="request_id" name="request_id" value="<?php echo $request_id; ?>">
											<input type="hidden" id="po_creator_id" name="po_creator_id" value="<?php echo $po_creator['EMP_ID']; ?>">
											<input type="hidden" id="po_plant" name="po_plant" value="<?php echo $po_creator['Plant']; ?>">
											<input type="hidden" id="po_mat_group" name="po_mat_group" value="<?php echo $po_creator['MaterialGroup']; ?>">
											<input type="hidden" id="quoatation_value" name="quoatation_value" value="<?php echo $request_id; ?>">
											<input type="hidden" name="approver_id" id="approver_id">
											<input type="hidden" name="approver2_id" id="approver2_id">

											<div class="table-wrapper">
												<table id="busDataTable" class="data heading-table" style="width: <?php echo $table_width; ?>;">
													<?php
													$selector = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.V_id,Tb_Recommender.Vendor_SAP,Tb_Recommender.Vendor_Name,Tb_Recommender.Vendor_City,Tb_Recommender.vendor_Active_SAP,
                                                        Tb_Recommender.Last_Purchase,Tb_Recommender.Delivery_Time1,Tb_Recommender.Value_Of,Tb_Recommender.Fright_Charges,Tb_Recommender.Insurance_Details,Tb_Recommender.GST_Component,
                                                        Tb_Recommender.Warrenty,Tb_Recommender.Payment_Terms,Tb_Recommender.Requester_Remarks,Tb_Recommender.Recommender_Remarks,Tb_Recommender.Attachment,
                                                        Tb_Recommender.Status,Tb_Recommender.Requester_Selection,Tb_Recommender.Recommender_Selection,Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                                        Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total,
                                                        Payment_master_PO.Payment_Name,Tb_Recommender.total_amount,Tb_Recommender.discount_amount,Tb_Recommender.package_amount,Tb_Recommender.package_percentage
                                                        FROM Tb_Recommender 
                                                        LEFT JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id
                                                        and Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id
																												LEFT JOIN Payment_master_PO ON Payment_master_PO.Payment_Code = Tb_Recommender.Payment_Terms 
                                                        WHERE Tb_Recommender.Request_id = '$request_id' and Recommender_Selection = '1'
                                                        GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,Tb_Recommender.Vendor_SAP,Tb_Recommender.Vendor_Name,Tb_Recommender.Vendor_City,Tb_Recommender.vendor_Active_SAP,
                                                        Tb_Recommender.Last_Purchase,Tb_Recommender.Delivery_Time1,Tb_Recommender.Value_Of,Tb_Recommender.Fright_Charges,Tb_Recommender.Insurance_Details,Tb_Recommender.GST_Component,
                                                        Tb_Recommender.Warrenty,Tb_Recommender.Payment_Terms,Tb_Recommender.Requester_Remarks,Tb_Recommender.Recommender_Remarks,Tb_Recommender.Attachment,
                                                        Tb_Recommender.Status,Tb_Recommender.Requester_Selection,Tb_Recommender.Recommender_Selection,Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                                        Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total,Payment_master_PO.Payment_Name,Tb_Recommender.total_amount,Tb_Recommender.discount_amount,Tb_Recommender.package_amount,Tb_Recommender.package_percentage");
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
																		$result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$request_id'", [], array("Scrollable" => SQLSRV_CURSOR_KEYSET));
																		$item_count = sqlsrv_num_rows($result);
																		while ($row = sqlsrv_fetch_array($result)) {
																			$ID = $row['ID'];
																			$ItemCode = $row['Item_Code'];
																			// print_r($ID);
																		?>
																			<tr>
																				<td>
																					<div>
																						<span class="badge badge-soft-primary"><?php echo substr(trim($ItemCode),-10); ?></span>
																						<span class="badge badge-soft-danger ms-3"><?php echo trim($row['UOM']); ?></span>

																					</div>																					
																					<input type="text"
																						class="form-control-plaintext"
																						readonly
																						value="<?php echo trim($row['Description']) ?>"
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
																			<th>GST(%)</th>
																			<th>Discount(%)</th>
																			<th>Total</th>
																		</thead>
																	</tr>
																	<tbody>
																		<?php
																		$mat_ind = 1;
																		$result = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
																																			Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
																																			Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total,Tb_Recommender_Meterial.gst_percentage,Tb_Recommender_Meterial.discount_percentage
																																			FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
																																			WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection  = '1' 
																																			GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
																																			Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
																																			Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total,Tb_Recommender_Meterial.gst_percentage,Tb_Recommender_Meterial.discount_percentage ORDER BY Tb_Recommender.V_id DESC");
																		$i = 0;
																		while ($row = sqlsrv_fetch_array($result)) {
																		?>
																			<tr class="material-tr">
																				<td>
																					<input type="text"
																						class="form-control qty" style="width: 75px;" readonly
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
																					<input type="text" style="width: 75px;" readonly
																						class="form-control price"
																						name="Price[]"
																						value="<?php echo $row['Price'] ?>">
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_gst_percent_1 gst_percent" style="width: 75px;"
																						name="gst_percent[]"
																						placeholder="Enter GST" step=".01" required error-msg='Price is mandatory.' data-rowid="1" readonly id="vendor1_gst_percentage_material<?php echo $mat_ind; ?>" value="<?php echo $row['gst_percentage'] ?>">
																					<span class="error_msg text-danger"></span>
																				</td>

																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_discount_percent_1 discount_percent" style="width: 75px;"
																						name="discount_percent[]"
																						placeholder="Enter Discount" step=".01" required error-msg='Discount is mandatory.' data-rowid="1" readonly id="vendor1_discount_percentage_material<?php echo $mat_ind; ?>" value="<?php echo $row['discount_percentage'] ?>">
																					<span class="error_msg text-danger"></span>
																				</td>

																				<td>
																					<input type="text"
																						class="form-control amount" style="width: 75px;" readonly
																						name="Total[]"
																						value="<?php echo $row['Total'] ?>">
																				</td>
																			</tr>
																		<?php $mat_ind++;
																		} ?>
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
															$vendor_ind = 2;
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
																				<th>GST(%)</th>
																				<th>Discount(%)</th>
																				<th>Total</th>
																			</thead>
																		</tr>
																		<tbody>
																			<?php
																			$mat_ind = 1;
																			$array_Details1111 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
																																			Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
																																			Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total,Tb_Recommender_Meterial.gst_percentage,Tb_Recommender_Meterial.discount_percentage
																																			FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
																																			WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = '$arr' AND Tb_Recommender_Meterial.V_id = '$arr'
																																			AND Tb_Recommender.Recommender_Selection ! = '1' 
																																			GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
																																			Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
																																			Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total,Tb_Recommender_Meterial.gst_percentage,Tb_Recommender_Meterial.discount_percentage ORDER BY Tb_Recommender.V_id DESC");
																			while ($array_dy_Details1111 = sqlsrv_fetch_array($array_Details1111)) {

																			?>
																				<tr class="material-tr">
																					<td>
																						<input type="text"
																							class="form-control qty" style="width: 75px;" readonly
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
																						<input type="text" style="width: 75px;" readonly
																							class="form-control price"
																							name="Price[]"
																							value="<?php echo $array_dy_Details1111['Price'] ?>">
																					</td>
																					<td>
																						<input type="number" min="0"
																							class="form-control vendor_gst_percent_1 gst_percent" style="width: 75px;"
																							name="gst_percent[]"
																							placeholder="Enter GST" step=".01" required error-msg='Price is mandatory.' data-rowid="1" readonly id="vendor<?php echo $vendor_ind; ?>_gst_percentage_material<?php echo $mat_ind; ?>" value="<?php echo $array_dy_Details1111['gst_percentage'] ?>">
																						<span class="error_msg text-danger"></span>
																					</td>

																					<td>
																						<input type="number" min="0"
																							class="form-control vendor_discount_percent_1 discount_percent" style="width: 75px;"
																							name="discount_percent[]"
																							placeholder="Enter Discount" step=".01" required error-msg='Discount is mandatory.' data-rowid="1" readonly id="vendor<?php echo $vendor_ind; ?>_discount_percentage_material<?php echo $mat_ind; ?>" value="<?php echo $array_dy_Details1111['discount_percentage'] ?>">
																						<span class="error_msg text-danger"></span>
																					</td>
																					<td>
																						<input type="text"
																							class="form-control amount" style="width: 75px;" readonly
																							name="Total[]"
																							value="<?php echo $array_dy_Details1111['Total'] ?>">
																					</td>
																				</tr>
																			<?php $mat_ind++;
																			} ?>
																		</tbody>
																	</table>
																</td>
															<?php $vendor_ind++;
															} ?>
														</tr>
														<tr id="seven1">
															<th>Total</th>
															<td>
																<input type="text" readonly class="form-control amt_tot1"
																	name="amt_tot[]" id="amt_tot1" title="Total" value="<?php echo $selector_arr['total_amount'] ?>">
															</td>
															<?php
															foreach ($charges_arr as $key => $value) {
															?>
																<td>
																	<input type="text" readonly class="form-control amt_tot<?php echo $key + 2; ?>"
																		name="amt_tot[]" id="amt_tot<?php echo $key + 2; ?>" title="Total" value="<?php echo $value['total_amount'] ?>">
																</td>
															<?php } ?>
														</tr>
														<tr id="nine">
															<th>Freight Charges</th>
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
														<tr id="ten2">
															<th>Package Forwarding Percentage</th>
															<td><input type="number" min="0" class="form-control package_calc"
																	name="package_percentage[]"
																	placeholder="Enter Packaging percentage" id="package_percentage_1" data-id="1" data-type="percent" readonly value="<?php echo $selector_arr['package_percentage'] ?>"></td>
															<?php
															foreach ($charges_arr as $key => $value) {
															?>

																<td>
																	<input type="number" min="0" class="form-control package_calc"
																		name="package_percentage[]"
																		placeholder="Enter Packaging percentage" id="package_percentage_<?php echo $key + 2; ?>" data-id="<?php echo $key + 2; ?>" data-type="percent" readonly value="<?php echo $value['package_percentage'] ?>">
																</td>
															<?php } ?>

														</tr>
														<tr id="ten1">
															<th>Package Forwarding Amount</th>
															<td><input type="number" min="0" class="form-control package_calc"
																	name="package_amount[]"
																	placeholder="Enter Packaging Charges" id="package_amount_1" data-id="1" data-type="amount" readonly value="<?php echo $selector_arr['package_amount'] ?>"></td>
															<?php
															foreach ($charges_arr as $key => $value) {
															?>

																<td>
																	<input type="number" min="0" class="form-control package_calc"
																		name="package_amount[]"
																		placeholder="Enter Packaging Charges" id="package_amount_<?php echo $key + 2; ?>" data-id="<?php echo $key + 2; ?>" data-type="amount" readonly value="<?php echo $value['package_amount'] ?>">
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
														<tr id="eleven1">
															<th>Discount Amount</th>
															<td>
																<input type="number" min="0" class="form-control"
																	name="discount_amount[]"
																	placeholder="Enter Discount Amount" id="discount_amount_1" readonly value="<?php echo $selector_arr['discount_amount'] ?>">
															</td>
															<?php
															foreach ($charges_arr as $key => $value) {
															?>

																<td>
																	<input type="number" min="0" class="form-control"
																		name="discount_amount[]"
																		placeholder="Enter Discount Amount" id="discount_amount_<?php echo $key + 2; ?>" readonly value="<?php echo $value['discount_amount'] ?>">
																</td>

															<?php } ?>
														</tr>
														<tr id="seven">
															<th>Net Amount</th>
															<td><input type="text" readonly class="form-control "
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
															$index = 1;
															while ($array_dy_Details7 = sqlsrv_fetch_array($array_Details7)) {

															?>
																<td><input type="text" readonly class="form-control valueof<?php echo $index; ?>"
																		name="Value_Of[]" id="totale2"
																		title="Total + AdditionalCharges"
																		value="<?php echo $array_dy_Details7['Value_Of'] ?>">
																</td>
															<?php $index++;
															} ?>
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
															<td>
																<!-- <input type="text" readonly class="form-control "
                                                                    name="Payment_Terms[]"
                                                                    value="<?php echo $selector_arr['Payment_Terms'] ?>"> -->
																<select readonly class="form-control" name="Payment_Terms[]">
																	<option value="<?php echo $selector_arr['Payment_Terms'] ?>"><?php echo $selector_arr['Payment_Terms'] ?> - <?php echo $selector_arr['Payment_Name'] ?></option>
																</select>
															</td>
															<?php
															$array_Details13 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Payment_Terms,Payment_master_PO.Payment_Name
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id
															LEFT JOIN Payment_master_PO ON Payment_master_PO.Payment_Code = Tb_Recommender.Payment_Terms 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Payment_Terms,Payment_master_PO.Payment_Name ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details13 = sqlsrv_fetch_array($array_Details13)) {

															?>
																<td>
																	<!-- 	<input type="text" readonly class="form-control "
                                                                    name="Payment_Terms[]"
                                                                    value="<?php echo $array_dy_Details13['Payment_Terms'] ?>"> -->
																	<select readonly class="form-control" name="Payment_Terms[]">
																		<option value="<?php echo $array_dy_Details13['Payment_Terms'] ?>"><?php echo $array_dy_Details13['Payment_Terms'] ?> - <?php echo $array_dy_Details13['Payment_Name'] ?></option>
																	</select>
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
																<input type="text" class="form-control Recommender_Selection" readonly
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
															$recindex = 1;
															while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

															?>
																<td class="woo">
																	<input type="text" class="form-control Recommender_Selection<?php echo $recindex; ?>" readonly
																		name="Recommender_Selection[]"
																		value="<?php echo $array_dy_Details14['Recommender_Selection'] ?>">
																</td>
															<?php $recindex++;
															} ?>

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
														if ($array_dy_finance['Remark'] == '') {
														} else {
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
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection ORDER BY Tb_Recommender.V_id DESC", [], array("Scrollable" => SQLSRV_CURSOR_KEYSET));
															$vendor_count = sqlsrv_num_rows($array_Details142);

															$array_dy_Details142 = sqlsrv_fetch_array($array_Details142);
															?>

															<td class="woo">
																<select class="form-control request_selection"
																	name="Approver_Selection[]">
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
																	<?php for ($i = 1; $i <= $vendor_count; $i++) { ?>
																		<option <?php if ($array_dy_Details1421['Recommender_Selection'] == $i) { ?> selected="selected"
																			<?php } ?>value="
                                                                        <?php echo $i; ?>">
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
															$resindex = 1;
															while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

															?>

																<td class="answer">
																	<?php
																	$array_Details142 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id  
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection ORDER BY Tb_Recommender.V_id DESC", [], array("Scrollable" => SQLSRV_CURSOR_KEYSET));
																	$vendor_count = sqlsrv_num_rows($array_Details142);

																	$array_dy_Details142 = sqlsrv_fetch_array($array_Details142);

																	?>
																	<select class="form-control request_selection"
																		name="Approver_Selection[]" data-id="<?php echo $resindex; ?>">
																		<option value="">Select Approver Selection
																		</option>
																		<?php for ($i = 1; $i <= $vendor_count; $i++) { ?>
																			<option <?php if ($array_dy_Details14['Recommender_Selection'] == $i) { ?> selected="selected"
																				<?php } ?>value="
                                                                        <?php echo $i; ?>">
																				<?php echo $i; ?>
																			</option>
																		<?php } ?>

																	</select>
																</td>

															<?php $resindex++;
															} ?>

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
															if ($array_dy_Details11['Approver_Remarks'] == '') {
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
															} else {
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
															if ($view_query16['Attachment'] == '') {
															} else { ?>
																<th>PDF / JPG attachment
																</th>
																<?php
																$view1 = sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id' AND Recommender_Selection = '1' ");
																$view_query1 = sqlsrv_fetch_array($view1);

																// $file_extension = explode('.', $view_query1['Attachment'])[1];
																?>
																<td>
																	<div class="d-flex align-items-center">
																		<input type="text" class="form-control" readonly
																			value="<?php echo $view_query1['Attachment'] ?>"
																			data-bs-toggle="modal"
																			data-bs-target="bs-example-modal-center1<?php echo $view_query1['id'] ?>">
																		<?php if ($view_query1['Attachment'] != '') { ?>
																			<!-- <span class="ms-2 file_view" data-bs-toggle="modal"
																				data-bs-target="#bs-example-modal-center1<?php echo $view_query1['id'] ?>"><i class="fa fa-eye text-primary"></i></span> -->
																			<!-- <button class="btn btn-info" type="button" data-bs-toggle="modal" -->
																			<!-- data-bs-target="#bs-example-modal-center1<?php echo $view_query18['id'] ?>">view</button> -->
																		<?php } ?>
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
																						<img class="preview_file_img_1 preview_image" src="" alt="your image" width="100%" style="display: block;">

																					 <iframe class="preview_file_pdf_1 preview_pdf" src=""
		                                                                                    style="width: 100%;height: 900px;display: block;"
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

																		<div class="row mt-2 display_section p-3" id="file_display_section_1" style="border: 2px dashed blue;height: 400px;overflow-y: auto;">
															                	<?php 
															                	$multi_files = explode(',',$view_query1['Attachment']);

															                	foreach ($multi_files as $key => $value) {
																					$file_extension = explode('.', $value)[1];

																                	if($file_extension == 'pdf') { ?>
																						<div class="col-md-3 h-50 mt-2">
																							<img src="https://play-lh.googleusercontent.com/IkcyuPcrQlDsv62dwGqteL_0K_Rt2BUTXfV3_vR4VmAGo-WSCfT2FgHdCBUsMw3TPGU"  class="multi_preview" style="width:100px;height: 100px;" data-filetype="pdf" data-id="1">
																							<input type="hidden" id="pdf_input1" value="file/<?php echo $value; ?>">
																						</div>	
																                	<?php } else { ?>
																                		<div class="col-md-3 h-50 mt-2">
																                			<i class="fa fa-eye text-primary preview_icon"></i>
																							<img src="file/<?php echo $value; ?>" class="multi_preview" data-filetype="img" style="width:100px;height: 100px;" data-id="1">
																						</div>
																					<?php }} ?>
															                		
		                                                				</div>

																	<?php
																	$view18 = sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id' AND Recommender_Selection != '1' ORDER BY Tb_Recommender.V_id DESC");
																	$rindex = 1;
																	while ($view_query18 = sqlsrv_fetch_array($view18)) {
																	// $file_extension = explode('.', $view_query18['Attachment'])[1];

																	?>
																<td>
																	<div class="d-flex align-items-center">
																		<input type="text" class="form-control" readonly
																			value="<?php echo $view_query18['Attachment'] ?>"
																			data-bs-toggle="modal"
																			data-bs-target="bs-example-modal-center1<?php echo $view_query18['id'] ?>">
																		<?php if ($view_query18['Attachment'] != '') { ?>
																			<!-- <span class="ms-2 file_view" data-bs-toggle="modal"
																				data-bs-target="#bs-example-modal-center1<?php echo $view_query18['id'] ?>"><i class="fa fa-eye text-primary"></i></span> -->
																			<!-- <button class="btn btn-info" type="button" data-bs-toggle="modal" -->
																			<!-- data-bs-target="#bs-example-modal-center1<?php echo $view_query18['id'] ?>">view</button> -->
																		<?php } ?>
																	</div>
			
																<!-- file preview modal -->
																<div class="modal fade" id="file_preview_modal_<?php echo $rindex; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
																  <div class="modal-dialog modal-lg">
																    <div class="modal-content">
																      <div class="modal-header">
																        <h1 class="modal-title fs-5" id="exampleModalLabel">File Preview</h1>
																        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																      </div>
																      <div class="modal-body">
																				<img class="preview_file_img_<?php echo $rindex; ?> preview_image" src="" alt="your image" width="100%" style="display: block;">

																			 <iframe class="preview_file_pdf_<?php echo $rindex; ?> preview_pdf" src=""
                                                                                    style="width: 100%;height: 900px;display: block;"
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

																<div class="row mt-2 display_section p-3" id="file_display_section_<?php echo $rindex; ?>" style="border: 2px dashed #ccc;height: 400px;overflow-y: auto;">
													                	<?php 
													                	$multi_files = explode(',',$view_query18['Attachment']);

													                	foreach ($multi_files as $key => $value) {
																			$file_extension = explode('.', $value)[1];

														                	if($file_extension == 'pdf') { ?>
																				<div class="col-md-3 h-50 mt-2">
																					<img src="https://play-lh.googleusercontent.com/IkcyuPcrQlDsv62dwGqteL_0K_Rt2BUTXfV3_vR4VmAGo-WSCfT2FgHdCBUsMw3TPGU"  class="multi_preview" style="width:100px;height: 100px;" data-filetype="pdf" data-id="<?php echo $rindex; ?>">
																					<input type="hidden" id="pdf_input<?php echo $rindex; ?>" value="file/<?php echo $value; ?>">
																				</div>	
														                	<?php } else { ?>
														                		<div class="col-md-3 h-50 mt-2">
														                			<i class="fa fa-eye text-primary preview_icon"></i>
																					<img src="file/<?php echo $value; ?>" class="multi_preview" data-filetype="img" style="width:100px;height: 100px;" data-id="<?php echo $rindex; ?>">
																				</div>
																			<?php }} ?>
													                		
                                                				</div>
																</td>
															<?php $rindex++; } ?>
														<?php } ?>
														</tr>

													</tbody>

												</table>
												<table>
													<tbody>
														<tr>
 															<th style="text-align: end;">Additional Reference Remark <span style="color:red">*</span> </th>
                                                            <td class="Reference">
                                                                <textarea rows="3" cols="40" class="form-control kim"
                                                                    placeholder="Remarks"
                                                                    name="Reference_Remark"></textarea>
                                                            </td>
														</tr>
													</tbody>
												</table>
											</div>
											<br>
											<div class="row" id="involved_persons_div" style="display:none;">
												<div class="col-md-5">
													<h4>Involved Persons</h4>
													<table class="table table-striped table-bordered table-hover">
														<thead>
															<tr>
																<th>Purchaser</th>
																<th>Recommender</th>
  																<th>Approver</th>
                                                                <th style="display:none;" class="inv_fin_appr">Final Approver</th>
															</tr>
														</thead>
														<tbody id="involved_persons_tbody">

														</tbody>
													</table>
												</div>
											</div>

                                                <center style="padding: 35px 0px 0px 0px;">
                                                        <!-- Default -->
                                                        <button class="btn btn-info mb-2 me-4 btn-sm two" type="submit"
                                                            name="Reference">
                                                            Submit
                                                        </button>                                                  
                                                </center>
										</form>
									</div>
								</div>
							</div> <!-- end col -->
						</div>
						<!-- end row -->
					</div>


                <!-- Modal -->
                <div class="modal fade" id="sendback_new_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">SendBack</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="col-md-12 form-group">
                            <label for="Recommender_sendback_remark">Remark<span class="text-danger"> *</span></label>
                            <textarea class="form-control" name="Recommender_sendback_remark" id="Recommender_sendback_remark" rows="5" cols="5"></textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="send_back_remark_submit">Send</button>
                      </div>
                    </div>
                  </div>
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
	<script type="text/javascript">
		$(document).on('click','.multi_preview',function(){
			var file_type = $(this).data('filetype');
			var src = $(this).attr('src');
			var row_id = $(this).data('id');


			 if(file_type == 'pdf') {
			 	// var src = $('#pdf_input'+row_id).val();
                var src = $(this).closest('div').find('#pdf_input'+row_id).val();
            	$('.preview_file_pdf_'+row_id).attr('src', src+'#toolbar=0');
            	$('.preview_file_img_'+row_id).hide();
            	$('.preview_file_pdf_'+row_id).show();
		     } else {
            	$('.preview_file_img_'+row_id).attr('src', src);
            	$('.preview_file_pdf_'+row_id).hide();
            	$('.preview_file_img_'+row_id).show();
		     } 
        	$('#file_preview_modal_'+row_id).modal('show');

  		});
	</script>


	<!-- CUSTOM SCRIPT END -->

</body>

</html>