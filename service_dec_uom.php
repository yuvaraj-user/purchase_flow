<?php

require_once "../auto_load.php";
// $ItemsCode = $_REQUEST['ItemCode']; 
// print_r($ItemCode);exit; 
// $ItemCode = $_POST["ItemCode"];
// $sql = sqlsrv_query($conn,"select * from MaterialMaster where ItemCode = '$ItemCode'");
// $row = sqlsrv_fetch_array($sql);

// json_encode($row);die;

$user = $_REQUEST['ItemCode'];
$sql = sqlsrv_query($conn, "SELECT ASKTX,MEINS FROM SERVICE_MATERIAL_MASTER WHERE ASNUM = 
'" . $user . "' ");
$row = sqlsrv_fetch_array($sql);

header('Content-Type: application/json'); //set the content type for browser
echo json_encode($row); //return result to browser
// mysqli_close($con); //close mysql connection