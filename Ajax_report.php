<?php 
include '../auto_load.php';
include 'Common_Filter.php';
error_reporting(-1);
$Common_Filter=new Common_Filter($conn);
$Action=@$_POST['Action'];

if($Action=="Purchase_report")
{
	$MustardData=$Common_Filter->Purchase_report($_POST);
	echo json_encode($MustardData);exit;
} else if($Action=="Purchase_report_dev")
{
	$MustardData=$Common_Filter->Purchase_report_dev($_POST);
	echo json_encode($MustardData);exit;
}






?>