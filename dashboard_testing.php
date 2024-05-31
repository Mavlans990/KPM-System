<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user'])) {
    header('Location:index.php');
}

$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
}


if (isset($_POST['terima'])) {
    begin();
    $valid = 1;
    $id_inv = mysqli_real_escape_string($conn, $_POST['id_inv']);

    $query_out = mysqli_query($conn, "
        SELECT * 
        FROM inv_adjust_out
        WHERE id_inv_out = '" . $id_inv . "'
    ");
    while ($row_out = mysqli_fetch_array($query_out)) {
        $query_stock = mysqli_query($conn, "
            SELECT *
            FROM tb_stock_cabang
            WHERE id_bahan = '" . $row_out['id_product'] . "' AND id_cabang = '" . $row_out['id_branch'] . "'
        ");
        $data_stock = mysqli_fetch_array($query_stock);

        $total_qty_out = $data_stock['stock'] - $row_out['stock_out'];
        if ($total_qty_out < 0) {
            $valid = 2;
        }

        if ($valid == 1) {
            $update_stock = mysqli_query($conn, "
            UPDATE tb_stock_cabang SET
            stock = '" . $total_qty_out . "'
            WHERE id_bahan = '" . $row_out['id_product'] . "' AND id_cabang = '" . $row_out['id_branch'] . "'
        ");
        }

        if ($valid == 1) {
            $select_out2 = "
                SELECT * 
                FROM inv_adjust_out
                WHERE id_inv_out = '" . $id_inv . "' GROUP BY id_product
            ";
            $query_out2 = mysqli_query($conn, $select_out2);
            while ($row_out2 = mysqli_fetch_array($query_out2)) {
                $select_stock2 = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_out2['id_product'] . "' AND id_cabang = '" . $row_out2['id_branch'] . "'";
                $query_stock2 = mysqli_query($conn, $select_stock2);
                $data_stock2 = mysqli_fetch_array($query_stock2);

                if ($data_stock2['stock'] < 1) {
                    $update_hpp = mysqli_query($conn, "
                        UPDATE tb_stock_cabang SET hpp = '0' WHERE id_bahan = '" . $row_out2['id_product'] . "' AND id_cabang = '" . $row_out2['id_branch'] . "'
                    ");
                }
            }
        }
    }

    if ($valid == 1) {
        $query_inv = mysqli_query($conn, "
        SELECT * 
        FROM inv_adjust_in
        WHERE id_inv_in = '" . $id_inv . "'
    ");
        while ($row_inv = mysqli_fetch_array($query_inv)) {

            $query_stock = mysqli_query($conn, "
                SELECT
                id_bahan,
                stock,
                hpp
                FROM tb_stock_cabang
                WHERE id_bahan = '" . $row_inv['id_product'] . "' AND id_cabang = '" . $row_inv['id_branch'] . "'
            ");
            $jum_stock = mysqli_num_rows($query_stock);
            $data_stock = mysqli_fetch_array($query_stock);
            if ($jum_stock > 0) {
                $hpp = (($data_stock['hpp'] * $data_stock['stock']) + $row_inv['biaya']) / ($data_stock['stock'] + $row_inv['stock_in']);
                $total_qty = $data_stock['stock'] + $row_inv['stock_in'];

                $query_update = mysqli_query($conn, "
                UPDATE tb_stock_cabang SET
                stock = '" . $total_qty . "',
                hpp = '" . $hpp . "'
                WHERE id_bahan = '" . $row_inv['id_product'] . "' AND id_cabang = '" . $row_inv['id_branch'] . "'
            ");
                if (!$query_update) {
                    $valid = 0;
                }
            } else {
                $hpp = (0 + $row_inv['biaya']) / (0 + $row_inv['stock_in']);
                $query_insert = mysqli_query($conn, "
                INSERT INTO tb_stock_cabang(
                    id,
                    id_bahan,
                    id_cabang,
                    stock,
                    hpp
                ) VALUES(
                    '',
                    '" . $row_inv['id_product'] . "',
                    '" . $row_inv['id_branch'] . "',
                    '" . $row_inv['stock_in'] . "',
                    '" . $hpp . "'
                )
            ");
                if (!$query_insert) {
                    $valid = 0;
                }
            }
        }
    }

    if ($valid == 1) {
        $update_status_in = mysqli_query($conn, "
            UPDATE inv_adjust_in SET 
            status_terima = 's'
            WHERE id_inv_in = '" . $id_inv . "'
        ");
        $update_status_out = mysqli_query($conn, "
            UPDATE inv_adjust_out SET 
            status_terima = 's'
            WHERE id_inv_out = '" . $id_inv . "'
        ");
        commit();
        echo '
            <script>alert("Success : Stock berhasil diterima");window.location.href="dashboard.php";</script>
        ';
    } else if ($valid == 2) {
        rollback();
        echo '
            <script>alert("Failed : Stock cabang kurang");window.location.href="dashboard.php";</script>
        ';
    } else {
        rollback();
        echo '
            <script>alert("Failed : Stock gagal diterima");window.location.href="dashboard.php";</script>
        ';
    }
}

$visible = "";
if ($_SESSION['group'] !== "super") {
    $visible = "style='display:none;'";
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
    <title>Inventory System</title>
    <meta name="description" content=" JPS ERP system by SentralSystem & stevsoft" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Morris Charts CSS -->
    <link href="vendors/morris.js/morris.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/jquery-toast-plugin/dist/jquery.toast.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="dist/css/app.css">

    <!-- Toastr CSS -->

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">


</head>

<body>
    <!-- Preloader -->
    <!--<div class="preloader-it">-->
    <!--    <div class="loader-pendulums"></div>-->
    <!--</div>-->
    <!-- /Preloader -->

    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

        <?php include "header.php"; ?>

        <!-- Main Content -->
        <div class="hk-pg-wrapper">
            <!-- Container -->
            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">

                    <!-- Page Heading -->


                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-1" style="text-transform: none;">
                            <div class="form-group mt-5">
                                <h1 class="h3 mb-2 text-gray-800">Dashboard</h1>
                            </div>
                            <div class="">
                                <form action="" method="post">

                                    <div class="row">
                                        <div class="col-10">
                                            <div class="form-group" >
                                                <select name="tanggal" id="" class="form-control form-control-sm mt--5">
                                                    <option value="">-- Pilih Bahan --</option>
                                                    <option value="Hari">Today</option>
                                                    <option value="Minggu">This Week</option>
                                                    <option value="Bulan">This Month</option>
                                                    
                                                </select>
                                                
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <button type="submit" class="btn btn-primary form-control-sm mt--5" name="search"><i class="fa fa-search"></i> Cari</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php
                        $filter = "";
                        $filter2 = "";
                        $filter3 = "";
                        $tanggal = "";
                        $sales = "0";
                        $qty = "0";
                        $yard = "0";
                        $sales1 = "0";
                        $qty1 = "0";
                        $yard1 = "0";
                        $s_invoice = "0";
                        $pay = "0";
                        $jum_piutang = "0";
                        if (isset($_POST['search'])) {
                            $tanggal = $_POST['tanggal'];
                            if ($tanggal == "Hari") {
                                $filter = "WHERE tgl_transaksi = '". date("Y-m-d") ."'";
                                $filter2 = "WHERE a.tgl_invoice = '". date("Y-m-d") ."'";
                                $filter3 = "WHERE tgl_terima = '". date("Y-m-d") ."'";
                            }
                            elseif ($tanggal == "Minggu") {
                                $start_date = date("Y-m-d", strtotime('last monday'));
                                $end_date = date("Y-m-d", strtotime('next sunday'));
                                $filter = "WHERE tgl_transaksi BETWEEN '". $start_date ."' AND '". $end_date ."'";
                                $filter2 = "WHERE a.tgl_invoice BETWEEN '". $start_date ."' AND '". $end_date ."'";
                                $filter3 = "WHERE tgl_terima BETWEEN '". $start_date ."' AND '". $end_date ."'";
                            }
                            elseif ($tanggal == "Bulan") {
                                $filter = "WHERE month(tgl_transaksi) = '". date("m") ."'";
                                $filter2 = "WHERE month(a.tgl_invoice) = '". date("m") ."'";
                                $filter3 = "WHERE month(tgl_terima) = '". date("m") ."'";
                            }
                            
                            $penjualan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS qty, SUM(berat) AS berat, SUM(subtotal) AS subtotal FROM tb_barang_keluar $filter "));
                            $sales = money($penjualan['subtotal']);
                            $qty = $penjualan['qty'];
                            $yard = $penjualan['berat'] * $qty;

                            $pembelian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS qty, SUM(berat) AS berat, SUM(total) AS total FROM tb_barang_masuk $filter "));
                            $sales1 = money($pembelian['total']);
                            $qty1 = $pembelian['qty'];
                            $yard1 = $pembelian['berat'] * $qty1;

                            $piutang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(subtotal) AS subtotal, a.jatuh_tempo FROM tb_barang_keluar a $filter2 AND a.jatuh_tempo != 0"));

                            $piutang2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(pay) AS pay FROM inv_payment $filter3 AND metode != '1-1006'"));

                            $sql_barang_keluar = mysqli_query($conn, "SELECT * FROM tb_barang_keluar $filter");
                            $jum_piutang = mysqli_num_rows($sql_barang_keluar);
                            $pay = money($piutang2['pay']);
                            $s_invoice = money($piutang['subtotal'] - $piutang2['pay']);
                        }
                        
                        ?>
                        <div class="container">
                            <div class="card shadow" style="padding: 10px;">
                                <h2 class="h3 text-gray-800 pl-3 pt-3">Penjualan</h2>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Total Sales</h5>
                                                <p class="card-text">Rp.<?= $sales ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Total QTY</h5>
                                                <p class="card-text"><?= money($qty) ?> ROLL</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Total YARD</h5>
                                                <p class="card-text"><?= money($yard) ?> YARD</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow" style="padding: 10px;">
                                <h2 class="h3 text-gray-800 pl-3 pt-3">Pembelian</h2>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Total Purchase</h5>
                                                <p class="card-text">Rp.<?= $sales1 ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Total QTY</h5>
                                                <p class="card-text"><?= money($qty1) ?> ROLL</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Total YARD</h5>
                                                <p class="card-text"><?= money($yard1) ?> YARD</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card shadow" style="padding: 10px;">
                                <h2 class="h3 text-gray-800 pl-3 pt-3">Piutang</h2>
                                <div class="row">
                                    <div class="col-4">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Total Piutang</h5>
                                                <p class="card-text">Rp.<?= $s_invoice ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Penerimaan</h5>
                                                <p class="card-text">Rp.<?= $pay ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="card text-white bg-primary">
                                            <div class="card-body">
                                                <h5 class="card-title" style="color: white;">Total Invoice</h5>
                                                <p class="card-text"><?= $jum_piutang ?> Invoice</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-50">
                            
                                <?php
                                $tanggal = "";
                                if (isset($_POST['search'])) {
                                    $tanggal = $_POST['tanggal'];
                                    if ($tanggal == "Bulan") {
                                        echo '
                                            <h2 class="mb-30">Grafik Pergerakan Omset</h2>
                                            <div class="card chart-container">
                                                <canvas id="chartjs-bar-month"></canvas>
                                            </div>
                                        ';
                                    }elseif($tanggal == "Minggu"){
                                        echo '
                                            <h2 class="mb-30">Grafik Pergerakan Omset</h2>
                                            <div class="card chart-container">
                                                <canvas id="chartjs-bar-week"></canvas>
                                            </div>
                                        ';
                                    }
                                }
                                
                                ?>
                                
                            
                        </div>
                        <div class="container mt-50">
                            <div class="row">
                                <div class="col-6">
                                    <h3 class="mb-30 text-center">Top 6 produk Terlaris</h3>
                                    <div class="card" style="border: 1px;">
                                      <canvas id="chartjs-pie-1"></canvas>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h3 class="mb-30 text-center">Top 6 Produk Stok Tertinggi</h3>
                                    <div class="card" style="border: 1px;">
                                      <canvas id="chartjs-pie-2"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container mt-50">
                            <div class="row">
                                <div class="col-6">
                                    <h3 class="mb-30 text-center">Top 6 Customer By Sales</h3>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $customer = mysqli_query($conn, "SELECT a.id_customer,SUM(total) as total,b.nama_customer FROM tb_barang_keluar a
                                                        JOIN tb_customer AS b ON a.id_customer = b.id_customer GROUP BY id_customer ORDER BY total DESC LIMIT 6");
                                                        while ($row = mysqli_fetch_assoc($customer)) {
                                                            echo '
                                                                <tr>
                                                                    <td class="text-center fs-11">'. $row['nama_customer'] .'</td>
                                                                    <td class="text-center fs-11">'. money($row['total']) .'</td>
                                                                </tr>
                                                            ';
                                                        }
                                                        
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h3 class="mb-30 text-center">Top 6 Customer By Piutang</h3>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Nama</th>
                                                        <th class="text-center">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $customer = mysqli_query($conn, "SELECT a.cust_invoice,SUM(subtotal) as subtotal,a.jatuh_tempo,a.cust_invoice, b.nama_customer FROM tb_barang_keluar a
                                                        JOIN tb_customer AS b ON a.cust_invoice = b.id_customer WHERE a.jatuh_tempo != 0 GROUP BY cust_invoice ORDER BY subtotal_invoice DESC LIMIT 6");
                                                        while ($row = mysqli_fetch_assoc($customer)) {
                                                            echo '
                                                                <tr>
                                                                    <td class="text-center fs-11">'. $row['nama_customer'] .'</td>
                                                                    <td class="text-center fs-11">'. money($row['subtotal']) .'</td>
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
                        </div>
                    </div>

                </div>
            <!-- /Container -->

            <!-- Footer -->
            <div class="hk-footer-wrap container-fluid">
            </div>
            <!-- /Footer -->
        </div>
        <!-- /Main Content -->

    </div>
    <!-- /HK Wrapper -->

    <div class="modal fade" id="terimaStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Terima Stock</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="id_inv" id="" class="form-control id_inv">
                        Apa anda yakin ingin menerima stock ini ?
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-success btn-sm" name="terima" value="Terima">
                        <button class="btn btn-danger btn-sm" type="button" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
    <script src="vendors/jquery-toast-plugin/dist/jquery.toast.min.js"></script>
    <script src="dist/js/toast-data.js"></script>
    <script src="vendors/jquery/dist/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {

            $(document).on("click", ".terima_stock", function() {
                var id_inv = $(this).data('id_inv');
                $('.id_inv').val(id_inv);
            });

            $(document).on("change", ".pilih_cabang", function() {
                var cabang = $(".pilih_cabang").val();

                $(".preloader-it").show();
                $(".preloader-it").css("opacity", "0.5");

                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_dashboard.php",
                    data: {
                        "pilih_cabang": cabang,
                    },
                    cache: true,
                    success: function(response) {
                        $(".preloader-it").hide();
                        $(".list_cabang").html(response);
                    }
                });
            });
        });
    </script>

    <script src="Chart.js"></script>
    <script>
      const ctx = document.getElementById("chart").getContext('2d');
      const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: [
            <?php
                $a= 0;
                $date = "";
                for ($i=1; $i <= 7; $i++) { 
                    $date = date("d",strtotime("last monday")) ;
                    echo $date + $a;
                    echo ",";
                    $a++;
                }    
            ?>
          ],
          datasets: [{
            label: '',
            backgroundColor: 'rgba(161, 198, 247, 1)',
            borderColor: 'rgb(47, 128, 237)',
            data: [
                <?php
                    $date1 = "2023-02-23";
                    $qry = mysqli_fetch_assoc(mysqli_query($conn, "SELECT sum(qty) as qty FROM tb_barang_keluar WHERE tgl_transaksi = '$date1'"));
                    echo $qry['qty'];
                    echo ",";
                    
                ?>
            ],
          }]
        },
        options: {
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
              }
            }]
          }
        },
      });
    </script>

    <script src="js/app.js"></script>
    <!-- Chart Bar Minggu -->
    <script>
        new Chart(document.getElementById("chartjs-bar-week"), {
        type: "bar",
        data: {
            labels: [
                <?php
                $date1 = date("Y-m-d", strtotime("last monday"));
                $date2 = date("Y-m-d", strtotime("next sunday"));
                $startdate1 = strtotime($date1);
                $enddate1 = strtotime($date2, $startdate1);
                $a = 0;
                while ($startdate1 <= $enddate1) {
                    echo $coba[$a] = date("d", $startdate1);
                    echo ",";
                    $startdate1 = strtotime("+1 day", $startdate1);
                    $a++;
                }
            ?>
            ],
            datasets: [{
            label: "This Week",
            backgroundColor: window.theme.primary,
            borderColor: window.theme.primary,
            hoverBackgroundColor: window.theme.primary,
            hoverBorderColor: window.theme.primary,
            data: [
                <?php
                    $date3 = date("Y-m-d", strtotime("last monday"));
                    $date4 = date("Y-m-d", strtotime("next sunday"));
                    $startdate1 = strtotime($date1);
                    $enddate1 = strtotime($date2, $startdate1);
                    $a = 0;
                    while ($startdate1 <= $enddate1) {
                        $coba[$a] = date("Y-m-d", $startdate1);
                        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS qty FROM tb_barang_keluar WHERE tgl_transaksi = '$coba[$a]' "));
                        echo $sql['qty'];
                        echo ",";
                        $startdate1 = strtotime("+1 day", $startdate1);
                        $a++;
                    }
                ?>
            ],
            barPercentage: .75,
            categoryPercentage: .5
            }]
        },
        options: {
            scales: {
            yAxes: [{
                gridLines: {
                display: false
                },
                stacked: false
            }],
            xAxes: [{
                stacked: false,
                gridLines: {
                color: "transparent"
                }
            }]
            }
        }
        });
    </script>

    <!-- Chart Bar Bulan -->
    <script>
        new Chart(document.getElementById("chartjs-bar-month"), {
        type: "bar",
        data: {
            labels: [
                <?php
                // $date1 = date("Y-m-d", strtotime("last monday"));
                // $date2 = date("Y-m-d", strtotime("next sunday"));
                // $startdate1 = strtotime($date1);
                // $enddate1 = strtotime($date2, $startdate1);
                // $a = 0;
                // while ($startdate1 <= $enddate1) {
                //     echo $coba[$a] = date("d", $startdate1);
                //     echo ",";
                //     $startdate1 = strtotime("+1 day", $startdate1);
                //     $a++;
                // }
                $kalender = CAL_GREGORIAN;
                $bulan = date('m');
                $tahun = date('Y');
                $hari = cal_days_in_month($kalender, $bulan, $tahun);
                for ($i=1; $i <= $hari; $i++) { 
                    echo $i;
                    echo ",";
                }
            ?>
            ],
            datasets: [{
            label: "This Month (<?= date("F") ?>)",
            backgroundColor: window.theme.primary,
            borderColor: window.theme.primary,
            hoverBackgroundColor: window.theme.primary,
            hoverBorderColor: window.theme.primary,
            data: [
                <?php
                    // $date3 = date("Y-m-d", strtotime("last month"));
                    // $date4 = date("Y-m-d", strtotime("next sunday"));
                    // $startdate1 = strtotime($date1);
                    // $enddate1 = strtotime($date2, $startdate1);
                    // $a = 0;
                    // while ($startdate1 <= $enddate1) {
                    //     $coba[$a] = date("Y-m-d", $startdate1);
                    //     $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS qty FROM tb_barang_keluar WHERE month(tgl_transaksi) = '". date("m") ."' "));
                    //     echo $sql['qty'];
                    //     echo ",";
                    //     $startdate1 = strtotime("+1 day", $startdate1);
                    //     $a++;
                    // }
                    $hari_ini = date("Y-m-d");
                    $tgl_1 = date("Y-m-01", strtotime($hari_ini));
                    $tgl_2 = date("Y-m-t", strtotime($hari_ini));
                    $startdate = strtotime($tgl_1);
                    $enddate = strtotime($tgl_2, $startdate);
                    $a = 0;
                    while ($startdate <= $enddate) {
                        $coba[$a] = date("Y-m-d", $startdate);
                        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(qty) AS qty FROM tb_barang_keluar WHERE tgl_transaksi = '$coba[$a]' "));
                        echo $sql['qty'];
                        echo ",";
                        $startdate = strtotime("+1 day", $startdate);
                        $a++;
                    }
                ?>
            ],
            barPercentage: .75,
            categoryPercentage: .5
            }]
        },
        options: {
            scales: {
            yAxes: [{
                gridLines: {
                display: false
                },
                stacked: false
            }],
            xAxes: [{
                stacked: false,
                gridLines: {
                color: "transparent"
                }
            }]
            }
        }
        });
    </script>

    <script>
        new Chart(document.getElementById("chartjs-pie-1"), {
        type: "pie",
        data: {
            labels: [
                <?php
                    $sql_tertinggi = mysqli_query($conn, "SELECT a.id_bahan,a.qty * a.berat, b.nama_bahan FROM tb_barang_keluar a 
                    JOIN tb_bahan AS b ON a.id_bahan = b.id_bahan ORDER BY qty * berat DESC LIMIT 6");
                    while ($row = mysqli_fetch_assoc($sql_tertinggi)) {
                        echo '"'.$row['nama_bahan'].'"' . ",";
                    }
                    
                ?>
            ],
            datasets: [{
            data: [
                <?php
                $sql_tertinggi = mysqli_query($conn, "SELECT id_bahan,qty * berat,qty FROM `tb_barang_keluar` ORDER BY `qty * berat` DESC LIMIT 6");
                while ($row = mysqli_fetch_assoc($sql_tertinggi)) {
                    echo $row['qty * berat'];
                    
                    echo ",";
                }
                ?>
            ],
            backgroundColor: [
                "#FF4500",
                "#FFA07A",
                "#FFD700",
                "#228B22",
                "#87CEFA",
                "#BA55D3"
            ],
            borderColor: "transparent"
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutoutPercentage: 65,
        }
        });
    </script>
    <script>
        new Chart(document.getElementById("chartjs-pie-2"), {
        type: "pie",
        data: {
            labels: [
                <?php
                    $sql_tertinggi = mysqli_query($conn, "SELECT a.id_bahan,a.qty,b.nama_bahan FROM tb_barang_keluar a
                    JOIN tb_bahan AS b ON a.id_bahan = b.id_bahan  ORDER BY qty DESC LIMIT 6");
                    while ($row = mysqli_fetch_assoc($sql_tertinggi)) {
                        echo '"'.$row['nama_bahan'].'"' . ",";
                    }
                    
                ?>
            ],
            datasets: [{
            data: [
                <?php
                $sql_tertinggi = mysqli_query($conn, "SELECT qty FROM tb_barang_keluar ORDER BY qty DESC LIMIT 6");
                while ($row = mysqli_fetch_assoc($sql_tertinggi)) {
                    echo $row['qty'];
                    echo ",";
                }
                ?>
            ],
            backgroundColor: [
                "#FF4500",
                "#FFA07A",
                "#FFD700",
                "#228B22",
                "#87CEFA",
                "#BA55D3"
            ],
            borderColor: "transparent"
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutoutPercentage: 65,
        }
        });
    </script>

</body>

</html>