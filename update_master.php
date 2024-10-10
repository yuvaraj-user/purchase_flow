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
if (isset($_POST["master"])) {
    $Purchase_Type = $_POST['Purchase_Type'];
    $Plant = $_POST['Plant'];
    $Material_Group = $_POST['MaterialGroup'];
    $Department = $_POST['Department'];
    $Value = $_POST['Value'];
    $PO_creator_Release_Codes = $_POST['Requester'];
    $Purchaser = $_POST['Purchaser'];
    $Recommender = $_POST['Recommender'];
    $Finance_Verfier = $_POST['Finance'];
    $Approver = $_POST['Approver'];
    $Status = $_POST['Status'];

        sqlsrv_query($conn, "UPDATE Tb_Master_Emp set  Purchase_Type = '$Purchase_Type',Plant = '$Plant',Material_Group = '$Material_Group',
        Department = '$Department',Value = '$Value',PO_creator_Release_Codes = '$PO_creator_Release_Codes',Purchaser = '$Purchaser'
        ,Recommender = '$Recommender',Finance_Verfier = '$Finance_Verfier',Approver = '$Approver',Status = '$Status'
         WHERE id = '$master_id' ");
		?>
		<script type="text/javascript">
			alert("update successsfully");
			window.location = "show_master.php";
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

        <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <!-- Bootstrap Css -->
        <link href="assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

        <style>
            		input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
        </style>

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
                                             <li class="breadcrumb-item active">Update Master</li>
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
                                            
                                            <form  method="POST">
                                                <div class="row">
                                                    <?php
                                                        $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Master_Emp WHERE id = '$master_id'");
                                                        $updated_query = sqlsrv_fetch_array($update_qry);
                                                        $plant = $updated_query['Plant'];
                                                    ?>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip01" class="form-label">Purchase Type</label>
                                                            <select name="Purchase_Type"  class="form-select">
                                                                <option value="">Select Type</option>
                                                                <option <?php if ($updated_query['Purchase_Type'] == 'Financial - Payment requests (FP)') { ?> selected="selected" <?php } ?> value="Financial - Payment requests (FP)">Financial - Payment requests (FP)</option>
                                                                <option <?php if ($updated_query['Purchase_Type'] == 'Financial - Rate and terms request (FR)') { ?> selected="selected" <?php } ?> value="Financial - Rate and terms request (FR)">Financial - Rate and terms request (FR)</option>
                                                                <option <?php if ($updated_query['Purchase_Type'] == 'Operational requests (OP)') { ?> selected="selected" <?php } ?> value="Operational requests (OP)">Operational requests (OP)</option>
                                                                <option <?php if ($updated_query['Purchase_Type'] == 'Purchase & Vendor selection (R&VS)') { ?> selected="selected" <?php } ?> value="Purchase & Vendor selection (R&VS)">Purchase & Vendor selection (R&VS)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip02" class="form-label">Plant</label>
                                                            <select name="Plant"  class="form-control select2 ">
                                                                <option value="">Select Plant</option>
                                                                <?php
                                                                    $plant = sqlsrv_query($conn, "SELECT Plant_Code,Plant_Description FROM PP_PLANT  GROUP BY Plant_Code,Plant_Description");
                                                                    while ($c = sqlsrv_fetch_array($plant)) {
                                                                        
                                                                ?>
                                                                <option <?php if ($updated_query['Plant'] == trim($c['Plant_Code'])) { ?> selected="selected" <?php } ?>
                                                                 value="<?php echo $c['Plant_Code'] ?>">
                                                                    <?php echo $c['Plant_Code'] ?>-
                                                                    <?php echo $c['Plant_Description'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltipUsername" class="form-label">Material Group</label>
                                                            <select name="MaterialGroup"  class="form-control select2 ">
                                                                <option value="">Select Material</option>
                                                                <?php
                                                                    $Material = sqlsrv_query($conn, "SELECT MaterialGroup FROM MaterialMaster GROUP BY MaterialGroup ");
                                                                    while ($m = sqlsrv_fetch_array($Material)) {
                                                                ?>
                                                                <option <?php if ($updated_query['Material_Group']  == trim($m['MaterialGroup']) ) { ?> selected="selected" <?php } ?>
                                                                 value="<?php echo $m['MaterialGroup'] ?>"><?php echo $m['MaterialGroup'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltipUsername" class="form-label">Department</label>
                                                            <select name="Department"  class="form-control select2 ">
                                                                <option value="">Select Department</option>
                                                                <?php
                                                                    $Department = sqlsrv_query($conn, "SELECT Department FROM HR_Master_Table GROUP BY Department");
                                                                    while ($d = sqlsrv_fetch_array($Department)) {
                                                                ?>
                                                                <option <?php if ($updated_query['Department']  == trim($d['Department']) ) { ?> selected="selected" <?php } ?>
                                                                 value="<?php echo $d['Department'] ?>"><?php echo $d['Department'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip04" class="form-label">Value</label>
                                                            <input type="text" class="form-control"  name="Value" value="<?php echo $updated_query['Value'] ?>">
                                                        </div>
                                                    </div>
    
                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Requester</label>
                                                            <select name="Requester"  class="form-control select2 ">
                                                                <option value="">Select Requester</option>
                                                                <?php
                                                                    $Requester = sqlsrv_query($conn, "SELECT Employee_Code,Employee_Name FROM HR_Master_Table GROUP BY Employee_Code,Employee_Name");
                                                                    while ($r = sqlsrv_fetch_array($Requester)) {
                                                                ?>
                                                                <option <?php if ($updated_query['PO_creator_Release_Codes']  == trim($r['Employee_Code']) ) { ?> selected="selected" <?php } ?>
                                                                 value="<?php echo $r['Employee_Code'] ?>">
                                                                    <?php echo $r['Employee_Name'] ?>&nbsp;-&nbsp;<?php echo $r['Employee_Code'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Purchaser</label>
                                                            <select name="Purchaser"  class="form-control select2 ">
                                                                <option value="">Select Purchaser</option>
                                                                <?php
                                                                    $Purchaser = sqlsrv_query($conn, "SELECT Employee_Code,Employee_Name FROM HR_Master_Table GROUP BY Employee_Code,Employee_Name");
                                                                    while ($p = sqlsrv_fetch_array($Purchaser)) {
                                                                ?>
                                                                <option <?php if ($updated_query['Purchaser']  == trim($p['Employee_Code']) ) { ?> selected="selected" <?php } ?>
                                                                 value="<?php echo $p['Employee_Code'] ?>">
                                                                    <?php echo $p['Employee_Name'] ?>&nbsp;-&nbsp;<?php echo $p['Employee_Code'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Finance Verifier</label>
                                                            <select name="Finance"  class="form-control select2 ">
                                                                <option value="">Select Financer</option>
                                                                <?php
                                                                    $Finance = sqlsrv_query($conn, "SELECT Employee_Code,Employee_Name FROM HR_Master_Table GROUP BY Employee_Code,Employee_Name");
                                                                    while ($f = sqlsrv_fetch_array($Finance)) {
                                                                ?>
                                                                <option <?php if ($updated_query['Finance_Verfier']  == trim($f['Employee_Code']) ) { ?> selected="selected" <?php } ?>
                                                                 value="<?php echo $f['Employee_Code'] ?>">
                                                                    <?php echo $f['Employee_Name'] ?>&nbsp;-&nbsp;<?php echo $f['Employee_Code'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Recommender</label>
                                                            <select name="Recommender"  class="form-control select2 ">
                                                                <option value="">Select Recommender</option>
                                                                <?php
                                                                    $Recommender = sqlsrv_query($conn, "SELECT Employee_Code,Employee_Name FROM HR_Master_Table GROUP BY Employee_Code,Employee_Name");
                                                                    while ($r = sqlsrv_fetch_array($Recommender)) {
                                                                ?>
                                                                <option <?php if ($updated_query['Recommender']  == trim($r['Employee_Code']) ) { ?> selected="selected" <?php } ?>
                                                                 value="<?php echo $r['Employee_Code'] ?>">
                                                                    <?php echo $r['Employee_Name'] ?>&nbsp;-&nbsp;<?php echo $r['Employee_Code'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Approver</label>
                                                            <select name="Approver"  class="form-control select2 ">
                                                                <option value="">Select Approver</option>
                                                                <?php
                                                                    $Approver = sqlsrv_query($conn, "SELECT Employee_Code,Employee_Name FROM HR_Master_Table GROUP BY Employee_Code,Employee_Name");
                                                                    while ($a = sqlsrv_fetch_array($Approver)) {
                                                                ?>
                                                                <option <?php if ($updated_query['Approver']  == trim($a['Employee_Code']) ) { ?> selected="selected" <?php } ?>
                                                                value="<?php echo $a['Employee_Code'] ?>">
                                                                    <?php echo $a['Employee_Name'] ?>&nbsp;-&nbsp;<?php echo $a['Employee_Code'] ?>
                                                                </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="mb-3 position-relative">
                                                            <label for="validationTooltip03" class="form-label">Status</label>
                                                            <select name="Status"  class="form-select">
                                                                <option value="">Select Status</option>
                                                                <option <?php if ($updated_query['Status']  == 'Active') { ?> selected="selected" <?php } ?> value="Active">Active</option>
                                                                <option <?php if ($updated_query['Status']  == 'In Active') { ?> selected="selected" <?php } ?> value="In Active">In Active</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
            
                                                    <center><button class="btn btn-primary" name="master" type="submit">Submit</button></center>
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

        <script>
                        $(document).ready(function() {
  $("input[type=number]").on("focus", function() {
    $(this).on("keydown", function(event) {
      if (event.keyCode === 38 || event.keyCode === 40) {
        event.preventDefault();
      }
    });
  });
});
        </script>
    </body>
</html>
