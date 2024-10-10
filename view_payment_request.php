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
$payment_id = $_GET['pay_id'];
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
                                             <li class="breadcrumb-item"><a href="show_payment_request.php">Show Payment Request</a></li>
                                             <li class="breadcrumb-item active">View Payment Request</li>
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
                                                $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Payment_Request WHERE Request_PID = '$payment_id'");
                                                $updated_query = sqlsrv_fetch_array($update_qry);
                                                $PERSION =  $updated_query['Persion_In_Workflow'];
                                            ?>
                                            <form  method="POST" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-md-1" style="width: 110px;"> 
                                                        <div class="mb-3">
                                                            <label for="validationCustom01" class="form-label">RequestID</label>
                                                            <input type="text" class="form-control" id="validationCustom01" name="Request_pid"
                                                                value="<?php echo $updated_query['Request_PID'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 150px;">
                                                        <div class="mb-3">
                                                            <label for="hasta" class="form-label">Request Date</label>
                                                            <input type="date" class="form-control" id="hasta" name="request_datef" value="<?php echo $updated_query['Request_Date'] ?>" readonly >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 190px;">
                                                        <div class="mb-3">
                                                            <label for="Requester" class="form-label">Requester</label>
                                                            <input type="text" class="form-control" id="Requester" readonly name="requesterf" value="<?php echo $updated_query['Requester'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-1" style="width: 214px;">
                                                        <div class="mb-3">
                                                            <input type="hidden" class="form-control" id="request" name="request_type1" value="Financial - Payment requests (FP)">
                                                            <label for="department" class="form-label">Department</label>
                                                            <input type="text" class="form-control" readonly name="requesterf" value="<?php echo $updated_query['Depatment'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 165px;">
                                                        <div class="mb-3">
                                                            <label for="subject" class="form-label">Subject</label>
                                                            <input type="text" class="form-control" id="Requester" readonly name="requesterf" value="<?php echo $updated_query['Subject'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 80px;">
                                                        <div class="mb-3">
                                                            <label for="bud" class="form-label">Budgeted</label>
                                                            <input type="text" class="form-control" id="Requester" readonly name="requesterf" value="<?php echo $updated_query['Budjected'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 161px;">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label">Approval Type</label>
                                                            <input type="text" class="form-control"  readonly name="requesterf" value="<?php echo $updated_query['Approvel_Type'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3" id="remark">
                                                            <label for="red" class="form-label">Remarks</label>
                                                                <textarea class="form-control" readonly rows="1" name="remarksf" id="red"><?php echo $updated_query['Remark'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-text-input" class="col-sm-2 col-form-label">To</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" name="tof[]" value="<?php echo $updated_query['To_Address'] ?>" readonly type="text">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-search-input" class="col-sm-2 col-form-label">Cc</label>
                                                    <div class="col-sm-10">
                                                        <?php 
                                                                $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
                                                                $idss = array();
                                                                while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
                                                                    $idss[] = $ids['Office_Email_Address'];
                                                                }
                                                                $implode = implode(',', $idss);
                                                                // print_r($implode);
                                                                ?>
                                                                
                                                        <input class="form-control" name="ccf[]" type="text" value="<?php echo $updated_query['Cc'] ?>,<?php echo $implode ?>" readonly >
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="Recommender" class="col-sm-2 col-form-label">Recommender</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="recommenderf"  value="<?php echo $updated_query['Recommender'] ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="Approver" class="col-sm-2 col-form-label">Approver</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text"  name="approverf" value="<?php echo $updated_query['Approver'] ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="subj" class="col-sm-2 col-form-label">Mail Subject</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="mail_subjf" value="<?php echo $updated_query['Mail_Subject'] ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-password-input" class="col-sm-2 col-form-label">Mail Body</label>
                                                    <div class="col-sm-10">
                                                    <textarea class="form-control" rows="15" name="remarksf" id="elm2" ><?php echo $updated_query['Mail_Body'] ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-password-input" class="col-sm-2 col-form-label">Amount</label>
                                                    <div class="col-sm-2">
                                                        <input class="form-control" type="text" readonly value="<?php echo $updated_query['Amount'] ?>">
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input class="form-control" type="text" readonly value="<?php echo $updated_query['Amount_In_Words'] ?>">
                                                    </div>
                                                </div>
                                                <?php 
                                                    if($updated_query['Attachement'] == ''){
                                                        ?>
                                                        <?php

                                                    }else{
                                                        ?>
                                                        <div class="row mb-3">
                                                        <label for="example-number-input" class="col-sm-2 col-form-label">Attachements</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control" name="attachements" value="<?php echo $updated_query['Attachement'] ?>"
                                                             readonly data-bs-toggle="modal" data-bs-target=".bs-example-modal-center">
                                                             <div class="modal fade bs-example-modal-center" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h5 class="modal-title mt-0">Attachment</h5>
                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                                        
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                <iframe src="file/<?php echo $updated_query['Attachement'] ?>"
                                                                                style="width: 100%;height: auto;" frameborder="0"></iframe>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                                </div>
                                                                            </div><!-- /.modal-content -->
                                                                        </div><!-- /.modal-dialog -->
                                                                    </div><!-- /.modal -->
                                                        </div>
                                                    </div>
                                                    <?php
                                                    }
                                                ?>
                                                <div class="row mb-3">
                                                    <label for="example-datetime-local-input" class="col-sm-2 col-form-label">Informer Details</label>
                                                    <div class="col-sm-10">
                                                        <?php
                                                            $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',',')) ");
                                                            $idss = array();
                                                            while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
                                                                $idss[] = $ids['Employee_Name'];
                                                            }
                                                            $implode = implode(',', $idss);
                                                        ?>
                                                        <input type="text" class="form-control" name="informer" value="<?php echo $implode ?>" readonly id="customFile">
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

        <script>
            $(document).ready(function(){0<$("#elm2").length&&tinymce.init({readonly : 1,
                    menubar: false,statusbar: false,
                    toolbar: false,selector:"textarea#elm2",height:300,
                    plugins:["advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                    "save table contextmenu directionality emoticons template paste textcolor"],
                    style_formats:[
                        {title:"Bold text",inline:"b"},
                        {title:"Red text",inline:"span",styles:{color:"#ff0000"}},
                        {title:"Red header",block:"h1",styles:{color:"#ff0000"}},
                        {title:"Example 1",inline:"span",classes:"example1"},
                        {title:"Example 2",inline:"span",classes:"example2"},
                        {title:"Table styles"},
                        {title:"Table row 1",selector:"tr",classes:"tablerow1"}
                    ]})});

        </script>
        <!-- ADD END -->
    </body>
</html>
