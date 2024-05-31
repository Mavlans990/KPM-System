<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

if (isset($_POST['save'])) {
    $valid = 1;
    if ($_POST['id'] == "") {

        $nama_motor = str_replace(" ", "_", strtolower($_POST['nama_motor']));
        $query = mysqli_query($conn, "INSERT INTO tb_motor(id,merek,nama_motor,filter,harga_plus,harga_minus) VALUES('','" . mysqli_real_escape_string($conn, $_POST['merek']) . "','" . mysqli_real_escape_string($conn, $_POST['nama_motor']) . "','" . mysqli_real_escape_string($conn, $nama_motor) . "','" . mysqli_real_escape_string($conn, $_POST['harga_plus']) . "','" . mysqli_real_escape_string($conn, $_POST['harga_minus']) . "')");
        if (!$query) {
            $valid = 0;
            $msg = "ERROR : Simpan data gagal";
        }
    } else {


        $nama_motor = str_replace(" ", "_", strtolower($_POST['nama_motor']));

        $query = mysqli_query($conn, "UPDATE tb_motor SET 
        merek = '" . mysqli_real_escape_string($conn, $_POST['merek']) . "',
        nama_motor = '" . mysqli_real_escape_string($conn, $_POST['nama_motor']) . "',
        filter = '" . mysqli_real_escape_string($conn, $nama_motor) . "',
        harga_plus = '" . mysqli_real_escape_string($conn, $_POST['harga_plus']) . "',
        harga_minus = '" . mysqli_real_escape_string($conn, $_POST['harga_minus']) . "'
        WHERE id = '" . mysqli_real_escape_string($conn, $_POST['id']) . "'
        ");
        if (!$query) {
            $valid = 0;
            $msg = "ERROR : Ubah data gagal";
        }
    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $msg = "Simpan / Ubah data berhasil";
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}

if (isset($_POST['delete'])) {
    $valid = 1;
    $query = mysqli_query($conn, "DELETE FROM tb_motor WHERE id='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
    if (!$query) {
        $valid = 0;
        $msg = "ERROR : Hapus data gagal";
    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $msg = "Hapus data berhasil";
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Admin Reydecal</title>
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
                    <h1 class="h3 mb-2 text-gray-800">Master Motor</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <a href="#" data-toggle="modal" data-target="#newBrandModal"><i class="fa fa-plus"></i> Tambah Baru</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Merek</th>
                                            <th>Nama Motor</th>
                                            <th>Harga Plus</th>
                                            <th>Harga Minus</th>
                                            <?php
                                            if (isset($_SESSION['grup'])) {
                                                if ($_SESSION['grup'] == 'super') {
                                                    echo '<th>Aksi</th>';
                                                }
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $n = 1;
                                        $sql = mysqli_query($conn, "SELECT * FROM tb_motor ORDER BY id ASC");
                                        while ($row = mysqli_fetch_array($sql)) {
                                            echo '
                                                <tr>
                                                <td>' . $n . '</td>
                                                
                                               ';
                                            $select_merek = "SELECT * FROM tb_merk WHERE filter = '" . $row['merek'] . "'";
                                            $query_merk = mysqli_query($conn, $select_merek);
                                            $data_merk = mysqli_fetch_array($query_merk);
                                            echo '
                                                <td>' . $data_merk['merk_motor'] . '</td>
                                                <td>' . $row['nama_motor'] . '</td>
                                                <td>Rp. ' . number_format($row['harga_plus']) . '</td>
                                                <td>Rp. ' . number_format($row['harga_minus']) . '</td>
                                            ';

                                            if (isset($_SESSION['grup'])) {
                                                if ($_SESSION['grup'] == 'super') {
                                                    echo '
                                                        <td>
                                                            <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal"
                                                            data-id="' . $row['id'] . '"
                                                            data-merk="' . $row['merek'] . '"
                                                            data-nama_motor="' . $row['nama_motor'] . '"
                                                            data-harga_plus="' . $row['harga_plus'] . '"
                                                            data-harga_minus="' . $row['harga_minus'] . '">
                                                            <i class="fa fa-edit"></i> Ubah</a>
                                                            <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                                                            data-id_hapus="' . $row['id'] . '">
                                                            <i class="fa fa-trash"></i> Hapus</a>
                                                        </td>
                                                            ';
                                                }
                                            }

                                            echo '
                    </tr>
                      ';
                                            $n++;
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

            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->

    <!-- NEW BRAND MODAL -->
    <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Motor</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id" name="id">
                        <div class="form-group">
                            <label>Merek</label>
                            <select name="merek" id="" class="form-control merek">
                                <?php
                                $select_merek = "SELECT * FROM tb_merk ORDER BY id ASC";
                                $query_merek = mysqli_query($conn, $select_merek);
                                while ($row_merek = mysqli_fetch_array($query_merek)) {
                                    echo '
                                            <option value="' . $row_merek['filter'] . '">' . $row_merek['merk_motor'] . '</option>
                                        ';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Motor</label>
                            <input type="text" name="nama_motor" id="" class="form-control nama_motor">
                        </div>
                        <div class="form-group">
                            <label>Harga Plus</label>
                            <input type="number" name="harga_plus" id="" class="form-control harga_plus" min="0" value="0">
                        </div>
                        <div class="form-group">
                            <label>Harga Minus</label>
                            <input type="number" name="harga_minus" id="" class="form-control harga_minus" min="0" value="0">
                        </div>
                        <!-- <div class="form-group">
              <input type="text" class="form-control filter" name="filter" placeholder="Nama Filter">
            </div> -->
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
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Motor</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin menghapus kategori motor ini ?
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
    $(document).on("click", '.edit_button', function(e) {
        var id = $(this).data("id");
        var nama_motor = $(this).data("nama_motor");
        var merek = $(this).data("merk");
        var harga_plus = $(this).data("harga_plus");
        var harga_minus = $(this).data("harga_minus");

        $(".id").val(id);
        $(".nama_motor").val(nama_motor);
        $(".merek").val(merek);
        $(".harga_plus").val(harga_plus);
        $(".harga_minus").val(harga_minus);
    });

    $(document).on("click", '.hapus_button', function(e) {
        var id_hapus = $(this).data('id_hapus');
        $(".id_hapus").val(id_hapus);
    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>