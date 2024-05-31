<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$hidn = "hidden";
$filled_input = "filled-input";
if ($_SESSION['grup'] == 'super' || $_SESSION['grup'] == 'franchise') {
    $hidn = "";
    $filled_input = "";
}

$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
}

$from = date("Y-m-d");
$to = date("Y-m-d");
$cabang = "";
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
                        <div class="card-header py-1" style="text-transform:none;">
                            <div class="form-group mt-5">
                                <h1 class="h3 mb-2 text-gray-800">Buku Besar</h1>
                            </div>
                            <div class="">
                                <form action="" method="post">
                                    <br>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group form-inline">
                                                <input type="date" name="tgl_from" id="" class="form-control form-control-sm" value="<?= $from; ?>" required>
                                                <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                                <input type="date" name="tgl_to" id="" class="form-control form-control-sm" value="<?= $to; ?>" required>
                                                <select name="cabang" id="" class="form-control form-control-sm mt--5 <?php echo $filled_input; ?>">
                                                    <?php
                                                    $where_branch = "";
                                                    if ($_SESSION['group'] == "franchise") {
                                                        $where_branch = " WHERE id_cabang IN('" . $filter_cabang . "')";
                                                    }
                                                    if ($_SESSION['group'] == "admin") {
                                                        $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                                    }
                                                    $query_cabang = mysqli_query($conn, "
                                                        SELECT 
                                                        id_cabang,
                                                        nama_cabang
                                                        FROM tb_cabang
                                                        " . $where_branch . "
                                                    ");
                                                    while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                                        echo '
                                                            <option value="' . $row_cabang['id_cabang'] . '">' . $row_cabang['nama_cabang'] . '</option>
                                                        ';
                                                    }
                                                    ?>
                                                </select>
                                                <button type="submit" class="btn btn-primary form-control-sm ml-1 mt--5" name="search"><i class="fa fa-search"></i> Cari</button>
                                                <?php
                                                if (isset($_POST['search'])) {
                                                    echo '
                                                        <a href="print/print_buku_besar.php?from=' . $from . '&to=' . $to . '&cabang=' . $cabang . '" class="btn btn-success form-control-sm mt--5 ml-1" target="_blank"><i class="fa fa-print"></i> Print</a>
                                                    ';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="printdivcontent">
                                <?php
                                if (isset($_POST['search'])) {
                                    $total_nominal = 0;
                                    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
                                    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
                                    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
                                    $query_akun = mysqli_query($conn, "
                                        SELECT * FROM tb_akun
                                        WHERE status_sj = 'aktif'
                                        ORDER BY id_akun ASC
                                    ");
                                    while ($row_akun = mysqli_fetch_array($query_akun)) {
                                        echo '
                                        <h4>' . $row_akun['bank'] . '</h4>
                                        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-5">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Total Pendapatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';

                                        $grand_total = 0;
                                        $query_keluar = mysqli_query($conn, "
                                                SELECT 
                                                b.tgl_transaksi,
                                                b.id_transaksi,
                                                b.id_cabang,
                                                p.metode,
                                                SUM(nominal) AS total_bayar
                                                FROM tb_barang_keluar b
                                                JOIN tb_pembayaran p ON p.id_pembayaran = b.id_transaksi
                                                WHERE b.tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "' AND b.id_cabang = '" . $cabang . "' AND b.status_keluar = 's' AND p.metode = '" . $row_akun['id_akun'] . "' GROUP BY b.tgl_transaksi
                                            ");
                                        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
                                            $total_bayar = 0;
                                            echo '
                                                <tr>
                                                    <td>' . date("d/m/Y", strtotime($row_keluar['tgl_transaksi'])) . '</td>
                                                    ';

                                            $query_bayar = mysqli_query($conn, "
                                                    SELECT 
                                                    b.tgl_transaksi,
                                                    b.id_transaksi,
                                                    b.id_cabang,
                                                    p.metode,
                                                    SUM(nominal) AS total_bayar
                                                    FROM tb_barang_keluar b
                                                    JOIN tb_pembayaran p ON p.id_pembayaran = b.id_transaksi
                                                    WHERE b.tgl_transaksi = '" . $row_keluar['tgl_transaksi'] . "' AND b.id_cabang = '" . $cabang . "' AND b.status_keluar = 's' AND p.metode = '" . $row_akun['id_akun'] . "' GROUP BY b.id_transaksi
                                                ");
                                            while ($row_bayar = mysqli_fetch_array($query_bayar)) {
                                                $query_bayaran = mysqli_query($conn, "SELECT SUM(nominal) AS total_bayar FROM tb_pembayaran WHERE id_pembayaran = '" . $row_bayar['id_transaksi'] . "'");
                                                $data_bayaran = mysqli_fetch_array($query_bayaran);
                                                $total_bayar = $total_bayar + $data_bayaran['total_bayar'];
                                            }
                                            $grand_total += $total_bayar;

                                            echo '
                                                    <td class="text-right">' . number_format($total_bayar) . '</td>
                                                </tr>
                                            ';
                                        }

                                        echo '
                                            <tr>
                                                <td><span style="font-weight:bold;">Grand Total</span></td>
                                                <td class="text-right"><span style="font-weight:bold;">'.number_format($grand_total).'</span></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        ';
                                    }
                                }
                                ?>
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