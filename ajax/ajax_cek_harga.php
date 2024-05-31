<?php
	ob_start();
	session_start();

	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";

	$product = mysql_real_escape_string($_POST['product']);
	
	$result="0";
	$sql=mysql_query("select harga from product where nm_product='".$product."' ");
	while ($row = mysql_fetch_array($sql)) {
		$result = $row['harga'];
	}

	echo $result;

	ob_flush();
?>