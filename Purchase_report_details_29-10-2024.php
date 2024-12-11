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
$Employee_Id = $_SESSION['EmpID'];

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
                                         <ol class="breadcrumb m-0">
                                             <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                                             <li class="breadcrumb-item active">Show Approver Purchase Request</li>
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
                                                        <span class="d-none d-sm-block">Report</span>    
                                                    </a>
                                                </li>
                                              
                                               
                                            </ul>
            
                                            <!-- Tab panes -->
                                            <div class="tab-content p-3 text-muted">
                                                <div class="tab-pane active" id="home" role="tabpanel">
                                                    <div class="table-responsive">
                                                        <table id="Purchase_report"
                                                            class="table table-striped table-bordered nowrap"
                                                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                                            <thead>
                                                                <tr>
                                                                <th>S/No</th>
                                                                <th>Request ID</th>
                                                                <th>Date Of request</th>
                                                                <th>Request Type</th>
                                                                <th>Category</th>
                                                                <th>Department</th>
                                                                <th>Vendor Code</th>
                                                                <th>Vendor Name</th>
                                                                <th>Vendor City</th>
                                                                <th>Material Code</th>
                                                                <th>Material Name</th>
                                                                <th>Material Price</th>
                                                                <th>Material Discount</th>
                                                                <th>GST Value</th>
                                                                <th>Net Amount</th>
                                                                <th>Status</th>

                                                               <!-- <th>Action</th> -->
                                                            </tr>
                                                            </thead>
                                                           
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
        <script>


        $(document).ready(function(){
    



     var user_input = {};
   
  

  
   Server_Side_Datatable("no",user_input);
   


  });


 function Server_Side_Datatable(destroy_status,user_input)
{

jQuery.fn.DataTable.Api.register( 'buttons.exportData()', function ( options ) {
                 if ( this.context.length ) {
                     var jsonResult = $.ajax({

                        "url": "Ajax_report.php", 
                         type:'POST',
                         dataType:'json',
                         "data": {Action:"Purchase_report",length:"All",user_input : user_input},
                         async: false,
                     });
      
                  
                     let headers=['S.No','Request Id','Date Of Request','Employee Name','Request Type','Request Category','Department','Vendor Code','Vendor Name','Vendor City','Material Code','Material Name','Price','Discount','GST','Net Amount','Approval Status'];

      
                     
                     return {
                       body: jsonResult.responseJSON.data, 
                       header: headers};
                 }
             } );
   var data_table='Purchase_report'
   if(destroy_status == "yes")
  {
    $('#'+data_table).DataTable().destroy();
  }
 $('#' + data_table).DataTable({

    "dom": 'Bfrtip',

    
 
    "scrollX": true,
    "buttons": ['copy', 'csv', 'excel', 'pdf', 'print'],
    "bprocessing": true,
    "serverSide": true,
    "pageLength": 10,
    "ajax": 
    {
      "url": "Ajax_report.php", 
      "type": "POST",
      "data": {Action:"Purchase_report",user_input : user_input}
    },

  
  });
}




        </script>
 

        <script src="assets/js/app.js"></script>

    </body>
</html>