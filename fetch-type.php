<?php
require_once '../auto_load.php';
$Request_Type = $_POST["Request_Type"];
// print_r($Plant_Code);exit;
// echo "select * from Plant_Master_Storage_Location where PlantCode = '$Plant_Code'";exit;
$result = sqlsrv_query($conn, "select Department from Tb_Approvel_Master where Request_Type = '$Request_Type' group by Department");
// echo $result;exit;
?>

<option value="">Select Department</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	?>
	<option value="<?php echo $row["Department"]; ?>">
		<?php echo $row["Department"]; ?>
	</option>
	<?php
}
?>