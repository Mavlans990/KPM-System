<?php
include "../lib/koneksi.php";
session_start();

$hasil = 0;

$select_karyawan = "SELECT * FROM tb_karyawan WHERE id_karyawan = '" . mysqli_real_escape_string($conn, $_POST['id_user']) . "'";
$query_karyawan = mysqli_query($conn, $select_karyawan);
$data_karyawan = mysqli_fetch_array($query_karyawan);

if ($data_karyawan['grup'] == "franchise") {
    $hasil = 1;
}
if ($data_karyawan['grup'] == "admin") {
    $hasil = 2;
}
echo $hasil;
