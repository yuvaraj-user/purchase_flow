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
    // $Finance_Verification = $_POST['Finance_Verification'];

    if(!isset($_POST['persionp'])){
        $Subject123="0";
        }else{
          $Subject123=$_POST['persionp'];
        }
    if($Subject123>0){ 
    
        $Persion= implode(',',$_POST['persionp']);
        $status = 'Requested';

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$Persion',','))  ");
        $idss = array();
        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
        // print_r($implode);exit;
        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
        $HR = sqlsrv_fetch_array($HR_Master_Table1);
        $To = $HR['Office_Email_Address'];
    }else{

        $Persion= '';
        $implode='';
        $status = 'Requested';
        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
        $HR = sqlsrv_fetch_array($HR_Master_Table1);
        $To = $HR['Office_Email_Address'];
    }

        
        
    $query = "INSERT INTO Tb_Request (Request_ID, Request_Type, Plant, Storage_Location,Time_Log,Request_Category,
     status, Department,Persion_In_Workflow,EMP_ID,Requested_to) VALUES 
	  ('$request_id','$request_type','$plant_id','$storage_location',GETDATE(),'$request_type1','$status','$Department','$Persion','$emp_id','$Requested_to')";
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


        $sql = "INSERT INTO Tb_Request_Items (Request_ID, Item_Code, UOM, Description, Quantity, Budget, Time_Log, status, Expected_Date, Specification, Attachment, Budject_Remark,
         MaterialGroup, EMP_ID,Requested_to) VALUES 
		('$request_id','$item_code','$uom','$description','$quantity','$budgest',GETDATE(),'$status','$Expected_Date','$Specification','$fil','$budget_remark',
        '$MaterialGroup','$emp_id','$Requested_to')"; 

        $params = array("updated data", 1);

        $stmt = sqlsrv_query($conn, $sql, $params);

        $rows_affected = sqlsrv_rows_affected($stmt);
        if ($rows_affected === false) {
            die(print_r(sqlsrv_errors(), true));
        } elseif ($rows_affected == -1) {
            echo "No information available.<br />";
        }else{
            move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);

        }

    }
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
    
    $to = explode(',', $To);
    $to = array('jr_developer4@mazenetsolution.com');
    
    foreach ($to as $address) {
        // $mail->AddAddress($address);
        $mail->AddAddress(trim($address));
    }
    // $cc = explode(',', $implode);
    // foreach ($cc as $ccc) {
    //     // $mail->AddAddress($address);
    //     $mail->addCC(trim($ccc));
    // }
    
