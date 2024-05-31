<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$hide = "";
$filled_input = "";
if ($_SESSION['group'] !== "super") {
    $hide = "style='display:none;'";
    $filled_input = "filled-input";
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
                    <h1 class="h3 mb-2 text-gray-800">Transfer Stock</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <a href="#" data-toggle="modal" class="add_new" data-target="#newBrandModal"><i class="fa fa-plus"></i> Tambah Baru</a>
                        </div>
                        <div class="card-header py-3">
                            <form action="" method="post" class="form-inline">
                                <div class="form-group">
                                    <input type="date" class="form-control" name="tgl_from" id="" value="<?php echo date("Y-m-d"); ?>" required>
                                    <span style="padding:10px;">S / D</span>
                                    <input type="date" class="form-control" name="tgl_to" id="" required>
                                    <select name="cabang_from" id="" class="form-control ml-2 filled-input">
                                        <?php
                                        if ($_SESSION['group'] !== "super") {
                                            $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                            $query_cabang = mysqli_query($conn, $select_cabang);
                                            $data_cabang = mysqli_fetch_array($query_cabang);

                                            echo '
                                                <option value="' . $data_cabang['id_cabang'] . '">' . $data_cabang['nama_cabang'] . '</option>
                                            ';
                                        } else {
                                            echo '
                                                <option value="">-- Dari Cabang --</option>
                                                ';
                                            $select_cabang = "SELECT * FROM tb_cabang ORDER BY nama_cabang ASC";
                                            $query_cabang = mysqli_query($conn, $select_cabang);
                                            while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                                echo '
                                                    <option value="' . $row_cabang['id_cabang'] . '">' . $row_cabang['nama_cabang'] . '</option>
                                                ';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <select name="cabang_to" id="" class="form-control ml-2">
                                        <option value="">-- Ke Cabang --</option>
                                        <?php
                                        $select_cabang = "SELECT * FROM tb_cabang ORDER BY nama_cabang ASC";
                                        $query_cabang = mysqli_query($conn, $select_cabang);
                                        while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                            echo '
                                                    <option value="' . $row_cabang['id_cabang'] . '">' . $row_cabang['nama_cabang'] . '</option>
                                                ';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary ml-2" name="cari"><i class="fa fa-search"></i> Cari</button>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                                    <thead>
                                        <tr>
                                            <th>ID Transfer</th>
                                            <th>Tgl Transfer</th>
                                            <th>Dari Cabang</th>
                                            <th>Ke Cabang</th>
                                            <th>Produk</th>
                                            <th>Jumlah</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_POST['cari'])) {
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
                    <h5 class="modal-title" id="exampleModalLabel">Transfer Stock</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form class="add_new_transfer" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">ID Transfer</label>
                            <input type="text" name="id_transfer" id="" class="form-control id_transfer filled-input" value="<?php echo generate_transfer(); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="">Dari Cabang</label>
                            <select name="dari_cabang" id="" class="form-control dari_cabang <?php echo $filled_input ?>" required>
                                <?php
                                if ($_SESSION['group'] !== "super") {
                                    $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                    $query_cabang = mysqli_query($conn, $select_cabang);
                                    $data_cabang = mysqli_fetch_array($query_cabang);

                                    echo '
                                        <option value="' . $data_cabang['id_cabang'] . '">' . $data_cabang['nama_cabang'] . '</option>
                                    ';
                                } else {
                                    echo '
                                        <option value="">-- Dari Cabang --</option>
                                        ';
                                    $select_cabang = "SELECT * FROM tb_cabang ORDER BY nama_cabang ASC";
                                    $query_cabang = mysqli_query($conn, $select_cabang);
                                    while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                        echo '
                                            <option value="' . $row_cabang['id_cabang'] . '">' . $row_cabang['nama_cabang'] . '</option>
                                        ';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Ke Cabang</label>
                            <select name="ke_cabang" id="" class="form-control ke_cabang" required>
                                <option value="">-- Ke Cabang --</option>
                                <?php
                                $select_cabang = "SELECT * FROM tb_cabang ORDER BY nama_cabang ASC";
                                $query_cabang = mysqli_query($conn, $select_cabang);
                                while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                    echo '
                                        <option value="' . $row_cabang['id_cabang'] . '">' . $row_cabang['nama_cabang'] . '</option>
                                    ';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Nama Bahan / Produk</label>
                            <select name="nama_bahan" id="" class="form-control nama_bahan" required></select>
                        </div>
                        <div class="form-group">
                            <label for="">Jumlah</label>
                            <input type="number" name="jumlah" id="" class="form-control jumlah" step="0.01" min="0" value="0" required>
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
                url: "ajax/ajax_transfer_stock.php",
                data: {
                    "dari_cabang_stock": dari_cabang
                },
                cache: true,
                success: function(response) {
                    var response = response.split("|");
                    $(".nama_bahan").html(response[0]);
                    $(".ke_cabang").html(response[1]);
                }
            });
        });

        $(document).on("submit", ".add_new_transfer", function(e) {
            e.preventDefault();
            var add_new_transfer = $(".id_transfer").val();
            var dari_cabang = $(".dari_cabang").val();
            var ke_cabang = $(".ke_cabang").val();
            var id_bahan = $(".nama_bahan").val();
            var jumlah = $(".jumlah").val();

            $.ajax({
                type: "POST",
                url: "ajax/ajax_transfer_stock.php",
                data: {
                    "add_new_transfer": add_new_transfer,
                    "dari_cabang": dari_cabang,
                    "ke_cabang": ke_cabang,
                    "id_bahan": id_bahan,
                    "jumlah": jumlah
                },
                cache: true,
                success: function(response) {
                    alert(response);
                }
            })
        });

    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>