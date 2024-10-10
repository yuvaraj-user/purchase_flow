<?php
require_once "../auto_load.php";

$result = sqlsrv_query($conn, "select * from SERVICE_MATERIAL_MASTER ");
// echo $result;exit;
?>
<option value="">Select Service Material</option>
<option value="New_Item">New Item</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	// echo $row;
	?>
	<option value="<?php echo $row["ASNUM"]; ?>"><?php echo $row["ASKTX"]; ?>&nbsp;-&nbsp;
		<?php echo $row["ASNUM"]; ?>
	</option>
	<?php
}
?>