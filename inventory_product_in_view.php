<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['branch'])) {
    header('Location:index.php');
}

$id_user = mysqli_real_escape_string($conn, $_SESSION['id_user']);
// $branch = mysqli_real_escape_string($conn,$_SESSION['branch']);
$date_now = date("Y-m-d");

// Delete Flexas
if (isset($_POST['delete'])) {
    $valid = 1;
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    if ($status == "Approved") {
        $query_get_inv = mysqli_query($conn, "SELECT id_product,stock_in,id_branch FROM inv_product_in WHERE id_inv_in = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' ");
        while ($row_inv = mysqli_fetch_array($query_get_inv)) {
            $id_product = $row_inv['id_product'];
            $amount = $row_inv['stock_in'];
            $branch = $row_inv['id_branch'];
            $query_get_product = mysqli_query($conn, "SELECT stock FROM m_branch_stock WHERE id_product = '" . $id_product . "' and id_branch = '" . $branch . "'  ");
            if ($row_stock = mysqli_fetch_array($query_get_product)) {
                $stock = intval($row_stock['stock']);

                $total_stock = $stock - $amount;
                mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_stock . "' WHERE id_product = '" . $id_product . "' and id_branch = '" . $branch . "' ");
            } else {
                $valid = 0;
                $msg = "Delete Transaction : Failed to update stock on branch !";
            }
        }
    }

    if ($valid == 1) {
        $query_del = mysqli_query($conn, "DELETE FROM inv_product_in WHERE id_inv_in ='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' ");
        if (!$query_del) {
            $valid = 0;
            $msg = "Delete Transaction : Failed !";
        }
    }

    if ($valid == 0) {
        //   rollback();
    } else {
        //   commit();
        $msg = "Delete Transaction : Success !";
        $page = "inventory_product_in_view.php";
        $sec = "0.5";
        $redirect = 1;
        header("Refresh: $sec; url=$page");
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}

$get_branch = $_SESSION['branch'];

if (isset($_POST['cari_tgl'])) {
    $branch_id = mysqli_real_escape_string($conn, $_POST['branch']);
    $date_now = mysqli_real_escape_string($conn, $_POST['tgl_inv']);
}


$add_new = 'href="inventory_product_in.php?branch=' . $get_branch . '&id_inv_in=new&status=0" ';

