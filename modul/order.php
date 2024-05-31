<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

$hitungan = "n";
$flag = 0;

if (isset($_POST['delete'])) {
	if ($_POST['id_hapus'] !== "") {
		$select = mysqli_query($conn, "SELECT total,id_sj1 FROM sj2 WHERE id_sj2 ='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
		$sel = mysqli_fetch_array($select);

		$update = mysqli_query($conn, "UPDATE order_main SET amount = amount-" . $sel['total'] . " WHERE id_order ='" . $sel['id_order'] . "' ");

		if (!$update) {
			echo "<script type='text/javascript'>alert('Amount update failed');window.location.href = '../stock-out.php?kat=confirm';</script>";
		} else {
			$hapus = mysqli_query($conn, "DELETE FROM sj2 WHERE id_sj2 ='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
			if (!$hapus) {
				echo "<script type='text/javascript'>alert('Delete product failed');window.location.href = '../stock-out.php?kat=confirm';</script>";
			} else {
				$hapus2 = mysqli_query($conn, "DELETE FROM pengiriman WHERE id_order = '" . $sel['id_order'] . "'");
				if (!$hapus2) {
					echo "<script type='text/javascript'>alert('Shipment delete failed');window.location.href = '../stock-out.php?kat=confirm';</script>";
				} else {
					echo "<script type='text/javascript'>alert('Product delete success');window.location.href = '../stock-out.php?kat=confirm';</script>";
				}
			}
		}
	}
}

if (isset($_POST['konfirmasi'])) {
	if ($_POST['appr'] !== "") {
		$select = mysqli_query($conn, "SELECT status_sj FROM sj1 WHERE id_sj1 ='" . mysqli_real_escape_string($conn, $_POST['appr']) . "'");
		$sel = mysqli_fetch_array($select);
		if ($sel['status_sj'] == 'n') {
			$update = mysqli_query($conn, "UPDATE sj1 SET status_sj = 'c',midtrans_transaction_status = 'success', otorisasi_tgl = '" . date('Y-m-d H:i:s') . "', otorisasi_oleh = '" . $_SESSION['id_user'] . "' WHERE id_sj1 ='" . mysqli_real_escape_string($conn, $_POST['appr']) . "' ");
			if (!$update) {
				echo "<script type='text/javascript'>alert('Ubah status gagal');window.location.href = '../stock-out.php?kat=confirm';</script>";
			} else {
				echo "<script type='text/javascript'>alert('Ubah status berhasil');window.location.href = '../stock-out.php?kat=confirm';</script>";
			}
		} else {
			echo "<script type='text/javascript'>alert('Tolong konfirmasi pesanan sesuai urutan');window.location.href = '../stock-out.php?kat=confirm';</script>";
		}
	} else {
		echo "<script type='text/javascript'>alert('Konfirmasi pesanan gagal');window.location.href = '../stock-out.php?kat=confirm';</script>";
	}
}

if (isset($_POST['resionly'])) {
	if ($_POST['sendresi'] !== "") {
		$order = $_POST['sendresi'];
		$select = mysqli_query($conn, "SELECT status_sj FROM sj1 WHERE id_sj1 ='" . mysqli_real_escape_string($conn, $_POST['sendresi']) . "'");
		$sel = mysqli_fetch_array($select);
		if ($sel['status_sj'] == 'c') {
			$update = mysqli_query($conn, "UPDATE sj1 SET resi = '" . mysqli_real_escape_string($conn, $_POST['noresi']) . "' WHERE id_sj1 ='" . mysqli_real_escape_string($conn, $_POST['sendresi']) . "' ");
			if (!$update) {
				echo "<script type='text/javascript'>alert('Ubah status gagal');window.location.href = '../stock-out.php?kat=confirm';</script>";
				$flag = 0;
			} else {
				echo "<script type='text/javascript'>alert('Ubah status berhasil');window.location.href = '../stock-out.php?kat=confirm';</script>";
				$flag = 1;
			}
		} else {
			echo "<script type='text/javascript'>alert('Tolong konfirmasi pesanan sesuai urutan');window.location.href = '../stock-out.php?kat=confirm';</script>";
			$flag = 0;
		}
	} else {
		echo "<script type='text/javascript'>alert('Gagal Konfirmasi Pesanan');window.location.href = '../stock-out.php?kat=confirm';</script>";
		$flag = 0;
	}
}

if (isset($_POST['paket'])) {
	if ($_POST['sendresi'] !== "") {
		$order = mysqli_real_escape_string($conn, $_POST['sendresi']);
		$select = mysqli_query($conn, "SELECT a.status_sj,a.id_cust,b.total,b.qty,a.ongkir FROM sj1 a JOIN sj2 b ON a.id_sj1=b.id_sj1 WHERE a.id_sj1 ='" . mysqli_real_escape_string($conn, $_POST['sendresi']) . "'");
		$sel = mysqli_fetch_array($select);
		$id_cust = $sel['id_cust'];
		$total_belanja = $sel['total'] - $sel['ongkir'];

		if ($sel['status_sj'] == "c") {
			$update = mysqli_query($conn, "UPDATE sj1 SET status_sj = 's', resi = '" . mysqli_real_escape_string($conn, $_POST['noresi']) . "' WHERE id_sj1 ='" . mysqli_real_escape_string($conn, $_POST['sendresi']) . "' ");
			if (!$update) {
				echo "<script type='text/javascript'>alert('Ubah status gagal');window.location.href = '../stock-out.php?kat=confirm';</script>";
				$flag = 0;
			} else {
				echo "<script type='text/javascript'>alert('Konfirmasi pesanan berhasil');window.location.href = '../stock-out.php?kat=confirm';</script>";

				$flag = 1;

				$sql = "SELECT id_bahan,qty FROM sj2 WHERE id_sj1 = '" . mysqli_real_escape_string($conn, $_POST['sendresi']) . "' ";
				$data = mysqli_query($conn, $sql);
			}
		}
	} else {
		echo "<script type='text/javascript'>alert('Konfirmasi pesanan gagal');window.location.href = '../stock-out.php?kat=confirm';</script>";
		$flag = 0;
	}
}




$stok = 1;
if (isset($_POST['pesanan'])) {
	if ($_POST['send'] !== "") {
		$stok = 1;
		$order = mysqli_real_escape_string($conn, $_POST['send']);
		$select = mysqli_query($conn, "SELECT s.status_sj,s.id_cust,p.total FROM sj1 s JOIN sj2 p ON s.id_sj1=p.id_sj1 WHERE s.id_sj1 ='" . mysqli_real_escape_string($conn, $_POST['send']) . "'");
		$sel = mysqli_fetch_array($select);
		$id_cust = $sel['id_cust'];
		$total_belanja = $sel['total'];


		if ($sel['status_sj'] == 'c') {
			$update = mysqli_query($conn, "UPDATE sj1 SET status_sj = 's' WHERE id_sj1 ='" . mysqli_real_escape_string($conn, $_POST['send']) . "' ");
			if (!$update) {
				echo "<script type='text/javascript'>alert('Ubah status gagal');window.location.href = '../stock-out.php?kat=confirm';</script>";
			} else {

				$select_sj2 = "SELECT * FROM sj2 WHERE id_sj1 = '" . mysqli_real_escape_string($conn, $_POST['send']) . "'";
				$query_sj2 = mysqli_query($conn, $select_sj2);
				while ($row_sj2 = mysqli_fetch_array($query_sj2)) {
					$update = "UPDATE sj2 SET is_sinkron = 'c' WHERE id_sj1 = '" . $row_sj2['id_sj1'] . "'";
					$query_update = mysqli_query($conn, $update);
					if ($query_update) {
						$valid = 1;
					} else {
						$valid = 0;
					}
				}

				if ($valid == 1) {
					echo "<script type='text/javascript'>alert('Ubah status berhasil');window.location.href = '../stock-out.php?kat=confirm';</script>";
					$flag = 1;
				}
			}
		} else {
			echo "<script type='text/javascript'>alert('Tolong konfirmasi pesanan sesuai urutan');window.location.href = '../stock-out.php?kat=confirm';</script>";
			$flag = 0;
		}
	}
} else {
	echo "<script type='text/javascript'>alert('Konfirmasi pesanan gagal');window.location.href = '../stock-out.php?kat=confirm';</script>";
	$flag = 0;
}


if ($flag == 1) {
	$cari = mysqli_query($conn, "SELECT * FROM sj1 WHERE id_sj1='" . $order . "'");
	$dt = mysqli_fetch_array($cari);
	$email = $dt['id_cust'];

	$query = mysqli_query($conn, "SELECT sum(total) AS total FROM sj2 WHERE id_sj1 = '" . $order . "'");
	$data = mysqli_fetch_array($query);

	date_default_timezone_set("Asia/Bangkok");

	$from = "MIME-Version: 1.0\r\n";
	$from .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	$from .= "From: Istana Songket <orders@istanasongket.com>";
	$to = "" . $email . "";
	//$to = "stevantong@gmail.com";
	$subject = "Order (" . $order . ") has been sended ";
	$body = "


	<br>
	Hello! Order number (" . $order . ") has been sended.
	<br><br>
	
	No Order : " . $order . "<br>
	Customer : " . $dt['nm_cust'] . " <br>
	Email    : " . $dt['id_cust'] . " <br>
	Phone    : " . $dt['telp_cust'] . " <br>
	Address  : " . $dt['alamat'] . " <br>
	Post Code : " . $dt['kode_pos'] . " <br>
	Shipping Date : " . $dt['tanggal_pengiriman'] . "<br><br>

	<table border=1>
	<tr><td>No</td><td>Product Name</td><td>Price</td><td>Qty</td><td>Total</td></tr>
	";

	$berat = 0;
	$total = 0;
	$x = 1;
	$ongkir = 0;
	$fetch = mysqli_query($conn, "SELECT a.id_sj1,a.id_bahan,a.qty,a.harga,a.total,p.id_product,p.nm_product,b.id_sj1,b.id_cust FROM sj2 a JOIN product p ON a.id_bahan=p.id_product JOIN sj1 b ON a.id_sj1=b.id_sj1 WHERE a.id_sj1='" . $order . "'");
	while ($row = mysqli_fetch_array($fetch)) {

		$body = $body . '
		<tr>
		<td>' . $x . '</td>
		<td>' . $row['nm_product'] . '</td>
		<td>' . money_idr($row['harga']) . '</td>
		<td>' . $row['qty'] . 'Pcs </td>
		<td>' . money_idr($row['total']) . ' </td></tr>';
		$x++;
	}
	$total_ongkir = ceil($berat / 1000) * $ongkir;
	$x++;

	$body = $body . "</table>";

	'<br>';


	$body = $body . "" . $ket . "
	<br><br>
	
	
	<hr></hr><br>
	<b>Subtotal     : " . money_idr($data['total']) . "</b><br>
	<b>Shipment Fee : " . money_idr($dt['ongkir']) . "</b><br>
	<b>Grand Total  : " . money_idr($data['total'] + $dt['ongkir']) . "</b><br>
	<br>
	<hr></hr>

	
	<br>
	Thank you for shopping!

	";

	if (mail($to, $subject, $body, $from)) {
		$process_status = "success";
	} else {
		$process_status = "failed-email";
	}
	echo "<script type='text/javascript'>alert('" . $process_status . "');window.location.href = '../stock-out.php?kat=confirm';</script>";
}
