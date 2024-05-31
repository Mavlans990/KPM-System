<?php
include "../lib/koneksi.php";

$select_motor = "SELECT * FROM tb_motor WHERE merek = '" . mysqli_real_escape_string($conn, $_POST['merk_motor']) . "'";
$query_motor = mysqli_query($conn, $select_motor);
while ($row_motor = mysqli_fetch_array($query_motor)) {
    $hasil = $hasil . "<option value='" . $row_motor['filter'] . "'>" . $row_motor['nama_motor'] . "</option>";
}
echo $hasil;
