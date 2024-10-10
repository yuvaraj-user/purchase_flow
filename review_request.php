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
	//     echo '<pre>';
    // print_r($_POST);die();
    $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
    if($updated_query['Verification_Type'] == '1'){

    
	for ($i = 0; $i < count($_POST['Vendor_Name']); $i++) {
        $emp_id = $Employee_Id;
        $Requested_to = $Purchaser_Code;

		$Vendor_Name = $_POST['Vendor_Name'][$i];
		$w_budget = $_POST['w_budget'][$i];
		$t_budget = $_POST['t_budget'][$i];
		$a_budget = $_POST['a_budget'][$i];
		$wi_budget = $_POST['wi_budget'][$i];
		$remark = $_POST['remark'][$i];
        $Value_Of = $_POST['Value_Of'][$i];
        // echo "INSERT INTO Tb_Finance_verifier (Request_Id,  Whether_Budget,Total_Budget,Available_Budget, 
        // Whether_Within_Budget, Remark, Status, Vendor_Name,Value_Of, Time_Log,EMP_ID,Requested_to) VALUES 
        // ('$request_id','$w_budget','$t_budget','$a_budget','$wi_budget','$remark','Recommended','$Vendor_Name','$Value_Of',GETDATE(),'$emp_id','$Requested_to')";
        // die();
		$query = "INSERT INTO Tb_Finance_verifier (Request_Id,  Whether_Budget,Total_Budget,Available_Budget, 
        Whether_Within_Budget, Remark, Status, Vendor_Name,Value_Of, Time_Log,EMP_ID,Requested_to) VALUES 
        ('$request_id','$w_budget','$t_budget','$a_budget','$wi_budget','$remark','Added','$Vendor_Name','$Value_Of',GETDATE(),'$emp_id','$Requested_to')";
		$rs = sqlsrv_query($conn, $query);

		$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Added',Requested_to = '$Purchaser_Code' WHERE Request_Id = '$request_id' ");
		$query12 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Added',Requested_to = '$Purchaser_Code' WHERE Request_Id = '$request_id' ");

		?>
		<script type="text/javascript">
			alert("Reviewed successsfully");
			window.location = "show_verifier.php";
		</script>
		<?php

	}
}else{

    if($updated_query['Verification_Type'] == '2'){
	//     echo '<pre>';
    // print_r($_POST);die();
        for ($i = 0; $i < count($_POST['Vendor_Name']); $i++) {
            $emp_id = $Employee_Id;
            $Requested_to = $Purchaser_Code;
    
            $Vendor_Name = $_POST['Vendor_Name'][$i];
            $w_budget = $_POST['w_budget'][$i];
            $t_budget = $_POST['t_budget'][$i];
            $a_budget = $_POST['a_budget'][$i];
            $wi_budget = $_POST['wi_budget'][$i];
            $remark = $_POST['remark'][$i];
            $Value_Of = $_POST['Value_Of'][$i];
            
            $Finance_Verification = 'Verified';
            // echo "INSERT INTO Tb_Finance_verifier (Request_Id,  Whether_Budget,Total_Budget,Available_Budget, 
            // Whether_Within_Budget, Remark, Status, Vendor_Name,Value_Of, Time_Log,EMP_ID,Requested_to) VALUES 
            // ('$request_id','$w_budget','$t_budget','$a_budget','$wi_budget','$remark','Recommended','$Vendor_Name','$Value_Of',GETDATE(),'$emp_id','$Requested_to')";
            // die();
            $query = "INSERT INTO Tb_Finance_verifier (Request_Id,  Whether_Budget,Total_Budget,Available_Budget, 
            Whether_Within_Budget, Remark, Status, Vendor_Name,Value_Of, Time_Log,EMP_ID,Requested_to) VALUES 
            ('$request_id','$w_budget','$t_budget','$a_budget','$wi_budget','$remark','Recommended','$Vendor_Name','$Value_Of',GETDATE(),'$emp_id','$Requested_to')";
            $rs = sqlsrv_query($conn, $query);
    
            $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Recommended',Requested_to = '$Purchaser_Code' WHERE Request_Id = '$request_id' ");
            $query12 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Recommended',Requested_to = '$Purchaser_Code' WHERE Request_id = '$request_id' ");
            $query12 = sqlsrv_query($conn, "UPDATE Tb_Recommender set status = 'Recommended',Finance_Verification = '$Finance_Verification' WHERE Request_Id = '$request_id' ");
            $query12 = sqlsrv_query($conn, "UPDATE Tb_Recommender_Meterial set status = 'Recommended' WHERE Request_id = '$request_id' ");
    
            ?>
            <script type="text/javascript">
                alert("Reviewed successsfully");
                window.location = "show_verifier.php";
            </script>
            <?php
    
        }
exit;
    }else{
    for ($i = 0; $i < count($_POST['Vendor_Name']); $i++) {
        $emp_id = $Employee_Id;
        $Requested_to = $Purchaser_Code;

		$Vendor_Name = $_POST['Vendor_Name'][$i];
		$w_budget = $_POST['w_budget'][$i];
		$t_budget = $_POST['t_budget'][$i];
		$a_budget = $_POST['a_budget'][$i];
		$wi_budget = $_POST['wi_budget'][$i];
		$remark = $_POST['remark'][$i];
        $Value_Of = $_POST['Value_Of'][$i];
        // echo "INSERT INTO Tb_Finance_verifier (Request_Id,  Whether_Budget,Total_Budget,Available_Budget, 
        // Whether_Within_Budget, Remark, Status, Vendor_Name,Value_Of, Time_Log,EMP_ID,Requested_to) VALUES 
        // ('$request_id','$w_budget','$t_budget','$a_budget','$wi_budget','$remark','Recommended','$Vendor_Name','$Value_Of',GETDATE(),'$emp_id','$Requested_to')";
        // die();
		$query = "INSERT INTO Tb_Finance_verifier (Request_Id,  Whether_Budget,Total_Budget,Available_Budget, 
        Whether_Within_Budget, Remark, Status, Vendor_Name,Value_Of, Time_Log,EMP_ID,Requested_to) VALUES 
        ('$request_id','$w_budget','$t_budget','$a_budget','$wi_budget','$remark','Recommended','$Vendor_Name','$Value_Of',GETDATE(),'$emp_id','$Requested_to')";
		$rs = sqlsrv_query($conn, $query);

		$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Recommended',Requested_to = '$Purchaser_Code' WHERE Request_Id = '$request_id' ");
		$query12 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Recommended',Requested_to = '$Purchaser_Code' WHERE Request_Id = '$request_id' ");

		?>
		<script type="text/javascript">
			alert("Reviewed successsfully");
			window.location = "show_verifier.php";
		</script>
		<?php

	}
}
}
}

