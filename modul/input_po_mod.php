<?php
$valid=1;
if(isset($_POST['save'])) {
	$valid=1;
	begin();

	//MODUL SAVE PO / SAVE PO BARU
	if($_POST['id_po1']=="new") {
		//INSERT KE PO1
		if($valid==1) {
			$data=mysql_query("select sum(total) total from temp_ro2 where id_user = '".$_SESSION['id_user']."' order by waktu asc ");
			while($row = mysql_fetch_array($data)) {
				$total_ro1=$row['total'];
			}
			$id_ro1=generate_transaction_key('PO','PO',date('m'),date('Y'));
			$query_1=mysql_query("insert into po1 (id_po1,tgl_po,id_supplier,disc_nominal,disc_persen,ppn_persen,net_total,dibuat_oleh,dibuat_tgl,ket) values ('".$id_ro1."','".$_POST['tgl']."','".$_POST['supp']."','".$_POST['totaldiscnom']."','".$_POST['totaldiscprc']."','".$_POST['ppn_s']."','".$total_ro1."','".$_SESSION['id_user']."','".date('Y-m-d')."','".$_POST['ket']."') ");
			if(!$query_1) {
				$valid=0;
				$process_status="ERROR : Insert to PO1 failed";
			}
		}
		//INSERT KE PO2, AMBIL DARI TEMP_RO2
		if($valid==1) {
			$sql=mysql_query("select * from temp_ro2 where id_user = '".$_SESSION['id_user']."' order by waktu asc ");
			while($row = mysql_fetch_array($sql)) {
				if($valid==1) {
					$id_ro2=generate_transaction_key('PO2','PO2',date('m'),date('Y'));
					$query_2=mysql_query("insert into po2 (id_po2,id_po1,id_bahan,qty,harga,total,disc_nominal,disc_persen) values ('".$id_ro2."','".$id_ro1."','".$row['id_bahan']."','".$row['qty']."','".$row['harga']."','".$row['total']."','".$row['disc_nominal']."','".$row['disc_persen']."') ");
					if(!$query_2) {
						$valid=0;
						$process_status="ERROR : Insert to PO2 failed";
					}
				}	
			}	
		}
		//UPDATE STOK BARANG
		if($valid==1) {
			$sql=mysql_query("select * from temp_ro2 where id_user = '".$_SESSION['id_user']."' order by waktu asc ");
			while($row = mysql_fetch_array($sql)) {
				$query_5=mysql_query("update product set qty = qty + ".$row['qty']." where id_product = '".$row['id_bahan']."' ");	
				if(!$query_5) {
					$valid=0;
					$process_status="ERROR : Stock Update failed";
				}
			}
		}
			
		if($valid==1) {
			$query_5=mysql_query("delete from temp_ro2 where id_user = '".$_SESSION['id_user']."' ");	
			if(!$query_5) {
				$valid=0;
				$process_status="Error : Deleting Error";
			}
		}
		
		
		if($valid==0) {  
			rollback();
			echo "<script type='text/javascript'>alert('".$process_status."')</script>";
			$page = "input_po.php?mode=ubah";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
		else {	
			commit();
			$process_status="Save Success";
			echo "<script type='text/javascript'>alert('".$process_status."')</script>";
			$page = "input_po.php?mode=view&id_po=".$id_ro1."";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
	}

	//MODUL UPDATE / EDIT PO
	if($_POST['id_po1']!="new") {
		//UPDATE KE PO1
		if($valid==1) {	
			$query_1=mysql_query("update po1 set tgl_po='".$_POST['tgl']."',id_supplier='".$_POST['supp']."',disc_nominal='".$_POST['totaldiscnom']."',disc_persen='".$_POST['totaldiscprc']."',ppn_persen='".$_POST['ppn_s']."',net_total='".$_POST['net_num']."',dibuat_oleh='".$_SESSION['id_user']."',diubah_tgl='".date('Y-m-d')."',status='edited',ket='".$_POST['ket']."' where id_po1 = '".$_POST['id_po1']."' ");
			if(!$query_1) {
				$valid=0;
				$process_status="ERROR : PO1 Update failed";
			}
		}
		//INSERT KE PO2, AMBIL DARI TEMP_RO2
		if($valid==1) {
			$sql=mysql_query("select * from temp_ro2 where id_user = '".$_SESSION['id_user']."' order by waktu asc ");
			while($row = mysql_fetch_array($sql)) {
				if($valid==1) {
					$id_ro2=generate_transaction_key('PO2','PO2',date('m'),date('Y'));
					$query_2=mysql_query("insert into po2 (id_po2,id_po1,id_bahan,qty,harga,total,disc_nominal,disc_persen) values ('".$id_ro2."','".$_POST['id_po1']."','".$row['id_bahan']."','".$row['qty']."','".$row['harga']."','".$row['total']."','".$row['disc_nominal']."','".$row['disc_persen']."') ");
					if(!$query_2) {
						$valid=0;
						$process_status="ERROR : Insert to PO2 failed";
					}
				}	
			}	
		}
		//UPDATE STOK BARANG
		if($valid==1) {
			$sql=mysql_query("select * from po2 where id_po1 = '".$_POST['id_po1']."' ");
			while($row = mysql_fetch_array($sql)) {
				$query_5=mysql_query("update product set qty = qty + ".$row['qty']." where id_product = '".$row['id_bahan']."' ");	
				if(!$query_5) {
					$valid=0;
					$process_status="ERROR : Stock Update failed";
				}
			}
		}
			
		if($valid==1) {
			$query_5=mysql_query("delete from temp_ro2 where id_user = '".$_SESSION['id_user']."' ");	
			if(!$query_5) {
				$valid=0;
				$process_status="Eror : Deleting Error";
			}
		}
		
		if($valid==0) {  
			rollback();
			echo "<script type='text/javascript'>alert('".$process_status."')</script>";
			$page = "input_po.php?mode=view&id_po=".$_POST['id_po1']."";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
		else
		{	
			commit();
			$process_status="Saving success";
			echo "<script type='text/javascript'>alert('".$process_status."')</script>";
			$page = "input_po.php?mode=view&id_po=".$_POST['id_po1']."";
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
	}
}

//MODUL HAPUS, EDIT & VIEW
if(isset($_GET['mode'])) {
	if($_GET['mode'] == "hapus") {
		if(isset($_GET['id_po2'])) {
			$sql=mysql_query("select * from po2 where id_po2 = '".$_GET['id_po2']."' ");
			while($row = mysql_fetch_array($sql)) {
				$query_5=mysql_query("update product set qty = qty - ".$row['qty']." where id_product = '".$row['id_bahan']."' ");	
				if(!$query_5) {
					$valid=0;
					$process_status="ERROR : Stock update failed";
				}
			}
			$query_1=mysql_query("delete from po2 where id_po2='".$_GET['id_po2']."' ");
			if(!$query_1) {
				echo "<script type='text/javascript'>alert('ERROR : Delete PO2 Failed')</script>";
			}
			$_SESSION['status']="afterdelete";
			
			
			if($_SESSION['revisi']=="on"){
				$page = "input_po.php?mode=ubah&id_po=".$_GET['id_po']."&jenis=revisi";
				$sec = "1";
				$redirect=1;
				header("Refresh: $sec; url=$page");
			}
			else {
				$page = "input_po.php?mode=ubah&id_po=".$_GET['id_po']."";
				$sec = "1";
				$redirect=1;
				header("Refresh: $sec; url=$page");
			}
			
		}
		
		if(isset($_GET['id_ro2temp'])) {
			$query_1=mysql_query("delete from temp_ro2 where id_ro2temp='".$_GET['id_ro2temp']."' ");
			if(!$query_1) {
				echo "<script type='text/javascript'>alert('ERROR : Delete TEMP_RO2 Failed')</script>";
			}
			$_SESSION['status']="afterdelete";
			
			if($_SESSION['revisi']=="on") {	
				$page = "input_po.php?mode=ubah&id_po=".$_SESSION['$id_po1']."jenis=revisi";
			} else {
				if($_SESSION['$id_po1']=="")
				{$page = "input_po.php?mode=add";}
				else
				{$page = "input_po.php?mode=ubah&id_po=".$_SESSION['$id_po1']."";}
			}
			$sec = "1";
			$redirect=1;
			header("Refresh: $sec; url=$page");
		}
	}

	if($_GET['mode']=='view'||$_GET['mode']=='ubah') {
		if(isset($_GET['id_po'])) {
			$sql="select p.*,s.nm_supp,s.hp,s.email from po1 p join tb_supp s on p.id_supplier=s.id_supp where p.id_po1 ='".$_GET['id_po']."' ";
			$data=mysql_query($sql);
			while($row = mysql_fetch_array($data))
			{
				$_SESSION['$id_po1']=$row['id_po1'];
				$id_po1=$row['id_po1'];
				$tgl_po=$row['tgl_po'];
				$id_supp=$row['id_supplier'];
				$nm_supp=$row['nm_supp'];
				$status=$row['status'];
				$ppn_persen=$row['ppn_persen'];
				$ket=$row['ket'];
				$dibuat_oleh=$row['dibuat_oleh'];
				$disc_nominal=$row['disc_nominal'];
				$disc_persen=$row['disc_persen'];
				$net_total=$row['net_total'];
			
			}
			$sql="select sum(total) as total from po2 where id_po1 ='".$_GET['id_po']."' ";
			$data=mysql_query($sql);
			while($row = mysql_fetch_array($data))
			{
				$total=$row['total'];
			}
				$ppn_persen=$total*($ppn_persen/100);
		} 
	}
	if ($_GET['mode']=='add') {
		$_SESSION['$id_po1']="";
		$id_po1='new';
		$tgl_po=date('Y-m-d');
		$id_supp='';
		$status='n';
		$ket='';
		$ppn_persen=10;
		$disc_nominal=0;
		$disc_persen=0;
		$net_total=0;
		$sql="select sum(total) as total from temp_ro2 where id_user ='".$_SESSION['id_user']."' ";
		$data=mysql_query($sql);
		while($row = mysql_fetch_array($data))
		{
			$total=$row['total'];
		}
		if($total<1)
		{
			$total=0;
		}
	}
}
?>