<?php


include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";
session_start();

$id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);

//onload="window.print();"


// $query = "select c.kredit_hari,c.sales, c.telp,c.alamat alamat_bill,c.alamat3 alamat_bill2,s.no_sj,s.flag,s.ket,c.no_npwp,c.nama_npwp,s.internal_notes,s.persen_pajak,s.amt_pajak,s.disc_tot,s.dpp,s.final_total,s.total,s.person,s.jenis_transaksi,s.id_cabang,s.no_po,u.kode,s.invoice,c.company,c.nm_cust,c.city,s.kode_kasir,c.alamat,c.telp,c.hp,DATE_FORMAT(s.date_i,'%d/%m/%Y') as tgl from sj s 
// 													left join tb_cust c on c.id_cust2 = s.kode_c 
// 													left join tb_user u on u.id_user = s.id_user 
// 													where s.no_sj = '" . $_GET['no_sj'] . "' ";



// $get_barang = mysql_query($query);
// while ($row1 = mysql_fetch_array($get_barang)) {
// 	$id_cabang = $row1['id_cabang'];
// 	$no_sj = $row1['no_sj'];
// 	$sales = $row1['sales'];
// 	$alamat_bill = $row1['alamat_bill'];
// 	$alamat_bill2 = $row1['alamat_bill2'];
// 	$no_po = $row1['no_po'];
// 	$company = $row1['company'];
// 	$nm_cust = $row1['nm_cust'];
// 	$alamat = $row1['alamat'];
// 	$person = $row1['person'];
// 	$ket = $row1['ket'];
// 	$internal_notes = $row1['internal_notes'];
// 	$persen_pajak = $row1['persen_pajak'];
// 	$amt_pajak = $row1['amt_pajak'];
// 	$disc_tot = $row1['disc_tot'];
// 	$dpp = $row1['dpp'];
// 	$final_total = $row1['final_total'];
// 	$total = $row1['total'];
// 	$city = $row1['city'];
// 	$telp = $row1['telp'];
// 	$invoice = $row1['invoice'];
// 	$kode = $row1['kode'];
// 	$no_npwp = $row1['no_npwp'];
// 	$nama_npwp = $row1['nama_npwp'];
// 	$kode_kasir = $row1['kode_kasir'];
// 	$kredit_hari = $row1['kredit_hari'];
// 	$jenis_transaksi = $row1['jenis_transaksi'];
// 	$tgl = $row1['tgl'];
// 	$telp = $row1['telp'];
// 	$flag = $row1['flag'];
// 	$hp = $row1['hp'];
// }

$company = "";
$alamat = "";
$kredit_hari = 0;
$sales = "";
$sql_get_barang_keluar = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'");
if ($row_barang_keluar = mysqli_fetch_array($sql_get_barang_keluar)) {
	$id_customer = $row_barang_keluar['id_customer'];
	$sql_get_customer = mysqli_query($conn, "SELECT * FROM tb_customer WHERE id_customer = '" . $id_customer . "'");
	if ($row_customer = mysqli_fetch_array($sql_get_customer)) {
		$company = $row_customer['nama_customer'];
		$alamat = $row_customer['alamat_lengkap'];
		$telp = $row_customer['no_telp'];
	}

	$kredit_hari = $row_barang_keluar['jatuh_tempo'];
	$dibuat_oleh = $row_barang_keluar['dibuat_oleh'];
	$tgl = $row_barang_keluar['tgl_transaksi'];
	$sql_karyawan = mysqli_query($conn, "SELECT * FROM tb_karyawan WHERE user_id = '" . $dibuat_oleh . "'");
	if ($row_karyawan = mysqli_fetch_array($sql_karyawan)) {
		$sales = $row_karyawan['nama_lengkap'];
	}
}

$judul = "
<span style='font-size:13px;font-weight:bold;'>
<center>
PT. SATU DUA TIGA
<BR>GUDANG EMAS NO. 8 JL. KERETA, MUTIARA Telp.(021)-11111111
<BR><U> INVOICE PENJUALAN</U>
</center>
</span>
";
?>



<!DOCTYPE html>
<html>

<head>
	<title>Print SJ</title>
	<style type="text/css">
		@font-face {
			font-family: Verdana, Geneva, sans-serif;
		}


		body {
			font-family: Verdana, Geneva, sans-serif;
			letter-spacing: 0.09em;
		}

		@media print {


			html,
			body {

				display: block;
				font-family: Verdana, Geneva, sans-serif;
				letter-spacing: 0.09em;

			}

			fieldset {
				border: 1px solid black;
			}

		}




		table {
			border-collapse: collapse;
		}

		.bordered {
			border: 1px solid black;
		}

		@media print {
			#w_a {
				display: none;
			}
		}
	</style>
