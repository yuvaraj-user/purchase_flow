<?php

require_once "../auto_load.php";
// $ItemsCode = $_REQUEST['ItemCode']; 
// print_r($ItemCode);exit; 
// $ItemCode = $_POST["ItemCode"];
// $sql = sqlsrv_query($conn,"select * from MaterialMaster where ItemCode = '$ItemCode'");
// $row = sqlsrv_fetch_array($sql);

// json_encode($row);die;

$user = $_REQUEST['VendorCode'];

$sql = sqlsrv_query($conn, "SELECT FORMAT (BUDAT_MKPF , 'dd/MM/yyyy') as  BUDAT_MKPF FROM MIGO_DET WHERE  LIFNR = '$user' ORDER BY LINE_ID DESC");
$row = sqlsrv_fetch_array($sql);
// echo $row;

header('Content-Type: application/json'); //set the content type for browser
echo json_encode($row); //return result to browser
// mysqli_close($con); //close mysql connection