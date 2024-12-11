<?php
include('../auto_load.php');
include('adition.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function strToHex($string)
{
	$hex = '';
	for ($i = 0; $i < strlen($string); $i++) {
		$hex .= dechex(ord($string[$i]));
	}
	return $hex;
}


function hexToStr($hex)
{
	$string = '';
	for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
		$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
	}
	return $string;
}

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
WHERE Approver = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$Approver_Code = $selector_arr1['Approver'];
$verifier_name = $selector_arr1['Finance_Verfier'];
$verifier_code = $selector_arr1['Finance_Verfier'];
$Purchaser_Code = $selector_arr1['Purchaser'];
// print_r($Purchaser_Code);
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];

$vendor_data_count_sql =  "SELECT COUNT(*) as data_count from Tb_Vendor_Selection where Request_Id = '" . $request_id . "'";

$vendor_data_count_exec = sqlsrv_query($conn, $vendor_data_count_sql);
$vendor_data_count = sqlsrv_fetch_array($vendor_data_count_exec)['data_count'];

$saved_quotation_sql = "SELECT * FROM Tb_Vendor_Selection WHERE Request_ID = '".$request_id."'";
$saved_quotation_exec = sqlsrv_query($conn, $saved_quotation_sql, array(), array("Scrollable" => 'static'));
$saved_count = sqlsrv_num_rows($saved_quotation_exec);
$saved_data = array();
while($saved_quotation_res = sqlsrv_fetch_array($saved_quotation_exec,SQLSRV_FETCH_ASSOC)) {
	$saved_data[] = $saved_quotation_res;
}

$first_preference_quotation_value = '';
foreach ($saved_data as $key => $value) {
	if($value['Requester_Selection'] == 1) {
		$first_preference_quotation_value = $value['Value_Of'];
	}
}

$table_width = '100%';
if ($vendor_data_count == 1) {
	$table_width = "100px";
}


function Post_SAP_Data($data, $url)
{
	$data = "JSON=" . $data;
	$ch = curl_init();
	$options = array(
		CURLOPT_URL            => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLINFO_HEADER_OUT    => true,
		CURLOPT_POST           => true,
		CURLOPT_POSTFIELDS     => $data,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_ENCODING       => "",
		CURLOPT_AUTOREFERER    => true,
		CURLOPT_CONNECTTIMEOUT => 120,
		CURLOPT_TIMEOUT        => 120,
		CURLOPT_MAXREDIRS      => 10,
		// CURLOPT_HTTPHEADER     => array('Content-Type: application/json','Content-Length: ' . strlen($data))
	);
	curl_setopt_array($ch, $options);
	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$res = curl_getinfo($ch);
	if ($httpCode != 200) {
		// echo "Return code is {$httpCode} \n".curl_error($ch);
		return array('Status' => 0, 'Message' => curl_error($ch));
	} else {
		//echo "<pre>".htmlspecialchars($response)."</pre>";
		return array('Status' => 1, 'Message' => $response);
	}
}

