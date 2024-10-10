<?php
require_once "../auto_load.php";
$Storage_Location = $_POST["Storage_Location"];


// print_r($Plant_Code);exit;
// echo "select * from MaterialMaster where Plant = '$Plant_Code'";exit;
$result = sqlsrv_query($conn, "select * from MaterialMaster where StorageLocation = '$Storage_Location'");
// echo $result;exit;
?>
<option value="">Select Item_Code</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	// echo $row;
	?>
	<option value="<?php echo $row["ItemCode"]; ?>">
		<?php echo $row["ItemCode"]; ?>
	</option>
	<?php
}
?>