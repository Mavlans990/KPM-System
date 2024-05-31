<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$id_user_filter = mysqli_real_escape_string($conn, $_GET['id_user']);
$valid = 1;

if (isset($_POST['finish'])) {
    $valid = 1;
    $id = $id_user_filter;
    $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nm_user = mysqli_real_escape_string($conn, $_POST['nm_user']);
    $pass_user = mysqli_real_escape_string($conn, $_POST['pass_user']);
    $email_user = mysqli_real_escape_string($conn, $_POST['email_user']);
    $id_branch = "pusat";
    $group = mysqli_real_escape_string($conn, $_POST['group_user']);
   

    if ($group == "Franchise") {
        $to = "";
        $no = 0;
        foreach ($_POST['branch'] as $selectedTo) {
            if ($no == 0) {
                $to = $selectedTo;
            } else {
                $to = $to . "#" . $selectedTo;
            }
            $no++;
        }
        $id_branch = mysqli_real_escape_string($conn, $to);
    }

    if ($group == "Supervisor" || $group == "Admin") {
        $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);
    }

    if ($id == "new") {
        $query_add_user = "INSERT INTO m_user ( id_user,
                                                        nm_user,
                                                        pass_user,
                                                        email_user,
                                                        id_branch,
                                                        group_user
                                                        ) VALUES (
                                                        '" . $id_user . "',
                                                        '" . $nm_user . "',
                                                        '" . md5($pass_user) . "',
                                                        '" . $email_user . "',
                                                        '" . $id_branch . "',
                                                        '" . $group . "'
                                                        )";
        if ($sql_add_user = mysqli_query($conn, $query_add_user)) {
            $active = "";
            $query_get_m_menu = "SELECT * FROM m_menu";
            $sql_get_m_menu = mysqli_query($conn, $query_get_m_menu);
            while ($row_m_menu = mysqli_fetch_array($sql_get_m_menu)) {
                $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
                $menu_id = mysqli_real_escape_string($conn, $row_m_menu['menu_id']);
                $query_add_akses = mysqli_query($conn, "INSERT INTO m_akses ( 
                                                                        id_user,
                                                                        id_menu,
                                                                        status
                                                                        ) Values (
                                                                        '" . $id_user . "',
                                                                        '" . $menu_id . "',
                                                                        '0'
                                                                        ) 
                                                                            ");
            }

            foreach ($_POST['kode_akses'] as $active) {
                $query_set_akses = mysqli_query($conn, " UPDATE m_akses SET status = '1' where id_menu = '" . $active . "' and id_user = '" . $id_user . "' ");
            }
            $valid = 1;
            $msg = "Add Data User : Success !";
        } else {
            $valid = 0;
            $msg = "Add Data User : Failed !";
        }
    } else {
        // Edit Branch Process
        $pass = "";
        if ($pass_user !== "") {
            $pass = "pass_user = '" . md5($pass_user) . "',";
        }
        $query_set_user = "  UPDATE m_user 
                                    SET nm_user = '" . $nm_user . "', 
                                        email_user = '" . $email_user . "', 
                                        " . $pass . "                                  
                                        group_user = '" . $group . "',                          
                                        id_branch = '" . $id_branch . "'
                                    WHERE id_user = '" . $id . "'";
        if ($sql_set_user = mysqli_query($conn, $query_set_user)) {
            $query_update = mysqli_query($conn, "DELETE FROM m_akses WHERE id_user = '" . $id_user . "' ");

            $query_get_m_menu = "SELECT * FROM m_menu";
            $sql_get_m_menu = mysqli_query($conn, $query_get_m_menu);
            while ($row_m_menu = mysqli_fetch_array($sql_get_m_menu)) {
                $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
                $menu_id = mysqli_real_escape_string($conn, $row_m_menu['menu_id']);
                $query_add_akses = mysqli_query($conn, "INSERT INTO m_akses ( 
                                                                        id_user,
                                                                        id_menu,
                                                                        status
                                                                        ) Values (
                                                                        '" . $id_user . "',
                                                                        '" . $menu_id . "',
                                                                        '0'
                                                                        ) 
                                                                            ");
            }

            foreach ($_POST['kode_akses'] as $active) {
                $query_set_akses = mysqli_query($conn, " UPDATE m_akses SET status = '1' where id_menu = '" . $active . "' and id_user = '" . $id_user . "' ");
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
        $page = "master_user.php";
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
    <title>New Master User Login</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- select2 CSS -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>


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
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id_user=<?php echo $id_user_filter; ?>" method="POST">
                            <section class="hk-sec-wrapper">
                                <h5 class="hk-sec-title">Add/Edit Master User Login </h5>
                                <div class="row">
                                    <div class="col-sm">
                                        <?php
                                        $marketing = '';
                                        $fill = '';

                                        $checked_sop = "";
                                        $query_get_user = "SELECT * FROM m_user WHERE id_user='" . $id_user_filter . "' ";
                                        $sql_get_user = mysqli_query($conn, $query_get_user);
                                        if ($row_user = mysqli_fetch_array($sql_get_user)) {
                                            $id_user = $row_user['id_user'];
                                            $nm_user = $row_user['nm_user'];
                                            $email_user = $row_user['email_user'];
                                            $id_branch = $row_user['id_branch'];
                                            $user_type = $row_user['group_user'];
                                            $checked_sop = "";
                                            $sop_checker = $row_user['sop_checker'];
                                            if ($row_user['sop_checker'] == "Y") {
                                                $checked_sop = "checked";
                                            }
                                            $pass_user = "Change";
                                            $readonly = "readonly";
                                            $required = "";
                                            $fill = 'filled-input';


                                            if ($user_type == "Franchise") {
                                                $display = "";
                                                $display_view = "hidden";
                                            }

                                            if ($user_type == "Supervisor" || $user_type == "Admin") {
                                                $display = "hidden";
                                                $display_view = "";
                                            }
                                        }
                                        echo '
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> User ID</span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="id_user"
                                                            id="id_user" class="form-control ' . $fill . ' form-control-sm"
                                                            value="' . $id_user . '" required ' . $readonly . '>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Username</span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="nm_user"
                                                            id="nm_user" class="form-control form-control-sm"
                                                            value="' . $nm_user . '" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">' . $pass_user . ' Password</span>
                                                        </div>
                                                        <input autocomplete="off" type="password" name="pass_user"
                                                            id="pass_user" class="form-control form-control-sm"
                                                            value="" ' . $required . '>
                                                    </div>

                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Email User</span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="email_user"
                                                            id="email_user" class="form-control form-control-sm"
                                                            value="' . $email_user . '">
                                                    </div>

                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> User Type </span>
                                                        </div>
                                                        <select name="group_user" id="group_user"
                                                            class="custom-select custom-select-sm form-control-sm group_user">
                                                            <option value="">- Choose Type --</option>
                                                            <option value="super" ';
                                        if ($user_type == "super") {
                                            echo 'selected';
                                        }
                                        echo '>Super Admin</option>';
                                        // if ($user_type == "Franchise") {
                                        //     echo 'selected';
                                        // }
                                        // echo '>Franchise</option>
                                        //                     <option value="Supervisor" ';
                                        // if ($user_type == "Supervisor") {
                                        //     echo 'selected';
                                        // }
                                        // echo '>Supervisor</option>
                                        echo '<option value="Admin" ';
                                        if ($user_type == "Admin") {
                                            echo 'selected';
                                        }
                                        echo '>Admin</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group franc_view" ' . $display . '>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Branch </span>
                                                        </div>
                                                        <select id="input_tags" class="form-control form-control-sm to_task" name="branch[]"
                                                            multiple="multiple">
                                                            ';
                                        $query_get_branch = mysqli_query($conn, "SELECT m_branch_id,
                                                                                                        nm_branch
                                                                                                FROM m_branch");
                                        while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                            echo '<option value="' . $row_branch['m_branch_id'] . '" ';
                                            if (strpos($id_branch, $row_branch['m_branch_id']) > -1) {
                                                echo 'selected';
                                            }
                                            echo '>' . $row_branch['nm_branch'] . '</option>';
                                        }

                                        echo '
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="form-group visor_view" ' . $display_view . '>
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Branch </span>
                                                        </div>
                                                        <select name="id_branch" id="id_branch"
                                                            class="custom-select custom-select-sm form-control-sm id_branch">
                                                            <option value="">- Choose Branch --</option>
                                                            <option value="pusat" ';
                                        if ($id_branch == "pusat") {
                                            echo 'selected';
                                        }
                                        echo '>Pusat</option>
                                                            ';
                                        $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE stat != 'no'");
                                        while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                            echo '<option value="' . $row_branch['id_cabang'] . '" ';
                                            if ($id_branch == $row_branch['id_cabang']) {
                                                echo 'selected';
                                            }
                                            echo '>' . $row_branch['nama_cabang'] . '</option>';
                                        }

                                        echo '
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
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
                                                                        order by no_urut asc";
                                        $sql_get_parent = mysqli_query($conn, $query_get_parent);
                                        while ($row_parent = mysqli_fetch_array($sql_get_parent)) {
                                            $id_parent = $row_parent['menu_id'];
                                            $nm_parent = $row_parent['nm_menu'];
                                            $checked = "";
                                            $visible = 'style="display:none;"';
                                            $query_get_akses = mysqli_query($conn, "SELECT status FROM m_akses where id_menu ='" . $id_parent . "' and id_user = '" . $id_user . "'");
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
                                            $query_get_menu = "SELECT menu_id,parent,nm_menu,link FROM m_menu WHERE parent = '" . $id_parent . "' order by no_urut asc";
                                            $sql_get_menu = mysqli_query($conn, $query_get_menu);
                                            while ($row_menu = mysqli_fetch_array($sql_get_menu)) {
                                                $id_menu = $row_menu['menu_id'];
                                                $nm_menu = $row_menu['nm_menu'];
                                                $link_menu = $row_menu['link'];
                                                $checked = "";
                                                $visible_sub = 'style="display:none;"';
                                                $query_get_akses = mysqli_query($conn, "SELECT status FROM m_akses where id_menu ='" . $id_menu . "' and id_user = '" . $id_user . "' ");
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
                        <a href="master_user.php" class="btn btn-sm btn-danger btn-block">Cancel</a>
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
    <script src="vendors/jquery/dist/jquery.min.js"></script>

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
    </script>


</body>

</html>