<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$filled_input = "filled-input";
$hidn = "hidden";
if ($_SESSION['grup'] == 'super' || $_SESSION['grup'] == 'franchise') {
    $hidn = "";
    $filled_input = "";
}

$from = date("Y-m-d");
$to = date("Y-m-d");
$dari = "";
$ke = "";
$bahan = "";
if (isset($_POST['search'])) {
    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    $dari = mysqli_real_escape_string($conn, $_POST['dari_cabang']);
    $ke = mysqli_real_escape_string($conn, $_POST['ke_cabang']);
    $bahan = mysqli_real_escape_string($conn, $_POST['bahan']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Coolplus System</title>
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
                                <h1 class="h3 mb-2 text-gray-800">Mutasi Stock</h1>
                            </div>
                            <div class="">
                                <form action="" method="post">
                                    <div class="row mt-15">
                                        <div class="col">
                                            <div class="form-group form-inline">
                                                <input type="date" name="tgl_from" id="" class="form-control form-control-sm" value="<?= $from; ?>" required>
                                                <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                                <input type="date" name="tgl_to" id="" class="form-control form-control-sm" value="<?= $to; ?>" required>
                                                <select name="dari_cabang" id="" class="form-control form-control-sm dari_cabang <?php echo $filled_input; ?> mt--5">
                                                    <?php
                                                    $where_branch = "";
                                                    if ($_SESSION['group'] == "franchise") {
                                                        $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
                                                        $where_branch = " WHERE id_cabang IN('" . $filter_cabang . "')";
                                                    }
                                                    if ($_SESSION['group'] == "admin") {
                                                        $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                                    }
                                                    $select_dari = "SELECT 
                                                                    id_cabang,
                                                                    nama_cabang 
                                                                    FROM tb_cabang " . $where_branch . " ORDER BY nama_cabang ASC";
                                                    $query_dari = mysqli_query($conn, $select_dari);

                                                    if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
                                                        echo '
                                                        <option value=""> -- Dari Cabang -- </option>
                                                    ';
                                                    }

                                                    while ($row_dari = mysqli_fetch_array($query_dari)) {
                                                        $selected = "";
                                                        if ($row_dari['id_cabang'] == $dari) {
                                                            $selected = "selected";
                                                        }
                                                        echo '
                                                        <option value="' . $row_dari['id_cabang'] . '" ' . $selected . '>' . $row_dari['nama_cabang'] . '</option>
                                                    ';
                                                    }
                                                    ?>
                                                </select>
                                                <select name="ke_cabang" id="" class="form-control form-control-sm ke_cabang mt--5">
                                                    <option value="">-- Ke Cabang --</option>
                                                    <?php
                                                    $where_branch = "";
                                                    if ($_SESSION['group'] == "franchise") {
                                                        $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
                                                        $where_branch = " WHERE id_cabang IN('" . $filter_cabang . "')";
                                                    }
                                                    if ($_SESSION['group'] == "admin") {
                                                        $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                                    }
                                                    $select_cabang = "SELECT 
                                                                            id_cabang,
                                                                            nama_cabang
                                                                            FROM tb_cabang
                                                                            " . $where_branch . "
                                                                            ORDER BY nama_cabang ASC
                                                        ";
                                                    $query_cabang = mysqli_query($conn, $select_cabang);
                                                    while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                                        $selected = "";
                                                        if ($row_cabang['id_cabang'] == $ke) {
                                                            $selected = "selected";
                                                        }
                                                        echo '
                                                                <option value="' . $row_cabang['id_cabang'] . '" ' . $selected . '>' . $row_cabang['nama_cabang'] . '</option>
                                                            ';
                                                    }

                                                    ?>
                                                </select>
                                                <select name="bahan" id="" class="form-control form-control-sm mt--5">
                                                    <option value="">-- Pilih Barang --</option>
                                                    <?php
                                                    $sql_get_bahan = mysqli_query($conn, "SELECT id_bahan,nama_bahan FROM tb_bahan ORDER BY id_bahan ASC");
                                                    while ($row_bahan = mysqli_fetch_array($sql_get_bahan)) {
                                                        $id_bahan = $row_bahan['id_bahan'];
                                                        $nama_bahan = $row_bahan['nama_bahan'];

                                                        $selected = "";
                                                        if ($id_bahan == $bahan) {
                                                            $selected = "selected";
                                                        }

                                                        echo '
                                                            <option value="' . $id_bahan . '" ' . $selected . '>' . $nama_bahan . '</option>
                                                        ';
                                                    }
                                                    ?>
                                                </select>
                                                <button type="submit" class="btn btn-primary form-control-sm mt-15 mt--5" name="search"><i class="fa fa-search"></i> Cari</button>
                                                <?php
                                                if (isset($_POST['search'])) {
                                                    echo '
                                                        <a href="print/print_laporan_mutasi_stock.php?tgl_from=' . $from . '&tgl_to=' . $to . '&dari=' . $dari . '&ke=' . $ke . '&bahan=' . $bahan . '" class="btn btn-success text-white form-control-sm mt-15 mt--5 ml-1" target="_blank"><i class="fa fa-print"></i> Print</a>
                                                        <a href="print/print_excel_mutasi_stock.php?tgl_from=' . $from . '&tgl_to=' . $to . '&dari=' . $dari . '&ke=' . $ke . '&bahan=' . $bahan . '" target="_blank" class="btn btn-warning text-white form-control-sm mt-15 mt--5 ml-1"><i class="fa fa-list"></i> Download Excel</a>
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
                                <table id="datable_1" class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Nama Barang</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-center">Biaya Jual</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_POST['search'])) {
                                            $where_branch = "";
                                            if ($_SESSION['group'] !== "super") {
                                                $where_branch = " and id_cabang = '" . $_SESSION['branch'] . "'";
                                            }

                                            $tgl_from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
                                            $tgl_to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
                                            $dari_cabang = mysqli_real_escape_string($conn, $_POST['dari_cabang']);
                                            $ke_cabang = mysqli_real_escape_string($conn, $_POST['ke_cabang']);
                                            $bahan = mysqli_real_escape_string($conn, $_POST['bahan']);
                                            $filter_cabang = str_replace(",", "','", $_SESSION['branch']);

                                            if ($_SESSION['group'] == "franchise") {
                                                $filter_dari = " AND i.id_branch IN('" . $filter_cabang . "')";
                                                $filter_ke = " AND o.id_branch IN('" . $filter_cabang . "')";
                                                if ($dari_cabang !== "") {
                                                    $filter_dari = " AND i.id_branch = '" . $dari_cabang . "'";
                                                }

                                                if ($ke_cabang !== "") {
                                                    $filter_ke = " AND o.id_branch = '" . $ke_cabang . "'";
                                                }
                                            } else {
                                                $filter_dari = "";
                                                $filter_ke = "";
                                                if ($dari_cabang !== "") {
                                                    $filter_dari = " AND i.id_branch = '" . $dari_cabang . "'";
                                                }

                                                if ($ke_cabang !== "") {
                                                    $filter_ke = " AND o.id_branch = '" . $ke_cabang . "'";
                                                }
                                            }

                                            $filter_bahan = "";
                                            $filter_bahan2 = "";
                                            if ($bahan !== "") {
                                                $filter_bahan = " AND id_product = '" . $bahan . "'";
                                                $filter_bahan2 = " AND i.id_product = '" . $bahan . "'";
                                            }

                                            $select_transfer = "SELECT 
                                                                    i.inv_out_id,
                                                                    i.id_inv_out,
                                                                    i.inv_date,
                                                                    i.id_product,
                                                                    i.stock_out,
                                                                    i.biaya,
                                                                    i.create_by,
                                                                    i.id_branch AS 'from',
                                                                    o.id_branch AS 'to',
                                                                    k.user_id,
                                                                    k.nama_lengkap
                                                                    FROM inv_adjust_out i
                                                                    JOIN inv_adjust_in o ON o.id_inv_in = i.id_inv_out
                                                                    JOIN tb_karyawan k ON k.user_id = i.create_by
                                                                    WHERE i.inv_date BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "'
                                                                    " . $filter_dari . "
                                                                    " . $filter_ke . " GROUP BY i.id_inv_out";
                                            $query_transfer = mysqli_query($conn, $select_transfer);
                                            while ($row_transfer = mysqli_fetch_array($query_transfer)) {

                                                $select_stock = "SELECT 
                                                                    i.inv_out_id,
                                                                    i.id_inv_out,
                                                                    i.id_product,
                                                                    i.stock_out,
                                                                    i.biaya,
                                                                    p.id_bahan,
                                                                    p.nama_bahan
                                                                    FROM inv_adjust_out i
                                                                    JOIN tb_bahan p ON p.id_bahan = i.id_product
                                                                    WHERE i.id_inv_out = '" . $row_transfer['id_inv_out'] . "' " . $filter_bahan . "

                                                ";
                                                $query_stock = mysqli_query($conn, $select_stock);
                                                $jum_stock = mysqli_num_rows($query_stock);

                                                $select_cabang_dari = "SELECT 
                                                                    id_cabang,
                                                                    nama_cabang
                                                                    FROM tb_cabang
                                                                    WHERE id_cabang = '" . $row_transfer['from'] . "'
                                                ";
                                                $query_cabang_dari = mysqli_query($conn, $select_cabang_dari);
                                                $data_cabang_dari = mysqli_fetch_array($query_cabang_dari);

                                                $select_cabang_ke = "SELECT 
                                                                    id_cabang,
                                                                    nama_cabang
                                                                    FROM tb_cabang
                                                                    WHERE id_cabang = '" . $row_transfer['to'] . "'
                                                ";
                                                $query_cabang_ke = mysqli_query($conn, $select_cabang_ke);
                                                $data_cabang_ke = mysqli_fetch_array($query_cabang_ke);






                                                if ($jum_stock > 0) {
                                                    echo '
                                                    <tr>
                                                        <td rowspan="' . $jum_stock . '">
                                                            ' . date("d/m/Y", strtotime($row_transfer['inv_date'])) . ' <br>
                                                            Dari : ' . $data_cabang_dari['nama_cabang'] . ' <br>
                                                            Ke : ' . $data_cabang_ke['nama_cabang'] . ' <br>
                                                            Dibuat Oleh : ' . $row_transfer['nama_lengkap'] . '
                                                        </td>
                                                ';

                                                    while ($row_stock = mysqli_fetch_array($query_stock)) {
                                                        echo '
                                                        <td class="text-center">' . $row_stock['nama_bahan'] . '</td>
                                                        <td class="text-center">' . $row_stock['stock_out'] . '</td>
                                                        <td class="text-right">' . number_format($row_stock['biaya']) . '</td>
                                                        </tr>
                                                    ';
                                                    }
                                                }
                                            }
                                        } else {
                                            echo '
                                                <tr>
                                                    <th class="text-center" colspan="4">Mohon Search Terlebih Dahulu</th>
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
    $(document).ready(function() {
        $(document).on("change", ".dari_cabang", function() {
            var dari_cabang = $(".dari_cabang").val();


            $.ajax({
                type: "POST",
                url: "ajax/ajax_mutasi_stock.php",
                data: {
                    "dari_cabang": dari_cabang
                },
                cache: true,
                success: function(response) {
                    $(".ke_cabang").html(response);
                }
            });

        });
    });
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