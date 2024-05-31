<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$id_customer = generate_customer();

if (isset($_POST['save'])) {
    $valid = 1;

    $id = mysqli_real_escape_string($conn, $_POST['id_customer']);
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $alamat_lengkap =  mysqli_real_escape_string($conn, $_POST['alamat_lengkap']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $source_customer = mysqli_real_escape_string($conn, $_POST['source_customer']);
    $no_npwp = mysqli_real_escape_string($conn, $_POST['no_npwp']);
    $nama_npwp = mysqli_real_escape_string($conn, $_POST['nama_npwp']);
    $alamat_npwp = mysqli_real_escape_string($conn, $_POST['alamat_npwp']);


    $select_customer = "SELECT * FROM tb_customer WHERE id_customer = '" . $id . "'";
    $query_customer = mysqli_query($conn, $select_customer);
    $jum_customer = mysqli_num_rows($query_customer);
    if ($jum_customer < 1) {
        $insert = "INSERT INTO tb_customer(
            id_customer,
            nama_customer,
            kota,
            alamat_lengkap,
            no_telp,
            email,
            source_customer,
            no_npwp,
            nama_npwp,
            alamat_npwp
        ) VALUES(
            '" . $id . "',
            '" . $nama_customer . "',
            '" . $kota . "',
            '" . $alamat_lengkap . "',
            '" . $no_telp . "',
            '" . $email . "',
            '" . $source_customer . "',
            '" . $no_npwp . "',
            '" . $nama_npwp . "',
            '" . $alamat_npwp . "'
        )";
        $query = mysqli_query($conn, $insert);
        if ($query) {
            $valid = 1;
            $msg_status = "Simpan data berhasil";
        } else {
            $valid = 0;
            $msg_status = "Simpan data gagal";
        }
    } else {
        $update = "UPDATE tb_customer SET
        nama_customer = '" . $nama_customer . "',
        kota = '" . $kota . "',
        alamat_lengkap = '" . $alamat_lengkap . "',
        no_telp = '" . $no_telp . "',
        email = '" . $email . "',
        source_customer = '" . $source_customer . "',
        no_npwp = '" . $no_npwp . "',
        nama_npwp = '" . $nama_npwp . "',
        alamat_npwp = '" . $alamat_npwp . "' WHERE id_customer = '" . $id . "';
        ";
        $query = mysqli_query($conn, $update);
        if ($query) {
            $valid = 1;
            $msg_status = "Ubah data berhasil";
        } else {
            $valid = 0;
            $msg_status = "Ubah data gagal";
        }
    }

    echo "<script>alert('" . $msg_status . "');window.location='master_customer.php'</script>";
}

