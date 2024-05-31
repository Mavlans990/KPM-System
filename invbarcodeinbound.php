<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$getall = "";

$no_id = '';
if (isset($_GET['id_lot'])) {
    if ($_GET['id_lot'] != "") {
        $no_id = $_GET['id_lot'];
        // echo "SELECT greige_by,celup_by,barcode,a.id_bahan_kain,lot,bruto,netto,susut,shrinkage,id_transaksi,tgl_transaksi,b.* FROM tb_barang_masuk a JOIN tb_bahan_kain b ON b.id_bahan_kain = a.id_bahan_kain where a.no_id = '".$no_id."'";
        $query_get = mysqli_query($conn, "SELECT greige_by,celup_by,barcode,a.id_bahan_kain,lot,bruto,netto,susut,shrinkage,id_transaksi,tgl_transaksi,b.* FROM tb_barang_masuk a JOIN tb_bahan_kain b ON b.id_bahan_kain = a.id_bahan_kain where a.no_id = '" . $no_id . "' ");

        $getall = "id_lot=" . $no_id;
    }
}



?>

<!DOCTYPE html>
<!-- 
Template Name: Griffin - Responsive Bootstrap 4 Admin Dashboard Template
Author: Hencework
Support: support@hencework.com

License: You must have a valid license purchased only from templatemonster to legally use the template for your project.
-->
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Print Barcode </title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="icon" href="dist/img/logo.png" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
    <style>
        @media print {
            @page {
                size: landscape
            }

            .non-print {
                display: none !important;
            }

            .pageBreak {
                page-break-after: always;
            }
        }

        .tetx {
            width: 80mm;
            display: block;
            /* font-family: 'courier', monospace; */
            font-size: 18px;
            /* width:80mm; */
            /* height:76mm; */
        }

        .font-weight-bold {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <!-- Container -->
    <!-- Row -->
    <div class="card-body" style="padding: 0%;">
        <div class="row non-print">
            <div class="col-3">
            </div>
        </div>
        <?php
        while ($row_get = mysqli_fetch_array($query_get)) {
            $barang = $row_get['kode_bahan_kain'] . ' - ' . $row_get['jenis_kain'] . ' - ' . $row_get['warna'] . ' - ' . $row_get['setting'] . ' - ' . $row_get['gramasi'];
            $greige_by = explode(" | ", $row_get['greige_by']);
            $celup_by = explode(" | ", $row_get['celup_by']);

            echo '
                            <div class="row tetx" style="margin-left:40px;">
                                <div class="col">
                                    <div class="row no-gutters"  style="font-size:13px;min-height:30px;max-height:45px;">
                                        <div class="col text-center" style="padding-left:16px;">
                                            <span class="font-weight-bold" style="line-height: 1.1;display:block;margin-top:1px;">' . $barang . '</span>
                                            <span class="font-weight-bold" style="line-height: 1.1;display:block;margin-top:1px;">' . $greige_by[1] . '</span>
                                            <span class="font-weight-bold" style="line-height: 1.1;display:block;margin-top:1px;">' . $celup_by[1] . '</span>
                                        </div>
                                        <div class="col-1 text-right" style="">

                                        </div>
                                    </div>
                                    <div class="row no-gutters" >
                                        <div class="col text-center" style="padding-left:16px;margin-top:15px">
                                            <img alt="testing" width="85%" height="85" src="barcode/barcode.php?size=50&text=' . $row_get['barcode'] . '&print=true"/>
                                            <span class="font-weight-bold" style="line-height: 0.2;margin-top:1500px">Bruto : ' . $row_get['bruto'] . '</span>
                                            <span class="font-weight-bold" style="line-height: 0.2;">Netto : ' . $row_get['netto'] . '</span>
                                            <br>
                                            <span class="font-weight-bold" style="line-height: 0.2;">Shrinkage : ' . $row_get['shrinkage'] . ' %</span>
                                        </div>
                                        <div class="col-1 text-right" style="">
                                        </div>
                                    </div>
                                </div>
                            </div> 
                        ';
            echo "<p style='page-break-after:always'></p>";
        }
        ?>
        <div class="row position-fixed non-print" style="bottom:10px;">
            <div class="col">
                <button class="btn btn-md btn-danger text-white w-100" onclick="window.close();">Back</button>
            </div>
            <div class="col">
                <a href="javascript:window.print()" class="btn btn-md btn-primary printlist text-white w-100">Print</a>
            </div>
        </div>
        <!-- /Row -->
        <!-- /Container -->

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

        <!-- Init JavaScript -->
        <script src="dist/js/init.js"></script>
        <!-- End -->

</body>

</html>