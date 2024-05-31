<?php
session_start();
include "../lib/koneksi.php";
include "../lib/format.php";
include "../lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
	header('Location:index.php');
}

$from = date("Y-m-d");
$to = "";
$supp = "";
$bah = "";
$nama_customer = '';
$nama_bahan = '';
$non_stock = "";
if (isset($_GET['tgl_to'])) {
	$from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
	$to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
	$supp = mysqli_real_escape_string($conn, $_GET['supplier']);
	$bah = mysqli_real_escape_string($conn, $_GET['bahan']);
	$non_stock = mysqli_real_escape_string($conn, $_GET['nonstock']);

	// if ($supp != '') {
	// 	$select_supplier = "SELECT nama_customer FROM tb_customer where id_customer = '" . $supp . "' ";
	// 	$query_supplier = mysqli_query($conn, $select_supplier);
	// 	$row_supplier = mysqli_fetch_array($query_supplier);
	// 	$nama_customer = $row_supplier['nama_customer'];
	// }

	if ($bah != '') {
		$select_bahan = "SELECT nama_bahan FROM tb_bahan where id_bahan = '" . $bah . "' ";
		$query_bahan = mysqli_query($conn, $select_bahan);
		$row_bahan = mysqli_fetch_array($query_bahan);
		$nama_bahan = $row_bahan['nama_bahan'];
	} else {
		$nama_bahan = "All";
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Print Excel</title>
</head>

<body>
	<style type="text/css">
		body {
			font-family: sans-serif;
		}

		table {
			margin: 20px auto;
			border-collapse: collapse;
		}

		table th,
		table td {
			border: 1px solid #3c3c3c;
			padding: 3px 8px;

		}

		a {
			background: blue;
			color: #fff;
			padding: 8px 10px;
			text-decoration: none;
			border-radius: 2px;
		}
	</style>

	<?php
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=Laporan Barang Keluar.xls");
	?>

	<!-- <center> -->
	<h3>LAPORAN BARANG KELUAR</h3>
	<h6><?= date('d/m/Y', strtotime($from)) ?> Sampai <?= date('d/m/Y', strtotime($to)) ?><br>
		<!-- CUSTOMER: <?= $nama_customer ?> <br> -->
		BAHAN: <?= $nama_bahan ?><br>
		<?php
		if ($non_stock !== "") {
			if ($non_stock == 1) {
				echo "
                            Tipe : Non-Stock
                        ";
			} else {
				echo "
                            Tipe : Stock
                        ";
			}
		}
		?><br></h6>
	<!-- </center> -->

	<table border="1">
		<tr>
			<th class="text-center">Tanggal</th>
			<th class="text-center">No. Transaksi</th>
			<th class="text-center">Item</th>
			<th class="text-right">Qty</th>
			<th class="text-center">UOM</th>
			<th class="text-right">Price</th>
			<th class="text-right">Total</th>
		</tr>
		<?php
		if (isset($_GET['tgl_from'])) {
			$where_branch = "";
			if ($_SESSION['group'] !== "super") {
				$where_branch = " and id_cabang = '" . $_SESSION['branch'] . "'";
			}

			$tgl_from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
			$tgl_to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
			$supplier = mysqli_real_escape_string($conn, $_GET['supplier']);
			$bahan = mysqli_real_escape_string($conn, $_GET['bahan']);
			$cabang = mysqli_real_escape_string($conn, $_GET['cabang']);
			$non_stock = mysqli_real_escape_string($conn, $_GET['nonstock']);

			$filter_supplier = "";
			$filter_bahan = "";
			$filter_cabang = "";
			$filter_non_stock = "";

			if ($supplier !== "") {
				$supp = "";
				$query_supp = mysqli_query($conn, "SELECT id_customer,nama_customer FROM tb_customer WHERE nama_customer LIKE '%" . $supplier . "%'");
				while ($row_supp = mysqli_fetch_array($query_supp)) {
					$supp = $supp . ",'" . $row_supp['id_customer'] . "'";
				}
				$filter_supplier = " AND id_customer IN (''" . $supp . ")";
			}
			if ($bahan !== "") {
				$filter_bahan = " AND id_bahan = '" . $bahan . "'";
			}

			if ($cabang != "" && $cabang > 0) {
				$array = explode("|", $cabang);
				$jml = count($array);
				for ($i = 0; $i < $jml; $i++) {
					if ($i == 0) {
						$cabangasli = "'" . $array[0] . "'";
					} else {
						$cabangasli = $cabangasli . ",'" . $array[$i] . "'";
					}
				}

				$filter_cabang = " and id_cabang in (" . $cabangasli . ") ";
			}

			if ($non_stock !== "") {
				$filter_non_stock = " AND non_stock = '" . $non_stock . "'";
			}

			$grand_total = 0;

			$source = "";

			// " . $where_branch . "
			$select_keluar = "SELECT * FROM tb_barang_keluar WHERE (tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "') " . $filter_supplier . " " . $filter_bahan . " " . $filter_cabang . "  " . $filter_non_stock . " and status_keluar <> 'd' GROUP BY id_transaksi";
			$query_keluar = mysqli_query($conn, $select_keluar);
			while ($row_keluar = mysqli_fetch_array($query_keluar)) {

				// $where_branch .
				$select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $row_keluar['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang .  $filter_non_stock;
				$query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
				$jum_barang_keluar = mysqli_num_rows($query_barang_keluar);

				$select_supplier = "SELECT * FROM tb_customer WHERE id_customer = '" . $row_keluar['id_customer'] . "'";
				$query_supplier = mysqli_query($conn, $select_supplier);
				$data_supplier = mysqli_fetch_array($query_supplier);
				

				if ($row_keluar['source_customer'] == "facebook") {
					$source = "Source : Facebook <br>";
				}
				if ($row_keluar['source_customer'] == "instagram") {
					$source = "Source : Instagram <br>";
				}
				if ($row_keluar['source_customer'] == "google") {
					$source = "Source : Google <br>";
				}
				if ($row_keluar['source_customer'] == "marketplace") {
					$source = "Source : Market Place <br>";
				}
				if ($row_keluar['source_customer'] == "olx") {
					$source = "Source : OLX <br>";
				}
				if ($row_keluar['source_customer'] == "youtube") {
					$source = "Source : Youtube <br>";
				}
				if ($row_keluar['source_customer'] == "walk_in") {
					$source = "Source : Walk In <br>";
				}
				if ($row_keluar['source_customer'] == "referensi") {
					$source = "Source : Referensi <br>";
				}
				if ($row_keluar['source_customer'] == "repeat_order") {
					$source = "Source : Repeat Order <br>";
				}
				if ($row_keluar['source_customer'] == "club_mobil") {
					$source = "Source : Club Mobil <br>";
				}

				$tipe_stock = "<span>Tipe : Stock</span>";
				if ($row_keluar['non_stock'] == 1) {
					$tipe_stock = "<span>Tipe : Non-Stock</span>";
				}

				if($row_keluar["jenis_transaksi"] == "penjualan"){
					$jns = "(Penjualan) ";
				}elseif($row_keluar["jenis_transaksi"] == "return"){
					$jns = "(Return) ";
				}else{
					$jns = " ";
				}

				echo '
						<tr>
							<td style="vertical-align:center;" class="text-center" rowspan="' . $jum_barang_keluar . '">' . date("d/m/Y", strtotime($row_keluar['tgl_transaksi'])) . '</td>
							<td style="vertical-align:center;" class="text-center" rowspan="' . $jum_barang_keluar . '">
								' . $row_keluar['id_transaksi'] . ' ' . $jns . '<br>  
								' . $data_supplier['nama_customer'] . '<br>
								' . $source . '
								' . $tipe_stock . '
							</td>
							';

				while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {

					$query_bahan = mysqli_query($conn, "
						SELECT nama_bahan
						FROM tb_bahan 
						WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'
					");
					$data_bahan = mysqli_fetch_array($query_bahan);

					echo '
									<td style="vertical-align:center;" class="text-center">' . $data_bahan['nama_bahan'] . '</td>
									<td style="vertical-align:center;" class="text-right">' . $row_barang_keluar['qty'] . ' Roll</td>
									<td style="vertical-align:center;" class="text-center">' . number_format($row_barang_keluar['berat']) . ' ' . $row_barang_keluar['uom'] . '</td>
									<td style="vertical-align:center;" class="text-left"><span style="float:right">' . number_format($row_barang_keluar['harga'], 2) . '</span></td>
									<td style="vertical-align:center;" class="text-left"><span style="float:right">' . number_format($row_barang_keluar['total'], 2) . '</span></td>
								</tr>
							';
				}

				// . $where_branch
				$select_total = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $row_keluar['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang ;
				$query_total = mysqli_query($conn, $select_total);
				$data_total = mysqli_fetch_array($query_total);

				$total = $data_total['total'];
				$ppn = $total * $row_keluar['ppn'] / 100;

				echo '
						<tr class="bg-primary text-white">
							<th colspan="2" class="text-right text-right text-white">Sub Total</th>
							<td class="text-left"><span style="float:right">' . number_format($total, 2) . '</span></td>
							<th class="text-center text-white">PPN ' . $row_keluar['ppn'] . ' %</th>
							<td class="text-left"><span style="float:right">' . number_format($ppn, 2) . '</span></td>
							<th class="text-right text-white">Total</th>
							<td class="text-left"><span style="float:right">' . number_format($total + $ppn, 2) . '</span></td>
						</tr>
					';

				$grand_total = $grand_total + ($total + $ppn);
			}

			echo '
					<tr class="bg-success text-white">
						<th colspan="2" class="text-right text-white">Grand Total</th>
						<th colspan="4" class="text-right text-white"></th>
						<td class="text-left"><span style="float:right">' . number_format($grand_total) . '</span></td>
					</tr>
				';
		}
		?>
	</table>
</body>

</html>