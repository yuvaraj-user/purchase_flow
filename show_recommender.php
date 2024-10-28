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

        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />


        
        <!-- DataTables -->
        <link href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

        <!-- Responsive datatable examples -->
        <link href="assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />  

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
                                             <li class="breadcrumb-item active">Show Recommender Request</li>
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
                                <div class="col-12">
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
                                                    <a class="nav-link" data-bs-toggle="tab" href="#settings" role="tab">
                                                        <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                                        <span class="d-none d-sm-block">Reject</span>    
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
                                                            class="table table-striped table-bordered nowrap"
                                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                <th>S/No</th>
                                                                <th>Request ID</th>
                                                                <th>Date Of Request</th>
                                                                <th>Request Type</th>
                                                                <th>Category</th>
                                                                <th>Department</th>
                                                                <th>Plant</th>
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    $i = 1;
                                                                    // $sql = "SELECT * FROM Tb_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                                    // WHERE Tb_Master_Emp.Recommender='$Employee_Id' and Tb_Request.status='Added' ORDER BY Tb_Request.Id DESC";

                                                                    // $sql = "SELECT * FROM Tb_Request Inner Join (select DISTINCT Purchaser,Purchase_Type,Recommender,Document_type from Tb_Master_Emp) as Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                                    //     WHERE Tb_Master_Emp.Recommender='$Employee_Id' and Tb_Request.status='Added'  AND Tb_Master_Emp.Document_type IS NOT NULL AND Tb_Master_Emp.Document_type != '' ORDER BY Tb_Request.Id DESC";

                                                                    $sql = "SELECT Tb_Request.Id,Tb_Request.Request_ID,Tb_Request.Request_Type,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Department,Tb_Request.status,Tb_Request.Plant FROM Tb_Request
                                                                    Inner Join (select DISTINCT Purchaser,Purchase_Type,Recommender,Document_type,Material_Group from Tb_Master_Emp) as Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                                    --INNER JOIN MaterialMaster ON MaterialMaster.Plant = Tb_Request.Plant AND MaterialMaster.MaterialGroup = Tb_Master_Emp.Material_Group AND
                                                                    --MaterialMaster.StorageLocation = Tb_Request.Storage_Location
                                                                    WHERE Tb_Master_Emp.Recommender='$Employee_Id' and Tb_Request.Recommender='$Employee_Id' and Tb_Request.status='Added' and (Tb_Request.is_sendbacked IS NULL OR Tb_Request.is_sendbacked = 0) 
                                                                     GROUP BY Tb_Request.Request_ID,Tb_Request.Request_Type,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Department,Tb_Request.status,Tb_Request.Id,Tb_Request.Plant  
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
                                                                        <?php echo $row['Plant'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                            if($row['status'] == 'Added'){ 
                                                                                echo'<span class="badge badge-soft-primary">Pending</span>';    
                                                                            }else{
                                                                                
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <form method="POST">
                                                                                <a href="recommender_screen.php?id=<?php echo $row['Request_ID'] ?>"
                                                                                    class="btn btn-primary btn-sm mb-2 me-4"> 
                                                                                        Recommendation</a>
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
                                                            class="table table-striped table-bordered nowrap"
                                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Request ID</th>
                                                                    <th>Date Of Request</th>
                                                                    <th>Request Type</th>
                                                                    <th>Category</th>
                                                                    <th>Department</th>
                                                                    <th>Approver Send Back Remark</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    $i = 1;
                                                                    $sql = "SELECT * FROM Tb_Request Inner Join (select DISTINCT Purchaser,Purchase_Type,Recommender from Tb_Master_Emp WHERE Tb_Master_Emp.Recommender='$Employee_Id') as Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                                    WHERE Tb_Request.Recommender='$Employee_Id' and Tb_Request.is_sendbacked='1' and Tb_Request.Recommender_back_remark IS NULL ORDER BY Tb_Request.Id DESC";

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
                                                                        <?php echo $row['Approver_back_remark'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                            if($row['status'] == 'Approver Send Back'){ 
                                                                                echo'<span class="badge badge-soft-warning">Retern</span>';    
                                                                            }else{
                                                                                
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="action-btns">
                                                                            <a href="view_recommender_request.php?id=<?php echo $row['Request_ID'] ?>" class="action-btn btn-view bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="View">
                                                                            <i class="mdi mdi-eye"></i>
                                                                        </a>
                                                                            <?php
                                                                            if($row['is_sendbacked'] == '1'){ 
                                                                                echo'<a href="Update_recommender_request.php?id='.$row['Request_ID'] .'" class="action-btn btn-edit bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="Edit">
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
                                                <div class="tab-pane" id="settings" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table id="datatable2"
                                                            class="table table-striped table-bordered nowrap"
                                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Request ID</th>
                                                                    <th>Date Of Request</th>
                                                                    <th>Request Type</th>
                                                                    <th>Category</th>
                                                                    <th>Department</th>
                                                                    <th>Plant</th>
                                                                    <th>Rejected For Approver Remark</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $i = 1;
                                                                // $sql = "SELECT * FROM Tb_Request Inner Join Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                                // WHERE Tb_Master_Emp.Recommender='$Employee_Id' and Tb_Request.status='Approver_Reject' ORDER BY Tb_Request.Id DESC";

                                                                $sql = "SELECT * FROM Tb_Request
                                                                    Inner Join (select DISTINCT Purchaser,Purchase_Type,Recommender from Tb_Master_Emp WHERE Tb_Master_Emp.Recommender='$Employee_Id') as Tb_Master_Emp On Tb_Master_Emp.Purchase_Type = Tb_Request.Request_Category
                                                                    WHERE Tb_Request.Recommender='$Employee_Id' and Tb_Request.status='Approver_Reject' 
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
                                                                            <?php echo $row['Plant'] ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php echo $row['Approver_reject'] ?>
                                                                        </td>
                                                                        <td>
                                                                            <?php
                                                                                if($row['status'] == 'Approver_Reject'){ 
                                                                                    echo'<span class="badge badge-soft-danger">Rejected</span>';    
                                                                                }else{
                                                                                    
                                                                                }
                                                                            ?>
                                                                        </td>
                                                                        <td>
                                                                        <div class="action-btns">
                                                                            <a href="view_recommender_request.php?id=<?php echo $row['Request_ID'] ?>" class="action-btn btn-view bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="View">
                                                                            <i class="mdi mdi-eye"></i>
                                                                        </a>
                                                                            <?php
                                                                            if($row['status'] == 'Approver_Reject'){ 
                                                                                echo'<a href="Update_recommender_request.php?id='.$row['Request_ID'] .'" class="action-btn btn-edit bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="Edit">
                                                                                <i class="mdi mdi-pencil"></i>
                                                                                </a>';    
                                                                            }else{
                                                                                

                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="messages" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table id="datatable3" class="table table-striped table-bordered nowrap"
                                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Request ID</th>
                                                                    <th>Date Of Request</th>
                                                                    <th>Request Type</th>
                                                                    <th>Category</th>
                                                                    <th>Department</th>
                                                                    <th>Plant</th>
                                                                    <th>Status</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    $i = 1;
                                                                    $sql = "SELECT Request_ID, Time_Log, Request_Category, Request_Type, Department, status, Plant,is_sendbacked FROM (SELECT Tb_Request.Request_ID,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Request_Type,Tb_Request.Department,Tb_Request.status,Tb_Request.Plant,Tb_Request.is_sendbacked
                                                                    FROM Tb_Recommender 
                                                                    INNER JOIN (SELECT DISTINCT Purchaser,Purchase_Type,Recommender from Tb_Master_Emp WHERE Tb_Master_Emp.Recommender='$Employee_Id') as Tb_Master_Emp On Tb_Master_Emp.Recommender=Tb_Recommender.EMP_ID 
                                                                    INNER JOIN Tb_Request ON Tb_Request.Request_ID = Tb_Recommender.Request_id
                                                                    WHERE Tb_Request.Recommender='$Employee_Id' AND ((Tb_Request.status !='Recommender_Rejected' and Tb_Request.status !='Approver_Reject') OR (Tb_Request.status !='Recommender_Rejected' and Tb_Request.status !='Approver_Reject' and Tb_Request.is_sendbacked = '1')) GROUP BY Tb_Request.Request_ID,Tb_Request.Time_Log,Tb_Request.Request_Category,Tb_Request.Request_Type,Tb_Request.Department,Tb_Request.status,Tb_Request.Plant,Tb_Request.is_sendbacked

                                                                    UNION
    
                                                                    SELECT Tb_Request.Request_ID, Tb_Request.Time_Log, Tb_Request.Request_Category, Tb_Request.Request_Type, Tb_Request.Department, Tb_Request.status, Tb_Request.Plant,Tb_Request.is_sendbacked 
                                                                    FROM Tb_Request 
                                                                    WHERE Approver = '$Employee_Id' 
                                                                    AND is_sendbacked = '1' 
                                                                    AND Recommender_back_remark IS NOT NULL) AS result
                                                                    ORDER BY Request_ID DESC";
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
                                                                        <?php echo $row['Plant'] ?>
                                                                    </td>
                                                                    <td>
                                                                        <?php
                                                                            if($row['status'] == 'Recommended' && $row['is_sendbacked'] != '1'){ 
                                                                                echo'  <span class="badge badge-soft-primary">Recommended</span>';    
                                                                            }else{
                                                                                if ($row['status'] == 'Approved' && $row['is_sendbacked'] != '1') {
                                                                                    echo'  <span class="badge badge-soft-success">Approved</span>';
                                                                                }elseif ($row['status'] == 'Review' && $row['is_sendbacked'] != '1' ) {
                                                                                    echo'  <span class="badge badge-soft-info">Finance Verification</span>';
                                                                                }elseif ($row['status'] == 'Waiting_for_approval2' && $row['is_sendbacked'] != '1') {
                                                                                    echo'  <span class="badge badge-soft-primary">Waiting for second level approval</span>';
                                                                                }elseif ($row['is_sendbacked'] == '1') {
                                                                                    echo'  <span class="badge badge-soft-warning">Sendback</span>';
                                                                                }
                                                                            }
                                                                        ?>
                                                                    </td>
                                                                    <td>
                                                                        <div class="action-btns">
                                                                            <?php if($row['is_sendbacked'] != 1) { ?>
                                                                            <a href="view_recommender_request.php?id=<?php echo $row['Request_ID'] ?>" class="action-btn btn-view bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="View">
                                                                            <i class="mdi mdi-eye"></i>
                                                                            </a>
                                                                            <?php } ?>

                                                                            <?php
                                                                            if($row['status'] == 'Recommended' && $row['is_sendbacked'] != '1'){ 
                                                                                echo'<a href="Update_recommender_request.php?id='.$row['Request_ID'] .'" class="action-btn btn-edit bs-tooltip me-2" data-toggle="tooltip" data-placement="top" title="Edit">
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
                                </div> <!-- end col -->
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