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
    $conn_other = mysqli_connect('203.161.184.26', 'coolplu1_warranty', 'stevsoft14*', 'coolplu1_warranty');
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nama_cabang = mysqli_real_escape_string($conn, $_POST['nama_cabang']);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jenis_cabang = mysqli_real_escape_string($conn, $_POST['jenis_cabang']);

    if ($id == "") {

        $query_cabang = mysqli_query($conn, "
            SELECT nama_cabang
            FROM tb_cabang
            WHERE nama_cabang = '" . $nama_cabang . "'
        ");
        $jum_cabang = mysqli_num_rows($query_cabang);

        if ($jum_cabang < 1) {
            $query = mysqli_query($conn, "INSERT INTO tb_cabang(
                id_cabang,
                nama_cabang,
                alamat,
                nomor_telepon,
                jenis_cabang
            ) VALUES(
                '',
                '" . $nama_cabang . "',
                '" . $alamat . "',
                '" . $nomor_telepon . "',
                '" . $jenis_cabang . "'
            )") or die(mysqli_error($conn));

            if (!$query) {
                $valid = 0;
                $msg = "ERROR : Simpan data gagal";
            } else {
                $query_other = mysqli_query($conn_other, "INSERT INTO tb_cabang(
                    id_cabang,
                    nama_cabang,
                    alamat,
                    nomor_telepon,
                    jenis_cabang
                ) VALUES(
                    '',
                    '" . $nama_cabang . "',
                    '" . $alamat . "',
                    '" . $nomor_telepon . "',
                    '" . $jenis_cabang . "'
                )") or die(mysqli_error($conn_other));
            }
        } else {
            $valid = 0;
            $msg = "ERROR : Data sudah ada";
        }
    } else {


        $query = mysqli_query($conn, "UPDATE tb_cabang SET
        nama_cabang = '" . $nama_cabang . "',
        alamat = '" . $alamat . "',
        nomor_telepon = '" . $nomor_telepon . "',
        jenis_cabang = '" . $jenis_cabang . "' WHERE id_cabang = '" . $id . "'
        ") or die(mysqli_error($conn));

        if (!$query) {
            $valid = 0;
            $msg = "ERROR : Ubah data gagal";
        } else {
            $query_other = mysqli_query($conn_other, "UPDATE tb_cabang SET
            nama_cabang = '" . $nama_cabang . "',
            alamat = '" . $alamat . "',
            nomor_telepon = '" . $nomor_telepon . "',
            jenis_cabang = '" . $jenis_cabang . "' WHERE id_cabang = '" . $id . "'
            ") or die(mysqli_error($conn_other));
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
    $conn_other = mysqli_connect('203.161.184.26', 'coolplu1_warranty', 'stevsoft14*', 'coolplu1_warranty');
    $query = mysqli_query($conn, "DELETE FROM tb_cabang WHERE id_cabang = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
    if (!$query) {
        $valid = 0;
        $msg = "ERROR : Hapus data gagal";
    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $msg = "Hapus data berhasil";

        $query_other = mysqli_query($conn_other, "DELETE FROM tb_cabang WHERE id_cabang = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
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
                        <div class="card-header py-3" style="text-transform: none;">
                            <div class="form-inline">
                                <h3 class="mr-2">Master Cabang</h3>
                                <a href="#" data-toggle="modal" class="add_new btn btn-primary form-control-sm" data-target="#newBrandModal"><i class="fa fa-plus"></i> Tambah Baru</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Cabang</th>
                                            <th>Jenis Cabang</th>
                                            <th>Aksi</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $n = 1;
                                        $sql = mysqli_query($conn, "SELECT * FROM tb_cabang ORDER BY id_cabang ASC");
                                        while ($row = mysqli_fetch_array($sql)) {
                                            if ($row['jenis_cabang'] == "f") {
                                                $jenis_cabang = "Franchise";
                                            } else {
                                                $jenis_cabang = "Non Franchise";
                                            }
                                            echo '
                                                <tr>
                                                    <td>' . $n . '</td>
                                                    <td>' . $row['nama_cabang'] . '</td>
                                                    <td>' . $jenis_cabang . '</td>
                                           
                                                <td>
                                                <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal"
                                                  data-id_cabang="' . $row['id_cabang'] . '"
                                                  data-nama_cabang="' . $row['nama_cabang'] . '"
                                                  data-alamat="' . $row['alamat'] . '"
                                                  data-notelp="' . $row['nomor_telepon'] . '"
                                                  data-jenis_cabang="' . $row['jenis_cabang'] . '">
                                                <i class="fa fa-edit"></i> Ubah</a>
                                                <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                                                  data-id_hapus="' . $row['id_cabang'] . '">
                                                <i class="fa fa-trash"></i> Hapus</a>
                                              </td>
                                               </tr>';
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


        </div>
        <!-- End of Content Wrapper -->

    </div>

    <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Master Cabang</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id" name="id">
                        <div class="form-group">
                            <label>Nama Cabang</label>
                            <input type="text" name="nama_cabang" id="" class="form-control nama_cabang" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Nomor Telepon</label>
                            <input type="number" name="nomor_telepon" id="" class="form-control nomor_telepon" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" id="" cols="30" rows="3" class="form-control alamat" autocomplete="off"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Jenis Cabang</label>
                            <select name="jenis_cabang" id="" class="form-control jenis_cabang">
                                <option value="f">Franchise</option>
                                <option value="n">Non Franchise</option>
                            </select>
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
                    <h5 class="modal-title" id="exampleModalLabel">Hapus cabang</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin menghapus cabang ini ?
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
        var id = $(this).data('id_cabang');
        var nama_cabang = $(this).data('nama_cabang');
        var alamat = $(this).data('alamat');
        var nomor_telepon = $(this).data('notelp');
        var jenis_cabang = $(this).data('jenis_cabang');

        $(".id").val(id);
        $(".nama_cabang").val(nama_cabang);
        $(".alamat").val(alamat);
        $(".nomor_telepon").val(nomor_telepon);
        $(".jenis_cabang").val(jenis_cabang);
    });

    $(document).on("click", ".add_new", function() {
        $(".id").val("");
        $(".nama_cabang").val("");
        $(".alamat").val("");
        $(".nomor_telepon").val("0");
        $(".jenis_cabang").val("f");
    });

    $(document).on("click", '.hapus_button', function(e) {
        var id_hapus = $(this).data('id_hapus');
        $(".id_hapus").val(id_hapus);
    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>