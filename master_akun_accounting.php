<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('location:index.php');
}

$tgl_1 = "set";
$query_get_tgl = mysqli_query($conn, "SELECT tgl_awal FROM m_saldo_awal");
if ($row_tgl = mysqli_fetch_array($query_get_tgl)) {
    $tgl_1 = $row_tgl['tgl_awal'];
}

// Delete Flexas
if (isset($_POST['delete'])) {
    $valid = 1;

    if ($valid == 1) {
        $id_akun = mysqli_real_escape_string($conn, $_POST['id_hapus']);
        $query_del = mysqli_query($conn, "DELETE FROM m_akun WHERE m_akun_id='" . $id_akun . "' ");

        $query_del_akses = mysqli_query($conn, "UPDATE m_akun SET head_akun = 'none' WHERE head_akun='" . $id_akun . "' ");
        if (!$query_del || !$query_del_akses) {
            $valid = 0;
            $msg = "ERROR : Delete User Failed";
        }
    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $msg = "Delete Data Success";
        $page = "master_akun_accounting.php";
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
    <title>Daftar Akun</title>
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

    <style>
        .expand-button:after {
            content: '-';
        }

        .expand-button:after {
            content: '+';
        }
    </style>

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
                                <div class="col-sm-10 d-flex">
                                    <h5 class="hk-sec-title mr-2">Daftar Akun
                                        <a href="master_akun_edit_accounting.php?id_akun=new" class="tambah_button btn btn-success btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span class="btn-text">Buat Akun</span><span class="icon-label"><i class="fa fa-angle-right"></i> </span></a>
                                    </h5>
                                    <h5 class="hk-sec-title">
                                        <a href="transaksi_jurnal_umum_view.php" class="btn btn-primary btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span class="btn-text">Buat Jurnal Umum</span><span class="icon-label"><i class="fa fa-angle-right"></i> </span></a>
                                    </h5>
                                </div>
                                <div class="col">
                                    <div class="btn-group">
                                        <div class="dropdown">
                                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info btn-sm dropdown-toggle btn-rounded" type="button">Tindakan <span class="caret"></span></button>
                                            <div role="menu" class="dropdown-menu">
                                                <a class="dropdown-item" href="akuntan_saldo_awal.php?set_date=<?= $tgl_1; ?>"><i class="dropdown-icon fa fa-book"></i><span>Atur Saldo Awal</span></a>
                                                <a class="dropdown-item" href="#"><i class="dropdown-icon fa fa-book"></i><span>Penutupan Buku</span></a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="print/master_akun_print.php"><i class="dropdown-icon fa fa-download"></i><span>Download Excel</span></a>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- <h5 class="hk-sec-title">
                                        <a href="print/master_akun_print.php"
                                            class="btn btn-info btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span
                                                class="btn-text">Download Excel</span></a>
                                    </h5> -->
                                </div>
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="table-wrap">
                                        <table id="datable_12" class="table table-hover w-100 display table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Kode Akun</th>
                                                    <th>Nama Akun</th>
                                                    <th>Detail Akun</th>
                                                    <th>Kategori Akun</th>
                                                    <th hidden>Pajak</th>
                                                    <th>Saldo (IDR)</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="accordion accordion-type-2" id="accordion_2">
                                                <?php
                                                // Panggil m.user 
                                                $query_get = mysqli_query($conn, "SELECT  ma.m_akun_id,
                                                                                    ma.kode_akun,
                                                                                    ma.nm_akun,
                                                                                    mk.nm_kategori,
                                                                                    ma.pajak_akun,
                                                                                    ma.saldo_akun,
                                                                                    ma.detail_akun,
                                                                                    ma.head_akun
                                                                                FROM m_akun ma
                                                                                LEFT JOIN m_kategori_akun mk ON ma.kat_akun = mk.kat_akun_id
                                                                                where ma.detail_akun = 'none' or ma.detail_akun = 'Akun Header dari' 
                                                                                ORDER BY ma.kode_akun asc");

                                                while ($row_get = mysqli_fetch_array($query_get)) {
                                                    $btn = "";
                                                    $id = "";
                                                    if ($row_get['detail_akun'] == 'Akun Header dari') {
                                                        $btn = '<a class="collapsed text-dark expand-button"  data-toggle="collapse" href="#collapse_1' . $row_get['m_akun_id'] . '" aria-expanded="true"><strong></strong></a>';
                                                        $id = 'id="' . $row_get['m_akun_id'] . '"';
                                                    }

                                                    echo '
                                                    <tr ' . $id . '> 
                                                    <td>' . $row_get['kode_akun'] . '   ' . $btn . '</td>
                                                    <td style="font-size:11px;">' . $row_get['nm_akun'] . '</td>
                                                    <td style="font-size:11px;">' . $row_get['detail_akun'] . '</td>
                                                    <td style="font-size:11px;">' . $row_get['nm_kategori'] . '</td>
                                                    <td style="font-size:11px;" hidden>' . $row_get['pajak_akun'] . '</td>
                                                    <td style="font-size:11px; text-align:right;">' . number_format($row_get['saldo_akun']) . '</td>
                                                    <td>
                                                        <a href="master_akun_edit_accounting.php?id_akun=' . $row_get['m_akun_id'] . '" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 "><span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                                                        <a href="#" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                                                            data-id_hapus="' . $row_get['m_akun_id'] . '">
                                                            <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                                                        </a>
                                                    </td>                                          
                                                </tr>
                                                ';
                                                    if ($row_get['detail_akun'] == "Akun Header dari") {
                                                        $query_get_sub = mysqli_query($conn, "SELECT  ma.m_akun_id,
                                                                                    ma.kode_akun,
                                                                                    ma.nm_akun,
                                                                                    mk.nm_kategori,
                                                                                    ma.pajak_akun,
                                                                                    ma.head_akun,
                                                                                    ma.saldo_akun,
                                                                                    ma.detail_akun
                                                                                FROM m_akun ma
                                                                                LEFT JOIN m_kategori_akun mk ON ma.kat_akun = mk.kat_akun_id
                                                                                where ma.head_akun = '" . $row_get['m_akun_id'] . "'
                                                                                ORDER BY ma.kode_akun ASC ");
                                                        while ($row_get_sub = mysqli_fetch_array($query_get_sub)) {

                                                            echo '
                                                        
                                                        <tr headers="' . $row_get_sub['head_akun'] . '" id="collapse_1' . $row_get_sub['head_akun'] . '" class="collapse" data-parent="#accordion_2" > 
                                                        <td style="font-size:11px;">' . $row_get_sub['kode_akun'] . '</td>
                                                        <td style="font-size:11px;">' . $row_get_sub['nm_akun'] . '</td>
                                                        <td style="font-size:11px;">' . $row_get_sub['detail_akun'] . '</td>
                                                        <td style="font-size:11px;">' . $row_get_sub['nm_kategori'] . '</td>
                                                        <td style="font-size:11px;" hidden>' . $row_get_sub['pajak_akun'] . '</td> 
                                                        <td style="font-size:11px; text-align:right;">' . number_format($row_get_sub['saldo_akun']) . '</td>
                                                        <td>
                                                            <a href="master_akun_edit_accounting.php?id_akun=' . $row_get_sub['m_akun_id'] . '" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 "><span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                                                            <a href="#" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                                                                data-id_hapus="' . $row_get_sub['m_akun_id'] . '">
                                                                <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                                                            </a>
                                                        </td>
                                                    
                                                    </tr>
                                                    ';
                                                        }
                                                    }
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
    <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Akun</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Akan menghapus data akun, Yakin?
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

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>

    <!-- Delete -->
    <script type="text/javascript">
        $(document).on("click", '.hapus_button', function(e) {
            var id_hapus = $(this).data('id_hapus');
            $(".id_hapus").val(id_hapus);
        });

        $('#datable_12').DataTable({
            responsive: true,
            autoWidth: false,
            "bSort": false,
            language: {
                search: "",
                searchPlaceholder: "Search",
                sLengthMenu: "_MENU_items"
            }

        });
    </script>


</body>

</html>