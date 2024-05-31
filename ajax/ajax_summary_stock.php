<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
session_start();

if (isset($_POST['type'])) {
    $type = mysqli_real_escape_string($conn, $_POST['type']);

    $hasil = '
        <option value="all">- Semua Product -</option>
    ';

    if ($type !== "") {
        $select_bahan = "
            SELECT *
            FROM tb_bahan WHERE jenis_bahan = '" . $type . "' ORDER BY nama_bahan ASC
        ";
        $query_bahan = mysqli_query($conn, $select_bahan);
        while ($row_bahan = mysqli_fetch_array($query_bahan)) {
            $hasil = $hasil . '
                <option value="' . $row_bahan['id_bahan'] . '">' . $row_bahan['nama_bahan'] . '</option>
            ';
        }
    } else {
        $select_bahan = "
            SELECT *
            FROM tb_bahan ORDER BY nama_bahan ASC
        ";
        $query_bahan = mysqli_query($conn, $select_bahan);
        while ($row_bahan = mysqli_fetch_array($query_bahan)) {
            $hasil = $hasil . '
                <option value="' . $row_bahan['id_bahan'] . '">' . $row_bahan['nama_bahan'] . '</option>
            ';
        }
    }
    echo $hasil;
}
