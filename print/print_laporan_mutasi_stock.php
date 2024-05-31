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
$dari = "";
$ke = "";
$bahan = "";
if (isset($_GET['tgl_from'])) {
    if ($_GET['tgl_from'] !== "") {
        $from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
    }
}
if (isset($_GET['tgl_to'])) {
    if ($_GET['tgl_to'] !== "") {
        $to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
    }
}
if (isset($_GET['dari'])) {
    if ($_GET['dari'] !== "") {
        $dari = mysqli_real_escape_string($conn, $_GET['dari']);
    }
}
if (isset($_GET['ke'])) {
    if ($_GET['ke'] !== "") {
        $ke = mysqli_real_escape_string($conn, $_GET['ke']);
    }
}
if (isset($_GET['bahan'])) {
    if ($_GET['bahan'] !== "") {
        $bahan = mysqli_real_escape_string($conn, $_GET['bahan']);
    }
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
                <h3>MUTASI STOCK</h3>
                <h6><?= date('d/m/Y', strtotime($from)) ?> Sampai <?= date('d/m/Y', strtotime($to)) ?></h6>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="table-responsive" id="printdivcontent">
                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Nama Barang</th>
                                <th class="text-center">Qty</th>
                                <th class="text-right">Biaya Jual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $where_branch = "";
                            if ($_SESSION['group'] !== "super") {
                                $where_branch = " and id_cabang = '" . $_SESSION['branch'] . "'";
                            }

                            $tgl_from = $from;
                            $tgl_to = $to;
                            $dari_cabang = $dari;
                            $ke_cabang = $ke;
                            $filter_cabang = str_replace(",", "','", $_SESSION['branch']);

                            if ($_SESSION['group'] == "franchise") {
                                $filter_dari = " AND i.id_branch IN('" . $filter_cabang . "')";
                                $filter_ke = " AND o.id_branch IN('" . $filter_cabang . "')";
                                if ($dari_cabang !== "") {
                                    $filter_dari = " AND i.id_branch = '" . $dari_cabang . "'";
                                }

                                if ($ke_cabang !== "") {
                                    $filter_ke = " AND o.id_branch = '" . $ke_cabang . "'";
                                }
                            } else {
                                $filter_dari = "";
                                $filter_ke = "";
                                if ($dari_cabang !== "") {
                                    $filter_dari = " AND i.id_branch = '" . $dari_cabang . "'";
                                }

                                if ($ke_cabang !== "") {
                                    $filter_ke = " AND o.id_branch = '" . $ke_cabang . "'";
                                }
                            }

                            $filter_bahan = "";
                            $filter_bahan2 = "";
                            if ($bahan !== "") {
                                $filter_bahan = " AND id_product = '" . $bahan . "'";
                                $filter_bahan2 = " AND i.id_product = '" . $bahan . "'";
                            }

                            $select_transfer = "SELECT 
                                                                    i.inv_out_id,
                                                                    i.id_inv_out,
                                                                    i.inv_date,
                                                                    i.id_product,
                                                                    i.stock_out,
                                                                    i.biaya,
                                                                    i.create_by,
                                                                    i.id_branch AS 'from',
                                                                    o.id_branch AS 'to',
                                                                    k.user_id,
                                                                    k.nama_lengkap
                                                                    FROM inv_adjust_out i
                                                                    JOIN inv_adjust_in o ON o.id_inv_in = i.id_inv_out
                                                                    JOIN tb_karyawan k ON k.user_id = i.create_by
                                                                    WHERE i.inv_date BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "'
                                                                    " . $filter_dari . "
                                                                    " . $filter_ke . " GROUP BY i.id_inv_out";
                                            $query_transfer = mysqli_query($conn, $select_transfer);
                                            while ($row_transfer = mysqli_fetch_array($query_transfer)) {

                                                $select_stock = "SELECT 
                                                                    i.inv_out_id,
                                                                    i.id_inv_out,
                                                                    i.id_product,
                                                                    i.stock_out,
                                                                    i.biaya,
                                                                    p.id_bahan,
                                                                    p.nama_bahan
                                                                    FROM inv_adjust_out i
                                                                    JOIN tb_bahan p ON p.id_bahan = i.id_product
                                                                    WHERE i.id_inv_out = '" . $row_transfer['id_inv_out'] . "' " . $filter_bahan . "

                                                ";
                                                $query_stock = mysqli_query($conn, $select_stock);
                                                $jum_stock = mysqli_num_rows($query_stock);

                                                $select_cabang_dari = "SELECT 
                                                                    id_cabang,
                                                                    nama_cabang
                                                                    FROM tb_cabang
                                                                    WHERE id_cabang = '" . $row_transfer['from'] . "'
                                                ";
                                                $query_cabang_dari = mysqli_query($conn, $select_cabang_dari);
                                                $data_cabang_dari = mysqli_fetch_array($query_cabang_dari);

                                                $select_cabang_ke = "SELECT 
                                                                    id_cabang,
                                                                    nama_cabang
                                                                    FROM tb_cabang
                                                                    WHERE id_cabang = '" . $row_transfer['to'] . "'
                                                ";
                                                $query_cabang_ke = mysqli_query($conn, $select_cabang_ke);
                                                $data_cabang_ke = mysqli_fetch_array($query_cabang_ke);






                                                if ($jum_stock > 0) {
                                                    echo '
                                                    <tr>
                                                        <td rowspan="' . $jum_stock . '">
                                                            ' . date("d/m/Y", strtotime($row_transfer['inv_date'])) . ' <br>
                                                            Dari : ' . $data_cabang_dari['nama_cabang'] . ' <br>
                                                            Ke : ' . $data_cabang_ke['nama_cabang'] . ' <br>
                                                            Dibuat Oleh : ' . $row_transfer['nama_lengkap'] . '
                                                        </td>
                                                ';

                                                    while ($row_stock = mysqli_fetch_array($query_stock)) {
                                                        echo '
                                                        <td class="text-center">' . $row_stock['nama_bahan'] . '</td>
                                                        <td class="text-center">' . $row_stock['stock_out'] . '</td>
                                                        <td class="text-right">' . number_format($row_stock['biaya']) . '</td>
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