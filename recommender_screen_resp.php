<?php
include('../auto_load.php');
include('adition.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

require 'Send_Mail.php';
$mail = new Send_Mail();

if(!isset($_SESSION['EmpID']))
{
    ?>
<script type="text/javascript">
   window.location = "../pages/indexAdmin.php";
</script>
<?php
}
$request_id = $_GET['id'];

$Employee_Id = $_SESSION['EmpID'];

$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
WHERE Recommender = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$Approver_Code = $selector_arr1['Approver'];
$verifier_name = $selector_arr1['Finance_Verfier'];
$verifier_code = $selector_arr1['Finance_Verfier'];
$Purchaser_Code = $selector_arr1['Purchaser'];
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];


$vendor_data_count_sql =  "SELECT COUNT(*) as data_count from Tb_Vendor_Selection where Request_Id = '".$request_id."'";

$vendor_data_count_exec = sqlsrv_query($conn, $vendor_data_count_sql);
$vendor_data_count = sqlsrv_fetch_array($vendor_data_count_exec)['data_count'];
$table_width = '100%';
if($vendor_data_count == 1) {
    $table_width = "100px";
}

if (isset($_POST["save"])) {
        $_POST = array_map(function($value) { 
             if(is_array($value)){
                     $mArr = array_map(function($value1) { 
                         return str_replace("'","''", $value1); 
                     },$value);
                     return $mArr;
             }else{
                 return str_replace("'","''", $value); 
             }
        }, $_POST);


    //approval mapping id insertion
    $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set approval_mapping_id = '".$_POST['mapping_id']."' WHERE Request_id = '$request_id' ");

    $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
    if($updated_query['Request_id'] == ''){
        for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
            $Vendor_SAP = $_POST['Vendor_SAP'][$i];
            $Vendor_Name = $_POST['Vendor_Name'][$i];
            $Vendor_City = $_POST['Vendor_City'][$i];
            $vendor_Active_SAP = $_POST['vendor_Active_SAP'][$i];
            $Last_Purchase = $_POST['Last_Purchase'][$i];
            $Delivery_Time = $_POST['Delivery_Time'][$i];
            $Value_Of = $_POST['Value_Of'][$i];
            $Fright_Charges = $_POST['Fright_Charges'][$i];
            $Insurance_Details = $_POST['Insurance_Details'][$i];
            $GST_Component = $_POST['GST_Component'][$i];
            $Warrenty = $_POST['Warrenty'][$i];
            $Payment_Terms = $_POST['Payment_Terms'][$i];
            $Requester_Selection = $_POST['Requester_Selection'][$i];
            $Recommender_Selection = $_POST['Recommender_Selection'][$i];
            $Requester_Remarks = $_POST['Requester_Remarks'][$i];
            $Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
            // $Finance_Verification = $_POST['finance_verification'][$i];
            if(!isset($_POST['Attachment'])){
                $fil="";
            }else{
                  $fil=$_POST['Attachment'][$i];
            }
            // $fil = $_POST["Attachment"][$i];
            $V_id = $_POST['V1_id'][$i];
            $emp_id = $Employee_Id;
            $approver_id = $_POST['approver_id'];
            // $Requested_to = $Approver_Code;
            $Requested_to = $approver_id;

            $status = 'Recommended';
            foreach ($_POST['finance_verification'] as $key => $value) {
                if($value == 'Yes') {
                    $status = 'Review';
                }else{
                    $value == 'Verified';
                    $status = 'Recommended';
                }
            }


                $total_amount    = $_POST['amt_tot'][$i];
                $discount_amount = $_POST['discount_amount'][$i];
                $package_amount = $_POST['package_amount'][$i];
                $package_percentage = $_POST['package_percentage'][$i];


                $query = "INSERT INTO Tb_Recommender 
                (Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time1,
                Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
                Recommender_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,Finance_Verification,EMP_ID,Requested_to,total_amount,discount_amount,package_amount,package_percentage)  OUTPUT inserted.Request_Id VALUES 
                ('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
                '$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
                '$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks',
                '$fil',GETDATE(),'$status','$Requester_Selection','$Recommender_Selection','$value','$emp_id','$Requested_to','$total_amount','$discount_amount','$package_amount','$package_percentage')";

                $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status',Approver = '$Requested_to' WHERE Request_Id = '$request_id' ");
                $query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status',Approver = '$Requested_to' WHERE Request_Id = '$request_id' ");

                $rs = sqlsrv_query($conn, $query);

                $last_request_id = sqlsrv_fetch_array($rs)['Request_Id'];


                // $approver_sql =  "SELECT Tb_Master_Emp.Approver from Tb_Request
                // INNER JOIN Tb_Recommender ON Tb_Recommender.Request_id = Tb_Request.Request_ID and Tb_Recommender.Recommender_Selection = '1'
                // INNER JOIN Tb_Master_Emp ON Tb_Master_Emp.Recommender = Tb_Recommender.EMP_ID and Tb_Master_Emp.Value != '' and  Tb_Recommender.Value_Of > Tb_Master_Emp.Value
                // INNER JOIN MaterialMaster ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group AND MaterialMaster.Plant = Tb_Master_Emp.Plant
                // where Tb_Request.Request_ID = '".$last_request_id."'"; 

                // $approver_sql_exec = sqlsrv_query($conn, $approver_sql);

                // $approver_code = sqlsrv_fetch_array($approver_sql_exec)['Approver'];

                // $update_approver = sqlsrv_query($conn, "UPDATE Tb_Recommender set Requested_to = '$approver_code' WHERE Tb_Recommender.Request_id = '".$last_request_id."'");


        }

            
    } else{
        for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
            $Vendor_SAP = $_POST['Vendor_SAP'][$i];
            $Vendor_Name = $_POST['Vendor_Name'][$i];
            $Vendor_City = $_POST['Vendor_City'][$i];
            $vendor_Active_SAP = $_POST['vendor_Active_SAP'][$i];
            $Last_Purchase = $_POST['Last_Purchase'][$i];
            $Delivery_Time = $_POST['Delivery_Time'][$i];
            $Value_Of = $_POST['Value_Of'][$i];
            $Fright_Charges = $_POST['Fright_Charges'][$i];
            $Insurance_Details = $_POST['Insurance_Details'][$i];
            $GST_Component = $_POST['GST_Component'][$i];
            $Warrenty = $_POST['Warrenty'][$i];
            $Payment_Terms = $_POST['Payment_Terms'][$i];
            $Requester_Selection = $_POST['Requester_Selection'][$i];
            $Recommender_Selection = $_POST['Recommender_Selection'][$i];
            $Requester_Remarks = $_POST['Requester_Remarks'][$i];
            $Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
            if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
            // $fil = $_POST["Attachment"][$i];
            $V_id = $_POST['V1_id'][$i];
            $emp_id = $Employee_Id;
            $Requested_to = $_POST['approver_id'];     
            $status = 'Recommended';
            foreach ($_POST['finance_verification'] as $key => $value) {
            if($value == 'Yes') {
                $status = 'Review';
            }else{
                $value == 'Verified';
                $status = 'Recommended';
            }
            }

             $query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set status = '$status',Recommender_Remarks = '$Recommender_Remarks',Finance_Verification  = '$value'
            ,Recommender_Selection = '$Recommender_Selection'  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

            $query2 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status',Approver = '$Requested_to' WHERE Request_Id = '$request_id' ");
            $query3 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status',Approver = '$Requested_to' WHERE Request_Id = '$request_id' ");

        }
      
    }

    if($updated_query['Request_id'] == ''){
        for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

            $Quantity_Details = $_POST['Quantity_Details'][$i];
            $Meterial_Name = $_POST['Meterial_Name'][$i];
            $Price = $_POST['Price'][$i];
            $Total = $_POST['Total'][$i];
            $V_id = $_POST['V_id'][$i];
            $gst_percentage      = $_POST['gst_percent'][$i];
            $discount_percentage = $_POST['discount_percent'][$i];

            $meterial = "INSERT INTO Tb_Recommender_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID,Requested_to,gst_percentage,discount_percentage) VALUES 
            ('$request_id','$Meterial_Name','$Quantity_Details','$status','$Price','$Total','$V_id','$emp_id','$Requested_to','$gst_percentage','$discount_percentage')";

            $rs_material = sqlsrv_query($conn, $meterial);
            
        }
    }else{

        for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {
            $Quantity_Details = $_POST['Quantity_Details'][$i];
            $Meterial_Name = $_POST['Meterial_Name'][$i];
            $Price = $_POST['Price'][$i];
            $Total = $_POST['Total'][$i];
            $V_id = $_POST['V_id'][$i];

            $query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender_Meterial set status = '$status' WHERE V_id = '$V_id' and Request_id = '$request_id'");
                
        }
    }

    if($updated_query['Status'] == 'Review'){

        $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items 
        ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id WHERE Tb_Request.Request_ID = '$request_id'");
        $updated_query = sqlsrv_fetch_array($update_qry);
        $PERSION =  $updated_query['Persion_In_Workflow'];
        // print_r($PERSION);exit;
        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
        $idss = array();
        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids['Office_Email_Address'];
        }
        $implode = implode(',', $idss);

        $update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$verifier_code'");
        $updated_query1 = sqlsrv_fetch_array($update_qry1);
        $To =  $updated_query1['Office_Email_Address'];

        // $update_qry12 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Recommender_Code'");
        // $updated_query12 = sqlsrv_fetch_array($update_qry12);
        // $Cc =  $updated_query12['Office_Email_Address'];

        // $to = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');                             
        $to = explode(',', $To);

        // informer mail cc
        $cc = ($implode != '') ? explode(',', $implode) : array();

        $bcc = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

        $subject = $emp_id.' - Purchase Request Review';

        $mail_template = '<html>
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
                                <th class="text-center">Request ID</th>
                                <th class="text-center">Department</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Plant</th>
                                <th class="text-center">Meterial</th>
                                <th class="text-center">Quantity</th>                          
                                <th class="text-center">Status</th>                          
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    1
                                </td>
                                <td>
                                    ' . $request_id . '
                                </td>
                                <td>
                                    ' . $updated_query['Department'] . '
                                </td>
                                <td>
                                    ' . $updated_query['Request_Type'] . '
                                </td>
                                <td>
                                    ' . $updated_query['Plant'] . '
                                </td>
                                <td>
                                    '.$updated_query['Description'].'(' . $updated_query['Item_Code'] . ')
                                </td>
                                <td>
                                    ' . $updated_query['Quantity'] . '
                                </td>
                                <td>
                                <h4><span class="badge badge-success"><i class="fa fa-check"></i>Finance Verification</span></h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </body>
        </html>';

        $process_mail = $mail->Send_Mail_Details($subject,'','',$mail_template,$to,$cc,$bcc);

        if (!$process_mail) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
          }else{
           
            ?>
            <script type="text/javascript">
                alert("Recommended successsfully");
                window.location = "show_recommender.php";
            </script>
            <?php
          }
    }else{
        $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items 
        ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id WHERE Tb_Request.Request_ID = '$request_id'");
        $updated_query = sqlsrv_fetch_array($update_qry);
        $PERSION =  $updated_query['Persion_In_Workflow'];

        $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
        $idss = array();
        while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
            $idss[] = $ids['Office_Email_Address'];
        }
        $implode = implode(',', $idss);
        
        // recommender mail id added in cc
        $recom_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$emp_id'");
        $recom_qry1 = sqlsrv_fetch_array($recom_qry1);
        $addition_cc =  $recom_qry1['Office_Email_Address'];

        $implode = $implode.','.$addition_cc; 

        $update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Requested_to'");
        $updated_query1 = sqlsrv_fetch_array($update_qry1);
        $To =  $updated_query1['Office_Email_Address'];

        // $update_qry12 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Recommender_Code'");
        // $updated_query12 = sqlsrv_fetch_array($update_qry12);
        // $Cc =  $updated_query12['Office_Email_Address'];

        // $to = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');
        $to = explode(',', $To);

        // informer mail cc
        $cc = ($implode != '') ? explode(',', $implode) : array();



        $bcc = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

        $mail_template = '<html>
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
                                <th class="text-center">Request ID</th>
                                <th class="text-center">Department</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Plant</th>
                                <th class="text-center">Meterial</th>
                                <th class="text-center">Quantity</th>                          
                                <th class="text-center">Status</th>                          
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    1
                                </td>
                                <td>
                                    ' . $request_id . '
                                </td>
                                <td>
                                    ' . $updated_query['Department'] . '
                                </td>
                                <td>
                                    ' . $updated_query['Request_Type'] . '
                                </td>
                                <td>
                                    ' . $updated_query['Plant'] . '
                                </td>
                                <td>'
                                    .$updated_query['Description'].'(' . $updated_query['Item_Code'] . ')
                                </td>
                                <td>
                                    ' . $updated_query['Quantity'] . '
                                </td>
                                <td>
                                <h4><span class="badge badge-success"><i class="fa fa-check"></i>Recommended</span></h4>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </body>
        </html>';
        
        $subject = $emp_id.' - Purchase Request Recommendation';

        $process_mail = $mail->Send_Mail_Details($subject,'','',$mail_template,$to,$cc,$bcc);

        if (!$process_mail) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
          }else{
            ?>
            <script type="text/javascript">
                alert("Recommended successsfully");
                window.location = "show_recommender.php";
            </script>
            <?php
          }
    }
}
// send 
if (isset($_POST["send"])) {

    $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
    if($updated_query['Request_id'] == ''){

        for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
            $Vendor_SAP = $_POST['Vendor_SAP'][$i];
            $Vendor_Name = $_POST['Vendor_Name'][$i];
            $Vendor_City = $_POST['Vendor_City'][$i];
            $vendor_Active_SAP = $_POST['vendor_Active_SAP'][$i];
            $Last_Purchase = $_POST['Last_Purchase'][$i];
            $Delivery_Time = $_POST['Delivery_Time'][$i];
            $Value_Of = $_POST['Value_Of'][$i];
            $Fright_Charges = $_POST['Fright_Charges'][$i];
            $Insurance_Details = $_POST['Insurance_Details'][$i];
            $GST_Component = $_POST['GST_Component'][$i];
            $Warrenty = $_POST['Warrenty'][$i];
            $Payment_Terms = $_POST['Payment_Terms'][$i];
            $Requester_Selection = $_POST['Requester_Selection'][$i];
            $Recommender_Selection = $_POST['Recommender_Selection'][$i];
            $Requester_Remarks = $_POST['Requester_Remarks'][$i];
            $Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
            if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
            $V_id = $_POST['V1_id'][$i];
            $emp_id = $Employee_Id;
            $Requested_to = $Approver_Code;

            for ($j = 0; $j < count($_POST['Recommender_back_remark']); $j++) {
                $Recommender_back_remark = $_POST['Recommender_back_remark'][$j];

                $query = "INSERT INTO Tb_Recommender
                (Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time1,
                Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
                Recommender_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,Recommender_back_remark,EMP_ID,Requested_to)VALUES
                ('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
                '$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
                '$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks',
                '$fil',GETDATE(),'Recommender Send Back','$Requester_Selection','$Recommender_Selection','$Recommender_back_remark','$emp_id','$Requested_to')";

                $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Recommender Send Back', Recommender_back_remark = '$Recommender_back_remark' WHERE Request_Id = '$request_id' ");
                $query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Recommender Send Back', Recommender_back_remark = '$Recommender_back_remark' WHERE Request_Id = '$request_id' ");

                $rs = sqlsrv_query($conn, $query);
            }
        }
        ?>
        <script type="text/javascript">
            alert("Send Back successsfully");
            window.location = "show_recommender.php";
        </script>
        <?php

    } else{
        for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
            $Vendor_SAP = $_POST['Vendor_SAP'][$i];
            $Vendor_Name = $_POST['Vendor_Name'][$i];
            $Vendor_City = $_POST['Vendor_City'][$i];
            $vendor_Active_SAP = $_POST['vendor_Active_SAP'][$i];
            $Last_Purchase = $_POST['Last_Purchase'][$i];
            $Delivery_Time = $_POST['Delivery_Time'][$i];
            $Value_Of = $_POST['Value_Of'][$i];
            $Fright_Charges = $_POST['Fright_Charges'][$i];
            $Insurance_Details = $_POST['Insurance_Details'][$i];
            $GST_Component = $_POST['GST_Component'][$i];
            $Warrenty = $_POST['Warrenty'][$i];
            $Payment_Terms = $_POST['Payment_Terms'][$i];
            $Requester_Selection = $_POST['Requester_Selection'][$i];
            $Recommender_Selection = $_POST['Recommender_Selection'][$i];
            $Requester_Remarks = $_POST['Requester_Remarks'][$i];
            $Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
            if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
            $V_id = $_POST['V1_id'][$i];
            $emp_id = $Employee_Id;
            $Requested_to = $Approver_Code;     
            for ($j = 0; $j < count($_POST['Recommender_back_remark']); $j++) {
                $Recommender_back_remark = $_POST['Recommender_back_remark'][$j];

                 $query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set Status = 'Recommender Send Back',Recommender_Remarks = '$Recommender_Remarks',
                 Recommender_Selection = '$Recommender_Selection', Recommender_back_remark = '$Recommender_back_remark'  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

                $query2 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Recommender Send Back', Recommender_back_remark = '$Recommender_back_remark' WHERE Request_Id = '$request_id' ");
                $query3 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Recommender Send Back', Recommender_back_remark = '$Recommender_back_remark' WHERE Request_Id = '$request_id' ");
            }
        }
        ?>
        <script type="text/javascript">
            alert("Send Back successsfully");
            window.location = "show_recommender.php";
        </script>
        <?php
    }

    if($updated_query['Request_id'] == ''){
        for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

            $Quantity_Details = $_POST['Quantity_Details'][$i];
            $Meterial_Name = $_POST['Meterial_Name'][$i];
            $Price = $_POST['Price'][$i];
            $Total = $_POST['Total'][$i];
            $V_id = $_POST['V_id'][$i];

            for ($j = 0; $j < count($_POST['Recommender_back_remark']); $j++) {
                $Recommender_back_remark = $_POST['Recommender_back_remark'][$j];

                $meterial = "INSERT INTO Tb_Recommender_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID,Recommender_back_remark) VALUES 
                ('$request_id','$Meterial_Name','$Quantity_Details','Recommender Send Back','$Price','$Total','$V_id','$emp_id','$Recommender_back_remark')";

                $rs_material = sqlsrv_query($conn, $meterial);
                if ($rs_material) {
                    ?>
                    <script type="text/javascript">
                        alert("Send Back successsfully");
                        window.location = "show_recommender.php";
                    </script>
                    <?php
                }
            }
        }
    } else{

        for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {
            $Quantity_Details = $_POST['Quantity_Details'][$i];
            $Meterial_Name = $_POST['Meterial_Name'][$i];
            $Price = $_POST['Price'][$i];
            $Total = $_POST['Total'][$i];
            $V_id = $_POST['V_id'][$i];
            for ($j = 0; $j < count($_POST['Recommender_back_remark']); $j++) {
                $Recommender_back_remark = $_POST['Recommender_back_remark'][$j];

                $query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender_Meterial set Status = 'Recommender Send Back',Recommender_back_remark = '$Recommender_back_remark' WHERE V_id = '$V_id' and Request_id = '$request_id' ");

                ?>
                <script type="text/javascript">
                    alert("Send Back successsfully");
                    window.location = "show_recommender.php";
                </script>
                <?php
            }
        }
    }
}