</head>

<body>
	<table border=0 width="1000px" style="border:none;">

		<tr>
			<td colspan="2">
				<?php echo $judul; ?>
				<br>
			</td>
		</tr>
		<tr>
			<td width="50%">

				<fieldset style="width: 92%;height:120px;">
					<legend><b style='font-size:15px;font-weight:bold;background:white;'>Tagihan Untuk :</b></legend>
					<b style='font-size:19px;font-weight:bold;'><?php echo strtoupper($company); ?></b><br>
					<span style='font-size:14px;'><?php echo strtoupper($alamat); ?><br>
						<b>Telp.</b> <?php echo $telp; ?>
					</span>
				</fieldset>


			</td>
			<td width="50%">

				<fieldset style="width: 92%;height:120px;">

					<legend><b style='font-size:15px;font-weight:bold;background:white;'>Dikirim Ke :</b></legend>
					<b style='font-size:19px;font-weight:bold;'><?php echo strtoupper($company); ?></b>
					<span style='font-size:14px;'>
						<p><?php echo strtoupper($alamat); ?></p>
					</span>
				</fieldset>

			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-top:12px;">

				<table class="bordered" style="clear: both;" style="border:1px solid black;margin-top:12px;" width="99%">

					<tr style='font-size:19px;'>
						<th class="bordered">
							<center><b>Sales</b></center>
						</th>
						<th class="bordered">
							<center><b>No. Surat Jalan</b></center>
						</th>
						<th class="bordered">
							<center><b>Tanggal</b></center>
						</th>
						<th class="bordered">
							<center><b>Termin</b></center>
						</th>
					</tr>

					<tr style='font-size:19px;'>
						<td class="bordered">
							<center><?php echo strtoupper($sales); ?></center>
						</td>
						<td class="bordered">
							<center><?php echo $id_transaksi; ?></center>
						</td>
						<td class="bordered">
							<center><?php echo $tgl; ?></center>
						</td>
						<td class="bordered">
							<center><?php echo $kredit_hari; ?> HARI</center>
						</td>
					</tr>


				</table>


				<table class="bordered" width="99%" style="">
					<tr style='font-size:19px;font-weight:bold;'>
						<td class="bordered" width="5%"><b>
								<center>NO.</center>
							</b></td>
						<td class="bordered" width="60%"><b>
								<center>DESKRIPSI</center>
							</b></td>
						<td class="bordered" width="15%"><b>
								<center>QTY ROLL</center>
							</b></td>

					</tr>


					<?php
					$x = 0;
					$total = 0;
					$jatah = 6;

					$total_roll = 0;
					$query = "SELECT a.*,b.nama_bahan FROM tb_barang_keluar a JOIN tb_bahan b ON b.id_bahan = a.id_bahan WHERE a.id_transaksi = '" . $id_transaksi . "'";

					$get_barang = mysqli_query($conn, $query);
					while ($row1 = mysqli_fetch_array($get_barang)) {

						//for($i=0;$i<50;$i++)
						//{


						echo '<tr  style="height:20px;font-size:15px;">';
						echo '<td  style="text-align:right;">' . ($x + 1) . '&nbsp;&nbsp;</td>';

						echo '<td  style="padding-top:0px;padding-bottom:0px;" > &nbsp;&nbsp; ' . strtoupper($row1['nama_bahan']) . ' ( @ ' . round($row1['berat']) . ' YARD)</td>';

						echo '<td style="padding-top:0px;text-align:right;">' . curr_replace($row1['qty']) . '&nbsp; ROLL &nbsp;&nbsp;&nbsp;&nbsp;</td>';

						echo '</tr>';
						$x++;
						$total_roll += $row1['qty'];

						//}												

					}

					if ($jatah - $x > 0) {
						for ($i = 1; $i <= ($jatah - $x); $i++) {
							echo '<tr  style="height:22px;font-size:12px;"><td colspan=4>
							
							</td></tr>';
						}
					}



					?>

<tr  style="height:22px;font-size:15px;"><td colspan=4 align="right">
				<b>TOTAL  : <?= $total_roll ?> ROLL </b>
							</td></tr>
				</table>
				
				
				<table width="100%">
					<tr>
						<td width="25%" style="font-size:17px;font-weight:bold;">
							<center>Disiapkan Oleh,</center>
						</td>
						<td width="25%" style="font-size:17px;font-weight:bold;">
							<center>Checker,</center>
						</td>
						<td width="25%" style="font-size:17px;font-weight:bold;">
							<center>Driver,</center>
						</td>
						<td width="25%" style="font-size:17px;font-weight:bold;">
							<center>Diterima Oleh,</center>
						</td>
					</tr>
				</table>

			</td>
		</tr>
	</table>




</body>

</html>