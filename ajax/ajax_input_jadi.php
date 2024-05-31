<?php
	ob_start();
	session_start();

	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";
		
	
		
if(isset($_POST['jadi']))
{

	$jadi=mysql_real_escape_string($_POST['jadi']);
	$qty_jadi=mysql_real_escape_string($_POST['qty_jadi']);

	$query=mysql_query("SELECT id_product FROM product WHERE nm_product = '".$jadi."'");
	$cell=mysql_fetch_array($query);

	$id_pr=$cell['id_product'];

	$id_bhntemp=generate_key('TMPJ','TMPJ',date('n'),date('Y'));
	$exec=mysql_query("INSERT INTO temp_jadi (id_temp_jadi, id_jadi, qty, id_user, waktu) VALUES ('".$id_bhntemp."','".$id_pr."','".$qty_jadi."','".$_SESSION['id_user']."','".date("H:i:s")."')");

	$fetch= mysql_query("SELECT t.*, p.nm_product FROM temp_jadi t LEFT JOIN product p ON t.id_jadi = p.id_product
						where t.id_user='".$_SESSION['id_user']."' order by t.waktu desc");
	$row=mysql_fetch_array($fetch);
	
	echo "
	<tr>
		<td>".$row['nm_product']."</td>
		<td align='center'>".floatval($row['qty'])."</td>
		<td>
			<a href='input_so.php?mode=hapus&id_bhntemp=".$row['id_temp_jadi']."' class='btn btn-small text-danger hapus_button'><i class='fas fa-trash'></i> Hapus</a>
		</td>
	</tr>
	";

}
ob_flush();
?>