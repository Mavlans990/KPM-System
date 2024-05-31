<?php
	ob_start();
	session_start();

	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";
		
	
		
if(isset($_POST['bahan']))
{

	$bahan=mysql_real_escape_string($_POST['bahan']);
	$qty_bhn=mysql_real_escape_string($_POST['qty_bhn']);

	$query=mysql_query("SELECT id_product FROM product WHERE nm_product = '".$bahan."'");
	$cell=mysql_fetch_array($query);

	$id_pr=$cell['id_product'];

	$id_bhntemp=generate_key('TMPB','TMPB',date('n'),date('Y'));
	$exec=mysql_query("INSERT INTO temp_bahan (id_temp_bhn, id_bahan, qty, id_user, waktu) VALUES ('".$id_bhntemp."','".$id_pr."','".$qty_bhn."','".$_SESSION['id_user']."','".date("H:i:s")."')");

	$fetch= mysql_query("SELECT t.*, p.nm_product FROM temp_bahan t LEFT JOIN product p ON t.id_bahan = p.id_product
						where t.id_user='".$_SESSION['id_user']."' order by t.waktu desc");
	$row=mysql_fetch_array($fetch);
	
	echo "
	<tr>
		<td>".$row['nm_product']."</td>
		<td align='center'>".floatval($row['qty'])."</td>
		<td>
			<a href='?mode=hapus&id_temp_bhn=".$row['id_temp_bhn']."' class='btn btn-small text-danger hapus_button'><i class='fas fa-trash'></i> Hapus</a>
		</td>
	</tr>
	";

}
ob_flush();
?>