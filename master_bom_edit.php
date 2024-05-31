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

$no_bom = "";
$kode_bom = "";
$nama_bom = "";
$keterangan = "";
$kode_barang[0] = "";
$kode_barang[1] = "";
$uom = "";
$nama_barang = "";

if (isset($_GET['id_transaksi'])) {
    if ($_GET['id_transaksi'] !== "new") {
        $select_barang_masuk = "SELECT * FROM tb_bom WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        $data_barang_masuk = mysqli_fetch_array($query_barang_masuk);

        // $select_customer = "SELECT * FROM tb_customer WHERE id_customer = '" . $data_barang_masuk['id_supplier'] . "'";
        // $query_customer = mysqli_query($conn, $select_customer);
        // $data_customer = mysqli_fetch_array($query_customer);

        // $nama_supplier = $data_customer['id_customer'] . " | " . $data_customer['nama_customer'];
        $no_bom = $data_barang_masuk['id_transaksi'];
        $kode_bom = $data_barang_masuk['kode_bom'];
        $nama_bom = $data_barang_masuk['nama_bom'];
        $keterangan = $data_barang_masuk['keterangan'];
        $kode_barang = $data_barang_masuk['id_sku'];
        $kode_barang = explode(" | ", $kode_barang);
        $uom = $data_barang_masuk['uom_sku'];
        $nama_barang = $data_barang_masuk['nama_sku'];
        // $jenis_trs = $data_barang_masuk['jenis_transaksi'];
    }
}

