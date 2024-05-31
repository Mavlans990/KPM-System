<?php
include "../lib/koneksi.php";
session_start();

$hasil = "";

$x = $_POST['offset'] + 1;
$select_testimoni = "SELECT * FROM product WHERE (jenis_motor = '" . mysqli_real_escape_string($conn, $_POST['jenis']) . "' OR merk = '" . mysqli_real_escape_string($conn, $_POST['jenis']) . "') AND tipe = 'testimoni' ORDER BY id_product ASC LIMIT 40 OFFSET " . mysqli_real_escape_string($conn, $_POST['offset']);
$query_testimoni = mysqli_query($conn, $select_testimoni);
while ($row_testimoni = mysqli_fetch_array($query_testimoni)) {

    if ($_POST['jenis'] == "motor" && $_POST['jenis'] == "mobil") {
        $jenis = ucfirst($_POST['jenis']);
    } else {
        $jenis = str_replace("_", " ", $_POST['jenis']);
        $jenis = ucfirst($jenis);
    }

    $gambar = "";
    $array = explode('|', $row_testimoni['gambar']);
    foreach ($array as $my_Array) {
        if ($my_Array != "") {
            $gambar = $my_Array;
        }
    }

    $hasil = $hasil . '
        <tr>
            <td>' . $x . '</td>
            <td><img src="../' . $gambar . '" width="100px" alt=""></td>
            <td>' . $jenis . '</td>
            ';
    if ($_SESSION['grup'] == "super") {
        $hasil = $hasil . '
                <td>
                <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal"
                  data-id="' . $row_testimoni['id_product'] . '"
                  data-kategori="' . $row_testimoni['merk'] . '"
                  data-warna="' . $row_testimoni['warna'] . '">
                <i class="fa fa-edit"></i> Ubah</a>
                <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                  data-id_hapus="' . $row_testimoni['id_product'] . '">
                <i class="fa fa-trash"></i> Hapus</a>
              </td>
                ';
    }
    $hasil = $hasil . '
        </tr>
    ';
    $x++;
}
echo $hasil;