if (isset($_POST["save"])) {
	// echo "<pre>";print_r($_POST);exit;
	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
    $updated_query = sqlsrv_fetch_array($update_qry);

    $status       = 'Approved'; 
    $Requested_to = NULL;
    

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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			$Approver2_Selection = $_POST['Approver2_Selection'][$i];
			$Approver2_Remarks = $_POST['Approver2_Remarks'][$i];

			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$query = "INSERT INTO Tb_Approver
			(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
			Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
			Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,
			Approver_Selection,EMP_ID,Requested_to,Approver2_Selection,Approver2_Remarks)VALUES
			('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
			'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
			'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks ',
	  		'$fil',GETDATE(),'$status','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$emp_id','$Requested_to','$Approver2_Selection','$Approver2_Remarks')";

			// $query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approved' WHERE Request_Id = '$request_id' ");
			// $query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approved' WHERE Request_Id = '$request_id' ");

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set status = '$status' WHERE Request_id = '$request_id' ");

            $rs = sqlsrv_query($conn, $query);
        }


	}else{
		// echo "<pre>";print_r($_POST);exit;
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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			$Approver2_Selection = $_POST['Approver2_Selection'][$i];
			$Approver2_Remarks = $_POST['Approver2_Remarks'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;


			 $query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = '$status',Finance_Remarks = '$Finance_Remarks',Total_Budget = '$Total_Budget',Approver_Remarks = '$Approver_Remarks'
            ,Approver_Selection = '$Approver_Selection',Approver2_Selection = '$Approver2_Selection',Approver2_Remarks = '$Approver2_Remarks'  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

            // $query2 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approved' WHERE Request_Id = '$request_id' ");
            // $query3 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approved' WHERE Request_Id = '$request_id' ");

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set status = '$status' WHERE Request_id = '$request_id' ");


        }

    }

	if($updated_query['Request_id'] == ''){
		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];

			$meterial = "INSERT INTO Tb_Approver_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID) VALUES 
			('$request_id','$Meterial_Name','$Quantity_Details','$status','$Price','$Total','$V_id','$emp_id')";

			$rs_material = sqlsrv_query($conn, $meterial);

		}
	}else{

		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {
			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = '$status'  WHERE V_id = '$V_id' and Request_id = '$request_id'");


		}
	}

	//POST SAP
	$Post_qry =  sqlsrv_query($conn, "SELECT Request_Type,Item_Code,Tb_Request_Items.Quantity
	,Tb_Vendor_Selection.Vendor_SAP,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_City
	,Tb_Vendor_Quantity.Quantity,price,Tb_Vendor_Selection.Fright_Charges,Tb_Vendor_Selection.Insurance_Details
	,Tb_Vendor_Selection.GST_Component,Total,Tb_Vendor_Selection.Payment_Terms,Tb_Approver.Approver_Remarks,Plant,Storage_Location,Tb_Vendor_Selection.Warrenty,Tb_Request.Request_ID,Tb_Request_Items.MaterialGroup,Tb_Request.Requested_to  FROM Tb_Request 

	INNER JOIN Tb_Request_Items ON Tb_Request.Request_ID = Tb_Request_Items.Request_Id 
	INNER JOIN (Select * from Tb_Vendor_Selection )Tb_Vendor_Selection  ON Tb_Vendor_Selection.Request_ID = Tb_Request.Request_Id
	INNER JOIN (Select * from Tb_Vendor_Quantity )Tb_Vendor_Quantity ON Tb_Vendor_Quantity.Request_ID = Tb_Request.Request_Id And Tb_Request_Items.Item_Code=Tb_Vendor_Quantity.Meterial_Name
	LEFT JOIN Tb_Approver ON Tb_Approver.Request_ID = Tb_Request.Request_Id ANd Tb_Approver.Vendor_SAP=Tb_Vendor_Selection.Vendor_SAP
	AND Tb_Vendor_Quantity.V_id = Tb_Approver.V_id

	WHERE Tb_Request.Request_ID = '$request_id' AND Tb_Vendor_Selection.Vendor_SAP!='Select Vendor SAP'
	and Tb_Approver.Approver_Selection = '1' 

	group by Request_Type,Item_Code,Tb_Request_Items.Quantity
	,Tb_Vendor_Selection.Vendor_SAP,Tb_Vendor_Selection.Vendor_Name,Tb_Vendor_Selection.Vendor_City
	,Tb_Vendor_Quantity.Quantity,price,Tb_Vendor_Selection.Fright_Charges,Tb_Vendor_Selection.Insurance_Details
	,Tb_Vendor_Selection.GST_Component,Total,Tb_Vendor_Selection.Payment_Terms,Tb_Approver.Approver_Remarks,Plant,Storage_Location,Tb_Vendor_Selection.Warrenty,Tb_Request.Request_ID,Tb_Request_Items.MaterialGroup,Tb_Request.Requested_to");


		//$Postvalue_qry = sqlsrv_fetch_array($Post_qry);


		while ($Postvalue_qry = sqlsrv_fetch_array($Post_qry)) {

			$Request_Type =  $Postvalue_qry['Request_Type'];
			$Request_ID =  $Postvalue_qry['Request_ID'];
			$Plant =  $Postvalue_qry['Plant'];
			$Storage_Location =  $Postvalue_qry['Storage_Location'];
			$Quantity =  $Postvalue_qry['Quantity'];
			$Vendor_SAP =  trim($Postvalue_qry['Vendor_SAP']);
			$Vendor_Name =  $Postvalue_qry['Vendor_Name'];
			$Vendor_City =  $Postvalue_qry['Vendor_City'];
			$vendorquantity =  $Postvalue_qry['vendorquantity'];
			$price =  $Postvalue_qry['price'];
			$Fright_Charges =  $Postvalue_qry['Fright_Charges'];
			$Insurance_Details =  $Postvalue_qry['Insurance_Details'];
			$GST_Component =  $Postvalue_qry['GST_Component'];
			$Total =  $Postvalue_qry['Total'];
			$Payment_Terms =  $Postvalue_qry['Payment_Terms'];
			$Totalbudget =  "0";
			$Availableabudget =  "0";
			$Approver_Remarks =  $Postvalue_qry['Approver_Remarks'];
			$Warrenty =  $Postvalue_qry['Warrenty'];
			$MaterialGroup_SER =  $Postvalue_qry['MaterialGroup'];
			$PURCHASER_ID =  $Postvalue_qry['Requested_to'];

			$array = [];
			$array['PO_REQUEST_ID'] = @$Postvalue_qry['Request_ID'];

			if (@$Postvalue_qry['Request_Type'] == "Asset purchases") {

				$array['REQUEST_TYPE'] = "ZCAP";
			} else if (@$Postvalue_qry['Request_Type'] == "Services") {
				$array['REQUEST_TYPE'] = "ZSER";


			$sql_ser="Select DISTINCT MaterialCode from Purchase_Servie_SAP_Master Where Material_discription='".@$Postvalue_qry['MaterialGroup']."' ";
			$stmt = sqlsrv_query($conn, $sql_ser);
			$Header_data_ser = sqlsrv_fetch_array($stmt,SQLSRV_FETCH_ASSOC);


				$array['MATKL'] = @$Header_data_ser['MaterialCode'];
			} else if (@$Postvalue_qry['Request_Type'] == "Material purchases") {
				$array['REQUEST_TYPE'] = "ZNB";
			}

			$array['PLANT'] = @$Postvalue_qry['Plant'];
			$array['MATERIAL_CODE'] = @$Postvalue_qry['Item_Code'];
			$array['STORAGE_LOCATION'] = @$Postvalue_qry['Storage_Location'];
			$array['QUANTITY'] = @$Postvalue_qry['Quantity'];
			$array['VENDOR_CODE'] = trim(@$Postvalue_qry['Vendor_SAP']);
			// $array['VENDOR_NAME']=trim(@$Postvalue_qry['Vendor_Name']);
			$array['VENDOR_NAME'] = strToHex(trim(@$Postvalue_qry['Vendor_Name']));
			// $array['VENDOR_NAME']=str_replace('&', 'AND', trim(@$Postvalue_qry['Vendor_Name']));

			$array['CITY'] = trim(@$Postvalue_qry['Vendor_City']);
			$array['VENDOR_QUANTITY'] = @$Postvalue_qry['vendorquantity'];
			$array['VENDOR_PRICE'] = round(@$Postvalue_qry['price']);
			$array['FRIGHT_CHARGES'] = round(@$Postvalue_qry['Fright_Charges']);
			$array['INSURANCE_DETAILS'] = round(@$Postvalue_qry['Insurance_Details']);
			$array['GST_AMT'] = round(@$Postvalue_qry['GST_Component']);
			$array['TOTAL_AMOUNT'] = round(@$Postvalue_qry['Total']);
			$array['WARRENTY'] = @$Postvalue_qry['Warrenty'];
			$array['PAYMENT_TERMS'] = @$Postvalue_qry['Payment_Terms'];
			$array['TOTAL_BUDGET_AMT'] = round(@$Totalbudget);
			$array['AVAILABLE_BUDGET_AMT'] = round(@$Availableabudget);
			$array['APPROVERREMARKS'] = @$Postvalue_qry['Approver_Remarks'];
			$array['PURCHASER_ID'] = @$Postvalue_qry['Requested_to'];
			$Sap_Data_Array[] = $array;


			$url = "http://192.168.162.213:8081/PR_PO_CREATE/PRD/ZIN_RFC_PR_PO_CREATION_UPDATE.php";
			$SAP_Json_Data = json_encode($Sap_Data_Array);
			// print_r($SAP_Json_Data);exit;

			$Post_To_SAP_Dets = Post_SAP_Data($SAP_Json_Data, $url);
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
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
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
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>Approved</span></h4>
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
		alert("Approved successsfully");
		window.location = "show_exceed_approver.php";
	</script>
	<?php
	}
}
// echo "<script>window.location.href ='show_approver.php'</script>";
///EXIT;
// send 
if (isset($_POST["send"])) {
	// echo '<pre>';
	// print_r($_POST);die();
	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;
	
            for ($j = 0; $j < count($_POST['Approver_back_remark']); $j++) {
                $Approver_back_remark = $_POST['Approver_back_remark'][$j];

				$query = "INSERT INTO Tb_Approver
				(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
				Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
				Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,Approver_Selection,Approver_back_remark,EMP_ID)VALUES
				('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
				'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
				'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks',
				  '$fil',GETDATE(),'Approver2 Send Back','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Approver_back_remark','$emp_id')";
		
				$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approver2 Send Back', Approver_back_remark = '$Approver_back_remark' WHERE Request_Id = '$request_id' ");
				$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approver2 Send Back', Approver_back_remark = '$Approver_back_remark' WHERE Request_Id = '$request_id' ");

                $rs = sqlsrv_query($conn, $query);
        }
    }

	}else{
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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
				$fil="";
				}else{
				$fil=$_POST['Attachment'][$i];
				}
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

				for ($j = 0; $j < count($_POST['Approver_back_remark']); $j++) {
					$Approver_back_remark = $_POST['Approver_back_remark'][$j];

				$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = 'Approver2 Send Back',Finance_Remarks = '$Finance_Remarks',
				Total_Budget = '$Total_Budget',Approver_Remarks = '$Approver_Remarks',
				Approver_Selection = '$Approver_Selection', Approver_back_remark = '$Approver_back_remark'  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

				$query2 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approver2 Send Back', Approver_back_remark = '$Approver_back_remark' WHERE Request_Id = '$request_id' ");
				$query3 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approver2 Send Back', Approver_back_remark = '$Approver_back_remark' WHERE Request_Id = '$request_id' ");

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

			for ($j = 0; $j < count($_POST['Approver_back_remark']); $j++) {
				$Approver_back_remark = $_POST['Approver_back_remark'][$j];

				$meterial = "INSERT INTO Tb_Approver_Meterial(Request_Id, Meterial_Name, Quantity, status, Price, Total,  V_id,
				Approver_back_remark,EMP_ID) VALUES 
				('$request_id','$Meterial_Name','$Quantity_Details','Approver Send Back','$Price','$Total','$V_id','$Approver_back_remark','$emp_id')";

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

			for ($j = 0; $j < count($_POST['Approver_back_remark']); $j++) {
				$Approver_back_remark = $_POST['Approver_back_remark'][$j];

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = 'Approver Send Back',Approver_back_remark = '$Approver_back_remark' WHERE V_id = '$V_id' and Request_id = '$request_id' ");


		}
	}
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
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
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
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>Send Back</span></h4>
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
		alert("Send Back successsfully");
		window.location = "show_exceed_approver.php";
	</script>
	<?php
	}
	}
//reject
if (isset($_POST["reject"])) {

	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			$Approver2_Selection = $_POST['Approver2_Selection'][$i];
			$Approver2_Remarks = $_POST['Approver2_Remarks'][$i];

			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

            for ($j = 0; $j < count($_POST['Approver_Reject']); $j++) {
                $Approver_Reject = $_POST['Approver_Reject'][$j];

				$query = "INSERT INTO Tb_Approver
				(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
				Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
				Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,Approver_Selection,Approver_Reject,EMP_ID,Approver2_Selection,Approver2_Remarks)VALUES
				('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
				'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
				'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks',
				  '$fil',GETDATE(),'Approver2_Reject','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Approver_Reject','$emp_id','$Approver2_Selection','$Approver2_Remarks')";
		
		$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approver2_Reject', Approver_reject = '$Approver_Reject' WHERE Request_Id = '$request_id' ");
		$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approver2_Reject', Approver_reject = '$Approver_Reject' WHERE Request_Id = '$request_id' ");

                $rs = sqlsrv_query($conn, $query);
        }
    }

	}else{
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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
				$fil="";
				}else{
				$fil=$_POST['Attachment'][$i];
				}
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			for ($j = 0; $j < count($_POST['Approver_Reject']); $j++) {
				$Approver_Reject = $_POST['Approver_Reject'][$j];

				$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = 'Approver2_Reject',Finance_Remarks = '$Finance_Remarks',
				Total_Budget = '$Total_Budget',Recommender_Remarks = '$Recommender_Remarks',
				Approver_Selection = '$Approver_Selection', Approver_Reject = '$Approver_Reject'  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

			$query2 = sqlsrv_query($conn, "UPDATE Tb_Request set status = 'Approver2_Reject', Approver_reject = '$Approver_Reject' WHERE Request_Id = '$request_id' ");
			$query3 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = 'Approver2_Reject', Approver_reject = '$Approver_Reject' WHERE Request_Id = '$request_id' ");

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

			for ($j = 0; $j < count($_POST['Approver_Reject']); $j++) {
				$Approver_Reject = $_POST['Approver_Reject'][$j];

				$meterial = "INSERT INTO Tb_Approver_Meterial(Request_Id, Meterial_Name, Quantity, status, Price, Total,  V_id,
				Approver_Reject,EMP_ID) VALUES 
				('$request_id','$Meterial_Name','$Quantity_Details','Approver2_Reject','$Price','$Total','$V_id','$Approver_Reject','$emp_id')";


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
			for ($j = 0; $j < count($_POST['Approver_Reject']); $j++) {
				$Approver_Reject = $_POST['Approver_Reject'][$j];

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = 'Approver2_Reject',Approver_Reject = '$Approver_Reject' WHERE V_id = '$V_id' and Request_id = '$request_id' ");


		}
	}
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

	// foreach ($to as $address) {
	// 	// $mail->AddAddress($address);
	// 	$mail->AddAddress(trim($address));
	// }
	// $array = "$Cc,$implode";

	// $cc = explode(',', $array);
	// // print_r($cc);exit;
	// foreach ($cc as $ccc) {
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
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
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>Rejected</span></h4>
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
		alert("Reject successsfully");
		window.location = "show_exceed_approver.php";
	</script>
	<?php
	}
}
if (isset($_POST["Verification"])) {

	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$status = 'Review';
            $Verification_Type = '2';

			$query = "INSERT INTO Tb_Approver
			(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
			Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
			Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,
			Approver_Selection,Verification_Type,EMP_ID)VALUES
			('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
			'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
			'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks ',
	  		'$fil',GETDATE(),'$status','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Verification_Type','$emp_id')";

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set Verification_Type = '$Verification_Type' WHERE Request_id = '$request_id' ");

            $rs = sqlsrv_query($conn, $query);
        }
	}else{
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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$status = 'Review';
            $Verification_Type = '2';

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = '$status',Verification_Type = '$Verification_Type'
			  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

			// $query = "INSERT INTO Tb_Approver
			// (Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
			// Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
			// Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,
			// Approver_Selection,Verification_Type,EMP_ID)VALUES
			// ('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
			// '$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
			// '$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks ',
	  		// '$fil',GETDATE(),'$status','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Verification_Type','$emp_id')";

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Recommender set Verification_Type = '$Verification_Type' WHERE Request_id = '$request_id' ");

            // $rs = sqlsrv_query($conn, $query);
        }
	}
	if($updated_query['Request_id'] == ''){

		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];

			$meterial = "INSERT INTO Tb_Approver_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID) VALUES 
			('$request_id','$Meterial_Name','$Quantity_Details','$status','$Price','$Total','$V_id','$emp_id')";

			$rs_material = sqlsrv_query($conn, $meterial);

		}
	}else{
		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];
	
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = '$status'  WHERE V_id = '$V_id' and Request_id = '$request_id'");
	
	
		}
	}
	// exit;
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
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
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
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>'.$status.'</span></h4>
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
		alert("Review successsfully");
		window.location = "show_exceed_approver.php";
	</script>
	<?php
	}
}

