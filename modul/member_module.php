<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (isset($_POST['save'])) {
	$query=mysql_query("SELECT id_member FROM member WHERE id_member = '".mysql_real_escape_string($_POST['id_member'])."'");
	$cek=mysql_num_rows($query);
	//UPDATE MEMBER DATA
	if ($cek>0) {
		if ($_POST['pass']!="") {
			$pass=", pass='".md5($_POST['pass'])."'";
		} else {
			$pass="";
		}
		$sql=mysql_query("UPDATE member SET nm_member='".mysql_real_escape_string($_POST['nm_member'])."' ".$pass.",id_parent='".mysql_real_escape_string($_POST['id_parent'])."',hp1='".mysql_real_escape_string($_POST['hp1'])."',hp2='".mysql_real_escape_string($_POST['hp2'])."',email='".mysql_real_escape_string($_POST['email'])."',alamat='".mysql_real_escape_string($_POST['alamat'])."',diubah_tgl='".date('Y-m-d')."' WHERE id_member='".mysql_real_escape_string($_POST['id_member'])."'");

		if ($sql) {
			$valid=1;
			$msg="Update Member Success";
		} else {
			$valid=0;
			$msg="Update Member Failed";
		}
	} else {
		$member=generate_idmember();
		$sql=mysql_query("INSERT INTO member(id_member, nm_member, pass, id_parent, hp1, hp2, email, alamat, dibuat_tgl) VALUES ('".mysql_real_escape_string($_POST['id_member'])."','".mysql_real_escape_string($_POST['nm_member'])."','".md5($_POST['pass'])."','".mysql_real_escape_string($_POST['id_parent'])."','".mysql_real_escape_string($_POST['hp1'])."','".mysql_real_escape_string($_POST['hp2'])."','".mysql_real_escape_string($_POST['email'])."','".mysql_real_escape_string($_POST['alamat'])."','".date('Y-m-d')."')");
		
		if ($sql) {
			$valid=1;
			$msg="Insert Member Success";
		} else {
			$valid=0;
			$msg="Insert Member Failed";
		}
	}

	if ($valid==0) {
		rollback();
		echo "<script type='text/javascript'>alert('".$msg."');window.location.href = 'member.php';</script>";
	} else {
		commit();
		echo "<script type='text/javascript'>alert('".$msg."');window.location.href = 'member.php';</script>";
	}
}
if(isset($_POST['delete'])){
	$del_query = mysql_query("DELETE FROM member WHERE id_member = '".mysql_real_escape_string($_POST['id_hapus'])."'");

	if($del_query){
		$msg = "Delete member success !!";
	}else{
		$msg = "Delete member failed !!";
	}
	echo "<script type='text/javascript'>alert('".$msg."');window.location.href = '../member.php';</script>";
}
?>