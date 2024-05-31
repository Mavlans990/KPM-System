<?php
include "../lib/koneksi.php";
session_start();

if (isset($_POST['dari_cabang'])) {
    $dari_cabang = mysqli_real_escape_string($conn, $_POST['dari_cabang']);

    $hasil = "<option value=''>-- Ke Cabang --</option>";

    $where_branch = "";
    if ($_SESSION['group'] == "super") {
        $where_branch = " WHERE id_cabang != '" . $_SESSION['branch'] . "'";
    }
    if ($_SESSION['group'] == "franchise") {
        $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
        $where_branch = " WHERE id_cabang IN('" . $filter_cabang . "')";
    }
    if ($_SESSION['group'] == "admin") {
        $where_branch = " WHERE id_cabang != '" . $_SESSION['branch'] . "'";
    }
    $select_cabang = "SELECT 
                        id_cabang,
                        nama_cabang
                        FROM tb_cabang " . $where_branch . " ORDER BY nama_cabang ASC";
    $query_cabang = mysqli_query($conn, $select_cabang);
    while ($row_cabang = mysqli_fetch_array($query_cabang)) {
        $hasil = $hasil . '
            <option value="' . $row_cabang['id_cabang'] . '">' . $row_cabang['nama_cabang'] . '</option>
        ';
    }
    echo $hasil;
}
