<?php
include "../lib/koneksi.php";
session_start();

$id = mysqli_real_escape_string($conn, $_POST['id_inv']);
$price = mysqli_real_escape_string($conn, $_POST['price']);

$select_inv_out = "SELECT * FROM inv_product_out WHERE inv_out_id = '" . $id . "'";
$query_inv_out = mysqli_query($conn, $select_inv_out);
$data_inv_out = mysqli_fetch_array($query_inv_out);



$total = $price * $data_inv_out['stock_out'];

$update = "UPDATE inv_product_out SET 
price = '" . $price . "',
total = '" . $total . "'
WHERE inv_out_id = '" . $id . "'
";
$query_update = mysqli_query($conn, $update);
