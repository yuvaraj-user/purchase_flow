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
        window.location = "../pages/index.php";
    </script>
    <?php
}
$rate_id = $_GET['pay_id'];
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

//end

if (isset($_POST["financial_rate"])) {
    //   echo '<pre>';
    // print_r($_POST);die();
    $emp_id = $Employee_Id;
    $Requested_to = $Recommender_Code;
    $Request_Type = $_POST['request_type1'];
    $Request_RTID = $_POST['Request_rtid'];
    $Department = $_POST['departmentr'];
    $Subject = $_POST['subjr'];
    $Request_Date = $_POST['request_dater'];
    $Requester = $_POST['requesterr'];
    $Budjected = $_POST['budgetr'];
    $Approvel_Type = $_POST['approval_typer'];
    $Recommender = $_POST['recommenderr'];
    $Approver = $_POST['approverr'];
    $Mail_Subject = $_POST['mail_subjr'];
    $Mail_Body = $_POST['mail_bodyr'];
    $Remark = $_POST['remarksr'];
    if(!isset($_POST['attachementsr'])){
        $fil="";
        }else{
          $fil=$_POST['attachementsr'];
        }
    // $fil = $_POST["attachementsr"];
    // $Persion = $_POST['persionr'];

    if (isset($_POST['persionr']))
    {
        $Persion= implode(',',$_POST['persionr']);
        $To_Address= implode(',',$_POST['tor']);
        $Cc= implode(',',$_POST['ccr']);

        $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_RateTrems_Request WHERE Request_RTID = '$rate_id'");
        $updated_query = sqlsrv_fetch_array($update_qry);
        $PERSION =  $updated_query['Persion_In_Workflow'];
    
        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
        $idss = array();
        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
    }else{
        $Persion= '';
        $To_Address= implode(',',$_POST['tor']);
        $Cc= implode(',',$_POST['ccr']);

        $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_RateTrems_Request WHERE Request_RTID = '$rate_id'");
        $updated_query = sqlsrv_fetch_array($update_qry);
        $PERSION =  $updated_query['Persion_In_Workflow'];
    
        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
        $idss = array();
        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
    }

    //Create an instance of PHPMailer class
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

    $to = explode(',', $To_Address);

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

    $mail->addAttachment($fil);         // Add attachments
    // $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $Mail_Subject;
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
                            <th class="text-center">Request_RTID</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Subject</th>
                            <th class="text-center">Request_Date</th>
                            <th class="text-center">Requester</th>
                            <th class="text-center">Budjected</th>
                            <th class="text-center">Approvel_Type</th>
                            <th class="text-center">Recommender</th>
                            <th class="text-center">Approver</th>
                            <th class="text-center">Mail_Body</th>
                            <th class="text-center">Attachement</th>
                            <th class="text-center">Remark</th>                           
                            <th class="text-center">Status</th>                           
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                1
                            </td>
                            <td>
                                ' . $Request_RTID . '
                            </td>
                            <td>
                                ' . $Department . '
                            </td>
                            <td>
                                ' . $Subject . '
                            </td>
                            <td>
                                ' . $Request_Date . '
                            </td>
                            <td>
                                ' . $Requester . '
                            </td>
                            <td>
                                ' . $Budjected . '
                            </td>
                            <td>
                                ' . $Approvel_Type . '
                            </td>
                            <td>
                                ' . $Recommender . '
                            </td>
                            <td>
                                ' . $Approver . '
                            </td>
                            <td>
                                ' . $Mail_Body . '
                            </td>
                            <td>
                                ' . $fil . '
                            </td>
                            <td>
                                ' . $Remark . '
                            </td>
                            <td>
                            <h4><span class="badge badge-success"><i class="fa fa-check"></i>Requested </span></h4>
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
    }else {

         sqlsrv_query($conn, "UPDATE Tb_RateTrems_Request SET Request_Type = '$Request_Type',Request_RTID = '$Request_RTID',Depatment = '$Department',Subject = '$Subject',Request_Date = '$Request_Date',
         Requester = '$Requester',Budjected = '$Budjected',Approvel_Type = '$Approvel_Type',To_Address = '$To_Address',Cc = '$Cc',Recommender = '$Recommender',
         Approver = '$Approver',Mail_Subject = '$Mail_Subject',Mail_Body = '$Mail_Body',Attachement = '$fil',Remark = '$Remark',Persion_In_Workflow = '$Persion',
         EMP_ID = '$emp_id',Requested_to = '$Requested_to',Time_Log = GETDATE(),Status = 'Requested' WHERE Request_RTID ='".$rate_id."'");
         
			?>
            <script type="text/javascript">
            alert("Update successsfully");
            window.location = "show_rateterms_request.php";
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
                                             <li class="breadcrumb-item"><a href="show_rateterms_request.php">Show Rate And Terms Request</a></li>
                                             <li class="breadcrumb-item active">Rate And Terms Request</li>
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
                                                $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_RateTrems_Request WHERE Request_RTID = '$rate_id'");
                                                $updated_query = sqlsrv_fetch_array($update_qry);
                                            ?>
                                            <form  method="POST" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-md-1" style="width: 110px;"> 
                                                        <div class="mb-3">
                                                            <label for="validationCustom01" class="form-label">RequestID</label>
                                                            <input type="text" class="form-control" id="validationCustom01" name="Request_rtid"
                                                                value="<?php echo $updated_query['Request_RTID'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 150px;">
                                                        <div class="mb-3">
                                                            <label for="hasta" class="form-label">Request Date</label>
                                                            <input type="date" class="form-control" id="hasta" name="request_dater" 
                                                                value="<?php echo $updated_query['Request_Date'] ?>"readonly >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="Requester" class="form-label">Requester</label>
                                                            <input type="text" class="form-control" id="Requester" readonly name="requesterr" value='<?php echo $updated_query['Requester']; ?>'>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <input type="hidden" class="form-control" id="request" name="request_type1" value="Financial - Rate and terms request (FR)">
                                                            <label for="department" class="form-label">Department</label>
                                                            <select class="form-select" id="department" name="departmentr" >
                                                            
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="subject" class="form-label">Subject</label>
                                                            <select class="form-select" id="subject" name="subjr">
                                                            
                                                            </select>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-1" style="width: 110px;">
                                                        <div class="mb-3">
                                                            <label for="bud" class="form-label">Budgeted</label>
                                                            <select class="form-select" name="budgetr" id="bud">
                                                                <option value="select">Select</option>

                                                                <option <?php if ($updated_query['Budjected'] == 'Yes') { ?> selected="selected" <?php } ?> value="Yes">Yes</option>
                                                                <option <?php if ($updated_query['Budjected'] == 'No') { ?> selected="selected" <?php } ?> value="No">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label">Approval Type</label>
                                                            <select class="form-control" readonly name="approval_typer" id="app">
                                                                <option value="">Select</option>
                                                                <option <?php if ($updated_query['Approvel_Type'] == $updated_query['Approvel_Type']) { ?> selected="selected" <?php } ?> value='<?php echo $updated_query['Approvel_Type']; ?>'>
                                                                <?php echo $updated_query['Approvel_Type'] ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3" id="remark">
                                                            <label for="red" class="form-label">Remarks</label>
                                                                <textarea class="form-control" rows="1" name="remarksr" id="red"> <?php echo $updated_query['Remark'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-text-input" class="col-sm-2 col-form-label">To</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" name="tor[]" value="<?php echo $updated_query['To_Address'] ?>" type="text" implode multiple data-role="tagsinput">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-search-input" class="col-sm-2 col-form-label">Cc</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" name="ccr[]" type="text" value="<?php echo $updated_query['Cc'] ?>"  implode multiple data-role="tagsinput">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="Recommender" class="col-sm-2 col-form-label">Recommender</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="recommenderr" value="<?php echo $updated_query['Recommender'] ?>" id="Recommender">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="Approver" class="col-sm-2 col-form-label">Approver</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text"  name="approverr" value="<?php echo $updated_query['Approver'] ?>" id="Approver">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="subj" class="col-sm-2 col-form-label">Mail Subject</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="mail_subjr" value="<?php echo $updated_query['Mail_Subject'] ?>" id="subj">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-password-input" class="col-sm-2 col-form-label">Mail Body</label>
                                                    <div class="col-sm-10">
                                                        <textarea id="elm1" name="mail_bodyr"><?php echo $updated_query['Mail_Body'] ?></textarea>
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
                                                            <input type="text" class="form-control" name="attachementsr" value="<?php echo $updated_query['Attachement'] ?>"
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
                                                        <select class="select2 form-control select2-multiple" name="persionr[]"  implode multiple data-placeholder="Choose ...">
                                                            
                                                        
                                                            <?php
                                                                $HR_Master_Table = sqlsrv_query($conn, "Select * from HR_Master_Table ");
                                                                while ($HR = sqlsrv_fetch_array($HR_Master_Table)) {
                                                                    $res = explode(',',$updated_query['Persion_In_Workflow']);
                                                                ?>

                                                                <?php 
                                                                if($HR['Employee_Code'] == ''){
                                                                    ?>
                                                                    <option value="<?php echo $HR['Employee_Code'] ?>">
                                                                        <?php echo $HR['Employee_Name'] ?>&nbsp;-&nbsp;<?php echo $HR['Employee_Code'] ?>
                                                                    </option>
                                                                    <?php
                                                                }else{
                                                                    ?>
                                                                    <option  <?= (in_array($HR['Employee_Code'],$res)) ? 'selected' : ''?> value="<?php echo $HR['Employee_Code'] ?>">
                                                                        <?php echo $HR['Employee_Name'] ?>&nbsp;-&nbsp;<?php echo $HR['Employee_Code'] ?>
                                                                    </option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            <?php                                                                         }
                                                                     ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-0">
                                                    <center>
                                                    <div>
                                                        <button name="financial_rate" id="financial_rate" type="submit" class="btn btn-primary waves-effect waves-light me-1">
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
            var i = 1;
            $(document).ready(function(){ 
                
                var Request_dept = $('#request').val();  
                $.ajax({
                url: "fetch-type-id.php?id=<?php echo $rate_id ?>",
                type: "POST",
                data: {
                    Request_dept: Request_dept
                },
                cache: false,
                success: function (result) {
                    $("#department").html(result);
                }
                });
                $('#department').on('change', function () {
                    var Department = this.value;
                    $.ajax({
                    url: "fetch-subject.php",
                    type: "POST",
                    data: {
                        Department: Department
                    },
                    cache: false,
                    success: function (result) {
                        $("#subject").html(result);

                    }
                    });
                });
                <?php
                    $update_qry1 =  sqlsrv_query($conn, "SELECT * FROM Tb_RateTrems_Request WHERE Request_RTID = '$rate_id'");
                    $updated_query1 = sqlsrv_fetch_array($update_qry1);
                    $dept = $updated_query1['Depatment'];
                ?>
                var Departmented = '<?php echo $dept ?>'; 
                    $.ajax({
                    url: "fetch-subject-id.php?id=<?php echo $rate_id ?>",
                    type: "POST",
                    data: {
                        Departmented: Departmented
                    },
                    cache: false,
                    success: function (result) {
                        $("#subject").html(result);
                    }
                });
                $("#subject").on("keyup click", function (e) {
                    $("#subj").val($(this).val());
                });
                $("#bud").change(function () {
                    var val = $(this).val();
                    if (val == "Yes") {
                    $("#app").html("<option value='Budget Approval'>Budget Approval</option>").attr("readonly", true);
                    } else if (val == "No") {
                    $("#app").html("<option value='Special Approval'>Special Approval</option>").attr("readonly", true);
                    } else if (val == "select") {
                    $("#app").html("<option value=''>Select</option>").attr("readonly", true);
                    }
                });

            });
            $(function () {
            $('#remark').hide();
            $('#bud').change(function () {
                if ($('#bud').val() == 'Yes') {
                $('#remark').hide();
                document.getElementById("red").required = false;
                } else {
                $('#remark').hide();
                // $('#no_select').hide();
                }
            });
            });
            $(function () {
            $('#remark').hide();
            $('#bud').change(function () {
                if ($('#bud').val() == 'No') {
                $('#remark').show();
                document.getElementById("red").required = true;
                } else {
                $('#remark').hide();
                // $('#no_select').hide();
                }
            });
            });
        </script>
        <!-- CUSTOM JS END -->
    </body>
</html>