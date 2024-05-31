<?php


session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['branch'])) {
    header('Location:index.php');
}

$id_inv = mysqli_real_escape_string($conn, $_GET['id_inv_out']);
$id_inv_filter = mysqli_real_escape_string($conn, $_GET['id_inv_out']);

$id_user = $_SESSION['id_user'];
if (isset($_GET['branch'])) {
    $id_branch = mysqli_real_escape_string($conn, $_GET['branch']);
}
$date = date("Y-m-d H:i:s");

if (isset($_POST['finish'])) {
    $valid = 0;
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $id_inv = mysqli_real_escape_string($conn, $_POST['id_inv']);
    $date_out = mysqli_real_escape_string($conn, $_POST['date_out']);
    $nm_cust = mysqli_real_escape_string($conn, $_POST['nm_cust']);
    $email_cust = mysqli_real_escape_string($conn, $_POST['email_cust']);
    $telp_cust = mysqli_real_escape_string($conn, $_POST['telp_cust']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $kodepos = mysqli_real_escape_string($conn, $_POST['kodepos']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $id_customer = $telp_cust;

    if ($email_cust !== "") {
        $query_get_user = mysqli_query($conn, "SELECT * FROM tb_user WHERE id_user='" . $email_cust . "' ");
        $num_rows_user = mysqli_num_rows($query_get_user);
        if ($num_rows_user == 0) {
            mysqli_query($conn, "INSERT INTO tb_user (id_user,nm_user,telp_user,grup) VALUES ('" . $email_cust . "','" . $nm_cust . "','" . $telp_cust . "','customer')");
        }
        $id_customer = mysqli_real_escape_string($conn, $_POST['email_cust']);
    }





    if ($id == $_SESSION['id_user']) {
        $find_date = date("ymd");
        $query = mysqli_query($conn, "SELECT max(id_inv_out) as kodeTerbesar FROM inv_product_out WHERE id_inv_out like '___" . $find_date . "%' ");
        if ($data = mysqli_fetch_array($query)) {
            $kode = $data['kodeTerbesar'];
            $urutan = (int) substr($kode, 9, 4);
            $urutan++;
            $tahun = date('y');
            $bulan = date('m');
            $tanggal = date('d');
            $huruf = "SO-";
            $id_inv = $huruf . $tahun . $bulan . $tanggal . sprintf("%04s", $urutan);
        } else {
            $id_inv = "SO-" . $find_date . "0001";
        }
        $query_add_inv_out = "  UPDATE inv_product_out 
                                    SET id_inv_out = '" . $id_inv . "',
                                        inv_date = '" . $date_out . "',
                                        id_user = '" . $email_cust . "',
                                        nm_cust = '" . $nm_cust . "',
                                        telp_cust = '" . $telp_cust . "',
                                        kota = '" . $kota . "',
                                        kodepos = '" . $kodepos . "',
                                        alamat = '" . $alamat . "',
                                        create_by = '" . $id_user . "',
                                        create_date = '" . $date . "'
                                    WHERE id_inv_out = '" . $id . "'";
        if (mysqli_query($conn, $query_add_inv_out)) {
            $query_get_inv = mysqli_query($conn, "SELECT id_product,stock_out,id_branch FROM inv_product_out WHERE id_inv_out = '" . $id_inv . "' ");
            while ($row_amount = mysqli_fetch_array($query_get_inv)) {
                $id_product = $row_amount['id_product'];
                $amount_origin = intval($row_amount['stock_out']);
                $id_branch = $row_amount['id_branch'];
                $query_get_product = mysqli_query($conn, "SELECT b.stock,
                                                            a.type_product 
                                                        FROM m_branch_stock b 
                                                            LEFT JOIN m_product a ON b.id_product = a.m_product_id 
                                                        WHERE b.id_product = '" . $id_product . "' and b.id_branch = '" . $id_branch . "' ");
                if ($row_stock = mysqli_fetch_array($query_get_product)) {
                    $stock = intval($row_stock['stock']);
                    $total_stock = $stock - $amount_origin;
                    mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_stock . "' WHERE id_product = '" . $id_product . "' and id_branch = '" . $id_branch . "' ");
                } else {
                    $query_get_ingredients = mysqli_query($conn, "SELECT i.id_ingredients,
                                                                p.stock,
                                                                i.amount_ingredients 
                                                            FROM m_branch_stock p 
                                                                LEFT JOIN m_product a ON p.id_product = a.m_product_id 
                                                                LEFT JOIN m_ingredients i ON i.id_ingredients = p.id_product 
                                                            WHERE i.id_product = '" . $id_product . "' and p.id_branch = '" . $id_branch . "' ");
                    while ($row_ingredients = mysqli_fetch_array($query_get_ingredients)) {
                        $stock_ingredients = $row_ingredients['stock'];
                        $amount_product = $row_ingredients['amount_ingredients'];
                        $total_out = $amount_origin * $amount_product;
                        $id_ingredients = $row_ingredients['id_ingredients'];
                        $total_stock = $stock_ingredients - $total_out;
                        mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_stock . "' WHERE id_product = '" . $id_ingredients . "' and id_branch = '" . $id_branch . "' ");
                    }
                }
            }
            $msg = "Add Transaction : Success !";
            $valid = 1;
        } else {
            $valid = 0;
            $msg = "Add Transaction : Failed !";
        }
    }
    if ($id !== $_SESSION['id_user']) {
        // Edit Branch Process
        $query_set_inv_out = "  UPDATE inv_product_out 
                                    SET id_inv_out = '" . $id_inv . "',
                                        inv_date = '" . $date_out . "',
                                        id_user = '" . $email_cust . "',
                                        nm_cust = '" . $nm_cust . "',
                                        telp_cust = '" . $telp_cust . "',
                                        kota = '" . $kota . "',
                                        kodepos = '" . $kodepos . "',
                                        alamat = '" . $alamat . "',
                                        change_by = '" . $id_user . "',
                                        change_date = '" . $date . "'
                                    WHERE id_inv_out = '" . $id . "'";
        if (mysqli_query($conn, $query_set_inv_out)) {
            $valid = 1;
            $msg = "Edit Transaction : Success !";
        } else {
            $valid = 0;
            $msg = "Edit Transaction : Failed !";
        }
        // End Edit Branch Process

    }

    if ($valid == 0) {
        // rollback();
    } else {
        // commit();
        $page = "inventory_product_out_view.php";
        $sec = "0.5";
        $redirect = 1;
        header("Refresh: $sec; url=$page");
    }

    //   echo "<script type='text/javascript'>alert('".$msg."')</script>";

}

if ($id_inv_filter == "new") {
    $id_inv_filter = $_SESSION['id_user'];
}
$transaction_date = $date = date("Y-m-d");
$id = "New";
$date = "";
$nm_cust = "";
$email_cust = "";
$telp_cust = "";
$id_cust = "";
$tgl_pasang = "";
$kota = "";
$kodepos = "";
$alamat = "";

if (isset($_GET['branch'])) {
    $get_branch = mysqli_real_escape_string($conn, $_GET['branch']);
}

$readonly = "";
$hidden = "";
$filledtext = "";
$disabled = "";
$hide = "style='display:none;'";
if (isset($_GET['view'])) {
    $readonly = "readonly";
    $disabled = "disabled";
    $hidden = "hidden";
    $filledtext = "filled-input";
    $hide = "";
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
    <title>Add/Edit Transaction Product Out</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

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
            <div class="preloader-it">
                <div class="loader-pendulums"></div>
            </div>
            <!-- Container -->
            <div class="container-fluid mt-15">
                <!-- Row -->
                <div class="row">
                    <div class="col">
                        <section class="hk-sec-wrapper">
                            <h5 class="hk-sec-title">Inventory Product OUT</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <form action="inventory_product_out.php?branch=<?= $id_branch; ?>&id_inv_out=<?php echo $id_inv_filter; ?>" method="POST">
                                        <?php
                                        $query_get_inv_out = mysqli_query($conn, "SELECT *
                                                                            FROM inv_product_out
                                                                            WHERE id_inv_out = '" . $id_inv_filter . "' ");
                                        if ($row_inv_out = mysqli_fetch_array($query_get_inv_out)) {
                                            $id = $row_inv_out['id_inv_out'];
                                            $transaction_date = $row_inv_out['inv_date'];
                                            $nm_cust = $row_inv_out['nm_cust'];
                                            $email_cust = $row_inv_out['id_user'];
                                            $telp_cust = $row_inv_out['telp_cust'];
                                            $id_cust = $row_inv_out['id_user'];
                                            $tgl_pasang = $row_inv_out['tgl_pasang'];
                                            $kota = $row_inv_out['kota'];
                                            $kodepos = $row_inv_out['kodepos'];
                                            $alamat = $row_inv_out['alamat'];
                                        }
                                        echo '
                                        <div class="row justify-content-center">
                                            <div class="col-sm-6">
                                                <input type="hidden" class="form-control id" name="id"
                                                    value="' . $id_inv_filter . '">
                                                    <input type="hidden" class="form-control id_branch"
                                                    value="' . $id_branch . '">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> ID Inventory OUT </span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="id_inv"
                                                            id="id_inv"
                                                            class="form-control filled-input form-control-sm id_inv"
                                                            value="' . $id . '" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Transaction date</span>
                                                        </div>
                                                        <input autocomplete="off" type="date" name="date_out"
                                                            id="date_out"
                                                            class="form-control filled-input form-control-sm date_out" value="' . $transaction_date . '"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-start">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Customer Email </span>
                                                        </div>
                                                        <input autocomplete="off" type="text"
                                                            id="" name="email_cust" list="cust_list"
                                                            class="form-control ' . $filledtext . ' form-control-sm email_cust" ' . $readonly . ' value="' . $email_cust . '">
                                                        <datalist id="cust_list" class="cust_list">
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Customer Name </span>
                                                        </div>
                                                        <input autocomplete="off" type="text" 
                                                            id="cust_nm" name="nm_cust"
                                                            class="form-control ' . $filledtext . ' form-control-sm cust_nm " ' . $readonly . ' value="' . $nm_cust . '">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Nomor HP </span>
                                                        </div>
                                                        <input autocomplete="off" type="text" 
                                                            id="telp" name="telp_cust" value="' . $telp_cust . '"
                                                            class="form-control ' . $filledtext . ' form-control-sm telp_cust " ' . $readonly . '>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Kota </span>
                                                        </div>
                                                        <input type="text" name="kota" class="form-control ' . $filledtext . ' form-control-sm" value="' . $kota . '" ' . $readonly . '>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Kode Pos </span>
                                                        </div>
                                                        <input type="number" name="kodepos" class="form-control ' . $filledtext . ' form-control-sm" value="' . $kodepos . '" ' . $readonly . '>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Alamat </span>
                                                        </div>
                                                        <textarea name="alamat" id="alamat" cols="30" rows="2" class="form-control ' . $filledtext . ' form-control-sm" ' . $readonly . '>' . $alamat . '</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6" ' . $hidden . '>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Product Name </span>
                                                        </div>
                                                        <input autocomplete="off" type="text" list="product_list"
                                                            id="product_nm" 
                                                            class="form-control ' . $filledtext . ' form-control-sm product_nm " ' . $readonly . '>
                                                        <datalist id="product_list" class="product_list">
                                                        </datalist>
                                                    </div>
                                                </div>
                                                <div class="form-group form-group-amount product">
                                                    <span style="width:150px; min-width:150px;"
                                                        class="form-control filled-input form-control-sm input-group-text "
                                                        id="inputGroup-sizing-sm">Stock Amount </span>
                                                    <div class="input-group input-group-sm">    
                                                        <input autocomplete="off" type="number" min="0" id="stock_in"
                                                            class="form-control form-control-sm amount_product filled-input" readonly>
                                                        <div class="uom">
                                                            <div class="input-group input-group-sm">
                                                                <span class="form-control filled-input form-control-sm input-group-text uom_product"
                                                                    id="inputGroup-sizing-sm">(UOM)</span>
                                                            </div>
                                                        </div>                                                        
                                                    </div>
                                                </div>
                                                <div class="form-group form-group-amount price_tag">
                                                    <span style="width:150px; min-width:150px;"
                                                        class="form-control filled-input form-control-sm input-group-text "
                                                        id="inputGroup-sizing-sm"> Harga </span>
                                                    <div class="input-group input-group-sm">    
                                                        <input autocomplete="off" type="number" min="0" id="price"
                                                            class="form-control form-control-sm filled-input sprice" value="0" readonly>
                                                                                                        
                                                    </div>
                                                </div>
                                                <div class="form-group pemasangan_input" style="display:none;">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Pemasangan </span>
                                                        </div>
                                                        <select name="pemasangan" id="pemasangan" class="form-control form-control-sm">
                                                            <option value="pasang_sendiri">Pasang Sendiri</option>
                                                            <option value="dipasangkan">Dipasangkan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group stat_cus_input" style="display:none;">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Body Custom </span>
                                                        </div>
                                                        <select name="stat_cus" id="stat_cus" class="form-control form-control-sm">
                                                        <option value="no">Tidak</option>
                                                            <option value="yes">Ya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group keterangan_input" style="display:none;">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Body custom </span>
                                                        </div>
                                                        <textarea name="keterangan" id="keterangan" cols="30" rows="2" class="form-control form-control-sm"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group part_input" style="display:none;">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Cat ulang / baret </span>
                                                        </div>
                                                        <select name="part" id="part" class="form-control form-control-sm">
                                                        <option value="no">Tidak</option>
                                                            <option value="yes">Ya</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group keterangan_part_input" style="display:none;">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Cat ulang / baret </span>
                                                        </div>
                                                        <textarea name="keterangan_part" id="keterangan_part" cols="30" rows="2" class="form-control form-control-sm"></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group paket_input" style="display:none;">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Paket </span>
                                                        </div>
                                                        <select name="paket" id="paket" class="form-control form-control-sm">
                                                            <option value="noexpress">Regular</option>
                                                            <option value="express">Express</option>
                                                            <option value="dirumah">Dirumah</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group tanggal_pasang_input" style="display:none;">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Tanggal Pasang </span>
                                                        </div>
                                                        <input type="date" name="tanggal_pasang" id="tanggal_pasang" class="form-control form-control-sm tgl_pasang">
                                                    </div>
                                                </div>
                                                <div class="button-add">
                                                    <a role="button" class="btn btn-sm btn-block btn-secondary button-plus" data-id_inv="' . $id_inv_filter . '" data-id_branch="' . mysqli_real_escape_string($conn, $_GET['branch']) . '"> 
                                                        <span class=""><i class="fa fa-plus text-white"></i></span>
                                                    </a> 
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="table-scroll mt-15">
                                        <table id="datable_2" class="table table-hover w-100 display mt-15">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Product Name</th>
                                                    <th>Price</th>
                                                    <th style="min-width:250px; width:250px;">Amount</th>
                                                    <th ' . $hidden . ' >Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="view">
                                                ';
                                        $no = 0;
                                        $where_branch = "";
                                        if ($get_branch !== "pusat") {
                                            $where_branch = " AND i.id_branch = '" . $get_branch . "' ";
                                        }
                                        $query_get_product = mysqli_query($conn, "SELECT i.*,p.nm_product,p.uom_product,p.type_product
                                                                                    FROM inv_product_out i 
                                                                                    left join m_product p on p.m_product_id = i.id_product
                                                                                    WHERE i.id_inv_out = '" . $id_inv_filter . "' 
                                                                                    " . $where_branch . " ");
                                        while ($row_product = mysqli_fetch_array($query_get_product)) {
                                            $no++;
                                            $id_inv = $row_product['inv_out_id'];
                                            $id_product = $row_product['id_product'];
                                            $id_branch = $row_product['id_branch'];
                                            $nm_product = $row_product['nm_product'];
                                            $amount = $row_product['stock_out'];
                                            $price = $row_product['price'];
                                            $uom_product = $row_product['uom_product'];

                                            $pemasangan = "";
                                            if ($row_product['type_product'] == "Product") {
                                                if ($row_product['pemasangan'] == "pasang_sendiri") {
                                                    $pemasangan = "<p>Pemasangan : Pasang Sendiri</p>";
                                                } else {
                                                    $pemasangan = "<p>Pemasangan : " . ucfirst($row_product['pemasangan']) . "</p>";
                                                }
                                            }

                                            $paket = "";
                                            if ($row_product['type_product'] == "Product") {
                                                if ($row_product['pemasangan'] == "dipasangkan") {
                                                    if ($row_product['paket'] == "express") {
                                                        $paket = "<p>Paket : Express</p>";
                                                    } else if ($row_product['paket'] == "dirumah") {
                                                        $paket = "<p>Paket : Pasang Dirumah</p>";
                                                    } else {
                                                        $paket = "<p>Paket : Regular</p>";
                                                    }
                                                }
                                            }

                                            $tanggal_pasang = "";
                                            if ($row_product['type_product'] == "Product") {
                                                if ($row_product['pemasangan'] == "dipasangkan") {
                                                    $tanggal_pasang = "<p>Tanggal Pasang : " . date("d M Y", strtotime($row_product['tgl_pasang'])) . "</p>";
                                                }
                                            }

                                            $cabang = "";
                                            if ($row_product['type_product'] == "Product") {
                                                if ($row_product['pemasangan'] == "dipasangkan") {
                                                    $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_product['id_branch'] . "'";
                                                    $query_cabang = mysqli_query($conn, $select_cabang);
                                                    $data_cabang = mysqli_fetch_array($query_cabang);
                                                    if ($row_product['paket'] !== "dirumah") {
                                                        $cabang = "<p>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                                                    }
                                                }
                                            }

                                            $stat_cus = "";
                                            if ($row_product['type_product'] == "Product") {
                                                if ($row_product['stat_cus'] == "yes") {
                                                    $stat_cus = "<p>Body Custom : Ya</p>";
                                                } else {
                                                    $stat_cus = "<p>Body Custom : Tidak</p>";
                                                }
                                            }

                                            $part = "";
                                            if ($row_product['type_product'] == "Product") {
                                                if ($row_product['part'] == "yes") {
                                                    $part = "<p>Cat ulang / baret : Ya</p>";
                                                } else {
                                                    $part = "<p>Cat ulang / baret : Tidak</p>";
                                                }
                                            }

                                            $keterangan = "";
                                            if ($row_product['type_product'] == "Product") {
                                                if ($row_product['keterangan'] !== "") {
                                                    if ($row_product['stat_cus'] == "yes") {
                                                        $keterangan = "<p>Keterangan body custom : " . $row_product['keterangan'] . "</p>";
                                                    }
                                                }
                                            }

                                            $keterangan_part = "";
                                            if ($row_product['type_product'] == "Product") {
                                                if ($row_product['keterangan'] !== "") {
                                                    if ($row_product['part'] == "yes") {
                                                        $keterangan_part = "<p>Keterangan body cat ulang / baret : " . $row_product['keterangan_part'] . "</p>";
                                                    }
                                                }
                                            }



                                            echo '
                                                <tr>
                                                    <td style="font-size:11px;">' . $no . '</td>
                                                    <td style="font-size:11px;">' . $nm_product . $pemasangan . $paket . $tanggal_pasang . $cabang . $stat_cus . $part . $keterangan . $keterangan_part . ' </td>
                                                    <td style="font-size:11px;">
                                                    <div class="input-group input-group-sm"><input type="number"
                                                        class="form-control ' . $filledtext . ' form-control-sm prices price_' . $id_inv . '"
                                                        name="price_' . $id_inv . '"
                                                            value="' . $price . '" id="price_' . $id_inv . '" data-id_inv="' . $id_inv . '" ' . $readonly . '>
                                                        <span style="width:-2vw;"
                                                            class="form-control filled-input form-control-sm input-group-text "
                                                            id="inputGroup-sizing-sm"> IDR </span>
                                                    </div>
                                                    </td>
                                                    <td style="font-size:11px;">
                                                        <div class="input-group input-group-sm"><input type="text"
                                                                class="form-control ' . $filledtext . ' form-control-sm amounts amount_' . $id_inv . '"
                                                                name="nm_product_' . $id_inv . '"
                                                                value="' . $amount . '" id="amount_' . $id_inv . '" data-id_inv="' . $id_inv . '" ' . $readonly . '>
                                                            <span style="width:-2vw;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> ' . $uom_product . ' </span>
                                                        </div>
                                                    </td>
                                                    <td ' . $hidden . '>
                                                        <a role="button"
                                                            class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list"
                                                            data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                                                            <span class="btn-icon-wrap"><i
                                                                    class="fa fa-pencil"></i></span></a>
                                                        <a role="button"
                                                            class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list"
                                                            data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                                                            <span class="btn-icon-wrap"><i
                                                                    class="icon-trash"></i></span>
                                                        </a>
                                                    </td>

                                                </tr>
                                                ';
                                        }
                                        echo '
                                            </tbody>
                                        </table>
                                        </div>
                                        ';
                                        ?>

                                        <div class="form-group justify-content-end">
                                            <div class="row">
                                                <div class="col-sm-2 mt-15" <?= $hidden ?>>
                                                    <button type="submit" class="btn btn-sm btn-success btn-block" name="finish" value="Save">Save</button>
                                                </div>
                                                <div class="col-sm-2 mt-15">
                                                    <a href="inventory_product_out_view.php" class="btn btn-sm btn-red btn-block">Cancel</a>
                                                </div>
                                                <div class="col-sm-2 mt-15" <?= $hide; ?>>
                                                    <a href="print/print_inv_product_out.php?id_inv=<?php echo $id_inv; ?>" class="btn btn-sm btn-info btn-block" target="_blank">Print</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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


    <!-- Modal -->


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

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>

    <!-- Delete -->
    <script type="text/javascript">
        $(document).on("keyup", '.product_nm', function(e) {

            var product_list = $(this).val();
            var dataString = 'product_out=' + product_list;


            if (product_list.length >= 1) {
                $.ajax({
                    type: 'POST',
                    url: 'ajax/ajax_change_datalist.php',
                    data: dataString,
                    success: function(response) {
                        $('.product_list').html(
                            response);
                    }
                });
            } else {
                $('.product_list').html('');
            }

        });

        $(document).on("change", '.product_nm', function(e) {

            var str = $(this).val();
            var split = str.split(" | ");
            var product = split[1];
            var dataString = 'product=' + product;
            if (str == "") {
                $('.amount_product').prop("readonly", true);
                $('.amount_product').addClass("filled-input");
                $('.sprice').prop("readonly", true);
                $('.sprice').addClass("filled-input");
            } else {
                $('.amount_product').prop("readonly", false);
                $('.amount_product').removeClass("filled-input");
                $('.sprice').prop("readonly", false);
                $('.sprice').removeClass("filled-input");
            }
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_product_out.php',
                data: dataString,
                success: function(response) {
                    var response = response.split("|");
                    $('.uom').html(response[0]);
                    var pemasangan = $("#pemasangan").val();
                    var stat_cus = $("#stat_cus").val();
                    var part = $("#part").val();
                    if (response[1] == "Product") {
                        $(".pemasangan_input").show(500);
                        $(".stat_cus_input").show(500);
                        $(".part_input").show(500);

                        if (pemasangan == "dipasangkan") {
                            $(".paket_input").hide(500);
                            $(".tanggal_pasang_input").hide(500);
                        }
                        if (stat_cus == "yes" || part == "yes") {
                            $("#keterangan").show(500);
                        }
                    } else {
                        $(".pemasangan_input").hide(500);
                        $(".stat_cus_input").hide(500);
                        $(".part_input").hide(500);
                        $(".paket_input").hide(500);
                        $(".tanggal_pasang_input").hide(500);
                    }

                }
            });
        });

        $(document).on("change", "#stat_cus", function() {
            var stat = $("#stat_cus").val();
            var part = $("#part").val();

            if (stat == "yes" || part == "yes") {
                $(".keterangan_input").show(500);
            } else {
                $(".keterangan_input").hide(500);
            }
        });

        $(document).on("change", "#part", function() {
            var stat = $("#stat_cus").val();
            var part = $("#part").val();

            if (part == "yes") {
                $(".keterangan_part_input").show(500);
            } else {
                $(".keterangan_part_input").hide(500);
            }
        });

        $(document).on("change", "#pemasangan", function() {
            var pemasangan = $("#pemasangan").val();
            if (pemasangan == "dipasangkan") {
                $(".paket_input").show(500);
                $(".tanggal_pasang_input").show(500);
            } else {
                $(".paket_input").hide(500);
                $(".tanggal_pasang_input").hide(500);
            }
        });

        $(document).on("keyup", '.email_cust', function(e) {
            var product_list = $(this).val();
            var dataString = 'cust_list=' + product_list;
            if (product_list.length >= 1) {
                $.ajax({
                    type: 'POST',
                    url: 'ajax/ajax_change_datalist.php',
                    data: dataString,
                    success: function(response) {
                        $('.cust_list').html(
                            response);
                    }
                });
            } else {
                $('.cust_list').html('');
            }

        });

        $(document).on("change", '.email_cust', function(e) {
            var product = $(this).val();
            var dataString = 'cust=' + product;
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_product.php',
                data: dataString,
                success: function(response) {
                    var response = response.split("|");
                    $('.telp_cust').val(response[0]);
                    $('.cust_nm').val(response[1]);
                }
            });

        });

        $(document).on("keyup", '.amount_product', function(e) {

            var str = $(".product_nm").val();
            var split = str.split(" | ");
            var product = split[1];
            var amount = $(this).val();
            var branch = $(".id_branch").val();
            var id = $(".id").val();
            var dataString = 'product_id=' + product +
                '&id_inv=' + id +
                '&branch=' + branch +
                '&amount_product=' + amount;

            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_product_out.php',
                data: dataString,
                success: function(response) {
                    $('.button-add').html(
                        response);

                }
            });

        });

        $(document).on("click", '.add_list', function(e) {

            var id_inv = $(this).data("id_inv");
            var amount = $('.amount_product').val();
            var price = $('.sprice').val();
            var str = $('.product_nm').val();
            var date = $('#tanggal_pasang').val();
            var pemasangan = $('#pemasangan').val();
            var paket = $('#paket').val();
            var stat_cus = $('#stat_cus').val();
            var part = $('#part').val();
            var keterangan = $('#keterangan').val();
            var keterangan_part = $("#keterangan_part").val();
            var split = str.split(" | ");
            var id_product = split[1];
            var id_branch = $(this).data("id_branch");
            var dataString = 'id_product=' + id_product +
                '&add=' + id_inv +
                '&date=' + date +
                '&id_branch=' + id_branch +
                '&amount=' + amount +
                '&price=' + price +
                '&pemasangan=' + pemasangan +
                '&paket=' + paket +
                '&stat_cus=' + stat_cus +
                '&part=' + part +
                '&keterangan=' + keterangan +
                '&keterangan_part=' + keterangan_part;
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_product_out.php',
                data: dataString,
                success: function(response) {

                    $('.view').html(
                        response);
                    $('.amount_product').val("");
                    $('.product_nm').val("");
                    $('.uom_product').val("(UOM)");
                    $('.amount_product').prop("readonly", true);
                    $('.amount_product').addClass("filled-input");
                    $('.sprice').prop("readonly", true);
                    $('.sprice').addClass("filled-input");
                    $('.button-plus').removeClass("btn-success");
                    $('.button-plus').removeClass("add_list");
                    $('.button-plus').addClass("btn-secondary");

                    $(".pemasangan_input").hide(500);
                    $(".stat_cus_input").hide(500);
                    $(".part_input").hide(500);
                    $(".keterangan_input").hide(500);
                    $(".keterangan_part_input").hide(500);
                    $(".paket_input").hide(500);
                    $(".tanggal_pasang_input").hide(500);

                    $(".sprice").val("0");
                    $("#pemasangan").val("pasang_sendiri");
                    $("#stat_cus").val("no");
                    $("#part").val("no");
                    $("#keterangan").val("");
                    $("#keterangan_part").val("");
                    $("#paket").val("noexpress");
                }
            });
        });

        $(document).on("click", '.set_list', function(e) {
            var id_product = $(this).data("id_product");
            var id_branch = $(this).data("id_branch");
            var id_inv = $(this).data("id_inv");
            var amount = $(".amount_" + id_inv).val();
            var price = $(".price_" + id_inv).val();
            var id_inv_filter = $(".id").val();
            var dataString = 'id_product=' + id_product +
                '&edit=' + id_inv +
                '&inv_filter=' + id_inv_filter +
                '&id_branch=' + id_branch +
                '&amount=' + amount +
                '&price=' + price;
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_product_out.php',
                data: dataString,
                success: function(response) {
                    alert("Change Amount Success");
                }
            });
        });

        $(document).on("click", '.del_list', function(e) {
            var id_product = $(this).data("id_product");
            var id_branch = $(this).data("id_branch");
            var id_inv = $(this).data("id_inv");
            var id_inv_filter = $(".id").val();
            var amount = $(".amount_" + id_inv).val();
            var dataString = 'delete=' + id_inv +
                '&id_branch=' + id_branch +
                '&inv_filter=' + id_inv_filter +
                '&amount=' + amount +
                '&id_product=' + id_product;
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_product_out.php',
                data: dataString,
                success: function(response) {
                    $('.view').html(
                        response);

                }
            });
        });


        // $(document).on("keyup", ".prices", function() {
        //     var id_inv = $(this).data("id_inv");
        //     var price = $(".price_" + id_inv).val();

        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_increase_inv_out.php",
        //         data: {
        //             "id_inv": id_inv,
        //             "price": price
        //         },
        //         cache: true,
        //         success: function(result) {

        //         }
        //     });
        // });
        // $(document).on("change", ".amounts", function() {
        //     var id_inv = $(this).data("id_inv");
        //     var amount = $(".amount_" + id_inv).val();

        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_increase_amount_inv_out.php",
        //         data: {
        //             "id_inv": id_inv,
        //             "amount": amount
        //         },
        //         cache: true,
        //         success: function(result) {

        //         }
        //     });
        // });




        $(document).ready(function() {
            $('#datatable_2').DataTable({
                scrollY: '40vh',
                scrollCollapse: true,
                paging: false,
                "ordering": false,
                AutoWidth: false,
                "bLengthChange": false,
                "sScrollXInner": "200%",
                language: {
                    search: "",
                    searchPlaceholder: "Search"
                }


            });

            // $('#table').css({
            //     sDom: 'r<"H"lf><"datatable-scroll"t><"F"ip>'
            // });
        });
    </script>


</body>

</html>