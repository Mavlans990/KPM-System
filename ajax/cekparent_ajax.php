<?php
	ob_start();
	session_start();

	include "../lib/koneksi.php";

	$parent = mysql_real_escape_string($_POST['id_parent']);
	$member = mysql_real_escape_string($_POST['id_member']);
	
	$sql=mysql_query("select id_member from member where id_member='".$parent."'");
	$jml=mysql_num_rows($sql);
	if ($jml>0) {
		$qu=mysql_query("select id_parent from member where id_member='".$member."'");
		$data=mysql_fetch_array($qu);
		if ($data['id_parent']==$parent) {
			echo "<span style='color:lightgreen'>✔ Upline masih tersedia</span>";
		} else {
			/*
			$sql=mysql_query("select id_member from member where id_parent='".$parent."' ");
			$num=mysql_num_rows($sql);
			*/
			$i=1; $status="yes"; $id_member=$parent;
			while ($status=="yes") {
				$queries=mysql_query("SELECT id_parent FROM member WHERE id_member='".mysql_real_escape_string($id_member)."'");
				while ($sum=mysql_fetch_array($queries)){
					if ($sum['id_parent']=='root') {
						$status="no";
					} else {
						$id_member=$sum['id_parent'];
						$i++;
					}
				}
			}

			if ($i>10) {
				echo "<span style='color:red'>❌ Upline tidak tersedia</span>";
			} else {
				echo "<span style='color:lightgreen'>✔ Upline masih tersedia</span>";
			}
		}
	} else {
		echo "<span style='color:red'>❌ Kode Parent ID salah</span>";
	}
		
	
	ob_flush();
?>