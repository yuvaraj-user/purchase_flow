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
</style>
    <div id="ajax-preloader" style="display: none;">
        <div id="ajax-loader">
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

<header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">

                       <!-- LOGO -->
                 <div class="navbar-brand-box">
                    <a href="#" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="logo.png" alt="" height="15">
                        </span>
                        <span class="logo-lg">
                            <img src="logo.png" alt="" height="50">
                        </span>
                    </a>

                    <a href="#" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="assets/images/logo-sm.png" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="assets/images/logo-light.png" alt="" height="20">
                        </span>
                    </a>
                </div>

                    <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                        <i class="mdi mdi-menu"></i>
                    </button>
            
                </div>

                 <!-- Search input -->

                <div class="d-flex">
     
                    <div class="dropdown d-none d-lg-inline-block ms-1">
                        <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                            <i class="mdi mdi-fullscreen"></i>
                        </button>
                    </div>
                    <div class="dropdown d-inline-block">
                    <a class="dropdown-item text-danger" style="padding: 21px 11px 0px 11px;" href="../logout.php"><i class="mdi mdi-power font-size-16 align-middle me-1 text-danger"></i> Logout</a>
                    </div>
            
                </div>
            </div>
        </header>