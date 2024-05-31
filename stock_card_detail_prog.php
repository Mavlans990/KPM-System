<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}
$from = date("Y-m-d");
$to = date("Y-m-d");
$nama_barang = "";
if (isset($_POST['nama_barang'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Inventory System</title>
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
                                <h1 class="h3 mb-2 text-gray-800">Stock Card Detail</h1>
                            </div>
                            <div class="">
                                <form action="" method="post">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group form-inline">
                                                <input type="date" name="tgl_from" id="" class="form-control form-control-sm" value="<?= $from; ?>">
                                                <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                                <input type="date" name="tgl_to" id="" class="form-control form-control-sm" value="<?= $to; ?>">
                                                <select class="form-control form-control-sm mt--5" name="nama_barang">
                                                    <option value="">-- Nama Barang --</option>
                                                    <?php
                                                    $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan ORDER BY nama_bahan ASC");
                                                    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
                                                        $selected = "";
                                                        if ($row_barang['id_bahan'] == $nama_barang) {
                                                            $selected = "selected";
                                                        }
                                                        echo '
                                                            <option value="' . $row_barang['id_bahan'] . '" ' . $selected . '>' . $row_barang['nama_bahan'] . '</option>
                                                        ';
                                                    }
                                                    ?>
                                                </select>
                                                <button type="submit" class="btn btn-primary form-control-sm mt--5" name="search"><i class="fa fa-search"></i> Cari</button>
                                                <?php
                                                if (isset($_POST['search'])) {
                                                    echo '
                                                    <a href="print/print_stock_card_detail.php?tgl_from=' . $from . '&tgl_to=' . $to . '&nama_barang=' . $nama_barang . '" class="btn btn-success text-white form-control-sm ml-1 mt--5" target="_blank"><i class="fa fa-print"></i> Print</a>
                                                    <a href="print/print_excel_stock_card_detail.php?tgl_from=' . $from . '&tgl_to=' . $to . '&nama_barang=' . $nama_barang . '" target="_blank" class="btn btn-warning text-white form-control-sm ml-1 mt--5"><i class="fa fa-list"></i> Download Excel</a>
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
                                    $where_barang = "";
                                    if ($nama_barang !== "") {
                                        $where_barang = " WHERE a.id_bahan = '" . $nama_barang . "'";
                                    }
                                    $no = 1;
                                    $sql_get_barang = mysqli_query($conn, "SELECT a.id_bahan,a.berat,a.uom,a.stock,b.total_qty,b.total_berat,b.nama_bahan FROM tb_stock a JOIN tb_bahan b ON b.id_bahan = a.id_bahan " . $where_barang . " ORDER BY b.nama_bahan ASC");
                                    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {

                                        $awal = 0;
                                        $sql_stock_masuk_awal = mysqli_query($conn, "SELECT SUM(qty) AS qty_awal_masuk FROM tb_barang_masuk WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi < '" . $from . "' AND status = 's'");
                                        while ($row_masuk_awal = mysqli_fetch_array($sql_stock_masuk_awal)) {
                                            $awal += $row_masuk_awal['qty_awal_masuk'];
                                        }
                                        $sql_stock_keluar_awal = mysqli_query($conn, "SELECT SUM(qty) AS qty_keluar_awal FROM tb_barang_keluar WHERE id_bahan = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi < '" . $from . "' AND status_keluar = 's' AND non_stock = '0'");
                                        while ($row_keluar_awal = mysqli_fetch_array($sql_stock_keluar_awal)) {
                                            $awal -= $row_keluar_awal['qty_keluar_awal'];
                                        }

                                        $sql_stock_opname = mysqli_query($conn, "SELECT stock_opname FROM inv_adjust_in WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND inv_date < '" . $from . "' AND status = 's'");
                                        while ($row_stock_opname = mysqli_fetch_array($sql_stock_opname)) {
                                            if ($row_stock_opname['stock_opname'] >= 0) {
                                                $awal += $row_stock_opname['stock_opname'];
                                            } else {
                                                $awal -= $row_stock_opname['stock_opname'];
                                            }
                                        }

                                        $ttl_masuk = 0;
                                        $sql_ttl_masuk = mysqli_query($conn, "SELECT SUM(qty) AS ttl_masuk FROM tb_barang_masuk WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "' AND status = 's'");
                                        while ($row_ttl_masuk = mysqli_fetch_array($sql_ttl_masuk)) {
                                            $ttl_masuk += $row_ttl_masuk['ttl_masuk'];
                                        }

                                        $sql_ttl_opname_masuk = mysqli_query($conn, "SELECT SUM(stock_opname) AS ttl_masuk FROM inv_adjust_in WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND inv_date BETWEEN '" . $from . "' AND '" . $to . "' AND status = 's' AND stock_opname >= 0");
                                        while ($row_opname_masuk = mysqli_fetch_array($sql_ttl_opname_masuk)) {
                                            $ttl_masuk += $row_opname_masuk['ttl_masuk'];
                                        }

                                        $ttl_keluar = 0;
                                        $sql_stock_keluar = mysqli_query($conn, "SELECT SUM(qty) AS ttl_keluar FROM tb_barang_keluar WHERE id_bahan = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "' AND status_keluar = 's' AND non_stock = '0'");
                                        while ($row_keluar = mysqli_fetch_array($sql_stock_keluar)) {
                                            $ttl_keluar += $row_keluar['ttl_keluar'];
                                        }

                                        $sql_ttl_opname_keluar = mysqli_query($conn, "SELECT SUM(stock_opname) AS ttl_keluar FROM inv_adjust_in WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND inv_date BETWEEN '" . $from . "' AND '" . $to . "' AND status = 's' AND stock_opname < 0");
                                        while ($row_opname_keluar = mysqli_fetch_array($sql_ttl_opname_keluar)) {
                                            $ttl_keluar += $row_opname_keluar['ttl_keluar'];
                                        }
                                        echo '
                                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                                <thead>
                                                    <tr class="bg-success">
                                                        <th class="text-center text-white">No.</th>
                                                        <th class="text-center text-white">Nama Barang</th>
                                                        <th class="text-center text-white">Yard</th>
                                                        <th class="text-center text-white">UOM</th>
                                                        <th class="text-center text-white">Stock Awal (Roll)</th>
                                                        <th class="text-center text-white">Masuk (roll)</th>
                                                        <th class="text-center text-white">Keluar (roll)</th>
                                                        <th class="text-center text-white">Sisa (roll)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">' . $no . '</td>
                                                        <td class="text-center">' . $row_barang['nama_bahan'] . '</td>
                                                        <td class="text-center">' . $row_barang['berat'] . '</td>
                                                        <td class="text-center">' . $row_barang['uom'] . '</td>
                                                        <td class="text-center">' . $awal . '</td>
                                                        <td class="text-center">' . $ttl_masuk . '</td>
                                                        <td class="text-center">' . $ttl_keluar . '</td>
                                                        <td class="text-center">' . ($awal + $ttl_masuk - $ttl_keluar) . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="8">
                                                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">Tgl</th>
                                                                        <th class="text-center">No. Transaksi</th>
                                                                        <th class="text-center">Masuk</th>
                                                                        <th class="text-center">Keluar</th>
                                                                        <th class="text-center">Total</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                ';

                                        $ttl_transaksi = $awal;
                                        $sql_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "' AND status = 's'");
                                        while ($row_barang_masuk = mysqli_fetch_array($sql_barang_masuk)) {
                                            $ttl_transaksi += $row_barang_masuk['qty'];
                                            echo '
                                                        <tr>
                                                            <td class="text-center">' . date("d-m-Y", strtotime($row_barang_masuk['tgl_transaksi'])) . '</td>
                                                            <td class="text-center">' . $row_barang_masuk['id_transaksi'] . '</td>
                                                            <td class="text-center">' . $row_barang_masuk['qty'] . '</td>
                                                            <td class="text-center">0</td>
                                                            <td class="text-center">' . $ttl_transaksi . '</td>
                                                        </tr>
                                                    ';
                                        }

                                        $sql_barang_keluar = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_bahan = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "' AND status_keluar = 's' AND non_stock = '0'");
                                        while ($row_barang_keluar = mysqli_fetch_array($sql_barang_keluar)) {
                                            $ttl_transaksi -= $row_barang_keluar['qty'];
                                            echo '
                                                <tr>
                                                    <td class="text-center">' . date("d-m-Y", strtotime($row_barang_keluar['tgl_transaksi'])) . '</td>
                                                    <td class="text-center">' . $row_barang_keluar['id_transaksi'] . '</td>
                                                    <td class="text-center">0</td>
                                                    <td class="text-center">' . $row_barang_keluar['qty'] . '</td>
                                                    <td class="text-center">' . $ttl_transaksi . '</td>
                                                </tr>
                                            ';
                                        }

                                        $sql_barang_opname = mysqli_query($conn, "SELECT * FROM inv_adjust_in WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND inv_date BETWEEN '" . $from . "' AND '" . $to . "' AND status = 's'");
                                        while ($row_barang_opname = mysqli_fetch_array($sql_barang_opname)) {
                                            if ($row_barang_opname['stock_opname'] >= 0) {
                                                $ttl_transaksi += $row_barang_opname['stock_opname'];
                                                echo '
                                                    <tr>
                                                        <td class="text-center">' . date("d-m-Y", strtotime($row_barang_opname['inv_date'])) . '</td>
                                                        <td class="text-center">' . $row_barang_opname['id_inv_in'] . '</td>
                                                        <td class="text-center">' . round($row_barang_opname['stock_opname']) . '</td>
                                                        <td class="text-center">0</td>
                                                        <td class="text-center">' . $ttl_transaksi . '</td>
                                                    </tr>
                                                ';
                                            } else {
                                                $ttl_transaksi -= $row_barang_opname['stock_opname'];
                                                echo '
                                                    <tr>
                                                        <td class="text-center">' . date("d-m-Y", strtotime($row_barang_opname['inv_date'])) . '</td>
                                                        <td class="text-center">' . $row_barang_opname['id_inv_in'] . '</td>
                                                        <td class="text-center">0</td>
                                                        <td class="text-center">' . round($row_barang_opname['stock_opname']) . '</td>
                                                        <td class="text-center">' . $ttl_transaksi . '</td>
                                                    </tr>
                                                ';
                                            }
                                        }

                                        echo '
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                ';

                                        echo '
                                                </tbody>
                                            </table>
                                                ';

                                        $sql_update_stock = mysqli_query($conn, "UPDATE tb_stock SET stock = '" . ($awal + $ttl_masuk - $ttl_keluar) . "' WHERE id_bahan = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "'");
                                        $no++;
                                    }

                                    $sql_update_bahan = mysqli_query($conn, "SELECT id_bahan FROM tb_bahan");
                                    while ($row_bahan = mysqli_fetch_array($sql_update_bahan)) {
                                        $id_bahan = $row_bahan['id_bahan'];
                                        $total_qty = 0;
                                        $total_berat = 0;
                                        $sql_get_stock = mysqli_query($conn, "
                                            SELECT 
                                                COALESCE(SUM(stock),0) AS total_qty,
                                                COALESCE(SUM(stock * berat),0) AS total_berat
                                            FROM
                                                tb_stock
                                            WHERE
                                                id_bahan = '" . $id_bahan . "'
                                        ");
                                        while ($row_stock = mysqli_fetch_array($sql_get_stock)) {
                                            $total_qty += $row_stock['total_qty'];
                                            $total_berat += $row_stock['total_berat'];
                                        }

                                        $sql_update_total_bahan = mysqli_query($conn, "
                                            UPDATE
                                                tb_bahan
                                            SET
                                                total_qty = '" . $total_qty . "',
                                                total_berat = '" . $total_berat . "'
                                            WHERE
                                                id_bahan = '" . $id_bahan . "'
                                        ");
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