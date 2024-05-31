<?php
	ob_start();
	session_start();

	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";
		
	
		
if(isset($_POST['product']))
{

	$product=mysql_real_escape_string($_POST['product']);
	$qty=mysql_real_escape_string($_POST['qty']);
	$disc_nominal=mysql_real_escape_string($_POST['disc_nominal']);
	$disc_persen=mysql_real_escape_string($_POST['disc_persen']);
	$harga=mysql_real_escape_string($_POST['harga']);
	$total=$harga*$qty-($disc_nominal*$qty)-($harga*$disc_persen*$qty/100);

	$query=mysql_query("select id_product from product where nm_product = '".$product."'");
	$cell=mysql_fetch_array($query);

	$id_pr=$cell['id_product'];

	$id_so2temp=generate_key('SO2T','SO2T',date('n'),date('Y'));
	$exec=mysql_query("insert into temp_so2 (id_so2temp,id_bahan,qty,harga,total,id_user,waktu,disc_nominal,disc_persen) 
				values('".$id_so2temp."','".$id_pr."','".$qty."','".$harga."','".$total."','".$_SESSION['id_user']."','".date("H:i:s")."','".$disc_nominal."','".$disc_persen."')");
	

	$fetch= mysql_query("select p.nm_product,a.id_so2temp,a.id_bahan,a.qty,a.harga,a.total,a.disc_nominal,a.disc_persen from  temp_so2 a
						left join product p on a.id_bahan = p.id_product
						where a.id_user='".$_SESSION['id_user']."' order by a.waktu desc 
						");
	$row=mysql_fetch_array($fetch);
	
	echo "
	<tr>
		<td width=300px>".$row['nm_product']."</td>
		<td width=50px align='center'>".floatval($row['qty'])."</td>
		<td width=150px align='right'>".money_idr($row['harga'])."</td>
		<td width=150px align='right'>".money_idr($row['disc_nominal'])."</td>
		<td width=80px align='right'>".$row['disc_persen']." %</td>
		<td width=200px align='right'>".money_idr($row['total'])."</td>
		<td width=150px>
			<a href='input_so.php?mode=hapus&id_so2temp=".$row['id_so2temp']."' class='btn btn-small text-danger hapus_button'><i class='fas fa-trash'></i> Hapus</a>
		</td>
	</tr>
	";

}
ob_flush();
?>