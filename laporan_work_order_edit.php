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
$inspek_by = "";
$tgl_inspek = "";
$jam_inspek = "";
$penerima = "";
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
        $warna = $data_barang_masuk['warna'];
        $kategori = $data_barang_masuk['kategori'];


        $select_inspek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi"));
        $inspek_by = $select_inspek['inspek_by'];
        $tgl_inspek = $select_inspek['tgl_inspek'];
        $jam_inspek = $select_inspek['jam_inspek'];
        $penerima = $select_inspek['penerima'];
        $keterangan = $select_inspek['keterangan'];

        $select_marker = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_marker WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi"));
        $marker_by = $select_marker['marker_by'];
        $efisiensi = $select_marker['efisiensi'];
        $pjg_marker = $select_marker['pjg_marker'];
        $keterangan = $select_marker['keterangan'];

        $select_cutting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_cutting WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi"));
        $nama_gelar = $select_cutting['nama_gelar'];
        $jam_gelar = $select_cutting['jam_gelar'];
        $shift_gelar = $select_cutting['shift_gelar'];
        $nama_potong = $select_cutting['nama_potong'];
        $jam_potong = $select_cutting['jam_potong'];
        $shift_potong = $select_cutting['shift_potong'];
    }
}

if (isset($_GET['detail'])) {
    $tgl_marker = date("Y-m-d", strtotime($select_marker['tgl_marker']));
}

