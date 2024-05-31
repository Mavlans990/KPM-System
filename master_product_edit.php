<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['branch'])) {
    header('Location:index.php');
}

$id_product = mysqli_real_escape_string($conn, $_GET['id_product']);
$id_product_filter = mysqli_real_escape_string($conn, $_GET['id_product']);

if ($id_product_filter !== "new") {
    $select_product = "SELECT * FROM m_product WHERE m_product_id = '" . $id_product_filter . "'";
    $query_product = mysqli_query($conn, $select_product);
    $data_product = mysqli_fetch_array($query_product);

    $type = $data_product['type_product'];
} else {
    $type = "";
}

$id_user = $_SESSION['id_user'];
$date = date("Y-m-d H:i:s");

if (isset($_POST['save'])) {
    $valid = 1;
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
    $nm_product = mysqli_real_escape_string($conn, $_POST['nm_product']);
    $type_product = mysqli_real_escape_string($conn, $_POST['type_product']);
    $uom_product = mysqli_real_escape_string($conn, $_POST['uom_product']);
    // if ($type_product == "Ingredients") {
    //     $price = 0;
    // } else {
    // $price = mysqli_real_escape_string($conn, $_POST['price']);
    // }

    // $file_name = $_FILES["image_file"]["name"];
    // $file_type = $_FILES["image_file"]["type"];
    // $temp_name = $_FILES["image_file"]["tmp_name"];
    // $file_size = $_FILES["image_file"]["size"];
    // $error = $_FILES["image_file"]["error"];
    // if (!$temp_name)
    // {
    //     echo "ERROR: Please browse for file before uploading";
    //     exit();
    // }
    // function compress_image($source_url, $destination_url, $quality)
    // {
    //     $info = getimagesize($source_url);
    //     if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
    //     elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
    //     imagejpeg($image, $destination_url, $quality);
    //     echo "Image uploaded successfully.";
    // }
    // if ($error > 0)
    // {
    //     echo $error;
    // }
    // else if (($file_type == "image/gif") || ($file_type == "image/jpeg") || ($file_type == "image/png") || ($file_type == "image/pjpeg"))
    // {
    //     $filename = compress_image($temp_name, "img/product/" . $file_name, 80);
    // }
    // else
    // {
    //     echo "Uploaded image should be jpg or png.";
    // }


    if ($id == "new" && $valid == 1) {
        $query = mysqli_query($conn, "SELECT max(m_product_id) as kodeTerbesar FROM m_product");
        if ($data = mysqli_fetch_array($query)) {
            $kode = $data['kodeTerbesar'];
            $urutan = (int) substr($kode, 2, 6);
            $urutan++;
            $huruf = "P-";
            $id_product = $huruf . sprintf("%06s", $urutan);
        } else {
            $id_product = "P-000001";
        }
        $query_add_product = "INSERT INTO m_product ( m_product_id,
                                                        nm_product,
                                                        type_product,
                                                        uom_product,
                                                        price,
                                                        create_by,
                                                        create_date
                                                        ) VALUES (
                                                        '" . $id_product . "',
                                                        '" . $nm_product . "',
                                                        '" . $type_product . "',
                                                        '" . $uom_product . "',
                                                        '0',
                                                        '" . $id_user . "',
                                                        '" . $date . "'
                                                        )";
        if (mysqli_query($conn, $query_add_product)) {
            // if ($type_product == "Product") {
            //     $query_set_ingredients = mysqli_query($conn, "UPDATE m_ingredients SET id_product = '" . $id_product . "' WHERE id_product = '" . $id_product_filter . "' ");
            // }

            $valid = 1;
            $msg = "Add Data product : Success !";
        } else {
            $valid = 0;
            $msg = "Add Data product : Failed !";
        }
    }

    if ($id !== "new" && $valid == 1) {
        // Edit Branch Process
        $query_set_product = "  UPDATE m_product 
                                    SET nm_product = '" . $nm_product . "',
                                        type_product = '" . $type_product . "',
                                        uom_product = '" . $uom_product . "',
                                        change_by = '" . $id_user . "',
                                        change_date = '" . $date . "'
                                    WHERE m_product_id = '" . $id . "'";
        if (mysqli_query($conn, $query_set_product)) {
            $valid = 1;
            $msg = "Edit Data product : Success !";
        } else {
            $valid = 0;
            $msg = "Edit Data product : Failed !";
        }
        // End Edit Branch Process


    }
    if ($valid == 0) {
        // rollback();
    } else {
        // commit();
        $page = "master_product.php";
        $sec = "0";
        $redirect = 1;
        header("Refresh: $sec; url=$page");
    }

    echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}