//reject
if (isset($_POST["reject"])) {

    $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
    if($updated_query['Request_id'] == ''){

        for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
            $Vendor_SAP = $_POST['Vendor_SAP'][$i];
            $Vendor_Name = $_POST['Vendor_Name'][$i];
            $Vendor_City = $_POST['Vendor_City'][$i];
            $vendor_Active_SAP = $_POST['vendor_Active_SAP'][$i];
            $Last_Purchase = $_POST['Last_Purchase'][$i];
            $Delivery_Time = $_POST['Delivery_Time'][$i];
            $Value_Of = $_POST['Value_Of'][$i];
            $Fright_Charges = $_POST['Fright_Charges'][$i];
            $Insurance_Details = $_POST['Insurance_Details'][$i];
            $GST_Component = $_POST['GST_Component'][$i];
            $Warrenty = $_POST['Warrenty'][$i];
            $Payment_Terms = $_POST['Payment_Terms'][$i];
            $Requester_Selection = $_POST['Requester_Selection'][$i];
            $Recommender_Selection = $_POST['Recommender_Selection'][$i];
            $Requester_Remarks = $_POST['Requester_Remarks'][$i];
            $Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
            if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
            $V_id = $_POST['V1_id'][$i];
            $emp_id = $Employee_Id;
            $Requested_to = $Approver_Code;

            for ($j = 0; $j < count($_POST['Recommender_Rejected']); $j++) {
                $Recommender_Rejected = $_POST['Recommender_Rejected'][$j];

                $query = "INSERT INTO Tb_Recommender
                (Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time1,
                Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
                Recommender_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,Recommender_Rejected,EMP_ID,Requested_to)VALUES
                ('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
                '$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
                '$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks',
                '$fil',GETDATE(),'Recommender_Rejected','$Requester_Selection','$Recommender_Selection','$Recommender_Rejected','$emp_id','$Requested_to')";

                $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Recommender_Rejected', recommender_reject = '$Recommender_Rejected' WHERE Request_Id = '$request_id' ");
                $query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Recommender_Rejected', recommender_reject = '$Recommender_Rejected' WHERE Request_Id = '$request_id' ");

                $rs = sqlsrv_query($conn, $query);
            }
        }

    } else {
        for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
            $Vendor_SAP = $_POST['Vendor_SAP'][$i];
            $Vendor_Name = $_POST['Vendor_Name'][$i];
            $Vendor_City = $_POST['Vendor_City'][$i];
            $vendor_Active_SAP = $_POST['vendor_Active_SAP'][$i];
            $Last_Purchase = $_POST['Last_Purchase'][$i];
            $Delivery_Time = $_POST['Delivery_Time'][$i];
            $Value_Of = $_POST['Value_Of'][$i];
            $Fright_Charges = $_POST['Fright_Charges'][$i];
            $Insurance_Details = $_POST['Insurance_Details'][$i];
            $GST_Component = $_POST['GST_Component'][$i];
            $Warrenty = $_POST['Warrenty'][$i];
            $Payment_Terms = $_POST['Payment_Terms'][$i];
            $Requester_Selection = $_POST['Requester_Selection'][$i];
            $Recommender_Selection = $_POST['Recommender_Selection'][$i];
            $Requester_Remarks = $_POST['Requester_Remarks'][$i];
            $Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
            if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
            $V_id = $_POST['V1_id'][$i];
            $emp_id = $Employee_Id;
            $Requested_to = $Approver_Code;     
            for ($j = 0; $j < count($_POST['Recommender_Rejected']); $j++) {
                $Recommender_Rejected = $_POST['Recommender_Rejected'][$j];

                 $query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set Status = 'Recommender_Rejected',Recommender_Remarks = '$Recommender_Remarks',
                 Recommender_Selection = '$Recommender_Selection', Recommender_Rejected = '$Recommender_Rejected'  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

                $query2 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Recommender_Rejected', recommender_reject = '$Recommender_Rejected' WHERE Request_Id = '$request_id' ");
                $query3 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Recommender_Rejected', recommender_reject = '$Recommender_Rejected' WHERE Request_Id = '$request_id' ");

            }
        }

    }

    if($updated_query['Request_id'] == ''){
        for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

            $Quantity_Details = $_POST['Quantity_Details'][$i];
            $Meterial_Name = $_POST['Meterial_Name'][$i];
            $Price = $_POST['Price'][$i];
            $Total = $_POST['Total'][$i];
            $V_id = $_POST['V_id'][$i];

            for ($j = 0; $j < count($_POST['Recommender_Rejected']); $j++) {
                $Recommender_Rejected = $_POST['Recommender_Rejected'][$j];

                $meterial = "INSERT INTO Tb_Recommender_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID,Recommender_Rejected) VALUES 
                ('$request_id','$Meterial_Name','$Quantity_Details','Recommender_Rejected','$Price','$Total','$V_id','$emp_id','$Recommender_Rejected')";

                $rs_material = sqlsrv_query($conn, $meterial);
            }
        }
    }else{

        for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {
            $Quantity_Details = $_POST['Quantity_Details'][$i];
            $Meterial_Name = $_POST['Meterial_Name'][$i];
            $Price = $_POST['Price'][$i];
            $Total = $_POST['Total'][$i];
            $V_id = $_POST['V_id'][$i];
            for ($j = 0; $j < count($_POST['Recommender_Rejected']); $j++) {
                $Recommender_Rejected = $_POST['Recommender_Rejected'][$j];

                $query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender_Meterial set Status = 'Recommender_Rejected',Recommender_Rejected = '$Recommender_Rejected' WHERE V_id = '$V_id' and Request_id = '$request_id' ");

            }
        }

    }

    $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items 
    ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id WHERE Tb_Request.Request_ID = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
    $PERSION =  $updated_query['Persion_In_Workflow'];

    $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
    $idss = array();
    while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
        $idss[] = $ids['Office_Email_Address'];
    }
    $implode = implode(',', $idss);

    $update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '".$updated_query['Requested_to']."'");
    $updated_query1 = sqlsrv_fetch_array($update_qry1);
    $To =  $updated_query1['Office_Email_Address'];

    $to = explode(',', $To);

    // informer mail cc
    $cc = ($implode != '') ? explode(',', $implode) : array();

    $bcc = array('jr_developer4@mazenetsolution.com','sathish.r@rasiseeds.com');

    $mail_template = '<html>
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
                            <th class="text-center">Request ID</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Plant</th>
                            <th class="text-center">Meterial</th>
                            <th class="text-center">Quantity</th>                          
                            <th class="text-center">Status</th>                          
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                1
                            </td>
                            <td>
                                ' . $request_id . '
                            </td>
                            <td>
                                ' . $updated_query['Department'] . '
                            </td>
                            <td>
                                ' . $updated_query['Request_Type'] . '
                            </td>
                            <td>
                                ' . $updated_query['Plant'] . '
                            </td>
                            <td>
                                ' . $updated_query['Item_Code'] . '
                            </td>
                            <td>
                                ' . $updated_query['Quantity'] . '
                            </td>
                            <td>
                            <h4><span class="badge badge-danger"><i class="fa fa-check"></i>Rejected</span></h4>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </body>
    </html>';
    
    $subject = $emp_id.' - Purchase Request Rejected';

    $process_mail = $mail->Send_Mail_Details($subject,'','',$mail_template,$to,$cc,$bcc);

    if (!$process_mail) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
      }else{
        ?>
        <script type="text/javascript">
            alert("Rejected successsfully");
            window.location = "show_recommender.php";
        </script>
        <?php
      }
}

