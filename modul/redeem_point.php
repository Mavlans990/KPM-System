<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (isset($_POST['save'])) {
	$id_redeem=generate_transaction_key('RDM','RDM',date('m'),date('y'));
    $query=mysql_query("INSERT INTO redeem (id_redeem, tgl, ket, id_member, total) VALUES ('".$id_redeem."','".date('Y-m-d')."','".mysql_real_escape_string($_POST['ket'])."','".mysql_real_escape_string($_POST['id'])."','".mysql_real_escape_string($_POST['total'])."')");
    
    if ($query) {
    	$sql=mysql_query("UPDATE point SET id_redeem='".$id_redeem."', tgl_redeem='".date('Y-m-d H:i:s')."', status='r' WHERE id_user = '".mysql_real_escape_string($_POST['id'])."' AND tgl BETWEEN '".mysql_real_escape_string($_POST['tgl'])."' AND '".mysql_real_escape_string($_POST['tgl2'])."'");

    	if ($sql) {
    		commit();
        	echo "<script type='text/javascript'>alert('Insert & Update Success');window.location.href = '../member_point.php';</script>";
    	} else {
    		rollback();
        	echo "<script type='text/javascript'>alert('Update Failed');window.location.href = '../member_point.php';</script>";
    	}
    } else {
    	rollback();
        echo "<script type='text/javascript'>alert('Insert Failed');window.location.href = '../member_point.php';</script>";
    }
}
?>