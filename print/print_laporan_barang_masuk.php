<?php
session_start();
include "../lib/koneksi.php";
include "../lib/format.php";
include "../lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$from = date("Y-m-d");
$to = "";
$supp = "";
$bah = "";
$nama_customer = '';
$nama_bahan = '';
$cabang = '';
if (isset($_GET['tgl_to'])) {
    $from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
    $supp = mysqli_real_escape_string($conn, $_GET['supplier']);
    $bah = mysqli_real_escape_string($conn, $_GET['bahan']);

    // if ($supp != '') {
    //     $select_supplier = "SELECT nama_customer FROM tb_customer where id_customer = '" . $supp . "' ";
    //     $query_supplier = mysqli_query($conn, $select_supplier);
    //     $row_supplier = mysqli_fetch_array($query_supplier);
    //     $nama_customer = $row_supplier['nama_customer'];
    // }

    if ($bah != '') {
        $select_bahan = "SELECT nama_bahan FROM tb_bahan where id_bahan = '" . $bah . "' ";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $row_bahan = mysqli_fetch_array($query_bahan);
        $nama_bahan = $row_bahan['nama_bahan'];
    } else {
        $nama_bahan = "All";
    }
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
                <h3>LAPORAN BARANG MASUK</h3>
                <h6><?= date('d/m/Y', strtotime($from)) ?> Sampai <?= date('d/m/Y', strtotime($to)) ?><br>
                    <!-- SUPPLIER: <?= $nama_customer ?> <br> -->
                    BAHAN: <?= $nama_bahan ?><br></h6>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="table-responsive" id="printdivcontent">
                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">No. Transaksi</th>
                                <th class="text-center">Item</th>
                                <th class="text-center">Qty (Roll)</th>
                                <th class="text-center">Yard</th>
                                <th class="text-center">UOM</th>
                                <th class="text-center">Price</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($_GET['tgl_from'])) {
                                $where_branch = "";
                                if ($_SESSION['group'] !== "super") {
                                    $where_branch = " and id_cabang = '" . $_SESSION['branch'] . "'";
                                }

                                $tgl_from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
                                $tgl_to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
                                $supplier = mysqli_real_escape_string($conn, $_GET['supplier']);
                                $bahan = mysqli_real_escape_string($conn, $_GET['bahan']);
                                $cabang = mysqli_real_escape_string($conn, $_GET['cabang']);

                                $filter_supplier = "";
                                $filter_bahan = "";
                                $filter_cabang = "";

                                if ($supplier !== "") {
                                    $filter_supplier = " AND id_supplier = '".$supplier."'";
                                }
                                if ($bahan !== "") {
                                    $filter_bahan = " AND id_product = '" . $bahan . "'";
                                }

                                if ($cabang != "" && $cabang > 0) {
                                    $array = explode("|", $cabang);
                                    $jml = count($array);
                                    for ($i = 0; $i < $jml; $i++) {
                                        if ($i == 0) {
                                            $cabangasli = "'" . $array[0] . "'";
                                        } else {
                                            $cabangasli = $cabangasli . ",'" . $array[$i] . "'";
                                        }
                                    }

                                    $filter_cabang = " and id_cabang in (" . $cabangasli . ") ";
                                }

                                // echo'<td>'.$jml.'</td>';

                                $grand_total = 0;

                                $select_masuk = "SELECT * FROM tb_barang_masuk WHERE (tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "') " . $filter_supplier . " " . $filter_bahan . " " . $filter_cabang . " " . $where_branch . " and status <> 'd'  GROUP BY id_transaksi";
                                $query_masuk = mysqli_query($conn, $select_masuk);
                                while ($row_masuk = mysqli_fetch_array($query_masuk)) {

                                    $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $row_masuk['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang . $where_branch;
                                    $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
                                    $jum_barang_masuk = mysqli_num_rows($query_barang_masuk);

                                    $select_supplier = "SELECT * FROM tb_customer WHERE id_customer = '" . $row_masuk['id_supplier'] . "'";
                                    $query_supplier = mysqli_query($conn, $select_supplier);
                                    $data_supplier = mysqli_fetch_array($query_supplier);


                                    echo '
                                        <tr>
                                            <td class="text-center" rowspan="' . $jum_barang_masuk . '">' . date("d/m/Y", strtotime($row_masuk['tgl_transaksi'])) . '</td>
                                            <td class="text-center" rowspan="' . $jum_barang_masuk . '">
                                                ' . $row_masuk['id_transaksi'] . '<br>  
                                                ' . $data_supplier['nama_customer'] . '<br>
                                            </td>
                                            ';

                                    while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
                                        $select_bahan = "SELECT id_bahan,nama_bahan 
                                                        FROM tb_bahan 
                                                        WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "'";
                                        $query_bahan = mysqli_query($conn, $select_bahan);
                                        $data_bahan = mysqli_fetch_array($query_bahan);
                                        echo '
                                                    <td class="text-center">' . $data_bahan['nama_bahan'] . '</td>
                                                    <td class="text-right">' . number_format($row_barang_masuk['qty']) . '</td>
                                                    <td class="text-right">' . number_format($row_barang_masuk['berat']) . '</td>
                                                    <td class="text-center">' . $row_barang_masuk['uom'] . '</td>
                                                    <td class="text-left"><span style="float:right">' . number_format($row_barang_masuk['harga']) . '</span></td>
                                                    <td class="text-left"><span style="float:right">' . number_format($row_barang_masuk['total']) . '</span></td>
                                                </tr>
                                            ';
                                    }

                                    $select_total = "SELECT SUM(total) AS total FROM tb_barang_masuk WHERE id_transaksi = '" . $row_masuk['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang . $where_branch;
                                    $query_total = mysqli_query($conn, $select_total);
                                    $data_total = mysqli_fetch_array($query_total);

                                    $total = $data_total['total'];
                                    $ppn = $total * $row_masuk['ppn'] / 100;

                                    echo '
                                        <tr class="">
                                            <th colspan="2" class="text-right text-right">Sub Total</th>
                                            <td class="text-left"><span style="float:right">' . number_format($total) . '</span></td>
                                            <th class="text-center">PPN ' . $row_masuk['ppn'] . ' %</th>
                                            <td class="text-left" colspan="2"><span style="float:right">' . number_format($ppn) . '</span></td>
                                            <th class="text-right">Total</th>
                                            <td class="text-left"><span style="float:right">' . number_format($total + $ppn) . '</span></td>
                                        </tr>
                                    ';

                                    $grand_total = $grand_total + ($total + $ppn);
                                }

                                echo '
                                    <tr class="">
                                        <th colspan="2" class="text-right">Grand Total</th>
                                        <th colspan="5" class="text-right"></th>
                                        <td class="text-left"><span style="float:right">' . number_format($grand_total) . '</span></td>
                                    </tr>
                                ';
                            }
                            ?>
                        </tbody>
                    </table>
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