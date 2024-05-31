<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../lib/koneksi.php";
session_start();

$hasil = 0;


$conn_other = mysqli_connect('localhost', 'coolplu1_warranty', 'warranty2020', 'coolplu1_warranty');
        
$select_penjualan2 = "SELECT * FROM tb_barang_keluar WHERE no_polisi != 'Product' GROUP BY no_polisi";
    $query_penjualan2 = mysqli_query($conn, $select_penjualan2);
    while ($row_penjualan2 = mysqli_fetch_array($query_penjualan2)) {

    
        $insert = "INSERT INTO invoice(
            no_garansi,
            nama,
            type_mobil,
            no_plat,
            seri_kaca_depan,
            seri_kaca_samping,
            seri_kaca_belakang,
            tgl_pasang
        ) VALUES(
            'MID0001',
            'Alief Daffa',
            'AGYA',
            'B 1234 KAU',
            'BLACK 40',
            'BLACK 40',
            'BLACK 40',
            '" . date("Y-m-d") . "'
        )";
    
        $queryInsert = mysqli_query($conn_other, $insert);
        if($queryInsert){
            $hasil = 1;
        }
        
    }
    echo $hasil;