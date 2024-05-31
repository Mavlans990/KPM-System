<?php
function generate_transaction_key($prefix, $nama, $bulan, $tahun)
{
	include "koneksi.php";
	$flag = 0;
	$ambil_data = mysqli_query($conn, "select * from tb_setup where nama_setup='" . $nama . "' and ket1='" . $bulan . "' and ket2='" . $tahun . "'");
	while ($row = mysqli_fetch_array($ambil_data)) {
		$flag = 1;
		$no_id = "" . $prefix . "-" . $tahun . "-" . $bulan . "-" . str_pad($row['ket3'], 5, 0, STR_PAD_LEFT) . "";
		mysqli_query($conn, "update tb_setup set ket3=" . $row['ket3'] . "+1 where nama_setup='" . $nama . "' and ket1='" . $bulan . "' and ket2='" . $tahun . "' ");
	}

	if ($flag == 0) {
		mysqli_query($conn, "insert into tb_setup values('','" . $nama . "','" . $bulan . "','" . $tahun . "',2) ");
		$no_id = "" . $prefix . "-" . $tahun . "-" . $bulan . "-00001";
	}
	return $no_id;
}
function generate_key_short($prefix, $nama, $bulan, $tahun)
{
	include "koneksi.php";
	$flag = 0;
	$ambil_data = mysqli_query($conn, "select * from tb_setup where nama_setup='" . $nama . "' ");
	while ($row = mysqli_fetch_array($ambil_data)) {
		$flag = 1;
		$no_id = "" . $prefix . "" . $bulan . "" . $tahun . "" . str_pad($row['ket3'], 4, 0, STR_PAD_LEFT) . "";
		mysqli_query($conn, "update tb_setup set ket3=" . $row['ket3'] . "+1 where nama_setup='" . $nama . "'");
	}

	if ($flag == 0) {
		mysqli_query($conn, "insert into tb_setup values('','" . $nama . "','','',2) ");
		$no_id = "" . $prefix . "" . $bulan . "" . $tahun . "0001";
	}
	return $no_id;
}



function generate_batch($hari, $bulan, $tahun)
{
	include "koneksi.php";
	$flag = 0;
	$nama = $hari . '-' . $bulan . '-' . $tahun;
	$ambil_data = mysqli_query($conn, "select * from tb_setup where nama_setup='" . $nama . "' ");
	while ($row = mysqli_fetch_array($ambil_data)) {
		$flag = 1;
		$no_id = $row['ket3'];

		if ($row['ket2'] == 5) {
			mysqli_query($conn, "update tb_setup set ket2=1 where nama_setup='" . $nama . "'");
			mysqli_query($conn, "update tb_setup set ket3=" . $row['ket3'] . "+1 where nama_setup='" . $nama . "'");
		} else {
			mysqli_query($conn, "update tb_setup set ket2=" . $row['ket2'] . "+1 where nama_setup='" . $nama . "'");
		}
	}

	if ($flag == 0) {
		mysqli_query($conn, "insert into tb_setup values('','" . $nama . "','',1,1) ");
		$no_id = "1";
	}
	return $no_id;
}

function generate_key($prefix, $nama, $bulan, $tahun)
{
	include "koneksi.php";
	$flag = 0;
	$ambil_data = mysqli_query($conn, "select * from tb_setup where nama_setup='" . $nama . "' and ket1='" . $bulan . "' and ket2='" . $tahun . "'");
	while ($row = mysqli_fetch_array($ambil_data)) {
		$flag = 1;
		$no_id = "" . $prefix . "" . $bulan . "" . $tahun . "" . str_pad($row['ket3'], 4, 0, STR_PAD_LEFT) . "";
		mysqli_query($conn, "update tb_setup set ket3=" . $row['ket3'] . "+1 where nama_setup='" . $nama . "' and ket1='" . $bulan . "' and ket2='" . $tahun . "' ");
	}

	if ($flag == 0) {
		mysqli_query($conn, "insert into tb_setup values('','" . $nama . "','" . $bulan . "','" . $tahun . "',2) ");
		$no_id = "" . $prefix . "" . $bulan . "" . $tahun . "0001";
	}
	return $no_id;
}

function generate_tipe_mobil()
{
	include "koneksi.php";
	$sql = "SELECT max(id_tipe) FROM tb_tipe_mobil";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));

	$kode_faktur = mysqli_fetch_array($query);

	if ($kode_faktur) {
		$nilai = substr($kode_faktur[0], 5);
		$kode = (int) $nilai;

		//tambahkan sebanyak + 1
		$kode = $kode + 1;
		$auto_kode = "TIP" . str_pad($kode, 6, "0",  STR_PAD_LEFT);
	} else {
		$auto_kode = "TIP000001";
	}
	return $auto_kode;
}