if (isset($_POST['simpan'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    // $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    // $ex_nm_supplier = explode(" | ", $nama_supplier);
    // $id_supp = $ex_nm_supplier[0];
    $tgl_inspek = mysqli_real_escape_string($conn, $_POST['tgl_inspek']);
    $warna = mysqli_real_escape_string($conn, $_POST['warna']);
    $inspek_by = mysqli_real_escape_string($conn, $_POST['inspek_by']);
    $jam_inspek = mysqli_real_escape_string($conn, $_POST['jam_inspek']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $penerima = mysqli_real_escape_string($conn, $_POST['penerima']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

    $update = "UPDATE tb_inspek SET
            tgl_inspek = '" . $tgl_inspek . "',
            inspek_by = '" . $inspek_by . "',
            jam_inspek = '" . $jam_inspek . "',
            kategori = '" . $kategori . "',
            warna = '" . $warna . "',
            penerima = '" . $penerima . "',
            keterangan = '" . $keterangan . "'
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";

    $query = mysqli_query($conn, $update);

    header("location:master_inspek_edit.php?id_transaksi=" . $id_transaksi);
}

if (isset($_POST['simpan_oto'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    // $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    // $ex_nm_supplier = explode(" | ", $nama_supplier);
    // $id_supplier = $ex_nm_supplier[0];
    $tgl_inspek = mysqli_real_escape_string($conn, $_POST['tgl_inspek']);
    $warna = mysqli_real_escape_string($conn, $_POST['warna']);
    $inspek_by = mysqli_real_escape_string($conn, $_POST['inspek_by']);
    $jam_inspek = mysqli_real_escape_string($conn, $_POST['jam_inspek']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $penerima = mysqli_real_escape_string($conn, $_POST['penerima']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);


    $update = "UPDATE tb_inspek SET
        tgl_inspek = '" . $tgl_inspek . "',
        inspek_by = '" . $inspek_by . "',
        jam_inspek = '" . $jam_inspek . "',
        kategori = '" . $kategori . "',
        warna = '" . $warna . "',
        penerima = '" . $penerima . "',
        keterangan = '" . $keterangan . "'
        WHERE id_transaksi = '" . $id_transaksi . "'
    ";

    $update2 = "UPDATE tb_work_order SET
        sts_inspek = 's'
        WHERE id_transaksi = '" . $id_transaksi . "'
    ";

    $query = mysqli_query($conn, $update);
    $query2 = mysqli_query($conn, $update2);
    if ($query) {
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
                            <!-- Menu Inspek -->
                            <h4 class="hk-sec-title" style="margin:1rem;">Menu Inspek</h4>

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
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL INSPEK</span>
                                                    </div>
                                                    <input type="date" name="tgl_inspek" id="" class="form-control form-control-sm tgl_inspek" value="<?= $tgl_inspek; ?>" <?= $readonly; ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL WO</span>
                                                    </div>
                                                    <input type="date" name="tgl_awal" id="" class="form-control form-control-sm tgl_awal" value="<?= $tgl_awal; ?>" <?= $readonly; ?>>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JAM INSPEK</span>
                                                    </div>
                                                    <input type="time" name="jam_inspek" id="" class="form-control form-control-sm jam_inspek" value="<?= $jam_inspek; ?>" <?= $readonly; ?> required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">INSPEK BY</span>
                                                    </div>
                                                    <input type="text" name="inspek_by" id="" class="form-control form-control-sm inspek_by" value="<?= $inspek_by; ?>" <?= $readonly; ?> required>
                                                </div>
                                            </div>
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
                            <!-- <hr style="margin-left: 10px;margin-right: 10px;"> -->

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
                                    $total_roll = 0;
                                    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain LIKE '%BHN%' GROUP BY id_bahan_kain");
                                    $jum_data = mysqli_num_rows($select_barang_masuk);
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                        $id_bahan_kain = $row['id_bahan_kain'];
                                        $no_id = $row['no_id'];
                                        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "'"));
                                        echo '
                                            <br>
                                            <p>' . $q_kain['jenis_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . ' : ' . $row['qty_kain'] . ' ROLL</p>
                                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Barcode</th>
                                                        <th class="text-center">Bolong</th>
                                                        <th class="text-center">Kotor</th>
                                                        <th class="text-center">Patah Jarum</th>
                                                        <th class="text-center">Belang</th>
                                                        <th class="text-center">Garis</th>
                                                        <th class="text-center">Total</th>
                                                        <th class="text-center">Ket</th>
                                                        <th class="text-center">Bruto</th>
                                                        <th ' . $hide . '></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                ';
                                        $select_inspek = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain = '" . $row['id_bahan_kain'] . "' ");
                                        while ($row2 = mysqli_fetch_assoc($select_inspek)) {
                                            $select_terima_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE barcode = '" . $row2['barcode'] . "'"));
                                            $no_id2 = $row2['no_id'];
                                            $lot = $select_terima_kain['lot'];
                                            $bruto = $select_terima_kain['bruto'];
                                            $total_ksl = $row2['bolong'] + $row2['kotor'] + $row2['patah'] + $row2['belang'] + $row2['garis'];

                                            echo '
                                                    <tr>
                                                        <td class="text-center">' . $lot . '  ' . $row2['barcode'] . '</td>
                                                        <td class="text-center">' . $row2['bolong'] . '</td>
                                                        <td class="text-center">' . $row2['kotor'] . '</td>
                                                        <td class="text-center">' . $row2['patah'] . '</td>
                                                        <td class="text-center">' . $row2['belang'] . '</td>
                                                        <td class="text-center">' . $row2['garis'] . '</td>
                                                        <td class="text-center">' . $total_ksl . '</td>
                                                        <td class="text-center">' . $row2['ket'] . '</td>
                                                        <td class="text-center" >' . $bruto . '</td>
                                                    ';
                                            if (!isset($_GET['detail'])) {
                                                echo '
                                                        <td class="text-center" >
                                                            <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list_3 del_list_3_' . $no_id2 . '" data-id="' . $no_id2 . '"><i class="fa fa-trash-o"></i></a>
                                                        </td>
                                                            ';
                                            }
                                            echo '
                                                    </tr>
                                                    ';
                                        }
                                        echo '
                                                    <tr ' . $hide . '>
                                                        <td class="text-center" colspan="9">
                                                        <input type="text" name="lot_' . $no_id . '"  class="form-control form-control-sm lot lot_' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '" autocomplete="off" list="list_lot" onclick="this.select();">
                                                        <datalist id="list_lot" class="list_lot">
                                                        </datalist>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '"><i class="fa fa-plus"></i></a>
                                                        </td>
                                                    </tr>
                                                ';
                                        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
                                        echo '
                                                </tbody>
                                            </table>
                                            ';
                                        $total_roll += $row['qty_kain'];
                                    }
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
                                                            <span style="width:150px;font-weight: 600;" id="inputGroup-sizing-sm">TOTAL ROLL</span>
                                                        </div>
                                                        <span style="width:150px;font-weight: 600;" id="inputGroup-sizing-sm"><?= $total_roll ?> ROLL</span>
                                                        <!-- <input type="text" name="" id="" class="form-control form-control-sm " value="<?= $penerima ?>" readonly> -->

                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENERIMA</span>
                                                        </div>
                                                        <input type="text" name="penerima" id="" class="form-control form-control-sm penerima" value="<?= $penerima ?>" <?= $readonly; ?> required>
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
                                // if (isset($_GET['detail'])) {
                                //     echo '
                                //     <div class="col-sm">
                                //         <div class="row">
                                //             <a href="master_work_order.php" class="btn btn-primary mb-2"><i class="fa fa-arrow-left"></i> Kembali</a>

                                //         </div>
                                //     </div>
                                //     ';
                                //     // <a href="print/print_invoice_barang_masuk.php?id_transaksi=' . $id_transaksi . '" class="btn btn-success ml-2 mb-2" target="_blank"><i class="fa fa-print"></i> Print Invoice</a>
                                // } else {
                                //     echo '
                                //     <div class="col-sm">
                                //         <div class="row">
                                //             <a href="master_work_order.php" class="btn btn-sm btn-danger mb-2"> Kembali</a>
                                //             <button type="submit" name="simpan" class="btn btn-sm btn-success ml-2 mb-2">Simpan</button>
                                //             <button type="submit" name="simpan_oto" class="btn btn-sm btn-warning mb-2 ml-2">Simpan & Otorisasi</button>
                                //         </div>
                                //     </div>
                                //     ';
                                // }
                                ?>
                            </div>


                            <!-- Menu Marker -->
                            <hr>
                            <h4 class="hk-sec-title" style="margin:1rem;">Menu Marker</h4>

                            <div class="col-sm">
                                <div class="row">
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

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
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
                                </div>
                            </div>
                            <!-- <hr style="margin-left: 10px;margin-right: 10px;"> -->

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
                                    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain LIKE '%BHN%' ORDER BY id_bahan_kain ASC");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                        $no_id = $row['no_id'];
                                        $id_bahan_kain = $row['id_bahan_kain'];
                                        $qty_kain = $row['qty_kain'];
                                        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
                                        echo '
                                                    <tr >
                                                        <td class="text-center" >' . $q_kain['jenis_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</td>
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';
                                    $no_id = "";
                                    $id_sku = "";
                                    $ksl_qty_sku = 0;
                                    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku LIKE '%SKU%' ORDER BY id_sku ASC");
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
                                                        <td class="text-center" >' . $rasio_real . '</td>
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
                                                </tr>
                                                <tr ' . $hide . ' class="d-none">
                                                    <td class="text-center">
                                                        <input type="text" name="nama_sku_' . $no_id . '" id="" class="form-control form-control-sm nama_sku nama_sku_' . $no_id . '" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                                                        <datalist id="list_barang_2" class="list_barang_2">
                                                        </datalist>
                                                    </td>
                                                    <td class="text-center"><input type="text" name="qty_sku_' . $no_id . '" class="form-control form-control-sm qty_sku_' . $no_id . '" ' . $readonly . '></td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-success add_list_2 add_list_2_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
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
                                                            <input type="number" name="efisiensi" step="0.01" id="" class="form-control form-control-sm efisiensi" value="<?= $efisiensi; ?><?= $persen ?>" <?= $readonly; ?> required>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <input type="text" name="efisiensi" step="0.01" id="" class="form-control form-control-sm efisiensi" value="<?= $efisiensi; ?><?= $persen ?>" <?= $readonly; ?> required>
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
                            </div>


                            <!-- Menu Cutting -->
                            <hr>
                            <h4 class="hk-sec-title" style="margin:1rem;">Menu Cutting</h4>

                            <!-- <hr style="margin-left: 10px;margin-right: 10px;"> -->

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
                                                    <th class="text-center">ITEM</th>
                                                    <th class="text-center">Bruto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';

                                    $no_id = "";
                                    $id_bahan_kain = "";
                                    $ksl_qty_kain = 0;
                                    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain LIKE '%BHN%' ORDER BY barcode ASC");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                        $no_id = $row['no_id'];
                                        $id_bahan_kain = $row['id_bahan_kain'];

                                        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND barcode = '" . $row['barcode'] . "' "));
                                        $lot = $q_kain['lot'];
                                        $bruto = $q_kain['bruto'];
                                        echo '
                                                    <tr >
                                                        <td class="text-center">' . $lot . '  ' . $row['barcode'] . '</td>
                                                        <td class="text-center">' . $bruto . '</td>
                                                        
                                                        </tr>
                                                ';
                                        // <td class="text-center" >
                                        // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                        // </td>
                                        $ksl_qty_kain += $bruto;
                                    }
                                    // <td class="text-center" style="color:red"></td>
                                    echo '
                                                <tr >
                                                    <td class="text-center" style="color:red">Total</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_kain . '</td>
                                                    
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
                                                    <th class="text-center">Rasio</th>
                                                    <th class="text-center">QTY</th>
                                                    <th class="text-center">Bun.Body</th>
                                                    <th class="text-center">Bun.Tangan</th>
                                                    <th class="text-center">BS</th>
                                                    <th class="text-center">Bom</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';
                                    $no_id = "";
                                    $id_sku = "";
                                    $ksl_qty_sku = 0;
                                    $ksl_bom = 0;
                                    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku LIKE '%SKU%' ORDER BY id_sku ASC");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
                                        $no_id = $row['no_id'];
                                        $id_sku = $row['id_sku'];
                                        $qty_sku = $row['qty_sku'];
                                        $rasio_real = $row['rasio_real'];
                                        $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                        $bom = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(qty_kain) qty_bom FROM tb_bom WHERE kode_bom LIKE '%".trim($q_sku['kode_sku'])."%'"));

                                        echo '
                                                    <tr >
                                                        <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                        <td class="text-center" >' . $qty_sku . '</td>
                                                        <td class="text-center">' . $row['qty'] . '</td>
                                                        <td class="text-center">' . $row['b_body'] . '</td>
                                                        <td class="text-center">' . $row['b_tangan'] . '</td>
                                                        <td class="text-center">' . $row['bs'] . '</td>
                                                        <td class="text-center">' . round($bom['qty_bom'] * $row['qty'],2) . '</td>
                                                    </tr>
                                                ';
                                        // <td class="text-center" >
                                        //     <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                        // </td>
                                        $ksl_qty_sku += $qty_sku;
                                        $ksl_bom += $bom['qty_bom'] * $row['qty'];
                                    }
                                    // echo '
                                    //     <tr >
                                    //         <td class="text-center" style="color:red">Total</td>
                                    //         <td class="text-center" style="color:red">'. $ksl_qty_sku .'</td>
                                    //         <td class="text-center" style="color:red"></td>
                                    //     </tr>
                                    //     <tr '. $hide .' class="d-none">
                                    //         <td class="text-center">
                                    //             <input type="text" name="nama_sku_'.$no_id.'" id="" class="form-control form-control-sm nama_sku nama_sku_'.$no_id.'" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                                    //             <datalist id="list_barang_2" class="list_barang_2">
                                    //             </datalist>
                                    //         </td>
                                    //         <td class="text-center"><input type="text" name="qty_sku_'.$no_id.'" class="form-control form-control-sm qty_sku_'.$no_id.'" '.$readonly.'></td>
                                    //         <td class="text-center">
                                    //             <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-success add_list_2 add_list_2_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
                                    //         </td>
                                    //     </tr>
                                    // ';
                                    // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
                                    echo '
                                            </tbody>
                                        </table>
                                    ';
                                    ?>
                                </div>

                                <br>
                                <div class="col-sm">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="row no-gutter">
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;font-weight: 600;color:#273238" id="inputGroup-sizing-sm">INFORMASI CUTTING</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                        <?php
                                                        $total_bom = $ksl_bom;
                                                        // echo $total_bom.'<br>';
                                                        $bahan_pakai = $ksl_qty_kain;
                                                        // echo $bahan_pakai.'<br>';

                                                        $efisiensi_bom = $total_bom  / $bahan_pakai;
                                                        // echo $efisiensi_bom.'<br>';

                                                        $persen_efisien = round($efisiensi_bom,2) * 100;
                                                        // echo $persen_efisien.'<br>';
                                                        ?>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Total Bom</span>
                                                        </div>
                                                        <input type="text" name="total_bom" id="" class="form-control form-control-sm total_bom" value="<?= round($total_bom,2); ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Total Bruto</span>
                                                        </div>
                                                        <!-- <textarea name="keterangan" id="" style="height:33px;" class="form-control form-control-sm keterangan " autocomplete="off" onclick="this.select();" readonly><?= $keterangan ?></textarea> -->
                                                        <input type="text" name="total_bruto" id="" class="form-control form-control-sm total_bruto" value="<?= $bahan_pakai; ?>" <?= $readonly; ?>>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">EFISIENSI</span>
                                                        </div>
                                                        <input type="text" name="efisiensi" id="" class="form-control form-control-sm efisiensi" value="<?= $persen_efisien; ?>%" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row no-gutter">
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">GELAR BY</span>
                                                        </div>
                                                        <input type="text" name="nama_gelar" id="" class="form-control form-control-sm " value="<?= $nama_gelar; ?>" <?= $readonly; ?> placeholder="Nama" required>
                                                        <input type="time" name="jam_gelar" id="" class="form-control form-control-sm " value="<?= $jam_gelar; ?>" <?= $readonly; ?> required>
                                                        <input type="text" name="shift_gelar" id="" class="form-control form-control-sm " value="<?= $shift_gelar; ?>" <?= $readonly; ?> placeholder="Shift" required>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">POTONG BY</span>
                                                        </div>
                                                        <input type="text" name="nama_potong" id="" class="form-control form-control-sm " value="<?= $nama_potong; ?>" <?= $readonly; ?> placeholder="Nama" required>
                                                        <input type="time" name="jam_potong" id="" class="form-control form-control-sm " value="<?= $jam_potong; ?>" <?= $readonly; ?> required>
                                                        <input type="text" name="shift_potong" id="" class="form-control form-control-sm " value="<?= $shift_potong; ?>" <?= $readonly; ?> placeholder="Shift" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive daftar_bahan_3">
                                            <?php
                                            echo '
                                            <h6>RESULT MATERIAL</h6>
                                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">BUNDLE</th>
                                                            <th class="text-center"></th>
                                                            <th class="text-center">QTY</th>
                                                            <th class="text-center">BERAT</th>
                                                            <th class="text-center">OLEH</th>
                                                            <th class="text-center"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    ';

                                            $no_id = "";
                                            $id_bahan_material = "";
                                            $total_qty_materail = 0;
                                            $berat_materail = 0;
                                            $no = 1;
                                            $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_cutting WHERE id_transaksi = '" . $id_transaksi . "'");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                                $no_id = $row['no_id'];
                                                $id_bahan_material = $row['id_bahan_material'];
                                                echo '
                                                            <tr >
                                                                <td class="text-center">' . $no++ . '</td>
                                                                <td class="text-center">' . $id_bahan_material . '</td>
                                                                <td class="text-center">' . $row['qty_material'] . '</td>
                                                                <td class="text-center">' . $row['berat'] . ' Kg</td>
                                                                <td class="text-center">' . $row['oleh'] . '</td>
                                                                <td class="text-center" >
                                                                    <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list_3 del_list_3_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                                                </td>
                                                            </tr>
                                                        ';
                                                // <td class="text-center" >
                                                // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                                // </td>
                                                $total_qty_materail += $row['qty_material'];
                                                $berat_materail += $row['berat'];
                                            }
                                            // <td class="text-center" style="color:red"></td>
                                            echo '
                                                        <tr >
                                                            <td class="text-center" style="color:red">Total</td>
                                                            <td class="text-center" style="color:red"></td>
                                                            <td class="text-center" style="color:red">' . $total_qty_materail . '</td>
                                                            <td class="text-center" style="color:red">' . $berat_materail . ' Kg</td>
                                                            <td class="text-center" style="color:red">' . $berat_materail . ' Kg</td>
                                                            <td class="text-center" style="color:red"></td>
                                                            
                                                        </tr>
                                                    ';
                                            // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
                                            echo '
                                                    </tbody>
                                                </table>
                                            '
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-6" style="margin-top:20px">
                                        <div class="table-responsive daftar_bahan_4">
                                            <?php
                                            echo '
                                            <h6>SISA POTONG</h6>
                                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">BAHAN KAIN</th>
                                                            <th class="text-center">QTY</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    ';
                                            $no_id = "";
                                            $id_sku = "";
                                            $total_qty_sp = 0;
                                            $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain LIKE '%BHN%' ORDER BY id_bahan_kain ASC");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
                                                $no_id = $row['no_id'];
                                                $id_bahan_kain = $row['id_bahan_kain'];
                                                $qty_sp = $row['qty_sp'];
                                                $q_kain_sisa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
                                                echo '
                                                            <tr >
                                                                <td class="text-center" >' . $q_kain_sisa['jenis_kain'] . ' - ' . $q_kain_sisa['warna'] . ' - ' . $q_kain_sisa['setting'] . ' - ' . $q_kain_sisa['gramasi'] . '</td>
                                                                <td class="text-center">' . $qty_sp . '</td>
                                                            </tr>
                                                        ';
                                                // <td class="text-center" >
                                                //     <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                                // </td>
                                                $total_qty_sp += $qty_sp;
                                            }
                                            echo '
                                                        <tr >
                                                            <td class="text-center" style="color:red">Total</td>
                                                            <td class="text-center" style="color:red">' . $total_qty_sp . ' Kg</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            ';
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-6" style="margin-top:20px">
                                        <div class="table-responsive daftar_bahan_5">
                                            <?php
                                            echo '
                                            <h6>BS POTONG</h6>
                                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center">BAHAN KAIN</th>
                                                            <th class="text-center">QTY</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    ';
                                            $no_id = "";
                                            $id_sku = "";
                                            $total_qty_bsp = 0;
                                            $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku LIKE '%SKU%' ORDER BY id_sku ASC");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
                                                $no_id = $row['no_id'];
                                                $id_sku = $row['id_sku'];
                                                $qty_bsp = $row['qty_bsp'];
                                                $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                                echo '
                                                            <tr >
                                                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                                <td class="text-center" >' . $qty_bsp . '</td>
                                                            </tr>
                                                        ';
                                                // <td class="text-center" >
                                                //     <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                                // </td>
                                                $total_qty_bsp += $qty_bsp;
                                            }
                                            echo '
                                                        <tr >
                                                            <td class="text-center" style="color:red">Total</td>
                                                            <td class="text-center" style="color:red">' . $total_qty_bsp . '</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            ';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Menu Sewing -->
                            <hr>
                            <h4 class="hk-sec-title" style="margin:1rem;">Menu Sewing </h4>

                            <div class="row no-gutter">
                                <div class="col-md-10">
                                    <div class="input-group mb-3">

                                    </div>
                                </div>
                                <div class="col-md-2">
                                </div>

                            </div>

                            <div class="col-12">
                                <div class="col-sm">
                                    <!-- <div class="row">
                                        <div class="col-md-5">
                                            <div class="row no-gutter">
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL SEWING</span>
                                                        </div>
                                                        <input type="date" name="tgl_sewing" id="" class="form-control form-control-sm tgl_sewing" value="<?= $tgl_sewing; ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">GELAR BY</span>
                                                        </div>
                                                        <input type="time" name="jam_mulai" id="" class="form-control form-control-sm " value="<?= $jam_mulai; ?>" <?= $readonly; ?> required>
                                                        <input type="time" name="jam_selesai" id="" class="form-control form-control-sm " value="<?= $jam_selesai; ?>" <?= $readonly; ?> required>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LINE SEWING</span>
                                                        </div>
                                                        <input type="number" name="line" id="" class="form-control form-control-sm line" value="<?= $line; ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                                                        </div>
                                                        <input type="number" name="anggota" id="" class="form-control form-control-sm anggota" value="<?= $anggota; ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                                                        </div>
                                                        <input type="text" name="pic" id="" class="form-control form-control-sm pic" value="<?= $pic; ?>" <?= $readonly ?>>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="table-responsive daftar_bahan">
                                            <?php
                                            $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_sewing");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                                $id_sewing = $row['id_sewing'];
                                                $tgl_sewing = $row['tgl_sewing'];
                                                $jam_mulai = $row['jam_mulai'];
                                                $jam_selesai = $row['jam_selesai'];
                                                $line = $row['line'];
                                                $anggota = $row['anggota'];
                                                $pic = $row['pic'];
                                                echo '
                                                <br>
                                                <div class="row" style="margin-right:0px;">
                                                    <div class="col-md-5">
                                                        <div class="row no-gutter">
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL SEWING</span>
                                                                    </div>
                                                                    <input type="date" name="tgl_sewing" id="" class="form-control form-control-sm ubah_data_input tgl_sewing_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '"  value="' . $tgl_sewing . '" ' . $readonly . ' required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                                                                    </div>
                                                                    <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_mulai . '" ' . $readonly . ' required>
                                                                    <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_selesai . '" ' . $readonly . ' required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LINE SEWING</span>
                                                                    </div>
                                                                    <input type="number" name="line" id="" class="form-control form-control-sm ubah_data_input line_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $line . '" ' . $readonly . ' required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                                                                    </div>
                                                                    <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $anggota . '" ' . $readonly . ' required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                                                                    </div>
                                                                    <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $pic . '" ' . $readonly . ' required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list del_list_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" style="margin-top:196px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
                                                    </div>
                                                </div>
                                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">NO</th>
                                                                <th class="text-center">ITEM BARANG jADI</th>
                                                                <th class="text-center">QTY</th>
                                                                <th class="text-center">BS</th>
                                                                <th class="text-center">TURUN SIZE</th>
                                                                <th class="text-center">BTJ</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        ';

                                                $no_id = "";
                                                $ttl_qty_jadi = 0;
                                                $ttl_bs = 0;
                                                $ttl_turun = 0;
                                                $ttl_btj = 0;
                                                $no = 1;
                                                $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' AND id_sewing = '" . $id_sewing . "'");
                                                while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                                                    $no_id = $row2['no_id'];
                                                    $id_sku = $row2['id_sku'];
                                                    $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                                    echo '
                                                                <tr >
                                                                    <td class="text-center">' . $no++ . '</td>
                                                                    <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                                    <td class="text-center">' . $row2['qty_jadi'] . '</td>
                                                                    <td class="text-center">' . $row2['bs'] . '</td>
                                                                    <td class="text-center">' . $row2['turun'] . '</td>
                                                                    <td class="text-center">' . $row2['btj'] . '</td>
                                                                </tr>
                                                            ';
                                                    $ttl_qty_jadi += $row2['qty_jadi'];
                                                    $ttl_bs += $row2['bs'];
                                                    $ttl_turun += $row2['turun'];
                                                    $ttl_btj += $row2['btj'];
                                                }
                                                // <td class="text-center" style="color:red"></td>
                                                echo '
                                                            <tr >
                                                                <td class="text-center" style="color:red"></td>
                                                                <td class="text-center" style="color:red">Total</td>
                                                                <td class="text-center" style="color:red">' . $ttl_qty_jadi . '</td>
                                                                <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                                                                <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                                                                <td class="text-center" style="color:red">' . $ttl_btj . '</td>
                                                            </tr>
                                                        ';
                                                // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
                                                echo '
                                                        </tbody>
                                                    </table>
                                                ';
                                            }
                                            ?>

                                        </div>
                                        <div class="row mt-10 justify-content-end">
                                            <div class="col-sm-2">
                                                <div class="form-group form-group-sm">
                                                    <button type="submit" <?= $hide ?> class="btn btn-success btn-sm" style="width:10vw;" name="add_tabel" style="color:white;"><i class="fa fa-plus"></i> Tambah Tabel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Menu Finishing -->
                            <hr>
                            <h4 class="hk-sec-title" style="margin:1rem;">Menu Finishing </h4>

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
                                            <h6>TABEL GOSOK LIPAT</h6>
                                            <?php
                                            $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 2 GROUP BY id_finishing");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                                $id_finishing = $row['id_finishing'];
                                                $tgl_qc = $row['tgl_qc'];
                                                $jam_mulai = $row['jam_mulai'];
                                                $jam_selesai = $row['jam_selesai'];
                                                $anggota = $row['anggota'];
                                                $pic = $row['pic'];
                                                echo '
                                                    <br>
                                                    <div class="row" style="margin-right:0px;">
                                                        <div class="col-md-5">
                                                            <div class="row no-gutter">
                                                                <div class="col-12">
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL GOSOK LIPAT</span>
                                                                        </div>
                                                                        <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_2 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '" ' . $readonly . ' required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                                                                        </div>
                                                                        <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_2 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '" ' . $readonly . ' required>
                                                                        <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_2 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '" ' . $readonly . ' required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                                                                        </div>
                                                                        <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_2 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '" ' . $readonly . ' required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="input-group mb-3">
                                                                        <div class="input-group-prepend">
                                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                                                                        </div>
                                                                        <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_2 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '" ' . $readonly . ' required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5">
                                                            <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
                                                        </div>

                                                        <div class="col-md-7">
                                                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="text-center">NO</th>
                                                                        <th class="text-center">BB,GOSOK,LIPAT</th>
                                                                        <th class="text-center">QTY</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                ';

                                                $no_id = "";
                                                $ttl_qty_bbgl = 0;
                                                $no = 1;
                                                $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
                                                while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                                                    $no_id = $row2['no_id'];
                                                    $id_sku = $row2['id_sku'];
                                                    $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                                    echo '
                                                                        <tr >
                                                                            <td class="text-center">' . $no++ . '</td>
                                                                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                                            <td class="text-center">' . $row2['qty_bbgl'] . '</td>
                                                                        </tr>
                                                                    ';
                                                    $ttl_qty_bbgl += $row2['qty_bbgl'];
                                                }
                                                // <td class="text-center" style="color:red"></td>
                                                echo '
                                                                    <tr >
                                                                        <td class="text-center" style="color:red"></td>
                                                                        <td class="text-center" style="color:red">Total</td>
                                                                        <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
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
                                        <br><br>
                                        <div class="table-responsive daftar_bahan">
                                            <h6>TABEL QC</h6>
                                            <?php
                                            $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 1 GROUP BY id_finishing");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                                $id_finishing = $row['id_finishing'];
                                                $tgl_qc = $row['tgl_qc'];
                                                $jam_mulai = $row['jam_mulai'];
                                                $jam_selesai = $row['jam_selesai'];
                                                $anggota = $row['anggota'];
                                                $pic = $row['pic'];
                                                echo '
                                                <br>
                                                <div class="row" style="margin-right:0px;">
                                                    <div class="col-md-5">
                                                        <div class="row no-gutter">
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL QC</span>
                                                                    </div>
                                                                    <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '" ' . $readonly . ' required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                                                                    </div>
                                                                    <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '" ' . $readonly . ' required>
                                                                    <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '" ' . $readonly . ' required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                                                                    </div>
                                                                    <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '" ' . $readonly . ' required>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="input-group mb-3">
                                                                    <div class="input-group-prepend">
                                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                                                                    </div>
                                                                    <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '" ' . $readonly . ' required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list del_list_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
                                                    </div>
                                                </div>
                                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">NO</th>
                                                                <th class="text-center">QC</th>
                                                                <th class="text-center">TOTAL</th>
                                                                <th class="text-center">BS</th>
                                                                <th class="text-center">CUCI</th>
                                                                <th class="text-center">KOTOR</th>
                                                                <th class="text-center">TURUN SIZE</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        ';

                                                $no_id = "";
                                                $ttl_total = 0;
                                                $ttl_bs = 0;
                                                $ttl_cuci = 0;
                                                $ttl_kotor = 0;
                                                $ttl_turun = 0;
                                                $no = 1;
                                                $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
                                                while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                                                    $no_id = $row2['no_id'];
                                                    $id_sku = $row2['id_sku'];
                                                    $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                                    echo '
                                                                <tr >
                                                                    <td class="text-center">' . $no++ . '</td>
                                                                    <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                                    <td class="text-center">' . $row2['total'] . '</td>
                                                                    <td class="text-center">' . $row2['bs'] . '</td>
                                                                    <td class="text-center">' . $row2['cuci'] . '</td>
                                                                    <td class="text-center">' . $row2['kotor'] . '</td>
                                                                    <td class="text-center">' . $row2['turun'] . '</td>
                                                                </tr>
                                                            ';
                                                    $ttl_total += $row2['total'];
                                                    $ttl_bs += $row2['bs'];
                                                    $ttl_cuci += $row2['cuci'];
                                                    $ttl_kotor += $row2['kotor'];
                                                    $ttl_turun += $row2['turun'];
                                                }
                                                // <td class="text-center" style="color:red"></td>
                                                echo '
                                                            <tr >
                                                                <td class="text-center" style="color:red"></td>
                                                                <td class="text-center" style="color:red">Total</td>
                                                                <td class="text-center" style="color:red">' . $ttl_total . '</td>
                                                                <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                                                                <td class="text-center" style="color:red">' . $ttl_cuci . '</td>
                                                                <td class="text-center" style="color:red">' . $ttl_kotor . '</td>
                                                                <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                                                            </tr>
                                                        ';
                                                // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
                                                echo '
                                                        </tbody>
                                                    </table>
                                                ';
                                            }
                                            ?>

                                        </div>
                                        <div class="row mt-10 justify-content-end">
                                            <div class="col-2" style="margin-right:40px;">
                                                <div class="form-group form-group-sm">
                                                    <button type="submit" <?= $hide ?> class="btn btn-success btn-sm" style="width:15vw;" name="add_tabel_2" style="color:white;"><i class="fa fa-plus"></i> Tambah Gosok Lipat</button>
                                                </div>
                                            </div>
                                            <div class="col-2" style="margin-right:13px;">
                                                <div class="form-group form-group-sm">
                                                    <button type="submit" <?= $hide ?> class="btn btn-success btn-sm" style="width:12vw;" name="add_tabel" style="color:white;"><i class="fa fa-plus"></i> Tambah Tabel QC</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Menu Packing -->
                            <hr>
                            <h4 class="hk-sec-title" style="margin:1rem;">Menu Packing </h4>

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
                                                                            <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL GOSOK LIPAT</span>
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
                                    </div>
                                </div>
                                <?php
                                if (isset($_GET['detail'])) {
                                    echo '
                                    <div class="col-sm">
                                        <div class="row">
                                            <a href="laporan_work_order.php" class="btn btn-primary mb-2"><i class="fa fa-arrow-left"></i> Kembali</a>
                                            
                                        </div>
                                    </div>
                                    ';
                                    // <a href="print/print_invoice_barang_masuk.php?id_transaksi=' . $id_transaksi . '" class="btn btn-success ml-2 mb-2" target="_blank"><i class="fa fa-print"></i> Print Invoice</a>
                                } else {
                                    // <button type="submit" name="simpan" class="btn btn-sm btn-success ml-2 mb-2">Simpan</button>
                                    echo '
                                    <div class="col-sm">
                                        <div class="row">
                                            <a href="laporan_work_order.php" class="btn btn-sm btn-danger mb-2"> Kembali</a>
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
            var id_bahan = $(this).data('id_bahan_kain');
            var lot = $(".lot").val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "cari_lot": lot,
                    "id_bahan": id_bahan
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
            var lot = $(".lot_" + id_tambah).val();

            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "add_list": id_tambah,
                    "id_transaksi": id_transaksi,
                    "lot": lot
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
                    $(".daftar_bahan").html(result);

                    $(".nama_barang").val("");
                    // $(".qty_barang").val("");
                    // $(".harga_barang").val("");
                    // $(".berat_barang").val("");
                    // $(".uom_barang").val("");
                }
            });
        });

        $(document).on("click", ".add_list_2", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var id_tambah = $(this).data('id');
            // var id_bahan_kain = $(this).data('id_bahan_kain');
            var nama_sku = $(".nama_sku_" + id_tambah).val();
            var qty_sku = $(".qty_sku_" + id_tambah).val();
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
                    url: "ajax/ajax_work_order.php",
                    data: {
                        "add_list_2": id_tambah,
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

            $(".del_list_" + id_hapus).removeClass("del_list");
            $(".del_list_" + id_hapus).removeClass("btn-danger");
            $(".del_list_" + id_hapus).addClass("btn-secondary");
            $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");

            $.ajax({
                type: "POST",
                url: "ajax/ajax_work_order.php",
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

        $(document).on("click", ".del_list_2", function() {
            var id_hapus = $(this).data('id');
            var id_hapus_transaksi = "<?= $_GET['id_transaksi']; ?>";

            // $(".del_list_2_" + id_hapus).removeClass("del_list_2");
            // $(".del_list_" + id_hapus).removeClass("btn-danger");
            // $(".del_list_" + id_hapus).addClass("btn-secondary");
            // $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");

            $.ajax({
                type: "POST",
                url: "ajax/ajax_work_order.php",
                data: {
                    "id_hapus_2": id_hapus,
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

        $(document).on("click", ".del_list_3", function() {
            var id_hapus = $(this).data('id');
            var id_hapus_transaksi = "<?= $_GET['id_transaksi']; ?>";

            // $(".del_list_2_" + id_hapus).removeClass("del_list_2");
            // $(".del_list_" + id_hapus).removeClass("btn-danger");
            // $(".del_list_" + id_hapus).addClass("btn-secondary");
            // $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");

            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "id_hapus_3": id_hapus,
                    "id_hapus_transaksi": id_hapus_transaksi
                },
                cache: true,
                success: function(response) {
                    // $(".del_list_2_" + id_hapus).addClass("del_list_2");
                    // $(".del_list_" + id_hapus).addClass("btn-danger");
                    // $(".del_list_" + id_hapus).removeClass("btn-secondary");
                    // $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");
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

        $(document).on("click", ".simpan", function() {
            var simpan = "<?= $_GET['id_transaksi']; ?>";
            var tanggal_transaksi = $(".tanggal_transaksi").val();
            var keterangan = $(".keterangan").val();
            if (tanggal_transaksi == "") {
                alert("Maaf, nama supplier tidak boleh kosong");
            } else {

                $(".btn_simpan").removeClass("simpan");
                $(".btn_simpan").removeClass("btn-success");
                $(".btn_simpan").addClass("btn-secondary");
                $(".btn_simpan").html("Loading <i class='fa fa-spinner fa-spin fa-fw'></i>");

                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_barang_masuk.php",
                    data: {
                        "simpan": simpan,
                        "tanggal_transaksi": tanggal_transaksi,
                        "keterangan": keterangan
                    },
                    cache: true,
                    success: function(response) {
                        if (response == 1) {
                            window.location.href = "master_barang_masuk.php";
                        } else {
                            alert("Maaf, proses simpan gagal");
                        }
                        $(".btn_simpan").addClass("simpan");
                        $(".btn_simpan").addClass("btn-success");
                        $(".btn_simpan").removeClass("btn-secondary");
                        $(".btn_simpan").html("Simpan");
                    }
                });
            }
        });

        $(document).on("click", ".simpan_oto", function() {
            var simpan_oto = "<?= $_GET['id_transaksi']; ?>";
            var tanggal_transaksi = $(".tanggal_transaksi").val();
            var keterangan = $(".keterangan").val();



            if (tanggal_transaksi == "") {
                alert("Maaf, nama supplier tidak boleh kosong");
            } else {

                $(".btn_simpan_oto").removeClass("simpan_oto");
                $(".btn_simpan_oto").removeClass("btn-danger");
                $(".btn_simpan_oto").addClass("btn-secondary");
                $(".btn_simpan_oto").html("Loading <i class='fa fa-spinner fa-spin fa-fw'></i>");

                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_barang_masuk.php",
                    data: {
                        "simpan_oto": simpan_oto,
                        "tanggal_transaksi": tanggal_transaksi,
                        "keterangan": keterangan
                    },
                    cache: true,
                    success: function(response) {
                        $(".btn_simpan_oto").addClass("simpan_oto");
                        $(".btn_simpan_oto").addClass("btn-danger");
                        $(".btn_simpan_oto").removeClass("btn-secondary");
                        $(".btn_simpan_oto").html("Simpan & Otorisasi");
                        if (response == 1) {
                            window.location.href = "master_barang_masuk.php";
                        } else {
                            alert("Maaf, proses simpan gagal");
                        }
                    }
                });
            }
        });

        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var bolong = $(".bolong_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "bolong": bolong
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });

        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var kotor = $(".kotor_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "kotor": kotor
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });

        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var patah = $(".patah_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "patah": patah
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });

        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var belang = $(".belang_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "belang": belang
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });

        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var garis = $(".garis_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "garis": garis
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });

        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var ket = $(".ket_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "ket": ket
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);
                }
            });
        });


    });
</script>