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
    <title>
        <?php echo $Title ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="../global/photos/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />



    <!-- DataTables -->
    <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet"
        type="text/css" />

    <!-- Responsive datatable examples -->
    <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet"
        type="text/css" />

</head>


<body data-keep-enlarged="true" class="vertical-collpsed" >
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
                                         <ol class="breadcrumb m-0">
                                             <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                             <li class="breadcrumb-item active">Show Informer Details For Payment</li>
                                         </ol>
                                 </div>
                             </div>
                             <div class="col-sm-6">
                                <div class="float-end d-none d-sm-block">
                                    <h4 style="color:white;"> <?php echo $_SESSION['EmpID'] ?></h4>
                                </div>
                                <div class="float-end d-none d-sm-block">
                                    <h4 style="color:white;">Informer :&nbsp; </h4>
                                </div>
                             </div>
                         </div>
                        </div>
                   </div>
                <!-- end page title -->


                <div class="container-fluid">

                    <div class="page-content-wrapper">

                        <div class="row">
                                <div class="col-xl-12">
                                    <div class="card">
                                        <div class="card-body">
            
                                            <!-- Nav tabs -->
                                           
            
                                            <!-- Tab panes -->
                                            <div class="tab-content p-3 text-muted">
                                                    <div class="table-responsive">
                                                        <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Request ID</th>
                                                                    <th>Request Type</th>
                                                                    <th>Department</th>
                                                                    <th>Date Of Request</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    $i = 1;
                                                                   
                                                                    $sql = "SELECT * FROM Tb_Payment_Request WHERE Persion_In_Workflow like '%".$_SESSION['EmpID']."%'    ORDER BY Id DESC";
                                                                    $params = array();
                                                                    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
                                                                    $stmt = sqlsrv_prepare($conn, $sql, $params, $options);
                                                                    sqlsrv_execute($stmt);
                                                                    while ($row = sqlsrv_fetch_array($stmt)) {
                                                                ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php echo $i++ ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $row['Request_PID'] ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $row['Request_Type'] ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $row['Depatment'] ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $row['Time_Log']->format('d/m/Y') ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                                if($row['Status'] == 'Requested'){ 
                                                                                    echo'  <span class="badge badge-soft-primary">Requested</span>';    
                                                                                }else{
                                                                                    if ($row['Status'] == 'Recommended') {
                                                                                        echo'  <span class="badge badge-soft-info">Recommended</span>';
                                                                                    } elseif($row['Status']=='Approved')
                                                                                    {
                                                                                        echo'<span class="badge badge-soft-success">Approved</span>';
                                                                                    } elseif($row['Status']=='Send Back')
                                                                                    {
                                                                                        echo'<span class="badge badge-soft-warning">Return</span>';
                                                                                    }elseif($row['Status']=='Rejected')
                                                                                    {
                                                                                        echo'<span class="badge badge-soft-danger">Rejected</span>';
                                                                                    }
                                                                                }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                            <div class="action-btns">
                                                                                <a href="view_informer_payment_details.php?pay_id=<?php echo $row['Request_PID'] ?>" class="action-btn btn-view bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="View">
                                                                                <i class="mdi mdi-eye"></i>
                                                                            </a>
                                                    
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                            </div>
            
                                        </div>
                                    </div>
                                </div> <!-- end row -->
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

        <!-- /Right-bar -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        <script src="assets/libs/jquery/jquery.min.js"></script>
        <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/libs/metismenu/metisMenu.min.js"></script>
        <script src="assets/libs/simplebar/simplebar.min.js"></script>
        <script src="assets/libs/node-waves/waves.min.js"></script>

        <!-- Required datatable js -->
        <script src="assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        <!-- Buttons examples -->
        <script src="assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
        <script src="assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
        <script src="assets/libs/jszip/jszip.min.js"></script>
        <script src="assets/libs/pdfmake/build/pdfmake.min.js"></script>
        <script src="assets/libs/pdfmake/build/vfs_fonts.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
        <script src="assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
        <!-- Responsive examples -->
        <script src="assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
        <script src="assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

        <!-- Datatable init js -->
        <script src="assets/js/pages/datatables.init.js"></script>
        <script>$(document).ready(function() {
            $('#datatable1').DataTable();
            $('#datatable2').DataTable();
            $('#datatable3').DataTable();
            } );
        </script>

        <script src="assets/js/app.js"></script>

</body>

</html>