//Set BCC address
// $mail->addBCC("softwaredeveloper@mazenetsolution.com", "Some BCC Name");
    
    $mail->addAttachment($fil);         // Add attachments
    // $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML
    
    $mail->Subject = $request_type1;
    $mail->Body    =   "<table width='100%' border='1' style='margin-top:15px;' align='left class='table table-striped'>". 
                          "<thead><tr>".
                          "<th>SN</th>".
                          "<th nowrap='nowrap'>Request ID</th>".
                          "<th nowrap='nowrap'>Department</th>".
                          "<th nowrap='nowrap'>Category</th>".
                          "<th nowrap='nowrap'>Plant</th>".
                          "<th nowrap='nowrap'>Meterial</th>".
                          "<th nowrap='nowrap'>Quantity</th>".
                          "</tr></thead><tbody>";
                          $qq = sqlsrv_query($conn,"SELECT Tb_Request.Request_ID,Tb_Request.Department,Tb_Request.Request_Type,Tb_Request.Plant,
                          Tb_Request_Items.Item_Code,Tb_Request_Items.Quantity FROM Tb_Request  INNER JOIN Tb_Request_Items 
                          ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID  WHERE Tb_Request.Request_ID = '$request_id'");
                          $d =0;
                          while($c = sqlsrv_fetch_array($qq)){ $d++;
      $mail->Body.=       "<tr>".
                          "<td nowrap='nowrap'>".$d."</td>".
                          "<td nowrap='nowrap'> ".$c['Request_ID']."</td>".
                          "<td nowrap='nowrap'> ".$c['Department']."</td>".
                          "<td nowrap='nowrap'> ".$c['Request_Type']."</td>".
                          "<td nowrap='nowrap'> ".$c['Plant']."</td>".
                          "<td nowrap='nowrap'> ".$c['Item_Code']."</td>".
                          "<td nowrap='nowrap'> ".$c['Quantity']."</td></tr>";
                          }
    $mail->Body.=       "</tbody></table>";
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
    if (!$mail->send()) {
        echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    }else{
        // exit;
        ?>
        <script type="text/javascript">
        alert("Request added successsfully");
        window.location = "show_purchacevendor_request.php";
        </script>
    
    <?php
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
    // $Finance_Verification = $_POST['Finance_Verification'];
    if(!isset($_POST['persionp'])){
        $Subject123="0";
        }else{
          $Subject123=$_POST['persionp'];
        }
    if($Subject123>0){ 
    
        $Persion= implode(',',$_POST['persionp']);
        $status = 'Requested';

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$Persion',','))  ");
        $idss = array();
        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
        // print_r($implode);exit;
        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
        $HR = sqlsrv_fetch_array($HR_Master_Table1);
        $To = $HR['Office_Email_Address'];
    }else{

        $Persion= '';
        $implode='';
        $status = 'Requested';
        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
        $HR = sqlsrv_fetch_array($HR_Master_Table1);
        $To = $HR['Office_Email_Address'];
    }
        
    $query = "INSERT INTO Tb_Request (Request_ID, Request_Type, Plant, Storage_Location,Time_Log,Request_Category, status,
     Department,Persion_In_Workflow,EMP_ID,Requested_to) VALUES 
	  ('$request_id','$request_type','$plant_id','$storage_location',GETDATE(),'$request_type1','$status','$Department','$Persion','$emp_id','$Requested_to')";
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


        $sql = "INSERT INTO Tb_Request_Items (Request_ID, Item_Code, UOM, Description, Quantity, Budget, Time_Log, status, Expected_Date, Specification, Attachment, Budject_Remark,
         MaterialGroup, EMP_ID,Requested_to) VALUES 
		('$request_id','$item_code','$uom','$description','$quantity','$budgest',GETDATE(),'$status','$Expected_Date','$Specification','$fil','$budget_remark',
        '$MaterialGroup','$emp_id','$Requested_to')";

        $params = array("updated data", 1);

        $stmt = sqlsrv_query($conn, $sql, $params);

        $rows_affected = sqlsrv_rows_affected($stmt);
        if ($rows_affected === false) {
            die(print_r(sqlsrv_errors(), true));
        } elseif ($rows_affected == -1) {
            echo "No information available.<br />";
        }else{
            move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);

        }

    }
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
    
    $to = explode(',', $To);
    $to = array('jr_developer4@mazenetsolution.com');
    
    foreach ($to as $address) {
        // $mail->AddAddress($address);
        $mail->AddAddress(trim($address));
    }
    // $cc = explode(',', $implode);
    // foreach ($cc as $ccc) {
    //     // $mail->AddAddress($address);
    //     $mail->addCC(trim($ccc));
    // }
    // $bcc = explode(',', $Bcc);
    
    // foreach ($bcc as $bccc) {
    //   // $mail->AddAddress($address);
    //   $mail->addBCC(trim($bccc));
    // }
    
    $mail->addAttachment($fil);         // Add attachments
    // $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML
    
    $mail->Subject = $request_type1;
    $mail->Body    =   "<table width='100%' border='1' style='margin-top:15px;' align='left class='table table-striped'>". 
                          "<thead><tr>".
                          "<th>SN</th>".
                          "<th nowrap='nowrap'>Request ID</th>".
                          "<th nowrap='nowrap'>Department</th>".
                          "<th nowrap='nowrap'>Category</th>".
                          "<th nowrap='nowrap'>Plant</th>".
                          "<th nowrap='nowrap'>Meterial</th>".
                          "<th nowrap='nowrap'>Quantity</th>".
                          "</tr></thead><tbody>";
                          $qq = sqlsrv_query($conn,"SELECT Tb_Request.Request_ID,Tb_Request.Department,Tb_Request.Request_Type,Tb_Request.Plant,
                          Tb_Request_Items.Item_Code,Tb_Request_Items.Quantity FROM Tb_Request  INNER JOIN Tb_Request_Items 
                          ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID  WHERE Tb_Request.Request_ID = '$request_id'");
                          $d =0;
                          while($c = sqlsrv_fetch_array($qq)){ $d++;
      $mail->Body.=       "<tr>".
                          "<td nowrap='nowrap'>".$d."</td>".
                          "<td nowrap='nowrap'> ".$c['Request_ID']."</td>".
                          "<td nowrap='nowrap'> ".$c['Department']."</td>".
                          "<td nowrap='nowrap'> ".$c['Request_Type']."</td>".
                          "<td nowrap='nowrap'> ".$c['Plant']."</td>".
                          "<td nowrap='nowrap'> ".$c['Item_Code']."</td>".
                          "<td nowrap='nowrap'> ".$c['Quantity']."</td></tr>";
                          }
    $mail->Body.=       "</tbody></table>";
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
    if (!$mail->send()) {
        echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
    }else{
        // exit;
        ?>
        <script type="text/javascript">
        alert("Request added successsfully");
        window.location = "show_purchacevendor_request.php";
        </script>
    
    <?php
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
    // $Finance_Verification = $_POST['Finance_Verification'];
    if(!isset($_POST['persionp'])){
        $Subject123="0";
        }else{
          $Subject123=$_POST['persionp'];
        }
    if($Subject123>0){ 
    
        $Persion= implode(',',$_POST['persionp']);
        $status = 'Requested';

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$Persion',','))  ");
        $idss = array();
        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
        // print_r($implode);exit;
        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
        $HR = sqlsrv_fetch_array($HR_Master_Table1);
        $To = $HR['Office_Email_Address'];
    }else{

        $Persion= '';
        $implode='';
        $status = 'Requested';
        $HR_Master_Table1 = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Purchaser_Code'  ");
        $HR = sqlsrv_fetch_array($HR_Master_Table1);
        $To = $HR['Office_Email_Address'];
    }
                                                   
        
    $query = "INSERT INTO Tb_Request (Request_ID, Request_Type, Plant, Storage_Location,Time_Log,Request_Category, status, Department,
    Persion_In_Workflow,EMP_ID,Requested_to) VALUES 
	  ('$request_id','$request_type','$plant_id','$storage_location',GETDATE(),'$request_type1','$status','$Department','$Persion','$emp_id','$Requested_to')";
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
        

        $sql = "INSERT INTO Tb_Request_Items (Request_ID, Item_Code, UOM, Description, Quantity, Budget, Time_Log, status, Expected_Date, Specification, Attachment, Budject_Remark,
         MaterialGroup, EMP_ID,Requested_to) VALUES 
		    ('$request_id','$item_code','$uom','$description','$quantity','$budgest',GETDATE(),'$status','$Expected_Date','$Specification','$fil','$budget_remark',
        '$MaterialGroup','$emp_id','$Requested_to')";
 


        $params = array("updated data", 1);

        $stmt = sqlsrv_query($conn, $sql, $params);

        $rows_affected = sqlsrv_rows_affected($stmt);
        if ($rows_affected === false) {
            die(print_r(sqlsrv_errors(), true));
        } elseif ($rows_affected == -1) {
            echo "No information available.<br />";
        }else{
            move_uploaded_file($_FILES["Attachment"]["tmp_name"][$i], 'file/' . $fil);
            // exit;

        }
    }
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

