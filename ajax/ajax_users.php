<?php
ob_start();
session_start();

include "../lib/koneksi.php";

$username = mysqli_real_escape_string($conn, $_POST['username']);

if ($_SESSION['id_user'] == $username) {
	echo "✔ Username masih tersedia";
} else {
	$sql = mysqli_query($conn, "select id_user from tb_user where id_user='" . $username . "' ");
	$num = mysqli_num_rows($sql);
	if ($num > 0) {
		echo " ❌ Username tidak tersedia";
	} else {
		echo "✔ Username masih tersedia";
	}
}

ob_flush();
