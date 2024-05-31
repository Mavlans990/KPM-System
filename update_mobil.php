<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

$no = 1;
$select_mobil = "SELECT id_tipe,tipe_mobil FROM tb_tipe_mobil GROUP BY tipe_mobil ORDER BY id_tipe ASC";
$query_mobil = mysqli_query($conn, $select_mobil);
while ($row_mobil = mysqli_fetch_array($query_mobil)) {
    $id_tipe = $row_mobil['id_tipe'];
    $tipe_mobil = $row_mobil['tipe_mobil'];

    $select_mobil2 = "SELECT * FROM tb_tipe_mobil WHERE tipe_mobil LIKE '%" . $tipe_mobil . "%' AND id_tipe != '" . $id_tipe . "'";
    $query_mobil2 = mysqli_query($conn, $select_mobil2);
    while ($row_mobil2 = mysqli_fetch_array($query_mobil2)) {

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE tipe_mobil = '" . $row_mobil2['id_tipe'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
            $update_mobil = "UPDATE tb_barang_keluar SET tipe_mobil = '" . $id_tipe . "' WHERE tipe_mobil = '" . $row_keluar['tipe_mobil'] . "'";
            $query_update_mobil = mysqli_query($conn, $update_mobil);
        }

        $delete_tipe_mobil = "DELETE FROM tb_tipe_mobil WHERE id_tipe = '" . $row_mobil2['id_tipe'] . "'";
        $query_delete_mobil = mysqli_query($conn, $delete_tipe_mobil);
    }
}
