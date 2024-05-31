<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

$no = 1;
$select_mobil = "SELECT nama from patching group by nama";
$query_mobil = mysqli_query($conn, $select_mobil);
while ($row_mobil = mysqli_fetch_array($query_mobil)) {
  
    $id_bahan = generate_bahan();
    $delete_tipe_mobil = "insert into tb_bahan (id_bahan,nama_bahan,uom) values  ('".$id_bahan."','" . $row_mobil['nama'] . "','YARD') ";
    $query_delete_mobil = mysqli_query($conn, $delete_tipe_mobil);
    
}
