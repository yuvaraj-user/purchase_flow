<?php
include('../auto_load.php');
include('adition.php');
if(!isset($_SESSION['EmpID']))
{
    ?>
    <script type="text/javascript">
       window.location = "../pages/indexAdmin.php";
    </script>
    <?php
}
?>
<!doctype html>
<html lang="en">

<head>

    
    <meta charset="utf-8" />
    <title><?php echo $Title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="../global/photos/favicon.ico">

    <!-- plugin css -->
    <link href="assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        .ag-format-container {
  /* width: 1295px; */
  margin: 0 auto;
}

.ag-courses_box {
  /* display: -webkit-box; */
  display: -ms-flexbox;
  /* display: flex; */
  -webkit-box-align: start;
  -ms-flex-align: start;
  align-items: flex-start;
  -ms-flex-wrap: wrap;
  flex-wrap: wrap;

  /* padding: 50px 0; */
}
.ag-courses_item {
  -ms-flex-preferred-size: calc(33.33333% - 30px);
  flex-basis: calc(33.33333% - 30px);

  margin: 0 15px 30px;

  overflow: hidden;

  border-radius: 28px;
}
.ag-courses-item_link {
  display: block;
  padding: 30px 20px;
  background-color: #ffffff;

  overflow: hidden;

  position: relative;
}
.ag-courses-item_link:hover,
.ag-courses-item_link:hover .ag-courses-item_date {
  text-decoration: none;
  color: #FFF;
}
.ag-courses-item_link:hover .ag-courses-item_bg {
  -webkit-transform: scale(10);
  -ms-transform: scale(10);
  transform: scale(10);
}
.ag-courses-item_title {
  min-height: 45px;
  margin: 0 0 0px;

  overflow: hidden;

  font-weight: bold;
  font-size: 17px;
  color: #040404;

  z-index: 2;
  position: relative;
}
.ag-courses-item_date-box {
  font-size: 18px;
  color: #0e0e0d;

  z-index: 2;
  position: relative;
}
.ag-courses-item_date {
  font-weight: bold;
  color: #f9b234;

  -webkit-transition: color .5s ease;
  -o-transition: color .5s ease;
  transition: color .5s ease
}
.ag-courses-item_bg {
  height: 128px;
  width: 128px;
  background-color: #f9b234;

  z-index: 1;
  position: absolute;
  top: -75px;
  right: -75px;

  border-radius: 50%;

  -webkit-transition: all .5s ease;
  -o-transition: all .5s ease;
  transition: all .5s ease;
}
.ag-courses_item:nth-child(2n) .ag-courses-item_bg {
  background-color: #3ecd5e;
}
.ag-courses_item:nth-child(3n) .ag-courses-item_bg {
  background-color: #e44002;
}
.ag-courses_item:nth-child(4n) .ag-courses-item_bg {
  background-color: #952aff;
}
.ag-courses_item:nth-child(5n) .ag-courses-item_bg {
  background-color: #cd3e94;
}
.ag-courses_item:nth-child(6n) .ag-courses-item_bg {
  background-color: #4c49ea;
}



@media only screen and (max-width: 979px) {
  .ag-courses_item {
    -ms-flex-preferred-size: calc(50% - 30px);
    flex-basis: calc(50% - 30px);
  }
  .ag-courses-item_title {
    font-size: 24px;
  }
}

@media only screen and (max-width: 767px) {
  .ag-format-container {
    width: 96%;
  }

}
@media only screen and (max-width: 639px) {
  .ag-courses_item {
    -ms-flex-preferred-size: 100%;
    flex-basis: 100%;
  }
  .ag-courses-item_title {
    min-height: 72px;
    line-height: 1;

    font-size: 17px;
  }
  .ag-courses-item_link {
    padding: 22px 40px;
  }
  .ag-courses-item_date-box {
    font-size: 14px;
  }

  .footer {
    left: 0 !important;
    text-align: center;
  }
}
    </style>

</head>