if (isset($_POST["Reference"])) {

	$update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Approver WHERE Request_id = '$request_id'");
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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$status = 'Reference';
			$Reference_emil = $_POST['Reference_emil'];
			$Remark_To_Reference = $_POST['Remark_To_Reference'];
            // $Verification_Type = '2';

			$query = "INSERT INTO Tb_Approver
			(Request_id,V_id,Vendor_SAP,Vendor_Name,Vendor_City,vendor_Active_SAP,Last_Purchase,Delivery_Time2,
			Value_Of,Fright_Charges,Insurance_Details,GST_Component,Warrenty,Payment_Terms,Requester_Remarks,
			Recommender_Remarks,Finance_Remarks,Total_Budget,Available_Budget,Approver_Remarks,Attachment,Time_Log,Status,Requester_Selection,Recommender_Selection,
			Approver_Selection,Reference_Empid,Remark_To_Reference,EMP_ID)VALUES
			('$request_id','$V_id','$Vendor_SAP','$Vendor_Name','$Vendor_City',
			'$vendor_Active_SAP','$Last_Purchase','$Delivery_Time','$Value_Of','$Fright_Charges','$Insurance_Details','$GST_Component',
			'$Warrenty','$Payment_Terms','$Requester_Remarks','$Recommender_Remarks','$Finance_Remarks','$Total_Budget ','$Available_Budget ','$Approver_Remarks ',
	  		'$fil',GETDATE(),'$status','$Requester_Selection','$Recommender_Selection','$Approver_Selection','$Reference_emil','$Remark_To_Reference','$emp_id')";

			
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");

            $rs = sqlsrv_query($conn, $query);
        //    exit; 
        }

			// exit;
	}else{
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
			if(!isset($_POST['Finance_Remarks'])){
				$Finance_Remarks="";
				}else{
					$Finance_Remarks = $_POST['Finance_Remarks'][$i];
				}
				if(!isset($_POST['Total_Budget'])){
					$Total_Budget="";
					}else{
						$Total_Budget = $_POST['Total_Budget'][$i];
					}
					if(!isset($_POST['Available_Budget'])){
						$Available_Budget="";
						}else{
							$Available_Budget = $_POST['Available_Budget'][$i];
						}
			$Approver_Remarks = $_POST['Approver_Remarks'][$i];
			$Approver_Selection = $_POST['Approver_Selection'][$i];
			// $fil = $_POST["Attachment"][$i];
			if(!isset($_POST['Attachment'])){
                $fil="";
                }else{
                  $fil=$_POST['Attachment'][$i];
                }
			$V_id = $_POST['V1_id'][$i];
			$emp_id = $Employee_Id;

			$status = 'Reference';
			$Reference_emil = $_POST['Reference_emil'];
			$Remark_To_Reference = $_POST['Remark_To_Reference'];
            // $Verification_Type = '2';

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver set Status = '$status',Reference_Empid = '$Reference_emil',Remark_To_Reference = '$Remark_To_Reference'
			  WHERE V_id = '$V_id' and Request_id = '$request_id' ");

			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request set status = '$status' WHERE Request_Id = '$request_id' ");
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Request_Items set status = '$status' WHERE Request_Id = '$request_id' ");

            // $rs = sqlsrv_query($conn, $query);
        }

	}
			
	if($updated_query['Request_id'] == ''){

		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];
	
			$meterial = "INSERT INTO Tb_Approver_Meterial(Request_id, Meterial_Name, Quantity, Status, Price, Total,  V_id,EMP_ID) VALUES 
			 ('$request_id','$Meterial_Name','$Quantity_Details','$status','$Price','$Total','$V_id','$emp_id')";
	
			$rs_material = sqlsrv_query($conn, $meterial);

		}
	}else{
		for ($i = 0; $i < count($_POST['Quantity_Details']); $i++) {

			$Quantity_Details = $_POST['Quantity_Details'][$i];
			$Meterial_Name = $_POST['Meterial_Name'][$i];
			$Price = $_POST['Price'][$i];
			$Total = $_POST['Total'][$i];
			$V_id = $_POST['V_id'][$i];
	
			$query1 = sqlsrv_query($conn, "UPDATE Tb_Approver_Meterial set Status = '$status'  WHERE V_id = '$V_id' and Request_id = '$request_id'");
	
	
		}
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
	$ecode =  $_POST['Reference_emil'];
	$update_qry1 =  sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code = '$ecode'");
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
	// 	// $mail->AddAddress($address);
	// 	$mail->addCC(trim($ccc));
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
							<h4><span class="badge badge-success"><i class="fa fa-check"></i>'.$status.'</span></h4>
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
		alert("Reference send successsfully");
		window.location = "show_approver.php";
	</script>
	<?php
	}
}

	// total,discount,packageamount display query
	$discount_charges_qry = sqlsrv_query($conn, "SELECT  Tb_Approver.Request_id,Tb_Approver.V_id,
		Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.total_amount,Tb_Approver.discount_amount,Tb_Approver.package_amount,Tb_Approver.package_percentage
		FROM Tb_Approver INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
		WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection ! = '1' 
		GROUP BY Tb_Approver.Request_id,Tb_Approver.V_id,
		Tb_Approver_Meterial.Request_id,Tb_Approver_Meterial.V_id,Tb_Approver.total_amount,Tb_Approver.discount_amount,Tb_Approver.package_amount,Tb_Approver.package_percentage ORDER BY Tb_Approver.V_id DESC");
	$charges_arr = [];	
	while ($discount_charges_res = sqlsrv_fetch_array($discount_charges_qry)) {
		$charges_arr[] = $discount_charges_res;
	}
?>
<!doctype html>
<html lang="en">

<head>


    <meta charset="utf-8" />
    <title>
        <?php echo $Title ?>
    </title>
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
            overflow-x: scroll;
            overflow-y: visible;
            /* width:200px; */
            margin-left: 305px;
        }


        td,
        th {
            padding: 5px 20px;
            /* width: 100px; */
        }

        tbody tr {}

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
    </style>

</head>


