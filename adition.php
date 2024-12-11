<?php
 $Title = "Rasi Seeds (P) Ltd";


//  Prifix

$_prefix_payment = 'FP00';
$_prefix_rate = 'FR00';
$_prefix_opration = 'OP00';
$_prefix_purchase = 'PV00';

$qry = sqlsrv_query($conn, "select * from Tb_Payment_Request ORDER BY Id desc") or die('error');
$nextID = sqlsrv_fetch_array($qry)['Id'] + 1;
$_paymentID = $_prefix_payment . $nextID;

$qry = sqlsrv_query($conn, "select * from Tb_RateTrems_Request ORDER BY Id desc") or die('error');
$nextID = sqlsrv_fetch_array($qry)['Id'] + 1;
$_ratetermsID = $_prefix_rate . $nextID;

$qry = sqlsrv_query($conn, "select * from Tb_Oprational_Request ORDER BY Id desc") or die('error');
$nextID = sqlsrv_fetch_array($qry)['Id'] + 1;
$_oprationID = $_prefix_opration . $nextID;

$qry = sqlsrv_query($conn, "select * from Tb_Request ORDER BY Id desc") or die('error');
$nextID = sqlsrv_fetch_array($qry)['Id'] + 1;
$_purchaseID = $_prefix_purchase . $nextID;
//end


$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$uri = dirname($_SERVER['REQUEST_URI']);

$currentUrl = $protocol . '://' . $host . $uri.'/';

?>