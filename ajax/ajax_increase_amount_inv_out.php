<?php
include "../lib/koneksi.php";
session_start();

$id = mysqli_real_escape_string($conn, $_POST['id_inv']);
$amount = mysqli_real_escape_string($conn, $_POST['amount']);

$select_inv_out = "SELECT * FROM inv_product_out WHERE inv_out_id = '" . $id . "'";
$query_inv_out = mysqli_query($conn, $select_inv_out);
$data_inv_out = mysqli_fetch_array($query_inv_out);



$total = $data_inv_out['price'] * $amount;

$update = "UPDATE inv_product_out SET 
total = '" . $total . "',
stock_out = '" . $amount . "'
WHERE inv_out_id = '" . $id . "'
";
$query_update = mysqli_query($conn, $update);
