<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$id_user_filter = mysqli_real_escape_string($conn, $_GET['id_user']);
$id_user = "";
if (isset($_GET['id_user'])) {

    $id_user = $_GET['id_user'];
}
$valid = 1;

if (isset($_POST['finish'])) {
    $valid = 1;
    $id = $id_user_filter;
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $pass = $_POST['pass'];
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $grup = mysqli_real_escape_string($conn, $_POST['role_user']);
    $jenis_pajak = mysqli_real_escape_string($conn, $_POST['jenis_pajak']);


    if ($id == "new") {



        $query_add_user = "INSERT INTO tb_karyawan(
            id_karyawan,
            user_id,
            pass,
            nama_lengkap,
            alamat,
            no_telp,
            grup,
            jenis_pajak
            ) VALUES(
                '',
                '" . $user_id . "',
                '" . mysqli_real_escape_string($conn, md5($pass)) . "',
                '" . $nama_lengkap . "',
                '" . $alamat . "',
                '" . $no_telp . "',
                '" . $grup . "',
                '" . $jenis_pajak . "'
            )";
        if ($sql_add_user = mysqli_query($conn, $query_add_user)) {
            $active = "";
            $query_get_m_menu = "SELECT * FROM m_menu";
            $sql_get_m_menu = mysqli_query($conn, $query_get_m_menu);
            while ($row_m_menu = mysqli_fetch_array($sql_get_m_menu)) {
                $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
                $menu_id = mysqli_real_escape_string($conn, $row_m_menu['menu_id']);
                $query_add_akses = mysqli_query($conn, "INSERT INTO m_akses ( 
                                                                        id_user,
                                                                        id_menu,
                                                                        status
                                                                        ) Values (
                                                                        '" . $user_id . "',
                                                                        '" . $menu_id . "',
                                                                        '0'
                                                                        ) 
                                                                            ");
            }

            foreach ($_POST['kode_akses'] as $active) {
                $query_set_akses = mysqli_query($conn, " UPDATE m_akses SET status = '1' where id_menu = '" . $active . "' and id_user = '" . $user_id . "' ");
            }

            $query_get_m_menu_2 = "SELECT * FROM m_menu_2";
            $sql_get_m_menu_2 = mysqli_query($conn, $query_get_m_menu_2);
            while ($row_m_menu_2 = mysqli_fetch_array($sql_get_m_menu_2)) {
                $user_id_2 = mysqli_real_escape_string($conn, $_POST['user_id']);
                $menu_id_2 = mysqli_real_escape_string($conn, $row_m_menu_2['menu_id']);
                $query_add_akses = mysqli_query($conn, "INSERT INTO m_akses_2 ( 
                                                                        id_user,
                                                                        id_menu,
                                                                        status
                                                                        ) Values (
                                                                        '" . $user_id_2 . "',
                                                                        '" . $menu_id_2 . "',
                                                                        '0'
                                                                        ) 
                                                                            ");
            }

            foreach ($_POST['kode_akses_2'] as $active) {
                $query_set_akses_2 = mysqli_query($conn, " UPDATE m_akses_2 SET status = '1' where id_menu = '" . $active . "' and id_user = '" . $user_id . "' ");
            }
            $valid = 1;
            $msg = "Tambah Data Karyawan : Berhasil !";
        } else {
            $valid = 0;
            $msg = "Tambah Data Karyawan : Gagal !";
        }
    } else {
        // Edit Branch Process
        $pas_user = "";
        if ($pass !== "") {
            $pas_user = "pass = '" . mysqli_real_escape_string($conn, md5($pass)) . "',";
        }
        $query_set_user = "  UPDATE tb_karyawan 
                                    SET nama_lengkap = '" . $nama_lengkap . "', 
                                        " . $pas_user . "
                                        alamat = '" . $alamat . "',
                                        no_telp = '" . $no_telp . "',
                                        grup = '" . $grup . "',
                                        jenis_pajak = '" . $jenis_pajak . "'
                                    WHERE id_karyawan = '" . $id . "'";
        if ($sql_set_user = mysqli_query($conn, $query_set_user)) {
            $query_update = mysqli_query($conn, "DELETE FROM m_akses WHERE id_user = '" . $user_id . "' ");
            $query_update_2 = mysqli_query($conn, "DELETE FROM m_akses_2 WHERE id_user = '" . $user_id . "' ");

            $query_get_m_menu = "SELECT * FROM m_menu";
            $sql_get_m_menu = mysqli_query($conn, $query_get_m_menu);
            while ($row_m_menu = mysqli_fetch_array($sql_get_m_menu)) {
                $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
                $menu_id = mysqli_real_escape_string($conn, $row_m_menu['menu_id']);
                $query_add_akses = mysqli_query($conn, "INSERT INTO m_akses ( 
                                                                        id_user,
                                                                        id_menu,
                                                                        status
                                                                        ) Values (
                                                                        '" . $user_id . "',
                                                                        '" . $menu_id . "',
                                                                        '0'
                                                                        ) 
                                                                            ");
            }

            foreach ($_POST['kode_akses'] as $active) {
                $query_set_akses = mysqli_query($conn, " UPDATE m_akses SET status = '1' where id_menu = '" . $active . "' and id_user = '" . $user_id . "' ");
            }

            $query_get_m_menu_2 = "SELECT * FROM m_menu_2";
            $sql_get_m_menu_2 = mysqli_query($conn, $query_get_m_menu_2);
            while ($row_m_menu_2 = mysqli_fetch_array($sql_get_m_menu_2)) {
                $user_id_2 = mysqli_real_escape_string($conn, $_POST['user_id']);
                $menu_id_2 = mysqli_real_escape_string($conn, $row_m_menu_2['menu_id']);
                $query_add_akses = mysqli_query($conn, "INSERT INTO m_akses_2 ( 
                                                                        id_user,
                                                                        id_menu,
                                                                        status
                                                                        ) Values (
                                                                        '" . $user_id_2 . "',
                                                                        '" . $menu_id_2 . "',
                                                                        '0'
                                                                        ) 
                                                                            ");
            }

            foreach ($_POST['kode_akses_2'] as $active) {
                $query_set_akses_2 = mysqli_query($conn, " UPDATE m_akses_2 SET status = '1' where id_menu = '" . $active . "' and id_user = '" . $user_id . "' ");
            }
            $valid = 1;
            $msg = "Edit Data User : Success !";
        } else {
            $valid = 0;
            $msg = "Edit Data User : Failed !";
        }
        // End Edit Branch Process
    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $page = "master_karyawan.php";
        $sec = "0.5";
        $redirect = 1;
        header("Refresh: $sec; url=$page");
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}