if (isset($_POST['simpan'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    $kode_bom = mysqli_real_escape_string($conn, $_POST['kode_bom']);
    $nama_bom = mysqli_real_escape_string($conn, $_POST['nama_bom']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $uom = mysqli_real_escape_string($conn, $_POST['uom']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);



    if ($id_transaksi == "new") {
        $id_transaksi = generate_bom("BOM", "BOM", date("m"), date("y"));

        $update = "UPDATE tb_bom SET
            id_transaksi = '" . $id_transaksi . "',
            kode_bom = '" . $kode_bom . "',
            nama_bom = '" . $nama_bom . "',
            keterangan = '" . $keterangan . "',
            id_sku = '" . $kode_barang . "',
            uom_sku = '" . $uom . "',
            status = 'd',
            nama_sku = '" . $nama_barang . "'
            WHERE id_transaksi = '" . $_SESSION['id_user'] . "'
        ";

        $query = mysqli_query($conn, $update);
    } else {
        $update = "UPDATE tb_bom SET
            kode_bom = '" . $kode_bom . "',
            nama_bom = '" . $nama_bom . "',
            keterangan = '" . $keterangan . "',
            id_sku = '" . $kode_barang . "',
            uom_sku = '" . $uom . "',
            status = 'd',
            nama_sku = '" . $nama_barang . "'
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";

        $query = mysqli_query($conn, $update);
    }

    header("location:master_bom_edit.php?id_transaksi=" . $id_transaksi);
}

if (isset($_POST['simpan_oto'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    $no_bom = mysqli_real_escape_string($conn, $_POST['no_bom']);
    $kode_bom = mysqli_real_escape_string($conn, $_POST['kode_bom']);
    $nama_bom = mysqli_real_escape_string($conn, $_POST['nama_bom']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $uom = mysqli_real_escape_string($conn, $_POST['uom']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);


    $total = 0;
    $g_total = 0;
    if ($id_transaksi == "new") {
        $ulang_stock = 0;
        $ulang_stock_2 = 0;
        $hasil = 0;
        $id_transaksi = generate_bom("BOM", "BOM", date("m"), date("y"));

        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $_SESSION['id_user'] . "'";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        // while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
        //     $query_stock = mysqli_query($conn, "SELECT * FROM tb_stock WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "'");
        //     $data_stock = mysqli_fetch_array($query_stock);
        //     $jum_stock = mysqli_num_rows($query_stock);
        //     $ulang_stock_2 += intval($row_barang_masuk['qty']);
        //     // $g_total = $row_barang_masuk['total_invoice'];
        //     while ($ulang_stock < $ulang_stock_2) {
        //         if ($jum_stock < 1) {
        //             $id_stock = id_gen_stock();
        //             $ppn = $row_barang_masuk['total'] * $row_barang_masuk['ppn'] / 100;
        //             $total = $row_barang_masuk['total'];
        //             $hpp_return = (0 + $total) / (0 + $row_barang_masuk['qty']);

        //             $insert_stock = mysqli_query($conn, "INSERT INTO tb_stock(
        //                 id,
        //                 id_bahan,
        //                 berat,
        //                 stock,
        //                 uom
        //             ) VALUES(
        //                 '" . $id_stock . "',
        //                 '" . $row_barang_masuk['id_product'] . "',
        //                 '" . $row_barang_masuk['berat'] . "',
        //                 '1',
        //                 'YARD'
        //             )");
        //         } else {
        //             $total_stock = $data_stock['stock'] + $row_barang_masuk['qty'];
        //             $update_stock = mysqli_query($conn, "UPDATE tb_stock SET
        //                 stock = '" . $total_stock . "' WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "' AND uom = 'YARD'
        //             ");
        //         }
        //         $ulang_stock++;
        //     }
        // }


        $update = "UPDATE tb_bom SET
            id_transaksi = '" . $id_transaksi . "',
            kode_bom = '" . $kode_bom . "',
            nama_bom = '" . $nama_bom . "',
            keterangan = '" . $keterangan . "',
            id_sku = '" . $kode_barang . "',
            uom_sku = '" . $uom . "',
            status = 's',
            nama_sku = '" . $nama_barang . "'
            WHERE id_transaksi = '" . $_SESSION['id_user'] . "'
        ";
        //  $jurnal_1 = add_jurnal('', '1-1022', '2-2011', $g_total,$_POST['tanggal_transaksi'], "Pembelian Bahan Baku", $_SESSION['id_user']);
        $query = mysqli_query($conn, $update);

        if ($query) {
            $msg_status = "Simpan & Otorisasi berhasil !";
        } else {
            $msg_status = "Simpan & Otorisasi gagal !";
        }

        // $sql_get_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "'");
        // while ($row_barang_masuk = mysqli_fetch_array($sql_get_barang_masuk)) {
        //     $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "'");
        //     $data_barang = mysqli_fetch_array($sql_get_barang);

        //     $total_qty = $data_barang['total_qty'];
        //     $total_berat = $data_barang['total_berat'];

        //     $total_input_qty = $total_qty + $row_barang_masuk['qty'];
        //     $total_input_berat = $total_berat + ($row_barang_masuk['qty'] * $row_barang_masuk['berat']);

        //     $sql_update_summ_barang = mysqli_query($conn, "
        //         UPDATE
        //             tb_bahan
        //         SET
        //             total_qty = '" . $total_input_qty . "',
        //             total_berat = '" . $total_input_berat . "'
        //         WHERE
        //             id_bahan = '" . $row_barang_masuk['id_product'] . "'
        //     ");
        // }

        echo '
            <script>
                alert("' . $msg_status . '");
                window.location.href="master_bom.php";
            </script>
        ';
    } else {
        $hasil = 0;

        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "'";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        // while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
        //     $select_stock = "SELECT * FROM tb_stock WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "'";
        //     $query_stock = mysqli_query($conn, $select_stock);
        //     $data_stock = mysqli_fetch_array($query_stock);
        //     $jum_stock = mysqli_num_rows($query_stock);
        //     // $g_total = $row_barang_masuk['total_invoice'];

        //     if ($jum_stock < 1) {
        //         $id_stock = id_gen_stock();
        //         $insert_stock = mysqli_query($conn, "INSERT INTO tb_stock(
        //             id,
        //             id_bahan,
        //             berat,
        //             stock,
        //             uom
        //         ) VALUES(
        //             '" . $id_stock . "',
        //             '" . $row_barang_masuk['id_product'] . "',
        //             '" . $row_barang_masuk['berat'] . "',
        //             '" . $row_barang_masuk['qty'] . "',
        //             'YARD'
        //         )");
        //     } else {
        //         $total_stock = $data_stock['stock'] + $row_barang_masuk['qty'];

        //         $update_stock = mysqli_query($conn, "UPDATE tb_stock SET
        //             stock = '" . $total_stock . "'WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "'
        //         ");
        //     }
        // }

        $update = "UPDATE tb_bom SET
            kode_bom = '" . $kode_bom . "',
            nama_bom = '" . $nama_bom . "',
            keterangan = '" . $keterangan . "',
            id_sku = '" . $kode_barang . "',
            uom_sku = '" . $uom . "',
            status = 's',
            nama_sku = '" . $nama_barang . "'
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";

        $query = mysqli_query($conn, $update);

        // $sql_get_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "'");
        // while ($row_barang_masuk = mysqli_fetch_array($sql_get_barang_masuk)) {
        //     $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "'");
        //     $data_barang = mysqli_fetch_array($sql_get_barang);

        //     $total_qty = $data_barang['total_qty'];
        //     $total_berat = $data_barang['total_berat'];

        //     $total_input_qty = $total_qty + $row_barang_masuk['qty'];
        //     $total_input_berat = $total_berat + ($row_barang_masuk['qty'] * $row_barang_masuk['berat']);

        //     $sql_update_summ_barang = mysqli_query($conn, "
        //         UPDATE
        //             tb_bahan
        //         SET
        //             total_qty = '" . $total_input_qty . "',
        //             total_berat = '" . $total_input_berat . "'
        //         WHERE
        //             id_bahan = '" . $row_barang_masuk['id_product'] . "'
        //     ");
        // }
        // $jurnal_1 = add_jurnal('', '1-1022', '2-2011', $g_total,$_POST['tanggal_transaksi'], "Pembelian Bahan Baku", $_SESSION['id_user']);

        if ($query) {
            $msg_status = "Simpan & Otorisasi berhasil !";
        } else {
            $msg_status = "Simpan & Otorisasi gagal !";
        }
        echo '
            <script>
                alert("' . $msg_status . '");
                window.location.href="master_bom.php";
            </script>
        ';
    }
}

$hide = "";
$readonly = "";
$filled_input = "";
if (isset($_GET['detail'])) {
    $hide = "style='display:none;'";
    $readonly = "readonly";
    $filled_input = "filled-input";
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
                            <h4 class="hk-sec-title" style="margin:1rem;">Master BOM</h4>

                            <div class="col-sm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">NO BOM</span>
                                                    </div>
                                                    <input type="text" name="no_bom" id="" class="form-control filled-input form-control-sm no_bom" value="<?= $no_bom; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama BOM</span>
                                                    </div>
                                                    <input type="text" name="nama_bom" id="" class="form-control form-control-sm" value="<?= $nama_bom; ?>" <?= $readonly; ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Kode BOM</span>
                                                    </div>
                                                    <input type="text" name="kode_bom" id="" class="form-control form-control-sm" value="<?= $kode_bom; ?>" <?= $readonly; ?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Keterangan</span>
                                                    </div>
                                                    <textarea name="keterangan" id="" style="height:33px;" class="form-control form-control-sm" autocomplete="off" <?= $readonly; ?>><?= $keterangan ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row no-gutter">
                                    <div class="col-md-8 mt-15">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Kode Barang</span>
                                            </div>
                                            <input type="text" name="kode_barang" id="" class="form-control form-control-sm <?= $filled_input; ?> kode_barang_sku" style="width:180px;" autocomplete="off" list="list_kode_sku" onclick="this.select()" value="<?= $kode_barang[0] . ' | ' . $kode_barang[1] ?>" <?= $readonly; ?>>
                                            <datalist id="list_kode_sku" class="list_kode_sku">
                                            </datalist>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mt-15">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">UOM</span>
                                            </div>
                                            <input type="text" name="uom" id="" class="form-control form-control-sm <?= $filled_input; ?>" style="width:180px;" value="<?= $uom ?>" <?= $readonly; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-15 d-none">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama Barang</span>
                                            </div>
                                            <input type="text" name="nama_barang" id="" class="form-control form-control-sm <?= $filled_input; ?> nama_barang_sku" style="width:180px;" autocomplete="off" list="list_nama_sku" onclick="this.select()" value="<?= $nama_barang ?>" <?= $readonly; ?>>
                                            <datalist id="list_nama_sku" class="list_nama_sku">
                                            </datalist>
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
                                    $id_transaksi_2 = generate_barang_masuk_key("DBM", "DBM", date("m"), date("y"));

                                    $q_bom = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi = '" . $id_transaksi . "'");
                                    $jum_data = mysqli_num_rows($q_bom);
                                    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_transaksi_2 ");
                                    if ($jum_data < 1) {
                                        echo '
                                        <br>
                                        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Kode Barang</th>
                                                    <th class="text-center">Nama Barang</th>
                                                    <th class="text-center">UOM</th>
                                                    <th class="text-center">QTY</th>  
                                                    <th class="text-center">Harga</th>
                                                    <th class="text-center">Total</th>
                                                    <th class="text-center"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr ' . $hide . '>
                                                    <td colspan="2" class="text-center">
                                                        <input type="text" name="kode_bahan_kainnew" class="form-control form-control-sm kode_bahan_kain kode_bahan_kain_new" autocomplete="off" list="list_kode_bahan_kain" onclick="this.select()" ' . $readonly . '>
                                                        <datalist id="list_kode_bahan_kain" class="list_kode_bahan_kain">
                                                        </datalist>
                                                    </td>
                                                    <td class="text-center d-none"><input type="text" name="jenis_kain" class="form-control form-control-sm jenis_kain_new" ' . $readonly . '></td>
                                                    <td class="text-center"><input type="text" name="uom_kain" class="form-control form-control-sm uom_kain_new" ' . $readonly . '></td>
                                                    <td class="text-center"><input type="number" name="qty_kain" class="form-control form-control-sm qty_kain_new" ' . $readonly . ' step="0.001"></td>
                                                    <td class="text-center"><input type="number" name="harga" class="form-control form-control-sm harga_new" ' . $readonly . '></td>
                                                    <td colspan="2" class="text-center">
                                                        <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-success add_list add_list_new" data-id="new" data-id_transaksi_2="' . $id_transaksi_2 . '"><i class="fa fa-plus"></i></a>
                                                    </td>
                                                </tr>
                                            ';
                                        echo '
                                            </tbody>
                                        </table>
                                        ';
                                    } else {
                                        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                                            $no_id = $row['no_id'];
                                            $kode_bahan_kain = $row['kode_bahan_kain'];

                                            // <p>' . $q_kain['jenis_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</p>
                                            // <p>' . $row['id_transaksi_2'] . '</p>
                                            echo '
                                            <br>
                                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Kode Barang</th>
                                                        <th class="text-center">Nama Barang</th>
                                                        <th class="text-center">UOM</th>
                                                        <th class="text-center">QTY</th>  
                                                        <th class="text-center">Harga</th>
                                                        <th class="text-center">Total</th>
                                                        <th class="text-center"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                ';
                                            $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi_2 = '" . $row['id_transaksi_2'] . "' AND id_transaksi = '" . $id_transaksi . "'");
                                            while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {

                                                $kode_bahan_kain_2 = $row2['kode_bahan_kain'];
                                                $jenis_kain = $row2['jenis_kain'];
                                                $uom_kain = $row2['uom_kain'];
                                                $qty_kain = $row2['qty_kain'];
                                                $harga = $row2['harga'];
                                                $total = $row2['total_harga'];

                                                $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE kode_bahan_kain = '" . $kode_bahan_kain_2 . "'"));
                                                echo '
                                                    <tr>
                                                        <td class="text-center">' . $kode_bahan_kain_2 . '</td>
                                                        <td class="text-center">' . $q_kain['nama_kain'] . '</td>
                                                        <td class="text-center">' . $uom_kain . '</td>
                                                        <td class="text-center">' . $qty_kain . '</td>
                                                        <td class="text-center">' . number_format($harga) . '</td>
                                                        <td class="text-center">' . number_format($total) . '</td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                                                        </td>
                                                    </tr>
                                                ';
                                            }
                                            echo '
                                                    <tr ' . $hide . '>
                                                        <td colspan="2" class="text-center">
                                                            <input type="text" name="kode_bahan_kain' . $no_id . '" class="form-control form-control-sm kode_bahan_kain kode_bahan_kain_' . $no_id . '" autocomplete="off" list="list_kode_bahan_kain" onclick="this.select()" ' . $readonly . '>
                                                            <datalist id="list_kode_bahan_kain" class="list_kode_bahan_kain">
                                                            </datalist>
                                                        </td>
                                                        <td class="text-center d-none"><input type="text" name="jenis_kain_' . $no_id . '" class="form-control form-control-sm jenis_kain_' . $no_id . '" ' . $readonly . '></td>
                                                        <td class="text-center"><input type="text" name="uom_kain_' . $no_id . '" class="form-control form-control-sm uom_kain_' . $no_id . '" ' . $readonly . '></td>
                                                        <td class="text-center"><input type="number" name="qty_kain_' . $no_id . '" class="form-control form-control-sm qty_kain_' . $no_id . '" ' . $readonly . ' step="0.001"></td>
                                                        <td class="text-center"><input type="number" name="harga_' . $no_id . '" class="form-control form-control-sm harga_' . $no_id . '" ' . $readonly . ' step="0.001"></td>
                                                        <td colspan="2" class="text-center">
                                                            <a href="javascript:void(0);" ' . $hide . ' class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_transaksi_2="' . $row['id_transaksi_2'] . '"><i class="fa fa-plus"></i></a>
                                                        </td>
                                                    </tr>
                                                ';
                                            echo '
                                                </tbody>
                                            </table>
                                            ';
                                        }
                                        echo '
                                            <a href="javascript:void(0);" ' . $hide . ' class="btn btn-sm btn-success add_list_2 "data-id_transaksi_2="' . $id_transaksi_2 . '">Save</a>
                                            <br><br>
                                        ';
                                    }
                                    ?>
                                </div>
                                <?php
                                if (isset($_GET['detail'])) {
                                    echo '
                                    <div class="col-sm">
                                        <div class="row">
                                            <a href="master_bom.php" class="btn btn-primary mb-2"><i class="fa fa-arrow-left"></i> Kembali</a>
                                        </div>
                                    </div>
                                    ';
                                } else {
                                    echo '
                                    <div class="col-sm">
                                        <div class="row">
                                            <a href="master_bom.php" class="btn btn-sm btn-danger mb-2"> Kembali</a>
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

        $(document).on("keyup", ".kode_barang_sku", function() {
            var kode_barang_sku = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_bom.php",
                data: {
                    "cari_kode_barang_sku": kode_barang_sku
                },
                cache: true,
                success: function(result) {
                    $(".list_kode_sku").html(result);
                }
            });
        });

        $(document).on("keyup", ".nama_barang_sku", function() {
            var nama_barang_sku = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_bom.php",
                data: {
                    "cari_nama_barang_sku": nama_barang_sku
                },
                cache: true,
                success: function(result) {
                    $(".list_nama_sku").html(result);
                }
            });
        });

        $(document).on("keyup", ".kode_bahan_kain", function() {
            var kode_bahan_kain = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_bom.php",
                data: {
                    "cari_kode_bahan_kain": kode_bahan_kain
                },
                cache: true,
                success: function(result) {
                    $(".list_kode_bahan_kain").html(result);
                }
            });
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

        $(document).on("click", ".add_list", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var id_tambah = $(this).data('id');
            var id_transaksi_2 = $(this).data('id_transaksi_2');
            var kode_bahan_kain = $(".kode_bahan_kain_" + id_tambah).val();
            var jenis_kain = $(".jenis_kain_" + id_tambah).val();
            var uom_kain = $(".uom_kain_" + id_tambah).val();
            var qty_kain = $(".qty_kain_" + id_tambah).val();
            var harga = $(".harga_" + id_tambah).val();
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
            if (kode_bahan_kain != "" && uom_kain != "" && qty_kain != "") {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_bom.php",
                    data: {
                        "add_list": id_tambah,
                        "id_transaksi": id_transaksi,
                        "id_transaksi_2": id_transaksi_2,
                        "kode_bahan_kain": kode_bahan_kain,
                        "jenis_kain": jenis_kain,
                        "uom_kain": uom_kain,
                        "qty_kain": qty_kain,
                        "harga": harga
                    },
                    cache: true,
                    beforeSend: function(response) {
                        $(".preloader-it").show();
                    },
                    success: function(result) {
                        console.log(result);
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
                alert("Mohon pilih barang sesuai database !");
            }
        });

        $(document).on("click", ".add_list_2", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var id_transaksi_2 = $(this).data('id_transaksi_2');
            // var susut = $(".susut_" + id_tambah).val();
            // var shrinkage = $(".shrinkage_" + id_tambah).val();
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
            // if (lot != "" && roll != "" && bruto != "" && netto != "") {
            $.ajax({
                type: "POST",
                url: "ajax/ajax_bom.php",
                data: {
                    "add_list_2": id_transaksi,
                    "id_transaksi": id_transaksi,
                    "id_transaksi_2": id_transaksi_2
                    // "susut": susut
                    // "shrinkage": shrinkage
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
            // } else {
            //     alert("Mohon Untuk Diisi Data yang Lengkap !");
            // }
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
                url: "ajax/ajax_bom.php",
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
            var qty = $(".qty_" + no_id).val();
            // var uom = $(".uom_" + no_id).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_masuk.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "qty": qty
                },
                cache: true,
                success: function(result) {
                    $(".daftar_bahan").html(result);

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