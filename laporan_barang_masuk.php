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
if (isset($_POST['search'])) {
    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    $supp = mysqli_real_escape_string($conn, $_POST['supplier']);
    $bah = mysqli_real_escape_string($conn, $_POST['bahan']);

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
                                <h1 class="h3 mb-2 text-gray-800">Laporan Barang Masuk</h1>
                            </div>
                            <div class="">
                                <form action="" method="post">

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group form-inline">
                                                <input type="date" name="tgl_from" id="" class="form-control form-control-sm" value="<?= $from; ?>" required>
                                                <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                                <input type="date" name="tgl_to" id="" class="form-control form-control-sm" value="<?= $to; ?>" required>
                                                <input type="text" name="supplier" id="supplier" class="form-control form-control-sm mt--5 supplier" placeholder="Nama Supplier" list="supplier_list" value="<?php echo $supp; ?>">
                                                <datalist id="supplier_list">
                                                    
                                                </datalist>
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
                                                <button type="submit" class="btn btn-primary form-control-sm mt--5" name="search"><i class="fa fa-search"></i> Cari</button>
                                                <?php
                                                if (isset($_POST['search'])) {
                                                    $id_supp = "";
                                                    if($supp !== "" && strpos($supp," | ") == true){
                                                        $ex_id_supp = explode(" | ",$supp);
                                                        $id_supp = $ex_id_supp[1];
                                                    }
                                                    echo '
                                                        <a href="print/print_laporan_barang_masuk.php?tgl_from=' . $from . '&tgl_to=' . $to . '&supplier=' . $id_supp . '&bahan=' . $bah . '&cabang=' . $idcabang . '" class="btn btn-success text-white form-control-sm ml-1 mt--5" target="_blank"><i class="fa fa-print"></i> Print</a>
                                                        <a href="print/print_excel_masuk.php?tgl_from=' . $from . '&tgl_to=' . $to . '&supplier=' . $id_supp . '&bahan=' . $bah . '&cabang=' . $idcabang . '" target="_blank" class="btn btn-warning text-white form-control-sm ml-1 mt--5"><i class="fa fa-list"></i> Download Excel</a>
                                                    ';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" id="printdivcontent">
                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">No. Transaksi</th>
                                            <th class="text-center">Item</th>
                                            <th class="text-center">Qty (Roll)</th>
                                            <th class="text-center">Yard</th>
                                            <th class="text-center">UOM</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Total</th>
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

                                            if ($supplier !== "") {
                                                if(strpos($supplier," | ") == TRUE){
                                                    $ex_id_supp = explode(" | ",$supplier);
                                                    $id_supp = $ex_id_supp[1];

                                                    $filter_supplier = " AND id_supplier = '".$id_supp."'";
                                                }
                                            }
                                            if ($bahan !== "") {
                                                $filter_bahan = " AND id_product = '" . $bahan . "'";
                                            }


                                            $grand_total = 0;

                                            $select_masuk = "SELECT * FROM tb_barang_masuk WHERE (tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "') " . $filter_supplier . " " . $filter_bahan . " " . $filter_cabang . " and status <> 'd' GROUP BY id_transaksi";

                                            $query_masuk = mysqli_query($conn, $select_masuk);

                                            while ($row_masuk = mysqli_fetch_array($query_masuk)) {

                                                $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $row_masuk['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang . $where_branch;
                                                $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
                                                $jum_barang_masuk = mysqli_num_rows($query_barang_masuk);

                                                $select_supplier = "SELECT * FROM tb_customer WHERE id_customer = '" . $row_masuk['id_supplier'] . "'";
                                                $query_supplier = mysqli_query($conn, $select_supplier);
                                                $data_supplier = mysqli_fetch_array($query_supplier);


                                                echo '
                                                    <tr>
                                                        <td class="text-center" rowspan="' . $jum_barang_masuk . '">' . date("d/m/Y", strtotime($row_masuk['tgl_transaksi'])) . '</td>
                                                        <td class="text-center" rowspan="' . $jum_barang_masuk . '">
                                                            ' . $row_masuk['id_transaksi'] . '<br>  
                                                            ' . $data_supplier['nama_customer'] . '<br>
                                                        </td>
                                                        ';

                                                while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
                                                    $select_bahan = "SELECT id_bahan,nama_bahan 
                                                                    FROM tb_bahan 
                                                                    WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "'";
                                                    $query_bahan = mysqli_query($conn, $select_bahan);
                                                    $data_bahan = mysqli_fetch_array($query_bahan);
                                                    echo '
                                                                <td class="text-center">' . $data_bahan['nama_bahan'] . '</td>
                                                                <td class="text-right">' . number_format($row_barang_masuk['qty']) . '</td>
                                                                <td class="text-right">' . number_format($row_barang_masuk['berat']) . '</td>
                                                                <td class="text-center">' . $row_barang_masuk['uom'] . '</td>
                                                                <td class="text-left"><span style="float:right">' . number_format($row_barang_masuk['harga']) . '</span></td>
                                                                <td class="text-left"><span style="float:right">' . number_format($row_barang_masuk['total']) . '</span></td>
                                                            </tr>
                                                        ';
                                                }

                                                $select_total = "SELECT SUM(total) AS total FROM tb_barang_masuk WHERE id_transaksi = '" . $row_masuk['id_transaksi'] . "'" . $filter_supplier . $filter_bahan . $filter_cabang . $where_branch;
                                                $query_total = mysqli_query($conn, $select_total);
                                                $data_total = mysqli_fetch_array($query_total);

                                                $total = $data_total['total'];
                                                $ppn = $total * $row_masuk['ppn'] / 100;

                                                echo '
                                                    <tr class="bg-primary text-white">
                                                        <th colspan="2" class="text-right text-right text-white">Sub Total</th>
                                                        <td class="text-left"><span style="float:right">' . number_format($total) . '</span></td>
                                                        <th class="text-center text-white">PPN ' . $row_masuk['ppn'] . ' %</th>
                                                        <td class="text-left" colspan="2"><span style="float:right">' . number_format($ppn) . '</span></td>
                                                        <th class="text-right text-white">Total</th>
                                                        <td class="text-left"><span style="float:right">' . number_format($total + $ppn) . '</span></td>
                                                    </tr>
                                                ';

                                                $grand_total = $grand_total + ($total + $ppn);
                                            }

                                            echo '
                                                <tr class="bg-success text-white">
                                                    <th colspan="2" class="text-right text-white">Grand Total</th>
                                                    <th colspan="5" class="text-right text-white"></th>
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
    $(document).ready(function(){
        $(document).on("keyup",".supplier",function(){
            var supplier = $(this).val();
            $.ajax({
                type : "POST",
                url : "ajax/ajax_laporan_barang_masuk.php",
                data : {
                    "get_supplier" : supplier
                },
                cache : true,
                success : function(result){
                    $("#supplier_list").html(result);
                }
            });
        });
    });
</script>