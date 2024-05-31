<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (isset($_POST['save'])) {
	if ($_POST['id'] == "") {
		$path = "";
		if ($_FILES['image']['name'] !== "") {
			move_uploaded_file($_FILES['image']['tmp_name'], "../../img/products/" . $_FILES['image']['name']);
			$path = "|img/products/" . $_FILES['image']['name'];
		}

		if ($_POST['kategori'] == "cat_velg") {
			$warna = $_POST['warna'];
		} else {
			$warna = "";
		}

		$insert = "INSERT INTO product(id_product,merk,jenis_motor,gambar,tipe,warna) VALUES(
			'" . uniqid() . "',
			'" . mysqli_real_escape_string($conn, $_POST['kategori']) . "',
			'" . mysqli_real_escape_string($conn, $_POST['kategori']) . "',
			'" . $path . "',
			'testimoni',
			'" . $warna . "'
		)";

		$query_insert = mysqli_query($conn, $insert);

		if ($query_insert) {
			echo "<script>alert('Simpan berhasil');window.location.href='../testimoni.php'</script>";
		} else {
			echo "<script>alert('Simpan gagal');window.location.href='../testimoni.php'</script>";
		}
	} else {
		$path = "";
		if ($_FILES['image']['name'] !== "") {
			move_uploaded_file($_FILES['image']['tmp_name'], "../../img/products/" . $_FILES['image']['name']);
			$path = "|img/products/" . $_FILES['image']['name'];
		}

		$path_file = "";
		if ($_FILES['image']['name'] !== "") {
			$path_file = "gambar = '" . $path . "',";
		}

		if ($_POST['kategori'] == "cat_velg") {
			$warna = "warna = '" . mysqli_real_escape_string($conn, $_POST['warna']) . "',";
		} else {
			$warna = "";
		}

		$update = "UPDATE product SET " . $path_file . " " . $warna . " merk = '" . mysqli_real_escape_string($conn, $_POST['kategori']) . "',jenis_motor = '" . mysqli_real_escape_string($conn, $_POST['kategori']) . "' WHERE id_product = '" . mysqli_real_escape_string($conn, $_POST['id']) . "'";

		$query_update = mysqli_query($conn, $update);

		if ($query_update) {
			echo "<script>alert('Ubah berhasil');window.location.href = '../testimoni.php';</script>";
		} else {
			echo "<script>alert('Ubah gagal');window.location.href = '../testimoni.php';</script>";
		}
	}
}

if (isset($_POST['delete'])) {
	$valid = 1;
	$query = mysqli_query($conn, "DELETE FROM product WHERE id_product='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
	if (!$query) {
		$valid = 0;
		$msg = "ERROR : Hapus data gagal";
	}

	if ($valid == 0) {
		rollback();
		echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../testimoni.php';</script>";
	} else {
		commit();
		$msg = "Hapus data berhasil";
		echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../testimoni.php';</script>";
	}
}
