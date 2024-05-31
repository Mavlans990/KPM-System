<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

$id_beban = generate_beban();

$rustam = $_SESSION['id_user'] == 'rustam';

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$pajak = "AND by_user_pajak = '" . $_SESSION['jenis_pajak'] . "'";

if ($_SESSION['id_user'] == 'Andnic') {
    $pajak = '';
}

// $select_keluar = "SELECT a.* FROM tb_delivery a WHERE a.id_transaksi = '" . $_SESSION['id_user'] . "' AND a.status_keluar = '' ";
// $query_keluar = mysqli_query($conn, $select_keluar);
// while ($row_keluar = mysqli_fetch_array($query_keluar)) {

//     $delete = mysqli_query($conn, "DELETE FROM tb_delivery WHERE id_transaksi = '" . $_SESSION['id_user'] . "' AND status_keluar = ''");
// }

// $tambah_baru = '<a href="master_delivery_edit.php?id_transaksi=new" class="btn btn-primary form-control-sm"><i class="fa fa-plus"></i> Tambah Baru</a>';

if (isset($_POST['delete'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus']);

    $msg = "Hapus Data Berhasil";

    // $sql_barang_keluar = mysqli_query($conn, "SELECT * FROM tb_delivery WHERE id_transaksi = '" . $id_transaksi . "'");
    // while ($row_barang_keluar = mysqli_fetch_array($sql_barang_keluar)) {
    //     $id_bahan = $row_barang_keluar['id_bahan'];
    //     $qty = $row_barang_keluar['qty'];
    //     $berat = $row_barang_keluar['berat'];

    //     $sql_stock_barang = mysqli_query($conn, "SELECT * FROM tb_stock WHERE id_bahan = '" . $id_bahan . "' AND berat = '" . $berat . "'");
    //     while ($row_stock_barang = mysqli_fetch_array($sql_stock_barang)) {
    //         $stock = $row_stock_barang['stock'];
    //         $grand_stock = $stock + $qty;

    //         $sql_update_stock = mysqli_query($conn, "
    //             UPDATE
    //                 tb_stock
    //             SET
    //                 stock = '" . $grand_stock . "'
    //             WHERE
    //                 id_bahan = '" . $id_bahan . "' AND
    //                 berat = '" . $berat . "'
    //         ");
    //     }

    //     $sql_stock_summ_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
    //     while ($row_stock_summ_bahan = mysqli_fetch_array($sql_stock_summ_barang)) {
    //         $total_qty = $row_stock_summ_bahan['total_qty'];
    //         $grand_qty = $total_qty + $qty;

    //         $total_berat = $row_stock_summ_bahan['total_berat'];
    //         $grand_berat = $total_berat + ($qty * $berat);

    //         $sql_update_summ_stock = mysqli_query($conn, "
    //             UPDATE
    //                 tb_bahan
    //             SET
    //                 total_qty = '" . $grand_qty . "',
    //                 total_berat = '" . $grand_berat . "'
    //             WHERE
    //                 id_bahan = '" . $id_bahan . "'
    //         ");
    //     }
    // }

    $sql_del_invoice = mysqli_query($conn, "DELETE FROM tb_delivery WHERE id_transaksi = '" . $id_transaksi . "'");


    echo '
        <script>alert("Hapus Data berhasil !");window.location.href="master_delivery.php";</script>
    ';
}

$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
}

$hidden = "style='display:none;'";
if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
    $hidden = "";
}

$customer = '';
$ex_customer = '';
$where_cust = '';
$id_cust = '';
$from = date("Y-m-d");
$to = date("Y-m-d");
if (isset($_POST['search'])) {
    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    // $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
    // $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    // $source_customer = mysqli_real_escape_string($conn, $_POST['source_customer']);
    // Error Gatau
}

if (isset($_GET['del_sj'])) {
    $sql_update = "update tb_packing set id_invoice = '',
    tgl_invoice=''
    where id_transaksi ='" . mysqli_real_escape_string($conn, $_GET['del_sj']) . "' ";
    $data = mysqli_query($conn, $sql_update);
    header('Location: laporan_sj.php');
}

if (isset($_POST['add_sj'])) {
    $buatsj = $_POST['buatsj'];
    for ($i = 0; $i < count($buatsj); $i++) {
        //echo "<script>alert('".$buatsj[$i]."');</script>";
        $sql_update = "update tb_packing set id_invoice = 'temp' 
        where id_packing ='" . $buatsj[$i] . "' ";
        $data = mysqli_query($conn, $sql_update);
    }
    header('Location: laporan_sj.php');
}

