<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

if ($_SESSION['group'] !== "super") {
    session_destroy();
    header('Location:index.php');
}

$hidn = "hidden";
if ($_SESSION['grup'] == 'super') {
    $hidn = "";
}

if (isset($_POST['submit_cabang'])) {
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);

    $_SESSION['cabang_hpp'] = $cabang;
    header("location:setting_hpp.php");
}
if (isset($_POST['reset_cabang'])) {
    unset($_SESSION['cabang_hpp']);
    header("location:setting_hpp.php");
}

if (isset($_POST['ubah_hpp'])) {
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    $id_bahan = mysqli_real_escape_string($conn, $_POST['id_bahan']);
    $hpp = mysqli_real_escape_string($conn, $_POST['hpp_hidden']);

    $query_stock = mysqli_query($conn, "
        SELECT *
        FROM tb_stock_cabang
        WHERE id_bahan = '" . $id_bahan . "' AND id_cabang = '" . $cabang . "'
    ");
    $jum_stock = mysqli_num_rows($query_stock);
    if ($jum_stock < 1) {
        $query_insert = mysqli_query($conn, "
            INSERT INTO tb_stock_cabang(
                id,
                id_bahan,
                id_cabang,
                hpp
            ) VALUES(
                '',
                '" . $id_bahan . "',
                '" . $cabang . "',
                '" . $hpp . "'
            )
        ");
        if ($query_insert) {
            $valid = 1;
        } else {
            $valid = 0;
        }
    } else {
        $query_update = mysqli_query($conn, "
            UPDATE tb_stock_cabang SET
            hpp = '" . $hpp . "'
            WHERE id_bahan = '" . $id_bahan . "' AND id_cabang = '" . $cabang . "'
        ");
        if ($query_update) {
            $valid = 1;
        } else {
            $valid = 0;
        }
    }

    if ($valid == 1) {
        echo '
            <script>alert("Success : ubah hpp");window.location.href="setting_hpp.php";</script>
        ';
    } else {
        echo '
            <script>alert("Failed : ubah hpp");window.location.href="setting_hpp.php";</script>
        ';
    }
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
                                <h1 class="h3 mb-2 text-gray-800">Setting HPP</h1>
                            </div>
                            <div class="">
                                <form action="" method="post">
                                    <br>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group form-inline">
                                                <select name="cabang" id="" class="form-control">
                                                    <option value="">-- Pilih Cabang --</option>
                                                    <?php
                                                    $where_branch = "";
                                                    if ($_SESSION['group'] !== "super") {
                                                        $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                                    }

                                                    $select_cabang = "SELECT * FROM tb_cabang " . $where_branch . " ORDER BY nama_cabang ASC";
                                                    $query_cabang = mysqli_query($conn, $select_cabang);
                                                    while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                                        $selected = "";
                                                        if (isset($_SESSION['cabang_hpp'])) {
                                                            if ($row_cabang['id_cabang'] == $_SESSION['cabang_hpp']) {
                                                                $selected = "selected";
                                                            }
                                                        }
                                                        echo '
                                                            <option value="' . $row_cabang['id_cabang'] . '" ' . $selected . '>' . $row_cabang['nama_cabang'] . '</option>
                                                        ';
                                                    }
                                                    ?>
                                                </select>
                                                <button type="submit" class="btn btn-primary mt--5" name="submit_cabang">Pilih</button>
                                                <?php
                                                if (isset($_SESSION['cabang_hpp'])) {
                                                    echo '
                                                        <button type="submit" class="btn btn-danger ml-1 mt--5" name="reset_cabang">Reset</button>
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
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>Nama Bahan</th>
                                            <th>HPP</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['cabang_hpp'])) {
                                            $x = 1;

                                            $query_bahan = mysqli_query($conn, "
                                                SELECT *
                                                FROM tb_bahan 
                                                ORDER BY id_bahan ASC
                                            ");
                                            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                                                $hpp = 0;

                                                $query_stock = mysqli_query($conn, "
                                                    SELECT hpp FROM tb_stock_cabang
                                                    WHERE id_cabang = '" . $_SESSION['cabang_hpp'] . "' AND id_bahan = '" . $row_bahan['id_bahan'] . "'
                                                ");
                                                while ($row_stock = mysqli_fetch_array($query_stock)) {
                                                    $hpp = $hpp + $row_stock['hpp'];
                                                }
                                                echo '
                                                    <tr>
                                                        <th>' . $x . '</th>
                                                        <th>' . $row_bahan['nama_bahan'] . '</th>
                                                        <th>' . number_format($hpp) . '</th>
                                                        <th>
                                                            <button class="btn btn-success form-control-sm ubah_hpp" data-toggle="modal" data-target="#ubahHpp"
                                                            data-cabang="' . $_SESSION['cabang_hpp'] . '"
                                                            data-id_bahan="' . $row_bahan['id_bahan'] . '"
                                                            data-hpp="' . $hpp . '"
                                                            ><i class="fa fa-pencil"></i></button>
                                                        </th>
                                                    </tr>
                                                ';
                                                $x++;
                                            }
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

    <div class="modal fade" id="ubahHpp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ubah Hpp</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="cabang" class="cabang">
                        <input type="hidden" name="id_bahan" class="id_bahan">
                        <div class="form-group">
                            <label>HPP</label>
                            <input type="text" name="hpp" class="form-control hpp">
                            <input type="hidden" name="hpp_hidden" class="hpp_hidden">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-primary" name="ubah_hpp" value="Ubah">
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

        $(".hpp").mask('#,##0', {
            reverse: true
        });

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

        $(document).on("keyup", ".hpp", function() {
            var subtotal = $(".hpp").val();
            subtotal = subtotal.split(',').join('');
            if (isNaN(subtotal)) subtotal = 0;

            $('.hpp_hidden').val(subtotal);
        });

        $(document).on("click", ".ubah_hpp", function() {
            var cabang = $(this).data('cabang');
            var id_bahan = $(this).data('id_bahan');
            var hpp = $(this).data('hpp');

            $(".cabang").val(cabang);
            $(".id_bahan").val(id_bahan);
            $(".hpp").val(koma(hpp));
            $(".hpp_hidden").val(hpp);

        });

    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>