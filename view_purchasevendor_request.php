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
$req_id = $_GET['request_id'];
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

        @media only screen and (max-width: 600px) {
            .footer {
                left: 0 !important;
                text-align: center;
             }      
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
                                             <li class="breadcrumb-item active">Update Purchase Request</li>
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
                                                $update_qry =  sqlsrv_query($conn, "SELECT *,Tb_Request_Items.MaterialGroup FROM Tb_Request 
                                                LEFT JOIN (SELECT TOP 1 * FROM Tb_Request_Items WHERE Tb_Request_Items.Request_ID = '$purchase_is') as Tb_Request_Items on Tb_Request_Items.Request_ID = Tb_Request.Request_ID 
                                                    WHERE Tb_Request.Request_ID = '$purchase_is'");
                                                $updated_query = sqlsrv_fetch_array($update_qry);
                                                $PERSION =  $updated_query['Persion_In_Workflow'];
                                            ?>
                                            <form  method="POST" enctype="multipart/form-data">
                                                 <input type="hidden" id="po_creator_id" value="<?php echo $updated_query['EMP_ID']; ?>">

                                                <div class="row">
                                                    <div class="col-md-2" > 
                                                        <div class="mb-3">
                                                            <label for="validationCustom01" class="form-label">RequestID</label>
                                                            <input type="text" class="form-control" id="validationCustom01" name="request_id"
                                                            value="<?php echo $updated_query['Request_ID'] ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" >
                                                        <div class="mb-3">
                                                            <label for="hasta" class="form-label">Department</label>
                                                            <input type="hidden" class="form-control" id="request" name="request_type1" value="Purchase & Vendor selection (R&VS)">
                                                            <input type="text" class="form-control" name="department" id="Department3" readonly
                                                                value="<?php echo $updated_query['Department'] ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" >
                                                        <div class="mb-3">
                                                            <label for="Requester" class="form-label">Request Category</label>
                                                             <input type="text" class="form-control" name="department" id="request_type" readonly
                                                                value="<?php echo $updated_query['Request_Type'] ?>" >                                                    
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <?php 
                                                            $sql = sqlsrv_query($conn, "SELECT DISTINCT Plant_Name from Plant_Master_PO where Plant_Code = '".$updated_query['Plant']."'");
                                                            $plant_name = sqlsrv_fetch_array($sql)['Plant_Name'];
                                                            ?>
                                                            <label for="app" class="form-label">Plant</label>
                                                            <input type="text" class="form-control" name="department" id="plant" readonly
                                                                value="<?php echo $updated_query['Plant'].'-'.$plant_name ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" style="<?php if($updated_query['Request_Type'] == 'Services'){ ?> display: none; <?php } ?>">
                                                        <div class="mb-3">
                                                            <?php 
                                                            $sql = sqlsrv_query($conn, "SELECT DISTINCT Storage_description from MaterialMaster where StorageLocation = '".$updated_query['Storage_Location']."'");
                                                            $storage_desc = sqlsrv_fetch_array($sql)['Storage_description'];
                                                            ?>
                                                            <label for="department" class="form-label">Storage Location</label>
                                                             <input type="text" class="form-control"  readonly
                                                                value="<?php echo trim($updated_query['Plant']).'-'.trim($updated_query['Storage_Location']).'-'.trim($storage_desc) ?>" >
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="department" class="form-label">Material group</label>
                                                             <input type="text" class="form-control"  id="mat_group" readonly
                                                                value="<?php echo $updated_query['MaterialGroup'] ?>" >
                                                        </div>
                                                    </div>

                                                    <?php if($department == 'Sales & Marketing') { ?>

                                                    <div class="col-md-2 season-div">
                                                        <div class="mb-3">
                                                            <label for="season" class="form-label season_label" >Season</label>
                                                             <input type="text" class="form-control"  id="season" readonly value="<?php echo $updated_query['Season'] ?>"> 
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 activity-div">
                                                        <div class="mb-3">
                                                            <label for="activity" class="form-label activity_label" >Activity</label>
                                                             <input type="text" class="form-control"  id="activity" readonly value="<?php echo $updated_query['Activity'] ?>"> 

                                                        </div>
                                                    </div>

                                                    <div class="col-md-2 crop-year-div">
                                                        <div class="mb-3">
                                                            <label for="crop_year" class="form-label crop_year_label" >Year</label>
                                                             <input type="text" class="form-control"  id="crop_year" readonly value="<?php echo $updated_query['Crop_Year'] ?>"> 

                                                        </div>
                                                    </div>

                                                    <?php } ?>
                                                </div>
                                                <div class="row" style="padding: 0px 0px 20px 0px;">
                                                    <div class="table-responsive">
                                                        <?php
                                                             
                                                            $replacement_style = ($updated_query['Request_Type'] != 'Asset purchases') ? 'display:none !important;' : '';   

                                                        ?>
                                                    <table id="table_id" class="table dt-table-hover" style="width:100%">
                                                            <thead>
                                                                <tr>
                                                                    <th>S/No</th>
                                                                    <th>Item Code</th>
                                                                    <th>Description</th>
                                                                    <th>UOM</th>
                                                                    <th>Material Group</th>
                                                                    <th>Quantity</th>
                                                                    <th class="replacement_feature" style="<?php echo $replacement_style; ?>">Replacement</th>
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
                                                                </tr>
                                                            </thead>
                                                            <tbody id="selected" class="tbody tbody1">
                                                             <?php
                                                                    $i = 1;
                                                                    $j = 1;
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
                                                                            value="<?php echo substr(trim($row['Item_Code']),-10); ?>" >
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

                                                                    <td class="replacement_feature" style="<?php echo $replacement_style; ?>">
                                                                    <input type="text" class="form-control" readonly
                                                                            value="<?php echo $row['Replace_Type'] ?>" >

                                                                            <?php if($row['Replace_Type'] == 'replacement')
                                                                                {?>
                                                                                    <button type="button" class="btn btn-primary viewModalBtn mt-1" data-bs-toggle="modal" id="editModalBtn<?php echo $j; ?>" data-bs-target="#viewModal" data-btn="<?php echo $j; ?>">
                                                                                        View Replacement
                                                                                  </button>

                                                                                   <div class="modal viewModal" id="viewModal" tabindex="-1">
                                                                                        <div class="modal-dialog">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <h5 class="modal-title">View Replacement Details</h5>
                                                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                                </div>
                                                                                                <div class="modal-body">
                                                                                                    <!-- Inputs for the modal -->
                                                                                                    <div class="form-group">
                                                                                                        <label for="modal_dateofpurchase">Date of Purchase</label>
                                                                                                        <input type="date" class="form-control emodal_dateofpurchase" id="emodal_dateofpurchase" required readonly>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label for="modal_qty">Quantity</label>
                                                                                                        <input type="number" class="form-control emodal_qty" id="emodal_qty" required readonly>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label for="modal_remarks">Remarks</label>
                                                                                                        <textarea class="form-control emodal_remarks" id="emodal_remarks" required readonly></textarea>
                                                                                                    </div>
                                                                                                    <div class="form-group">
                                                                                                        <label for="modal_cost">Cost</label>
                                                                                                        <input type="text" class="form-control emodal_cost" id="emodal_cost" required readonly>
                                                                                                    </div>
                                                                                                    <!-- Hidden input to store the row ID -->
                                                                                                    <input type="hidden" class="erow_id" id="erow_id">
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                                                    <!-- <button type="button" class="btn btn-primary updateReplacement" id="updateReplacement">Save changes</button> -->
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>

                                                                              <?php } ?>
                                                                                <input type="hidden" id="rdateofpurchase<?php echo $j; ?>" name="rdateofpurchase[]" value="<?php echo $row['Date_of_Purchase']; ?>">
                                                                                <input type="hidden" id="rqty<?php echo $j; ?>" value="<?php echo $row['Replace_Qty']; ?>" name="rqty[]">
                                                                                <input type="hidden" id="rremarks<?php echo $j; ?>" name="rremarks[]" value="<?php echo $row['Replace_Remarks']; ?>">
                                                                                <input type="hidden" id="rcost<?php echo $j; ?>" name="rcost[]" value="<?php echo $row['Replace_Cost']; ?>">

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
                                                                    
                                                                </tr>
                                                                <?php $j++; } ?>
                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- MODEL -->
                                                    <div class="modal-section">
                                                    </div>
                                                    <!-- END MODEL -->
                                                </div>

                                                <div class="row" id="involved_persons_div" style="display:none;">
                                                    <div class="col-md-5">
                                                        <h4>Involved Persons</h4>
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-bordered table-hover" >
                                                              <thead>
                                                                <tr>
                                                                  <th>Purchaser</th>
                                                                  <!-- <th>Finance</th> -->
                                                                  <th>Recommender</th>
                                                                  <th>Approver</th>
                                                                  <th style="display:none;" class="inv_fin_appr">Final Approver</th>
                                                                </tr>
                                                              </thead>
                                                              <tbody id="involved_persons_tbody">

                                                              </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

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

        <!-- CUSTOM JS START -->
        <script>
             $(document).ready(function () {     
                var Plant         = $('#plant').val();
                var MaterialGroup = $('#mat_group').val();

                get_involved_persons(Plant,MaterialGroup); 
            });

            function get_involved_persons(Plant,MaterialGroup)
            {
                var po_creator_id = $('#po_creator_id').val();

                $.ajax({
                    url: "common_ajax.php",
                    type: "POST",
                    data: {
                        Plant : Plant,
                        MaterialGroup : MaterialGroup,
                        Action : 'get_involved_persons',
                        po_creator_id : po_creator_id
                    },
                    cache: false,
                    dataType: 'json',                        
                    beforeSend:function(){
                        $('#ajax-preloader').show();
                    },
                    success: function (result) {
                        var table_data = '';
                        if(result.length > 0) {
                            // <td>${ result[0].verifier_name }</td>
                            $('.inv_fin_appr').hide();

                            table_data = `<tr>
                            <td>${ (result[0].purchaser_name != '' && result[0].purchaser_name != null) ? result[0].purchaser_name : '-' }</td>
                            <td>${ (result[0].recommendor_name != '' && result[0].recommendor_name != null) ? result[0].recommendor_name : '-' }</td>
                            <td>${ (result[0].approver_name != '' && result[0].approver_name != null) ? result[0].approver_name : '-' }</td>`; 

                            if(result[0].approver2_name != null) {
                                table_data += `<td>${ (result[0].approver2_name != null) ? result[0].approver2_name : '-' }</td>`;
                                $('.inv_fin_appr').show();
                            }
                            table_data += `</tr>`; 


                        }
                        $('#involved_persons_tbody').html(table_data);  
                        $('#involved_persons_div').show();                                  
                    },
                    complete:function(){
                        $('#ajax-preloader').hide();
                    }
                });
            }



            $(document).on('click', '.viewModalBtn', function (e) {
                e.preventDefault();

                var rowId = $(this).data('btn');
                console.log(rowId)
                var newDateInputId = 'emodal_dateofpurchase' + rowId;
                $('.emodal_dateofpurchase').attr('id', newDateInputId);

                var newQtyInputId = 'emodal_qty' + rowId;
                $('.emodal_qty').attr('id', newQtyInputId);

                var newRemarkInputId = 'emodal_remarks' + rowId;
                $('.emodal_remarks').attr('id', newRemarkInputId);

                var newCostInputId = 'emodal_cost' + rowId;
                $('.emodal_cost').attr('id', newCostInputId);

                var newRowInputId = 'erow_id' + rowId;
                $('.erow_id').attr('id', newRowInputId);

                 // var newBtnInputId = 'erow_id' + rowId;
                 $('.updateReplacement').attr('data-btnupdate', rowId);


                var rdateofpurchase = $('#rdateofpurchase'+ rowId).val();
                // var rqty = $('#rqty'+ rowId).val();

                var appendQty1 = $('#rqty'+rowId).val();

                var rremarks = $('#rremarks'+ rowId).val();
                var rcost = $('#rcost'+ rowId).val();


                $('#erow_id'+ rowId).val(rowId);
                $('#emodal_dateofpurchase'+ rowId).val(rdateofpurchase);
                $('#emodal_remarks'+ rowId).val(rremarks);
                $('#emodal_cost'+ rowId).val(rcost);

                // if(appendQty1!='')
                // {
                    $('#emodal_qty' + rowId).val(appendQty1);
                // }
                // else
                // {
                //     $('#emodal_qty' + rowId).val(rqty);
                // }


                $('#viewModal').modal('show');
            });
            
        </script>
        <!-- CUSTOM JS END -->
    </body>
</html>