if (isset($_POST['otorisasi'])) {
    if ($_POST['id_invoice'] == "temp") {
        $id_invoice = generate_transaction_key('INV', 'INV', date('m'), date('Y'));
    } else {
        $id_invoice = $_POST['id_invoice'];
    }
    $tanggal_invoice = date("Y-m-d");
    $sql_update = "update tb_packing set 
        id_invoice = '" . $id_invoice . "',
        tgl_invoice='" . $tanggal_invoice . "'
        where id_invoice ='" . mysqli_real_escape_string($conn, $_POST['id_invoice']) . "' ";
        echo $sql_update.'<br>';
        $data = mysqli_query($conn, $sql_update);
        // return 0;

    header('Location: laporan_sj.php');
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

    <style>
        .fs-11 {
            font-size: 11px !important;
        }
    </style>
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

                    <!-- Page Heading -->



                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3" style="text-transform: none;">
                            <div class="form-inline">
                                <h3 class="mr-2">Laporan Surat Jalan</h3>
                                <!-- <?php if (!$rustam) {
                                            echo $tambah_baru;
                                        } else {
                                        }  ?> -->
                            </div>
                        </div>
                        <div class="card-header py-1">
                            <div class="">
                                <form action="" method="post">
                                    <br>
                                    <div class="row no-gutters">

                                        <div class="col-3">
                                            <div class="form-group">
                                                <input type="date" name="tgl_from" id="" class="form-control form-control-sm " value="<?= $from; ?>">
                                            </div>
                                        </div>
                                        <p class="mt-5">S/D</p>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <input type="date" name="tgl_to" id="" class="form-control form-control-sm " value="<?= $to; ?>">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-sm btn-primary" name="search"><i class="fa fa-search"></i> Cari</button>
                                            <!-- <a href="list_sj.php" class="btn btn-sm btn-success" ><i class="fa fa-print"></i>  List SJ</a> -->
                                        </div>
                                    </div>
                                    <div class="row no-gutters">
                                        <div class="form-group form-inline">
                                            <!-- <input type="date" name="tgl_from" id="" class="form-control form-control-sm " value="<?= $from; ?>">
                                            <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                            <input type="date" name="tgl_to" id="" class="form-control form-control-sm " value="<?= $to; ?>"> -->
                                            <!-- <input type="text" placeholder=" - Nama Customer - " name="customer" id="" class="form-control form-control-sm customer" value="<?= $customer; ?>" autocomplete="off" list="list_customer">
                                            <datalist id="list_customer" class="list_customer"></datalist> -->
                                            <!-- <select name="sales" class="form-control form-control-sm <?= $filled_input; ?> sales" >
                                                <option value="">Pilih Sales</option>
                                                <option value="sales1" <?php if ($sales == "sales1") {
                                                                            echo "selected";
                                                                        } ?>>Sales 1</option>
                                                <option value="sales2" <?php if ($sales == "sales2") {
                                                                            echo "selected";
                                                                        } ?>>Sales 2</option>
                                                <option value="sales3" <?php if ($sales == "sales3") {
                                                                            echo "selected";
                                                                        } ?>>Sales 3</option>
                                            </select> -->
                                            <!-- <select name="cabang" class="form-control form-control-sm <?= $filled_input; ?> cabang">
                                                <option value="">Pilih Category</option>
                                                <?php
                                                $sql = mysqli_query($conn, "SELECT * FROM tb_cabang");
                                                while ($data_cabang = mysqli_fetch_array($sql)) {
                                                ?>
                                                    <option value="<?= $data_cabang["nama_cabang"] ?>"><?= $data_cabang["nama_cabang"] ?></option> ';
                                                <?php } ?>
                                            </select> -->

                                            <!-- <button type="submit" class="mt--5 btn btn-primary form-control-sm ml-1" name="search"><i class="fa fa-search"></i> Cari</button> -->
                                            <!-- <?php
                                                    if (isset($_POST['search'])) {

                                                        echo '
                                                <a href="print/laporan_delivery_search_excel.php?tgl_from=' . $from . '&tgl_to=' . $to . '&cabang=' . $cabang . '" target="blank" class="btn btn-success text-white form-control-sm ml-1" ><i class="fa fa-print"></i> Download Excel</a>
                                                    ';
                                                    }
                                                    ?> -->
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No. Transaksi</th>
                                                <th class="text-center">Tgl Kirim</th>
                                                <!-- <th class="text-center">Jatuh Tempo</th> -->
                                                <th class="text-center">Pengirim</th>
                                                <th class="text-center">Penerima</th>
                                                <th class="text-center">Nama SJ</th>
                                                <th class="text-center">No. SJ</th>
                                                <!-- <th class="text-center">Aksi</th> -->
                                                <!-- <th class="text-center">Checklist</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $where_tgl = "";
                                            $limit = "LIMIT 50";
                                            if (isset($_POST['search'])) {
                                                $where_tgl = " AND a.tgl_kirim BETWEEN '" . $from . "' AND '" . $to . "'";
                                                $limit = "";
                                            }

                                            // echo "SELECT a.* FROM tb_packing a WHERE a.id_transaksi LIKE '%WO%' " . $where_tgl . " AND id_invoice != 'temp' GROUP BY a.id_packing ORDER BY a.id_invoice,a.tgl_kirim DESC " . $limit . " <br>";
                                            $sql_get_barang_keluar = mysqli_query($conn, "SELECT a.* FROM tb_packing a WHERE a.id_transaksi LIKE '%WO%' " . $where_tgl . " AND id_invoice != 'temp' GROUP BY a.id_packing ORDER BY a.id_invoice DESC " . $limit . "");
                                            while ($row_barang_keluar = mysqli_fetch_array($sql_get_barang_keluar)) {
                                                $id_transaksi = $row_barang_keluar['id_transaksi'];
                                                $tgl_kirim = $row_barang_keluar['tgl_kirim'];
                                                // $tgl_jatuh_tempo = $row_barang_keluar['tgl_jatuh_tempo'];
                                                $pengirim = $row_barang_keluar['pengirim'];
                                                $penerima = $row_barang_keluar['penerima'];
                                                $id_invoice = $row_barang_keluar['id_invoice'];
                                                $keterangan = $row_barang_keluar['nama_sj'];

                                                // $sts = '<span class="text-warning">Open</span>';
                                                // if ($status_keluar == "s" && $id_invoice == "") {
                                                //     $sts = '<span class="text-danger">Siap Kirim</span>';
                                                // } elseif ($status_keluar == "s" && $id_invoice != "") {
                                                //     $sts = '<span class="text-danger">Sudah Terkirim</span>';
                                                // }

                                                // $nama_customer = "";
                                                // $sql_get_customer = mysqli_query($conn, "SELECT nama_customer FROM tb_customer WHERE id_customer = '" . $id_customer . "'");
                                                // while ($row_customer = mysqli_fetch_array($sql_get_customer)) {
                                                //     $nama_customer = $row_customer['nama_customer'];
                                                // }

                                                // $btn_edit = "btn-success";
                                                // $link_edit = "master_delivery_edit.php?id_transaksi=" . $id_transaksi;
                                                // $edit_text  = "<i class='fa fa-pencil'></i> Ubah";
                                                // if ($status_keluar == "s") {
                                                //     $btn_edit = "btn-primary";
                                                //     $link_edit = "master_delivery_edit.php?id_transaksi=" . $id_transaksi . "&view=detail";
                                                //     $edit_text = "<i class='fa fa-eye'></i> Lihat";
                                                // }

                                                // $tipe = "Stock";
                                                // if ($non_stock == 1) {
                                                //     $tipe = "Non-Stock";
                                                // }

                                                // <td class="text-center fs-11">' . date("d-m-Y", strtotime($tgl_jatuh_tempo)) . '</td>
                                                echo '
                                                <tr>
                                                    <td class="text-center fs-11">' . $id_transaksi . '</td>
                                                    <td class="text-center fs-11">' . date("d-m-Y", strtotime($tgl_kirim)) . '</td>
                                                    <td class="text-center fs-11">' . $pengirim . '</td>
                                                    <td class="text-center fs-11">' . $penerima . '</td>
                                                    <td class="text-center fs-11">' . $keterangan . '</td>
                                                    <td class="text-center fs-11">
                                                        <a href="print/print_sj_2.php?id_invoice=' . $id_invoice . '" target="_blank" style="color:black;">' . $id_invoice . '</a>
                                                    </td>
                                                    ';
                                                // <td class="text-center fs-11">' . $sts . '</td>
                                                // <td class="text-center fs-11 d-none">
                                                //     <a href="' . $link_edit . '" class="btn btn-sm ' . $btn_edit . '">' . $edit_text . '</a>
                                                // </td>
                                                // if($_SESSION['id_user'] == 'Andnic'){echo '<button type="button" class="btn btn-sm btn-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal" data-id_transaksi="' . $id_transaksi . '"><i class="fa fa-trash-o"></i> Hapus</button>';}else{}

                                                if ($id_invoice == "") {
                                                    echo '
                                                    <td class="text-center fs-11">
                                                    <input type="checkbox" name="buatsj[]" class="check_so" id="" value="' . $row_barang_keluar['id_packing'] . '" data-id_so="' . $row_barang_keluar['id_packing'] . '" >
                                                    </td>';
                                                }

                                                echo '
                                                </tr>
                                            ';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-right">
                                    <input type="submit" name="add_sj" class="btn btn-sm btn-secondary btn_bayar mt-15" value="Tambah Ke Pengiriman" />
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="card shadow mb-4">
                        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No. Transaksi</th>
                                                <th class="text-center">Tgl Kirim</th>
                                                <!-- <th class="text-center">Jatuh Tempo</th> -->
                                                <th class="text-center">Pengirim</th>
                                                <th class="text-center">Penerima</th>
                                                <!-- <th class="text-center">Status</th> -->
                                                <th class="text-center">No. SJ</th>
                                                <!-- <th class="text-center">Checklist</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $where_tgl = "";
                                            if (isset($_POST['search'])) {
                                                $where_tgl = " AND a.tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "'";
                                            }


                                            $sql_get_barang_keluar = mysqli_query($conn, "SELECT a.* FROM tb_packing a WHERE a.id_transaksi LIKE '%WO%'  AND id_invoice = 'temp' GROUP BY a.id_packing ORDER BY a.id_invoice ASC LIMIT 50");
                                            while ($row_barang_keluar = mysqli_fetch_array($sql_get_barang_keluar)) {
                                                $id_transaksi = $row_barang_keluar['id_transaksi'];
                                                $tgl_kirim = $row_barang_keluar['tgl_kirim'];
                                                // $tgl_jatuh_tempo = $row_barang_keluar['tgl_jatuh_tempo'];
                                                $pengirim = $row_barang_keluar['pengirim'];
                                                $penerima = $row_barang_keluar['penerima'];
                                                $id_invoice = $row_barang_keluar['id_invoice'];

                                                // $sts = '<span class="text-warning">Open</span>';
                                                // if ($status_keluar == "s" && $id_invoice == "temp") {
                                                //     $sts = '<span class="text-danger">Siap Kirim</span>';
                                                // } elseif ($status_keluar == "s" && $id_invoice != "") {
                                                //     $sts = '<span class="text-danger">Sudah Terkirim</span>';
                                                // }

                                                // $nama_customer = "";
                                                // $sql_get_customer = mysqli_query($conn, "SELECT nama_customer FROM tb_customer WHERE id_customer = '" . $id_customer . "'");
                                                // while ($row_customer = mysqli_fetch_array($sql_get_customer)) {
                                                //     $nama_customer = $row_customer['nama_customer'];
                                                // }

                                                // $btn_edit = "btn-success";
                                                // $link_edit = "master_delivery_edit.php?id_transaksi=" . $id_transaksi;
                                                // $edit_text  = "<i class='fa fa-pencil'></i> Ubah";
                                                // if ($status_keluar == "s") {
                                                //     $btn_edit = "btn-primary";
                                                //     $link_edit = "master_delivery_edit.php?id_transaksi=" . $id_transaksi . "&view=detail";
                                                //     $edit_text = "<i class='fa fa-eye'></i> Lihat";
                                                // }

                                                // $tipe = "Stock";
                                                // if ($non_stock == 1) {
                                                //     $tipe = "Non-Stock";
                                                // }

                                                // $hide_rem = "";
                                                // if (isset($_GET['view'])) {
                                                //     $hide_rem = "d-none";
                                                // }

                                                // <td class="text-center fs-11">' . date("d-m-Y", strtotime($tgl_jatuh_tempo)) . '</td>
                                                echo '
                                                <tr>
                                                    <td class="text-center fs-11">' . $id_transaksi . '</td>
                                                    <td class="text-center fs-11">' . date("d-m-Y", strtotime($tgl_kirim)) . '</td>
                                                    <td class="text-center fs-11">' . $pengirim . '</td>
                                                    <td class="text-center fs-11">' . $penerima . '</td>
                                                    <td class="text-center fs-11">
                                                        <a href="print/print_sj_2.php?id_invoice=' . $id_invoice . '" target="_blank" style="color:black;">' . $id_invoice . '</a>
                                                    </td>
                                                    <td class="text-center fs-11">
                                                        <a class="btn btn-xs btn-danger" href="laporan_sj.php?id_invoice=' . $id_invoice . '&del_sj=' . $row_barang_keluar['id_transaksi'] . '">Remove</a>
                                                    </td>
                                                ';

                                                // if($_SESSION['id_user'] == 'Andnic'){echo '<button type="button" class="btn btn-sm btn-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal" data-id_transaksi="' . $id_transaksi . '"><i class="fa fa-trash-o"></i> Hapus</button>';}else{}
                                                // if ($status_keluar == "s" && $id_invoice == "") {
                                                //     echo '
                                                // <td class="text-center fs-11">
                                                // <input type="checkbox" name="buatsj[]" class="check_so" id="" value="' . $row_barang_keluar['id_transaksi'] . '" data-id_so="' . $row_barang_keluar['id_transaksi'] . '" >
                                                // </td>';
                                                // }

                                                echo '
                                                </tr>
                                            ';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- <div class="text-right">
                                <input type="submit" name="add_sj" class="btn btn-sm btn-secondary btn_bayar mt-15" value="Tambah Ke Pengiriman" />
                            </div> -->
                                <div class="col-md-6">
                                    <div class="input-group mb-3">
                                        <input type="hidden" name="id_invoice" id="" class="form-control form-control-sm " value="temp" readonly>
                                    </div>
                                </div>
                                <!-- <div class="col-md-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span style="width:130px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Tanggal Invoice</span>
                                    </div>
                                    <input type="date" name="tgl_invoice" id="" class="form-control form-control-sm " >
                                </div>
                            </div> -->
                                <div class="row no-gutters">
                                    <button type="submit" name="otorisasi" class="btn btn-sm btn-success mb-2 ml-2">Buat Surat Jalan</button>
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

    </div>

    <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Delivery</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin hapus Delivery ini ?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-danger" name="delete" value="Hapus">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ChooseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Cabang</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span style="" class="form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Cabang</span>
                            </div>

                            <select id="" class="form-control form-control-sm custom-select custom-select-sm branch_modal" name="branch">
                                <?php

                                $where_branch = "";
                                if ($_SESSION['group'] == "franchise") {
                                    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
                                    $where_branch = " WHERE id_cabang IN ('" . $filter_cabang . "')";
                                }

                                $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang " . $where_branch . " ORDER BY id_cabang ASC");
                                // echo $query_get_branch;
                                while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                    $id_branch = $row_branch['id_cabang'];
                                    $nama_cabang = $row_branch['nama_cabang'];
                                    echo '
                                        <option value="' . $id_branch . '" >' . $nama_cabang . '</option>
                                    ';
                                }


                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <a class="choose_branch btn btn-success btn-sm"><span class="text-white">Choose</span></a>
                    </div>
                </form>
            </div>
        </div>
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

</body>

</html>
<script type="text/javascript">
    $(document).ready(function() {

        $(document).on("click", ".check_so", function() {
            var id_so = $(this).data('id_so');
            var check_so = $(".check_so:checked").length;
            if (check_so > 0) {
                $(".btn_bayar").removeClass("btn-secondary");
                $(".btn_bayar").addClass("btn-primary");
                $(".btn_bayar").addClass("bayar_btn");
            } else {
                $(".btn_bayar").removeClass("btn-primary");
                $(".btn_bayar").removeClass("bayar_btn");
                $(".btn_bayar").addClass("btn-secondary");
            }
        });

        $(document).on("click", ".choose_branch", function() {
            var branch = $(".branch_modal").val();
            window.location.replace("master_barang_keluar_edit.php?id_transaksi=new&id_cabang=" + branch);
        });

        $(document).on("click", ".hapus_button", function() {
            var id_transaksi = $(this).data('id_transaksi');
            $(".id_hapus").val(id_transaksi);
        });

        $(document).on("keyup", ".customer", function() {
            var nm_cust = $(this).val();



            $.ajax({

                type: "POST",

                url: "ajax/ajax_get_customer.php",

                data: {

                    "get_cust": nm_cust

                },

                cache: true,

                success: function(result) {

                    $(".list_customer").html(result);

                }

            });

        });

    });
</script>