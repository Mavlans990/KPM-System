<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

if (isset($_POST['simpan'])) {
    $id_petty = mysqli_real_escape_string($conn,$_POST['id_petty']);
    $petty_tipe = mysqli_real_escape_string($conn,$_POST['petty_tipe']);
    $tgl_petty = mysqli_real_escape_string($conn,$_POST['tgl_petty']);
    $keterangan = mysqli_real_escape_string($conn,$_POST['keterangan']);
    $jumlah = mysqli_real_escape_string($conn,$_POST['jumlah']);
    $jumlah = str_replace(",", "", $jumlah);
    if ($jumlah == "") {
        $jumlah = 0;
    }

    $in_cash = 0;
    $out_cash = 0;
    if ($petty_tipe == "in") {
        $in_cash = floatval($jumlah);
    }
    if ($petty_tipe == "out") {
        $out_cash = floatval($jumlah);
    }

    if ($id_petty == "") {
        begin();
        $this_date = date('y');
        $query = "SELECT max(id_petty_kura) as kodeTerbesar FROM tb_petty_cash_kura_kura WHERE id_petty_kura LIKE 'PTY-" . $this_date . "-%' ";
        $sql_query = mysqli_query($conn,$query);
        if ($data = mysqli_fetch_array($sql_query)) {
            $kodeBarang = $data['kodeTerbesar'];
            $urutan = (int) substr($kodeBarang, 7, 6);
            $urutan++;
            $tahun = date('y') . "-";
            $huruf = "PTY-";
            $kodepetty = $huruf . $tahun . sprintf("%06s", $urutan);
        }else {
            $kodepetty = "PTY-" . date('y') . "-000001";
        }

        $path_bukti = "";
        if ($_FILES['bukti']['name'] !== "") {
            move_uploaded_file($_FILES['bukti']['tmp_name'], "image/bukti_petty/" . $kodepetty . "-" . $_FILES['bukti']['name']);
            $path_bukti = "image/bukti_petty/" . $kodepetty . "-" . $_FILES['bukti']['name'];
        }

        $query_petty = "
            INSERT INTO tb_petty_cash_kura(
                id_petty_kura,
                tgl,
                keterangan,
                in_cash,
                out_cash,
                bukti_petty,
                dibuat_oleh,
                dibuat_tgl
            ) VALUES(
                '" . $kodepetty . "',
                '" . $tgl_petty . "',
                '" . $keterangan . "',
                '" . $in_cash . "',
                '" . $out_cash . "',
                '" . $path_bukti . "',
                '" . $_SESSION['id_user'] . "',
                '" . date("Y-m-d H:i:s") . "'
            )
        ";

        $sql_insert_petty_cash = mysqli_query($conn,$query_petty);

        if ($sql_insert_petty_cash) {
            $valid = 1;
            commit();
        } else {
            $valid = 0;
            rollback();
        }

        if ($valid == 1) {
            $msg = "Input petty cash berhasil !";
        } else {
            $msg = "Input petty cash gagal !";
        }
    } else {
        $path_bukti = "";
        if ($_FILES['bukti']['name'] !== "") {
            move_uploaded_file($_FILES['bukti']['tmp_name'], "image/bukti_petty/" . $id_petty . "-" . $_FILES['bukti']['name']);
            $path_bukti = "bukti_petty = 'image/bukti_petty/" . $id_petty . "-" . $_FILES['bukti']['name'] . "',";
        }

        $sql_update= "
            UPDATE
                tb_petty_cash_kura
            SET
                tgl = '" . $tgl_petty . "',
                keterangan = '" . $keterangan . "',
                in_cash = '" . $in_cash . "',
                out_cash = '" . $out_cash . "',
                " . $path_bukti . "
                diubah_oleh = '" . $_SESSION['id_user'] . "',
                diubah_tgl = '" . date("Y-m-d H:i:s") . "'
            WHERE
                id_petty_kura = '" . $id_petty . "'
        ";

        $sql_update_petty = mysqli_query($conn,$sql_update);

        if ($sql_update_petty) {
            $valid = 1;
            commit();
        } else {
            $valid = 0;
            rollback();
        }

        if ($valid == 1) {
            $msg = "Ubah petty cash berhasil !";
        } else {
            $msg = "Ubah petty cash gagal !";
        }
    }


    echo '
        <script>
            alert("' . $msg . '");
            window.location.href="petty_cash_kura.php";
        </script>
    ';
}

