<?php
require_once "../auto_load.php";
$employeee = $_GET["employe"];

// print_r($employeee);exit;
// echo "select * from Tb_Master_Emp where Matrial_Group = '$MatrialGroup'";exit;

$MatrialGroup = $_REQUEST['ItemCode'];
$request_type_code = $_POST["request_type_code"];

if($request_type_code == 'ZSER') {
	$result = sqlsrv_query($conn, "SELECT DISTINCT ASNUM as ItemCode,ASKTX as ItemDescription from SERVICE_MATERIAL_MASTER"); 
} else {	
	$sql = sqlsrv_query($conn, "SELECT MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group FROM MaterialMaster 
	INNER JOIN Tb_Master_Emp ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group WHERE MaterialMaster.ItemCode = '$MatrialGroup' 
	--- AND Tb_Master_Emp.PO_creator_Release_Codes = '$employeee'
	GROUP BY MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group");
}
$row = sqlsrv_fetch_array($sql);

header('Content-Type: application/json'); //set the content type for browser
echo json_encode($row); //return result to browser
// mysqli_close($con); //close mysql connection

?>