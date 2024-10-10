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
$master_id = $_GET['id'];

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

        <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />


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
                                             <li class="breadcrumb-item"><a href="show_master.php">Show Master</a></li>
                                             <li class="breadcrumb-item active">View Master</li>
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
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            
                                            <form method="POST">
                                                <div class="row">
                                                    <?php
                                                        $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Master_Emp WHERE id = '$master_id'");
                                                        $updated_query = sqlsrv_fetch_array($update_qry);
                                                    ?>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip01" class="form-label">Purchase Type</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Purchase_Type'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip02" class="form-label">Plant</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Plant'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltipUsername" class="form-label">Material Group</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Material_Group'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltipUsername" class="form-label">Department</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Department'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip04" class="form-label">Value</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Value'] ?>">
                                                        </div>
                                                    </div>
    
                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Requester</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['PO_creator_Release_Codes'] ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Purchaser</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Purchaser'] ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Finance Verifier</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Finance_Verfier'] ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Recommender</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Recommender'] ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Approver</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Approver'] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Status</label>
                                                            <input type="text" class="form-control" readonly value="<?php echo $updated_query['Status'] ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
        
                        
                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

              
                
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <script>document.write(new Date().getFullYear())</script> © Morvin.
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-end d-none d-sm-block">
                                    Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesdesign
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>
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

        <script src="assets/libs/parsleyjs/parsley.min.js"></script>

        <script src="assets/js/pages/form-validation.init.js"></script>

        <script src="assets/libs/select2/js/select2.min.js"></script>
        <script src="assets/js/pages/form-advanced.init.js"></script>

        <script src="assets/js/app.js"></script>

    </body>
</html>
