<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

$id_bahan = generate_supplier();

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

if (isset($_POST['save'])) {

    $id = mysqli_real_escape_string($conn, $_POST['id_supplier']);
    $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $pic = mysqli_real_escape_string($conn, $_POST['pic']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $telp = mysqli_real_escape_string($conn, $_POST['telp']);

    $select_bahan = "SELECT * FROM tb_supplier WHERE id_supplier = '" . $id . "'";
    $query_bahan = mysqli_query($conn, $select_bahan);
    $jum_bahan = mysqli_num_rows($query_bahan);

    if ($jum_bahan < 1) {
        $insert = "INSERT INTO tb_supplier(
            id_supplier,
            nama_supplier,
            pic,
            kota,
            alamat,
            telp
            ) VALUES(
                '" . $id . "',
                '" . $nama_supplier . "',
                '" . $pic . "',
                '" . $kota . "',
                '" . $alamat . "',
                '" . $telp . "'
            )";

        $query = mysqli_query($conn, $insert);
        if ($query) {
            $msg = "Simpan data berhasil";
        } else {
            $msg = "Simpan data gagal";
        }
    } else {
        $update = "UPDATE tb_supplier SET
            nama_supplier = '" . $nama_supplier . "',
            pic = '" . $pic . "',
            kota = '" . $kota . "',
            alamat = '" . $alamat . "',
            telp = '" . $telp . "'
            WHERE id_supplier = '" . $id . "';
        ";

        $query = mysqli_query($conn, $update);
        if ($query) {
            $msg = "Ubah data berhasil";
        } else {
            $msg = "Ubah data gagal";
        }
    }

    echo "<script type='text/javascript'>alert('" . $msg . "');window.location='master_supplier.php';</script>";
}