if (isset($_POST["Verification"])) {
    $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
    // if($updated_query['Request_id'] == ''){

        for ($i = 0; $i < count($_POST['Vendor_SAP']); $i++) {
            $Vendor_SAP = $_POST['Vendor_SAP'][$i];
            $Vendor_Name = $_POST['Vendor_Name'][$i];
            $Vendor_City = $_POST['Vendor_City'][$i];
            $vendor_Active_SAP = $_POST['vendor_Active_SAP'][$i];
            $Last_Purchase = $_POST['Last_Purchase'][$i];
            $Delivery_Time = $_POST['Delivery_Time'][$i];
            $Value_Of = $_POST['Value_Of'][$i];
            $Fright_Charges = $_POST['Fright_Charges'][$i];
            $Insurance_Details = $_POST['Insurance_Details'][$i];
            $GST_Component = $_POST['GST_Component'][$i];
            $Warrenty = $_POST['Warrenty'][$i];
            $Payment_Terms = $_POST['Payment_Terms'][$i];
            $Requester_Selection = $_POST['Requester_Selection'][$i];
            $Recommender_Selection = $_POST['Recommender_Selection'][$i];
            $Requester_Remarks = $_POST['Requester_Remarks'][$i];
            $Recommender_Remarks = $_POST['Recommender_Remarks'][$i];
            if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
            $V_id = $_POST['V1_id'][$i];
            $emp_id = $Employee_Id;
            $Requested_to = $Approver_Code;

            $status = 'Review';
            $Verification_Type = '1';
            for ($k = 0; $k < count($_POST['finance_verification']); $k++) {
            $Finance_Verification = $_POST['finance_verification'][$k];


                $query = "INSERT INTO Tb_Recommender
                (Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time1,
                Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
                Recommender_Remarks,Attachment,Time_Log,Status,Requester_Selection,Verification_Type,Recommender_Selection,Finance_Verification,EMP_ID,Requested_to)VALUES
                ('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
                '$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
                '$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks',
                '$fil',GETDATE(),'$status','$Requester_Selection','$Verification_Type','$Recommender_Selection','$Finance_Verification','$emp_id','$Requested_to')";

                $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status',Approver = '$Requested_to' WHERE Request_Id = '$request_id' ");
                $query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status',Approver = '$Requested_to' WHERE Request_Id = '$request_id' ");

                $rs = sqlsrv_query($conn, $query);
        }
    }

    for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

        $Quantity_Details = $_POST['Quantity_Details'][$i];
        $Meterial_Name = $_POST['Meterial_Name'][$i];
        $Price = $_POST['Price'][$i];
        $Total = $_POST['Total'][$i];
        $V_id = $_POST['V_id'][$i];
      
        
        $meterial = "INSERT INTO Tb_Recommender_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID,Requested_to) VALUES 
        ('$request_id','$Meterial_Name','$Quantity_Details','$status','$Price','$Total','$V_id','$emp_id','$Requested_to')";

        $rs_material = sqlsrv_query($conn, $meterial);
        
    }


    $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items 
    ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id WHERE Tb_Request.Request_ID = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);
    $PERSION =  $updated_query['Persion_In_Workflow'];
    // print_r($PERSION);exit;
    $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',','))  ");
    $idss = array();
    while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
        $idss[] = $ids['Office_Email_Address'];
    }
    $implode = implode(',', $idss);
    // print_r($implode);exit;

    $update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$verifier_code'");
    $updated_query1 = sqlsrv_fetch_array($update_qry1);
    $To =  $updated_query1['Office_Email_Address'];
    // print_r($To);exit;

    $update_qry12 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$Recommender_Code'");
    $updated_query12 = sqlsrv_fetch_array($update_qry12);
    $Cc =  $updated_query12['Office_Email_Address'];
    //  print_r($Cc);exit;

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
    // $array = "$Cc,$implode";

    // $cc = explode(',', $array);
    // // print_r($cc);exit;
    // foreach ($cc as $ccc) {
    //     // $mail->AddAddress($address);
    //     $mail->addCC(trim($ccc));
    // }
    // $bcc = explode(',', $Bcc);

    // foreach ($bcc as $bccc) {
    //   // $mail->AddAddress($address);
    //   $mail->addBCC(trim($bccc));
    // }

    // $mail->addAttachment($fil);         // Add attachments
    // $mail->addAttachment($_FILES["attachements"]["tmp_name"], $fil);    // Optional name
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = $updated_query['Request_Category'];
            
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
                            <th class="text-center">Request ID</th>
                            <th class="text-center">Department</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Plant</th>
                            <th class="text-center">Meterial</th>
                            <th class="text-center">Quantity</th>                          
                            <th class="text-center">Status</th>                          
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                1
                            </td>
                            <td>
                                ' . $request_id . '
                            </td>
                            <td>
                                ' . $updated_query['Department'] . '
                            </td>
                            <td>
                                ' . $updated_query['Request_Type'] . '
                            </td>
                            <td>
                                ' . $updated_query['Plant'] . '
                            </td>
                            <td>
                                ' . $updated_query['Item_Code'] . '
                            </td>
                            <td>
                                ' . $updated_query['Quantity'] . '
                            </td>
                            <td>
                            <h4><span class="badge badge-success"><i class="fa fa-check"></i>Finance Verification</span></h4>
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
      }else{
        ?>
        <script type="text/javascript">
            alert("Recommended successsfully");
            window.location = "show_recommender.php";
        </script>
        <?php
      }

}


    // total,discount,packageamount display query
    $discount_charges_qry = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
    Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.total_amount,Tb_Vendor_Selection.discount_amount,Tb_Vendor_Selection.package_amount,Tb_Vendor_Selection.package_percentage
    FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
    ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
    WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
    AND Tb_Vendor_Selection.Requester_Selection ! = '1'
    GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
    Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.total_amount,Tb_Vendor_Selection.discount_amount,Tb_Vendor_Selection.package_amount,Tb_Vendor_Selection.package_percentage ORDER BY Tb_Vendor_Selection.V_id DESC");
    $charges_arr = [];  
    while ($discount_charges_res = sqlsrv_fetch_array($discount_charges_qry)) {
        $charges_arr[] = $discount_charges_res;
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

        <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

        <style>
        /* body {
            position: relative;
        } */
        .table-wrapper { 
            overflow-x:scroll;
            overflow-y:visible;
            /* width:200px; */
            margin-left: 305px;
        }


        td, th {
            padding: 5px 20px;
            /* width: 100px; */
        }
        tbody tr {
        
        }
        th:first-child {
            position: absolute;
            left: 5px
        }

.heading-table {
    border-collapse: collapse;
    border-spacing: 0;
    width: 100%;
    max-width: 100ch;
}

.heading-table caption {
    font-size: 1.1em;
    text-align: left;
    font-weight: bold;
    margin-bottom: 0.5em;
}

.heading-table .highlight {
    background: rgba(2 130 12 / 27%);
}

/* table, tr, th, td {
    border: 1px solid #c4c4c4;
} */

        .display_section {
            cursor: pointer;
        }

        .preview_icon {
            display: none;
            position: absolute;
            top: 23%;
            left: 42%;
            font-size: 20px;
        }

        .material-tr {
            height: 83px;
        }

        .material-tr > td {
            height: auto;
            vertical-align: middle;
        }

        .btn-close {
            margin: unset !important;
        }


        .modal-content {
            cursor: move;
        }

        body {
            /* STOP MOVING AROUND! */
            overflow-x: hidden;
            overflow-y: scroll !important;
        }

        .modal_css_load {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1055;
            display: none;
            width: 100%;
            height: 100%;
             -ms-overflow-style: none;  /* Internet Explorer 10+ */
            scrollbar-width: none;  /* Firefox */
        }

        .material_tbl {
            width: 300px !important;
        }

        @media only screen and (max-width: 600px) {
            .plant_top_detail {
                font-size: 10px !important;
            }
            .form-control-plaintext {
                font-size: 10px;
            }
            .material_tbl {
                width: auto !important;
            }

            th:first-child {
                position: unset;
                left: unset;
            }

            .table-wrapper {
                margin-left: unset;
            }
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
                                         <ol class="breadcrumb m-0">
                                             <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                             <li class="breadcrumb-item"><a href="show_recommender.php">Show Recommender Request</a></li>
                                             <li class="breadcrumb-item active">Recommender For Request</li>
                                         </ol>
                                 </div>
                             </div>
                             <div class="col-sm-6">
                                <div class="float-end d-none d-sm-block">
                                    <h4 class="">Request ID : <?php echo $request_id ?></h4>
                                     <input type="hidden" id="r_id" value="<?php echo $request_id; ?>">

                                    <?php
                                    $po_creator_sql = sqlsrv_query($conn, "SELECT TOP 1 Tb_Request.EMP_ID,Tb_Request.Plant,Tb_Request_Items.MaterialGroup from Tb_Request 
                                        left join Tb_Request_Items ON Tb_Request_Items.Request_ID = Tb_Request.Request_ID
                                        where Tb_Request.Request_ID = '$request_id'");
                                      $po_creator = sqlsrv_fetch_array($po_creator_sql);
                                     ?>
                                </div>
                             </div>
                         </div>
                        </div>
                     </div>
                     <!-- end page title -->    
                     <br>

                            <!-- <div class="table-responsive"> -->
                    <div class="container-fluid">

                        <div class="page-content-wrapper">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <?php 
                                                $plant_sql = "SELECT Plant_Name FROM Plant_Master_PO WHERE Plant_Code = '".$po_creator['Plant']."'";
                                                $plant_sql_exec =  sqlsrv_query($conn,$plant_sql);
                                                $plant_detail = sqlsrv_fetch_array($plant_sql_exec);

                                            ?>
                                            <h1 class="badge bg-success plant_top_detail" style="font-size: 15px;">Plant Details - <span><?php echo $po_creator['Plant']; ?> (<?php echo $plant_detail['Plant_Name']; ?>)</span></h1>

                                            <form method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" id="mapping_id" name="mapping_id">
                                                 <input type="hidden" id="po_creator_id" value="<?php echo $po_creator['EMP_ID']; ?>">
                                                 <input type="hidden" name="recommendor_id" id="recommendor_id">
                                                 <input type="hidden" name="finance_verifier_id" id="finance_verifier_id">
                                                 <input type="hidden" name="approver_id" id="approver_id">
                                                 <input type="hidden" name="approver2_id" id="approver2_id">

                                                <input type="hidden" id="po_plant" value="<?php echo $po_creator['Plant']; ?>">
                                                 <input type="hidden" id="po_mat_group" value="<?php echo $po_creator['MaterialGroup']; ?>">

                                                <div class="table-wrapper">
                                                    <table id="busDataTable" class="data heading-table" style="width: <?php echo $table_width;?>;">
                                                        <?php
                                                        $selector = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.Vendor_SAP,Tb_Vendor_Selection.V_id,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_City,Tb_Vendor_Selection.vendor_Active_SAP,
                                                            Tb_Vendor_Selection.Last_Purchase,Tb_Vendor_Selection.Delivery_Time,Tb_Vendor_Selection.Value_Of,Tb_Vendor_Selection.Fright_Charges,
                                                            Tb_Vendor_Selection.Insurance_Details,Tb_Vendor_Selection.GST_Component,Tb_Vendor_Selection.Warrenty,Tb_Vendor_Selection.Payment_Terms,
                                                            Tb_Vendor_Selection.Requester_Selection,Tb_Vendor_Selection.Requester_Remarks,Tb_Vendor_Selection.Attachment,Tb_Vendor_Quantity.Quantity,
                                                            Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Selection.total_amount,Tb_Vendor_Selection.discount_amount,Tb_Vendor_Selection.package_amount,Tb_Vendor_Selection.package_percentage
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.Vendor_SAP,Tb_Vendor_Selection.V_id,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_City,Tb_Vendor_Selection.vendor_Active_SAP,
                                                            Tb_Vendor_Selection.Last_Purchase,Tb_Vendor_Selection.Delivery_Time,Tb_Vendor_Selection.Value_Of,Tb_Vendor_Selection.Fright_Charges,
                                                            Tb_Vendor_Selection.Insurance_Details,Tb_Vendor_Selection.GST_Component,Tb_Vendor_Selection.Warrenty,Tb_Vendor_Selection.Payment_Terms,
                                                            Tb_Vendor_Selection.Requester_Selection,Tb_Vendor_Selection.Requester_Remarks,Tb_Vendor_Selection.Attachment,Tb_Vendor_Quantity.Quantity,
                                                            Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Selection.total_amount,Tb_Vendor_Selection.discount_amount,Tb_Vendor_Selection.package_amount,Tb_Vendor_Selection.package_percentage");
                                                        $selector_arr = sqlsrv_fetch_array($selector);
                                                        ?>
                                                        <colgroup>
                                                            <col>
                                                            <col class="highlight">
                                                            <col>
                                                        </colgroup>
                                                        <tbody class="results" id="rone">
                                                        <tr id="head">
                                                            <th>Particulars</th>
                                                            <td>
                                                                <?php echo $selector_arr['V_id'] ?><input type="hidden"
                                                                    class="form-control" name="V1_id[]"
                                                                    value="<?php echo $selector_arr['V_id'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details1 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id ORDER BY Tb_Vendor_Selection.V_id DESC ");
                                                            while ($array_dy_Details1 = sqlsrv_fetch_array($array_Details1)) {

                                                                ?>
                                                                <td>
                                                                    <?php echo $array_dy_Details1['V_id'] ?><input
                                                                        type="hidden" class="form-control" name="V1_id[]"
                                                                        value="<?php echo $array_dy_Details1['V_id'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="one">
                                                            <th>Vendor SAP Code, if available</th>
                                                            <td><input type="text" class="form-control" readonly name="Vendor_SAP[]" value="<?php echo $selector_arr['Vendor_SAP'] ?>"></td>
                                                            <?php
                                                            $array_Details1 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_SAP
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_SAP ORDER BY Tb_Vendor_Selection.V_id DESC ");
                                                            while ($array_dy_Details1 = sqlsrv_fetch_array($array_Details1)) {

                                                                ?>
                                                                <td><input type="text" class="form-control" readonly name="Vendor_SAP[]"
                                                                        value="<?php echo $array_dy_Details1['Vendor_SAP'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="two">
                                                            <th>Name of vendor</th>
                                                            <td><input type="text" class="form-control disabled" 
                                                                    readonly id="vendorname" name="Vendor_Name[]"
                                                                    value="<?php echo $selector_arr['Vendor_Name'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details2 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_Name
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_Name ORDER BY Tb_Vendor_Selection.V_id DESC ");
                                                            while ($array_dy_Details2 = sqlsrv_fetch_array($array_Details2)) {

                                                                ?>
                                                                <td><input type="text" class="form-control disabled"
                                                                        readonly id="vendorname" name="Vendor_Name[]"
                                                                        value="<?php echo $array_dy_Details2['Vendor_Name'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="three">
                                                            <th>City of vendor</th>
                                                            <td><input type="text" class="form-control disabled" readonly id="city" name="Vendor_City[]"
                                                                    value="<?php echo $selector_arr['Vendor_City'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details3 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_City
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_City ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details3 = sqlsrv_fetch_array($array_Details3)) {

                                                                ?>
                                                                <td><input type="text" class="form-control disabled" readonly id="city" name="Vendor_City[]" 
                                                                        value="<?php echo $array_dy_Details3['Vendor_City'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="four">
                                                            <th>Whether vendor is active in SAP?</th>
                                                            <td><input type="text" class="form-control dis" readonly name="vendor_Active_SAP[]" 
                                                                    value="<?php echo $selector_arr['vendor_Active_SAP'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details4 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.vendor_Active_SAP
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.vendor_Active_SAP ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details4 = sqlsrv_fetch_array($array_Details4)) {

                                                                ?>
                                                                <td><input type="text" class="form-control dis" readonly name="vendor_Active_SAP[]" 
                                                                        value="<?php echo $array_dy_Details4['vendor_Active_SAP'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="five">
                                                            <th>Last purchase made on</th>
                                                            <td><input type="text" class="form-control dis" readonly name="Last_Purchase[]"
                                                                    value="<?php echo $selector_arr['Last_Purchase'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details5 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Last_Purchase
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Last_Purchase ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details5 = sqlsrv_fetch_array($array_Details5)) {

                                                                ?>
                                                                <td><input type="text" class="form-control dis" readonly name="Last_Purchase[]"
                                                                        value="<?php echo $array_dy_Details5['Last_Purchase'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="six">
                                                            <th>
                                                                <table class="table table-bordered material_tbl">
                                                                    <thead>
                                                                        <tr>
                                                                            <td>
                                                                                <b>Material Wise Quantity Details</b>
                                                                </td>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$request_id'",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                                                                        $item_count = sqlsrv_num_rows($result);
                                                                        $mt_index = 1;
                                                                        while ($row = sqlsrv_fetch_array($result)) {
                                                                            $ID = $row['ID'];
                                                                            $ItemCode = $row['Item_Code'];
                                                                            // print_r($ID);
                                                                            ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <div>
                                                                                        <span class="badge badge-soft-primary"><?php echo substr(trim($ItemCode),-10); ?></span>
                                                                                        <span class="badge badge-soft-danger ms-3"><?php echo trim($row['UOM']); ?></span>

                                                                                    </div>                                                                                      
                                                                                    <input type="text" class="form-control-plaintext" readonly value="<?php echo $mt_index.') '.trim($row['Description']) ?>"
                                                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center<?php echo $ID ?>">
                                                                                        <!-- Modal -->
                                                                                        <div class="modal fade bs-example-modal-center<?php echo $ID ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                                                            <div class="modal-dialog modal-dialog-centered">
                                                                                                <div class="modal-content">
                                                                                                    <div class="modal-header">
                                                                                                        <h5 class="modal-title mt-0">Last Purchace Date For Vendor</h5>
                                                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                                                            
                                                                                                        </button>
                                                                                                    </div>
                                                                                                    <div class="modal-body">
                                                                                                        <table class="table table-bordered" >
                                                                                                            <thead>
                                                                                                                <tr>
                                                                                                                    <td>Vendor Code</td>
                                                                                                                    <th>Material Name</th>
                                                                                                                    <th>Price</th>
                                                                                                                    <th>Purchace Date</th>
                                                                                                                </tr>
                                                                                                            </thead>
                                                                                                            <tbody>
                                                                                                            <?php
                                                                                                                $result1 = sqlsrv_query($conn, "SELECT TOP 3 * FROM MIGO_DET WHERE  MATNR = '$ItemCode' ORDER BY LINE_ID DESC ");
                                                                                                                while ($row1 = sqlsrv_fetch_array($result1)) {
                                                                                                            ?>
                                                                                                                <tr>
                                                                                                                    <td><?php echo $row1['LIFNR'] ?></td>
                                                                                                                    <td><?php echo $row['Description'] ?></td>
                                                                                                                    <td><?php echo $row1['MENGE'] ?></td>
                                                                                                                    <td><?php echo $row1['MENGE'] ?></td>
                                                                                                                    <td><?php echo ($row1['BUDAT_MKPF'] != null && $row1['BUDAT_MKPF'] != '') ? $row1['BUDAT_MKPF']->format('Y-m-d') : '' ?></td>
                                                                                                                </tr>
                                                                                                                <?php
                                                                                                                        }
                                                                                                                    ?>
                                                                                                            </tbody>
                                                                                                        </table>
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                        <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                                                    </div>
                                                                                                </div><!-- /.modal-content -->
                                                                                            </div><!-- /.modal-dialog -->
                                                                                        </div><!-- /.modal -->
                                                                                </td>
                                                                            </tr>
                                                                        <?php
                                                                        $mt_index++;}
                                                                        ?>
                                                                    </tbody>
                                                                </table>

                                                            </th>
                                                            <td>
                                                                <table id="myTable1" class="table table-bordered" style="width: 345px !important;">
                                                                    <tr>
                                                                        <thead>
                                                                            <td>Quantity</td>
                                                                            <th>Price</th>
                                                                            <th>GST(%)</th>
                                                                            <th>Discount(%)</th>
                                                                            <th>Total</th>
                                                                        </thead>
                                                                    </tr>
                                                                    <tbody>
                                                                        <?php
                                                                        $i = 1;
                                                                        $mat_ind = 1;
                                                                        $result = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                                        Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.Request_Id,
                                                                        Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.gst_percentage,Tb_Vendor_Quantity.discount_percentage
                                                                        FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                                        ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                                        WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                                        AND Tb_Vendor_Selection.Requester_Selection = '1'
                                                                        GROUP BY Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                                        Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.Request_Id,
                                                                        Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.gst_percentage,Tb_Vendor_Quantity.discount_percentage");
                                                                        while ($row = sqlsrv_fetch_array($result)) {
                                                                            ?>
                                                                            <tr  class="material-tr">
                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control qty"
                                                                                        readonly name="Quantity_Details[]"
                                                                                        value="<?php echo $row['Quantity'] ?>"
                                                                                        placeholder="Enter Quantity Details" style="width: 75px;">
                                                                                    <input type="hidden"
                                                                                        class="form-control"
                                                                                        name="Meterial_Name[]"
                                                                                        value="<?php echo $row['Meterial_Name'] ?>">
                                                                                    <input type="hidden"
                                                                                        class="form-control" name="V_id[]"
                                                                                        value="<?php echo $row['V_id'] ?>">
                                                                                </td>

                                                                                <td>
                                                                                    <input type="text" readonly 
                                                                                        class="form-control price"
                                                                                        name="Price[]"
                                                                                        value="<?php echo $row['Price'] ?>" style="width: 75px;">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" min="0"
                                                                                        class="form-control vendor_gst_percent_1 gst_percent" style="width: 75px;"
                                                                                        name="gst_percent[]" 
                                                                                        placeholder="Enter GST" step=".01"  required error-msg='Price is mandatory.' data-rowid="1" readonly id="vendor1_gst_percentage_material<?php echo $mat_ind;?>" value="<?php echo $row['gst_percentage'] ?>">
                                                                                    <span class="error_msg text-danger"></span>
                                                                                </td>

                                                                                <td>
                                                                                    <input type="number" min="0"
                                                                                        class="form-control vendor_discount_percent_1 discount_percent" style="width: 75px;"
                                                                                        name="discount_percent[]"
                                                                                        placeholder="Enter Discount" step=".01"  required error-msg='Discount is mandatory.' data-rowid="1" readonly id="vendor1_discount_percentage_material<?php echo $mat_ind;?>" value="<?php echo $row['discount_percentage'] ?>">
                                                                                    <span class="error_msg text-danger"></span>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control amount"
                                                                                        readonly name="Total[]" 
                                                                                        value="<?php echo $row['Total'] ?>" style="width: 75px;">
                                                                                </td>
                                                                            </tr>
                                                                            <?php $mat_ind++; } ?>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                            <?php
                                                            $array_Details111 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            $vendor_ind = 2;
                                                            while ($array_dy_Details111 = sqlsrv_fetch_array($array_Details111)) {
                                                                $arr = $array_dy_Details111['V_id'];
                                                                ?>
                                                            <td>
                                                                <table id="myTable1" class="table table-bordered"
                                                                    style="width: 345px !important;">
                                                                    <tr>
                                                                        <thead>
                                                                            <td>Quantity</td>
                                                                            <th>Price</th>
                                                                            <th>GST(%)</th>
                                                                            <th>Discount(%)</th>
                                                                            <th>Total</th>
                                                                        </thead>
                                                                    </tr>
                                                                    <tbody>
                                                                        <?php
                                                                            $mat_ind = 1;
                                                                            $array_Details1111 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name,
                                                                            Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.gst_percentage,Tb_Vendor_Quantity.discount_percentage 
                                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND  Tb_Vendor_Selection.V_id = '$arr'
                                                                            AND Tb_Vendor_Quantity.V_id = '$arr'
                                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Quantity.Quantity,Tb_Vendor_Quantity.Meterial_Name,
                                                                            Tb_Vendor_Quantity.Price,Tb_Vendor_Quantity.Total,Tb_Vendor_Quantity.gst_percentage,Tb_Vendor_Quantity.discount_percentage  ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                                            while ($array_dy_Details1111 = sqlsrv_fetch_array($array_Details1111)) {

                                                                                ?>
                                                                            <tr  class="material-tr">
                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control qty"
                                                                                        readonly name="Quantity_Details[]" 
                                                                                        value="<?php echo $array_dy_Details1111['Quantity'] ?>"
                                                                                        placeholder="Enter Quantity Details" style="width: 75px;">
                                                                                    <input type="hidden"
                                                                                        class="form-control"
                                                                                        name="Meterial_Name[]"
                                                                                        value="<?php echo $array_dy_Details1111['Meterial_Name'] ?>">
                                                                                    <input type="hidden"
                                                                                        class="form-control" name="V_id[]"
                                                                                        value="<?php echo $arr ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" readonly
                                                                                        class="form-control price"
                                                                                        name="Price[]" style="width: 75px;"
                                                                                        value="<?php echo $array_dy_Details1111['Price'] ?>">
                                                                                </td>
                                                                                <td>
                                                                                    <input type="number" min="0"
                                                                                        class="form-control vendor_gst_percent_1 gst_percent" style="width: 75px;"
                                                                                        name="gst_percent[]" 
                                                                                        placeholder="Enter GST" step=".01"  required error-msg='Price is mandatory.' data-rowid="1" readonly id="vendor<?php echo $vendor_ind; ?>_gst_percentage_material<?php echo $mat_ind;?>" value="<?php echo $array_dy_Details1111['gst_percentage'] ?>">
                                                                                    <span class="error_msg text-danger"></span>
                                                                                </td>

                                                                                <td>
                                                                                    <input type="number" min="0"
                                                                                        class="form-control vendor_discount_percent_1 discount_percent" style="width: 75px;"
                                                                                        name="discount_percent[]"
                                                                                        placeholder="Enter Discount" step=".01"  required error-msg='Discount is mandatory.' data-rowid="1" readonly id="vendor<?php echo $vendor_ind; ?>_discount_percentage_material<?php echo $mat_ind;?>" value="<?php echo $array_dy_Details1111['discount_percentage'] ?>">
                                                                                    <span class="error_msg text-danger"></span>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text" 
                                                                                        class="form-control amount"
                                                                                        readonly name="Total[]"
                                                                                        value="<?php echo $array_dy_Details1111['Total'] ?>" style="width: 75px;">
                                                                                </td>
                                                                            </tr>
                                                                        <?php $mat_ind++; } ?>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                            <?php $vendor_ind++;} ?>
                                                        </tr>
                                                        <tr id="seven1">
                                                            <th>Total</th>
                                                            <td>
                                                                <input type="text" readonly class="form-control amt_tot1" 
                                                                    name="amt_tot[]" id="amt_tot1" title="Total" value="<?php echo $selector_arr['total_amount'] ?>">
                                                            </td>
                                                            <?php
                                                            foreach ($charges_arr as $key => $value) {
                                                            ?>
                                                            <td>
                                                                <input type="text" readonly class="form-control amt_tot<?php echo $key+2; ?>" 
                                                                    name="amt_tot[]" id="amt_tot<?php echo $key+2; ?>" title="Total" value="<?php echo $value['total_amount'] ?>">
                                                            </td>
                                                           <?php } ?>
                                                        </tr>
                                                        <tr id="nine">
                                                            <th>Freight Charges</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Fright_Charges[]" 
                                                                    value="<?php echo $selector_arr['Fright_Charges'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details9 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Fright_Charges
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Fright_Charges ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details9 = sqlsrv_fetch_array($array_Details9)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control "
                                                                        name="Fright_Charges[]" 
                                                                        value="<?php echo $array_dy_Details9['Fright_Charges'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="ten">
                                                            <th>Insurance Details</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Insurance_Details[]" 
                                                                    value="<?php echo $selector_arr['Insurance_Details'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details10 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Insurance_Details
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Insurance_Details ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details10 = sqlsrv_fetch_array($array_Details10)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control"
                                                                        name="Insurance_Details[]" 
                                                                        value="<?php echo $array_dy_Details10['Insurance_Details'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="ten2">
                                                            <th>Package Forwarding Percentage</th>
                                                            <td><input type="number" min="0" class="form-control package_calc" 
                                                                    name="package_percentage[]"
                                                                    placeholder="Enter Packaging percentage" id="package_percentage_1" data-id="1" data-type="percent" readonly  value="<?php echo $selector_arr['package_percentage'] ?>"></td>
                                                            <?php 
                                                            foreach ($charges_arr as $key => $value) {
                                                            ?>
                                                            
                                                            <td>
                                                                <input type="number" min="0" class="form-control package_calc"
                                                                    name="package_percentage[]"
                                                                    placeholder="Enter Packaging percentage" id="package_percentage_<?php echo $key+2; ?>" data-id="<?php echo $key+2; ?>" data-type="percent" readonly value="<?php echo $value['package_percentage'] ?>">
                                                            </td>
                                                           <?php } ?>
                                                                                                                
                                                        </tr>
                                                        <tr id="ten1">
                                                            <th>Package Forwarding Amount</th>
                                                            <td><input type="number" min="0" class="form-control package_calc" 
                                                                    name="package_amount[]"
                                                                    placeholder="Enter Packaging Charges" id="package_amount_1" data-id="1" data-type="amount" readonly value="<?php echo $selector_arr['package_amount'] ?>"></td>
                                                            <?php 
                                                            foreach ($charges_arr as $key => $value) {
                                                            ?>
                                                            
                                                            <td>
                                                                    <input type="number" min="0" class="form-control package_calc"
                                                                    name="package_amount[]"
                                                                    placeholder="Enter Packaging Charges" id="package_amount_<?php echo $key+2; ?>" data-id="<?php echo $key+2; ?>" data-type="amount" readonly value="<?php echo $value['package_amount'] ?>">
                                                            </td>
                                                           <?php } ?>
                                                        </tr>                                                        
                                                        <tr id="eleven">
                                                            <th>GST Component</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="GST_Component[]" 
                                                                    value="<?php echo $selector_arr['GST_Component'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details11 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.GST_Component
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.GST_Component ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details11 = sqlsrv_fetch_array($array_Details11)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control"
                                                                        name="GST_Component[]" 
                                                                        value="<?php echo $array_dy_Details11['GST_Component'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="eleven1">
                                                            <th>Discount Amount</th>
                                                            <td>
                                                                <input type="number" min="0" class="form-control"
                                                                    name="discount_amount[]"
                                                                    placeholder="Enter Discount Amount" id="discount_amount_1" readonly value="<?php echo $selector_arr['discount_amount'] ?>">
                                                            </td>
                                                            <?php 
                                                            foreach ($charges_arr as $key => $value) {
                                                            ?>
                                                            
                                                            <td>
                                                                <input type="number" min="0" class="form-control"
                                                                    name="discount_amount[]"
                                                                    placeholder="Enter Discount Amount" id="discount_amount_<?php echo $key+2; ?>" readonly value="<?php echo $value['discount_amount'] ?>">
                                                            </td>

                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="seven">
                                                            <th>Net Amount</th>
                                                            <td><input type="text" readonly class="form-control valueof1 requested_value"
                                                                    name="Value_Of[]" id="totale2" title="Total + AdditionalCharges"
                                                                    value="<?php echo $selector_arr['Value_Of'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details7 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Value_Of
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Value_Of ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            $index = 2;
                                                            while ($array_dy_Details7 = sqlsrv_fetch_array($array_Details7)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control valueof<?php echo $index; ?>"
                                                                        name="Value_Of[]" id="totale2" title="Total + AdditionalCharges"
                                                                        value="<?php echo $array_dy_Details7['Value_Of'] ?>">
                                                                </td>
                                                            <?php $index++; } ?>
                                                        </tr>
                                                        <tr id="eight">
                                                            <th>Delivery Time</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Delivery_Time[]" 
                                                                    value="<?php echo $selector_arr['Delivery_Time'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details8 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Delivery_Time
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Delivery_Time ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details8 = sqlsrv_fetch_array($array_Details8)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control "
                                                                        name="Delivery_Time[]" 
                                                                        value="<?php echo $array_dy_Details8['Delivery_Time'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="twelve">
                                                            <th>Warrenty</th>
                                                            <td><input type="text" readonly class="form-control"
                                                                    name="Warrenty[]" 
                                                                    value="<?php echo $selector_arr['Warrenty'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details12 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Warrenty
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Warrenty ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details12 = sqlsrv_fetch_array($array_Details12)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control "
                                                                        name="Warrenty[]" 
                                                                        value="<?php echo $array_dy_Details12['Warrenty'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="thirteen">
                                                            <th>Payment Terms</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Payment_Terms[]"
                                                                    value="<?php echo $selector_arr['Payment_Terms'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details13 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Payment_Terms
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Payment_Terms ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details13 = sqlsrv_fetch_array($array_Details13)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control "
                                                                        name="Payment_Terms[]" 
                                                                        value="<?php echo $array_dy_Details13['Payment_Terms'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                    </tbody>
                                                    <div class="separator-solid"></div>

                                                    <tbody id="eone">
                                                                                                                    
                                                            <tr id="tara" class="src-table">
                                                            <?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Finance_verifier INNER JOIN Tb_Recommender ON Tb_Finance_verifier.Request_Id = Tb_Recommender.Request_id
                                                        WHERE Tb_Finance_verifier.Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Remark'] == '' ){
                                                            ?>
                                                            <th>Requester's Selection&nbsp;&nbsp;<br>Copy as&nbsp;&nbsp;<input type="checkbox" id="submit">
                                                            <?php

                                                        }else{
                                                            ?>
                                                            <th>Requester's Selection
                                                        <?php }
                                                        ?>
                                                            <td class="woo"><input type="text" class="form-control requester_selection1"
                                                                    readonly name="Requester_Selection[]" style="color: black;"
                                                                    value="<?php echo $selector_arr['Requester_Selection'] ?>">
                                                                
                                                            </td>
                                                            <?php
                                                            $array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Selection
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Selection ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            $rsindex = 2;
                                                            while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {
                                                                ?>
                                                                <td class="woo"><input type="text" readonly style="color: black;"
                                                                        class="form-control requester_selection<?php echo $rsindex;?>" name="Requester_Selection[]"
                                                                        value="<?php echo $array_dy_Details14['Requester_Selection'] ?>">
                                                                </td>
                                                            <?php } ?>

                                                        </tr>
                                                        <tr id="text">
                                                        <?php 
                                                            $requester_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
                                                            WHERE Employee_Code = '$Purchaser_Code' ");
                                                            $requester_names = sqlsrv_fetch_array($requester_name);
                                                            $name = $requester_names['Employee_Name'];
                                                            ?>
                                                            <th>Requester's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span></th>
                                                            <td><input type="text" class="form-control" readonly
                                                                    name="Requester_Remarks[]"
                                                                    value="<?php echo $selector_arr['Requester_Remarks'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details15 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Remarks
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Remarks ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details15 = sqlsrv_fetch_array($array_Details15)) {

                                                                ?>
                                                                <td><input type="text" class="form-control" readonly
                                                                        name="Requester_Remarks[]"
                                                                        value="<?php echo $array_dy_Details15['Requester_Remarks'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <!--  -->
                                                        <?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Finance_verifier INNER JOIN Tb_Recommender ON Tb_Finance_verifier.Request_Id = Tb_Recommender.Request_id
                                                        WHERE Tb_Finance_verifier.Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Remark'] == '' ){
                                                            ?>
                                                            <tr class="target-table">
                                                            <th>Recommender Selection<span class="required-label"style="color:red">*</span>
                                                            </th>
                                                            <?php
                                                            $array_Details141 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Selection
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Selection 
                                                            ORDER BY Tb_Vendor_Selection.V_id DESC",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                                                                // $res = sqlsrv_fetch_array($array_Details14);
                                                                // $vendor_count = COUNT($res);
                                                                $vendor_count = sqlsrv_num_rows($array_Details141);
                                                                $resindex = 1;
                                                                while ($array_dy_Details141 = sqlsrv_fetch_array($array_Details141)) { ?>
                                                                <td class="answer">
                                                                    <select class="form-control request_selection"
                                                                        name="Recommender_Selection[]" data-id="<?php echo $resindex; ?>">
                                                                        <option value="">Select Recommender Selection</option>
                                                                        <?php for($i = 1;$i<= $vendor_count;$i++) { ?>
                                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                            <?php $resindex++; } ?>
                                                            <td class="root">
                                                                    <input type="text" class="form-control " 
                                                                    readonly name="Recommender_Selection[]"
                                                                    value="<?php echo $selector_arr['Requester_Selection'] ?>">
                                                                </td>
                                                                <?php
                                                            $array_Details141 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Selection
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Requester_Selection ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details141 = sqlsrv_fetch_array($array_Details141)) {

                                                                ?>
                                                                <td class="root">
                                                                <input type="text" class="form-control " 
                                                                    readonly name="Recommender_Selection[]"
                                                                    value="<?php echo $array_dy_Details141['Requester_Selection'] ?>">
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <?php 

                                                        }else{
                                                            ?>
                                                            <?php
                                                        $selector = sqlsrv_query($conn, "SELECT Tb_Recommender.Request_id,Tb_Recommender.V_id,Tb_Recommender.Vendor_SAP,Tb_Recommender.Vendor_Name,Tb_Recommender.Vendor_City,Tb_Recommender.vendor_Active_SAP,
                                                        Tb_Recommender.Last_Purchase,Tb_Recommender.Delivery_Time1,Tb_Recommender.Value_Of,Tb_Recommender.Fright_Charges,Tb_Recommender.Insurance_Details,Tb_Recommender.GST_Component,
                                                        Tb_Recommender.Warrenty,Tb_Recommender.Payment_Terms,Tb_Recommender.Requester_Remarks,Tb_Recommender.Recommender_Remarks,Tb_Recommender.Attachment,
                                                        Tb_Recommender.Status,Tb_Recommender.Requester_Selection,Tb_Recommender.Recommender_Selection,Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                                        Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total
                                                        FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id
                                                        WHERE Tb_Recommender.Request_id = '$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id  AND Tb_Recommender.Recommender_Selection = '1'
                                                        GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,Tb_Recommender.Vendor_SAP,Tb_Recommender.Vendor_Name,Tb_Recommender.Vendor_City,Tb_Recommender.vendor_Active_SAP,
                                                        Tb_Recommender.Last_Purchase,Tb_Recommender.Delivery_Time1,Tb_Recommender.Value_Of,Tb_Recommender.Fright_Charges,Tb_Recommender.Insurance_Details,Tb_Recommender.GST_Component,
                                                        Tb_Recommender.Warrenty,Tb_Recommender.Payment_Terms,Tb_Recommender.Requester_Remarks,Tb_Recommender.Recommender_Remarks,Tb_Recommender.Attachment,
                                                        Tb_Recommender.Status,Tb_Recommender.Requester_Selection,Tb_Recommender.Recommender_Selection,Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender_Meterial.Quantity,
                                                        Tb_Recommender_Meterial.Meterial_Name,Tb_Recommender_Meterial.Price,Tb_Recommender_Meterial.Total");
                                                        $selector_arr = sqlsrv_fetch_array($selector);
                                                        ?>
                                                        <tr id="tara" class="src-table">
                                                            <th>Recommender's Selection</th>
                                                            <td class="woo">
                                                                    <input type="text" class="form-control " 
                                                                    readonly name="Recommender_Selection[]"
                                                                    value="<?php echo $selector_arr['Recommender_Selection'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details14 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                                            Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection
                                                            FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                                            WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
                                                            GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                                            Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Selection ORDER BY Tb_Recommender.V_id DESC");
                                                            while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

                                                                ?>
                                                                <td class="woo">
                                                                        <input type="text" class="form-control "
                                                                    readonly name="Recommender_Selection[]" 
                                                                    value="<?php echo $array_dy_Details14['Recommender_Selection'] ?>">
                                                                </td>
                                                            <?php } ?>

                                                        </tr>
                                                        <?php } ?>


                                                        <?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Finance_verifier INNER JOIN Tb_Recommender ON Tb_Finance_verifier.Request_Id = Tb_Recommender.Request_id
                                                        WHERE Tb_Finance_verifier.Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Remark'] == '' ){
                                                            ?>
                                                        <tr class="remark">
                                                        <?php 
                                                            $requester_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
                                                            WHERE Employee_Code = '$Recommender_Code' ");
                                                            $requester_names = sqlsrv_fetch_array($requester_name);
                                                            $name = $requester_names['Employee_Name'];
                                                            ?>
                                                            <th>Recommender's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span></th>

                                                            <?php
                                                            $array_Details11 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_City
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Vendor_City ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            while ($array_dy_Details11 = sqlsrv_fetch_array($array_Details11)) {

                                                                ?>
                                                                <td><input type="text" class="form-control"
                                                                        name="Recommender_Remarks[]"
                                                                        placeholder="Enter Recommender Remarks"></td>
                                                            <?php } ?>
                                                        </tr>
                                                        <?php
                                                        }else{
                                                            ?>
                                                            <tr>
                                                            <th>Recommender's Remarks<span class="required-label"></span>
                                                            </th>
                                                            <td><input type="text" class="form-control " readonly 
                                                                    name="Recommender_Remarks[]"
                                                                    value="<?php echo $selector_arr['Recommender_Remarks'] ?>">
                                                            </td>
                                                            <?php
                                                            $array_Details121 = sqlsrv_query($conn, "SELECT  Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                                            Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Remarks
                                                            FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id 
                                                            WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.V_id = Tb_Recommender_Meterial.V_id AND Tb_Recommender.Recommender_Selection ! = '1' 
                                                            GROUP BY Tb_Recommender.Request_id,Tb_Recommender.V_id,
                                                            Tb_Recommender_Meterial.Request_id,Tb_Recommender_Meterial.V_id,Tb_Recommender.Recommender_Remarks ORDER BY Tb_Recommender.V_id DESC");
                                                            while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control " 
                                                                        name="Recommender_Remarks[]"
                                                                        value="<?php echo $array_dy_Details121['Recommender_Remarks'] ?>">
                                                                </td>
                                                            <?php } ?>

                                                        </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Finance_verifier INNER JOIN Tb_Recommender ON Tb_Finance_verifier.Request_Id = Tb_Recommender.Request_id
                                                        WHERE Tb_Finance_verifier.Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Remark'] == '' ){

                                                        }else{
                                                            ?>
                                                            <tr>
                                                            <th>Total Budget (in INR)<span class="required-label"></span>
                                                            </th>
                                                            <?php
                                                            $array_Deta = sqlsrv_query($conn, "SELECT  * 
                                                            FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                                            WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'");
                                                            $array_dy_Deta = sqlsrv_fetch_array($array_Deta)
                                                            ?>
                                                            <td><input type="text" class="form-control " readonly 
                                                                    name="Total_Budget[]"
                                                                    value="<?php echo $array_dy_Deta['Total_Budget'] ?>">
                                                            </td>
                                                            <?php 
                                                            $array_Details1212 = sqlsrv_query($conn, "SELECT  *
                                                            FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                                            WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1' 
                                                            ");
                                                            while ($array_dy_Details1212 = sqlsrv_fetch_array($array_Details1212)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control " 
                                                                        name="Total_Budget[]"
                                                                        value="<?php echo $array_dy_Details1212['Total_Budget'] ?>">
                                                                </td>
                                                            <?php } ?>

                                                        </tr>
                                                        <tr>
                                                            <th>Available Budget (in INR)<span class="required-label"></span>
                                                            </th>
                                                            <?php
                                                            $array_Deta = sqlsrv_query($conn, "SELECT  * 
                                                            FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                                            WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'");
                                                            $array_dy_Deta = sqlsrv_fetch_array($array_Deta)
                                                            ?>
                                                            <td><input type="text" class="form-control " readonly 
                                                                    name="Available_Budget[]"
                                                                    value="<?php echo $array_dy_Deta['Available_Budget'] ?>">
                                                            </td>
                                                            <?php 
                                                            $array_Details1212 = sqlsrv_query($conn, "SELECT  *
                                                            FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                                            WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1' 
                                                            ");
                                                            while ($array_dy_Details1212 = sqlsrv_fetch_array($array_Details1212)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control " 
                                                                        name="Available_Budget[]"
                                                                        value="<?php echo $array_dy_Details1212['Available_Budget'] ?>">
                                                                </td>
                                                            <?php } ?>

                                                        </tr>
                                                        <tr>
                                                            <th>Financer's Remarks<span class="required-label"></span>
                                                            </th>
                                                            <?php
                                                            $array_Deta = sqlsrv_query($conn, "SELECT  * 
                                                            FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                                            WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection = '1'");
                                                            $array_dy_Deta = sqlsrv_fetch_array($array_Deta)
                                                            ?>
                                                            <td><input type="text" class="form-control " readonly 
                                                                    name="Finance_Remarks[]"
                                                                    value="<?php echo $array_dy_Deta['Remark'] ?>">
                                                            </td>
                                                            <?php 
                                                            $array_Details1212 = sqlsrv_query($conn, "SELECT  *
                                                            FROM Tb_Recommender INNER JOIN Tb_Finance_verifier ON Tb_Recommender.Request_id = Tb_Finance_verifier.Request_Id 
                                                            WHERE Tb_Recommender.Request_id ='$request_id' AND Tb_Recommender.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Recommender.Recommender_Selection ! = '1' 
                                                            ");
                                                            while ($array_dy_Details1212 = sqlsrv_fetch_array($array_Details1212)) {

                                                                ?>
                                                                <td><input type="text" readonly class="form-control " 
                                                                        name="Finance_Remarks[]"
                                                                        value="<?php echo $array_dy_Details1212['Remark'] ?>">
                                                                </td>
                                                            <?php } ?>

                                                        </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        
                                                        
                                                        <tr id="pdf">
                                                        <?php
                                                                    $view16 = sqlsrv_query($conn, "SELECT * FROM Tb_Vendor_Selection WHERE Request_Id = '$request_id' ");
                                                                    $view_query16 = sqlsrv_fetch_array($view16);
                                                                    $file_extension = explode('.', $view_query16['Attachment'])[1];

                                                            ?>
                                                            <?php 
                                                            if($view_query16['Attachment'] == ''){

                                                            }else{
                                                                ?>
                                                            <th>PDF / JPG attachment
                                                            </th>
                                                            <?php 
                                                            }?>
                                                            <?php 
                                                            if($selector_arr['Attachment'] == ''){

                                                            }else{
                                                                ?>
                                                                <td>
                                                                    <input type="text" class="form-control" name="Attachment[]" readonly value="<?php echo $selector_arr['Attachment'] ?>"
                                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center<?php echo $view_query16['id'] ?>">
                                                                

                                                                    <!-- file preview modal -->
                                                                        <div class="modal fade modal_css_load" id="file_preview_modal_1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-bs-backdrop="static">
                                                                          <div class="modal-dialog modal-lg">
                                                                            <div class="modal-content">
                                                                              <div class="modal-header">
                                                                                <h1 class="modal-title fs-5" id="exampleModalLabel">File Preview</h1>
                                                                                <a href="#" id="attachment_download_1" class="ms-auto" download><button type="button" class="btn btn-sm btn-info me-3"><i class='mdi mdi-download font-size-16 align-middle me-1 text-white'></i> Download</button></a>
                                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                              </div>
                                                                              <div class="modal-body">
                                                                                        <img class="preview_file_img_1 preview_image" src="" alt="your image" width="100%" style="display: block;">

                                                                                     <iframe class="preview_file_pdf_1 preview_pdf"
                                                                                            style="width: 100%;height: 900px;display: block;"
                                                                                            frameborder="0">
                                                                                      </iframe>



                                                                                     

                                                                                      <!-- <div id="pdfContainer"></div> -->

                                                                              </div>
                                                                              <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                              </div>
                                                                            </div>
                                                                          </div>
                                                                        </div>
                                                                        <!-- file preview modal end -->

                                                                        <div class="row mt-2 display_section p-3" id="file_display_section_1" style="border: 2px dashed blue;height: 400px;overflow-y: auto;">
                                                                                <?php 
                                                                                $multi_files = explode(',',$view_query16['Attachment']);

                                                                                foreach ($multi_files as $key => $value) {
                                                                                    $file_extension = explode('.', $value)[1];

                                                                                    if($file_extension == 'pdf') { ?>
                                                                                        <div class="col-md-3 h-50 mt-2">
                                                                                            <img src="https://play-lh.googleusercontent.com/IkcyuPcrQlDsv62dwGqteL_0K_Rt2BUTXfV3_vR4VmAGo-WSCfT2FgHdCBUsMw3TPGU"  class="multi_preview" style="width:100px;height: 100px;" data-filetype="pdf" data-id="1">
                                                                                            <input type="hidden" id="pdf_input1" value="file/<?php echo $value; ?>">
                                                                                        </div>  
                                                                                    <?php } else { ?>
                                                                                        <div class="col-md-3 h-50 mt-2">
                                                                                            <i class="fa fa-eye text-primary preview_icon"></i>
                                                                                            <img src="file/<?php echo $value; ?>" class="multi_preview" data-filetype="img" style="width:100px;height: 100px;" data-id="1">
                                                                                        </div>
                                                                                    <?php }} ?>
                                                                                    
                                                                        </div>

                                                            </td>
                                                            <?php
                                                            }?>
                                                            
                                                            
                                                            <?php
                                                            $array_Details16 = sqlsrv_query($conn, "SELECT  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Attachment,Tb_Vendor_Selection.Id
                                                            FROM Tb_Vendor_Selection INNER JOIN Tb_Vendor_Quantity 
                                                            ON Tb_Vendor_Selection.Request_Id = Tb_Vendor_Quantity.Request_Id
                                                            WHERE Tb_Vendor_Selection.Request_Id = '$request_id' AND Tb_Vendor_Selection.V_id = Tb_Vendor_Quantity.V_id 
                                                            AND Tb_Vendor_Selection.Requester_Selection ! = '1'
                                                            GROUP BY  Tb_Vendor_Selection.Request_Id,Tb_Vendor_Selection.V_id,
                                                            Tb_Vendor_Quantity.Request_Id,Tb_Vendor_Quantity.V_id,Tb_Vendor_Selection.Attachment,Tb_Vendor_Selection.Id ORDER BY Tb_Vendor_Selection.V_id DESC");
                                                            $rindex = 1;
                                                            while ($array_dy_Details16 = sqlsrv_fetch_array($array_Details16)) {
                                                            ?>
                                                            
                                                            <?php 
                                                            if($array_dy_Details16['Attachment'] == ''){

                                                            }else{

                                                                    $file_extension = explode('.', $array_dy_Details16['Attachment'])[1];

                                                                ?>
                                                            <td>
                                                                <input type="text" class="form-control" name="Attachment[]" readonly value="<?php echo $array_dy_Details16['Attachment'] ?>"
                                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-center1<?php echo $array_dy_Details16['Id'] ?>">


                                                                <!-- file preview modal -->
                                                                <div class="modal fade modal_css_load" id="file_preview_modal_<?php echo $rindex; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static">
                                                                  <div class="modal-dialog modal-lg">
                                                                    <div class="modal-content">
                                                                      <div class="modal-header">
                                                                        <h1 class="modal-title fs-5" id="exampleModalLabel">File Preview</h1>
                                                                         <a href="#" id="attachment_download_<?php echo $rindex; ?>" class="ms-auto" download><button type="button" class="btn btn-sm btn-info ms-auto me-3"><i class='mdi mdi-download font-size-16 align-middle me-1 text-white'></i> Download</button></a>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                      </div>
                                                                      <div class="modal-body">
                                                                                <img class="preview_file_img_<?php echo $rindex; ?> preview_image" src="" alt="your image" width="100%" style="display: block;">

                                                                             <iframe class="preview_file_pdf_<?php echo $rindex; ?> preview_pdf" src=""
                                                                                    style="width: 100%;height: 900px;display: block;"
                                                                                    frameborder="0">
                                                                              </iframe>

                                                                      </div>
                                                                      <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                      </div>
                                                                    </div>
                                                                  </div>
                                                                </div>
                                                                <!-- file preview modal end -->

                                                                <div class="row mt-2 display_section p-3" id="file_display_section_<?php echo $rindex; ?>" style="border: 2px dashed #ccc;height: 400px;overflow-y: auto;">
                                                                        <?php 
                                                                        $multi_files = explode(',',$array_dy_Details16['Attachment']);

                                                                        foreach ($multi_files as $key => $value) {
                                                                            $file_extension = explode('.', $value)[1];

                                                                            if($file_extension == 'pdf') { ?>
                                                                                <div class="col-md-3 h-50 mt-2">
                                                                                    <img src="https://play-lh.googleusercontent.com/IkcyuPcrQlDsv62dwGqteL_0K_Rt2BUTXfV3_vR4VmAGo-WSCfT2FgHdCBUsMw3TPGU"  class="multi_preview" style="width:100px;height: 100px;" data-filetype="pdf" data-id="<?php echo $rindex; ?>">
                                                                                    <input type="hidden" id="pdf_input<?php echo $rindex; ?>" value="file/<?php echo $value; ?>">
                                                                                </div>  
                                                                            <?php } else { ?>
                                                                                <div class="col-md-3 h-50 mt-2">
                                                                                    <i class="fa fa-eye text-primary preview_icon"></i>
                                                                                    <img src="file/<?php echo $value; ?>" class="multi_preview" data-filetype="img" style="width:100px;height: 100px;" data-id="<?php echo $rindex; ?>">
                                                                                </div>
                                                                            <?php }} ?>
                                                                            
                                                                </div>

                                                            </td>
                                                            <?php 
                                                            $rindex++;
                                                            } ?>
                                                            <?php } ?>
                                                        </tr>
                                                        
                                                    </tbody>
                                            
                                                    </table>
                                                    
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="mb-3">
                                                        <?php
                                                        $array = sqlsrv_query($conn, "SELECT * FROM Tb_Request WHERE Request_ID = '$request_id'");
                                                        $array_qry = sqlsrv_fetch_array($array);
                                                        ?>
                                                        <label for="validationCustom03" class="form-label">Finance Verification</label>
                                                        <?php 
                                                        if($array_dy_finance['Remark'] == '' ){
                                                            ?>
                                                            <select class="form-select" id="validationCustom03" name="finance_verification[]" required>
                                                                <option  value="">Select</option>
                                                                <!-- <option <?php if ($array_qry['Finance_Verification'] == "Yes") { ?> selected="selected" <?php } ?> value="Yes">Yes</option> -->
                                                                <option <?php if ($array_qry['Finance_Verification'] == "No" || $array_qry['Finance_Verification'] == '') { ?> selected="selected" <?php } ?>  value="No">No</option>
                                                            </select>
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <input type="text" class="form-control" readonly name="finance_verification[]" value="Verified">
                                                        <?php
                                                        }
                                                        ?>
                                                        
                                                    </div>
                                                </div>
                                                
                                                <div class="row" id="involved_persons_div" style="display:none;">
                                                    <div class="col-md-5">
                                                        <h4>Involved Persons</h4>
                                                        <table class="table table-striped table-bordered table-hover" >
                                                          <thead>
                                                            <tr>
                                                              <th>Purchaser</th>
                                                              <th>Recommender</th>
                                                              <th>Approver</th>
                                                              <th style="display:none;" class="inv_fin_appr">Final Approver</th>
                                                            </tr>
                                                          </thead>
                                                          <tbody id="involved_persons_tbody">

                                                          </tbody>
                                                        </table>
                                                    </div>
                                                </div>

                                                <center style="padding: 35px 0px 0px 0px;">
                                                    <!-- Default -->
                                                        <div class="recommendation_options" <?php if($array_qry['Finance_Verification'] == 'Yes') {?> style="display:none;" <?php } ?>>
                                                            <button class="btn btn-success mb-2 me-4 btn-sm" type="submit" name="save" >
                                                                Recommend
                                                            </button>
                                                            <button class="btn btn-warning mb-2 me-4 btn-sm" type="button" id="send_back_btn">
                                                                Send Back
                                                            </button>
                                                            <button class="btn btn-danger mb-2 me-4 btn-sm" type="button"data-bs-toggle="modal" data-bs-target=".bs-example-modal-centere" >
                                                                Reject
                                                            </button>
                                                        </div>
                                                        <?php 
                                                        if($array_dy_finance['Remark'] == '' && $array_qry['Finance_Verification'] == "Yes"){
                                                            ?>
                                                            <button class="btn btn-danger mb-2 me-4 btn-sm"  type="submit" name="Verification" id="finance_verification_btn" <?php if($array_qry['Finance_Verification'] == 'No') {?> style="display:none;" <?php } ?>>
                                                                Finance Verification
                                                            </button>
                                                        <?php
                                                        }else{
                                                            ?>
                                                            <?php

                                                        }
                                                        ?>
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
                                                                <textarea class="form-control" name="Recommender_back_remark[]" aria-label="With textarea"></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                <button type="submit" class="btn btn-success waves-effect waves-light btn-sm"  name="send" data-bs-dismiss="modal">Submit</button>
                                                            </div>
                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
                                                <div class="modal fade bs-example-modal-centere" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title mt-0">Remarks For Reject</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                                    
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <textarea class="form-control" name="Recommender_Rejected[]" aria-label="With textarea"></textarea>
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
                            <!-- </div> -->
                </div>
                <!-- End Page-content -->


                <!-- Modal -->
                <div class="modal fade" id="sendback_new_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">SendBack</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <div class="col-md-12 form-group">
                            <label for="Recommender_sendback_remark">Remark<span class="text-danger"> *</span></label>
                            <textarea class="form-control" name="Recommender_sendback_remark" id="Recommender_sendback_remark" rows="5" cols="5"></textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="send_back_remark_submit">Send</button>
                      </div>
                    </div>
                  </div>
                </div>
              
                
                <?php include('footer.php') ?>
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>

        <script src="assets/libs/select2/js/select2.min.js"></script>
        <script src="assets/js/pages/form-advanced.init.js"></script>

        <script src="assets/js/app.js"></script>
        <!-- CUSTOM SCRIPT -->
        
        <!-------Model Trag and Trap ---------->
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <!-----------End --------------->

        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.7.570/pdf.min.js"></script>


        <script>
            $(document).ready(function () {
                var value = $('#totale2').val();
                get_involved_persons(value);

                $('.request_selection').change(function () {
                    var myOpt = [];
                    $(".request_selection").each(function () {
                        myOpt.push($(this).val());
                    });
                    $(".request_selection").each(function () {
                        $(this).find("option").attr('disabled', false);
                        var sel = $(this);
                        $.each(myOpt, function (key, value) {
                            if ((value != "New") && (value != sel.val())) {
                                sel.find("option").filter('[value="' + value + '"]').attr('disabled', 'disabled');
                            }
                        });
                    });
                });

                var request_id    = $('#r_id').val();
                var po_creator_id = $('#po_creator_id').val();
                var quotation_value = $('.valueof1').val();

                $.ajax({
                    url: 'common_ajax.php',
                    type: 'POST',
                    data: { Action : 'get_emp_mapping_details',request_id : request_id,quotation_value : quotation_value,po_creator_id : po_creator_id },
                    dataType: "json",
                    success: function(response) {
                        $('#mapping_id').val(response[0].id);
                        $('#approver_id').val(response[0].Approver)
                        $('#approver2_id').val(response[0].Approver_2)

                    }
                });
            });
            // Recommender selection copy as and show hide//
            $(".root").hide();
            $(".answer").find('.request_selection').attr('disabled',false).attr("required", true);
            $(document).on("click", "#submit", function () {
                if (this.checked) {

                    $(".answer").hide();
                    $(".remark").hide();
                    $(".root").show();
                    $(".answer").find('.request_selection').attr('disabled',true).attr("required", false);        
                    
                } else {
                    $(".answer").show();
                    $(".remark").show();
                    $(".root").hide();
                    $(".answer").find('.request_selection').attr('disabled',false).attr("required", true);
                    // $("input:not(:checked)").closest('.target-table').find(".woo").remove();
                }
            }).change();
            // vendor selection//disable Select//
            $('input[type=checkbox]').on("change", function () {
                var target = $(this).parent().find('input[type=hidden]').val();
                if (target == 0) {
                    target = 1;
                } else {
                    target = 0;
                }
                $(this).parent().find('input[type=hidden]').val(target);
            });

            var i = 1;
            $(document).ready(function () {
                $('#vendor-dropdown').on('change', function () {
                    var VendorCode = $(this).val();
                    var VendorCode_closet = $(this);
                    $.ajax({
                        url: "getvendor.php",
                        dataType: 'json',
                        type: 'POST',
                        async: true,
                        data: {
                            VendorCode: VendorCode
                        },
                        success: function (data) {
                            $("#vendorname").val(data.VendorName);
                            $("#city").val(data.City);
                        }
                    });
                });

                $('#vendor-dropdown1').on('change', function () {
                    var VendorCode = $(this).val();
                    var VendorCode_closet = $(this);
                    $.ajax({
                        url: "getvendor.php",
                        dataType: 'json',
                        type: 'POST',
                        async: true,
                        data: {
                            VendorCode: VendorCode
                        },
                        success: function (data) {
                            $("#vendorname1").val(data.VendorName);
                            $("#city1").val(data.City);

                        }
                    });
                });

                $('#vendor-dropdown2').on('change', function () {
                    var VendorCode = $(this).val();
                    var VendorCode_closet = $(this);
                    $.ajax({
                        url: "getvendor.php",
                        dataType: 'json',
                        type: 'POST',
                        async: true,
                        data: {
                            VendorCode: VendorCode
                        },
                        success: function (data) {
                            $("#vendorname2").val(data.VendorName);
                            $("#city2").val(data.City);

                        }
                    });
                });
                $('.vendors-dropdown').change(function () {
                    var myOpt = [];
                    $(".vendors-dropdown").each(function () {
                        myOpt.push($(this).val());
                    });
                    $(".vendors-dropdown").each(function () {
                        $(this).find("option").prop('hidden', false);
                        var sel = $(this);
                        $.each(myOpt, function (key, value) {
                            if ((value != "New") && (value != sel.val())) {
                                sel.find("option").filter('[value="' + value + '"]').prop(
                                    'hidden', true);
                            }
                        });
                    });
                });
            });

            //END//

            //disabled//

            $(document).on('change', '#vendor-dropdown', function () {
                // debugger
                var stateID = $(this).val();
                if (stateID == 'New')
                    $("input.disabled").prop("readonly", false);
                else
                    $("input.disabled").prop("readonly", true);

                if (stateID == 'New')
                    $("input.dis").prop("readonly", true);
                else
                    $("input.dis").prop("readonly", false);
            });

            $(document).on('change', '#vendor-dropdown1', function () {
                // debugger
                var stateID = $(this).val();
                if (stateID == 'New')
                    $("input.disabled1").prop("readonly", false);
                else
                    $("input.disabled1").prop("readonly", true);

                if (stateID == 'New')
                    $("input.dis1").prop("readonly", true);
                else
                    $("input.dis2").prop("readonly", false);
            });

            $(document).on('change', '#vendor-dropdown2', function () {
                // debugger
                var stateID = $(this).val();
                if (stateID == 'New')
                    $("input.disabled2").prop("readonly", false);
                else
                    $("input.disabled2").prop("readonly", true);

                if (stateID == 'New')
                    $("input.dis1").prop("readonly", true);
                else
                    $("input.dis2").prop("readonly", false);
            });

            //disabled end//

            //##########################//

            //SUM //

            $(document).ready(function () {
                update_amounts();
                $('.price').keyup(function () {
                    update_amounts();
                });

                function update_amounts() {
                    var sum = 0.0;
                    $('#myTable1 > tbody  > tr').each(function () {
                        var qty = parseFloat($(this).find('.qty').val() || 0, 10);
                        var price = parseFloat($(this).find('.price').val() || 0, 10);
                        var amount = (qty * price)
                        sum += amount;
                        $(this).find('.amount').val('' + amount);
                    });
                    $('input.totale2').val(sum);
                }
            });

            $(document).ready(function () {
                update_amounts();
                $('.price1').keyup(function () {
                    update_amounts();
                });

                function update_amounts() {
                    var sum = 0.0;
                    $('#myTable2 > tbody  > tr').each(function () {
                        var qty = parseFloat($(this).find('.qty1').val() || 0, 10);
                        var price = parseFloat($(this).find('.price1').val() || 0, 10);
                        var amount = (qty * price)
                        sum += amount;
                        $(this).find('.amount1').val('' + amount);
                    });
                    $('input.total1').val(sum);
                }
            });

            $(document).ready(function () {
                update_amounts();
                $('.price2').keyup(function () {
                    update_amounts();
                });

                function update_amounts() {
                    var sum = 0.0;
                    $('#myTable3 > tbody  > tr').each(function () {
                        var qty = parseFloat($(this).find('.qty2').val() || 0, 10);
                        var price = parseFloat($(this).find('.price2').val() || 0, 10);
                        var amount = (qty * price)
                        sum += amount;
                        $(this).find('.amount2').val('' + amount);
                    });
                    $('input.total2').val(sum);
                }
            });

            //SUM end//
        </script>

        <script type="text/javascript">
            function myFunction() {
                var allRows = document.getElementById('busDataTable').rows;
                for (var i = 0; i < allRows.length; i++) {
                    allRows[i].deleteCell(-1);
                }
            }

            var i = 4;

            // add column
            $('#remove').attr('disabled', true);

            $("#addColumn").click(function () {

                var myOpt = [];
                $(".vendors-dropdown").each(function () {
                    myOpt.push($(this).val());
                });

                //SUM //
                $(document).ready(function () {
                    $("tbody").on("input", ".form-control", function () {
                        const $parent = $(this).closest("#six")
                        var index = $(this).data('id');
                        update_amounts();
                        $('#price3' + index).keyup(function () {
                            update_amounts();
                        });

                        function update_amounts() {
                            var sum = 0.0;

                            $('#myTable4 > tbody  > tr').each(function () {
                                var qty = parseFloat($(this).find('#qty3' + index).val() || 0,
                                    10);
                                var price = parseFloat($(this).find('#price3' + index).val() ||
                                    0, 10);
                                var amount = (qty * price)
                                sum += amount;
                                $(this).find('#amount3' + index).val('' + amount);
                            });
                            $('input#total3' + index).val(sum);
                        }
                    });
                });

                //SUM end//

                $('tbody').find('#head').append("<td>Vendor" + i +
                    " <input type='hidden' class='form-control' value=Vendor " + i + "  name='V1_id[]' > </td>");
                $('tbody').find('#one').append(`<td><select class="form-control vendors-dropdown" name="Vendor_SAP[]" data-id="${i}"  id="vendors-dropdown${i}">
                            <option value="">Select Vendor SAP</option>
                            <option value="New">New Vendor</option>
                            <?php
                            $VendorCode = sqlsrv_query($conn, 'SELECT * FROM vendor_master');
                            while ($c = sqlsrv_fetch_array($VendorCode)) {
                                ?>
                                                <option value='<?php echo $c['VendorCode'] ?>'><?php echo $c['VendorCode'] ?></option>
                                                <?php
                            }
                            ?>
                        </select></td>`);

                $(document).ready(function () {
                    $('.vendors-dropdown').on('change', function () {
                        var VendorCode = $(this).val();
                        var VendorCode_closet = $(this);
                        var index = $(this).data('id');
                        $.ajax({
                            url: "getvendor.php",
                            dataType: 'json',
                            type: 'POST',
                            async: true,
                            data: {
                                VendorCode: VendorCode
                            },
                            success: function (data) {
                                VendorCode_closet.closest('tbody').find("#vendornames" +
                                    index).val(data.VendorName);
                                VendorCode_closet.closest('tbody').find('#citys' + index)
                                    .val(data.City);
                            }

                        });
                    });
                    $('.vendors-dropdown').change(function () {
                        var myOpt = [];
                        $(".vendors-dropdown").each(function () {
                            myOpt.push($(this).val());
                        });
                        $(".vendors-dropdown").each(function () {
                            // debugger
                            var index = $(this).data('id');
                            $(this).find("option").prop('hidden', false);
                            var sel = $(this);
                            $.each(myOpt, function (key, value) {
                                if ((value != "New" + index) && (value != sel.val())) {
                                    sel.find("option").filter('[value="' + value + '"]')
                                        .prop('hidden', true);
                                }
                            });
                        });
                    });
                    $(document).on('change', '.vendors-dropdown', function () {
                        var stateID = $(this).val();
                        var state = $(this).data('id');
                        if (stateID == 'New')
                            $("input.disabled" + state).prop("readonly", false);
                        else
                            $("input.disabled" + state).prop("readonly", true);

                        if (stateID == 'New')
                            $("input.dis" + state).prop("readonly", true);
                        else
                            $("input.dis" + state).prop("readonly", false);
                    });

                });
                $('tbody').find('#two').append(
                    `<td><input type='text' class='form-control disabled${i}'  name='Vendor_Name[]'  id='vendornames${i}'     placeholder='Enter Name of vendor'></td>`
                );
                $('tbody').find('#three').append(
                    `<td><input type='text' class='form-control disabled${i}'  name='Vendor_City[]'  id='citys${i}'      placeholder='Enter City of vendor'></td>`
                );
                $('tbody').find('#four').append(
                    `<td><input type='text' class='form-control dis${i}'  name='vendor_Active_SAP[]' placeholder='Enter vendor is active in SAP'></td>`
                );
                $('tbody').find('#five').append(
                    `<td><input type='text' class='form-control dis${i}'  name='Last_Purchase[]'     placeholder='Enter Last Purchase'></td>`
                );
                $('tbody').find('#six').append(`<td><table id='myTable4'  class="table table-bordered" style="width: 235px !important;">
                                                                <tr>
                                                                    <thead>
                                                                        <th>Quantity</th>
                                                                        <th>Price</th>
                                                                        <th>Total</th>
                                                                    </thead>
                                                                </tr>
                                                                <tbody>
                                                                <?php
                                                                $result = sqlsrv_query($conn, "select * from Tb_Request_Items where Request_ID = '$request_id'");
                                                                while ($row = sqlsrv_fetch_array($result)) {
                                                                    ?>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input type="text" class="form-control form-control-sm qty3" readonly data-id="${i}" id='qty3${i}' name="Quantity_Details[]-<?php echo $row['ID'] ?>${i}" value="<?php echo $row['Quantity'] ?>" placeholder="Enter Quantity Details" > <input type="hidden" class="form-control" name="Meterial_Name[]" value="<?php echo $row['Item_Code'] ?>" >
                                                                                            <input type="hidden" class="form-control" name="V_id[]" value="Vendor ${i}" >
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" class="form-control form-control-sm price3" data-id="${i}" id='price3${i}' name="Price[]-<?php echo $row['ID'] ?>${i}" value="" placeholder="Enter Price" >
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" class="form-control form-control-sm amount3" readonly data-id="${i}" id='amount3${i}' name="Total[]-<?php echo $row['ID'] ?>${i}" value="" >
                                                                                        </td>
                                                                                    </tr>
                                                                                <?php
                                                                }
                                                                ?>
                                                                </tbody>
                                                            </table></td>`);
                $('tbody').find('#seven').append(
                    `<td><input type='text' readonly class='form-control total3' name='Value_Of[]' id='total3${i}' data-id="${i}" placeholder='Enter Value of' ></td>`
                );
                $('tbody').find('#eight').append(
                    "<td><input type='date' class='form-control'  name='Delivery_Time[]'     placeholder='Enter Quantity Details'></td>"
                );
                $('tbody').find('#nine').append(
                    "<td><input type='text' class='form-control'  name='Fright_Charges[]'    placeholder='Enter Value of'></td>"
                );
                $('tbody').find('#ten').append(
                    "<td><input type='text' class='form-control'  name='Insurance_Details[]' placeholder='Enter Fright Charges'></td>"
                );
                $('tbody').find('#eleven').append(
                    "<td><input type='text' class='form-control'  name='GST_Component[]'     placeholder='Enter Insurance Details'></td>"
                );
                $('tbody').find('#twelve').append(
                    "<td><input type='text' class='form-control'  name='Warrenty[]'          placeholder='Enter GST Component'></td>"
                );
                $('tbody').find('#thirteen').append(
                    "<td><input type='text' class='form-control'  name='Payment_Terms[]'     placeholder='Enter Warrenty'></td>"
                );
                $('tbody').find('#checkbox').append(
                    "<td><center><input type='checkbox' /><input type='hidden' name='Requester_Selection[]' value='0' /></center></td>"
                );
                $('tbody').find('#text').append(
                    "<td><input type='text' class='form-control'  name='Requester_Remarks[]' placeholder='Enter Terms'></td>"
                );
                $('tbody').find('#pdf').append(
                    "<td><input type='file' class='form-control'  name='Attachment[]'></td>");

                $('input[type=checkbox]').on("change", function () {
                    var target = $(this).parent().find('input[type=hidden]').val();
                    if (target == 0) {
                        target = 1;
                    } else {
                        target = 0;
                    }
                    $(this).parent().find('input[type=hidden]').val(target);
                });
                // disable select 

                $(".vendors-dropdown").each(function () {
                    var index = $(this).data('id');
                    $(this).find("option").prop('hidden', false);
                    var sel = $(this);
                    $.each(myOpt, function (key, value) {
                        if ((value != "New") && (value != sel.val())) {
                            sel.find("option").filter('[value="' + value + '"]').prop('hidden', true);
                        }
                    });
                });

                i = i + 1;
                $("input[type='file']").on("change", function () {
                    if (this.files[0].size > 2000000) {
                        alert("Please upload file less than 2MB. Thanks!!");
                        $(this).val('');
                    }
                });

                if (i > 1) {
                    $('#remove').attr('disabled', false);
                }
            });

            // file size 

            $("input[type='file']").on("change", function () {
                if (this.files[0].size > 2000000) {
                    alert("Please upload file less than 2MB. Thanks!!");
                    $(this).val('');
                }
            });



            $(".Requester_Selection").change(function () {
                if (this.checked) {

                    $('.Requester_Selection').val(1);
                    //Do stuff
                } else {
                    $('.Requester_Selection').val(0);

                }
            });

            $(document).on('change','#validationCustom03',function(){
                var verifier_status = $(this).val();
                $('#finance_verification_btn').hide();
                $('.recommendation_options').show();
                if(verifier_status == 'Yes') {
                    $('.recommendation_options').hide();
                    $('#finance_verification_btn').show();
                }
            });

             $(document).on('change','.request_selection',function(){
                    var recommend_preference = $(this).val();
                    var row_id = $(this).data('id');
                    var requester_selection =  $('.requester_selection'+row_id).val();
                    var request_id    = $('#r_id').val();
                    var po_creator_id = $('#po_creator_id').val();
                    var quotation_value = $('.valueof'+row_id).val();
                    var current_mapping_id = $('#mapping_id').val();

                    if(recommend_preference == 1) {

                         $.ajax({
                            url: 'common_ajax.php',
                            type: 'POST',
                            data: { Action : 'get_emp_mapping_details',request_id : request_id,quotation_value : quotation_value,po_creator_id : po_creator_id },
                            dataType: "json",
                            success: function(response) {
                                if(recommend_preference != requester_selection) {
                                    var last_approver = (response[0].Approver_2 != '' && response[0].Approver_2 != null) ? response[0].Approver_2+' - '+response[0].approver2_name+' - '+response[0].approver2_Department : response[0].Approver+' - '+response[0].approver_name+' - '+response[0].approver_Department;

                                    if(current_mapping_id != response[0].id) {
                                        var msg = '';
                                        if(response[0].Value == '') {
                                            msg += "You have changed the request selection to within limit range.It will approved by ";
                                        } else if(response[0].Value != '') {
                                            msg += "You have changed the request selection to limit exceed range.It will approved by ";
                                        }
                                        alert(`${msg}`+last_approver);
                                        $('#mapping_id').val(response[0].id);
                                    }
                                }

                                $('#recommendor_id').val(response[0].Recommender);
                                $('#finance_verifier_id').val(response[0].Finance_Verfier);
                                $('#approver_id').val(response[0].Approver);
                                $('#approver2_id').val(response[0].Approver_2);

                                if(quotation_value > 0) {
                                    get_involved_persons(quotation_value);
                                }   
                            }
                        });     
                    }
            });

            function get_involved_persons(value)
            {
                var po_creator_id = $('#po_creator_id').val();
                var Plant = $('#po_plant').val();
                var MaterialGroup = $('#po_mat_group').val();
                var value = value;

                $.ajax({
                    url: "common_ajax.php",
                    type: "POST",
                    data: {
                        Plant : Plant,
                        MaterialGroup : MaterialGroup,
                        Action : 'get_involved_persons',
                        po_creator_id : po_creator_id,
                        value : value
                    },
                    cache: false,
                    dataType: 'json',                        
                    beforeSend:function(){
                        // $('#ajax-preloader').show();
                    },
                    success: function (result) {
                            $('.inv_fin_appr').hide();

                            var table_data = '';
                            if(result.length > 0) {
                                table_data = `<tr>
                                <td>${ (result[0].purchaser_name != null) ? result[0].purchaser_name : '-' }</td>
                                <td>${ (result[0].recommendor_name != null) ? result[0].recommendor_name : '-' }</td>
                                <td>${ (result[0].approver_name != null) ? result[0].approver_name : '-' }</td>`;

                                if(result[0].approver2_name != null) {
                                    table_data += `<td>${ (result[0].approver2_name != null) ? result[0].approver2_name : '-' }</td>`;
                                    $('.inv_fin_appr').show();
                                }

                                table_data += `</tr>`; 

                            }
                            $('#involved_persons_tbody').html(table_data);  
                            $('#involved_persons_div').show();                                    
                    },
                    complete:function(){
                        // $('#ajax-preloader').hide();
                    }
                });
            }

        $(document).on('click','.multi_preview',function(){
            var file_type = $(this).data('filetype');
            var src = $(this).attr('src');
            var row_id = $(this).data('id');

             if(file_type == 'pdf') {
                // var src = $('#pdf_input'+row_id).val();
                var src = $(this).closest('div').find('#pdf_input'+row_id).val();
                // loadPDF(src);

                var src_url = 'https://docs.google.com/viewer?url=https://corporate.rasiseeds.com/corporate/final_request/'+src+'&embedded=true';

                $('.preview_file_pdf_'+row_id).attr('src', src_url+'#toolbar=0');
                $('.preview_file_img_'+row_id).hide();
                $('.preview_file_pdf_'+row_id).show();
             } else {
                $('.preview_file_img_'+row_id).attr('src', src);
                $('.preview_file_pdf_'+row_id).hide();
                $('.preview_file_img_'+row_id).show();
             } 

            $('#attachment_download_'+row_id).attr('href',src);
            $('#file_preview_modal_'+row_id).modal('show');

        });

        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.7.570/pdf.worker.min.js';

        function loadPDF(url) {
            pdfjsLib.getDocument(url).promise.then(function(pdf) {
                var numPages = pdf.numPages;
                for (var pageNum = 1; pageNum <= numPages; pageNum++) {
                    pdf.getPage(pageNum).then(function(page) {
                        var scale = 1.3; // Adjust scale for better viewing on mobile
                        var viewport = page.getViewport({ scale: scale });

                        var canvas = document.createElement('canvas');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        var context = canvas.getContext('2d');
                        var renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };
                        page.render(renderContext);

                        document.getElementById('pdfContainer').appendChild(canvas);
                    });
                }
            }).catch(function(error) {
                console.error('Error loading PDF: ', error);
            });
        }


        $(document).on('click','#send_back_btn',function(){
            $('#sendback_new_modal').modal('show');
        });

        function Alert_Msg(Msg,Type){
            swal({
              title: Msg,
              icon: Type,
            });
        }

        $(document).on('click','#send_back_remark_submit',function(){
            let remark        = $('#Recommender_sendback_remark').val();
            let sendback_from = 'Recommender';
            let request_id    = $('#r_id').val(); 
            $('#sendback_new_modal').modal('hide');


            $.ajax({
                url: "common_ajax.php",
                type: "POST",
                data: {
                    Action : 'purchase_request_sendback',
                    sendback_from : sendback_from,
                    remark : remark,
                    request_id : request_id,
                },
                cache: false,
                dataType: 'json',                        
                beforeSend:function(){
                    $('#ajax-preloader').show();
                },
                success: function (result) {
                    if(result.status == 200) {
                        // Alert_Msg(result.message,'success');
                        swal({
                          title: result.message,
                          icon: 'success',
                        }).then(() => {
                             window.location.href = 'show_recommender.php';
                        });

                    } else {
                        Alert_Msg(result.message,'error');
                    }                               
                },  
                complete:function(){
                    $('#ajax-preloader').hide();
                }
            });
        });


        
            $(".modal").draggable({
                handle: ".modal-content"
            });

                
        </script>

        <!-- CUSTOM SCRIPT END -->

    </body>
</html>
