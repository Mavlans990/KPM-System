<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (isset($_POST['save'])) {
	if ($_POST['id'] == "") {
		$ekstensi_diperbolehkan = array('png', 'jpg', 'bmp', 'jpeg', 'gif', 'PNG', 'JPG', 'BMP', 'JPEG', 'GIF');
		$gambar = $_FILES['image']['name'];
		$x = explode('.', $gambar);
		$ekstensi = strtolower(end($x));
		$ukuran = $_FILES['image']['size'];
		$file_tmp = $_FILES['image']['tmp_name'];


		move_uploaded_file($file_tmp, '../../images/slider/' . $gambar);
		$path = "images/slider/" . $gambar;

		$query = mysqli_query($conn, "INSERT INTO slider(id, jenis, path, text) VALUES ('','" . mysqli_real_escape_string($conn, $_POST['jenis']) . "','" . mysqli_real_escape_string($conn, $path) . "','" . mysqli_real_escape_string($conn, $_POST['text']) . "')");

		if ($query) {
			echo "<script type='text/javascript'>alert('Insert Success');window.location.href = '../slider.php';</script>";
		} else {
			echo "<script type='text/javascript'>alert('Insert Failed');window.location.href = '../slider.php';</script>";
		}
	} else {
		$jml = 0;
		if ($_FILES['image']['name'] == "") {
			$jml = 1;
		}
		$ekstensi_diperbolehkan = array('png', 'jpg', 'bmp', 'jpeg', 'gif', 'PNG', 'JPG', 'BMP', 'JPEG', 'GIF');
		$gambar = $_FILES['image']['name'];
		$x = explode('.', $gambar);
		$ekstensi = strtolower(end($x));
		$ukuran = $_FILES['image']['size'];
		$file_tmp = $_FILES['image']['tmp_name'];


		move_uploaded_file($file_tmp, '../../images/slider/' . $gambar);
		$path = "images/slider/" . $gambar;


		if ($jml == 0) {
			$path2 = " path='" . $path . "',";
		} else {
			$path2 = "";
		}

		$query = mysqli_query($conn, "UPDATE slider SET " . $path2 . " text='" . mysqli_real_escape_string($conn, $_POST['text']) . "' WHERE id='" . mysqli_real_escape_string($conn, $_POST['id']) . "' ");

		if ($query) {
			echo "<script type='text/javascript'>alert('Update Success');window.location.href = '../slider.php';</script>";
		} else {
			echo "<script type='text/javascript'>alert('Update Failed');window.location.href = '../slider.php';</script>";
		}
	}
}

if (isset($_POST['delete'])) {
	$valid = 1;
	$query = mysqli_query($conn, "DELETE FROM slider WHERE id='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
	if (!$query) {
		$valid = 0;
		$msg = "ERROR : Delete Data Failed";
	}

	if ($valid == 0) {
		rollback();
		echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../slider.php';</script>";
	} else {
		commit();
		$msg = "Delete Data Success";
		echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href = '../slider.php';</script>";
	}
}
