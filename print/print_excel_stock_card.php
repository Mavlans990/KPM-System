<?php
session_start();
include "../lib/koneksi.php";
include "../lib/format.php";
include "../lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$bah = "";
if (isset($_GET['nama_barang'])) {
    $bah = mysqli_real_escape_string($conn, $_GET['nama_barang']);

    if ($bah !== '') {
        $select_bahan = "SELECT nama_bahan FROM tb_bahan where id_bahan = '" . $bah . "' ";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $row_bahan = mysqli_fetch_array($query_bahan);
        $nama_bahan = $row_bahan['nama_bahan'];
    } else {
        $nama_bahan = "Semua";
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
    header("Content-Disposition: attachment; filename=Laporan Stock Card General.xls");
    ?>

    <!-- <center> -->
    <h3>LAPORAN STOCK CARD GENERAL</h3>
    <h6>BAHAN: <?= $nama_bahan ?></h6>
    <!-- </center> -->

    <table border="1">
        <thead>
            <tr class="bg-success">
                <th class="text-center text-white">No.</th>
                <th class="text-center text-white">Nama Barang</th>
                <th class="text-center text-white">Total QTY</th>
                <th class="text-center text-white">Total Berat</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $where_barang = "";
            $total_roll = 0;
            $total_yard = 0;
            if ($bah !== "") {
                $where_barang = " WHERE id_bahan = '" . $bah . "'";
            }

            $no = 1;
            $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan " . $where_barang . " ORDER BY id_bahan ASC");
            while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
                $total_roll += $row_barang['total_qty'];
                $total_yard += $row_barang['total_berat'];
                echo '
                    <tr>
                        <td class="text-center">' . $no . '</td>
                        <td class="text-center">' . $row_barang['nama_bahan'] . '</td>
                        <td class="text-center">' . number_format($row_barang['total_qty']) . '</td>
                        <td class="text-center">' . number_format($row_barang['total_berat']) . '</td>
                    </tr>
                ';
                $no++;
            }
            echo '
            <tr>
                <td class="text-center" colspan="2" style="font-weight:bold;">Total</td>
                <td class="text-center" style="font-weight:bold;">'.number_format($total_roll).'</td>
                <td class="text-center" style="font-weight:bold;">'.number_format($total_yard).'</td>
            </tr>
        ';
            ?>
        </tbody>
    </table>
</body>

</html>