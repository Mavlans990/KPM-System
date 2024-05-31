<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

$sql_patch = mysqli_query($conn,"SELECT * from patching_kain");
while($row = mysqli_fetch_array($sql_patch)){
    // // echo $row['nama_kain'];
    // $query_update = "UPDATE tb_bahan_kain SET nama_kain = '".$row['nama_kain']."',warna = '".$row['warna']."',gramasi = '".$row['gramasi']."',jenis_kain = '".$row['jenis_kain']."',kategori = '".$row['kategori']."' WHERE nama_kain LIKE '%".$row['nama_kain']."%'";
    // echo $query_update.'<br><br>';
    // $sql = mysqli_query($query_update);
    $id_bahan = generate_bahan_kain();

    $insert = "INSERT INTO tb_bahan_kain(
        id_bahan_kain,
        kode_bahan_kain,
        nama_kain,
        warna,
        gramasi,
        jenis_kain,
        kategori
    ) VALUES(
        '" . $id_bahan . "',
        '" . $row['kode_bahan_kain'] . "',
        '" . $row['nama_kain'] . "',
        '" . $row['warna'] . "',
        '" . $row['gramasi'] . "',
        '" . $row['jenis_kain'] . "',
        '" . $row['kategori'] . "'
    )";
    echo $insert;
    $query = mysqli_query($conn, $insert);
}
?>