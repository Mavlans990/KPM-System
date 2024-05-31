<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// $conn_other = mysqli_connect('203.161.184.26', 'coolplu1_warranty', 'warranty2020', 'coolplu1_warranty');
$server = "203.161.184.26";
$username = "coolplu1_warranty";
$password = "warranty2020";
$database = "coolplu1_warranty";
//Koneksi dan memilih database di server
$conn_other = mysqli_connect($server, $username, $password) or die("Koneksi database gagal");
mysqli_select_db($conn_other, $database) or die("Database tidak tersedia");
