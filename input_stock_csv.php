<?php
include "lib/koneksi.php";
session_start();

if (isset($_POST['upload'])) {
    $filename = $_FILES['import_file']['name'];
    $file_tmp = $_FILES['import_file']['tmp_name'];
    move_uploaded_file($file_tmp, 'modul/script/' . $filename);
    // Open the file for reading
    if (($h = fopen("modul/script/{$filename}", "r")) !== FALSE) {
        // Each line in the file is converted into an individual array that we call $data
        // The items of the array are comma separated

        while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
            // Each individual array is being pushed into the nested array
            $valid = 1;


            $insert = "INSERT INTO tb_barang_masuk(
                id_transaksi,
                tgl_transaksi,
                id_product,
                qty,
                id_cabang
            ) VALUES(
                'manual_ho1',
                '2021-02-02',
                '" . $data[0] . "',
                '" . $data[2] . "',
                '1'
            )";

            echo $insert;

            $query = mysqli_query($conn, $insert);

            if ($query) {
                $status = "success";
            } else {
                $status = "failed";
            }
        }

        // Close the file
        fclose($h);
    } else {
        echo "<script>alert('invalid input');window.location='input_stock_csv.php';</script>";
        $status = "";
    }
    echo "<script>alert('" . $status . "');window.location='input_stock_csv.php';</script>";
    // Display the code in a readable format
}

?>
<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="import_file" id="" required>
    <button type="submit" name="upload">Upload</button>
</form>