function generate_customer()
{
	include "koneksi.php";

	$query = mysqli_query($conn, "SELECT max(id_customer) as kodeTerbesar FROM tb_customer WHERE id_customer LIKE '%CUS-" . date("ym") . "%'");
	$data = mysqli_fetch_array($query);
	$kodeBarang = $data['kodeTerbesar'];
	$urutan = (int) substr($kodeBarang, 8, 6);
	$urutan++;
	$huruf = "CUS-" . date("ym");
	$kodesppa = $huruf . sprintf("%06s", $urutan);

	return $kodesppa;
}

function generate_beban()
{
	include "koneksi.php";
	$sql = "SELECT max(id_beban) FROM tb_beban";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));

	$kode_faktur = mysqli_fetch_array($query);

	if ($kode_faktur) {
		$nilai = substr($kode_faktur[0], 5);
		$kode = (int) $nilai;

		//tambahkan sebanyak + 1
		$kode = $kode + 1;
		$auto_kode = "B" . str_pad($kode, 6, "0",  STR_PAD_LEFT);
	} else {
		$auto_kode = "B000001";
	}
	return $auto_kode;
}

function generate_bahan()
{
	include "koneksi.php";
	$sql = "SELECT max(id_bahan) FROM tb_bahan";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));

	$kode_faktur = mysqli_fetch_array($query);

	if ($kode_faktur) {
		$nilai = substr($kode_faktur[0], 5);
		$kode = (int) $nilai;

		//tambahkan sebanyak + 1
		$kode = $kode + 1;
		$auto_kode = "BA" . str_pad($kode, 6, "0",  STR_PAD_LEFT);
	} else {
		$auto_kode = "BA000001";
	}
	return $auto_kode;
}

function generate_idsupp()
{
	include "koneksi.php";
	$sql = "SELECT max(id_supp) FROM tb_supp";
	$query = mysqli_query($conn, $sql) or die(mysqli_error($conn));

	$kode_faktur = mysqli_fetch_array($query);

	if ($kode_faktur) {
		$nilai = substr($kode_faktur[0], 5);
		$kode = (int) $nilai;

		//tambahkan sebanyak + 1
		$kode = $kode + 1;
		$auto_kode = "SUP" . str_pad($kode, 6, "0",  STR_PAD_LEFT);
	} else {
		$auto_kode = "SUP000001";
	}
	return $auto_kode;
}

function generate_barang_masuk_key($prefix, $nama, $bulan, $tahun)
{
	include "koneksi.php";
	$flag = 0;
	$ambil_data = mysqli_query($conn, "select * from tb_setup where nama_setup='" . $nama . "' and ket1='" . $bulan . "' and ket2='" . $tahun . "'");
	$jum_data = mysqli_num_rows($ambil_data);

	if ($jum_data > 0) {
		while ($row = mysqli_fetch_array($ambil_data)) {
			$flag = 1;
			$no_id = "" . $prefix . "/" . $tahun . "" . $bulan . "/" . str_pad($row['ket3'], 4, 0, STR_PAD_LEFT) . "";
			mysqli_query($conn, "update tb_setup set ket3=" . $row['ket3'] . "+1 where nama_setup='" . $nama . "' and ket1='" . $bulan . "' and ket2='" . $tahun . "' ");
		}
	} else {
		mysqli_query($conn, "insert into tb_setup values('','" . $nama . "','" . $bulan . "','" . $tahun . "',2) ");
		$no_id = "" . $prefix . "/" . $tahun . "" . $bulan . "/0001";
	}


	return $no_id;
}
function generate_barang_keluar_key($prefix, $nama, $bulan, $tahun)
{
	include "koneksi.php";
	$flag = 0;
	$ambil_data = mysqli_query($conn, "select * from tb_setup where nama_setup='" . $nama . "' and ket1='" . $bulan . "' and ket2='" . $tahun . "'");
	$jum_data = mysqli_num_rows($ambil_data);

	if ($jum_data > 0) {
		while ($row = mysqli_fetch_array($ambil_data)) {
			$flag = 1;
			$no_id = "" . $prefix . "/" . $tahun . "" . $bulan . "/" . str_pad($row['ket3'], 4, 0, STR_PAD_LEFT) . "";
			mysqli_query($conn, "update tb_setup set ket3=" . $row['ket3'] . "+1 where nama_setup='" . $nama . "' and ket1='" . $bulan . "' and ket2='" . $tahun . "' ");
		}
	} else {
		mysqli_query($conn, "insert into tb_setup values('','" . $nama . "','" . $bulan . "','" . $tahun . "',2) ");
		$no_id = "" . $prefix . "/" . $tahun . "" . $bulan . "/0001";
	}


	return $no_id;
}

