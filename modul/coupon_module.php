<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (isset($_POST['save'])) {
	if ($_POST['pk'] == "") {
		$valid = 1;

		$query = mysqli_query($conn, "SELECT kode FROM coupon WHERE kode = '" . mysqli_real_escape_string($conn, $_POST['kode']) . "'");
		$cek = mysqli_num_rows($conn, $query);
		if ($cek > 0) {
			$valid = 0;
			$msg = "Kode kupon tidak boleh sama, Buat kupon gagal";
		} else {
			$valid = 1;
			$include = "";
			if (isset($_POST['include']) && $_POST['include'] !== "") {
				$number = count($_POST['include']);
				for ($i = 0; $i < $number; $i++) {
					if (trim($_POST['include'][$i] != '')) {
						$include = $include . '|' . $_POST['include'][$i];
					}
				}
				//$include=substr($include, 1);
			}

			$exclude = "";
			if (isset($_POST['exclude']) && $_POST['exclude'] !== "") {
				$number = count($_POST['exclude']);
				for ($i = 0; $i < $number; $i++) {
					if (trim($_POST['exclude'][$i] != '')) {
						$exclude = $exclude . '|' . $_POST['exclude'][$i];
					}
				}
				//$exclude=substr($exclude, 1);
			}

			$sql = mysqli_query($conn, "INSERT INTO coupon (kode, include, exclude, disc_rp, disc_prs, min_order, kurir, ongkir, freeongkir, min_qty, jml_pakai, untuk, mulai, expired, deskripsi) VALUES ('" . mysqli_real_escape_string($conn, $_POST['kode']) . "','" . mysqli_real_escape_string($conn, $include) . "','" . mysqli_real_escape_string($conn, $exclude) . "','" . mysqli_real_escape_string($conn, $_POST['disc_rp']) . "','" . mysqli_real_escape_string($conn, $_POST['disc_prs']) . "','" . mysqli_real_escape_string($conn, $_POST['min_order']) . "','" . mysqli_real_escape_string($conn, $_POST['kurir']) . "','" . mysqli_real_escape_string($conn, $_POST['ongkir']) . "','" . mysqli_real_escape_string($conn, $_POST['freeongkir']) . "','" . mysqli_real_escape_string($conn, $_POST['min_qty']) . "','" . mysqli_real_escape_string($conn, $_POST['jml_pakai']) . "','" . mysqli_real_escape_string($conn, $_POST['untuk']) . "','" . mysqli_real_escape_string($conn, $_POST['mulai']) . "','" . mysqli_real_escape_string($conn, $_POST['expired']) . "','" . mysqli_real_escape_string($conn, $_POST['deskripsi']) . "')");

			if ($sql) {
				$valid = 1;
				$msg = "Update Coupon Success";
			} else {
				$valid = 0;
				$msg = "Update Coupon Failed";
			}
		}
	} else {
		$valid = 1;

		$include = "";
		if (isset($_POST['include']) && $_POST['include'] !== "") {
			$number = count($_POST['include']);
			for ($i = 0; $i < $number; $i++) {
				if (trim($_POST['include'][$i] != '')) {
					$include = $include . '|' . $_POST['include'][$i];
				}
			}
			//$include=substr($include, 1);
		}

		$exclude = "";
		if (isset($_POST['exclude']) && $_POST['exclude'] !== "") {
			$number = count($_POST['exclude']);
			for ($i = 0; $i < $number; $i++) {
				if (trim($_POST['exclude'][$i] != '')) {
					$exclude = $exclude . '|' . $_POST['exclude'][$i];
				}
			}
			//$exclude=substr($exclude, 1);
		}

		$sql = mysqli_query($conn, "UPDATE coupon SET include='" . mysqli_real_escape_string($conn, $include) . "',exclude='" . mysqli_real_escape_string($conn, $exclude) . "',disc_rp='" . mysqli_real_escape_string($conn, $_POST['disc_rp']) . "',disc_prs='" . mysqli_real_escape_string($conn, $_POST['disc_prs']) . "',min_order='" . mysqli_real_escape_string($conn, $_POST['min_order']) . "',kurir='" . mysqli_real_escape_string($conn, $_POST['kurir']) . "',ongkir='" . mysqli_real_escape_string($conn, $_POST['ongkir']) . "',freeongkir='" . mysqli_real_escape_string($conn, $_POST['freeongkir']) . "',min_qty='" . mysqli_real_escape_string($conn, $_POST['min_qty']) . "',jml_pakai='" . mysqli_real_escape_string($conn, $_POST['jml_pakai']) . "',untuk='" . mysqli_real_escape_string($conn, $_POST['untuk']) . "',mulai='" . mysqli_real_escape_string($conn, $_POST['mulai']) . "',expired='" . mysqli_real_escape_string($conn, $_POST['expired']) . "',deskripsi='" . mysqli_real_escape_string($conn, $_POST['deskripsi']) . "' WHERE kode='" . mysqli_real_escape_string($conn, $_POST['pk']) . "'");

		if ($sql) {
			$valid = 1;
			$msg = "Update Coupon Success";
		} else {
			$valid = 0;
			$msg = "Update Coupon Failed";
		}
	}

	if ($valid == 0) {
		rollback();
		echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../coupon.php';</script>";
	} else {
		commit();
		echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../coupon.php';</script>";
	}
}

if (isset($_POST['delete'])) {
	$valid = 1;
	$query = mysqli_query($conn, "DELETE FROM coupon WHERE kode='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
	if (!$query) {
		$valid = 0;
		$msg = "ERROR : Delete Coupon Failed";
	}

	if ($valid == 0) {
		rollback();
		echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../coupon.php';</script>";
	} else {
		commit();
		$msg = "Delete Data Success";
		echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../coupon.php';</script>";
	}
}
