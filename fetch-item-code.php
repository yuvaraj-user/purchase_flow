<?php
require_once "../auto_load.php";
$Storage_Location = $_POST["Storage_Location"];
$Plant = $_POST["Plant"];
$employeee = $_GET["employe"];
$MaterialGroup = $_POST["MaterialGroup"];
$request_type_code = $_POST["request_type_code"];

// print_r($_POST);exit;
// echo "select * from MaterialMaster where Plant = '$Plant_Code'";exit;

// $result = sqlsrv_query($conn, "SELECT TOP 1 MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group,Tb_Master_Emp.Plant  FROM MaterialMaster INNER JOIN Tb_Master_Emp
// ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group 
// WHERE MaterialMaster.StorageLocation = '$Storage_Location' AND Tb_Master_Emp.PO_creator_Release_Codes = '$employeee'
// GROUP BY MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group,Tb_Master_Emp.Plant ");

if($request_type_code == 'ZSER') {
	$result = sqlsrv_query($conn, "SELECT DISTINCT ASNUM as ItemCode,ASKTX as ItemDescription from SERVICE_MATERIAL_MASTER"); 
} else {	
	$result = sqlsrv_query($conn, "SELECT MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,
	MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group FROM MaterialMaster 
	INNER JOIN (SELECT DISTINCT Material_Group,PO_creator_Release_Codes FROM Tb_Master_Emp) as Tb_Master_Emp ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group 
	WHERE  MaterialMaster.Plant = '$Plant' AND MaterialMaster.MaterialGroup = '$MaterialGroup' 
	-- AND Tb_Master_Emp.PO_creator_Release_Codes = '$employeee' 
	GROUP BY MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,MaterialMaster.MaterialGroup,
	Tb_Master_Emp.Material_Group");
}

/*SELECT MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,
	MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group FROM MaterialMaster 
	INNER JOIN (SELECT DISTINCT Material_Group,PO_creator_Release_Codes FROM Tb_Master_Emp) as Tb_Master_Emp ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group 
	WHERE MaterialMaster.StorageLocation = '$Storage_Location' AND MaterialMaster.Plant = '$Plant' AND MaterialMaster.MaterialGroup = '$MaterialGroup' 
	-- AND Tb_Master_Emp.PO_creator_Release_Codes = '$employeee' 
	GROUP BY MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,MaterialMaster.MaterialGroup,
	Tb_Master_Emp.Material_Group*/

?>
<option value="">Select Item_Code</option>
<option value="New_Item">New Item</option>

<?php
while ($row = sqlsrv_fetch_array($result)) {
	// echo $row;
	?>
	<option value="<?php echo $row["ItemCode"]; ?>"><?php echo $row["ItemDescription"]; ?>&nbsp;-&nbsp;
		<?php echo $row["ItemCode"]; ?>
	</option>
	<?php
}

?>
