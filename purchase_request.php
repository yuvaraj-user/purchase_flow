<?php
include('../auto_load.php');
include('adition.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function auto_increment_request_id($conn)
{
    $previous_request_id_sql = "SELECT TOP 1 Request_ID from Tb_Request order by Id desc";
    $previous_request_id_sql_exec = sqlsrv_query($conn, $previous_request_id_sql);
    $previous_request_id = sqlsrv_fetch_array($previous_request_id_sql_exec)['Request_ID'];
    $previous_request_id_number = filter_var($previous_request_id, FILTER_SANITIZE_NUMBER_INT);

    $total_request_id_no_count = strlen($previous_request_id_number);
    $number_request_id = ltrim($previous_request_id_number,'0');

    $leading_zero_count = $total_request_id_no_count - strlen($number_request_id);
    $number_request_id++;

    $incremented_request_id = $number_request_id;
    if($leading_zero_count > 0) {
        $leading_zeros = '';
        for ($i=0; $i < $leading_zero_count; $i++) { 
            $leading_zeros = $leading_zeros.'0';
        }

        $incremented_request_id = "PV".$leading_zeros.$number_request_id;
    }
    return $incremented_request_id; 
}

if(!isset($_SESSION['EmpID']))
{
    ?>
    <script type="text/javascript">
        window.location = "../pages/indexAdmin.php";
    </script>
    <?php
}
// global query
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

        .error_msg {
            display: none;
            font-size: 13px;
        }

      /*  .replacement_feature {
            display: none !important;
        }*/

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
                                <input type="hidden" id="trow_no" value="1">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
    
                                            <form  method="POST" id="request_form" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-md-2" > 
                                                        <div class="mb-3">
                                                            <label for="validationCustom01" class="form-label">RequestID</label>
                                                            <input type="text" class="form-control" id="validationCustom01" name="request_id"
                                                                value="<?php echo auto_increment_request_id($conn) ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" >
                                                        <div class="mb-3">
                                                            <label for="hasta" class="form-label">Department</label>
                                                            <input type="hidden" class="form-control" id="request" name="request_type1" value="Purchase & Vendor selection (R&VS)">
                                                            <input type="text" class="form-control" name="department" id="Department3" readonly
                                                                value="<?php echo $department ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" >
                                                        <div class="mb-3">
                                                            <label for="Requester" class="form-label">Request Category</label>
                                                            <select class="select2 form-select " id="request_category" name="request_type">
                                                                <option value="">Select Category</option>
                                                                <option value="Asset purchases">Asset purchases</option>
                                                                <option value="Material purchases">Material purchases</option>
                                                                <option value="Services">Services</option>
                                                            </select>                                                        
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 plant_div" style="display:none;">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label plant-dropdown-label">Plant</label><br>
                                                            <select class="form-select plant-dropdowns" name="plant_id" id="plant-dropdown">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 storage_div" style="display:none;">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label storage-dropdown-label">Storage Location</label>
                                                            <select class="storage-dropdowns form-select" name="storage_location" id="storage-dropdown">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 mat_group_div" style="display:none;">
                                                        <div class="mb-3">
                                                            <label for="mat_group" class="form-label mat_group_label" >Material Group</label>
                                                           <select class="mat_group form-select add" name="mat_group" id="mat_group">
                                                            </select>
                                                        </div>
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
                                                                    <th class="replacement_feature">Replacement</th>
                                                                    <th>Expected Date</th>
                                                                    <th>Specification </th>
                                                                    <th>Attachment</th>
                                                                    <th>Whether Budgeted</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="tbody material_section_body">

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- MODEL -->
                                                    <div class="modal-section">
                                                    </div>
                                                    <!-- END MODEL -->
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
                                                        <input type="text" class="CD_Supplr form-control" name="reference" >
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
                                                                                    <center>
                                                                                        <button type="button" id="filter" name="filter" class="btn btn-primary">Apply</button>
                                                                                         <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                                                                    </center>
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

                       <div class="modal replacementModal" id="replacementModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Replacement Details</h5>
                                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                                    </div>
                                    <div class="modal-body">
                                        <!-- Inputs for the modal -->
                                        <div class="form-group">
                                            <label for="modal_dateofpurchase">Date of Purchase</label>
                                            <input type="date" class="form-control" id="modal_dateofpurchase" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_qty">Quantity</label>
                                            <input type="number" class="form-control" id="modal_qty" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_remarks">Remarks</label>
                                            <textarea class="form-control" id="modal_remarks" required></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="modal_cost">Cost</label>
                                            <input type="text" class="form-control" id="modal_cost" required>
                                        </div>
                                        <!-- Hidden input to store the row ID -->
                                        <input type="hidden" id="row_id">
                                    </div>
                                    <div class="modal-footer">
                                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                                        <button type="button" class="btn btn-primary" id="saveReplacement">Save changes</button>
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
            var appendQty = '';

            // ON CHANGE DROPDOWN 
            $(document).ready(function () {
                $('.plant_div').hide();
                $('.storage_div').hide();
                $('.mat_group_div').hide();     

                $('#save').attr('disabled', true);
                $('#add').attr('disabled', true);
            });

            $(document).on('input', '.quantity', function () {

                var id = $(this).data('id');
                appendQty = $('#quantity'+id).val();
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
                            option += `<option value="${ response[i].Plant }">${ response[i].Plant } - ${ response[i].Plant_Name}</option>`;
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


                // replacement option show only for asset purchases category
                $('.replacement_feature').show();
                if(request_type != 'Asset purchases') {
                    $('.replacement_feature').hide();
                }

            });



            $('#plant-dropdown').on('change', function () {
                var Plant_Code = this.value;
                var request_type = $('#request_category').val();
                var request_type_code = (request_type == 'Asset purchases') ? 'ZCAP' : ((request_type == 'Material purchases') ? 'ZNB' : 'ZSER');

                $(".storage_div").hide();
                $(".mat_group_div").hide();
                $('.material_section_body').empty();
                $('#trow_no').val(1);

                if(request_type_code == 'ZSER') {
                    $.ajax({
                        url: "common_ajax.php",
                        type: "POST",
                        data: {
                            Action : 'get_material_group',
                            Plant : Plant_Code,
                            request_type : request_type_code
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
                } else {
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
                            if(request_type_code == 'ZSER') {
                                $(".storage_div").hide();
                            } 
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
                        }
                    });
                }
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

                var request_type = $('#request_category').val();

              // request type document no set 
                var request_type_code = (request_type == 'Asset purchases') ? 'ZCAP' : ((request_type == 'Material purchases') ? 'ZNB' : 'ZSER');

                $.ajax({
                    url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
                    type: "POST",
                    data: {
                        Storage_Location: Storage_Location,
                        Plant : Plant,
                        MaterialGroup : MaterialGroup,
                        request_type_code : request_type_code
                    },
                    cache: false,                        
                    beforeSend:function(){
                        $('#ajax-preloader').show();
                    },
                    success: function (result) {
                        $(".items-dropdown").html(result);
                        $(".items-dropdown").select2();

                        $(".replacement-dropdown").select2();

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

                        } else {
                            $('#save').attr('disabled','disabled');
                        }
                        $('#involved_persons_tbody').html(table_data);  
                        $('#involved_persons_div').show();                                  
                    },
                    complete:function(){
                        $('#ajax-preloader').hide();
                    }
                });
            }


            $(document).on('change', '.add', function () {
                add_row();
                $('#save').attr('disabled',false)
            });

            $(document).on('change', '.items-dropdown', function () {
                var ItemCode = $(this).val();
                var ItemCode_closet = $(this);
                var id = $(this).data('id');
                $('#replace'+id).prop('disabled',false);

                if(ItemCode=='New_Item')
                {
                    $('#replace'+id).val('new').trigger("change");
                    $('#uom'+id).prop('readonly',false);
                    $('#uom'+id).removeClass('disabled');
                    $('#uom'+id).val('');
                    $('#description'+id).val('');
                }
                else
                {
                    $('#replace'+id).val('').trigger("change");
                    $('#uom'+id).prop('readonly',true);
                    $('#uom'+id).addClass('disabled');
                    $(this).closest('td').find('.item_code_info').text(ItemCode);
                    $(this).closest('td').find('.item_code_info').show();

                }



                var request_type = $('#request_category').val();

              // request type document no set 
                var request_type_code = (request_type == 'Asset purchases') ? 'ZCAP' : ((request_type == 'Material purchases') ? 'ZNB' : 'ZSER');

                $.ajax({
                    url: "getUser.php",
                    dataType: 'json',
                    type: 'POST',
                    async: true,
                    data: { ItemCode: ItemCode,request_type_code : request_type_code },
                    success: function (data) {
                        ItemCode_closet.closest('tr').find('.uom').val(data.UOM);
                        ItemCode_closet.closest('tr').find('.description').val(data.ItemDescription);
                    }
                });
                // $.ajax({
                //     url: "fetch-new.php?employe=<?php echo $Employee_Id ?>",
                //     type: "POST",
                //     data: {
                //         ItemCode: ItemCode
                //     },
                //     cache: false,
                //     success: function (result) {
                //         $(".MaterialGroupda").html(result);
                //         $(".MaterialGroupda").select2();
                //     }
                // });

                $.ajax({
                    url: "fetch-item-metrial.php?employe=<?php echo $Employee_Id ?>",
                    type: "POST",
                    data: {
                        ItemCode: ItemCode,
                        request_type_code : request_type_code
                    },
                    cache: false,
                    success: function (result) {
                        if(request_type_code == 'ZSER') {
                            var mat_group = $('#mat_group').val();
                            ItemCode_closet.closest('tr').find('.MaterialGroup').val(mat_group);
                        } else {
                            ItemCode_closet.closest('tr').find('.MaterialGroup').val(result.MaterialGroup);
                        }
                    }
                });

            });

            $(document).on('change', '.replacement-dropdown', function () {
                $('#modal_dateofpurchase').val('');
                $('#modal_qty').val('');
                $('#modal_remarks').val('');
                $('#modal_cost').val('');

                var id = $(this).data('id');
                var type = $('#replace' + id).val();
                var quantity = $('#quantity' + id).val(); // Get the quantity value

                    if (type == 'replacement') {

                        if (quantity === '' || quantity === null) {
                            // Throw an alert if quantity is empty
                            
                                alert('Please enter a quantity before selecting replacement type.');
                                $('#replace' + id).val('').trigger('change');
                                return;
                        }
                        else
                        {
                            $('#replacementModal').modal({
                                backdrop: 'static',
                                keyboard: false
                            });

                            $('#row_id').val(id);
                            $('#modal_qty').val(quantity); // Set quantity value in modal
                            $('#modal_qty').attr('readonly', true);
                            $('#replacementModal').modal('show');
                        }
                }
            });


            // $(document).on('change', '.replacement-dropdown', function () {

            //  $('#modal_dateofpurchase').val('');
            //  $('#modal_qty').val('');
            //  $('#modal_remarks').val('');
            //  $('#modal_cost').val(''); 

            //     var id = $(this).data('id');
            //     var type = $('#replace'+id).val();

            //     var quantity = $('#quantity'+id).val();

            //     if(type == 'replacement')
            //     {
            //         $('#replacementModal').modal({
            //             backdrop: 'static',
            //             keyboard: false
            //         });

            //         $('#row_id').val(id);
            //         $('#modal_qty').val(appendQty);
            //         $('#modal_qty').attr('readonly',true);
            //         $('#replacementModal').modal('show');
            //     }
            // });

            // Save modal data and update the hidden fields in the corresponding row
            $(document).on('click', '#saveReplacement', function () {
                var row_id = $('#row_id').val();  // Retrieve the stored row ID from the modal

                // Extract values from modal inputs
                var dateOfPurchase = $('#modal_dateofpurchase').val();
                var qty = $('#modal_qty').val();
                var remarks = $('#modal_remarks').val();
                var cost = $('#modal_cost').val();

                // Check for empty fields and show alert for each individually
                if (row_id === '' || row_id === null) {
                    alert('Row ID is missing. Please try again.');
                    return;
                }
                if (dateOfPurchase === '' || dateOfPurchase === null) {
                    alert('Please enter the date of purchase.');
                    return;
                }
                if (qty === '' || qty === null) {
                    alert('Please enter the quantity.');
                    return;
                }
                if (remarks === '' || remarks === null) {
                    alert('Please enter remarks.');
                    return;
                }
                if (cost === '' || cost === null) {
                    alert('Please enter the cost.');
                    return;
                }

                // If all fields are filled, update the hidden fields in the corresponding row
                $('#rdateofpurchase' + row_id).val(dateOfPurchase);  // Update hidden field
                $('#rqty' + row_id).val(qty);  // Update hidden field
                $('#rremarks' + row_id).val(remarks);  // Update hidden field
                $('#rcost' + row_id).val(cost);  // Update hidden field

                // Close the modal after saving
                $('#replacementModal').modal('hide');
            });


            // Save modal data and update the hidden fields in the corresponding row
            // $(document).on('click', '#saveReplacement', function () {
            //     var row_id = $('#row_id').val();  // Retrieve the stored row ID from the modal

            //     // Extract values from modal inputs
            //     var dateOfPurchase = $('#modal_dateofpurchase').val();
            //     var qty = $('#modal_qty').val();
            //     var remarks = $('#modal_remarks').val();
            //     var cost = $('#modal_cost').val();

            //     // Update the hidden fields in the corresponding row
            //     // $('#replace' + row_id).val('replacement');  // Set replacement dropdown value
            //     $('#rdateofpurchase' + row_id).val(dateOfPurchase);  // Update hidden field
            //     $('#rqty' + row_id).val(qty);  // Update hidden field
            //     $('#rremarks' + row_id).val(remarks);  // Update hidden field
            //     $('#rcost' + row_id).val(cost);  // Update hidden field

            //     // Close the modal after saving
            //     $('#replacementModal').modal('hide');
            // });


            
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

                var request_type = $('#request_category').val();
                $('#MaterialGroup'+row_no).val(MaterialGroup);

              // request type document no set 
                var request_type_code = (request_type == 'Asset purchases') ? 'ZCAP' : ((request_type == 'Material purchases') ? 'ZNB' : 'ZSER');

                $.ajax({
                    url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
                    type: "POST",
                    data: {
                        Storage_Location: Storage_Location,
                        Plant : Plant,
                        MaterialGroup : MaterialGroup,
                        request_type_code : request_type_code
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
                var row_no = $('#trow_no').val();
                if(row_no == 0) {
                    $('#trow_no').val(1);
                    $('#save').attr('disabled',false);
                }

                var request_type = $('#request_category').val();
                let style = (request_type != 'Asset purchases') ? 'display:none !important;' : '';   
                let required_class = (request_type == 'Asset purchases') ? 'required_for_valid' : '';

                var modal = `<div class="row">
                <div id="modalCustom" class="col-lg-12 layout-spacing">
                    <div class="statbox widget box box-shadow">
                            <div class="modal fade inputForm-modal" id="inputFormModal${row_no}" tabindex="-1" role="dialog" aria-labelledby="inputFormModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" id="inputFormModalLabel">
                                            <h5 class="modal-title">Remarks For <b>SendBack</b></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                                        </div>
                                        <div class="modal-body">
                                                <div class="form-group">
                                                    <div class="input-group mb-3">
                                                        <textarea class="form-control" name="budget_remark[]" aria-label="With textarea"></textarea>
                                                    </div>
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger mt-2 mb-2 btn-no-effect" data-bs-dismiss="modal">Close</button>
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
                                    <select class="select2 form-control items-dropdown required_for_valid" id="item-dropdown${row_no}" style="width: 175px;" name="item_code[]" placeholder="Select Name." data-id="${row_no}" error-msg='Item code is mandatory.'>
                                    </select>
                                </div>
                                <span class="error_msg text-danger"></span>
                                <span class="badge bg-info text-white text-center p-1 mt-2 item_code_info" style="display:none;    font-size: 13px;"></span>
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
                                    <input type="number" min="0" class="form-control required_for_valid quantity" id="quantity${row_no}" style="width: 125px;" name="quantity[]"  placeholder="Enter Quantity" required="" data-id="${row_no}" error-msg='Quantity is mandatory.'>
                                </div>
                                <span class="error_msg text-danger"></span>
                            </td>

                            <td class="replacement_feature" style="${style}">
                                <div class="col-md-12">
                                
                                    <select class="select2 form-control ${required_class} replacement-dropdown" id="replace${row_no}" data-id="${row_no}"  name="replace[]" style="width: 145px;" disabled>
                                        <option value="">Select</option>
                                        <option value="new">New</option>
                                        <option value="replacement">Replacement</option>
                                    </select>

                                    <input type="hidden" id="rdateofpurchase${row_no}" name="rdateofpurchase[]">
                                    <input type="hidden" id="rqty${row_no}" name="rqty[]">
                                    <input type="hidden" id="rremarks${row_no}" name="rremarks[]">
                                    <input type="hidden" id="rcost${row_no}" name="rcost[]">
                                </div>
                                <span class="error_msg text-danger"></span>
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
                            </td>`;

                            if(row_no != 1) {
                                output += `<td>
                                    <i class="fa fa-times remove text-danger"  ></i>
                                </td>`;
                            } 

                output += `</tr>`;


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

                if(row_no == 0) {
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

                        if((items_arr.length == 0 && $(this).val() != '') || (!items_arr.includes($(this).val()) && $(this).val() != '') ) {
                            items_arr.push($(this).val());
                        }   
                  });  

                  // duplicate items validation functionality End

                  var error_count = validation();

                  if(item_validation_error != '') {
                        swal({
                          title: "Error!",
                          text: item_validation_error,
                          icon: "warning",
                        });
                  } else if(error_count == 0) {
                      var formdata = document.querySelector('#request_form');
                      var form = new FormData(formdata);    
                      form.append('Action', 'save_purchase_request_div');

                      $('.file-upload-input').each(function(index){
                        index++;
                        form.append('Attachment'+index , $('#Attachment'+index)[0].files[0]);
                      });

                      $.ajax({
                          url: 'common_ajax.php',
                          type: 'POST',
                          data: form,
                          processData: false,
                          contentType: false,
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

            // END MORE 
        </script>
        <!-- CUSTOM JS END -->
    </body>
</html>
