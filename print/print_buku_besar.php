<?php
session_start();
include "../lib/koneksi.php";
include "../lib/format.php";
include "../lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
}

$from = date("Y-m-d");
$to = date("Y-m-d");
$cabang = "";
if (isset($_GET['from'])) {
    $from = mysqli_real_escape_string($conn, $_GET['from']);
    $to = mysqli_real_escape_string($conn, $_GET['to']);
    $cabang = mysqli_real_escape_string($conn, $_GET['cabang']);
}

$query_cabang = mysqli_query($conn, "
    SELECT * FROM tb_cabang WHERE id_cabang = '" . $cabang . "'
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
</head>

<body id="page-top">



    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h3>BUKU BESAR</h3>
                <h6>Cabang <?= $data_cabang['nama_cabang']; ?><br>
                    <?= date('d/m/Y', strtotime($from)) ?> Sampai <?= date('d/m/Y', strtotime($to)) ?><br>
                    <br>
                </h6>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="table-responsive" id="printdivcontent">
                    <?php
                    $total_nominal = 0;
                    $from = mysqli_real_escape_string($conn, $_GET['from']);
                    $to = mysqli_real_escape_string($conn, $_GET['to']);
                    $cabang = mysqli_real_escape_string($conn, $_GET['cabang']);
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

                            echo '
                                                    <td class="text-right">' . number_format($total_bayar) . '</td>
                                                </tr>
                                            ';
                        }

                        echo '
                            </tbody>
                        </table>
                        ';
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