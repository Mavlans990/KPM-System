<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user'])) {
    header("location:index.php");
}

$id_user = $_SESSION['id_user'];

$product_id = "all";

$tgl_1 = date("Y-m-d");
$tgl_2 = date("Y-m-d");

$where = "";
$get_branch = $_SESSION['branch'];
$branch_id = "1";
$filter_cabang = str_replace(",", "','", $_SESSION['branch']);
$type = "";

if (isset($_POST['cari_tgl'])) {
    $tgl_1 = mysqli_real_escape_string($conn, $_POST['tgl_1']);
    $tgl_2 = mysqli_real_escape_string($conn, $_POST['tgl_2']);
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch']);
    $product_id = mysqli_real_escape_string($conn, $_POST['product_nm']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Summary Stock</title>
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

    <!-- Daterangepicker CSS -->
    <link href="vendors/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>
    <div class="preloader-it">
        <div class="loader-pendulums"></div>
    </div>

    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

        <?php include "header.php"; ?>

        <!-- Main Content -->
        <div class="hk-pg-wrapper">

            <!-- Header -->
            <div class="card-header" style="background-color:#E5E5E5 !important">
                <h5 class="hk-sec-title text-dark-light-3 my-3"> Summary Stock
                </h5>
            </div>
            <!-- /Header -->

            <!-- Container -->

            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
                <?php

                if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
                    $readonly = "";
                    $class = "";
                } else {
                    $readonly = "readonly";
                    $class = "filled-input";
                    $where_branch = " AND i.id_branch = '" . $get_branch . "' ";
                }

                echo '
                <form action="' . $_SERVER['PHP_SELF'] . '" method="post">
                    <div class="row" style="">
                        <div class="col-md-2 d mt-10">
                            <div class="">
                                <span for="product_nm" class="text-dark">
                                       Product Name
                                </span>
                                <div class="input-group input-group-sm">
                                    <select name="product_nm" id="product_nm" class="form-control-sm custom-select custom-select-sm product_nm">
                                        <option value="all"';
                if ($product_id == "all") {
                    echo 'selected';
                }
                echo '>- Semua Product -</option>
                                        ';

                if ($type !== "") {
                    $where_product = " WHERE jenis_bahan = '" . $type . "'";
                } else {
                    $where_product = "";
                }
                $query_get_product_id = mysqli_query($conn, "SELECT id_bahan,nama_bahan FROM tb_bahan " . $where_product . " ORDER BY nama_bahan ASC");
                while ($row_product = mysqli_fetch_array($query_get_product_id)) {
                    echo '
                                                    <option value="' . $row_product['id_bahan'] . '" ';
                    if ($product_id == $row_product['id_bahan']) {
                        echo 'selected';
                    }
                    echo '>' . $row_product['nama_bahan'] . '</option>
                                                ';
                }
                echo '
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 d mt-10">
                            <div class="">
                                <span for="product_nm" class="text-dark">
                                       Type
                                </span>
                                <div class="input-group input-group-sm">
                                    <select name="type" id="type" class="form-control-sm custom-select custom-select-sm jenis_type">
                                        <option value="">- Semua Tipe -</option>
                                        <option value="kaca_film"
                                            ';

                if ($type == "kaca_film") {
                    echo "selected";
                }

                echo '
                                        >Kaca Film</option>
                                        <option value="product"
                                        ';

                if ($type == "product") {
                    echo "selected";
                }

                echo '
                                        >Produk</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 d mt-10">
                            
                            <span for="" class="text-dark">
                                <span style="" class="text-dark" id="inputGroup-sizing-sm">Branch</span>
                            </span>
                            
                            <select id="" class="form-control form-control-sm custom-select custom-select-sm id_cabang ' . $class . '" name="branch" ' . $readonly . '>
                             
                            
                            <option value="" ';
                if ($branch_id == "") {
                    echo 'selected';
                }
                echo ' >-- Choose Branch --</option>
                                ';

                if ($_SESSION['group'] == "super") {
                    $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang");
                    // echo $query_get_branch;
                    while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                        $id_branch = $row_branch['id_cabang'];
                        $nama_cabang = $row_branch['nama_cabang'];
                        if ($id_branch == $branch_id) {
                            echo '
                                            <option value="' . $id_branch . '" selected >' . $nama_cabang . '</option>
                                        ';
                        } else {
                            echo '
                                            <option value="' . $id_branch . '" >' . $nama_cabang . '</option>
                                        ';
                        }
                    }
                } else if ($_SESSION['group'] == "franchise") {

                    $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang IN('" . $filter_cabang . "')");
                    // echo $query_get_branch;
                    while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                        $id_branch = $row_branch['id_cabang'];
                        $nama_cabang = $row_branch['nama_cabang'];
                        if ($id_branch == $branch_id) {
                            echo '
                                            <option value="' . $id_branch . '" selected >' . $nama_cabang . '</option>
                                        ';
                        } else {
                            echo '
                                            <option value="' . $id_branch . '" >' . $nama_cabang . '</option>
                                        ';
                        }
                    }
                } else {

                    $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang = '" . $get_branch . "' ");
                    // echo $query_get_branch;
                    if ($row_branch = mysqli_fetch_array($query_get_branch)) {
                        $id_branch = $row_branch['id_cabang'];
                        $nama_cabang = $row_branch['nama_cabang'];
                        echo '
                                            <option value="' . $id_branch . '" selected >' . $nama_cabang . '</option>
                                        ';
                    }
                    $where_branch = " AND i.id_branch = '" . $get_branch . "' AND i.tgl = '" . $date_now . "' ";
                }

                echo '
                            </select>
                        </div>
                        <div class="col-md-4 d mt-10">
                            <div class="row no-gutters" >
                                <div class="col-12">
                                    <span style="width:200px;" class="mt-5 text-dark" id="inputGroup-sizing-sm">Transaction Date</span>
                                </div>
                                <div class="col-12">
                                    <div class="input-group">
                                        <input autocomplete="off" type="text" readonly
                                            id="cari_tanggal_1" name="tgl_1" class="form-control single_date form-control-sm" value="' . $tgl_1 . '" >
                                            <span class="mt-5 text-dark mx-2">To</span>
                                            <input autocomplete="off" type="text" readonly
                                            id="cari_tanggal_2" name="tgl_2" class="form-control single_date form-control-sm" value="' . $tgl_2 . '" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mt-10">   
                            <div class="row no-gutters" >
                                <div class="col-12">
                                <br>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-info form-control-sm" name="cari_tgl"><i class="fa fa-search"></i></button>
                                    ';

                if (isset($_POST['cari_tgl'])) {
                    $tgl_from = mysqli_real_escape_string($conn, $_POST['tgl_1']);
                    $tgl_to = mysqli_real_escape_string($conn, $_POST['tgl_2']);
                    $cabang = mysqli_real_escape_string($conn, $_POST['branch']);
                    $product = mysqli_real_escape_string($conn, $_POST['product_nm']);
                    $type = mysqli_real_escape_string($conn, $_POST['type']);
                    echo '
                                        <a href="print/print_excel_ringkasan_stock.php?tgl_from=' . $tgl_from . '&tgl_to=' . $tgl_to . '&cabang=' . $cabang . '&product=' . $product . '&type=' . $type . '" class="btn btn-success form-control-sm" target="_blank"><i class="fa fa-download"></i> Excel</a>
                                        ';
                }

                echo '
                                </div>
                                
                            </div>                        
                        </div>  
                    </div>     
                    
                </form>
                ';
                ?>
                <div class=" mt-15 table-scroll">
                    <table border="1" class="table table-responsive table-hover table-sm w-100 display mt-15">
                        <thead>
                            <tr class="text-center text-white bg-green">
                                <td class="text-center">Product Name</td>
                                <td class="text-center">Beginning</td>
                                <td class="text-center">Purchase Order (PO)</td>
                                <td class="text-center">Usage</td>
                                <td class="text-center">Transfer</td>
                                <?php
                                if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
                                    echo '
                                        <td class="text-center">Pending(Trf)</td>
                                        ';
                                }
                                ?>
                                <td class="text-center">Adjustment</td>
                                <td class="text-center">Ending</td>
                                <?php
                                if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
                                    echo '
                                        <td class="text-center">Actual Stock</td>
                                        <td class="text-center">HPP</td>
                                        <td class="text-center">Total Asset</td>
                                        ';
                                }
                                ?>
                            </tr>
                        </thead>
                        <?php
                        // Search Product and Date Proses
                        if (isset($_POST['cari_tgl'])) {
                            $product_id = mysqli_real_escape_string($conn, $_POST['product_nm']);
                            if ($product_id == "all") {
                                $where_product = "";
                                if ($type !== "") {
                                    $where_type = " WHERE jenis_bahan = '" . $type . "'";
                                } else {
                                    $where_type = "";
                                }
                            } else {
                                $where_product = "WHERE id_bahan = '" . $product_id . "'";
                                if ($type !== "") {
                                    $where_type = " AND jenis_bahan = '" . $type . "'";
                                } else {
                                    $where_type = "";
                                }
                            }



                            $awal = 0;
                            $total_po = 0;
                            $total_so = 0;
                            $total_trf = 0;
                            $total_pending = 0;
                            $total_adj = 0;
                            $akhir = 0;
                            $query_get_product = mysqli_query($conn, "SELECT id_bahan,nama_bahan FROM tb_bahan " . $where_product . " " . $where_type . " order by nama_bahan asc");

                            while ($row_product = mysqli_fetch_array($query_get_product)) {


                                $select_stock = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_product['id_bahan'] . "' AND id_cabang = '" . $branch_id . "'";
                                $query_stock = mysqli_query($conn, $select_stock);
                                $data_stock = mysqli_fetch_array($query_stock);
                                $jum_stock = mysqli_num_rows($query_stock);
                                if ($jum_stock > 0) {
                                    $stock = $data_stock['stock'];
                                    $hpp = $data_stock['hpp'];
                                } else {
                                    $stock = 0;
                                    $hpp = 0;
                                }

                                $tgl_1 = mysqli_real_escape_string($conn, $_POST['tgl_1']);
                                $tgl_2 = mysqli_real_escape_string($conn, $_POST['tgl_2']);

                                // $branch_id = mysqli_real_escape_string($conn,$_POST['branch']);
                                if ($tgl_1 !== "" || $tgl_2 !== "") {


                                    // if ($tgl_1 !== "" && $tgl_2 == "") {
                                    //     $where_in = "AND i.tgl_transaksi = '" . $tgl_1 . "' ";
                                    //     $where_beginning_in = "AND i.tgl_transaksi < '" . $tgl_1 . "' ";
                                    //     $where_out = "AND o.tgl_transaksi = '" . $tgl_1 . "' ";
                                    //     $where_beginning_out = "AND o.tgl_transaksi < '" . $tgl_1 . "' ";
                                    // }
                                    // if ($tgl_1 == "" && $tgl_2 !== "") {
                                    //     $where_in = "AND i.tgl_transaksi = '" . $tgl_2 . "' ";
                                    //     $where_beginning_in = "AND i.tgl_transaksi < '" . $tgl_2 . "' ";
                                    //     $where_out = "AND o.tgl_transaksi = '" . $tgl_2 . "' ";
                                    //     $where_beginning_out = "AND o.tgl_transaksi < '" . $tgl_2 . "' ";
                                    // }
                                    // if ($tgl_1 !== "" && $tgl_2 !== "") {
                                    $where_in = "AND i.tgl_transaksi BETWEEN '" . $tgl_1 . "' and '" . $tgl_2 . "' ";
                                    $where_beginning_in = "AND i.tgl_transaksi < '" . $tgl_1 . "' ";
                                    $where_out = "AND o.tgl_transaksi BETWEEN '" . $tgl_1 . "' and '" . $tgl_2 . "' ";
                                    $where_beginning_out = "AND o.tgl_transaksi < '" . $tgl_1 . "' ";
                                    // }

                                    $where_branch1 = "AND i.id_cabang='" . $branch_id . "'";
                                    $where_branch2 = "AND o.id_cabang='" . $branch_id . "'";
                                    $where_branch3 = "AND ai.id_branch='" . $branch_id . "'";
                                    $where_branch4 = "AND ao.id_branch='" . $branch_id . "'";
                                    $where_branch5 = "AND aj.id_branch='" . $branch_id . "'";
                                    if ($branch_id == "") {
                                        $where_branch1 = "";
                                        $where_branch2 = "";
                                        $where_branch3 = "";
                                        $where_branch4 = "";
                                        $where_branch5 = "";
                                    }

                                    // Beginning
                                    $sql_det = mysqli_query($conn, "
                                    SELECT i.id_cabang as branch, i.id_transaksi as nama,i.qty as masuk,'0' as keluar FROM tb_barang_masuk i JOIN tb_bahan p on i.id_product = p.id_bahan  WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' and i.status = 's' " . $where_branch1 . " " . $where_beginning_in . "

                                    UNION ALL

                                    SELECT o.id_cabang as branch, o.id_transaksi as nama,'0' as masuk ,o.qty as keluar  FROM tb_barang_keluar o JOIN tb_bahan p on o.id_bahan = p.id_bahan  WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' AND o.status_keluar = 's' " . $where_branch2 . " " . $where_beginning_out . " 

                                    ");



                                    $total_in = 0;
                                    $total_out = 0;
                                    $awal = 0;
                                    while ($row_in = mysqli_fetch_array($sql_det)) {
                                        $total_in += $row_in['masuk'];
                                        $total_out += $row_in['keluar'];
                                        $awal = $awal + $row_in['masuk'] - $row_in['keluar'];
                                        // if ($row_in['type'] == "Product") {
                                        //     $awal = 0;
                                        // }
                                    }


                                    $select_transfer_in = "
                                    SELECT stock_in
                                    FROM inv_adjust_in WHERE inv_date < '" . $tgl_1 . "' AND id_product = '" . $row_product['id_bahan'] . "' AND id_branch = '" . $branch_id . "' AND status_terima = 's'
                                    ";
                                    $query_transfer_in = mysqli_query($conn, $select_transfer_in);
                                    while ($row_transfer_in = mysqli_fetch_array($query_transfer_in)) {
                                        $awal = $awal + $row_transfer_in['stock_in'];
                                    }

                                    $select_transfer_out = "
                                    SELECT stock_out
                                    FROM inv_adjust_out WHERE inv_date < '" . $tgl_1 . "' AND id_product = '" . $row_product['id_bahan'] . "' AND id_branch = '" . $branch_id . "' AND status_terima = 's'
                                    ";
                                    $query_transfer_out = mysqli_query($conn, $select_transfer_out);
                                    while ($row_transfer_out = mysqli_fetch_array($query_transfer_out)) {
                                        $awal = $awal - $row_transfer_out['stock_out'];
                                    }



                                    $select_adjust_in = "
                                    SELECT stock_adj
                                    FROM inv_adjust WHERE inv_date < '" . $tgl_1 . "' AND id_product = '" . $row_product['id_bahan'] . "' AND id_branch = '" . $branch_id . "'
                                    ";
                                    $query_adjust_in = mysqli_query($conn, $select_adjust_in);
                                    while ($row_adjust_in = mysqli_fetch_array($query_adjust_in)) {
                                        $awal = $awal + $row_adjust_in['stock_adj'];
                                    }


                                    // End Beginning

                                    // Purchase Order (PO)
                                    $sql_in = mysqli_query($conn, "SELECT i.qty as po FROM tb_barang_masuk i JOIN tb_bahan p on i.id_product = p.id_bahan   WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' and i.status = 's' " . $where_branch1 . " " . $where_in . " ");

                                    $total_po = 0;
                                    while ($row_in = mysqli_fetch_array($sql_in)) {
                                        $total_po += $row_in['po'];
                                    }

                                    // echo"SELECT i.stock_in as po FROM inv_product_in i JOIN m_product p on i.id_product = p.m_product_id   WHERE p.m_product_id = '".$row_product['m_product_id']."' and  status = 'Approved' ".$where_branch1." ".$where." ";
                                    // End Purchase Order (PO)

                                    // Usage
                                    $sql_out = mysqli_query($conn, "SELECT o.qty as so FROM tb_barang_keluar o JOIN tb_bahan p on o.id_bahan = p.id_bahan  WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' " . $where_branch2 . " " . $where_out . " AND o.status_keluar = 's'");

                                    $total_so = 0;
                                    while ($row_out = mysqli_fetch_array($sql_out)) {
                                        $total_so += $row_out['so'];
                                    }
                                    // End Usage

                                    // Transfer



                                    $sql_trf = mysqli_query($conn, "
                                    SELECT ai.stock_in as masuk,'0' as keluar FROM inv_adjust_in ai JOIN tb_bahan p on ai.id_product = p.id_bahan  WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' and ai.id_inv_in LIKE 'SM-%' " . $where_branch3 . " AND ai.status_terima = 's' AND ai.inv_date BETWEEN '" . $tgl_1 . "' AND '" . $tgl_2 . "' 

                                        UNION ALL

                                    SELECT '0' as masuk ,ao.stock_out as keluar  FROM inv_adjust_out ao JOIN tb_bahan p on ao.id_product = p.id_bahan  WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' and ao.id_inv_out LIKE 'SM-%' " . $where_branch4 . " AND ao.status_terima = 's' AND ao.inv_date BETWEEN '" . $tgl_1 . "' AND '" . $tgl_2 . "'
                                    ");
                                    $total_trf_in = 0;
                                    $total_trf_out = 0;
                                    $total_trf = 0;
                                    while ($row_det = mysqli_fetch_array($sql_trf)) {
                                        $total_trf_in += $row_det['masuk'];
                                        $total_trf_out += $row_det['keluar'];
                                        $total_trf = $total_trf + $row_det['masuk'] - $row_det['keluar'];
                                    }
                                    // End Transfer

                                    // Pending
                                    $sql_pending = mysqli_query($conn, "SELECT ai.stock_in as masuk,'0' as keluar FROM inv_adjust_in ai JOIN tb_bahan p on ai.id_product = p.id_bahan  WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' and ai.id_inv_in LIKE 'SM-%' " . $where_branch3 . " AND ai.status_terima != 's' 

                                    UNION ALL

                                    SELECT '0' as masuk ,ao.stock_out as keluar  FROM inv_adjust_out ao JOIN tb_bahan p on ao.id_product = p.id_bahan  WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' and ao.id_inv_out LIKE 'SM-%' " . $where_branch4 . " AND ao.status_terima != 's'
                                    ");

                                    $total_pending_in = 0;
                                    $total_pending_out = 0;
                                    $total_pending = 0;
                                    while ($row_det = mysqli_fetch_array($sql_pending)) {
                                        $total_pending_in += $row_det['masuk'];
                                        $total_pending_out += $row_det['keluar'];
                                        $total_pending = $total_pending + $row_det['masuk'] - $row_det['keluar'];
                                    }

                                    // Adjustment
                                    $sql_adj = mysqli_query($conn, "SELECT aj.stock_adj as adj FROM inv_adjust aj JOIN tb_bahan p on aj.id_product = p.id_bahan  WHERE aj.id_product = '" . $row_product['id_bahan'] . "'  " . $where_branch5 . " AND aj.inv_date BETWEEN '" . $tgl_1 . "' AND '" . $tgl_2 . "'");



                                    $total_adj = 0;
                                    while ($row_adj = mysqli_fetch_array($sql_adj)) {
                                        $total_adj += $row_adj['adj'];
                                    }
                                    // End Adjustment

                                    // Ending 
                                    $sql_stk = mysqli_query($conn, "SELECT i.id_cabang as branch, i.id_transaksi as nama,i.qty as masuk,'0' as keluar  FROM tb_barang_masuk i JOIN tb_bahan p on i.id_product = p.id_bahan  WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' and  i.status = 's' " . $where_branch1 . " " . $where_in . "

                                                                    UNION ALL

                                                                    SELECT o.id_cabang as branch, o.id_transaksi as nama,'0' as masuk ,o.qty as keluar FROM tb_barang_keluar o JOIN tb_bahan p on o.id_bahan = p.id_bahan  WHERE p.id_bahan = '" . $row_product['id_bahan'] . "' " . $where_branch2 . " " . $where_beginning_out . "

                                                                    
                                                                ");

                                    $total_in_stk = 0;
                                    $total_out_stk = 0;
                                    $akhir = 0;
                                    while ($row_stk = mysqli_fetch_array($sql_stk)) {
                                        $total_in_stk += $row_stk['masuk'];
                                        $total_out_stk += $row_stk['keluar'];
                                        $akhir = $akhir + $row_stk['masuk'] - $row_stk['keluar'];
                                    }
                                    // End Ending
                                    $total_all = $awal + $total_po - $total_so + $total_trf + $total_adj;
                                    $total_actual = $awal + $total_po - $total_so + $total_trf + $total_pending + $total_adj;
                                }
                                echo '
                                    <tbody>
                                        <tr>
                                            <td>' . $row_product['nama_bahan'] . '</td>
                                            <td class="text-center">' . number_format($awal) . ' </td>
                                            <td class="text-center">' . number_format($total_po) . ' </td>                                            
                                            <td class="text-center">' . number_format($total_so) . ' </td>
                                            <td class="text-center">' . number_format($total_trf) . ' </td>
                                            ';

                                if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
                                    echo '
                                                <td class="text-center">' . number_format($total_pending) . ' </td>
                                                ';
                                }

                                echo '
                                            <td class="text-center">' . number_format($total_adj) . ' </td>
                                            <td class="text-center">' . number_format($total_all) . ' (' . number_format((float)$total_all / 2900, 2, '.', '') . ' Roll)</td>
                                            ';

                                if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
                                    echo '
                                                <td class="text-center">' . number_format($total_actual) . ' (' . number_format((float)$total_actual / 2900, 2, '.', '') . ' Roll)</td>
                                                <td class="text-center">' . number_format($hpp) . '</td>
                                                <td class="text-center">' . number_format($total_all * $hpp) . '</td>
                                                ';
                                }
                                $total_hpp += $hpp;
                                $total_asset += $total_all * $hpp;

                                echo '
                                        </tr>
                                ';

                                $update_stock = "
                                    UPDATE 
                                        tb_stock_cabang
                                    SET
                                        stock = '" . $total_all . "'
                                    WHERE 
                                        id_bahan = '" . $row_product['id_bahan'] . "' AND
                                        id_cabang = '" . $branch_id . "'
                                ";
                                $query_update_stock = mysqli_query($conn, $update_stock);
                            }
                        } else {
                            if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
                                $colspan = 9;
                            } else {
                                $colspan = 7;
                            }
                            echo '
                                <tr class="text-center">
                                    <td colspan="' . $colspan . '" >Mohon untuk search terlebih dahulu</td>
                                </tr>
                            ';
                        }
                        echo '
                            <tr>
                                <td class="bg-success text-white" colspan="10">Grand Total Asset</td>
                                <td class="bg-success text-white">' . number_format($total_asset) . '</td>
                            </tr>
                        ';
                        ?>
                        </tbody>
                    </table>
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

    <!-- Daterangepicker JavaScript -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/daterangepicker/daterangepicker.js"></script>
    <script src="dist/js/daterangepicker-data.js"></script>

    <!-- Select2 JavaScript -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="dist/js/select2-data.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $(document).on("click", '.hapus_button', function(e) {
                var id_hapus = $(this).data('id_hapus');
                $(".id_hapus").val(id_hapus);
            });

            $(document).on("change", ".jenis_type", function() {
                var type = $(".jenis_type").val();

                $(".preloader-it").show();
                $(".preloader-it").css("opacity", "0.5");

                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_summary_stock.php",
                    data: {
                        "type": type
                    },
                    cache: true,
                    success: function(response) {
                        $(".product_nm").html(response);
                        $(".preloader-it").hide();
                    }
                });
            });
        });

        $('#datable_12').DataTable({
            responsive: true,
            autoWidth: false,
            "bSort": false,
            "bPaginate": false,
            language: {
                search: "",
                searchPlaceholder: "Search",
                sLengthMenu: "_MENU_items"
            }

        });
    </script>


</body>

</html>