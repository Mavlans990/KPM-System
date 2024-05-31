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
<html>

<head>
    <title>Print Excel</title>
</head>

<body>
    <style type="text/css">
        body {
            font-family: sans-serif;
        }

        table {
            margin: 20px auto;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #3c3c3c;
            padding: 3px 8px;

        }

        a {
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Laporan Mutasi Stock.xls");
    ?>

    <!-- <center> -->
    <h3>LAPORAN MUTASI STOCK</h3>
    <h6><?= date('d/m/Y', strtotime($from)) ?> Sampai <?= date('d/m/Y', strtotime($to)) ?></h6>
    <!-- </center> -->

    <table border="1">
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

            $filter_dari = "";
            $filter_ke = "";

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
</body>

</html>