function combofield($comboname, $table, $combo_id, $combo_display)
{
	include "koneksi.php";
	$sql = "select " . $combo_id . "," . $combo_display . " from " . $table . "";
	$data = mysqli_query($conn, $sql);
	echo "<select name=" . $comboname . " class='cmb300'>";
	while ($row = mysqli_fetch_array($data)) {
		echo "<option value=" . $row["" . $combo_id . ""] . ">" . $row["" . $combo_display . ""] . "</option>";
	}
	echo "</select>";
}


function validasi_hak_akses($modul_id)
{
	include "koneksi.php";
	global $hak_tambah, $hak_ubah, $hak_hapus;
	$hak_akses = 1;
	if ($_SESSION['grup'] == "rs") {

		$hak_tambah = 1;
		$hak_ubah = 1;
		$hak_hapus = 1;
	}

	if ($_SESSION['grup'] == "rn") {
		$sql = "select * from tb_modul_grant where id_karyawan='" . $_SESSION['id_karyawan'] . "' and modul_id='" . $modul_id . "' ";
		$data = mysqli_query($conn, $sql);
		while ($row = mysqli_fetch_array($data)) {
			$hak_tambah = $row['tambah'];
			$hak_ubah = $row['ubah'];
			$hak_hapus = $row['hapus'];
		}
	}
}

function validasi_hak_paket($id_reseller)
{
	include "koneksi.php";
	global $daftar;
	$daftar = "'m0000'";
	$sql1 = "select * from tb_reseller where id_reseller='" . $id_reseller . "' ";
	$data1 = mysqli_query($conn, $sql1);
	while ($row1 = mysqli_fetch_array($data1)) {
		if ($row1['inventory'] == 1) {
			$daftar = $daftar . ",'i0000'";
		}

		if ($row1['pos'] == 1) {
			$daftar = $daftar . ",'s0000'";
		}
		if ($row1['hrd'] == 1) {
			$daftar = $daftar . ",'h0000'";
		}
		if ($row1['pusat'] == 1) {
			$daftar = $daftar . ",'p0000'";
		}
		if ($row1['apps'] == 1) {
			$daftar = $daftar . ",'a0000'";
		}
		if ($row1['website'] == 1) {
			$daftar = $daftar . ",'w0000'";
		}
	}
}

function id_gen_barang_masuk()
{
	include "koneksi.php";
	$query12 = mysqli_query($conn, "SELECT max(no_id) as kodeTerbesar FROM tb_barang_masuk WHERE no_id LIKE 'IM" . date("my") . "%' ");
	$data12 = mysqli_fetch_array($query12);
	$kodeBarang12 = $data12['kodeTerbesar'];
	$urutan12 = (int) substr($kodeBarang12, 6, 6);
	$urutan12++;
	$tahun12 = date('my');
	$huruf12 = "IM";
	$kodeadds12 = $huruf12 . $tahun12 . sprintf("%06s", $urutan12);

	return $kodeadds12;
}

function id_gen_stock()
{
	include "koneksi.php";
	$query12 = mysqli_query($conn, "SELECT max(id) as kodeTerbesar FROM tb_stock WHERE id LIKE 'SB" . date("my") . "%' ");
	$data12 = mysqli_fetch_array($query12);
	$kodeBarang12 = $data12['kodeTerbesar'];
	$urutan12 = (int) substr($kodeBarang12, 6, 6);
	$urutan12++;
	$tahun12 = date('my');
	$huruf12 = "SB";
	$kodeadds12 = $huruf12 . $tahun12 . sprintf("%06s", $urutan12);

	return $kodeadds12;
}

function id_gen_barang_keluar()
{
	include "koneksi.php";
	$query12 = mysqli_query($conn, "SELECT max(no_id) as kodeTerbesar FROM tb_barang_keluar WHERE no_id LIKE 'IK" . date("my") . "%' ");
	$data12 = mysqli_fetch_array($query12);
	$kodeBarang12 = $data12['kodeTerbesar'];
	$urutan12 = (int) substr($kodeBarang12, 6, 6);
	$urutan12++;
	$tahun12 = date('my');
	$huruf12 = "IK";
	$kodeadds12 = $huruf12 . $tahun12 . sprintf("%06s", $urutan12);

	return $kodeadds12;
}

