<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

$id_beban = generate_beban();



if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$select_keluar = "SELECT a.* FROM tb_barang_keluar a WHERE a.id_transaksi = '" . $_SESSION['id_user'] . "' AND a.status_keluar = '' ";
$query_keluar = mysqli_query($conn, $select_keluar);
while ($row_keluar = mysqli_fetch_array($query_keluar)) {

    $delete = mysqli_query($conn, "DELETE FROM tb_barang_keluar WHERE id_transaksi = '" . $_SESSION['id_user'] . "' AND status_keluar = ''");
}

$tambah_baru = '<a href="master_barang_po_edit.php?id_transaksi=new" class="btn btn-primary form-control-sm"><i class="fa fa-plus"></i> Tambah Baru</a>';

if (isset($_POST['delete'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus']);

    $msg = "Hapus Invoice Berhasil";

    $sql_barang_keluar = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'");
    while ($row_barang_keluar = mysqli_fetch_array($sql_barang_keluar)) {
        $id_bahan = $row_barang_keluar['id_bahan'];
        $qty = $row_barang_keluar['qty'];
        $berat = $row_barang_keluar['berat'];

        $sql_stock_barang = mysqli_query($conn, "SELECT * FROM tb_stock WHERE id_bahan = '" . $id_bahan . "' AND berat = '" . $berat . "'");
        while ($row_stock_barang = mysqli_fetch_array($sql_stock_barang)) {
            $stock = $row_stock_barang['stock'];
            $grand_stock = $stock + $qty;

            $sql_update_stock = mysqli_query($conn, "
                UPDATE
                    tb_stock
                SET
                    stock = '" . $grand_stock . "'
                WHERE
                    id_bahan = '" . $id_bahan . "' AND
                    berat = '" . $berat . "'
            ");
        }

        $sql_stock_summ_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
        while ($row_stock_summ_bahan = mysqli_fetch_array($sql_stock_summ_barang)) {
            $total_qty = $row_stock_summ_bahan['total_qty'];
            $grand_qty = $total_qty + $qty;

            $total_berat = $row_stock_summ_bahan['total_berat'];
            $grand_berat = $total_berat + ($qty * $berat);

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

    $sql_del_invoice = mysqli_query($conn, "DELETE FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'");


    echo '
        <script>alert("Hapus invoice berhasil !");window.location.href="master_barang_keluar.php";</script>
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

$from = date("Y-m-d");
$to = date("Y-m-d");
$cabang = "";
$source_customer = "";
if (isset($_POST['search'])) {
    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    $source_customer = mysqli_real_escape_string($conn, $_POST['source_customer']);
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
                                <h3 class="mr-2">PO Customer</h3>
                                <?= $tambah_baru; ?>
                            </div>
                        </div>
                        <div class="card-header py-1">
                            <div class="" <?php echo $hidden; ?>>
                                <form action="" method="post">
                                    <br>
                                    <div class="row no-gutters">
                                        <div class="form-group form-inline">
                                            <input type="date" name="tgl_from" id="" class="form-control form-control-sm " value="<?= $from; ?>" required>
                                            <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                            <input type="date" name="tgl_to" id="" class="form-control form-control-sm " value="<?= $to; ?>" required>

                                            <button type="submit" class="mt--5 btn btn-primary form-control-sm ml-1" name="search"><i class="fa fa-search"></i> Cari</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No. Transaksi</th>
                                            <th class="text-center">Tgl PO</th>
                           
                                            <th class="text-center">Customer</th>
                                            <th class="text-center">Keterangan</th>
                                            <th class="text-center">Tipe</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $where_tgl = "";
                                        if (isset($_POST['search'])) {
                                            $where_tgl = " AND a.tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "'";
                                        }

                                        $sql_get_barang_keluar = mysqli_query($conn, "SELECT a.* FROM tb_barang_po a WHERE a.id_transaksi LIKE '%PO%' AND by_user_pajak = '" . $_SESSION['jenis_pajak'] . "' " . $where_tgl . "  GROUP BY a.id_transaksi ORDER BY a.id_transaksi DESC LIMIT 50");
                                        while ($row_barang_keluar = mysqli_fetch_array($sql_get_barang_keluar)) {
                                            $id_transaksi = $row_barang_keluar['id_transaksi'];
                                            $tgl_faktur = $row_barang_keluar['tgl_transaksi'];
                                            $tgl_jatuh_tempo = $row_barang_keluar['tgl_jatuh_tempo'];
                                            $id_customer = $row_barang_keluar['id_customer'];
                                            $keterangan = $row_barang_keluar['keterangan'];
                                            $status_keluar = $row_barang_keluar['status_keluar'];
                                            $non_stock = $row_barang_keluar['non_stock'];

                                            $sts = '<span class="text-warning">Draft</span>';
                                            if ($status_keluar == "s") {
                                                $sts = '<span class="text-danger">Close</span>';
                                            }

                                            $nama_customer = "";
                                            $sql_get_customer = mysqli_query($conn, "SELECT nama_customer FROM tb_customer WHERE id_customer = '" . $id_customer . "'");
                                            while ($row_customer = mysqli_fetch_array($sql_get_customer)) {
                                                $nama_customer = $row_customer['nama_customer'];
                                            }

                                            $btn_edit = "btn-success";
                                            $link_edit = "master_barang_po_edit.php?id_transaksi=" . $id_transaksi;
                                            $edit_text  = "<i class='fa fa-pencil'></i> Ubah";
                                            if ($status_keluar == "s") {
                                                $btn_edit = "btn-primary";
                                                $link_edit = "master_barang_po_edit.php?id_transaksi=" . $id_transaksi . "&view=detail";
                                                $edit_text = "<i class='fa fa-eye'></i> Lihat";
                                            }

                                            $tipe = "Stock";
                                            if ($non_stock == 1) {
                                                $tipe = "Non-Stock";
                                            }

                                            echo '
                                                <tr>
                                                    <td class="text-center fs-11">' . $id_transaksi . '</td>
                                                    <td class="text-center fs-11">' . date("d-m-Y", strtotime($tgl_faktur)) . '</td>
                                           
                                                    <td class="text-center fs-11">' . $nama_customer . '</td>
                                                    <td class="fs-11">' . $keterangan . '</td>
                                                    <td class="text-center fs-11">' . $tipe . '</td>
                                                    <td class="text-center fs-11">' . $sts . '</td>
                                                    <td class="text-center fs-11">
                                                        <a href="' . $link_edit . '" class="btn btn-sm ' . $btn_edit . '">' . $edit_text . '</a>
                                                        <button type="button" class="btn btn-sm btn-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal" data-id_transaksi="' . $id_transaksi . '"><i class="fa fa-trash-o"></i> Hapus</button>
                                                    </td>
                                                </tr>
                                            ';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Invoice</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin hapus invoice ini ?
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

        $(document).on("click", ".choose_branch", function() {
            var branch = $(".branch_modal").val();
            window.location.replace("master_barang_keluar_edit.php?id_transaksi=new&id_cabang=" + branch);
        });

        $(document).on("click", ".hapus_button", function() {
            var id_transaksi = $(this).data('id_transaksi');
            $(".id_hapus").val(id_transaksi);
        });

    });
</script>