if (isset($_POST['delete'])) {
    $valid = 1;
    $query = mysqli_query($conn, "DELETE FROM tb_customer WHERE id_customer = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
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

$search_nm_cust = "";
if (isset($_POST['search'])) {
    $search_nm_cust = mysqli_real_escape_string($conn, $_POST['search_nm_cust']);
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
    <link rel="shortcut icon" href="logo_inventory-removebg-preview (1).png">
    <link rel="icon" href="logo_inventory-removebg-preview (1).png" type="image/x-icon">

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
                                <h3 class="mr-2">Master Customer</h3>
                                <a href="#" data-toggle="modal" class="add_new btn btn-primary form-control-sm mt--1-5" data-target="#newBrandModal"><i class="fa fa-plus"></i> Tambah Baru</a>
                            </div>
                        </div>
                        <div class="col-6">
                            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search_nm_cust" id="" class="form-control form-control-sm" placeholder="Nama Customer" value="<?php echo $search_nm_cust; ?>">
                                    <button type="submit" class="btn btn-xs btn-info ml-5" name="search"><i class="fa fa-search"></i> Cari</button>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama Customer</th>
                                            <th>No. Telp</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $where_cust = "";
                                        if ($search_nm_cust !== "") {
                                            $where_cust = " WHERE nama_customer LIKE '%" . $search_nm_cust . "%'";
                                        }
                                        $n = 1;
                                        $sql = mysqli_query($conn, "SELECT * FROM tb_customer " . $where_cust . " ORDER BY id_customer ASC LIMIT 50");
                                        while ($row = mysqli_fetch_array($sql)) {
                                            echo '
                                                <tr>
                                                    <td>' . $row['id_customer'] . '</td>
                                                    <td>' . $row['nama_customer'] . '</td>
                                                    <td>' . $row['no_telp'] . '</td>
                                           
                                                <td>
                                                <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal"
                                                  data-id_customer="' . $row['id_customer'] . '"
                                                  data-nama_customer="' . $row['nama_customer'] . '"
                                                  data-kota="' . $row['kota'] . '"
                                                  data-alamat_lengkap="' . $row['alamat_lengkap'] . '"
                                                  data-no_telp="' . $row['no_telp'] . '"
                                                  data-email="' . $row['email'] . '"
                                                  data-source_customer="' . $row['source_customer'] . '"
                                                  data-no_npwp="' . $row['no_npwp'] . '"
                                                  data-nama_npwp="' . $row['nama_npwp'] . '"
                                                  data-alamat_npwp="' . $row['alamat_npwp'] . '">
                                                <i class="fa fa-edit"></i> Ubah</a>
                                                <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                                                  data-id_hapus="' . $row['id_customer'] . '">
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
                    <h5 class="modal-title" id="exampleModalLabel">Customer</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id" name="id">
                        <div class="form-group">
                            <label>ID Customer</label>
                            <input type="text" name="id_customer" id="" class="form-control filled-input id_customer" value="<?php echo $id_customer; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nama Customer</label>
                            <input type="text" name="nama_customer" id="" class="form-control nama_customer" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label>Kota</label>
                            <input type="text" name="kota" id="" class="form-control kota" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Alamat Lengkap</label>
                            <textarea name="alamat_lengkap" id="" cols="30" rows="3" class="form-control alamat_lengkap " autocomplete="off"></textarea>
                        </div>
                        <div class="form-group">
                            <label>No. Telp</label>
                            <input type="number" name="no_telp" id="" class="form-control no_telp" autocomplete="off" >
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="" class="form-control email" autocomplete="off" >
                        </div>
                        <div class="form-group">
                            <label>Source Customer</label>
                            <select name="source_customer" id="" class="form-control source_customer">
                                <option value="website">Website</option>
                                <option value="social_media">Social Media</option>
                                <option value="visit_store">Visit Store</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>No. NPWP</label>
                            <input type="text" name="no_npwp" id="" class="form-control no_npwp" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Nama NPWP</label>
                            <input type="text" name="nama_npwp" id="" class="form-control nama_npwp" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Alamat NPWP</label>
                            <textarea name="alamat_npwp" id="" cols="30" rows="3" class="form-control alamat_npwp" autocomplete="off"></textarea>
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
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Customer</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin menghapus data customer ini ?
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
        var id_customer = $(this).data('id_customer');
        var nama_customer = $(this).data('nama_customer');
        var kota = $(this).data('kota');
        var alamat_lengkap = $(this).data('alamat_lengkap');
        var no_telp = $(this).data('no_telp');
        var email = $(this).data('email');
        var source_customer = $(this).data('source_customer');
        var no_npwp = $(this).data('no_npwp');
        var nama_npwp = $(this).data('nama_npwp');
        var alamat_npwp = $(this).data('alamat_npwp');

        $(".id_customer").val(id_customer);
        $(".nama_customer").val(nama_customer);
        $(".kota").val(kota);
        $(".alamat_lengkap").val(alamat_lengkap);
        $(".no_telp").val(no_telp);
        $(".email").val(email);
        $(".source_customer").val(source_customer);
        $(".no_npwp").val(no_npwp);
        $(".nama_npwp").val(nama_npwp);
        $(".alamat_npwp").val(alamat_npwp);
    });

    $(document).on("click", ".add_new", function() {
        $(".id_customer").val("<?php echo $id_customer; ?>");
        $(".nama_customer").val("");
        $(".kota").val("");
        $(".alamat_lengkap").val("");
        $(".no_telp").val("");
        $(".email").val("");
        $(".source_customer").val("");
        $(".no_npwp").val("");
        $(".nama_npwp").val("");
        $(".alamat_npwp").val("");
    });

    $(document).on("click", '.hapus_button', function(e) {
        var id_hapus = $(this).data('id_hapus');
        $(".id_hapus").val(id_hapus);
    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>