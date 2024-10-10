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
$Request_ID = $_GET['id'];
$Employee_Id = $_SESSION['EmpID'];

$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
WHERE Finance_Verfier = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$verifier_name = $selector_arr1['Finance_Verfier'];
$verifier_code = $selector_arr1['Finance_Verfier'];
$Purchaser_Code = $selector_arr1['Purchaser'];
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];
if (isset($_POST["save"])) {
	for ($i = 0; $i < count($_POST['w_budget']); $i++) {
		$w_budget = $_POST['w_budget'][$i];
		$t_budget = $_POST['t_budget'][$i];
		$a_budget = $_POST['a_budget'][$i];
		$wi_budget = $_POST['wi_budget'][$i];
		$remark = $_POST['remark'][$i];

        // echo "UPDATE Tb_Finance_verifier SET Whether_Budget = '$w_budget',Total_Budget = '$t_budget',Available_Budget = '$a_budget',Whether_Within_Budget = '$wi_budget',
        // Remark = '$remark' WHERE Request_Id ='".$Request_ID."'";
        // die();
        $query = "UPDATE Tb_Finance_verifier SET Whether_Budget = '$w_budget',Total_Budget = '$t_budget',Available_Budget = '$a_budget',Whether_Within_Budget = '$wi_budget',
        Remark = '$remark' WHERE Request_Id ='".$Request_ID."'";
        $rs = sqlsrv_query($conn, $query);

		?>
		<script type="text/javascript">
			alert("Reviewed successsfully");
			window.location = "show_verifier.php";
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

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />


        
        <!-- DataTables -->
        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />  

        
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
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
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
                                             <li class="breadcrumb-item"><a href="show_verifier.php">Show Verifier Request</a></li>
                                             <li class="breadcrumb-item active"> Update Review Request</li>
                                         </ol>
                                 </div>
                             </div>
                             <div class="col-sm-6">
                                <div class="float-end d-none d-sm-block">
                                    <h4 class="">Request ID : <?php echo $Request_ID ?></h4>
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
                            <table id="busDataTable" class="data heading-table">
                                <?php
                                $selector = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.V_id,Tb_Recommender.Vendor_SAP,Tb_Recommender.Vendor_Name,Tb_Recommender.Vendor_City,Tb_Recommender.vendor_Active_SAP,
                                Tb_Recommender.Last_Purchase,Tb_Recommender.Delivery_Time1,Tb_Recommender.Value_Of,Tb_Recommender.Fright_Charges,Tb_Recommender.Insurance_Details,Tb_Recommender.GST_Component,
                                Tb_Recommender.Warrenty,Tb_Recommender.Payment_Terms,Tb_Recommender.Requester_Remarks,Tb_Recommender.Recommender_Remarks,Tb_Recommender.Attachment,
                                Tb_Recommender.Status,Tb_Recommender.Requester_Selection,Tb_Recommender.Recommender_Selection,Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total
                                FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id
                                WHERE Tb_Recommender.Request_id = '$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id  AND Tb_Recommender.Recommender_Selection = '1'
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
                                    WHERE Tb_Recommender.Request_id = '$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id  AND Tb_Recommender.Recommender_Selection ! = '1'
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
                                    <td><input type="text" class="form-control" readonly name="Vendor_SAP[]"
                                            value="<?php echo $selector_arr['Vendor_SAP'] ?>">
                                    </td>
                                    <?php
                                    $array_Details1 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Vendor_SAP
                                    FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Vendor_SAP ORDER BY Tb_Recommender.V_id DESC");
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
                                    $array_Details2 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Vendor_Name
                                    FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                                $result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$Request_ID'",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
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
                                                $result = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                                Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                                Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total
                                                FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                                WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection  = '1' 
                                                GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                                Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                                Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total ORDER BY Tb_Recommender.V_id DESC");
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
                                    $array_Details111 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id
                                    FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id 
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
                                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = '$arr' AND Tb_Recommender_Meterial.V_id = '$arr'
                                                    AND Tb_Recommender.Recommender_Selection ! = '1' 
                                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                                    Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total ORDER BY Tb_Recommender.V_id DESC");
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
                                    $array_Details7 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Value_Of
                                    FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Value_Of ORDER BY Tb_Recommender.V_id DESC");
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
                                            value="<?php echo $selector_arr['Delivery_Time1'] ?>">
                                    </td>
                                    <?php
                                    $array_Details8 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Delivery_Time1
                                    FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                                            <th>Requester's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span></th>
                                    </th>
                                    <td><input type="text" class="form-control " readonly 
                                            name="Requester_Remarks[]"
                                            value="<?php echo $selector_arr['Requester_Remarks'] ?>">
                                    </td>
                                    <?php
                                    $array_Details15 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Requester_Remarks
                                    FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                    <th>Recommender's Selection</th>
                                    <td class="woo">
                                            <input type="text" class="form-control " 
                                            readonly name="Recommender_Selection[]"
                                            value="<?php echo $selector_arr['Recommender_Selection'] ?>">
                                    </td>
                                    <?php
                                    $array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection
                                    FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                
                                
                                <tr>
                                <?php 
															$requester_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Recommender_Code' ");
															$requester_names = sqlsrv_fetch_array($requester_name);
															$name = $requester_names['Employee_Name'];
															?>
                                                            <th>Recommender's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span></th>
                                    <td><input type="text" class="form-control " readonly 
                                            name="Recommender_Remarks[]"
                                            value="<?php echo $selector_arr['Recommender_Remarks'] ?>">
                                    </td>
                                    <?php
                                    $array_Details121 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                    Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Remarks
                                    FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                
                                <tr class="remark">
                                    <th>Whether Budgeted<span
                                            class="required-label"></span>
                                    </th>
                                    <?php
                                    $array_Details11 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Whether_Budget FROM Tb_Recommender 
                                    INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id WHERE Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Whether_Budget");
                                   $array_dy_Details11 = sqlsrv_fetch_array($array_Details11)

                                        ?>
                                        <td><select class="form-control"  name="w_budget[]">
                                                    <option <?php if ($array_dy_Details11['Whether_Budget'] == "Yes") { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                    <option <?php if ($array_dy_Details11['Whether_Budget'] == "No") { ?> selected="selected" <?php } ?> valu="No">No</option>
                                                </select>
                                    </td>
                                    <?php
                                    $array_Details11 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,
                                    Tb_Finance_verifier.Whether_Budget FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id WHERE
                                     Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection != '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Whether_Budget");
                                   while($array_dy_Details11 = sqlsrv_fetch_array($array_Details11)){

                                        ?>
                                        <td><select class="form-control"  name="w_budget[]">
                                                    <option <?php if ($array_dy_Details11['Whether_Budget'] == "Yes") { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                    <option <?php if ($array_dy_Details11['Whether_Budget'] == "No") { ?> selected="selected" <?php } ?> valu="No">No</option>
                                                </select>
                                   <?php } ?>
                                </tr>
                                <tr>
                                    <th>Total Budget (in INR)<span class="required-label"></span>
                                    </th>
                                    <?php
                                    $array_Details11 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,
                                    Tb_Finance_verifier.Total_Budget FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Total_Budget");
                                   $array_dy_Details11 = sqlsrv_fetch_array($array_Details11)

                                        ?>
                                    <td><input type="number" min="0" name="t_budget[]" Value="<?php echo $array_dy_Details11['Total_Budget'] ?>"
                                                    class="form-control">
                                    </td>
                                    <?php
                                    $array_Details121 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,
                                    Tb_Finance_verifier.Total_Budget FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Total_Budget");
                                    while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

                                        ?>
                                        <td><input type="number" min="0" name="t_budget[]"  Value="<?php echo $array_dy_Details121['Total_Budget'] ?>"
                                                    class="form-control">
                                        </td>
                                    <?php } ?>

                                </tr>

                                <tr>
                                    <th>Available Budget (in INR)<span class="required-label"></span>
                                    </th>
                                    <?php
                                    $array_Details11 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,
                                    Tb_Finance_verifier.Available_Budget FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Available_Budget");
                                   $array_dy_Details11 = sqlsrv_fetch_array($array_Details11)

                                        ?>
                                    <td><input type="number" min="0" name="a_budget[]" Value="<?php echo $array_dy_Details11['Available_Budget'] ?>"
                                                    class="form-control">
                                    </td>
                                    <?php
                                    $array_Details121 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,
                                    Tb_Finance_verifier.Available_Budget FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Available_Budget");
                                    while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

                                        ?>
                                        <td><input type="number" min="0" name="a_budget[]" Value="<?php echo $array_dy_Details121['Available_Budget'] ?>"
                                                    class="form-control">
                                        </td>
                                    <?php } ?>

                                </tr>

                                <tr class="remark">
                                    <th>Within Budget<span
                                            class="required-label"></span>
                                    </th>
                                    <?php
                                    $array_Details11 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,
                                    Tb_Finance_verifier.Whether_Within_Budget FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Whether_Within_Budget");
                                   $array_dy_Details11 = sqlsrv_fetch_array($array_Details11)

                                        ?>
                                        <td><select class="form-control"  name="wi_budget[]">
                                                    <option <?php if ($array_dy_Details11['Whether_Within_Budget'] == "Yes") { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                    <option <?php if ($array_dy_Details11['Whether_Within_Budget'] == "No") { ?> selected="selected" <?php } ?>  valu="No">No</option>
                                                </select></td>
                                    <?php
                                    $array_Details11 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,
                                    Tb_Finance_verifier.Whether_Within_Budget FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Whether_Within_Budget");
                                    while ($array_dy_Details11 = sqlsrv_fetch_array($array_Details11)) {

                                        ?>
                                        <td><select class="form-control"  name="wi_budget[]">
                                                    <option <?php if ($array_dy_Details11['Whether_Within_Budget'] == "Yes") { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                    <option <?php if ($array_dy_Details11['Whether_Within_Budget'] == "No") { ?> selected="selected" <?php } ?>  valu="No">No</option>
                                                </select></td>
                                    <?php } ?>
                                </tr>

                                <tr>
                                <?php 
															$requester_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$verifier_code' ");
															$requester_names = sqlsrv_fetch_array($requester_name);
															$name = $requester_names['Employee_Name'];
															?>
                                                            <th>Finance's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span></th>
                                    <?php
                                    $array_Details11 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,
                                    Tb_Finance_verifier.Remark FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Remark");
                                   $array_dy_Details11 = sqlsrv_fetch_array($array_Details11)

                                        ?>
                                    <td><input type="text" class="form-control " Value="<?php echo $array_dy_Details11['Remark'] ?>"
                                                name="remark[]"
                                                placeholder="Enter Finance Remarks">
                                    </td>
                                    <?php
                                    $array_Details121 = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,
                                    Tb_Finance_verifier.Remark FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                    WHERE Tb_Recommender.Request_id ='$Request_ID'
                                    AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1'
                                    GROUP BY Tb_Recommender.Request_id,Tb_Recommender.Vendor_Name,Tb_Finance_verifier.Request_Id,Tb_Finance_verifier.Vendor_Name,Tb_Finance_verifier.Remark");
                                    while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

                                        ?>
                                        <td><input type="text" class="form-control " Value="<?php echo $array_dy_Details121['Remark'] ?>"
                                                name="remark[]"
                                                placeholder="Enter Finance Remarks">
                                        </td>
                                    <?php } ?>

                                </tr>

                                <tr id="pdf">
                                                        <?php
																	$view16 = sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$Request_ID' ");
																	$view_query16 = sqlsrv_fetch_array($view16);
															?>
															<?php 
															if($view_query16['Attachment'] == ''){

															}else{
																?>
                                                            <th>PDF / JPG attachment
															</th>
                                                            <?php 
                                                            }?>
															<?php 
															if($selector_arr['Attachment'] == ''){

															}else{
																?>
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
                                                            }
                                                            ?>
                                                            <?php
															$array_Details16 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Attachment,Tb_Recommender.id
															FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
															WHERE Tb_Recommender.Request_id ='$Request_ID' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
															GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
															Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Attachment,Tb_Recommender.id ORDER BY Tb_Recommender.V_id DESC");
															while ($array_dy_Details16 = sqlsrv_fetch_array($array_Details16)) {
															?>
                                                            <?php 
															if($array_dy_Details16['Attachment'] == ''){

															}else{
																?>
                                                            <td><input type="text" class="form-control" name="Attachment[]" readonly value="<?php echo $array_dy_Details16['Attachment'] ?>"
                                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center1<?php echo $array_dy_Details16['id'] ?>">
																<div class="modal fade bs-example-modal-center1<?php echo $array_dy_Details16['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
                                                            <?php } ?>
														</tr>
                            </tbody>
                    
                            </table>
                        </div>
                        <center style="padding: 0px 0px 30px 0px;">
                            <button class="btn btn-success waves-effect waves-light btn-sm" type="submit" name="save" style="width: 150px;">Submit</button>
                        </center>
                        <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title mt-0">Send Back Remark</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                            
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="input-group mb-3">
                                                <textarea class="form-control form-control-sm" name="sendback[]" aria-label="With textarea"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary btn-sm" name="send">Submit</button>
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

        <!-- Right Sidebar -->

        <!-- /Right-bar -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>

         <!-- Required datatable js -->
         <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
         <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
         <!-- Buttons examples -->
         <script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
         <script src="assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
         <script src="assets/libs/jszip/jszip.min.js"></script>
         <script src="assets/libs/pdfmake/build/pdfmake.min.js"></script>
         <script src="assets/libs/pdfmake/build/vfs_fonts.js"></script>
         <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
         <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
         <script src="assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
         <!-- Responsive examples -->
         <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
         <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

          <!-- Datatable init js -->
        <script src="assets/js/pages/datatables.init.js"></script>   
 

        <script src="assets/js/app.js"></script>

    </body>
</html>