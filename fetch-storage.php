<?php
require_once '../auto_load.php';
$Plant_Code = $_POST["Plant_Code"];
// print_r($Plant_Code);exit;
// echo "select * from Plant_Master_Storage_Location where PlantCode = '$Plant_Code'";exit;
//$result = sqlsrv_query($conn, "select * from Plant_Master_Storage_Location where PlantCode = '$Plant_Code'");

$result = sqlsrv_query($conn, "SELECT DISTINCT StorageLocation,Plant,Storage_description from MaterialMaster Where Plant = '$Plant_Code'");
// echo $result;exit;
?>



<option value="">Select Storage Location</option>

<?php
while ($row = sqlsrv_fetch_array($result)) {
	?>
	<option value="<?php echo $row["StorageLocation"]; ?>">
		<?php echo $row["Plant"]; ?>-
		<?php echo $row["StorageLocation"] ?>-
		<?php echo $row["Storage_description"]; ?>
	</option>
	<?php
}
?>