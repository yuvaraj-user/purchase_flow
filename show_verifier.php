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
                                        <li class="breadcrumb-item active">Show Verifier Request</li>
                                    </ol>
                                </div>
                            </div>
                            <!-- <div class="col-sm-6">
                                <div class="float-end d-none d-sm-block">
                                    <a href="payment_request.php" class="btn btn-success">New Request</a>
                                </div>
                             </div> -->
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
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#home" role="tab">
                                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                                        <span class="d-none d-sm-block">Pending</span>    
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#profile" role="tab">
                                                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                        <span class="d-none d-sm-block">Send Back</span>    
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#messages" role="tab">
                                                        <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                                        <span class="d-none d-sm-block">Verification</span>    
                                                    </a>
                                                </li>
                                            </ul>
            
                                            <!-- Tab panes -->
                                            <div class="tab-content p-3 text-muted">
                                                <div class="tab-pane active" id="home" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table id="datatable"
                                                            class="table table-striped table-bordered dt-responsive nowrap"
                                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Request ID</th>
                                                                    <th>Date Of Request</th>
                                                                    <th>Request Type</th>
                                                                    <th>Category</th>
                                                                    <th>Department</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $i = 1;
                                                                // $sql = "SELECT * FROM Tb_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                                // WHERE Tb_Master_Emp.Finance_Verfier='".$_SESSION['EmpID']."' and Tb_Request.status='Review' ORDER BY Tb_Request.Request_ID DESC";

                                                                $sql = "SELECT Tb_Request.Id,Tb_Request.Request_ID,Tb_Request.Request_Type,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Department,Tb_Request.status FROM Tb_Request
                                                                    Inner Join (select DISTINCT Purchaser,Purchase_Type,Finance_Verfier,Document_type,Material_Group from Tb_Master_Emp) as Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                                    INNER JOIN MaterialMaster ON MaterialMaster.Plant = Tb_Request.Plant AND MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group AND
                                                                    MaterialMaster.StorageLocation = Tb_Request.Storage_Location
                                                                    WHERE Tb_Master_Emp.Finance_Verfier='".$_SESSION['EmpID']."' and Tb_Request.status='Review' AND Tb_Master_Emp.Document_type IS NOT NULL AND Tb_Master_Emp.Document_type != ''
                                                                     GROUP BY Tb_Request.Request_ID,Tb_Request.Request_Type,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Department,Tb_Request.status,Tb_Request.Id  
                                                                     ORDER BY Tb_Request.Id DESC";


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
                                                                        <?php echo $row['Request_ID'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Time_Log']->format('d/m/Y') ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Request_Category'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Request_Type'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Department'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                            if($row['status'] == 'Review'){ 
                                                                                echo'<span class="badge badge-soft-primary">Pending</span>';    
                                                                            }else{
                                                                                
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <form method="POST">
                                                                            <a href="review_request.php?id=<?php echo $row['Request_ID'] ?>"
                                                                                class="btn btn-primary waves-effect waves-light btn-sm">Review</a>
                                                                        </form>
                                                                    </td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="profile" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table id="datatable1"
                                                            class="table table-striped table-bordered dt-responsive nowrap"
                                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Request ID</th>
                                                                    <th>Date Of Request</th>
                                                                    <th>Request Type</th>
                                                                    <th>Category</th>
                                                                    <th>Department</th>
                                                                    <th>Remarks For Send Back</th>
                                                                    <th>Status</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    $i = 1;
                                                                    $sql = "SELECT * FROM Tb_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                                    WHERE Tb_Master_Emp.Finance_Verfier='".$_SESSION['EmpID']."' and Tb_Request.status='Send Back' ORDER BY Tb_Request.Request_ID DESC";
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
                                                                        <?php echo $row['Request_ID'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Time_Log']->format('d/m/Y') ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Request_Category'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Request_Type'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Department'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Sendback_remark'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                            if($row['status'] == 'Send Back'){ 
                                                                                echo'<span class="badge badge-soft-warning">Send Back</span>';    
                                                                            }else{
                                                                                
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="messages" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table id="datatable2"
                                                            class="table table-striped table-bordered dt-responsive nowrap"
                                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Request ID</th>
                                                                    <th>Date Of Request</th>
                                                                    <th>Request Type</th>
                                                                    <th>Category</th>
                                                                    <th>Department</th>
                                                                    <th>Remarks For Send Back</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    $i = 1;
                                                                    $sql = "SELECT Tb_Finance_verifier.Request_Id,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Request_Type,Tb_Finance_verifier.Sendback_remarks,
                                                                    Tb_Request.Request_ID,Tb_Request.status,Tb_Request.Department
                                                                    FROM Tb_Finance_verifier Inner Join Tb_Master_Emp 
                                                                    On Tb_Master_Emp.Finance_Verfier=Tb_Finance_verifier.EMP_ID INNER JOIN Tb_Request ON
                                                                    Tb_Request.Request_ID = Tb_Finance_verifier.Request_Id WHERE Tb_Master_Emp.Finance_Verfier='".$_SESSION['EmpID']."'
                                                                    GROUP BY  Tb_Finance_verifier.Request_Id,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Request_Type,Tb_Finance_verifier.Sendback_remarks,
                                                                    Tb_Request.Request_ID,Tb_Request.status,Tb_Request.Department ORDER BY Tb_Finance_verifier.Request_Id DESC";
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
                                                                        <?php echo $row['Request_Id'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Time_Log']->format('d/m/Y') ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Request_Category'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Request_Type'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Department'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php echo $row['Sendback_remarks'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                            if($row['status'] == 'Requested'){ 
                                                                                echo'  <span class="badge badge-soft-primary">Requested</span>';    
                                                                            }else{
                                                                                if ($row['status'] == 'Added') {
                                                                                    echo'  <span class="badge badge-soft-info">Quotation Added</span>';
                                                                                } elseif($row['status']=='Recommended')
                                                                                {
                                                                                    echo'<span class="badge badge-soft-info">Recommended</span>';
                                                                                }
                                                                                elseif($row['status']=='Approved')
                                                                                {
                                                                                    echo'<span class="badge badge-soft-success">Approved</span>';
                                                                                }
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="action-btns">
                                                                            <a href="view_review_request.php?id=<?php echo $row['Request_Id'] ?>" class="action-btn btn-view bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="View">
                                                                            <i class="mdi mdi-eye"></i>
                                                                        </a>
                                                                            <?php
                                                                            if($row['status'] == 'Recommended'){ 
                                                                                echo'<a href="update_review_request.php?id='.$row['Request_Id'] .'" class="action-btn btn-edit bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="Edit">
                                                                                <i class="mdi mdi-pencil"></i>
                                                                                </a>';    
                                                                            }else{
                                                                                

                                                                            }
                                                                            ?>
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
            } );
        </script>

        <script src="assets/js/app.js"></script>

</body>

</html>