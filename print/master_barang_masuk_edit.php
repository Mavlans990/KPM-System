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

$tanggal_transaksi = date("Y-m-d");
$nama_supplier = "";
$nama_bahan = "";
$keterangan = "";

if (isset($_GET['id_transaksi'])) {
    if ($_GET['id_transaksi'] !== "new") {
        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_transaksi";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        $data_barang_masuk = mysqli_fetch_array($query_barang_masuk);

        $select_customer = "SELECT * FROM tb_customer WHERE id_customer = '" . $data_barang_masuk['id_supplier'] . "'";
        $query_customer = mysqli_query($conn, $select_customer);
        $data_customer = mysqli_fetch_array($query_customer);

        $tanggal_transaksi = date("Y-m-d", strtotime($data_barang_masuk['tgl_transaksi']));
        $nama_supplier = $data_customer['id_customer'] . " | " . $data_customer['nama_customer'];
        $keterangan = $data_barang_masuk['keterangan'];
    }
}

if (isset($_POST['simpan'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $ex_nm_supplier = explode(" | ", $nama_supplier);
    $id_supp = $ex_nm_supplier[0];
    $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);



    if ($id_transaksi == "new") {
        $id_transaksi = generate_barang_masuk_key("PO", "PO", date("m"), date("y"));



        $update = "UPDATE tb_barang_masuk SET
            id_transaksi = '" . $id_transaksi . "',
            id_supplier = '" . $id_supp . "',
            keterangan = '" . $keterangan . "',
            status = 'd',
            by_user_pajak = '" . $_SESSION['jenis_pajak'] . "' 
                WHERE id_transaksi = '" . $_SESSION['id_user'] . "'
        ";

        $query = mysqli_query($conn, $update);
    } else {
        $update = "UPDATE tb_barang_masuk SET
            id_supplier = '" . $id_supp . "',
            keterangan = '" . $keterangan . "',
            status = 'd',
            by_user_pajak = '" . $_SESSION['jenis_pajak'] . "' 
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";

        $query = mysqli_query($conn, $update);
    }

    header("location:master_barang_masuk_edit.php?id_transaksi=" . $id_transaksi);
}

if (isset($_POST['simpan_oto'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $ex_nm_supplier = explode(" | ", $nama_supplier);
    $id_supplier = $ex_nm_supplier[0];
    $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);


    $total = 0;

    if ($id_transaksi == "new") {
        $hasil = 0;
        $id_transaksi = generate_barang_masuk_key("PO", "PO", date("m"), date("y"));

        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $_SESSION['id_user'] . "'";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
            $select_stock = "SELECT * FROM tb_stock WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "'";
            $query_stock = mysqli_query($conn, $select_stock);
            $data_stock = mysqli_fetch_array($query_stock);
            $jum_stock = mysqli_num_rows($query_stock);

            if ($jum_stock < 1) {
                $id_stock = id_gen_stock();
                $ppn = $row_barang_masuk['total'] * $row_barang_masuk['ppn'] / 100;
                $total = $row_barang_masuk['total'];
                $hpp_return = (0 + $total) / (0 + $row_barang_masuk['qty']);

                $insert_stock = mysqli_query($conn, "INSERT INTO tb_stock(
                    id,
                    id_bahan,
                    berat,
                    stock,
                    uom
                ) VALUES(
                    '" . $id_stock . "',
                    '" . $row_barang_masuk['id_product'] . "',
                    '" . $row_barang_masuk['berat'] . "',
                    '" . $row_barang_masuk['qty'] . "',
                    '" . $row_barang_masuk['uom'] . "'
                )");
            } else {
                $total_stock = $data_stock['stock'] + $row_barang_masuk['qty'];


                $update_stock = mysqli_query($conn, "UPDATE tb_stock SET
                    stock = '" . $total_stock . "' WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "' AND uom = '" . $row_barang_masuk['uom'] . "'
                ");
            }
        }


        $update = "UPDATE tb_barang_masuk SET
            id_transaksi = '" . $id_transaksi . "',
            id_supplier = '" . $id_supplier . "',
            keterangan = '" . $keterangan . "',
            status = 's',
            by_user_pajak = '" . $_SESSION['jenis_pajak'] . "'
            WHERE id_transaksi = '" . $_SESSION['id_user'] . "'
        ";

        $query = mysqli_query($conn, $update);

        if ($query) {
            $msg_status = "Simpan & Otorisasi berhasil !";
        } else {
            $msg_status = "Simpan & Otorisasi gagal !";
        }

        $sql_get_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "'");
        while ($row_barang_masuk = mysqli_fetch_array($sql_get_barang_masuk)) {
            $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "'");
            $data_barang = mysqli_fetch_array($sql_get_barang);

            $total_qty = $data_barang['total_qty'];
            $total_berat = $data_barang['total_berat'];

            $total_input_qty = $total_qty + $row_barang_masuk['qty'];
            $total_input_berat = $total_berat + ($row_barang_masuk['qty'] * $row_barang_masuk['berat']);

            $sql_update_summ_barang = mysqli_query($conn, "
                UPDATE
                    tb_bahan
                SET
                    total_qty = '" . $total_input_qty . "',
                    total_berat = '" . $total_input_berat . "'
                WHERE
                    id_bahan = '" . $row_barang_masuk['id_product'] . "'
            ");
        }

        echo '
            <script>
                alert("' . $msg_status . '");
                window.location.href="master_barang_masuk.php";
            </script>
        ';
    } else {
        $hasil = 0;

        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "'";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
            $select_stock = "SELECT * FROM tb_stock WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "'";
            $query_stock = mysqli_query($conn, $select_stock);
            $data_stock = mysqli_fetch_array($query_stock);
            $jum_stock = mysqli_num_rows($query_stock);


            if ($jum_stock < 1) {
                $id_stock = id_gen_stock();
                $insert_stock = mysqli_query($conn, "INSERT INTO tb_stock(
                    id,
                    id_bahan,
                    berat,
                    stock
                ) VALUES(
                    '" . $id_stock . "',
                    '" . $row_barang_masuk['id_product'] . "',
                    '" . $row_barang_masuk['berat'] . "',
                    '" . $row_barang_masuk['qty'] . "'
                )");
            } else {
                $total_stock = $data_stock['stock'] + $row_barang_masuk['qty'];

                $update_stock = mysqli_query($conn, "UPDATE tb_stock SET
                    stock = '" . $total_stock . "'WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "'
                ");
            }
        }

        $update = "UPDATE tb_barang_masuk SET
            id_supplier = '" . $id_supplier . "',
            keterangan = '" . $keterangan . "',
            status = 's',
            by_user_pajak = '" . $_SESSION['jenis_pajak'] . "'
            WHERE id_transaksi = '" . $id_transaksi . "'
        ";

        $query = mysqli_query($conn, $update);

        $sql_get_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "'");
        while ($row_barang_masuk = mysqli_fetch_array($sql_get_barang_masuk)) {
            $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "'");
            $data_barang = mysqli_fetch_array($sql_get_barang);

            $total_qty = $data_barang['total_qty'];
            $total_berat = $data_barang['total_berat'];

            $total_input_qty = $total_qty + $row_barang_masuk['qty'];
            $total_input_berat = $total_berat + ($row_barang_masuk['qty'] * $row_barang_masuk['berat']);

            $sql_update_summ_barang = mysqli_query($conn, "
                UPDATE
                    tb_bahan
                SET
                    total_qty = '" . $total_input_qty . "',
                    total_berat = '" . $total_input_berat . "'
                WHERE
                    id_bahan = '" . $row_barang_masuk['id_product'] . "'
            ");
        }

        if ($query) {
            $msg_status = "Simpan & Otorisasi berhasil !";
        } else {
            $msg_status = "Simpan & Otorisasi gagal !";
        }
        echo '
            <script>
                alert("' . $msg_status . '");
                window.location.href="master_barang_masuk.php";
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

                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                        <div class="card shadow mb-4">
                            <h4 class="hk-sec-title" style="margin:1rem;">Master Barang Masuk</h4>

                            <div class="col-sm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Tanggal Transaksi</span>
                                                    </div>
                                                    <input type="date" name="tanggal_transaksi" id="" class="form-control filled-input form-control-sm tanggal_transaksi" value="<?= $tanggal_transaksi; ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Supplier</span>
                                                    </div>
                                                    <input type="text" name="nama_supplier" id="" class="form-control form-control-sm <?= $filled_input; ?> nama_supplier" value="<?= $nama_supplier; ?>" autocomplete="off" list="list_supplier" onclick="this.select()" <?= $readonly; ?>>
                                                    <datalist id="list_supplier" class="list_supplier">
                                                    </datalist>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text" id="inputGroup-sizing-sm">Keterangan</span>
                                            </div>
                                            <textarea name="keterangan" id="" style="height:33px;" class="form-control form-control-sm <?= $filled_input; ?> keterangan" <?= $readonly; ?> autocomplete="off" onclick="this.select();"><?= $keterangan; ?></textarea>
                                        </div>
                                    </div>




                                </div>
                            </div>

                            <div class="col-12">
                                <div class="row no-gutter">
                                    <div class="col-md-12 text-left">
                                        <h6 class="text-left">Detail Barang</h6>
                                    </div>
                                    <div class="col-md-6 mt-15">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama Barang</span>
                                            </div>
                                            <input type="text" name="" id="" class="form-control form-control-sm <?= $filled_input; ?> nama_barang" style="width:180px;" autocomplete="off" list="list_barang" onclick="this.select()" <?= $readonly; ?>>
                                            <datalist id="list_barang" class="list_barang">
                                            </datalist>
                                        </div>


                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text text-center" id="inputGroup-sizing-sm">Qty</span>
                                            </div>
                                            <input type="number" step="0.01" name="" id="" class="form-control form-control-sm <?= $filled_input; ?> qty_barang" autocomplete="off" onclick="this.select()" <?= $readonly; ?>>
                                        </div>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text text-center" id="inputGroup-sizing-sm">Harga</span>
                                            </div>
                                            <input type="text" name="" id="" class="form-control form-control-sm input_mask text-right <?= $filled_input; ?> harga_barang" autocomplete="off" onclick="this.select()" <?= $readonly; ?>>
                                        </div>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text text-center" id="inputGroup-sizing-sm">Berat</span>
                                            </div>
                                            <input type="number" step="0.01" name="" id="" class="form-control form-control-sm <?= $filled_input; ?> berat_barang" autocomplete="off" onclick="this.select()" <?= $readonly; ?>>
                                        </div>

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text text-center" id="inputGroup-sizing-sm">UOM</span>
                                            </div>
                                            <input name="" id="" class="form-control form-control-sm filled-input uom_barang" readonly>
                                            <button type="button" class="btn btn-sm btn-success ml-5 add_list" <?php echo $hide; ?>><i class="fa fa-plus"></i></button>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="row no-gutter">
                                <div class="col-md-10">
                                    <div class="input-group mb-3">

                                    </div>
                                </div>
                                <div class="col-md-2">
                                </div>

                            </div>

                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-1">
                                        <thead>
                                            <tr>
                                                <th class="text-center">ID Barang</th>
                                                <th class="text-center">Nama Barang</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Berat</th>
                                                <th class="text-center">UOM</th>
                                                <th class="text-center">Harga</th>
                                                <th class="text-center">Total</th>
                                                <th <?php echo $hide; ?>></th>
                                            </tr>
                                        </thead>
                                        <tbody class="daftar_bahan">
                                            <?php
                                            $total = 0;
                                            $select_barang_masuk = "SELECT a.*,b.id_bahan,b.nama_bahan FROM tb_barang_masuk a JOIN tb_bahan b ON b.id_bahan = a.id_product WHERE a.id_transaksi = '" . $id_transaksi . "'";
                                            $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
                                            $jum_barang_masuk = mysqli_num_rows($query_barang_masuk);
                                            while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
                                                $total = $total + $row_barang_masuk['total'];
                                                echo '
                                                <tr>
                                                    <td class="text-center">' . $row_barang_masuk['id_bahan'] . '</td>
                                                    <td class="text-center">' . $row_barang_masuk['nama_bahan'] . '</td>
                                                    <td class="text-center">' . $row_barang_masuk['qty'] . '</td>
                                                    <td class="text-center">' . $row_barang_masuk['berat'] . '</td>
                                                    <td class="text-center">' . $row_barang_masuk['uom'] . '</td>
                                                    <td class="text-center">' . number_format($row_barang_masuk['harga']) . '</td>
                                                    <td class="text-center"> ' . number_format($row_barang_masuk['total']) . '</td>
                                                    ';

                                                if (!isset($_GET['detail'])) {
                                                    echo '
                                                            <td class="text-center">
                                                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $row_barang_masuk['no_id'] . '" data-id="' . $row_barang_masuk['no_id'] . '"><i class="fa fa-trash-o"></i></a>
                                                            </td>
                                                        ';
                                                }

                                                echo '
                                                </tr>
                                                ';
                                            }

                                            if ($jum_barang_masuk > 0) {
                                                $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "'";
                                                $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
                                                $data_barang_masuk = mysqli_fetch_array($query_barang_masuk);

                                                $ppn = $total * $data_barang_masuk['ppn'] / 100;

                                                echo '
                                                <tr>
                                                    <th class="text-right" colspan="6">Subtotal</th>
                                                    <th class="text-right" colspan="2"> ' . number_format($total) . '</th>
                                                </tr>
                                                ';

                                                if (!isset($_GET['detail']) && $data_barang_masuk['by_user_pajak'] == 1) {
                                                    echo '
                                                        <tr>
                                                            <th class="text-right" colspan="6">PPN <input type="number" style="max-width:50px;" name="" class="text-right ppn_input" data-id_transaksi="' . $id_transaksi . '" min="0" max="100" value="' . $data_barang_masuk['ppn'] . '" autocomplete="off" onclick="this.select();"> % </th>
                                                            <th class="text-right" colspan="2"> ' . number_format($ppn) . '</th>
                                                        </tr>
                                                    ';
                                                } else {
                                                    if ($_SESSION['jenis_pajak'] == 1) {
                                                        echo '
                                                            <tr>
                                                                <th class="text-right" colspan="6">PPN ' . $data_barang_masuk['ppn'] . ' % </th>
                                                                <th class="text-right" colspan="2"> ' . number_format($ppn) . '</th>
                                                            </tr>
                                                        ';
                                                    }
                                                }

                                                echo '
                                                <tr>
                                                    <th class="text-right" colspan="6">Grand Total</th>
                                                    <th class="text-right" colspan="2"> ' . number_format($total + $ppn) . '</th>
                                                </tr>
                                                ';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                                if (isset($_GET['detail'])) {
                                    echo '
                                    <div class="col-sm">
                                        <div class="row">
                                            <a href="master_barang_masuk.php" class="btn btn-primary mb-2"><i class="fa fa-arrow-left"></i> Kembali</a>
                                            <a href="print/print_invoice_barang_masuk.php?id_transaksi=' . $id_transaksi . '" class="btn btn-success ml-2 mb-2" target="_blank"><i class="fa fa-print"></i> Print Invoice</a>
                                        </div>
                                    </div>
                                    ';
                                } else {
                                    echo '
                                    <div class="col-sm">
                                        <div class="row">
                                            <a href="master_barang_masuk.php" class="btn btn-sm btn-danger mb-2"> Kembali</a>
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

        $(document).on("keyup", ".nama_barang", function() {
            var nama_barang = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_masuk.php",
                data: {
                    "cari_nama_barang": nama_barang
                },
                cache: true,
                success: function(result) {
                    $(".list_barang").html(result);
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

        $(document).on("click", ".add_list", function() {
            var id_transaksi = "<?php echo mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var nama_barang = $(".nama_barang").val();
            var qty_barang = $(".qty_barang").val();
            var harga_barang = $(".harga_barang").val();
            if (harga_barang == "") {
                var harga_barang = 0;
            } else {
                var harga_barang = harga_barang.split(",").join("");
                var harga_barang = parseFloat(harga_barang);
            }
            var berat_barang = $(".berat_barang").val();
            var uom_barang = $(".uom_barang").val();

            if (nama_barang.indexOf("|") >= 0 && (qty_barang !== "" && qty_barang > 0) && (harga_barang !== "" && harga_barang > 0)) {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_barang_masuk.php",
                    data: {
                        "add_list": id_transaksi,
                        "nama_barang": nama_barang,
                        "qty_barang": qty_barang,
                        "harga_barang": harga_barang,
                        "berat_barang": berat_barang,
                        "uom_barang": uom_barang
                    },
                    cache: true,
                    beforeSend: function(response) {
                        $(".preloader-it").show();
                    },
                    success: function(result) {
                        $(".preloader-it").hide();
                        $(".daftar_bahan").html(result);

                        $(".nama_barang").val("");
                        $(".qty_barang").val("");
                        $(".harga_barang").val("");
                        $(".berat_barang").val("");
                        $(".uom_barang").val("");
                    }
                });
            } else {
                alert("Mohon pilih barang sesuai database !");
            }
        });

        $(document).on("click", ".del_list", function() {
            var id_hapus = $(this).data('id');
            var id_hapus_transaksi = "<?= $_GET['id_transaksi']; ?>";
            var id_cabang = "<?= $_GET['id_cabang']; ?>";

            $(".del_list_" + id_hapus).removeClass("del_list");
            $(".del_list_" + id_hapus).removeClass("btn-danger");
            $(".del_list_" + id_hapus).addClass("btn-secondary");
            $(".del_list_" + id_hapus).html("<i class='fa fa-spinner fa-spin fa-fw'></i>");

            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_masuk.php",
                data: {
                    "id_hapus": id_hapus,
                    "id_hapus_transaksi": id_hapus_transaksi,
                    "id_cabang": id_cabang
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
            var nama_supplier = $(".nama_supplier").val();
            var tanggal_transaksi = $(".tanggal_transaksi").val();
            var keterangan = $(".keterangan").val();
            if (nama_supplier == "") {
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
                        "nama_supplier": nama_supplier,
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
            var nama_supplier = $(".nama_supplier").val();
            var tanggal_transaksi = $(".tanggal_transaksi").val();
            var keterangan = $(".keterangan").val();



            if (nama_supplier == "") {
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
                        "nama_supplier": nama_supplier,
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