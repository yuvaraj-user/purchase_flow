<?php
require_once "../auto_load.php";

if(!isset($_POST['Category'])){
    $Cate = "";
} else {
    $Cate = $_POST['Category'];
}
if(!isset($_POST['Plant'])){
    $Plan = "";
} else {
    $Plan = $_POST['Plant'];
}
if(!isset($_POST['Storage'])){
    $Store = "";
} else {
    $Store = $_POST['Storage'];
}
if(!isset($_POST['Item'])){
    $Ite = "";
} else {
    $Ite = $_POST['Item'];
}
if(!isset($_POST['Department'])){
    $Depart = "";
} else {
    $Depart = $_POST['Department'];
}

if($Depart != '' || $Cate != '' || $Plan != '' || $Store != '' || $Ite != '') { 
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
        Tb_Request.status = 'Approved' AND TRIM(Approver_Selection) LIKE '%1%' AND Tb_Approver.V_id = Tb_Approver_Meterial.V_id";

    if($Depart != '') { 
        $query .= " AND Tb_Request.Department = '$Depart' ";
    }
    if($Cate != '') { 
        $query .= " AND Tb_Request.Request_Type = '$Cate' ";
    }
    if($Plan != '') { 
        $query .= " AND Tb_Request.Plant = '$Plan' ";
    }
    if($Store != '') { 
        $query .= " AND Tb_Request.Storage_Location = '$Store' ";
    }
    if($Ite != '') { 
        $query .= " AND Tb_Approver_Meterial.Meterial_Name = '$Ite' ";
    }
    $query .= " GROUP BY 
        Tb_Request.Request_ID,
        Tb_Request.Department,
        Tb_Request.Request_Type,
        Tb_Request.Plant,
        Tb_Request.Storage_Location,
        Tb_Approver.Request_id,
        Tb_Approver.Vendor_Name,
        Tb_Approver.Vendor_SAP,
        Tb_Approver.vendor_Active_SAP";

    // echo $query; // Debugging line to output the query
    $result = sqlsrv_query($conn, $query);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true)); // Output SQL errors
    }

    $content = '';
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $id = $row['Request_ID'];
        $vendor = $row['Vendor_Name'];
        $sap = $row['Vendor_SAP'];
        $vendor_Active_SAP = $row['vendor_Active_SAP'];
        $Last_Purchase = $row['Last_Purchase'];
        $Delivery_Time2 = $row['Delivery_Time2'];
        $Quantity = $row['Total_Quantity'];
        $Price = $row['Average_Price'];
        $Total = $row['Total_Price'];
        $Value_Of = $row['Value_Of'];
        $Fright_Charges = $row['Fright_Charges'];
        $Insurance_Details = $row['Insurance_Details'];
        $GST_Component = $row['GST_Component'];
        $Warrenty = $row['Warrenty'];
        $Payment_Terms = $row['Payment_Terms'];
        $Total_Budget = $row['Total_Budget'];
        $Available_Budget = $row['Available_Budget'];
        $Verification_Type = $row['Verification_Type'];
        $Finance_Remarks = $row['Finance_Remarks'];
        $Requester_Remarks = $row['Requester_Remarks'];
        $Recommender_Remarks = $row['Recommender_Remarks'];
        $Approver_Remarks = $row['Approver_Remarks'];

        $content .= '<tr class="item-row"><td><input type="checkbox" class="checkboxstyle" value='.$id.' ></td>';
        $content .= '<td>'.$id.'</td>';
        $content .= '<td>'.$vendor.'</td>';
        $content .= '<td>'.$sap.'</td>';
        $content .= '<td>'.$vendor_Active_SAP.'</td>';
        $content .= '<td>'.$Last_Purchase.'</td>';
        $content .= '<td>'.$Delivery_Time2.'</td>';
        $content .= '<td>'.$Quantity.'</td>';
        $content .= '<td>'.$Price.'</td>';
        $content .= '<td>'.$Total.'</td>';
        $content .= '<td>'.$Value_Of.'</td>';
        $content .= '<td>'.$Fright_Charges.'</td>';
        $content .= '<td>'.$Insurance_Details.'</td>';
        $content .= '<td>'.$GST_Component.'</td>';
        $content .= '<td>'.$Warrenty.'</td>';
        $content .= '<td>'.$Payment_Terms.'</td>';
        $content .= '<td>'.$Total_Budget.'</td>';
        $content .= '<td>'.$Available_Budget.'</td>';
        $content .= '<td>'.$Verification_Type.'</td>';
        $content .= '<td>'.$Finance_Remarks.'</td>';
        $content .= '<td>'.$Requester_Remarks.'</td>';
        $content .= '<td>'.$Recommender_Remarks.'</td>';
        $content .= '<td>'.$Approver_Remarks.'</td></tr>';
    }

    $storeData = array(
        "content" => $content
    );
    echo json_encode($storeData);
} else {
    echo json_encode(array("content" => ""));
}
?>
