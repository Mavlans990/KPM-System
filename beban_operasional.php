<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$hidn = "hidden";
$filled_input = "filled-input";
if ($_SESSION['grup'] == 'super' || $_SESSION['grup'] == "franchise") {
    $hidn = "";
    $filled_input = "";
}
$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
}

$from = date("Y-m-d");
$to = date("Y-m-d");

if (isset($_SESSION['tgl_from'])) {
    $from = $_SESSION['tgl_from'];
    $to = $_SESSION['tgl_to'];
}

if (isset($_POST['search'])) {
    $_SESSION['tgl_from'] = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $_SESSION['tgl_to'] = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    $_SESSION['cabang'] = mysqli_real_escape_string($conn, $_POST['cabang']);
}

if (isset($_POST['reset'])) {
    unset($_SESSION['tgl_from']);
    unset($_SESSION['tgl_to']);
    unset($_SESSION['cabang']);
}

if (isset($_POST['save'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $tgl_buat = mysqli_real_escape_string($conn, $_POST['tanggal_buat']);
    $nama_beban = mysqli_real_escape_string($conn, $_POST['nama_beban']);
    $total = mysqli_real_escape_string($conn, $_POST['total_beban_hidden']);
    $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori_beban']);


    if ($id == "") {
        $query_insert = mysqli_query($conn, "INSERT INTO tb_operasional(
            id,
            tgl_buat,
            kategori,
            nama_beban,
            total,
            cabang,
            dibuat_oleh,
            dibuat_tgl
        ) VALUES(
            '',
            '" . $tgl_buat . "',
            '" . $kategori . "',
            '" . $nama_beban . "',
            '" . $total . "',
            '" . $id_cabang . "',
            '" . $_SESSION['id_user'] . "',
            '" . date("Y-m-d") . "'
        )");

        if ($query_insert) {
            $hasil = 1;
            $msg_status = "Success : Tambah beban";
        } else {
            $hasil = 0;
            $msg_status = "Failed : Tambah Beban";
        }
    } else {
        $query_update = mysqli_query($conn, "
            UPDATE tb_operasional SET
            tgl_buat = '" . $tgl_buat . "',
            kategori = '" . $kategori . "',
            nama_beban = '" . $nama_beban . "',
            total = '" . $total . "',
            cabang = '" . $id_cabang . "',
            diubah_oleh = '" . $_SESSION['id_user'] . "',
            diubah_tgl = '" . date("Y-m-d") . "' WHERE id = '" . $id . "'
        ");

        if ($query_update) {
            $hasil = 1;
            $msg_status = "Success : Ubah beban";
        } else {
            $hasil = 0;
            $msg_status = "Failed : Ubah beban";
        }
    }
    echo '
        <script>alert("' . $msg_status . '");window.location.href="beban_operasional.php"</script>
    ';
}

if (isset($_POST['delete'])) {
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus']);

    $query_delete = mysqli_query($conn, "DELETE FROM tb_operasional WHERE id = '" . $id_hapus . "'");

    if ($query_delete) {
        $msg_status = "Success : Hapus beban";
    } else {
        $msg_status = "Failed : Hapus beban";
    }

    echo '
        <script>alert("' . $msg_status . '");window.location.href="beban_operasional.php";</script>
    ';
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
                                <h3 class="mr-2">Beban Operasional</h3>
                                <a href="#" data-toggle="modal" class="add_new btn btn-primary form-control-sm mt--1-5" data-target="#newBrandModal"><i class="fa fa-plus"></i> Tambah Baru</a>
                            </div>
                        </div>
                        <div class="card-header py-1">
                            <form action="" method="post">
                                <br>
                                <div class="row no-gutters">
                                    <div class="col">
                                        <div class="form-group form-inline">
                                            <input type="date" name="tgl_from" id="" class="form-control form-control-sm mb--5" value="<?= $from; ?>" required>
                                            <span class="w--100 text-center">S/D</span>
                                            <input type="date" name="tgl_to" id="" class="form-control form-control-sm mt--5" value="<?= $to; ?>" required>
                                            <select name="cabang" id="" class="form-control form-control-sm mt--5 <?php echo $filled_input; ?>">
                                                <?php
                                                $where_branch = "";
                                                if ($_SESSION['group'] == "franchise") {
                                                    $where_branch = " WHERE id_cabang IN('" . $filter_cabang . "')";
                                                }
                                                if ($_SESSION['group'] == "admin") {
                                                    $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                                }

                                                $query_cabang = mysqli_query($conn, "SELECT 
                                                                                id_cabang,
                                                                                nama_cabang FROM tb_cabang
                                                                                " . $where_branch . " ORDER BY nama_cabang ASC");
                                                while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                                    $selected = "";
                                                    if (isset($_SESSION['cabang'])) {
                                                        if ($row_cabang['id_cabang'] == $_SESSION['cabang']) {
                                                            $selected = "selected";
                                                        }
                                                    }
                                                    echo '
                                                            <option value="' . $row_cabang['id_cabang'] . '" ' . $selected . '>' . $row_cabang['nama_cabang'] . '</option>
                                                        ';
                                                }
                                                ?>
                                            </select>
                                            <button type="submit" class="btn btn-primary form-control-sm mt--5 ml-1" name="search"><i class="fa fa-search"></i> Cari</button>
                                            <?php
                                            if (isset($_SESSION['tgl_from'])) {
                                                echo '
                                                        <button type="submit" class="btn btn-danger form-control-sm ml-1 mt--5" name="reset"><i class="fa fa-refresh"></i> Reset</button>
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
                                <table id="datable_1" class="table table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Tanggal</th>
                                            <th class="text-center">Cabang</th>
                                            <th class="text-center">Kategori Beban</th>
                                            <th class="text-center">Keterangan Beban</th>
                                            <th class="text-center">Total</th>
                                            <?php
                                            if ($_SESSION['group'] == "super") {
                                                echo '
                                                    <th></th>
                                                    ';
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['tgl_from'])) {
                                            $total_beban = 0;
                                            $tgl_from = $_SESSION['tgl_from'];
                                            $tgl_to = $_SESSION['tgl_to'];
                                            $cabang = $_SESSION['cabang'];

                                            $query_beban = mysqli_query($conn, "
                                                SELECT * FROM tb_operasional
                                                WHERE tgl_buat BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND cabang = '" . $cabang . "'
                                            ");
                                            while ($row_beban = mysqli_fetch_array($query_beban)) {
                                                $total_beban += $row_beban['total'];
                                                $query_cabang = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang = '" . $row_beban['cabang'] . "'");
                                                $data_cabang = mysqli_fetch_array($query_cabang);

                                                $query_kategori = mysqli_query($conn, "
                                                    SELECT * FROM tb_beban WHERE id_beban = '" . $row_beban['kategori'] . "'
                                                ");
                                                $data_kategori = mysqli_fetch_array($query_kategori);

                                                echo '
                                                    <tr>
                                                        <td class="text-center">' . date("d/m/Y", strtotime($row_beban['tgl_buat'])) . '</td>
                                                        <td class="text-center">' . $data_cabang['nama_cabang'] . '</td>
                                                        <td class="text-center">' . $data_kategori['nama_beban'] . '</td>
                                                        <td class="text-center">' . $row_beban['nama_beban'] . '</td>
                                                        <td class="text-right">' . number_format($row_beban['total']) . '</td>
                                                        ';

                                                if ($_SESSION['group'] == "super") {
                                                    echo '
                                                            <td class="text-center">
                                                                <a href="javascript:void(0)" class="btn btn-warning edit_beban" data-toggle="modal" data-target="#newBrandModal"
                                                                data-id = "' . $row_beban['id'] . '"
                                                                data-tgl_buat = "' . $row_beban['tgl_buat'] . '"
                                                                data-kategori = "' . $row_beban['kategori'] . '"
                                                                data-cabang = "' . $row_beban['cabang'] . '"
                                                                data-nama_beban = "' . $row_beban['nama_beban'] . '"
                                                                data-total = "' . $row_beban['total'] . '"
                                                                ><i class="fa fa-pencil"></i></a>
                                                                <a href="javascript:void(0)" class="btn btn-danger hapus_beban" data-toggle="modal" data-target="#DeleteBrandModal"
                                                                data-id="' . $row_beban['id'] . '"
                                                                ><i class="fa fa-trash-o"></i></a>
                                                            </td>
                                                            ';
                                                }

                                                echo '
                                                    </tr>
                                                ';
                                            }
                                            echo '
                                                <tr>
                                                    <td class="text-right" colspan="4"><h5>Total</h5></td>
                                                    <td colspan="2"><h5>' . number_format($total_beban) . '</h5></td>
                                                </tr>
                                            ';
                                        } else {
                                            echo '
                                                <tr>
                                                    <td colspan="6" class="text-center">Mohon Search terlebih dahulu</td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Beban Operasional</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id" name="id">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal_buat" id="" class="form-control tanggal_buat" value="<?php echo $from; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Cabang</label>
                            <select name="id_cabang" id="" class="form-control id_cabang <?php echo $filled_input; ?>">
                                <?php
                                $where_branch = "";
                                if ($_SESSION['group'] == "franchise") {
                                    $where_branch = " WHERE id_cabang IN('" . $filter_cabang . "')";
                                }
                                if ($_SESSION['group'] == "admin") {
                                    $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                }

                                $query_cabang = mysqli_query($conn, "
                                    SELECT 
                                    id_cabang,
                                    nama_cabang
                                    FROM tb_cabang
                                    " . $where_branch . "
                                ");
                                while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                    echo '
                                        <option value="' . $row_cabang['id_cabang'] . '">' . $row_cabang['nama_cabang'] . '</option>
                                    ';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Kategori Beban</label>
                            <select name="kategori_beban" id="" class="form-control kategori_beban">
                                <?php
                                $query_kategori = mysqli_query($conn, "SELECT * FROM tb_beban ORDER BY nama_beban");
                                while ($row_kategori = mysqli_fetch_array($query_kategori)) {
                                    echo '
                                            <option value="' . $row_kategori['id_beban'] . '">' . $row_kategori['nama_beban'] . '</option>
                                        ';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Keterangan Beban</label>
                            <input type="text" name="nama_beban" id="" class="form-control nama_beban" required>
                        </div>
                        <div class="form-group">
                            <label>Total Beban</label>
                            <input type="text" name="total_beban" id="" class="form-control text-right total_beban" min="0" value="0">
                            <input type="hidden" name="total_beban_hidden" id="" class="total_beban_hidden" min="0" value="0">
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
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Beban</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin menghapus beban ini ?
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
    <script src="vendors/jquery/dist/jquery.mask.min.js"></script>

</body>

</html>
<script type="text/javascript">
    $(document).ready(function() {

        function koma(nStr) {
            nStr += '';
            var x = nStr.split(',');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        $('.total_beban').mask('#,##0', {
            reverse: true
        });

        $(document).on("keyup", ".total_beban", function() {
            var subtotal = $(".total_beban").val();
            subtotal = subtotal.split(',').join('');
            if (isNaN(subtotal)) subtotal = 0;

            $('.total_beban_hidden').val(subtotal);
        });

        $(document).on("click", ".add_new", function() {
            $(".id").val("");
            $(".tgl_buat").val("");
            $(".nama_beban").val("");
            $(".total_beban").val("0");
        });

        $(document).on("click", ".edit_beban", function() {
            var id = $(this).data('id');
            var tgl_buat = $(this).data('tgl_buat');
            var kategori = $(this).data('kategori');
            var nama_beban = $(this).data('nama_beban');
            var cabang = $(this).data('cabang');
            var total = $(this).data('total');

            $('.id').val(id);
            $('.tgl_buat').val(tgl_buat);
            $('.kategori_beban').val(kategori);
            $('.nama_beban').val(nama_beban);
            $('.id_cabang').val(cabang);
            $('.total_beban').val(koma(total));
            $('.total_beban_hidden').val(total);
        });

        $(document).on("click", ".hapus_beban", function() {
            var id_hapus = $(this).data('id');

            $('.id_hapus').val(id_hapus);
        });

    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>