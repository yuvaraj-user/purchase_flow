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
$purchase_is = $_GET['purchase_id'];

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
                                             <li class="breadcrumb-item active">Update Purchase Request</li>
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
                                                $update_qry =  sqlsrv_query($conn, "SELECT *,Tb_Request_Items.MaterialGroup FROM Tb_Request 
                                                LEFT JOIN (SELECT TOP 1 * FROM Tb_Request_Items WHERE Tb_Request_Items.Request_ID = '$purchase_is') as Tb_Request_Items on Tb_Request_Items.Request_ID = Tb_Request.Request_ID 
                                                    WHERE Tb_Request.Request_ID = '$purchase_is'");
                                                $updated_query = sqlsrv_fetch_array($update_qry);
                                                $PERSION =  $updated_query['Persion_In_Workflow'];
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
                                                             <input type="text" class="form-control" name="department" id="request_type" readonly
                                                                value="<?php echo $updated_query['Request_Type'] ?>" >                                                    
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label">Plant</label>
                                                            <input type="text" class="form-control" name="department" id="plant" readonly
                                                                value="<?php echo $updated_query['Plant'] ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" style="<?php if($updated_query['Request_Type'] == 'Services'){ ?> display: none; <?php } ?>">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label">Storage Location</label>
                                                             <input type="text" class="form-control"  readonly
                                                                value="<?php echo $updated_query['Storage_Location'] ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label">Material group</label>
                                                             <input type="text" class="form-control"  id="mat_group" readonly
                                                                value="<?php echo $updated_query['MaterialGroup'] ?>" >
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
                                                                    <th>Expected Date</th>
                                                                    <th>Specification </th>
                                                                     <th>
                                                                    <?php 
                                                                    $sql =  sqlsrv_query($conn,"SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items
                                                                    ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID WHERE Tb_Request.Request_ID = '$purchase_is'");
                                                                    $row = sqlsrv_fetch_array($sql);
                                                                    ?>
                                                                    <?php
                                                                    if($row['Attachment'] == ''){

                                                                    }else{
                                                                        echo'Attachment';
                                                                        
                                                                    }
                                                                    ?>
                                                                    </th>
                                                                    <th>Whether Budgeted</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="selected" class="tbody tbody1">
                                                             <?php
                                                                    $i = 1;
                                                                    $sql = "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items
                                                                    ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID WHERE Tb_Request.Request_ID = '$purchase_is'";
                                                                    $params = array();
                                                                    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
                                                                    $stmt = sqlsrv_prepare($conn, $sql, $params, $options);
                                                                    sqlsrv_execute($stmt);
                                                                    while ($row = sqlsrv_fetch_array($stmt)) {
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                       <?php echo $i++ ?>
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Item_Code'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Description'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['UOM'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['MaterialGroup'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Quantity'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Expected_Date'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Specification'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <?php 
                                                                    if($row['Attachment'] == ''){

                                                                    }else{
                                                                        ?>
                                                                        <input type="text" class="form-control" readonly data-bs-toggle="modal" 
                                                                    data-bs-target=".bs-example-modal-center<?php echo $row['ID'] ?>" value="<?php echo $row['Attachment'] ?>" >
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                    
                                                            
                                                                    </td>
                                                                        <div class="modal fade bs-example-modal-center<?php echo $row['ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
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
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Budget'] ?>" >
                                                                    </td>
                                                                    
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
             $(document).ready(function () {     
                var Plant         = $('#plant').val();
                var MaterialGroup = $('#mat_group').val();

                get_involved_persons(Plant,MaterialGroup); 
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
            
        </script>
        <!-- CUSTOM JS END -->
    </body>
</html>
