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
$Requester = $selector_arr1['PO_creator_Release_Codes'];
// echo $Requester;
// echo $Recommender_Code;

//end

if (isset($_POST["financial_payment"])) {
    //   echo '<pre>';
    //  print_r($_POST);die();
    $emp_id = $Employee_Id;
    $Requested_to = $Recommender_Code;
    $Request_Type = $_POST['request_type1'];
    $Request_PID = $_POST['Request_pid'];
    $Department = $_POST['departmentf'];
    if(!isset($_POST['subjf'])){
        $Subject="";
        }else{
          $Subject=$_POST['subjf'];
        }
    $Request_Date = $_POST['request_datef'];
    $Requester = $_POST['requesterf'];
    $Budjected = $_POST['budgetf'];
    if(!isset($_POST['approval_typef'])){
        $Approvel_Type="";
        }else{
          $Approvel_Type=$_POST['approval_typef'];
        }
    $Recommender = $_POST['recommenderf'];
    $Approver = $_POST['approverf'];
    $Mail_Subject = $_POST['mail_subjf'];
    $Mail_Body = $_POST['mail_bodyf'];
    $Remark = $_POST['remarksf'];
    $Amount = $_POST['Amount'];
    $Amount_In_Words = $_POST['Amount_In_Words'];
    $fil = $_FILES["attachementsf"]["name"];

    if(!isset($_POST['persionf'])){
        $Subject123="0";
        }else{
          $Subject123=$_POST['persionf'];
        }
    if($Subject123>0){
    
        $Persion= implode(',',$_POST['persionf']);
        $To_Address= implode(',',$_POST['tof']);
        $Cc= implode(',',$_POST['ccf']);

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$Persion',','))  ");
        $idss = array();
        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
    }else{

        // echo "Hai123";
        $To_Address= implode(',',$_POST['tof']);
        $Cc= implode(',',$_POST['ccf']);
        $implode='';
        $Persion='';
    }
   
    $selector1d = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
    WHERE PO_creator_Release_Codes = '$Employee_Id' ");
    $selector_arr1d = sqlsrv_fetch_array($selector1d);
    $statuwss = $selector_arr1d['PO_creator_Release_Codes']; 
    // print_r($statuwss);
    if($statuwss == $Recommender_Code) {
        $status = 'Recommended';
    }else{
        $status = 'Requested'; 
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
    // $mail->addBCC('softwaredeveloper@mazenetsolution.com');

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
                            <h4><span class="badge badge-success"><i class="fa fa-check"></i>' . $status . ' </span></h4>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </body>
  </html>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';



    $tmp_name = $_FILES["attachementsf"]["tmp_name"];
		$path = "file/" . $fil;
		$file1 = explode(".", $fil);
        if( !isset($file1[1]) ){
            $file1[1]=0;
         }
		$ext = $file1[1];

		$allowed = array("jpg", "png", "gif", "pdf", "wmv", "pdf", "zip");
		if (!$mail->send()) {
      echo 'Message could not be sent.';
      echo 'Mailer Error: ' . $mail->ErrorInfo;
    }elseif (in_array($ext, $allowed)) {
      move_uploaded_file($tmp_name, $path);

      $selector1d = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
      WHERE PO_creator_Release_Codes = '$Employee_Id' ");
      $selector_arr1d = sqlsrv_fetch_array($selector1d);
      $statuwss = $selector_arr1d['PO_creator_Release_Codes']; 
        if($statuwss == $Recommender_Code){
            sqlsrv_query($conn, "INSERT INTO Tb_Payment_Request
            (Request_Type,Request_PID,Depatment,Subject,Request_Date,Requester,Budjected,Approvel_Type,To_Address,Cc,Recommender,Approver,
            Mail_Subject,Mail_Body,Amount,Amount_In_Words,Attachement,Remark,Persion_In_Workflow,EMP_ID,Requested_to,Time_Log,Status)
            VALUES('$Request_Type','$Request_PID','$Department','$Subject','$Request_Date','$Requester','$Budjected','$Approvel_Type','$To_Address','$Cc','$Recommender','$Approver','$Mail_Subject',
        '$Mail_Body','$Amount','$Amount_In_Words','$fil','$Remark','$Persion','$emp_id','$Requested_to',GETDATE(),'$status')");

        sqlsrv_query($conn, "INSERT INTO Tb_Recommender_EV_Remark
        (Request_E_Id,Request_Type,Department,Subject,Request_Date,Requester,Budjected,
        Approvel_Type,Remark,To_Address,Cc,Recommender,Approver,Mail_Subject,Mail_Body,
        Attachment,Persion_In_workflow,Time_Log,Status,EMP_ID)
        VALUES('$Request_PID','$Request_Type','$Department','$Subject','$Request_Date',
        '$Requester','$Budjected','$Approvel_Type','$Remark','$To_Address','$Cc',
        '$Recommender','$Approver','$Mail_Subject','$Mail_Body','$fil','$Persion',
        GETDATE(),'$status','$emp_id')");
        }else{
            sqlsrv_query($conn, "INSERT INTO Tb_Payment_Request
            (Request_Type,Request_PID,Depatment,Subject,Request_Date,Requester,Budjected,Approvel_Type,To_Address,Cc,Recommender,Approver,
            Mail_Subject,Mail_Body,Amount,Amount_In_Words,Attachement,Remark,Persion_In_Workflow,EMP_ID,Requested_to,Time_Log,Status)
            VALUES('$Request_Type','$Request_PID','$Department','$Subject','$Request_Date','$Requester','$Budjected','$Approvel_Type','$To_Address','$Cc','$Recommender','$Approver','$Mail_Subject',
        '$Mail_Body','$Amount','$Amount_In_Words','$fil','$Remark','$Persion','$emp_id','$Requested_to',GETDATE(),'$status')");
        }


//  exit;
			?>
            <script type="text/javascript">
            alert("Request added successsfully");
            window.location = "show_payment_request.php";
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
        <style>

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
                                     <!-- <h4>Payment Request</h4> -->
                                         <ol class="breadcrumb m-0">
                                             <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                             <li class="breadcrumb-item"><a href="show_payment_request.php">Show Payment Request</a></li>
                                             <li class="breadcrumb-item active">Payment Request</li>
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
                                                    <div class="col-md-1" style="width: 110px;"> 
                                                        <div class="mb-3">
                                                            <label for="validationCustom01" class="form-label">RequestID</label>
                                                            <input type="text" class="form-control" id="validationCustom01" name="Request_pid"
                                                                value="<?php echo $_paymentID ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 150px;">
                                                        <div class="mb-3">
                                                            <label for="hasta" class="form-label">Request Date</label>
                                                            <input type="date" class="form-control" id="hasta" name="request_datef" readonly
                                                                value="<?php echo date('Y-m-d'); ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="Requester" class="form-label">Requester</label>
                                                            <input type="text" class="form-control" id="Requester" readonly name="requesterf" value='<?php echo $selector_arr['Employee_Name']; ?>'>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <input type="hidden" class="form-control" id="request" name="request_type1" value="Financial - Payment requests (FP)">
                                                            <label for="department" class="form-label">Department</label>
                                                            <select class="form-select" id="department" name="departmentf" >
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 185px;">
                                                        <div class="mb-3">
                                                            <label for="subject" class="form-label">Subject</label>
                                                            <select class="form-select" id="subject" name="subjf">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 110px;">
                                                        <div class="mb-3">
                                                            <label for="bud" class="form-label">Budgeted</label>
                                                            <select class="form-select" name="budgetf" id="bud">
                                                                <option value="select">Select</option>
                                                                <option value="Yes">Yes</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 160px;">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label">Approval Type</label>
                                                            <select class="form-control" readonly name="approval_typef" id="app">
                                                                <!-- <option value="">Select</option> -->
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3" id="remark">
                                                            <label for="red" class="form-label">Remarks<span style="color:red">*</span></label>
                                                                <textarea class="form-control" rows="1" name="remarksf" id="red"></textarea>
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
                                                        <input class="form-control" name="tof[]" value="<?php echo $selector_arrq['Office_Email_Address'] ?>" type="text" implode multiple data-role="tagsinput">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-search-input" class="col-sm-2 col-form-label">Cc</label>
                                                    <div class="col-sm-10">
                                                    <?php                                                     
                                                        $L1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
                                                            WHERE PO_creator_Release_Codes = '$Requester' ");
                                                        $L1_Manager = sqlsrv_fetch_array($L1);
                                                        $L1_Mail =  $L1_Manager['Recommender'];
                                                        // print_r($L1_Mail);

                                                        $L11 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$Requester' ");
                                                        $L1_Manager1 = sqlsrv_fetch_array($L11);
                                                        $L1_Mail1=  $L1_Manager1['Office_Email_Address'];
                                                        $L1_Mail12=  $L1_Manager1['L1_Manager_Code'];
                                                        // print_r($L1_Mail12);

                                                        $L112 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$L1_Mail12' ");
                                                        $L1_Manager12 = sqlsrv_fetch_array($L112);
                                                        $L1_Mail121=  $L1_Manager12['Office_Email_Address'];
                                                        $L1_Mail1212=  $L1_Manager12['Employee_Code'];
                                                        // print_r($L1_Mail1212);

                                                        if($L1_Mail == $L1_Mail1212){
                                                            ?>
                                                            <input class="form-control" name="ccf[]" type="text" value="<?php echo $selector_arr['Office_Email_Address'] ?>" 
                                                             implode multiple data-role="tagsinput">
                                                            
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <input class="form-control" name="ccf[]" type="text" value="<?php echo $selector_arr['Office_Email_Address'] ?>,
                                                            <?php echo $L1_Manager12['Office_Email_Address'] ?>"  implode multiple data-role="tagsinput">
                                                            <?php
                                                        }
                                                    ?>
                                                        
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="Recommender" class="col-sm-2 col-form-label">Recommender</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="recommenderf" value="<?php echo $selector_arrq['Employee_Name'] ?>" id="Recommender">
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
                                                        <input class="form-control" type="text"  name="approverf" value="<?php echo $selector_arrq2['Employee_Name'] ?>" id="Approver">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="subj" class="col-sm-2 col-form-label">Mail Subject</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="mail_subjf" id="subj">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-password-input" class="col-sm-2 col-form-label">Mail Body</label>
                                                    <div class="col-sm-10">
                                                        <textarea id="elm1" name="mail_bodyf"></textarea>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-number-input" class="col-sm-2 col-form-label">Attachements</label>
                                                    <div class="col-sm-10">
                                                        <input type="file" class="form-control" name="attachementsf" multiple id="customFile">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-number-input" class="col-sm-2 col-form-label">Amount</label>
                                                    <div class="col-sm-2">
                                                        <input type="text" name="Amount" class="form-control" id="bdt" onkeyup="convertAmount()"/> 
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text"  id="container" readonly class="form-control"  name="Amount_In_Words" />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-datetime-local-input" class="col-sm-2 col-form-label">Informer Details</label>
                                                    <div class="col-sm-10">
                                                        <select class="select2 form-control select2-multiple" name="persionf[]"  implode  multiple="multiple" data-placeholder="Choose ...">
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
                                                        <button name="financial_payment" id="financial_payment" type="submit" class="btn btn-primary waves-effect waves-light me-1">
                                                            Submit
                                                        </button>
                                                        <!-- <input name="financial_payment" id="financial_payment" value="Submit" type="submit" class="btn btn-primary waves-effect waves-light me-1"/> -->
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

            var iWords = ['zero', ' one', ' two', ' three', ' four', ' five', ' six', ' seven', ' eight', ' nine'];
            var ePlace = ['ten', ' eleven', ' twelve', ' thirteen', ' fourteen', ' fifteen', ' sixteen', ' seventeen', ' eighteen', ' nineteen'];
            var tensPlace = ['', ' ten', ' twenty', ' thirty', ' forty', ' fifty', ' sixty', ' seventy', ' eighty', ' ninety'];
            var inWords = [];

            var numReversed, inWords, actnumber, i, j;

            function tensComplication() {
                if (actnumber[i] == 0) {
                    inWords[j] = '';
                } else if (actnumber[i] == 1) {
                    inWords[j] = ePlace[actnumber[i - 1]];
                } else {
                    inWords[j] = tensPlace[actnumber[i]];
                }
            }


                function convertAmount() {
                    var numericValue = document.getElementById('bdt').value;
                    numericValue = parseFloat(numericValue).toFixed(2);
                    
                    var amount = numericValue.toString().split('.');
                    var taka = amount[0];
                    var paisa = amount[1];
                var str = convert(taka);
                if(paisa > 0) {
                            str += "  and "+ convert(paisa)+" paisa only";
                
                }else{
                    str += " rupees only";
                }
                    document.getElementById('container').value = str;
                }
            function convert(numericValue) {
                inWords = []
                if(numericValue == "00" || numericValue =="0"){
                    return 'zero';
                }
                var obStr = numericValue.toString();
                numReversed = obStr.split('');
                actnumber = numReversed.reverse();

                
                if (Number(numericValue) == 0) {
                    document.getElementById('container').innerHTML = 'BDT Zero';
                    return false;
                }
                
                var iWordsLength = numReversed.length;
                var finalWord = '';
                j = 0;
                for (i = 0; i < iWordsLength; i++) {
                    switch (i) {
                        case 0:
                            if (actnumber[i] == '0' || actnumber[i + 1] == '1') {
                                inWords[j] = '';
                            } else {
                                inWords[j] = iWords[actnumber[i]];
                            }
                            inWords[j] = inWords[j] + '';
                            break;
                        case 1:
                            tensComplication();
                            break;
                        case 2:
                            if (actnumber[i] == '0') {
                                inWords[j] = '';
                            } else if (actnumber[i - 1] !== '0' && actnumber[i - 2] !== '0') {
                                inWords[j] = iWords[actnumber[i]] + ' hundred';
                            } else {
                                inWords[j] = iWords[actnumber[i]] + ' hundred';
                            }
                            break;
                        case 3:
                            if (actnumber[i] == '0' || actnumber[i + 1] == '1') {
                                inWords[j] = '';
                            } else {
                                inWords[j] = iWords[actnumber[i]];
                            }
                            if (actnumber[i + 1] !== '0' || actnumber[i] > '0') {
                                inWords[j] = inWords[j] + ' thousand';
                            }
                            break;
                        case 4:
                            tensComplication();
                            break;
                        case 5:
                            if (actnumber[i] == '0' || actnumber[i + 1] == '1') {
                                inWords[j] = '';
                            } else {
                                inWords[j] = iWords[actnumber[i]];
                            }
                            if (actnumber[i + 1] !== '0' || actnumber[i] > '0') {
                                inWords[j] = inWords[j] + ' lakh';
                            }
                            break;
                        case 6:
                            tensComplication();
                            break;
                        case 7:
                            if (actnumber[i] == '0' || actnumber[i + 1] == '1') {
                                inWords[j] = '';
                            } else {
                                inWords[j] = iWords[actnumber[i]];
                            }
                            inWords[j] = inWords[j] + ' crore';
                            break;
                        case 8:
                            tensComplication();
                            break;
                        default:
                            break;
                    }
                    j++;
                }


                inWords.reverse();
                for (i = 0; i < inWords.length; i++) {
                    finalWord += inWords[i];
                }
                return finalWord;
            }

            $(document).ready(function() {
                $("input[type=number]").on("focus", function() {
                    $(this).on("keydown", function(event) {
                    if (event.keyCode === 38 || event.keyCode === 40) {
                        event.preventDefault();
                    }
                    });
                });
            });
            
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
                    $("#app").html("").attr("readonly", true);
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
