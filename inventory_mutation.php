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
    $valid = 1;
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $id_inv = "";
    $date_out = date("Y-m-d");
    $branch_out = mysqli_real_escape_string($conn, $_GET['branch']);
    $branch_in = mysqli_real_escape_string($conn, $_POST['branch_to']);
    // if($_SESSION['branch'] !== "pusat" || $_SESSION['group'] !== "Franchise"){
    //     $branch_out = mysql_real_escpae_string($_GET['branch']));
    // }


    $select_inv = "SELECT distinct i.inv_out_id,i.id_inv_out,i.id_product,i.biaya,i.stock_out, 
    i.inv_date, i.id_branch as 'from', 
    o.id_branch as 'to',o.inv_in_id 
FROM inv_adjust_out i 
LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
LEFT JOIN inv_adjust_in o on o.id_inv_in = i.id_inv_out 
LEFT JOIN tb_cabang m on m.id_cabang = o.id_branch 
where i.id_inv_out = '" . $id . "'";
    $query_inv = mysqli_query($conn, $select_inv);
    while ($row_inv = mysqli_fetch_array($query_inv)) {
        $to = $row_inv['to'];
        $from = $row_inv['from'];

        $select_stock = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_inv['id_product'] . "' AND id_cabang = '" . $branch_in . "'";
        $query_stock = mysqli_query($conn, $select_stock);
        $data_stock = mysqli_fetch_array($query_stock);
        $jum_stock = mysqli_num_rows($query_stock);
        if ($jum_stock > 0) {
            $hpp_new = (($data_stock['hpp'] * $data_stock['stock']) + $row_inv['biaya']) / ($data_stock['stock'] + $row_inv['stock_out']);
            $hpp = $data_stock['hpp'];

            $selisih = $hpp - $hpp_new;
        } else {
            $selisih = (0 + $row_inv['biaya']) / (0 + $row_inv['stock_out']);
        }

        // $select_stock_in = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_inv['id_product'] . "' AND id_cabang = '" . $to . "'";
        // $query_stock_in = mysqli_query($conn, $select_stock_in);
        // $data_stock_in = mysqli_fetch_array($query_stock_in);
        // $jum_stock_in = mysqli_num_rows($query_stock_in);
        // if ($jum_stock_in > 0) {
        //     $selisih_in = (($data_stock_in['hpp'] * $data_stock_in['stock']) + $row_inv['biaya']) / ($data_stock_in['stock'] + $row_inv['stock_out']) - $data_stock_in['hpp'];
        // } else {
        //     $selisih_in = (0 + $row_inv['biaya']) / (0 + $row_inv['stock_out']);
        // }

        $query_add_inv_out = mysqli_query($conn, "UPDATE inv_adjust_out SET 
        selisih = '" . $selisih . "' WHERE inv_out_id = '" . $row_inv['inv_out_id'] . "'");
        $query_add_inv_in = mysqli_query($conn, "UPDATE inv_adjust_in SET 
        selisih = '" . $selisih . "' WHERE inv_in_id = '" . $row_inv['inv_out_id'] . "'");
    }



    if ($id == $_SESSION['id_user']) {
        $find_date = date("ymd");
        $query = mysqli_query($conn, "SELECT max(id_inv_out) as kodeTerbesar FROM inv_adjust_out WHERE id_inv_out like 'SM-" . $find_date . "%' ");
        if ($data = mysqli_fetch_array($query)) {
            $kode = $data['kodeTerbesar'];
            $urutan = (int) substr($kode, 9, 4);
            $urutan++;
            $tahun = date('y');
            $bulan = date('m');
            $tanggal = date('d');
            $huruf = "SM-";
            $id_inv = $huruf . $tahun . $bulan . $tanggal . sprintf("%04s", $urutan);
        } else {
            $id_inv = "SM-" . $find_date . "0001";
        }

        $query_cabang_from = mysqli_query($conn, "
            SELECT 
            id_cabang,
            nama_cabang,
            jenis_cabang
            FROM tb_cabang
            WHERE id_cabang = '" . $branch_out . "'
        ");
        $data_cabang_from = mysqli_fetch_array($query_cabang_from);
        $jenis_cabang_from = $data_cabang_from['jenis_cabang'];

        $query_cabang_to = mysqli_query($conn, "
            SELECT 
            id_cabang,
            nama_cabang,
            jenis_cabang
            FROM tb_cabang
            WHERE id_cabang = '" . $branch_in . "'
        ");
        $data_cabang_to = mysqli_fetch_array($query_cabang_to);
        $jenis_cabang_to = $data_cabang_to['jenis_cabang'];

        $is_sell = 0;
        if ($jenis_cabang_from == "n" && $jenis_cabang_to == "f") {
            $is_sell = 1;
        }
        if ($jenis_cabang_from == "f" && $jenis_cabang_to == "f") {
            $is_sell = 1;
        }
        if ($jenis_cabang_from == "f" && $jenis_cabang_to == "n") {
            $is_sell = 1;
        }

        $query_add_inv_out = "  UPDATE inv_adjust_out 
                                    SET id_inv_out = '" . $id_inv . "',
                                        inv_date = '" . $date_out . "',
                                        id_branch = '" . $branch_out . "',
                                        is_sell = '" . $is_sell . "',
                                        create_by = '" . $id_user . "',
                                        create_date = '" . $date . "'
                                    WHERE id_inv_out = '" . $id . "'";
        if (mysqli_query($conn, $query_add_inv_out)) {

            $select_out = "
                SELECT * 
                FROM inv_adjust_out 
                WHERE id_inv_out = '" . $id_inv . "'
            ";
            $query_out = mysqli_query($conn, $select_out);
            while ($row_out = mysqli_fetch_array($query_out)) {
                $query_insert = mysqli_query($conn, "
                    INSERT INTO inv_adjust_in(
                        inv_in_id,
                        id_inv_in,
                        inv_date,
                        id_product,
                        id_branch,
                        stock_in,
                        biaya,
                        selisih,
                        hpp,
                        is_sell,
                        create_by,
                        create_date
                    ) VALUES(
                        '',
                        '" . $row_out['id_inv_out'] . "',
                        '" . date("Y-m-d") . "',
                        '" . $row_out['id_product'] . "',
                        '" . $branch_in . "',
                        '" . $row_out['stock_out'] . "',
                        '" . $row_out['biaya'] . "',
                        '" . $row_out['selisih'] . "',
                        '" . $row_out['hpp'] . "',
                        '" . $row_out['is_sell'] . "',
                        '" . $row_out['create_by'] . "',
                        '" . $row_out['create_date'] . "'
                    )
                ");
            }

            // $query_get_inv = mysqli_query($conn, "SELECT id_product,stock_out,biaya FROM inv_adjust_out WHERE id_inv_out = '" . $id_inv . "' ");
            // while ($row_amount = mysqli_fetch_array($query_get_inv)) {
            //     $id_product = $row_amount['id_product'];
            //     $amount_origin = intval($row_amount['stock_out']);
            //     $hpp = (0 + $row_amount['biaya']) / (0 + $amount_origin);
            //     $query_get_product = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_out . "' ");
            //     if ($row_stock = mysqli_fetch_array($query_get_product)) {
            //         $stock = intval($row_stock['stock']);

            //         $total_stock = $stock - $amount_origin;
            //         mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_out . "'");

            //         $query_get_product = mysqli_query($conn, "SELECT stock,hpp FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_in . "' ");
            //         if ($row_stock = mysqli_fetch_array($query_get_product)) {
            //             $stock_origin = intval($row_stock['stock']);

            //             $total_stock_in = $stock_origin + $amount_origin;

            //             $hpp = (($row_stock['hpp'] * $stock_origin) + $row_amount['biaya']) / $total_stock_in;

            //             mysqli_query($conn, "UPDATE tb_stock_cabang SET 
            //             stock = '" . $total_stock_in . "',
            //             hpp = '" . $hpp . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_in . "'");
            //         } else {

            //             mysqli_query($conn, "insert into tb_stock_cabang (id_bahan,
            //                                                         id_cabang,
            //                                                         stock,
            //                                                         hpp                                    
            //                                                     ) values(		
            //                                                         '" . $id_product . "',
            //                                                         '" . $branch_in . "',
            //                                                         '" . $amount_origin . "',
            //                                                         '" . $hpp . "'
            //                                                     )");
            //         }
            //     }
            // }
            $valid = 1;
            $msg = "Add Transaction : Success !";
        } else {
            $valid = 0;
            $msg = "Add Transaction : Failed !";
        }
    }
    if ($id !== $_SESSION['id_user']) {
        // Edit Branch Process
        // $query_get_inv_out = mysqli_query($conn, "SELECT id_branch FROM inv_adjust_out WHERE id_inv_out = '" . $id . "' ");
        // $row_inv_branch = mysqli_fetch_array($query_get_inv_out);
        // $branch_from = $row_inv_branch['id_branch'];
        // if ($branch_out !== $branch_from) {
        //     $query_get_inv = mysqli_query($conn, "SELECT id_product,stock_out,biaya FROM inv_adjust_out  WHERE id_inv_out = '" . $id . "' ");
        //     while ($row_inv = mysqli_fetch_array($query_get_inv)) {
        //         $id_product = $row_inv['id_product'];
        //         $stock_inv = $row_inv['stock_out'];
        //         $hpp = (0 + $row_inv['biaya']) / (0 + $stock_inv);
        //         // From Branch
        //         $query_get_stock = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_from . "'  ");
        //         if ($row_stock = mysqli_fetch_array($query_get_stock)) {
        //             $stock_origin = $row_stock['stock'];

        //             $total_stock = $stock_origin - $stock_inv;
        //             mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_from . "' ");
        //             // To Branch
        //             $query_get_stock_in = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_product = '" . $id_product . "' and id_cabang = '" . $branch_out . "'  ");
        //             if ($row_stock_in = mysqli_fetch_array($query_get_stock_in)) {
        //                 $stock_origin_in = $row_stock_in['stock'];
        //                 $total_stock_in = $stock_origin_in + $stock_inv;
        //                 $hpp = (($row_stock['hpp'] * $stock_origin_in) + $row_inv['biaya']) / $total_stock_in;
        //                 mysqli_query($conn, "UPDATE tb_stock_cabang SET 
        //                 stock = '" . $total_stock_in . "',
        //                 hpp = '" . $hpp . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_out . "' ");
        //             } else {
        //                 mysqli_query($conn, "insert into tb_stock_cabang (id_bahan,
        //                                                                 id_cabang,
        //                                                                 stock,
        //                                                                 hpp                                    
        //                                                             ) values(		
        //                                                                 '" . $id_product . "',
        //                                                                 '" . $branch_out . "',
        //                                                                 '" . $total_stock_in . "',
        //                                                                 '" . $hpp . "'
        //                                                             )");
        //             }
        //         }
        //     }
        // }
        $query_set_inv_out = "  UPDATE inv_adjust_out 
                                    SET id_branch = '" . $branch_out . "',
                                        change_by = '" . $id_user . "',
                                        change_date = '" . $date . "'
                                    WHERE id_inv_out = '" . $id . "'";
        if (mysqli_query($conn, $query_set_inv_out)) {
            $valid = 1;
            // $query_get_inv_in = mysqli_query($conn, "SELECT id_branch FROM inv_adjust_in WHERE id_inv_in = '" . $id . "' ");
            // $row_inv_branch = mysqli_fetch_array($query_get_inv_in);
            // $branch_to = $row_inv_branch['id_branch'];
            // if ($branch_in !== $branch_to) {
            //     echo "<script type='text/javascript'>alert('Gerbang1')</script>";
            //     $query_get_inv = mysqli_query($conn, "SELECT id_product,stock_out,biaya FROM inv_adjust_out  WHERE id_inv_out = '" . $id . "' ");
            //     while ($row_inv = mysqli_fetch_array($query_get_inv)) {
            //         $id_product = $row_inv['id_product'];
            //         $stock_inv = $row_inv['stock_out'];
            //         $hpp = (0 + $row_inv['biaya']) / (0 + $stock_inv);
            //         // From Branch
            //         $query_get_stock = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_to . "'  ");
            //         if ($row_stock = mysqli_fetch_array($query_get_stock)) {
            //             $stock_origin = $row_stock['stock'];

            //             $total_stock = $stock_origin - $stock_inv;
            //             mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_to . "' ");
            //             // To Branch
            //             $query_get_stock_in = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_in . "'  ");

            //             if ($row_stock_in = mysqli_fetch_array($query_get_stock_in)) {
            //                 $stock_origin_in = $row_stock_in['stock'];

            //                 $total_stock_in = $stock_origin_in + $stock_inv;

            //                 $hpp = (($row_stock['hpp'] * $stock_origin_in) + $row_inv['biaya']) / $total_stock_in;
            //                 mysqli_query($conn, "UPDATE tb_stock_cabang SET 
            //                 stock = '" . $total_stock_in . "',hpp = '" . $hpp . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $branch_in . "' ");
            //             } else {
            //                 mysqli_query($conn, "insert into tb_stock_barang (id_bahan,
            //                                                                 id_cabang,
            //                                                                 stock,
            //                                                                 hpp                               
            //                                                             ) values(		
            //                                                                 '" . $id_product . "',
            //                                                                 '" . $branch_in . "',
            //                                                                 '" . $total_stock_in . "',
            //                                                                 '" . $hpp . "'
            //                                                             )");
            //             }
            //         }
            //     }
            // }
            $query_add_inv_in = mysqli_query($conn, "  UPDATE inv_adjust_in 
                                    SET id_branch = '" . $branch_in . "',
                                        create_by = '" . $id_user . "',
                                        create_date = '" . $date . "'
                                    WHERE id_inv_in = '" . $id . "'");
            $msg = "Edit Transaction : Success !";
        } else {
            $valid = 0;
            $msg = "Edit Transaction : Failed !";
        }
        // End Edit Branch Process

    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $page = "inventory_mutation_view.php";
        $sec = "0.5";
        $redirect = 1;
        header("Refresh: $sec; url=$page");
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}

if ($id_inv_filter == "new") {
    $id_inv_filter = $_SESSION['id_user'];
}
$transaction_date = $date = date("Y-m-d");
$id = "New";
$date = "";

if (isset($_GET['branch'])) {
    $get_branch = mysqli_real_escape_string($conn, $_GET['branch']);
}

$readonly = "";
$hidden = "";
$hide = "style='display:none;'";
if (isset($_GET['view'])) {
    $readonly = "readonly";
    $hidden = "hidden";
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
    <title>Add/Edit Stock Mutation</title>
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
            <!-- <div class="preloader-it">
                <div class="loader-pendulums"></div>
            </div> -->
            <!-- Container -->
            <div class="container-fluid mt-15">
                <!-- Row -->
                <div class="row">
                    <div class="col">
                        <section class="hk-sec-wrapper">
                            <h5 class="hk-sec-title">Stock Mutation</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <form action="inventory_mutation.php?id_inv_out=<?php echo $id_inv_filter; ?>" method="POST">
                                        <?php
                                        $query_get_inv_in = mysqli_query($conn, "SELECT id_inv_out,
                                                                                inv_date 
                                                                            FROM inv_adjust_out
                                                                            WHERE id_inv_out = '" . $id_inv_filter . "' ");
                                        if ($row_inv_in = mysqli_fetch_array($query_get_inv_in)) {
                                            $id = $row_inv_in['id_inv_out'];
                                            $transaction_date = $row_inv_in['inv_date'];
                                        }

                                        if ($id == $_SESSION['id_user']) {
                                            $id = "New";
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
                                                                id="inputGroup-sizing-sm"> ID Mutation </span>
                                                        </div>
                                                        <input autocomplete="off" type="text"  name="id_inv"
                                                            id="id_inv"
                                                            class="form-control filled-input form-control-sm id_inv"
                                                            value="' . $id . '" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Mutation Date</span>
                                                        </div>
                                                        <input autocomplete="off" type="date" name="date_in"
                                                            id="date_in"
                                                            class="form-control filled-input form-control-sm date_in"  value="' . $transaction_date . '"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center" ' . $hidden . '>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Product Name </span>
                                                        </div>
                                                        <input autocomplete="off" type="text" list="product_list"
                                                            id="product_nm"
                                                            class="form-control form-control-sm product_nm " autocomplete="off" onclick="this.select();">
                                                        <datalist id="product_list" class="product_list">
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-amount product">
                                                    <span style="width:150px; min-width:150px;"
                                                        class="form-control filled-input form-control-sm input-group-text "
                                                        id="inputGroup-sizing-sm"> Sisa Stock </span>
                                                    <div class="input-group input-group-sm">
                                                        <input autocomplete="off" type="number" min="0" id="stock_out"
                                                            class="form-control form-control-sm sisa_stock filled-input " autocomplete="off" onclick="this.select();" value="0" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-amount product">
                                                    <span style="width:150px; min-width:150px;"
                                                        class="form-control filled-input form-control-sm input-group-text "
                                                        id="inputGroup-sizing-sm"> Stock Amount (Roll) </span>
                                                    <div class="input-group input-group-sm">
                                                        <input autocomplete="off" type="number" min="0" id="stock_out"
                                                            class="form-control form-control-sm amount_roll filled-input " autocomplete="off" onclick="this.select();" readonly>
                                                        <div >
                                                            <div class="input-group input-group-sm">
                                                                <span class="form-control filled-input form-control-sm input-group-text"
                                                                    id="inputGroup-sizing-sm">Roll</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-amount product">
                                                    <span style="width:150px; min-width:150px;"
                                                        class="form-control filled-input form-control-sm input-group-text "
                                                        id="inputGroup-sizing-sm"> Stock Amount </span>
                                                    <div class="input-group input-group-sm">
                                                        <input autocomplete="off" type="number" min="0" id="stock_out"
                                                            class="form-control form-control-sm amount_product filled-input " autocomplete="off" onclick="this.select();" readonly>
                                                        <div class="uom">
                                                            <div class="input-group input-group-sm">
                                                                <span class="form-control filled-input form-control-sm input-group-text uom_product"
                                                                    id="inputGroup-sizing-sm">(UOM)</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-amount product">
                                                    <span style="width:150px; min-width:150px;"
                                                        class="form-control filled-input form-control-sm input-group-text "
                                                        id="inputGroup-sizing-sm"> Biaya </span>
                                                    <div class="input-group input-group-sm">
                                                        <input autocomplete="off" type="text" min="0" id="stock_out"
                                                            class="text-right form-control form-control-sm biaya filled-input " autocomplete="off" onclick="this.select();" value="0" readonly>
                                                        <input type="hidden" name="" class="biaya_hidden" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6"></div>
                                            <div class="col-md-6">
                                                <div class="form-group form-group-amount product">
                                                    <span style="width:150px; min-width:150px;"
                                                        class="form-control filled-input form-control-sm input-group-text "
                                                        id="inputGroup-sizing-sm"> HPP </span>
                                                    <div class="input-group input-group-sm">
                                                        <input autocomplete="off" type="text" min="0" id="stock_out"
                                                            class="text-right form-control form-control-sm hpp filled-input " autocomplete="off" onclick="this.select();" value="0" readonly>
                                                        <input type="hidden" name="" class="hpp_hidden" value="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-end" ' . $hidden . '>
                                            <div class="col-xl-6 col-sm-12 col-md-6 ">
                                                <div class="button-add">
                                                    <a role="button" class="btn btn-sm btn-block btn-secondary button-plus" data-id_inv="' . $id_inv_filter . '" data-id_branch="' . $id_branch . '"> 
                                                        <span class=""><i class="fa fa-plus text-white"></i></span>
                                                    <a> 
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="table-scroll mt-15">
                                        <table id="datable_1" class="table table-hover w-100 display mt-15">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Product Name</th>
                                                    <th>Biaya</th>
                                                    <th>Amount</th>
                                                    <th>Amount / Roll</th>
                                                    ';

                                        if (!isset($_GET['view'])) {
                                            echo '<th>Action</th>';
                                        }

                                        echo '
                                                </tr>
                                            </thead>
                                            <tbody class="view">
                                                ';
                                        $no = 0;
                                        $where_branch = "";
                                        if ($get_branch !== "pusat") {
                                            $where_branch = " AND i.id_branch = '" . $get_branch . "' ";
                                        }
                                        $query_get_product = mysqli_query($conn, "SELECT DISTINCT i.*, p.*
                                                                                    FROM inv_adjust_out i 
                                                                                    left join tb_bahan p on p.id_bahan = i.id_product
                                                                                    WHERE i.id_inv_out = '" . $id_inv_filter . "' 
                                                                                    " . $where_branch . "");
                                        while ($row_product = mysqli_fetch_array($query_get_product)) {
                                            $no++;
                                            $id_inv = $row_product['inv_out_id'];
                                            $id_product = $row_product['id_bahan'];
                                            $id_branch = $row_product['id_branch'];
                                            $nm_product = $row_product['nama_bahan'];
                                            $amount = $row_product['stock_out'];
                                            $uom_product = $row_product['uom'];
                                            $date_out = $row_product['inv_date'];
                                            $biaya = $row_product['biaya'];
                                            $query_get_inv_in = mysqli_query($conn, "SELECT * FROM inv_adjust_out WHERE id_inv_out = '" . $id_inv_filter . "' and inv_date = '" . $date_out . "' ");
                                            if ($row_inv_in = mysqli_fetch_array($query_get_inv_in)) {
                                                $id_inv_in = $row_inv_in['inv_out_id'];

                                                if (isset($_GET['view'])) {
                                                    echo '   
                                                        <tr>
                                                            <td style="font-size:11px;">' . $no . '</td>
                                                            <td style="font-size:11px;">' . $nm_product . ' </td>
                                                            <td style="font-size:11px;">Rp. ' . number_format($biaya) . ' </td>
                                                            <td style="font-size:11px;">
                                                                ' . $amount . ' ' . $uom_product . '
                                                            </td>
                                                            <td style="font-size:11px;">' . number_format((float)$amount / 2900, 2, '.', '') . '</td>
                                                            ';
                                                } else {
                                                    echo '   
                                                        <tr>
                                                            <td style="font-size:11px;">' . $no . '</td>
                                                            <td style="font-size:11px;">' . $nm_product . ' </td>
                                                            <td style="font-size:11px;">Rp. ' . number_format($biaya) . ' </td>
                                                            <td style="font-size:11px;">
                                                                <div class="input-group input-group-sm"><input type="number"
                                                                        class="form-control form-control-sm amount_' . $id_inv . '"
                                                                        name="nm_product_' . $id_inv . '"
                                                                        value="' . $amount . '" id="amount_' . $id_inv . '" ' . $readonly . ' autocomplete="off" onclick="this.select();">
                                                                    <span style="width:-2vw;"
                                                                        class="form-control filled-input form-control-sm input-group-text "
                                                                        id="inputGroup-sizing-sm"> ' . $uom_product . ' </span>
                                                                </div>
                                                            </td>
                                                            <td style="font-size:11px;">' . number_format((float)$amount / 2900, 2, '.', '') . '</td>
                                                            ';
                                                }

                                                if (!isset($_GET['view'])) {
                                                    echo '
                                                                <td>
                                                                <a role="button"
                                                                    class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list"
                                                                    data-id_inv="' . $id_inv . '" data-id_inv_in="' . $id_inv_in . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                                                                    <span class="btn-icon-wrap"><i
                                                                            class="fa fa-pencil"></i></span></a>
                                                                <a role="button"Name
                                                                    class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list"
                                                                    data-id_inv="' . $id_inv . '" data-id_inv_in="' . $id_inv_in . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                                                                    <span class="btn-icon-wrap"><i
                                                                            class="icon-trash"></i></span>
                                                                </a>
                                                            </td>
                                                                ';
                                                }

                                                echo '

                                                        </tr>
                                                        ';
                                            }
                                        }
                                        echo '
                                            </tbody>
                                        </table>
                                        </div>
                                        ';
                                        ?>

                                        <div class="form-group justify-content-end">
                                            <div class="row">
                                                <div class="col-sm-2 mt-15" <?= $hidden; ?>>
                                                    <a href="javascript:void(0)" class="btn btn-success btn-sm btn-block add_button" data-toggle="modal" data-target="#SaveOpanmeModal" data-id_opname="<?= $id_inv_filter ?>">Save</a>
                                                </div>
                                                <div class="col-sm-2 mt-15">
                                                    <a href="inventory_mutation_view.php" class="btn btn-sm btn-danger btn-block">Cancel</a>
                                                </div>
                                                <div class="col-sm-2 mt-15" <?php echo $hide; ?>>
                                                    <a href="print/print_invoice_transfer_stock.php?id_inv_out=<?php echo $_GET['id_inv_out']; ?>" class="btn btn-sm btn-success btn-block" target="blank_"><i class="fa fa-print"></i> Print</a>
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


    <!-- MODAL -->
    <div class="modal fade" id="SaveOpanmeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Save Mutation</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <?php
                $date = "";
                $description = "";
                $where_branch_save = "";
                if ($get_branch !== "pusat") {
                    $where_branch_save = " AND a.id_branch = '" . $get_branch . "' ";
                }
                $query_get_opname = mysqli_query($conn, "SELECT  b.nama_cabang,
                                                        a.id_branch 
                                                FROM inv_adjust_out a
                                                left join tb_cabang b on b.id_cabang = a.id_branch
                                                WHERE a.id_inv_out = '" . $id_inv_filter . "' 
                                                " . $where_branch_save . " ");
                if ($row_opname = mysqli_fetch_array($query_get_opname)) {
                    $branch = $row_opname['id_branch'];
                }
                echo '
                <form action="' . $_SERVER['PHP_SELF'] . '?id_inv_out=' . mysqli_real_escape_string($conn, $_GET['id_inv_out']) . '&branch=' . mysqli_real_escape_string($conn, $_GET['branch']) . '" method="POST">
                    <div class="modal-body">
                    <input type="hidden" class="form-control id" name="id">
                        <div class="form-group">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span style="width:150px;"
                                        class="form-control filled-input form-control-sm input-group-text "
                                        id="inputGroup-sizing-sm"> From Branch </span>
                                </div>
                                ';
                $query_get_nm_branch = mysqli_query($conn, "SELECT nama_cabang FROM tb_cabang WHERE id_cabang = '" . $get_branch . "' ");
                if ($row_branch_from = mysqli_fetch_array($query_get_nm_branch)) {
                    $branch_to = $row_branch_from['nama_cabang'];
                }
                echo '
                                <input autocomplete="off" type="text" id="banch_from" name="branch_from"                             
                                    class="form-control filled-input form-control-sm branch_from" value="' . $branch_to . '" readonly
                                     >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span style="width:150px;"
                                        class="form-control filled-input form-control-sm input-group-text "
                                        id="inputGroup-sizing-sm"> To Branch </span>
                                </div>
                                <select id="" class="form-control form-control-sm custom-select custom-select-sm branch_to" name="branch_to">
                                <option value="">-- Choose Branch --</option>
                                ';
                $query_get_to_branch = mysqli_query($conn, "SELECT  b.nama_cabang,
                                                                            a.id_branch 
                                                                    FROM inv_adjust_in a
                                                                    left join tb_cabang b on b.id_cabang = a.id_branch
                                                                    WHERE a.id_inv_in = '" . $id_inv_filter . "' ");
                if ($row_to_branch = mysqli_fetch_array($query_get_to_branch)) {
                    $branch = $row_to_branch['id_branch'];
                }
                $query_get_branch = mysqli_query($conn, "SELECT nama_cabang,id_cabang FROM tb_cabang WHERE NOT id_cabang = '1' and NOT id_cabang = '" . $get_branch . "' ");
                while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                    $id_branch = $row_branch['id_cabang'];
                    $nm_branch = $row_branch['nama_cabang'];
                    if ($branch == $id_branch) {
                        echo '
                                        <option value="' . $id_branch . '" selected>' . $nm_branch . '</option>
                                        ';
                    } else {
                        echo '
                                        <option value="' . $id_branch . '">' . $nm_branch . '</option>
                                        ';
                    }
                }
                echo '
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-success btn-sm" name="finish" value="Save">
                        <button class="btn btn-danger btn-sm" type="button" data-dismiss="modal">Cancel</button>                  
                    </div>
                </form>
                ';
                ?>
            </div>
        </div>
    </div>
    <!-- END MODAL -->

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
    <script src="vendors/jquery/dist/jquery.mask.min.js"></script>

    <!-- Delete -->
    <script type="text/javascript">
        $(".biaya").mask('#,##0', {
            reverse: true
        });

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

        $(document).on("keyup", ".biaya", function() {
            var subtotal = $(".biaya").val();
            subtotal = subtotal.split(',').join('');
            if (isNaN(subtotal)) subtotal = 0;

            $('.biaya_hidden').val(subtotal);
        });

        $(document).on("click", '.add_button', function(e) {
            var id_hapus = $(this).data('id_opname');
            $(".id").val(id_hapus);
        });

        $(document).on("keyup", ".amount_roll", function() {
            var roll = $(".amount_roll").val();
            var amount = roll * 2900;
            $(".amount_product").val(amount);

            var str = $(".product_nm").val();
            var split = str.split(" | ");
            var product = split[1];
            var amount = amount;
            var id = $(".id").val();
            var branch = $(".id_branch").val();
            var dataString = 'product_id=' + product +
                '&id_inv=' + id +
                '&branch=' + branch +
                '&amount_product=' + amount;

            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_mutation.php',
                data: dataString,
                success: function(response) {
                    $('.button-add').html(
                        response);

                }
            });

        });

        $(document).on("keyup", '.product_nm', function(e) {

            var product_list = $(this).val();
            var dataString = 'product_in=' + product_list;
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
                $('.biaya').val('0');
                $('.amount_product').val('');
                $('.amount_roll').val('');
            }

        });

        $(document).on("change", '.product_nm', function(e) {

            var str = $(this).val();
            var split = str.split(" | ");
            var product = split[1];
            var cabang = "<?php echo $_GET['branch']; ?>";
            var dataString = 'product=' + product + "&cabang=" + cabang;
            $(".preloader-it").show();
            $(".preloader-it").css("opacity", "0.5");
            if (str == "" || str.includes("|") == false) {
                $('.biaya').prop("readonly", true);
                $('.biaya').addClass("filled-input");
                $('.hpp').val("0");
                $('.hpp_hidden').val("0");
                $('.sisa_stock').val("0");
                $('.uom').html('<div class="input-group input-group-sm"><span class="form-control filled-input form-control-sm input-group-text uom_product" id="inputGroup-sizing-sm">(UOM)</span></div>');
                $('.amount_roll').prop("readonly", true);
                $('.amount_roll').addClass("filled-input");
                $('.amount_product').prop("readonly", true);
                $('.amount_product').addClass("filled-input");
                $('.button-plus').removeClass("btn-success");
                $('.button-plus').addClass("btn-secondary");
                $('.button-plus').removeClass("add_list");
            } else {
                $('.biaya').prop("readonly", false);
                $('.biaya').removeClass("filled-input");
                $('.amount_roll').prop("readonly", false);
                $('.amount_roll').removeClass("filled-input");
                $('.amount_product').prop("readonly", false);
                $('.amount_product').removeClass("filled-input");
                $.ajax({
                    type: 'POST',
                    url: 'ajax/ajax_product.php',
                    data: dataString,
                    success: function(response) {
                        var response = response.split("|");
                        $('.uom').html(response[0]);
                        $('.hpp').val(
                            koma(response[1])
                        );
                        $('.sisa_stock').val(response[2]);
                        $('.hpp_hidden').val(response[1]);
                    }
                });
            }

            $(".preloader-it").hide();

        });

        $(document).on("keyup", '.amount_product', function(e) {

            var str = $(".product_nm").val();
            var split = str.split(" | ");
            var product = split[1];
            var amount = $(this).val();
            var roll = Number(amount / 2900);
            var roll_fix = roll.toFixed(2);
            var roll_fixed = Number(roll_fix);

            var id = $(".id").val();
            var branch = $(".id_branch").val();
            var dataString = 'product_id=' + product +
                '&id_inv=' + id +
                '&branch=' + branch +
                '&amount_product=' + amount;
            $(".amount_roll").val(roll_fixed);
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_mutation.php',
                data: dataString,
                success: function(response) {
                    $('.button-add').html(
                        response);

                }
            });
        });

        $(document).on("change", '.type_product', function(e) {
            var type = $(this).val();

            if (type == "Product") {
                $(".product").show(500);
            } else {
                $(".product").hide(500);
            }
        });

        $(document).on("click", '.add_list', function(e) {

            var id_inv = $(this).data("id_inv");
            var amount = $('.amount_product').val();
            var date = $('.date_in').val();
            var str = $('.product_nm').val();
            var split = str.split(" | ");
            var id_product = split[1];
            var id_branch = $(this).data("id_branch");
            var biaya = $(".biaya_hidden").val();
            var hpp = $('.hpp_hidden').val();
            var dataString = 'id_product=' + id_product +
                '&add=' + id_inv +
                '&date=' + date +
                '&id_branch=' + id_branch +
                '&amount=' + amount +
                '&biaya=' + biaya +
                '&hpp=' + hpp;

            $('.button-plus').removeClass(".btn-success");
            $('.button-plus').html("<i class='fa fa-spinner fa-spin fa-fw'></i>");

            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_mutation.php',
                data: dataString,
                success: function(response) {
                    var response = response.split("|");
                    if (response[0] < 1) {
                        alert("Maaf, stock tidak mencukupi");
                    }
                    $('.view').html(response[1]);
                    $('.amount_roll').val("");
                    $('.biaya').val("0");
                    $('.biaya_hidden').val("0");
                    $('.amount_product').val("");
                    $('.product_nm').val("");
                    $('.uom_product').val("(UOM)");
                    $('.amount_product').prop("readonly", true);
                    $('.amount_product').addClass("filled-input");
                    $('.biaya').prop("readonly", true);
                    $('.biaya').addClass("filled-input");
                    $('.amount_roll').prop("readonly", true);
                    $('.amount_roll').addClass("filled-input");
                    $('.hpp').val("0");
                    $('.hpp_hidden').val("0");
                    $('.hpp').prop("readonly", true);
                    $('.hpp').addClass("filled-input");
                    $('.button-plus').removeClass("btn-success");
                    $('.button-plus').removeClass("add_list");
                    $('.button-plus').addClass("btn-secondary");
                    $('.button-plus').html("<i class='fa fa-plus'></i>");

                }
            });
        });

        $(document).on("click", '.set_list', function(e) {
            var id_product = $(this).data("id_product");
            var id_branch = $(this).data("id_branch");
            var id_inv = $(this).data("id_inv");
            var id_inv_in = $(this).data("id_inv_in");
            var amount = $(".amount_" + id_inv).val();
            var id_inv_filter = $(".id").val();
            var dataString = 'id_product=' + id_product +
                '&edit=' + id_inv +
                '&edit_in=' + id_inv_in +
                '&id_branch=' + id_branch +
                '&inv_filter=' + id_inv_filter +
                '&amount=' + amount;
            $(".preloader-it").show();
            $(".preloader-it").css("opacity", "0.5");
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_mutation.php',
                data: dataString,
                success: function(response) {
                    $(".preloader-it").hide();
                    alert("Change Amount Success");
                }
            });
        });

        $(document).on("click", '.del_list', function(e) {
            var id_product = $(this).data("id_product");
            var id_branch = $(this).data("id_branch");
            var id_inv = $(this).data("id_inv");
            var id_inv_in = $(this).data("id_inv_in");
            var id_inv_filter = $(".id").val();
            var amount = $(".amount_" + id_inv).val();

            var dataString = 'delete=' + id_inv +
                '&delete_in=' + id_inv_in +
                '&id_branch=' + id_branch +
                '&amount=' + amount +
                '&inv_filter=' + id_inv_filter +
                '&id_product=' + id_product;

            $(".preloader-it").show();
            $(".preloader-it").css("opacity", "0.5");

            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_mutation.php',
                data: dataString,
                success: function(response) {
                    $('.view').html(response);
                    $('.preloader-it').hide();
                }
            });
        });



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


<!-- <div class="form-group" '.$hidden.'>
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span style="width:150px;"
                                        class="form-control filled-input form-control-sm input-group-text "
                                        id="inputGroup-sizing-sm"> From Branch </span>
                                </div>
                                <select id="" class="form-control form-control-sm custom-select custom-select-sm from_branch" name="branch_from">
                                <option value="">-- Choose Branch --</option>
                                ';
                                $query_get_branch_from = mysqli_query($conn,"SELECT m_branch_id,nm_branch FROM m_branch WHERE NOT m_branch_id = 'pusat' ");
                                while($row_branch_from = mysqli_fetch_array($query_get_branch_from)){
                                    $id_branch = $row_branch_from['m_branch_id'];
                                    $nm_branch = $row_branch_from['nm_branch'];
                                    if($branch == $id_branch){
                                        echo'
                                        <option value="'.$id_branch.'" selected>'.$nm_branch.'</option>
                                        ';
                                    }else{
                                        echo'
                                        <option value="'.$id_branch.'">'.$nm_branch.'</option>
                                        ';
                                    }
                                }
                                echo'
                                </select>
                            </div>
                        </div> -->

</html>