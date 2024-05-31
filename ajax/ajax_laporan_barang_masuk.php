<?php 
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";
session_start();

if(isset($_POST['get_supplier'])){
    $supplier = mysqli_real_escape_string($conn, $_POST['get_supplier']);

    $hasil = "";
    $sql_get_supplier = mysqli_query($conn, "SELECT id_customer,nama_customer FROM tb_customer WHERE nama_customer LIKE '%".$supplier."%' OR id_customer LIKE '%".$supplier."%' ORDER BY nama_customer ASC LIMIT 50");
    while($row_supplier = mysqli_fetch_array($sql_get_supplier)){
        $hasil = $hasil . "<option value='".$row_supplier['nama_customer']." | ".$row_supplier['id_customer']."'>";
    }

    echo $hasil;
}