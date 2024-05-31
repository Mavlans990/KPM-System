<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
session_start();

$hasil = "";
$valid = 0;

$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
}

$filter_branch = "";
if ($_SESSION['group'] == "franchise") {
    $filter_branch = " WHERE id_cabang IN('" . $filter_cabang . "')";
}
if ($_SESSION['group'] == "admin") {
    $filter_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
}


$select_cabang = "
    SELECT *
    FROM tb_cabang " . $filter_branch . "
";
$query_cabang = mysqli_query($conn, $select_cabang);
while ($row_cabang = mysqli_fetch_array($query_cabang)) {
    $filter_all = "";
    if ($row_cabang['id_cabang'] !== 1) {
        $filter_all = " AND jenis_bahan = 'kaca_film'";
    }
    $hasil = $hasil . '
            <b class="h5 text-danger">' . $row_cabang['nama_cabang'] . '</b> <br>
            
        ';
    $x = 1;
    $select_bahan = "
    SELECT * 
    FROM tb_bahan 
    WHERE stock_min > 0 " . $filter_all . "
";
    $query_bahan = mysqli_query($conn, $select_bahan);
    while ($row_bahan = mysqli_fetch_array($query_bahan)) {

        $cabang = "";
        if ($_SESSION['group'] == "franchise") {
            $cabang = " AND id_cabang IN('" . $filter_cabang . "')";
        }
        if ($_SESSION['group'] == "admin") {
            $cabang = " AND id_cabang = '" . $_SESSION['branch'] . "'";
        }
        if ($_SESSION['group'] == "super") {
            $cabang = " AND id_cabang = '" . $row_cabang['id_cabang'] . "'";
        }


        $select_stock = "
        SELECT *
        FROM tb_stock_cabang
        WHERE id_bahan = '" . $row_bahan['id_bahan'] . "' " . $cabang . " AND id_cabang != '1' GROUP BY id_bahan
    ";
        $query_stock = mysqli_query($conn, $select_stock);
        while ($row_stock = mysqli_fetch_array($query_stock)) {
            if ($row_stock['stock'] < $row_bahan['stock_min']) {
                $valid = 1;
                $hasil = $hasil . '
                ' . $x . '. ' . $row_bahan['nama_bahan'] . ' x <span style="font-weight:bold;">' . $row_stock['stock'] . '</span> <br>
            ';
                $x++;
            }
        }
    }
    $hasil = $hasil . '<br>';
}
echo $valid . "|" . $hasil;
