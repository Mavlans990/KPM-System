<?php

session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['branch'])) {
    header('Location:index.php');
}

$id_user = $_SESSION['id_user'];
// $branch = $_SESSION['branch'];
$date_now = date("Y-m-d");
$date_to = date("Y-m-d");
$statuss = "";

// Delete Flexas
if (isset($_POST['delete'])) {
    $valid = 1;
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $query_get_inv = mysqli_query($conn, "SELECT id_product,stock_out,id_branch,selisih,status_terima FROM inv_adjust_out WHERE id_inv_out = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' ");
    while ($row_inv = mysqli_fetch_array($query_get_inv)) {
        if ($row_inv['status_terima'] == "s") {
            $id_product = $row_inv['id_product'];
            $amount = $row_inv['stock_out'];
            $branch = $row_inv['id_branch'];
            $query_get_mutation = mysqli_query($conn, "SELECT distinct i.id_inv_out,
                                                            i.inv_date, 
                                                            i.id_branch as 'from', 
                                                            o.id_branch as 'to' 
                                            FROM inv_adjust_out i 
                                                LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
                                                LEFT JOIN inv_adjust_in o on o.id_inv_in = i.id_inv_out 
                                                LEFT JOIN tb_cabang m on m.id_cabang = o.id_branch 
                                            where i.id_inv_out = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' ");
            if ($row_mutation = mysqli_fetch_array($query_get_mutation)) {
                $from = $row_mutation['from'];
                $to = $row_mutation['to'];

                $query_get_product = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $from . "' ");
                if ($row_stock = mysqli_fetch_array($query_get_product)) {
                    $stock = intval($row_stock['stock']);

                    $total_stock = $stock + $amount;
                    mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $from . "'");
                    $query_get_product_in = mysqli_query($conn, "SELECT stock,hpp FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $to . "' ");
                    if ($row_stock_in = mysqli_fetch_array($query_get_product_in)) {
                        $stock_in = intval($row_stock_in['stock']);

                        $total_stock_in = $stock_in - $amount;

                        if ($row_inv['selisih'] > 1) {
                            $hpp_less = $row_stock_in['hpp'] - $row_inv['selisih'];
                        } else {
                            $hpp_less = $row_stock_in['hpp'] + $row_inv['selisih'];
                        }

                        if ($hpp_less < 1) {
                            $hpp_less = 0;
                        }


                        mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock_in . "',hpp = '" . $hpp_less . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $to . "'");
                    }
                } else {
                    $valid = 0;
                    $msg = "Delete Transaction : Failed !";
                    // $msg=$query_get_product;
                    // echo $query_get_product;
                }
            }
        }
    }

    if ($valid == 1) {
        $select_out = "
                SELECT *,SUM(stock_out) AS total_out
                FROM inv_adjust_out
                WHERE id_inv_out = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' GROUP BY id_product
            ";
        $query_out = mysqli_query($conn, $select_out);
        while ($row_out = mysqli_fetch_array($query_out)) {
            $select_stock = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_out['id_product'] . "' AND id_cabang = '" . $row_out['id_branch'] . "'";
            $query_stock = mysqli_query($conn, $select_stock);
            $data_stock = mysqli_fetch_array($query_stock);

            if ($data_stock['hpp'] < 1) {
                $hpp = (0 + ($row_out['hpp'] * $row_out['total_out'])) / (0 + $row_out['total_out']);
                $update_hpp = mysqli_query($conn, "
                    UPDATE tb_stock_cabang SET hpp = '" . $hpp . "' WHERE id_bahan = '" . $row_out['id_product'] . "' AND id_cabang = '" . $row_out['id_branch'] . "'
                ");
            }
        }
    }
    if ($valid == 1) {
        $select_out = "
                SELECT * 
                FROM inv_adjust_in
                WHERE id_inv_in = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' GROUP BY id_product
            ";
        $query_out = mysqli_query($conn, $select_out);
        while ($row_out = mysqli_fetch_array($query_out)) {
            $select_stock = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_out['id_product'] . "' AND id_cabang = '" . $row_out['id_branch'] . "'";
            $query_stock = mysqli_query($conn, $select_stock);
            $data_stock = mysqli_fetch_array($query_stock);

            if ($data_stock['stock'] < 1) {
                $update_hpp = mysqli_query($conn, "
                    UPDATE tb_stock_cabang SET hpp = '0' WHERE id_bahan = '" . $row_out['id_product'] . "' AND id_cabang = '" . $row_out['id_branch'] . "'
                ");
            }
        }
    }

    if ($valid == 1) {
        $query_del = mysqli_query($conn, "DELETE FROM inv_adjust_out WHERE id_inv_out ='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' ");
        $query_del_in = mysqli_query($conn, "DELETE FROM inv_adjust_in WHERE id_inv_in ='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' ");
        if (!$query_del) {
            $valid = 0;
            $msg = "Delete Transaction : Failed !";
        }
    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $msg = "Delete Transaction : Success !";
        $page = "inventory_mutation_view.php";
        $sec = "0.5";
        $redirect = 1;
        header("Refresh: $sec; url=$page");
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}

if (isset($_POST['bayar'])) {
    $id_inv = mysqli_real_escape_string($conn, $_POST['id_inv']);
    $akun = mysqli_real_escape_string($conn, $_POST['akun']);
    $nominal = mysqli_real_escape_string($conn, $_POST['nominal_hidden']);

    $select_payment = "
        SELECT * FROM inv_payment
        WHERE id_inv = '" . $id_inv . "' AND metode = '" . $akun . "'
   ";
    $query_payment = mysqli_query($conn, $select_payment);
    $data_payment = mysqli_fetch_array($query_payment);
    $jum_payment = mysqli_num_rows($query_payment);

    if ($jum_payment > 0) {
        echo "<script type='text/javascript'>alert('Maaf, pembayaran dengan metode ini sudah ada');window.location.href='inventory_mutation_view.php';</script>";
    } else {

        $select_biaya = "
            SELECT SUM(biaya) AS total_biaya FROM inv_adjust_out
            WHERE id_inv_out = '" . $id_inv . "'
        ";
        $query_biaya = mysqli_query($conn, $select_biaya);
        $data_biaya = mysqli_fetch_array($query_biaya);

        $select_bayar = "
            SELECT SUM(pay) AS total_bayar FROM inv_payment
            WHERE id_inv = '" . $id_inv . "'
        ";
        $query_bayar = mysqli_query($conn, $select_bayar);
        $data_bayar = mysqli_fetch_array($query_bayar);

        if ($data_biaya['total_biaya'] < ($data_bayar['total_bayar'] + $nominal)) {
            echo "<script type='text/javascript'>alert('Maaf, pembayaran anda melebihi sisa bayar');window.location.href='inventory_mutation_view.php';</script>";
        } else {
            $insert_payment = "
                INSERT INTO inv_payment(
                    id,
                    id_inv,
                    metode,
                    pay
                ) VALUES(
                    '',
                    '" . $id_inv . "',
                    '" . $akun . "',
                    '" . $nominal . "'
                )
            ";
            $query_insert_payment = mysqli_query($conn, $insert_payment);
            if ($query_insert_payment) {
                echo "<script type='text/javascript'>alert('Success : Bayar Transfer Stock');window.location.href='inventory_mutation_view.php';</script>";
            } else {
                echo "<script type='text/javascript'>alert('Failed : Bayar Transfer Stock');window.location.href='inventory_mutation_view.php';</script>";
            }
        }
    }
}


$get_branch = $_SESSION['branch'];

if (isset($_POST['cari_tgl'])) {
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch']);
    $date_now = mysqli_real_escape_string($conn, $_POST['tgl_inv']);
    $date_to = mysqli_real_escape_string($conn,$_POST['tgl_inv_to']);
    $statuss = mysqli_real_escape_string($conn,$_POST['statuss']);
}


$add_new = 'href="inventory_mutation.php?branch=' . $get_branch . '&id_inv_out=new" ';

if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
    $add_new = 'href="javascript: void(0)" data-toggle="modal" data-target="#ChooseModal"';
}

$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, product-scalable=no" />
    <title>Inventory Mutation</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="../favicon.ico">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Daterangepicker CSS -->
    <link href="vendors/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>

    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

        <?php include "header.php"; ?>

        <!-- Main Content -->
        <div class="hk-pg-wrapper">
            <!-- Container -->
            <div class="container-fluid  mt-15">
                <!-- Row -->
                <div class="row">
                    <div class="col">
                        <section class="hk-sec-wrapper">

                            <div class="row">
                                <div class="col-sm-10">
                                    <div class="form-group form-inline">
                                        <h5 class=" mr-5">Inventory Mutation
                                        </h5>
                                        <a <?= $add_new; ?> class="add_button btn btn-success btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span class="btn-text">Add New</span><span class="icon-label"><i class="fa fa-plus"></i> </span></a>
                                    </div>
                                </div>
                                <div class="col">
                                    <!-- <h5 class="hk-sec-title">
                                        <a href="print/master_product_login_print.php"
                                            class="btn btn-info btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span
                                                class="btn-text">Download Excel</span></a>
                                    </h5> -->
                                </div>
                            </div>
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
                                <div class="col-12">
                                <div class="row" style=""  >
                                    <div class="col-md-3 d mt-15">
                                        <div class="row no-gutters">
                                            <div class="col-4">
                                                <span style="" class=" form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Branch</span>
                                            </div>
                                            <div class="col-8">
                                                <select id="" class="form-control form-control-sm custom-select custom-select-sm m_branch_id ' . $class . '" name="branch" ' . $readonly . '>
                                                <option value="all">-- Pilih Cabang --</option>
                                                ';
                            if ($_SESSION['group'] == "super") {
                                $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang ORDER BY nama_cabang ASC");
                                // echo $query_get_branch;
                                while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                    $id_branch = $row_branch['id_cabang'];
                                    $nm_branch = $row_branch['nama_cabang'];
                                    if ($id_branch == $branch_id) {
                                        echo '
                                                                <option value="' . $id_branch . '" selected >' . $nm_branch . '</option>
                                                            ';
                                    } else {
                                        echo '
                                                                <option value="' . $id_branch . '" >' . $nm_branch . '</option>
                                                            ';
                                    }
                                }
                                $where_branch = " AND i.id_branch = '1' or o.id_branch = '1' AND i.inv_date = '" . $date_now . "' ";
                                $branch_id = "BRANCH-001";
                            } else if ($_SESSION['group'] == "franchise") {
                                $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
                                $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang IN('" . $filter_cabang . "') ORDER BY nama_cabang ASC");
                                // echo $query_get_branch;
                                while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                    $id_branch = $row_branch['id_cabang'];
                                    $nm_branch = $row_branch['nama_cabang'];
                                    if ($id_branch == $branch_id) {
                                        echo '
                                                                <option value="' . $id_branch . '" selected >' . $nm_branch . '</option>
                                                            ';
                                    } else {
                                        echo '
                                                                <option value="' . $id_branch . '" >' . $nm_branch . '</option>
                                                            ';
                                    }
                                }
                                $where_branch = " AND i.id_branch IN('" . $filter_cabang . "') or o.id_branch IN('" . $filter_cabang . "') AND i.inv_date = '" . $date_now . "' ";
                                $branch_id = "BRANCH-001";
                            } else {
                                $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang = '" . $get_branch . "' ");
                                // echo $query_get_branch;
                                if ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                    $id_branch = $row_branch['id_cabang'];
                                    $nm_branch = $row_branch['nama_cabang'];
                                    echo '
                                                            <option value="' . $id_branch . '" selected >' . $nm_branch . '</option>
                                                        ';
                                }
                                $where_branch = " AND i.id_branch = '" . $get_branch . "' or o.id_branch = '" . $get_branch . "' AND i.inv_date = '" . $date_now . "' ";
                                $branch_id = $get_branch;
                            }
                            echo '
                                                
                                                </select>
                                            </div>
                                        </div>                                    
                                    </div>
                                    <div class="col-md-4 mt-15">
                                        <div class="row no-gutters">
                                            <div class="col-2">
                                                <span style="" class=" form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Date</span>
                                            </div>
                                            <div class="col-4">
                                                <input type="text" class="form-control single_date form-control-sm" value="' . $date_now . '" name="tgl_inv" readonly>
                                            </div>
                                            <div class="col-2 text-center">
                                                <p class="mt-1">S/D</p>
                                            </div>
                                            <div class="col-4">
                                                <input type="text" class="form-control single_date form-control-sm" value="' . $date_to . '" name="tgl_inv_to" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-15">
                                        <div class="row no-gutters">
                                        <div class="col-2">
                                            <span style="" class=" form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Status</span>
                                        </div>
                                        <div class="col-4">
                                           <select name="statuss" id="" class="form-control form-control-sm">
                                                <option value="lunas">Lunas</option>
                                                <option value="belum_lunas">Belum Lunas</option>
                                           </select>
                                        </div>
                                            <div class="col-1 ml-2">
                                                <button type="submit" class="btn btn-info form-control-sm" name="cari_tgl"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                                </div>
                            </form>
                            ';

                            ?>
                            <br>
                            <div class="row">
                                <div class="col-md">
                                    <div class="table-responsive">
                                        <table id="datable_1" class="table table-hover w-100 display">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Transaction ID</th>
                                                    <th>Transaction Date</th>
                                                    <th>Product</th>
                                                    <th>From Branch</th>
                                                    <th>To Branch</th>
                                                    <?php
                                                    if ($_SESSION['group'] == "super") {
                                                        echo '
                                                                <th>Status</th>
                                                            ';
                                                    }
                                                    ?>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                // Panggil m.product
                                                $where_branch = "";
                                                $where_status = "";
                                                if ($_SESSION['group'] == "franchise") {
                                                    $where_branch = " AND i.id_branch IN('" . $filter_cabang . "')";
                                                }
                                                if ($_SESSION['group'] == "admin") {
                                                    $where_branch = " AND i.id_branch = '" . $get_branch . "' ";
                                                }
                                                if (isset($_POST['cari_tgl'])) {
                                                    $branch_id = mysqli_real_escape_string($conn, $_POST['branch']);
                                                    $tanggal_inv = mysqli_real_escape_string($conn, $_POST['tgl_inv']);
                                                    $tanggal_inv_to = mysqli_real_escape_string($conn, $_POST['tgl_inv_to']);
                                                    $statuss = mysqli_real_escape_string($conn,$_POST['statuss']);

                                                    if($statuss == "lunas"){
                                                        $where_status = " AND i.biaya <= (SELECT IFNULL(SUM(c.pay),0) FROM inv_payment c WHERE c.id_inv = i.id_inv_out)";
                                                    }
                                                    if($statuss == "belum_lunas"){
                                                        $where_status = " AND i.biaya > (SELECT IFNULL(SUM(c.pay),0) FROM inv_payment c WHERE c.id_inv = i.id_inv_out)";
                                                    }
                                                   

                                                    if($branch_id == "all"){
                                                        $where_branch = " AND i.inv_date BETWEEN '" . $tanggal_inv . "' AND  '".$tanggal_inv_to."' ";
                                                    }
                                                    else{
                                                        $where_branch = " AND (i.id_branch = '" . $branch_id . "' OR o.id_branch = '" . $branch_id . "') AND i.inv_date BETWEEN '" . $tanggal_inv . "' AND  '".$tanggal_inv_to."' ";
                                                    }
                                                }
                                                $query_get = mysqli_query($conn, "SELECT i.id_inv_out,
                                                                                    i.inv_date,
                                                                                    b.nama_cabang,
                                                                                    i.id_branch,
                                                                                    i.biaya
                                                                                FROM inv_adjust_out i
                                                                                LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
                                                                                LEFT JOIN inv_adjust_in o on i.id_inv_out = o.id_inv_in
                                                                                where i.id_inv_out <> 'new' and i.id_inv_out LIKE 'SM-%'
                                                                                " . $where_branch . "
                                                                                ".$where_status."
                                                                                GROUP BY i.id_inv_out ORDER BY i.inv_out_id desc");
                                                

                                                while ($row_get = mysqli_fetch_array($query_get)) {
                                                    $style = "";
                                                    $id_branch = $row_get['id_branch'];
                                                    $branch = $row_get['nama_cabang'];
                                                    if ($branch == "" && $get_branch == "pusat") {
                                                        $branch = "Pusat";
                                                    }



                                                    $query_get_in = mysqli_query($conn, "SELECT distinct 
                                                                                        b.nama_cabang,
                                                                                        i.id_branch
                                                                                    FROM inv_adjust_in i
                                                                                    LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
                                                                                    where i.id_inv_in = '" . $row_get['id_inv_out'] . "' ");
                                                    if ($row_get_in = mysqli_fetch_array($query_get_in)) {
                                                        $nm_branch_in = $row_get_in['nama_cabang'];
                                                        $id_branch_in = $row_get_in['id_branch'];
                                                    }

                                                    $hiddden_btn = "";
                                                    if ($id_branch_in == $branch_id) {
                                                        $hiddden_btn = "hidden";
                                                    }

                                                    

                                                    echo '
                                                    <tr ' . $style . '>
                                                ';

                                                    echo '
                                                    <td style="font-size:11px;">' . $no . '</td>    
                                                    <td style="font-size:11px;">' . $row_get['id_inv_out'] . '</td>
                                                    <td style="font-size:11px;">' . $row_get['inv_date'] . '</td>
                                                    <td style="font-size:11px;">
                                                    ';
                                                    $query_get_product = mysqli_query($conn, "SELECT p.nama_bahan,p.uom,i.stock_out
                                                                            FROM tb_bahan p
                                                                            JOIN inv_adjust_out i ON i.id_product = p.id_bahan
                                                                            where i.id_inv_out = '" . $row_get['id_inv_out'] . "'
                                                                            ORDER BY p.nama_bahan asc");
                                                    $baris_product = mysqli_num_rows($query_get_product);
                                                    if ($row_product = mysqli_fetch_array($query_get_product)) {
                                                        if ($baris_product == "1") {
                                                            echo '
                                                                <span>' . $row_product['nama_bahan'] . ' = ' . $row_product['stock_out'] . ' ' . $row_product['uom'] . '</span> <br>
                                                            ';
                                                        } else {
                                                            echo '
                                                                <span>' . $baris_product . ' Items</span>
                                                            ';
                                                        }
                                                    }
                                                    echo '
                                                    </td>

                                                    <td style="font-size:11px;">' . $branch . '</td>
                                                    <td style="font-size:11px;">' . $nm_branch_in . '</td>
                                                    ';

                                                    if ($_SESSION['group'] == "super") {

                                                        $select_total = "
                                                            SELECT SUM(biaya) AS total_biaya FROM inv_adjust_out
                                                            WHERE id_inv_out = '" . $row_get['id_inv_out'] . "'
                                                        ";
                                                        $query_total = mysqli_query($conn, $select_total);
                                                        $data_total = mysqli_fetch_array($query_total);

                                                        $select_payment = "
                                                            SELECT SUM(pay) AS total_bayar FROM inv_payment WHERE id_inv = '" . $row_get['id_inv_out'] . "'
                                                        ";
                                                        $query_payment = mysqli_query($conn, $select_payment);
                                                        $data_payment = mysqli_fetch_array($query_payment);

                                                        echo '
                                                        <td>
                                                            <div class="form-group">
                                                            <form action="" method="post">
                                                            <input type="hidden" name="id_inv" class="" value="' . $row_get['id_inv_out'] . '">
                                                            <select name="akun" class="akun" id="">
                                                        ';

                                                        $select_akun = "
                                                            SELECT * 
                                                            FROM tb_akun 
                                                            WHERE status_sj = 'aktif'
                                                        ";
                                                        $query_akun = mysqli_query($conn, $select_akun);
                                                        while ($row_akun = mysqli_fetch_array($query_akun)) {
                                                            echo '
                                                                <option value="' . $row_akun['id_akun'] . '">' . $row_akun['bank'] . '</option>
                                                            ';
                                                        }



                                                        echo '
                                                            </select>
                                                            <br>
                                                            <input type="text" class="text-right nominal nominal_' . $row_get['id_inv_out'] . '" name="nominal" data-id_inv="' . $row_get['id_inv_out'] . '" value="0">
                                                            <input type="hidden" class="text-right nominal_hidden_' . $row_get['id_inv_out'] . '" name="nominal_hidden" value="0">

                                                            ';

                                                        $select_payment = "
                                                                SELECT * FROM inv_payment
                                                                WHERE id_inv = '" . $row_get['id_inv_out'] . "'
                                                            ";
                                                        $query_payment = mysqli_query($conn, $select_payment);
                                                        while ($row_payment = mysqli_fetch_array($query_payment)) {

                                                            $select_akun = "
                                                                    SELECT * FROM tb_akun WHERE id_akun = '" . $row_payment['metode'] . "'
                                                                ";
                                                            $query_akun = mysqli_query($conn, $select_akun);
                                                            $data_akun = mysqli_fetch_array($query_akun);

                                                            echo '<p style="font-size:11px;">' . $data_akun['bank'] . ' : ' . number_format($row_payment['pay']) . '</p>';
                                                        }

                                                        if ($data_total['total_biaya'] - $data_payment['total_bayar']  < 1) {
                                                            $paid = "<span style='font-size:11px;color:green;'>Lunas</span>";
                                                        } else {
                                                            $paid = "<span style='font-size:11px;color:red;'>Belum Lunas</span>";
                                                        }

                                                        echo '
                                                            <p style="font-size:11px;">Sisa : ' . number_format($data_total['total_biaya'] - $data_payment['total_bayar']) . '</p>
                                                            <p style="font-size:11px;">Grand Total : ' . number_format($data_total['total_biaya']) . '</p>
                                                            <p style="font-size:11px;">Status : ' . $paid . '</p>
                                                        ';

                                                        if ($data_total['total_biaya'] - $data_payment['total_bayar'] > 0) {
                                                            echo '
                                                            <button type="submit" class="btn btn-xs btn-secondary" name="bayar">Bayar</button>
                                                            ';
                                                        }
                                                        echo '
                                                                </form>
                                                            </div>
                                                        </td>';
                                                    }

                                                    echo '
                                                    <td>
                                                        <a href="inventory_mutation.php?branch=' . $id_branch . '&id_inv_out=' . $row_get['id_inv_out'] . '&view=detail" class="btn btn-xs btn-icon btn-info btn-icon-style-1" >
                                                            <span class="btn-icon-wrap"><i class="fa fa-eye"></i></span>
                                                        </a>
                                                        
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 hapus_button" data-toggle="modal" data-target="#DeleteProductModal" ' . $hiddden_btn . '
                                                            data-id_hapus="' . $row_get['id_inv_out'] . '" ">
                                                            <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                                                        </a>
                                                    </td>
                                                
                                                </tr>
                                                    ';
                                                    $no++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <!-- /Row -->
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

    <!-- DELETE MODAL -->
    <div class="modal fade" id="DeleteProductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Transaction</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        <input type="hidden" class="form-control status" name="status">
                        Do you want to delete this transaction?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-danger btn-sm" name="delete" value="Delete">
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- END DELETE MODAL -->

    <!-- Add New MODAL -->
    <div class="modal fade" id="ChooseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Choose Branch</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span style="" class="form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Branch</span>
                            </div>

                            <select id="" class="form-control form-control-sm custom-select custom-select-sm branch_modal" name="branch">
                                <?php

                                if ($_SESSION['group'] == "super") {
                                    $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang");
                                    // echo $query_get_branch;
                                    while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                        $id_branch = $row_branch['id_cabang'];
                                        $nm_branch = $row_branch['nama_cabang'];
                                        echo '
                                            <option value="' . $id_branch . '" >' . $nm_branch . '</option>
                                        ';
                                    }
                                }
                                if ($_SESSION['group'] == "franchise") {
                                    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
                                    $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang IN('" . $filter_cabang . "')");
                                    // echo $query_get_branch;
                                    while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                        $id_branch = $row_branch['id_cabang'];
                                        $nm_branch = $row_branch['nama_cabang'];
                                        echo '
                                            <option value="' . $id_branch . '" >' . $nm_branch . '</option>
                                        ';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <a class="choose_branch btn btn-success btn-sm"><span class="text-white">Choose</span></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END Add New MODAL -->

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

    <!-- Daterangepicker JavaScript -->
    <script src="vendors/moment/min/moment.min.js"></script>
    <script src="vendors/daterangepicker/daterangepicker.js"></script>
    <script src="dist/js/daterangepicker-data.js"></script>

    <!-- Toggles JavaScript -->
    <script src="vendors/jquery-toggles/toggles.min.js"></script>
    <script src="dist/js/toggle-data.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>
    <script src="vendors/jquery/dist/jquery.mask.min.js"></script>

    <!-- Delete -->
    <script type="text/javascript">
        $(".nominal").mask('#,##0', {
            reverse: true
        });

        $(document).on("keyup", ".nominal", function() {
            var id_inv = $(this).data('id_inv');
            var subtotal = $(".nominal_" + id_inv).val();
            subtotal = subtotal.split(',').join('');
            if (isNaN(subtotal)) subtotal = 0;

            $('.nominal_hidden_' + id_inv).val(subtotal);
        });

        $(document).on("click", '.hapus_button', function(e) {
            var id_hapus = $(this).data('id_hapus');
            var status = $(this).data('status');
            $(".id_hapus").val(id_hapus);
            $(".status").val(status);
        });

        $(document).on("click", '.choose_branch', function(e) {
            var id_branch = $('.branch_modal').val();
            window.location.replace("inventory_mutation.php?branch=" + id_branch + "&id_inv_out=new");
        });

        $(document).on("click", ".pelunasan", function() {
            var id_inv = $(this).data('id_inv');
            $('.id_inv').val(id_inv);
        });

        $(document).on("click", ".void_pelunasan", function() {
            var id_inv = $(this).data('id_inv');
            $('.id_inv_void').val(id_inv);
        });

        // $(document).on("click", '.add_button', function (e) {
        //     var id = $(this).data('id_product');
        //     $(".id").val(id);
        //     $(".id_product").prop("readonly",false);
        //     $(".id_product").removeClass("filled-input");
        //     $(".id_product").val("");
        //     $(".nm_product").val("");
        //     $(".pass_product").val("");
        //     $(".id_branch").val("");
        // });

        // $(document).on("click", '.edit_button', function (e) {
        //     var id = $(this).data('id_product');
        //     var nm = $(this).data('nm_product');
        //     var pass = $(this).data('pass_product');
        //     var branch = $(this).data('id_branch');
        //     $(".id").val(id);
        //     $(".id_product").val(id);
        //     $(".nm_product").val(nm);
        //     $(".pass_product").val(pass);
        //     $(".id_branch").val(branch);
        //     $(".id_product").prop("readonly",true);
        //     $(".id_product").addClass("filled-input");
        // });
    </script>


</body>

</html>