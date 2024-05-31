<?php
include "../lib/koneksi.php";
session_start();

$total = 0;

$kaca_depan = mysqli_real_escape_string($conn, $_POST['kaca_depan']);
$kaca_belakang = mysqli_real_escape_string($conn, $_POST['kaca_belakang']);
$lainnya = mysqli_real_escape_string($conn, $_POST['lainnya']);

$total = $total + $kaca_depan + $kaca_belakang + $lainnya;

echo $total;
