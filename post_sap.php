<?php 
include('../auto_load.php');

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

	// $request_id = 'PV000349';

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


	// $Postvalue_qry = sqlsrv_fetch_array($Post_qry);

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
		$array['VENDOR_PRICE'] = @$Postvalue_qry['price'];
		$array['FRIGHT_CHARGES'] = @$Postvalue_qry['Fright_Charges'];
		$array['INSURANCE_DETAILS'] = @$Postvalue_qry['Insurance_Details'];
		$array['GST_AMT'] = @$Postvalue_qry['GST_Component'];
		$array['TOTAL_AMOUNT'] = @$Postvalue_qry['Total'];
		$array['WARRENTY'] = @$Postvalue_qry['Warrenty'];
		$array['PAYMENT_TERMS'] = @$Postvalue_qry['Payment_Terms'];
		$array['TOTAL_BUDGET_AMT'] = @$Totalbudget;
		$array['AVAILABLE_BUDGET_AMT'] = @$Availableabudget;
		$array['APPROVERREMARKS'] = @$Postvalue_qry['Approver_Remarks'];
		$array['PURCHASER_ID'] = @$Postvalue_qry['Requested_to'];
		$Sap_Data_Array[] = $array;


		$url = "http://192.168.162.213:8081/PR_PO_CREATE/PRD/ZIN_RFC_PR_PO_CREATION_UPDATE.php";
		$SAP_Json_Data = json_encode($Sap_Data_Array);

		// echo $SAP_Json_Data;exit; 


		$Post_To_SAP_Dets = Post_SAP_Data($SAP_Json_Data, $url);
	}

?>