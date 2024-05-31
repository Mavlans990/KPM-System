<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

// $no = 1;
// $select_mobil = "SELECT id_tipe,tipe_mobil FROM tb_tipe_mobil GROUP BY tipe_mobil ORDER BY id_tipe ASC";
// $query_mobil = mysqli_query($conn, $select_mobil);
// while ($row_mobil = mysqli_fetch_array($query_mobil)) {
//     echo $no . " - " . $row_mobil['id_tipe'] . " - " . $row_mobil['tipe_mobil'] . "<br>";
//     $no++;
// }


$select_keluar = "SELECT * FROM tb_barang_keluar WHERE no_polisi LIKE '%Product%' ORDER BY no_id ASC";
$query_keluar = mysqli_query($conn, $select_keluar);
while ($row_keluar = mysqli_fetch_array($query_keluar)) {
    $select_mobil = "SELECT id_tipe,tipe_mobil FROM tb_tipe_mobil WHERE id_tipe = '" . $row_keluar['tipe_mobil'] . "' ORDER BY id_tipe ASC";
    $query_mobil = mysqli_query($conn, $select_mobil);
    $jum_mobil = mysqli_num_rows($query_mobil);

    if ($jum_mobil < 1) {
        echo $row_keluar['no_id'] . " - " . $row_keluar['tipe_mobil'] . "<br>";
    }
}