$id_user = "";
$nm_user = "";
$group = "";
$id_branch = "";
$pass_user = "New";
$email_user = "";
$user_type = "";
$readonly = "";
$required = "required";
$display = "hidden";
$display_view = "hidden";
$sop_checker = "";

$user_id = "";
$nama_lengkap = "";
$alamat = "";
$no_telp = "";
$divisi = "";
$role_user = "";
$cabang = "";


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

    <!-- select2 CSS -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="logo_inventory-removebg-preview (1).png">
    <link rel="icon" href="logo_inventory-removebg-preview (1).png" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
    <script src="vendors/jquery/dist/jquery.min.js"></script>
    <script>
        function cabang() {
            var id_user = "<?= $id_user_filter; ?>";

            if (id_user !== "new") {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_cabang_karyawan.php",
                    data: {
                        "id_user": id_user
                    },
                    cache: true,
                    success: function(response) {
                        if (response == 1) {
                            $(".cabang_franchise_list").show(500);
                        }
                        if (response == 2) {
                            $(".cabang_list").show(500);
                        }
                    }
                });
            }
        }
    </script>
</head>

<body onload="cabang();">


    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

        <?php include "header.php"; ?>

        <!-- Main Content -->
        <div class="hk-pg-wrapper">
            <!-- Container -->
            <div class="container-fluid mt-xl-50 mt-sm-30 mt-15">
                <!-- Row -->
                <div class="row">
                    <div class="col">
                        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" enctype="multipart/form-data">
                            <section class="hk-sec-wrapper">
                                <h5 class="hk-sec-title">Master User Login</h5>
                                <div class="row">
                                    <div class="col-sm">
                                        <?php
                                        $marketing = '';
                                        $fill = '';

                                        $checked_sop = "";
                                        $jenis_pajak = "";
                                        $query_get_user = "SELECT * FROM tb_karyawan WHERE id_karyawan ='" . $id_user_filter . "' ";
                                        $sql_get_user = mysqli_query($conn, $query_get_user);
                                        if ($row_user = mysqli_fetch_array($sql_get_user)) {

                                            $user_id = $row_user['user_id'];
                                            $nama_lengkap = $row_user['nama_lengkap'];
                                            $alamat = $row_user['alamat'];
                                            $no_telp = $row_user['no_telp'];
                                            $role_user = $row_user['grup'];
                                            $jenis_pajak = $row_user['jenis_pajak'];


                                            $pass_user = "Change";
                                            $readonly = "readonly";
                                            $required = "";
                                            $fill = 'filled-input';


                                            // if ($user_type == "Franchise") {
                                            //     $display = "";
                                            //     $display_view = "hidden";
                                            // }

                                            // if ($user_type == "Supervisor" || $user_type == "Admin") {
                                            //     $display = "hidden";
                                            //     $display_view = "";
                                            // }
                                        }
                                        echo '
                                        <div class="row">
                                            <div class="col-md-6">
                                            <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Username </span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="user_id"
                                                            id="id_user" class="form-control ' . $fill . ' form-control-sm"
                                                            value="' . $user_id . '" autocomplete="off" required  ' . $readonly . '>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">' . $pass_user . ' Password</span>
                                                        </div>
                                                        <input autocomplete="off" type="password" name="pass"
                                                            id="pass_user" class="form-control form-control-sm"
                                                            autocomplete="off" value="" ' . $required . '>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Nama Lengkap</span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="nama_lengkap"
                                                            id="nm_user" class="form-control form-control-sm"
                                                            value="' . $nama_lengkap . '" autocomplete="off" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Alamat</span>
                                                        </div>
                                                        <textarea name="alamat" id="" cols="30" rows="3" class="form-control form-control-sm" autocomplete="off" >' . $alamat . '</textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">No. Telp</span>
                                                        </div>
                                                        <input type="number" name="no_telp" id="" class="form-control form-control-sm" min="0" autocomplete="off" value="' . $no_telp . '" >
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Role User</span>
                                                         </div>
                                                        <select name="role_user" id="" class="form-control form-control-sm role_user">
                                                            <option value="super" ';
                                        if ($role_user == "super") {
                                            echo "selected";
                                        }
                                        echo '>Admin</option>
                                                            <option value="manajer" ';
                                        if ($role_user == "manajer") {
                                            echo "selected";
                                        }
                                        echo '>Manager</option>
                                                            <option value="owner" ';
                                        if ($role_user == "owner") {
                                            echo "selected";
                                        }
                                        echo '>Owner</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Admin Pajak</span>
                                                        </div>
                                                        <select name="jenis_pajak" id="" class="form-control form-control-sm jenis_pajak" required>
                                                            <option value="">-- Pajak / Non Pajak --</option>
                                                            <option value="1" ';
                                        if ($jenis_pajak == "1") {
                                            echo "selected";
                                        }
                                        echo '>Pajak</option>
                                                            <option value="2" ';
                                        if ($jenis_pajak == "2") {
                                            echo "selected";
                                        }
                                        echo '>Non Pajak</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:190px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">--- Role Pekerjaan ---</span>
                                                        </div>
                                                        <div class="d-none custom-control custom-checkbox ml-2 mt-1">
                                                            <input type="checkbox"
                                                                class="select-all custom-control-input"
                                                                id="pilih-semua">
                                                            <label class="custom-control-label" for="pilih-semua">Pilih
                                                                Semua</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <ul class="list-group mb-15">
                                                    ';

                                        $query_get_parent = "SELECT menu_id,
                                                                                nm_menu
                                                                        FROM m_menu_2
                                                                        WHERE parent = '0'
                                                                        AND status = 'Y'
                                                                        order by no_urut asc";
                                        $sql_get_parent = mysqli_query($conn, $query_get_parent);
                                        while ($row_parent = mysqli_fetch_array($sql_get_parent)) {
                                            $id_parent = $row_parent['menu_id'];
                                            $nm_parent = $row_parent['nm_menu'];
                                            $checked = "";
                                            $visible = 'style="display:none;"';
                                            $query_get_akses = mysqli_query($conn, "SELECT status FROM m_akses_2 where id_menu ='" . $id_parent . "' and id_user = '" . $user_id . "'");
                                            if ($row_get_akses = mysqli_fetch_array($query_get_akses)) {

                                                if ($row_get_akses['status'] == "1") {
                                                    $checked = "checked";
                                                    $visible = "";
                                                }
                                            }
                                            echo '
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="parent parent' . $id_parent . ' custom-control-input"
                                                                name="kode_akses_2[]" data-parent="' . $id_parent . '"
                                                                id="' . $nm_parent . '" data-id="' . $id_parent . '"
                                                                value="' . $id_parent . '" ' . $checked . '>
                                                            <label class="custom-control-label"
                                                                for="' . $nm_parent . '"><strong>' . $nm_parent . '</strong></label>
                                                        </div>
                                                    </li>
                                                    ';
                                        }
                                        echo '
                                                </ul>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:190px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">--- Choose Access ---</span>
                                                        </div>
                                                        <div class="custom-control custom-checkbox ml-2 mt-1">
                                                            <input type="checkbox"
                                                                class="select-all custom-control-input"
                                                                id="pilih-semua">
                                                            <label class="custom-control-label" for="pilih-semua">Pilih
                                                                Semua</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">

                                                </div>
                                                <ul class="list-group mb-15">
                                                    ';

                                        $query_get_parent = "SELECT menu_id,
                                                                                nm_menu
                                                                        FROM m_menu
                                                                        WHERE parent = '0'
                                                                        AND status = 'Y'
                                                                        order by no_urut asc";
                                        $sql_get_parent = mysqli_query($conn, $query_get_parent);
                                        while ($row_parent = mysqli_fetch_array($sql_get_parent)) {
                                            $id_parent = $row_parent['menu_id'];
                                            $nm_parent = $row_parent['nm_menu'];
                                            $checked = "";
                                            $visible = 'style="display:none;"';
                                            $query_get_akses = mysqli_query($conn, "SELECT status FROM m_akses where id_menu ='" . $id_parent . "' and id_user = '" . $user_id . "'");
                                            if ($row_get_akses = mysqli_fetch_array($query_get_akses)) {

                                                if ($row_get_akses['status'] == "1") {
                                                    $checked = "checked";
                                                    $visible = "";
                                                }
                                            }


                                            echo '
                                                    <li
                                                        class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="parent parent' . $id_parent . ' custom-control-input"
                                                                name="kode_akses[]" data-parent="' . $id_parent . '"
                                                                id="' . $nm_parent . '" data-id="' . $id_parent . '"
                                                                value="' . $id_parent . '" ' . $checked . '>
                                                            <label class="custom-control-label"
                                                                for="' . $nm_parent . '"><strong>' . $nm_parent . '</strong></label>
                                                        </div>
                                                    </li>
                                                    <div id="' . $id_parent . '" ' . $visible . '>
                                                        ';
                                            $query_get_menu = "SELECT menu_id,parent,nm_menu,link,status FROM m_menu WHERE parent = '" . $id_parent . "' AND status = 'Y' order by no_urut asc";
                                            $sql_get_menu = mysqli_query($conn, $query_get_menu);
                                            while ($row_menu = mysqli_fetch_array($sql_get_menu)) {
                                                $id_menu = $row_menu['menu_id'];
                                                $nm_menu = $row_menu['nm_menu'];
                                                $link_menu = $row_menu['link'];
                                                $checked = "";
                                                $visible_sub = 'style="display:none;"';
                                                $query_get_akses = mysqli_query($conn, "SELECT status FROM m_akses where id_menu ='" . $id_menu . "' and id_user = '" . $user_id . "' ");
                                                if ($row_get_akses = mysqli_fetch_array($query_get_akses)) {

                                                    if ($row_get_akses['status'] == "1") {
                                                        $checked = "checked";
                                                        $visible_sub = "";
                                                    }
                                                }

                                                echo '
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div class="custom-control custom-checkbox">
                                                                    <input type="checkbox"
                                                                        class="custom-control-input child' . $id_parent . '"
                                                                        name="kode_akses[]" id="' . $id_menu . '_1"
                                                                    value="' . $id_menu . '" ' . $checked . '>
                                                                       <label class="custom-control-label"
                                                                        for="' . $id_menu . '_1">' . $nm_menu . '</label>
                                                                </div>
                                                            </li>
                                                            ';
                                            }
                                            echo '
                                                    </div>
                                                    ';
                                        }
                                        echo '
                                                </ul>
                                            </div>
                                        </div>
                                        ';
                                        ?>
                                    </div>
                                </div>
                            </section>
                    </div>
                </div>
                <!-- /Row -->

                <div class="row justify-content-end">
                    <div class="col-12 col-md-2">

                        <button type="submit" class="btn btn-success btn-sm btn-block" name="finish"> Save </button>
                    </div>
                    <div class="col-12 col-md-2">
                        <a href="master_karyawan.php" class="btn btn-sm btn-danger btn-block">Cancel</a>
                    </div>
                </div>
                </form>
            </div>
            <!-- /Container -->

            <!-- Footer -->
            <div class="hk-footer-wrap container-fluid">
            </div>
            <!-- /Footer -->
        </div>
        <!-- /Main Content -->

    </div>
    <!-- /HK Wrapper -->

    <!-- jQuery -->


    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Data Table JavaScript -->
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="vendors/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="vendors/jszip/dist/jszip.min.js"></script>
    <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="vendors/pdfmake/build/vfs_fonts.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="dist/js/dataTables-data.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="dist/js/feather.min.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Toggles JavaScript -->
    <script src="vendors/jquery-toggles/toggles.min.js"></script>
    <script src="dist/js/toggle-data.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>

    <!-- Select2 JavaScript -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="dist/js/select2-data.js"></script>

    <!-- Delete -->
    <script type="text/javascript">
        $(document).ready(function() {
            // console.log( "ready!" );
            // $(".franc_view").hide(500);
            // $(".visor_view").hide(500);
        });

        $(document).on("click", '.hapus_button', function(e) {
            var id_hapus = $(this).data('id_hapus');
            $(".id_hapus").val(id_hapus);
        });

        $(document).on("change", '.group_user', function(e) {
            var type = $(this).val();
            if (type == "super") {
                $(".franc_view").attr("hidden", true);
                $(".visor_view").attr("hidden", true);
            }
            if (type == "Franchise") {
                $(".franc_view").attr("hidden", false);
                $(".visor_view").attr("hidden", true);
            }
            if (type == "Supervisor" || type == "Admin") {
                $(".franc_view").attr("hidden", true);
                $(".visor_view").attr("hidden", false);
            }
        });

        $(document).on("click", '.parent', function(e) {

            var parent = $(this).data("parent");
            if ($(".parent" + parent).is(":checked")) {
                $("#" + parent).show(500);
            } else {
                $("#" + parent).hide(500);
                $(".child" + parent).prop("checked", false);
            }
        });

        // $(document).on("click", '.child', function (e) {
        //     var parent = $(this).data("parent");
        //     if ($(".child" + parent).is(":checked")) {
        //         $("#" + parent).show(500);
        //     } else {
        //         $("#" + parent).hide(500);
        //         $(".child_sub" + parent).prop("checked", false);
        //     }
        // });

        $('.select-all').click(function(event) {
            if (this.checked) {
                $(':checkbox').prop('checked', true);
            } else {
                $(':checkbox').prop('checked', false);
            }
        });

        $(document).on("change", ".role_user", function() {
            var role = $(".role_user").val();
            if (role == "super") {
                $(".cabang_list").hide(500);
                $(".cabang_franchise_list").hide(500);
            } else if (role == "franchise") {
                $(".cabang_list").hide(500);
                $(".cabang_franchise_list").show(500);
            } else {
                $(".cabang_list").show(500);
                $(".cabang_franchise_list").hide(500);
            }
        });
    </script>


</body>

</html>