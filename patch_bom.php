<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

$sql_grup = mysqli_query($conn,"SELECT * FROM patching_bom GROUP BY kode_bom");
while($row_group = mysqli_fetch_array($sql_grup)){
    $id_transaksi = generate_bom("BOM", "BOM", date("m"), date("y"));
    $id_transaksi_2 = generate_barang_masuk_key("DBM", "DBM", date("m"), date("y"));
$sql_patch = mysqli_query($conn,"SELECT * from patching_bom WHERE kode_bom = '".$row_group['kode_bom']."'");
while($row = mysqli_fetch_array($sql_patch)){
    // // echo $row['nama_kain'];
    // $query_update = "UPDATE tb_bahan_kain SET nama_kain = '".$row['nama_kain']."',warna = '".$row['warna']."',gramasi = '".$row['gramasi']."',jenis_kain = '".$row['jenis_kain']."',kategori = '".$row['kategori']."' WHERE nama_kain LIKE '%".$row['nama_kain']."%'";
    // echo $query_update.'<br><br>';
    // $sql = mysqli_query($query_update);
    
    $no_id = id_gen_bom();
    

    $insert = "INSERT INTO tb_bom(
        no_id,
        id_transaksi,
        id_transaksi_2,
        kode_bom,
        nama_bom,
        uom_sku,
        keterangan,
        id_sku,
        nama_sku,
        kode_bahan_kain,
        jenis_kain,
        uom_kain,
        qty_kain,
        harga,
        total_harga,
        status
    ) VALUES(
        '" . $no_id . "',
        '" . $id_transaksi. "',
        '" . $id_transaksi_2 . "',
        '" . $row['kode_bom'] . "',
        '" . $row['nama_bom'] . "',
        '" . $row['uom_sku'] . "',
        '" . $row['keterangan'] . "',
        '" . $row['id_sku'] . "',
        '',
        '" . $row['kode_bahan_kain'] . "',
        '" . $row['jenis_kain'] . "',
        '" . $row['uom_kain'] . "',
        '" . $row['qty_kain'] . "',
        '" . $row['harga'] . "',
        '" . $row['total_harga'] . "',
        's'


    )";
    echo $insert.'<br><br>';
    $query = mysqli_query($conn, $insert);
}
}
?>