if (isset($_POST['delete'])) {
    $valid = 1;
    $query = mysqli_query($conn, "DELETE FROM tb_supplier WHERE id_supplier='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
    if (!$query) {
        $valid = 0;
        $msg = "Hapus data gagal";
    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $msg = "Hapus data berhasil";
    }

    echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href='master_supplier.php';</script>";
}

if (isset($_POST['import'])) {
    $filename = $_FILES['import_file']['name'];
    $file_tmp = $_FILES['import_file']['tmp_name'];
    move_uploaded_file($file_tmp, 'modul/script/' . $filename);
    // Open the file for reading
    if (($h = fopen("modul/script/{$filename}", "r")) !== FALSE) {
        // Each line in the file is converted into an individual array that we call $data
        // The items of the array are comma separated

        while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
            // Each individual array is being pushed into the nested array
            $valid = 1;
            $id_bahan = generate_bahan();

            $insert = "INSERT INTO tb_bahan(
                id_bahan,
                kota,
                jenis_bahan,
                uom,
                stock_min,
                stock_max,
            ) VALUES(
                '" . $id_bahan . "',
                '" . $data[0] . "',
                '" . $data[1] . "',
                '" . $data[2] . "',
                '" . $data[3] . "',
                '" . $data[4] . "'
            )";

            $query = mysqli_query($conn, $insert);

            if ($query) {
                $status = "success";
            } else {
                $status = "failed";
            }
        }

        // Close the file
        fclose($h);
    } else {
        echo "<script>alert('invalid input');window.location='master_bahan.php';</script>";
        $status = "";
    }
    echo "<script>alert('" . $status . "');window.location='master_bahan.php';</script>";
    // Display the code in a readable format

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
                        <div class="card-header py-3" style="text-transform: none;">
                            <div class="form-inline">
                                <h3 class="mr-2">Master Supplier</h3>
                                <a href="#" data-toggle="modal" class="add_new btn btn-primary form-control-sm mr-2 ml--5" data-target="#newBrandModal"><i class="fa fa-plus"></i> Tambah Baru</a>
                                <!-- <a href="javascript:void(0);" class="btn btn-success form-control-sm" data-toggle="modal" data-target="#ImportModal"><i class="fa fa-download"></i> Import CSV</a> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                                    <thead>
                                        <tr>
                                            <th>ID Supplier</th>
                                            <th>Nama</th>
                                            <th>PIC</th>
                                            <th>Kota</th>
                                            <th>Alamat</th>
                                            <th>No.Telp</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $n = 1;
                                        $sql = mysqli_query($conn, "SELECT * FROM tb_supplier ORDER BY id_supplier ASC");
                                        while ($row = mysqli_fetch_array($sql)) {

                                            echo '
                                                <tr>
                                                    <td>' . $row['id_supplier'] . '</td>
                                                    <td>' . $row['nama_supplier'] . '</td>
                                                    <td>' . $row['pic'] . '</td>
                                                    <td>' . $row['kota'] . '</td>
                                                    <td>' . $row['alamat'] . '</td>
                                                    <td>' . $row['telp'] . '</td>
                                                <td>
                                                <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal"
                                                data-id_supplier="' . $row['id_supplier'] . '"
                                                data-nama_supplier="' . $row['nama_supplier'] . '"
                                                data-pic="' . $row['pic'] . '"
                                                data-kota="' . $row['kota'] . '"
                                                data-alamat="' . $row['alamat'] . '"
                                                data-telp="' . $row['telp'] . '">
                                                <i class="fa fa-edit"></i> Ubah</a>
                                                <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                                                  data-id_hapus="' . $row['id_supplier'] . '">
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
                    <h5 class="modal-title" id="exampleModalLabel">Master Supplier</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>ID Supplier</label>
                            <input type="text" name="id_supplier" id="" class="form-control filled-input id_supplier" value="<?= $id_bahan; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama_supplier" id="" class="form-control nama_supplier" required>
                        </div>
                        <div class="form-group">
                            <label>PIC</label>
                            <input type="text" name="pic" id="" class="form-control pic" required>
                        </div>
                        <div class="form-group">
                            <label>Kota</label>
                            <input type="text" name="kota" id="" class="form-control kota" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" id="" class="form-control form-control-sm <?= $filled_input; ?> alamat" <?= $readonly; ?> autocomplete="off" onclick="this.select();"></textarea>
                        </div>
                        <div class="form-group">
                            <label>No.Telp</label>
                            <input type="number" name="telp" id="" class="form-control telp" autocomplete="off" required>
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
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Bahan</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin menghapus bahan ini ?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-danger" name="delete" value="Hapus">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ImportModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Import Data</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Upload CSV</label>
                            <input type="file" name="import_file" id="" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-success" name="import" value="Import">
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
    $(document).on("change", ".jenis_bahan", function() {
        var jenis = $(".jenis_bahan").val();
        if (jenis == "kaca_film") {
            $(".masa_berlaku_field").show(500);
        } else {
            $(".masa_berlaku_field").hide(500);
        }
    });

    $(document).on("click", '.edit_button', function(e) {
        var id_supplier = $(this).data('id_supplier');
        var nama_supplier = $(this).data('nama_supplier');
        var pic = $(this).data('pic');
        var kota = $(this).data('kota');
        var alamat = $(this).data('alamat');
        var telp = $(this).data('telp');

        $(".id_supplier").val(id_supplier);
        $(".nama_supplier").val(nama_supplier);
        $(".pic").val(pic);
        $(".kota").val(kota);
        $(".alamat").val(alamat);
        $(".telp").val(telp);
    });

    $(document).on("click", ".add_new", function() {
        $(".id_supplier").val("<?= $id_bahan ?>");
        $(".nama_supplier").val("");
        $(".pic").val("");
        $(".kota").val("");
        $(".alamat").val("");
        $(".telp").val("");
    });

    $(document).on("click", '.hapus_button', function(e) {
        var id_hapus = $(this).data('id_hapus');
        $(".id_hapus").val(id_hapus);
    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>