<?php
include('../auto_load.php');
include('adition.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
$FR = $_GET['Rate_id'];
$qry = sqlsrv_query($conn, "SELECT  count(*) AS Request_PID FROM Tb_Payment_Request WHERE  Request_PID like '%$FR%'  ");
$query = sqlsrv_fetch_array($qry);
$payment_id = $query['Request_PID'] ;
$pay = substr($payment_id, 2, 4) + 1;
$nextid = substr($payment_id, 0, 4);
$result = str_pad($pay, 6, $nextid, STR_PAD_LEFT);
// $test = str_pad($FR, strlen($nextid) + 4, "0", STR_PAD_LEFT);
// print_r($pay);
print_r($result);
if(!isset($_SESSION['EmpID']))
{
    ?>
    <script type="text/javascript">
        window.location = "../pages/index.php";
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

//end

if (isset($_POST["financial_rate"])) {
    //   echo '<pre>';
    // print_r($_POST);die();
    $emp_id = $Employee_Id;
    $Requested_to = $Recommender_Code;
    $Request_Type = $_POST['request_type1'];
    $Request_RTID = $_POST['Request_rtid'];
    $Department = $_POST['departmentr'];
    if(!isset($_POST['subjr'])){
        $Subject="Null";
        }else{
          $Subject=$_POST['subjr'];
        }
    $Request_Date = $_POST['request_dater'];
    $Requester = $_POST['requesterr'];
    $Budjected = $_POST['budgetr'];
    $Approvel_Type = $_POST['approval_typer'];
    $Recommender = $_POST['recommenderr'];
    $Approver = $_POST['approverr'];
    $Mail_Subject = $_POST['mail_subjr'];
    $Mail_Body = $_POST['mail_bodyr'];
    $Remark = $_POST['remarksr'];
    $fil = $_FILES["attachementsr"]["name"];
    // $Persion = $_POST['persionr'];

    if (isset($_POST['persionr']))
    {
        $Persion= implode(',',$_POST['persionr']);
        $To_Address= implode(',',$_POST['tor']);
        $Cc= implode(',',$_POST['ccr']);

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
    $cc = explode(',', $Cc);
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
                            <th class="text-center">Request_PID</th>
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
                                ' . $Request_PID . '
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



    $tmp_name = $_FILES["attachementsr"]["tmp_name"];
		$path = "file/" . $fil;
		$file1 = explode(".", $fil);
		$ext = $file1[1];
		$allowed = array("jpg", "png", "gif", "pdf", "wmv", "pdf", "zip");
		if (!$mail->send()) {
      echo 'Message could not be sent.';
      echo 'Mailer Error: ' . $mail->ErrorInfo;
    }elseif (in_array($ext, $allowed)) {
      move_uploaded_file($tmp_name, $path);


		sqlsrv_query($conn, "INSERT INTO Tb_Payment_Request
        (Request_Type,Request_PID,Depatment,Subject,Request_Date,Requester,Budjected,Approvel_Type,To_Address,Cc,Recommender,Approver,
        Mail_Subject,Mail_Body,Attachement,Remark,Persion_In_Workflow,EMP_ID,Requested_to,Time_Log,Status)
        VALUES('$Request_Type','$Request_RTID','$Department','$Subject','$Request_Date','$Requester','$Budjected','$Approvel_Type','$To_Address','$Cc','$Recommender','$Approver','$Mail_Subject',
       '$Mail_Body','$fil','$Remark','$Persion','$emp_id','$Requested_to',GETDATE(),'Requested')");
   
			?>
            <script type="text/javascript">
            alert("Request added successsfully");
            window.location = "show_rateterms_request.php";
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

    
    <body >

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
                                             <li class="breadcrumb-item"><a href="show_rateterms_request.php">Show Rate & Terms Request</a></li>
                                             <li class="breadcrumb-item active">Rate & Terms Request</li>
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
    
                                            <form  method="POST" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-md-1" style="width: 175px;"> 
                                                        <div class="mb-3">
                                                            <label for="validationCustom01" class="form-label">RequestID</label>
                                                            <input type="text" class="form-control" id="validationCustom01" name="Request_pid"
                                                                value="<?php echo $result ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="hasta" class="form-label">Request Date</label>
                                                            <input type="date" class="form-control" id="hasta" name="request_dater" readonly
                                                                value="<?php echo date('Y-m-d'); ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="Requester" class="form-label">Requester</label>
                                                            <input type="text" class="form-control" id="Requester" readonly name="requesterr" value='<?php echo $selector_arr['Employee_Name']; ?>'>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label">Approval Type</label>
                                                            <select class="form-control" readonly name="approval_typer" id="app">
                                                                <option value="">Select</option>
                                                            </select>
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
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="bud" class="form-label">Budgeted</label>
                                                            <select class="form-select" name="budgetr" id="bud">
                                                                <option value="select">Select</option>
                                                                <option value="Yes">Yes</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3" id="remark">
                                                            <label for="red" class="form-label">Remarks</label>
                                                                <textarea class="form-control" rows="2" name="remarksr" id="red"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-text-input" class="col-sm-2 col-form-label">To</label>
                                                    <div class="col-sm-10">
                                                    <?php
                                                        $selectorq = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$recommender_to' ");
                                                        $selector_arrq = sqlsrv_fetch_array($selectorq);
                                                    ?>
                                                        <input class="form-control" name="tor[]" value="<?php echo $selector_arrq['Office_Email_Address'] ?>" type="text" implode multiple data-role="tagsinput">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-search-input" class="col-sm-2 col-form-label">Cc</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" name="ccr[]" type="text" value="<?php echo $selector_arr['Office_Email_Address'] ?>"  implode multiple data-role="tagsinput">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="Recommender" class="col-sm-2 col-form-label">Recommender</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="recommenderr" value="<?php echo $selector_arrq['Employee_Name'] ?>" id="Recommender">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="Approver" class="col-sm-2 col-form-label">Approver</label>
                                                    <div class="col-sm-10">
                                                    <?php
                                                        $selectorq2 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$approver_name' ");
                                                        $selector_arrq2 = sqlsrv_fetch_array($selectorq2);
                                                    ?>
                                                        <input class="form-control" type="text"  name="approverr" value="<?php echo $selector_arrq2['Employee_Name'] ?>" id="Approver">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="subj" class="col-sm-2 col-form-label">Mail Subject</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="mail_subjr" id="subj">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-password-input" class="col-sm-2 col-form-label">Mail Body</label>
                                                    <div class="col-sm-10">
                                                        <textarea id="elm1" name="mail_bodyr"></textarea>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-number-input" class="col-sm-2 col-form-label">Attachements</label>
                                                    <div class="col-sm-10">
                                                        <input type="file" class="form-control" name="attachementsr" multiple id="customFile">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-datetime-local-input" class="col-sm-2 col-form-label">Informer Details</label>
                                                    <div class="col-sm-10">
                                                        <select class="select2 form-control select2-multiple" name="persionr[]"  implode multiple data-placeholder="Choose ...">
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
                var Request_Type = $('#request').val();  
                $.ajax({
                url: "fetch-type.php",
                type: "POST",
                data: {
                    Request_Type: Request_Type
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
