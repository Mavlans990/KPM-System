<?php
  	session_start();
  	include "../lib/koneksi.php";
  	include "../lib/appcode.php";
  	include "../lib/format.php";
  
    if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
    {
        header('Location:login.php'); 
    }


	$valid=1;
	//MODUL BALIKIN STOK PO
	if(isset($_GET['mode'])) {
		if($_GET['mode']=='ubah') {
			if(isset($_GET['id_po'])) {
				$sql=mysql_query("select * from po2 where id_po1 = '".$_GET['id_po']."' ");
				while($row = mysql_fetch_array($sql)) {
					$query_5=mysql_query("update product set qty = qty - ".$row['qty']." where id_product = '".$row['id_bahan']."' ");	
					if(!$query_5) {
						$valid=0;
						$process_status="ERROR : Gagal Update Stok";
					}
				}
				if ($valid==0) {
					rollback();
					echo "<script type='text/javascript'>alert('".$process_status."')</script>";
				} else {
					header("Location:../input_po.php?mode=ubah&id_po=".$_GET['id_po']);
				}
			} 
		}
	}
?>