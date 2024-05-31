<?php
include "lib/koneksi.php";
session_start();
date_default_timezone_set("Asia/Bangkok");


$valid = 1;
$select_motor = "SELECT * FROM tb_motor";
$query_motor = mysqli_query($conn, $select_motor);
while ($row_motor = mysqli_fetch_array($query_motor)) {

    $query = mysqli_query($conn, "SELECT max(m_product_id) as kodeTerbesar FROM m_product");
    if ($data = mysqli_fetch_array($query)) {
        $kode = $data['kodeTerbesar'];
        $urutan = (int) substr($kode, 2, 6);
        $urutan++;
        $huruf = "P-";
        $id_product = $huruf . sprintf("%06s", $urutan);
    } else {
        $id_product = "P-000001";
    }

    $insert_m_product = mysqli_query($conn, "INSERT INTO m_product(m_product_id,nm_product,type_product,uom_product,price,create_by) VALUES('" . $id_product . "','" . $row_motor['nama_motor'] . "','Product','Pieces','1200000','" . $_SESSION['id_user'] . "')");

    if ($insert_m_product) {
        $valid = 1;
    } else {
        $valid = 0;
    }
}

if ($valid == 1) {
    echo "<script>alert('Success');</script>";
} else {
    echo "<script>alert('Failed');</script>";
}
