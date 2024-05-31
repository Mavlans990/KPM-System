<?php
include "lib/koneksi.php";
session_start();

$select_hpp = "
    SELECT
        id_bahan,
        hpp
    FROM
        tb_stock_cabang
    WHERE
        id_cabang = '1'
";
$query_hpp = mysqli_query($conn, $select_hpp);
while ($row_hpp = mysqli_fetch_array($query_hpp)) {
    $update = "
        UPDATE
            tb_barang_keluar
        SET
            hpp = '" . $row_hpp['hpp'] . "'
        WHERE
            tgl_transaksi BETWEEN '2021-03-01' AND '2021-03-31' AND
            id_cabang = '1' AND
            id_bahan = '" . $row_hpp['id_bahan'] . "'
    ";
    $query_update = mysqli_query($conn, $update);
}
