<?php
ob_start();
session_start();
include "lib/koneksi.php";

if (isset($_POST['login'])) {
    $flag = 0;
    $sql = "select * from tb_karyawan where user_id='" . mysqli_real_escape_string($conn, $_POST['username']) . "' and pass='" . mysqli_real_escape_string($conn, md5($_POST['pass'])) . "'  ";
    $data = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_array($data)) {
        $flag = 1;
        $_SESSION['id_user'] = $row['user_id'];
        $_SESSION['nm_user'] = $row['nama_lengkap'];
        $_SESSION['branch'] = $row['cabang'];
        $_SESSION['grup'] = $row['grup'];
        $_SESSION['group'] = $row['grup'];
        $_SESSION['jenis_pajak'] = $row['jenis_pajak'];
        // $_SESSION['sop'] = $row['sop_checker'];
        // $_SESSION['pass_user'] = mysqli_real_escape_string($conn, $_POST['pass']);
        //   $_SESSION['id_cabang']=$row['id_cabang'];

        header('Location:dashboard.php');
    }
    if ($flag == 0) {
        echo "<script type='text/javascript'>alert('Maaf, Login Gagal')</script>";
    }
}

?>

<!DOCTYPE html>
<!-- 
Template Name: Griffin - Responsive Bootstrap 4 Admin Dashboard Template
Author: Hencework
Support: support@hencework.com

License: You must have a valid license purchased only from templatemonster to legally use the template for your project.
-->
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Inventory System</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="logo_inventory-removebg-preview.png">
    <link rel="icon" href="logo_inventory-removebg-preview.png" type="image/x-icon">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
    <!-- Preloader -->
    <div class="preloader-it">
        <div class="loader-pendulums"></div>
    </div>
    <!-- /Preloader -->

    <!-- HK Wrapper -->
    <div class="hk-wrapper">

        <!-- Main Content -->
        <div class="hk-pg-wrapper hk-auth-wrapper">
            <header class="d-flex justify-content-between align-items-center">
                <a class="d-flex auth-brand" href="#">
                    <!--<img class="brand-img" width="100px" height="100px" src="dist/img/main-logo.png" alt="brand" />-->
                </a>
                <div class="btn-group btn-group-sm">
                    <a href="#" class="btn btn-outline-secondary">Help / Support</a>
                </div>
            </header>
            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
                <div class="row">
                    <div class="col-xl-5 pa-0">
                        <div id="owl_demo_1" class="owl-carousel dots-on-item owl-theme">
                            <div class="fadeOut item auth-cover-img overlay-wrap" style="background-image:url(inventory_img.jpeg);">
                                <div class="auth-cover-info py-xl-0 pt-100 pb-50">
                                    <div class="auth-cover-content text-center w-xxl-75 w-sm-90 w-xs-100">
                                        <h1 class="display-3 text-white mb-20"></h1>
                                        <p class="text-white"> </p>
                                    </div>
                                </div>
                                <div class="bg-overlay bg-trans-dark-50"></div>
                            </div>
                            <!-- <div class="fadeOut item auth-cover-img overlay-wrap" style="background-image:url(dist/img/produk-6.png);">
                                <div class="auth-cover-info py-xl-0 pt-100 pb-50">
                                    <div class="auth-cover-content text-center w-xxl-75 w-sm-90 w-xs-100">
                                        <h1 class="display-3 text-white mb-20"></h1>
                                    </div>
                                </div>
                                <div class="bg-overlay bg-trans-dark-50"></div>
                            </div> -->
                        </div>
                    </div>
                    <div class="col-xl-7 pa-0">
                        <div class="auth-form-wrap py-xl-0 py-50">
                            <div class="auth-form w-xxl-55 w-xl-75 w-sm-90 w-xs-100">
                                <form class="user" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                                    <img class="brand-img d-inline-block" src="logo_inventory-removebg-preview (1).png" alt="brand" />
                                    <br><br>
                                    <p class="mb-30">Masuk ke akun anda </p>
                                    <div class="form-group">
                                        <input class="form-control" type="text" name="username" placeholder="Username" type="id_user">
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input class="form-control" id="pass_user" type="password" name="pass" placeholder="Password" type="password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <a href="javascript:void(0);" class="text-dark btn-hide">Show Password</a>
                                    </div>

                                    <input type="submit" class="btn btn-success btn-block" name="login" value="Login">

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Main Content -->

    </div>
    <!-- /HK Wrapper -->

    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Owl JavaScript -->
    <script src="vendors/owl.carousel/dist/owl.carousel.min.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="dist/js/feather.min.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>
    <script src="dist/js/login-data.js"></script>
    <script>
        $(".btn-hide").click(function() {

            const password = document.querySelector('#pass_user');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            if (type == "password") {
                $(".btn-hide").html("Show Password");
            } else {
                $(".btn-hide").html("Hide Password");
            }
        });
    </script>
</body>

</html>