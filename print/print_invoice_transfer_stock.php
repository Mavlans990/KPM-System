<?php
include "../lib/koneksi.php";
session_start();

$id_inv_out = mysqli_real_escape_string($conn, $_GET['id_inv_out']);

$query_inv_out = mysqli_query($conn, "
    SELECT
    inv_date,
    create_by,
    id_branch
    FROM inv_adjust_out
    WHERE id_inv_out = '" . $id_inv_out . "'  GROUP BY id_inv_out
");
$data_inv_out = mysqli_fetch_array($query_inv_out);

$query_inv_in = mysqli_query($conn, "
    SELECT
    id_branch
    FROM inv_adjust_in
    WHERE id_inv_in = '" . $id_inv_out . "' GROUP BY id_inv_in
");
$data_inv_in = mysqli_fetch_array($query_inv_in);

$query_cabang_from = mysqli_query($conn, "
    SELECT
    nama_cabang
    FROM tb_cabang
    WHERE id_cabang = '" . $data_inv_out['id_branch'] . "'
");
$data_cabang_from = mysqli_fetch_array($query_cabang_from);

$query_cabang_in = mysqli_query($conn, "
    SELECT
    nama_cabang
    FROM tb_cabang
    WHERE id_cabang = '" . $data_inv_in['id_branch'] . "'
");
$data_cabang_in = mysqli_fetch_array($query_cabang_in);

$query_karyawan = mysqli_query($conn, "
    SELECT 
    nama_lengkap
    FROM tb_karyawan
    WHERE user_id = '" . $data_inv_out['create_by'] . "'
");
$data_karyawan = mysqli_fetch_array($query_karyawan);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Coolplus System</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="../favicon.ico">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="../vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="../vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- select2 CSS -->
    <link href="../vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="../vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="../vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="../dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body id="page-top">



    <div class="container">
        <div class="col-12 text-center">
            <img src="../dist/img/main-logo.png" width="300px" alt="">
            <h3>Transfer Stock</h3>
        </div>
        <div class="col-5" style="margin-top:20px;">
            <table border="0">
                <tr>
                    <th style="width:140px;">Nomor Transaksi</th>
                    <th style="width:20px;">:</th>
                    <td><?= $id_inv_out; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Tanggal Transaksi</th>
                    <th style="width:20px;">:</th>
                    <td><?= date("d/m/Y", strtotime($data_inv_out['inv_date'])); ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Nama Penginput</th>
                    <th style="width:20px;">:</th>
                    <td><?= $data_karyawan['nama_lengkap']; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Dari Cabang</th>
                    <th style="width:20px;">:</th>
                    <td><?= $data_cabang_from['nama_cabang']; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Ke Cabang</th>
                    <th style="width:20px;">:</th>
                    <td><?= $data_cabang_in['nama_cabang']; ?></td>
                </tr>
            </table>
        </div>
        <div class="col-12 mt-4">
            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                <thead>
                    <tr>
                        <th class="text-center">ID Product</th>
                        <th class="text-center">Nama Bahan</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center">UOM</th>
                        <th class="text-center">Harga Beli</th>
                    </tr>
                </thead>
                <tbody class="daftar_bahan">
                    <?php
                    $grand_total = 0;
                    $query_inv_out = mysqli_query($conn, "
                        SELECT
                        inv_date,
                        create_by,
                        id_branch,
                        id_product,
                        stock_out,
                        biaya
                        FROM inv_adjust_out
                        WHERE id_inv_out = '" . $id_inv_out . "'
                    ");
                    while ($row_inv_out = mysqli_fetch_array($query_inv_out)) {
                        $grand_total = $grand_total + $row_inv_out['biaya'];
                        $query_bahan = mysqli_query($conn, "
                            SELECT
                            id_bahan,
                            nama_bahan,
                            uom,
                            jenis_bahan
                            FROM tb_bahan
                            WHERE id_bahan = '" . $row_inv_out['id_product'] . "'
                        ");
                        $data_bahan = mysqli_fetch_array($query_bahan);

                        if ($data_bahan['jenis_bahan'] == "kaca_film") {
                            $qty = number_format($row_inv_out['stock_out']) . " / " . number_format((float)$row_inv_out['stock_out'] / 2900, 2, '.', '') . " Roll";
                        } else {
                            $qty = number_format($row_inv_out['stock_out']);
                        }

                        echo '
                            <tr>
                                <td class="text-center">
                                    ' . $data_bahan['id_bahan'] . '
                                </td>
                                <td class="text-center">
                                    ' . $data_bahan['nama_bahan'] . '
                                </td>
                                <td class="text-right">
                                    ' . $qty . '
                                </td>
                                <td class="text-center">
                                    ' . $data_bahan['uom'] . '
                                </td>
                                <td class="text-right">
                                    ' . number_format($row_inv_out['biaya']) . '
                                </td>
                            </tr>
                        ';
                    }
                    echo '
                        <tr>
                            <td class="text-right" colspan="4">Grand Total</td>
                            <td class="text-right">' . number_format($grand_total) . '</td>
                        </tr>
                    ';
                    ?>
                </tbody>
            </table>
            <table class="w-100 mt-5">
                <tr>
                    <th style="width:85%;padding-top:9%;"></th>
                    <th class="text-center">
                        Dibuat Oleh
                    </th>
                </tr>
                <tr>
                    <th style="width:85%;padding-top:9%;"></th>
                    <th class="text-center">( <?php echo $data_karyawan['nama_lengkap']; ?> )</th>
                </tr>
            </table>
        </div>
    </div>


    <!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="../dist/js/jquery.slimscroll.js"></script>



    <!-- Data Table JavaScript -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../vendors/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../vendors/pdfmake/build/vfs_fonts.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../dist/js/dataTables-data.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="../dist/js/feather.min.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="../dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Toggles JavaScript -->
    <script src="../vendors/jquery-toggles/toggles.min.js"></script>
    <script src="../dist/js/toggle-data.js"></script>

    <!-- Select2 JavaScript -->
    <script src="../vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="../dist/js/select2-data.js"></script>

    <!-- Init JavaScript -->
    <script src="../dist/js/init.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../js/demo/datatables-demo.js"></script>

</body>

</html>
<script type="text/javascript">
    window.print();
</script>