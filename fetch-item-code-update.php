<?php
require_once "../auto_load.php";
$Storage_Location = $_POST["Storage"];
$employeee = $_GET["employe"];

$result = sqlsrv_query($conn, "SELECT MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group,Tb_Master_Emp.Plant  FROM MaterialMaster INNER JOIN Tb_Master_Emp
ON MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group 
WHERE MaterialMaster.StorageLocation = '$Storage_Location' AND Tb_Master_Emp.PO_creator_Release_Codes = '$employeee'
GROUP BY MaterialMaster.Plant,MaterialMaster.ItemCode,MaterialMaster.ItemDescription,MaterialMaster.MaterialGroup,Tb_Master_Emp.Material_Group,Tb_Master_Emp.Plant ");

$result122 = sqlsrv_query($conn, "SELECT Tb_Request_Items.Item_Code FROM Tb_Request_Items INNER JOIN Tb_Master_Emp ON Tb_Request_Items.MaterialGroup =Tb_Master_Emp.Material_Group
INNER JOIN Tb_Request ON Tb_Request_Items.Request_ID = Tb_Request.Request_ID WHERE Tb_Request.Storage_Location = '$Storage_Location'
AND Tb_Master_Emp.PO_creator_Release_Codes = '$employeee'");
$updated_query122 = sqlsrv_fetch_array($result122);
?>
<option value="">Select Item_Code</option>
<option <?php if ($updated_query122['Item_Code'] == 'New_Item') { ?> selected="selected" <?php } ?> value="New_Item">New Item</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {

	?>
	<option <?php if ($updated_query122['Item_Code'] == $row["ItemCode"]) { ?> selected="selected" <?php } ?> value="<?php echo $row["ItemCode"]; ?>"><?php echo $row["ItemDescription"]; ?>&nbsp;-&nbsp;
		<?php echo $row["ItemCode"]; ?>
	</option>
	<?php
}
?>