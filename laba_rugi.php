<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$required = "";
$hidn = "hidden";
$filled_input = "filled-input";
if ($_SESSION['grup'] == 'super' || $_SESSION['grup'] == 'franchise') {
    $hidn = "";
    $required = "required";
    $filled_input = "";
}

$from = date("Y-m-d");
$to = "";
$supp = "";
$bah = "";
$cabang = '';
if (isset($_POST['search'])) {
    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Coolplus System</title>
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

<body id="page-top">

    <!-- Page Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

        <!-- Sidebar -->
        <?php include "header.php"; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div class="hk-wrapper hk-vertical-nav">

            <!-- Main Content -->
            <div class="hk-pg-wrapper">

                <!-- Topbar -->
                <?php //include "part/topbar.php"; 
                ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">

                    <!-- Page Heading -->

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-1" style="text-transform: none;">
                            <div class="form-group mt-5">
                                <h1 class="h3 mb-2 text-gray-800">Laporan Laba Rugi</h1>
                            </div>
                            <form action="" method="post">

                                <br>
                                <div class="row no-gutters">
                                    <div class="form-group form-inline">

                                        <input type="date" name="tgl_from" id="" class="form-control form-control-sm mb--5" value="<?= $from; ?>" required>
                                        <span class="w--100 text-center">S/D</span>
                                        <input type="date" name="tgl_to" id="" class="form-control form-control-sm mt--5" value="<?= $to; ?>" required>
                                        <select class="form-control cabang form-control-sm mt--5 <?php echo $filled_input; ?>" name="cabang">
                                            <?php
                                            $where_branch = "";
                                            if ($_SESSION['group'] == "franchise") {
                                                $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
                                                $where_branch = " WHERE id_cabang IN('" . $filter_cabang . "')";
                                            }
                                            if ($_SESSION['group'] == "admin") {
                                                $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                            }
                                            $select_cabang = "SELECT * FROM tb_cabang " . $where_branch . " ORDER BY id_cabang ASC";
                                            $querycabang = mysqli_query($conn, $select_cabang);
                                            while ($row_vcabang = mysqli_fetch_array($querycabang)) {
                                                $selected = "";
                                                if ($row_vcabang['id_cabang'] == $cabang) {
                                                    $selected = "selected";
                                                }
                                                echo '
                                                    <option value="' . $row_vcabang['id_cabang'] . '" ' . $selected . '>' . $row_vcabang['nama_cabang'] . '</option>                                                
                                                ';
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" class="btn btn-primary form-control-sm ml-1 mt--5" name="search"><i class="fa fa-search"></i> Cari</button>
                                        <?php
                                        if (isset($_POST['search'])) {
                                            echo '
                                                        <a href="print/print_laba_rugi.php?tgl_from=' . $from . '&tgl_to=' . $to . '&cabang=' . $cabang . '" class="btn btn-info text-white form-control-sm ml-1 mt--5" target="_blank"><i class="fa fa-print"></i> Print</a>
                                                        <a href="print/print_excel_laba_rugi.php?tgl_from=' . $from . '&tgl_to=' . $to . '&cabang=' . $cabang . '" class="btn btn-success form-control-sm mt--5 ml-1" target="_blank"><i class="fa fa-download"></i> Excel</a>
                                                    ';
                                        }
                                        ?>

                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="printdivcontent">

                                <div class="row justify-content-center">
                                    <div class="col-md-8 col-sm-12 ">
                                        <?php
                                        if (isset($_POST['search'])) {
                                            $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);

                                            $query_cabang = mysqli_query($conn, "
                                        SELECT 
                                        id_cabang,
                                        nama_cabang
                                        FROM tb_cabang
                                        WHERE id_cabang = '" . $cabang . "'
                                    ");
                                            $data_cabang = mysqli_fetch_array($query_cabang);

                                            echo '
                                        <h3 class="text-center">PT. Inovasi Sukses Sejahtera</h3>
                                        <h5 class="text-center mt-2 mb-3">Cabang ' . $data_cabang['nama_cabang'] . '</h5>
                                        <p class="text-center">Dari ' . date("d M Y", strtotime($_POST['tgl_from'])) . ' Sampai ' . date("d M Y", strtotime($_POST['tgl_to'])) . '</p>
                                        ';
                                        }
                                        ?>
                                        <table class="table table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                            <?php
                                            if (isset($_POST['search'])) {

                                                $tgl_1 = mysqli_real_escape_string($conn, $_POST['tgl_from']);
                                                $tgl_2 = mysqli_real_escape_string($conn, $_POST['tgl_to']);
                                                $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);

                                                $filter_cabang = "";
                                                if ($cabang !== "") {
                                                    $filter_cabang = " AND id_cabang = '" . $cabang . "'";
                                                }

                                                $total_penjualan = 0;
                                                $total_modal = 0;
                                                $total_biaya = 0;

                                                $select_penjualan = "SELECT * FROM tb_barang_keluar WHERE tgl_transaksi BETWEEN '" . $tgl_1 . "' AND '" . $tgl_2 . "' " . $filter_cabang . " ";
                                                $query_penjualan = mysqli_query($conn, $select_penjualan);
                                                while ($row_penjualan = mysqli_fetch_array($query_penjualan)) {
                                                    $query_pembayaran = mysqli_query($conn, "
                                                        SELECT SUM(nominal) AS total_nominal FROM tb_pembayaran WHERE id_pembayaran = '" . $row_penjualan['id_transaksi'] . "'
                                                    ");
                                                    $data_pembayaran = mysqli_fetch_array($query_pembayaran);
                                                    $total_penjualan = $total_penjualan + $row_penjualan['total'];

                                                    $total_modal = $total_modal + $row_penjualan['hpp'] * $row_penjualan['qty'];
                                                }

                                                $filter_biaya = "";

                                                if ($cabang !== '') {
                                                    $filter_biaya = " AND cabang = '" . $cabang . "'";
                                                }

                                                $select_biaya = "SELECT * FROM tb_operasional WHERE tgl_buat BETWEEN '" . $tgl_1 . "' AND '" . $tgl_2 . "' " . $filter_biaya;
                                                $query_biaya = mysqli_query($conn, $select_biaya);
                                                while ($row_biaya = mysqli_fetch_array($query_biaya)) {
                                                    $total_biaya = $total_biaya + $row_biaya['total'];
                                                }

                                                echo '
                                            <tr>
                                                <th style="border-top:2px solid black;"><b style="font-weight:bold;">Pendapatan</b></th>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <div class="row">
                                                        <div class="col-6 text-left"><p style="margin-left:1rem;">Pendapatan Penjualan</p></div>
                                                        <div class="col-6 text-right">' . number_format($total_penjualan) . '</div>
                                                    </div>
                                                </th>
                                            </tr>
                                            ';
                                                $pendapatan_mutasi = 0;
                                                $penjualan_mutasi = 0;
                                                $query_inv_out = mysqli_query($conn, "
                                                SELECT * 
                                                FROM inv_adjust_out
                                                WHERE inv_date BETWEEN '" . $tgl_1 . "' AND '" . $tgl_2 . "' AND id_branch = '" . $cabang . "' AND is_sell = '1'
                                                ");
                                                $jum_inv_out = mysqli_num_rows($query_inv_out);
                                                while ($row_inv_out = mysqli_fetch_array($query_inv_out)) {
                                                    $pendapatan_mutasi = $pendapatan_mutasi + $row_inv_out['biaya'];
                                                    $penjualan_mutasi = $penjualan_mutasi + ($row_inv_out['stock_out'] * $row_inv_out['hpp']);
                                                }

                                                if ($jum_inv_out > 0) {
                                                    echo '
                                                    <tr>
                                                        <th>
                                                            <div class="row">
                                                                <div class="col-6 text-left"><p style="margin-left:1rem;">Pendapatan Mutasi</p></div>
                                                                <div class="col-6 text-right">' . number_format($pendapatan_mutasi) . '</div>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                    ';
                                                }


                                                echo '
                                            
                                            <tr>
                                              <th style="border-bottom:2px solid black;">
                                                <div class="row">
                                                <div class="col-6 text-left"><b style="font-weight:bold;">Total Pendapatan</b></div>
                                                <div class="col-6 text-right"><b style="font-weight:bold;">' . number_format($total_penjualan + $pendapatan_mutasi) . '</div>
                                                </div>
                                              </th>
                                            </tr>
                                            <tr>
                                                <th><b style="font-weight:bold;">Harga Pokok Penjualan</b></th>
                                            </tr>
                                            <tr>
                                                <th>
                                                    <div class="row">
                                                        <div class="col-6 text-left"><p style="margin-left:1rem;">Harga Pokok Penjualan</p></div>
                                                        <div class="col-6 text-right">' . number_format($total_modal) . '</div>
                                                    </div>
                                                </th>
                                            </tr>
                                            ';

                                                if ($jum_inv_out > 0) {
                                                    echo '
                                                <tr>
                                                    <th>
                                                        <div class="row">
                                                            <div class="col-6 text-left"><p style="margin-left:1rem;">Harga Pokok Mutasi</p></div>
                                                            <div class="col-6 text-right">' . number_format($penjualan_mutasi) . '</div>
                                                        </div>
                                                    </th>   
                                                </tr>
                                                ';
                                                }

                                                echo '
                                            <tr>
                                              <th style="border-bottom:2px solid black;">
                                                <div class="row">
                                                <div class="col-6 text-left"><b style="font-weight:bold;">Total Pokok Penjualan</b></div>
                                                <div class="col-6 text-right"><b style="font-weight:bold;">(' . number_format($total_modal + $penjualan_mutasi) . ')</div>
                                                </div>
                                              </th>
                                            </tr>
                                            <tr style="background-color:#e7e7de;">
                                                <th>
                                                    <div class="row">
                                                        <div class="col-6 text-left"><p class="text_small">Total Laba Kotor</p></div>
                                                        <div class="col-6 text-right"><p class="text_small">' . number_format(($total_penjualan + $pendapatan_mutasi) - ($total_modal + $penjualan_mutasi)) . '</p></div>
                                                    </div>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="border-top:2px solid black;"><b style="font-weight:bold;">Beban Operasional</b></th>
                                                
                                            </tr>
                                        ';

                                                $select_biaya = "SELECT * FROM tb_operasional WHERE tgl_buat BETWEEN '" . $tgl_1 . "' AND '" . $tgl_2 . "' " . $filter_biaya;
                                                $query_biaya = mysqli_query($conn, $select_biaya);
                                                while ($row_biaya = mysqli_fetch_array($query_biaya)) {
                                                    echo '
                                                    <tr>
                                                        <th>
                                                            <div class="row">
                                                                <div class="col-6 text-left"><p style="margin-left:1rem;">' . $row_biaya['nama_beban'] . '</p></div>
                                                                <div class="col-6 text-right">' . number_format($row_biaya['total']) . '</div>
                                                            </div>
                                                        </th>
                                                    </tr>
                                                ';
                                                }
                                                echo '
                                                <tr>
                                                    <th style="border-bottom:2px solid black;">
                                                    <div class="row">
                                                    <div class="col-6 text-left"><b style="font-weight:bold;">Total Beban Operasional</b></div>
                                                    <div class="col-6 text-right"><b style="font-weight:bold;">(' . number_format($total_biaya) . ')</div>
                                                    </div>
                                                    </th>
                                                </tr>
                                                <tr style="background-color:#e7e7de;">
                                                    <th>
                                                        <div class="row">
                                                            <div class="col-6 text-left"><p class="text_small">Laba Bersih Operasional</p></div>
                                                            <div class="col-6 text-right"><p class="text_small">' . number_format($total_biaya) . '</p></div>
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr style="background-color:#e7e7de;">
                                                    <th style="border-top:2px solid black;border-bottom:2px solid black;">
                                                        <div class="row">
                                                            <div class="col-6 text-left"><p class="text_small">Laba/(Rugi) Bersih</p></div>
                                                            <div class="col-6 text-right"><p class="text_small">' . number_format(($total_penjualan + $pendapatan_mutasi) - ($total_modal + $penjualan_mutasi) - $total_biaya) . '</p></div>
                                                        </div>
                                                    </th>
                                                </tr>
                                                ';
                                            }
                                            ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->


        </div>
        <!-- End of Content Wrapper -->

    </div>

    <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Merek Mobil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id" name="id">
                        <div class="form-group">
                            <label>Merek Mobil</label>
                            <input type="text" name="merek_motor" id="" class="form-control merek_motor">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-primary" name="save" value="Simpan">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Merek</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin menghapus merk ini ?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-danger" name="delete" value="Hapus">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
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

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>
<script type="text/javascript">
    $(document).on("click", ".print_page", function() {
        var divContents = document.getElementById("printdivcontent").innerHTML;
        var printWindow = window.open('', '', 'height=200,width=400');
        printWindow.document.write('<html><head><title>Print DIV Content</title>');
        printWindow.document.write('</head><body >');
        printWindow.document.write(divContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>