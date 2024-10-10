<?php
require_once '../auto_load.php';
$Department = $_POST["Department"];
// print_r($Plant_Code);exit;
// echo "select * from Plant_Master_Storage_Location where PlantCode = '$Plant_Code'";exit;
$result = sqlsrv_query($conn, "select * from Tb_Approvel_Master where Department = '$Department'");
// echo $result;exit;
?>

<option value="">Select Subject</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	?>
	<option value="<?php echo $row["Subject"]; ?>">
		<?php echo $row["Subject"]; ?>
	</option>
	<?php
}
?>