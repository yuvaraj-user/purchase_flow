<?php
include('../auto_load.php');
include('adition.php');
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
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];

	// total,discount,packageamount display query
	$discount_charges_qry = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
		Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.total_amount,Tb_Approver.discount_amount,Tb_Approver.package_amount,Tb_Approver.package_percentage
		FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
		WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection ! = '1' 
		GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
		Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.total_amount,Tb_Approver.discount_amount,Tb_Approver.package_amount,Tb_Approver.package_percentage ORDER BY Tb_Approver.V_id DESC");
	$charges_arr = [];	
	while ($discount_charges_res = sqlsrv_fetch_array($discount_charges_qry)) {
		$charges_arr[] = $discount_charges_res;
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
                                             <li class="breadcrumb-item"><a href="show_approver.php">Show Approver Purchase Request</a></li>
                                             <li class="breadcrumb-item active">View Approver Purchase Request</li>
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
                                                        $selector = sqlsrv_query($conn, "SELECT Tb_Approver.Request_id,Tb_Approver.V_id,Tb_Approver.Vendor_SAP,Tb_Approver.Vendor_Name,Tb_Approver.Vendor_City,Tb_Approver.vendor_Active_SAP,
                                                        Tb_Approver.Last_Purchase,Tb_Approver.Delivery_Time2,Tb_Approver.Value_Of,Tb_Approver.Fright_Charges,Tb_Approver.Insurance_Details,Tb_Approver.GST_Component,
                                                        Tb_Approver.Warrenty,Tb_Approver.Payment_Terms,Tb_Approver.Requester_Remarks,Tb_Approver.Recommender_Remarks,Tb_Approver.Finance_Remarks,Tb_Approver.Attachment,
                                                        Tb_Approver.Status,Tb_Approver.Requester_Selection,Tb_Approver.Approver_Selection,Tb_Approver.Recommender_Selection,Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver_Meterial.Quantity,
                                                        Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver.Approver_Remarks,Tb_Approver.total_amount,Tb_Approver.discount_amount,Tb_Approver.package_amount,Tb_Approver.package_percentage
                                                        FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
                                                        WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Recommender_Selection = '1'
                                                        GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,Tb_Approver.Vendor_SAP,Tb_Approver.Vendor_Name,Tb_Approver.Vendor_City,Tb_Approver.vendor_Active_SAP,
                                                        Tb_Approver.Last_Purchase,Tb_Approver.Delivery_Time2,Tb_Approver.Value_Of,Tb_Approver.Fright_Charges,Tb_Approver.Insurance_Details,Tb_Approver.GST_Component,
                                                        Tb_Approver.Warrenty,Tb_Approver.Payment_Terms,Tb_Approver.Requester_Remarks,Tb_Approver.Recommender_Remarks,Tb_Approver.Finance_Remarks,Tb_Approver.Attachment,
                                                        Tb_Approver.Status,Tb_Approver.Requester_Selection,Tb_Approver.Approver_Selection,Tb_Approver.Recommender_Selection,Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver_Meterial.Quantity,
                                                        Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver.Approver_Remarks,Tb_Approver.total_amount,Tb_Approver.discount_amount,Tb_Approver.package_amount,Tb_Approver.package_percentage");
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
															$array_Details1 = sqlsrv_query($conn, "SELECT Tb_Approver.Request_id,Tb_Approver.V_id,Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
															WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Recommender_Selection ! = '1'
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details1 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Vendor_SAP
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Vendor_SAP ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details2 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Vendor_Name
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Vendor_Name ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details3 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Vendor_City
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Vendor_City ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details4 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.vendor_Active_SAP
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.vendor_Active_SAP ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details5 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Last_Purchase
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Last_Purchase ORDER BY Tb_Approver.V_id DESC");
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
																			<th>Discount(%)</th>
																			<th>GST(%)</th>
																			<th>Total</th>
																		</thead>
																	</tr>
																	<tbody>
																		<?php
																		$mat_ind = 1;	
																		$i = 1;
																		$result = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
																		Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver_Meterial.Quantity,
																		Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver_Meterial.gst_percentage,Tb_Approver_Meterial.discount_percentage FROM Tb_Approver
																		INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
																		WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id 
																		AND Tb_Approver.Recommender_Selection  = '1' 
																		GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
																		Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver_Meterial.Quantity,
																		Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver_Meterial.gst_percentage,Tb_Approver_Meterial.discount_percentage ORDER BY Tb_Approver.V_id DESC");
																		while ($row = sqlsrv_fetch_array($result)) {
																			?>
																			<tr>
																				<td>
																					<input type="text"
																						class="form-control qty"
																						readonly name="Quantity_Details[]"
																						value="<?php echo $row['Quantity'] ?>"
																						placeholder="Enter Quantity Details" style="width: 75px;">
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
																						value="<?php echo $row['Price'] ?>" style="width: 75px;">
																				</td>

																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_discount_percent_1 discount_percent" style="width: 75px;"
																						name="discount_percent[]"
																						placeholder="Enter Discount" step=".01"  required error-msg='Discount is mandatory.' data-rowid="1" readonly id="vendor1_discount_percentage_material<?php echo $mat_ind;?>" value="<?php echo $row['discount_percentage'] ?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_gst_percent_1 gst_percent" style="width: 75px;"
																						name="gst_percent[]" 
																						placeholder="Enter GST" step=".01"  required error-msg='Price is mandatory.' data-rowid="1" readonly id="vendor1_gst_percentage_material<?php echo $mat_ind;?>" value="<?php echo $row['gst_percentage'] ?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="text"
																						class="form-control amount"
																						readonly name="Total[]" 
																						value="<?php echo $row['Total'] ?>" style="width: 75px;">
																				</td>
																			</tr>
																			<?php $mat_ind++; } ?>
																	</tbody>
																</table>
															</td>
															<?php
															$vendor_ind = 2;
															$array_Details111 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id 
															AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id ORDER BY Tb_Approver.V_id DESC");
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
																			<th>Discount(%)</th>
																			<th>GST(%)</th>
																			<th>Total</th>
																		</thead>
																	</tr>
																	<tbody>
                                                                        <?php
                                                                       		$mat_ind = 1;
																			$array_Details1111 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
																			Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver_Meterial.Quantity,
																			Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver_Meterial.gst_percentage,Tb_Approver_Meterial.discount_percentage
																			FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
																			WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = '$arr' AND Tb_Approver_Meterial.V_id = '$arr'
																			AND Tb_Approver.Recommender_Selection ! = '1' 
																			GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
																			Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver_Meterial.Quantity,
																			Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver_Meterial.gst_percentage,Tb_Approver_Meterial.discount_percentage ORDER BY Tb_Approver.V_id DESC");
																			while ($array_dy_Details1111 = sqlsrv_fetch_array($array_Details1111)) {

																				?>
																			<tr>
																				<td>
																					<input type="text"
																						class="form-control qty"
																						readonly name="Quantity_Details[]" 
																						value="<?php echo $array_dy_Details1111['Quantity'] ?>"
																						placeholder="Enter Quantity Details" style="width: 75px;">
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
																						value="<?php echo $array_dy_Details1111['Price'] ?>" style="width: 75px;">
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_discount_percent_1 discount_percent" style="width: 75px;"
																						name="discount_percent[]"
																						placeholder="Enter Discount" step=".01"  required error-msg='Discount is mandatory.' data-rowid="1" readonly id="vendor<?php echo $vendor_ind; ?>_discount_percentage_material<?php echo $mat_ind;?>" value="<?php echo $array_dy_Details1111['discount_percentage'] ?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_gst_percent_1 gst_percent" style="width: 75px;"
																						name="gst_percent[]" 
																						placeholder="Enter GST" step=".01"  required error-msg='Price is mandatory.' data-rowid="1" readonly id="vendor<?php echo $vendor_ind; ?>_gst_percentage_material<?php echo $mat_ind;?>" value="<?php echo $array_dy_Details1111['gst_percentage'] ?>">
																					<span class="error_msg text-danger"></span>
																				</td>

																				<td>
																					<input type="text" 
																						class="form-control amount"
																						readonly name="Total[]"
																						value="<?php echo $array_dy_Details1111['Total'] ?>" style="width: 75px;">
																				</td>
																			</tr>
																		<?php $mat_ind++; } ?>
																	</tbody>
																</table>
															</td>
															<?php $vendor_ind++; } ?>
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
																<input type="text" readonly class="form-control amt_tot<?php echo $key+2; ?>" 
																	name="amt_tot[]" id="amt_tot<?php echo $key+2; ?>" title="Total" value="<?php echo $value['total_amount'] ?>">
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
															$array_Details9 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Fright_Charges
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Fright_Charges ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details10 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Insurance_Details
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Insurance_Details ORDER BY Tb_Approver.V_id DESC");
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
																	placeholder="Enter Packaging percentage" id="package_percentage_1" data-id="1" data-type="percent" readonly  value="<?php echo $selector_arr['package_percentage'] ?>"></td>
															<?php 
															foreach ($charges_arr as $key => $value) {
															?>
															
															<td>
																<input type="number" min="0" class="form-control package_calc"
																	name="package_percentage[]"
																	placeholder="Enter Packaging percentage" id="package_percentage_<?php echo $key+2; ?>" data-id="<?php echo $key+2; ?>" data-type="percent" readonly value="<?php echo $value['package_percentage'] ?>">
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
																	placeholder="Enter Packaging Charges" id="package_amount_<?php echo $key+2; ?>" data-id="<?php echo $key+2; ?>" data-type="amount" readonly value="<?php echo $value['package_amount'] ?>">
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
															$array_Details11 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.GST_Component
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.GST_Component ORDER BY Tb_Approver.V_id DESC");
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
                                                                    placeholder="Enter Discount Amount" id="discount_amount_<?php echo $key+2; ?>" readonly value="<?php echo $value['discount_amount'] ?>">
                                                            </td>

                                                            <?php } ?>
														</tr>
														<tr id="seven">
															<th>Net Amount</th>
															<td><input type="text" readonly class="form-control " 
																	name="Value_Of[]" id="totale2" title="Total + AdditionalCharges"
																	value="<?php echo $selector_arr['Value_Of'] ?>">
															</td>
															<?php
															$array_Details7 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Value_Of
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Value_Of ORDER BY Tb_Approver.V_id DESC");
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
																	value="<?php echo $selector_arr['Delivery_Time2'] ?>">
															</td>
															<?php
															$array_Details8 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Delivery_Time2
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Delivery_Time2 ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details8 = sqlsrv_fetch_array($array_Details8)) {

																?>
																<td><input type="text" readonly class="form-control " 
																		name="Delivery_Time[]"
																		value="<?php echo $array_dy_Details8['Delivery_Time2'] ?>">
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
															$array_Details12 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Warrenty
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Warrenty ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details13 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Payment_Terms
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Payment_Terms ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details15 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Requester_Selection
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Requester_Selection ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details15 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Requester_Remarks
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Requester_Remarks ORDER BY Tb_Approver.V_id DESC");
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
																	<input type="text" class="form-control " 
																	readonly name="Recommender_Selection[]"
																	value="<?php echo $selector_arr['Recommender_Selection'] ?>">
															</td>
															<?php
															$array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Recommender_Selection
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Recommender_Selection ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

																?>
																<td class="woo">
																		<input type="text" class="form-control "
																	readonly name="Recommender_Selection[]" 
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
														<tr class="target-table">
															<th>Approver Selection<span class="required-label  ">

															</span>
															</th>
                                                            <td><input type="text" class="form-control " readonly 
																	name="Recommender_Remarks[]"
																	value="<?php echo trim($selector_arr['Approver_Selection']) ?>">
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
														<tr class="remark">
														<?php 
															$approver_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Approver_Code' ");
															$approver_names = sqlsrv_fetch_array($approver_name);
															$name3 = $approver_names['Employee_Name'];
															?>
                                                            <th>Approver's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name3 ?>)</span>
                                                            </th>
															<td><input type="text" class="form-control " readonly 
																	name="Requester_Remarks[]"
																	value="<?php echo $selector_arr['Approver_Remarks'] ?>">
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
															<td><input type="text" class="form-control" readonly value="<?php echo $view_query18['Attachment'] ?>"
                                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center1?php echo $view_query18['id'] ?>">
																<div class="modal fade bs-example-modal-center1<?php echo $view_query18['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title mt-0">Attachment</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                                    
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
																			<iframe src="file/<?php echo $view_query18['Attachment'] ?>"
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
                                                            
																<?php
															}
															?>
                                                            <?php
															$array_Details11 = sqlsrv_query($conn, "SELECT Reference_Empid,Remark_To_Reference,Reference_Remark
															FROM Tb_Approver 
															WHERE Request_id ='$request_id' 
															GROUP BY Reference_Empid,Remark_To_Reference,Reference_Remark");
															$array_dy_Details11 = sqlsrv_fetch_array($array_Details11);
															?>
                                                            <?php
															if($array_dy_Details11['Reference_Remark'] == ''){
																?>
																	
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
