<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$hidn = "hidden";
if ($_SESSION['grup'] == 'super' || $_SESSION['grup'] == 'franchise') {
    $hidn = "";
}

$from = date("Y-m-d");
$to = date("Y-m-d");
$supp = "";
$bah = "";
$cabang = '';
$idcabang = '';
$non_stock = "";
if (isset($_POST['search'])) {
    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    $supp = mysqli_real_escape_string($conn, $_POST['supplier']);
    $bah = mysqli_real_escape_string($conn, $_POST['bahan']);
    $non_stock = mysqli_real_escape_string($conn, $_POST['non_stock']);

    $cabang = '';
    $idcabang = $_SESSION['branch'];
    if (isset($_POST['cabang'])) {
        if ($_POST['cabang'] != '') {
            $x = 1;
            foreach ($_POST['cabang'] as $selectedOption) {
                if ($x == 1) {
                    $cabang = "'" . $selectedOption . "'";
                } else {
                    $cabang = $cabang . ",'" . $selectedOption . "'";
                }
                $x++;
            }

            $y = 1;
            foreach ($_POST['cabang'] as $selection) {
                if ($y == 1) {
                    $idcabang = $selection;
                } else {
                    $idcabang = $idcabang . "|" . $selection;
                }
                $y++;
            }
        }
    }
}

$required = "";
if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
    $required = "required";
}

