<?php
include('../auto_load.php');
include('adition.php');

$emp_id = $_GET['emp_id'];

$user_credentials_sql  = "SELECT * from EMPLTABLE where EMPLID = '".$emp_id."'";
$user_credentials_exec = sqlsrv_query($conn,$user_credentials_sql);
$user_credentials_res  = sqlsrv_fetch_array($user_credentials_exec);

$password = $user_credentials_res['PASSWORD'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title><?php echo $Title ?></title>
    <link rel="icon" type="image/x-icon" href="../global/photos/favicon.ico"/>
    <link href="layouts/collapsible-menu/css/light/loader.css" rel="stylesheet" type="text/css" />
    <link href="layouts/collapsible-menu/css/dark/loader.css" rel="stylesheet" type="text/css" />
    <script src="layouts/collapsible-menu/loader.js"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="src/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="layouts/collapsible-menu/css/light/plugins.css" rel="stylesheet" type="text/css" />
    <link href="src/assets/css/light/authentication/auth-cover.css" rel="stylesheet" type="text/css" />
    
    <link href="layouts/collapsible-menu/css/dark/plugins.css" rel="stylesheet" type="text/css" />
    <link href="src/assets/css/dark/authentication/auth-cover.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
</head>
<style>
    #ajax-preloader {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ffffffa6;
        z-index: 9999;
    }
    #ajax-loader {
        width: 40px;
        height: 40px;
        position: absolute;
        left: 50%;
        top: 50%;
        margin: -20px 0 0 -20px;
    }
    @media only screen and (max-width: 600px) {
        #ajax-preloader {
            left: -100px !important;
        }
    }
</style>
<body class="form">

       <!-- Loader -->

    <div id="ajax-preloader" style="display: none;">
        <div id="ajax-loader">
            <img src="assets/images/purchase_cart.gif">
        </div>
    </div>

    <input type="hidden" id="emp_id" value="<?php echo $emp_id; ?>">
    <input type="hidden" id="password" value="<?php echo $password; ?>">    

    <div class="auth-container d-flex">

        <div class="container mx-auto align-self-center">
    
            <div class="row">
                
                <div class="col-6 d-lg-flex d-none h-100 my-auto top-0 start-0 text-center justify-content-center flex-column">
                    <div class="auth-cover-bg-image"></div>
                    <div class="auth-overlay"></div>
                        
                    <div class="auth-cover">
    
                        <div class="position-relative">
    
                            <img src="src/assets/img/auth-cover.svg" alt="auth-img">
    
                            <!-- <h2 class="mt-5 text-white font-weight-bolder px-2">Join the community of expert developers</h2> -->
                            <!-- <p class="text-white px-2">It is easy to setup with great customer experience. Start your 7-day free trial</p> -->
                        </div>
                        
                    </div>

                </div>

                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center ms-lg-auto me-lg-0 mx-auto">
                    <div class="card">
                        <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <img src="logo.png" alt="auth-img">
                                        <h2>Corporate Portal</h2>
                                        <p>Sign in with your credentials</p>
                                    </div>
                                    <form method="POST">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Employee Id</label>
                                                <input id="username" name="EMPLID" type="text" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="passw" class="form-label">Password</label>
                                                <input id="passw" name="PASSWORD" type="password" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-4">
                                                <button type="submit" name="login" class="btn btn-secondary w-100">SIGN IN</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>

    </div>
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->

    <script type="text/javascript">
        $(document).ready(function(){
            let emp_id   = $('#emp_id').val();
            let password = $('#password').val();

            if(emp_id != '' && password != '') {

                let data     = { Action:'Login',emp_id : emp_id,password : password };   

                $.ajax({
                    url: '../pages/Ajax1.php',
                    type: 'POST',
                    dataType:'json',
                    data: data,
                    beforeSend:function(){
                      $('#ajax-preloader').show();
                    },
                    success:function(res){
                      if(res.status=='ok'){
                        // setTimeout(function(){
                            window.location.href = 'dashboard.php';
                        // },1000);
                      }else if(res.status=='unauthorize'){

                      }else{

                     }
                   },
                   complete:function(){
                      $('#ajax-preloader').hide();  
                   }
                 });

            }

        });
    </script>

</body>
</html>