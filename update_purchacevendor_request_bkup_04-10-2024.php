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

// echo "Select Plant_Code,Plant_Description from PP_PLANT where Plant_Code = '$Plant' group by Plant_Code,Plant_Description";exit;
//end

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

        .select2-selection--single {
            height: 39px !important;
        }

        .select2-selection__rendered,.select2-selection__arrow {
            margin-top: 4px !important;
        }

        .mat_group_div div .select2-container--default,.storage_div div .select2-container--default,.plant_div div .select2-container--default {
            width: 209px !important;
        }

        </style>
    </head>

    
    <body  data-keep-enlarged="true" class="vertical-collpsed">

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
                                                $pl = $updated_query['Plant'];
                                                $sl = $updated_query['Storage_Location'];

                                                $mat_group_query =  sqlsrv_query($conn, "SELECT TOP 1 * from Tb_Request_Items WHERE Request_ID = '".$updated_query['Request_ID']."'");

                                                $mat_group_query_exec = sqlsrv_fetch_array($mat_group_query);

                                            ?>
                                            <form  method="POST" id="request_form" enctype="multipart/form-data">
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
                                                            <select class="select2 form-select " id="request_category" name="request_type">
                                                                <option value="">Select Category</option>
                                                                <option  <?php if ($updated_query['Request_Type'] == "Asset purchases") { ?> selected="selected" <?php } ?> value="Asset purchases">Asset purchases</option>
                                                                <option  <?php if ($updated_query['Request_Type'] == "Material purchases") { ?> selected="selected" <?php } ?> value="Material purchases">Material purchases</option>
                                                                <option  <?php if ($updated_query['Request_Type'] == "Services") { ?> selected="selected" <?php } ?> value="Services">Services</option>
                                                            </select>                                                        
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 plant_div">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label plant-dropdown-label">Plant</label>
                                                            <select class="form-select plant-dropdowns" name="plant_id" id="plant-dropdown">
                                                                <option value="">Select Plant</option>
                                                                <?php
                                                                    $request_type = ($updated_query['Request_Type'] == 'Asset purchases') ? 'ZCAP' : (($updated_query['Request_Type'] == 'Material purchases') ? 'ZNB' : 'ZSER');

                                                                    $plant = sqlsrv_query($conn, "SELECT DISTINCT Plant from Tb_Master_Emp where Document_type = '".$request_type."' AND PO_creator_Release_Codes = '".$_SESSION['EmpID']."'");
                                                                    while ($c = sqlsrv_fetch_array($plant)) {
                                                                ?>
                                                                <option <?php if ($updated_query['Plant'] == $c['Plant']) { ?> selected="selected" <?php } ?> value="<?php echo $c['Plant'] ?>">
                                                                    <?php echo $c['Plant'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 storage_div" style="<?php if($updated_query['Request_Type'] == 'Services') { ?> display: none; <?php } ?>">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label storage-dropdown-label">Storage Location</label>
                                                            <select class="storage-dropdowns form-select" name="storage_location" id="storage-dropdown">
                                                                <option value="">Select Storage Location</option>
                                                                <?php
                                                                    $storage = sqlsrv_query($conn, "SELECT DISTINCT StorageLocation,Plant from MaterialMaster Where Plant = '".$updated_query['Plant']."'");
                                                                    while ($row = sqlsrv_fetch_array($storage)) {
                                                                ?>

                                                                <option <?php if ($updated_query['Storage_Location'] == $row['StorageLocation']) { ?> selected="selected" <?php } ?> value="<?php echo $row["StorageLocation"]; ?>">
                                                                    <?php echo $row["Plant"]; ?>-
                                                                    <?php echo $row["StorageLocation"] ?>
                                                                </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mat_group_div">
                                                        <div class="mb-3">
                                                            <label for="mat_group" class="form-label mat_group_label" >Material Group</label>
                                                           <select class="mat_group form-select add" name="mat_group" id="mat_group">
                                                                <option value="">Select Material Group</option>
                                                                <?php


                                                                if($updated_query['Request_Type'] == 'Services') {

                                                                    $material  = sqlsrv_query($conn, "SELECT DISTINCT Material_Group as MaterialGroup from Tb_Master_Emp where Document_type = 'ZSER' and Plant = '".$updated_query['Plant']."' and PO_creator_Release_Codes = '".$_SESSION['EmpID']."'");
                                                                } else {
                                                                    $material = sqlsrv_query($conn, "SELECT MaterialMaster.MaterialGroup FROM MaterialMaster 
                                                                    INNER JOIN (SELECT DISTINCT Material_Group,PO_creator_Release_Codes FROM Tb_Master_Emp) as Tb_Master_Emp ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group 
                                                                    AND Tb_Master_Emp.PO_creator_Release_Codes = '".$_SESSION['EmpID']."' 
                                                                    WHERE MaterialMaster.StorageLocation = '".$updated_query['Storage_Location']."' AND MaterialMaster.Plant = '".$updated_query['Plant']."'  
                                                                    GROUP BY MaterialMaster.MaterialGroup");
                                                                }


                                                                    while ($row = sqlsrv_fetch_array($material)) {
                                                                ?>

                                                                <option <?php if ($mat_group_query_exec['MaterialGroup'] == $row['MaterialGroup']) { ?> selected="selected" <?php } ?> value="<?php echo $row["MaterialGroup"]; ?>">
                                                                    <?php echo $row["MaterialGroup"]; ?>
                                                                </option>
                                                                <?php } ?>
                                                            
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="text-end pb-3">
                                                        <button type="button" class="btn btn-primary btn-sm" id="add">
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
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
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="tbody material_section_body">
                                                                <?php
                                                      
                                                                    $row_no = 1;
                                                                    $sql = "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items
                                                                    ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID WHERE Tb_Request.Request_ID = '$req_id'";
                                                                    $stmt = sqlsrv_query($conn, $sql,array(), array("Scrollable" => 'static'));
                                                                    $row_count = sqlsrv_num_rows($stmt);

                                                                    while ($row = sqlsrv_fetch_array($stmt)) {
                                                                        $MatrialGroup = $row['MaterialGroup'];
                                                                        $storage = $row['Storage_Location'];
                                                                ?>
                                                                <input type="hidden" id="trow_no" value="<?php echo $row_count; ?>">

                                                                <tr data-rowno="<?php echo $row_no; ?>">
                                                                        <td class="sr_no">
                                                                            <input type="hidden" class="form-control"name="id[]" value="<?php echo $row['ID'] ?>">
                                                                            <?php echo $row_no; ?>
                                                                        </td>
                                                                        <td>
                                                                            <div class="col-md-12" >
                                                                                <select class="select2 form-control items-dropdown" id="item-dropdown<?php echo $row_no; ?>" style="width: 175px;" name="item_code[]" placeholder="Select Name." >
                                                                                    <option value="">Select Item Code</option>
                                                                                    <option <?php if ($row['Item_Code'] == 'New_Item') { ?> selected="selected" <?php } ?> value="New_Item">New Item</option>
                                                                                    <?php
                                                                                    if($row['Request_Type'] == 'Services') {
                                                                                        $result1 = sqlsrv_query($conn, "SELECT DISTINCT ASNUM as ItemCode,ASKTX as ItemDescription from SERVICE_MATERIAL_MASTER"); 
                                                                                    } else {    
                                                                                        $result1 = sqlsrv_query($conn, "SELECT MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group,Tb_Master_Emp.Plant FROM MaterialMaster INNER JOIN Tb_Master_Emp
                                                                                        ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group 
                                                                                         AND Tb_Master_Emp.Plant = MaterialMaster.Plant 
                                                                                        WHERE MaterialMaster.Plant = '".$updated_query['Plant']."' AND MaterialMaster.StorageLocation = '".$updated_query['Storage_Location']."' AND Material_Group = '".$mat_group_query_exec['MaterialGroup']."' AND Tb_Master_Emp.PO_creator_Release_Codes = '$Employee_Id'
                                                                                        GROUP BY MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group,Tb_Master_Emp.Plant");
                                                                                    }
                                                                                    while ($row1 = sqlsrv_fetch_array($result1)) {
                                                                                    ?>
                                                                                    <option <?php if ($row['Item_Code'] == $row1['ItemCode']) { ?> selected="selected" <?php } ?> value="<?php echo $row1["ItemCode"]; ?>"><?php echo $row1["ItemDescription"]; ?>&nbsp;-&nbsp;
                                                                                        <?php echo $row1["ItemCode"]; ?></option>
                                                                                    <?php } ?>

                                                                                </select>
                                                                            </div>
                                                                        </td>
                                                                        <td id="divn">
                                                                            <div class="col-md-12">
                                                                            <textarea id="description<?php echo $row_no; ?>" style="width: 210px;" class="form-control disabled description" name="description[]"   row="2" placeholder="Enter Description" required=""><?php echo $row['Description']; ?></textarea>

                                                                            </div>
                                                                        </td>
                                                                        <td id="divb">
                                                                            <div class="col-md-12" >
                                                                                <input type="text" class="form-control disabled uom" id="uom<?php echo $row_no; ?>" style="width: 110px;"  name="uom[]" readonly placeholder="Enter UOM" value="<?php echo $row['UOM']; ?>">
                                                                            </div>
                                                                        </td>
                                                                        <td id="divb">
                                                                            <div class="col-md-12" id="existing_material_div">
                                                                                <input type="text" class="form-control  disabled MaterialGroup" style="width: 100px;" id="MaterialGroup<?php echo $row_no; ?>"  readonly name="MaterialGroup[]" placeholder="Enter MaterialGroup" value="<?php echo $row['MaterialGroup']; ?>">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="col-md-12">
                                                                                <input type="number" min="0" class="form-control" id="quantity<?php echo $row_no; ?>" style="width: 125px;" name="quantity[]"  placeholder="Enter Quantity" required="" value="<?php echo $row['Quantity']; ?>">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="col-md-12">
                                                                                <input type="date" class="form-control" id="Expected_Date<?php echo $row_no; ?>" style="width: 155px;" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date('Y-m-d'); ?>" name="Expected_Date[]"  placeholder="Enter Quantity" required="" value="<?php echo $row['Expected_Date']; ?>">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="col-md-12">
                                                                                <textarea id="Specification<?php echo $row_no; ?>" class="form-control" name="Specification[]" style="width: 155px;height: 36px;"  row="2" placeholder="Enter Specification" required=""><?php echo $row['Specification']; ?></textarea>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="col-md-12">
                                                                                <input class="form-control file-upload-input" type="file"  id="Attachment<?php echo $row_no; ?>" style="width: 170px;" name="Attachment[]"  placeholder="Enter Quantity" >
                                                                                <input type="hidden" name="Saved_Attachment[]" value="<?php echo $row['Attachment']; ?>">
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="col-md-12">
                                                                            
                                                                                <select class="select2 form-control" id="type_val" data-id="<?php echo $row_no; ?>"  name="budget[]" style="width: 145px;"  onchange="openPopup(this);" >
                                                                                    <option <?php if ($row['Budget'] == "Yes") { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                                                    <option <?php if ($row['Budget'] == "No") { ?> selected="selected" <?php } ?> value="No">No</option>
                                                                                </select>
                                                                            </div>
                                                                        </td>

                                                                        <td>
                                                                            <i class="fa fa-times remove text-danger"  ></i>
                                                                        </td>

                                                                </tr>
                                                                <?php $row_no++; } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- MODEL -->
                                                    <div class="modal-section">
                                                    </div>
                                                    <!-- END MODEL -->
                                                </div>
                                                <div class="row mb-3 d-none">
                                                <label for="example-datetime-local-input" class="col-sm-2 col-form-label">Finance Verification<span class="required-label"style="color:red">*</span></label>
                                                    <div class="col-sm-2">
                                                      <!--   <?php
                                                        $finance_verifier_exist_check = "SELECT TOP 1 Finance_Verifier FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."'";
                                                        $finance_verifier_exist_check_exec = sqlsrv_query($conn, $finance_verifier_exist_check);
                                                        $verifier_status = sqlsrv_fetch_array($finance_verifier_exist_check_exec)['Finance_Verifier'];

                                                        ?> -->
                                                        <select class="select2 form-control" name="Finance_Verification">
                                                            <option value="">Select</option>
                                                            <option value="Yes">Yes</option>
                                                            <option value="No">No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-datetime-local-input" class="col-md-2 col-sm-2 col-form-label">Informer Details</label>
                                                    <div class="col-md-10 col-sm-10 informer-div">
                                                        <select class="select2 form-control select2-multiple" name="persion[]"  implode multiple data-placeholder="Choose ...">
                                                            <?php
                                                                $HR_Master_Table = sqlsrv_query($conn, "Select * from HR_Master_Table ");
                                                                while ($HR = sqlsrv_fetch_array($HR_Master_Table)) {
                                                                    $res = explode(',',$updated_query['Persion_In_Workflow']);
                                                                ?>
                                                                 <option <?= (in_array($HR['Employee_Code'],$res)) ? 'selected' : ''?> value="<?php echo $HR['Employee_Code'] ?>">
                                                                    <?php echo $HR['Employee_Name'] ?>&nbsp;-&nbsp;<?php echo $HR['Employee_Code'] ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row" id="involved_persons_div" style="display:none;">
                                                    <div class="col-md-5">
                                                        <h4>Involved Persons</h4>
                                                        <table class="table table-striped table-bordered table-hover" >
                                                          <thead>
                                                            <tr>
                                                              <th>Purchaser</th>
                                                              <!-- <th>Finance</th> -->
                                                              <th>Recommender</th>
                                                              <th>Approver</th>
                                                            </tr>
                                                          </thead>
                                                          <tbody id="involved_persons_tbody">

                                                          </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                  <div class="row mb-3 refer1">
                                                    <label for="example-datetime-local-input" class="col-sm-2 col-form-label">Reference</label>
                                                    <div class="col-sm-5">
                                                        <input type="text" class="CD_Supplr form-control" name="reference" value="<?php echo $updated_query['Reference']; ?>">
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <tr>
                                                            <td>
                                                                <!-- Extra Large modal -->
                                                                <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target=".bs-example-modal-xl">View Reference</button>

                                                                <!--  Modal content for the above example -->
                                                                <div class="modal fade bs-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-xl">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title mt-0" id="myExtraLargeModalLabel">Previous Reference</h5>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                    
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <!-- <form method="POST" > -->
                                                                                    <div class="row">
                                                                                        <div class="col-sm-3 nofilterhide">
                                                                                            <div class="form-group">
                                                                                                <label>Department</label>
                                                                                                <input type="text" class="form-control" value="<?php echo $department ?>" Readonly id="dept" name="dept">
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-2 datefilterhide">
                                                                                            <div class="form-group">
                                                                                                <label>Request Category</label>
                                                                                                
                                                                                                <select class="form-control" id="category" name="category" >
                                                                                                <option value="">Select Category</option>
                                                                                                    <?php
                                                                                                        $Request_Category = sqlsrv_query($conn, "SELECT Request_Type FROM Tb_Request WHERE EMP_ID = '$Employee_Id' AND status = 'Approved' GROUP BY Request_Type  ");
                                                                                                        while ($RC = sqlsrv_fetch_array($Request_Category)) {
                                                                                                        ?>
                                                                                                        <option value="<?php echo $RC['Request_Type'] ?>">
                                                                                                            <?php echo $RC['Request_Type'] ?>
                                                                                                        </option>
                                                                                                    <?php } ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-2 datefilterhide">
                                                                                            <div class="form-group">
                                                                                                <label>Plant</label>
                                                                                                <!-- <input type="text" class="form-control" name="plant"> -->
                                                                                                
                                                                                                <select class="form-control" id="plant" name="plant" >
                                                                                                <option value="">Select Plant</option>
                                                                                                    <?php
                                                                                                        $Request_Category = sqlsrv_query($conn, "SELECT Plant FROM Tb_Request WHERE EMP_ID = '$Employee_Id' AND status = 'Approved' GROUP BY Plant");
                                                                                                        while ($RC = sqlsrv_fetch_array($Request_Category)) {
                                                                                                        ?>
                                                                                                        <option value="<?php echo $RC['Plant'] ?>">
                                                                                                            <?php echo $RC['Plant'] ?>
                                                                                                        </option>
                                                                                                    <?php } ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-2 datefilterhide">
                                                                                            <div class="form-group">
                                                                                                <label>Storage Location</label>
                                                                                                <!-- <input type="text" class="form-control" name="storage"> -->
                                                                                                <select class="form-control" id="storage" name="storage" >
                                                                                                <option value="">Select Storage Location</option>
                                                                                                    <?php
                                                                                                        $Request_Category = sqlsrv_query($conn, "SELECT Storage_Location FROM Tb_Request WHERE EMP_ID = '$Employee_Id' AND status = 'Approved' GROUP BY Storage_Location");
                                                                                                        while ($RC = sqlsrv_fetch_array($Request_Category)) {
                                                                                                        ?>
                                                                                                        <option value="<?php echo $RC['Storage_Location'] ?>">
                                                                                                            <?php echo $RC['Storage_Location'] ?>
                                                                                                        </option>
                                                                                                    <?php } ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3 datefilterhide">
                                                                                            <div class="form-group">
                                                                                                <label>Item Code</label>
                                                                                                <!-- <input type="text" class="form-control" name="item"> -->
                                                                                                <select class="form-control" id="item" name="item" >
                                                                                                    <option value="">Select Item Code</option>
                                                                                                    <?php
                                                                                                        $Request_Category = sqlsrv_query($conn, "SELECT Item_Code FROM Tb_Request_Items WHERE EMP_ID = '$Employee_Id' AND status = 'Approved' GROUP BY Item_Code  ");
                                                                                                        while ($RC = sqlsrv_fetch_array($Request_Category)) {
                                                                                                        ?>
                                                                                                        <option value="<?php echo $RC['Item_Code'] ?>">
                                                                                                            <?php echo $RC['Item_Code'] ?>
                                                                                                        </option>
                                                                                                    <?php } ?>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <br>
                                                                                    <center><button type="button" id="filter" name="filter" class="btn btn-primary">Apply</button>
                                                                                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button></center>
                                                                                <!-- </form> -->
                                                                                <div class="table-responsive">  
                                                                                <table id="datatable" class="items table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>#</th>
                                                                                            <th>Request ID</th>
                                                                                            <th>Vendor Name</th>
                                                                                            <th>Vendor SAP</th>
                                                                                            <th>vendor Active SAP</th>
                                                                                            <th>Last Purchase</th>
                                                                                            <th>Delivery Time</th>
                                                                                            <th>Quantity</th>
                                                                                            <th>Price</th>
                                                                                            <th>Total</th>
                                                                                            <th>Total Amount</th>
                                                                                            <th>Fright Charges</th>
                                                                                            <th>Insurance Details</th>
                                                                                            <th>GST Component</th>
                                                                                            <th>Warrenty</th>
                                                                                            <th>Payment Terms</th>
                                                                                            <th>Total Budget Finance</th>
                                                                                            <th>Available Budget Finance</th>
                                                                                            <th>Verification Type Finance</th>
                                                                                            <th>Finance Remarks</th>
                                                                                            <th>Requester Remarks</th>
                                                                                            <th>Recommender Remarks</th>
                                                                                            <th>Approver_Remarks</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody class='item-row'>

                                                                                    </tbody>
                                                                                    
                                                                                   
                                                                                    <input type="hidden" class="nbcomp" name="nbcomp" id="nbcomp" value="0"/>
                                                                                </table>
                                                                            </div>
                                                                            </div>
                                                                        </div><!-- /.modal-content -->
                                                                    </div><!-- /.modal-dialog -->
                                                                </div><!-- /.modal -->
                                                            </td>
                                                        </tr>
                                                    </div>
                                                </div>

                                                <div class="mb-0">
                                                    <center>
                                                    <div>
                                                        <button class="btn btn-primary waves-effect waves-light me-1" type="button" id="save" name="save" >
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

       <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

        <!-- ADD END -->

        <!-- CUSTOM JS START -->
        <script>
            var i = 1;
            var sno = 1;

            // ON CHANGE DROPDOWN 
            $(document).ready(function () {     
                $('#plant-dropdown').select2();
                $("#storage-dropdown").select2();
                $('#mat_group').select2();

                var Plant         = $('#plant-dropdown').val();
                var MaterialGroup = $('#mat_group').val();

                get_involved_persons(Plant,MaterialGroup); 


                //update the items row no start
                var row_no = $('#trow_no').val();                         
                row_no++;

                $('#trow_no').val(row_no);
                //update the items row no end 
            });



            $(document).on('change', '#request_category', function () {
                var request_type = $(this).val();
                $(".plant_div").hide();
                $(".storage_div").hide();
                $(".mat_group_div").hide();

                $('.material_section_body').empty();

                // request type document no set 
                var request_type_code = (request_type == 'Asset purchases') ? 'ZCAP' : ((request_type == 'Material purchases') ? 'ZNB' : 'ZSER');

                $('#trow_no').val(1);

                $.ajax({
                    url: 'common_ajax.php',
                    type: 'POST',
                    data: { Action : 'get_plant_code',request_type : request_type_code },
                    dataType: "json",
                    beforeSend: function(){
                        $('#ajax-preloader').show();
                    },
                    success: function(response) {
                        var option = '<option value"">Choose Plant</option>';
                        for(i in response) {
                            option += `<option value="${ response[i].Plant }">${ response[i].Plant }</option>`;
                        }

                        $('#plant-dropdown').html(option);
                        $('#plant-dropdown').select2();
                        $('#plant-dropdown').show();
                        $('.plant_div').show();

                    },
                    complete:function() {
                        $('#ajax-preloader').hide();
                    }
                });
            });



            $('#plant-dropdown').on('change', function () {
                var Plant_Code = this.value;
                $(".storage_div").hide();
                $(".mat_group_div").hide();
                $('.material_section_body').empty();
                $('#trow_no').val(1);

                $.ajax({
                    url: "fetch-storage.php",
                    type: "POST",
                    data: {
                        Plant_Code: Plant_Code
                    },
                    cache: false,
                    beforeSend:function(){
                        $('#ajax-preloader').show();
                    },
                    success: function (result) {
                        $("#storage-dropdown").html(result);
                        $("#storage-dropdown").select2();
                        $(".storage_div").show();
                    },
                    complete:function(){
                        $('#ajax-preloader').hide();
                    }
                });
            });


            $('#storage-dropdown').on('change', function () {
               // alert("Hai");
                var Storage_Location = this.value;
                var Plant  = $('#plant-dropdown').val();

                var request_type = $('#request_category').val();

              // request type document no set 
                var request_type_code = (request_type == 'Asset purchases') ? 'ZCAP' : ((request_type == 'Material purchases') ? 'ZNB' : 'ZSER');


                $('#trow_no').val(1);

                $.ajax({
                    url: "common_ajax.php",
                    type: "POST",
                    data: {
                        Action : 'get_material_group',
                        Storage_Location: Storage_Location,
                        Plant : Plant,request_type : request_type_code
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend:function(){
                        $('#ajax-preloader').show();
                    },
                    success: function (result) {
                        var option = '<option value="">Select Material Group</option>'
                        for(i in result) {
                            option += `<option value="${ result[i].MaterialGroup }">${ result[i].MaterialGroup }</option>`;
                        }
                        $('#mat_group').html(option);
                        $('#mat_group').select2();
                        $('.mat_group_div').show();
                    },
                    complete:function(){
                        $('#ajax-preloader').hide();
                    }
                });
            });

         


            $('#mat_group').on('change', function () {
                var request_category = $('#request_category').val();
                var Storage_Location = $('#storage-dropdown').val();
                var Plant  = $('#plant-dropdown').val();
                var MaterialGroup = $(this).val();
                $('.material_section_body').empty();
                $('#trow_no').val(1);

                $.ajax({
                    url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
                    type: "POST",
                    data: {
                        Storage_Location: Storage_Location,
                        Plant : Plant,
                        MaterialGroup : MaterialGroup
                    },
                    cache: false,                        
                    beforeSend:function(){
                        $('#ajax-preloader').show();
                    },
                    success: function (result) {
                        $(".items-dropdown").html(result);
                        $(".items-dropdown").select2();
                        $('#MaterialGroup1').val(MaterialGroup);
                        $('#add').prop('disabled',false);  
                        get_involved_persons(Plant,MaterialGroup);                          
                    },
                    complete:function(){
                        $('#ajax-preloader').hide();
                    }
                });
            });

            function get_involved_persons(Plant,MaterialGroup)
            {
                $.ajax({
                    url: "common_ajax.php",
                    type: "POST",
                    data: {
                        Plant : Plant,
                        MaterialGroup : MaterialGroup,
                        Action : 'get_involved_persons'
                    },
                    cache: false,
                    dataType: 'json',                        
                    beforeSend:function(){
                        $('#ajax-preloader').show();
                    },
                    success: function (result) {
                        var table_data = '';
                        if(result.length > 0) {
                            // <td>${ result[0].verifier_name }</td>
                            table_data = `<tr>
                            <td>${ (result[0].purchaser_name != '' && result[0].purchaser_name != null) ? result[0].purchaser_name : '-' }</td>
                            <td>${ (result[0].recommendor_name != '' && result[0].recommendor_name != null) ? result[0].recommendor_name : '-' }</td>
                            <td>${ (result[0].approver_name != '' && result[0].approver_name != null) ? result[0].approver_name : '-' }</td>
                            </tr>`; 

                        }
                        $('#involved_persons_tbody').html(table_data);  
                        $('#involved_persons_div').show();                                  
                    },
                    complete:function(){
                        $('#ajax-preloader').hide();
                    }
                });
            }


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


            
            // DISABLE
            $(document).on('change', '.items-dropdown', function () {
                var stateID = $(this).val();
                if (stateID != 'New_Item') {
                    // $('.items-dropdown option').each(function(){
                    //     $(this).prop('disabled', false);
                    // });
                    // $('.items-dropdown option:selected').prop('disabled', true);
                }
            });
           

            function openPopup(item) {
                var id = $(item).data('id');
                $("#inputFormModal" + id).modal('show');
            }

            function append_items(row_no) {

                var Storage_Location = $('#storage-dropdown').val();
                var Plant  = $('#plant-dropdown').val();
                var MaterialGroup = $('#mat_group').val();

                $.ajax({
                    url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
                    type: "POST",
                    data: {
                        Storage_Location: Storage_Location,
                        Plant : Plant,
                        MaterialGroup : MaterialGroup
                    },
                    cache: false,
                    beforeSend:function(){
                        $('#ajax-preloader').show();
                    },
                    success: function (result) {
                        $('#item-dropdown'+row_no).html(result);
                        $('#item-dropdown'+row_no).select2();
                    },
                    complete: function() {
                        $('#ajax-preloader').hide();
                    }
                });

            }


            function add_row(action = '')
            {
                if($('#trow_no').val() == 0) {
                    $('#trow_no').val(1);
                    $('#save').attr('disabled',false);
                }   
                var row_no = $('#trow_no').val();

                var modal = `<div class="row">
                <div id="modalCustom" class="col-lg-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                            <div class="modal fade inputForm-modal" id="inputFormModal${row_no}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
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
                        <tr data-rowno="${row_no}">
                            <td class="sr_no">
                                `+ (row_no) + `
                            </td>
                            <td>
                                <div class="col-md-12" >
                                    <select class="select2 form-control items-dropdown" id="item-dropdown${row_no}" style="width: 175px;" name="item_code[]" placeholder="Select Name." >
                                    </select>
                                </div>
                            </td>
                            <td id="divn">
                                <div class="col-md-12">
                                <textarea id="description${row_no}" style="width: 210px;" class="form-control disabled description" name="description[]"   row="2" placeholder="Enter Description" required=""></textarea>

                                </div>
                            </td>
                            <td id="divb">
                                <div class="col-md-12" >
                                    <input type="text" class="form-control disabled uom" id="uom${row_no}" style="width: 110px;"  name="uom[]" readonly placeholder="Enter UOM" >
                                </div>
                            </td>
                            <td id="divb">
                                <div class="col-md-12" id="existing_material_div">
                                    <input type="text" class="form-control  disabled MaterialGroup" style="width: 100px;" id="MaterialGroup${row_no}"  readonly name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <input type="number" min="0" class="form-control" id="quantity${row_no}" style="width: 125px;" name="quantity[]"  placeholder="Enter Quantity" required="">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <input type="date" class="form-control" id="Expected_Date${row_no}"style="width: 155px;" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date('Y-m-d'); ?>" name="Expected_Date[]"  placeholder="Enter Quantity" required="">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <textarea id="Specification${row_no}" class="form-control" name="Specification[]" style="width: 155px;height: 36px;"  row="2" placeholder="Enter Specification" required=""></textarea>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <input class="form-control file-upload-input" type="file"  id="Attachment${row_no}" style="width: 170px;" name="Attachment[]"  placeholder="Enter Quantity" >
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                
                                    <select class="select2 form-control" id="type_val${row_no}" data-id="${row_no}"  name="budget[]" style="width: 145px;"  onchange="openPopup(this);" >
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </td>

                            <td>
                                <i class="fa fa-times remove text-danger"  ></i>
                            </td>

                </tr>`;


                $(".tbody").append(output);
                if(action == 'add_more') {
                    append_items(row_no);
                }
                row_no++;
                $('#trow_no').val(row_no);
            }
           $(document).on('click', '#add', function () {
                add_row('add_more');
            });

            $(document).on('click', '.remove', function () {
                $($(this).closest("tr")).remove();
                var row_no = $('#trow_no').val();

                row_no--;
                $('#trow_no').val(row_no);

                if($('#trow_no').val() == 0) {
                    $('#save').attr('disabled',true);
                }

                serial_no();

            });

            function serial_no()
            {
                  $('.sr_no').each(function(index){
                     index++;
                     $(this).text(index);

                     $(this).closest("tr").find('.file-upload-input').attr('id','Attachment'+index);
                  });
            }

            $(document).on('click','#save',function(){

                  // duplicate items validation functionality Start
                  var items_arr = [];
                  var item_validation_error = '';
                  
                  $('.items-dropdown').each(function(index){
                        index++;
                        if(items_arr.length > 0 && items_arr.includes($(this).val())) {
                            item_validation_error = `Row no - ${ index } item code is already added.please remove and another one.`;
                            return false;
                        } 

                        if(items_arr.length == 0 || !items_arr.includes($(this).val()) ) {
                            items_arr.push($(this).val());
                        }   
                  });  

                  // duplicate items validation functionality End


                  if(item_validation_error != '') {
                        swal({
                          title: "Error!",
                          text: item_validation_error,
                          icon: "warning",
                        });
                  } else {
                      var formdata = document.querySelector('#request_form');
                      var form = new FormData(formdata);    
                      form.append('Action', 'update_purchase_request');

                      $('.file-upload-input').each(function(index){
                        index++;
                        form.append('Attachment'+index , $(this)[0].files[0]);
                      });

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
                                  alert(response.message);
                                  window.location.href = 'show_purchacevendor_request.php';
                              } else {
                                  alert(response.message);
                              }
                          },
                          complete:function() {
                                $('#ajax-preloader').hide();
                          }
                      });
                  }
            });


                $('#filter').on('click', function () {
                    
                    $(".items tr>td").remove();
                    var Department=$("#dept").val();
                    var Category=$("#category").val();
                    var Plant=$("#plant").val();
                    var Storage=$("#storage").val();
                    var Item=$("#item").val();
                    $.ajax({
                            data: { Department: Department , Category: Category , Plant: Plant , Storage: Storage , Item: Item },
                            type: 'POST',
                            dataType: 'json',
                            url: 'filter_ajax.php',
                            success: function(response) {
                            var content = response.content;
                            $(".item-row:first").before(content);
                                var selected_checkbox=[];
                                $('.checkboxstyle').change(function()
                                {
                                if($(this).is(':checked'))
                                {
                                        //If checked add it to the array
                                        selected_checkbox.push($(this).val());
                                } 
                                else 
                                {
                                    //If unchecked remove it from array
                                    for (var i=selected_checkbox.length-1; i>=0; i--) 
                                    {
                                        if (selected_checkbox[i] === $(this).val()) 
                                            selected_checkbox.splice(i, 1);
                                    }
                                }
                                    $('.CD_Supplr').val(selected_checkbox.join(','));
                                });
                            },
                            error: function(xhr, status, error) {
                                alert(xhr.responseText);
                            }
                    });
                });

            // END MORE 
        </script>
        <!-- CUSTOM JS END -->
    </body>
</html>
