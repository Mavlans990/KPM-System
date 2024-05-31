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
$non_stock = "";
if (isset($_GET['tgl_to'])) {
    $from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
    $supp = mysqli_real_escape_string($conn, $_GET['supplier']);
    $bah = mysqli_real_escape_string($conn, $_GET['bahan']);
    $non_stock = mysqli_real_escape_string($conn, $_GET['nonstock']);

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
                <h3>LAPORAN BARANG KELUAR</h3>
                <h6><?= date('d/m/Y', strtotime($from)) ?> Sampai <?= date('d/m/Y', strtotime($to)) ?><br>
                    <!-- CUSTOMER: <?= $nama_customer ?> <br> -->
                    BAHAN: <?= $nama_bahan ?><br>
                    <?php
                    if ($non_stock !== "") {
                        if ($non_stock == 1) {
                            echo "
                            Tipe : Non-Stock
                        ";
                        } else {
                            echo "
                            Tipe : Stock
                        ";
                        }
                    }
                    ?><br></h6>
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
                                <th class="text-right">Qty</th>
                                <th class="text-center">UOM</th>
                                <th class="text-right">Price</th>
                                <th class="text-right">Total</th>
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
                                $non_stock = mysqli_real_escape_string($conn, $_GET['nonstock']);

                                $filter_supplier = "";
                                $filter_bahan = "";
                                $filter_cabang = "";
                                $filter_non_stock = "";

                                if ($supplier !== "") {
                                    $supp = "";
                                    $query_supp = mysqli_query($conn, "SELECT id_customer,nama_customer FROM tb_customer WHERE nama_customer LIKE '%" . $supplier . "%'");
                                    while ($row_supp = mysqli_fetch_array($query_supp)) {
                                        $supp = $supp . ",'" . $row_supp['id_customer'] . "'";
                                    }
                                    $filter_supplier = " AND id_customer IN (''" . $supp . ")";
                                }
                                if ($bahan !== "") {
                                    $filter_bahan = " AND id_bahan = '" . $bahan . "'";
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
                                } else {
                                    $filter_cabang = "";
                                }

                                if ($non_stock !== "") {
                                    $filter_non_stock = " and non_stock = '" . $non_stock . "'";
                                }

                                $grand_total = 0;

                                $source = "";

                                // .$where_branch
                                $select_keluar = "SELECT * FROM tb_barang_keluar WHERE (tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "') " . $filter_supplier . " " . $filter_bahan . " " . $filter_cabang . " " . $filter_non_stock . " and status_keluar <> 'd' GROUP BY id_transaksi";
                                $query_keluar = mysqli_query($conn, $select_keluar);
                                while ($row_keluar = mysqli_fetch_array($query_keluar)) {

                                    // .$where_branch
                                    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $row_keluar['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang  . $filter_non_stock;
                                    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
                                    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);

                                    $select_supplier = "SELECT * FROM tb_customer WHERE id_customer = '" . $row_keluar['id_customer'] . "'";
                                    $query_supplier = mysqli_query($conn, $select_supplier);
                                    $data_supplier = mysqli_fetch_array($query_supplier);

                                    $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_keluar['id_cabang'] . "'";
                                    $query_cabang = mysqli_query($conn, $select_cabang);
                                    $data_cabang = mysqli_fetch_array($query_cabang);

                                    if ($row_keluar['source_customer'] == "facebook") {
                                        $source = "Source : Facebook <br>";
                                    }
                                    if ($row_keluar['source_customer'] == "instagram") {
                                        $source = "Source : Instagram <br>";
                                    }
                                    if ($row_keluar['source_customer'] == "google") {
                                        $source = "Source : Google <br>";
                                    }
                                    if ($row_keluar['source_customer'] == "marketplace") {
                                        $source = "Source : Market Place <br>";
                                    }
                                    if ($row_keluar['source_customer'] == "olx") {
                                        $source = "Source : OLX <br>";
                                    }
                                    if ($row_keluar['source_customer'] == "youtube") {
                                        $source = "Source : Youtube <br>";
                                    }
                                    if ($row_keluar['source_customer'] == "walk_in") {
                                        $source = "Source : Walk In <br>";
                                    }
                                    if ($row_keluar['source_customer'] == "referensi") {
                                        $source = "Source : Referensi <br>";
                                    }
                                    if ($row_keluar['source_customer'] == "repeat_order") {
                                        $source = "Source : Repeat Order <br>";
                                    }
                                    if ($row_keluar['source_customer'] == "club_mobil") {
                                        $source = "Source : Club Mobil <br>";
                                    }

                                    $tipe_stock = "<span>Tipe : Stock</span>";
                                    if ($row_keluar['non_stock'] == 1) {
                                        $tipe_stock = "<span>Tipe : Non-Stock</span>";
                                    }

                                    if($row_keluar["jenis_transaksi"] == "penjualan"){
                                        $jns = "(Penjualan) ";
                                    }elseif($row_keluar["jenis_transaksi"] == "return"){
                                        $jns = "(Return) ";
                                    }else{
                                        $jns = " ";
                                    }

                                    echo '
                                        <tr>
                                            <td class="text-center" rowspan="' . $jum_barang_keluar . '">' . date("d/m/Y", strtotime($row_keluar['tgl_transaksi'])) . '</td>
                                            <td class="text-center" rowspan="' . $jum_barang_keluar . '">
                                                ' . $row_keluar['id_transaksi'] . ' ' . $jns .'<br>  
                                                ' . $data_supplier['nama_customer'] . '<br>
                                                ' . $source . ' 
                                                ' . $tipe_stock . '
                                            </td>
                                            ';

                                    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {

                                        $query_bahan = mysqli_query($conn, "
                                            SELECT nama_bahan
                                            FROM tb_bahan 
                                            WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'
                                        ");
                                        $data_bahan = mysqli_fetch_array($query_bahan);

                                        echo '
                                                    <td class="text-center">' . $data_bahan['nama_bahan'] . '</td>
                                                    <td class="text-right">' . $row_barang_keluar['qty'] . ' Roll</td>
                                                    <td class="text-center">'. number_format($row_barang_keluar['berat']) . ' ' . $row_barang_keluar['uom'] . '</td>
                                                    <td class="text-left"><span style="float:right">' . number_format($row_barang_keluar['harga']) . '</span></td>
                                                    <td class="text-left"><span style="float:right">' . number_format($row_barang_keluar['total']) . '</span></td>
                                                </tr>
                                            ';
                                    }
                                    // .$where_branch
                                    $select_total = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $row_keluar['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang ;
                                    $query_total = mysqli_query($conn, $select_total);
                                    $data_total = mysqli_fetch_array($query_total);

                                    $total = $data_total['total'];
                                    $ppn = $total * $row_keluar['ppn'] / 100;

                                    echo '
                                        <tr class="bg-primary text-white">
                                            <th colspan="2" class="text-right text-right text-white">Sub Total</th>
                                            <td class="text-left"><span style="float:right">' . number_format($total) . '</span></td>
                                            <th class="text-center text-white">PPN ' . $row_keluar['ppn'] . ' %</th>
                                            <td class="text-left"><span style="float:right">' . number_format($ppn) . '</span></td>
                                            <th class="text-right text-white">Total</th>
                                            <td class="text-left"><span style="float:right">' . number_format($total + $ppn) . '</span></td>
                                        </tr>
                                    ';

                                    $grand_total = $grand_total + ($total + $ppn);
                                }

                                echo '
                                    <tr class="bg-success text-white">
                                        <th colspan="2" class="text-right text-white">Grand Total</th>
                                        <th colspan="4" class="text-right text-white"></th>
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