if ($_SESSION['group'] == "Franchise" || $_SESSION['branch'] == "pusat") {
    $add_new = 'href="javascript: void(0)" data-toggle="modal" data-target="#ChooseModal"';
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
    <title>Reydecal Admin</title>
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
            <!-- Container -->
            <div class="container-fluid  mt-15">
                <!-- Row -->
                <div class="row">
                    <div class="col">
                        <section class="hk-sec-wrapper">

                            <div class="row">
                                <div class="col-sm-12 col-md-3">
                                    <h5 class="hk-sec-title">Inventory Stock In

                                    </h5>
                                    <a <?= $add_new; ?> class="add_button btn btn-success btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span class="btn-text">Add New</span><span class="icon-label"><i class="fa fa-plus"></i> </span></a>
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
                            if ($get_branch == "pusat" || $_SESSION['group'] == "Franchise") {
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
                                    <div class="col-md-5 mt-15">
                                        <div class="row no-gutters">
                                            <div class="col-12 col-md-3 col-xl-2">
                                                <span style="" class="form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Branch</span>
                                            </div>
                                            <div class="col-12 col-md-9 col-xl-10">
                                                <select id="" class="form-control form-control-sm custom-select custom-select-sm id_cabang ' . $class . '" name="branch" ' . $readonly . '>
                                                ';
                            if ($_SESSION['group'] == "Franchise") {
                                $query_get_id_branch = mysqli_query($conn, "SELECT id_branch FROM m_user where id_user = '" . $id_user . "' ");
                                $row_id_branch = mysqli_fetch_array($query_get_id_branch);
                                $array_id = $row_id_branch['id_branch'];
                                $array_id = explode("#", $array_id);
                                foreach ($array_id as $branch_user) {
                                    $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang = '" . $branch_user . "' WHERE stat != 'no'");
                                    // echo $query_get_branch;
                                    if ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                        $id_branch = $row_branch['id_cabang'];
                                        $nama_cabang = $row_branch['nama_cabang'];

                                        // Untuk Franchise
                                        if ($id_branch == $branch_id) {
                                            echo '
                                                                    <option value="' . $id_branch . '" selected >' . $nama_cabang . '</option>
                                                                ';
                                        } else {
                                            echo '
                                                                    <option value="' . $id_branch . '" >' . $nama_cabang . '</option>
                                                                ';
                                        }
                                        // END
                                    }
                                }
                                $where_branch = " AND i.id_branch = '" . $array_id[0] . "' AND i.inv_date = '" . $date_now . "' ";
                            }
                            // Untuk Pusat dan superadmin
                            else if ($_SESSION['branch'] == "pusat") {
                                $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE stat != 'no'");
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
                                $where_branch = " AND i.id_branch = '1' AND i.inv_date = '" . $date_now . "' ";
                            } else {
                                $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang = '" . $get_branch . "'");
                                // echo $query_get_branch;
                                if ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                    $id_branch = $row_branch['id_cabang'];
                                    $nama_cabang = $row_branch['nama_cabang'];
                                    echo '
                                                            <option value="' . $id_branch . '" selected >' . $nama_cabang . '</option>
                                                        ';
                                }
                                $where_branch = " AND i.id_branch = '" . $get_branch . "' AND i.inv_date = '" . $date_now . "' ";
                            }
                            echo '
                                                </select>  
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 mt-15">
                                        <div class="row no-gutters">
                                            <div class="col-12 col-md-3 col-xl-2">
                                                <span style="" class=" form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Date</span>
                                            </div>
                                            <div class="col-12 col-md-9 col-xl-10">
                                                <input type="text" class="form-control single_date form-control-sm" value="' . $date_now . '" name="tgl_inv" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-15">
                                        <input type="submit" name="cari_tgl" class="btn btn-info btn-xs btn-block" value=" Search ">
                                    </div>
                                </div>

                            </form>
                            ';
                            ?>
                            <br>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="table-wrap table-scroll w-100">
                                        <table id="datable_2" class="table table-hover  w-100 display">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Transaction ID</th>
                                                    <th>Transaction Date</th>
                                                    <th>Product</th>
                                                    <th>Branch</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                // Panggil m.product
                                                // $where_branch = "";
                                                // if($get_branch !== "pusat"){
                                                //     $where_branch = " AND i.id_branch = '".$get_branch."' ";
                                                // }

                                                if (isset($_POST['cari_tgl'])) {
                                                    $branch_id = mysqli_real_escape_string($conn, $_POST['branch']);
                                                    $tanggal_inv = mysqli_real_escape_string($conn, $_POST['tgl_inv']);
                                                    $where_branch = " AND i.id_branch = '" . $branch_id . "' AND i.inv_date = '" . $tanggal_inv . "' ";
                                                }
                                                $query_get = mysqli_query($conn, "SELECT distinct i.id_inv_in,
                                                                                    i.inv_date,
                                                                                    i.status,
                                                                                    b.nama_cabang,
                                                                                    i.id_branch
                                                                                FROM inv_product_in i
                                                                                LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
                                                                                where i.status <> ''
                                                                                " . $where_branch . "
                                                                                ORDER BY i.inv_in_id desc");
                                                while ($row_get = mysqli_fetch_array($query_get)) {
                                                    $style = "";
                                                    $branch_id = $row_get['id_branch'];
                                                    $branch = $row_get['nama_cabang'];
                                                    if ($branch == "" && $get_branch == "pusat") {
                                                        $branch = "Pusat";
                                                    }

                                                    echo '
                                                    <tr ' . $style . '>
                                                ';

                                                    echo '
                                                    <td style="font-size:11px;">' . $no . '</td>    
                                                    <td style="font-size:11px;">' . $row_get['id_inv_in'] . '</td>
                                                    <td style="font-size:11px;">' . date("d/m/Y", strtotime($row_get['inv_date'])) . '</td>
                                                    <td style="font-size:11px;">
                                                    ';
                                                    $query_get_product = mysqli_query($conn, "SELECT p.nm_product,p.uom_product,i.stock_in
                                                                            FROM m_product p
                                                                            JOIN inv_product_in i ON i.id_product = p.m_product_id
                                                                            where i.id_inv_in = '" . $row_get['id_inv_in'] . "'
                                                                            ORDER BY p.nm_product asc");
                                                    $baris_product = mysqli_num_rows($query_get_product);
                                                    if ($row_product = mysqli_fetch_array($query_get_product)) {
                                                        if ($baris_product == "1") {
                                                            echo '
                                                                <span>' . $row_product['nm_product'] . ' = ' . $row_product['stock_in'] . ' ' . $row_product['uom_product'] . '</span> <br>
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
                                                    <td>
                                                    ';
                                                    $color = "btn-success";
                                                    $disabled = "inventory_product_in.php?branch=" . $branch_id . "&id_inv_in=" . $row_get['id_inv_in'] . "&status=1";
                                                    $hide = "";
                                                    $status = "0";
                                                    $hidden = "";
                                                    if ($row_get['status'] == "Approved") {
                                                        $disabled = "";
                                                        $color = "btn-secondary";
                                                        $hide = 'style="display:none;"';
                                                        $status = "2";
                                                        $hidden = "hidden";
                                                        // if( $_SESSION['group'] == "Supervisor" || $_SESSION['group'] == "Franchise" || $_SESSION['branch'] == "pusat" ){
                                                        //     $hidden = "";
                                                        // }
                                                    }
                                                    echo '
                                                        
                                                        <a href="inventory_product_in.php?branch=' . $branch_id . '&id_inv_in=' . $row_get['id_inv_in'] . '&view=detail&status=' . $status . '" class="mr-2 btn btn-xs btn-icon btn-info btn-icon-style-1" >
                                                            <span class="btn-icon-wrap"><i class="fa fa-eye"></i></span>
                                                        </a>
                                                        <a href="inventory_product_in.php?branch=' . $branch_id . '&id_inv_in=' . $row_get['id_inv_in'] . '&status=' . $status . '" ' . $hidden . ' class="mr-2 btn btn-xs btn-icon btn-warning btn-icon-style-1" >
                                                            <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span>
                                                        </a>
                                                        <a href="javascript:void(0)"  class="mr-2 btn btn-xs btn-icon btn-danger btn-icon-style-1 hapus_button" data-toggle="modal" data-target="#DeleteProductModal"
                                                            data-id_hapus="' . $row_get['id_inv_in'] . '" data-status="' . $row_get['status'] . '">
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

                                $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang WHERE stat != 'no'");
                                // echo $query_get_branch;
                                while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                    $id_branch = $row_branch['id_cabang'];
                                    $nama_cabang = $row_branch['nama_cabang'];
                                    echo '
                                            <option value="' . $id_branch . '" >' . $nama_cabang . '</option>
                                        ';
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

    <!-- Delete -->
    <script type="text/javascript">
        $(document).on("click", '.hapus_button', function(e) {
            var id_hapus = $(this).data('id_hapus');
            var status = $(this).data('status');
            $(".id_hapus").val(id_hapus);
            $(".status").val(status);
        });

        $(document).on("click", '.choose_branch', function(e) {
            var id_branch = $('.branch_modal').val();
            window.location.replace("inventory_product_in.php?branch=" + id_branch + "&id_inv_in=new&status=0");
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

<!-- // <a href="inventory_product_in.php?id_inv_in='.$row_get['id_inv_in'].'&status=0" class="btn btn-xs btn-icon btn-warning btn-icon-style-1" >
                                                    //         <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span>
                                                    //     // </a> -->