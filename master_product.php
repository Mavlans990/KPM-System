<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['branch'])) {
    header('Location:index.php');
}



if (isset($_POST['save'])) {
    $valid = 1;
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
    $nm_product = mysqli_real_escape_string($conn, $_POST['nm_product']);
    $pass_product = mysqli_real_escape_string($conn, $_POST['padd_product']);

    if ($id == "new") {

        $query_add_product = "INSERT INTO m_product ( id_product,
                                                        nm_product,
                                                        pass_product,
                                                        create_by,
                                                        create_date
                                                        ) VALUES (
                                                        '" . $id_product . "',
                                                        '" . $nm_product . "',
                                                        '" . md5($pass_product) . "',
                                                        )";
        if (mysqli_query($conn, $query_add_product)) {
            $valid = 1;
            $msg = "Add Data product : Success !";
        } else {
            $valid = 0;
            $msg = "Add Data product : Failed !";
        }
    } else {
        // Edit Branch Process
        $query_set_product = "  UPDATE m_product 
                                    SET nm_product = '" . $nm_product . "',
                                        pass_product = '" . $pass_product . "',
                                        id_branch = '" . $id_branch . "'
                                    WHERE id_product = '" . $id . "'";
        if (mysqli_query($conn, $query_set_product)) {
            $valid = 1;
            $msg = "Edit Data product : Success !";
        } else {
            $valid = 0;
            $msg = "Edit Data product : Failed !";
        }
        // End Edit Branch Process
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}

// Delete Flexas
if (isset($_POST['delete'])) {
    $valid = 1;

    if ($valid == 1) {
        $query_del = mysqli_query($conn, "DELETE FROM m_product WHERE m_product_id='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "' ");

        if (!$query_del) {
            $valid = 0;
            $msg = "Delete Data product : Failed !";
        }
    }

    if ($valid == 0) {
    } else {
        mysqli_query($conn, "DELETE FROM m_ingredients WHERE id_product = '" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");


        $msg = "Delete Data product : Success !";
        $page = "master_product.php";
        $sec = "0.5";
        $redirect = 1;
        header("Refresh: $sec; url=$page");
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}

// $get_branch = $_SESSION['branch'];
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
    <title>Master SKU</title>
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
                                    <h5 class="hk-sec-title">Master Data SKU
                                        <a href="master_product_edit.php?id_product=new" class="add_button btn btn-success btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span class="btn-text">Add New SKU</span><span class="icon-label"><i class="fa fa-angle-right"></i> </span></a>
                                    </h5>
                                </div>
                                <!-- <div class="col">
                                    <h5 class="hk-sec-title">
                                        <a href="print/master_product_login_print.php"
                                            class="btn btn-info btn-sm btn-wth-icon icon-wthot-bg btn-rounded icon-right"><span
                                                class="btn-text">Download Excel</span></a>
                                    </h5>
                                </div> -->
                            </div>

                            <br>
                            <div class="row">
                                <div class="col-sm">
                                    <div class="table-wrap table-scroll">
                                        <table id="datable_2" class="table table-hover w-100 display">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>ID SKU</th>
                                                    <th>Product Name</th>
                                                    <th>Type of Product</th>
                                                    <th>UOM</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                // Panggil m.product
                                                // $where_branch = "";
                                                // if($get_branch !== "pusat"){
                                                //     $where_branch = " WHERE id_branch = '".$get_branch."' ";
                                                // }
                                                $query_get = mysqli_query($conn, "SELECT p.nm_product, 
                                                                                    p.m_product_id,
                                                                                    p.type_product,
                                                                                    p.uom_product,
                                                                                    p.stock_product,
                                                                                    p.id_branch,
                                                                                    p.price
                                                                                FROM m_product p 
                                                                                LEFT JOIN tb_cabang b on b.id_cabang = p.id_branch 
                                                                                
                                                                                ORDER BY p.m_product_id asc");
                                                while ($row_get = mysqli_fetch_array($query_get)) {
                                                    $style = "";

                                                    echo '
                                                    <tr ' . $style . '>
                                                ';
                                                    if ($row_get['type_product'] == "Ingredients") {
                                                        $type = "Bahan Sticker";
                                                    } else {
                                                        $type = "Product Decal / Cutting";
                                                    }
                                                    echo '
                                                    <td style="font-size:11px;">' . $no . '</td>    
                                                    <td style="font-size:11px;">' . $row_get['m_product_id'] . '</td>
                                                    <td style="font-size:11px;">' . $row_get['nm_product'] . '</td>
                                                    <td style="font-size:11px;">' . $type . '</td>
                                                    <td style="font-size:11px;">' . $row_get['uom_product'] . '</td>
                                                    <td>
                                                        <a href="master_product_edit.php?id_product=' . $row_get['m_product_id'] . '" class="btn btn-xs btn-icon btn-info btn-icon-style-1" >
                                                            <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span>
                                                        </a>
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 hapus_button" data-toggle="modal" data-target="#DeleteProductModal"
                                                            data-id_hapus="' . $row_get['m_product_id'] . '">
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
                    <h5 class="modal-title" id="exampleModalLabel">Delete product</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Akan menghapus data product, Yakin?
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

    <!-- Delete -->
    <script type="text/javascript">
        $(document).on("click", '.hapus_button', function(e) {
            var id_hapus = $(this).data('id_hapus');
            $(".id_hapus").val(id_hapus);
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