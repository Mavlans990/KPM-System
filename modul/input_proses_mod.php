<?php
$valid=1;
if(isset($_POST['save'])) {
	$valid=1;
	begin();

	//MODUL SAVE PO / SAVE PO BARU
	if($_POST['proses']=="new") {
		//INSERT KE PO1
		if($valid==1) {
			$id_pro=generate_transaction_key('PRO','PRO',date('m'),date('Y'));
			$query_1=mysql_query("INSERT INTO proses (id_proses, tgl_proses, dibuat_oleh, dibuat_tgl, ket) VALUES ('".$id_pro."','".mysql_real_escape_string($_POST['tgl'])."','".$_SESSION['id_user']."','".date('Y-m-d')."','".mysql_real_escape_string($_POST['ket'])."') ");
			if(!$query_1) {
				$valid=0;
				$process_status="ERROR : Gagal Insert PO1";
			}
		}
		//INSERT KE PROSES_BAHAN, AMBIL DARI TEMP_BHN
		if($valid==1) {
			$sql=mysql_query("SELECT * FROM temp_bahan WHERE id_user = '".$_SESSION['id_user']."' ORDER BY waktu ASC ");
			while($row = mysql_fetch_array($sql)) {
				if($valid==1) {
					$id_bhn=generate_transaction_key('BHN','BHN',date('m'),date('Y'));
					$query_2=mysql_query("INSERT INTO proses_bhn (id_pro_bhn, id_proses, id_bahan, qty) VALUES ('".$id_bhn."','".$id_pro."','".$row['id_bahan']."','".$row['qty']."') ");
					if(!$query_2) {
						$valid=0;
						$process_status="ERROR : Gagal Insert PROSES_BHN";
					}
				}	
			}	
		}
		//INSERT KE PROSES_JADI, AMBIL DARI TEMP_JADI
		if($valid==1) {
			$sql=mysql_query("SELECT * FROM temp_jadi WHERE id_user = '".$_SESSION['id_user']."' ORDER BY waktu ASC ");
			while($row = mysql_fetch_array($sql)) {
				if($valid==1) {
					$id_jdi=generate_transaction_key('JADI','JADI',date('m'),date('Y'));
					$query_2=mysql_query("INSERT INTO proses_jadi (id_pro_jadi, id_proses, id_jadi, qty) VALUES ('".$id_jdi."','".$id_pro."','".$row['id_jadi']."','".$row['qty']."') ");
					if(!$query_2) {
						$valid=0;
						$process_status="ERROR : Gagal Insert PROSES_JADI";
					}
				}	
			}	
		}
		//UPDATE STOK BARANG
		if($valid==1) {
			$sql=mysql_query("SELECT * FROM temp_bahan WHERE id_user = '".$_SESSION['id_user']."' ORDER BY waktu ASC ");
			while($row = mysql_fetch_array($sql)) {
				$query_5=mysql_query("UPDATE product SET qty = qty - ".$row['qty']." WHERE id_product = '".$row['id_bahan']."' ");	
				if(!$query_5) {
					$valid=0;
					$process_status="ERROR : Gagal Update Stok Bahan";
				}
			}
		}
		//UPDATE STOK BARANG
		if($valid==1) {
			$sql=mysql_query("SELECT * FROM temp_jadi WHERE id_user = '".$_SESSION['id_user']."' ORDER BY waktu ASC ");
			while($row = mysql_fetch_array($sql)) {
				$query_5=mysql_query("UPDATE product SET qty = qty + ".$row['qty']." WHERE id_product = '".$row['id_jadi']."' ");	
				if(!$query_5) {
					$valid=0;
					$process_status="ERROR : Gagal Update Stok Produk";
				}
			}
		}
		//HAPUS TEMP_BAHAN
		if($valid==1) {
			$query_5=mysql_query("DELETE FROM temp_bahan WHERE id_user = '".$_SESSION['id_user']."' ");	
			if(!$query_5) {
				$valid=0;
				$process_status="Eror : eror delete temp_bahan";
			}
		}
		//HAPUS TEMP_JADI
		if($valid==1) {
			$query_6=mysql_query("DELETE FROM temp_jadi WHERE id_user = '".$_SESSION['id_user']."' ");	
			if(!$query_6) {
				$valid=0;
				$process_status="Eror : eror delete temp_jadi";
			}
		}
		
		
		if($valid==0) {  
			rollback();
			echo "<script type='text/javascript'>alert('".$process_status."')</script>";
			$page = "input_process.php?mode=ubah";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
		else {	
			commit();
			$process_status="Save Success";
			echo "<script type='text/javascript'>alert('".$process_status."')</script>";
			$page = "input_process.php?mode=view&proses=".$id_pro."";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
	}

	//MODUL UPDATE / EDIT PO
	if($_POST['proses']!="new") {
		//UPDATE KE TABEL PROSES
		if($valid==1) {	
			$query_1=mysql_query("UPDATE proses SET tgl_proses='".mysql_real_escape_string($_POST['tgl'])."',diubah_tgl='".date('Y-m-d H:i:s')."',status='edited',ket='".mysql_real_escape_string($_POST['ket'])."' WHERE id_proses='".mysql_real_escape_string($_POST['proses'])."'");
			if(!$query_1) {
				$valid=0;
				$process_status="ERROR : Gagal Update Tabel Proses";
			}
		}
		//INSERT KE PROSES_BAHAN, AMBIL DARI TEMP_BHN
		if($valid==1) {
			$sql=mysql_query("SELECT * FROM temp_bahan WHERE id_user = '".$_SESSION['id_user']."' ORDER BY waktu ASC ");
			while($row = mysql_fetch_array($sql)) {
				if($valid==1) {
					$id_bhn=generate_transaction_key('BHN','BHN',date('m'),date('Y'));
					$query_2=mysql_query("INSERT INTO proses_bhn (id_pro_bhn, id_proses, id_bahan, qty) VALUES ('".$id_bhn."','".mysql_real_escape_string($_POST['proses'])."','".$row['id_bahan']."','".$row['qty']."') ");
					if(!$query_2) {
						$valid=0;
						$process_status="ERROR : Gagal Insert PROSES_BHN";
					}
				}	
			}	
		}
		//INSERT KE PROSES_JADI, AMBIL DARI TEMP_JADI
		if($valid==1) {
			$sql=mysql_query("SELECT * FROM temp_jadi WHERE id_user = '".$_SESSION['id_user']."' ORDER BY waktu ASC ");
			while($row = mysql_fetch_array($sql)) {
				if($valid==1) {
					$id_jdi=generate_transaction_key('JADI','JADI',date('m'),date('Y'));
					$query_2=mysql_query("INSERT INTO proses_jadi (id_pro_jadi, id_proses, id_jadi, qty) VALUES ('".$id_jdi."','".mysql_real_escape_string($_POST['proses'])."','".$row['id_jadi']."','".$row['qty']."') ");
					if(!$query_2) {
						$valid=0;
						$process_status="ERROR : Gagal Insert PROSES_JADI";
					}
				}	
			}	
		}
		//UPDATE STOK BAHAN BAKU
		if($valid==1) {
			$sql=mysql_query("SELECT * FROM proses_bhn WHERE id_proses = '".$_POST['proses']."' ");
			while($row = mysql_fetch_array($sql)) {
				$query_5=mysql_query("UPDATE product SET qty = qty - ".$row['qty']." where id_product = '".$row['id_bahan']."' ");	
				if(!$query_5) {
					$valid=0;
					$process_status="ERROR : Gagal Update Stok Bahan";
				}
			}
		}
		//UPDATE STOK PRODUK JADI
		if($valid==1) {
			$sql=mysql_query("SELECT * FROM proses_jadi WHERE id_proses = '".$_POST['proses']."' ");
			while($row = mysql_fetch_array($sql)) {
				$query_5=mysql_query("UPDATE product SET qty = qty + ".$row['qty']." where id_product = '".$row['id_jadi']."' ");	
				if(!$query_5) {
					$valid=0;
					$process_status="ERROR : Gagal Update Stok Produk";
				}
			}
		}
		//HAPUS TEMP_BAHAN
		if($valid==1) {
			$query_5=mysql_query("DELETE FROM temp_bahan WHERE id_user = '".$_SESSION['id_user']."' ");	
			if(!$query_5) {
				$valid=0;
				$process_status="Eror : eror delete temp_bahan";
			}
		}
		//HAPUS TEMP_JADI
		if($valid==1) {
			$query_6=mysql_query("DELETE FROM temp_jadi WHERE id_user = '".$_SESSION['id_user']."' ");	
			if(!$query_6) {
				$valid=0;
				$process_status="Eror : eror delete temp_jadi";
			}
		}
		
		if($valid==0) {  
			rollback();
			echo "<script type='text/javascript'>alert('".$process_status."')</script>";
			$page = "input_process.php?mode=view&proses=".$_POST['proses']."";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
		else
		{	
			commit();
			$process_status="simpan dan Otorisasi berhasil";
			echo "<script type='text/javascript'>alert('".$process_status."')</script>";
			$page = "input_process.php?mode=view&proses=".$_POST['proses']."";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
	}
}

//MODUL HAPUS, EDIT & VIEW
if(isset($_GET['mode'])) {
	if($_GET['mode'] == "hapus") {
		if(isset($_GET['id_pro_bhn'])) {
			$sql=mysql_query("SELECT * FROM proses_bhn WHERE id_pro_bhn = '".mysql_real_escape_string($_GET['id_pro_bhn'])."' ");
			while($row = mysql_fetch_array($sql)) {
				$query_5=mysql_query("UPDATE product SET qty = qty + ".$row['qty']." where id_product = '".$row['id_bahan']."' ");	
				if(!$query_5) {
					$valid=0;
					$process_status="ERROR : Gagal Update Stok";
				}
			}
			$query_1=mysql_query("DELETE FROM proses_bhn WHERE id_pro_bhn='".mysql_real_escape_string($_GET['id_pro_bhn'])."' ");
			if(!$query_1) {
				echo "<script type='text/javascript'>alert('ERROR : Delete proses_bhn Failed')</script>";
			}

			$page = "input_process.php?mode=ubah&proses=".$_GET['proses']."";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}

		if(isset($_GET['id_pro_jadi'])) {
			$sql=mysql_query("SELECT * FROM proses_jadi WHERE id_pro_jadi = '".mysql_real_escape_string($_GET['id_pro_jadi'])."' ");
			while($row = mysql_fetch_array($sql)) {
				$query_5=mysql_query("UPDATE product SET qty = qty - ".$row['qty']." where id_product = '".$row['id_jadi']."' ");	
				if(!$query_5) {
					$valid=0;
					$process_status="ERROR : Gagal Update Stok";
				}
			}
			$query_1=mysql_query("DELETE FROM proses_jadi WHERE id_pro_jadi='".mysql_real_escape_string($_GET['id_pro_jadi'])."' ");
			if(!$query_1) {
				echo "<script type='text/javascript'>alert('ERROR : Delete proses_jadi Failed')</script>";
			}

			$page = "input_process.php?mode=ubah&proses=".$_GET['proses']."";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
		
		if(isset($_GET['id_temp_bhn'])) {
			$query_1=mysql_query("DELETE FROM temp_bahan WHERE id_temp_bhn='".$_GET['id_temp_bhn']."' ");
			if(!$query_1) {
				echo "<script type='text/javascript'>alert('ERROR : Delete Temp Bahan Failed')</script>";
			}
			$_SESSION['status']="afterdelete";
			
			if($_SESSION['proses']=="")
			{$page = "?mode=add";}
			else
			{$page = "?mode=ubah&proses=".$_SESSION['proses']."";}

			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
		
		if(isset($_GET['id_temp_jadi'])) {
			$query_1=mysql_query("DELETE FROM temp_jadi WHERE id_temp_jadi='".$_GET['id_temp_jadi']."' ");
			if(!$query_1) {
				echo "<script type='text/javascript'>alert('ERROR : Delete Temp Jadi Failed')</script>";
			}
			$_SESSION['status']="afterdelete";
			
			if($_SESSION['proses']=="")
			{$page = "?mode=add";}
			else
			{$page = "?mode=ubah&proses=".$_SESSION['proses']."";}
			
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
	}

	if($_GET['mode']=='view'||$_GET['mode']=='ubah') {
		if(isset($_GET['proses'])) {
			$sql=mysql_query("SELECT * FROM proses WHERE id_proses ='".mysql_real_escape_string($_GET['proses'])."' ");
			while($row = mysql_fetch_array($sql))
			{
				$_SESSION['proses']=$row['id_proses'];
				$proses=$row['id_proses'];
				$tgl_proses=$row['tgl_proses'];
				$status=$row['status'];
				$ket=$row['ket'];
				$dibuat_oleh=$row['dibuat_oleh'];
			
			}
		} 
	}
	if ($_GET['mode']=='add') {
		$_SESSION['proses']="";
		$proses='new';
		$tgl_po=date('Y-m-d');
		$ket='';
	}
}
?>