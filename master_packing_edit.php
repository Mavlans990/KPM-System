<?php
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";
session_start();


if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

if ($_GET['id_transaksi'] == "new") {
    $id_transaksi = $_SESSION['id_user'];
} else {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
}

$site = "";
$tgl_awal = date("Y-m-d");
$tgl_marker = "";
$dibuat_oleh = "";
$kategori = "";
$warna = "";
$tgl_qc = "";
$jam_mulai = "";
$jam_selesai = "";
$line = "";
$anggota = "";
$pic = "";

if (isset($_GET['id_transaksi'])) {
    if ($_GET['id_transaksi'] !== "new") {
        $select_barang_masuk = "SELECT * FROM tb_work_order WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        $data_barang_masuk = mysqli_fetch_array($query_barang_masuk);

        // $select_customer = "SELECT * FROM tb_customer WHERE id_customer = '" . $data_barang_masuk['id_supplier'] . "'";
        // $query_customer = mysqli_query($conn, $select_customer);
        // $data_customer = mysqli_fetch_array($query_customer);

        // $nama_supplier = $data_customer['id_customer'] . " | " . $data_customer['nama_customer'];
        // $jenis_trs = $data_barang_masuk['jenis_transaksi'];
        $site = $data_barang_masuk['site'];
        $tgl_awal = date("Y-m-d", strtotime($data_barang_masuk['tgl_awal']));
        $dibuat_oleh = $data_barang_masuk['dibuat_oleh'];
        $kategori = $data_barang_masuk['kategori'];
        $warna = $data_barang_masuk['warna'];

        $select_cutting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi"));
        // $tgl_sewing = $select_cutting['tgl_sewing'];
        // $jam_mulai = $select_cutting['jam_mulai'];
        // $jam_selesai = $select_cutting['jam_selesai'];
        // $line = $select_cutting['line'];
        // $anggota = $select_cutting['anggota'];
        // $pic = $select_cutting['pic'];
    }
}


