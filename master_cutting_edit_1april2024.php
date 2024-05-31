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
$efisiensi = "";
$pjg_marker = "";
$keterangan = "";
$nama_gelar = "";
$jam_gelar = "";
$shift_gelar = "";
$nama_potong = "";
$jam_potong = "";
$shift_potong = "";
$qty_ = "";
$b_body_ = "";
$b_tangan_ = "";
$bs_ = "";
$no_id = "";
$qty_sp = "";

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
    // print_r($_POST);
    // return 0;
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    // $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    // $ex_nm_supplier = explode(" | ", $nama_supplier);
    // $id_supplier = $ex_nm_supplier[0];
    $nama_gelar = mysqli_real_escape_string($conn, $_POST['nama_gelar']);
    $jam_gelar = mysqli_real_escape_string($conn, $_POST['jam_gelar']);
    $shift_gelar = mysqli_real_escape_string($conn, $_POST['shift_gelar']);
    $nama_potong = mysqli_real_escape_string($conn, $_POST['nama_potong']);
    $jam_potong = mysqli_real_escape_string($conn, $_POST['jam_potong']);
    $shift_potong = mysqli_real_escape_string($conn, $_POST['shift_potong']);


    $query_cut = mysqli_query($conn, "SELECT * FROM tb_cutting WHERE id_transaksi = '" . $id_transaksi . "'");
    $jum_cut = mysqli_num_rows($query_cut);
    // echo "SELECT * FROM tb_cutting WHERE id_transaksi = '" . $id_transaksi . "'";
    // echo $jum_cut;
    // return 0;



    // if ($jum_cut > 0) {

    $update = "UPDATE tb_cutting SET
            nama_gelar = '" . $nama_gelar . "',
            jam_gelar = '" . $jam_gelar . "',
            shift_gelar = '" . $shift_gelar . "',
            nama_potong = '" . $nama_potong . "',
            jam_potong = '" . $jam_potong . "',
            shift_potong = '" . $shift_potong . "'
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";
    // $update = "SELECT * FROM tb_ukuran";

    $query_update = mysqli_query($conn, $update);
    // echo "x";

    if ($query_update) {
        // echo"xxx";
        // echo "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku != ''";
        $select_wo =  mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku != ''");

        while ($row_wo = mysqli_fetch_assoc($select_wo)) {
            $no_id = $row_wo['no_id'];
            $qty = mysqli_real_escape_string($conn, $_POST['qty_' . $no_id]);
            $b_body = mysqli_real_escape_string($conn, $_POST['b_body_' . $no_id]);
            $b_tangan = mysqli_real_escape_string($conn, $_POST['b_tangan_' . $no_id]);
            $bs = mysqli_real_escape_string($conn, $_POST['bs_' . $no_id]);

            $update_wo = "UPDATE tb_work_order SET
                    qty = '" . $qty . "',
                    b_body = '" . $b_body . "',
                    b_tangan = '" . $b_tangan . "',
                    bs = '" . $bs . "',
                    qty_sp ='" . $qty_sp . "'
                    WHERE no_id = '" . $no_id . "';
                    ";

            $query_wo = mysqli_query($conn, $update_wo);

            // echo "UPDATE tb_work_order SET
            // qty = '" . $qty . "',
            // b_body = '" . $b_body . "',
            // b_tangan = '" . $b_tangan . "',
            // bs = '" . $bs . "',
            // qty_sp ='" . $qty_sp . "'
            // WHERE no_id = '" . $no_id . "';
            // ";
        }

        $select_sp =  mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain != ''");

        while ($row_sp = mysqli_fetch_assoc($select_sp)) {
            $no_id = $row_sp['no_id'];
            $qty_sp = mysqli_real_escape_string($conn, $_POST['qty_sp_' . $no_id]);

            $update_sp = "UPDATE tb_work_order SET
                    qty_sp ='" . $qty_sp . "'
                    WHERE no_id = '" . $no_id . "';
                    ";

            $query_sp = mysqli_query($conn, $update_sp);

            // echo "UPDATE tb_work_order SET
            // qty_sp ='" . $qty_sp . "'
            // WHERE no_id = '" . $no_id . "';
            // ";
        }

        $select_sisa =  mysqli_query($conn, "SELECT w.id_transaksi as id_wo,w.id_bahan_kain,w.tgl_awal,w.qty_sp, i.barcode, bm.id_transaksi,bm.tgl_transaksi,bm.lot,bm.no_sj_greige,bm.no_sj_celup,bm.no_ph_bahan,bm.celup_by,bm.greige_by,bm.status FROM tb_work_order w LEFT JOIN tb_inspek i ON w.id_transaksi = i.id_transaksi AND w.id_bahan_kain = i.id_bahan_kain LEFT JOIN tb_barang_masuk bm ON i.id_bahan_kain = bm.id_bahan_kain AND i.barcode = bm.barcode WHERE w.id_transaksi = '" . $id_transaksi . "' AND w.id_bahan_kain != '' AND i.barcode = bm.barcode ORDER BY i.id_bahan_kain ASC");
        while ($row_sisa = mysqli_fetch_assoc($select_sisa)) {
            $id_bahan_kain = $row_sisa['id_bahan_kain'];
            $id_wo = $row_sisa['id_wo'];
            $tgl_masuk = $row_sisa['tgl_awal'];
            $sisa_bahan = $row_sisa['qty_sp'];
            $barcode = rand(1000000, 9999999);
            $id_transaksi = $row_sisa['id_transaksi'];
            $tgl_transaksi = $row_sisa['tgl_transaksi'];
            $lot = $row_sisa['lot'];
            $sj_greige = $row_sisa['no_sj_greige'];
            $sj_celup = $row_sisa['no_sj_celup'];
            $ph_bahan = $row_sisa['no_ph_bahan'];
            $celup = $row_sisa['celup_by'];
            $greige = $row_sisa['greige_by'];
            $status = $row_sisa['status'];

            if ($sisa_bahan > 0) {
                $select_update_sisa = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND id_transaksi = '" . $id_transaksi . "' AND keterangan LIKE '%Bahan Sisa%'");
                while ($row_update = mysqli_fetch_array($select_update_sisa)) {
                    // $barcode = $row_update['barcode'];
                    $keterangan = $row_update['keterangan'];
                }

                // echo $keterangan . '<br>';
                $bahan_sisa = "Bahan Sisa - " . $id_wo . "";
                // echo $bahan_sisa . '<br>';
                if ($keterangan != $bahan_sisa) {
                    $no_id = id_gen_barang_masuk();
                    // $id_transaksi = generate_barang_masuk_key("IN", "IN", date("m"), date("y"));
                    $tgl_masuk = date('Y-m-d');
                    $sql_sisa = "INSERT INTO tb_barang_masuk(
                            no_id,
                            id_transaksi,
                            tgl_transaksi,
                            id_bahan_kain,
                            no_sj_greige,
                            no_sj_celup,
                            greige_by,
                            celup_by,
                            no_ph_bahan,
                            status,
                            lot,
                            bruto,
                            netto,
                            barcode,
                            keterangan,
                            dibuat_oleh,
                            dibuat_tgl  
                        )VALUES(
                            '" . $no_id . "',
                            '" . $id_transaksi . "',
                            '" . $tgl_transaksi . "',
                            '" . $id_bahan_kain . "',
                            '" . $sj_greige . "',
                            '" . $sj_celup . "',
                            '" . $greige . "',
                            '" . $celup . "',
                            '" . $ph_bahan . "',
                            '" . $status . "',
                            '" . $lot . "',
                            '" . $sisa_bahan . "',
                            '" . $sisa_bahan . "',
                            '" . $barcode . "',
                            'Bahan Sisa - " . $id_wo . "',
                            '" . $_SESSION['id_user'] . "',
                            '" . $tgl_masuk . "'
                        )";

                    // echo "<br>$sql_sisa <br>";
                    mysqli_query($conn,$sql_sisa);
                }
            }
        }
        // echo 
        // return 0;
        // if($row_sp[])
        $jum_wo = mysqli_num_rows($select_wo);
        if ($jum_wo = 0) {
            $insert_wo = "INSERT INTO tb_work_order(
                        id_transaksi,
                        qty,
                        b_body,
                        b_tangan,
                        bs,
                        qty_sp
                        ) VALUES (
                            '" . $id_transaksi . "',
                            '" . $qty . "',
                            '" . $b_tangan . "',
                            '" . $bs . "',
                            '" . $qty_sp . "';
                            )
                        ";
            $query_wo = mysqli_query($conn, $insert_wo);
        }
    }
    if ($query_update) {
        $msg = "Ubah data berhasil";
    } else {
        $msg = "Ubah data gagal";
    }

    if ($jum_cut = 0) {
        $insert = "INSERT INTO tb_cutting(
                id_transaksi,
                nama_gelar,
                jam_gelar,
                shift_gelar,
                nama_potong,
                jam_potong,
                shift_potong
            ) VALUES(
                '" . $id_transaksi . "',
                '" . $nama_gelar . "',
                '" . $jam_gelar . "',
                '" . $shift_gelar . "',
                '" . $nama_potong . "',
                '" . $jam_potong . "',
                '" . $shift_potong . "'
                )";
        $query_insert = mysqli_query($conn, $insert);
        if ($query_insert) {
            $msg = "Ubah data berhasil";
        } else {
            $msg = "Ubah data gagal";
        }
    }
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    // return 0;
    // return 0;
    echo "<script type='text/javascript'>alert('" . $msg . "');window.location='master_cutting_edit.php?id_transaksi=$id_transaksi';</script>";
    // header("location:master_cutting_edit.php?id_transaksi=" . $id_transaksi);
}

