<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

$id_bahan = generate_bahan();

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$tgl1 = date("Y-m-d");
$tgl2 = date("Y-m-d");
if (isset($_POST['cari'])) {
    $tgl1 = mysqli_real_escape_string($conn, $_POST['tgl_1']);
    $tgl2 = mysqli_real_escape_string($conn, $_POST['tgl_2']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Lumier System</title>
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

        th {
            color: black;
        }

        td {
            color: black;
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
                                <h3 class="mr-2">Pelunasan Hutang</h3>
                                <!-- <a href="javascript:void(0);" class="add_new btn btn-primary form-control-sm mr-2 ml--5" data-toggle="modal" data-target="#newBarangModal"><i class="fa fa-plus"></i> Add New</a> -->
                                <!-- <a href="javascript:void(0);" class="btn btn-success form-control-sm" data-toggle="modal" data-target="#ImportModal"><i class="fa fa-download"></i> Import CSV</a> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="date" name="tgl_1" id="" class="form-control form-control-sm" value="<?php echo $tgl1; ?>" required>
                                        </div>
                                    </div>
                                    <p class="mt-10">S/D</p>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="date" name="tgl_2" id="" class="form-control form-control-sm" value="<?php echo $tgl2; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-sm btn-primary" name="cari"><i class="fa fa-search"></i> Cari</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                                    <thead>
                                        <tr>
                                            <th class="fs-11">No PO</th>
                                            <th class="fs-11">Tgl</th>
                                            <th class="fs-11">Supplier</th>
                                            <th class="fs-11">Status</th>
                                            <th class="text-center fs-11">Total</th>
                                            <th class="text-center">Checklist</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $filter_waktu = " WHERE a.tgl_transaksi BETWEEN '" . $tgl1 . "' AND '" . $tgl2 . "'";
                                        $sql_get_hutang = mysqli_query($conn, "
                                            SELECT 
                                                a.id_transaksi,
                                                a.tgl_transaksi,
                                                SUM(a.total) AS total,
                                                a.status_lunas,
                                                b.nama_customer
                                            FROM
                                                tb_barang_masuk a
                                            LEFT JOIN
                                                tb_customer b ON b.id_customer = a.id_supplier
                                            " . $filter_waktu . "
                                            GROUP BY a.id_transaksi
                                            ORDER BY a.id_transaksi DESC
                                        ");
                                        while ($row_hutang = mysqli_fetch_array($sql_get_hutang)) {

                                            $id_po = $row_hutang['id_transaksi'];
                                            $tgl_po = date("d-m-Y", strtotime($row_hutang['tgl_transaksi']));
                                            $nm_supp = $row_hutang['nama_customer'];
                                            $total = $row_hutang['total'];
                                            $style = "style='background:#e6ffe6;'";
                                            $status = "Lunas";
                                            $check_disable = "disabled";
                                            if ($row_hutang['status_lunas'] == "0") {
                                                $status = "Belum Lunas";
                                                $style = "";
                                                $check_disable = "";
                                            }


                                            echo '
                                                <tr ' . $style . '>
                                                    <td class="fs-11">' . $id_po . '</td>
                                                    <td class="fs-11">' . $tgl_po . '</td>
                                                    <td class="fs-11">' . $nm_supp . '</td>
                                                    <td class="fs-11">' . $status . '</td>
                                                    <td class="text-right fs-11">' . number_format($total) . '</td>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="" class="check_po" id="" data-id_po="' . $id_po . '" ' . $check_disable . '>
                                                    </td>
                                                </tr>
                                            ';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                <button type="button" class="btn btn-sm btn-secondary btn_bayar mt-15">Bayar</button>
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

    <div class="modal fade" id="newBarangModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Bahan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_product hidden" name="id_product">
                        <div class="form-group">
                            <label for="nm_product">Bahan Name*</label>
                            <input type="text" class="form-control nm_product" name="nm_product" required>
                        </div>
                        <div class="form-group d-none">
                            <label>Gambar</label>
                            <input type="file" name="gambar" class="form-control gambar" accept="gambar/*">
                        </div>
                        <div class="form-group">
                            <label for="name">Minimum Stock</label>
                            <input type="number" class="form-control minim" name="minim" placeholder="Min Stock" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label for="name">Maximum Stock</label>
                            <input type="number" class="form-control maxim" name="maxim" placeholder="Max Stock" value="0" min="0">
                        </div>
                        <div class="form-group">
                            <label for="name">UOM</label>
                            <div class="row">
                                <div class="col" hidden>
                                    <input type="text" class="form-control berat" name="berat" placeholder="UOM">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control satuan" name="satuan" placeholder="Contoh: Kilogram">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Bahan Price</label>
                            <input type="text" class="form-control price" name="price" placeholder="Price">
                        </div>
                        <div class="form-group">
                            <label for="name">HPP</label>
                            <input type="text" class="form-control hrg_modal" name="hrg_modal" placeholder="HPP">
                        </div>

                        <div class="form-group">
                            <label for="name">Brand</label>
                            <select name="brand" id="brand" class="form-control brand">
                                <?php
                                $sql = mysqli_query($conn, "select * from tb_kategori order by nm_kategori");
                                while ($row = mysqli_fetch_array($sql)) {
                                    echo '
                <option value="' . $row['id_kategori'] . '">' . $row['nm_kategori'] . '</option>
                  ';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="name">Bundling Item</label>
                            <input type="text" class="form-control bundling" name="bundling" placeholder="ITEM01,ITEM02,ITEM03" value="BASIC">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-primary" name="save" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteBarangModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Bahan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Delete this product?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-danger" name="delete" value="Delete">
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

        $(document).on("click", ".check_po", function() {
            var id_po = $(this).data('id_po');
            var check_po = $(".check_po:checked").length;
            if (check_po > 0) {
                $(".btn_bayar").removeClass("btn-secondary");
                $(".btn_bayar").addClass("btn-primary");
                $(".btn_bayar").addClass("bayar_btn");
            } else {
                $(".btn_bayar").removeClass("btn-primary");
                $(".btn_bayar").removeClass("bayar_btn");
                $(".btn_bayar").addClass("btn-secondary");
            }
        });

        $(document).on("click", ".bayar_btn", function() {
            var check_po = [];
            $(".check_po:checked").each(function() {
                check_po.push($(this).data('id_po'));
            });

            window.location.href = "detail_bayar_hutang.php?id_po=" + check_po;
        });

    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>