$id = "New";
$name = "";
$type = "";
$uom = "";
$price = 0;
$stock = "";
$image = "";

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
    <title>Add/Edit SKU</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- select2 CSS -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

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
            <div class="container-fluid mt-15">
                <!-- Row -->
                <div class="row">
                    <div class="col">

                        <section class="hk-sec-wrapper">
                            <h5 class="hk-sec-title">Add/Edit Data SKU</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <form action="master_product_edit.php?id_product=<?php echo $id_product_filter; ?>" method="POST">
                                        <div class="row justify-content-center">
                                            <div class="col-sm-11">
                                                <?php
                                                $query_get_product = mysqli_query($conn, "SELECT * FROM m_product WHERE m_product_id = '" . $id_product_filter . "'");
                                                if ($row_product = mysqli_fetch_array($query_get_product)) {
                                                    $id = $row_product['m_product_id'];
                                                    $name = $row_product['nm_product'];
                                                    $type = $row_product['type_product'];
                                                    $uom = $row_product['uom_product'];
                                                    $price = $row_product['price'];
                                                    $stock = $row_product['stock_product'];
                                                    $image = $row_product['img_product'];
                                                }
                                                // $hide_table = 'style="display:none;"';
                                                // if ($type == "Product") {
                                                $hide_table = "";
                                                // }
                                                echo '
                                                
                                                <input type="hidden" class="form-control id" name="id" value="' . $id_product_filter . '">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Product ID</span>
                                                        </div>
                                                            <input autocomplete="off" type="text" name="id_product"
                                                                id="nm_product"
                                                                class="form-control filled-input form-control-sm nm_product" value="' . $id . '" readonly>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Product Name</span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="nm_product"
                                                            id="nm_product"
                                                            class="form-control form-control-sm nm_product" value="' . $name . '" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Type of Product </span>
                                                        </div>
                                                        <select name="type_product" id="type_product"
                                                            class="custom-select custom-select-sm form-control-sm type_product"
                                                            required>
                                                            <option value="">-- Choose Type --</option>
                                                            <option value="Product" ';
                                                if ($type == "Product") {
                                                    echo 'selected';
                                                }
                                                echo '>Product Decal/Cutting</option>
                                                            <option value="Ingredients" ';
                                                if ($type == "Ingredients") {
                                                    echo 'selected';
                                                }
                                                echo '>Bahan Stiker</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- <div class="form-group mt-15">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Image </span>
                                                        </div>
                                                        <input type="file" class="custom-file-input" accept="image/*" value="' . $image . '" name="image_file"
                                                             id="customFile">
                                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                                    </div>
                                                </div> -->
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">  UOM </span>
                                                        </div>
                                                        <select name="uom_product" id="uom_product"
                                                            class="custom-select custom-select-sm form-control-sm uom_product"
                                                            required>
                                                            <option value="">-- Choose UOM --</option>
                                                            <option value="G" ';
                                                if ($uom == "G") {
                                                    echo 'selected';
                                                }
                                                echo '>Gram (G)</option>
                                                            <option value="L" ';
                                                if ($uom == "L") {
                                                    echo 'selected';
                                                }
                                                echo '>Liter (L)</option>
                                                            <option value="Ml" ';
                                                if ($uom == "Ml") {
                                                    echo 'selected';
                                                }
                                                echo '>Mililiter (Ml)</option>    
                                                            <option value="M" ';
                                                if ($uom == "M") {
                                                    echo 'selected';
                                                }
                                                echo '>Meter (M)</option>   
                                                            <option value="CM" ';
                                                if ($uom == "Cm") {
                                                    echo 'selected';
                                                }
                                                echo '>Centimeter (Cm)</option>   
                                                            <option value="Pack" ';
                                                if ($uom == "Pack") {
                                                    echo 'selected';
                                                }
                                                echo '>Pack</option>
                                                            <option value="Box" ';
                                                if ($uom == "Box") {
                                                    echo 'selected';
                                                }
                                                echo '>Box</option>  
                                                            <option value="Pieces" ';
                                                if ($uom == "Pieces") {
                                                    echo 'selected';
                                                }
                                                echo '>Pieces</option>                                                         
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                ';
                                                echo '
                                                </div>
                                                <div class="form-group justify-content-end">    
                                            <button class="btn btn-sm btn-success" name="save" value="Save"
                                                type="submit">Save</button>
                                            <a href="master_product.php" class="btn btn-sm btn-red">Cancel</a>
                                            </div>
                                            ';
                                                ?>
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


    <!-- jQuery -->
    <script src="vendors/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="dist/js/jquery.slimscroll.js"></script>

    <!-- Jasny-bootstrap  JavaScript -->
    <script src="vendors/jasny-bootstrap/dist/js/jasny-bootstrap.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="dist/js/select2-data.js"></script>

    <!-- Pickr JavaScript -->
    <script src="vendors/pickr-widget/dist/pickr.min.js"></script>
    <script src="dist/js/pickr-data.js"></script>

    <!-- Bootstrap Tagsinput JavaScript -->
    <script src="vendors/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

    <!-- Bootstrap Input spinner JavaScript -->
    <script src="vendors/bootstrap-input-spinner/src/bootstrap-input-spinner.js"></script>
    <script src="dist/js/inputspinner-data.js"></script>

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

    <!-- Bootstrap Tagsinput JavaScript -->
    <script src="vendors/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js"></script>

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>

    <!-- Delete -->
    <script type="text/javascript">
        $(document).ready(function() {
            var type = "<?php echo $type; ?>";
            if (type == "Product") {
                if (type == "Product") {
                    $(".price_input").show(500);
                } else {
                    $(".price_input").hide(500);
                }
            }

            $(document).on("keyup", '.ingredients_nm', function(e) {

                var product_list = $(this).val();
                var dataString = 'product_list=' + product_list;
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

            $(document).on("change", '.ingredients_nm', function(e) {

                var str = $(this).val();
                var split = str.split(" | ");
                var product = split[1];
                var dataString = 'product=' + product;
                $.ajax({
                    type: 'POST',
                    url: 'ajax/ajax_product.php',
                    data: dataString,
                    success: function(response) {
                        $('.uom').html(
                            response);

                    }
                });

            });

            // $(document).on("change", '.type_product', function(e) {
            //     var type = $(this).val();

            //     if (type == "Product") {
            //         $(".price_input").show(500);
            //     } else {
            //         $(".price_input").hide(500);
            //     }
            // });

            $(document).on("click", '.add_list', function(e) {

                var id_product = $(this).data("id_product");
                var amount = $('.amount_product').val();
                var str = $('.ingredients_nm').val();
                var split = str.split(" | ");
                var id_ingredients = split[1];
                var dataString = 'id_product=' + id_product +
                    '&add=' + id_ingredients +
                    '&amount=' + amount;
                $.ajax({
                    type: 'POST',
                    url: 'ajax/ajax_product.php',
                    data: dataString,
                    success: function(response) {

                        $('.view').html(
                            response);
                        $('.amount_product').val("");
                        $('.ingredients_nm').val("");
                    }
                });
            });

            $(document).on("click", '.set_list', function(e) {

                var id_product = $(this).data("id_product");
                var id_ingredients = $(this).data("id_ingredients");
                var amount = $(".amount_" + id_ingredients).val();
                var dataString = 'id_product=' + id_product +
                    '&edit=' + id_ingredients +
                    '&amount=' + amount;

                $.ajax({
                    type: 'POST',
                    url: 'ajax/ajax_product.php',
                    data: dataString,
                    success: function(response) {
                        alert("Change Amount Success");
                    }
                });
            });

            $(document).on("click", '.del_list', function(e) {

                var id_product = $(this).data("id_product");
                var id_ingredients = $(this).data("id_ingredients");
                var amount = $(".amount_" + id_ingredients).val();
                var dataString = 'id_product=' + id_product +
                    '&delete=' + id_ingredients;
                $.ajax({
                    type: 'POST',
                    url: 'ajax/ajax_product.php',
                    data: dataString,
                    success: function(response) {
                        $('.view').html(
                            response);

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
        });
    </script>


</body>

</html>