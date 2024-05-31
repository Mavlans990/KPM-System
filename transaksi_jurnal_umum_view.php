<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

// $id_transaksi_filter = mysqli_real_escape_string($conn,$_GET['id_jurnal']);
$id_user = $_SESSION['id_user'];
$date = date("Y-m-d H:i:s");

$tgl_1 = "";
$tgl_2 = "";


if (isset($_POST['cari_tgl'])) {
    $tgl_1 = mysqli_real_escape_string($conn, $_POST['tgl_1']);
    $tgl_2 = mysqli_real_escape_string($conn, $_POST['tgl_2']);
    if ($_SESSION['grup'] !== "super") {
        if ($tgl_1 !== "" && $tgl_2 == "") {
            $where = "WHERE tgl_jurnal_umum = '" . $tgl_1 . "' AND dibuat_oleh = '" . $_SESSION['nm_user'] . "'";
        }
        if ($tgl_1 == "" && $tgl_2 !== "") {
            $where = "WHERE tgl_jurnal_umum = '" . $tgl_2 . "' AND dibuat_oleh = '" . $_SESSION['nm_user'] . "'";
        }
        if ($tgl_1 !== "" && $tgl_2 !== "") {
            $where = "WHERE tgl_jurnal_umum BETWEEN '" . $tgl_1 . "' and '" . $tgl_2 . "' AND dibuat_oleh = '" . $_SESSION['nm_user'] . "'";
        }
    } else {
        if ($tgl_1 !== "" && $tgl_2 == "") {
            $where = "WHERE tgl_jurnal_umum = '" . $tgl_1 . "' ";
        }
        if ($tgl_1 == "" && $tgl_2 !== "") {
            $where = "WHERE tgl_jurnal_umum = '" . $tgl_2 . "' ";
        }
        if ($tgl_1 !== "" && $tgl_2 !== "") {
            $where = "WHERE tgl_jurnal_umum BETWEEN '" . $tgl_1 . "' and '" . $tgl_2 . "' ";
        }
    }
} else {
    $where = "WHERE dibuat_oleh = '" . $_SESSION['nm_user'] . "'";
}

if (isset($_POST['delete'])) {
    $msg = "Delete Jurnal Umum Gagal";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus']);
    $sql_del = "DELETE FROM tb_jurnal_umum WHERE no_transaksi = '" . $id_hapus . "' ";
    if ($query_del = mysqli_query($conn, $sql_del)) {
        $msg = "Delete Jurnal Umum Berhasil";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Transaksi Jurnal Umum</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- select2 CSS -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

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

            <!-- Header -->
            <div class="card-header bg-green-light-4">
                Daftar Transaksi
                <h5 class="hk-sec-title text-dark-light-3"> Jurnal Umum
                    <a href="transaksi_jurnal_umum.php?id_jurnal=new" class="btn btn-primary btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span class="btn-text">Buat Jurnal Umum</span><span class="icon-label"><i class="fa fa-angle-right"></i> </span></a>
                </h5>
            </div>
            <!-- /Header -->

            <!-- Container -->

            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
                    <div class="row" style="">

                        <div class="col-sm-7 d">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span style="" class="mt-20 mr-1 text-dark" id="inputGroup-sizing-sm">Cari Tanggal</span>
                                </div>
                                <input autocomplete="off" type="date" id="cari_tanggal_1" name="tgl_1" class="form-control form-control-sm mt-15 mr-1" value="<?php echo $tgl_1 ?>">
                                <p class="mt-20 text-dark mr-1">s/d</p>
                                <input autocomplete="off" type="date" id="cari_tanggal_2" name="tgl_2" class="form-control form-control-sm mt-15 mr-2" value="<?php echo $tgl_2 ?>">
                                <input type="submit" name="cari_tgl" class="btn btn-info btn-xs mt-15" value=" Cari ">
                            </div>
                        </div>
                    </div>

                </form>

                <table class="table table-hover table-sm w-100 display mt-15" id="datatables1">
                    <thead>
                        <tr>
                            <td>Tgl Transaksi</td>
                            <td>No Transaksi</td>
                            <td>Di Buat Oleh</td>
                            <td>Memo</td>
                            <td>Status</td>
                            <td>Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $query_get_jurnal_umum = mysqli_query($conn, "SELECT DISTINCT no_transaksi,
                                                                tgl_jurnal_umum,
                                                                dibuat_oleh,
                                                                memo,
                                                                status_jurnal
                                                         FROM  tb_jurnal_umum
                                                         " . $where . " ");



                        while ($row_jurnal = mysqli_fetch_array($query_get_jurnal_umum)) {

                            echo '                               
                        <tr>
                            <td style="font-size:11px;">' . date('d/m/Y', strtotime($row_jurnal['tgl_jurnal_umum'])) . '</td>
                            <td style="font-size:11px;">Jurnal Umum #' . $row_jurnal['no_transaksi'] . '</td>
                            <td style="font-size:11px;">' . $row_jurnal['dibuat_oleh'] . '</td>
                            <td style="font-size:11px;">' . $row_jurnal['memo'] . '</td>

                            <td style="font-size:11px;">' . $row_jurnal['status_jurnal'] . '</td>
                            <td>
                                <a href="transaksi_jurnal_umum.php?view=' . $row_jurnal['no_transaksi'] . '" class="btn btn-xs btn-icon btn-info btn-icon-style-1"><span class="btn-icon-wrap"><i class="fa fa-eye"></i></span></a>
                                ';
                            $btn_edit = "btn-warning";
                            $btn_del = "btn-danger";
                            $href = 'href="transaksi_jurnal_umum.php?id_jurnal=' . $row_jurnal['no_transaksi'] . '" ';
                            $hapus = 'hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                                data-id_hapus="' . $row_jurnal['no_transaksi'] . '" ';
                            if ($row_jurnal['status_jurnal'] == 'Posted') {
                                $href = '';

                                $hapus = '';
                                $btn_edit = "btn-secondary";
                                $btn_del = "btn-secondary";
                            }
                            echo '
                                <a ' . $href . ' class="btn btn-xs btn-icon ' . $btn_edit . ' btn-icon-style-1 "><span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                                <a class="btn btn-xs btn-icon ' . $btn_del . ' btn-icon-style-1 ' . $hapus . '">
                                    <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                                </a>
                            </td>
                        </tr>
                        ';
                        }
                        ?>
                    </tbody>
                </table>

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
    <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Jurnal Umum</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Akan menghapus data transaksi Jurnal Umum, Yakin?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-danger" name="delete" value="Delete">
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <!-- Select2 JavaScript -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="dist/js/select2-data.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $(document).on("click", '.hapus_button', function(e) {
                var id_hapus = $(this).data('id_hapus');
                $(".id_hapus").val(id_hapus);
            });
        });
    </script>


</body>

</html>