<body data-keep-enlarged="true" class="vertical-collpsed">
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
                                        <li class="breadcrumb-item"><a href="show_approver.php">Show Exceed Approver Purchase Request</a></li>
                                        <li class="breadcrumb-item active">Exceed Request Approve</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-end d-none d-sm-block">
                                    <h4 class="">Request ID :
                                        <?php echo $request_id ?>
                                  	<?php
                                     $po_creator_sql = sqlsrv_query($conn, "SELECT TOP 1 Tb_Request.EMP_ID,Tb_Request.Plant,Tb_Request_Items.MaterialGroup from Tb_Request 
                                        left join Tb_Request_Items ON Tb_Request_Items.Request_ID = Tb_Request.Request_ID
                                        where Tb_Request.Request_ID = '$request_id'");
                                      $po_creator = sqlsrv_fetch_array($po_creator_sql);

                                     ?>
                                    </h4>
		                                <input type="hidden" id="r_id" value="<?php echo $request_id; ?>">
		                                <input type="hidden" id="mapping_id">
                                </div>

                                <?php
                                 $po_creator_sql = sqlsrv_query($conn, "SELECT EMP_ID from Tb_Request where Request_ID = '$request_id'");
                                  $po_creator_id = sqlsrv_fetch_array($po_creator_sql)['EMP_ID']; 

                                 $approver_sql = sqlsrv_query($conn, "SELECT EMP_ID from Tb_Approver where Request_ID = '$request_id'");
                                  $approver_id = sqlsrv_fetch_array($approver_sql)['EMP_ID']; 


                                 ?>
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
                                                $plant_sql = "SELECT Plant_Name FROM Plant_Master_PO WHERE Plant_Code = '".$po_creator['Plant']."'";
                                                $plant_sql_exec =  sqlsrv_query($conn,$plant_sql);
                                                $plant_detail = sqlsrv_fetch_array($plant_sql_exec);

                                            ?>
                                            <h1 class="badge bg-success" style="font-size: 15px;">Plant Details - <span><?php echo $po_creator['Plant']; ?> (<?php echo $plant_detail['Plant_Name']; ?>)</span></h1>

                                        <form method="POST" enctype="multipart/form-data">
                                  	       <input type="hidden" id="po_creator_id" value="<?php echo $po_creator_id; ?>">
                                           <input type="hidden" name="recommendor_id" id="recommendor_id">
                                           <input type="hidden" name="finance_verifier_id" id="finance_verifier_id">
                                           <input type="hidden" name="approver_id" id="approver_id">
                                           <input type="hidden" name="approver2_id" id="approver2_id">


			                                     <input type="hidden" id="po_plant" value="<?php echo $po_creator['Plant']; ?>">
			                                     <input type="hidden" id="po_mat_group" value="<?php echo $po_creator['MaterialGroup']; ?>">
											     									<input type="hidden" id="first_pref_quote_value" value="<?php echo $first_preference_quotation_value; ?>">

                                            <div class="table-wrapper">
                                                <table id="busDataTable" class="data heading-table" style="width: <?php echo $table_width; ?>;">
                                                    <?php
                                           


                                                        $selector = sqlsrv_query($conn, "SELECT Tb_Approver.Vendor_SAP, Tb_Approver.Vendor_Name, Tb_Approver.Vendor_City, Tb_Approver.vendor_Active_SAP, Tb_Approver.Last_Purchase, Tb_Approver.Value_Of, Tb_Approver.Fright_Charges, Tb_Approver.Insurance_Details, Tb_Approver.GST_Component, Tb_Approver.Warrenty, Tb_Approver.Payment_Terms, Tb_Approver.Requester_Selection, Tb_Approver.Requester_Remarks, Tb_Approver.Recommender_Selection,Tb_Approver.Delivery_Time2,Tb_Approver.Recommender_Remarks,Tb_Approver.V_id,Tb_Approver.total_amount,Tb_Approver.discount_amount,Tb_Approver.package_amount,Tb_Approver.package_percentage FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection = '1' 
																												GROUP BY Tb_Approver.Vendor_SAP, Tb_Approver.Vendor_Name, Tb_Approver.Vendor_City, Tb_Approver.vendor_Active_SAP, Tb_Approver.Last_Purchase, Tb_Approver.Value_Of, Tb_Approver.Fright_Charges, Tb_Approver.Insurance_Details, Tb_Approver.GST_Component, Tb_Approver.Warrenty, Tb_Approver.Payment_Terms, Tb_Approver.Requester_Selection, Tb_Approver.Requester_Remarks, Tb_Approver.Recommender_Selection,Tb_Approver.Delivery_Time2,Tb_Approver.Recommender_Remarks,Tb_Approver.V_id,Tb_Approver.total_amount,Tb_Approver.discount_amount,Tb_Approver.package_amount,Tb_Approver.package_percentage");
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
                                                            

															$array_Details1 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1'
																												GROUP BY Tb_Approver.V_id
																												 ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details1 = sqlsrv_fetch_array($array_Details1)) {

																?>
                                                            <th>
                                                                <?php echo $array_dy_Details1['V_id'] ?><input
                                                                    type="hidden" class="form-control" name="V1_id[]"
                                                                    value="<?php echo $array_dy_Details1['V_id'] ?>">
                                                            </th>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="one">
                                                            <th>Vendor SAP Code, if available</th>
                                                            <td><input type="text" class="form-control" readonly
                                                                    name="Vendor_SAP[]"
                                                                    value="<?php echo $selector_arr['Vendor_SAP'] ?>">
                                                            </td>
                                                            <?php
															$array_Details1 = sqlsrv_query($conn, "SELECT Tb_Approver.Vendor_SAP,Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.Vendor_SAP,Tb_Approver.V_id
																												ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details1 = sqlsrv_fetch_array($array_Details1)) {

																?>
                                                            <td><input type="text" class="form-control" readonly
                                                                    name="Vendor_SAP[]"
                                                                    value="<?php echo $array_dy_Details1['Vendor_SAP'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="two">
                                                            <th>Name of vendor</th>
                                                            <td><input type="text" class="form-control  disabled"
                                                                    readonly id="vendorname" name="Vendor_Name[]"
                                                                    value="<?php echo $selector_arr['Vendor_Name'] ?>">
                                                            </td>
                                                            <?php
															$array_Details2 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Vendor_Name FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1'
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Vendor_Name
																												 ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details2 = sqlsrv_fetch_array($array_Details2)) {

																?>
                                                            <td><input type="text" class="form-control  disabled"
                                                                    readonly id="vendorname" name="Vendor_Name[]"
                                                                    value="<?php echo $array_dy_Details2['Vendor_Name'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="three">
                                                            <th>City of vendor</th>
                                                            <td><input type="text" class="form-control  disabled"
                                                                    readonly id="city" name="Vendor_City[]"
                                                                    value="<?php echo $selector_arr['Vendor_City'] ?>">
                                                            </td>
                                                            <?php
															$array_Details3 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Vendor_City FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1'
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Vendor_City
																												 ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details3 = sqlsrv_fetch_array($array_Details3)) {

																?>
                                                            <td><input type="text" class="form-control  disabled"
                                                                    readonly id="city" name="Vendor_City[]"
                                                                    value="<?php echo $array_dy_Details3['Vendor_City'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="four">
                                                            <th>Whether vendor is active in SAP?</th>
                                                            <td><input type="text" class="form-control  dis" readonly
                                                                    name="vendor_Active_SAP[]"
                                                                    value="<?php echo $selector_arr['vendor_Active_SAP'] ?>">
                                                            </td>
                                                            <?php
															$array_Details4 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.vendor_Active_SAP FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.vendor_Active_SAP
																												ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details4 = sqlsrv_fetch_array($array_Details4)) {

																?>
                                                            <td><input type="text" class="form-control  dis" readonly
                                                                    name="vendor_Active_SAP[]"
                                                                    value="<?php echo $array_dy_Details4['vendor_Active_SAP'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="five">
                                                            <th>Last purchase made on</th>
                                                            <td><input type="text" class="form-control  dis" readonly
                                                                    name="Last_Purchase[]"
                                                                    value="<?php echo $selector_arr['Last_Purchase'] ?>">
                                                            </td>
                                                            <?php
															$array_Details5 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Last_Purchase FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Last_Purchase
																												ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details5 = sqlsrv_fetch_array($array_Details5)) {

																?>
                                                            <td><input type="text" class="form-control  dis" readonly
                                                                    name="Last_Purchase[]"
                                                                    value="<?php echo $array_dy_Details5['Last_Purchase'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="six">
                                                            <th>
                                                                <table class="table table-bordered"
                                                                    style="width: 300px !important;">
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
                                                                            	<input type="text"
                                                                                    class="form-control-plaintext"
                                                                                    readonly
                                                                                    value="<?php echo trim($row['Description']) ?>"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target=".bs-example-modal-center<?php echo $ID ?>">
                                                                                <!-- Modal -->
                                                                                <div class="modal fade bs-example-modal-center<?php echo $ID ?>"
                                                                                    tabindex="-1" role="dialog"
                                                                                    aria-labelledby="mySmallModalLabel"
                                                                                    aria-hidden="true">
                                                                                    <div
                                                                                        class="modal-dialog modal-dialog-centered">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h5
                                                                                                    class="modal-title mt-0">
                                                                                                    Last Purchace Date
                                                                                                    For Vendor</h5>
                                                                                                <button type="button"
                                                                                                    class="btn-close"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close">

                                                                                                </button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <table
                                                                                                    class="table table-bordered">
                                                                                                    <thead>
                                                                                                        <tr>
                                                                                                            <td>Vendor
                                                                                                                Code
                                                                                                            </td>
                                                                                                            <th>Material
                                                                                                                Name
                                                                                                            </th>
                                                                                                            <th>Price
                                                                                                            </th>
                                                                                                            <th>Purchace
                                                                                                                Date
                                                                                                            </th>
                                                                                                        </tr>
                                                                                                    </thead>
                                                                                                    <tbody>
                                                                                                        <?php
                                                                                                                $result1 = sqlsrv_query($conn, "SELECT TOP 3 * FROM MIGO_DET WHERE  MATNR = '$ItemCode' ORDER BY LINE_ID DESC ");
                                                                                                                while ($row1 = sqlsrv_fetch_array($result1)) {
                                                                                                            ?>
                                                                                                        <tr>
                                                                                                            <td>
                                                                                                                <?php echo $row1['LIFNR'] ?>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <?php echo $row['Description'] ?>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <?php echo $row1['MENGE'] ?>
                                                                                                            </td>
                                                                                                            <td>
                                                                                                                <?php echo $row1['BUDAT_MKPF']->format('Y-m-d') ?>
                                                                                                            </td>
                                                                                                        </tr>
                                                                                                        <?php
                                                                                                                        }
                                                                                                                    ?>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button"
                                                                                                    class="btn btn-danger waves-effect waves-light btn-sm"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    aria-label="Close">Close</button>
                                                                                            </div>
                                                                                        </div><!-- /.modal-content -->
                                                                                    </div><!-- /.modal-dialog -->
                                                                                </div><!-- /.modal -->
                                                                            </td>
                                                                        </tr>
                                                                        <?php
																		}
																		?>
                                                                    </tbody>
                                                                </table>

                                                            </th>
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
																																			$i = 1;
																																			$result = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver_Meterial.Quantity,Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver_Meterial.discount_percentage FROM Tb_Approver 
																																													INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																																													WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection = '1'
																																													GROUP BY Tb_Approver.V_id,Tb_Approver_Meterial.Quantity,Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver_Meterial.discount_percentage
																																													 ORDER BY Tb_Approver.V_id DESC");
																																			while ($row = sqlsrv_fetch_array($result)) {
																																				?>
                                                                        <tr class="material-tr">
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control qty" readonly
                                                                                    name="Quantity_Details[]"
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
                                                                                    class="form-control amount" readonly
                                                                                    name="Total[]"
                                                                                    value="<?php echo $row['Total'] ?>" style="width: 75px;">
                                                                            </td>
                                                                        </tr>
                                                                        <?php $mat_ind++; } ?>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                            <?php
																														$vendor_ind = 2;
																														$array_Details111 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																																											INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																																											WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1'
																																											GROUP BY Tb_Approver.V_id
																																											 ORDER BY Tb_Approver.V_id DESC");
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
																																			$array_Details1111 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver_Meterial.Quantity,Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver_Meterial.gst_percentage,Tb_Approver_Meterial.discount_percentage FROM Tb_Approver 
																																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = '$arr' AND Tb_Approver_Meterial.V_id = '$arr' AND Tb_Approver.Approver_Selection != '1'
																																												GROUP BY Tb_Approver.V_id,Tb_Approver_Meterial.Quantity,Tb_Approver_Meterial.Meterial_Name,Tb_Approver_Meterial.Price,Tb_Approver_Meterial.Total,Tb_Approver_Meterial.discount_percentage,Tb_Approver_Meterial.gst_percentage
																																												 ORDER BY Tb_Approver.V_id DESC");
																																			while ($array_dy_Details1111 = sqlsrv_fetch_array($array_Details1111)) {

																																				?>
                                                                        <tr class="material-tr">
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control qty" readonly
                                                                                    name="Quantity_Details[]"
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
                                                                                    class="form-control amount" readonly
                                                                                    name="Total[]"
                                                                                    value="<?php echo $array_dy_Details1111['Total'] ?>" style="width: 75px;">
                                                                            </td>
                                                                        </tr>
                                                                        <?php $mat_ind++; } ?>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                            <?php $vendor_ind++; } ?>
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
                                                            <th>Fright Charges</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Fright_Charges[]"
                                                                    value="<?php echo $selector_arr['Fright_Charges'] ?>">
                                                            </td>
															                              <?php
																														$array_Details9 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Fright_Charges FROM Tb_Approver 
																																											INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																																											WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																																											GROUP BY Tb_Approver.V_id,Tb_Approver.Fright_Charges
																																											ORDER BY Tb_Approver.V_id DESC");
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
																														$array_Details10 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Insurance_Details FROM Tb_Approver 
																																											INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																																											WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																																											GROUP BY Tb_Approver.V_id,Tb_Approver.Insurance_Details
																																											ORDER BY Tb_Approver.V_id DESC");
																														while ($array_dy_Details10 = sqlsrv_fetch_array($array_Details10)) {

																															?>
                                                            <td><input type="text" readonly class="form-control "
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
																														$array_Details11 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.GST_Component FROM Tb_Approver 
																																											INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																																											WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																																											GROUP BY Tb_Approver.V_id,Tb_Approver.GST_Component
																																											ORDER BY Tb_Approver.V_id DESC");
																														while ($array_dy_Details11 = sqlsrv_fetch_array($array_Details11)) {

																															?>
                                                            <td><input type="text" readonly class="form-control "
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
                                                            <td><input type="text" readonly class="form-control valueof1"
                                                                    name="Value_Of[]" id="totale2"
                                                                    title="Total + AdditionalCharges"
                                                                    value="<?php echo $selector_arr['Value_Of'] ?>">
                                                            </td>
                                                            <?php
																														$array_Details7 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Value_Of FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1'
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Value_Of
																												 ORDER BY Tb_Approver.V_id DESC");

																														$tot_index = 2;
																														while ($array_dy_Details7 = sqlsrv_fetch_array($array_Details7)) {

																															?>
                                                            <td><input type="text" readonly class="form-control valueof<?php echo $tot_index; ?>"
                                                                    name="Value_Of[]" id="totale2"
                                                                    title="Total + AdditionalCharges"
                                                                    value="<?php echo $array_dy_Details7['Value_Of'] ?>">
                                                            </td>
                                                            <?php $tot_index++; } ?>
                                                        </tr>
                                                        <tr id="eight">
                                                            <th>Delivery Time</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Delivery_Time[]"
                                                                    value="<?php echo $selector_arr['Delivery_Time2'] ?>">
                                                            </td>
                                                            <?php
															$array_Details8 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Delivery_Time2 FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Delivery_Time2
																												ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details8 = sqlsrv_fetch_array($array_Details8)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Delivery_Time[]"
                                                                    value="<?php echo $array_dy_Details8['Delivery_Time2'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="twelve">
                                                            <th>Warrenty</th>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Warrenty[]"
                                                                    value="<?php echo $selector_arr['Warrenty'] ?>">
                                                            </td>
                                                            <?php
																														$array_Details12 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Warrenty FROM Tb_Approver 
																																											INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																																											WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																																											GROUP BY Tb_Approver.V_id,Tb_Approver.Warrenty
																																											ORDER BY Tb_Approver.V_id DESC");
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
															$array_Details13 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Payment_Terms FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Payment_Terms
																												ORDER BY Tb_Approver.V_id DESC");
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
                                                            <th>Requester's Selection
                                                            </th>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Requester_Selection[]"
                                                                    value="<?php echo $selector_arr['Requester_Selection'] ?>">
                                                            </td>
                                                            <?php
															$array_Details15 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Requester_Selection FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Requester_Selection
																												ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details15 = sqlsrv_fetch_array($array_Details15)) {

																?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Requester_Selection[]"
                                                                    value="<?php echo $array_dy_Details15['Requester_Selection'] ?>">
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
                                                            <th>Requester's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name ?>)</span>
                                                            </th>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Requester_Remarks[]"
                                                                    value="<?php echo $selector_arr['Requester_Remarks'] ?>">
                                                            </td>
                                                            <?php
															$array_Details15 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Requester_Remarks FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Requester_Remarks
																												ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details15 = sqlsrv_fetch_array($array_Details15)) {

																?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Requester_Remarks[]"
                                                                    value="<?php echo $array_dy_Details15['Requester_Remarks'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                        </tr>
                                                        <tr id="tara" class="src-table">
                                                            <th>Recommender's Selection
                                                            </th>
                                                            <td class="woo">
                                                                <input type="text" class="form-control " readonly
                                                                    name="Recommender_Selection[]"
                                                                    value="<?php echo $selector_arr['Recommender_Selection'] ?>">
                                                            </td>
                                                            <?php
															$array_Details14 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Recommender_Selection FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Recommender_Selection
																												ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

																?>
                                                            <td class="woo">
                                                                <input type="text" class="form-control " readonly
                                                                    name="Recommender_Selection[]"
                                                                    value="<?php echo $array_dy_Details14['Recommender_Selection'] ?>">
                                                            </td>
                                                            <?php } ?>

                                                        </tr>
                                                        <tr>
														<?php 
															$recommender_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$Recommender_Code' ");
															$recommender_names = sqlsrv_fetch_array($recommender_name);
															$name1 = $recommender_names['Employee_Name'];
															?>
                                                            <th>Recommender's Remarks<span
                                                                    class="required-label">&nbsp;&nbsp;(<?php echo $name1 ?>)</span>
                                                            </th>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Recommender_Remarks[]"
                                                                    value="<?php echo $selector_arr['Recommender_Remarks'] ?>">
                                                            </td>
                                                            <?php
															$array_Details121 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Recommender_Remarks FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Recommender_Remarks
																												ORDER BY Tb_Approver.V_id DESC");
															while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

																?>
                                                            <td><input type="text" readonly class="form-control "
                                                                    name="Recommender_Remarks[]"
                                                                    value="<?php echo $array_dy_Details121['Recommender_Remarks'] ?>">
                                                            </td>
                                                            <?php } ?>

                                                        </tr>



                                                        <?php 
                                                        $array_finance = sqlsrv_query($conn, "SELECT  * 
                                                        FROM Tb_Finance_verifier INNER JOIN Tb_Approver ON Tb_Finance_verifier.Request_Id = Tb_Approver.Request_id 
                                                        WHERE Tb_Finance_verifier.Request_id ='$request_id' ");
                                                        $array_dy_finance = sqlsrv_fetch_array($array_finance);
                                                        ?>
                                                        <?php 
                                                        if($array_dy_finance['Remark'] == '' ){

                                                        }else{
                                                            ?>
                                                        <tr>
                                                            <th>Total Budget (in INR)<span
                                                                    class="required-label"></span>
                                                            </th>
                                                            <?php
															$array_Deta = sqlsrv_query($conn, "SELECT  * 
															FROM Tb_Approver INNER JOIN Tb_Finance_verifier ON Tb_Approver.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Approver.Approver_Selection = '1'");
															$array_dy_Deta = sqlsrv_fetch_array($array_Deta)
															?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Total_Budget[]"
                                                                    value="<?php echo $array_dy_Deta['Total_Budget'] ?>">
                                                            </td>
                                                            <?php 
															$array_Details1212 = sqlsrv_query($conn, "SELECT  *
															FROM Tb_Approver INNER JOIN Tb_Finance_verifier ON Tb_Approver.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Approver.Approver_Selection ! = '1' 
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
                                                            <th>Available Budget (in INR)<span
                                                                    class="required-label"></span>
                                                            </th>
                                                            <?php
															$array_Deta = sqlsrv_query($conn, "SELECT  * 
															FROM Tb_Approver INNER JOIN Tb_Finance_verifier ON Tb_Approver.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Approver.Approver_Selection = '1'");
															$array_dy_Deta = sqlsrv_fetch_array($array_Deta)
															?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Available_Budget[]"
                                                                    value="<?php echo $array_dy_Deta['Available_Budget'] ?>">
                                                            </td>
                                                            <?php 
															$array_Details1212 = sqlsrv_query($conn, "SELECT  *
															FROM Tb_Approver INNER JOIN Tb_Finance_verifier ON Tb_Approver.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Approver.Approver_Selection ! = '1' 
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
														<?php 
															$finance_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$verifier_code' ");
															$finance_names = sqlsrv_fetch_array($finance_name);
															$name2 = $finance_names['Employee_Name'];
															?>
                                                            <th>Financer's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name2 ?>)</span>
                                                            </th>
                                                            <?php
															$array_Deta = sqlsrv_query($conn, "SELECT  * 
															FROM Tb_Approver INNER JOIN Tb_Finance_verifier ON Tb_Approver.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Approver.Approver_Selection = '1'");
															$array_dy_Deta = sqlsrv_fetch_array($array_Deta)
															?>
                                                            <td><input type="text" class="form-control " readonly
                                                                    name="Finance_Remarks[]"
                                                                    value="<?php echo $array_dy_Deta['Remark'] ?>">
                                                            </td>
                                                            <?php 
															$array_Details1212 = sqlsrv_query($conn, "SELECT  *
															FROM Tb_Approver INNER JOIN Tb_Finance_verifier ON Tb_Approver.Request_id = Tb_Finance_verifier.Request_Id 
															WHERE Tb_Approver.Request_id ='$request_id' AND Tb_Approver.Vendor_Name = Tb_Finance_verifier.Vendor_Name AND Tb_Approver.Approver_Selection ! = '1' 
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
                                                        <tr id="tara" class="src-table">
                                                            <th>Approver's Selection
                                                            </th>
															                              <?php
															                             

																														$array_Details142 = sqlsrv_query($conn, "SELECT * FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id ORDER BY Tb_Approver.V_id DESC",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
																														$vendor_count = sqlsrv_num_rows($array_Details142);

																														$array_dy_Details142 = sqlsrv_fetch_array($array_Details142);
																															?>

                                                            <td class="woo">
                                                               <!--  <select class="form-control request_selection"
                                                                    name="Approver_Selection[]" readonly>
                                                                    <option value="">Select Approver Selection
                                                                    </option> -->
                                                                    <?php
                                                                    

																																		$array_Details1421 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Approver_Selection FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection = '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Approver_Selection
																												ORDER BY Tb_Approver.V_id DESC");

																																		$array_dy_Details1421 = sqlsrv_fetch_array($array_Details1421);
																																			?>
                                                                   <!--  <?php for($i = 1;$i<= $vendor_count;$i++) { ?>
                                                                    <option <?php if
                                                                        ($array_dy_Details1421['Recommender_Selection']==$i)
                                                                        { ?> selected="selected"
                                                                        <?php } ?>value="
                                                                        <?php echo $i; ?>">
                                                                        <?php echo $i; ?>
                                                                    </option>
                                                                    <?php } ?> -->

                                                                </select>
                                                            		<input type="text" class="form-control" name="Approver_Selection[]" readonly value="<?php echo $array_dy_Details1421['Approver_Selection']; ?>">
                                                            </td>
                                                            <?php
																														$array_Details14 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id
																												ORDER BY Tb_Approver.V_id DESC");
																														while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

																															?>

                                                            <td class="answer">
                                                                <?php
                                                                
																																$array_Details142 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Approver_Selection FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = '".$array_dy_Details14['V_id']."'
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Approver_Selection
																												 ORDER BY Tb_Approver.V_id DESC",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
																																$vendor_count = sqlsrv_num_rows($array_Details142);

																																$array_dy_Details142 = sqlsrv_fetch_array($array_Details142);

																																	?>
                                                              <!--   <select class="form-control request_selection"
                                                                    name="Approver_Selection[]">
                                                                    <option value="">Select Approver Selection
                                                                    </option>
                                                                    <?php for($i = 1;$i<= $vendor_count;$i++) { ?>
                                                                    <option <?php if
                                                                        ($array_dy_Details14['Approver_Selection']==$i)
                                                                        { ?> selected="selected"
                                                                        <?php } ?>value="
                                                                        <?php echo $i; ?>">
                                                                        <?php echo $i; ?>
                                                                    </option>
                                                                    <?php } ?>

                                                                </select> -->
                                                            		<input type="text" class="form-control" name="Approver_Selection[]" readonly value="<?php echo $array_dy_Details142['Approver_Selection']; ?>">
                                                            </td>

                                                            <?php } ?>

                                                        </tr>
                                                        <tr class="remark">
                                                            <?php
																															$array_Details11 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Approver_Remarks FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection = '1'
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Approver_Remarks
																												 ORDER BY Tb_Approver.V_id DESC");
																															$array_dy_Details11 = sqlsrv_fetch_array($array_Details11);
																															?>
                                                            <?php
																															if($array_dy_Details11['Approver_Remarks'] == ''){
																																?>
																																<?php 
																															$approver_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
																															WHERE Employee_Code = '$approver_id' ");
																															$approver_names = sqlsrv_fetch_array($approver_name);
																															$name3 = $approver_names['Employee_Name'];
																															?>
                                                            <th>Approver's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name3 ?>)</span>
                                                            </th>
                                                            <?php
																														$array_Details11 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Approver_Remarks FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Approver_Remarks
																												ORDER BY Tb_Approver.V_id DESC");
																														while ($array_dy_Details11 = sqlsrv_fetch_array($array_Details11)) {

																															?>
                                                            <td><input type="text" class="form-control "
                                                                    name="Approver_Remarks[]"
                                                                    placeholder="Enter Approver Remarks" readonly></td>
                                                            <?php } ?>
                                                            <?php
																														}else{
																															?>
                                                            <?php 
																														$approver_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
																														WHERE Employee_Code = '$approver_id' ");
																														$approver_names = sqlsrv_fetch_array($approver_name);
																														$name3 = $approver_names['Employee_Name'];
																														?>
                                                            <th>Approver's Remarks<span class="required-label">&nbsp;&nbsp;(<?php echo $name3 ?>)</span>
                                                            </th>
                                                            <?php
																														$array_Details11 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Approver_Remarks FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection = '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Approver_Remarks
																												ORDER BY Tb_Approver.V_id DESC");
																														$array_dy_Details11 = sqlsrv_fetch_array($array_Details11);
																														?>
                                                            <td><input type="text" class="form-control "
                                                                    name="Approver_Remarks[]"
                                                                    value="<?php echo $array_dy_Details11['Approver_Remarks'] ?>" readonly>
                                                            </td>
                                                            <?php
																														$array_Details121 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id,Tb_Approver.Approver_Remarks FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Approver_Selection != '1' 
																												GROUP BY Tb_Approver.V_id,Tb_Approver.Approver_Remarks
																												ORDER BY Tb_Approver.V_id DESC");
																														while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

																															?>
                                                            <td><input type="text" class="form-control "
                                                                    name="Approver_Remarks[]"
                                                                    value="<?php echo $array_dy_Details121['Approver_Remarks'] ?>" readonly>
                                                            </td>
                                                            <?php } ?>
                                                            <?php
																														}
																														?>

                                                        </tr>

                                                        <!-- Approver 2 selection and remarks  -->
                                                        <tr id="tara" class="src-table">
                                                            <th>Approver2 Selection
                                                            </th>
															                              <?php
																														$array_Details142 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id 
																												GROUP BY Tb_Approver.V_id
																												ORDER BY Tb_Approver.V_id DESC",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
																														$vendor_count = sqlsrv_num_rows($array_Details142);

																														$array_dy_Details142 = sqlsrv_fetch_array($array_Details142);
																															?>

                                                            <td class="woo">
                                                                <select class="form-control request_selection"
                                                                    name="Approver2_Selection[]" required>
                                                                    <option value="">Select Approver2 Selection
                                                                    </option>
                                                                    <?php
																																		$array_Details1421 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																																INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																																WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection = '1' 
																																GROUP BY Tb_Approver.V_id
																																ORDER BY Tb_Approver.V_id DESC");

																																		$array_dy_Details1421 = sqlsrv_fetch_array($array_Details1421);
																																			?>
                                                                    <?php for($i = 1;$i<= $vendor_count;$i++) { ?>
                                                                    <option <?php if
                                                                        ($array_dy_Details1421['Recommender_Selection']==$i)
                                                                        { ?> selected="selected"
                                                                        <?php } ?>value="<?php echo $i; ?>">
                                                                        <?php echo $i; ?>
                                                                    </option>
                                                                    <?php } ?>

                                                                </select>
                                                          
                                                            </td>
                                                            <?php
																														$array_Details14 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection != '1' 
																												GROUP BY Tb_Approver.V_id
																												ORDER BY Tb_Approver.V_id DESC");
																														while ($array_dy_Details14 = sqlsrv_fetch_array($array_Details14)) {

																															?>

                                                            <td class="answer">
                                                                <?php
																																$array_Details142 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id 
																												GROUP BY Tb_Approver.V_id
																												ORDER BY Tb_Approver.V_id DESC",[],array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
																																$vendor_count = sqlsrv_num_rows($array_Details142);

																																$array_dy_Details142 = sqlsrv_fetch_array($array_Details142);

																																	?>
                                                                <select class="form-control request_selection"
                                                                    name="Approver2_Selection[]">
                                                                    <option value="">Select Approver2 Selection
                                                                    </option>
                                                                    <?php for($i = 1;$i<= $vendor_count;$i++) { ?>
                                                                    <option <?php if
                                                                        ($array_dy_Details14['Recommender_Selection']==$i)
                                                                        { ?> selected="selected"
                                                                        <?php } ?>value="<?php echo $i; ?>">
                                                                        <?php echo $i; ?>
                                                                    </option>
                                                                    <?php } ?>

                                                                </select>
                                                            	
                                                            </td>

                                                            <?php } ?>

                                                        </tr>
                                                        <tr class="remark">
                                                            <?php
																															$array_Details11 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id  
																												GROUP BY Tb_Approver.V_id
																												ORDER BY Tb_Approver.V_id DESC");
																															$array_dy_Details11 = sqlsrv_fetch_array($array_Details11);
																															?>
                                                            <?php
																															if($array_dy_Details11['Approver_Remarks'] == ''){
																																?>
																																<?php 
																															$approver_name = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
																															WHERE Employee_Code = '$Approver_Code' ");
																															$approver_names = sqlsrv_fetch_array($approver_name);
																															$name3 = $approver_names['Employee_Name'];
																															?>
                                                            <th>Approver2 Remarks</th>
                                                            <?php
																														$array_Details11 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id 
																												GROUP BY Tb_Approver.V_id
																												ORDER BY Tb_Approver.V_id DESC");
																														while ($array_dy_Details11 = sqlsrv_fetch_array($array_Details11)) {

																															?>
                                                            <td><input type="text" class="form-control "
                                                                    name="Approver2_Remarks[]"
                                                                    placeholder="Enter Approver Remarks"></td>
                                                            <?php } ?>
                                                            <?php
																														}else{
																															?>
                                                            <?php 
																														$approver_name = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM HR_Master_Table 
																														WHERE Employee_Code = '$Approver_Code' ");
																														$approver_names = sqlsrv_fetch_array($approver_name);
																														$name3 = $approver_names['Employee_Name'];
																														?>
                                                            <th>Approver2 Remarks</th>
                                                            <?php
																														$array_Details11 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection = '1'
																												GROUP BY Tb_Approver.V_id
																												 ORDER BY Tb_Approver.V_id DESC");
																														$array_dy_Details11 = sqlsrv_fetch_array($array_Details11);
																														?>
                                                            <td><input type="text" class="form-control "
                                                                    name="Approver2_Remarks[]"
                                                                    value="">
                                                            </td>
                                                            <?php
																														$array_Details121 = sqlsrv_query($conn, "SELECT Tb_Approver.V_id FROM Tb_Approver 
																												INNER JOIN Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id
																												WHERE Tb_Approver.Request_id = '$request_id' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id AND Tb_Approver.Recommender_Selection != '1' 
																												GROUP BY Tb_Approver.V_id
																												ORDER BY Tb_Approver.V_id DESC");
																														while ($array_dy_Details121 = sqlsrv_fetch_array($array_Details121)) {

																															?>
                                                            <td><input type="text" class="form-control "
                                                                    name="Approver2_Remarks[]"
                                                                    value="<?php echo $array_dy_Details121['Approver_Remarks'] ?>">
                                                            </td>
                                                            <?php } ?>
                                                            <?php
																														}
																														?>

                                                        </tr>

                                                        <!-- Approver 2 selection and remarks end -->

                                                        <tr id="pdf">
                                                            <?php
																	$view16 = sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id' ");
																	$view_query16 = sqlsrv_fetch_array($view16);
															?>
                                                            <?php 
															if($view_query16['Attachment'] == ''){

															}else{
																?>
                                                            <th>PDF / JPG attachment
                                                            </th>
                                                            <?php
																	$view18 = sqlsrv_query($conn, "SELECT * FROM Tb_Recommender WHERE Request_id = '$request_id' ");
																	$rindex = 1;
																	while ($view_query18 = sqlsrv_fetch_array($view18)) {
																	$file_extension = explode('.', $view_query18['Attachment'])[1];
																		
															?>
                                                            <td>
                                                            	<input type="text" class="form-control" readonly
                                                                    value="<?php echo $view_query18['Attachment'] ?>"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target=".bs-example-modal-center1<?php echo $view_query18['id'] ?>">

																															<!-- file preview modal -->
																															<div class="modal fade" id="file_preview_modal_<?php echo $rindex; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
																															  <div class="modal-dialog modal-lg">
																															    <div class="modal-content">
																															      <div class="modal-header">
																															        <h1 class="modal-title fs-5" id="exampleModalLabel">File Preview</h1>
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

																															<div class="row mt-2 display_section p-3" id="file_display_section_<?php echo $rindex; ?>" <?php if($rindex == 1) { ?> style="border: 2px dashed blue;height: 400px;overflow-y: auto;" <?php } else { ?> style="border: 2px dashed #ccc;height: 400px;overflow-y: auto;" <?php } ?>>
																												                	<?php 
																												                	$multi_files = explode(',',$view_query18['Attachment']);

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
                                                            <?php $rindex++; } ?>
                                                            <?php } ?>
                                                        </tr>

                                                    </tbody>

                                                </table>
                                                <table>
                                                    <tbody>
                                                        <tr>
														<?php
															$array_Details11 = sqlsrv_query($conn, "SELECT Reference_Empid,Remark_To_Reference,Reference_Remark
															FROM Tb_Approver 
															WHERE Request_id ='$request_id' 
															GROUP BY Reference_Empid,Remark_To_Reference,Reference_Remark");
															$array_dy_Details11 = sqlsrv_fetch_array($array_Details11);
															?>
                                                            <?php
															if($array_dy_Details11['Reference_Empid'] == ''){
																?>
																	<th>Additional Reference&nbsp;&nbsp;<input type="checkbox"
                                                                    	id="submit"></th>
																	<td class="Reference">
																		<select class="form-control kim select2"
																			name="Reference_emil">
																			<option selected disabled>Select Reference</option>
																			<?php
																				$plant = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employment_status = 'Active' AND Grade IN('SM1','SM2','SM3','SM4','SM5','SM6','SM7','M1','M2','M3') ");
																				while ($c = sqlsrv_fetch_array($plant)) {
																					?>
																			<option
																				value="<?php echo $c['Employee_Code'] ?>">
																				<?php echo $c['Employee_Name'] ?>&nbsp;-&nbsp;
																				<?php echo $c['Employee_Code'] ?>&nbsp;-&nbsp;
																				<?php echo $c['Department'] ?>
																			</option>
																			<?php
																				}
																				?>
																		</select>
																	</td>
																	<td class="Reference">
																		<textarea rows="1" cols="40" class="form-control kim"
																			placeholder="Remarks For Reference"
																			name="Remark_To_Reference"></textarea>
																	</td>
																<?php
															}else{
																?>
																<th>Additional Reference</th>
															<td>Reference Employee
															<?php 
															$emp = $array_dy_Details11['Reference_Empid'];
															$aditional_emp = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table 
															WHERE Employee_Code = '$emp' ");
															$aditional_emps = sqlsrv_fetch_array($aditional_emp);
															$name4 = $aditional_emps['Employee_Name'];
															$name4_dep = $aditional_emps['Department'];
															?>
                                                            
                                                                <textarea rows="3" cols="40" class="form-control " 
                                                                    readonly=""
                                                                    name=""><?php echo $name4 ?>&nbsp;-&nbsp;<?php echo $name4_dep ?></textarea>
                                                            </td>
                                                            <td>Remark To Reference&nbsp;&nbsp;(<?php echo $name3 ?>)
                                                                <textarea rows="3" cols="40" class="form-control " 
                                                                    readonly=""
                                                                    name=""><?php echo $array_dy_Details11['Remark_To_Reference'] ?></textarea>
                                                            </td>
                                                            <td>Reference Remark&nbsp;&nbsp;(<?php echo $name4 ?>)
                                                                <textarea rows="3" cols="40" class="form-control "
																	readonly=""
                                                                    name=""><?php echo $array_dy_Details11['Reference_Remark'] ?></textarea>
                                                            </td>
																<?php
															}
															?>
                                                            
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <center style="padding: 35px 0px 0px 0px;">
                                                <!-- Default -->
                                                <button class="btn btn-success mb-2 me-4 btn-sm one" type="submit"
                                                    name="save">
                                                    Submit
                                                </button>
												<button class="btn btn-info mb-2 me-4 btn-sm two" type="submit"
                                                    name="Reference">
                                                    Submit
                                                </button>
                                                <button class="btn btn-warning mb-2 me-4 btn-sm three" type="button" id="send_back_btn">
                                                    Send Back
                                                </button>
                                                <button class="btn btn-danger mb-2 me-4 btn-sm four" type="button"
                                                    data-bs-toggle="modal" data-bs-target=".bs-example-modal-cente">
                                                    Reject
                                                </button>
                                                <?php 
														
														$view18 = sqlsrv_query($conn, "SELECT * FROM Tb_Recommender INNER JOIN Tb_Recommender_Meterial 
														ON Tb_Recommender.Request_id = Tb_Recommender_Meterial.Request_id  WHERE Tb_Recommender.Request_id = '$request_id' ");
														$view_query18 = sqlsrv_fetch_array($view18);
												          if($view_query18['Finance_Verification'] == 'No' ){
                                                            ?>
                                                <button class="btn btn-danger mb-2 me-4 btn-sm five d-none" type="submit"
                                                    name="Verification">
                                                    Finance Verification
                                                </button <?php }else{ } ?>
                                            </center>
                                            <div class="modal fade bs-example-modal-centered" tabindex="-1"
                                                role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title mt-0">Remarks For SendBack</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close">

                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <textarea class="form-control" name="Approver_back_remark[]"
                                                                aria-label="With textarea"></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                class="btn btn-danger waves-effect waves-light btn-sm"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-success waves-effect waves-light btn-sm"
                                                                name="send" data-bs-dismiss="modal">Submit</button>
                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->
                                            <div class="modal fade bs-example-modal-cente" tabindex="-1" role="dialog"
                                                aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title mt-0">Remarks For Reject</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal" aria-label="Close">

                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <textarea class="form-control" name="Approver_Reject[]"
                                                                aria-label="With textarea"></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button"
                                                                class="btn btn-danger waves-effect waves-light btn-sm"
                                                                data-bs-dismiss="modal"
                                                                aria-label="Close">Close</button>
                                                            <button type="submit"
                                                                class="btn btn-success waves-effect waves-light btn-sm"
                                                                name="reject" data-bs-dismiss="modal">Submit</button>
                                                        </div>
                                                    </div><!-- /.modal-content -->
                                                </div><!-- /.modal-dialog -->
                                            </div><!-- /.modal -->


                                                <div class="row" id="involved_persons_div" style="display:none;">
                                                    <div class="col-md-5">
                                                        <h4 class="text-start">Involved Persons</h4>
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

    <script>
        $(document).ready(function () {
        	var first_pref_quote_value = $('#first_pref_quote_value').val();
					var request_id = '<?php echo $request_id ?>';
					var emp_id = '<?php echo $Employee_Id ?>';

					if(first_pref_quote_value != '') {
						get_involved_persons(first_pref_quote_value);
						get_mapping_details(request_id,first_pref_quote_value,emp_id);
					}

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

        // $(".root").hide();
        // $(".answer").find('.request_selection').attr('disabled',false).attr("required", true);
        // $(document).on("click", "#submit", function () {
        //     if (this.checked) {

        //         $(".answer").hide();
        //         $(".remark").hide();
        //         $(".root").show();
        //         $(".answer").find('.request_selection').attr('disabled',true).attr("required", false);        

        //     } else {
        //         $(".answer").show();
        //         $(".remark").show();
        //         $(".root").hide();
        //         $(".answer").find('.request_selection').attr('disabled',false).attr("required", true);
        //         // $("input:not(:checked)").closest('.target-table').find(".woo").remove();
        //     }
        // }).change();


        $(".Reference").hide();
		$(".one").show();
		$(".two").hide();
		$(".three").show();
		$(".four").show();
        $(document).on("click", "#submit", function () {
            if (this.checked) {

                $(".Reference").show();
                $(".one").hide();
                $(".two").show();
                $(".three").hide();
                $(".four").hide();
                $(".five").hide();
                $(".Reference").find('.kim').attr("required", true);

            } else {
                $(".Reference").hide();
				$(".one").show();
                $(".two").hide();
                $(".three").show();
                $(".four").show();
                $(".Reference").find('.kim').attr("required", false);
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
                `<td><input type='text' class='form-control dis${i}'  name='Last_Purchase[]' 	 placeholder='Enter Last Purchase'></td>`
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
                                                                                    <tr class="material-tr">
                                                                                        <td>
                                                                                            <input type="text" class="form-control  qty3" readonly data-id="${i}" id='qty3${i}' name="Quantity_Details[]-<?php echo $row['ID'] ?>${i}" value="<?php echo $row['Quantity'] ?>" placeholder="Enter Quantity Details" > <input type="hidden" class="form-control" name="Meterial_Name[]" value="<?php echo $row['Item_Code'] ?>" >
                                                                                            <input type="hidden" class="form-control" name="V_id[]" value="Vendor ${i}" >
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" class="form-control  price3" data-id="${i}" id='price3${i}' name="Price[]-<?php echo $row['ID'] ?>${i}" value="" placeholder="Enter Price" >
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" class="form-control  amount3" readonly data-id="${i}" id='amount3${i}' name="Total[]-<?php echo $row['ID'] ?>${i}" value="" >
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
                "<td><input type='date' class='form-control'  name='Delivery_Time[]'  	 placeholder='Enter Quantity Details'></td>"
            );
            $('tbody').find('#nine').append(
                "<td><input type='text' class='form-control'  name='Fright_Charges[]'    placeholder='Enter Value of'></td>"
            );
            $('tbody').find('#ten').append(
                "<td><input type='text' class='form-control'  name='Insurance_Details[]' placeholder='Enter Fright Charges'></td>"
            );
            $('tbody').find('#eleven').append(
                "<td><input type='text' class='form-control'  name='GST_Component[]' 	 placeholder='Enter Insurance Details'></td>"
            );
            $('tbody').find('#twelve').append(
                "<td><input type='text' class='form-control'  name='Warrenty[]'     	 placeholder='Enter GST Component'></td>"
            );
            $('tbody').find('#thirteen').append(
                "<td><input type='text' class='form-control'  name='Payment_Terms[]'  	 placeholder='Enter Warrenty'></td>"
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

        $(document).on('click','.multi_preview',function(){
					var file_type = $(this).data('filetype');
					var src = $(this).attr('src');
					var row_id = $(this).data('id');


				 if(file_type == 'pdf') {
				 	// var src = $('#pdf_input'+row_id).val();
	                var src = $(this).closest('div').find('#pdf_input'+row_id).val();
	            	$('.preview_file_pdf_'+row_id).attr('src', src+'#toolbar=0');
	            	$('.preview_file_img_'+row_id).hide();
	            	$('.preview_file_pdf_'+row_id).show();
			     } else {
	            	$('.preview_file_img_'+row_id).attr('src', src);
	            	$('.preview_file_pdf_'+row_id).hide();
	            	$('.preview_file_img_'+row_id).show();
			     } 
	        	$('#file_preview_modal_'+row_id).modal('show');

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
                    let sendback_from = 'Approver2';
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
                                     window.location.href = 'show_exceed_approver.php';
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
    </script>

    <!-- CUSTOM SCRIPT END -->

</body>

</html>