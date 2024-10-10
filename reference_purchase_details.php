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
                                             <li class="breadcrumb-item"><a href="show_informer_purchase_request.php">Show Informer Details For Purchace</a></li>
                                             <li class="breadcrumb-item active">View Informer Details For Purchace</li>
                                         </ol>
                                 </div>
                             </div>
							 <div class="col-sm-6">
                                <div class="float-end d-none d-sm-block">
                                    <h4 class="">Request ID : <?php echo $request_id ?></h4>
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
                                            <form method="POST" enctype="multipart/form-data">
                                                <div class="table-wrapper">
                                                    <table id="busDataTable" class="data">
													<?php
                                                        $selector = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.Vendor_SAP,Tb_Vendor_Selection.V_id,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_City,Tb_Vendor_Selection.vendor_Active_SAP,
                                                            Tb_Vendor_Selection.Last_Purchase,Tb_Vendor_Selection.Delivery_Time,Tb_Vendor_Selection.Value_Of,Tb_Vendor_Selection.Fright_Charges,
                                                            Tb_Vendor_Selection.Insurance_Details,Tb_Vendor_Selection.GST_Component,Tb_Vendor_Selection.Warrenty,Tb_Vendor_Selection.Payment_Terms,
                                                            Tb_Vendor_Selection.Requester_Selection,Tb_Vendor_Selection.Requester_Remarks,Tb_Vendor_Selection.Attachment,Tb_Vendor_Quantity.Quantity,
                                                            Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.Request_Id
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.Vendor_SAP,Tb_Vendor_Selection.V_id,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_City,Tb_Vendor_Selection.vendor_Active_SAP,
                                                            Tb_Vendor_Selection.Last_Purchase,Tb_Vendor_Selection.Delivery_Time,Tb_Vendor_Selection.Value_Of,Tb_Vendor_Selection.Fright_Charges,
                                                            Tb_Vendor_Selection.Insurance_Details,Tb_Vendor_Selection.GST_Component,Tb_Vendor_Selection.Warrenty,Tb_Vendor_Selection.Payment_Terms,
                                                            Tb_Vendor_Selection.Requester_Selection,Tb_Vendor_Selection.Requester_Remarks,Tb_Vendor_Selection.Attachment,Tb_Vendor_Quantity.Quantity,
                                                            Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.Request_Id");
                                                        $selector_arr = sqlsrv_fetch_array($selector);
                                                        ?>
                                                        <tbody class="results" id="rone">
														<tr id="head">
															<th>Particulars</th>
                                                            <td>
																<?php echo $selector_arr['V_id'] ?><input type="hidden"
																	class="form-control" name="V1_id[]"
																	value="<?php echo $selector_arr['V_id'] ?>">
															</td>
															<?php
															$array_Details1 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id ORDER BY Tb_Vendor_Selection.V_id DESC ");
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
                                                            <td><input type="text" class="form-control" readonly name="Vendor_SAP[]"
																	value="<?php echo $selector_arr['Vendor_SAP'] ?>">
															</td>
															<?php
															$array_Details1 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_SAP
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_SAP ORDER BY Tb_Vendor_Selection.V_id DESC ");
															while ($array_dy_Details1 = sqlsrv_fetch_array($array_Details1)) {

																?>
																<td><input type="text" class="form-control" readonly name="Vendor_SAP[]"
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
															$array_Details2 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_Name
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_Name ORDER BY Tb_Vendor_Selection.V_id DESC ");
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
															$array_Details3 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_City
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_City ORDER BY Tb_Vendor_Selection.V_id DESC");
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
															$array_Details4 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.vendor_Active_SAP
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.vendor_Active_SAP ORDER BY Tb_Vendor_Selection.V_id DESC");
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
															$array_Details5 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Last_Purchase
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Last_Purchase ORDER BY Tb_Vendor_Selection.V_id DESC");
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
																<table id="myTable1" class="table table-bordered" style="width: 345px !important;">
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
																		$result = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
																		Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.Request_Id,
																		Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name
																		FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
																		ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
																		WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
																		AND Tb_Vendor_Selection.Requester_Selection = '1'
																		GROUP BY Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
																		Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.Request_Id,
																		Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name");
																		while ($row = sqlsrv_fetch_array($result)) {
																			?>
																			<tr>
																				<td>
																					<input type="text"
																						class="form-control qty"
																						readonly name="Quantity_Details[]"
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
																						class="form-control amount"
																						readonly name="Total[]" 
																						value="<?php echo $row['Total'] ?>">
																				</td>
																			</tr>
																			<?php } ?>
																	</tbody>
																</table>
															</td>
															<?php
															$array_Details111 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id ORDER BY Tb_Vendor_Selection.V_id DESC");
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
																			$array_Details1111 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
																			Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name,
																			Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total
																			FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
																			ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
																			WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND  Tb_Vendor_Selection.V_id = '$arr'
																			AND Tb_Vendor_Quantity.V_id = '$arr'
																			AND Tb_Vendor_Selection.Requester_Selection ! = '1'
																			GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
																			Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name,
																			Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total ORDER BY Tb_Vendor_Selection.V_id DESC");
																			while ($array_dy_Details1111 = sqlsrv_fetch_array($array_Details1111)) {

																				?>
																			<tr>
																				<td>
																					<input type="text"
																						class="form-control qty"
																						readonly name="Quantity_Details[]" 
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
																						class="form-control amount"
																						readonly name="Total[]"
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
															<td><input type="text" readonly class="form-control " 
																	name="Value_Of[]" id="totale2" title="Total + AdditionalCharges"
																	value="<?php echo $selector_arr['Value_Of'] ?>">
															</td>
															<?php
															$array_Details7 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Value_Of
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Value_Of ORDER BY Tb_Vendor_Selection.V_id DESC");
															while ($array_dy_Details7 = sqlsrv_fetch_array($array_Details7)) {

																?>
																<td><input type="text" readonly class="form-control " 
																		name="Value_Of[]" id="totale2" title="Total + AdditionalCharges"
																		value="<?php echo $array_dy_Details7['Value_Of'] ?>">
																</td>
															<?php } ?>
														</tr>
														<tr id="eight">
															<th>Delivery Time</th>
															<td><input type="text" readonly class="form-control " 
																	name="Delivery_Time[]"
																	value="<?php echo $selector_arr['Delivery_Time'] ?>">
															</td>
															<?php
															$array_Details8 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Delivery_Time
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Delivery_Time ORDER BY Tb_Vendor_Selection.V_id DESC");
															while ($array_dy_Details8 = sqlsrv_fetch_array($array_Details8)) {

																?>
																<td><input type="text" readonly class="form-control " 
																		name="Delivery_Time[]"
																		value="<?php echo $array_dy_Details8['Delivery_Time'] ?>">
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
															$array_Details9 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Fright_Charges
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Fright_Charges ORDER BY Tb_Vendor_Selection.V_id DESC");
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
															$array_Details10 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Insurance_Details
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Insurance_Details ORDER BY Tb_Vendor_Selection.V_id DESC");
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
															$array_Details11 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.GST_Component
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.GST_Component ORDER BY Tb_Vendor_Selection.V_id DESC");
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
															$array_Details12 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Warrenty
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Warrenty ORDER BY Tb_Vendor_Selection.V_id DESC");
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
															$array_Details13 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Payment_Terms
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Payment_Terms ORDER BY Tb_Vendor_Selection.V_id DESC");
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
															$array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Selection
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Selection ORDER BY Tb_Vendor_Selection.V_id DESC");
															while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {
																?>
																<td><input type="text" class="form-control " readonly 
																		name="Requester_Selection[]"
																		value="<?php echo $array_dy_Details14['Requester_Selection'] ?>">
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
															$array_Details15 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Remarks
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Remarks ORDER BY Tb_Vendor_Selection.V_id DESC");
															while ($array_dy_Details15 = sqlsrv_fetch_array($array_Details15)) {

																?>
																<td><input type="text" class="form-control " readonly 
																		name="Requester_Remarks[]"
																		value="<?php echo $array_dy_Details15['Requester_Remarks'] ?>">
																</td>
															<?php } ?>
														</tr>
														
														<?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Recommender 
                                                        WHERE Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Recommender_Selection'] == '' ){

                                                        }else{
                                                            ?>
														<tr id="tara" class="src-table">
															<th>Recommender's Selection
															</th>
															<td class="woo">
																<?php
															$array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection  = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection ORDER BY Tb_Recommender.V_id DESC");
															$array_dy_Details14 = sqlsrv_fetch_array($array_Details14);
															?>
																	<input type="text" class="form-control " 
																	readonly name="Recommender_Selection[]"
																	value="<?php echo $array_dy_Details14['Recommender_Selection'] ?>">
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
																		<input type="text" class="form-control "
																	readonly name="Recommender_Selection[]" 
																	value="<?php echo $array_dy_Details14['Recommender_Selection'] ?>">
																</td>
															<?php } ?>

														</tr>
														<?php } ?>
														<?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Recommender 
                                                        WHERE Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Recommender_Remarks'] == '' ){

                                                        }else{
                                                            ?>
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
															<?php
															$array_Details121 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Recommender_Remarks
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection  = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Recommender_Remarks ORDER BY Tb_Approver.V_id DESC");
															$array_dy_Details121 = sqlsrv_fetch_array($array_Details121);

																?>
															<td><input type="text" class="form-control " readonly 
																	name="Recommender_Remarks[]"
																	value="<?php echo $array_dy_Details121['Recommender_Remarks'] ?>">
															</td>
															<?php
															$array_Details121 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Recommender_Remarks
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Recommender_Remarks ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

																?>
																<td><input type="text" readonly class="form-control " 
																		name="Recommender_Remarks[]"
																		value="<?php echo $array_dy_Details121['Recommender_Remarks'] ?>">
																</td>
															<?php } ?>

														</tr>
														<?php } ?>
														
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
															<th>Total Budget (in INR)<span class="required-label"></span>
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
															<th>Available Budget (in INR)<span class="required-label"></span>
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
														<?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Approver 
                                                        WHERE Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Approver_Selection'] == '' ){

                                                        }else{
                                                            ?>
                                                        <tr class="target-table">
															<th>Approver Selection<span class="required-label  ">

															</span>
															</th>
															<?php
															$array_Details144 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Selection
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection  = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Selection ORDER BY Tb_Approver.V_id DESC");
															$array_dy_Details144 = sqlsrv_fetch_array($array_Details144);

																?>
                                                            <td><input type="text" class="form-control " readonly 
																	name="Recommender_Remarks[]"
																	value="<?php echo trim($array_dy_Details144['Approver_Selection']) ?>">
															</td>
                                                            <?php
															$array_Details144 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Selection
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Selection ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details144 = sqlsrv_fetch_array($array_Details144)) {

																?>
																<td><input type="text" class="form-control " readonly 
																		name="Requester_Remarks[]"
																		value="<?php echo trim($array_dy_Details144['Approver_Selection']) ?>">
																</td>
															<?php } ?>
														</tr>
														<?php } ?>
														<?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Approver 
                                                        WHERE Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Approver_Remarks'] == '' ){

                                                        }else{
                                                            ?>
														<tr class="remark">
														<?php 
															$approver_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Approver_Code' ");
															$approver_names = sqlsrv_fetch_array($approver_name);
															$name3 = $approver_names['Employee_Name'];
															?>
                                                            <th>Approver's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name3 ?>)</span>
                                                            </th>
															<?php
															$array_Details155 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection  = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks ORDER BY Tb_Approver.V_id DESC");
															$array_dy_Details155 = sqlsrv_fetch_array($array_Details155);

																?>
															<td><input type="text" class="form-control " readonly 
																	name="Requester_Remarks[]"
																	value="<?php echo $array_dy_Details155['Approver_Remarks'] ?>">
															</td>
															<?php
															$array_Details155 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details155 = sqlsrv_fetch_array($array_Details155)) {

																?>
																<td><input type="text" class="form-control " readonly 
																		name="Requester_Remarks[]"
																		value="<?php echo $array_dy_Details155['Approver_Remarks'] ?>">
																</td>
															<?php } ?>
														</tr>
														<?php } ?>
                                                        <tr>
                                                            <th>Additional Reference</th>
                                                            <?php
															$array_Details155 = sqlsrv_query($conn, "SELECT  Remark_To_Reference
															FROM Tb_Approver 
															WHERE Request_id ='$request_id' 
															GROUP BY Remark_To_Reference");
															$array_dy_Details155 = sqlsrv_fetch_array($array_Details155);

																?>
                                                            <td class="Reference">
                                                                <textarea rows="3" cols="40" class="form-control kim" 
                                                                    readonly=""
                                                                    name="Remark_To_Reference"><?php echo $array_dy_Details155['Remark_To_Reference'] ?></textarea>
                                                            </td>
                                                        
                                                            <td style="text-align: end;">Additional Reference Remark <span style="color:red">*</span> </td>

                                                            <td class="Reference">
                                                                <textarea rows="3" cols="40" class="form-control kim"
                                                                    placeholder="Remarks"
                                                                    name="Reference_Remark"></textarea>
                                                            </td>
                                                        </tr>
                                                        <?php 
                                                        if($selector_arr['Attachment'] == '' ){

                                                        }else{
                                                            ?>
														<tr id="pdf">
															<th>PDF / JPG attachment
															</th>
															<td><input type="text" class="form-control" name="Attachment[]" readonly value="<?php echo $selector_arr['Attachment'] ?>"
                                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
																<div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title mt-0">Attachment</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                                    
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
																			<iframe src="file/<?php echo $selector_arr['Attachment'] ?>"
																			style="width: 100%;height: auto;" frameborder="0"></iframe>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                            </div>
                                                                        </div><!-- /.modal-content -->
                                                                    </div><!-- /.modal-dialog -->
                                                                </div><!-- /.modal -->
															</td>
															<?php
															$array_Details16 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Attachment,Tb_Vendor_Selection.Id
															FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
															ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
															WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
															AND Tb_Vendor_Selection.Requester_Selection ! = '1'
															GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
															Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Attachment,Tb_Vendor_Selection.Id ORDER BY Tb_Vendor_Selection.V_id DESC");
															while ($array_dy_Details16 = sqlsrv_fetch_array($array_Details16)) {
															?>
                                                            <td><input type="text" class="form-control" name="Attachment[]" readonly value="<?php echo $array_dy_Details16['Attachment'] ?>"
                                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center1<?php echo $array_dy_Details16['Id'] ?>">
																<div class="modal fade bs-example-modal-center1<?php echo $array_dy_Details16['Id'] ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title mt-0">Attachment</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                                    
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
																			<iframe src="file/<?php echo $array_dy_Details16['Attachment'] ?>"
																			style="width: 100%;height: auto;" frameborder="0"></iframe>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                            </div>
                                                                        </div><!-- /.modal-content -->
                                                                    </div><!-- /.modal-dialog -->
                                                                </div><!-- /.modal -->
															</td>
                                                            <?php } ?>
														</tr>
                                                         <?php   }?>
													</tbody>
                                            
                                                    </table>

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
   

    </body>
</html>
