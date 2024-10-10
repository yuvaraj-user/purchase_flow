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

        .select2-container--default  {
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
                                                            <select class="select2 form-select " id="smallSelect" name="request_type">
                                                                <option value="">Select Category</option>
                                                                <option value="Asset purchases">Asset purchases</option>
                                                                <option value="Material purchases">Material purchases</option>
                                                                <option value="Services">Services</option>
                                                            </select>                                                        
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label plant-dropdown-label" style="display:none;">Plant</label>
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
                                                            <select class="form-select plant-dropdowns" name="plant_id1" id="plant-dropdown1">
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
                                                            <select class="form-select plant-dropdowns" name="plant_id2" id="plant-dropdown2">
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
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label storage-dropdown-label" style="display:none;">Storage Location</label>
                                                            <select class="storage-dropdowns form-select" name="storage_location" id="storage-dropdown">
                                                            </select>
                                                            <select class="storage-dropdowns form-select" name="storage_location" id="storage-dropdown1">
                                                            </select>
                                                            <select class="storage-dropdowns form-select" name="storage_location" id="storage-dropdown2">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="mat_group" class="form-label mat_group_label" style="display:none;">Material Group</label>
                                                           <select class="mat_group form-select add" name="mat_group" id="mat_group">
                                                            </select>
                                                            <select class="mat_group form-select add1" name="mat_group" id="mat_group1">
                                                            </select>
                                                            <select class="mat_group form-select add2" name="mat_group" id="mat_group2">
                                                            </select>
                                                        </div>
                                                    </div>
                                                   <!--  <div class="d-grid gap-2 col-2 mx-auto" style="padding: 26px 0px 48px 0px;">
                                                        <button class="btn btn-primary btn-sm" style="position: absolute;right: 20px;" id="add">
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
                                                        <button class="btn btn-primary btn-sm" style="position: absolute;right: 20px;"
                                                        id="add1">
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
                                                        <button class="btn btn-primary btn-sm" style="position: absolute;right: 20px;"
                                                        id="add2">
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
                                                    </div> -->
                                                </div>
                                                <div class="row">
                                                    <div class="text-end pb-3">
                                                        <button class="btn btn-primary btn-sm" id="add">
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
                                                        <button class="btn btn-primary btn-sm" id="add1">
                                                        <span class="btn-label">
                                                            <i class="fa fa-plus"></i>
                                                        </span>
                                                        Add More
                                                        </button>
                                                        <button class="btn btn-primary btn-sm" id="add2">
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
                                                            <tbody class="tbody tbody1 tbody2 material_section_body">

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
                                                        <button class="btn btn-primary waves-effect waves-light me-1" type="submit" id="save1" name="save1">
                                                            Submit
                                                        </button>
                                                        <button class="btn btn-primary waves-effect waves-light me-1" type="submit" id="save2" name="save2">
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
            // MAIN HIDE AND SHOW 
            $(function () {
                $('#plant-dropdown1').hide();
                $('#storage-dropdown1').hide();
                $('#mat_group1').hide();
                $('.table_id').hide();
                $('#add1').hide();
                $('#save1').hide();
                $('#smallSelect').change(function () {
                    if ($('#smallSelect').val() == 'Services') {
                    $('#plant-dropdown1').show();
                    // $('#storage-dropdown1').show();
                    $('.table_id').show();
                    $('#add1').show();
                    $('#add1').prop('disabled',false);
                    $('#save1').show();
                    $('#save1').prop('disabled',false);
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
                $('#mat_group').hide();
                $('.table_id').hide();
                $('#add').hide();
                $('#save').hide();
                $('#smallSelect').change(function () {
                    if ($('#smallSelect').val() == 'Material purchases') {
                    $('#plant-dropdown').show();
                    // $('#storage-dropdown').show();
                    $('.table_id').show();
                    $('#add').show();
                    $('#add').prop('disabled',false);
                    $('#save').show();
                    $('#save').prop('disabled',false);
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
                $('#mat_group2').hide();
                $('.table_id').hide();
                $('#add2').hide();
                $('#save2').hide();
                $('#smallSelect').change(function () {
                    if ($('#smallSelect').val() == 'Asset purchases') {
                    $('#plant-dropdown2').show();
                    // $('#storage-dropdown2').show();
                    $('.table_id').show();
                    $('#add2').show();
                    $('#add2').prop('disabled',false);
                    $('#save2').show();
                    $('#save2').prop('disabled',false);
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
                        $('.storage-dropdowns').each(function(){
                            $(this).next('.select2-container').hide();
                        });

                        $('.material_section_body').empty();
                        sno = 1;

                        $('.mat_group').each(function(){
                            $(this).next('.select2-container').hide();
                        });
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
                            $(".storage-dropdown-label").show();
                            $("#storage-dropdown").show();
                            $(".mat_group_label").hide();
                            $("#mat_group").hide();
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
                        }
                        });
                    });

                    $('#plant-dropdown1').on('change', function () {
                        var Plant_Code = this.value;
                        $('.storage-dropdowns').each(function(){
                            $(this).next('.select2-container').hide();
                        });
                        $('.material_section_body').empty();
                        sno = 1;

                        $('.mat_group').each(function(){
                            $(this).next('.select2-container').hide();
                        });

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
                            $("#storage-dropdown1").html(result);
                            $("#storage-dropdown1").select2();
                            $(".storage-dropdown-label").show();                            
                            $("#storage-dropdown1").show();
                            $(".mat_group_label").hide();
                            $("#mat_group1").hide();
                            $('.items-dropdown1').html('<option value="">Select Item_Code</option>');
                            $('.item-dropdown1').html('<option value="">Select Item_Code</option>');
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
                        }
                        });
                    });

                    $('#plant-dropdown2').on('change', function () {


                        var Plant_Code = this.value;
                        $('.storage-dropdowns').each(function(){
                            $(this).next('.select2-container').hide();
                        });
                        $('.material_section_body').empty();
                        sno = 1;

                        $('.mat_group').each(function(){
                            $(this).next('.select2-container').hide();
                        });

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
                            $("#storage-dropdown2").html(result);
                            $("#storage-dropdown2").select2();
                            $(".storage-dropdown-label").show();
                            $("#storage-dropdown2").show();
                            $(".mat_group_label").hide();
                            $("#mat_group2").hide();
                            $('.items-dropdown2').html('<option value="">Select Item_Code</option>');
                            $('.item-dropdown2').html('<option value="">Select Item_Code</option>');
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
                        }
                        });
                    });

                    $('#storage-dropdown').on('change', function () {
                        var Storage_Location = this.value;
                        var Plant  = $('#plant-dropdown').val();

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
                            // $(".items-dropdown").html(result);
                            // $(".items-dropdown").select2();
                            // $(".item-dropdown").html(result);
                            // $(".item-dropdown").select2();                            
                            // $("#add").val(Storage_Location);
                            // $('#items').show();
                            $('#News').hide();
                            $('.mat_group_label').show();

                            var option = '<option value="">Select Material Group</option>'
                            for(i in result) {
                                option += `<option value="${ result[i].MaterialGroup }">${ result[i].MaterialGroup }</option>`;
                            }
                            $('#mat_group').html(option);
                            $('#mat_group').select2();
                            $('#mat_group').show();
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
                        }
                        });
                    });

                    $('#storage-dropdown1').on('change', function () {
                        var Storage_Location = this.value;
                        var Plant  = $('#plant-dropdown1').val();

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
                            // $(".items-dropdown").html(result);
                            // $(".items-dropdown").select2();
                            // $(".item-dropdown").html(result);
                            // $(".item-dropdown").select2();                            
                            // $("#add").val(Storage_Location);
                            // $('#items').show();
                            $('#News1').hide();
                            $('.mat_group_label').show();

                            var option = '<option value="">Select Material Group</option>'
                            for(i in result) {
                                option += `<option value="${ result[i].MaterialGroup }">${ result[i].MaterialGroup }</option>`;
                            }
                            $('#mat_group1').html(option);
                            $('#mat_group1').select2();
                            $('#mat_group1').show();
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
                        }
                        });
                    });

                    $('#storage-dropdown2').on('change', function () {
                        var Storage_Location = this.value;
                        var Plant  = $('#plant-dropdown2').val();

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
                            // $(".items-dropdown").html(result);
                            // $(".items-dropdown").select2();
                            // $(".item-dropdown").html(result);
                            // $(".item-dropdown").select2();                            
                            // $("#add").val(Storage_Location);
                            // $('#items').show();
                            $('#News2').hide();
                            $('.mat_group_label').show();

                            var option = '<option value="">Select Material Group</option>'
                            for(i in result) {
                                option += `<option value="${ result[i].MaterialGroup }">${ result[i].MaterialGroup }</option>`;
                            }
                            $('#mat_group2').html(option);
                            $('#mat_group2').select2();
                            $('#mat_group2').show();
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
                        }
                        });
                    });

                    // $('#storage-dropdown1').on('change', function () {
                    //     var Storage_Location = this.value;
                    //     var Plant  = $('#plant-dropdown1').val();

                    //     $.ajax({
                    //     url: "service_material.php",
                    //     type: "POST",
                    //     data: {
                    //         Storage_Location: Storage_Location,
                    //         Plant : Plant
                    //     },
                    //     cache: false,                        
                    //     beforeSend:function(){
                    //         $('#ajax-preloader').show();
                    //     },
                    //     success: function (result) {
                    //         $(".items-dropdown1").html(result);
                    //         $(".items-dropdown1").select2();
                    //         $(".item-dropdown1").html(result);
                    //         $(".item-dropdown1").select2();                            
                    //         $("#add1").val(Storage_Location);
                    //     },
                    //     complete:function(){
                    //         $('#ajax-preloader').hide();
                    //     }
                    //     });
                    // });

                    // asset purchase request storage dropdown
                    // $('#storage-dropdown2').on('change', function () {
                    //     var Storage_Location = this.value;
                    //     var Plant  = $('#plant-dropdown2').val();

                    //     $.ajax({
                    //     url: "fetch-item-code.php?employe=<?php echo $Employee_Id ?>",
                    //     type: "POST",
                    //     data: {
                    //         Storage_Location: Storage_Location,
                    //         Plant : Plant
                    //     },
                    //     cache: false,
                    //     beforeSend:function(){
                    //         $('#ajax-preloader').show();
                    //     },
                    //     success: function (result) {
                    //         $(".items-dropdown2").html(result);
                    //         $(".items-dropdown2").select2();
                    //         $(".item-dropdown2").html(result);
                    //         $(".item-dropdown2").select2();                            
                    //         $("#add2").val(Storage_Location);
                    //         $('#items2').show();
                    //         $('#News2').hide();
                    //     },
                    //     complete:function(){
                    //         $('#ajax-preloader').hide();
                    //     }
                    //     });
                    // });


                    $('#mat_group').on('change', function () {
                        var request_category = $('#smallSelect').val();
                        var Storage_Location = '';
                        if(request_category == 'ZCAP') {
                            Storage_Location = $('#storage-dropdown2').val();
                        } else if(request_category == 'ZNB') {
                            Storage_Location = $('#storage-dropdown').val();
                        } else if(request_category == 'ZSER') {
                            Storage_Location = $('#storage-dropdown1').val();
                        }
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
                            $(".items-dropdown1").html(result);
                            $(".items-dropdown1").select2();
                            $(".item-dropdown1").html(result);
                            $(".item-dropdown1").select2();                            
                            $("#add1").val(Storage_Location);
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
                        }
                        });
                    });


                    $('#mat_group1').on('change', function () {
                        var request_category = $('#smallSelect').val();
                        var Storage_Location = '';
                        if(request_category == 'ZCAP') {
                            Storage_Location = $('#storage-dropdown2').val();
                        } else if(request_category == 'ZNB') {
                            Storage_Location = $('#storage-dropdown').val();
                        } else if(request_category == 'ZSER') {
                            Storage_Location = $('#storage-dropdown1').val();
                        }
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
                            $(".items-dropdown1").html(result);
                            $(".items-dropdown1").select2();
                            $(".item-dropdown1").html(result);
                            $(".item-dropdown1").select2();                            
                            $("#add1").val(Storage_Location);
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
                        }
                        });
                    });


                    $('#mat_group2').on('change', function () {
                        var request_category = $('#smallSelect').val();
                        var Storage_Location = '';
                        if(request_category == 'ZCAP') {
                            Storage_Location = $('#storage-dropdown2').val();
                        } else if(request_category == 'ZNB') {
                            Storage_Location = $('#storage-dropdown').val();
                        } else if(request_category == 'ZSER') {
                            Storage_Location = $('#storage-dropdown1').val();
                        }
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
                            $(".items-dropdown1").html(result);
                            $(".items-dropdown1").select2();
                            $(".item-dropdown1").html(result);
                            $(".item-dropdown1").select2();                            
                            $("#add1").val(Storage_Location);
                        },
                        complete:function(){
                            $('#ajax-preloader').hide();
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
                    $(".MaterialGroupda").select2();
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
                    $(".MaterialGroupda2").select2();
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
                    ItemCode_closet.closest('tr').find(".MaterialGroupda2" + state).select2();

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
                    $('.item-dropdown').select2();
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
                    $('.item-dropdown1').select2();

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
                    $('.item-dropdown2').select2();

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
            // END DISABLE
            // ADD MORE

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
                                        <select class="select2 form-control items-dropdown" id="item-dropdown" style="width: 175px;" name="item_code[]" placeholder="Select Name." >
                                        </select>
                                    </div>
                                </td>
                                <td id="divn">
                                    <div class="col-md-12">
                                    <textarea id="description${i}" style="width: 210px;" class="form-control disabled description" name="description[]"   row="2" placeholder="Enter Description" required=""></textarea>

                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled  uom" id="uom${i}" style="width: 110px;"  name="uom[]" placeholder="Enter UOM" >
                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" id="items">
                                        <input type="text" class="form-control  disabled MaterialGroup" style="width: 100px;" id="MaterialGroup${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                    <div class="col-md-12" id="News">
                                        <select class="select2 form-control  MaterialGroupda" id="MaterialGroupda${i}" style="width: 100px;"   name="MaterialGroup[]">
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" style="width: 125px;" name="quantity[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}"style="width: 155px;" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date('Y-m-d'); ?>" name="Expected_Date[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: 155px;height: 36px;"  row="2" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input class="form-control file-upload-input" type="file"  id="Attachment${i}" style="width: 170px;" name="Attachment[]"  placeholder="Enter Quantity" >
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    
                                        <select class="select2 form-control" id="type_val${i}" data-id="${i}"  name="budget[]" style="width: 145px;"  onchange="openPopup(this);" >
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
                                        <select class="select2 form-control items-dropdown1" id="item-dropdown1" style="width: 175px;" name="item_code[]"  placeholder="Select Name.">
                                        </select>
                                    </div>
                                </td>
                                <td id="divn">
                                    <div class="col-md-12">
                                    <textarea id="description1${i}" style="width: 210px;" class="form-control disabled description1" name="description[]"   row="2" placeholder="Enter Description" required=""></textarea>
                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled  uom1" id="uom1${i}" style="width: 110px;"  name="uom[]"  placeholder="Enter UOM">
                                    </div>
                                </td>	
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control  disabled MaterialGroup" style="width: 100px;" id="MaterialGroup${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                </td>			
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" style="width: 125px;" name="quantity[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}" style="width: 155px;" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date('Y-m-d'); ?>" name="Expected_Date[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: 155px;height: 36px;"  rows="2" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input class="form-control file-upload-input" type="file" id="Attachment${i}" style="width: 170px;" name="Attachment[]"  placeholder="Enter Quantity"
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    
                                        <select class="select2 form-control" id="type_val${i}" data-id="${i}" name="budget[]" style="width: 145px;"  onchange="openPopup(this);" >
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
                    console.log(i);

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
                                    `+ (sno) + `
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <select class="select2 form-control items-dropdown2" id="item-dropdown2" style="width: 175px;" name="item_code[]"  placeholder="Select Name.">
                                        </select>
                                    </div>
                                </td>
                                <td id="divn">
                                    <div class="col-md-12">
                                    <textarea id="description2${i}" style="width: 210px;" class="form-control disabled description2" name="description[]"   row="2" placeholder="Enter Description" required=""></textarea>
                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled  uom2" id="uom2${i}" style="width: 110px;"  name="uom[]"  placeholder="Enter UOM">
                                    </div>
                                </td>	
                                <td id="divb">
                                <div class="col-md-12" id="items2">
                                        <input type="text" class="form-control  disabled MaterialGroup2" style="width: 100px;" id="MaterialGroup2${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                    <div class="col-md-12" id="News2">
                                        <select class="select2 form-control   MaterialGroupda2" id="MaterialGroupda2${i}" style="width: 100px;"  name="MaterialGroup[]">
                                        </select>
                                    </div>
                                </td>				
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" style="width: 125px;" name="quantity[]"   placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}" min="<?php echo date("Y-m-d"); ?>" style="width: 155px;" value="<?php echo date('Y-m-d'); ?>" name="Expected_Date[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: 155px;height: 36px;"  rows="2" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input class="form-control file-upload-input" type="file" id="Attachment${i}" style="width: 170px;" name="Attachment[]"  placeholder="Enter Quantity">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    
                                        <select class="select2 form-control" id="type_val${i}" data-id="${i}" name="budget[]" style="width: 145px;"  onchange="openPopup(this);" >
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
                    sno++;
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
                                        <select class="select2 form-control item-dropdown" id="item-dropdown${i}" style="width: 175px;"  data-id="${i}" name="item_code[]" placeholder="Select Name.">
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <textarea id="description${i}" style="width: 210px;" class="form-control disabled1${i} description" name="description[]"   row="2" placeholder="Enter Description" required=""></textarea>

                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled1${i} uom" id="uom${i}" style="width: 110px;"  name="uom[]" placeholder="Enter UOM">
                                    </div>
                                </td>
                                <td id="div_item">
                                    <div class="col-md-12" id="item${i}">
                                        <input type="text" class="form-control  disabled1${i} MaterialGroup${i}" style="width: 100px;" id="MaterialGroup${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                    <div class="col-md-12" id="New${i}">
                                        <select class="select2 form-control  MaterialGroupda${i}" id="MaterialGroupda${i}" style="width: 100px;"   name="MaterialGroup[]">
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" style="width: 125px;" name="quantity[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}" min="<?php echo date("Y-m-d"); ?>" style="width: 155px;" value="<?php echo date('Y-m-d'); ?>" name="Expected_Date[]"  placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: 155px;height: 36px;"  rows="2" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input class="form-control file-upload-input" type="file" id="Attachment${i}" style="width: 170px;" name="Attachment[]"  placeholder="Enter Quantity" >
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <select class="select2 form-control" id="type_val1${i}" data-id="${i}" name="budget[]" style="width: 145px;"  onchange="openPopup(this);" >
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
                                        <select class="select2 form-control item-dropdown1" id="item-dropdown1${i}"  style="width: 175px;" data-id="${i}" name="item_code[]" placeholder="Select Name.">
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <textarea id="description1${i}" style="width: 210px;" class="form-control disabled1${i} description1" name="description[]"   row="2" placeholder="Enter Description" required=""></textarea>

                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control disabled1${i} uom1" id="uom1${i}"  style="width: 110px;"  name="uom[]" placeholder="Enter UOM">
                                    </div>
                                </td>
                                <td id="divb">
                                    <div class="col-md-12" >
                                        <input type="text" class="form-control  disabled1${i} MaterialGroup" id="MaterialGroup${i}" style="width: 100px;"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="number" min="0" class="form-control" id="quantity${i}" name="quantity[]" style="width: 125px;" placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                        <input type="date" class="form-control" id="Expected_Date${i}" min="<?php echo date("Y-m-d"); ?>" name="Expected_Date[]" value="<?php echo date('Y-m-d'); ?>" style="width: 155px;" placeholder="Enter Quantity" required="">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: 155px;height: 36px;" placeholder="Enter Specification" required=""></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <input class="form-control file-upload-input" type="file" id="Attachment${i}" style="width: 170px;" name="Attachment[]"  placeholder="Enter Quantity">
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-12">
                                    <select class="select2 form-control" id="type_val1${i}" data-id="${i}" name="budget[]" style="width: 145px;"  onchange="openPopup(this);" >
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
                                `+ (sno) + `
                            </td>
                            <td>
                                <div class="col-md-12" >
                                <select class="select2 form-control item-dropdown2" id="item-dropdown2${i}" style="width: 175px;" data-id="${i}"  name="item_code[]" placeholder="Select Name.">
                                </select>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                <textarea id="description2${i}" style="width: 210px;" class="form-control disabled1${i} description2" name="description[]"   row="2" placeholder="Enter Description" required=""></textarea>

                                </div>
                            </td>
                            <td>
                                <div class="col-md-12" >
                                <input type="text" class="form-control disabled1${i} uom2" id="uom2${i}" style="width: 110px;"  name="uom[]" placeholder="Enter UOM">
                                </div>
                            </td>
                            <td id="divb">
                            <div class="col-md-12" id="item2${i}">
                                        <input type="text" class="form-control  disabled1${i} MaterialGroup2${i}" style="width: 100px;" id="MaterialGroup2${i}"   name="MaterialGroup[]" placeholder="Enter MaterialGroup">
                                    </div>
                                    <div class="col-md-12" id="New2${i}">
                                        <select class="select2 form-control  MaterialGroupda2${i}" id="MaterialGroupda2${i}" style="width: 100px;"  name="MaterialGroup[]">
                                        </select>
                                    </div>
                                </td>
                            <td>
                                <div class="col-md-12">
                                <input type="number" min="0" class="form-control" id="quantity${i}"  name="quantity[]" style="width: 125px;" placeholder="Enter Quantity" required="">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                <input type="date" class="form-control" id="Expected_Date${i}" min="<?php echo date("Y-m-d"); ?>" value="<?php echo date('Y-m-d'); ?>"  name="Expected_Date[]" style="width: 155px;" placeholder="Enter Quantity" required="">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                                    <textarea id="Specification${i}" class="form-control" name="Specification[]" style="width: 155px;height: 36px;" placeholder="Enter Specification" required=""></textarea>
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                    <input class="form-control file-upload-input" type="file" id="Attachment${i}" name="Attachment[]" style="width: 170px;"  placeholder="Enter Quantity">
                                </div>
                            </td>
                            <td>
                                <div class="col-md-12">
                                <select class="select2 form-control" id="type_val1${i}" data-id="${i}" name="budget[]" style="width: 145px;"  onchange="openPopup(this);" >
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
                    sno++;
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

            $(document).on('change', '#smallSelect', function () {
                var request_type = $(this).val();
                $('.plant-dropdowns').each(function(){
                    $(this).next('.select2-container').hide();
                });

                $('.storage-dropdowns').each(function(){
                    $(this).next('.select2-container').hide();
                });

                $('.mat_group').each(function(){
                    $(this).next('.select2-container').hide();
                });

                $('.material_section_body').empty();
                sno = 1;
                $(".storage-dropdown-label").hide();
                $(".mat_group_label").hide();


                // request type document no set 
                var request_type_code = (request_type == 'Asset purchases') ? 'ZCAP' : ((request_type == 'Material purchases') ? 'ZNB' : 'ZSER');

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

                        if(request_type == 'Asset purchases') {
                            plant_selectbox_id = 'plant-dropdown2';
                        } else if(request_type == 'Material purchases') {
                            plant_selectbox_id = 'plant-dropdown';
                        } else if(request_type == 'Services') {
                            plant_selectbox_id = 'plant-dropdown1';
                        }


                        $('#'+plant_selectbox_id).html(option);
                        $('#'+plant_selectbox_id).select2();
                        $('.plant-dropdown-label').show();
                        $('#'+plant_selectbox_id).show();


                    },
                    complete:function() {
                        $('#ajax-preloader').hide();
                    }
                });
            });



            // END MORE 
        </script>
        <!-- CUSTOM JS END -->
    </body>
</html>
