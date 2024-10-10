<?php
require_once '../auto_load.php';
$Department = $_POST["Departmented"];
$Request_get = $_GET["id"];

$result = sqlsrv_query($conn, "select * from Tb_Approvel_Master where Department = '$Department'");

$result1 = sqlsrv_query($conn, "SELECT Tb_Approvel_Master.Subject FROM Tb_Approvel_Master INNER JOIN Tb_Payment_Request ON Tb_Approvel_Master.Subject = Tb_Payment_Request.Subject 
WHERE Tb_Approvel_Master.Department ='$Department' AND Tb_Payment_Request.Request_PID = '$Request_get' GROUP BY Tb_Approvel_Master.Subject");
$updated_query = sqlsrv_fetch_array($result1);

$result12 = sqlsrv_query($conn, "SELECT Tb_Approvel_Master.Subject FROM Tb_Approvel_Master INNER JOIN Tb_RateTrems_Request ON Tb_Approvel_Master.Subject = Tb_RateTrems_Request.Subject 
WHERE Tb_Approvel_Master.Department ='$Department' AND Tb_RateTrems_Request.Request_RTID = '$Request_get' GROUP BY Tb_Approvel_Master.Subject");
$updated_query12 = sqlsrv_fetch_array($result12);

$result122 = sqlsrv_query($conn, "SELECT Tb_Approvel_Master.Subject FROM Tb_Approvel_Master INNER JOIN Tb_Oprational_Request ON Tb_Approvel_Master.Subject = Tb_Oprational_Request.Subject 
WHERE Tb_Approvel_Master.Department ='$Department' AND Tb_Oprational_Request.Request_OID = '$Request_get' GROUP BY Tb_Approvel_Master.Subject");
$updated_query122 = sqlsrv_fetch_array($result122);
?>

<option value="">Select Subject</option>
<?php
while ($row = sqlsrv_fetch_array($result)) {
	?>
    <?php
        if($updated_query)
          {
            ?>
            <option <?php if ($updated_query['Subject'] == $row['Subject']) { ?> selected="selected" <?php } ?> value="<?php echo $row["Subject"]; ?>">
                <?php echo $row["Subject"]; ?>
            </option>
        <?php
          }
          else{
            if($updated_query12)
            {
          ?>
            <option <?php if ($updated_query12['Subject'] == $row['Subject']) { ?> selected="selected" <?php } ?> value="<?php echo $row["Subject"]; ?>">
                <?php echo $row["Subject"]; ?>
            </option>
             <?php
          }
          elseif($updated_query122)
          {
              ?>
            <option <?php if ($updated_query122['Subject'] == $row['Subject']) { ?> selected="selected" <?php } ?> value="<?php echo $row["Subject"]; ?>">
                <?php echo $row["Subject"]; ?>
            </option>
            <?php
          }
        }
    ?>
	<?php
}
?>

