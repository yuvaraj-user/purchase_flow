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
// global query
$req_id = $_GET['request_id'];
$Employee_Id = $_SESSION['EmpID'];
$selector = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
WHERE Employee_Code = '$Employee_Id' ");
$selector_arr = sqlsrv_fetch_array($selector);

$department = $selector_arr['Department'];
$L1_Manager = $selector_arr['L1 Manager'];

$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
WHERE PO_creator_Release_Codes = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$verifier_name = $selector_arr1['Finance_Verfier'];
$verifier_code = $selector_arr1['Finance_Verfier'];
$Purchaser_Code = $selector_arr1['Purchaser'];
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];

//end

if (isset($_POST["save"])) {
    // echo '<pre>';
    // print_r($_POST);die();
    $emp_id = $Employee_Id;
    $Requested_to = $Purchaser_Code;
    $request_id = $_POST['request_id'];
    $request_type = $_POST['request_type'];
    $plant_id = $_POST['plant_id'];
    $storage_location = $_POST['storage_location'];
    $request_type1 = $_POST['request_type1'];
    $Department = $_POST['department'];
    if (isset($_POST['persionp']))
    {
        $Persion= implode(',',$_POST['persionp']);
        $status = 'Requested';
        foreach ($_POST['Finance_Verification'] as $key => $value) {
          if($value == 'Yes') {
            $status = 'Review';
            $Requested_to = $verifier_code;
          }
        }
//     echo "INSERT INTO Tb_Request (Request_ID, Request_Type, Plant, Storage_Location,Time_Log,Request_Category,
//     status, Department,Persion_In_Workflow,EMP_ID,Requested_to) VALUES 
//    ('$request_id','$request_type','$plant_id','$storage_location',GETDATE(),'$request_type1','$status','$Department','$Persion','$emp_id','$Requested_to')";
//       die();
$query = "UPDATE Tb_Request SET Request_ID = '$request_id',Request_Type = '$request_type',Plant = '$plant_id',Storage_Location = '$storage_location',Time_Log = GETDATE(),
Request_Category = '$request_type1',status = '$status',Department = '$Department',Persion_In_Workflow = '$Persion',EMP_ID = '$emp_id',Requested_to = '$Requested_to'
WHERE Request_ID ='".$req_id."'";
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
        $Finance_Verification = $_POST['Finance_Verification'][$i];

            // echo "INSERT INTO Tb_Request_Items (Request_ID, Item_Code, UOM, Description, Quantity, Budget, Time_Log, status, Expected_Date, Specification, Attachment, Budject_Remark,
            // MaterialGroup, Finance_Verification,EMP_ID,Requested_to) VALUES 
            // ('$request_id','$item_code','$uom','$description','$quantity','$budgest',GETDATE(),'$status','$Expected_Date','$Specification','$fil','$budget_remark',
            //  '$MaterialGroup','$Finance_Verification','$emp_id','$Requested_to')";
            // die();

            $sql = "UPDATE Tb_Request_Items SET Request_ID = '$request_id',Item_Code = '$item_code',UOM = '$uom',Description = '$description',Quantity = '$quantity',
            Budget = '$budgest',Time_Log = GETDATE(),status = '$status',Expected_Date = '$Expected_Date',Specification = '$Specification',Attachment = '$fil',
            Budject_Remark = '$budget_remark',MaterialGroup ='$MaterialGroup',Finance_Verification = '$Finance_Verification',EMP_ID = '$emp_id',Requested_to ='$Requested_to'
            WHERE Request_ID ='".$req_id."'";

        $params = array("updated data", 1);

        $stmt = sqlsrv_query($conn, $sql, $params);

        $rows_affected = sqlsrv_rows_affected($stmt);
        if ($rows_affected === false) {
            die(print_r(sqlsrv_errors(), true));
        } elseif ($rows_affected == -1) {
            echo "No information available.<br />";
        } else {
            move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);
            ?>
            <script type="text/javascript">
            alert("Request added successsfully");
            window.location = "show_purchacevendor_request.php";
            </script>
<?php
        }

    }
}
}
if (isset($_POST["save1"])) {
    // echo '<pre>';
    // print_r($_POST);die();
    $emp_id = $Employee_Id;
    $Requested_to = $Purchaser_Code;
    $request_id = $_POST['request_id'];
    $request_type = $_POST['request_type'];
    $plant_id = $_POST['plant_id1'];
    $storage_location = $_POST['storage_location'];
    $request_type1 = $_POST['request_type1'];
    $Department = $_POST['department'];
    if (isset($_POST['persionp']))
    {
        $Persion= implode(',',$_POST['persionp']);
        $status = 'Requested';
        foreach ($_POST['Finance_Verification'] as $key => $value) {
          if($value == 'Yes') {
            $status = 'Review';
            $Requested_to = $verifier_code;
          }
        }
        $query = "UPDATE Tb_Request SET Request_ID = '$request_id',Request_Type = '$request_type',Plant = '$plant_id',Storage_Location = '$storage_location',Time_Log = GETDATE(),
        Request_Category = '$request_type1',status = '$status',Department = '$Department',Persion_In_Workflow = '$Persion',EMP_ID = '$emp_id',Requested_to = '$Requested_to'
        WHERE Request_ID ='".$req_id."'";
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
        $Finance_Verification = $_POST['Finance_Verification'][$i];

  //       echo "INSERT INTO Tb_Request_Items (Request_ID, Item_Code, UOM, Description, Quantity, Budget, Time_Log, status, Expected_Date, Specification, Attachment, Budject_Remark,
  //       MaterialGroup, Finance_Verification) VALUES 
  //  ('$request_id','$item_code','$uom','$description','$quantity','$budgest',GETDATE(),'Requested','$Expected_Date','$Specification','$fil','$budget_remark',
  //      '$MaterialGroup','$Finance_Verification')";
  //       die();

  $sql = "UPDATE Tb_Request_Items SET Request_ID = '$request_id',Item_Code = '$item_code',UOM = '$uom',Description = '$description',Quantity = '$quantity',
  Budget = '$budgest',Time_Log = GETDATE(),status = '$status',Expected_Date = '$Expected_Date',Specification = '$Specification',Attachment = '$fil',
  Budject_Remark = '$budget_remark',MaterialGroup ='$MaterialGroup',Finance_Verification = '$Finance_Verification',EMP_ID = '$emp_id',Requested_to ='$Requested_to'
  WHERE Request_ID ='".$req_id."'";


        $params = array("updated data", 1);

        $stmt = sqlsrv_query($conn, $sql, $params);

        $rows_affected = sqlsrv_rows_affected($stmt);
        if ($rows_affected === false) {
            die(print_r(sqlsrv_errors(), true));
        } elseif ($rows_affected == -1) {
            echo "No information available.<br />";
        } else {
            move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);
            ?>
            <script type="text/javascript">
            alert("Request added successsfully");
            window.location = "show_purchacevendor_request.php";
            </script>

<?php
        }

    }
}
}
if (isset($_POST["save2"])) {
    // echo '<pre>';
    // print_r($_POST);die();
    $emp_id = $Employee_Id;
    $Requested_to = $Purchaser_Code;
    $request_id = $_POST['request_id'];
    $request_type = $_POST['request_type'];
    $plant_id = $_POST['plant_id2'];
    $storage_location = $_POST['storage_location'];
    $request_type1 = $_POST['request_type1'];
    $Department = $_POST['department'];
    if (isset($_POST['persionp']))
    {
        $Persion= implode(',',$_POST['persionp']);

        $status = 'Requested';
        foreach ($_POST['Finance_Verification'] as $key => $value) {
          if($value == 'Yes') {
            $status = 'Review';
            $Requested_to = $verifier_code;
          }
        }
        // echo $status;exit;
        $query = "UPDATE Tb_Request SET Request_ID = '$request_id',Request_Type = '$request_type',Plant = '$plant_id',Storage_Location = '$storage_location',Time_Log = GETDATE(),
        Request_Category = '$request_type1',status = '$status',Department = '$Department',Persion_In_Workflow = '$Persion',EMP_ID = '$emp_id',Requested_to = '$Requested_to'
        WHERE Request_ID ='".$req_id."'";
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
        $Finance_Verification = $_POST['Finance_Verification'][$i];

        $sql = "UPDATE Tb_Request_Items SET Request_ID = '$request_id',Item_Code = '$item_code',UOM = '$uom',Description = '$description',Quantity = '$quantity',
        Budget = '$budgest',Time_Log = GETDATE(),status = '$status',Expected_Date = '$Expected_Date',Specification = '$Specification',Attachment = '$fil',
        Budject_Remark = '$budget_remark',MaterialGroup ='$MaterialGroup',Finance_Verification = '$Finance_Verification',EMP_ID = '$emp_id',Requested_to ='$Requested_to'
        WHERE Request_ID ='".$req_id."'";
        
        $params = array("updated data", 1);

        $stmt = sqlsrv_query($conn, $sql, $params);

        $rows_affected = sqlsrv_rows_affected($stmt);
        if ($rows_affected === false) {
            die(print_r(sqlsrv_errors(), true));
        } elseif ($rows_affected == -1) {
            echo "No information available.<br />";
        } else {
            move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);
            ?>
            <script type="text/javascript">
            alert("Request added successsfully");
            window.location = "show_purchacevendor_request.php";
            </script>

<?php
        }

    }
}
}
if (isset($_POST["save31"])) {
    // echo '<pre>';
    // print_r($_POST);die();
    $emp_id = $Employee_Id;
    $Requested_to = $Purchaser_Code;
    $request_id = $_POST['request_id'];
    $request_type = $_POST['request_type'];
    $plant_id = $_POST['plant_id31'];
    $storage_location = $_POST['storage_location'];
    $request_type1 = $_POST['request_type1'];
    $Department = $_POST['department'];
    if (isset($_POST['persionp']))
    {
        $Persion= implode(',',$_POST['persionp']);

        $status = 'Requested';
        foreach ($_POST['Finance_Verification'] as $key => $value) {
          if($value == 'Yes') {
            $status = 'Review';
            $Requested_to = $verifier_code;
          }
        }
        // echo "UPDATE Tb_Request SET Request_ID = '$request_id',Request_Type = '$request_type',Plant = '$plant_id',Storage_Location = '$storage_location',Time_Log = GETDATE(),
        // Request_Category = '$request_type1',status = '$status',Department = '$Department',Persion_In_Workflow = '$Persion',EMP_ID = '$emp_id',Requested_to = '$Requested_to'
        // WHERE Request_ID ='".$req_id."'";die();
        $query = "UPDATE Tb_Request SET Request_ID = '$request_id',Request_Type = '$request_type',Plant = '$plant_id',Storage_Location = '$storage_location',Time_Log = GETDATE(),
        Request_Category = '$request_type1',status = '$status',Department = '$Department',Persion_In_Workflow = '$Persion',EMP_ID = '$emp_id',Requested_to = '$Requested_to'
        WHERE Request_ID ='".$req_id."'";
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
        $Finance_Verification = $_POST['Finance_Verification'][$i];

        // echo "UPDATE Tb_Request_Items SET Request_ID = '$request_id',Item_Code = '$item_code',UOM = '$uom',Description = '$description',Quantity = '$quantity',
        // Budget = '$budgest',Time_Log = GETDATE(),status = '$status',Expected_Date = '$Expected_Date',Specification = '$Specification',Attachment = '$fil',
        // Budject_Remark = '$budget_remark',MaterialGroup ='$MaterialGroup',Finance_Verification = '$Finance_Verification',EMP_ID = '$emp_id',Requested_to ='$Requested_to'
        // WHERE Request_ID ='".$req_id."'";die();
        $sql = "UPDATE Tb_Request_Items SET Request_ID = '$request_id',Item_Code = '$item_code',UOM = '$uom',Description = '$description',Quantity = '$quantity',
        Budget = '$budgest',Time_Log = GETDATE(),status = '$status',Expected_Date = '$Expected_Date',Specification = '$Specification',Attachment = '$fil',
        Budject_Remark = '$budget_remark',MaterialGroup ='$MaterialGroup',Finance_Verification = '$Finance_Verification',EMP_ID = '$emp_id',Requested_to ='$Requested_to'
        WHERE Request_ID ='".$req_id."'";
        
        $params = array("updated data", 1);

        $stmt = sqlsrv_query($conn, $sql, $params);

        $rows_affected = sqlsrv_rows_affected($stmt);
        if ($rows_affected === false) {
            die(print_r(sqlsrv_errors(), true));
        } elseif ($rows_affected == -1) {
            echo "No information available.<br />";
        } else {
            move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);
            ?>
            <script type="text/javascript">
            alert("Request added successsfully");
            window.location = "show_purchacevendor_request.php";
            </script>

<?php
        }

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

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <!-- ADD ON -->

        <link rel="stylesheet" type="text/css" href="tag.css">
            <!-- Summernote css -->
        <link href="assets/libs/summernote/summernote-bs4.min.css" rel="stylesheet" type="text/css" />
            <!-- multiselect -->
        <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

        <!-- ADD END -->
        <style>
        #involved .th,.td {
        border: 2px solid;
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
                                     <!-- <h4>Payment Request</h4> -->
                                         <ol class="breadcrumb m-0">
                                             <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                             <li class="breadcrumb-item"><a href="show_purchacevendor_request.php">Show Purchase Request</a></li>
                                             <li class="breadcrumb-item active">Purchase Request</li>
                                         </ol>
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
                                                $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request WHERE Request_ID = '$req_id'");
                                                $updated_query = sqlsrv_fetch_array($update_qry);
                                            ?>
                                            <form  method="POST" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-md-2" > 
                                                        <div class="mb-3">
                                                            <label for="validationCustom01" class="form-label">RequestID</label>
                                                            <input type="text" class="form-control" id="validationCustom01" name="request_id"
                                                                value="<?php echo $updated_query['Request_ID'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" >
                                                        <div class="mb-3">
                                                            <label for="hasta" class="form-label">Department</label>
                                                            <input type="hidden" class="form-control" id="request" name="request_type1" value="Purchase & Vendor selection (R&VS)">
                                                            <input type="text" class="form-control" name="department" id="Department3" readonly
                                                                value="<?php echo $updated_query['Department'] ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" >
                                                        <div class="mb-3">
                                                            <label for="Requester" class="form-label">Request Category</label>
                                                            <select class="form-select " id="smallSelect" name="request_type">
                                                                <option value="">Select Category</option>
                                                                <?php echo $updated_query['Request_Type'] ?>
                                                                <option  <?php if ($updated_query['Request_Type'] == "Asset purchases") { ?> selected="selected" <?php } ?> value="Asset purchases">Asset purchases</option>
                                                                <option  <?php if ($updated_query['Request_Type'] == "Material purchases") { ?> selected="selected" <?php } ?> value="Material purchases">Material purchases</option>
                                                                <option  <?php if ($updated_query['Request_Type'] == "Services") { ?> selected="selected" <?php } ?> value="Services">Services</option>
                                                            </select>                                                        
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label">Plant</label>
                                                            <select class="form-select" name="plant_id" id="plant-dropdown">
                                                                <option value="">Select Plant</option>
                                                                <?php
                                                                    $plant = sqlsrv_query($conn, "Select Plant_Code,Plant_Description from PP_PLANT where Plant_Code = '$Plant' group by Plant_Code,Plant_Description");
                                                                    while ($c = sqlsrv_fetch_array($plant)) {
                                                                ?>
                                                                <option value="<?php echo $c['Plant_Code'] ?>">
                                                                    <?php echo $c['Plant_Code'] ?>-
                                                                    <?php echo $c['Plant_Description'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <select class="form-select " name="plant_id1" id="plant-dropdown1">
                                                                <option value="">Select Plant</option>
                                                                <?php
                                                                    $plant = sqlsrv_query($conn, "Select Plant_Code,Plant_Description from PP_PLANT where Plant_Code = '$Plant' group by Plant_Code,Plant_Description");
                                                                    while ($c = sqlsrv_fetch_array($plant)) {
                                                                ?>
                                                                <option value="<?php echo $c['Plant_Code'] ?>">
                                                                    <?php echo $c['Plant_Code'] ?>-
                                                                    <?php echo $c['Plant_Description'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <select class="form-select " name="plant_id2" id="plant-dropdown2">
                                                                <option value="">Select Plant</option>
                                                                <?php
                                                                    $plant = sqlsrv_query($conn, "Select Plant_Code,Plant_Description from PP_PLANT where Plant_Code = '$Plant' group by Plant_Code,Plant_Description");
                                                                    while ($c = sqlsrv_fetch_array($plant)) {
                                                                ?>
                                                                <option value="<?php echo $c['Plant_Code'] ?>">
                                                                    <?php echo $c['Plant_Code'] ?>-
                                                                    <?php echo $c['Plant_Description'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <select class="form-select " name="plant_id31" id="plant-dropdown3">
                                                                <option value="">Select Plant</option>
                                                                <?php
                                                                    $plant = sqlsrv_query($conn, "Select Plant_Code,Plant_Description from PP_PLANT where Plant_Code = '$Plant' group by Plant_Code,Plant_Description");
                                                                    while ($c = sqlsrv_fetch_array($plant)) {
                                                                ?>
                                                                
                                                                <option <?php if ($updated_query['Plant'] == $c['Plant_Code']) { ?> selected="selected" <?php } ?> value="<?php echo $c['Plant_Code'] ?>">
                                                                    <?php echo $c['Plant_Code'] ?>-
                                                                    <?php echo $c['Plant_Description'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label">Storage Location</label>
                                                            <select class="form-select add" name="storage_location" id="storage-dropdown">

                                                            </select>
                                                            <select class="form-select add1" name="storage_location" id="storage-dropdown1">

                                                            </select>
                                                            <select class="form-select add2" name="storage_location" id="storage-dropdown2">

                                                            </select>
                                                            <select class="form-select add3" name="storage_location" id="storage-dropdown3">
                                                            <?php
                                                                $result = sqlsrv_query($conn, "select * from Plant_Master_Storage_Location where PlantCode = '".$updated_query['Plant']."'");
                                                                ?>

                                                                <option value="">Select Storage Location</option>

                                                                <?php
                                                                while ($row = sqlsrv_fetch_array($result)) {
                                                                    ?>
                                                                    <option <?php if ($updated_query['Storage_Location'] == $row['Storage_Location']) { ?> selected="selected" <?php } ?> value="<?php echo $row["Storage_Location"]; ?>">
                                                                        <?php echo $row["Storage_Location"]; ?>-
                                                                        <?php echo $row["Location_Desc"] ?>
                                                                    </option>
                                                                    <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="d-grid gap-2 col-2 mx-auto" style="padding: 26px 0px 48px 0px;">
                                                        <button class="btn btn-primary btn-sm" style="position: absolute;right: 20px;" id="add" >
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
                                                        <button class="btn btn-primary btn-sm" style="position: absolute;right: 20px;"
                                                        id="add1" >
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
                                                        <button class="btn btn-primary btn-sm" style="position: absolute;right: 20px;"
                                                        id="add2" >
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
                                                        <?php
                                                        if ($updated_query['Request_Type']=='Services') {
                                                            ?>
                                                            <button class="btn btn-primary btn-sm" style="position: absolute;right: 20px;"
                                                        id="add31" value="<?php echo $updated_query['Storage_Location'];?>">
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
                                                        <?php
                                                        }else{
                                                            ?>
                                                            <button class="btn btn-primary btn-sm" style="position: absolute;right: 20px;"
                                                        id="add3" value="<?php echo $updated_query['Storage_Location'];?>">
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding: 0px 0px 20px 0px;">
                                                    <div class="table-responsive">
                                                        <table id="table_id" class="table dt-table-hover" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Item Code</th>
                                                                    <th>Description</th>
                                                                    <th>UOM</th>
                                                                    <th>Material Group</th>
                                                                    <th>Quantity</th>
                                                                    <th>Expected Date</th>
                                                                    <th>Specification </th>
                                                                    <th>Attachment</th>
                                                                    <th>Whether Budgeted</th>
                                                                    <th>Finance Verification</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="selected" class="tbody tbody1 tbody2 tbody31 tbody3">
                                                            <?php
                                                                    $i = 1;
                                                                    $sql = "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items
                                                                    ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID WHERE Tb_Request.Request_ID = '$req_id'";
                                                                    $params = array();
                                                                    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
                                                                    $stmt = sqlsrv_prepare($conn, $sql, $params, $options);
                                                                    sqlsrv_execute($stmt);
                                                                    while ($row = sqlsrv_fetch_array($stmt)) {
                                                                        $MatrialGroup = $row['MaterialGroup'];
                                                                ?>
                                                                <tr>
                                                                    <?php
                                                                    if ($row['Request_Type'] == 'Services') {
                                                                       ?>
                                                                    <td>
                                                                       <?php echo $i++ ?>
                                                                    </td>
                                                                    <td>

                                                                    <select class="form-control update_item" id="update_item" style="width: auto;" name="item_code[]" placeholder="Select Name.">
                                                                    <?php
                                                                    $result = sqlsrv_query($conn, "select * from SERVICE_MATERIAL_MASTER ");
                                                                    ?>
                                                                        <option value="">Select Service Material</option>
                                                                        <option value="New_Item">New Item</option>
                                                                    <?php
                                                                    while ($row1 = sqlsrv_fetch_array($result)) {
                                                                        ?>
                                                                    <option <?php if ($row['Item_Code'] == $row1['ASNUM']) { ?> selected="selected" <?php } ?> value="<?php echo $row1["ASNUM"]; ?>">
                                                                        <?php echo $row1["ASKTX"]; ?>-
                                                                        <?php echo $row1["ASNUM"] ?>
                                                                    </option>
                                                                    <?php } ?>
                                                                    </select>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            <input type="text" class="form-control  update_description" style="width: auto;"  id="update_description" name="description[]" value="<?php echo $row['Description'] ?>" placeholder="Enter Description" onkeyup="checkemail();" >
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12" >
                                                                            <input type="text" class="form-control  disabled1 update_uom" id="update_uom"  style="width: auto;" name="uom[]" value="<?php echo $row['UOM'] ?>" placeholder="Enter UOM">
                                                                        </div>
                                                                    </td>
                                                                    <td id="div_item">
                                                                        <div class="col-md-12" >
                                                                            <input type="text" class="form-control disabled update_MaterialGroup" id="update_MaterialGroup" style="width: auto;" value="<?php echo $row['MaterialGroup'] ?>"  name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            <input type="number" min="0" class="form-control " id="update_quantity" name="quantity[]" style="width: auto;" placeholder="Enter Quantity" value="<?php echo $row['Quantity'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            <input type="date" class="form-control " id="update_Expected_Date" name="Expected_Date[]" style="width: auto;" placeholder="Enter Quantity" value="<?php echo $row['Expected_Date'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                        <textarea id="update_Specification" class="form-control " name="Specification[]" style="width:auto;" row="1" placeholder="Enter Specification" ><?php echo $row['Specification'] ?></textarea>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            <input class="form-control file-upload-input" type="file" id="update_Attachment" name="Attachment[]" style="width: auto;" placeholder="Enter Quantity" >
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                        <select class="form-select " id="update_type_val1"  name="budget[]" style="width: auto;"  onchange="openPopup(this);" >
                                                                                <option <?php if ($row['Budget'] == "Yes") { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                                                <option <?php if ($row['Budget'] == "No") { ?> selected="selected" <?php } ?> value="No">No</option>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            
                                                                            <select class="form-select " id="update_Finance_Verification"  name="Finance_Verification[]" style="width: auto;"  >
                                                                                <option <?php if ($row['Finance_Verification'] == 'Yes') { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                                                <option <?php if ($row['Finance_Verification'] == "No") { ?> selected="selected" <?php } ?> value="No">No</option>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <i class="fa fa-times remove text-danger"  ></i>
                                                                    </td>
                                                                       <?php
                                                                    }else{
                                                                        ?>
                                                                    <td>
                                                                       <?php echo $i++ ?>
                                                                    </td>
                                                                    <td>

                                                                    <select class="form-control update_item1" id="update_item1" style="width: auto;" name="item_code[]" placeholder="Select Name.">
                                                                    <?php
                                                                    $result = sqlsrv_query($conn, "SELECT MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group FROM MaterialMaster 
                                                                    INNER JOIN Tb_Master_Emp ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group WHERE MaterialMaster.ItemCode = '$MatrialGroup'  AND Tb_Master_Emp.EMP_ID = '$Employee_Id'
                                                                    GROUP BY MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group");
                                                                    ?>
                                                                        <option value="">Select Item_Code</option>
                                                                        <option value="New_Item">New Item</option>
                                                                    <?php
                                                                    while ($row1 = sqlsrv_fetch_array($result)) {
                                                                        ?>
                                                                    <option <?php if ($row['Item_Code'] == $row1['ItemCode']) { ?> selected="selected" <?php } ?> value="<?php echo $row1["ItemCode"]; ?>">
                                                                        <?php echo $row1["ItemCode"]; ?>-
                                                                        <?php echo $row1["ItemCode"] ?>
                                                                    </option>
                                                                    <?php } ?>
                                                                    </select>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            <input type="text" class="form-control  update_description1" style="width: auto;"  id="update_description1" name="description[]" value="<?php echo $row['Description'] ?>" placeholder="Enter Description" onkeyup="checkemail();" >
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12" >
                                                                            <input type="text" class="form-control  disabled1 update_uom1" id="update_uom1"  style="width: auto;" name="uom[]" value="<?php echo $row['UOM'] ?>" placeholder="Enter UOM">
                                                                        </div>
                                                                    </td>
                                                                    <td id="div_item">
                                                                        <div class="col-md-12" id="update_items">
                                                                            <input type="text" class="form-control  disabled update_MaterialGroup1" id="update_MaterialGroup1" style="width: 120px;" value="<?php echo $row['MaterialGroup'] ?>" name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                                                        </div>
                                                                        <div class="col-md-12" id="update_News">
                                                                            <select class="form-select   update_MaterialGroupda1" id="update_MaterialGroupda1" style="width: 120px;"  name="MaterialGroup[]" value="<?php echo $row['MaterialGroup'] ?>">
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            <input type="number" min="0" class="form-control " id="update_quantity1" name="quantity[]" style="width: auto;" placeholder="Enter Quantity" value="<?php echo $row['Quantity'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            <input type="date" class="form-control " id="update_Expected_Date1" name="Expected_Date[]" style="width: auto;" placeholder="Enter Quantity" value="<?php echo $row['Expected_Date'] ?>">
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                        <textarea id="update_Specification1" class="form-control " name="Specification[]" style="width:auto;" row="1" placeholder="Enter Specification" ><?php echo $row['Specification'] ?></textarea>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            <input  id="update_Attachment1" name="Attachment[]" style="width: auto;"type="text" class="form-control" readonly data-bs-toggle="modal" data-bs-target=".bs-example-modal-center"
                                                                            value="<?php echo $row['Attachment'] ?>" >
                                                                            <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-centered">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h5 class="modal-title mt-0">Attachment</h5>
                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                                            
                                                                                        </button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                    <iframe src="file/<?php echo $row['Attachment'] ?>"
                                                                                    style="width: 100%;height: auto;" frameborder="0"></iframe>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                                    </div>
                                                                                </div><!-- /.modal-content -->
                                                                            </div><!-- /.modal-dialog -->
                                                                        </div><!-- /.modal -->
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                        <select class="form-select " id="update_type_val11"  name="budget[]" style="width: auto;"  onchange="openPopup(this);" >
                                                                                <option <?php if ($row['Budget'] == "Yes") { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                                                <option <?php if ($row['Budget'] == "No") { ?> selected="selected" <?php } ?> value="No">No</option>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <div class="col-md-12">
                                                                            
                                                                            <select class="form-select " id="update_Finance_Verification1"  name="Finance_Verification[]" style="width: auto;"  >
                                                                                <option <?php if ($row['Finance_Verification'] == 'Yes') { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                                                <option <?php if ($row['Finance_Verification'] == "No") { ?> selected="selected" <?php } ?> value="No">No</option>
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <i class="fa fa-times remove text-danger"  ></i>
                                                                    </td>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </tr>
                                                                <?php } ?>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- MODEL -->
                                                    <div class="modal-section">
                                                    </div>
                                                    <!-- END MODEL -->
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-datetime-local-input" class="col-sm-2 col-form-label">Informer Details</label>
                                                    <div class="col-sm-10">
                                                        <select class="select2 form-control select2-multiple" name="persionp[]"  implode multiple data-placeholder="Choose ...">
                                                            <?php
                                                                $HR_Master_Table = sqlsrv_query($conn, "Select * from HR_Master_Table ");
                                                                while ($HR = sqlsrv_fetch_array($HR_Master_Table)) {
                                                                ?>
                                                                <option value="<?php echo $HR['Employee_Code'] ?>">
                                                                    <?php echo $HR['Employee_Name'] ?>&nbsp;-&nbsp;<?php echo $HR['Employee_Code'] ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <h4>Involved Persion</h4>
                                        <table id="involved" >
                                          <thead>
                                            <tr>
                                              <th  class="th">Finance</th>
                                              <th  class="th">Recommender</th>
                                              <th  class="th">Approver</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <tr>
                                              <td class="td">
                                                    <?php
                                                        $selectorqw2 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$verifier_name' ");
                                                        $selector_arrqw2 = sqlsrv_fetch_array($selectorqw2);
                                                    ?>
                                                                                                        <?php
                                                        $selectorq2 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$approver_name' ");
                                                        $selector_arrq2 = sqlsrv_fetch_array($selectorq2);
                                                    ?>
                                                                                                        <?php
                                                        $selectorq = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$recommender_to' ");
                                                        $selector_arrq = sqlsrv_fetch_array($selectorq);
                                                    ?>
                                                    <?php echo $selector_arrqw2['Employee_Name'] ?></td>
                                              <td  class="td"><?php echo $selector_arrq['Employee_Name'] ?></td>
                                              <td  class="td"><?php echo $selector_arrq2['Employee_Name'] ?></td>
                                            </tr>
                                          </tbody>
                                        </table>
                                                <div class="mb-0">
                                                    <center>
                                                    <div>
                                                        <button class="btn btn-primary waves-effect waves-light me-1" type="submit" id="save" name="save" >
                                                            Submit
                                                        </button>
                                                        <button class="btn btn-primary waves-effect waves-light me-1" type="submit" id="save1" name="save1">
                                                            Submit
                                                        </button>
                                                        <button class="btn btn-primary waves-effect waves-light me-1" type="submit" id="save2" name="save2">
                                                            Submit
                                                        </button>
                                                        <button class="btn btn-primary waves-effect waves-light me-1" type="submit" id="save31" name="save31">
                                                            Submit
                                                        </button>
                                                    </div>
                                                    </center>
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

        <script src="assets/js/app.js"></script>
        <!-- ADD ON -->

        <script src="tag.js"></script>
            <!--tinymce js-->
        <script src="assets/libs/tinymce/tinymce.min.js"></script>
            <!-- Summernote js -->
        <script src="assets/libs/summernote/summernote-bs4.min.js"></script>
            <!-- init js -->
        <script src="assets/js/pages/form-editor.init.js"></script>
            <!-- multiselect -->
        <script src="assets/libs/select2/js/select2.min.js"></script>
        <script src="assets/js/pages/form-advanced.init.js"></script>

        <!-- ADD END -->

        <!-- CUSTOM JS START -->
        <script>
            <?php
                                                $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request WHERE Request_ID = '$req_id'");
                                                $updated_query = sqlsrv_fetch_array($update_qry);
                                            ?>
            var i = 1;
            // MAIN HIDE AND SHOW 
            $(function () {
                // $('#plant').hide();
                // $('#storage').show();
                // $('#add12').show();
                // $('#save12').show();
                $('#smallSelect').change(function () {
                    if ($('#smallSelect').val() == '<?php echo $updated_query['Request_Type'] ?>') {
                    $('#plant-dropdown3').show();
                    $('#storage-dropdown3').show();
                    $('#add31').show();
                    $('#save31').show();
                    } else {
                    $('#plant-dropdown3').hide();
                    $('#storage-dropdown3').hide();
                    $('#selected').hide();
                    $('#add31').hide();
                    $('#save31').hide();
                    }
                });
            });
            $(function () {
                $('#plant-dropdown1').hide();
                $('#storage-dropdown1').hide();
                $('.table_id').hide();
                $('#add1').hide();
                $('#save1').hide();
                $('#smallSelect').change(function () {
                    if ($('#smallSelect').val() == 'Services') {
                    $('#plant-dropdown1').show();
                    $('#storage-dropdown1').show();
                    $('.table_id').show();
                    $('#add1').show();
                    $('#save1').show();
                    } else {
                    $('#plant-dropdown1').hide();
                    $('#storage-dropdown1').hide();
                    $('.table_id').hide();
                    $('#add1').hide();
                    $('#save1').hide();
                    }
                });
            });
            $(function () {
                $('#plant-dropdown').hide();
                $('#storage-dropdown').hide();
                $('.table_id').hide();
                $('#add').hide();
                $('#save').hide();
                $('#smallSelect').change(function () {
                    if ($('#smallSelect').val() == 'Material purchases') {
                    $('#plant-dropdown').show();
                    $('#storage-dropdown').show();
                    $('.table_id').show();
                    $('#add').show();
                    $('#save').show();
                    } else {
                    $('#plant-dropdown').hide();
                    $('#storage-dropdown').hide();
                    $('.table_id').hide();
                    $('#add').hide();
                    $('#save').hide();

                    }
                });
            });
            $(function () {
                $('#plant-dropdown2').hide();
                $('#storage-dropdown2').hide();
                $('.table_id').hide();
                $('#add2').hide();
                $('#save2').hide();
                $('#smallSelect').change(function () {
                    if ($('#smallSelect').val() == 'Asset purchases') {
                    $('#plant-dropdown2').show();
                    $('#storage-dropdown2').show();
                    $('.table_id').show();
                    $('#add2').show();
                    $('#save2').show();
                    } else {
                    $('#plant-dropdown2').hide();
                    $('#storage-dropdown2').hide();
                    $('.table_id').hide();
                    $('#add2').hide();
                    $('#save2').hide();

                    }
                });
            });
            // END SHOW 

            // ON CHANGE DROPDOWN 
            $(document).ready(function () {
                    $('#plant-dropdown').on('change', function () {
                        var Plant_Code = this.value;
                        $.ajax({
                        url: "fetch-storage.php",
                        type: "POST",
                        data: {
                            Plant_Code: Plant_Code
                        },
                        cache: false,
                        success: function (result) {
                            $("#storage-dropdown").html(result);
                        }
                        });
                    });

                    $('#plant-dropdown1').on('change', function () {
                        var Plant_Code = this.value;
                        $.ajax({
                        url: "fetch-storage.php",
                        type: "POST",
                        data: {
                            Plant_Code: Plant_Code
                        },
                        cache: false,
                        success: function (result) {
                            $("#storage-dropdown1").html(result);
                            $('.items-dropdown1').html('<option value="">Select Item_Code</option>');
                            $('.item-dropdown1').html('<option value="">Select Item_Code</option>');
                        }
                        });
                    });

                    $('#plant-dropdown2').on('change', function () {
                        var Plant_Code = this.value;
                        $.ajax({
                        url: "fetch-storage.php",
                        type: "POST",
                        data: {
                            Plant_Code: Plant_Code
                        },
                        cache: false,
                        success: function (result) {
                            $("#storage-dropdown2").html(result);
                            $('.items-dropdown2').html('<option value="">Select Item_Code</option>');
                            $('.item-dropdown2').html('<option value="">Select Item_Code</option>');
                        }
                        });
                    });

                    $('#storage-dropdown').on('change', function () {
                        var Storage_Location = this.value;
                        $.ajax({
                        url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
                        type: "POST",
                        data: {
                            Storage_Location: Storage_Location
                        },
                        cache: false,
                        success: function (result) {
                            $(".items-dropdown").html(result);
                            $(".item-dropdown").html(result);
                            $("#add").val(Storage_Location);
                            $('#items').show();
                            $('#News').hide();
                        }
                        });
                    });

                    $('#storage-dropdown1').on('change', function () {
                        var Storage_Location = this.value;
                        $.ajax({
                        url: "service_material.php",
                        type: "POST",
                        data: {
                            Storage_Location: Storage_Location
                        },
                        cache: false,
                        success: function (result) {
                            $(".items-dropdown1").html(result);
                            $(".item-dropdown1").html(result);
                            $("#add1").val(Storage_Location);
                        }
                        });
                    });

                    $('#storage-dropdown2').on('change', function () {
                        var Storage_Location = this.value;
                        $.ajax({
                        url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
                        type: "POST",
                        data: {
                            Storage_Location: Storage_Location
                        },
                        cache: false,
                        success: function (result) {
                            $(".items-dropdown2").html(result);
                            $(".item-dropdown2").html(result);
                            $("#add2").val(Storage_Location);
                            $('#items2').show();
                            $('#News2').hide();
                        }
                        });
                    });

                });
            // END DROPDOWN
            
            // DROPDOWN 
            $(document).on('change', '.items-dropdown', function () {
                var ItemCode = $(this).val();
                var ItemCode_closet = $(this);
                $.ajax({
                url: "getUser.php",
                dataType: 'json',
                type: 'POST',
                async: true,
                data: { ItemCode: ItemCode },
                success: function (data) {
                    ItemCode_closet.closest('tr').find('.uom').val(data.UOM);
                    ItemCode_closet.closest('tr').find('.description').val(data.ItemDescription);
                }
                });
                $.ajax({
                url: "fetch-new.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    ItemCode: ItemCode
                },
                cache: false,
                success: function (result) {
                    $(".MaterialGroupda").html(result);
                }
                });


                if ($('.items-dropdown').val() == 'New_Item') {
                $('#items').hide();
                $('#News').show();
                } else {
                $('#items').show();
                $('#News').hide();

                }
                $.ajax({
                url: "fetch-item-metrial.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    ItemCode: ItemCode
                },
                cache: false,
                success: function (result) {
                    ItemCode_closet.closest('tr').find('.MaterialGroup').val(result.MaterialGroup);
                }
                });

                });
                $(document).on('change', '.items-dropdown1', function () {
                var ItemCode = $(this).val();
                var ItemCode_closet = $(this);
                $.ajax({
                url: "service_dec_uom.php",
                dataType: 'json',
                type: 'POST',
                async: true,
                data: { ItemCode: ItemCode },
                success: function (data) {
                    ItemCode_closet.closest('tr').find('.uom1').val(data.MEINS);
                    ItemCode_closet.closest('tr').find('.description1').val(data.ASKTX);

                }
                });
                });
                $(document).on('change', '.items-dropdown2', function () {
                var ItemCode = $(this).val();
                var ItemCode_closet = $(this);
                $.ajax({
                url: "fetch-item-metrial.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    ItemCode: ItemCode
                },
                cache: false,
                success: function (result) {
                    ItemCode_closet.closest('tr').find('.MaterialGroup2').val(result.MaterialGroup);
                }
                });
                $.ajax({
                url: "getUser.php",
                dataType: 'json',
                type: 'POST',
                async: true,
                data: { ItemCode: ItemCode },
                success: function (data) {
                    ItemCode_closet.closest('tr').find('.uom2').val(data.UOM);
                    ItemCode_closet.closest('tr').find('.description2').val(data.ItemDescription);
                    // ItemCode_closet.closest('tr').find('.MaterialGroup2').val(data.MaterialGroup);
                }
                });
                $.ajax({
                url: "fetch-new.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    ItemCode: ItemCode
                },
                cache: false,
                success: function (result) {
                    $(".MaterialGroupda2").html(result);
                }
                });

                if ($('.items-dropdown2').val() == 'New_Item') {
                $('#items2').hide();
                $('#News2').show();
                } else {
                $('#items2').show();
                $('#News2').hide();

                }
                });

                $(document).on('change', '.item-dropdown', function () {

                var ItemCode = $(this).val();
                var ItemCode_closet = $(this);
                var state = $(this).data('id');

                $.ajax({
                url: "getUser.php",
                dataType: 'json',
                type: 'POST',
                async: true,
                data: { ItemCode: ItemCode },
                success: function (data) {
                    ItemCode_closet.closest('tr').find('.uom').val(data.UOM);
                    ItemCode_closet.closest('tr').find('.description').val(data.ItemDescription);
                    // ItemCode_closet.closest('tr').find('.MaterialGroup').val(data.MaterialGroup);

                }
                });

                $.ajax({
                url: "fetch-item-metrial.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    ItemCode: ItemCode
                },
                cache: false,
                success: function (result) {
                    ItemCode_closet.closest('tr').find('.MaterialGroup' + state).val(result.MaterialGroup);
                }
                });
                $.ajax({
                url: "fetch-new.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    ItemCode: ItemCode
                },
                cache: false,
                success: function (result) {
                    ItemCode_closet.closest('tr').find(".MaterialGroupda" + state).html(result);
                }
                });

                if ($('.item-dropdown').val() == "New_Item") {
                ItemCode_closet.closest('tr').find('#item' + state).hide();
                ItemCode_closet.closest('tr').find('#New' + state).show();
                } else {
                ItemCode_closet.closest('tr').find('#item' + state).show();
                ItemCode_closet.closest('tr').find('#New' + state).hide();

                }

                });
                $(document).on('change', '.item-dropdown1', function () {
                var ItemCode = $(this).val();
                var ItemCode_closet = $(this);
                $.ajax({
                url: "service_dec_uom.php",
                dataType: 'json',
                type: 'POST',
                async: true,
                data: { ItemCode: ItemCode },
                success: function (data) {
                    ItemCode_closet.closest('tr').find('.uom1').val(data.MEINS);
                    ItemCode_closet.closest('tr').find('.description1').val(data.ASKTX);

                }
                });
                });
                $(document).on('change', '.item-dropdown2', function () {

                var ItemCode = $(this).val();
                var ItemCode_closet = $(this);
                var state = $(this).data('id');

                $.ajax({
                url: "getUser.php",
                dataType: 'json',
                type: 'POST',
                async: true,
                data: { ItemCode: ItemCode },
                success: function (data) {
                    ItemCode_closet.closest('tr').find('.uom2').val(data.UOM);
                    ItemCode_closet.closest('tr').find('.description2').val(data.ItemDescription);
                    // ItemCode_closet.closest('tr').find('.MaterialGroup').val(data.MaterialGroup);

                }
                });

                $.ajax({
                url: "fetch-item-metrial.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    ItemCode: ItemCode
                },
                cache: false,
                success: function (result) {
                    ItemCode_closet.closest('tr').find('.MaterialGroup2' + state).val(result.MaterialGroup);
                }
                });
                $.ajax({
                url: "fetch-new.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    ItemCode: ItemCode
                },
                cache: false,
                success: function (result) {
                    ItemCode_closet.closest('tr').find(".MaterialGroupda2" + state).html(result);
                }
                });

                if ($('.item-dropdown2').val() == "New_Item") {
                ItemCode_closet.closest('tr').find('#item2' + state).hide();
                ItemCode_closet.closest('tr').find('#New2' + state).show();
                } else {
                ItemCode_closet.closest('tr').find('#item2' + state).show();
                ItemCode_closet.closest('tr').find('#New2' + state).hide();

                }
                });


                $(document).on('click', '#add', function () {

                var Storage_Location = this.value;
                var id = (i);
                $.ajax({
                url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    Storage_Location: Storage_Location
                },
                cache: false,
                success: function (result) {
                    $('.item-dropdown').append(i).append(" " + result + " ");
                    $('#item' + id).show();
                    $('#New' + id).hide();

                }
                });

                });
                $(document).on('click', '#add1', function () {
                var Storage_Location = this.value;
                $.ajax({
                url: "service_material.php",
                type: "POST",
                data: {
                    Storage_Location: Storage_Location
                },
                cache: false,
                success: function (result) {
                    $('.item-dropdown1').append(i).append(" " + result + " ");

                }
                });
                });
                $(document).on('click', '#add2', function () {
                var Storage_Location = this.value;
                var id = (i);
                $.ajax({
                url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
                type: "POST",
                data: {
                    Storage_Location: Storage_Location
                },
                cache: false,
                success: function (result) {
                    $('.item-dropdown2').append(i).append(" " + result + " ");
                    $('#item2' + id).show();
                    $('#New2' + id).hide();
                }
                });
            });
            // END DROPDOWN

            // DISABLE
            $(document).on('change', '.items-dropdown', function () {
            var stateID = $(this).val();
            if (stateID == 'New_Item')
            $("input.disabled").prop("readonly", false);
            else
            $("input.disabled").prop("readonly", true);
            });
            $(document).on('change', '.items-dropdown1', function () {

            var stateID = $(this).val();
            if (stateID == 'New_Item')
            $("input.disabled").prop("readonly", false);
            else
            $("input.disabled").prop("readonly", true);
            });
            $(document).on('change', '.items-dropdown2', function () {

            var stateID = $(this).val();
            if (stateID == 'New_Item')
            $("input.disabled").prop("readonly", false);
            else
            $("input.disabled").prop("readonly", true);
            });

            $(document).on('change', '.item-dropdown', function () {
            var stateID = $(this).val();
            var state = $(this).data('id');
            if (stateID == 'New_Item')
            $("input.disabled1" + state).prop("readonly", false);
            else
            $("input.disabled1" + state).prop("readonly", true);
            });
            $(document).on('change', '.item-dropdown1', function () {
            var stateID = $(this).val();
            var state = $(this).data('id');
            if (stateID == 'New_Item')
            $("input.disabled1" + state).prop("readonly", false);
            else
            $("input.disabled1" + state).prop("readonly", true);
            });
            $(document).on('change', '.item-dropdown2', function () {
            var stateID = $(this).val();
            var state = $(this).data('id');
            if (stateID == 'New_Item')
            $("input.disabled1" + state).prop("readonly", false);
            else
            $("input.disabled1" + state).prop("readonly", true);
            });

            function openPopup(item) {

            var id = $(item).data('id');
            $("#inputFormModal" + id).modal('show');
            }
            $(document).on('change', '.update_item', function () {
            var ItemCode = $(this).val();
            var ItemCode_closet = $(this);
            $.ajax({
                url: "service_dec_uom.php",
                dataType: 'json',
                type: 'POST',
                async: true,
                data: { ItemCode: ItemCode },
                success: function (data) {
                ItemCode_closet.closest('tr').find('.update_uom').val(data.MEINS);
                ItemCode_closet.closest('tr').find('.update_description').val(data.ASKTX);

                }
            });
            });
            $(document).on('click', '#add31', function () {
                
            var Storage_Location = this.value;
            $.ajax({
                url: "service_material.php",
                type: "POST",
                data: {
                Storage_Location: Storage_Location
                },
                cache: false,
                success: function (result) {
                $('.update_item').append(i).append(" " + result + " ");

                }
            });
            });
            $(document).on('change', '.update_item1', function () {

var ItemCode = $(this).val();
var ItemCode_closet = $(this);
$.ajax({
  url: "getUser.php",
  dataType: 'json',
  type: 'POST',
  async: true,
  data: { ItemCode: ItemCode },
  success: function (data) {
    ItemCode_closet.closest('tr').find('.update_uom1').val(data.UOM);
    ItemCode_closet.closest('tr').find('.update_description1').val(data.ItemDescription);
  }
});
$.ajax({
  url: "fetch-new.php?employe=<?php echo $Employee_Id ?>",
  type: "POST",
  data: {
    ItemCode: ItemCode
  },
  cache: false,
  success: function (result) {
    $(".update_MaterialGroupda1").html(result);
  }
});


if ($('.items-dropdown').val() == 'New_Item') {
  $('#update_items').hide();
  $('#update_News').show();
} else {
  $('#update_items').show();
  $('#update_News').hide();

}
$.ajax({
  url: "fetch-item-metrial.php?employe=<?php echo $Employee_Id ?>",
  type: "POST",
  data: {
    ItemCode: ItemCode
  },
  cache: false,
  success: function (result) {
    ItemCode_closet.closest('tr').find('.update_MaterialGroup1').val(result.MaterialGroup);
  }
});

});
$(document).on('click', '#add3', function () {
      var Storage_Location = this.value;
      var id = (i);
      $.ajax({
        url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
        type: "POST",
        data: {
          Storage_Location: Storage_Location
        },
        cache: false,
        success: function (result) {
          $('.update_item1').append(i).append(" " + result + " ");
          $('#update_items' + id).show();
          $('#update_News' + id).hide();
        }
      });
    });
     //SERVICE ONCLICK START//
var i=2;
    //  $('#save31').attr('disabled', true);
    //   $('#add31').attr('disabled', true);
      $(document).on('click', '#add31', function () {

        $('#add31').attr('disabled', false);
        var modal = `<div class="row">
											<div id="modalCustom" class="col-lg-12 layout-spacing">
												<div class="statbox widget box box-shadow">
														<div class="modal fade inputForm-modal" id="inputFormModal${i}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
															<div class="modal-dialog modal-dialog-centered" role="document">
																<div class="modal-content">
																	<div class="modal-header" id="inputFormModalLabel">
																		<h5 class="modal-title">Remarks For <b>SendBack</b></h5>
																		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
																	</div>
																	<div class="modal-body">
																			<div class="form-group">
																				<div class="input-group mb-3">
																					<textarea class="form-control form-control-sm" name="budget_remark[]" aria-label="With textarea"></textarea>
																				</div>
																			</div>
																	</div>
																	<div class="modal-footer">
																		<button type="submit" class="btn btn-light-danger mt-2 mb-2 btn-no-effect" data-bs-dismiss="modal">Close</button>
																	</div>
																</div>
															</div>
														</div>
												</div>
											</div>
										</div>`;
        $('.modal-section').append(modal);
        var output = `
                <tr>
                    <td>
                        `+ (i) + `
                    </td>
                    <td>
                        <div class="col-md-12" >
                            <select class="form-select  update_item" id="update_item${i}" style="width: auto;" data-id="${i}" name="item_code[]" placeholder="Select Name.">
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                            <input type="text" class="form-control disabled1${i} update_description" style="width: auto;"  id="update_description${i}" name="description[]" placeholder="Enter Description" onkeyup="checkemail();" required="">
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12" >
                            <input type="text" class="form-control disabled1${i} update_uom" id="update_uom${i}"  style="width: auto;" name="uom[]" placeholder="Enter UOM">
                        </div>
                    </td>
                    <td id="divb">
                        <div class="col-md-12" >
                            <input type="text" class="form-control disabled update_MaterialGroup" id="update_MaterialGroup${i}" style="width: auto;"  name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                            <input type="number" min="0" class="form-control" id="update_quantity${i}" name="quantity[]" style="width: auto;" placeholder="Enter Quantity" required="">
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                            <input type="date" class="form-control " id="update_Expected_Date${i}" name="Expected_Date[]" style="width: auto;" placeholder="Enter Quantity" required="">
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                        <textarea id="update_Specification${i}" class="form-control " name="Specification[]" style="width:auto;" row="1" placeholder="Enter Specification" required=""></textarea>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                        <input class="form-control file-upload-input" type="file" id="update_Attachment${i}" name="Attachment[]" style="width: auto;" placeholder="Enter Quantity">
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                        <select class="form-select form-select " id="update_type_val1${i}" data-id="${i}" name="budget[]" style="width: auto;" onchange="openPopup(this);" >
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="col-md-12">
                            
                            <select class="form-select form-select " id="update_Finance_Verification${i}" data-id="${i}" name="Finance_Verification[]" style="width: auto;"  >
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <i class="fa fa-times remove text-danger"  ></i>
                    </td>

                </tr>
                `;

        i++;
        if (i > 1) {
          $('#save31').attr('disabled', false);
          $('#add31').attr('disabled', false);
        }
        $(".tbody31").append(output);

      });
      $(document).on('change', '#add3', function () {

var modal = `<div class="row">
                                    <div id="modalCustom" class="col-lg-12 layout-spacing">
                                        <div class="statbox widget box box-shadow">
                                                <div class="modal fade inputForm-modal" id="inputFormModal${i}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header" id="inputFormModalLabel">
                                                                <h5 class="modal-title">Remarks For <b>SendBack</b></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <div class="input-group mb-3">
                                                                            <textarea class="form-control form-control-sm" name="budget_remark[]" aria-label="With textarea"></textarea>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-light-danger mt-2 mb-2 btn-no-effect" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>`;
$('.modal-section').append(modal);
var output = `
        <tr>
            <td>
                `+ (i) + `
            </td>
            <td>
                <div class="col-md-12" >
                    <select class="form-select form-select update_item1" id="update_item1" name="item_code[]" style="width: auto;" placeholder="Select Name.">
                    </select>
                </div>
            </td>
            <td id="divn">
                <div class="col-md-12">
                    <input type="text" class="form-control   disabled update_description1"  id="update_description1${i}" style="width: auto;" name="description[]" placeholder="Enter Description" onkeyup="checkemail();" required="">
                </div>
            </td>
            <td id="divb">
                <div class="col-md-12" >
                    <input type="text" class="form-control disabled  update_uom1" id="update_uom1${i}"  name="uom[]" style="width: auto;" placeholder="Enter UOM">
                </div>
            </td>	
            <td id="divb">
            <div class="col-md-12" id="update_items">
                    <input type="text" class="form-control   disabled update_MaterialGroup1" id="update_MaterialGroup1${i}" style="width: auto;"  name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                </div>
                <div class="col-md-12" id="update_News">
                    <select class="form-select update_MaterialGroupda1" id="update_MaterialGroupda1${i}" style="width: auto;"  name="MaterialGroup[]">
                    </select>
                </div>
            </td>				
            <td>
                <div class="col-md-12">
                    <input type="number" min="0" class="form-control " id="update_quantity${i}" name="quantity[]" style="width: auto;"  placeholder="Enter Quantity" required="">
                </div>
            </td>
            <td>
                <div class="col-md-12">
                    <input type="date" class="form-control " id="update_Expected_Date${i}" name="Expected_Date[]" style="width: auto;" placeholder="Enter Quantity" required="">
                </div>
            </td>
            <td>
                <div class="col-md-12">
                    <textarea id="update_Specification${i}" class="form-control " name="Specification[]" style="width:auto;" row="1" placeholder="Enter Specification" required=""></textarea>
                </div>
            </td>
            <td>
                <div class="col-md-12">
                    <input class="form-control file-upload-input" type="file" id="update_Attachment${i}" name="Attachment[]" style="width: auto;" placeholder="Enter Quantity">
                </div>
            </td>
            <td>
                <div class="col-md-12">
                
                    <select class="form-select " id="update_type_val1${i}" data-id="${i}" name="budget[]" style="width: auto;" onchange="openPopup(this);" >
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </td>
            <td>
                <div class="col-md-12">
                    
                    <select class="form-select " id="update_Finance_Verification${i}" data-id="${i}" name="Finance_Verification[]" style="width: auto;"  >
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </td>
            <td>
                <i class="fa fa-times remove text-danger"  ></i>
            </td>

        </tr>
        `;

i++;
if (i > 1) {
  $('#save31').attr('disabled', false);
  $('#add3').attr('disabled', false);
}
$(".tbody3").append(output);

});
$(document).on('click', '.remove', function () {
$($(this).closest("tr")).remove();
i--;
if (i == 1) {
  $('#save31').attr('disabled', true);
  $('#add3').attr('disabled', true);

}
})
//MATERIAL START//
$(document).ready(function () {
                $('#save').attr('disabled', true);
                $('#add').attr('disabled', true);
                $(document).on('change', '.add', function () {

                    var modal = `<div class="row">
                                                        <div id="modalCustom" class="col-lg-12 layout-spacing">
                                                            <div class="statbox widget box box-shadow">
                                                                    <div class="modal fade inputForm-modal" id="inputFormModal${i}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" id="inputFormModalLabel">
                                                                                    <h5 class="modal-title">Remarks For <b>SendBack</b></h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                        <div class="form-group">
                                                                                            <div class="input-group mb-3">
                                                                                                <textarea class="form-control" name="budget_remark[]" aria-label="With textarea"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="submit" class="btn btn-light-danger mt-2 mb-2 btn-no-effect" data-bs-dismiss="modal">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
                    $('.modal-section').append(modal);
                    var output = `
                            <tr>
                                <td>
                                    `+ (i) + `
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <select class="form-control items-dropdown" id="item-dropdown" style="width: auto;" name="item_code[]" placeholder="Select Name." >
                                        </select>
                                    </div>
                                </td>
                                <td id="divn">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control  disabled description" style="width: auto;" id="description${i}" name="description[]"  placeholder="Enter Description" onkeyup="checkemail();" required="">
                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled  uom" id="uom${i}" style="width: auto;"  name="uom[]" placeholder="Enter UOM" >
                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" id="items">
                                        <input type="text" class="form-control  disabled MaterialGroup" style="width: auto;" id="MaterialGroup${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                    <div class="col-md-12" id="News">
                                        <select class="form-control  MaterialGroupda" id="MaterialGroupda${i}" style="width: auto;"   name="MaterialGroup[]">
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" style="width: auto;" name="quantity[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}"style="width: auto;" name="Expected_Date[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: auto;"  row="2" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input class="form-control file-upload-input" type="file"  id="Attachment${i}" style="width: auto;" name="Attachment[]"  placeholder="Enter Quantity" >
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    
                                        <select class="form-control" id="type_val${i}" data-id="${i}"  name="budget[]" style="width: auto;"  onchange="openPopup(this);" >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        
                                        <select class="form-control" id="Finance_Verification${i}" data-id="${i}" style="width: auto;" name="Finance_Verification[]"   >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <i class="fa fa-times remove text-danger"  ></i>
                                </td>

                            </tr>
                            `;

                    i++;
                    if (i > 1) {
                    $('#save').attr('disabled', false);
                    $('#add').attr('disabled', false);
                    }
                    $(".tbody").append(output);

                });
                $(document).on('click', '.remove', function () {
                    $($(this).closest("tr")).remove();
                    i--;
                    if (i == 1) {
                    $('#save').attr('disabled', true);
                    $('#add').attr('disabled', true);

                    }
                })
                //MATERIAL END//

                //SERVICE START//
                $('#save1').attr('disabled', true);
                $('#add1').attr('disabled', true);
                $(document).on('change', '.add1', function () {

                    var modal = `<div class="row">
                                                        <div id="modalCustom" class="col-lg-12 layout-spacing">
                                                            <div class="statbox widget box box-shadow">
                                                                    <div class="modal fade inputForm-modal" id="inputFormModal${i}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" id="inputFormModalLabel">
                                                                                    <h5 class="modal-title">Remarks For <b>SendBack</b></h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                        <div class="form-group">
                                                                                            <div class="input-group mb-3">
                                                                                                <textarea class="form-control" name="budget_remark[]" aria-label="With textarea"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="submit" class="btn btn-light-danger mt-2 mb-2 btn-no-effect" data-bs-dismiss="modal">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
                    $('.modal-section').append(modal);
                    var output = `
                            <tr>
                                <td>
                                    `+ (i) + `
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <select class="form-control items-dropdown1" id="item-dropdown1" style="width: auto;" name="item_code[]"  placeholder="Select Name.">
                                        </select>
                                    </div>
                                </td>
                                <td id="divn">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control  disabled description1" style="width: auto;"  id="description1${i}"  name="description[]" placeholder="Enter Description" onkeyup="checkemail();" required="">
                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled  uom1" id="uom1${i}" style="width: auto;"  name="uom[]"  placeholder="Enter UOM">
                                    </div>
                                </td>	
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control  disabled MaterialGroup" style="width: auto;" id="MaterialGroup${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                </td>			
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" style="width: auto;" name="quantity[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}" style="width: auto;" name="Expected_Date[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: auto;"  rows="2" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input class="form-control file-upload-input" type="file" id="Attachment${i}" style="width: auto;" name="Attachment[]"  placeholder="Enter Quantity"
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    
                                        <select class="form-control" id="type_val${i}" data-id="${i}" name="budget[]" style="width: auto;"  onchange="openPopup(this);" >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        
                                        <select class="form-control" id="Finance_Verification${i}" data-id="${i}" style="width: auto;" name="Finance_Verification[]"   >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <i class="fa fa-times remove text-danger"  ></i>
                                </td>

                            </tr>
                            `;

                    i++;
                    if (i > 1) {
                    $('#save1').attr('disabled', false);
                    $('#add1').attr('disabled', false);
                    }
                    $(".tbody1").append(output);

                });

                //SERVICE END//

                //ASSET START//

                $(document).on('click', '.remove', function () {
                    $($(this).closest("tr")).remove();
                    i--;
                    if (i == 1) {
                    $('#save1').attr('disabled', true);
                    $('#add1').attr('disabled', true);

                    }
                })
                $('#save2').attr('disabled', true);
                $('#add2').attr('disabled', true);
                $(document).on('change', '.add2', function () {

                    var modal = `<div class="row">
                                                        <div id="modalCustom" class="col-lg-12 layout-spacing">
                                                            <div class="statbox widget box box-shadow">
                                                                    <div class="modal fade inputForm-modal" id="inputFormModal${i}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" id="inputFormModalLabel">
                                                                                    <h5 class="modal-title">Remarks For <b>SendBack</b></h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                        <div class="form-group">
                                                                                            <div class="input-group mb-3">
                                                                                                <textarea class="form-control" name="budget_remark[]" aria-label="With textarea"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="submit" class="btn btn-light-danger mt-2 mb-2 btn-no-effect" data-bs-dismiss="modal">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
                    $('.modal-section').append(modal);
                    var output = `
                            <tr>
                                <td>
                                    `+ (i) + `
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <select class="form-control items-dropdown2" id="item-dropdown2" style="width: auto;" name="item_code[]"  placeholder="Select Name.">
                                        </select>
                                    </div>
                                </td>
                                <td id="divn">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control  disabled description2" style="width: auto;" id="description2${i}"  name="description[]" placeholder="Enter Description" onkeyup="checkemail();" required="">
                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled  uom2" id="uom2${i}" style="width: auto;"  name="uom[]"  placeholder="Enter UOM">
                                    </div>
                                </td>	
                                <td id="divb">
                                <div class="col-md-12" id="items2">
                                        <input type="text" class="form-control  disabled MaterialGroup2" style="width: auto;" id="MaterialGroup2${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                    <div class="col-md-12" id="News2">
                                        <select class="form-control   MaterialGroupda2" id="MaterialGroupda2${i}" style="width: auto;"  name="MaterialGroup[]">
                                        </select>
                                    </div>
                                </td>				
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" style="width: auto;" name="quantity[]"   placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}" style="width: auto;" name="Expected_Date[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: auto;"  rows="2" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input class="form-control file-upload-input" type="file" id="Attachment${i}" style="width: auto;" name="Attachment[]"  placeholder="Enter Quantity">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    
                                        <select class="form-control" id="type_val${i}" data-id="${i}" name="budget[]" style="width: auto;"  onchange="openPopup(this);" >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        
                                        <select class="form-control" id="Finance_Verification${i}" data-id="${i}" style="width: auto;" name="Finance_Verification[]"   >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <i class="fa fa-times remove text-danger"  ></i>
                                </td>

                            </tr>
                            `;

                    i++;
                    if (i > 1) {
                    $('#save2').attr('disabled', false);
                    $('#add2').attr('disabled', false);
                    }
                    $(".tbody2").append(output);

                });
                $(document).on('click', '.remove', function () {
                    $($(this).closest("tr")).remove();
                    i--;
                    if (i == 1) {
                    $('#save2').attr('disabled', true);
                    $('#add2').attr('disabled', true);

                    }
                })

                //ASSET END//

                //MATERIAL ONCLICK START//
                $(document).on('click', '#add', function () {

                    $('#add').attr('disabled', false);
                    var modal = `<div class="row">
                                                        <div id="modalCustom" class="col-lg-12 layout-spacing">
                                                            <div class="statbox widget box box-shadow">
                                                                    <div class="modal fade inputForm-modal" id="inputFormModal${i}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" id="inputFormModalLabel">
                                                                                    <h5 class="modal-title">Remarks For <b>SendBack</b></h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                        <div class="form-group">
                                                                                            <div class="input-group mb-3">
                                                                                                <textarea class="form-control" name="budget_remark[]" aria-label="With textarea"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="submit" class="btn btn-light-danger mt-2 mb-2 btn-no-effect" data-bs-dismiss="modal">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
                    $('.modal-section').append(modal);
                    var output = `
                            <tr>
                                <td>
                                    `+ (i) + `
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <select class="form-control item-dropdown" id="item-dropdown${i}" style="width: auto;"  data-id="${i}" name="item_code[]" placeholder="Select Name.">
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control disabled1${i} description" style="width: auto;"  id="description${i}" name="description[]" placeholder="Enter Description" onkeyup="checkemail();" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled1${i} uom" id="uom${i}" style="width: auto;"  name="uom[]" placeholder="Enter UOM">
                                    </div>
                                </td>
                                <td id="div_item">
                                    <div class="col-md-12" id="item${i}">
                                        <input type="text" class="form-control  disabled1${i} MaterialGroup${i}" style="width: auto;" id="MaterialGroup${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                    <div class="col-md-12" id="New${i}">
                                        <select class="form-control  MaterialGroupda${i}" id="MaterialGroupda${i}" style="width: auto;"   name="MaterialGroup[]">
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" style="width: auto;" name="quantity[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}" style="width: auto;" name="Expected_Date[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: auto;"  rows="2" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input class="form-control file-upload-input" type="file" id="Attachment${i}" style="width: auto;" name="Attachment[]"  placeholder="Enter Quantity" >
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <select class="form-control" id="type_val1${i}" data-id="${i}" name="budget[]" style="width: auto;"  onchange="openPopup(this);" >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        
                                        <select class="form-control" id="Finance_Verification${i}" data-id="${i}" style="width: auto;" name="Finance_Verification[]"   >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <i class="fa fa-times remove text-danger"  ></i>
                                </td>

                            </tr>
                            `;

                    i++;
                    if (i > 1) {
                    $('#save').attr('disabled', false);
                    $('#add').attr('disabled', false);
                    }
                    $(".tbody").append(output);

                });

                //MATERIAL ONCLICK END//

                //SERVICE ONCLICK START//

                $('#save1').attr('disabled', true);
                $('#add1').attr('disabled', true);
                $(document).on('click', '#add1', function () {

                    $('#add1').attr('disabled', false);
                    var modal = `<div class="row">
                                                        <div id="modalCustom" class="col-lg-12 layout-spacing">
                                                            <div class="statbox widget box box-shadow">
                                                                    <div class="modal fade inputForm-modal" id="inputFormModal${i}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" id="inputFormModalLabel">
                                                                                    <h5 class="modal-title">Remarks For <b>SendBack</b></h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                        <div class="form-group">
                                                                                            <div class="input-group mb-3">
                                                                                                <textarea class="form-control" name="budget_remark[]" aria-label="With textarea"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="submit" class="btn btn-light-danger mt-2 mb-2 btn-no-effect" data-bs-dismiss="modal">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
                    $('.modal-section').append(modal);
                    var output = `
                            <tr>
                                <td>
                                    `+ (i) + `
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <select class="form-control item-dropdown1" id="item-dropdown1${i}"  style="width: auto;" data-id="${i}" name="item_code[]" placeholder="Select Name.">
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="text" class="form-control disabled1${i} description1"  style="width: auto;"  id="description1${i}" name="description[]" placeholder="Enter Description" onkeyup="checkemail();" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled1${i} uom1" id="uom1${i}"  style="width: auto;"  name="uom[]" placeholder="Enter UOM">
                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control  disabled MaterialGroup" id="MaterialGroup${i}" style="width: auto;"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" name="quantity[]" style="width: auto;" placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}" name="Expected_Date[]" style="width: auto;" placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: auto;" rows="2" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <input class="form-control file-upload-input" type="file" id="Attachment${i}" style="width: auto;" name="Attachment[]"  placeholder="Enter Quantity">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <select class="form-control" id="type_val1${i}" data-id="${i}" name="budget[]" style="width: auto;"  onchange="openPopup(this);" >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        
                                        <select class="form-control" id="Finance_Verification${i}" data-id="${i}" style="width: auto;" name="Finance_Verification[]"   >
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <i class="fa fa-times remove text-danger"  ></i>
                                </td>

                            </tr>
                            `;

                    i++;
                    if (i > 1) {
                    $('#save1').attr('disabled', false);
                    $('#add1').attr('disabled', false);
                    }
                    $(".tbody1").append(output);

                });

                //SERVICE ONCLICK END //

                //ASSET ONCLICK START//

                $('#save2').attr('disabled', true);
                $('#add2').attr('disabled', true);
                $(document).on('click', '#add2', function () {

                    $('#add2').attr('disabled', false);
                    var modal = `<div class="row">
                                                        <div id="modalCustom" class="col-lg-12 layout-spacing">
                                                            <div class="statbox widget box box-shadow">
                                                                    <div class="modal fade inputForm-modal" id="inputFormModal${i}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header" id="inputFormModalLabel">
                                                                                    <h5 class="modal-title">Remarks For <b>SendBack</b></h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                        <div class="form-group">
                                                                                            <div class="input-group mb-3">
                                                                                                <textarea class="form-control" name="budget_remark[]" aria-label="With textarea"></textarea>
                                                                                            </div>
                                                                                        </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="submit" class="btn btn-light-danger mt-2 mb-2 btn-no-effect" data-bs-dismiss="modal">Close</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
                    $('.modal-section').append(modal);
                    var output = `
                            <tr>
                            <td>
                                `+ (i) + `
                            </td>
                            <td>
                                <div class="col-md-12" >
                                <select class="form-control item-dropdown2" id="item-dropdown2${i}" style="width: auto;" data-id="${i}"  name="item_code[]" placeholder="Select Name.">
                                </select>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                <input type="text" class="form-control disabled1${i} description2" style="width: auto;"  id="description2${i}" name="description[]" placeholder="Enter Description" onkeyup="checkemail();" required="">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12" >
                                <input type="text" class="form-control disabled1${i} uom2" id="uom2${i}" style="width: auto;"  name="uom[]" placeholder="Enter UOM">
                                </div>
                            </td>
                            <td id="divb">
                            <div class="col-md-12" id="item2${i}">
                                        <input type="text" class="form-control  disabled1${i} MaterialGroup2${i}" style="width: auto;" id="MaterialGroup2${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                    <div class="col-md-12" id="New2${i}">
                                        <select class="form-control  MaterialGroupda2${i}" id="MaterialGroupda2${i}" style="width: auto;"  name="MaterialGroup[]">
                                        </select>
                                    </div>
                                </td>
                            <td>
                                <div class="col-md-12">
                                <input type="number" min="0" class="form-control" id="quantity${i}"  name="quantity[]" style="width: auto;" placeholder="Enter Quantity" required="">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                <input type="date" class="form-control" id="Expected_Date${i}"  name="Expected_Date[]" style="width: auto;" placeholder="Enter Quantity" required="">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                                    <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: auto;" rows="2" placeholder="Enter Specification" required=""></textarea>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <input class="form-control file-upload-input" type="file" id="Attachment${i}" name="Attachment[]" style="width: auto;"  placeholder="Enter Quantity">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                <select class="form-control" id="type_val1${i}" data-id="${i}" name="budget[]" style="width: auto;"  onchange="openPopup(this);" >
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                                </div>
                            </td>
                                                <td>
                                                    <div class="col-md-12">
                                                        
                                                        <select class="form-control" id="Finance_Verification${i}" style="width: auto;" data-id="${i}" name="Finance_Verification[]"   >
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </td>
                            <td>
                                <i class="fa fa-times remove text-danger"  ></i>
                            </td>

                            </tr>
                            `;

                    i++;
                    if (i > 1) {
                    $('#save2').attr('disabled', false);
                    $('#add2').attr('disabled', false);
                    }
                    $(".tbody2").append(output);

                });

                //ASSET ONCLICK END//

                $(document).on('click', '.remove', function () {
                    $($(this).closest("tr")).remove();
                    item.closest('div').find('.modal-section').remove();
                    // $($(item).closest(".modal-section")).remove();
                    i--;
                    if (i == 1) {
                    $('#save').attr('disabled', true);
                    $('#add').attr('disabled', true);
                    }
                })
            });
      //SERVICE END//
        </script>
        <!-- CUSTOM JS END -->
    </body>
</html>
