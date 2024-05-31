<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";




if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}



$estimasi_harga = 0;

if ($_GET['id_transaksi'] == "new") {
    $id_transaksi = $_SESSION['id_user'];
} else {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
}

$type = "";
if (isset($_GET['type'])) {
    $type = mysqli_real_escape_string($conn, $_GET['type']);
}


if (isset($_GET['id_po'])) {
    $id_po = $_GET['id_po'];
} else {
    $id_po = "";
}

if (isset($_GET['harga_ongkos'])) {
    $harga_ongkos = $_GET['harga_ongkos'];
} else {
    $harga_ongkos = "";
}



$tanggal_transaksi = date("Y-m-d");

$nama_sales = $_SESSION['nm_user'];
$nama_customer = "";
$no_telp = "";
$email = "";
$kota = "";
$alamat = "";
$keterangan = "";
$jenis_penjualan = "";
$source_customer = "";
$subtotal_invoice = "";
$total_invoice = "";
$cash = "";
$id_invoice = "";
$akun = "";
// Edit Dzaky
// $ongkos = "";
$jatuh_tempo = 0;
$non_stock = 0;
$jenis_trs = "";


if (isset($_GET['id_transaksi'])) {
    if ($_GET['id_transaksi'] !== "new") {
        $select_keluar_data = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi";
        $query_keluar_data = mysqli_query($conn, $select_keluar_data);
        $data_keluar = mysqli_fetch_array($query_keluar_data);

        $select_sales = "SELECT * FROM tb_karyawan WHERE user_id = '" . $data_keluar['dibuat_oleh'] . "'";
        $query_sales = mysqli_query($conn, $select_sales);
        $data_sales = mysqli_fetch_array($query_sales);

        $select_customer = "SELECT * FROM tb_customer WHERE id_customer = '" . $data_keluar['id_customer'] . "'";
        $query_customer = mysqli_query($conn, $select_customer);
        $data_customer = mysqli_fetch_array($query_customer);

        $tanggal_transaksi = date("Y-m-d", strtotime($data_keluar['tgl_transaksi']));
        $nama_sales = $data_sales['nama_lengkap'];
        $nama_customer = $data_customer['id_customer'] . " | " . $data_customer['nama_customer'];
        $keterangan = $data_keluar['keterangan'];
        $id_po = $data_keluar['no_po'];
        $jatuh_tempo = $data_keluar['jatuh_tempo'];
        $non_stock = $data_keluar['non_stock'];
        $harga_ongkos = $data_keluar['ongkos_kirim'];
        $jenis_trs = $data_keluar['jenis_transaksi'];
        $akun = $data_keluar['akun'];
        // Edit Dzaky
        // $ongkos = $data_keluar['ongkos'];
    }
}

if (isset($_GET['cust_po'])) {
    $nama_customer = $_GET['cust_po'];
}

if (isset($_POST['simpan'])) {
    $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jatuh_tempo = mysqli_real_escape_string($conn, $_POST['jatuh_tempo']);
    $non_stock = mysqli_real_escape_string($conn, $_POST['non_stock']);
    $no_po = mysqli_real_escape_string($conn, $_POST['no_po']);
    $akun = mysqli_real_escape_string($conn, $_POST['akun']);
    // Edit Dzaky
    // $ongkos = mysqli_real_escape_string($conn, $_POST['ongkos']);



    if ($_GET['id_transaksi'] == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }


    $id_customer = explode(" | ", $nama_customer);
    $id_customer = $id_customer[0];

    // Edit Dzaky

    // if($jatuh_tempo == 0) {
    //     $sql = mysqli_query($conn, "select tgl_invoice,SUM(subtotal) AS subtotal,ppn,disc_invoice,cust_invoice
    //     from tb_barang_keluar where id_invoice = '" . $id_invoice . "' ");
    //     while ($row = mysqli_fetch_array($sql)) {
    //         $subtotal_invoice = $row['subtotal'];
    //         $total_invoice = $row['subtotal'];
    //     }
    //     $cash = ",id_invoice = '".generate_transaction_key('INV', 'INV', date('m'), date('Y'))."',cust_invoice = '".$id_customer."',subtotal_invoice = '".$subtotal_invoice."',total_invoice = '".$total_invoice."'";
    // }
    // Edit Dzaky

    if ($_GET['id_transaksi'] == "new") {
        $id_keluar = generate_barang_keluar_key("SO", "SO", date("m"), date("y"));
        $id_transaksi = $_SESSION['id_user'];
        $update_id = "id_transaksi = '" . $id_keluar . "',";
    } else {
        $id_keluar = $id_transaksi;
        $id_transaksi = $id_transaksi;
        $update_id = "";
    }

    $select_transaksi = "SELECT * FROM tb_barang_keluar
                            WHERE id_transaksi = '" . $id_transaksi . "'";
    $query_transaksi = mysqli_query($conn, $select_transaksi);
    while ($row_transaksi = mysqli_fetch_array($query_transaksi)) {
        if ($row_transaksi['id_bahan'] == "") {
            $hasil = 2;
        }
    }


    $update = "UPDATE tb_barang_keluar SET
        " . $update_id . "
        id_customer = '" . $id_customer . "',
        keterangan = '" . $keterangan . "',
        no_po = '" . $no_po . "',
        status_keluar = 'd' ,
        tgl_transaksi = '" . $tanggal_transaksi . "',
        jatuh_tempo = '" . $jatuh_tempo . "',
        tgl_jatuh_tempo = '" . date("Y-m-d", strtotime("+" . $jatuh_tempo . " days", strtotime($tanggal_transaksi))) . "',
        by_user_pajak = '" . $_SESSION['jenis_pajak'] . "',
        non_stock = '" . $non_stock . "',
        akun = '" . $akun . "'
        
        WHERE id_transaksi = '" . $id_transaksi . "';
    ";
    // Edit Dzaky
    $query_update = mysqli_query($conn, $update);

    header("location:master_barang_keluar_edit.php?id_transaksi=" . $id_keluar);
}

