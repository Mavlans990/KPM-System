<?php
include "lib/koneksi.php";
session_start();

$conn_other = mysqli_connect('203.161.184.26', 'coolplu1_warranty', 'stevsoft14*', 'coolplu1_warranty');

$insert = "INSERT INTO invoice(
                no_garansi,
                nama,
                type_mobil,
                no_plat,
                seri_kaca_depan,
                seri_kaca_samping,
                seri_kaca_belakang,
                tgl_pasang,
                grnsi_kacadepan,
                grns_kacabelakang,
                grns_kacasamping,
                pemasangan                 
            ) VALUES(
                'TES',
                'TES',
                'TES',
                'TES',
                'TES',
                'TES',
                'TES',
                '" . date("Y-m-d") . "',
                '" . date("Y-m-d") . "',
                '" . date("Y-m-d") . "',
                '" . date("Y-m-d") . "',
                'mobil'
            )";
$query_insert = mysqli_query($conn_other, $insert);
if ($query_insert) {
    echo "success";
} else {
    echo "failed";
}
