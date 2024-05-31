<?php
session_start();
include "../lib/koneksi.php";
include "../lib/format.php";
include "../lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}




$tgl_from = date("Y-m-d");
$tgl_to = date("Y-m-d");
$nama_barang = "";
if (isset($_GET['nama_barang'])) {
    $tgl_from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
    $tgl_to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
    $nama_barang = mysqli_real_escape_string($conn, $_GET['nama_barang']);

    if ($bah !== '') {
        $select_bahan = "SELECT nama_bahan FROM tb_bahan where id_bahan = '" . $nama_barang . "' ";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $row_bahan = mysqli_fetch_array($query_bahan);
        $nama_bahan = $row_bahan['nama_bahan'];
    } else {
        $nama_bahan = "Semua";
    }
}
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan Stock Card Detail.xls");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Inventory System</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="../favicon.ico">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="../vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="../vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- select2 CSS -->
    <link href="../vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="../vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="../vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="../dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body id="page-top">



    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h3>LAPORAN STOCK CARD DETAIL</h3>
                <h6>Dari <?= date("d/m/Y", strtotime($tgl_from)) ?> Sampai <?= date("d/m/Y", strtotime($tgl_to)) ?></h6>
                <h6>BAHAN: <?= $nama_bahan; ?></h6>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="table-responsive" id="printdivcontent">
                    <?php
                    $where_barang = "";
                    if ($nama_barang !== "") {
                        $where_barang = " WHERE a.id_bahan = '" . $nama_barang . "'";
                    }
                    $no = 1;
                    $sql_get_barang = mysqli_query($conn, "SELECT a.id_bahan,a.berat,a.uom,a.stock,b.total_qty,b.total_berat,b.nama_bahan FROM tb_stock a JOIN tb_bahan b ON b.id_bahan = a.id_bahan " . $where_barang . " ORDER BY b.nama_bahan ASC");
                    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {

                        $awal = 0;
                        $sql_stock_masuk_awal = mysqli_query($conn, "SELECT SUM(qty) AS qty_awal_masuk FROM tb_barang_masuk WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi < '" . $tgl_from . "' AND status = 's'");
                        while ($row_masuk_awal = mysqli_fetch_array($sql_stock_masuk_awal)) {
                            $awal += $row_masuk_awal['qty_awal_masuk'];
                        }
                        $sql_stock_keluar_awal = mysqli_query($conn, "SELECT SUM(qty) AS qty_keluar_awal FROM tb_barang_keluar WHERE id_bahan = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi < '" . $tgl_from . "' AND status_keluar = 's' AND non_stock = '0'");
                        while ($row_keluar_awal = mysqli_fetch_array($sql_stock_keluar_awal)) {
                            $awal -= $row_keluar_awal['qty_keluar_awal'];
                        }

                        $sql_stock_opname = mysqli_query($conn, "SELECT stock_opname FROM inv_adjust_in WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND inv_date < '" . $tgl_from . "' AND status = 's'");
                        while ($row_stock_opname = mysqli_fetch_array($sql_stock_opname)) {
                            if ($row_stock_opname['stock_opname'] >= 0) {
                                $awal += $row_stock_opname['stock_opname'];
                            } else {
                                $awal -= $row_stock_opname['stock_opname'];
                            }
                        }

                        $ttl_masuk = 0;
                        $sql_ttl_masuk = mysqli_query($conn, "SELECT SUM(qty) AS ttl_masuk FROM tb_barang_masuk WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND status = 's'");
                        while ($row_ttl_masuk = mysqli_fetch_array($sql_ttl_masuk)) {
                            $ttl_masuk += $row_ttl_masuk['ttl_masuk'];
                        }

                        $sql_ttl_opname_masuk = mysqli_query($conn, "SELECT SUM(stock_opname) AS ttl_masuk FROM inv_adjust_in WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND inv_date BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND status = 's' AND stock_opname >= 0");
                        while ($row_opname_masuk = mysqli_fetch_array($sql_ttl_opname_masuk)) {
                            $ttl_masuk += $row_opname_masuk['ttl_masuk'];
                        }

                        $ttl_keluar = 0;
                        $sql_stock_keluar = mysqli_query($conn, "SELECT SUM(qty) AS ttl_keluar FROM tb_barang_keluar WHERE id_bahan = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND status_keluar = 's' AND non_stock = '0'");
                        while ($row_keluar = mysqli_fetch_array($sql_stock_keluar)) {
                            $ttl_keluar += $row_keluar['ttl_keluar'];
                        }

                        $sql_ttl_opname_keluar = mysqli_query($conn, "SELECT SUM(stock_opname) AS ttl_keluar FROM inv_adjust_in WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND inv_date BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND status = 's' AND stock_opname < 0");
                        while ($row_opname_keluar = mysqli_fetch_array($sql_ttl_opname_keluar)) {
                            $ttl_keluar += $row_opname_keluar['ttl_keluar'];
                        }
                        echo '
                                    <br>
                                    <table border="1">
                                    <thead>
                                        <tr class="bg-success">
                                            <th class="text-center text-white">No.</th>
                                            <th class="text-center text-white">Nama Barang</th>
                                            <th class="text-center text-white">Berat</th>
                                            <th class="text-center text-white">UOM</th>
                                            <th class="text-center text-white">Stock Awal</th>
                                            <th class="text-center text-white">Masuk</th>
                                            <th class="text-center text-white">Keluar</th>
                                            <th class="text-center text-white">Sisa</th>
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
                                                <table border="1">
                                                    <thead>
                                                        <tr class="bg-success">
                                                            <th class="text-center text-white" colspan="2">Tgl</th>
                                                            <th class="text-center text-white" colspan="3">No. Transaksi</th>
                                                            <th class="text-center text-white">Masuk</th>
                                                            <th class="text-center text-white">Keluar</th>
                                                            <th class="text-center text-white">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    ';

                        $ttl_transaksi = $awal;
                        $sql_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND status = 's'");
                        while ($row_barang_masuk = mysqli_fetch_array($sql_barang_masuk)) {
                            $ttl_transaksi += $row_barang_masuk['qty'];
                            echo '
                                            <tr>
                                                <td class="text-center" colspan="2">' . date("d-m-Y", strtotime($row_barang_masuk['tgl_transaksi'])) . '</td>
                                                <td class="text-center" colspan="3">' . $row_barang_masuk['id_transaksi'] . '</td>
                                                <td class="text-center">' . $row_barang_masuk['qty'] . '</td>
                                                <td class="text-center">0</td>
                                                <td class="text-center">' . $ttl_transaksi . '</td>
                                            </tr>
                                        ';
                        }

                        $sql_barang_keluar = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_bahan = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND status_keluar = 's' AND non_stock = '0'");
                        while ($row_barang_keluar = mysqli_fetch_array($sql_barang_keluar)) {
                            $ttl_transaksi -= $row_barang_keluar['qty'];
                            echo '
                                    <tr>
                                        <td class="text-center" colspan="2">' . date("d-m-Y", strtotime($row_barang_keluar['tgl_transaksi'])) . '</td>
                                        <td class="text-center" colspan="3">' . $row_barang_keluar['id_transaksi'] . '</td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">' . $row_barang_keluar['qty'] . '</td>
                                        <td class="text-center">' . $ttl_transaksi . '</td>
                                    </tr>
                                ';
                        }

                        $sql_barang_opname = mysqli_query($conn, "SELECT * FROM inv_adjust_in WHERE id_product = '" . $row_barang['id_bahan'] . "' AND berat = '" . $row_barang['berat'] . "' AND uom = '" . $row_barang['uom'] . "' AND inv_date BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND status = 's'");
                        while ($row_barang_opname = mysqli_fetch_array($sql_barang_opname)) {
                            if ($row_barang_opname['stock_opname'] >= 0) {
                                $ttl_transaksi += $row_barang_opname['stock_opname'];
                                echo '
                                        <tr>
                                            <td class="text-center" colspan="2">' . date("d-m-Y", strtotime($row_barang_opname['inv_date'])) . '</td>
                                            <td class="text-center" colspan="3">' . $row_barang_opname['id_inv_in'] . '</td>
                                            <td class="text-center">' . round($row_barang_opname['stock_opname']) . '</td>
                                            <td class="text-center">0</td>
                                            <td class="text-center">' . $ttl_transaksi . '</td>
                                        </tr>
                                    ';
                            } else {
                                $ttl_transaksi -= $row_barang_opname['stock_opname'];
                                echo '
                                    <tr>
                                        <td class="text-center" colspan="2">' . date("d-m-Y", strtotime($row_barang_opname['inv_date'])) . '</td>
                                        <td class="text-center" colspan="3">' . $row_barang_opname['id_inv_in'] . '</td>
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
                        $no++;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>


    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="../dist/js/jquery.slimscroll.js"></script>



    <!-- Data Table JavaScript -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../vendors/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../vendors/pdfmake/build/vfs_fonts.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../dist/js/dataTables-data.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="../dist/js/feather.min.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="../dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Toggles JavaScript -->
    <script src="../vendors/jquery-toggles/toggles.min.js"></script>
    <script src="../dist/js/toggle-data.js"></script>

    <!-- Select2 JavaScript -->
    <script src="../vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="../dist/js/select2-data.js"></script>

    <!-- Init JavaScript -->
    <script src="../dist/js/init.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../js/demo/datatables-demo.js"></script>

</body>

</html>
<script type="text/javascript">
    window.print();
</script>