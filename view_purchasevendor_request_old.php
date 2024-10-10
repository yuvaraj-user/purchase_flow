<?php
include('../auto_load.php');
include('adition.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer library files
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
if(!isset($_SESSION['EmpID']))
{
    ?>
    <script type="text/javascript">
        window.location = "../pages/indexAdmin.php";
    </script>
    <?php
}
// global query
$Employee_Id = $_SESSION['EmpID'];
$selector = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
WHERE Employee_Code = '$Employee_Id' ");
$selector_arr = sqlsrv_fetch_array($selector);

$department = $selector_arr['Department'];
$L1_Manager = $selector_arr['L1 Manager'];

$selector1 = sqlsrv_query($conn, "SELECT  * FROM Tb_Master_Emp
WHERE PO_creator_Release_Codes = '$Employee_Id' ");
$selector_arr1 = sqlsrv_fetch_array($selector1);

$recommender_to = $selector_arr1['Recommender'];
$approver_name = $selector_arr1['Approver'];
$verifier_name = $selector_arr1['Finance_Verfier'];
$verifier_code = $selector_arr1['Finance_Verfier'];
$Purchaser_Code = $selector_arr1['Purchaser'];
$Recommender_Code = $selector_arr1['Recommender'];
$Plant = $selector_arr1['Plant'];

//end
$purchase_is = $_GET['purchase_id'];
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
        <!-- ADD ON -->

        <link rel="stylesheet" type="text/css" href="tag.css">
            <!-- Summernote css -->
        <link href="assets/libs/summernote/summernote-bs4.min.css" rel="stylesheet" type="text/css" />
            <!-- multiselect -->
        <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

        <!-- ADD END -->
        <style>
        #involved .th,.td {
        border: 2px solid;
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
                                     <!-- <h4>Payment Request</h4> -->
                                         <ol class="breadcrumb m-0">
                                             <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                             <li class="breadcrumb-item"><a href="show_purchacevendor_request.php">Show Purchase Request</a></li>
                                             <li class="breadcrumb-item active">View Purchase Request</li>
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
                                            <?php
                                                $update_qry =  sqlsrv_query($conn, "SELECT * FROM Tb_Request WHERE Request_ID = '$purchase_is'");
                                                $updated_query = sqlsrv_fetch_array($update_qry);
                                                $PERSION =  $updated_query['Persion_In_Workflow'];
                                            ?>
                                            <form  method="POST" enctype="multipart/form-data">
                                                <div class="row">
                                                    <div class="col-md-2" > 
                                                        <div class="mb-3">
                                                            <label for="validationCustom01" class="form-label">RequestID</label>
                                                            <input type="text" class="form-control" 
                                                                value="<?php echo $updated_query['Request_ID'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" >
                                                        <div class="mb-3">
                                                            <label for="hasta" class="form-label">Department</label>
                                                            <input type="hidden" class="form-control"  value="Purchase & Vendor selection (R&VS)">
                                                            <input type="text" class="form-control"  readonly
                                                                value="<?php echo $updated_query['Department'] ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" >
                                                        <div class="mb-3">
                                                            <label for="Requester" class="form-label">Request Category</label>
                                                            <input type="text" class="form-control" readonly
                                                                value="<?php echo $updated_query['Request_Type'] ?>" >                                                        
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="app" class="form-label">Plant</label>
                                                            <input type="text" class="form-control"  readonly
                                                                value="<?php echo $updated_query['Plant'] ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label">Storage Location</label>
                                                            <input type="text" class="form-control"  readonly
                                                                value="<?php echo $updated_query['Storage_Location'] ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label">Finance Verification</label>
                                                            <input type="text" class="form-control"  readonly
                                                                value="<?php echo $updated_query['Finance_Verification'] ?>" >
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="padding: 0px 0px 20px 0px;">
                                                    <div class="table-responsive">
                                                        <table  class="table dt-table-hover" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Item Code</th>
                                                                    <th>Description</th>
                                                                    <th>UOM</th>
                                                                    <th>Material Group</th>
                                                                    <th>Quantity</th>
                                                                    <th>Expected Date</th>
                                                                    <th>Specification </th>
                                                                    <th>
                                                                    <?php 
                                                                    $sql =  sqlsrv_query($conn,"SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items
                                                                    ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID WHERE Tb_Request.Request_ID = '$purchase_is'");
                                                                    $row = sqlsrv_fetch_array($sql);
                                                                    ?>
                                                                    <?php
                                                                    if($row['Attachment'] == ''){

                                                                    }else{
                                                                        echo'Attachment';
                                                                        
                                                                    }
                                                                    ?>
                                                                    </th>
                                                                    <th>Whether Budgeted</th>
                                                                    <th>Involved Persion</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                    $i = 1;
                                                                    $sql = "SELECT * FROM Tb_Request INNER JOIN Tb_Request_Items
                                                                    ON Tb_Request.Request_ID = Tb_Request_Items.Request_ID WHERE Tb_Request.Request_ID = '$purchase_is'";
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
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Item_Code'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Description'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['UOM'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['MaterialGroup'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Quantity'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Expected_Date'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Specification'] ?>" >
                                                                    </td>
                                                                    <td>
                                                                    <?php 
                                                                    if($row['Attachment'] == ''){

                                                                    }else{
                                                                        ?>
                                                                        <input type="text" class="form-control" readonly data-bs-toggle="modal" 
                                                                    data-bs-target=".bs-example-modal-center<?php echo $row['ID'] ?>" value="<?php echo $row['Attachment'] ?>" >
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                    
                                                            
                                                                    </td>
                                                                    <div class="modal fade bs-example-modal-center<?php echo $row['ID'] ?>" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title mt-0">Attachment</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
																    <iframe src="file/<?php echo $row['Attachment'] ?>"
																      style="width: 100%;height: auto;" frameborder="0"></iframe>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger waves-effect waves-light btn-sm" data-bs-dismiss="modal" aria-label="Close">Close</button>
                                                                    </div>
                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal -->
                                                                    <td>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Budget'] ?>" >
                                                                    </td>
                                                                    
                                                                    <td>
                                                                    <?php
                                                            $HR_Master_Table = sqlsrv_query($conn, "SELECT * FROM HR_Master_Table WHERE Employee_Code IN (SELECT * FROM SPLIT_STRING('$PERSION',',')) ");
                                                            $idss = array();
                                                            while ($ids = sqlsrv_fetch_array($HR_Master_Table)){
                                                                $idss[] = $ids['Employee_Name'];
                                                            }
                                                            $implode = implode(',', $idss);
                                                        ?>
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $implode ?>" >
                                                                    </td>
                                                                </tr>
                                                                <?php } ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- MODEL -->
                                                    <div class="modal-section">
                                                    </div>
                                                    <!-- END MODEL -->
                                                </div>
                                                <h4>Involved Persion</h4>
                                                <table id="involved" >
                                          <thead>
                                            <tr>
                                              <th  class="th">Purchaser</th>
                                              <th  class="th">Finance</th>
                                              <th  class="th">Recommender</th>
                                              <th  class="th">Approver</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                            <tr>
                                            <?php
                                                        $selectorqw2 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$Purchaser_Code' ");
                                                        $selector_arrqw21 = sqlsrv_fetch_array($selectorqw2);
                                                    ?>
                                                     <td  class="td"><?php echo $selector_arrqw21['Employee_Name'] ?></td>
                                              <td class="td">
                                              
                                                    <?php
                                                        $selectorqw2 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$verifier_name' ");
                                                        $selector_arrqw2 = sqlsrv_fetch_array($selectorqw2);
                                                    ?>
                                                                                                        <?php
                                                        $selectorq2 = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$approver_name' ");
                                                        $selector_arrq2 = sqlsrv_fetch_array($selectorq2);
                                                    ?>
                                                                                                        <?php
                                                        $selectorq = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                                            WHERE Employee_Code = '$recommender_to' ");
                                                        $selector_arrq = sqlsrv_fetch_array($selectorq);
                                                    ?>
                                                    <?php echo $selector_arrqw2['Employee_Name'] ?></td>
                                              <td  class="td"><?php echo $selector_arrq['Employee_Name'] ?></td>
                                             
                                              <td  class="td"><?php echo $selector_arrq2['Employee_Name'] ?></td>
                                            </tr>
                                          </tbody>
                                        </table>
                                            </form>
                                        </div>
                                    </div>
                                </div> <!-- end col -->
                            </div>
                            <!-- end row -->

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

        <script src="assets/js/app.js"></script>
        <!-- ADD ON -->

        <script src="tag.js"></script>
            <!--tinymce js-->
        <script src="assets/libs/tinymce/tinymce.min.js"></script>
            <!-- Summernote js -->
        <script src="assets/libs/summernote/summernote-bs4.min.js"></script>
            <!-- init js -->
        <script src="assets/js/pages/form-editor.init.js"></script>
            <!-- multiselect -->
        <script src="assets/libs/select2/js/select2.min.js"></script>
        <script src="assets/js/pages/form-advanced.init.js"></script>

        <!-- ADD END -->
    </body>
</html>
