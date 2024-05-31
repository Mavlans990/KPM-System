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


        $select_marker = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi"));
        $inspek_by = $select_marker['inspek_by'];
        $tgl_inspek = $select_marker['tgl_inspek'];
        $jam_inspek = $select_marker['jam_inspek'];
        $penerima = $select_marker['penerima'];
        $keterangan = $select_marker['keterangan'];
    }
}

// if (isset($_GET['detail'])) {
//     $tgl_marker = date("Y-m-d", strtotime($select_marker['tgl_marker']));
// }

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

    $query_wo = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "'");
    $jum = mysqli_num_rows($query_wo);

    if ($jum > 0) {
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
    } else {
        $insert = "INSERT INTO tb_inspek(
            id_transaksi,
            tgl_inspek,
            inspek_by,
            jam_inspek,
            kategori,
            warna,
            penerima,
            keterangan
        ) VALUES(
            '" . $id_transaksi . "',
            '" . $tgl_inspek . "',
            '" . $inspek_by . "',
            '" . $jam_inspek . "',
            '" . $kategori . "',
            '" . $warna . "',
            '" . $penerima . "',
            '" . $keterangan . "'
        )";
        $query = mysqli_query($conn, $insert);
    }



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
    // echo $update;

    $update2 = "UPDATE tb_work_order SET
        sts_inspek = 's'
        WHERE id_transaksi = '" . $id_transaksi . "'
    ";

    if($update2){
        $sql_barcode = mysqli_query($conn,"SELECT barcode FROM tb_inspek WHERE id_transaksi = '".$id_transaksi."'");
        while($row_barcode = mysqli_fetch_array($sql_barcode)){
            $update_barang = mysqli_query($conn,"UPDATE tb_barang_masuk SET terpakai = '".$id_transaksi."' WHERE barcode = '".$row_barcode['barcode']."' ");
        }
    }

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
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">INSPEK BY</span>
                                                    </div>
                                                    <input type="text" name="inspek_by" id="" class="form-control form-control-sm inspek_by" value="<?= $inspek_by; ?>" <?= $readonly; ?> required>
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
                                    $total_roll = 0;
                                    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain != '' GROUP BY id_bahan_kain");
                                    $jum_data = mysqli_num_rows($select_barang_masuk);
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                        $id_bahan_kain = $row['id_bahan_kain'];
                                        $no_id = $row['no_id'];
                                        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "'"));
                                        echo '
                                            <br>
                                            <p>' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . ' : ' . $row['qty_kain'] . ' ROLL</p>
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
                                                        <td class="text-center">
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="bolong_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bolong_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                                            <input type="number" style="display:inline;width:30%" name="bolong_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak bolong_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['bolong'] . '" ' . $readonly . ' >
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="bolong_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bolong_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center">
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="kotor_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                                            <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['kotor'] . '" ' . $readonly . ' >
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="kotor_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center">
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="patah_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang patah_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                                            <input type="number" style="display:inline;width:30%" name="patah_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak patah_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['patah'] . '" ' . $readonly . ' >
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="patah_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah patah_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center">
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="belang_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang belang_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                                            <input type="number" style="display:inline;width:30%" name="belang_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak belang_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['belang'] . '" ' . $readonly . ' >
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="belang_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah belang_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center">
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="garis_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang garis_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="5"><i class="fa fa-minus"></i></button>
                                                            <input type="number" style="display:inline;width:30%" name="garis_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak garis_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['garis'] . '" ' . $readonly . ' >
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="garis_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah garis_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center">' . $total_ksl . '</td>
                                                        <td class="text-center">
                                                            <textarea name="ket_' . $no_id2 . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id2 . '" data-no_id="' . $no_id2 . '" ' . $readonly . '>' . $row2['ket'] . '</textarea>
                                                        </td>
                                                        <td class="text-center" >' . $bruto . '</td>
                                                    ';
                                            if (!isset($_GET['detail'])) {
                                                echo '
                                                        <td class="text-center" >
                                                            <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list del_list_' . $no_id2 . '" data-id="' . $no_id2 . '"><i class="fa fa-trash-o"></i></a>
                                                        </td>
                                                            ';
                                            }
                                            echo '
                                                    </tr>
                                                    ';
                                        }
                                        // <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '"><i class="fa fa-plus"></i></a>
                                        // <a href="www.facebook.com" class="btn btn-xs btn-success add_list add_list_' . $no_id . '">CEK</a>
                                        echo '
                                                    <tr ' . $hide . '>
                                                        <td class="text-center" colspan="2">
                                                        <input type="text" name="lot_' . $no_id . '"  class="form-control form-control-sm lot lot_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '" autocomplete="off" list="list_lot" onclick="this.select();">
                                                        <datalist id="list_lot" class="list_lot">
                                                        </datalist>
                                                        </td>
                                                        <td class="text-center">
                                                        <button href="javascript:void(0);" ' . $hide . ' type="button" class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '"><i class="fa fa-plus"></i></button>
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
                                            <button type="submit" name="simpan" class="simpan btn btn-sm btn-success ml-2 mb-2">Simpan</button>
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

        $(document).on("mouseover", ".lot", function() {
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

        $(document).on("click", ".lot", function() {
            var id_tambah = $(this).data('id');
            var id_tambah_2 = $('.add_list').data('id');
            var tombol = $('.add_list');
            $(".lot").on("keypress", function (e) {
                if (e.keyCode === 13) {
                    var addButton = document.querySelector('.add_list');
                    tombol.data('id', id_tambah);
                    // $(".add_list").val(id_tambah);
                    addButton.click()
                }
            })
            // tombol.data('id', id_tambah);
            // console.log("punya lot" + id_tambah);
            // console.log("punya add_list" + id_tambah_2);
        });

        // $(document).on("click", ".lot", function() {
        //     var id_tambah = $(this).data('id');
        //     var lotInput = $(".lot_" + id_tambah);

        //     function handleKeyPress(e) {
        //         if (e.keyCode === 13) {
        //             var addButton = document.querySelector('.add_list');
        //             addButton.click();
        //             console.log(id_tambah);

        //             // Hapus event listener setelah Enter ditekan
        //             // lotInput.off("keypress", handleKeyPress);
        //         }
        //     }

        //     lotInput.on("keypress", handleKeyPress);
        // });

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

        // $(document).on("paste", ".lot", function(e) {
        //     var addButton = document.querySelector('.add_list');
        //     var pastedData = e.originalEvent.clipboardData.getData("text");
        //     $(".lot").val(pastedData);
        //     addButton.click()
        // });

        $(document).on("click", ".add_list", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var id_tambah = $(this).data('id');
            // var id_bahan_kain = $(this).data('id_bahan_kain');
            var lot = $(".lot_" + id_tambah).val();
            // console.log(id_transaksi);
            // console.log(id_tambah);
            // console.log(lot);
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

            // $(".del_list_2_" + id_hapus).removeClass("del_list_2");
            // $(".del_list_" + id_hapus).removeClass("btn-danger");
            // $(".del_list_" + id_hapus).addClass("btn-secondary");
            // $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");

            $.ajax({
                type: "POST",
                url: "ajax/ajax_inspek.php",
                data: {
                    "id_hapus": id_hapus,
                    "id_hapus_transaksi": id_hapus_transaksi
                },
                cache: true,
                success: function(response) {
                    // console.log(response);
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

        // $(document).on("click", ".simpan", function() {
        //     var simpan = "<?= $_GET['id_transaksi']; ?>";
        //     var tanggal_transaksi = $(".tanggal_transaksi").val();
        //     var keterangan = $(".keterangan").val();
        //     if (tanggal_transaksi == "") {
        //         alert("Maaf, nama supplier tidak boleh kosong");
        //     } else {

        //         $(".btn_simpan").removeClass("simpan");
        //         $(".btn_simpan").removeClass("btn-success");
        //         $(".btn_simpan").addClass("btn-secondary");
        //         $(".btn_simpan").html("Loading <i class='fa fa-spinner fa-spin fa-fw'></i>");

        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_barang_masuk.php",
        //             data: {
        //                 "simpan": simpan,
        //                 "tanggal_transaksi": tanggal_transaksi,
        //                 "keterangan": keterangan
        //             },
        //             cache: true,
        //             success: function(response) {
        //                 if (response == 1) {
        //                     window.location.href = "master_barang_masuk.php";
        //                 } else {
        //                     alert("Maaf, proses simpan gagal");
        //                 }
        //                 $(".btn_simpan").addClass("simpan");
        //                 $(".btn_simpan").addClass("btn-success");
        //                 $(".btn_simpan").removeClass("btn-secondary");
        //                 $(".btn_simpan").html("Simpan");
        //             }
        //         });
        //     }
        // });

        // $(document).on("click", ".simpan_oto", function() {
        //     var simpan_oto = "<?= $_GET['id_transaksi']; ?>";
        //     var tanggal_transaksi = $(".tanggal_transaksi").val();
        //     var keterangan = $(".keterangan").val();



        //     if (tanggal_transaksi == "") {
        //         alert("Maaf, nama supplier tidak boleh kosong");
        //     } else {

        //         $(".btn_simpan_oto").removeClass("simpan_oto");
        //         $(".btn_simpan_oto").removeClass("btn-danger");
        //         $(".btn_simpan_oto").addClass("btn-secondary");
        //         $(".btn_simpan_oto").html("Loading <i class='fa fa-spinner fa-spin fa-fw'></i>");

        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_barang_masuk.php",
        //             data: {
        //                 "simpan_oto": simpan_oto,
        //                 "tanggal_transaksi": tanggal_transaksi,
        //                 "keterangan": keterangan
        //             },
        //             cache: true,
        //             success: function(response) {
        //                 $(".btn_simpan_oto").addClass("simpan_oto");
        //                 $(".btn_simpan_oto").addClass("btn-danger");
        //                 $(".btn_simpan_oto").removeClass("btn-secondary");
        //                 $(".btn_simpan_oto").html("Simpan & Otorisasi");
        //                 if (response == 1) {
        //                     window.location.href = "master_barang_masuk.php";
        //                 } else {
        //                     alert("Maaf, proses simpan gagal");
        //                 }
        //             }
        //         });
        //     }
        // });

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

        //Fungsi Kurang
        $(document).on("click", ".ubah_data_barang_kurang", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var bolong = $(".bolong_k_" + no_id).val();
            var kotor = $(".kotor_k_" + no_id).val();
            var patah = $(".patah_k_" + no_id).val();
            var belang = $(".belang_k_" + no_id).val();
            var garis = $(".garis_k_" + no_id).val();
            var tes = $(this).data('tes');
            // console.log(tes);
            if (tes == 1) {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_kurang": no_id,
                        "id_transaksi": id_transaksi,
                        "bolong": bolong
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }else if(tes == 2){
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_kurang": no_id,
                        "id_transaksi": id_transaksi,
                        "kotor": kotor
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }else if(tes == 3){
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_kurang": no_id,
                        "id_transaksi": id_transaksi,
                        "patah": patah
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }else if(tes == 4){
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_kurang": no_id,
                        "id_transaksi": id_transaksi,
                        "belang": belang
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }else if(tes == 5){
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_kurang": no_id,
                        "id_transaksi": id_transaksi,
                        "garis": garis
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }
            
        });

        //Fungsi Tambah
        $(document).on("click", ".ubah_data_barang_tambah", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var bolong = $(".bolong_t_" + no_id).val();
            var kotor = $(".kotor_t_" + no_id).val();
            var patah = $(".patah_t_" + no_id).val();
            var belang = $(".belang_t_" + no_id).val();
            var garis = $(".garis_t_" + no_id).val();
            var tes = $(this).data('tes2');
            // console.log(tes);
            if (tes == 1) {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_tambah": no_id,
                        "id_transaksi": id_transaksi,
                        "bolong": bolong
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }else if(tes == 2){
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_tambah": no_id,
                        "id_transaksi": id_transaksi,
                        "kotor": kotor
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }else if(tes == 3){
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_tambah": no_id,
                        "id_transaksi": id_transaksi,
                        "patah": patah
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }else if(tes == 4){
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_tambah": no_id,
                        "id_transaksi": id_transaksi,
                        "belang": belang
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }else if(tes == 5){
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_inspek.php",
                    data: {
                        "ubah_data_barang_tambah": no_id,
                        "id_transaksi": id_transaksi,
                        "garis": garis
                    },
                    cache: true,
                    success: function(result) {
                        $(".daftar_bahan").html(result);
                    }
                });
            }
        });


    });
</script>