<?php
include "../lib/koneksi.php";
session_start();

if (isset($_POST['dari_cabang_stock'])) {

    $id_cabang = mysqli_real_escape_string($conn, $_POST['dari_cabang_stock']);

    $hasil = "";
    $hasil2 = "";

    $select_stock = "SELECT s.id_cabang,s.stock,b.id_bahan,b.nama_bahan FROM tb_stock_cabang s 
    JOIN tb_bahan b ON b.id_bahan = s.id_bahan WHERE s.id_cabang = '" . $id_cabang . "' ORDER BY b.nama_bahan ASC";
    $query_stock = mysqli_query($conn, $select_stock);
    while ($row_stock = mysqli_fetch_array($query_stock)) {
        $hasil = $hasil . '
            <option value="' . $row_stock['id_bahan'] . '">' . $row_stock['nama_bahan'] . '</option>
        ';
    }

    $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang != '" . $id_cabang . "' ORDER BY nama_cabang ASC";
    $query_cabang = mysqli_query($conn, $select_cabang);
    while ($row_cabang = mysqli_fetch_array($query_cabang)) {
        $hasil2 = $hasil2 . '
            <option value="' . $row_cabang['id_cabang'] . '">' . $row_cabang['nama_cabang'] . '</option>
        ';
    }

    echo $hasil . "|" . $hasil2;
}

if (isset($_POST['add_new_transfer'])) {

    $valid = "";

    $add_new_transfer = mysqli_real_escape_string($conn, $_POST['add_new_transfer']);
    $dari_cabang = mysqli_real_escape_string($conn, $_POST['dari_cabang']);
    $ke_cabang = mysqli_real_escape_string($conn, $_POST['ke_cabang']);
    $id_bahan = mysqli_real_escape_string($conn, $_POST['id_bahan']);
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);

    $select_transfer = "SELECT * FROM tb_transfer_stock WHERE id_transfer = '" . $add_new_transfer . "'";
    $query_transfer = mysqli_query($conn, $select_transfer);
    $jum_transfer = mysqli_num_rows($query_transfer);

    if ($jum_transfer > 0) {
        $select_stock = "SELECT stock FROM tb_stock_cabang WHERE id_cabang = '" . $dari_cabang . "' AND id_bahan = '" . $id_bahan . "'";
        $query_stock = mysqli_query($conn, $select_stock);
        $data_stock = mysqli_fetch_array($query_stock);

        if ($data_stock['stock'] <= $jumlah) {
            $valid = "stock_less";
        } else {
            $update = "UPDATE tb_transfer_stock SET
            cabang_from = '" . $dari_cabang . "',
            cabang_to = '" . $ke_cabang . "',
            id_product = '" . $id_bahan . "',
            amount = '" . $jumlah . "',
            diubah_oleh = '" . $_SESSION['id_user'] . "',
            diubah_tgl = '" . date("Y-m-d") . "' WHERE id_transfer = '" . $add_new_transfer . "'
            ";

            $query_update = mysqli_query($conn, $update);

            if ($query_update) {
                $valid = "update_success";
            } else {
                $valid = "update_failed";
            }
        }
    } else {
        $select_stock = "SELECT stock FROM tb_stock_cabang WHERE id_cabang = '" . $dari_cabang . "' AND id_bahan = '" . $id_bahan . "'";
        $query_stock = mysqli_query($conn, $select_stock);
        $data_stock = mysqli_fetch_array($query_stock);

        if ($data_stock['stock'] <= $jumlah) {
            $valid = "stock_less";
        } else {
            $insert = "INSERT INTO tb_transfer_stock(
                id_transfer,
                tgl_transfer,
                cabang_from,
                cabang_to,
                id_product,
                amount,
                dibuat_oleh,
                dibuat_tgl
            ) VALUES(
                '" . $add_new_transfer . "',
                '" . date("Y-m-d") . "',
                '" . $dari_cabang . "',
                '" . $ke_cabang . "',
                '" . $id_bahan . "',
                '" . $jumlah . "',
                '" . $_SESSION['id_user'] . "',
                '" . date("Y-m-d") . "'
            )";

            $query_insert = mysqli_query($conn, $insert);

            if ($query_insert) {
                $valid = "insert_success";
            } else {
                $valid = "insert_failed";
            }
        }
    }
    echo $valid;
}