// $to = explode(',', $To);
$to = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

foreach ($to as $address) {
    // $mail->AddAddress($address);
    $mail->AddAddress(trim($address));
}
// $cc = explode(',', $implode);
// foreach ($cc as $ccc) {
//     // $mail->AddAddress($address);
//     $mail->addCC(trim($ccc));
// }
// $bcc = explode(',', $Bcc);

// foreach ($bcc as $bccc) {
//   // $mail->AddAddress($address);
//   $mail->addBCC(trim($bccc));
// }

$mail->addAttachment($fil);         // Add attachments
// $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = $request_type1;
$mail->Body    =   "<table width='100%' border='1' style='margin-top:15px;' align='left class='table table-striped'>". 
                      "<thead><tr>".
                      "<th>SN</th>".
                      "<th nowrap='nowrap'>Request ID</th>".
                      "<th nowrap='nowrap'>Department</th>".
                      "<th nowrap='nowrap'>Category</th>".
                      "<th nowrap='nowrap'>Plant</th>".
                      "<th nowrap='nowrap'>Meterial</th>".
                      "<th nowrap='nowrap'>Quantity</th>".
                      "</tr></thead><tbody>";
                      $qq = sqlsrv_query($conn,"SELECT Tb_Request.Request_ID,Tb_Request.Department,Tb_Request.Request_Type,Tb_Request.Plant,
                      Tb_Request_Items.Item_Code,Tb_Request_Items.Quantity FROM Tb_Request  INNER JOIN Tb_Request_Items 
                      ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID  WHERE Tb_Request.Request_ID = '$request_id'");
                      $d =0;
                      while($c = sqlsrv_fetch_array($qq)){ $d++;
  $mail->Body.=       "<tr>".
                      "<td nowrap='nowrap'>".$d."</td>".
                      "<td nowrap='nowrap'> ".$c['Request_ID']."</td>".
                      "<td nowrap='nowrap'> ".$c['Department']."</td>".
                      "<td nowrap='nowrap'> ".$c['Request_Type']."</td>".
                      "<td nowrap='nowrap'> ".$c['Plant']."</td>".
                      "<td nowrap='nowrap'> ".$c['Item_Code']."</td>".
                      "<td nowrap='nowrap'> ".$c['Quantity']."</td></tr>";
                      }
$mail->Body.=       "</tbody></table>";
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';  
if (!$mail->send()) {
    echo 'Message could not be sent.';
echo 'Mailer Error: ' . $mail->ErrorInfo;
}else{
    // exit;
    ?>
    <script type="text/javascript">
    alert("Request added successsfully");
    window.location = "show_purchacevendor_request.php";
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
        #involved .th,.td {
        border: 2px solid;
        }
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
                                <input type="hidden" id="trow_no" value="1">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
    
                                            <form  method="POST" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-md-2" > 
                                                        <div class="mb-3">
                                                            <label for="validationCustom01" class="form-label">RequestID</label>
                                                            <input type="text" class="form-control" id="validationCustom01" name="request_id"
                                                                value="<?php echo $_purchaseID ?>" readonly>
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
                                                            <label for="app" class="form-label plant-dropdown-label">Plant</label>
                                                            <select class="form-select plant-dropdowns" name="plant_id" id="plant-dropdown">
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
                                                <h4>Involved Persons</h4>
                                        <table id="involved" >
                                          <thead>
                                            <tr>
                                              <th  class="th">Purchaser</th>
                                              <th  class="th">Finance</th>
                                              <th  class="th">Recommender</th>
                                              <th  class="th">Approver</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <tr>
                                            <?php
                                                        $selectorqw2 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$Purchaser_Code' ");
                                                        $selector_arrqw21 = sqlsrv_fetch_array($selectorqw2);
                                                    ?>
                                                     <td  class="td"><?php echo $selector_arrqw21['Employee_Name'] ?></td>
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
            var sno = 1;

            // ON CHANGE DROPDOWN 
            $(document).ready(function () {    
                $('.plant_div').hide();
                $('.storage_div').hide();
                $('.mat_group_div').hide();     

                $('#save').attr('disabled', true);
                $('#add').attr('disabled', true);
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
                var Storage_Location = this.value;
                var Plant  = $('#plant-dropdown').val();
                $('#trow_no').val(1);

                $.ajax({
                    url: "common_ajax.php",
                    type: "POST",
                    data: {
                        Action : 'get_material_group',
                        Storage_Location: Storage_Location,
                        Plant : Plant
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
                    },
                    complete:function(){
                        $('#ajax-preloader').hide();
                    }
                });
            });

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
                        $(".MaterialGroupda").select2();
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
                if (stateID == 'New_Item')
                $("input.disabled").prop("readonly", false);
                else
                    $('select option:selected').prop('disabled', true);
                $("input.disabled").prop("readonly", true);
            });
           

            function openPopup(item) {
                var id = $(item).data('id');
                $("#inputFormModal" + id).modal('show');
            }


            $(document).on('change', '.add', function () {
                add_row();
                $('#save').attr('disabled',false)
            });


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
                            <td>
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
                                    <input type="text" class="form-control disabled uom" id="uom${row_no}" style="width: 110px;"  name="uom[]" placeholder="Enter UOM" >
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
                i--;
                if (i == 1) {
                $('#save').attr('disabled', true);
                $('#add').attr('disabled', true);

                }
            });

            // END MORE 
        </script>
        <!-- CUSTOM JS END -->
    </body>
</html>
