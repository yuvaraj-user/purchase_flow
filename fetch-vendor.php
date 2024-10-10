<?php
require_once "../auto_load.php";
$VendorCode = $_POST["VendorCode"];


// print_r($UOM);
// echo "select * from MaterialMaster where ItemCode = '$ItemCode'";exit;
$result = sqlsrv_query($conn, "SELECT VendorName,City,VendorCode FROM vendor_master WHERE VendorCode = '$VendorCode'");
// echo $result;exit;
?>
<option value="">Select Vendor</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	// echo $row;
	?>
	<option value="<?php echo $row["VendorCode"]; ?>">
		<?php echo $row["VendorCode"]; ?>
	</option>
	<?php
}
?>