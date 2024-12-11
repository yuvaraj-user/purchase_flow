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
WHERE Purchaser = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$verifier_name = $selector_arr1['Finance_Verfier'];
$Purchaser_Code = $selector_arr1['Purchaser'];
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];

$saved_quotation_sql = "SELECT * FROM Tb_Vendor_Selection WHERE Request_ID = '".$request_id."'";
$saved_quotation_exec = sqlsrv_query($conn, $saved_quotation_sql, array(), array("Scrollable" => 'static'));
$saved_count = sqlsrv_num_rows($saved_quotation_exec);
$saved_data = array();
while($saved_quotation_res = sqlsrv_fetch_array($saved_quotation_exec,SQLSRV_FETCH_ASSOC)) {
	$saved_data[] = $saved_quotation_res;
}


$first_preference_quotation_value = '';
foreach ($saved_data as $key => $value) {
	if($value['Requester_Selection'] == 1) {
		$first_preference_quotation_value = $value['Value_Of'];
	}
}

	// total,discount,packageamount display query
	$discount_charges_qry = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id'");
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

		.modal-content {
    		cursor: move;
		}

		body {
		    /* STOP MOVING AROUND! */
		    overflow-x: hidden;
		    overflow-y: scroll !important;
		}

		.modal_css_load {
		    position: fixed;
		    top: 0;
		    left: 0;
		    z-index: 1055;
		    display: none;
		    width: 100%;
		    height: 100%;
		     -ms-overflow-style: none;  /* Internet Explorer 10+ */
		    scrollbar-width: none;  /* Firefox */
		}

		@media only screen and (max-width: 600px) {
            .plant_top_detail {
                font-size: 10px !important;
            }
            .form-control-plaintext {
                font-size: 10px;
            }
            .material_tbl {
                width: auto !important;
            }

            th:first-child {
                position: unset;
                left: unset;
            }

            .table-wrapper {
                margin-left: unset;
            }

            .footer {
                left: 0 !important;
                text-align: center;
             }              
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
                                             <li class="breadcrumb-item active">View Quotation For Request</li>
                                         </ol>
                                 </div>
                             </div>
							 <div class="col-sm-6">
                                <div class="float-end d-none d-sm-block">
								     <h4 class="">Request ID : <?php echo $request_id ?></h4>
								    <?php
                                     $po_creator_sql = sqlsrv_query($conn, "SELECT TOP 1 Tb_Request.EMP_ID,Tb_Request.Plant,Tb_Request_Items.MaterialGroup,Tb_Request.vendor_justification from Tb_Request 
                                        left join Tb_Request_Items ON Tb_Request_Items.Request_ID = Tb_Request.Request_ID
                                        where Tb_Request.Request_ID = '$request_id'");
                                      $po_creator = sqlsrv_fetch_array($po_creator_sql);

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
                                            <h1 class="badge bg-success plant_top_detail text-wrap" style="font-size: 15px;">Plant Details - <span><?php echo $po_creator['Plant']; ?> (<?php echo $plant_detail['Plant_Name']; ?>)</span></h1>

                                            <form method="POST" enctype="multipart/form-data">
                                            	  <input type="hidden" id="po_creator_id" value="<?php echo $po_creator['EMP_ID']; ?>">
											
			                                     <input type="hidden" id="po_plant" value="<?php echo $po_creator['Plant']; ?>">
			                                     <input type="hidden" id="po_mat_group" value="<?php echo $po_creator['MaterialGroup']; ?>">
											     <input type="hidden" id="first_pref_quote_value" value="<?php echo $first_preference_quotation_value; ?>">

                                                <div class="table-wrapper">
                                                    <table id="busDataTable" class="data">
                                                        <tbody class="results" id="rone">
														<tr id="head">
															<th>Particulars</th>
															<?php
																	$view = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query = sqlsrv_fetch_array($view)) {
															?>
															<td><?php echo $view_query['V_id'] ?></td>
															<?php } ?>
														</tr>
														<tr id="one">
															<th>Vendor SAP Code, if available</th>
															<?php
																	$view1 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query1 = sqlsrv_fetch_array($view1)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query1['Vendor_SAP'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="two">
															<th>Name of vendor</th>
															<?php
																	$view2 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query2 = sqlsrv_fetch_array($view2)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query2['Vendor_Name'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="three">
															<th>City of vendor</th>
															<?php
																	$view3 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query3 = sqlsrv_fetch_array($view3)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query3['Vendor_City'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="four">
															<th>Whether vendor is active in SAP?</th>
															<?php
																	$view4 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query4 = sqlsrv_fetch_array($view4)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query4['vendor_Active_SAP'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="five">
															<th>Last purchase made on</th>
															<?php
																	$view5 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query5 = sqlsrv_fetch_array($view5)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query5['Last_Purchase'] ?>"></td>
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
																		$item_code_array = [];
																		$mt_index = 1;
																		while ($row = sqlsrv_fetch_array($result)) {
																			$ID = $row['ID'];
																			$ItemCode = $row['Item_Code'];
																			array_push($item_code_array, $ItemCode);
																			// print_r($ID);
																			?>
																			<tr>
																				<td>
																					<div>
																						<span class="badge badge-soft-primary"><?php echo substr(trim($ItemCode),-10); ?></span>
																						<span class="badge badge-soft-danger ms-3"><?php echo trim($row['UOM']); ?></span>

																					</div>
																					<input type="text" class="form-control-plaintext" readonly value="<?php echo $mt_index.') '.trim($row['Description']) ?>"
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
                                                                                                    	<div class="table-responsive">
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
																		$mt_index++;}
																		?>
																	</tbody>
																</table>

															</th>
															<?php
																	$view6 = sqlsrv_query($conn, "SELECT Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
																	Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id
																	FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
																	ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
																	WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
																
																	GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
																	Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id  ");
																	while ($view_query6 = sqlsrv_fetch_array($view6)) {
																		$arr = $view_query6['V_id'];
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
																		foreach ($item_code_array as $ickey => $icvalue) {
																			
																		$i = 1;
																		$result = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
																		Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name,
																		Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.gst_percentage,Tb_Vendor_Quantity.discount_percentage
																		FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
																		ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
																		WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND  Tb_Vendor_Selection.V_id = '$arr'
																		AND Tb_Vendor_Quantity.V_id = '$arr'
																		and Tb_Vendor_Quantity.Meterial_Name = '".$icvalue."'

																		GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
																		Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name,
																		Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.gst_percentage,Tb_Vendor_Quantity.discount_percentage");
																		// while ($row = sqlsrv_fetch_array($result)) {
																		$row = sqlsrv_fetch_array($result);
																		// echo "<pre>";print_r($row);exit;

																			?>
																			<tr class="material-tr">
																				<td>
																					<input type="text" class="form-control" style="width: 75px;"readonly value="<?php echo $row['Quantity'] ?>">
																				</td>
																				<td>
																					<input type="number" class="form-control" style="width: 75px;"readonly value="<?php echo $row['Price'] ?>">
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_gst_percent_1 gst_percent " style="width: 75px;"
																						name="gst_percent[]" 
																						placeholder="Enter GST" step=".01"  required error-msg='GST is mandatory.' data-rowid="1" readonly id="vendor1_gst_percentage_material<?php echo $i;?>" value="<?php echo $row['gst_percentage'] ?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="number" min="0"
																						class="form-control vendor_discount_percent_1 discount_percent" style="width: 75px;"
																						name="discount_percent[]" 
																						placeholder="Enter Discount" step=".01"  required error-msg='Discount is mandatory.' data-rowid="1" readonly id="vendor1_discount_percentage_material<?php echo $i;?>" value="<?php echo $row['discount_percentage'] ?>">
																					<span class="error_msg text-danger"></span>
																				</td>
																				<td>
																					<input type="text" class="form-control" style="width: 75px;"readonly value="<?php echo $row['Total'] ?>">
																				</td>
																			</tr>
																		<?php
																		}
																		
																		?>
																	</tbody>
																</table>
															</td>
															<?php } ?>
														</tr>
														<tr id="seven1">
															<th>Total</th>
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
															<?php
																	$view9 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query9 = sqlsrv_fetch_array($view9)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query9['Fright_Charges'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="ten">
															<th>Insurance Details</th>
															<?php
																	$view10 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query10 = sqlsrv_fetch_array($view10)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query10['Insurance_Details'] ?>"></td>
															<?php } ?>
														</tr>
                                                        <tr id="ten2">
															<th>Package Forwarding Percentage</th>
															<?php 
															foreach ($charges_arr as $key => $value) {
															?>
															<td>
																<input type="number" min="0" class="form-control package_calc"
																	name="package_percentage[]"
																	placeholder="Enter Packaging percentage" id="package_percentage_<?php echo $key+1; ?>" data-id="<?php echo $key+1; ?>" data-type="percent" readonly value="<?php echo $value['package_percentage'] ?>">
															</td>
															<?php } ?>
														</tr>
														<tr id="ten1">
															<th>Package Forwarding Amount</th>
															<?php 
															foreach ($charges_arr as $key => $value) {
															?>
															<td>
																<input type="number" min="0" class="form-control package_calc"
																name="package_amount[]"
																placeholder="Enter Packaging Charges" id="package_amount_<?php echo $key+1; ?>" data-id="<?php echo $key+1; ?>" data-type="amount" readonly value="<?php echo $value['package_amount'] ?>">
															</td>
                                                           <?php } ?>
														</tr>
														<tr id="eleven">
															<th>GST Component</th>
															<?php
																	$view11 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query11 = sqlsrv_fetch_array($view11)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query11['GST_Component'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="eleven1">
															<th>Discount Amount</th>
                                                            <?php 
                                                            foreach ($charges_arr as $key => $value) {
                                                            ?>
                                                            
                                                            <td>
                                                                <input type="number" min="0" class="form-control"
                                                                    name="discount_amount[]"
                                                                    placeholder="Enter Discount Amount" id="discount_amount_<?php echo $key+1; ?>" readonly value="<?php echo $value['discount_amount'] ?>">
                                                            </td>

                                                            <?php } ?>
														</tr>
														<tr id="seven">
															<th>Net Amount</th>
															<?php
																	$view7 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query7 = sqlsrv_fetch_array($view7)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query7['Value_Of'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="eight">
															<th>Delivery Time</th>
															<?php
																	$view8 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query8 = sqlsrv_fetch_array($view8)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query8['Delivery_Time'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="twelve">
															<th>Warrenty</th>
															<?php
																	$view12 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query12 = sqlsrv_fetch_array($view12)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query12['Warrenty'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="thirteen">
															<th>Payment Terms</th>
															<?php
																	$view13 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query13 = sqlsrv_fetch_array($view13)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query13['Payment_Terms'] ?>"></td>
															<?php } ?>
														</tr>
													</tbody>
													<div class="separator-solid"></div>

													<tbody id="eone">
														<tr id="checkbox">
															<th>Requester's selection</th>
															<?php
																	$view14 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query14 = sqlsrv_fetch_array($view14)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query14['Requester_Selection'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="text">
														<?php 
															$requester_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Purchaser_Code' ");
															$requester_names = sqlsrv_fetch_array($requester_name);
															$name = $requester_names['Employee_Name'];
															?>
                                                            <th>Requester's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span></th>
															<?php
																	$view15 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	while ($view_query15 = sqlsrv_fetch_array($view15)) {
															?>
															<td><input type="text" Class="form-control" readonly value="<?php echo $view_query15['Requester_Remarks'] ?>"></td>
															<?php } ?>
														</tr>
														<tr id="pdf">
														<?php
																	$view16 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	$view_query16 = sqlsrv_fetch_array($view16);
															?>
															<?php 
															if($view_query16['Attachment'] == ''){

															}else{
																?>
																<th>PDF / JPG attachment
															</th>
															<?php
																	$view16 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
																	$rindex = 1;
																	while ($view_query16 = sqlsrv_fetch_array($view16)) {

																$file_extension = explode('.', $view_query16['Attachment'])[1];
																		
															?>
															<td>
																<input type="text" class="form-control" readonly value="<?php echo $view_query16['Attachment'] ?>"
                                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center<?php echo $view_query16['Id'] ?>">


																<!-- file preview modal -->
																<div class="modal fade modal_css_load" id="file_preview_modal_<?php echo $rindex; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
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
													                	$multi_files = explode(',',$view_query16['Attachment']);

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
																<?php
															}
															?>
															
														</tr>
													</tbody>
                                            
                                                    </table>
                                                </div>

                                                <div class="row" id="involved_persons_div" style="display:none;">
                                                    <div class="col-md-5">
                                                        <h4>Involved Persons</h4>
                                                        <div class="table-responsive">
	                                                        <table class="table table-striped table-bordered table-hover" >
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
                                                </div>

                                                <?php if(COUNT($saved_data) == 1 && ($po_creator['vendor_justification'] != null)) { ?>
                                                <br>
                                                <div class="row justification_div justify-content-center"> 
                                                    <div class="col-2">
                                                        <label for="justification" class="float-end">Justification For Single Vendor<span class="text-danger"> *</span></label>
                                                    </div>   
                                                    <div class="col-6">
	                                                    <textarea class="form-control required_for_valid" name="justification" id="justification" required readonly error-msg="Justification field is required"><?php echo $po_creator['vendor_justification']; ?>
	                                                    </textarea>
	                                                    <span class="error_msg text-danger" style="display: none;"></span>
	                                                </div>
                                                </div>
                                            	<?php } ?> 

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
        
        <!-- CUSTOM SCRIPT END -->

       	<!-------Model Trag and Trap ---------->
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
		<!-----------End --------------->

        <script type="text/javascript">

	        	$(document).ready(function() {
					var first_pref_quote_value = $('#first_pref_quote_value').val();
					var request_id = '<?php echo $request_id ?>';
					var emp_id = '<?php echo $Employee_Id ?>';

					if(first_pref_quote_value != '') {
						get_involved_persons(first_pref_quote_value);
						get_mapping_details(request_id,first_pref_quote_value,emp_id);
					}

				});

				function get_mapping_details(request_id, quotation_value, po_creator_id) {
					$.ajax({
						url: 'common_ajax.php',
						type: 'POST',
						data: {
							Action: 'get_emp_mapping_details',
							request_id: request_id,
							quotation_value: quotation_value,
							po_creator_id: po_creator_id
						},
						dataType: "json",
						success: function(response) {
							$('#recommendor_id').val(response[0].Recommender);
							$('#finance_verifier_id').val(response[0].Finance_Verfier);
							$('#approver_id').val(response[0].Approver);
							$('#approver2_id').val(response[0].Approver_2);
							$('#mapping_id').val(response[0].id);
						}
					});
				}

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
  							$('.inv_fin_appr').hide();

                            var table_data = '';
                            if(result.length > 0) {
                                table_data = `<tr>
                                <td>${ (result[0].purchaser_name != null) ? result[0].purchaser_name : '-' }</td>
                                <td>${ (result[0].recommendor_name != null) ? result[0].recommendor_name : '-' }</td>
                                <td>${ (result[0].approver_name != null) ? result[0].approver_name : '-' }</td>`;

                                if(result[0].approver2_name != null) {
                                    table_data += `<td>${ (result[0].approver2_name != null) ? result[0].approver2_name : '-' }</td>`;
                                    $('.inv_fin_appr').show();
                                }

                                table_data += `</tr>`; 

                            }
                            $('#involved_persons_tbody').html(table_data);  
                            $('#involved_persons_div').show();                                 
		                },
		                complete:function(){
		                    // $('#ajax-preloader').hide();
		                }
		            });
		        }

        	$(document).on('click','.multi_preview',function(){
    				var file_type = $(this).data('filetype');
    				var src = $(this).attr('src');
    				var row_id = $(this).data('id');


					 if(file_type == 'pdf') {
					 	// var src = $('#pdf_input'+row_id).val();
                        var src = $(this).closest('div').find('#pdf_input'+row_id).val();
                        var src_url = 'https://docs.google.com/viewer?url=https://corporate.rasiseeds.com/corporate/final_request/'+src+'&embedded=true';
	                	$('.preview_file_pdf_'+row_id).attr('src', src_url+'#toolbar=0');
	                	$('.preview_file_img_'+row_id).hide();
	                	$('.preview_file_pdf_'+row_id).show();
				     } else {
	                	$('.preview_file_img_'+row_id).attr('src', src);
	                	$('.preview_file_pdf_'+row_id).hide();
	                	$('.preview_file_img_'+row_id).show();
				     } 
		        	$('#file_preview_modal_'+row_id).modal('show');

		  		});

        	$(".modal").draggable({
                handle: ".modal-content"
            });
        </script>

    </body>
</html>