if (isset($_POST['simpan'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    // $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    // $ex_nm_supplier = explode(" | ", $nama_supplier);
    // $id_supp = $ex_nm_supplier[0];
    $site = mysqli_real_escape_string($conn, $_POST['site']);
    $tgl_awal = mysqli_real_escape_string($conn, $_POST['tgl_awal']);
    $tgl_marker = mysqli_real_escape_string($conn, $_POST['tgl_marker']);
    $dibuat_oleh = mysqli_real_escape_string($conn, $_POST['dibuat_oleh']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $warna = mysqli_real_escape_string($conn, $_POST['warna']);

    $tgl1 = new DateTime($tgl_awal);
    $tgl2 = new DateTime($tgl_marker);
    $jarak1 = $tgl2->diff($tgl1);
    $jarak2 = $jarak1->d;


    if ($id_transaksi == "new") {
        $id_transaksi = generate_work_order_key("WO", "WO", date("m"), date("y"));

        $update = "UPDATE tb_work_order SET
            id_transaksi = '" . $id_transaksi . "',
            site = '" . $site . "',
            tgl_awal = '" . $tgl_awal . "',
            tgl_marker = '" . $tgl_marker . "',
            dibuat_oleh = '" . $dibuat_oleh . "',
            kategori = '" . $kategori . "',
            warna = '" . $warna . "',
            status = 'd',
            remaining = '" . $jarak2 . "'
            WHERE id_transaksi = '" . $_SESSION['id_user'] . "'
        ";

        $query = mysqli_query($conn, $update);
    } else {
        $update = "UPDATE tb_work_order SET
            site = '" . $site . "',
            tgl_awal = '" . $tgl_awal . "',
            tgl_marker = '" . $tgl_marker . "',
            dibuat_oleh = '" . $dibuat_oleh . "',
            kategori = '" . $kategori . "',
            warna = '" . $warna . "',
            status = 'd',
            remaining = '" . $jarak2 . "'
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";

        $query = mysqli_query($conn, $update);
    }

    header("location:master_work_order_edit.php?id_transaksi=" . $id_transaksi);
}

if (isset($_POST['simpan_oto'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['id_packing']);
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    // $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    // $ex_nm_supplier = explode(" | ", $nama_supplier);
    // $id_supplier = $ex_nm_supplier[0];
    $query_wo = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing != '" . $_SESSION['id_user'] . "'");
    $jum = mysqli_num_rows($query_wo);

    if ($jum > 0) {
        $update = "UPDATE tb_work_order SET
            sts_packing = 's'
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";

        $query = mysqli_query($conn, $update);

        $delete = mysqli_query($conn, "DELETE FROM tb_packing WHERE id_packing = '" . $id_tambah . "'");
        $query2 = mysqli_query($conn, $delete);
        if ($query) {
            $msg_status = "Simpan & Otorisasi berhasil !";
        } else {
            $msg_status = "Simpan & Otorisasi gagal !";
        }
    } else {
        echo '
        <script>
            alert("Data Harus Terisi !");
            window.location.href="master_packing_edit.php?id_transaksi=' . $id_transaksi . '";
        </script>
        ';
    }


    echo '
        <script>
            alert("' . $msg_status . '");
            window.location.href="master_work_order.php";
        </script>
    ';
}

if (isset($_POST['add_tabel'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    $query_wo = mysqli_query($conn, "SELECT id_sku FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku != '' ");

    while ($row = mysqli_fetch_assoc($query_wo)) {
        $sql_update_barang_keluar = mysqli_query($conn, "INSERT INTO tb_packing(
            id_packing,
            id_transaksi,
            id_sku
        ) VALUES(
            '" . $_SESSION['id_user'] . "',
            '" . $id_transaksi . "',
            '" . $row['id_sku'] . "'
        )");
    }

    header("location:master_packing_edit.php?id_transaksi=" . $id_transaksi);
}

if (isset($_POST['save_packing'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['id_packing']);
    $id_packing = generate_id_finishing("PKG", "PKG", date("m"), date("y"));
    $update = "UPDATE tb_packing SET
        id_packing = '" . $id_packing . "' WHERE id_packing = '" . $id_tambah . "';
    ";
    $query = mysqli_query($conn, $update);

    header("location:master_packing_edit.php?id_transaksi=" . $id_transaksi);
}

if (isset($_POST['delete_packing'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['id_packing']);

    $delete = mysqli_query($conn, "DELETE FROM tb_packing WHERE id_packing = '" . $id_tambah . "'");
    $query = mysqli_query($conn, $delete);

    header("location:master_packing_edit.php?id_transaksi=" . $id_transaksi);
}

// if (isset($_POST['add_tabel_2'])) {
//     $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
//     $query_wo = mysqli_query($conn, "SELECT id_sku FROM tb_work_order WHERE id_transaksi = '". $id_transaksi ."' AND id_sku != '' ");

//     $id_finishing = generate_id_finishing("FSH", "FSH", date("m"), date("y"));
//     while ($row = mysqli_fetch_assoc($query_wo)) {
//         $sql_update_barang_keluar = mysqli_query($conn, "INSERT INTO tb_finishing(
//             id_finishing,
//             id_transaksi,
//             id_sku,
//             tabel
//         ) VALUES(
//             '" . $id_finishing . "',
//             '" . $id_transaksi . "',
//             '" . $row['id_sku'] . "',
//             '2'
//         )");
//     }

//     header("location:master_finishing_edit.php?id_transaksi=" . $id_transaksi);
// }

$hide = "";
$readonly = "";
$filled_input = "";
$persen = "";
if (isset($_GET['detail'])) {
    $hide = "style='display:none;'";
    $readonly = "readonly";
    $filled_input = "filled-input";
    $persen = "%";
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Program Andnic</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- select2 CSS -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

        <!-- Sidebar -->
        <?php include "header.php"; ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div class="hk-wrapper hk-vertical-nav">

            <!-- Main Content -->
            <div class="hk-pg-wrapper">

                <!-- Topbar -->
                <?php //include "part/topbar.php"; 
                ?>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">

                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                        <div class="card shadow mb-4">

                            <h4 class="hk-sec-title" style="margin:1rem;">Menu Packing </h4>

                            <div class="col-sm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">NO WO</span>
                                                    </div>
                                                    <input type="text" name="id_transaksi" id="" class="form-control form-control-sm filled-input id_transaksi" value="<?= $id_transaksi; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL WO</span>
                                                    </div>
                                                    <input type="date" name="tgl_awal" id="" class="form-control form-control-sm tgl_awal" value="<?= $tgl_awal; ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr style="margin-left: 10px;margin-right: 10px;">

                            <div class="row no-gutter">
                                <div class="col-md-10">
                                    <div class="input-group mb-3">

                                    </div>
                                </div>
                                <div class="col-md-2">
                                </div>

                            </div>

                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive daftar_bahan_2">
                                            <h6>PACKING LIST</h6>
                                            <?php
                                            $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing LIKE '%PKG%' GROUP BY id_packing");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                                $id_packing = $row['id_packing'];
                                                $tgl_kirim = $row['tgl_kirim'];
                                                $pengirim = $row['pengirim'];
                                                $penerima = $row['penerima'];
                                                $nama = $row['nama_sj'];
                                                echo '
                                                    <br>
                                                    <div class="row" style="margin-right:0px;">
                                                        <div class="col-md-5">
                                                            <div class="row no-gutter">
                                                                <div class="col-12">
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">NO.PACKING LIST</span>
                                                                        </div>
                                                                        <input type="text" name="id_packing" id="" class="form-control form-control-sm ubah_data_input_2 id_packing_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $id_packing . '" readonly required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENGIRIM</span>
                                                                        </div>
                                                                        <input type="text" name="pengirim" id="" class="form-control form-control-sm ubah_data_input_2 pengirim_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $pengirim . '" readonly required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENERIMA</span>
                                                                        </div>
                                                                        <input type="text" name="penerima" id="" class="form-control form-control-sm ubah_data_input_2 penerima_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $penerima . '" readonly required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama SJ</span>
                                                                    </div>
                                                                    <input type="text" name="nama" id="" class="form-control form-control-sm ubah_data_input keterangan_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $nama . '" ' . $readonly . '>
                                                                </div>
                                                            </div>
                                                                <div class="col-12">
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL KIRIM</span>
                                                                        </div>
                                                                        <input type="date" name="tgl_kirim" id="" class="form-control form-control-sm ubah_data_input_2 tgl_kirim_' . $id_packing . '" data-id_packing="' . $id_packing . '"  value="' . $tgl_kirim . '" readonly required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $id_packing . '" data-id_packing="' . $id_packing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
                                                        </div>

                                                        <div class="col-md-7">
                                                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">NO</th>
                                                                        <th class="text-center">ITEM BAJU</th>
                                                                        <th class="text-center">POLYBAG</th>
                                                                        <th class="text-center">PCS</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                ';

                                                $no_id = "";
                                                $ttl_polybag = 0;
                                                $ttl_pcs = 0;
                                                $no = 1;
                                                $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing = '" . $id_packing . "'");
                                                while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                                                    $no_id = $row2['no_id'];
                                                    $id_sku = $row2['id_sku'];
                                                    $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                                    echo '
                                                                        <tr >
                                                                            <td class="text-center">' . $no++ . '</td>
                                                                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                                            <td class="text-center">' . $row2['polybag'] . '</td>
                                                                            <td class="text-center">' . $row2['pcs'] . '</td>
                                                                            </tr>
                                                                    ';
                                                    $ttl_polybag += $row2['polybag'];
                                                    $ttl_pcs += $row2['pcs'];
                                                }
                                                // <td class="text-center" style="color:red"></td>
                                                echo '
                                                                    <tr >
                                                                        <td class="text-center" style="color:red"></td>
                                                                        <td class="text-center" style="color:red">Total</td>
                                                                        <td class="text-center" style="color:red">' . $ttl_polybag . '</td>
                                                                        <td class="text-center" style="color:red">' . $ttl_pcs . '</td>
                                                                    </tr>
                                                                ';
                                                // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
                                                echo '
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    ';
                                            }
                                            ?>

                                        </div>
                                        <hr style="margin-left: 10px;margin-right: 10px;">
                                        <div <?= $hide ?> class="table-responsive daftar_bahan">
                                            <h6>BUAT PACKING LIST BARU</h6>
                                            <?php
                                            $select_barang_masuk_3 = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing = '" . $_SESSION['id_user'] . "' GROUP BY id_packing");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk_3)) {
                                                $id_packing = $row['id_packing'];
                                                $tgl_kirim = $row['tgl_kirim'];
                                                $nama = $row['nama_sj'];
                                                $pengirim = $row['pengirim'];
                                                $penerima = $row['penerima'];
                                                echo '
                                                <br>
                                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">NO</th>
                                                            <th class="text-center">ITEM BAJU</th>
                                                            <th class="text-center">POLYBAG</th>
                                                            <th class="text-center">PCS</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    ';

                                                $no_id = "";
                                                $ttl_polybag = 0;
                                                $ttl_pcs = 0;
                                                $ttl_cuci = 0;
                                                $ttl_kotor = 0;
                                                $ttl_turun = 0;
                                                $no = 1;
                                                $select_barang_masuk_4 = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing = '" . $id_packing . "'");
                                                while ($row2 = mysqli_fetch_assoc($select_barang_masuk_4)) {
                                                    $no_id = $row2['no_id'];
                                                    $id_sku = $row2['id_sku'];
                                                    $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                                    echo '
                                                            <tr >
                                                                <td class="text-center">' . $no++ . '</td>
                                                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                                <td class="text-center"><input type="number" style="display:inline;width:70px" name="polybag_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak polybag_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['polybag'] . '" ' . $readonly . '></td>
                                                                <td class="text-center"><input type="number" style="display:inline;width:70px" name="pcs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak pcs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['pcs'] . '" ' . $readonly . '></td>
                                                            </tr>
                                                        ';
                                                    $ttl_polybag += $row2['polybag'];
                                                    $ttl_pcs += $row2['pcs'];
                                                }
                                                // <td class="text-center" style="color:red"></td>
                                                echo '
                                                        <tr >
                                                            <td class="text-center" style="color:red"></td>
                                                            <td class="text-center" style="color:red">Total</td>
                                                            <td class="text-center" style="color:red">' . $ttl_polybag . '</td>
                                                            <td class="text-center" style="color:red">' . $ttl_pcs . '</td>
                                                        </tr>
                                                    ';
                                                // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
                                                echo '
                                                    </tbody>
                                                </table>
                                                <div class="row" style="margin-right:0px;">
                                                    <div class="col-md-5">
                                                        <div class="row no-gutter">
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">NO.PACKING LIST</span>
                                                                    </div>
                                                                    <input type="text" name="id_packing" id="" class="form-control form-control-sm ubah_data_input id_packing_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $id_packing . '" readonly required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                            <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENGIRIM</span>
                                                            </div>
                                                            <input type="text" name="pengirim" id="" class="form-control form-control-sm ubah_data_input pengirim_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $pengirim . '" ' . $readonly . ' >
                                                            </div>
                                                            </div>
                                                            <div class="col-12">
                                                            <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENERIMA</span>
                                                            </div>
                                                            <input type="text" name="penerima" id="" class="form-control form-control-sm ubah_data_input penerima_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $penerima . '" ' . $readonly . ' >
                                                            </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama SJ</span>
                                                                    </div>
                                                                    <input type="text" name="nama" id="" class="form-control form-control-sm ubah_data_input keterangan_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $nama . '" ' . $readonly . '>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL KIRIM</span>
                                                                    </div>
                                                                    <input type="date" name="tgl_kirim" id="" class="form-control form-control-sm ubah_data_input tgl_kirim_' . $id_packing . '" data-id_packing="' . $id_packing . '"  value="' . $tgl_kirim . '" ' . $readonly . ' >
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <input type="hidden" name="id_packing" value="' . $id_packing . '">
                                                        <button type="submit" class="btn btn-success btn-xs" style="width:15vw;" name="save_packing" style="color:white;"><i class="fa fa-plus"></i> Simpan Packing List</button>
                                                        
                                                    </div>
                                                </div>
                                                ';
                                                // <button type="submit" class="btn btn-danger btn-xs" style="width:15vw;" name="delete_packing" style="color:white;"><i class="fa fa-trash-o"></i> Hapus Packing List</button>
                                                // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-success add_list add_list_' . $id_packing . '" data-id_packing="' . $id_packing . '" style="margin-top:150px" ><i class="fa fa-plus"></i> Simpan Packing List</a>
                                            }
                                            ?>

                                        </div>
                                        <div class="row mt-10 justify-content-end">
                                            <?php
                                            $jum_query = mysqli_num_rows($select_barang_masuk_3);
                                            if ($jum_query < 1) {
                                            ?>
                                                <div class="col-3" style="margin-right:0px;">
                                                    <div class="form-group form-group-sm">
                                                        <button type="submit" <?= $hide ?> class="btn btn-success btn-sm" style="width:15vw;" name="add_tabel" style="color:white;"><i class="fa fa-plus"></i> Tambah Packing List</button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>



                                <?php
                                if (isset($_GET['detail'])) {
                                    echo '
                                    <div class="col-sm">
                                        <div class="row">
                                            <a href="master_work_order.php" class="btn btn-primary mb-2"><i class="fa fa-arrow-left"></i> Kembali</a>
                                            
                                        </div>
                                    </div>
                                    ';
                                    // <a href="print/print_invoice_barang_masuk.php?id_transaksi=' . $id_transaksi . '" class="btn btn-success ml-2 mb-2" target="_blank"><i class="fa fa-print"></i> Print Invoice</a>
                                } else {
                                    // <button type="submit" name="simpan" class="btn btn-sm btn-success ml-2 mb-2">Simpan</button>
                                    echo '
                                    <div class="col-sm">
                                        <div class="row">
                                            <a href="master_work_order.php" class="btn btn-sm btn-danger mb-2"> Kembali</a>
                                            <input type="hidden" name="id_packing" value="' . $_SESSION['id_user'] . '">
                                            <button type="submit" name="simpan_oto" class="btn btn-sm btn-warning mb-2 ml-2">Simpan & Otorisasi</button>
                                        </div>
                                    </div>
                                    ';
                                }
                                ?>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

        <!-- Footer -->


    </div>
    <!-- End of Content Wrapper -->



    <!-- Bootstrap core JavaScript-->
    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>



    <!-- Data Table JavaScript -->
    <script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="vendors/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="vendors/jszip/dist/jszip.min.js"></script>
    <script src="vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="vendors/pdfmake/build/vfs_fonts.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="dist/js/dataTables-data.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="dist/js/feather.min.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Toggles JavaScript -->
    <script src="vendors/jquery-toggles/toggles.min.js"></script>
    <script src="dist/js/toggle-data.js"></script>

    <!-- Select2 JavaScript -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="dist/js/select2-data.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    <script src="vendors/jquery/dist/jquery.mask.min.js"></script>

</body>

</html>
<script type="text/javascript">
    $(document).ready(function() {

        function koma(nStr) {
            nStr += '';
            var x = nStr.split(',');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        $(".harga_bahan").mask('#,##0', {
            reverse: true
        });

        $(".harga").mask('#,##0', {
            reverse: true
        });

        $(".qty").mask('#,##0', {
            reverse: true
        });

        $(".input_mask").mask('#,##0', {
            reverse: true
        });

        $(document).on("change", ".nama_barang", function() {
            var nama_barang = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_masuk.php",
                data: {
                    "get_barang": nama_barang
                },
                cache: true,
                beforeSend: function(response) {
                    $(".preloader-it").show();
                },
                success: function(result) {
                    $(".preloader-it").hide();
                    $(".uom_barang").val(result);
                }
            });
        });

        $(document).on("keyup", ".nama_supplier", function() {
            var nama_supplier = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_masuk.php",
                data: {
                    "cari_nama_supplier": nama_supplier
                },
                cache: true,
                success: function(result) {
                    $(".list_supplier").html(result);
                }
            });
        });

        $(document).on("keyup", ".lot", function() {
            // var nama_supplier = $(this).val();
            var id_tambah = $(this).data('id');
            var lot = $(".lot").val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_masuk.php",
                data: {
                    "cari_lot": lot
                },
                cache: true,
                success: function(result) {
                    $(".list_lot").html(result);
                }
            });
        });

        $(document).on("keyup", ".harga_bahan", function() {
            var subtotal = $(".harga_bahan").val();
            subtotal = subtotal.split(',').join('');
            if (isNaN(subtotal)) subtotal = 0;

            $('.harga_bahan_hidden').val(subtotal);
        });

        $(document).on("keyup", ".harga", function() {
            var no_id = $(this).data('no_id');
            var subtotal = $(".harga_" + no_id).val();
            subtotal = subtotal.split(',').join('');
            if (isNaN(subtotal)) subtotal = 0;

            $('.harga_hidden_' + no_id).val(subtotal);
        });

        $(document).on("keyup", ".qty", function() {
            var no_id = $(this).data('no_id');
            var subtotal = $(".qty_" + no_id).val();
            subtotal = subtotal.split(',').join('');
            if (isNaN(subtotal)) subtotal = 0;

            $('.qty_hidden_' + no_id).val(subtotal);
        });

        $(document).on("change", ".nama_bahan", function() {
            var filter_bahan = $(".nama_bahan").val();
            if (filter_bahan !== "") {
                if (filter_bahan.includes(" | ")) {
                    $.ajax({
                        type: "POST",
                        url: "ajax/ajax_barang_masuk.php",
                        data: {
                            "filter_bahan": filter_bahan
                        },
                        cache: true,
                        success: function(response) {
                            $(".uom_bahan").html(response);
                            $(".btn_add").removeClass("btn-secondary");
                            $(".btn_add").addClass("btn-success");
                            $(".btn_add").addClass("add_list");
                        }
                    });
                } else {
                    $(".uom_bahan").html("(UOM)");
                    $(".btn_add").addClass("btn-secondary");
                    $(".btn_add").removeClass("btn-success");
                    $(".btn_add").removeClass("add_list");
                }
            } else {
                $(".uom_bahan").html("(UOM)");
                $(".btn_add").addClass("btn-secondary");
                $(".btn_add").removeClass("btn-success");
                $(".btn_add").removeClass("add_list");
            }
        });

        $(document).on("keyup", ".nama_material", function() {
            var nama_material = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_cutting.php",
                data: {
                    "cari_nama_barang": nama_material
                },
                cache: true,
                success: function(result) {
                    $(".list_barang").html(result);
                }
            });
        });

        $(document).on("keyup", ".nama_sku", function() {
            var nama_sku = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_work_order.php",
                data: {
                    "cari_nama_barang_2": nama_sku
                },
                cache: true,
                success: function(result) {
                    $(".list_barang_2").html(result);
                }
            });
        });

        $(document).on("click", ".add_list", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var id_tambah = $(this).data('id_packing');
            // var id_bahan_kain = $(this).data('id_bahan_kain');
            var pengirim = $(".pengirim_" + id_tambah).val();
            var penerima = $(".penerima_" + id_tambah).val();
            var tgl_kirim = $(".tgl_kirim_" + id_tambah).val();
            // var qty_barang = $(".qty_barang").val();
            // var harga_barang = $(".harga_barang").val();
            // if (harga_barang == "") {
            //     var harga_barang = 0;
            // } else {
            //     var harga_barang = harga_barang.split(",").join("");
            //     var harga_barang = parseFloat(harga_barang);
            // }
            // var berat_barang = $(".berat_barang").val();
            // var uom_barang = $(".uom_barang").val();

            // && (harga_barang !== "" && harga_barang > 0)
            if (pengirim != "" && penerima != "" && tgl_kirim != "") {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_packing.php",
                    data: {
                        "add_list": id_tambah,
                        "id_transaksi": id_transaksi
                        // "id_bahan_kain": id_bahan_kain,
                        // "nama_kain": nama_kain,
                        // "qty_kain": qty_kain
                        // "qty_barang": qty_barang,
                        // "harga_barang": harga_barang,
                        // "berat_barang": berat_barang,
                        // "uom_barang": uom_barang
                    },
                    cache: true,
                    beforeSend: function(response) {
                        $(".preloader-it").show();
                    },
                    success: function(result) {
                        $(".preloader-it").hide();
                        $(".daftar_bahan_2").html(result);

                        $(".nama_barang").val("");
                        // $(".qty_barang").val("");
                        // $(".harga_barang").val("");
                        // $(".berat_barang").val("");
                        // $(".uom_barang").val("");
                    }
                });
            } else {
                alert("Mohon Isi Data Yang Lengkap !");
            }
        });


        //TABEL 2
        $(document).on("click", ".del_list_2", function() {
            var id_hapus_2 = $(this).data('id_packing');
            var id_hapus_transaksi = "<?= $_GET['id_transaksi']; ?>";

            $(".del_list_" + id_hapus_2).removeClass("del_list_2");
            $(".del_list_" + id_hapus_2).removeClass("btn-danger");
            $(".del_list_" + id_hapus_2).addClass("btn-secondary");
            $(".del_list_" + id_hapus_2).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");

            $.ajax({
                type: "POST",
                url: "ajax/ajax_packing.php",
                data: {
                    "id_hapus_2": id_hapus_2,
                    "id_hapus_transaksi": id_hapus_transaksi
                },
                cache: true,
                success: function(response) {
                    $(".del_list_" + id_hapus_2).addClass("del_list_2");
                    $(".del_list_" + id_hapus_2).addClass("btn-danger");
                    $(".del_list_" + id_hapus_2).removeClass("btn-secondary");
                    $(".del_list_" + id_hapus_2).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");
                    $(".daftar_bahan_2").html(response);
                    $(".harga_bahan").mask('#,##0', {
                        reverse: true
                    });

                    $(".harga").mask('#,##0', {
                        reverse: true
                    });

                    $(".qty").mask('#,##0', {
                        reverse: true
                    });
                }
            });
        });

        //TABEL 1
        $(document).on("click", ".del_list", function() {
            var id_hapus = $(this).data('id_finishing');
            var id_hapus_transaksi = "<?= $_GET['id_transaksi']; ?>";

            $(".del_list_" + id_hapus).removeClass("del_list");
            $(".del_list_" + id_hapus).removeClass("btn-danger");
            $(".del_list_" + id_hapus).addClass("btn-secondary");
            $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");

            $.ajax({
                type: "POST",
                url: "ajax/ajax_finishing.php",
                data: {
                    "id_hapus": id_hapus,
                    "id_hapus_transaksi": id_hapus_transaksi
                },
                cache: true,
                success: function(response) {
                    $(".del_list_" + id_hapus).addClass("del_list");
                    $(".del_list_" + id_hapus).addClass("btn-danger");
                    $(".del_list_" + id_hapus).removeClass("btn-secondary");
                    $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");
                    $(".daftar_bahan").html(response);
                    $(".harga_bahan").mask('#,##0', {
                        reverse: true
                    });

                    $(".harga").mask('#,##0', {
                        reverse: true
                    });

                    $(".qty").mask('#,##0', {
                        reverse: true
                    });
                }
            });
        });

        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi ?>";
            var polybag = $(".polybag_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_packing.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "polybag": polybag
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });
        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi ?>";
            var pcs = $(".pcs_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_packing.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "pcs": pcs
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });


        $(document).on("change", ".ubah_data_input", function() {
            var id_packing = $(this).data('id_packing');
            var id_transaksi = "<?= $id_transaksi ?>";
            var pengirim = $(".pengirim_" + id_packing).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_packing.php",
                data: {
                    "ubah_data_input": id_packing,
                    "id_transaksi": id_transaksi,
                    "pengirim": pengirim
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });
        $(document).on("change", ".ubah_data_input", function() {
            var id_packing = $(this).data('id_packing');
            var id_transaksi = "<?= $id_transaksi ?>";
            var keterangan = $(".keterangan_" + id_packing).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_packing.php",
                data: {
                    "ubah_data_input": id_packing,
                    "id_transaksi": id_transaksi,
                    "keterangan": keterangan
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });
        $(document).on("change", ".ubah_data_input", function() {
            var id_packing = $(this).data('id_packing');
            var id_transaksi = "<?= $id_transaksi ?>";
            var penerima = $(".penerima_" + id_packing).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_packing.php",
                data: {
                    "ubah_data_input": id_packing,
                    "id_transaksi": id_transaksi,
                    "penerima": penerima
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });
        $(document).on("change", ".ubah_data_input", function() {
            var id_packing = $(this).data('id_packing');
            var id_transaksi = "<?= $id_transaksi ?>";
            var tgl_kirim = $(".tgl_kirim_" + id_packing).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_packing.php",
                data: {
                    "ubah_data_input": id_packing,
                    "id_transaksi": id_transaksi,
                    "tgl_kirim": tgl_kirim
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });

    });
</script>