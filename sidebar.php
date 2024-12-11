<div class="vertical-menu">

            <div data-simplebar class="h-100">

            <?php
                                    $Employee_Id = $_SESSION['EmpID'];
                                    $selector = sqlsrv_query($conn, "SELECT  * FROM HR_Master_Table
                                    WHERE Employee_Code = '$Employee_Id' ");
                                    $selector_arr = sqlsrv_fetch_array($selector);
                                ?>
                <div class="user-sidebar text-center">
                    <div class="dropdown">
                        <div class="user-img">
                            <img src="assets/images/users/avatar-7.jpg" alt="" class="rounded-circle">
                            <span class="avatar-online bg-success"></span>
                        </div>
                        <div class="user-info">
                            <h5 class="mt-3 font-size-16 text-white"><?php echo $selector_arr['Employee_Name'] ?></h5>
                            <span class="font-size-13 text-white-50"><?php echo $selector_arr['Department'] ?></span>
                        </div>
                    </div>
                </div>



                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title">Menu</li>

                        <li>
                            <a href="dashboard.php" class="waves-effect">
                                <i class="dripicons-home"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                    <?php  //if($_SESSION['Dcode']=='ADMIN' || $_SESSION['Dcode']=='SUPERADMIN') {
                        if($_SESSION['EmpID']=='RS3015' || $_SESSION['EmpID']=='RS4310' || $_SESSION['EmpID']=='RS7361') {

                        ?>
                        <li>
                            <a href="show_master.php" class="waves-effect">
                                <i class="dripicons-gear"></i>
                                <span>Mater Role Mapping</span>
                            </a>
                        </li>
                    <?php } ?>

                        <?php 
                        $sql1 = "SELECT * FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."' AND Status = 'Active'  ";
                        $params1 = array();
                        $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                        $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );

                        $row_count1 = sqlsrv_num_rows( $stmt1 );
                        $purchase = false;
                        if ($row_count1 > 0) {
                            $purchase = true;
                        }
                        if($purchase) {?>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="dripicons-message"></i>
                                <span>Request Selection</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
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
                                <li><a href="show_payment_request.php">Payment (FP)</a></li>
                            <?php }?>
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
                                <li><a href="show_rateterms_request.php">Rate and terms (FR)</a></li>
                            <?php }?>
                            <?php 
                            $sql1 = "SELECT * FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Operational requests (OP)' AND Status = 'Active' ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );

                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                                <li><a href="show_oprational_request.php">Operational (OP)</a></li>
                            <?php }?>
                            <?php 
                            $sql1 = "SELECT * FROM Tb_Master_Emp WHERE PO_creator_Release_Codes = '".$_SESSION['EmpID']."' AND Purchase_Type = 'Purchase & Vendor selection (R&VS)' AND Status = 'Active' ";
                            $params1 = array();
                            $options1 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                            $stmt1 = sqlsrv_query( $conn, $sql1 , $params1, $options1 );

                            $row_count1 = sqlsrv_num_rows( $stmt1 );
                            $purchase = false;
                            if ($row_count1 > 0) {
                                $purchase = true;
                            }
                            if($purchase) {?>
                                <li><a href="show_purchacevendor_request.php">Purchase (R&VS)</a></li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php }?>
                        
                        <!-- Finance  -->

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
                        <li>
                            <a href="show_verifier.php" class=" waves-effect">
                                <i class="dripicons-document-edit"></i>
                                <span>Verifier Section</span>
                            </a>
                        </li>
                        <?php }?>
                        
                        <!-- Purchase -->

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
                        <li>
                            <a href="show_vendor_request.php" class=" waves-effect">
                                <i class="dripicons-user-group"></i>
                                <span>Vendor Section</span>
                            </a>
                        </li>
                        <?php }?>

                        <!-- Recommender  -->

                        <?php 
                        $sql4 = "SELECT * FROM Tb_Master_Emp WHERE Recommender = '".$_SESSION['EmpID']."' AND Status = 'Active' ";
                        $params4 = array();
                        $options4 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                        $stmt4 = sqlsrv_query( $conn, $sql4 , $params4, $options4 );

                        $row_count4 = sqlsrv_num_rows( $stmt4 );
                        $recommender = false;
                        if ($row_count4 > 0) {
                            $recommender = true;
                        }
                        if($recommender) {?>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="dripicons-tags"></i>
                                <span>Recommender Selection</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
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
                                <li><a href="show_payment_recommender.php">Payment (FP)</a></li>
                            <?php }?>
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
                                <li><a href="show_rateterms_recommender.php">Rate&Terms (FR)</a></li>
                            <?php }?>
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
                                <li><a href="show_operational_recommender.php">Operational (OP)</a></li>
                            <?php }?>
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
                                <li><a href="show_recommender.php">Purchase (R&VS)</a></li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php }?>

                        <!-- Approver -->

                        <?php 
                        $sql5 = "SELECT * FROM Tb_Master_Emp WHERE Approver = '".$_SESSION['EmpID']."' AND Status = 'Active'  ";
                        $params5 = array();
                        $options5 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                        $stmt5 = sqlsrv_query( $conn, $sql5 , $params5, $options5 );

                        $row_count5 = sqlsrv_num_rows( $stmt5 );
                        $approver = false;
                        if ($row_count5 > 0) {
                            $approver = true;
                        }
                        if($approver) {?>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="dripicons-bookmarks"></i>
                                <span>Approver Selection</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
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
                                <li><a href="show_payment_approver.php">Payment (FP)</a></li>
                            <?php }?>
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
                                <li><a href="show_rateterms_approver.php">Rate&Terms (FR)</a></li>
                            <?php }?>
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
                                <li><a href="show_operational_approver.php">Operational (OP)</a></li>
                            <?php }?>
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
                                <li><a href="show_approver.php">Purchase (R&VS)</a></li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php }?>

                        <!-- INFORMER DETAILS -->

                        <?php 
                         $sql2 = "SELECT * FROM Tb_Payment_Request WHERE Persion_In_Workflow like '%".$_SESSION['EmpID']."%'";
                         $params2 = array();
                         $options2 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                         $stmt2 = sqlsrv_query( $conn, $sql2 , $params2, $options2 );
 
                         $row_count2 = sqlsrv_num_rows( $stmt2 );
                         $payment_vendor = false;
                         if ($row_count2 > 0) {
                             $payment_vendor = true;
                         }
 
                         $sql2 = "SELECT * FROM Tb_RateTrems_Request WHERE Persion_In_Workflow like '%".$_SESSION['EmpID']."%'";
                         $params2 = array();
                         $options2 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                         $stmt2 = sqlsrv_query( $conn, $sql2 , $params2, $options2 );
 
                         $row_count2 = sqlsrv_num_rows( $stmt2 );
                         $terms_vendor = false;
                         if ($row_count2 > 0) {
                             $terms_vendor = true;
                         }

                         $sql2 = "SELECT * FROM Tb_Oprational_Request WHERE Persion_In_Workflow like '%".$_SESSION['EmpID']."%'";
                         $params2 = array();
                         $options2 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                         $stmt2 = sqlsrv_query( $conn, $sql2 , $params2, $options2 );
 
                         $row_count2 = sqlsrv_num_rows( $stmt2 );
                         $operational_vendor = false;
                         if ($row_count2 > 0) {
                             $operational_vendor = true;
                         }

                         $sql2 = "SELECT * FROM Tb_Request WHERE Persion_In_Workflow like '%".$_SESSION['EmpID']."%'";
                         $params2 = array();
                         $options2 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                         $stmt2 = sqlsrv_query( $conn, $sql2 , $params2, $options2 );
 
                         $row_count2 = sqlsrv_num_rows( $stmt2 );
                         $purchase_vendor = false;
                         if ($row_count2 > 0) {
                             $purchase_vendor = true;
                         }
                         if($payment_vendor || $terms_vendor || $operational_vendor || $purchase_vendor) {?>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="dripicons-checklist"></i>
                                <span>Informer Selection</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                            <?php 
                            
                            if($payment_vendor) {?>
                                <li><a href="show_informer_payment_request.php">Payment Details (FP)</a></li>
                            <?php }?>
                            <?php 
                            
                            if($terms_vendor) {?>
                                <li><a href="show_informer_rate_request.php">Rate Details (FR)</a></li>
                            <?php }?>
                            <?php 
                            
                            if($operational_vendor) {?>
                                <li><a href="show_informer_operational_request.php">Operational Details (OP)</a></li>
                            <?php }?>
                            <?php 
                            
                            if($purchase_vendor) {?>
                                <li><a href="show_informer_purchase_request.php">Purchase Details (R&VS)</a></li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php }?>

                        <!-- Reference -->

                        <?php 
                         $sql2 = "SELECT * FROM Tb_Approver WHERE Reference_Empid like '%".$_SESSION['EmpID']."%'";
                         $params2 = array();
                         $options2 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                         $stmt2 = sqlsrv_query( $conn, $sql2 , $params2, $options2 );
 
                         $row_count2 = sqlsrv_num_rows( $stmt2 );
                         $purchase_vendor = false;
                         if ($row_count2 > 0) {
                             $purchase_vendor = true;
                         }
                         if($purchase_vendor) {?>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="dripicons-checklist"></i>
                                <span>Reference Selection</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                            
                            <?php 
                            
                            if($purchase_vendor) {?>
                                <li><a href="show_reference_purchase_request.php">Purchase Details (R&VS)</a></li>
                                <?php }?>
                            </ul>
                        </li>
                        <?php }?>


                         <!-- Limit exceed approval  -->

                        <?php 
                        $sql3 = "SELECT * FROM Tb_Master_Emp WHERE Approver_2 = '".$_SESSION['EmpID']."' AND Status = 'Active'";
                        $params3 = array();
                        $options3 =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
                        $stmt3 = sqlsrv_query( $conn, $sql3 , $params3, $options3 );

                        $row_count3 = sqlsrv_num_rows( $stmt3 );
                        $limit_exceed_approver = false;
                        if ($row_count3 > 0) {
                            $limit_exceed_approver = true;
                        }
                        if($limit_exceed_approver) {?>
                        <li>
                            <a href="show_exceed_approver.php" class="waves-effect">
                                <i class="dripicons-wallet"></i>
                                <span>Limit Exceed Approval</span>
                            </a>
                        </li>
                        <?php }?>


                        <li>
                            <a href="Purchase_report_Details.php" class="waves-effect">
                                <i class="dripicons-wallet"></i>
                                <span>Purchase Report</span>
                            </a>
                        </li>


                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>