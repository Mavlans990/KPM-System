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
        $select_opname = "SELECT * FROM inv_adjust_in WHERE id_inv_in = '" . mysqli_real_escape_string($conn, $_GET['id_transaksi']) . "' GROUP BY id_inv_in";
        $query_opname = mysqli_query($conn, $select_opname);
        $data_opname = mysqli_fetch_array($query_opname);

        $tanggal_transaksi = $data_opname['inv_date'];
        $keterangan = $data_opname['keterangan'];
    }
}

if (isset($_POST['simpan'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);



    if ($id_transaksi == "new") {
        $id_transaksi = id_opname();

        $update = "UPDATE inv_adjust_in SET
            id_inv_in = '" . $id_transaksi . "',
            inv_date = '" . $tanggal_transaksi . "',
            keterangan = '" . $keterangan . "',
            status = 'd',
            by_user_pajak = '" . $_SESSION['jenis_pajak'] . "' 
                WHERE id_inv_in = '" . $_SESSION['id_user'] . "'
        ";

        $query = mysqli_query($conn, $update);
    } else {
        $update = "UPDATE inv_adjust_in SET
            keterangan = '" . $keterangan . "',
            inv_date = '" . $tanggal_transaksi . "',
            status = 'd',
            by_user_pajak = '" . $_SESSION['jenis_pajak'] . "' 
            WHERE id_inv_in = '" . $id_transaksi . "'
        ";

        $query = mysqli_query($conn, $update);
    }

    header("location:inventory_adjust_in.php?id_transaksi=" . $id_transaksi);
}

if (isset($_POST['simpan_oto'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
    $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);


    $total = 0;

    if ($id_transaksi == "new") {
        $hasil = 0;
        $id_transaksi = id_opname();

        $update = "UPDATE inv_adjust_in SET
            id_inv_in = '" . $id_transaksi . "',
            keterangan = '" . $keterangan . "',
            status = 's',
            by_user_pajak = '" . $_SESSION['jenis_pajak'] . "'
            WHERE id_inv_in = '" . $_SESSION['id_user'] . "'
        ";
        // echo $update . '<br>';
        $query = mysqli_query($conn, $update);

        $sql_get_opname = mysqli_query($conn, "SELECT * FROM inv_adjust_in WHERE id_inv_in = '" . $id_transaksi . "'");
        while ($row_opname = mysqli_fetch_array($sql_get_opname)) {
            $sql_get_stock = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $row_opname['id_product'] . "' AND barcode = '" . $row_opname['barcode'] . "'");
            $data_stock = mysqli_fetch_array($sql_get_stock);
            $stock = $data_stock['bruto'];

            $total_stock = $stock + $row_opname['stock_opname'];
            if ($total_stock <= 0) {
                $sql_update_stock = mysqli_query($conn, "
                UPDATE
                    tb_barang_masuk
                SET
                    bruto = '" . $total_stock . "',
                    terpakai = '" . $row_opname['id_inv_in'] . "'
                WHERE
                    id_bahan_kain = '" . $row_opname['id_product'] . "' AND
                    barcode = '" . $row_opname['barcode'] . "'
            ");
                //         echo "
                //     UPDATE
                //             tb_barang_masuk
                //         SET
                //             netto = '" . $total_stock . "',
                //             terpakai = '" . $row_opname['id_inv_in'] . "'
                //         WHERE
                //             id_bahan_kain = '" . $row_opname['id_product'] . "' AND
                //             barcode = '" . $row_opname['barcode'] . "'
                // <br>";
            } else {
                $sql_update_stock = mysqli_query($conn, "
                    UPDATE
                        tb_barang_masuk
                    SET
                        bruto = '" . $total_stock . "',
                        terpakai = '',
                        keterangan = 'Adjustment di " . $row_opname['id_inv_in'] . "'
                    WHERE
                        id_bahan_kain = '" . $row_opname['id_product'] . "' AND
                        barcode = '" . $row_opname['barcode'] . "'
                ");
                //         echo "
                //             UPDATE
                //                 tb_barang_masuk
                //             SET
                //                 netto = '" . $total_stock . "',
                //                 keterangan = 'Adjustment di " . $row_opname['id_inv_in'] . "'
                //             WHERE
                //                 id_bahan_kain = '" . $row_opname['id_product'] . "' AND
                //                 barcode = '" . $row_opname['barcode'] . "'
                // <br>";
            }

            // $sql_update_summ_stock = mysqli_query($conn, "
            //     UPDATE
            //         tb_bahan
            //     SET
            //         total_qty = total_qty + '" . $row_opname['stock_opname'] . "',
            //         total_berat = total_berat + '" . ($row_opname['stock_opname'] * $row_opname['berat']) . "'
            //     WHERE
            //         id_bahan = '" . $row_opname['id_product'] . "'
            // ");
        }

        if ($query) {
            $msg_status = "Simpan & Otorisasi berhasil !";
        } else {
            $msg_status = "Simpan & Otorisasi gagal !";
        }

        // return 0;

        echo '
            <script>
                alert("' . $msg_status . '");
                window.location.href="inventory_adjust_in_view.php";
            </script>
        ';
    } else {
        $update = "UPDATE inv_adjust_in SET
            keterangan = '" . $keterangan . "',
            status = 's',
            by_user_pajak = '" . $_SESSION['jenis_pajak'] . "'
            WHERE id_inv_in = '" . $id_transaksi . "'
        ";
        // echo $update . '<br>';
        $query = mysqli_query($conn, $update);

        $sql_get_opname = mysqli_query($conn, "SELECT * FROM inv_adjust_in WHERE id_inv_in = '" . $id_transaksi . "'");
        while ($row_opname = mysqli_fetch_array($sql_get_opname)) {
            $sql_get_stock = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $row_opname['id_product'] . "' AND barcode = '" . $row_opname['barcode'] . "'");
            $data_stock = mysqli_fetch_array($sql_get_stock);
            $stock = $data_stock['bruto'];

            $total_stock = $stock + $row_opname['stock_opname'];
            if ($total_stock == 0) {
                $sql_update_stock = mysqli_query($conn, "
                UPDATE
                    tb_barang_masuk
                SET
                    bruto = '" . $total_stock . "',
                    terpakai = '" . $row_opname['id_inv_in'] . "'
                    WHERE
                    id_bahan_kain = '" . $row_opname['id_product'] . "' AND
                    barcode = '" . $row_opname['barcode'] . "'
                    ");
                    // netto = '" . $total_stock . "',
                //         echo "
                //     UPDATE
                //             tb_barang_masuk
                //         SET
                //             netto = '" . $total_stock . "',
                //             terpakai = '" . $row_opname['id_inv_in'] . "'
                //         WHERE
                //             id_bahan_kain = '" . $row_opname['id_product'] . "' AND
                //             barcode = '" . $row_opname['barcode'] . "'
                // <br>";
            } else {
                $sql_update_stock = mysqli_query($conn, "
                    UPDATE
                        tb_barang_masuk
                    SET
                        bruto = '" . $total_stock . "',
                        terpakai = '',
                        keterangan = 'Adjustment di " . $row_opname['id_inv_in'] . "'
                    WHERE
                        id_bahan_kain = '" . $row_opname['id_product'] . "' AND
                        barcode = '" . $row_opname['barcode'] . "'
                ");
                //         echo "
                //             UPDATE
                //                 tb_barang_masuk
                //             SET
                //                 netto = '" . $total_stock . "',
                //                 keterangan = 'Adjustment di " . $row_opname['id_inv_in'] . "'
                //             WHERE
                //                 id_bahan_kain = '" . $row_opname['id_product'] . "' AND
                //                 barcode = '" . $row_opname['barcode'] . "'
                // <br>";
            }

            // $sql_update_summ_stock = mysqli_query($conn, "
            //     UPDATE
            //         tb_bahan
            //     SET
            //         total_qty = total_qty + '" . $row_opname['stock_opname'] . "',
            //         total_berat = total_berat + '" . ($row_opname['stock_opname'] * $row_opname['berat']) . "'
            //     WHERE
            //         id_bahan = '" . $row_opname['id_product'] . "'
            // ");
        }

        if ($query) {
            $msg_status = "Simpan & Otorisasi berhasil !";
        } else {
            $msg_status = "Simpan & Otorisasi gagal !";
        }

        // return 0;
        echo '
            <script>
                alert("' . $msg_status . '");
                window.location.href="inventory_adjust_in_view.php";
            </script>
        ';
    }
}

$hide = "";
$readonly = "";
$filled_input = "";
if (isset($_GET['view'])) {
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
                            <h4 class="hk-sec-title" style="margin:1rem;">Stock Opname</h4>

                            <div class="col-sm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row no-gutter">
                                            <div class="col-12">
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Tanggal Opname</span>
                                                    </div>
                                                    <input type="date" name="tanggal_transaksi" id="" class="form-control filled-input form-control-sm tanggal_transaksi" value="<?= $tanggal_transaksi; ?>" readonly>
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

                            <div class="col-12" <?= $hide; ?>>
                                <div class="row no-gutter">
                                    <div class="col-md-12 text-left">
                                        <h6 class="text-left">Detail Barang</h6>
                                    </div>
                                    <div class="col-md-6 mt-15">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama Bahan</span>
                                            </div>
                                            <input type="text" name="" id="" class="form-control form-control-sm <?= $filled_input; ?> nama_barang" style="width:180px;" autocomplete="off" list="list_barang" onclick="this.select()" <?= $readonly; ?>>
                                            <datalist id="list_barang" class="list_barang">
                                            </datalist>
                                        </div>
                                        <!-- <div class="input-group mb-3">
                                                <div class="input-group-prepend text-center">
                                                    <span style="width:150px;" class="form-control form-control-sm input-group-text text-center" id="inputGroup-sizing-sm">LOT | ROLL</span>
                                                </div>
                                                <select name="" class=" form-control form-control-sm berat_barang <?= $filled_input; ?>" <?= $readonly; ?>></select>
                                            </div> -->
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend text-center">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text text-center" id="inputGroup-sizing-sm" >Barcode</span>
                                            </div>
                                            <input type="text" name="" id="" class="form-control form-control-sm <?= $filled_input; ?> berat_barang" style="width:180px;" autocomplete="off" list="list_barcode" onclick="this.select()" <?= $readonly; ?>>
                                            <datalist id="list_barcode" class="list_barcode" <?= $readonly; ?>></datalist>
                                            <button type="button" class="btn btn-sm btn-success ml-5 add_list" <?php echo $hide; ?>><i class="fa fa-plus"></i></button>
                                        </div>
                                        <div class="input-group mb-3">
                                            <!-- <div class="input-group-prepend text-center">
                                                <span style="width:150px;" class="form-control form-control-sm input-group-text text-center" id="inputGroup-sizing-sm">UOM</span>
                                            </div>
                                            <select name="" id="" class="form-control form-control-sm uom_barang <?= $filled_input; ?>" <?= $readonly; ?>>
                                            </select> -->
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
                                                <th class="text-center">Barcode</th>
                                                <th class="text-center">Qty Awal</th>
                                                <th class="text-center">Qty Baru</th>
                                                <th class="text-center">Qty Opname</th>
                                                <th <?php echo $hide; ?>></th>
                                            </tr>
                                        </thead>
                                        <tbody class="daftar_bahan">
                                            <?php
                                            $sql_get_opname = mysqli_query($conn, "SELECT * FROM inv_adjust_in WHERE id_inv_in = '" . $id_transaksi . "'");
                                            while ($row_opname = mysqli_fetch_array($sql_get_opname)) {
                                                $inv_in_id = $row_opname['inv_in_id'];
                                                $id_product = $row_opname['id_product'];
                                                $nm_barang = "";
                                                $sql_get_barang = mysqli_query($conn, "SELECT nama_kain FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_product . "'");
                                                if ($row_barang = mysqli_fetch_array($sql_get_barang)) {
                                                    $nm_barang = $row_barang['nama_kain'];
                                                }
                                                $stock_awal = $row_opname['stock_awal'];
                                                $stock_change = $row_opname['stock_change'];
                                                $stock_opname = $row_opname['stock_opname'];
                                                $barcode = $row_opname['barcode'];

                                                echo '
                                                <tr>
                                                <td class="text-center">' . $id_product . '</td>
                                                <td class="text-center">' . $nm_barang . '</td>
                                                <td class="text-center">' . $barcode . '</td>
                                                <td class="text-center">' . $stock_awal . '</td>
                                                            ';

                                                if (isset($_GET['detail'])) {
                                                    echo '
                                                        <td class="text-center">
                                                            ' . $stock_change . '
                                                        </td>
                                                    ';
                                                } else {
                                                    echo '
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm opname_input text-right" data-inv_in_id="' . $inv_in_id . '" value="' . $stock_change . '" '.$readonly.'>
                                                        </td>
                                                    ';
                                                }

                                                echo '
                                                            <td class="text-center">' . $stock_opname . '</td>
                                                            ';

                                                if (!isset($_GET['detail'])) {
                                                    echo '
                                                                    <td class="text-center">
                                                                        <button type="button" class="btn btn-sm btn-danger del_opname" data-inv_in_id="' . $inv_in_id . '"><i class="fa fa-trash-o"></i> Hapus</button>
                                                                    </td>
                                                                ';
                                                }

                                                echo '
                                                        </tr>
                                                    ';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-sm">
                                    <div class="row">
                                        <a href="inventory_adjust_in_view.php" class="btn btn-sm btn-danger mb-2"> Kembali</a>
                                        <button type="submit" name="simpan" class="btn btn-sm btn-success ml-2 mb-2" <?= $hide; ?>>Simpan</button>
                                        <button type="submit" name="simpan_oto" class="btn btn-sm btn-warning mb-2 ml-2" <?= $hide; ?>>Simpan & Otorisasi</button>
                                    </div>
                                </div>
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

        $(".input_mask").mask('#,##0', {
            reverse: true
        });

        $(document).on("keyup", ".nama_barang", function() {
            var nama_barang = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_stock_opname.php",
                data: {
                    "cari_nama_barang": nama_barang
                },
                cache: true,
                success: function(result) {
                    $(".list_barang").html(result);
                }
            });
        });

        $(document).on("change", ".nama_barang", function() {
            var nama_barang = $(this).val();
            // alert(nama_barang);
            $.ajax({
                type: "POST",
                url: "ajax/ajax_stock_opname.php",
                data: {
                    "get_stock_berat": nama_barang
                },
                cache: true,
                success: function(result) {
                    // alert(result);
                    var result = result.split("|");
                    $(".list_barcode").html(result[0]);
                    $(".uom_barang").html(result[1]);
                }
            });
        });

        $(document).on("change", ".opname_input", function() {
            var id_transaksi = "<?= mysqli_real_escape_string($conn, $_GET['id_transaksi']); ?>";
            var inv_in_id = $(this).data('inv_in_id');
            var input_opname = $(this).val();

            $.ajax({
                type: "POST",
                url: "ajax/ajax_stock_opname.php",
                data: {
                    "opname_input": id_transaksi,
                    "inv_in_id": inv_in_id,
                    "input_opname": input_opname
                },
                cache: true,
                beforeSend: function(response) {
                    $(".preloader-it").show();
                },
                success: function(result) {
                    $(".preloader-it").hide();
                    $(".daftar_bahan").html(result);
                }
            });
        });

        $(document).on("click", ".add_list", function() {
            var id_transaksi = "<?= $id_transaksi; ?>";
            var nama_barang = $(".nama_barang").val();
            var berat_barang = $(".berat_barang").val();
            // var uom_barang = $(".uom_barang").val();

            // alert(nama_barang + " | " + berat_barang);
            if (id_transaksi == "" || berat_barang == "") {
                alert("Mohon isi lengkap detail barang !");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_stock_opname.php",
                    data: {
                        "add_list": id_transaksi,
                        "nama_barang": nama_barang,
                        "berat_barang": berat_barang
                    },
                    cache: true,
                    beforeSend: function(response) {
                        $(".preloader-it").show();
                    },
                    success: function(result) {
                        $(".preloader-it").hide();
                        // alert(result);
                        var result = result.split("|");
                        if (result[0] == 0) {
                            alert("Maaf, stock barang ini belum terdaftar !");
                        }
                        $(".daftar_bahan").html(result[1]);
                        // alert(result[2]);
                    }
                });
            }
        });

        $(document).on("click", ".del_opname", function() {
            var id_transaksi = "<?= mysqli_real_escape_string($conn, $_GET['id_transaksi']) ?>";
            var inv_in_id = $(this).data('inv_in_id');

            $.ajax({
                type: "POST",
                url: "ajax/ajax_stock_opname.php",
                data: {
                    "del_opname": id_transaksi,
                    "inv_in_id": inv_in_id
                },
                cache: true,
                beforeSend: function(response) {
                    $(".preloader-it").show();
                },
                success: function(result) {
                    $(".preloader-it").hide();
                    $(".daftar_bahan").html(result);
                }
            });
        });
    });
</script>