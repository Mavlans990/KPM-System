<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

$no = 1;
$select_mobil = "SELECT a.nama,a.yard,a.jumlah,b.id_bahan from patching a 
join tb_bahan b on a.nama = b.nama_bahan
where a.jumlah > 0";
$query_mobil = mysqli_query($conn, $select_mobil);
while ($row = mysqli_fetch_array($query_mobil)) {
  
    $no_id = id_gen_barang_masuk();

   

        $insert = "INSERT INTO tb_barang_masuk(
            no_id,
            id_transaksi,
            tgl_transaksi,
            id_product,
            qty,
            uom,
            berat,
            harga,
            total,
            dibuat_oleh,
            dibuat_tgl
        ) VALUES(
            '" . $no_id . "',
            'STOCKAWAL',
            '" . date("Y-m-d") . "',
            '" . $row['id_bahan'] . "',
            '" . $row['jumlah'] . "',
            'YARD',
            '" . $row['yard'] . "',
            0,
            0,
            'stevan',
            '" . date("Y-m-d") . "'
        )";

        $query = mysqli_query($conn, $insert);
    
}
