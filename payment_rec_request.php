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
$payment_id = $_GET['id'];

// print_r($_id1);exit;
$Employee_Id = $_SESSION['EmpID'];
// echo"SELECT  * FROM HR_Master_Table
// WHERE Employee_Code = '$Employee_Id' ";
 $selector = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
WHERE Employee_Code = '$Employee_Id' ");
$selector_arrr = sqlsrv_fetch_array($selector);

$department = $selector_arrr['Department'];
$L1_Manager = $selector_arrr['L1 Manager'];
$L2_Manager = $selector_arrr['L2_Manager'];
// print_r($L2_Manager);
$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
WHERE Recommender = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$requester = $selector_arr1['PO_creator_Release_Codes'];
// print_r($approver_name);

$Approver_Code = $selector_arr1['Approver'];
// print_r($Approver_Code);die();
if (isset($_POST["save"])) {
    // echo '<pre>';
    // print_r($_POST);die();
    $Request_E_Id = $_POST['Request_pid'];
    $emp_id = $Employee_Id;
    $Requested_to = $Approver_Code;
    $Request_Type = $_POST['request_type'];
    $Department = $_POST['department'];
    $Subject = $_POST['subject'];
    $Request_Date = $_POST['request_date'];
    $Requester = $_POST['requester'];
    $Budjected = $_POST['budject'];
    $Approvel_Type = $_POST['approver_type'];
    $Remark = $_POST['remarks'];
    // $To_Address = $_POST['to'];
    // $Cc = $_POST['cc'];
    $Recommender = $_POST['recommender'];
    $Approver = $_POST['approver'];
    $Mail_subject = $_POST['mail_subj'];
    $Mail_Body = $_POST['mail_body'];
    if(!isset($_POST['attachements'])){
        $Attachment="";
        }else{
          $Attachment=$_POST['attachements'];
        }
    // $Attachment = $_POST['attachements'];
    $Recommender_Remarks = $_POST["recommender_remarks"];

    if (isset($_POST['informer']))
    {
        $informer= implode(',',$_POST['informer']);
        $To_Address= implode(',',$_POST['to']);
        $Cc= implode(',',$_POST['cc']);

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$informer',','))  ");
        $idss = array();
        while ($ids1 = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids1['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
        
    }else{
        $informer= '';
        $To_Address= implode(',',$_POST['to']);
        $Cc= implode(',',$_POST['cc']);

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$informer',','))  ");
        $idss = array();
        while ($ids1 = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids1['Office_Email_Address'];
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
    // $mail->addBCC('softwaredeveloper@mazenetsolution.com');

    $mail->addAttachment($Attachment);         // Add attachments
  // $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $Mail_subject;
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
                            <th class="text-center">Request_E_id</th>
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
                            <th class="text-center">Recommender Remark</th>                           
                            <th class="text-center">Status</th>                           
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                1
                            </td>
                            <td>
                                ' . $Request_E_Id . '
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
                                ' . $Attachment . '
                            </td>
                            <td>
                                ' . $Remark . '
                            </td>
                            <td>
                                ' . $Recommender_Remarks . '
                            </td>
                            <td>
                            <h4><span class="badge badge-success"><i class="fa fa-check"></i>Recommended </span></h4>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </body>
  </html>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Recommender_EV_Remark WHERE Request_E_Id = '$payment_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
if($updated_query['Request_E_Id'] == ''){


    $query = "INSERT INTO Tb_Recommender_EV_Remark (Request_E_Id,Request_Type,Department,Subject,Request_Date,Requester,Budjected,Approvel_Type,Remark,To_Address,
            Cc,Recommender,Approver,Mail_Subject,Mail_Body,Attachment,Persion_In_workflow,Recommender_Remarks,Time_Log,Status,EMP_ID,Requested_to)
         VALUES('$Request_E_Id','$Request_Type','$Department','$Subject','$Request_Date','$Requester','$Budjected','$Approvel_Type','$Remark','$To_Address','$Cc',
         '$Recommender','$Approver','$Mail_subject','$Mail_Body','$Attachment','$informer','$Recommender_Remarks',
         GETDATE(),'Recommended','$emp_id','$Requested_to')";
    $query1 = sqlsrv_query($conn, "UPDATE Tb_Payment_Request set Status = 'Recommended',Approver_to = '$Requested_to' WHERE Request_PID = '$Request_E_Id' ");

    $rs = sqlsrv_query($conn, $query);
}else{
    $query12 = sqlsrv_query($conn, "UPDATE Tb_Payment_Request set Status = 'Recommended' WHERE Request_PID = '$Request_E_Id' ");
    $query11 = sqlsrv_query($conn, "UPDATE Tb_Recommender_EV_Remark set Recommender_Remarks = '$Recommender_Remarks',Status = 'Recommended' WHERE Request_E_Id = '$Request_E_Id' ");
}
    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        // mail($to, $subject, $htmlContent, $headers);
        // move_uploaded_file($_FILES["attachements"]["tmp_name"], 'file/' . $fil);
        ?>
                        <script type="text/javascript">
                            alert("Recommended successsfully");
                            window.location = "show_payment_recommender.php";
                        </script>

                        <?php
    }

}
if (isset($_POST["send"])) {
    // echo '<pre>';
    // print_r($_POST);die();
    $Request_E_Id = $_POST['Request_pid'];
    $emp_id = $Employee_Id;
    $Request_Type = $_POST['request_type'];
    $Department = $_POST['department'];
    $Subject = $_POST['subject'];
    $Request_Date = $_POST['request_date'];
    $Requester = $_POST['requester'];
    $Budjected = $_POST['budject'];
    $Approvel_Type = $_POST['approver_type'];
    $Remark = $_POST['remarks'];
    // $update_qry1 =  sqlsrv_query($conn, "SELECT * FROM Tb_Payment_Request WHERE Request_PID = '$payment_id'");
    // $updated_query1 = sqlsrv_fetch_array($update_qry1);
    // $PERSION1 =  $updated_query1['To_Address'];

    // $update_qry12 =  sqlsrv_query($conn, "SELECT * FROM Tb_Payment_Request WHERE Request_PID = '$payment_id'");
    // $updated_query12 = sqlsrv_fetch_array($update_qry12);
    // $PERSION12 =  $updated_query12['Cc'];

    // $To_Address = $PERSION1;
    // $Cc = $PERSION12;
    $Recommender = $_POST['recommender'];
    $Approver = $_POST['approver'];
    $Mail_subject = $_POST['mail_subj'];
    $Mail_Body = $_POST['mail_body'];
    if(!isset($_POST['attachements'])){
        $Attachment="";
        }else{
          $Attachment=$_POST['attachements'];
        }
    // $informer = $_POST['informer'];
    $Recommender_Remarks = $_POST["recommender_remarks"];
    $Recommender_back_remark = $_POST['Recommender_back_remark'];

    if (isset($_POST['informer']))
    {
        $informer= implode(',',$_POST['informer']);
        $To_Address= implode(',',$_POST['to']);
        $Cc= implode(',',$_POST['cc']);

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$informer',','))  ");
        $idss = array();
        while ($ids1 = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids1['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
        
    }else{
        $informer= '';
        $To_Address= implode(',',$_POST['to']);
        $Cc= implode(',',$_POST['cc']);

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$informer',','))  ");
        $idss = array();
        while ($ids1 = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids1['Office_Email_Address'];
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

    $mail->addAttachment($Attachment);         // Add attachments
  // $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $Mail_subject;
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
                            <th class="text-center">Request_E_id</th>
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
                            <th class="text-center">Recommender Back Remark</th>                           
                            <th class="text-center">Status</th>                           
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                1
                            </td>
                            <td>
                                ' . $Request_E_Id . '
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
                                ' . $Attachment . '
                            </td>
                            <td>
                                ' . $Remark . '
                            </td>
                            <td>
                                ' . $Recommender_back_remark . '
                            </td>
                            <td>
                            <h4><span class="badge badge-success"><i class="fa fa-check"></i>Send Back </span></h4>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </body>
  </html>';
      $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

      $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Recommender_EV_Remark WHERE Request_E_Id = '$payment_id'");
      $updated_query = sqlsrv_fetch_array($update_qry);
if($updated_query['Request_E_Id'] == ''){

    $query = "INSERT INTO Tb_Recommender_EV_Remark
  (Request_E_Id,Request_Type,Department,Subject,Request_Date,Requester,Budjected,
  Approvel_Type,Remark,To_Address,Cc,Recommender,Approver,Mail_Subject,Mail_Body,
  Attachment,Persion_In_workflow,Recommender_Remarks,Recommender_back_remark,Time_Log,Status,EMP_ID)
   VALUES('$Request_E_Id','$Request_Type','$Department','$Subject','$Request_Date',
   '$Requester','$Budjected','$Approvel_Type','$Remark','$To_Address','$Cc',
   '$Recommender','$Approver','$Mail_subject','$Mail_Body','$Attachment','$informer','$Recommender_Remarks','$Recommender_back_remark',
   GETDATE(),'Send Back','$emp_id')";

    $query1 = sqlsrv_query($conn, "UPDATE Tb_Payment_Request set Status = 'Send Back' ,Send_Back = '$Recommender_back_remark' WHERE Request_PID = '$Request_E_Id' ");
    $rs = sqlsrv_query($conn, $query);
}else{
    // echo "UPDATE Tb_Payment_Request set Status = 'Send Back' ,Send_Back = '$Recommender_back_remark' WHERE Request_PID = '$Request_E_Id' ";die();
    $query12 = sqlsrv_query($conn, "UPDATE Tb_Payment_Request set Status = 'Send Back' ,Send_Back = '$Recommender_back_remark' WHERE Request_PID = '$Request_E_Id' ");
    $query11 = sqlsrv_query($conn, "UPDATE Tb_Recommender_EV_Remark set Status = 'Send Back', Recommender_Remarks = '$Recommender_Remarks',Recommender_back_remark = '$Recommender_back_remark' WHERE Request_E_Id = '$Request_E_Id' ");
}
    
    // echo $rs;die();
    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        // mail($to, $subject, $htmlContent, $headers);
        // move_uploaded_file($_FILES["attachements"]["tmp_name"], 'file/' . $fil);
        ?>
                <script type="text/javascript">
                    alert("Recommended Back successsfully");
                    window.location = "show_payment_recommender.php";
                </script>

                <?php
    }
}
if (isset($_POST["reject"])) {
    $Request_E_Id = $_POST['Request_pid'];
    $emp_id = $Employee_Id;
    $Requested_to = $Approver_Code;
    $Request_Type = $_POST['request_type'];
    $Department = $_POST['department'];
    $Subject = $_POST['subject'];
    $Request_Date = $_POST['request_date'];
    $Requester = $_POST['requester'];
    $Budjected = $_POST['budject'];
    $Approvel_Type = $_POST['approver_type'];
    $Remark = $_POST['remarks'];
    // $update_qry1 =  sqlsrv_query($conn, "SELECT * FROM Tb_Payment_Request WHERE Request_PID = '$payment_id'");
    // $updated_query1 = sqlsrv_fetch_array($update_qry1);
    // $PERSION1 =  $updated_query1['To_Address'];

    // $update_qry12 =  sqlsrv_query($conn, "SELECT * FROM Tb_Payment_Request WHERE Request_PID = '$payment_id'");
    // $updated_query12 = sqlsrv_fetch_array($update_qry12);
    // $PERSION12 =  $updated_query12['Cc'];

    // $To_Address = $PERSION1;
    // $Cc = $PERSION12;
    $Recommender = $_POST['recommender'];
    $Approver = $_POST['approver'];
    $Mail_subject = $_POST['mail_subj'];
    $Mail_Body = $_POST['mail_body'];
    if(!isset($_POST['attachements'])){
        $Attachment="";
        }else{
          $Attachment=$_POST['attachements'];
        }
    $Recommender_Remarks = $_POST["recommender_remarks"];
    $Recommender_reject = $_POST['Recommender_reject'];

    if (isset($_POST['informer']))
    {
        $informer= implode(',',$_POST['informer']);
        $To_Address= implode(',',$_POST['to']);
        $Cc= implode(',',$_POST['cc']);

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$informer',','))  ");
        $idss = array();
        while ($ids1 = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids1['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
        
    }else{
        $informer= '';
        $To_Address= implode(',',$_POST['to']);
        $Cc= implode(',',$_POST['cc']);

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$informer',','))  ");
        $idss = array();
        while ($ids1 = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids1['Office_Email_Address'];
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


    $mail->addAttachment($Attachment);         // Add attachments
  // $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $Mail_subject;
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
                            <th class="text-center">Request_E_id</th>
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
                            <th class="text-center">Recommender Remark</th>                           
                            <th class="text-center">Status</th>                           
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                1
                            </td>
                            <td>
                                ' . $Request_E_Id . '
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
                                ' . $Attachment . '
                            </td>
                            <td>
                                ' . $Remark . '
                            </td>
                            <td>
                                ' . $Recommender_Remarks . '
                            </td>
                            <td>
                            <h4><span class="badge badge-warning"><i class="fa fa-check"></i>Rejected </span></h4>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </body>
  </html>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Recommender_EV_Remark WHERE Request_E_Id = '$payment_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
if($updated_query['Request_E_Id'] == ''){
    $query = "INSERT INTO Tb_Recommender_EV_Remark (Request_E_Id,Request_Type,Department,Subject,Request_Date,Requester,Budjected,Approvel_Type,Remark,To_Address,
            Cc,Recommender,Approver,Mail_Subject,Mail_Body,Attachment,Persion_In_workflow,Recommender_Remarks,Time_Log,Status,EMP_ID,Requested_to,Recommender_reject)
         VALUES('$Request_E_Id','$Request_Type','$Department','$Subject','$Request_Date','$Requester','$Budjected','$Approvel_Type','$Remark','$To_Address','$Cc',
         '$Recommender','$Approver','$Mail_subject','$Mail_Body','$Attachment','$informer','$Recommender_Remarks',
         GETDATE(),'Rejected','$emp_id','$Requested_to','$Recommender_reject')";
    $query1 = sqlsrv_query($conn, "UPDATE Tb_Payment_Request set Status = 'Rejected' WHERE Request_PID = '$Request_E_Id' ");

    $rs = sqlsrv_query($conn, $query);
}else{
    $query12 = sqlsrv_query($conn, "UPDATE Tb_Payment_Request set Status = 'Rejected',Recommender_reject = '$Recommender_reject' WHERE Request_PID = '$Request_E_Id' ");
    $query11 = sqlsrv_query($conn, "UPDATE Tb_Recommender_EV_Remark set Recommender_reject = '$Recommender_reject',Status = 'Rejected' WHERE Request_E_Id = '$Request_E_Id' ");
}
    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        // mail($to, $subject, $htmlContent, $headers);
        // move_uploaded_file($_FILES["attachements"]["tmp_name"], 'file/' . $fil);
        ?>
                        <script type="text/javascript">
                            alert("Rejected successsfully");
                            window.location = "show_payment_recommender.php";
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
                                             <li class="breadcrumb-item"><a href="show_payment_recommender.php">Show Recommender Payment Request</a></li>
                                             <li class="breadcrumb-item active">Recommender Payment Request</li>
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
                                                            <input type="date" class="form-control" id="hasta" name="request_date" value="<?php echo $updated_query['Request_Date'] ?>" readonly >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="Requester" class="form-label">Requester</label>
                                                            <input type="text" class="form-control" id="Requester" readonly name="requester" value="<?php echo $updated_query['Requester'] ?>" readonly>
                                                            <input type="hidden" class="form-control" id="Requester" readonly name="request_type" value="<?php echo $updated_query['Request_Type'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label">Approval Type</label>
                                                            <input type="text" class="form-control"  readonly name="approver_type" value="<?php echo $updated_query['Approvel_Type'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label">Department</label>
                                                            <input type="text" class="form-control" readonly name="department" value="<?php echo $updated_query['Depatment'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3">
                                                            <label for="subject" class="form-label">Subject</label>
                                                            <input type="text" class="form-control" id="Requester" readonly name="subject" value="<?php echo $updated_query['Subject'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 110px;">
                                                        <div class="mb-3">
                                                            <label for="bud" class="form-label">Budgeted</label>
                                                            <input type="text" class="form-control" id="Requester" readonly name="budject" value="<?php echo $updated_query['Budjected'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1" style="width: 175px;">
                                                        <div class="mb-3" id="remark">
                                                            <label for="red" class="form-label">Remarks</label>
                                                                <textarea class="form-control" readonly rows="1" name="remarks" id="red"><?php echo $updated_query['Remark'] ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-text-input" class="col-sm-2 col-form-label">To</label>
                                                    <div class="col-sm-10">
                                                    
                                                    <select class="select2 form-control select2-multiple" name="to[]"  implode multiple data-placeholder="Choose ...">
                                                            
                                                            <?php

                                                                $selectorq = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                                WHERE Employee_Code = '$approver_name' ");
                                                                $selector_arrq = sqlsrv_fetch_array($selectorq);
                                                                $res = explode(',',$selector_arrq['Office_Email_Address']);
                                                                $res1 = explode(',',$approver_name);
                                                                
                                                                $HR_Master_Table = sqlsrv_query($conn, "Select * from HR_Master_Table ");
                                                                while ($HR = sqlsrv_fetch_array($HR_Master_Table)) {
                                                            
                                                            ?>

                                                        <option  <?= (in_array($HR['Employee_Code'],$res1)) ? 'selected' : ''?> value="<?php echo $HR['Office_Email_Address'] ?>">
                                                            <?php echo $HR['Office_Email_Address'] ?>&nbsp;-&nbsp;<?php echo $HR['Employee_Code'] ?>
                                                        </option>

                                                            <?php                                                                       
                                                                    } ?>
                                                        </select>
                                                    <!-- <input type="email" class="form-control" name="to"  value='<?php echo $selector_arrq['Office_Email_Address']; ?>' multiple data-role="tagsinput"> -->
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-search-input" class="col-sm-2 col-form-label">Cc</label>
                                                    <div class="col-sm-10">
                                                    <!-- <?php
                                                        $selectorq12 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$recommender_to' ");
                                                        $selector_arrq12 = sqlsrv_fetch_array($selectorq12);

                                                        $selectorq121 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$requester' ");
                                                        $selector_arrq121 = sqlsrv_fetch_array($selectorq121);
                                                    ?>
                                                    <input type="email" class="form-control"  name="cc"  value='<?php echo $selector_arrq12['Office_Email_Address']; ?>,<?php echo $selector_arrq121['Office_Email_Address']; ?>'  data-role="tagsinput" multiple>  -->
                                                    <select class="select2 form-control select2-multiple" name="cc[]"  implode multiple data-placeholder="Choose ...">
                                                            
                                                            <?php   
                                                                $HR_Master_Table = sqlsrv_query($conn, "Select * from HR_Master_Table ");
                                                                while ($HR = sqlsrv_fetch_array($HR_Master_Table)) {
                                                                    $res = "$requester";
                                                                    $res1 = "$recommender_to";
                                                                    $array = "$res,$res1";
                                                                    $array_code = explode(',', $array);
                                                            ?>

                                                        <option  <?= (in_array($HR['Employee_Code'],$array_code)) ? 'selected' : ''?> value="<?php echo $HR['Office_Email_Address'] ?>">
                                                            <?php echo $HR['Office_Email_Address'] ?>&nbsp;-&nbsp;<?php echo $HR['Employee_Code'] ?>
                                                        </option>

                                                            <?php                                                                       
                                                                    } ?>
                                                        </select>
                                                
                                                </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="Recommender" class="col-sm-2 col-form-label">Recommender</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="recommender"  value="<?php echo $updated_query['Recommender'] ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="Approver" class="col-sm-2 col-form-label">Approver</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text"  name="approver" value="<?php echo $updated_query['Approver'] ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="subj" class="col-sm-2 col-form-label">Mail Subject</label>
                                                    <div class="col-sm-10">
                                                        <input class="form-control" type="text" name="mail_subj" value="<?php echo $updated_query['Mail_Subject'] ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-password-input" class="col-sm-2 col-form-label">Mail Body</label>
                                                    <div class="col-sm-10">
                                                    <textarea class="form-control" rows="2" name="mail_body" id="elm2" readonly><?php echo $updated_query['Mail_Body'] ?></textarea>
                                                    </div>
                                                </div>
                                                <?php 
                                                    if($updated_query['Attachement'] == ''){
                                                        ?>
                                                        <!-- <input type="hidden" class="form-control" name="attachements" > -->
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
                                                    <label for="example-password-input" class="col-sm-2 col-form-label">Amount</label>
                                                    <div class="col-sm-2">
                                                        <input type="text"  style="font-weight: bold; color: #0000ff;" name="Amount" class="form-control" id="bdt" readonly onkeyup="convertAmount()" value="<?php echo $updated_query['Amount'] ?>"/> 
                                                    </div>
                                                    <div class="col-sm-8">
                                                        <input type="text" style="font-weight: bold; color: #0000ff;" id="container" readonly class="form-control" value="<?php echo $updated_query['Amount_In_Words'] ?>" name="Amount_In_Words" />
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-datetime-local-input" class="col-sm-2 col-form-label">Informer Details</label>
                                                    <div class="col-sm-10">
                                                    <select class="select2 form-control select2-multiple" name="informer[]"  implode multiple data-placeholder="Choose ...">
                                                            
                                                            <?php
                                                                $res = explode(',',$updated_query['Persion_In_Workflow']);
                                                                $HR_Master_Table = sqlsrv_query($conn, "Select * from HR_Master_Table ");
                                                                while ($HR = sqlsrv_fetch_array($HR_Master_Table)) {
                                                                    
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
                                                            <?php                                                                       
                                                                    } ?>
                                                        </select>
                                                    </div>
                                                   
                                                </div>
                                                <div class="row mb-3">
                                                    <label for="example-datetime-local-input" class="col-sm-2 col-form-label">Recommender Remark</label>
                                                    <div class="col-sm-10">
                                                        <textarea class="form-control" rows="2" name="recommender_remarks" ></textarea>
                                                        <!-- <input type="text" class="form-control"  name="recommender_remarks" > -->
                                                    </div>
                                                </div>
                                                <center style="padding: 15px 0px 15px 0px;">
                                                    <button class="btn btn-success mb-2 me-4 btn-sm" type="submit" name="save" style="width: 150px;">
                                                    Submit
                                                    </button>
                                                    <button class="btn btn-warning mb-2 me-4 btn-sm" type="button" style="width: 150px;" data-bs-toggle="modal" data-bs-target=".bs-example-modal-centered">
                                                    Send Back
                                                    </button>
                                                    <button class="btn btn-danger mb-2 me-4 btn-sm" type="button" style="width: 150px;" data-bs-toggle="modal" data-bs-target=".bs-example-modal-centered1">
                                                    Reject
                                                    </button>
                                                </center>
                                                <div class="modal fade bs-example-modal-centered" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title mt-0">Remarks For SendBack</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                    
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
															    <textarea class="form-control" name="Recommender_back_remark" aria-label="With textarea"></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                <button type="submit" class="btn btn-success waves-effect waves-light btn-sm"  name="send" data-bs-dismiss="modal">Submit</button>
                                                            </div>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                                <div class="modal fade bs-example-modal-centered1" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title mt-0">Remarks For Reject</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                    
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
															    <textarea class="form-control" name="Recommender_reject" aria-label="With textarea"></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                <button type="submit" class="btn btn-success waves-effect waves-light btn-sm"  name="reject" data-bs-dismiss="modal">Submit</button>
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
