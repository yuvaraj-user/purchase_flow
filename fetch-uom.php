<?php
require_once "../auto_load.php";
$ItemCode = $_POST["ItemCode"];


// print_r($ItemCode);
// echo "select * from MaterialMaster where ItemCode = '$ItemCode'";exit;
$result = sqlsrv_query($conn, "select * from MaterialMaster where ItemCode = '$ItemCode'");
// echo $result;exit;
?>
<option value="">Select UOM</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	// echo $row;
	?>
	<option value="<?php echo $row["UOM"]; ?>">
		<?php echo $row["UOM"]; ?>
	</option>
	<?php
}
?>