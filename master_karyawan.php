<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['nm_user'])) {
    header('Location:index.php');
}

if (isset($_POST['save'])) {
    $valid = 1;
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $id_user = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nm_user = mysqli_real_escape_string($conn, $_POST['nm_user']);
    $pass_user = mysqli_real_escape_string($conn, $_POST['pass_user']);
    $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);

    if ($id == "new") {
        $query_add_user = "INSERT INTO m_user ( id_user,
                                                        nm_user,
                                                        pass_user,
                                                        id_branch
                                                        ) VALUES (
                                                        '" . $id_user . "',
                                                        '" . $nm_user . "',
                                                        '" . md5($pass_user) . "',
                                                        '" . $id_branch . "'
                                                        )";
        if ($sql_add_user = mysqli_query($conn, $query_add_user)) {
            $valid = 1;
            $msg = "Add Data User : Success !";
        } else {
            $valid = 0;
            $msg = "Add Data User : Failed !";
        }
    } else {
        // Edit Branch Process
        $query_set_user = "  UPDATE m_user 
                                    SET nm_user = '" . $nm_user . "',
                                        pass_user = '" . md5($pass_user) . "',
                                        id_branch = '" . $id_branch . "'
                                    WHERE id_user = '" . $id . "'";
        if ($sql_set_user = mysqli_query($conn, $query_set_user)) {
            $valid = 1;
            $msg = "Edit Data User : Success !";
        } else {
            $valid = 0;
            $msg = "Edit Data User : Failed !";
        }
        // End Edit Branch Process
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}

// Delete Flexas
if (isset($_POST['delete'])) {
    $valid = 1;

    if ($valid == 1) {
        $select_karyawan = "SELECT user_id FROM tb_karyawan WHERE id_karyawan = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'";
        $query_karyawan = mysqli_query($conn, $select_karyawan);
        $data_karyawan = mysqli_fetch_array($query_karyawan);

        $delete = mysqli_query($conn, "DELETE FROM m_akses WHERE id_user = '" . $data_karyawan['user_id'] . "'");
    }

    if ($valid == 1) {
        $query_del = mysqli_query($conn, "DELETE FROM tb_karyawan WHERE id_karyawan='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' ");
        if (!$query_del) {
            $valid = 0;
            $msg = "Delete Data User : Failed !";
        }
    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $msg = "Delete Data User : Success !";
        $page = "master_karyawan.php";
        $sec = "0.5";
        $redirect = 1;
        header("Refresh: $sec; url=$page");
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
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
    <title>Master User Login</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

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
            <div class="container-fluid  mt-15">
                <!-- Row -->
                <div class="row">
                    <div class="col">
                        <section class="hk-sec-wrapper">

                            <div class="row">
                                <div class="col-sm-10">
                                    <h5 class="hk-sec-title">Master User Login
                                        <a href="master_karyawan_edit.php?id_user=new" class="btn btn-success btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span class="btn-text">Add New</span><span class="icon-label"><i class="fa fa-angle-right"></i> </span></a>
                                    </h5>
                                </div>
                                <!-- <div class="col">
                                    <h5 class="hk-sec-title">
                                        <a href="print/master_karyawan_login_print.php"
                                            class="btn btn-info btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span
                                                class="btn-text">Download Excel</span></a>
                                    </h5>
                                </div> -->
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-sm">

                                    <div class="table-responsive">
                                        <table id="datable_2" class="table table-hover w-100 display">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>User ID</th>
                                                    <th>Nama Lengkap</th>
                                                    <th>Role User</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                // Panggil m.user
                                                $query_get = mysqli_query($conn, "SELECT * FROM tb_karyawan ORDER BY id_karyawan ASC");
                                                while ($row_get = mysqli_fetch_array($query_get)) {

                                                    if ($row_get['grup'] == "super") {
                                                        $role_user = "Admin";
                                                    } else if ($row_get['grup'] == "manajer") {
                                                        $role_user = "Manager";
                                                    } else {
                                                        $role_user = "Owner";
                                                    }

                                                    echo '
                                                    <tr>
                                                    <td>' . $no . '</td>    
                                                    <td><a href="#" target="_blank">' . $row_get['user_id'] . '</a></td>
                                                    <td>' . $row_get['nama_lengkap'] . '</td>
                                                    <td>' . $role_user . '</td>
                                                 
                                                    <td>
                                                        <a href="master_karyawan_edit.php?id_user=' . $row_get['id_karyawan'] . '" class="btn btn-xs btn-icon btn-warning btn-icon-style-1" >
                                                            <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                                                        <a class="btn btn-xs btn-icon btn-danger btn-icon-style-1 hapus_button" data-toggle="modal" data-target="#DeleteUserModal"
                                                            data-id_hapus="' . $row_get['id_karyawan'] . '">
                                                            <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                                                        </a>
                                                    </td>
                                                
                                                </tr>
                                                    ';
                                                    $no++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <!-- /Row -->
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

    <!-- DELETE MODAL -->
    <div class="modal fade" id="DeleteUserModal" tabindex="1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete User</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Akan menghapus data user, Yakin?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-danger btn-sm" name="delete" value="Delete">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END DELETE MODAL -->

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

    <!-- Delete -->
    <script type="text/javascript">
        $(document).on("click", '.hapus_button', function(e) {
            var id_hapus = $(this).data('id_hapus');
            $(".id_hapus").val(id_hapus);
        });

        $(document).on("click", '.add_button', function(e) {
            var id = $(this).data('id');
            $(".id").val(id);
            $(".id_user").prop("readonly", false);
            $(".id_user").removeClass("filled-input");
            $(".id_user").val("");
            $(".nm_user").val("");
            $(".pass_user").val("");
            $(".id_branch").val("");
            $(".pass-text").text("Password");
            $(".pass_user").prop("required", true);
        });

        $(document).on("click", '.edit_button', function(e) {
            var id = $(this).data('id_user');
            var nm = $(this).data('nm_user');
            var pass = $(this).data('pass_user');
            var branch = $(this).data('id_branch');
            $(".id").val(id);
            $(".id_user").val(id);
            $(".nm_user").val(nm);
            $(".pass_user").val("");
            $(".id_branch").val(branch);
            $(".id_user").prop("readonly", true);
            $(".id_user").addClass("filled-input");
            $(".pass-text").text("Change Password");
            $(".pass_user").prop("required", false);
        });
    </script>


</body>

</html>