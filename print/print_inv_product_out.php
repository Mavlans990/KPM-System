<?php
include "../lib/koneksi.php";
session_start();
$id_inv = mysqli_real_escape_string($conn, $_GET['id_inv']);


$select_inv_out = "SELECT * FROM inv_product_out WHERE inv_out_id = '" . $id_inv . "'";
$query_inv_out = mysqli_query($conn, $select_inv_out);
$data_inv_out = mysqli_fetch_array($query_inv_out);

$select_branch = "SELECT alamat FROM tb_cabang WHERE id_cabang = '" . $data_inv_out['id_branch'] . "'";
$query_branch = mysqli_query($conn, $select_branch);
$data_branch = mysqli_fetch_array($query_branch);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Add/Edit Transaction Product Out</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="../vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="../vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="../vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="../vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Daterangepicker CSS -->
    <link href="../vendors/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="../dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>



    <div class="container">
        <div class="text-center">
            <img src="../../img/logo/main-logo.png" width="200px" alt="">
            <p style="margin-top:-50px;margin-bottom:30px;"><?= $data_branch['alamat']; ?></p>
            <h5 style="margin-bottom:30px;">INVOICE</h5>
        </div>
        <div class="col-5 mt-5">
            <table border="0">
                <?php


                ?>
                <tr>
                    <th style="width:140px;">Tanggal Transaksi</th>
                    <th style="width:20px;"> : </th>
                    <td><?= date("d M Y", strtotime($data_inv_out['inv_date'])); ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Nama</th>
                    <th style="width:20px;"> : </th>
                    <td><?= $data_inv_out['nm_cust']; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Email</th>
                    <th style="width:20px;"> : </th>
                    <td><?= $data_inv_out['id_user']; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">No. Telp</th>
                    <th style="width:20px;"> : </th>
                    <td><?= $data_inv_out['telp_cust']; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Kota</th>
                    <th style="width:20px;"> : </th>
                    <td><?= $data_inv_out['kota']; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Kode Pos</th>
                    <th style="width:20px;"> : </th>
                    <td><?= $data_inv_out['kodepos']; ?></td>
                </tr>
                <tr>
                    <th style="width:140px;">Alamat</th>
                    <th style="width:20px;"> : </th>
                    <td><?= nl2br($data_inv_out['alamat']); ?></td>
                </tr>
            </table>
        </div>
        <div class="col-12 mt-4">
            <table class="table table-bordered tb_jps_ins tb_jps_re_ins">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $x = 1;
                    $total = 0;
                    $select_inv_out = "SELECT * FROM inv_product_out WHERE id_inv_out = '" . $data_inv_out['id_inv_out'] . "'";
                    $query_inv_out = mysqli_query($conn, $select_inv_out);
                    while ($row_inv_out = mysqli_fetch_array($query_inv_out)) {
                        $select_product = "SELECT * FROM m_product WHERE m_product_id = '" . $row_inv_out['id_product'] . "'";
                        $query_product = mysqli_query($conn, $select_product);
                        $data_product = mysqli_fetch_array($query_product);

                        if ($data_product['type_product'] == "Product") {
                            $harga = $row_inv_out['price'];
                        } else {
                            $harga = 0;
                        }

                        $total = $total + ($harga * $row_inv_out['stock_out']);

                        if ($data_product['type_product'] == "Product") {


                            if ($row_inv_out['pemasangan'] == "dipasangkan") {
                                $pemasangan = "<p style='font-size:10px;'>Pemasangan : Dipasangkan</p>";
                            } else {
                                $pemasangan = "<p style='font-size:10px;'>Pemasangan : Pasang Sendiri</p>";
                            }

                            if ($row_inv_out['pemasangan'] == "dipasangkan") {
                                if ($row_inv_out['paket'] == "express") {
                                    $paket = "<p style='font-size:10px;'>Paket : Express</p>";
                                } else if ($row_inv_out['paket'] == "dirumah") {
                                    $paket = "<p style='font-size:10px;'>Paket : Pasang Dirumah</p>";
                                } else {
                                    $paket = "<p style='font-size:10px;'>Paket : Regular</p>";
                                }
                            } else {
                                $paket = "";
                            }

                            if ($row_inv_out['pemasangan'] == "dipasangkan") {
                                $tanggal_pasang = "<p style='font-size:10px;'>Tanggal Pasang : " . date("D, d M Y", strtotime($row_inv_out['tgl_pasang'])) . "</p>";
                            } else {
                                $tanggal_pasang = "";
                            }

                            if ($row_inv_out['pemasangan'] == "dipasangkan") {
                                $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_inv_out['id_branch'] . "'";
                                $query_cabang = mysqli_query($conn, $select_cabang);
                                $data_cabang = mysqli_fetch_array($query_cabang);

                                if ($row_inv_out['paket'] !== "dirumah") {
                                    $cabang = "<p style='font-size:10px;'>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                                } else {
                                    $cabang = "";
                                }
                            } else {
                                $cabang = "";
                            }

                            if ($row_inv_out['stat_cus'] == "yes") {
                                $stat_cus = "<p style='font-size:10px;'>Body Custom : Ya</p>";
                            } else {
                                $stat_cus = "<p style='font-size:10px;'>Body Custom : Tidak</p>";
                            }

                            if ($row_inv_out['part'] == "yes") {
                                $part = "<p style='font-size:10px;'>Cat ulang / baret : Ya</p>";
                            } else {
                                $part = "<p style='font-size:10px;'>Cat ulang / baret : Tidak</p>";
                            }

                            if ($row_inv_out['keterangan'] !== "") {
                                if ($row_inv_out['stat_cus'] == "yes") {
                                    $keterangan = "<p style='font-size:10px;'>Keterangan body custom : " . $row_inv_out['keterangan'] . "</p>";
                                } else {
                                    $keterangan = "";
                                }
                            } else {
                                $keterangan = "";
                            }

                            if ($row_inv_out['keterangan_part'] !== "") {
                                if ($row_inv_out['part'] == "yes") {
                                    $keterangan_part = "<p style='font-size:10px;'>Keterangan body cat ulang / baret : " . $row_inv_out['keterangan_part'] . "</p>";
                                } else {
                                    $keterangan_part = "";
                                }
                            } else {
                                $keterangan_part = "";
                            }

                            echo '
                            <tr>
                                <td>' . $x . '</td>
                                <td>
                                    ' . $data_product['nm_product'] . ' 
                                    ' . $pemasangan . '
                                    ' . $paket . '
                                    ' . $tanggal_pasang . '
                                    ' . $cabang . '
                                    ' . $stat_cus . '
                                    ' . $part . '
                                    ' . $keterangan . '
                                    ' . $keterangan_part . '
                                </td>
                                <td>' . $row_inv_out['stock_out'] . ' ' . $data_product['uom_product'] . '</td>
                                <td>Rp. ' . number_format($row_inv_out['price']) . '</td>
                            </tr>
                        ';
                        } else {
                            echo '
                            <tr>
                                <td>' . $x . '</td>
                                <td>' . $data_product['nm_product'] . '</td>
                                <td></td>
                                <td></td>
                            </tr>
                        ';
                        }
                        $x++;
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="text-right">Total</td>
                        <td>Rp. <?= number_format($total); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>


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

    <!-- Daterangepicker JavaScript -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/daterangepicker/daterangepicker.js"></script>
    <script src="dist/js/daterangepicker-data.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>
    <script>
        window.print();
    </script>
</body>

</html>