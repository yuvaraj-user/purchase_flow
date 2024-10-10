<?php
require_once '../auto_load.php';
$Plant_Code = $_GET["plant"];
// print_r($Plant_Code);exit;
// echo "select * from Plant_Master_Storage_Location where PlantCode = '$Plant_Code'";exit;
$result = sqlsrv_query($conn, "select * from Plant_Master_Storage_Location where PlantCode = '$Plant_Code'");
// echo $result;exit;
?>

<option value="">Select Storage Location</option>

<?php
while ($row = sqlsrv_fetch_array($result)) {
	?>
	<option <?php if ($row['Storage_Location'] == $row['Storage_Location']) { ?> selected="selected" <?php } ?> value="<?php echo $row["Storage_Location"]; ?>">
		<?php echo $row["Storage_Location"]; ?>-
		<?php echo $row["Location_Desc"] ?>
	</option>
	<?php
}
?>