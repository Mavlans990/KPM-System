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
if (isset($_GET['tgl_to'])) {
	$from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
	$to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
	$supp = mysqli_real_escape_string($conn, $_GET['supplier']);
	$bah = mysqli_real_escape_string($conn, $_GET['bahan']);

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
	}
}

$supplier = "";
$get_supp = mysqli_real_escape_string($conn, $_GET['supplier']);
if($get_supp !== ""){
	$sql_get_supp = mysqli_query($conn, "SELECT nama_customer FROM tb_customer WHERE id_customer = '".$get_supp."'");
	if($row_supp = mysqli_fetch_array($sql_get_supp)){
		$supplier = $row_supp['nama_customer'];
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
	header("Content-Disposition: attachment; filename=Laporan Barang Masuk.xls");
	?>

	<!-- <center> -->
	<h3>LAPORAN BARANG MASUK</h3>
	<h6><?= date('d/m/Y', strtotime($from)) ?> Sampai <?= date('d/m/Y', strtotime($to)) ?><br>
		<?php if($supplier !== "") {
			echo "SUPPLIER : ".$supplier."<br>";
		} ?>
		BAHAN: <?= $nama_bahan ?><br></h6>
	<!-- </center> -->

	<table border="1">
		<tr>
			<th class="text-center">Tanggal</th>
			<th class="text-center">No. Transaksi</th>
			<th class="text-center">Item</th>
			<th class="text-center">Qty (Roll)</th>
			<th class="text-center">Yard</th>
			<th class="text-center">UOM</th>
			<th class="text-center">Price</th>
			<th class="text-center">Total</th>
		</tr>
		<?php
		if (isset($_GET['tgl_from'])) {
			$where_branch = "";

			$tgl_from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
			$tgl_to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
			$supplier = mysqli_real_escape_string($conn, $_GET['supplier']);
			$bahan = mysqli_real_escape_string($conn, $_GET['bahan']);

			$filter_supplier = "";
			$filter_bahan = "";
			$filter_cabang = "";

			if ($supplier !== "") {
				if(strpos($supplier," | ") == TRUE){
					$ex_id_supp = explode(" | ",$supplier);
					$id_supp = $ex_id_supp[1];

					$filter_supplier = " AND id_supplier = '".$id_supp."'";
				}
			}
			if ($bahan !== "") {
				$filter_bahan = " AND id_product = '" . $bahan . "'";
			}


			$grand_total = 0;

			$select_masuk = "SELECT * FROM tb_barang_masuk WHERE (tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "') " . $filter_supplier . " " . $filter_bahan . " " . $filter_cabang . " and status <> 'd' GROUP BY id_transaksi";

			$query_masuk = mysqli_query($conn, $select_masuk);

			while ($row_masuk = mysqli_fetch_array($query_masuk)) {

				$select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $row_masuk['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang . $where_branch;
				$query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
				$jum_barang_masuk = mysqli_num_rows($query_barang_masuk);

				$select_supplier = "SELECT * FROM tb_customer WHERE id_customer = '" . $row_masuk['id_supplier'] . "'";
				$query_supplier = mysqli_query($conn, $select_supplier);
				$data_supplier = mysqli_fetch_array($query_supplier);


				echo '
					<tr>
						<td class="text-center" rowspan="' . $jum_barang_masuk . '">' . date("d/m/Y", strtotime($row_masuk['tgl_transaksi'])) . '</td>
						<td class="text-center" rowspan="' . $jum_barang_masuk . '">
							' . $row_masuk['id_transaksi'] . '<br>  
							' . $data_supplier['nama_customer'] . '<br>
						</td>
						';

				while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
					$select_bahan = "SELECT id_bahan,nama_bahan 
									FROM tb_bahan 
									WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "'";
					$query_bahan = mysqli_query($conn, $select_bahan);
					$data_bahan = mysqli_fetch_array($query_bahan);
					echo '
								<td class="text-center">' . $data_bahan['nama_bahan'] . '</td>
								<td class="text-right">' . number_format($row_barang_masuk['qty']) . '</td>
								<td class="text-right">' . number_format($row_barang_masuk['berat']) . '</td>
								<td class="text-center">' . $row_barang_masuk['uom'] . '</td>
								<td class="text-left"><span style="float:right">' . number_format($row_barang_masuk['harga']) . '</span></td>
								<td class="text-left"><span style="float:right">' . number_format($row_barang_masuk['total']) . '</span></td>
							</tr>
						';
				}

				$select_total = "SELECT SUM(total) AS total FROM tb_barang_masuk WHERE id_transaksi = '" . $row_masuk['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang . $where_branch;
				$query_total = mysqli_query($conn, $select_total);
				$data_total = mysqli_fetch_array($query_total);

				$total = $data_total['total'];
				$ppn = $total * $row_masuk['ppn'] / 100;

				echo '
					<tr class="bg-primary text-white">
						<th colspan="2" class="text-right text-right text-white">Sub Total</th>
						<td class="text-left"><span style="float:right">' . number_format($total) . '</span></td>
						<th class="text-center text-white">PPN ' . $row_masuk['ppn'] . ' %</th>
						<td class="text-left" colspan="2"><span style="float:right">' . number_format($ppn) . '</span></td>
						<th class="text-right text-white">Total</th>
						<td class="text-left"><span style="float:right">' . number_format($total + $ppn) . '</span></td>
					</tr>
				';

				$grand_total = $grand_total + ($total + $ppn);
			}

			echo '
				<tr class="bg-success text-white">
					<th colspan="2" class="text-right text-white">Grand Total</th>
					<th colspan="5" class="text-right text-white"></th>
					<td class="text-left"><span style="float:right">' . number_format($grand_total) . '</span></td>
				</tr>
			';
		}
		?>
	</table>
</body>

</html>