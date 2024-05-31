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

$dibuat_oleh = "";
$kategori = "";
$warna = "";
$efisiensi = "";
$pjg_marker = "";
$keterangan = "";

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

        $select_marker = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_marker WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi"));
        $marker_by = $select_marker['marker_by'];
        $efisiensi = $select_marker['efisiensi'];
        $pjg_marker = $select_marker['pjg_marker'];
        $keterangan = $select_marker['keterangan'];
        $tgl_wo = date("Y-m-d", strtotime($select_marker['tgl_wo']));
        $tgl_marker = date("Y-m-d", strtotime($select_marker['tgl_marker']));
    }
    if ($tgl_wo == '1970-01-01') {
        $tgl_wo = date("Y-m-d");
    }
    if ($tgl_marker == '1970-01-01') {
        $tgl_marker = date("Y-m-d");
    }
}

// if (isset($_GET['detail'])) {
//     $tgl_marker = date("Y-m-d", strtotime($select_marker['tgl_marker']));
// }

if (isset($_POST['simpan'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    // $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    // $ex_nm_supplier = explode(" | ", $nama_supplier);
    // $id_supplier = $ex_nm_supplier[0];
    $marker_by = mysqli_real_escape_string($conn, $_POST['marker_by']);
    $tgl_wo = mysqli_real_escape_string($conn, $_POST['tgl_wo']);
    $tgl_marker = mysqli_real_escape_string($conn, $_POST['tgl_marker']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $warna = mysqli_real_escape_string($conn, $_POST['warna']);
    $efisiensi = mysqli_real_escape_string($conn, $_POST['efisiensi']);
    $pjg_marker = mysqli_real_escape_string($conn, $_POST['pjg_marker']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);


    // if ($id_transaksi == "new") {
    //     $id_transaksi = generate_work_order_key("WO", "WO", date("m"), date("y"));

    //     $update = "UPDATE tb_work_order SET
    //         id_transaksi = '" . $id_transaksi . "',
    //         site = '" . $site . "',
    //         tgl_awal = '" . $tgl_awal . "',
    //         tgl_marker = '" . $tgl_marker . "',
    //         dibuat_oleh = '" . $dibuat_oleh . "',
    //         kategori = '" . $kategori . "',
    //         warna = '" . $warna . "',
    //         status = 'd',
    //         remaining = '" . $jarak2 . "'
    //         WHERE id_transaksi = '" . $_SESSION['id_user'] . "'
    //     ";

    //     $query = mysqli_query($conn, $update);
    // } else {
    //     $update = "UPDATE tb_work_order SET
    //         site = '" . $site . "',
    //         tgl_awal = '" . $tgl_awal . "',
    //         tgl_marker = '" . $tgl_marker . "',
    //         dibuat_oleh = '" . $dibuat_oleh . "',
    //         kategori = '" . $kategori . "',
    //         warna = '" . $warna . "',
    //         status = 'd',
    //         remaining = '" . $jarak2 . "'
    //         WHERE id_transaksi = '" . $id_transaksi . "'
    //     ";

    //     $query = mysqli_query($conn, $update);
    // }
    $query_wo = mysqli_query($conn, "SELECT * FROM tb_marker WHERE id_transaksi = '" . $id_transaksi . "'");
    $jum = mysqli_num_rows($query_wo);
    if ($jum > 0) {
        $update = "UPDATE tb_marker SET
            marker_by = '" . $marker_by . "',
            tgl_wo = '" . $tgl_wo . "',
            tgl_marker = '" . $tgl_marker . "',
            kategori = '" . $kategori . "',
            warna = '" . $warna . "',
            efisiensi = '" . $efisiensi . "',
            pjg_marker = '" . $pjg_marker . "',
            keterangan = '" . $keterangan . "'
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";

        $query = mysqli_query($conn, $update);
    } else {
        $insert = "INSERT INTO tb_marker(
            id_transaksi,
            marker_by,
            tgl_wo,
            tgl_marker,
            kategori,
            warna,
            efisiensi,
            pjg_marker,
            keterangan
        ) VALUES(
            '" . $id_transaksi . "',
            '" . $marker_by . "',
            '" . $tgl_wo . "',
            '" . $tgl_marker . "',
            '" . $kategori . "',
            '" . $warna . "',
            '" . $efisiensi . "',
            '" . $pjg_marker . "',
            '" . $keterangan . "'
        )";
        // echo $insert;
        $query = mysqli_query($conn, $insert);
    }

    header("location:master_marker_edit.php?id_transaksi=" . $id_transaksi);
}

if (isset($_POST['simpan_oto'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    // $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    // $ex_nm_supplier = explode(" | ", $nama_supplier);
    // $id_supplier = $ex_nm_supplier[0];
    $marker_by = mysqli_real_escape_string($conn, $_POST['marker_by']);
    $tgl_wo = mysqli_real_escape_string($conn, $_POST['tgl_wo']);
    $tgl_marker = mysqli_real_escape_string($conn, $_POST['tgl_marker']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $warna = mysqli_real_escape_string($conn, $_POST['warna']);
    $efisiensi = mysqli_real_escape_string($conn, $_POST['efisiensi']);
    $pjg_marker = mysqli_real_escape_string($conn, $_POST['pjg_marker']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $query_wo = mysqli_query($conn, "SELECT * FROM tb_marker WHERE id_transaksi = '" . $id_transaksi . "'");
    $jum = mysqli_num_rows($query_wo);
    $update = '';
    if ($jum > 0) {
        $update = "UPDATE tb_marker SET
            marker_by = '" . $marker_by . "',
            tgl_wo = '" . $tgl_wo . "',
            tgl_marker = '" . $tgl_marker . "',
            kategori = '" . $kategori . "',
            warna = '" . $warna . "',
            efisiensi = '" . $efisiensi . "',
            pjg_marker = '" . $pjg_marker . "',
            keterangan = '" . $keterangan . "'
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";
    } else {
        $update = "INSERT INTO tb_marker(
            id_transaksi,
            marker_by,
            tgl_wo,
            tgl_marker,
            kategori,
            warna,
            efisiensi,
            pjg_marker,
            keterangan
        ) VALUES(
            '" . $id_transaksi . "',
            '" . $marker_by . "',
            '" . $tgl_wo . "',
            '" . $tgl_marker . "',
            '" . $kategori . "',
            '" . $warna . "',
            '" . $efisiensi . "',
            '" . $pjg_marker . "',
            '" . $keterangan . "'
        )";
        // echo $insert;
        // $query = mysqli_query($conn, $insert);
    }

    $update2 = "UPDATE tb_work_order SET
        sts_marker = 's'
        WHERE id_transaksi = '" . $id_transaksi . "'
    ";

    $query = mysqli_query($conn, $update);
    $query2 = mysqli_query($conn, $update2);
    if ($query && $query2) {
        $msg_status = "Simpan & Otorisasi berhasil !";
    } else {
        $msg_status = "Simpan & Otorisasi gagal !";
    }

    echo '
        <script>
            alert("' . $msg_status . '");
            window.location.href="master_work_order.php";
        </script>
    ';
}

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
                            <h4 class="hk-sec-title" style="margin:1rem;">Menu Marker</h4>

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
                                                    <input type="date" name="tgl_wo" id="" class="form-control form-control-sm filled-input tgl_wo" value="<?= $tgl_wo; ?>" <?= $readonly; ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">MARKER BY</span>
                                                    </div>
                                                    <input type="text" name="marker_by" id="" class="form-control form-control-sm marker_by" value="<?= $marker_by; ?>" <?= $readonly; ?> required>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL MARKER</span>
                                                    </div>
                                                    <input type="date" name="tgl_marker" id="" class="form-control form-control-sm tgl_marker" value="<?= $tgl_marker; ?>" <?= $readonly; ?> required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">WARNA</span>
                                                    </div>
                                                    <input type="text" name="warna" id="" class="form-control form-control-sm warna" value="<?= $warna; ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">CATEGORY</span>
                                                    </div>
                                                    <input type="text" name="kategori" id="" class="form-control form-control-sm kategori" value="<?= $kategori; ?>" readonly>
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
                                <div class="table-responsive daftar_bahan">
                                    <?php
                                    echo '
                                    <h6>BAHAN BAKU</h6>
                                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Nama Kain</th>
                                                    <th class="text-center">QTY</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';

                                    $no_id = "";
                                    $id_bahan_kain = "";
                                    $ksl_qty_kain = 0;
                                    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain != '' ");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                        $no_id = $row['no_id'];
                                        $id_bahan_kain = $row['id_bahan_kain'];
                                        $qty_kain = $row['qty_kain'];
                                        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
                                        echo '
                                                    <tr >
                                                        <td class="text-center" >' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</td>
                                                        <td class="text-center" >' . $qty_kain . ' ROLL</td>
                                                        
                                                        </tr>
                                                ';
                                        // <td class="text-center" >
                                        // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                        // </td>
                                        $ksl_qty_kain += $qty_kain;
                                    }
                                    // <td class="text-center" style="color:red"></td>
                                    echo '
                                                <tr >
                                                    <td class="text-center" style="color:red">Total</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_kain . ' ROLL</td>
                                                    
                                                </tr>
                                                <tr ' . $hide . ' class="d-none">
                                                    <td class="text-center">
                                                        <input type="text" name="nama_kain_' . $no_id . '" id="" class="form-control form-control-sm nama_kain nama_kain_' . $no_id . '" autocomplete="off" list="list_barang" onclick="this.select()" >
                                                        <datalist id="list_barang" class="list_barang">
                                                        </datalist>
                                                    </td>
                                                    <td class="text-center"><input type="text" name="qty_kain_' . $no_id . '" class="form-control form-control-sm qty_kain_' . $no_id . '" ' . $readonly . '></td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '"><i class="fa fa-plus"></i></a>
                                                    </td>
                                                </tr>
                                            ';
                                    // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
                                    echo '
                                            </tbody>
                                        </table>
                                    '
                                    ?>
                                </div>

                                <div class="table-responsive daftar_bahan_2">
                                    <?php
                                    echo '
                                    <h6>RENCANA PENGERJAAN</h6>
                                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Nama Item</th>
                                                    <th class="text-center ">Rasio</th>
                                                    <th class="text-center">Rasio Real</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';
                                    $no_id = "";
                                    $id_sku = "";
                                    $ksl_qty_sku = 0;
                                    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku != ''");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
                                        $no_id = $row['no_id'];
                                        $id_sku = $row['id_sku'];
                                        $qty_sku = $row['qty_sku'];
                                        $rasio_real = $row['rasio_real'];
                                        $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                        echo '
                                                    <tr >
                                                        <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                        <td class="text-center" >' . $qty_sku . '</td>
                                                        <td class="text-center" ' . $hide . '>
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="rasio_real_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_rasio_real_kurang rasio_real_k_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-minus"></i></button>
                                                            <input type="number" name="rasio_real_' . $no_id . '" style="display:inline;width:150px;" class="form-control form-control-sm ubah_data_barang_tembak rasio_real_' . $no_id . ' " id="" data-no_id="' . $no_id . '" value="' . $rasio_real . '" ' . $readonly . '>
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="rasio_real_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_rasio_real_tambah rasio_real_t_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center" ' . $hide . '>
                                                            <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                                        </td>
                                                    </tr>
                                                ';
                                        // <td class="text-center" >
                                        //     <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                        // </td>
                                        $ksl_qty_sku += $qty_sku;
                                    }
                                    echo '
                                                <tr >
                                                    <td class="text-center" style="color:red">Total</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_sku . '</td>
                                                    <td class="text-center" style="color:red"></td>
                                                    <td></td>
                                                </tr>
                                                <tr ' . $hide . ' class="">
                                                    <td class="text-center">
                                                        <input type="text" name="nama_sku_' . $no_id . '" id="" class="form-control form-control-sm nama_sku nama_sku_' . $no_id . '" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                                                        <datalist id="list_barang_2" class="list_barang_2">
                                                        </datalist>
                                                    </td>
                                                    <td class="text-center"><input type="text" name="qty_sku_' . $no_id . '" class="form-control form-control-sm qty_sku_' . $no_id . '" ' . $readonly . '></td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            ';
                                    // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
                                    echo '
                                            </tbody>
                                        </table>
                                    '
                                    ?>
                                </div>

                                <br>
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row no-gutter">
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">EFISIENSI</span>
                                                        </div>
                                                        <?php
                                                        if (!isset($_GET['detail'])) {
                                                        ?>
                                                            <input type="number" name="efisiensi" step="0.001" id="" class="form-control form-control-sm efisiensi" value="<?= $efisiensi; ?><?= $persen ?>" <?= $readonly; ?> required>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input type="text" name="efisiensi" step="0.001" id="" class="form-control form-control-sm efisiensi" value="<?= $efisiensi; ?><?= $persen ?>" <?= $readonly; ?> required>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PANJANG MARKER</span>
                                                        </div>
                                                        <input type="number" name="pjg_marker" step="0.01" id="" class="form-control form-control-sm pjg_marker" value="<?= $pjg_marker; ?>" <?= $readonly; ?> required>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">KETERANGAN</span>
                                                        </div>
                                                        <textarea name="keterangan" id="" style="height:33px;" class="form-control form-control-sm keterangan " autocomplete="off" onclick="this.select();" <?= $readonly ?> required><?= $keterangan ?></textarea>
                                                        <!-- <input type="text" name="keterangan" id="" class="form-control form-control-sm keterangan" value="<?= $keterangan; ?>" <?= $readonly; ?>> -->
                                                    </div>
                                                </div>
                                            </div>
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
                                    echo '
                                    <div class="col-sm">
                                        <div class="row">
                                            <a href="master_work_order.php" class="btn btn-sm btn-danger mb-2"> Kembali</a>
                                            <button type="submit" name="simpan" class="btn btn-sm btn-success ml-2 mb-2">Simpan</button>
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

        $(document).on("keyup", ".nama_kain", function() {
            var nama_kain = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_work_order.php",
                data: {
                    "cari_nama_barang": nama_kain
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
            var id_tambah = $(this).data('id');
            // var id_bahan_kain = $(this).data('id_bahan_kain');
            var nama_sku = $(".nama_sku_" + id_tambah).val();
            var qty_sku = $(".qty_sku_" + id_tambah).val();
            console.log(id_transaksi + id_tambah + nama_sku + qty_sku);
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
            if (nama_sku != "" && qty_sku != "") {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_marker.php",
                    data: {
                        "add_list": id_tambah,
                        "id_transaksi": id_transaksi,
                        // "id_bahan_kain": id_bahan_kain,
                        "nama_sku": nama_sku,
                        "qty_sku": qty_sku
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

                        // $(".nama_barang").val("");
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

        $(document).on("click", ".del_list", function() {
            var id_hapus = $(this).data('id');
            var id_hapus_transaksi = "<?= $_GET['id_transaksi']; ?>";

            // $(".del_list_2_" + id_hapus).removeClass("del_list_2");
            // $(".del_list_" + id_hapus).removeClass("btn-danger");
            // $(".del_list_" + id_hapus).addClass("btn-secondary");
            // $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");

            $.ajax({
                type: "POST",
                url: "ajax/ajax_marker.php",
                data: {
                    "id_hapus": id_hapus,
                    "id_hapus_transaksi": id_hapus_transaksi
                },
                cache: true,
                success: function(response) {
                    // $(".del_list_2_" + id_hapus).addClass("del_list_2");
                    // $(".del_list_" + id_hapus).addClass("btn-danger");
                    // $(".del_list_" + id_hapus).removeClass("btn-secondary");
                    // $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");
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

        //Fungsi Kurang 
        $(document).on("click", ".ubah_data_rasio_real_kurang", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var rasio_real = $(".rasio_real_k_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_marker.php",
                data: {
                    "ubah_data_rasio_real_kurang": no_id,
                    "id_transaksi": id_transaksi,
                    "rasio_real": rasio_real
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan_2").html(result);
                }
            });
            
        });

        //Fungsi Tambah 
        $(document).on("click", ".ubah_data_rasio_real_tambah", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var rasio_real = $(".rasio_real_t_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_marker.php",
                data: {
                    "ubah_data_rasio_real_tambah": no_id,
                    "id_transaksi": id_transaksi,
                    "rasio_real": rasio_real
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan_2").html(result);
                }
            });
            
        });

        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var rasio_real = $(".rasio_real_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_marker.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "rasio_real": rasio_real
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan_2").html(result);

                    // $(".input_mask").mask('#,##0', {
                    //     reverse: true
                    // });
                }
            });
        });

        $(document).on("change", ".ppn_input", function() {
            var id_ppn = $(this).data('id_transaksi');
            var ppn_input = $(".ppn_input").val();

            $(".preloader-it").show();
            $(".preloader-it").css("opacity", "0.5");

            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_masuk.php",
                data: {
                    "id_ppn": id_ppn,
                    "ppn_input": ppn_input
                },
                cache: true,
                success: function(response) {

                    $(".daftar_bahan").html(response);
                    $(".input_mask").mask('#,##0', {
                        reverse: true
                    });
                    $(".preloader-it").hide();
                }
            });
        });

    });
</script>