if (isset($_POST["send"])) {
    // 	echo '<pre>';
	// print_r($_POST);die();
	for ($i = 0; $i < count($_POST['Vendor_Name']); $i++) {
        $emp_id = $Employee_Id;
		// $category = $_POST['category'][$i];
		// $type = $_POST['type'][$i];
		$Vendor_Name = $_POST['Vendor_Name'][$i];
		$w_budget = $_POST['w_budget'][$i];
		$t_budget = $_POST['t_budget'][$i];
		$a_budget = $_POST['a_budget'][$i];
		$wi_budget = $_POST['wi_budget'][$i];
		$remark = $_POST['remark'][$i];
        $Value_Of = $_POST['Value_Of'][$i];
		
        for ($j = 0; $j < count($_POST['sendback']); $j++) {
            $sendback_remark = $_POST['sendback'][$j];

		$query = "INSERT INTO Tb_Finance_verifier (Request_Id,  Whether_Budget,Total_Budget,Available_Budget, 
	  Whether_Within_Budget, Remark, Status, Vendor_Name,Value_Of, Sendback_remarks, EMP_ID) VALUES 
	  ('$request_id','$w_budget','$t_budget','$a_budget','$wi_budget','$remark','Send Back','$Vendor_Name','$Value_Of', '$sendback_remark','$emp_id')";
		$rs = sqlsrv_query($conn, $query);

        // echo "UPDATE Tb_Request_Items set status = 'Send Back', Sendback_remark = '$sendback_remark' WHERE Request_Id = '$Request_ID' ";
        // die();
		$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Send Back', Sendback_remark = '$sendback_remark' WHERE Request_Id = '$request_id' ");
		$query12 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Send Back', Sendback_remark = '$sendback_remark' WHERE Request_ID = '$request_id' ");
        
		?>
		<script type="text/javascript">
			alert("Send Back successsfully");
			window.location = "show_verifier.php";
		</script>
		<?php

	}
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
                                             <li class="breadcrumb-item active">Review Request</li>
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
                                                            <td><input type="text" class="form-control" readonly name="Vendor_SAP[]"
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
																		WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection  = '1' 
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
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                                            <th>Requester's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span></th>
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
																	$view16 = sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id' ");
																	$view_query16 = sqlsrv_fetch_array($view16);
														?>
														<?php
                                                        if($view_query16['Verification_Type'] == ''){

                                                        }else{
                                                            ?>
                                                            <tr id="tara" class="src-table">
															<th>Approver's Selection</th>
                                                            <?php
															$array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Selection
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection  = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Selection ORDER BY Tb_Approver.V_id DESC");
															$array_dy_Details14 = sqlsrv_fetch_array($array_Details14);
                                                            ?>
															<td class="woo">
																	<input type="text" class="form-control " 
																	readonly name="Recommender_Selection[]"
																	value="<?php echo $array_dy_Details14['Approver_Selection'] ?>">
															</td>
															<?php
															$array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Selection
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Selection ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

																?>
																<td class="woo">
																		<input type="text" class="form-control "
																	readonly name="Recommender_Selection[]" 
																	value="<?php echo $array_dy_Details14['Approver_Selection'] ?>">
																</td>
															<?php } ?>

														</tr>
                                                        <tr>
                                                        <?php 
															$requester_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$approver_name' ");
															$requester_names = sqlsrv_fetch_array($requester_name);
															$name = $requester_names['Employee_Name'];
															?>
                                                            <th>Approver's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span></th>
															</th>
                                                            <?php
															$array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection  = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks ORDER BY Tb_Approver.V_id DESC");
															$array_dy_Details14 = sqlsrv_fetch_array($array_Details14);
                                                            ?>
															<td><input type="text" class="form-control " readonly 
																	name="Recommender_Remarks[]"
																	value="<?php echo $array_dy_Details14['Approver_Remarks'] ?>">
															</td>
															<?php
															$array_Details121 = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks
															FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection ! = '1' 
															GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
															Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.Approver_Remarks ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

																?>
																<td><input type="text" readonly class="form-control " 
																		name="Recommender_Remarks[]"
																		value="<?php echo $array_dy_Details121['Approver_Remarks'] ?>">
																</td>
															<?php } ?>

														</tr>
                                                            <?php
                                                        }
                                                        ?>
														<tr class="remark">
															<th>Whether Budgeted<span
																	class="required-label"></span>
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
																<td><select class="form-control"  name="w_budget[]">
                                                                            <option value="Yes">Yes</option>
                                                                            <option valu="No">No</option>
                                                                        </select>
															<?php } ?>
														</tr>
                                                        <tr>
															<th>Total Budget (in INR)<span class="required-label"></span>
															</th>
															<td><input type="number" min="0" name="t_budget[]" required id="myInput1" onchange="myChangeFunction(this)"
                                                                            class="form-control">
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
																<td><input type="number" min="0" name="t_budget[]" 
                                                                            class="form-control myInput2">
																</td>
															<?php } ?>

														</tr>

                                                        <tr>
															<th>Available Budget (in INR)<span class="required-label"></span>
															</th>
															<td><input type="number" min="0" name="a_budget[]" required id="myInput3" onchange="myChangeFunction1(this)"
                                                                            class="form-control">
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
																<td><input type="number" min="0" name="a_budget[]"
                                                                            class="form-control myInput4">
																</td>
															<?php } ?>

														</tr>

                                                        <tr class="remark">
															<th>Within Budget<span
																	class="required-label"></span>
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
																<td><select class="form-control"  name="wi_budget[]">
                                                                            <option value="Yes">Yes</option>
                                                                            <option valu="No">No</option>
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
															
															<td><input type="text" class="form-control " required id="myInput5" onchange="myChangeFunction2(this)"
																		name="remark[]"
																		placeholder="Enter Finance Remarks">
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
																<td><input type="text" class="form-control myInput6"
																		name="remark[]"
																		placeholder="Enter Finance Remarks">
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
															WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
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
                                                    <button type="button" class="btn btn-warning btn-sm waves-effect waves-light" style="width: 150px;" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">Send Back</button>
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

                    $(document).ready(function() {
                    $("input[type=number]").on("focus", function() {
                        $(this).on("keydown", function(event) {
                        if (event.keyCode === 38 || event.keyCode === 40) {
                            event.preventDefault();
                        }
                        });
                    });
                    });
                    function myChangeFunction(input1) {

                        var input2 = $('.myInput2').val(input1.value);
                    }
                    function myChangeFunction1(input3) {

                    var input4 = $('.myInput4').val(input3.value);
                    }
                    function myChangeFunction2(input5) {

                    var input6 = $('.myInput6').val(input5.value);
                    }
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
            });
            // Recommender selection copy as and show hide//

            $(".root").hide();
            $(".answer").find('.request_selection').attr('disabled',false).attr("required", true);
            $(document).on("click", "#submit", function () {
                if (this.checked) {

                    $(".answer").hide();
                    $(".remark").hide();
                    $(".root").show();
                    $(".answer").find('.request_selection').attr('disabled',true).attr("required", false);        
                    
                } else {
                    $(".answer").show();
                    $(".remark").show();
                    $(".root").hide();
                    $(".answer").find('.request_selection').attr('disabled',false).attr("required", true);
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
        </script>

        <!-- CUSTOM SCRIPT END -->

    </body>
</html>
