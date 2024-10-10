<?php
require_once "../auto_load.php";
header('Content-Type: application/json');

// Get the reference ID from the request
$reference = $_POST['reference'];

// echo  $_POST['reference'];
// exit;

$referenceArray = explode(',', $reference);
    $ref = implode("','", $referenceArray); 
    // echo $ref;
    // exit;

if(isset($reference))
{

    $query = "SELECT 
        Tb_Request.Request_ID,
        Tb_Request.Department,
        Tb_Request.Request_Type,
        Tb_Request.Plant,
        Tb_Request.Storage_Location,
        Tb_Approver.Request_id,
        Tb_Approver.Vendor_Name,
        Tb_Approver.Vendor_SAP,
        Tb_Approver.vendor_Active_SAP,
        MAX(Tb_Approver.Last_Purchase) AS Last_Purchase,
        MAX(Tb_Approver.Delivery_Time2) AS Delivery_Time2,
        MAX(Tb_Approver.Value_Of) AS Value_Of,
        MAX(Tb_Approver.Fright_Charges) AS Fright_Charges,
        MAX(Tb_Approver.Insurance_Details) AS Insurance_Details,
        MAX(Tb_Approver.GST_Component) AS GST_Component,
        MAX(Tb_Approver.Warrenty) AS Warrenty,
        MAX(Tb_Approver.Payment_Terms) AS Payment_Terms,
        MAX(Tb_Approver.Total_Budget) AS Total_Budget,
        MAX(Tb_Approver.Available_Budget) AS Available_Budget,
        MAX(Tb_Approver.Verification_Type) AS Verification_Type,
        MAX(Tb_Approver.Finance_Remarks) AS Finance_Remarks,
        MAX(Tb_Approver.Requester_Remarks) AS Requester_Remarks,
        MAX(Tb_Approver.Recommender_Remarks) AS Recommender_Remarks,
        MAX(Tb_Approver.Approver_Remarks) AS Approver_Remarks,
        MAX(Tb_Approver_Meterial.Quantity) AS Total_Quantity,
        MAX(Tb_Approver_Meterial.Price) AS Average_Price,
        MAX(Tb_Approver_Meterial.Total) AS Total_Price
    FROM 
        Tb_Request 
    INNER JOIN 
        Tb_Approver ON Tb_Request.Request_ID = Tb_Approver.Request_id 
    INNER JOIN 
        Tb_Approver_Meterial ON Tb_Approver.Request_id = Tb_Approver_Meterial.Request_id 
    WHERE 
        Tb_Request.status = 'Approved' AND TRIM(Approver_Selection) LIKE '%1%' AND Tb_Request.Request_ID in('".$ref."')
        GROUP BY 
        Tb_Request.Request_ID,
        Tb_Request.Department,
        Tb_Request.Request_Type,
        Tb_Request.Plant,
        Tb_Request.Storage_Location,
        Tb_Approver.Request_id,
        Tb_Approver.Vendor_Name,
        Tb_Approver.Vendor_SAP,
        Tb_Approver.vendor_Active_SAP";
        // echo $query;
        // exit;
    $stmt = sqlsrv_query($conn, $query);

    $data = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
    }

    // echo"<pre>";
    // print_r($data);
    // exit;

    // Return data as JSON
    echo json_encode(array('data' => $data));
}
?>
