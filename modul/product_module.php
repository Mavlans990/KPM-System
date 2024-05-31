<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (isset($_POST['save'])) {
    //UNTUK SAVE PRODUCT BARU
    if ($_POST['id'] == "") {
        $ekstensi_diperbolehkan = array('png', 'jpg', 'bmp', 'jpeg', 'gif', 'PNG', 'JPG', 'BMP', 'JPEG', 'GIF');
        $gambar = $_FILES['gambar']['name'];
        $x = explode('.', $gambar);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['gambar']['size'];
        $file_tmp = $_FILES['gambar']['tmp_name'];


        if (isset($_FILES['gambar'])) {
            $path_file = "";
            for ($i = 0; $i < count($_FILES['gambar']['name']); $i++) {
                $foto = $_FILES['gambar']['name'][$i];
                $img_ext = "pdf, PDF, jpeg, JPEG, png, PNG, gif, GIF, bmp, BMP, svg, SVG";
                $max_size = 10 * 1024 * 1024;

                $sumber = $_FILES['gambar']['tmp_name'][$i];
                $tujuan = '../../img/products/' . $_FILES['gambar']['name'][$i];

                move_uploaded_file($sumber, $tujuan);

                $path_file = $path_file . '|img/products/' . $_FILES['gambar']['name'][$i];
            }
        }




        $id_product = uniqid();
        $insert = "INSERT INTO product(id_product,nm_product,merk,jenis_motor,jenis_bahan,deskripsi,harga,qty,gambar,tag,tipe) VALUES(
            '" . mysqli_real_escape_string($conn, $id_product) . "',
            '" . mysqli_real_escape_string($conn, $_POST['nm_product']) . "',
            '" . mysqli_real_escape_string($conn, $_POST['merk_motor']) . "',
            '" . mysqli_real_escape_string($conn, $_POST['jenis_motor']) . "',
            '" . mysqli_real_escape_string($conn, $_POST['jenis_bahan']) . "',
            '" . mysqli_real_escape_string($conn, $_POST['deskripsi']) . "',
            '" . mysqli_real_escape_string($conn, $_POST['harga']) . "',
            '" . mysqli_real_escape_string($conn, $_POST['qty']) . "',
            '" . mysqli_real_escape_string($conn, $path_file) . "',
            '" . mysqli_real_escape_string($conn, $_POST['tag']) . "',
            'decal'
            
        )";
        $query_insert = mysqli_query($conn, $insert);

        if ($query_insert) {
            echo "<script type='text/javascript'>alert('Simpan berhasil');window.location.href = '../product.php';</script>";
        } else {
            echo "<script type='text/javascript'>alert('Simpan gagal');window.location.href = '../product.php';</script>";
        }
        //UNTUK EDIT PRODUCT
    } else {
        if ($_FILES['gambar']['name'] !== "") {
            $path_file = "";
            $ket = 1;
            for ($i = 0; $i < count($_FILES['gambar']['name']); $i++) {
                if ($_FILES['gambar']['name'][$i] == "") {
                    $ket = 0;
                }
                $foto = $_FILES['gambar']['name'][$i];
                $img_ext = "pdf, PDF, jpeg, JPEG, png, PNG, gif, GIF, bmp, BMP, svg, SVG";
                $max_size = 10 * 1024 * 1024;

                $sumber = $_FILES['gambar']['tmp_name'][$i];
                $tujuan = '../../img/products/' . $_FILES['gambar']['name'][$i];

                move_uploaded_file($sumber, $tujuan);

                $path_file = $path_file . '|img/products/' . $_FILES['gambar']['name'][$i];
            }
        }

        if ($ket == 1) {
            $path = "gambar = '" . mysqli_real_escape_string($conn, $path_file) . "',";
        } else {
            $path = "";
        }

        $update = "UPDATE product SET " . $path . " 
            nm_product = '" . mysqli_real_escape_string($conn, $_POST['nm_product']) . "',
            merk = '" . mysqli_real_escape_string($conn, $_POST['merk_motor']) . "',
            jenis_motor = '" . mysqli_real_escape_string($conn, $_POST['jenis_motor']) . "',
            jenis_bahan = '" . mysqli_real_escape_string($conn, $_POST['jenis_bahan']) . "',
            deskripsi = '" . mysqli_real_escape_string($conn, $_POST['deskripsi']) . "',
            harga = '" . mysqli_real_escape_string($conn, $_POST['harga']) . "',
            qty = '" . mysqli_real_escape_string($conn, $_POST['qty']) . "',
            tag = '" . mysqli_real_escape_string($conn, $_POST['tag']) . "'
            WHERE id_product = '" . mysqli_real_escape_string($conn, $_POST['id']) . "';
            ";

        $query_update = mysqli_query($conn, $update);

        if ($query_update) {
            echo "<script type='text/javascript'>alert('Ubah berhasil');window.location.href = '../product.php';</script>";
        } else {
            echo "<script type='text/javascript'>alert('Ubah gagal');window.location.href = '../product.php';</script>";
        }
    }
}

if (isset($_POST['delete'])) {
    $valid = 1;
    $query = mysqli_query($conn, "DELETE FROM product WHERE id_product='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
    if (!$query) {
        $valid = 0;
        $msg = "ERROR : Hapus data gagal";
    }

    if ($valid == 0) {
        rollback();
        echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../product.php';</script>";
    } else {
        commit();
        $msg = "Hapus data berhasil";
        echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../product.php';</script>";
    }
}