if (isset($_POST['simpan_oto'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    // $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    // $ex_nm_supplier = explode(" | ", $nama_supplier);
    // $id_supplier = $ex_nm_supplier[0];
    $nama_gelar = mysqli_real_escape_string($conn, $_POST['nama_gelar']);
    $jam_gelar = mysqli_real_escape_string($conn, $_POST['jam_gelar']);
    $shift_gelar = mysqli_real_escape_string($conn, $_POST['shift_gelar']);
    $nama_potong = mysqli_real_escape_string($conn, $_POST['nama_potong']);
    $jam_potong = mysqli_real_escape_string($conn, $_POST['jam_potong']);
    $shift_potong = mysqli_real_escape_string($conn, $_POST['shift_potong']);
    $efisiensi = mysqli_real_escape_string($conn, $_POST['efisiensi_cutting']);

    $update = "UPDATE tb_cutting SET
        nama_gelar = '" . $nama_gelar . "',
        jam_gelar = '" . $jam_gelar . "',
        shift_gelar = '" . $shift_gelar . "',
        nama_potong = '" . $nama_potong . "',
        jam_potong = '" . $jam_potong . "',
        shift_potong = '" . $shift_potong . "'
        WHERE id_transaksi = '" . $id_transaksi . "'
    ";

    $update2 = "UPDATE tb_work_order SET
        sts_cutting = 's',
        efisiensi_cutting = '".$efisiensi."'
        WHERE id_transaksi = '" . $id_transaksi . "'
    ";

    // echo $update2.'<br>';
    // return 0;

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
                            <h4 class="hk-sec-title" style="margin:1rem;">Menu Cutting</h4>

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
                                                    <th class="text-center">ITEM</th>
                                                    <th class="text-center">Bruto</th>
                                                    <th class="text-center">Netto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';

                                    $no_id = "";
                                    $id_bahan_kain = "";
                                    $ksl_qty_kain = 0;
                                    $ksl_qty_netto = 0;

                                    // echo "SELECT a.* FROM tb_inspek a LEFT JOIN tb_bahan_kain b WHERE a.id_transaksi = '" . $id_transaksi . "' AND a.id_bahan_kain != '' AND (b.nama_kain NOT LIKE '%RIB%' AND b.nama_kain NOT LIKE '%MANSET%' AND b.nama_kain NOT LIKE '%KERAH%') ORDER BY a.barcode ASC<br>";
                                    // $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain != '' ORDER BY barcode ASC");
                                    $select_barang_masuk = mysqli_query($conn, "SELECT a.*,b.nama_kain FROM tb_inspek a LEFT JOIN tb_bahan_kain b ON a.id_bahan_kain = b.id_bahan_kain WHERE a.id_transaksi = '" . $id_transaksi . "' AND a.id_bahan_kain != '' AND (b.nama_kain NOT LIKE '%RIB%' AND b.nama_kain NOT LIKE '%MANSET%' AND b.nama_kain NOT LIKE '%KERAH%') ORDER BY a.barcode ASC");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                        $no_id = $row['no_id'];
                                        $id_bahan_kain = $row['id_bahan_kain'];
                                        $nama_kain = $row['nama_kain'];

                                        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND barcode = '" . $row['barcode'] . "' ORDER BY lot ASC"));
                                        $lot = $q_kain['lot'];
                                        $bruto = $q_kain['bruto'];
                                        $netto = $q_kain['netto'];
                                        //      echo "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND barcode = '" . $row['barcode'] . "' ORDER BY lot ASC";
                                        echo '
                                                    <tr >
                                                        <td class="text-center">' . $lot . '  ' . $row['barcode'] . '</td>
                                                        <td class="text-center">' . $bruto . '</td>
                                                        <td class="text-center">' . $netto . '</td>
                                                        </tr>
                                                ';
                                        // <td class="text-center" >
                                        // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                        // </td>
                                        $ksl_qty_kain += $bruto;
                                        $ksl_qty_netto += $netto;
                                    }
                                    // <td class="text-center" style="color:red"></td>
                                    echo '
                                                <tr >
                                                    <td class="text-center" style="color:red">Total</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_kain . '</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_netto . '</td>
                                                    
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
                                <div class="table-responsive daftar_bahan">
                                    <?php
                                    echo '
                                    <h6>BAHAN RIB   </h6>
                                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ITEM</th>
                                                    <th class="text-center">Bruto</th>
                                                    <th class="text-center">Netto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';

                                    $no_id = "";
                                    $id_bahan_kain = "";
                                    $ksl_qty_kain = 0;
                                    $ksl_qty_netto = 0;

                                    // echo "SELECT a.* FROM tb_inspek a LEFT JOIN tb_bahan_kain b WHERE a.id_transaksi = '" . $id_transaksi . "' AND a.id_bahan_kain != '' AND (b.nama_kain NOT LIKE '%RIB%' AND b.nama_kain NOT LIKE '%MANSET%' AND b.nama_kain NOT LIKE '%KERAH%') ORDER BY a.barcode ASC<br>";
                                    // $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain != '' ORDER BY barcode ASC");
                                    $select_barang_masuk = mysqli_query($conn, "SELECT a.*,b.nama_kain FROM tb_inspek a LEFT JOIN tb_bahan_kain b ON a.id_bahan_kain = b.id_bahan_kain WHERE a.id_transaksi = '" . $id_transaksi . "' AND a.id_bahan_kain != '' AND b.nama_kain LIKE '%RIB%' ORDER BY a.barcode ASC");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                        $no_id = $row['no_id'];
                                        $id_bahan_kain = $row['id_bahan_kain'];
                                        $nama_kain = $row['nama_kain'];

                                        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND barcode = '" . $row['barcode'] . "' ORDER BY lot ASC"));
                                        $lot = $q_kain['lot'];
                                        $bruto = $q_kain['bruto'];
                                        $netto = $q_kain['netto'];
                                        //      echo "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND barcode = '" . $row['barcode'] . "' ORDER BY lot ASC";
                                        echo '
                                                    <tr >
                                                        <td class="text-center">' . $lot . '  ' . $row['barcode'] . '</td>
                                                        <td class="text-center">' . $bruto . '</td>
                                                        <td class="text-center">' . $netto . '</td>
                                                        </tr>
                                                ';
                                        // <td class="text-center" >
                                        // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                        // </td>
                                        $ksl_qty_kain += $bruto;
                                        $ksl_qty_netto += $netto;
                                    }
                                    // <td class="text-center" style="color:red"></td>
                                    echo '
                                                <tr >
                                                    <td class="text-center" style="color:red">Total</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_kain . '</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_netto . '</td>
                                                    
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
                                <div class="table-responsive daftar_bahan">
                                    <?php
                                    echo '
                                    <h6>BAHAN KERAH</h6>
                                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ITEM</th>
                                                    <th class="text-center">Bruto</th>
                                                    <th class="text-center">Netto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';

                                    $no_id = "";
                                    $id_bahan_kain = "";
                                    $ksl_qty_kain = 0;
                                    $ksl_qty_netto = 0;

                                    // echo "SELECT a.* FROM tb_inspek a LEFT JOIN tb_bahan_kain b WHERE a.id_transaksi = '" . $id_transaksi . "' AND a.id_bahan_kain != '' AND (b.nama_kain NOT LIKE '%RIB%' AND b.nama_kain NOT LIKE '%MANSET%' AND b.nama_kain NOT LIKE '%KERAH%') ORDER BY a.barcode ASC<br>";
                                    // $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain != '' ORDER BY barcode ASC");
                                    $select_barang_masuk = mysqli_query($conn, "SELECT a.*,b.nama_kain FROM tb_inspek a LEFT JOIN tb_bahan_kain b ON a.id_bahan_kain = b.id_bahan_kain WHERE a.id_transaksi = '" . $id_transaksi . "' AND a.id_bahan_kain != '' AND b.nama_kain LIKE '%KERAH%' ORDER BY a.barcode ASC");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                        $no_id = $row['no_id'];
                                        $id_bahan_kain = $row['id_bahan_kain'];
                                        $nama_kain = $row['nama_kain'];

                                        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND barcode = '" . $row['barcode'] . "' ORDER BY lot ASC"));
                                        $lot = $q_kain['lot'];
                                        $bruto = $q_kain['bruto'];
                                        $netto = $q_kain['netto'];
                                        //      echo "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND barcode = '" . $row['barcode'] . "' ORDER BY lot ASC";
                                        echo '
                                                    <tr >
                                                        <td class="text-center">' . $lot . '  ' . $row['barcode'] . '</td>
                                                        <td class="text-center">' . $bruto . '</td>
                                                        <td class="text-center">' . $netto . '</td>
                                                        </tr>
                                                ';
                                        // <td class="text-center" >
                                        // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                        // </td>
                                        $ksl_qty_kain += $bruto;
                                        $ksl_qty_netto += $netto;
                                    }
                                    // <td class="text-center" style="color:red"></td>
                                    echo '
                                                <tr >
                                                    <td class="text-center" style="color:red">Total</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_kain . '</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_netto . '</td>
                                                    
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
                                <div class="table-responsive daftar_bahan">
                                    <?php
                                    echo '
                                    <h6>BAHAN MANSET</h6>
                                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">ITEM</th>
                                                    <th class="text-center">Bruto</th>
                                                    <th class="text-center">Netto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';

                                    $no_id = "";
                                    $id_bahan_kain = "";
                                    $ksl_qty_kain = 0;
                                    $ksl_qty_netto = 0;

                                    // echo "SELECT a.* FROM tb_inspek a LEFT JOIN tb_bahan_kain b WHERE a.id_transaksi = '" . $id_transaksi . "' AND a.id_bahan_kain != '' AND (b.nama_kain NOT LIKE '%RIB%' AND b.nama_kain NOT LIKE '%MANSET%' AND b.nama_kain NOT LIKE '%KERAH%') ORDER BY a.barcode ASC<br>";
                                    // $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain != '' ORDER BY barcode ASC");
                                    $select_barang_masuk = mysqli_query($conn, "SELECT a.*,b.nama_kain FROM tb_inspek a LEFT JOIN tb_bahan_kain b ON a.id_bahan_kain = b.id_bahan_kain WHERE a.id_transaksi = '" . $id_transaksi . "' AND a.id_bahan_kain != '' AND b.nama_kain LIKE '%MANSET%' ORDER BY a.barcode ASC");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                        $no_id = $row['no_id'];
                                        $id_bahan_kain = $row['id_bahan_kain'];
                                        $nama_kain = $row['nama_kain'];

                                        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND barcode = '" . $row['barcode'] . "' ORDER BY lot ASC"));
                                        $lot = $q_kain['lot'];
                                        $bruto = $q_kain['bruto'];
                                        $netto = $q_kain['netto'];
                                        //      echo "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_bahan_kain . "' AND barcode = '" . $row['barcode'] . "' ORDER BY lot ASC";
                                        echo '
                                                    <tr >
                                                        <td class="text-center">' . $lot . '  ' . $row['barcode'] . '</td>
                                                        <td class="text-center">' . $bruto . '</td>
                                                        <td class="text-center">' . $netto . '</td>
                                                        </tr>
                                                ';
                                        // <td class="text-center" >
                                        // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                        // </td>
                                        $ksl_qty_kain += $bruto;
                                        $ksl_qty_netto += $netto;
                                    }
                                    // <td class="text-center" style="color:red"></td>
                                    echo '
                                                <tr >
                                                    <td class="text-center" style="color:red">Total</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_kain . '</td>
                                                    <td class="text-center" style="color:red">' . $ksl_qty_netto . '</td>
                                                    
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
                                    <!-- <a onclick="tambahNilaiqty('tes')" >tomboltes</a> -->
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
                                                    <th class="text-center">BOM</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            ';
                                    $no_id = "";
                                    $id_sku = "";
                                    $ksl_qty_sku = 0;
                                    $ksl_qty = 0;
                                    $ksl_qty_bb = 0;
                                    $ksl_qty_bt = 0;
                                    $ksl_qty_bs = 0;
                                    $ksl_bom = 0;
                                    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku != ''");
                                    while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
                                        $no_id = $row['no_id'];
                                        $id_sku = $row['id_sku'];
                                        $qty_sku = $row['qty_sku'];
                                        $rasio_real = $row['rasio_real'];
                                        $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                        // $bom = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(qty_kain) qty_bom FROM tb_bom WHERE kode_bom LIKE '%".trim($q_sku['kode_sku'])."%'"));
                                        $bom = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(qty_kain) qty_bom FROM tb_bom WHERE kode_bom LIKE '%".trim($q_sku['kode_sku'])."%'  AND (jenis_kain NOT LIKE '%KERAH%' AND jenis_kain NOT LIKE '%MANSET%' AND jenis_kain NOT LIKE '%RIB%')"));
                                        // echo $q_sku['kode_sku'].' - SELECT SUM(qty_kain) qty_bom FROM tb_bom WHERE kode_bom LIKE %'.$q_sku['kode_sku'].'% <br>';
                                        // echo $q_sku['kode_sku'].' - SELECT SUM(qty_kain) qty_bom FROM tb_bom WHERE kode_bom LIKE %'.$q_sku['kode_sku'].'% AND ( jenis_kain NOT LIKE "%KERAH%" OR jenis_kain NOT LIKE "%MANSET%" OR jenis_kain NOT LIKE "%RIB%" ) <br>';
                                        // echo '<br>'.$bom['qty_bom'].'<br>';

                                        echo '
                                                    <tr >
                                                        <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                        <td class="text-center" >' . $rasio_real . '</td>
                                                        <td class="text-center">
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="qty_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang qty_k_' . $no_id . '" onclick="kurangiNilaiqty(\'' . $no_id . '\')" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                                            <input type="number" style="display:inline;width:70px" name="qty_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak qty_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row['qty'] . '" ' . $readonly . '>
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="qty_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah qty_t_' . $no_id . '" onclick="tambahNilaiqty(\'' . $no_id . '\')" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center">
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="b_body_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang b_body_k_' . $no_id . '" onclick="kurangiNilaiBody_(\'' . $no_id . '\')" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                                            <input type="number" style="display:inline;width:70px" name="b_body_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak b_body_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row['b_body'] . '" ' . $readonly . ' >
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="b_body_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah b_body_t_' . $no_id . '" onclick="tambahNilaiBody_(\'' . $no_id . '\')" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center">
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="b_tangan_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang b_tangan_k_' . $no_id . '" onclick="kurangiNilaib_Tangan_k_(\'' . $no_id . '\')" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                                            <input type="number" style="display:inline;width:70px" name="b_tangan_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak b_tangan_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row['b_tangan'] . '" ' . $readonly . ' >
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="b_tangan_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah b_tangan_t_' . $no_id . '" onclick="tambahNilaib_Tangan_k_(\'' . $no_id . '\')" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center">
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" onclick="kurangiNilaiBs_k_(\'' . $no_id . '\')" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                                            <input type="number" style="display:inline;width:70px" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row['bs'] . '" ' . $readonly . ' >
                                                            <button href="javascript:void(0);" ' . $hide . ' type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" onclick="tambahNilaiBs_k_(\'' . $no_id . '\')" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                                                        </td>
                                                        <td class="text-center">
                                                            <input type="number" style="display:inline;width:80px" name="bom_' . $no_id . '" class="form-control form-control-sm bom_' . $no_id . '" data-no_id="' . $no_id . '" value="' . round($bom['qty_bom'] * $row['qty'],2) . '" readonly>
                                                        </td>
                                                    </tr>
                                                ';
                                                // echo $bom['qty_bom'].' - '.$row['qty'].'<br>';
                                        // <td class="text-center" >
                                        //     <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                        // </td>
                                        $ksl_qty_sku += $qty_sku;
                                        $ksl_qty += $row['qty'];
                                        $ksl_qty_bb += $row['b_body'];
                                        $ksl_qty_bt += $row['b_tangan'];
                                        $ksl_qty_bs += $row['bs'];
                                        $ksl_bom += $bom['qty_bom'] * $row['qty'];
                                    }
                                    echo '
                                        <tr >
                                            <td class="text-center" style="color:red">Total</td>
                                            <td class="text-center" style="color:red">' . $ksl_qty_sku . '</td>
                                            <td class="text-center" style="color:red">' . $ksl_qty . '</td>
                                            <td class="text-center" style="color:red">' . $ksl_qty_bb . '</td>
                                            <td class="text-center" style="color:red">' . $ksl_qty_bt . '</td>
                                            <td class="text-center" style="color:red">' . $ksl_qty_bs . '</td>
                                            <td class="text-center" style="color:red">' . number_format($ksl_bom,2) . '</td>
                                        </tr>
                                    ';
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

                                                        $efisiensi_bom = 0;
                                                        if($total_bom != 0 && $bahan_pakai != 0){
                                                            $efisiensi_bom = $total_bom  / $bahan_pakai;
                                                        }
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
                                                        <input type="text" name="efisiensi_cutting" id="" class="form-control form-control-sm efisiensi" value="<?= $persen_efisien; ?>%" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-sm">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="row no-gutter">
                                                    <div class="col-12">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span style="width:150px;font-weight: 600;color:#273238" id="inputGroup-sizing-sm">INFORMASI MARKER</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">EFISIENSI</span>
                                                            </div>
                                                            <input type="text" name="efisiensi" id="" class="form-control form-control-sm efisiensi" value="<?= $efisiensi; ?>%" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PANJANG MARKER</span>
                                                            </div>
                                                            <input type="text" name="pjg_marker" id="" class="form-control form-control-sm pjg_marker" value="<?= $pjg_marker; ?>" readonly>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">KETERANGAN</span>
                                                            </div>
                                                            <textarea name="keterangan" id="" style="height:33px;" class="form-control form-control-sm keterangan " autocomplete="off" onclick="this.select();" readonly><?= $keterangan ?></textarea>
                                                            <!-- <input type="text" name="keterangan" id="" class="form-control form-control-sm keterangan" value="<?= $keterangan; ?>" <?= $readonly; ?>> -->
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
                                    <br>
                                    <br>
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
                                                            <th class="text-center">NAMA ITEM</th>
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

                                                $q_material = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_material WHERE id_bahan_material = '" . $row['id_bahan_material'] . "'"));
                                                echo '
                                                            <tr >
                                                                <td class="text-center">' . $no++ . '</td>
                                                                <td class="text-center">' . $q_material['id_bahan_material'] . ' - ' . $q_material['nama_item'] . '</td>
                                                                <td class="text-center">' . $row['qty_material'] . '</td>
                                                                <td class="text-center">' . $row['berat'] . ' Kg</td>
                                                                <td class="text-center">' . $row['oleh'] . '</td>
                                                                <td class="text-center" >
                                                                    <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
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
                                                            <td class="text-center" style="color:red"></td>
                                                            <td class="text-center" style="color:red"></td>
                                                            
                                                        </tr>
                                                        <tr ' . $hide . '>
                                                        <td class="text-center"></td>
                                                            <td class="text-center">
                                                                <input type="text" name="nama_material_' . $no_id . '" id="" class="form-control form-control-sm nama_material nama_material_' . $no_id . '" autocomplete="off" list="list_barang" onclick="this.select()" placeholder="Pilih Material" >
                                                                <datalist id="list_barang" class="list_barang">
                                                                </datalist>
                                                            </td>
                                                            <td class="text-center"><input type="number" name="qty_material_' . $no_id . '" class="form-control form-control-sm qty_material_' . $no_id . '" ' . $readonly . '  ></td>
                                                            <td class="text-center"><input type="number" name="berat_' . $no_id . '" class="form-control form-control-sm berat_' . $no_id . '" ' . $readonly . '></td>
                                                            <td class="text-center"><input type="text" name="oleh_' . $no_id . '" class="form-control form-control-sm oleh_' . $no_id . '" ' . $readonly . ' ></td>
                                                            <td class="text-center">
                                                                <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-success add_list_3 add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_material="' . $id_bahan_material . '"><i class="fa fa-plus"></i></a>
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
                                    </div>
                                    <div class="col-6" style="margin-top:20px">
                                        <div class="table-responsive daftar_bahan_4">
                                            <?php
                                            echo '
                                            <h6>SISA POTONG</h6>
                                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:70%" class="text-center">BAHAN KAIN</th>
                                                            <th class="text-center">QTY</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    ';
                                            $no_id = "";
                                            $id_sku = "";
                                            $total_qty_sp = 0;
                                            $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain != '' ");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
                                                $no_id = $row['no_id'];
                                                $id_bahan_kain = $row['id_bahan_kain'];
                                                $qty_sp = $row['qty_sp'];
                                                $q_kain_sisa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
                                                echo '
                                                            <tr >
                                                                <td class="text-center" >' . $q_kain_sisa['nama_kain'] . ' - ' . $q_kain_sisa['warna'] . ' - ' . $q_kain_sisa['setting'] . ' - ' . $q_kain_sisa['gramasi'] . '</td>
                                                                <td class="text-center">
                                                                    <button href="javascript:void(0);" ' . $hide . ' type="button" name="qty_sp_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_sp_kurang qty_sp_k_' . $no_id . '" onclick="kurangiNilaiQty_sp(\'' . $no_id . '\')" data-no_id="' . $no_id . '" ><i class="fa fa-minus"></i></button>
                                                                    <input type="number" step="0.01" name="qty_sp_' . $no_id . '" style="display:inline;width:65px" class="form-control form-control-sm ubah_data_qty_sp qty_sp_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $qty_sp . '" ' . $readonly . ' >
                                                                    <button href="javascript:void(0);" ' . $hide . ' type="button" name="qty_sp_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_sp_tambah qty_sp_t_' . $no_id . '" onclick="tambahNilaiQty_sp(\'' . $no_id . '\')" data-no_id="' . $no_id . '" ><i class="fa fa-plus"></i></button>
                                                                </td>
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
                                    <!-- <div class="col-6" style="margin-top:20px">
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
                                            $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku LIKE '%SKU%'");
                                            while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
                                                $no_id = $row['no_id'];
                                                $id_sku = $row['id_sku'];
                                                $qty_bsp = $row['qty_bsp'];
                                                $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                                                echo '
                                                            <tr >
                                                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                                                <td class="text-center"><input type="number" name="qty_bsp_' . $no_id . '" style="display:inline;width:65px" class="form-control form-control-sm ubah_data_qty_bsp qty_bsp_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $qty_bsp . '" ' . $readonly . ' ></td>
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
                                    </div> -->
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
    //     document.addEventListener("DOMContentLoaded", function () {
    //     // Temukan tombol "Simpan" dalam formulir
    //     var submitButton = document.querySelector('button[name="simpan"]');

    //     // Tangani klik pada tombol "Simpan"
    //     submitButton.addEventListener("click", function (event) {
    //         // Cari tabel "RESULT MATERIAL" berdasarkan ID
    //         var resultMaterialTable = document.querySelector(".tb_jps_ins");

    //         // Periksa apakah tabel "RESULT MATERIAL" memiliki baris data
    //         if (resultMaterialTable && resultMaterialTable.rows.length <= 2) {
    //             // Tabel "RESULT MATERIAL" kosong, tampilkan pesan kesalahan
    //             alert("Tabel RESULT MATERIAL harus diisi!");
    //             event.preventDefault(); // Hentikan pengiriman formulir
    //         }
    //     });
    // });

    function kurangiNilaiqty(no_id) {
        var input = document.querySelector('input[name="qty_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            if (value > 0) {
                value -= 1;
                input.value = value;
            }
        }
    }

    function tambahNilaiqty(no_id) {
        var input = document.querySelector('input[name="qty_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            value += 1;
            input.value = value;
        }
    }

    function kurangiNilaiBody_(no_id) {
        var input = document.querySelector('input[name="b_body_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            if (value > 0) {
                value -= 1;
                input.value = value;
            }
        }
    }

    function tambahNilaiBody_(no_id) {
        var input = document.querySelector('input[name="b_body_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            value += 1;
            input.value = value;
        }
    }

    function kurangiNilaib_Tangan_k_(no_id) {
        var input = document.querySelector('input[name="b_tangan_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            if (value > 0) {
                value -= 1;
                input.value = value;
            }
        }
    }

    function tambahNilaib_Tangan_k_(no_id) {
        var input = document.querySelector('input[name="b_tangan_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            value += 1;
            input.value = value;
        }
    }

    function kurangiNilaiBs_k_(no_id) {
        var input = document.querySelector('input[name="bs_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            if (value > 0) {
                value -= 1;
                input.value = value;
            }
        }
    }

    function tambahNilaiBs_k_(no_id) {
        var input = document.querySelector('input[name="bs_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            value += 1;
            input.value = value;
        }
    }

    function kurangiNilaiQty_sp(no_id) {
        var input = document.querySelector('input[name="qty_sp_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            if (value > 0) {
                value -= 1;
                input.value = value;
            }
        }
    }

    function tambahNilaiQty_sp(no_id) {
        var input = document.querySelector('input[name="qty_sp_' + no_id + '"]');
        if (input.value != "") {
            let value = parseInt(input.value);
            value += 1;
            input.value = value;
        }
    }

    // alert('hello2');
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
            var id_tambah = $(this).data('id');
            // var id_bahan_kain = $(this).data('id_bahan_kain');
            var nama_kain = $(".nama_kain_" + id_tambah).val();
            var qty_kain = $(".qty_kain_" + id_tambah).val();
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
            if (nama_kain != "" && qty_kain != "") {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_work_order.php",
                    data: {
                        "add_list": id_tambah,
                        "id_transaksi": id_transaksi,
                        // "id_bahan_kain": id_bahan_kain,
                        "nama_kain": nama_kain,
                        "qty_kain": qty_kain
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
            } else {
                alert("Mohon Isi Data Yang Lengkap !");
            }
        });

        $(document).on("click", ".del_list_3", function() {
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

        //  $(document).on("change", ".ubah_data_barang_tembak", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var qty = $(".qty_" + no_id).val();
        //     // var uom = $(".uom_" + no_id).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_cutting.php",
        //         data: {
        //             "ubah_data_barang_tembak": no_id,
        //             "id_transaksi": id_transaksi,
        //             "qty": qty
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".daftar_bahan_2").html(result);
        //         }
        //     });
        // });

        // $(document).on("change", ".ubah_data_barang_tembak", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var b_body = $(".b_body_" + no_id).val();
        //     // var uom = $(".uom_" + no_id).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_cutting.php",
        //         data: {
        //             "ubah_data_barang_tembak": no_id,
        //             "id_transaksi": id_transaksi,
        //             "b_body": b_body
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".daftar_bahan_2").html(result);
        //         }
        //     });
        // });

        // $(document).on("change", ".ubah_data_barang_tembak", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var b_tangan = $(".b_tangan_" + no_id).val();
        //     // var uom = $(".uom_" + no_id).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_cutting.php",
        //         data: {
        //             "ubah_data_barang_tembak": no_id,
        //             "id_transaksi": id_transaksi,
        //             "b_tangan": b_tangan
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".daftar_bahan_2").html(result);
        //         }
        //     });
        // });

        // $(document).on("change", ".ubah_data_barang_tembak", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var bs = $(".bs_" + no_id).val();
        //     // var uom = $(".uom_" + no_id).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_cutting.php",
        //         data: {
        //             "ubah_data_barang_tembak": no_id,
        //             "id_transaksi": id_transaksi,
        //             "bs": bs
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".daftar_bahan_2").html(result);
        //         }
        //     });
        // });

        // //Fungsi Kurang Rencana Pengerjaan
        // $(document).on("click", ".ubah_data_barang_kurang", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var qty = $(".qty_k_" + no_id).val();
        //     var b_body = $(".b_body_k_" + no_id).val();
        //     var b_tangan = $(".b_tangan_k_" + no_id).val();
        //     var bs = $(".bs_k_" + no_id).val();
        //     var tes = $(this).data('tes');
        //     // console.log(tes);
        //     if (tes == 1) {
        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_cutting.php",
        //             data: {
        //                 "ubah_data_barang_kurang": no_id,
        //                 "id_transaksi": id_transaksi,
        //                 "qty": qty
        //             },
        //             cache: true,
        //             success: function(result) {
        //                 $(".daftar_bahan_2").html(result);
        //             }
        //         });
        //     }else if(tes == 2){
        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_cutting.php",
        //             data: {
        //                 "ubah_data_barang_kurang": no_id,
        //                 "id_transaksi": id_transaksi,
        //                 "b_body": b_body
        //             },
        //             cache: true,
        //             success: function(result) {
        //                 $(".daftar_bahan_2").html(result);
        //             }
        //         });
        //     }else if(tes == 3){
        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_cutting.php",
        //             data: {
        //                 "ubah_data_barang_kurang": no_id,
        //                 "id_transaksi": id_transaksi,
        //                 "b_tangan": b_tangan
        //             },
        //             cache: true,
        //             success: function(result) {
        //                 $(".daftar_bahan_2").html(result);
        //             }
        //         });
        //     }else if(tes == 4){
        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_cutting.php",
        //             data: {
        //                 "ubah_data_barang_kurang": no_id,
        //                 "id_transaksi": id_transaksi,
        //                 "bs": bs
        //             },
        //             cache: true,
        //             success: function(result) {
        //                 $(".daftar_bahan_2").html(result);
        //             }
        //         });
        //     }

        // });

        // //Fungsi Tambah Rencana Pengerjaan
        // $(document).on("click", ".ubah_data_barang_tambah", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var qty = $(".qty_t_" + no_id).val();
        //     var b_body = $(".b_body_t_" + no_id).val();
        //     var b_tangan = $(".b_tangan_t_" + no_id).val();
        //     var bs = $(".bs_t_" + no_id).val();
        //     var tes = $(this).data('tes2');
        //     // console.log(tes);
        //     if (tes == 1) {
        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_cutting.php",
        //             data: {
        //                 "ubah_data_barang_tambah": no_id,
        //                 "id_transaksi": id_transaksi,
        //                 "qty": qty
        //             },
        //             cache: true,
        //             success: function(result) {
        //                 $(".daftar_bahan_2").html(result);
        //             }
        //         });
        //     }else if(tes == 2){
        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_cutting.php",
        //             data: {
        //                 "ubah_data_barang_tambah": no_id,
        //                 "id_transaksi": id_transaksi,
        //                 "b_body": b_body
        //             },
        //             cache: true,
        //             success: function(result) {
        //                 $(".daftar_bahan_2").html(result);
        //             }
        //         });
        //     }else if(tes == 3){
        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_cutting.php",
        //             data: {
        //                 "ubah_data_barang_tambah": no_id,
        //                 "id_transaksi": id_transaksi,
        //                 "b_tangan": b_tangan
        //             },
        //             cache: true,
        //             success: function(result) {
        //                 $(".daftar_bahan_2").html(result);
        //             }
        //         });
        //     }else if(tes == 4){
        //         $.ajax({
        //             type: "POST",
        //             url: "ajax/ajax_cutting.php",
        //             data: {
        //                 "ubah_data_barang_tambah": no_id,
        //                 "id_transaksi": id_transaksi,
        //                 "bs": bs
        //             },
        //             cache: true,
        //             success: function(result) {
        //                 $(".daftar_bahan_2").html(result);
        //             }
        //         });
        //     }

        // });

        $(document).on("click", ".add_list_3", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var id_tambah = $(this).data('id');
            // var id_bahan_kain = $(this).data('id_bahan_kain');
            var nama_material = $(".nama_material_" + id_tambah).val();
            var qty_material = $(".qty_material_" + id_tambah).val();
            var berat = $(".berat_" + id_tambah).val();
            var oleh = $(".oleh_" + id_tambah).val();
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
            if (nama_material != "" && qty_material != "" && berat != "" && oleh != "") {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_cutting.php",
                    data: {
                        "add_list_3": id_tambah,
                        "id_transaksi": id_transaksi,
                        "nama_material": nama_material,
                        "qty_material": qty_material,
                        "berat": berat,
                        "oleh": oleh
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
                        $(".daftar_bahan_3").html(result);
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
                url: "ajax/ajax_cutting.php",
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
                    $(".daftar_bahan_3").html(response);
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

        // $(document).on("change", ".ubah_data_qty_sp", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var qty_sp = $(".qty_sp_" + no_id).val();
        //     // var uom = $(".uom_" + no_id).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_cutting.php",
        //         data: {
        //             "ubah_data_qty_sp": no_id,
        //             "id_transaksi": id_transaksi,
        //             "qty_sp": qty_sp
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".daftar_bahan_4").html(result);
        //         }
        //     });
        // });

        //Fungsi Kurang Sisa Potong
        // $(document).on("click", ".ubah_data_qty_sp_kurang", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var qty_sp = $(".qty_sp_k_" + no_id).val();
        //     // var uom = $(".uom_" + no_id).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_cutting.php",
        //         data: {
        //             "ubah_data_qty_sp_kurang": no_id,
        //             "id_transaksi": id_transaksi,
        //             "qty_sp": qty_sp
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".daftar_bahan_4").html(result);
        //         }
        //     });

        // });

        // //Fungsi Tambah Sisa Potong
        // $(document).on("click", ".ubah_data_qty_sp_tambah", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var qty_sp = $(".qty_sp_t_" + no_id).val();
        //     // var uom = $(".uom_" + no_id).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_cutting.php",
        //         data: {
        //             "ubah_data_qty_sp_tambah": no_id,
        //             "id_transaksi": id_transaksi,
        //             "qty_sp": qty_sp
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".daftar_bahan_4").html(result);
        //         }
        //     });

        // });

        // $(document).on("change", ".ubah_data_qty_bsp", function() {
        //     var no_id = $(this).data('no_id');
        //     var id_transaksi = "<?= $id_transaksi; ?>";
        //     var qty_bsp = $(".qty_bsp_" + no_id).val();
        //     // var uom = $(".uom_" + no_id).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_cutting.php",
        //         data: {
        //             "ubah_data_qty_bsp": no_id,
        //             "id_transaksi": id_transaksi,
        //             "qty_bsp": qty_bsp
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".daftar_bahan_5").html(result);
        //         }
        //     });
        // });



    });
</script>