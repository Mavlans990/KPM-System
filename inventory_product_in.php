<?php
  session_start();
  include "lib/koneksi.php";
  include "lib/appcode.php";
  include "lib/format.php";
  
    if(!isset($_SESSION['id_user']) && !isset($_SESSION['branch']))
    {
        header('Location:index.php'); 
    }

	$id_inv = mysqli_real_escape_string($conn,$_GET['id_inv_in']);
    $id_inv_filter = mysqli_real_escape_string($conn,$_GET['id_inv_in']);
    $status = mysqli_real_escape_string($conn,$_GET['status']);

    $id_user = $_SESSION['id_user'];
    if(isset($_GET['branch'])){
    $id_branch = mysqli_real_escape_string($conn,$_GET['branch']);
    }
    $date=date("Y-m-d H:i:s");
    
    // Proses Simpan dan Ubah
    if (isset($_POST['finish'])) {
        $valid=1;
        $id = mysqli_real_escape_string($conn,$_POST['id']);
        $status = mysqli_real_escape_string($conn,$_GET['status']);
        $id_inv = mysqli_real_escape_string($conn,$_POST['id_inv']);
        $date_in = mysqli_real_escape_string($conn,$_POST['date_in']);
        
        //Proses simpan transaksi baru
        if($id == $_SESSION['id_user']){
            $find_date = date("ymd");
            $query = mysqli_query($conn,"SELECT max(id_inv_in) as kodeTerbesar FROM inv_product_in WHERE id_inv_in like '___".$find_date."%' ");
	        if($data = mysqli_fetch_array($query)){
	            $kode = $data['kodeTerbesar'];
	            $urutan = (int) substr($kode, 9, 4);
                $urutan++;
                $tahun = date('y');
                $bulan = date('m');
                $tanggal = date('d');
	            $huruf = "PO-";
                $id_inv = $huruf . $tahun . $bulan . $tanggal . sprintf("%04s", $urutan);
            }else{
                $id_inv = "PO-".$find_date."0001";
            }
            $query_add_inv_in = "  UPDATE inv_product_in 
                                    SET id_inv_in = '".$id_inv."',
                                        inv_date = '".$date_in."',
                                        status = 'Approved',
                                        create_by = '".$id_user."',
                                        create_date = '".$date."'
                                    WHERE id_inv_in = '".$id."'";
            if(mysqli_query($conn,$query_add_inv_in)){  
                $query_get_inv = mysqli_query($conn,"SELECT id_product,stock_in FROM inv_product_in WHERE id_inv_in = '".$id_inv."' ");
                    while($row_amount = mysqli_fetch_array($query_get_inv)){
                        $id_product = $row_amount['id_product'];
                        $amount_origin = intval($row_amount['stock_in']);
                        $query_get_product = mysqli_query($conn,"SELECT stock FROM m_branch_stock WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
                        if($row_stock = mysqli_fetch_array($query_get_product)){
                            $stock = intval($row_stock['stock']);
    
                            $total_stock = $stock + $amount_origin;
                            mysqli_query($conn,"UPDATE m_branch_stock SET stock = '".$total_stock."' WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
                            
                        }else{
                            mysqli_query($conn,"insert into m_branch_stock (   id_product,
                                                                        id_branch,
                                                                        stock                                    
                                                                    ) values(		
                                                                        '".$id_product."',
                                                                        '".$id_branch."',
                                                                        '".$amount_origin."'
                                                                    )");
                        }
                    }
                $valid=1;
                $msg="Add Transaction : Success !";
            }else{
                $valid=0;
                $msg="Add Transaction : Failed !";
            }

        }
        // end


        // Proses Approve Transaksi
        if($id !== $_SESSION['id_user']){
            // Edit Branch Process
             $query_app_inv_in = "  UPDATE inv_product_in 
                                    SET id_inv_in = '".$id_inv."',
                                        inv_date = '".$date_in."',
                                        status = 'Approved',
                                        change_by = '".$id_user."',
                                        change_date = '".$date."'
                                    WHERE id_inv_in = '".$id."'";
                    
            if(mysqli_query($conn,$query_app_inv_in)){
                $query_get_inv = mysqli_query($conn,"SELECT id_product,stock_in FROM inv_product_in WHERE id_inv_in = '".$id_inv."' ");
                    while($row_amount = mysqli_fetch_array($query_get_inv)){
                        $id_product = $row_amount['id_product'];
                        $amount_origin = intval($row_amount['stock_in']);
                        $query_get_product = mysqli_query($conn,"SELECT stock FROM m_branch_stock WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
                        if($row_stock = mysqli_fetch_array($query_get_product)){
                            $stock = intval($row_stock['stock']);
    
                            $total_stock = $stock + $amount_origin;
                            mysqli_query($conn,"UPDATE m_branch_stock SET stock = '".$total_stock."' WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
                            
                        }else{
                            mysqli_query($conn,"insert into m_branch_stock (   id_product,
                                                                        id_branch,
                                                                        stock                                    
                                                                    ) values(		
                                                                        '".$id_product."',
                                                                        '".$id_branch."',
                                                                        '".$amount_origin."'
                                                                    )");
                        }
                    }
                $valid=1;
                $msg="Approve Transaction : Success !";
            }else{
                $valid=0;
                $msg="Approve Transaction : Failed !";
            }
            // End Edit Branch Process

            
        }
        // End

        // Proses ubah transaksi yang sudah ter approve
        if($id !== $_SESSION['id_user']){
            // Edit Branch Process
             $query_app_inv_in = "  UPDATE inv_product_in 
                                    SET id_inv_in = '".$id_inv."',
                                        change_by = '".$id_user."',
                                        change_date = '".$date."'
                                    WHERE id_inv_in = '".$id."'";
                    
            if(mysqli_query($conn,$query_app_inv_in)){
                $valid=1;
                $msg="Edit Transaction : Success !";
            }else{
                $valid=0;
                $msg="Edit Transaction : Failed !";
            }
            // End Edit Branch Process

            
        }
        // END

        //Function Eksekusi 
        if($valid==0) {  
          } else { 
            $page = "inventory_product_in_view.php";
            $sec = "0";
            $redirect=1;
            header("Refresh: $sec; url=$page");
          }

          echo "<script type='text/javascript'>alert('".$msg."')</script>";
        // End
     
    }
    //end

    // Ambil data dari id transaksi dan id branch
    if ($id_inv_filter == "new") {
        $id_inv_filter = $_SESSION['id_user'];
    }
    $transaction_date = $date=date("Y-m-d");
    $id = "New";
    $date = "";

    if(isset($_GET['branch'])){
        $get_branch = mysqli_real_escape_string($conn,$_GET['branch']);
    }
    $status = mysqli_real_escape_string($conn,$_GET['status']);
    // End

    // Show Detail Function
    $readonly = "";
    $hidden = "";
    $filled = "";
    if(isset($_GET['view'])){
            $readonly = "readonly";
            $hidden = "hidden";
            $filled = "filled-input";
    }
    // End
	
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
    <title>Add/Edit Transaction Product IN</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet"
        type="text/css" />

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
            <div class="preloader-it">
                <div class="loader-pendulums"></div>
            </div>
            <!-- Container -->
            <div class="container-fluid mt-15">
                <!-- Row -->
                <div class="row">
                    <div class="col">
                        <section class="hk-sec-wrapper">
                            <h5 class="hk-sec-title">Inventory Product IN</h5>
                            <div class="row">
                                <div class="col-sm">
                                    <form action="inventory_product_in.php?branch=<?= $id_branch; ?>&id_inv_in=<?php echo $id_inv_filter; ?>&status=<?php echo $status; ?>"
                                        method="POST">
                                        <?php
                                        $query_get_inv_in = mysqli_query($conn,"SELECT id_inv_in,
                                                                                inv_date 
                                                                            FROM inv_product_in
                                                                            WHERE id_inv_in = '".$id_inv_filter."' ");
                                        if($row_inv_in = mysqli_fetch_array($query_get_inv_in)){
                                            
                                            $id = $row_inv_in['id_inv_in'];
                                            $transaction_date = $row_inv_in['inv_date'];

                                            if($id == $id_user){
                                                $id = "New";
                                            }
                                        }
                                        echo'
                                        <div class="row justify-content-center">
                                            <div class="col-md-6">
                                                <input type="hidden" class="form-control id" name="id"
                                                    value="'.$id_inv_filter.'">
                                                <input type="hidden" class="form-control status"
                                                    value="'.$status.'">
                                                <input type="hidden" class="form-control id_branch"
                                                    value="'.$id_branch.'">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> ID Inventory IN </span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="id_inv"
                                                            id="id_inv"
                                                            class="form-control filled-input form-control-sm id_inv"
                                                            value="'.$id.'" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Transaction date</span>
                                                        </div>
                                                        <input autocomplete="off" type="date" name="date_in"
                                                            id="date_in"
                                                            class="form-control filled-input form-control-sm date_in" value="'.$transaction_date.'"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-center" '.$hidden.'>
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
                                                            class="form-control form-control-sm product_nm ">
                                                        <datalist id="product_list" class="product_list">
                                                        </datalist>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
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
                                            </div>
                                        </div>
                                        <div class="row justify-content-end" '.$hidden.'>
                                            <div class="col-xl-6 col-sm-12 col-md-6 ">
                                                <div class="button-add">
                                                    <a role="button" class="btn btn-sm btn-block btn-secondary button-plus" data-id_inv="'.$id_inv_filter.'" data-id_branch="'.mysqli_real_escape_string($conn,$_GET['branch']).'"> 
                                                        <span class=""><i class="fa fa-plus text-white"></i></span>
                                                    </a> 
                                                </div>   
                                            </div>
                                        </div>
                                        <div class="table-scroll mt-15">
                                        <table id="datable_2" class="table table-hover  display mt-15" >
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Product Name</th>
                                                    <th style="min-width:250px; width:250px;">Amount</th>
                                                    <th '.$hidden.'>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="view">
                                                ';
                                                
                                                $no = 0;
                                                $where_branch = "";
                                                    if($get_branch !== "pusat"){
                                                        $where_branch = " AND i.id_branch = '".$get_branch."' ";
                                                    }
                                                $query_get_product = mysqli_query($conn,"SELECT i.*,p.nm_product,p.uom_product 
                                                                                    FROM inv_product_in i 
                                                                                    left join m_product p on p.m_product_id = i.id_product
                                                                                    WHERE i.id_inv_in = '".$id_inv_filter."' 
                                                                                    ".$where_branch." ");
                                                while ($row_product = mysqli_fetch_array($query_get_product)) {
                                                    $no++;
                                                    $id_inv = $row_product['inv_in_id'];
                                                    $id_product = $row_product['id_product'];
                                                    $id_branch = $row_product['id_branch'];
                                                    $nm_product = $row_product['nm_product'];
                                                    $amount = $row_product['stock_in'];
                                                    $uom_product = $row_product['uom_product'];
                                                
                                                echo'
                                                <tr>
                                                    <td style="font-size:11px;">'.$no.'</td>
                                                    <td style="font-size:11px;">'.$nm_product.' </td>
                                                    <td style="font-size:11px; ">
                                                        <div class="input-group input-group-sm" width="250px">
                                                            <input type="number"
                                                                class="form-control '.$filled.' form-control-sm amount_'.$id_inv.'"
                                                                name="nm_product_'.$id_inv.'"
                                                                value="'.$amount.'" id="amount_'.$id_inv.'" '.$readonly.'>
                                                            <span style="width:-2vw;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> '.$uom_product.' </span>
                                                        </div>
                                                    </td>
                                                    <td '.$hidden.'>
                                                        <a role="button"
                                                            class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list"
                                                            data-id_inv="'.$id_inv.'" data-id_product="'.$id_product.'" data-id_branch="'.$id_branch.'">
                                                            <span class="btn-icon-wrap"><i
                                                                    class="fa fa-pencil"></i></span></a>
                                                        <a role="button"
                                                            class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list"
                                                            data-id_inv="'.$id_inv.'" data-id_product="'.$id_product.'" data-id_branch="'.$id_branch.'">
                                                            <span class="btn-icon-wrap"><i
                                                                    class="icon-trash"></i></span>
                                                        </a>
                                                    </td>

                                                </tr>
                                                ';
                                                }
                                                echo'
                                            </tbody>
                                        </table>
                                        </div>
                                        ';
                                        ?>
                                
                                        <div class="form-group justify-content-end">
                                            <div class="row">
                                                <div class="col-sm-2 mt-15" <?= $hidden; ?> >
                                                    <button class="btn btn-sm btn-success btn-block" name="finish" value="Save"
                                                        type="submit">Save</button>
                                                </div>
                                                <div class="col-sm-2 mt-15">
                                                    <a href="inventory_product_in_view.php" class="btn btn-sm btn-red btn-block">Cancel</a>
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

    <!-- Init JavaScript -->
    <script src="dist/js/init.js"></script>

    <!-- Delete -->
    <script type="text/javascript">
        $(document).on("keyup", '.product_nm', function (e) {

            var product_list = $(this).val();
            var dataString = 'product_in=' + product_list;
            if (product_list.length >= 1) {
                $.ajax({
                    type: 'POST',
                    url: 'ajax/ajax_change_datalist.php',
                    data: dataString,
                    success: function (response) {
                        $('.product_list').html(
                            response);

                    }
                });
            } else {
                $('.product_list').html('');
            }

        });

        $(document).on("change", '.product_nm', function (e) {

            var str = $(this).val();
            var split = str.split(" | ");
            var product = split[1];
            var dataString = 'product=' + product;
            if(str == ""){
                $('.amount_product').prop("readonly",true);
                $('.amount_product').addClass("filled-input");
            }else{
                $('.amount_product').prop("readonly",false);
                $('.amount_product').removeClass("filled-input");
            }
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_product.php',
                data: dataString,
                success: function (response) {
                    $('.uom').html(
                        response);

                }
            });

        });

        $(document).on("keyup", '.amount_product', function (e) {

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
                url: 'ajax/ajax_inventory_product_in.php',
                data: dataString,
                success: function (response) {
                    $('.button-add').html(
                        response);

                }
            });

        });

        // $(document).on("change", '.type_product', function (e) {
        //     var type = $(this).val();

        //     if (type == "Product") {
        //         $(".product").show(500);
        //     } else {
        //         $(".product").hide(500);
        //     }
        // });

        $(document).on("click", '.add_list', function (e) {

            var id_inv = $(this).data("id_inv");
            var amount = $('.amount_product').val();
            var date = $('.date_in').val();
            var str = $('.product_nm').val();
            var split = str.split(" | ");
            var id_product = split[1];
            var id_branch = $(this).data("id_branch");
            var status = $(".status").val();
            var dataString = 'id_product=' + id_product +
                            '&add=' + id_inv +
                            '&date=' + date +
                            '&status=' + status +
                            '&id_branch=' + id_branch +
                            '&amount=' + amount;
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_product_in.php',
                data: dataString,
                success: function (response) {

                    $('.view').html(
                        response);
                    $('.amount_product').val("");
                    $('.product_nm').val("");
                    $('.uom_product').val("(UOM)");
                    $('.amount_product').prop("readonly",true);
                    $('.amount_product').addClass("filled-input");
                    $('.button-plus').removeClass("btn-success");
                    $('.button-plus').removeClass("add_list");
                    $('.button-plus').addClass("btn-secondary");
                }
            });
        });

        $(document).on("click", '.set_list', function (e) {
            var id_product = $(this).data("id_product");
            var id_branch = $(this).data("id_branch");
            var id_inv = $(this).data("id_inv");
            var id_inv_filter = $(".id").val();
            var status = $(".status").val();
            var amount = $(".amount_" + id_inv).val();
            
            var dataString ='id_product=' + id_product + 
                            '&edit=' + id_inv +
                            '&id_branch=' + id_branch +
                            '&status=' + status +
                            '&inv_filter=' + id_inv_filter +
                            '&amount=' + amount;
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_product_in.php',
                data: dataString,
                success: function (response) {
                    alert("Change Amount Success");
                }
            });
        });

        $(document).on("click", '.del_list', function (e) {
            var id_product = $(this).data("id_product");
            var id_branch = $(this).data("id_branch");
            var id_inv = $(this).data("id_inv");
            var id_inv_filter = $(".id").val();
            var status = $(".status").val();
            var amount = $(".amount_" + id_inv).val();
            var dataString = 'delete=' + id_inv +
                             '&id_branch=' + id_branch +
                             '&inv_filter=' + id_inv_filter +
                             '&inv_filter=' + id_inv_filter +
                             '&status=' + status +
                             '&id_product=' + id_product;
            $.ajax({
                type: 'POST',
                url: 'ajax/ajax_inventory_product_in.php',
                data: dataString,
                success: function (response) {
                    $('.view').html(
                        response);

                }
            });
        });



        $(document).ready(function () {
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