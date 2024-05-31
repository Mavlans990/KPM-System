<?php
	ob_start();
	session_start();

	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";

	$absen = mysql_real_escape_string($_POST['absen']);
	
	$sql=mysql_query("INSERT INTO tb_absen(pk, tgl, id_user, jenis, jam) VALUES ('','".date('Y-m-d')."','".$_SESSION['id_user']."','".$absen."','".date('H :i:s')."')");
	if ($sql) {
		$result="Absen ".$absen." berhasil pada waktu".date('H:i:s');
	} else {
		$result="Absen ".$absen." gagal";
	}
	echo $result;

	ob_flush();
?>