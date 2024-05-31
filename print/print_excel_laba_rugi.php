<?php
include "../lib/koneksi.php";
session_start();

$from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
$to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
// $supp = mysqli_real_escape_string($conn, $_GET['supplier']);
// $bah = mysqli_real_escape_string($conn, $_GET['bahan']);

$from = date("Y-m-d");
$to = "";
$supp = "";
$bah = "";
$nama_customer = '';
$nama_bahan = '';
if (isset($_GET['tgl_to'])) {
    $from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
    $cabang = mysqli_real_escape_string($conn, $_GET['cabang']);
}
$query_cabang = mysqli_query($conn, "
    SELECT 
    id_cabang,
    nama_cabang
    FROM tb_cabang
    WHERE id_cabang = '" . $cabang . "'
");
$data_cabang = mysqli_fetch_array($query_cabang);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Coolplus System</title>
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
    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan Laba Rugi (Cabang " . $data_cabang['nama_cabang'] . ") (" . date("d M Y") . ").xls");

    ?>
</head>

<body>





    <table border="1" class="table table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
        <?php


        $tgl_1 = mysqli_real_escape_string($conn, $_GET['tgl_from']);
        $tgl_2 = mysqli_real_escape_string($conn, $_GET['tgl_to']);
        $cabang = mysqli_real_escape_string($conn, $_GET['cabang']);


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

        if ($cabang != "") {
            $filter_biaya = " AND cabang = '" . $cabang . "'";
        }
        $select_biaya = "SELECT * FROM tb_operasional WHERE tgl_buat BETWEEN '" . $tgl_1 . "' AND '" . $tgl_2 . "' " . $filter_biaya;
        $query_biaya = mysqli_query($conn, $select_biaya);
        while ($row_biaya = mysqli_fetch_array($query_biaya)) {
            $total_biaya = $total_biaya + $row_biaya['total'];
        }

        echo '
                                            <tr>
                                                <th colspan="2">
                                                    <b style="font-weight:bold;">PT. Inovasi Sukses Sejahtera</b><br>
                                                    <b style="font-weight:bold;">Cabang ' . $data_cabang['nama_cabang'] . '</b><br>
                                                    <b style="font-weight:bold;">Dari ' . date("d M Y", strtotime($from)) . ' Sampai ' . date("d M Y", strtotime($to)) . '</b>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>Pendapatan Penjualan</td>
                                                <td>' . number_format($total_penjualan) . '</td>
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
                                                        <td>Pendapatan Penjualan Mutasi</td>
                                                        <td>' . number_format($pendapatan_mutasi) . '</td>
                                                    </tr>
                                                    ';
        }

        echo '
                                            <tr>
                                              <th style="border-bottom:2px solid black;">
                                                <h4 style="font-weight:bold;">Total Pendapatan</h4>
                                              </th>
                                              <th style="border-bottom:2px solid black;">
                                                <h4 style="font-weight:bold;text-align:right">' . number_format($total_penjualan + $pendapatan_mutasi) . '</h4>
                                              </th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p style="margin-left:1rem;">Harga Pokok Penjualan</p>
                                                </td>
                                                <td>
                                                    (' . number_format($total_modal) . ')
                                                </td>
                                            </tr>
                                            ';

        if ($jum_inv_out > 0) {
            echo '
                                                <tr>
                                                    <td>
                                                        <p style="margin-left:1rem;">Harga Pokok Mutasi</p>
                                                    </td>
                                                    <td>
                                                        (' . number_format($penjualan_mutasi) . ')
                                                    </td>
                                                </tr>
                                                ';
        }

        echo '
                                            <tr>
                                              <th style="border-bottom:2px solid black;">
                                                <h4 style="font-weight:bold;">Total Pokok Penjualan</h4>
                                              </th>
                                              <th style="border-bottom:2px solid black;">
                                              <h4 style="font-weight:bold;text-align:right;">(' . number_format($total_modal + $penjualan_mutasi) . ')</h4>
                                              </th>
                                            </tr>
                                            <tr>
                                                <th style="background-color:#e7e7de;">
                                                    <h4 style="font-size:1.5rem;">Total Laba Kotor</h4>
                                                </th>
                                                <th style="background-color:#e7e7de;">
                                                <h4 style="font-size:1.5rem;text-align:right;">' . number_format(($total_penjualan + $pendapatan_mutasi) - ($total_modal + $penjualan_mutasi)) . '</h4>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="2">
                                                    <b style="font-weight:bold;">Beban Operasional</b>
                                                </th>
                                                
                                            </tr>
                                        ';

        $select_biaya = "SELECT * FROM tb_operasional WHERE tgl_buat BETWEEN '" . $tgl_1 . "' AND '" . $tgl_2 . "' " . $filter_biaya;
        $query_biaya = mysqli_query($conn, $select_biaya);
        while ($row_biaya = mysqli_fetch_array($query_biaya)) {
            echo '
                                            <tr>
                                                <td>
                                                    <p style="margin-left:1rem;">' . $row_biaya['nama_beban'] . '</p>
                                                </td>
                                                <td style="text-align:right;">
                                                ' . number_format($row_biaya['total']) . '
                                                </td>
                                            </tr>
                                        ';
        }
        echo '
                                                <tr>
                                                    <th style="border-bottom:2px solid black;">
                                                        <b style="font-weight:bold;">Total Beban Operasional</b>
                                                    </th>
                                                    <th style="border-bottom:2px solid black;">
                                                        <b style="font-weight:bold;">(' . number_format($total_biaya) . ')</b>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="background-color:#e7e7de;">
                                                        <h4 style="font-size:1.5rem;">Laba Bersih Operasional</h4>
                                                    </th>
                                                    <th style="background-color:#e7e7de;">
                                                        <h4 style="font-size:1.5rem;">(' . number_format($total_biaya) . ')</h4>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="background-color:#e7e7de;border-top:2px solid black;border-bottom:2px solid black;">
                                                        <h4 style="font-size:1.5rem;">Laba/(Rugi) Bersih</h4>
                                                    </th>
                                                    <th style="background-color:#e7e7de;border-top:2px solid black;border-bottom:2px solid black;">
                                                        <h4 style="font-size:1.5rem;">' . number_format(($total_penjualan + $pendapatan_mutasi) - ($total_modal + $penjualan_mutasi) - $total_biaya) . '</h4>
                                                    </th>
                                                </tr>
                                                ';

        ?>
    </table>



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