$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
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
                        <div class="card-header py-1" style="text-transform: none;">
                            <div class="form-group mt-5">
                                <h1 class="h3 mb-2 text-gray-800">Laporan Barang Keluar</h1>
                            </div>

                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group form-inline">
                                            <input type="date" name="tgl_from" id="" class="form-control form-control-sm" value="<?= $from; ?>" required>
                                            <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                            <input type="date" name="tgl_to" id="" class="form-control form-control-sm" value="<?= $to; ?>" required>
                                            <input type="text" name="supplier" id="supplier" class="form-control form-control-sm mt--5" placeholder="Nama Customer" value="<?php echo $supp; ?>">
                                            <select name="bahan" id="" class="form-control form-control-sm mt--5">
                                                <option value="">-- Pilih Bahan --</option>
                                                <?php
                                                $select_bahan = "SELECT * FROM tb_bahan ORDER BY id_bahan ASC";
                                                $query_bahan = mysqli_query($conn, $select_bahan);
                                                while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                                                    $selected = "";
                                                    if ($bah == $row_bahan['id_bahan']) {
                                                        $selected = "selected";
                                                    }
                                                    echo '
                                                        <option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>
                                                    ';
                                                }
                                                ?>
                                            </select>
                                            <select name="non_stock" class="form-control form-control-sm mt--5">
                                                <option value="">-- Stock / Non --</option>
                                                <option value="0" <?php if ($non_stock == 0 && $non_stock !== "") {
                                                                        echo "selected";
                                                                    } ?>>Stock</option>
                                                <option value="1" <?php if ($non_stock == 1 && $non_stock !== "") {
                                                                        echo "selected";
                                                                    } ?>>Non-Stock</option>
                                            </select> <br>
                                        </div>
                                        <div class="form-group form-inline">
                                            <button type="submit" class="btn btn-primary form-control-sm mt--5" name="search"><i class="fa fa-search"></i> Cari</button>
                                            <?php
                                            if (isset($_POST['search'])) {
                                                echo '
                                                        <a href="print/print_laporan_barang_keluar.php?tgl_from=' . $from . '&tgl_to=' . $to . '&supplier=' . $supp . '&bahan=' . $bah . '&cabang=' . $idcabang . '&nonstock=' . $non_stock . '" class="btn btn-success text-white form-control-sm mt--5 ml-1" target="_blank"><i class="fa fa-print"></i> Print</a>
                                                        <a href="print/print_excel_keluar.php?tgl_from=' . $from . '&tgl_to=' . $to . '&supplier=' . $supp . '&bahan=' . $bah . '&cabang=' . $idcabang . '&nonstock=' . $non_stock . '" target="_blank" class="btn btn-warning text-white form-control-sm mt--5 ml-1"><i class="fa fa-list"></i> Download Excel</a>
                                                    ';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="printdivcontent">
                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">No. Transaksi</th>
                                            <th class="text-center">Item</th>
                                            <th class="text-right">Qty</th>
                                            <th class="text-center">UOM</th>
                                            <th class="text-right">Price</th>
                                            <th class="text-right">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_POST['search'])) {
                                            $where_branch = "";

                                            $tgl_from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
                                            $tgl_to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
                                            $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
                                            $bahan = mysqli_real_escape_string($conn, $_POST['bahan']);

                                            $filter_supplier = "";
                                            $filter_bahan = "";
                                            $filter_cabang = "";
                                            $filter_non_stock = "";

                                            if ($supplier !== "") {
                                                $supp = "";
                                                $query_supp = mysqli_query($conn, "SELECT id_customer,nama_customer FROM tb_customer WHERE nama_customer LIKE '%" . $supplier . "%'");
                                                while ($row_supp = mysqli_fetch_array($query_supp)) {
                                                    $supp = $supp . ",'" . $row_supp['id_customer'] . "'";
                                                }
                                                $filter_supplier = " AND id_customer IN (''" . $supp . ")";
                                            }
                                            if ($bahan !== "") {
                                                $filter_bahan = " AND id_bahan = '" . $bahan . "'";
                                            }

                                            if ($non_stock !== "") {
                                                $filter_non_stock = " AND non_stock = '" . $non_stock . "'";
                                            }

                                            $grand_total = 0;

                                            $select_keluar = "SELECT * FROM tb_barang_keluar WHERE (tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "') " . $filter_supplier . " " . $filter_bahan . " " . $filter_cabang . " " . $filter_non_stock . " and status_keluar <> 'd' GROUP BY id_transaksi";

                                            $source = "";
                                            $query_keluar = mysqli_query($conn, $select_keluar);
                                            while ($row_keluar = mysqli_fetch_array($query_keluar)) {

                                                $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $row_keluar['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang . $where_branch . $filter_non_stock;
                                                $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
                                                $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);

                                                $select_supplier = "SELECT * FROM tb_customer WHERE id_customer = '" . $row_keluar['id_customer'] . "'";
                                                $query_supplier = mysqli_query($conn, $select_supplier);
                                                $data_supplier = mysqli_fetch_array($query_supplier);

                                                $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_keluar['id_cabang'] . "'";
                                                $query_cabang = mysqli_query($conn, $select_cabang);
                                                $data_cabang = mysqli_fetch_array($query_cabang);

                                                if ($row_keluar['source_customer'] == "facebook") {
                                                    $source = "Source : Facebook <br>";
                                                }
                                                if ($row_keluar['source_customer'] == "instagram") {
                                                    $source = "Source : Instagram <br>";
                                                }
                                                if ($row_keluar['source_customer'] == "google") {
                                                    $source = "Source : Google <br>";
                                                }
                                                if ($row_keluar['source_customer'] == "marketplace") {
                                                    $source = "Source : Market Place <br>";
                                                }
                                                if ($row_keluar['source_customer'] == "olx") {
                                                    $source = "Source : OLX <br>";
                                                }
                                                if ($row_keluar['source_customer'] == "youtube") {
                                                    $source = "Source : Youtube <br>";
                                                }
                                                if ($row_keluar['source_customer'] == "walk_in") {
                                                    $source = "Source : Walk In <br>";
                                                }
                                                if ($row_keluar['source_customer'] == "referensi") {
                                                    $source = "Source : Referensi <br>";
                                                }
                                                if ($row_keluar['source_customer'] == "repeat_order") {
                                                    $source = "Source : Repeat Order <br>";
                                                }
                                                if ($row_keluar['source_customer'] == "club_mobil") {
                                                    $source = "Source : Club Mobil <br>";
                                                }

                                                $tipe_stock = "<span>Tipe : Stock</span>";
                                                if ($row_keluar['non_stock'] == 1) {
                                                    $tipe_stock = "<span>Tipe : Non-Stock</span>";
                                                }

                                                echo '
                                                    <tr>
                                                        <td class="text-center" rowspan="' . $jum_barang_keluar . '">' . date("d/m/Y", strtotime($row_keluar['tgl_transaksi'])) . '</td>
                                                        <td class="text-center" rowspan="' . $jum_barang_keluar . '">
                                                            ' . $row_keluar['id_transaksi'] . '<br>  
                                                            ' . $data_supplier['nama_customer'] . '<br>
                                                            ' . $source . ' 
                                                            ' . $tipe_stock . '
                                                        </td>
                                                        ';

                                                while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {
                                                    $select_bahan = "SELECT id_bahan,nama_bahan 
                                                                    FROM tb_bahan 
                                                                    WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
                                                    $query_bahan = mysqli_query($conn, $select_bahan);
                                                    $data_bahan = mysqli_fetch_array($query_bahan);
                                                    echo '
                                                                <td class="text-center">' . $data_bahan['nama_bahan'] . '</td>
                                                                <td class="text-right">' . number_format($row_barang_keluar['qty']) . ' Roll</td>
                                                                <td class="text-center">'. number_format($row_barang_keluar['berat']) . ' ' . $row_barang_keluar['uom'] . '</td>
                                                                <td class="text-left"><span style="float:right">' . number_format($row_barang_keluar['harga']) . '</span></td>
                                                                <td class="text-left"><span style="float:right">' . number_format($row_barang_keluar['total']) . '</span></td>
                                                            </tr>
                                                        ';
                                                }

                                                $select_total = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $row_keluar['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang . $where_branch;
                                                $query_total = mysqli_query($conn, $select_total);
                                                $data_total = mysqli_fetch_array($query_total);

                                                $total = $data_total['total'];
                                                $ppn = $total * $row_keluar['ppn'] / 100;

                                                echo '
                                                    <tr class="bg-primary text-white">
                                                        <th colspan="2" class="text-right text-right text-white">Sub Total</th>
                                                        <td class="text-left"><span style="float:right">' . number_format($total) . '</span></td>
                                                        <th class="text-center text-white">PPN ' . $row_keluar['ppn'] . ' %</th>
                                                        <td class="text-left"><span style="float:right">' . number_format($ppn) . '</span></td>
                                                        <th class="text-right text-white">Total</th>
                                                        <td class="text-left"><span style="float:right">' . number_format($total + $ppn) . '</span></td>
                                                    </tr>
                                                ';

                                                $grand_total = $grand_total + ($total + $ppn);
                                            }

                                            echo '
                                                <tr class="bg-success text-white">
                                                    <th colspan="2" class="text-right text-white">Grand Total</th>
                                                    <th colspan="4" class="text-right text-white"></th>
                                                    <td class="text-left"><span style="float:right">' . number_format($grand_total) . '</span></td>
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

    <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Merek Mobil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id" name="id">
                        <div class="form-group">
                            <label>Merek Mobil</label>
                            <input type="text" name="merek_motor" id="" class="form-control merek_motor">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-primary" name="save" value="Simpan">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Merek</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin menghapus merk ini ?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-danger" name="delete" value="Hapus">
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
    $(document).on("click", ".print_page", function() {
        var divContents = document.getElementById("printdivcontent").innerHTML;
        var printWindow = window.open('', '', 'height=200,width=400');
        printWindow.document.write('<html><head><title>Print DIV Content</title>');
        printWindow.document.write('</head><body >');
        printWindow.document.write(divContents);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>