<?php
include "../lib/koneksi.php";
session_start();

$id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);

$select_transaksi = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_transaksi";
$query_transaksi = mysqli_query($conn, $select_transaksi);
$data_transaksi = mysqli_fetch_array($query_transaksi);

$select_karyawan = "SELECT * FROM tb_karyawan WHERE user_id = '" . $data_transaksi['dibuat_oleh'] . "'";
$query_karyawan = mysqli_query($conn, $select_karyawan);
$data_karyawan = mysqli_fetch_array($query_karyawan);

$nm_supp = "";
$sql_get_supp = mysqli_query($conn,"SELECT nama_customer FROM tb_customer WHERE id_customer = '".$data_transaksi['id_supplier']."'");
if($row_supp = mysqli_fetch_array($sql_get_supp)){
    $nm_supp = $row_supp['nama_customer'];
}

$hide = "";
$readonly = "";
$filled_input = "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Invoice System</title>
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
            <h3>Purchase Order</h3>
        </div>
        <div class="col-5" style="margin-top:20px;">
            <table border="0">
                <tr>
                    <th style="width:140px;">Nomor Transaksi</th>
                    <th style="width:20px;">:</th>
                    <td><?= $data_transaksi['id_transaksi']; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Supplier</th>
                    <th style="width:20px;">:</th>
                    <td><?= $nm_supp; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Tanggal Transaksi</th>
                    <th style="width:20px;">:</th>
                    <td><?= date("d/m/Y", strtotime($data_transaksi['tgl_transaksi'])); ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Nama Penginput</th>
                    <th style="width:20px;">:</th>
                    <td><?= $data_karyawan['nama_lengkap']; ?></td>
                </tr>
            </table>
        </div>
        <div class="col-12 mt-4">
            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                <thead>
                    <tr>
                        <th class="text-center">ID Product</th>
                        <th class="text-center">Nama Bahan</th>
                        <th class="text-center">Qty (Roll)</th>
                        <th class="text-center">Yard</th>
                        <th class="text-center">UOM</th>
                        <th class="text-center">Harga</th>
                        <th class="text-center">Total</th>
                    </tr>
                </thead>
                <tbody class="daftar_bahan">
                    <?php
                    $total = 0;
                    $jenis_pajak = 1;
                    $select_barang_masuk = "SELECT a.*,b.id_bahan,b.nama_bahan,b.uom FROM tb_barang_masuk a JOIN tb_bahan b ON b.id_bahan = a.id_product WHERE a.id_transaksi = '" . $id_transaksi . "'";
                    $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
                    $jum_barang_masuk = mysqli_num_rows($query_barang_masuk);
                    while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
                        $jenis_pajak = $row_barang_masuk['by_user_pajak'];
                        $total = $total + $row_barang_masuk['total'];
                        echo '
                                            <tr>
                                                <td class="text-center">' . $row_barang_masuk['id_bahan'] . '</td>
                                                <td class="text-center">' . $row_barang_masuk['nama_bahan'] . '</td>
                                                <td class="text-right">' . number_format($row_barang_masuk['qty']) . '</td>
                                                <td class="text-center">' . $row_barang_masuk['berat'] . '</td>
                                                <td class="text-center">' . $row_barang_masuk['uom'] . '</td>
                                                <td class="text-right">' . number_format($row_barang_masuk['harga']) . '</td>
                                                <td class="text-right">' . number_format($row_barang_masuk['total']) . '</td>
                                            </tr>
                                        ';
                    }

                    if ($jum_barang_masuk > 0) {
                        if (isset($_GET['detail'])) {
                            $colspan = 1;
                        } else {
                            $colspan = 2;
                        }

                        $select_barang_masuk = "SELECT * FROM tb_barang_masuk  WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_transaksi";
                        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
                        $data_barang_masuk = mysqli_fetch_array($query_barang_masuk);

                        $ppn = $data_barang_masuk['ppn'] * $total / 100;

                        echo '
                                            <tr>
                                                <th class="text-right" colspan="6">Subtotal</th>
                                                <th class="text-right">' . number_format($total) . '</th>
                                            </tr>
                                            ';

                        if ($jenis_pajak == 1) {
                            echo '
                                                    <tr>
                                                        <th class="text-right" colspan="6">PPN ' . $data_barang_masuk['ppn'] . ' % </th>
                                                        <th class="text-right">' . number_format($ppn) . '</th>
                                                    </tr>
                                                ';
                        }

                        echo '
                                            <tr>
                                                <th class="text-right" colspan="6">Grand Total</th>
                                                <th class="text-right">' . number_format($total + $ppn) . '</th>
                                            </tr>
                                        ';
                    }
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