<body data-keep-enlarged="true" class="vertical-collpsed">

       <!-- Loader -->
       <div id="preloader">
        <div id="status">
            <div class="spinner-chase">
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
                <div class="chase-dot"></div>
            </div>
        </div>
    </div>
    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include('navbar.php') ?>

        <!-- ========== Left Sidebar Start ========== -->
        <?php include('sidebar.php') ?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">

                <!-- start page title -->
                <div class="page-title-box">
                    <div class="container-fluid">
                     <div class="row align-items-center">
                         <div class="col-sm-6">
                             <div class="page-title">
                                 <h4>Dashboard</h4>
                                     <ol class="breadcrumb m-0">
                                         <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                         <li class="breadcrumb-item active">Dashboard</li>
                                     </ol>
                             </div>
                         </div>
                     </div>
                    </div>
                 </div>
                 <!-- end page title -->    


                <div class="container-fluid">

                    <div class="page-content-wrapper">

                        <div class="row">
                        <?php  //if($_SESSION['Dcode']=='ADMIN' || $_SESSION['Dcode']=='SUPERADMIN') {
                                if($_SESSION['EmpID']=='RS3015' || $_SESSION['EmpID']=='RS4310' || $_SESSION['EmpID']=='RS7361') {

                                ?>
                                <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_master.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #952aff;"></div>
                                            <div class="ag-courses-item_title">
                                            Master Role Mapping
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>


                            <?php 
                            $sql4 = "SELECT * FROM Tb_Master_Emp WHERE Recommender = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Purchase & Vendor selection (R&VS)' AND Status = 'Active' ";
                            $params4 = array();
                            $options4 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt4 = sqlsrv_query( $conn, $sql4 , $params4, $options4 );
                            $row_count4 = sqlsrv_num_rows( $stmt4 );
                            $recommender = false;
                            if ($row_count4 > 0) {
                                $recommender = true;
                            }
                            if($recommender) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_recommender.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #952aff;"></div>
                                            <div class="ag-courses-item_title">
                                            Purchase Recommendation (R&VS)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                            
                                                // $sql12 = "SELECT * FROM Tb_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_Request.EMP_ID 
                                                // WHERE Tb_Master_Emp.Recommender='".$_SESSION['EmpID']."' and Tb_Request.status='Added'";

                                                $sql12 = "SELECT * FROM Tb_Request
                                                INNER JOIN(select DISTINCT id,Purchaser,Purchase_Type,Recommender from Tb_Master_Emp WHERE Tb_Master_Emp.Recommender = '".$_SESSION['EmpID']."') as Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category and Tb_Master_Emp.id = Tb_Request.approval_mapping_id
                                                WHERE Tb_Master_Emp.Recommender='".$_SESSION['EmpID']."' AND Tb_Request.Recommender = '".$_SESSION['EmpID']."' and Tb_Request.status='Added' and (Tb_Request.is_sendbacked IS NULL OR Tb_Request.is_sendbacked = 0)";

                                                $params12 = array();
                                                $options12 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt12 = sqlsrv_query( $conn, $sql12 , $params12, $options12 );
                                                $row_count12= sqlsrv_num_rows( $stmt12 );
                                                ?>
                                                <?php echo $row_count12 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php 
                            $sql5 = "SELECT * FROM Tb_Master_Emp WHERE Approver = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Purchase & Vendor selection (R&VS)' AND Status = 'Active' ";
                            $params5 = array();
                            $options5 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt5 = sqlsrv_query( $conn, $sql5 , $params5, $options5 );
                            $row_count5 = sqlsrv_num_rows( $stmt5 );
                            $approver = false;
                            if ($row_count5 > 0) {
                                $approver = true;
                            }
                            if($approver) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_approver.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #3ecd5e;"></div>
                                            <div class="ag-courses-item_title">
                                            Purchase Approval (R&VS)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                // $sql12 = "SELECT * FROM Tb_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_Request.EMP_ID  
                                                // WHERE Tb_Master_Emp.Approver='".$_SESSION['EmpID']."' and Tb_Request.status='Recommended'";

                                                $sql12 = "SELECT DISTINCT(Tb_Request.Request_id) FROM Tb_Request
                                                Inner Join (select DISTINCT Purchaser,Purchase_Type,Approver,Document_type,Material_Group from Tb_Master_Emp) as Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                --INNER JOIN MaterialMaster ON MaterialMaster.Plant = Tb_Request.Plant AND MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group AND
                                                -- MaterialMaster.StorageLocation = Tb_Request.Storage_Location
                                                INNER JOIN Tb_Recommender ON Tb_Recommender.Requested_to = Tb_Master_Emp.Approver
                                                WHERE Tb_Master_Emp.Approver='".$_SESSION['EmpID']."' AND Tb_Request.Approver = '".$_SESSION['EmpID']."' and Tb_Request.status='Recommended' and (Tb_Request.is_sendbacked IS NULL OR Tb_Request.is_sendbacked = 0)";

                                                $params12 = array();
                                                $options12 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt12 = sqlsrv_query( $conn, $sql12 , $params12, $options12 );
                                                $row_count12= sqlsrv_num_rows( $stmt12 );
                                                ?>
                                               <?php echo $row_count12 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>



                            <?php 
                            $sql5 = "SELECT * FROM Tb_Master_Emp WHERE Approver_2 = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Purchase & Vendor selection (R&VS)' AND Status = 'Active' ";
                            $params5 = array();
                            $options5 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt5 = sqlsrv_query( $conn, $sql5 , $params5, $options5 );
                            $row_count5 = sqlsrv_num_rows( $stmt5 );
                            $approver_2 = false;
                            if ($row_count5 > 0) {
                                $approver_2 = true;
                            }
                            if($approver_2) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_exceed_approver.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #3ecd5e;"></div>
                                            <div class="ag-courses-item_title">
                                            Purchase Limit Exceed Approval (R&VS)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                // $sql12 = "SELECT * FROM Tb_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_Request.EMP_ID  
                                                // WHERE Tb_Master_Emp.Approver='".$_SESSION['EmpID']."' and Tb_Request.status='Recommended'";

                                                $sql12 = "SELECT DISTINCT(Tb_Request.Request_id) FROM Tb_Request
                                                Inner Join (select DISTINCT Purchaser,Purchase_Type,Approver_2,Document_type,Material_Group from Tb_Master_Emp) as Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                INNER JOIN MaterialMaster ON MaterialMaster.Plant = Tb_Request.Plant AND MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group AND
                                                MaterialMaster.StorageLocation = Tb_Request.Storage_Location
                                                INNER JOIN Tb_Approver ON Tb_Approver.Requested_to = Tb_Master_Emp.Approver_2
                                                WHERE Tb_Master_Emp.Approver_2='".$Employee_Id."' and Tb_Request.status='Waiting_for_approval2' and (Tb_Request.is_sendbacked IS NULL OR Tb_Request.is_sendbacked = 0) AND Tb_Master_Emp.Document_type IS NOT NULL AND Tb_Master_Emp.Document_type != ''";

                                                $params12 = array();
                                                $options12 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt12 = sqlsrv_query( $conn, $sql12 , $params12, $options12 );
                                                $row_count12= sqlsrv_num_rows( $stmt12 );
                                                ?>
                                               <?php echo $row_count12 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php 
                            $sql1 = "SELECT * FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Financial - Payment requests (FP)' AND Status = 'Active' ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );
                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_payment_request.php" class="ag-courses-item_link" >
                                            <div class="ag-courses-item_bg" ></div>
                                            <div class="ag-courses-item_title">
                                            Payment requests (FP)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php 
                            $sql1 = "SELECT * FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Financial - Rate and terms request (FR)' AND Status = 'Active' ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );
                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_rateterms_request.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" ></div>
                                            <div class="ag-courses-item_title">
                                            Rate and terms request (FR)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php 
                            $sql1 = "SELECT * FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Operational requests (OP)' AND Status = 'Active'  ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );
                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_oprational_request.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" ></div>
                                            <div class="ag-courses-item_title">
                                            Operational requests (OP)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php 
                            $sql1 = "SELECT * FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Purchase & Vendor selection (R&VS)' AND Status = 'Active'  ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );
                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_purchacevendor_request.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" ></div>
                                            <div class="ag-courses-item_title">
                                            Purchase & Vendor selection (R&VS)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php 
                            $sql2 = "SELECT * FROM Tb_Master_Emp WHERE Purchaser = '".$_SESSION['EmpID']."' AND Status = 'Active' ";
                            $params2 = array();
                            $options2 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt2 = sqlsrv_query( $conn, $sql2 , $params2, $options2 );
                            $row_count2 = sqlsrv_num_rows( $stmt2 );
                            $vendor = false;
                            if ($row_count2 > 0) {
                                $vendor = true;
                            }
                            if($vendor) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_vendor_request.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #cd3e94;"></div>
                                            <div class="ag-courses-item_title">
                                            Vendor Quotation
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                // $sql11 = "SELECT * FROM Tb_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_Request.EMP_ID 
                                                // WHERE Tb_Master_Emp.Purchaser='".$_SESSION['EmpID']."' and Tb_Request.status='Requested'";

                                                $sql11 = "SELECT * FROM Tb_Request Inner Join (select DISTINCT Purchaser,Purchase_Type from Tb_Master_Emp) as Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                WHERE Tb_Master_Emp.Purchaser='".$_SESSION['EmpID']."' and Tb_Request.Requested_to ='".$_SESSION['EmpID']."' and Tb_Request.status='Requested' and (Tb_Request.is_sendbacked IS NULL OR Tb_Request.is_sendbacked = 0) ORDER BY Tb_Request.Request_ID DESC";

                                                $params11 = array();
                                                $options11 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt11 = sqlsrv_query( $conn, $sql11 , $params11, $options11 );
                                                $row_count11 = sqlsrv_num_rows( $stmt11 );
                                                ?>
                                                <?php echo $row_count11 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php 
                            $sql3 = "SELECT * FROM Tb_Master_Emp WHERE Finance_Verfier = '".$_SESSION['EmpID']."' AND Status = 'Active' ";
                            $params3 = array();
                            $options3 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt3 = sqlsrv_query( $conn, $sql3 , $params3, $options3 );
                            $row_count3 = sqlsrv_num_rows( $stmt3 );
                            $verifier = false;
                            if ($row_count3 > 0) {
                                $verifier = true;
                            }
                            if($verifier) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_verifier.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #e44002;"></div>
                                            <div class="ag-courses-item_title">
                                            Finance Verification
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                $sql11 = "SELECT * FROM Tb_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_Request.EMP_ID 
                                                WHERE Tb_Master_Emp.Finance_Verfier='".$_SESSION['EmpID']."' and Tb_Request.status='Review'";
                                                $params11 = array();
                                                $options11 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt11 = sqlsrv_query( $conn, $sql11 , $params11, $options11 );
                                                $row_count11 = sqlsrv_num_rows( $stmt11 );
                                                ?>
                                                <?php echo $row_count11 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php 
                            $sql4 = "SELECT * FROM Tb_Master_Emp WHERE Recommender = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Financial - Payment requests (FP)' AND Status = 'Active' ";
                            $params4 = array();
                            $options4 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt4 = sqlsrv_query( $conn, $sql4 , $params4, $options4 );
                            $row_count4 = sqlsrv_num_rows( $stmt4 );
                            $recommender = false;
                            if ($row_count4 > 0) {
                                $recommender = true;
                            }
                            if($recommender) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_payment_recommender.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #952aff;"></div>
                                            <div class="ag-courses-item_title">
                                            Payment Recommendation (FP)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                $sql11 = "SELECT * FROM Tb_Payment_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_Payment_Request.EMP_ID 
                                                WHERE Tb_Master_Emp.Recommender='".$_SESSION['EmpID']."' and Tb_Payment_Request.Status='Requested' ";
                                                $params11 = array();
                                                $options11 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt11 = sqlsrv_query( $conn, $sql11 , $params11, $options11 );
                                                $row_count11 = sqlsrv_num_rows( $stmt11 );
                                                ?>
                                                <?php echo $row_count11 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php 
                            $sql4 = "SELECT * FROM Tb_Master_Emp WHERE Recommender = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Financial - Rate and terms request (FR)' AND Status = 'Active' ";
                            $params4 = array();
                            $options4 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt4 = sqlsrv_query( $conn, $sql4 , $params4, $options4 );
                            $row_count4 = sqlsrv_num_rows( $stmt4 );
                            $recommender = false;
                            if ($row_count4 > 0) {
                                $recommender = true;
                            }
                            if($recommender) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_rateterms_recommender.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #952aff;"></div>
                                            <div class="ag-courses-item_title">
                                            Rate Recommendation (FR)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                $sql11 = "SELECT * FROM Tb_RateTrems_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_RateTrems_Request.EMP_ID 
                                                WHERE Tb_Master_Emp.Recommender='".$_SESSION['EmpID']."' and Tb_RateTrems_Request.Status='Requested' ";
                                                $params11 = array();
                                                $options11 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt11 = sqlsrv_query( $conn, $sql11 , $params11, $options11 );
                                                $row_count11 = sqlsrv_num_rows( $stmt11 );
                                                ?>
                                                <?php echo $row_count11 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php 
                            $sql4 = "SELECT * FROM Tb_Master_Emp WHERE Recommender = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Operational requests (OP)' AND Status = 'Active' ";
                            $params4 = array();
                            $options4 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt4 = sqlsrv_query( $conn, $sql4 , $params4, $options4 );
                            $row_count4 = sqlsrv_num_rows( $stmt4 );
                            $recommender = false;
                            if ($row_count4 > 0) {
                                $recommender = true;
                            }
                            if($recommender) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_operational_recommender.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #952aff;"></div>
                                            <div class="ag-courses-item_title">
                                            Operational Recommendation (OP)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                $sql11 = "SELECT * FROM Tb_Oprational_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_Oprational_Request.EMP_ID 
                                                WHERE Tb_Master_Emp.Recommender='".$_SESSION['EmpID']."' and Tb_Oprational_Request.Status='Requested'";
                                                $params11 = array();
                                                $options11 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt11 = sqlsrv_query( $conn, $sql11 , $params11, $options11 );
                                                $row_count11 = sqlsrv_num_rows( $stmt11 );
                                                ?>
                                                <?php echo $row_count11 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>


                            <?php 
                            $sql5 = "SELECT * FROM Tb_Master_Emp WHERE Approver = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Financial - Payment requests (FP)' AND Status = 'Active' ";
                            $params5 = array();
                            $options5 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt5 = sqlsrv_query( $conn, $sql5 , $params5, $options5 );
                            $row_count5 = sqlsrv_num_rows( $stmt5 );
                            $approver = false;
                            if ($row_count5 > 0) {
                                $approver = true;
                            }
                            if($approver) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_payment_approver.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #3ecd5e;"></div>
                                            <div class="ag-courses-item_title">
                                            Payment Approval (FP)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                $sql12 = "SELECT * FROM Tb_Payment_Request Inner Join Tb_Master_Emp 
                                                On Tb_Master_Emp.PO_creator_Release_Codes=Tb_Payment_Request.EMP_ID  
                                                WHERE Tb_Master_Emp.Approver='".$_SESSION['EmpID']."' and Tb_Payment_Request.Status='Recommended'";
                                                $params12 = array();
                                                $options12 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt12 = sqlsrv_query( $conn, $sql12 , $params12, $options12 );
                                                $row_count12= sqlsrv_num_rows( $stmt12 );
                                                ?>
                                               <?php echo $row_count12 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php 
                            $sql5 = "SELECT * FROM Tb_Master_Emp WHERE Approver = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Financial - Rate and terms request (FR)' AND Status = 'Active' ";
                            $params5 = array();
                            $options5 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt5 = sqlsrv_query( $conn, $sql5 , $params5, $options5 );
                            $row_count5 = sqlsrv_num_rows( $stmt5 );
                            $approver = false;
                            if ($row_count5 > 0) {
                                $approver = true;
                            }
                            if($approver) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_rateterms_approver.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #3ecd5e;"></div>
                                            <div class="ag-courses-item_title">
                                            Rate Approval (FR)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                $sql12 = "SELECT * FROM Tb_RateTrems_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_RateTrems_Request.EMP_ID  
                                                WHERE Tb_Master_Emp.Approver='".$_SESSION['EmpID']."' and Tb_RateTrems_Request.Status='Recommended'";
                                                $params12 = array();
                                                $options12 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt12 = sqlsrv_query( $conn, $sql12 , $params12, $options12 );
                                                $row_count12= sqlsrv_num_rows( $stmt12 );
                                                ?>
                                                <?php echo $row_count12 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>

                            <?php 
                            $sql5 = "SELECT * FROM Tb_Master_Emp WHERE Approver = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Operational requests (OP)' AND Status = 'Active' ";
                            $params5 = array();
                            $options5 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt5 = sqlsrv_query( $conn, $sql5 , $params5, $options5 );
                            $row_count5 = sqlsrv_num_rows( $stmt5 );
                            $approver = false;
                            if ($row_count5 > 0) {
                                $approver = true;
                            }
                            if($approver) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_operational_approver.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #3ecd5e;"></div>
                                            <div class="ag-courses-item_title">
                                            Operational Approval (OP)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            Pending:
                                            <span class="ag-courses-item_date">
                                            <?php 
                                                $sql12 = "SELECT * FROM Tb_Oprational_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.PO_creator_Release_Codes=Tb_Oprational_Request.EMP_ID  
                                                WHERE Tb_Master_Emp.Approver='".$_SESSION['EmpID']."' and Tb_Oprational_Request.Status='Recommended'";
                                                $params12 = array();
                                                $options12 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                                                $stmt12 = sqlsrv_query( $conn, $sql12 , $params12, $options12 );
                                                $row_count12= sqlsrv_num_rows( $stmt12 );
                                                ?>
                                                <?php echo $row_count12 ?>
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>



                        <!-- INFORMER SCREEN -->

                            <?php 
                            $sql1 = "SELECT * FROM Tb_Payment_Request WHERE Persion_In_Workflow like '%".$_SESSION['EmpID']."%'  ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );
                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_informer_payment_request.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg"></div>
                                            <div class="ag-courses-item_title">
                                            Payment Details (FP)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php 
                            $sql1 = "SELECT * FROM Tb_RateTrems_Request WHERE Persion_In_Workflow like '%".$_SESSION['EmpID']."%' ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );
                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_informer_rate_request.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #952aff;"></div>
                                            <div class="ag-courses-item_title">
                                            Rate Details (FR)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php 
                            $sql1 = "SELECT * FROM Tb_Oprational_Request WHERE Persion_In_Workflow like '%".$_SESSION['EmpID']."%' ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );
                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_informer_operational_request.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #3ecd5e;"></div>
                                            <div class="ag-courses-item_title">
                                            Operational Details (OP)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php 
                            $sql1 = "SELECT * FROM Tb_Request WHERE Persion_In_Workflow like '%".$_SESSION['EmpID']."%' ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );
                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_informer_purchase_request.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #952aff;"></div>
                                            <div class="ag-courses-item_title">
                                            Purchase Details (R&VS)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <?php 
                            $sql1 = "SELECT * FROM Tb_Approver WHERE Reference_Empid like '%".$_SESSION['EmpID']."%' ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );
                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                            <div class="col-xl-3 col-md-3">
                                <div class="ag-format-container">
                                    <div class="ag-courses_box">
                                        <div class="ag-courses_item">
                                        <a href="show_reference_purchase_request.php" class="ag-courses-item_link">
                                            <div class="ag-courses-item_bg" style="background-color: #952aff;"></div>
                                            <div class="ag-courses-item_title">
                                            Additional Reference (R&VS)
                                            </div>
                                            <div class="ag-courses-item_date-box">
                                            &nbsp;
                                            <span class="ag-courses-item_date">
                                            &nbsp;
                                            </span>
                                            </div>
                                        </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php } ?>


                        </div>
                    </div>


                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <?php include('footer.php') ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
    <div class="right-bar">
        <div data-simplebar class="h-100">
            <div class="rightbar-title d-flex align-items-center px-3 py-4">
            
                <h5 class="m-0 me-2">Settings</h5>

                <a href="javascript:void(0);" class="right-bar-toggle ms-auto">
                    <i class="mdi mdi-close noti-icon"></i>
                </a>
            </div>

            <!-- Settings -->
            <hr class="mt-0" />
            <h6 class="text-center mb-0">Choose Layouts</h6>

            <div class="p-4">
                <div class="mb-2">
                    <img src="assets/images/layouts/layout-1.jpg" class="img-fluid img-thumbnail" alt="layout-1">
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input theme-choice" type="checkbox" id="light-mode-switch" checked>
                    <label class="form-check-label" for="light-mode-switch">Light Mode</label>
                </div>
    
                <div class="mb-2">
                    <img src="assets/images/layouts/layout-2.jpg" class="img-fluid img-thumbnail" alt="layout-2">
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input theme-choice" type="checkbox" id="dark-mode-switch" data-bsStyle="assets/css/bootstrap-dark.min.css" data-appStyle="assets/css/app-dark.min.css">
                    <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
                </div>
    
                <div class="mb-2">
                    <img src="assets/images/layouts/layout-3.jpg" class="img-fluid img-thumbnail" alt="layout-3">
                </div>
                <div class="form-check form-switch mb-5">
                    <input class="form-check-input theme-choice" type="checkbox" id="rtl-mode-switch" data-appStyle="assets/css/app-rtl.min.css">
                    <label class="form-check-label" for="rtl-mode-switch">RTL Mode</label>
                </div>

            
            </div>

        </div> <!-- end slimscroll-menu-->
    </div>
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    <!-- JAVASCRIPT -->
    <script src="assets/libs/jquery/jquery.min.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>

    <!-- apexcharts -->
    <script src="assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- Plugins js-->
    <script src="assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.min.js"></script>
    <script src="assets/libs/admin-resources/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js"></script>

    <script src="assets/js/pages/dashboard.init.js"></script>


    <script src="assets/js/app.js"></script>

</body>

</html>