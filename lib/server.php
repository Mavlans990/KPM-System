<?php
	$server = 'localhost';
	$user	= 'u7145153_vivere';
	$pass	= 'stevsoft14*';
	$db		= 'u7145153_vivere';
	
	$con = mysql_connect($server,$user,$pass);
	$db = mysql_select_db($db);
	date_default_timezone_set("Asia/Bangkok");
?>