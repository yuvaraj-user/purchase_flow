<?php
require_once "../auto_load.php";
// $Storage_Location = $_POST["Storage_Location"];
$employeee = $_GET["employe"];

// print_r($Plant_Code);exit;
// echo "select * from MaterialMaster where Plant = '$Plant_Code'";exit;

$result = sqlsrv_query($conn, "SELECT Material_Group FROM Tb_Master_Emp  WHERE  PO_creator_Release_Codes = '$employeee'
GROUP BY Material_Group");
// echo $result;exit;
?>
<option value="">Select Item_Code</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	// echo $row;
	?>
	<option value="<?php echo $row["Material_Group"]; ?>"><?php echo $row["Material_Group"]; ?>
	</option>
	<?php
}
?>
