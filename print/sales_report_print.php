<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
	header('Location:../login.php');
}

header("Content-type: application/vnd-ms-excel");

header("Content-Disposition: attachment; filename=SalesReport.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>
<table border="1" style="border: 1px solid black">
	<thead>
		<tr>
			<th>No</th>
			<th>Tanggal</th>
			<th>Invoice</th>
			<th>Nama</th>

			<th>Nama Produk</th>
			<th>QTY</th>
			<th>Harga</th>
			<th>Net Total</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$n = 1;

		$sql2 = mysqli_query($conn, "SELECT a.id_sj1, a.tgl_sj, a.nm_cust, c.total, c.id_sj1 , c.no_seri , c.id_bahan, c.qty, c.harga, c.total
		FROM sj2 c
		JOIN sj1 a  ON c.id_sj1=a.id_sj1
		WHERE a.tgl_sj >= '" . mysqli_real_escape_string($conn, $_GET['tgl1']) . "' 
		AND a.tgl_sj <= '" . mysqli_real_escape_string($conn, $_GET['tgl2']) . "' 
		ORDER BY a.tgl_sj");

		while ($row = mysqli_fetch_array($sql2)) {
			if ($row['no_seri'] == "decal") {
				$select_product = "SELECT * FROM product WHERE id_product = '" . $row['id_bahan'] . "'";
			} else {
				$select_product = "SELECT * FROM product_lain WHERE id_product = '" . $row['id_bahan'] . "'";
			}

			$query_product = mysqli_query($conn, $select_product);
			$data_product = mysqli_fetch_array($query_product);
			echo '
		<tr>
			<td style="vertical-align:middle">' . $n . '</td>
			<td style="vertical-align:middle">' . date('d-m-Y', strtotime($row['tgl_sj'])) . '</td>
			<td style="vertical-align:middle">' . $row['id_sj1'] . '</td>
			<td style="vertical-align:middle">' . $row['nm_cust'] . '</td>
			<td style="vertical-align:middle">' . $data_product['nm_product'] . '</td>
			<td style="vertical-align:middle">' . $row['qty'] . '</td>
			<td style="vertical-align:middle">Rp. ' . number_format($row['harga']) . '</td>
			<td style="vertical-align:middle">Rp. ' . number_format($row['total']) . '</td>
		</tr>
			';
			$n++;
		}
		$query3 = mysqli_query($conn, "SELECT a.id_sj1,a.tgl_sj,b.id_sj1,sum(b.total) AS total FROM sj1 a JOIN sj2 b ON a.id_sj1=b.id_sj1 WHERE a.tgl_sj >= '" . mysqli_real_escape_string($conn, $_GET['tgl1']) . "' AND a.tgl_sj <= '" . mysqli_real_escape_string($conn, $_GET['tgl2']) . "'");
		$row3 = mysqli_fetch_array($query3);
		echo '
			<tr>
			<td colspan="7" style="text-align:right;font-size:20px;"><b>Grand Total</b></td>
			<td style="vertical-align:middle;font-size:20px;"><b>Rp. ' . number_format($row3['total']) . '</b></td>
			</tr>
		';
		?>
	</tbody>
</table>