function id_gen_barang_po()
{
	include "koneksi.php";
	$query12 = mysqli_query($conn, "SELECT max(no_id) as kodeTerbesar FROM tb_barang_po WHERE no_id LIKE 'IK" . date("my") . "%' ");
	$data12 = mysqli_fetch_array($query12);
	$kodeBarang12 = $data12['kodeTerbesar'];
	$urutan12 = (int) substr($kodeBarang12, 6, 6);
	$urutan12++;
	$tahun12 = date('my');
	$huruf12 = "IK";
	$kodeadds12 = $huruf12 . $tahun12 . sprintf("%06s", $urutan12);

	return $kodeadds12;
}

function id_gen_opname()
{
	include "koneksi.php";
	$query12 = mysqli_query($conn, "SELECT max(inv_in_id) as kodeTerbesar FROM inv_adjust_in WHERE inv_in_id LIKE 'IP" . date("my") . "%' ");
	$data12 = mysqli_fetch_array($query12);
	$kodeBarang12 = $data12['kodeTerbesar'];
	$urutan12 = (int) substr($kodeBarang12, 6, 6);
	$urutan12++;
	$tahun12 = date('my');
	$huruf12 = "IP";
	$kodeadds12 = $huruf12 . $tahun12 . sprintf("%06s", $urutan12);

	return $kodeadds12;
}

function id_opname()
{
	include "koneksi.php";
	$query = mysqli_query($conn, "SELECT max(id_inv_in) as kodeTerbesar FROM inv_adjust_in WHERE id_inv_in LIKE '%OP-" . date("ym") . "%'");
	$data = mysqli_fetch_array($query);
	$kodeBarang = $data['kodeTerbesar'];
	$urutan = (int) substr($kodeBarang, 7, 6);
	$urutan++;
	$huruf = "OP-" . date("ym");
	$kodesppa = $huruf . sprintf("%06s", $urutan);

	return $kodesppa;
}

function add_jurnal($id_jurnal, $akun_debit, $akun_kredit, $amount, $tgl_bayar, $tipe_transaksi, $id_user)
{
	include "koneksi.php";
	if ($id_jurnal == "") {
		$find_date = date("ymd");
		$query = mysqli_query($conn, "SELECT max(no_transaksi) as kodeTerbesar FROM tb_jurnal_umum WHERE no_transaksi like '%" . $find_date . "%' ");
		if ($data = mysqli_fetch_array($query)) {
			$kode = $data['kodeTerbesar'];
			$urutan = (int) substr($kode, 9, 6);
			$urutan++;
			$tahun = date('y');
			$bulan = date('m');
			$tanggal = date('d');
			$huruf = "JU-";
			$id_jurnal = $huruf . $tahun . $bulan . $tanggal . sprintf("%06s", $urutan);
		} else {
			$id_jurnal = "JU-" . $find_date . "000001";
		}
	}

	begin();
	$sql_insert_debit = mysqli_query($conn, "
		INSERT INTO tb_jurnal_umum(
			no_transaksi,
			tgl_jurnal_umum,
			kode_akun,
			debit,
			total_debit,
			memo,
			status_jurnal,
			dibuat_oleh,
			dibuat_tgl
		) VALUES(
			'" . $id_jurnal . "',
			'" . $tgl_bayar . "',
			'" . $akun_debit . "',
			'" . $amount . "',
			'" . $amount . "',
			'" . $tipe_transaksi . "',
			'Posted',
			'" . $id_user . "',
			'" . date("Y-m-d H:i:s") . "'
		)
	");

	$sql_insert_kredit = mysqli_query($conn, "
		INSERT INTO tb_jurnal_umum(
			no_transaksi,
			tgl_jurnal_umum,
			kode_akun,
			kredit,
			total_kredit,
			memo,
			status_jurnal,
			dibuat_oleh,
			dibuat_tgl
		) VALUES(
			'" . $id_jurnal . "',
			'" . $tgl_bayar . "',
			'" . $akun_kredit . "',
			'" . $amount . "',
			'" . $amount . "',
			'" . $tipe_transaksi . "',
			'Posted',
			'" . $id_user . "',
			'" . date("Y-m-d H:i:s") . "'
		)
	");

	if ($sql_insert_debit && $sql_insert_kredit) {
		commit();
		$valid = 1;
	} else {
		rollback();
		$valid = 0;
	}

	return $id_jurnal;
	//return $valid . "|" . $id_jurnal;
}

function begin()
{
	include "koneksi.php";
	mysqli_query($conn, "BEGIN");
}

function commit()
{
	include "koneksi.php";
	mysqli_query($conn, "COMMIT");
}

function rollback()
{
	include "koneksi.php";
	mysqli_query($conn, "ROLLBACK");
}