if (isset($_POST['simpan_oto'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jatuh_tempo = mysqli_real_escape_string($conn, $_POST['jatuh_tempo']);
    $non_stock = mysqli_real_escape_string($conn, $_POST['non_stock']);
    $no_po = mysqli_real_escape_string($conn, $_POST['no_po']);
    $ongkos_kirim = mysqli_real_escape_string($conn, $_POST['ongkos_kirim']);
    $jenis_transaksi = mysqli_real_escape_string($conn, $_POST['jenis_transaksi']);
    $akun = mysqli_real_escape_string($conn, $_POST['akun']);

    $id_customer = explode(" | ", $nama_customer);
    $id_customer = $id_customer[0];

    if ($id_transaksi == "new") {
        $id_keluar = generate_barang_keluar_key("SO", "SO", date("m"), date("y"));
        $id_transaksi = $_SESSION['id_user'];
        $update_id = "id_transaksi = '" . $id_keluar . "',";
    } else {
        $id_keluar = $id_transaksi;
        $id_transaksi = $id_transaksi;
        $update_id = "";
    }

    begin();
    // Jurnal
    if ($jatuh_tempo == '0') {
        $sql = mysqli_query($conn, "select tgl_invoice,SUM(subtotal) AS subtotal,SUM(total) AS total,ppn,disc_invoice,cust_invoice,akun
        from tb_barang_keluar where id_transaksi = '" . $id_transaksi . "' ");
        while ($row = mysqli_fetch_array($sql)) {
            $subtotal_invoice = $row['subtotal'];
            $total_invoice = $row['total'];
        }
        $id_invoice = generate_transaction_key('INV', 'INV', date('m'), date('Y'));
        $cash = ",id_invoice = '" . $id_invoice . "',cust_invoice = '" . $id_customer . "',subtotal_invoice = '" . $subtotal_invoice . "',total_invoice = '" . $total_invoice . "',tgl_invoice = '" . $tanggal_transaksi . "'";

        $query = mysqli_query($conn, "insert into inv_payment(id_inv,metode,pay,tgl_terima,tgl_pay) 
        values ('" . $id_invoice . "','1-1006','" . $total_invoice . "','" . date('Y-m-d H:i:s') . "','" . date('Y-m-d H:i:s') . "')  ");
        if ($query) {
            $jurnal_1 = add_jurnal('',  $akun, '4-4011', $total_invoice, date('Y-m-d H:i:s'), "Penerimaan Pembayaran " . $id_invoice . "", $_SESSION['id_user']);
            $id_jurnal = explode("|", $jurnal_1);
            $sts_jurnal = $id_jurnal[0];
            $id_jurnal = $id_jurnal[1];
        }
    }else{
        $sql = mysqli_query($conn, "select tgl_invoice,SUM(subtotal) AS subtotal,SUM(total) AS total,ppn,disc_invoice,cust_invoice,akun
        from tb_barang_keluar where id_transaksi = '" . $id_transaksi . "' ");
        while ($row = mysqli_fetch_array($sql)) {
            $subtotal_invoice = $row['subtotal'];
            $total_invoice = $row['total'];
        }
        $jurnal_1 = add_jurnal('',  '1-1011', '4-4011', $total_invoice, date('Y-m-d H:i:s'), "Penerimaan Pembayaran " . $id_invoice . "", $_SESSION['id_user']);
    }
    // Edit Dzaky ATas

    $pass_stock = 1;
    $sql_check_stock = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'");
    while ($row_check_stock = mysqli_fetch_array($sql_check_stock)) {
        $id_bahan = $row_check_stock['id_bahan'];
        $berat = $row_check_stock['berat'];
        $qty = $row_check_stock['qty'];

        $qty_stock = 0;
        $sql_qty_stock = mysqli_query($conn, "SELECT SUM(stock) AS ttl_stock FROM tb_stock WHERE id_bahan = '" . $id_bahan . "' AND berat = '" . $berat . "'");
        if ($row_stock = mysqli_fetch_array($sql_qty_stock)) {
            $qty_stock = $row_stock['ttl_stock'];
        }

        if ($pass_stock == 1) {
            if ($qty_stock > 0) {
                if ($qty_stock >= $qty) {
                    $pass_stock = 1;
                }
            } else {
                $pass_stock = 0;
            }
        }
    }

    if ($pass_stock == 1 && $non_stock == 0) {
        $sql_check_stock = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'");
        while ($row_check_stock = mysqli_fetch_array($sql_check_stock)) {
            $id_bahan = $row_check_stock['id_bahan'];
            $berat = $row_check_stock['berat'];
            $qty = $row_check_stock['qty'];
            $uom = $row_check_stock['uom'];

            $sql_qty_stock = mysqli_query($conn, "SELECT * FROM tb_stock WHERE id_bahan = '" . $id_bahan . "' AND berat = '" . $berat . "' AND uom = '" . $uom . "'");
            if ($row_stock = mysqli_fetch_array($sql_qty_stock)) {
                $stock = $row_stock['stock'];
                $grand_stock = $stock - $qty;

                $sql_update_stock = mysqli_query($conn, "
                    UPDATE
                        tb_stock
                    SET
                        stock = '" . $grand_stock . "'
                    WHERE
                        id_bahan = '" . $id_bahan . "' AND
                        berat = '" . $berat . "' AND
                        uom = '" . $uom . "'
                ");
            }

            $sql_summ_stock = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
            if ($row_summ_stock = mysqli_fetch_array($sql_summ_stock)) {
                $total_qty = $row_summ_stock['total_qty'];
                $grand_qty = $total_qty - $qty;

                $total_berat = $row_summ_stock['total_berat'];
                $grand_berat = $total_berat - ($qty * $berat);

                $sql_update_summ_stock = mysqli_query($conn, "
                    UPDATE
                        tb_bahan
                    SET
                        total_qty = '" . $grand_qty . "',
                        total_berat = '" . $grand_berat . "'
                    WHERE
                        id_bahan = '" . $id_bahan . "'
                ");
            }
        }
    }

    if ($pass_stock == 1 && $non_stock == 0) {

        $update = "UPDATE tb_barang_keluar SET
            " . $update_id . "
            id_customer = '" . $id_customer . "',
            keterangan = '" . $keterangan . "',
            no_po = '" . $no_po . "',
            status_keluar = 's',
            tgl_transaksi = '" . $tanggal_transaksi . "',
            jatuh_tempo = '" . $jatuh_tempo . "',
            ongkos_kirim = '" . $ongkos_kirim . "',
            jenis_transaksi = '" . $jenis_transaksi . "',
            tgl_jatuh_tempo = '" . date("Y-m-d", strtotime("+" . $jatuh_tempo . " days", strtotime($tanggal_transaksi))) . "',
            by_user_pajak = '" . $_SESSION['jenis_pajak'] . "',
            non_stock = '" . $non_stock . "' " . $cash . ",
            akun = '" . $akun . "'
           
            WHERE id_transaksi = '" . $id_transaksi . "';
        ";
        // Edit Dzaky

        $query_update = mysqli_query($conn, $update);

        commit();

        header("location:master_barang_keluar.php");
    } else {
        if ($non_stock == 1) {
            $update = "UPDATE tb_barang_keluar SET
                " . $update_id . "
                id_customer = '" . $id_customer . "',
                keterangan = '" . $keterangan . "',
                no_po = '" . $no_po . "',
                status_keluar = 's',
                tgl_transaksi = '" . $tanggal_transaksi . "',
                jatuh_tempo = '" . $jatuh_tempo . "',
                ongkos_kirim = '" . $ongkos_kirim . "',
                jenis_transaksi = '" . $jenis_transaksi . "',
                tgl_jatuh_tempo = '" . date("Y-m-d", strtotime("+" . $jatuh_tempo . " days", strtotime($tanggal_transaksi))) . "',
                by_user_pajak = '" . $_SESSION['jenis_pajak'] . "',
                non_stock = '" . $non_stock . "' " . $cash . ",
                akun = '" . $akun . "'
                
                WHERE id_transaksi = '" . $id_transaksi . "';
            ";
            // Edit Dzaky

            $query_update = mysqli_query($conn, $update);

            commit();

            header("location:master_barang_keluar.php");
        } else {
            rollback();
            echo '
                <script>
                    alert("Maaf, ada stock barang yang tidak mencukupi !");
                    window.location.href="master_barang_keluar_edit.php?id_transaksi=' . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . '";
                </script>
            ';
        }
    }
}

$hide = "";
$hideen = "style='display:none;'";
$readonly = "";
$filled_input = "";
if (isset($_GET['view'])) {
    $hide = "style='display:none;'";
    $hideen = "style='display:none;'";
    $readonly = "readonly";
    $filled_input = "filled-input";
}

$readonly_type = "";
$filled_input_type = "";
if (isset($_GET['type'])) {
    $readonly_type = "readonly";
    $filled_input_type = "filled-input";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Inventory System</title>
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

    <div class="preloader-it">
        <div class="loader-pendulums"></div>
    </div>

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

                    <!-- Page Heading -->

                    <!-- DataTales Example -->
                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <div class="card shadow mb-4">
                            <h4 class="hk-sec-title" style="margin:1rem;">Master Barang Keluar</h4>
                            <div class="col-12">
                                <div class="row justify-content">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Tgl Faktur</span>
                                            </div>
                                            <input type="date" name="tanggal_transaksi" id="" class="form-control form-control-sm tanggal_transaksi" value="<?= $tanggal_transaksi; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text" id="inputGroup-sizing-sm">Keterangan</span>
                                            </div>
                                            <input type="text" name="keterangan" id="" class="form-control form-control-sm keterangan <?= $filled_input; ?>" value="<?= $keterangan; ?>" autocomplete="off" onclick="this.select();" <?= $readonly; ?>>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Customer</span>
                                            </div>
                                            <input type="text" name="nama_customer" id="" class="form-control form-control-sm <?= $filled_input; ?> nama_customer" value="<?= $nama_customer; ?>" autocomplete="off" list="list_customer" onclick="this.select();" required <?= $readonly; ?>>
                                            <datalist id="list_customer" class="list_customer">
                                            </datalist>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Admin</span>
                                            </div>
                                            <input type="text" name="sales_person" id="" class="form-control form-control-sm sales_person filled-input" value="<?= $nama_sales; ?>" autocomplete="off" onclick="this.select();" readonly>
                                        </div>


                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Jatuh Tempo</span>
                                            </div>
                                            <input type="text" name="jatuh_tempo" id="" class="form-control form-control-sm <?= $filled_input; ?>" value="<?php echo $jatuh_tempo; ?>" autocomplete="off" onclick="this.select();" required <?= $readonly; ?>>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Stock / Non</span>
                                            </div>
                                            <select name="non_stock" class="form-control form-control-sm non_stock <?= $filled_input . $filled_input_type; ?>" <?= $readonly . $readonly_type; ?>>
                                                <?php
                                                if ($type == "") {
                                                ?>

                                                    <option value="0" <?php if ($non_stock == 0) {
                                                                            echo "selected";
                                                                        } ?>>Stock (Gudang)</option>

                                                <?php
                                                }
                                                ?>
                                                <option value="1" <?php if ($non_stock == 1) {
                                                                        echo "Selected";
                                                                    } ?>>Non-Stock (pabrik)</option>
                                            </select>
                                        </div>


                                    </div>

                                    <div class="col-md-6">

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text" id="inputGroup-sizing-sm">Nomor PO</span>
                                            </div>
                                            <input type="text" name="no_po" id="" class="form-control form-control-sm no_po" value="<?= $id_po; ?>" autocomplete="off" onclick="this.select();" <?= $readonly; ?>>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text" id="inputGroup-sizing-sm">Ongkos</span>
                                            </div>
                                            <input type="text" name="ongkos_kirim" id="" class="form-control form-control-sm ongkos_kirim" value="<?= $harga_ongkos; ?>" value="0" autocomplete="off" onclick="this.select();" <?= $readonly; ?>>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text" id="inputGroup-sizing-sm">Akun</span>
                                            </div>
                                            <select name="akun" id="" class="form-control form-control-sm metode" value="<?= $akun; ?>">
                                                <?php
                                                $selected2 = '';
                                                if($akun ==''){
                                                $selected2 = 'selected';
                                                }
                                                echo '
                                                <option value="" '.$selected2.'>Tempo (Isi Tempo Jika Tempo)</option>
                                                ';
                                                $sql_get_akun = mysqli_query($conn, "
                                                                            SELECT 
                                                                                kode_akun,
                                                                                nm_akun
                                                                            FROM
                                                                                m_akun
                                                                            WHERE
                                                                                kat_akun = '3'
                                                                            ORDER BY m_akun_id ASC
                                                                        ");
                                                while ($row_akun = mysqli_fetch_array($sql_get_akun)) {
                                                    $kode_akun = $row_akun['kode_akun'];
                                                    $nm_akun = $row_akun['nm_akun'];
                                                    $selected = '';
                                                    if($akun == $kode_akun){
                                                        $selected = 'selected';
                                                    }
                                                    echo '
                                                                                <option value="' . $kode_akun . '" '.$selected.'>' . $nm_akun . '</option>
                                                                            ';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <!-- Edit Dzaky -->
                                        <!-- <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text" id="inputGroup-sizing-sm">Ongkos</span>
                                            </div>
                                            <input type="text" name="ongkos" id="" class="form-control form-control-sm input_mask ongkos" value="<?= $ongkos; ?>" autocomplete="off" onclick="this.select();" <?= $readonly; ?> list="list_ongkos">
                                            <datalist id="list_ongkos" class="list_ongkos">
                                                <option value="950000" <?php if ($ongkos == 950000) {
                                                                            echo "selected";
                                                                        } ?>>Bogor</option>
                                                <option value="600000" <?php if ($ongkos == 600000) {
                                                                            echo "Selected";
                                                                        } ?>>Bandung</option>
                                                <option value="800000" <?php if ($ongkos == 800000) {
                                                                            echo "Selected";
                                                                        } ?>>Bu jenni</option>
                                                <option value="300000" <?php if ($ongkos == 300000) {
                                                                            echo "Selected";
                                                                        } ?>>Andersen Jakarta Ambil Sendiri</option>
                                                <option value="875000" <?php if ($ongkos == 875000) {
                                                                            echo "Selected";
                                                                        } ?>>Tata Bekasi / KKB Mobil Rosid / Andersen Jakarta / Bekasi</option>
                                                <option value="100000" <?php if ($ongkos == 100000) {
                                                                            echo "Selected";
                                                                        } ?>>KKB Mobil Shinta</option>
                                            </select>
                                            </datalist>
                                        </div> -->

                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Jenis Transaksi</span>
                                            </div>
                                            <select name="jenis_transaksi" class="form-control form-control-sm <?= $filled_input; ?> jenis_transaksi" <?= $readonly; ?>>
                                                <option value="">-- Pilih Jenis Transaksi --</option>
                                                <option value="penjualan" <?php if ($jenis_trs == 'penjualan') echo 'selected'; ?>>Penjualan</option>
                                                <option value="return" <?php if ($jenis_trs == 'return') echo 'selected'; ?>>Return</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <div class="row no-gutter">
                                    <div class="col-md-6" <?= $hide; ?>>
                                        <h6>Detail Barang</h6>
                                        <div class="input-group mt-5 mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama Barang</span>
                                            </div>
                                            <input type="text" name="" id="" class="form-control form-control-sm <?= $filled_input; ?> nama_barang" style="width:180px;" autocomplete="off" list="list_barang" onclick="this.select()" <?= $readonly; ?>>
                                            <datalist id="list_barang" class="list_barang">
                                            </datalist>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Yard</span>
                                            </div>

                                            <input name="berat_barang" id="" class="form-control form-control-sm berat_barang <?= $filled_input; ?>" <?= $readonly; ?> list="list_yard" />
                                            <datalist id="list_yard" class="list_yard">
                                            </datalist>

                                        </div>
                                        <input type="hidden" name="uom_barang" id="" value="YARD" class="form-control form-control-sm uom_barang" />

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Qty Roll</span>
                                            </div>
                                            <input type="number" step="0.01" name="" id="" class="form-control form-control-sm qty_barang <?= $filled_input; ?>" autocomplete="off" onclick="this.select();" <?= $readonly; ?>>
                                        </div>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Harga per Yard</span>
                                            </div>
                                            <input type="text" name="" id="" class="form-control form-control-sm text-left harga_barang <?= $filled_input; ?>" autocomplete="off" onclick="this.select();" <?= $readonly; ?>>
                                            <button type="button" class="btn btn-sm btn-success ml-5 add_barang" <?= $hide; ?>><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID Barang</th>
                                                <th class="text-center">Nama Barang</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Yard</th>
                                                <th class="text-center">Satuan</th>
                                                <th class="text-center">Harga</th>
                                                <th class="text-center">Total</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="daftar_barang">
                                            <?php
                                            $t_yard = 0;
                                            $t_qty = 0;
                                            $sql_get_transaksi = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' ORDER BY no_id ASC");
                                            $jum_transaksi = mysqli_num_rows($sql_get_transaksi);
                                            while ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
                                                $no_id = $row_transaksi['no_id'];
                                                $id_bahan = $row_transaksi['id_bahan'];
                                                $qty = $row_transaksi['qty'];
                                                $berat = $row_transaksi['berat'];
                                                $uom = $row_transaksi['uom'];
                                                $harga = $row_transaksi['harga'];
                                                $subtotal = $row_transaksi['subtotal'];

                                                $k_yard = $qty * $berat;
                                                $t_yard += $k_yard;
                                                $t_qty += $qty;

                                                $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
                                                $data_barang = mysqli_fetch_array($sql_get_barang);

                                                echo '
                                                <tr>
                                                    <td class="text-center">' . $id_bahan . '</td>
                                                    <td class="text-center">' . $data_barang['nama_bahan'] . '</td>
                                                    ';

                                                if ($type !== "") {
                                                    echo '
                                                            <td class="text-center">
                                                                <input type="text" name="qty_' . $no_id . '" class="ubah_data_barang_tembak qty_' . $no_id . ' text-right input_mask" id="" data-no_id="' . $no_id . '" value="' . number_format($qty) . '">
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="number" name="berat_' . $no_id . '" class="ubah_data_barang_tembak berat_' . $no_id . ' text-right" id="" data-no_id="' . $no_id . '" value="' . round($berat) . '">
                                                            </td>
                                                            <td class="text-center">' . $uom . '</td>
                                                            <td class="text-center">
                                                                <input type="text" name="harga_' . $no_id . '" class="ubah_data_barang_tembak harga_' . $no_id . ' text-right input_mask" id="" data-no_id="' . $no_id . '" value="' . number_format($harga) . '">
                                                            </td>
                                                        ';
                                                } else {
                                                    echo '
                                                            <td class="text-center">' . $qty . '</td>
                                                            <td class="text-center">' . round($berat) . '</td>
                                                            <td class="text-center">' . $uom . '</td>
                                                            <td class="text-center">' . number_format($harga) . '</td>
                                                        ';
                                                }

                                                echo '
                                                    
                                                    <td class="text-center total_harga_' . $no_id . '">' . number_format($subtotal) . '</td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-danger del_barang" data-no_id="' . $no_id . '" ' . $hide . '><i class="fa fa-trash-o"></i></button>
                                                    </td>
                                                </tr>
                                            ';
                                            }
                                            if ($jum_transaksi > 0) {
                                                $sql_get_transaksi = mysqli_query($conn, "SELECT SUM(subtotal) AS ttl_subtotal,ppn,qty,berat FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'");
                                                if ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
                                                    $ttl_subtotal = $row_transaksi['ttl_subtotal'];
                                                    $ppn_persen = $row_transaksi['ppn'];
                                                    $ppn = $ttl_subtotal * $ppn_persen / 100;

                                                    echo '
                                                    <tr>
                                                        <td class="text-right" colspan="6">T. QTY</td>
                                                        <td class="text-right" colspan="2">' . $t_qty . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right" colspan="6">T. YARD</td>
                                                        <td class="text-right" colspan="2">' . $t_yard . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right" colspan="6">Total</td>
                                                        <td class="text-right" colspan="2">' . number_format($ttl_subtotal) . '</td>
                                                    </tr>
                                                    ';

                                                    if ($_SESSION['jenis_pajak'] == 1) {
                                                        if (isset($_GET['view'])) {
                                                            echo '
                                                                <tr>
                                                                    <td class="text-right" colspan="6">PPN ' . $ppn_persen . ' %</td>
                                                                    <td class="text-right" colspan="2">' . number_format($ppn) . '</td>
                                                                </tr>
                                                            ';
                                                        } else {
                                                            echo '
                                                                <tr>
                                                                    <td class="text-right" colspan="6">PPN <input type="number" name="" id="" class="ppn_input text-right" style="max-width:50px;" value="' . $ppn_persen . '"> %</td>
                                                                    <td class="text-right" colspan="2">' . number_format($ppn) . '</td>
                                                                </tr>
                                                            ';
                                                        }
                                                    }

                                                    echo '
                                                    <tr>
                                                        <td class="text-right" colspan="6">Grand Total</td>
                                                        <td class="text-right" colspan="2">' . number_format($ttl_subtotal + $ppn) . '</td>
                                                    </tr>
                                                    ';
                                                }
                                            }
                                            ?>

                                        </tbody>
                                    </table>
                                </div>

                                <div class="table-responsive d-none">
                                    <table class="table table-bordered w-50 w--100 display tb_jps_re_ins tb_jps_ins mt-15 mt-15">
                                        <thead>
                                            <tr>
                                                <th>Pembayaran</th>
                                                <th>Nominal</th>
                                                <th <?php echo $hide; ?>></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list_bayar">
                                            <?php
                                            $select_pembayaran = "SELECT * FROM tb_pembayaran WHERE id_pembayaran = '" . $id_transaksi . "'";
                                            $query_pembayaran = mysqli_query($conn, $select_pembayaran);
                                            $jum_pembayaran = mysqli_num_rows($query_pembayaran);
                                            if ($jum_pembayaran > 0) {
                                                while ($row_pembayaran = mysqli_fetch_array($query_pembayaran)) {
                                                    echo '
                                                    <tr>
                                                        <td>
                                                            ';

                                                    if (isset($_GET['view'])) {

                                                        $select_akun = "SELECT * FROM tb_akun WHERE id_akun = '" . $row_pembayaran['metode'] . "' AND status_sj = 'aktif' ORDER BY bank ASC";
                                                        $query_akun = mysqli_query($conn, $select_akun);
                                                        $data_akun = mysqli_fetch_array($query_akun);

                                                        echo $data_akun['bank'];
                                                    } else {
                                                        echo '
                                                                <select name="" id="" class="form-control form-control-sm bank_' . $row_pembayaran['no_id'] . '">
                                                                ';

                                                        $select_akun = "SELECT * FROM tb_akun WHERE status_sj = 'aktif' ORDER BY bank ASC";
                                                        $query_akun = mysqli_query($conn, $select_akun);
                                                        while ($row_akun = mysqli_fetch_array($query_akun)) {
                                                            $selected = "";
                                                            if ($row_akun['id_akun'] == $row_pembayaran['metode']) {
                                                                $selected = "selected";
                                                            }
                                                            echo '
                                                        <option value="' . $row_akun['id_akun'] . '" ' . $selected . '>' . $row_akun['bank'] . '</option>
                                                    ';
                                                        }

                                                        echo '
                                                                </select>
                                                                ';
                                                    }

                                                    echo '
                                                        </td>
                                                        ';

                                                    if (isset($_GET['view'])) {
                                                        echo '
                                                <td class="text-right">' . number_format($row_pembayaran['nominal']) . '</td>
                                                ';
                                                    } else {
                                                        echo '
                                                            <td><input type="text" name="" id="" class="text-right' . $filled_input . ' form-control form-control-sm nominal_terbayar nominal_terbayar_' . $row_pembayaran['no_id'] . '" data-no_id="' . $row_pembayaran['no_id'] . '" min="0" value="' . $row_pembayaran['nominal'] . '" ' . $readonly . '>
                                                            <input type="hidden" name="" class="nominal_terbayar_hidden nominal_terbayar_hidden_' . $row_pembayaran['no_id'] . '" value="' . $row_pembayaran['nominal'] . '">
                                                            </td>
                                                            ';
                                                    }

                                                    if (!isset($_GET['view'])) {
                                                        echo '
                                                        <td ' . $hide . '>
                                                        <button type="button" class="btn btn-warning ubah_pembayaran ubah_pembayaran_' . $row_pembayaran['no_id'] . '" data-no_id="' . $row_pembayaran['no_id'] . '"><i class="fa fa-pencil"></i></button>
                                                        <button type="button" class="btn btn-danger hapus_pembayaran hapus_pembayaran_' . $row_pembayaran['no_id'] . '" data-no_id="' . $row_pembayaran['no_id'] . '"><i class="fa fa-trash-o"></i></button>
                                                        </td>
                                                   
                                                ';
                                                    }

                                                    echo ' </tr>';
                                                }
                                                if (!isset($_GET['view'])) {
                                                    echo '
                                            <tr>
                                                <td>
                                                    <select name="" id="" class="form-control form-control-sm bank_bayar">
                                                    ';

                                                    $select_akun = "SELECT * FROM tb_akun WHERE status_sj = 'aktif' ORDER BY bank ASC";
                                                    $query_akun = mysqli_query($conn, $select_akun);
                                                    while ($row_akun = mysqli_fetch_array($query_akun)) {

                                                        echo '
                                                    <option value="' . $row_akun['id_akun'] . '">' . $row_akun['bank'] . '</option>
                                                ';
                                                    }

                                                    echo '
                                                        </select>
                                                    </td ' . $hide . '>
                                                    <td><input type="text" name="" id="" class="text-right form-control form-control-sm nominal_bayar" min="0" value="0">
                                                    <input type="hidden" name="" class="nominal_bayar_hidden" value="0"></td>
                                                    <td ' . $hide . '>
                                                        <button type="button" class="btn btn-secondary btn_bayar"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                </tr>
                                            ';
                                                }
                                            } else {
                                                if (!isset($_GET['view'])) {
                                                    echo '
                                            <tr>
                                                <td>
                                                    <select name="" id="" class="form-control form-control-sm bank_bayar">
                                                    ';

                                                    $select_akun = "SELECT * FROM tb_akun WHERE status_sj = 'aktif' ORDER BY bank ASC";
                                                    $query_akun = mysqli_query($conn, $select_akun);
                                                    while ($row_akun = mysqli_fetch_array($query_akun)) {

                                                        echo '
                                                    <option value="' . $row_akun['id_akun'] . '">' . $row_akun['bank'] . '</option>
                                                ';
                                                    }

                                                    echo '
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="" id="" class="text-right form-control form-control-sm nominal_bayar" min="0" value="0">
                                                    <input type="hidden" name="" class="nominal_bayar_hidden" value="0"></td>
                                                    <td>
                                                        <button type="button" class="btn btn-secondary btn_bayar"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                </tr>
                                            ';
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>





                                <div class="table-responsive">
                                    <?php
                                    if (isset($_GET['view'])) {
                                        echo '
                               
                                    <div class="row no-gutters">
                                        <a href="master_barang_keluar.php" class="btn btn-danger mb-2 font_keluar"><i class="fa fa-arrow-left"></i> Kembali</a>
                                        <a href="print/print_invoice_barang_keluar.php?id_transaksi=' . $id_transaksi . '" class="btn btn-success ml-2 mb-2 font_keluar" target="_blank"><i class="fa fa-print"></i> Print Invoice</a>
                                        <a href="print/print_sj.php?id_transaksi=' . $id_transaksi . '" class="btn btn-warning ml-2 mb-2 font_keluar" target="_blank"><i class="fa fa-print"></i> Print SJ</a>
                                    </div>
                                </div>
                                ';
                                    } else {
                                        echo '
                               
                                    <div class="row no-gutters">
                                        <a href="master_barang_keluar.php" class="btn btn-sm btn-danger mb-2"><i class="fa fa-arrow-left"></i> Kembali</a>
                                        <button type="submit" name="simpan" class="btn btn-sm btn-success mb-2 ml-2">Simpan</button>
                                        <button type="submit" name="simpan_oto" class="btn btn-sm btn-primary mb-2 ml-2">Simpan & Otorisasi</button>
                                    </div>
                                </div>
                                ';
                                    }
                                    ?>
                                </div>



                            </div>
                            <!-- /.container-fluid -->

                        </div>
                    </form>
                    <!-- End of Main Content -->

                    <!-- Footer -->


                </div>
                <!-- End of Content Wrapper -->

            </div>



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
        $(".input_mask").mask('#,##0', {
            reverse: true
        });

        $(document).on("change", ".nama_barang", function() {
            var nama_barang = $(this).val();
            var nama_customer = $(".nama_customer").val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_keluar.php",
                data: {
                    "get_barang": nama_barang,
                    "nama_customer": nama_customer
                },
                cache: true,
                beforeSend: function(response) {
                    $(".preloader-it").show();
                },
                success: function(result) {
                    $(".preloader-it").hide();
                    var result = result.split("|");
                    $(".list_yard").html(result[0]);
                    $(".uom_barang").html(result[1]);
                    $(".harga_barang").val(result[2]);
                    //alert(result[2]);
                }
            });
        });

        $(document).on("keyup", ".nama_customer", function() {
            var nama_customer = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_keluar.php",
                data: {
                    "cari_nama_customer": nama_customer
                },
                cache: true,
                success: function(result) {
                    $(".list_customer").html(result);
                }
            });
        });

        $(document).on("keyup", ".nama_barang", function() {
            var nama_barang = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_keluar.php",
                data: {
                    "cari_nama_barang": nama_barang
                },
                cache: true,
                success: function(result) {
                    $(".list_barang").html(result);
                }
            });
        });

        $(document).on("click", ".add_barang", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var type = "<?= $type ?>";
            var nama_barang = $(".nama_barang").val();
            var qty_barang = $(".qty_barang").val();
            var berat_barang = $(".berat_barang").val();
            var uom_barang = $(".uom_barang").val();
            var uom_barang = $(".uom_barang").val();
            var non_stock = $(".non_stock").val();
            var harga_barang = $(".harga_barang").val();
            if (harga_barang == "") {
                var harga_barang = 0;
            } else {
                var harga_barang = harga_barang.split(",").join("");
                var harga_barang = parseFloat(harga_barang);
            }

            if (nama_barang == "" || qty_barang == "" || berat_barang == "" || uom_barang == "" || qty_barang < 1) {
                alert("Mohon isi semua kolom barang !");
            } else {

                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_barang_keluar.php",
                    data: {
                        "add_barang": id_transaksi,
                        "nama_barang": nama_barang,
                        "qty_barang": qty_barang,
                        "berat_barang": berat_barang,
                        "uom_barang": uom_barang,
                        "non_stock": non_stock,
                        "harga_barang": harga_barang,
                        "type": type
                    },
                    cache: true,
                    beforeSend: function(response) {
                        $(".preloader-it").show();
                    },
                    success: function(result) {
                        //alert(result);
                        $(".preloader-it").hide();

                        var result = result.split("|");

                        if (result[0] == 0 || result[0] == 2) {
                            alert("Maaf, stock barang ini tidak cukup");
                        } else {
                            $(".daftar_barang").html(result[1]);

                            $(".nama_barang").val("");
                            $(".berat_barang").html("");
                            $(".uom_barang").html("");
                            $(".qty_barang").val("");
                            $(".harga_barang").val("");
                        }

                    }
                });
            }
        });

        $(document).on("click", ".del_barang", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var type = "<?= $type ?>";
            var no_id = $(this).data('no_id');

            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_keluar.php",
                data: {
                    "del_barang": id_transaksi,
                    "no_id": no_id,
                    "type": type
                },
                cache: true,
                beforeSend: function(response) {
                    $(".preloader-it").show();
                },
                success: function(result) {
                    $(".preloader-it").hide();
                    $(".daftar_barang").html(result);
                }
            });
        });

        $(document).on("change", ".ppn_input", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var type = "<?= $type ?>";
            var ppn_input = $(this).val();

            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_keluar.php",
                data: {
                    "id_ppn": id_transaksi,
                    "ppn_input": ppn_input,
                    "type": type
                },
                cache: true,
                beforeSend: function(response) {
                    $(".preloader-it").show();
                },
                success: function(result) {
                    $(".preloader-it").hide();
                    $(".daftar_barang").html(result);
                }
            });
        });

        $(document).on("change", ".ubah_data_barang_tembak", function() {
            var no_id = $(this).data('no_id');
            var id_transaksi = "<?= $id_transaksi; ?>";
            var type = "<?= $type ?>";

            var qty = $(".qty_" + no_id).val();
            var berat = $(".berat_" + no_id).val();
            var harga = $(".harga_" + no_id).val();
            if (harga !== "") {
                var harga = harga.split(",").join("");
            }

            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_keluar.php",
                data: {
                    "ubah_data_barang_tembak": no_id,
                    "id_transaksi": id_transaksi,
                    "type": type,
                    "qty": qty,
                    "berat": berat,
                    "harga": harga
                },
                cache: true,
                success: function(result) {
                    $(".daftar_barang").html(result);

                    $(".input_mask").mask('#,##0', {
                        reverse: true
                    });
                }
            });
        });
    });
</script>