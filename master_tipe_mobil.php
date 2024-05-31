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

    $merk_mobil = mysqli_real_escape_string($conn, $_POST['merk_mobil']);
    $tipe_mobil = mysqli_real_escape_string($conn, $_POST['tipe_mobil']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun_mobil']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $kaca_depan = mysqli_real_escape_string($conn, $_POST['kaca_depan']);
    $kaca_belakang = mysqli_real_escape_string($conn, $_POST['kaca_belakang']);
    $lainnya = mysqli_real_escape_string($conn, $_POST['lainnya']);
    $total = mysqli_real_escape_string($conn, $_POST['total']);

    if ($_POST['id'] == "") {

        $id_tipe = generate_tipe_mobil();

        $insert = "INSERT INTO tb_tipe_mobil(
            id_tipe,
            merk_mobil,
            tipe_mobil,
            tahun,
            kategori,
            kaca_depan,
            kaca_skkb,
            lainnya,
            total
        ) VALUES(
            '" . $id_tipe . "',
            '" . $merk_mobil . "',
            '" . $tipe_mobil . "',
            '" . $tahun . "',
            '" . $kategori . "',
            '" . $kaca_depan . "',
            '" . $kaca_belakang . "',
            '" . $lainnya . "',
            '" . $total . "'
        )";

        $query = mysqli_query($conn, $insert);

        if (!$query) {
            $valid = 0;
            $msg = "ERROR : Simpan data gagal";
        }
    } else {

        $update = "UPDATE tb_tipe_mobil SET
            merk_mobil = '" . $merk_mobil . "',
            tipe_mobil = '" . $tipe_mobil . "',
            tahun = '" . $tahun . "',
            kategori = '" . $kategori . "',
            kaca_depan = '" . $kaca_depan . "',
            kaca_skkb = '" . $kaca_belakang . "',
            lainnya = '" . $lainnya . "',
            total = '" . $total . "' WHERE id_tipe = '" . mysqli_real_escape_string($conn, $_POST['id']) . "'
        ";

        $query = mysqli_query($conn, $update);

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
    $query = mysqli_query($conn, "DELETE FROM tb_tipe_mobil WHERE id_tipe = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
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
            $id_tipe = generate_tipe_mobil();

            $total = $data[5] + $data[6] + $data[7];

            $query_tipe_mobil = mysqli_query($conn, "
                SELECT * FROM tb_tipe_mobil
                WHERE id_tipe = '" . $data[0] . "'
            ");
            $jum_tipe_mobil = mysqli_num_rows($query_tipe_mobil);

            if ($jum_tipe_mobil > 0) {
                $update = "UPDATE tb_tipe_mobil SET
                merk_mobil = '" . $data[1] . "',
                tipe_mobil = '" . $data[2] . "',
                tahun = '" . $data[3] . "',
                kategori = '" . $data[4] . "',
                kaca_depan = '" . $data[5] . "',
                kaca_skkb = '" . $data[6] . "',
                lainnya = '" . $data[7] . "',
                total = '" . $data[8] . "' WHERE id_tipe = '" . $data[0] . "'
                ";
                $query = mysqli_query($conn, $update);
                if ($query) {
                    $status = "success";
                } else {
                    $status = "failed";
                }
            } else {
                $insert = "INSERT INTO tb_tipe_mobil(
                    id_tipe,
                    merk_mobil,
                    tipe_mobil,
                    tahun,
                    kategori,
                    kaca_depan,
                    kaca_skkb,
                    lainnya,
                    total
                ) VALUES(
                    '" . $id_tipe . "',
                    '" . $data[1] . "',
                    '" . $data[2] . "',
                    '" . $data[3] . "',
                    '" . $data[4] . "',
                    '" . $data[5] . "',
                    '" . $data[6] . "',
                    '" . $data[7] . "',
                    '" . $total . "'
                )";

                $query = mysqli_query($conn, $insert);
                if ($query) {
                    $status = "success";
                } else {
                    $status = "failed";
                }
            }
        }

        // Close the file
        fclose($h);
    } else {
        echo "<script>alert('invalid input');window.location='master_tipe_mobil.php';</script>";
        $status = "";
    }
    echo "<script>alert('" . $status . "');window.location='master_tipe_mobil.php';</script>";
    // Display the code in a readable format

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
                                <h3 class="mr-2">Master Tipe Mobil</h3>
                                <a href="#" data-toggle="modal" class="add_new btn btn-primary form-control-sm mr-2 mt--1-5" data-target="#newBrandModal"><i class="fa fa-plus"></i> Tambah Baru</a>
                                <a href="javascript:void(0);" class="btn btn-success form-control-sm mt--1-5" data-toggle="modal" data-target="#ImportModal"><i class="fa fa-download"></i> Import CSV</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Merek Mobil</th>
                                            <th>Tipe Mobil</th>
                                            <th>Kategori</th>
                                            <th>Pakai Bahan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $n = 1;
                                        $sql = mysqli_query($conn, "SELECT * FROM tb_tipe_mobil ORDER BY id_tipe ASC");
                                        while ($row = mysqli_fetch_array($sql)) {

                                            $kategori = "";
                                            if ($row['kategori'] == "small") {
                                                $kategori = "Small";
                                            }
                                            if ($row['kategori'] == "medium") {
                                                $kategori = "Medium";
                                            }
                                            if ($row['kategori'] == "large") {
                                                $kategori = "Large";
                                            }
                                            if ($row['kategori'] == "extra_large") {
                                                $kategori = "Extra Large";
                                            }

                                            $merk = "";
                                            $select_merk = "SELECT * FROM tb_merk WHERE id = '" . $row['merk_mobil'] . "'";
                                            $query_merk = mysqli_query($conn, $select_merk);
                                            $data_merk = mysqli_fetch_array($query_merk);
                                            $jum_merk = mysqli_num_rows($query_merk);
                                            if ($row['merk_mobil'] > 0) {
                                                if ($jum_merk > 0) {
                                                    $merk = $data_merk['merk_mobil'];
                                                }
                                            }

                                            echo '
                                                <tr>
                                                    <td>' . $n . '</td>
                                                    <td>' . $merk . '</td>
                                                    <td>' . $row['tipe_mobil'] . '</td>
                                                    <td>' . $kategori . '</td>
                                                    <td>' . $row['total'] . ' CM</td>
                                           
                                                <td>
                                                <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal"
                                                  data-id="' . $row['id_tipe'] . '"
                                                  data-merk_mobil="' . $row['merk_mobil'] . '"
                                                  data-tipe_mobil="' . $row['tipe_mobil'] . '"
                                                  data-tahun="' . $row['tahun'] . '"
                                                  data-kategori="' . $row['kategori'] . '"
                                                  data-kaca_depan="' . $row['kaca_depan'] . '"
                                                  data-kaca_belakang="' . $row['kaca_skkb'] . '"
                                                  data-lainnya="' . $row['lainnya'] . '"
                                                  data-total="' . $row['total'] . '">
                                                <i class="fa fa-edit"></i> Ubah</a>
                                                <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                                                  data-id_hapus="' . $row['id_tipe'] . '">
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
                    <h5 class="modal-title" id="exampleModalLabel">Tipe Mobil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id" name="id">
                        <div class="form-group">
                            <label>Merek Mobil</label>
                            <select name="merk_mobil" id="" class="form-control merk_mobil" required>
                                <?php
                                $select_merk = "SELECT * FROM tb_merk ORDER BY id ASC";
                                $query_merk = mysqli_query($conn, $select_merk);
                                while ($row_merk = mysqli_fetch_array($query_merk)) {
                                    echo '
                                        <option value="' . $row_merk['id'] . '">' . $row_merk['merk_mobil'] . '</option>
                                    ';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Tipe Mobil</label>
                            <input type="text" name="tipe_mobil" id="" class="form-control tipe_mobil" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Tahun Mobil</label>
                            <input type="number" name="tahun_mobil" id="" class="form-control tahun_mobil" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" id="" class="form-control kategori">
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                                <option value="extra_large">Extra Large</option>
                            </select>
                        </div>
                        <hr>
                        <div class="text-center">
                            <p style="font-weight:bold;">Pemakaian Bahan (CM)</p>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label>Kaca Depan</label>
                            <input type="number" name="kaca_depan" id="" class="form-control kaca_depan" step="0.01" min="0" value="0" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Kaca SKKB</label>
                            <input type="number" name="kaca_belakang" id="" class="form-control kaca_belakang" step="0.01" min="0" value="0" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>lainnya</label>
                            <input type="number" name="lainnya" id="" class="form-control lainnya" step="0.01" min="0" value="0" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Total</label>
                            <input type="number" name="total" id="" class="form-control filled-input total" step="0.01" min="0" value="0" readonly>
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
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Tipe Mobil</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin menghapus tipe mobil ini ?
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
    $(document).on("click", '.edit_button', function(e) {
        var id = $(this).data('id');
        var merk_mobil = $(this).data('merk_mobil');
        var tipe_mobil = $(this).data('tipe_mobil');
        var tahun = $(this).data('tahun');
        var kategori = $(this).data('kategori');
        var kaca_depan = $(this).data('kaca_depan');
        var kaca_belakang = $(this).data('kaca_belakang');
        var lainnya = $(this).data('lainnya');
        var total = $(this).data('total');

        $(".id").val(id);
        $(".merk_mobil").val(merk_mobil);
        $(".tipe_mobil").val(tipe_mobil);
        $(".tahun_mobil").val(tahun);
        $(".kategori").val(kategori);
        $(".kaca_depan").val(kaca_depan);
        $(".kaca_belakang").val(kaca_belakang);
        $(".lainnya").val(lainnya);
        $(".total").val(total);
    });

    $(document).on("click", ".add_new", function() {
        $(".id").val("");
        $(".merk_mobil").val("");
        $(".tipe_mobil").val("");
        $(".tahun_mobil").val("");
        $(".kategori").val("small");
        $(".kaca_depan").val("0");
        $(".kaca_belakang").val("0");
        $(".lainnya").val("0");
        $(".total").val("0");
    });

    $(document).on("click", '.hapus_button', function(e) {
        var id_hapus = $(this).data('id_hapus');
        $(".id_hapus").val(id_hapus);
    });

    $(document).on("change", ".kaca_depan", function() {
        var kaca_depan = $(".kaca_depan").val();
        var kaca_belakang = $(".kaca_belakang").val();
        var lainnya = $(".lainnya").val();

        $.ajax({
            type: "POST",
            url: "ajax/ajax_count_total_tipe_mobil.php",
            data: {
                "kaca_depan": kaca_depan,
                "kaca_belakang": kaca_belakang,
                "lainnya": lainnya
            },
            cache: true,
            success: function(response) {
                $(".total").val(response);
            }
        });
    });

    $(document).on("change", ".kaca_belakang", function() {
        var kaca_depan = $(".kaca_depan").val();
        var kaca_belakang = $(".kaca_belakang").val();
        var lainnya = $(".lainnya").val();

        $.ajax({
            type: "POST",
            url: "ajax/ajax_count_total_tipe_mobil.php",
            data: {
                "kaca_depan": kaca_depan,
                "kaca_belakang": kaca_belakang,
                "lainnya": lainnya
            },
            cache: true,
            success: function(response) {
                $(".total").val(response);
            }
        });
    });

    $(document).on("change", ".baris1_kanan", function() {
        var kaca_depan = $(".kaca_depan").val();
        var kaca_belakang = $(".kaca_belakang").val();
        var lainnya = $(".lainnya").val();

        $.ajax({
            type: "POST",
            url: "ajax/ajax_count_total_tipe_mobil.php",
            data: {
                "kaca_depan": kaca_depan,
                "kaca_belakang": kaca_belakang,
                "lainnya": lainnya
            },
            cache: true,
            success: function(response) {
                $(".total").val(response);
            }
        });
    });

    $(document).on("change", ".baris1_kiri", function() {
        var kaca_depan = $(".kaca_depan").val();
        var kaca_belakang = $(".kaca_belakang").val();
        var lainnya = $(".lainnya").val();

        $.ajax({
            type: "POST",
            url: "ajax/ajax_count_total_tipe_mobil.php",
            data: {
                "kaca_depan": kaca_depan,
                "kaca_belakang": kaca_belakang,
                "lainnya": lainnya
            },
            cache: true,
            success: function(response) {
                $(".total").val(response);
            }
        });
    });

    $(document).on("change", ".baris2_kanan", function() {
        var kaca_depan = $(".kaca_depan").val();
        var kaca_belakang = $(".kaca_belakang").val();
        var lainnya = $(".lainnya").val();

        $.ajax({
            type: "POST",
            url: "ajax/ajax_count_total_tipe_mobil.php",
            data: {
                "kaca_depan": kaca_depan,
                "kaca_belakang": kaca_belakang,
                "lainnya": lainnya
            },
            cache: true,
            success: function(response) {
                $(".total").val(response);
            }
        });
    });

    $(document).on("change", ".baris2_kiri", function() {
        var kaca_depan = $(".kaca_depan").val();
        var kaca_belakang = $(".kaca_belakang").val();
        var lainnya = $(".lainnya").val();

        $.ajax({
            type: "POST",
            url: "ajax/ajax_count_total_tipe_mobil.php",
            data: {
                "kaca_depan": kaca_depan,
                "kaca_belakang": kaca_belakang,
                "lainnya": lainnya
            },
            cache: true,
            success: function(response) {
                $(".total").val(response);
            }
        });
    });

    $(document).on("change", ".baris3_kanan", function() {
        var kaca_depan = $(".kaca_depan").val();
        var kaca_belakang = $(".kaca_belakang").val();
        var lainnya = $(".lainnya").val();

        $.ajax({
            type: "POST",
            url: "ajax/ajax_count_total_tipe_mobil.php",
            data: {
                "kaca_depan": kaca_depan,
                "kaca_belakang": kaca_belakang,
                "lainnya": lainnya
            },
            cache: true,
            success: function(response) {
                $(".total").val(response);
            }
        });
    });

    $(document).on("change", ".baris3_kiri", function() {
        var kaca_depan = $(".kaca_depan").val();
        var kaca_belakang = $(".kaca_belakang").val();
        var lainnya = $(".lainnya").val();

        $.ajax({
            type: "POST",
            url: "ajax/ajax_count_total_tipe_mobil.php",
            data: {
                "kaca_depan": kaca_depan,
                "kaca_belakang": kaca_belakang,
                "lainnya": lainnya
            },
            cache: true,
            success: function(response) {
                $(".total").val(response);
            }
        });
    });

    $(document).on("change", ".lainnya", function() {
        var kaca_depan = $(".kaca_depan").val();
        var kaca_belakang = $(".kaca_belakang").val();
        var lainnya = $(".lainnya").val();

        $.ajax({
            type: "POST",
            url: "ajax/ajax_count_total_tipe_mobil.php",
            data: {
                "kaca_depan": kaca_depan,
                "kaca_belakang": kaca_belakang,
                "lainnya": lainnya
            },
            cache: true,
            success: function(response) {
                $(".total").val(response);
            }
        });
    });


    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>