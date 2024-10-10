<?php
require_once "../auto_load.php";
$UOM = $_POST["UOM"];


// print_r($UOM);
// echo "select * from MaterialMaster where ItemCode = '$ItemCode'";exit;
$result = sqlsrv_query($conn, "select * from MaterialMaster where UOM = '$UOM'");
// echo $result;exit;
?>
<option value="">Select Description</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	// echo $row;
	?>
	<option value="<?php echo $row["ItemDescription"]; ?>">
		<?php echo $row["ItemDescription"]; ?>
	</option>
	<?php
}
?>