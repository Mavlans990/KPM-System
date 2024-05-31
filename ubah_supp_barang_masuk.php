<?php
include "lib/koneksi.php";
session_start();


$select_masuk = "
    SELECT *
    FROM
        tb_barang_masuk
    WHERE
        id_supplier LIKE '%CUS%' AND id_supplier <> ''
";
$query_masuk = mysqli_query($conn, $select_masuk);
while ($row_masuk = mysqli_fetch_array($query_masuk)) {
    $select_supp = "
        SELECT *
        FROM
            tb_customer
        WHERE
            id_customer = '" . $row_masuk['id_supplier'] . "'
    ";
    $query_supp = mysqli_query($conn, $select_supp);
    $jum_supp = mysqli_num_rows($query_supp);
    if ($jum_supp > 0) {
        $data_supp = mysqli_fetch_array($query_supp);
        $update_masuk = "
            UPDATE
                tb_barang_masuk
            SET
                id_supplier = '" . $data_supp['nama_customer'] . "'
            WHERE
                no_id = '" . $row_masuk['no_id'] . "'
        ";
        $query_update_masuk = mysqli_query($conn, $update_masuk);
    }
}
