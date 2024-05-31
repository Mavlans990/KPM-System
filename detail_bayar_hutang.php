<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

if (!isset($_GET['id_po'])) {
    header("location:pelunasan_hutang.php");
} else {
    if ($_GET['id_po'] == "") {
        header("location:pelunasan_hutang.php");
    }
}

$id_po = mysqli_real_escape_string($conn, $_GET['id_po']);

if (isset($_POST['bayar'])) {
    $tgl_bayar = mysqli_real_escape_string($conn, $_POST['tgl_bayar']);
    $bank_acc = mysqli_real_escape_string($conn, $_POST['bank_acc']);

    $biaya_admin = mysqli_real_escape_string($conn, $_POST['biaya_admin']);
    if ($biaya_admin == "") {
        $biaya_admin = 0;
    } else {
        $biaya_admin = str_replace(",", "", $biaya_admin);
        $biaya_admin = (int) $biaya_admin;
    }

    $grand_total = mysqli_real_escape_string($conn, $_POST['grand_total']);

    $jurnal_1 = add_jurnal('', '2-2011', $bank_acc, $grand_total, $tgl_bayar, "Pembayaran Utang Bahan Baku", $_SESSION['id_user']);
    $id_jurnal = explode("|", $jurnal_1);
    $sts_jurnal = $id_jurnal[0];
    $id_jurnal = $id_jurnal[1];


    $valid = 1;
    if ($valid == 1) {
        $msg = "Proses bayar hutang berhasil !";

        $filter_po = " WHERE id_transaksi = '" . $id_po . "'";
        if (strpos($id_po, ",") !== false) {
            $filter_po = " WHERE id_transaksi IN ('" . str_replace(",", "','", $id_po) . "')";
        }

        $sql_update_po = mysqli_query($conn, "
            UPDATE
                tb_barang_masuk
            SET
                status_lunas = '1',
                tgl_lunas = '" . $tgl_bayar . "',
                id_jurnal = '" . $id_jurnal . "'
            " . $filter_po . "
        ");
        // if ($sql_update_po) {
        //     $sql_get_po = mysqli_query($conn, "SELECT * FROM po2 " . $filter_po);
        //     while ($row_po = mysqli_fetch_array($sql_get_po)) {
        //         if ($row_po['kurs'] == "IDR") {
        //             $sql_get_stock = mysqli_query($conn, "SELECT * FROM tb_stock WHERE id_product = '" . $row_po['id_bahan'] . "' AND cogs = '" . $row_po['harga'] . "'");
        //             if ($row_stock = mysqli_fetch_array($sql_get_stock)) {
        //                 $sql_update_stock = mysqli_query($conn, "
        //                     UPDATE
        //                         tb_stock
        //                     SET
        //                         stock = stock + " . $row_po['qty'] . "
        //                     WHERE
        //                         id_product = '" . $row_stock['id_product'] . "' AND
        //                         cogs = '" . $row_stock['cogs'] . "'
        //                 ");
        //             } else {
        //                 $sql_insert_stock = mysqli_query($conn, "
        //                     INSERT INTO tb_stock(
        //                         tgl,
        //                         id_product,
        //                         stock,
        //                         cogs
        //                     ) VALUES(
        //                         '" . date("Y-m-d H:i:s") . "',
        //                         '" . $row_po['id_bahan'] . "',
        //                         '" . $row_po['qty'] . "',
        //                         '" . $row_po['harga'] . "'
        //                     )
        //                 ");
        //             }
        //         }
        //     }
        // }
    } else {
        $msg = "Proses bayar hutang gagal !";
    }

    echo '
        <script>
            alert("' . $msg . '");
            window.location.href="pelunasan_hutang.php";
        </script>
    ';
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

    <style>
        .fs-11 {
            font-size: 11px !important;
        }

        .tb_detail_pembayaran td {
            border: none !important;
        }

        .tb_detail_pembayaran th {
            border: none !important;
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
                                <h3 class="mr-2">Detail Bayar Hutang</h3>
                                <!-- <a href="javascript:void(0);" class="add_new btn btn-primary form-control-sm mr-2 ml--5" data-toggle="modal" data-target="#newBarangModal"><i class="fa fa-plus"></i> Add New</a> -->
                                <!-- <a href="javascript:void(0);" class="btn btn-success form-control-sm" data-toggle="modal" data-target="#ImportModal"><i class="fa fa-download"></i> Import CSV</a> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                <thead>
                                    <tr>
                                        <th class="fs-11">No PO</th>
                                        <th class="fs-11">Tgl</th>
                                        <th class="fs-11">Supplier</th>
                                        <th class="fs-11">Status</th>
                                        <th class="text-center fs-11">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $filter_hutang = " WHERE a.id_transaksi = '" . $id_po . "'";
                                    if (strpos($id_po, ",") !== false) {
                                        $filter_hutang = " WHERE a.id_transaksi IN ('" . str_replace(",", "','", $id_po) . "')";
                                    }
                                    $grand_total = 0;
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
                                            " . $filter_hutang . "
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

                                        $grand_total += $total;


                                        echo '
                                                <tr ' . $style . '>
                                                    <td class="fs-11">' . $id_po . '</td>
                                                    <td class="fs-11">' . $tgl_po . '</td>
                                                    <td class="fs-11">' . $nm_supp . '</td>
                                                    <td class="fs-11">' . $status . '</td>
                                                    <td class="text-right fs-11">' . number_format($total) . '</td>
                                                </tr>
                                            ';
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                                <div class="container-fluid justify-content-center d-flex justify-content-center">
                                    <div class="col-6 text-center">
                                        <h5 class="text-center">Detail Pembayaran</h5>
                                        <table class="table  tb_detail_pembayaran">
                                            <tbody>
                                                <tr>
                                                    <td>Tgl Bayar</td>
                                                    <td>:</td>
                                                    <td>
                                                        <input type="date" name="tgl_bayar" id="" class="form-control form-control-sm" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Bank Acc</td>
                                                    <td>:</td>
                                                    <td>
                                                        <select name="bank_acc" id="" class="form-control form-control-sm bank_acc" required>
                                                            <?php
                                                            $sql_get_akun = mysqli_query($conn, "
                                                        SELECT 
                                                            kode_akun,
                                                            nm_akun
                                                        FROM
                                                            m_akun
                                                        WHERE
                                                            kode_akun LIKE '%1-100%' OR
                                                            Kode_akun LIKE '%2-210%'
                                                        ORDER BY m_akun_id ASC
                                                    ");
                                                            while ($row_akun = mysqli_fetch_array($sql_get_akun)) {
                                                                $kode_akun = $row_akun['kode_akun'];
                                                                $nm_akun = $row_akun['nm_akun'];
                                                                echo '
                                                            <option value="' . $kode_akun . '">' . $nm_akun . '</option>
                                                        ';
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Biaya Admin</td>
                                                    <td>:</td>
                                                    <td>
                                                        <input type="text" name="biaya_admin" id="" class="form-control form-control-sm text-right biaya_admin">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Total Bayar</td>
                                                    <td>:</td>
                                                    <td>
                                                        <input type="hidden" name="" class="grand_total" value="<?php echo $grand_total; ?>">
                                                        IDR. <span><?php echo number_format($grand_total); ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" class="text-center">
                                                        <input type="hidden" name="grand_total" value="<?php echo $grand_total; ?>">
                                                        <button type="submit" class="btn btn-sm btn-success" name="bayar">Bayar</button>
                                                        <a href="pelunasan_hutang.php" class="btn btn-sm btn-danger">Batal</a>
                                                    </td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </form>
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

    $(".biaya_admin").mask('#,##0', {
        reverse: true
    });

    // $(document).on("keyup", ".biaya_admin", function() {
    //     var biaya_admin = $(this).val();

    //     if (biaya_admin == "") {
    //         var biaya_admin = 0;
    //     } else {
    //         biaya_admin = biaya_admin.split(",").join("");
    //         biaya_admin = parseInt(biaya_admin);
    //     }

    //     var grand_total = parseInt($(".grand_total").val());

    //     var total = parseInt(grand_total + biaya_admin);

    //     $(".total_biaya").html(koma(biaya_admin));
    // });


    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>