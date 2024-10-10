<?php
require_once '../auto_load.php';
$Request_dept = $_POST["Request_dept"];
$Request_get = $_GET["id"];

$result = sqlsrv_query($conn, "select Department from Tb_Approvel_Master where Request_Type = '$Request_dept' group by Department");

$result1 = sqlsrv_query($conn, "SELECT Tb_Approvel_Master.Department FROM Tb_Approvel_Master INNER JOIN Tb_Payment_Request ON Tb_Approvel_Master.Department = Tb_Payment_Request.Depatment 
WHERE Tb_Approvel_Master.Request_Type ='$Request_dept' AND Tb_Payment_Request.Request_PID = '$Request_get' GROUP BY Tb_Approvel_Master.Department");
$updated_query = sqlsrv_fetch_array($result1);

$result12 = sqlsrv_query($conn, "SELECT Tb_Approvel_Master.Department FROM Tb_Approvel_Master INNER JOIN Tb_RateTrems_Request ON Tb_Approvel_Master.Department = Tb_RateTrems_Request.Depatment 
WHERE Tb_Approvel_Master.Request_Type ='$Request_dept' AND Tb_RateTrems_Request.Request_RTID = '$Request_get' GROUP BY Tb_Approvel_Master.Department");
$updated_query12 = sqlsrv_fetch_array($result12);

$result122 = sqlsrv_query($conn, "SELECT Tb_Approvel_Master.Department FROM Tb_Approvel_Master INNER JOIN Tb_Oprational_Request ON Tb_Approvel_Master.Department = Tb_Oprational_Request.Depatment 
WHERE Tb_Approvel_Master.Request_Type ='$Request_dept' AND Tb_Oprational_Request.Request_OID = '$Request_get' GROUP BY Tb_Approvel_Master.Department");
$updated_query122 = sqlsrv_fetch_array($result122);
?>

<option value="">Select Department</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	?>
    <?php
        if($updated_query)
          {
            ?>
            <option <?php if ($updated_query['Department'] == $row['Department']) { ?> selected="selected" <?php } ?> value="<?php echo $row["Department"]; ?>">
                <?php echo $row["Department"]; ?>
            </option>
        <?php
          }
          else{
            if($updated_query12)
            {
          ?>
            <option <?php if ($updated_query12['Department'] == $row['Department']) { ?> selected="selected" <?php } ?> value="<?php echo $row["Department"]; ?>">
                <?php echo $row["Department"]; ?>
            </option>
             <?php
          }
          elseif($updated_query122)
          {
              ?>
            <option <?php if ($updated_query122['Department'] == $row['Department']) { ?> selected="selected" <?php } ?> value="<?php echo $row["Department"]; ?>">
                <?php echo $row["Department"]; ?>
            </option>
            <?php
          }
        }
    ?>
	<?php
}
?>