if (isset($_POST['hapus'])) {
    $id_petty = mysqli_real_escape_string($conn,$_POST['id_petty']);

    begin();
    $sql_del = "
        DELETE FROM tb_petty_cash_kura WHERE id_petty_kura = '" . $id_petty . "'
    ";

    $sql_del_petty_cash = mysqli_query($conn,$sql_del);

    if ($sql_del_petty_cash) {
        $valid = 1;
        commit();
    } else {
        $valid = 0;
        rollback();
    }

    if ($valid == 1) {
        $msg = "Hapus petty cash berhasil !";
    } else {
        $msg = "Hapus petty cash gagal !";
    }

    echo '
        <script>
            alert("' . $msg . '");
            window.location.href="petty_cash_kura.php";
        </script>
    ';
}

$tgl_from = date("Y-m") . "-01";
$tgl_to = date("Y-m-d");
if (isset($_POST['search'])) {
    $tgl_from = mysqli_real_escape_string($conn,$_POST['tgl_from']);
    $tgl_to = mysqli_real_escape_string($conn,$_POST['tgl_to']);
}


?>
<!DOCTYPE html>
<html lang="en">
<meta charset="UTF-8" />
    
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Invoice System</title>
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
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">

                    <!-- Page Heading -->
                    
                    <h1 class="h3 mb-2 text-gray-800">Petty Cash Kura2
                        <button type="button" class="btn btn-sm btn-primary ml-2 add_petty" data-toggle="modal" data-target="#addPetty">Add New</button>
                    </h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                                <div class="col-7">
                                    <div class="input-group">
                                        <input type="date" class="form-control form-control-sm filled-input" name="tgl_from" value="<?php echo $tgl_from; ?>" readonly>
                                        <span class="ml-2 mr-2">S/D</span>
                                        <input type="date" class="form-control form-control-sm filled-input" name="tgl_to" value="<?php echo $tgl_to; ?>" readonly>
                                        <button type="submit" class="btn btn-sm btn-info ml-2" name="search">Cari</button>
                                        <a href="export_excel_petty_cash_kura.php?tgl_from=<?php echo $tgl_from; ?>&tgl_to=<?php echo $tgl_to ?>" class="btn btn-sm btn-success ml-2" target="_blank">Export Excel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <?php
                            $saldo = 0;
                            $sql_saldo = "SELECT SUM(in_cash) AS ttl_in,SUM(out_cash) AS ttl_out FROM tb_petty_cash_kura WHERE tgl < '" . $tgl_from . "'";
                            $sql_get_saldo = mysqli_query($conn,$sql_saldo);
                            if ($row_saldo = mysqli_fetch_array($sql_get_saldo)) {
                                $saldo = $row_saldo['ttl_in'] - $row_saldo['ttl_out'];
                            }

                            $sisa_saldo = $saldo;
                            ?>
                            <h4 class="mb-3">Saldo Awal : <?php echo number_format($saldo, 2); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th class="text-center">Tgl</th>
                                            <th class="text-center">Keterangan</th>
                                            <th class="text-center">In</th>
                                            <th class="text-center">Out</th>
                                            <th class="text-center">Saldo</th>
                                            <?php
                                            if ($_SESSION['grup'] == "super") {
                                                echo '
                                                    <th class="text-center">Action</th>
                                                ';
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no_petty = 1;
                                        $ttl_in = 0;
                                        $ttl_out = 0;
                                        $sql_petty_cash = mysqli_query($conn,"SELECT * FROM tb_petty_cash_kura WHERE tgl BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' ORDER BY tgl ASC");
                                        while ($row_petty = mysqli_fetch_array($sql_petty_cash)) {
                                            $in_cash = $row_petty['in_cash'];
                                            $out_cash = $row_petty['out_cash'];

                                            $ttl_in += $in_cash;
                                            $ttl_out += $out_cash;

                                            $sisa_saldo = $sisa_saldo + $in_cash - $out_cash;

                                            $keterangan = nl2br($row_petty['keterangan']);
                                            if ($row_petty['bukti_petty'] !== "" && file_exists($row_petty['bukti_petty'])) {
                                                $keterangan = '<a href="' . $row_petty['bukti_petty'] . '" target="_blank">' . nl2br($row_petty['keterangan']) . '</a>';
                                            }

                                            echo '
                                                <tr>
                                                    <td class="text-center">' . $no_petty . '</td>
                                                    <td class="text-center">' . date("d/m/Y", strtotime($row_petty['tgl'])) . '</td>
                                                    <td>' . $keterangan . '</td>
                                                    <td class="text-right">' . number_format($row_petty['in_cash'], 2) . '</td>
                                                    <td class="text-right">(' . number_format($row_petty['out_cash'], 2) . ')</td>
                                                    <td class="text-right">' . number_format($sisa_saldo, 2) . '</td>
                                                    ';

                                            
                                                echo '
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-warning edit_petty" data-toggle="modal" data-target="#addPetty" 
                                                        data-id_petty="' . $row_petty['id_petty_kura'] . '"
                                                        data-tgl="' . $row_petty['tgl'] . '"
                                                        data-keterangan="' . $row_petty['keterangan'] . '"
                                                        data-in_cash="' . $row_petty['in_cash'] . '"
                                                        data-out_cash="' . $row_petty['out_cash'] . '">Ubah</button>

                                                        <button type="button" class="btn btn-sm btn-danger del_petty" data-toggle="modal" data-target="#delPetty" 
                                                        data-id_petty="' . $row_petty['id_petty_kura'] . '">Hapus</button>
                                                    </td>
                                                ';
                                            

                                            echo '
                                                </tr>
                                            ';
                                            $no_petty++;
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
    

    <!-- Logout Modal-->
   
    <!-- DELETE MODAL -->
    <div class="modal fade" id="addPetty" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Petty Cash</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_petty" class="id_petty">
                        <div class="form-group">
                            <label for="">Tipe</label>
                            <select class="form-control form-control-sm petty_type" name="petty_tipe" required>
                                <option value=""></option>
                                <option value="in">In Cash</option>
                                <option value="out">Out Cash</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Tanggal</label>
                            <input type="date" class="form-control form-control-sm tgl_petty" name="tgl_petty" value="<?php echo date("Y-m-d"); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <textarea id="" cols="30" rows="3" class="form-control form-control-sm keterangan" name="keterangan"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="">Bukti</label>
                            <input type="file" name="bukti" class="form-control form-control-sm bukti_petty" id="">
                        </div>
                        <div class="form-group">
                            <label for="">Jumlah</label>
                            <input type="text" name="jumlah" id="" class="form-control form-control-sm text-right jumlah input_nominal" autocomplete="false">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-primary" name="simpan" value="Simpan">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delPetty" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Petty Cash</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id_petty" class="id_petty">
                        Anda yakin ingin hapus data petty cash ini ?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-danger" name="hapus" value="Hapus">
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

            $(document).on("click", ".add_petty", function(e) {
                $(".id_petty").val("");
                $(".petty_type").val("");
            
                $(".keterangan").val("");
                $(".jumlah").val("");

                $(".bukti_petty").val("");
                // $(".bukti_petty").attr("required", true);
            });

            $(document).on("click", ".edit_petty", function(e) {
                var id_petty = $(this).data('id_petty');
                var tgl = $(this).data('tgl');
                var keterangan = $(this).data('keterangan');
                var in_cash = $(this).data('in_cash');
                var out_cash = $(this).data('out_cash');

                if (in_cash > 0) {
                    var nilai = parseFloat(in_cash);
                    $(".petty_type").val("in");
                } else {
                    var nilai = parseFloat(out_cash);
                    $(".petty_type").val("out");
                }

                $(".id_petty").val(id_petty);
                $(".tgl_petty").val(tgl);
                $(".keterangan").val(keterangan);
                $(".jumlah").val(koma(nilai.toFixed(2)));
                $(".bukti_petty").attr("required", false);
            });

            $(document).on("click", ".del_petty", function(e) {
                var id_petty = $(this).data('id_petty');

                $(".id_petty").val(id_petty);
            });
        });
    </script>

</body>

</html>