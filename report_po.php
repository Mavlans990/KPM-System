<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

$id_user = $_SESSION['id_user'];

$tgl_1 = date("Y-m-d");
$tgl_2 = date("Y-m-d");

$where = "";
// $get_branch = $_SESSION['branch'];
$branch_id = "1";
$where_branch = "AND i.id_branch = '".$branch_id."' ";

if (isset($_POST['cari_tgl'])) {
    $tgl_1 = mysqli_real_escape_string($conn,$_POST['tgl_1']);
    $tgl_2 = mysqli_real_escape_string($conn,$_POST['tgl_2']);
    $branch_id = mysqli_real_escape_string($conn,$_POST['branch']);
    $where_branch = " AND i.id_branch = '".$branch_id."' ";
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
    <title>Report Purchase Order</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="../favicon.ico">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="../vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="../vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet"
        type="text/css" />

    <!-- select2 CSS -->
    <link href="../vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="../vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="../vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Daterangepicker CSS -->
    <link href="../vendors/daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

    <!-- Custom CSS -->
    <link href="../dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body>

    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

        <?php include "header.php"; ?>

        <!-- Main Content -->
        <div class="hk-pg-wrapper">

            <!-- Header -->
            <div class="card-header" style="background-color:#E5E5E5 !important">
                Report
                <h5 class="hk-sec-title text-dark-light-3"> Inventory Stock IN
                </h5>
            </div>
            <!-- /Header -->

            <!-- Container -->

            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
                <?php
                //  if($get_branch == "pusat" || $_SESSION['group'] == "Franchise"){
                //     $display = "";
                // }else{
                //     $display = "hidden";
                //     $where_branch = " AND i.id_branch = '".$get_branch."' ";
                // } 
                echo' 
                <form action="'.$_SERVER['PHP_SELF'].'" method="post">
                    <div class="row" style="" >
                        <div class="col-md-6 mt-15 d">
                            <div class="row no-gutters">
                                <div class="col-12 col-xl-4">
                                    <span style="" class=" text-dark form-control filled-input form-control-sm" id="inputGroup-sizing-sm">Transaction Date</span>
                                </div>
                                <div class="col-12 col-xl-8">
                                    <div class="input-group">
                                    <input autocomplete="off" type="text" readonly
                                        id="cari_tanggal_1" name="tgl_1" class="form-control single_date form-control-sm" value="'.$tgl_1.'" >
                                        <span class="mt-5 text-dark mx-2">To</span>
                                        <input autocomplete="off" type="text" readonly
                                        id="cari_tanggal_2" name="tgl_2" class="form-control single_date form-control-sm" value="'.$tgl_2.'" >
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-4 mt-15 d" '.$display.'>
                            <div class="row no-gutters">
                                <div class="col-12 col-xl-3">
                                    <span style="" class=" form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Branch</span>
                                </div>
                                <div class="col-12 col-xl-9">
                                    <select id="" class="form-control form-control-sm custom-select custom-select-sm id_cabang" name="branch">
                                    
                                    
                                    <option value="" ';if($branch_id == ""){echo'selected';}echo' >-- Choose Branch --</option>
                                        '        ;
                                    // if($_SESSION['group'] == "Franchise"){
                                    //     $query_get_id_branch = mysqli_query($conn,"SELECT id_branch FROM m_user where id_user = '".$id_user."' ");
                                    //     $row_id_branch = mysqli_fetch_array($query_get_id_branch);
                                    //     $array_id = $row_id_branch['id_branch'];
                                    //     $array_id = explode("#",$array_id);
                                    
                                    //     foreach ($array_id as $branch_user) {
                                    //         $query_get_branch = mysqli_query($conn,"SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang = '".$branch_user."'");
                                    //         // echo $query_get_branch;
                                    //         if($row_branch = mysqli_fetch_array($query_get_branch)){
                                    //             $id_branch = $row_branch['id_cabang'];
                                    //             $nama_cabang = $row_branch['nama_cabang'];
                                    //             // Untuk Franchise
                                    //             if($id_branch == $branch_id){
                                    //                 echo'
                                    //                     <option value="'.$id_branch.'" selected >'.$nama_cabang .'</option>
                                    //                 ';
                                    //             }else{
                                    //                 echo'
                                    //                     <option value="'.$id_branch.'" >'.$nama_cabang.'</option>
                                    //                 ';
                                    //             }
                                    //             // END
                                    //         }
                                        
                                    //     }
                                    // }
                                    // // Untuk Pusat dan superadmin
                                    // if ($_SESSION['branch'] == "pusat") {
                                        $query_get_branch = mysqli_query($conn,"SELECT id_cabang,nama_cabang FROM tb_cabang ");
                                        // echo $query_get_branch;
                                        while($row_branch = mysqli_fetch_array($query_get_branch)){
                                            $id_branch = $row_branch['id_cabang'];
                                            $nama_cabang = $row_branch['nama_cabang'];
                                            if($id_branch == $branch_id){
                                                echo'
                                                    <option value="'.$id_branch.'" selected >'.$nama_cabang .'</option>
                                                ';
                                            }else{
                                                echo'
                                                    <option value="'.$id_branch.'" >'.$nama_cabang.'</option>
                                                ';
                                            }
                                        }
                                    // }
                                    echo'
                                    </select>
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
                <div class="table-scroll mt-15">
                <table class="table table-hover table-sm w-100 display mt-15" id="datable_2">
                    <thead>
                        <tr class="bg-green text-white">
                            <td>No</td>
                            <td>Transaction ID</td>
                            <td>Transaction Date</td>
                            <td>Product</td>
                            <td>Branch</td>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(isset($_POST['cari_tgl'])){

                        $tgl_1 = mysqli_real_escape_string($conn,$_POST['tgl_1']);
                        $tgl_2 = mysqli_real_escape_string($conn,$_POST['tgl_2']);
                        if($tgl_1 !== "" || $tgl_2 !== ""){

                        
                        if($tgl_1 !== "" && $tgl_2 == ""){
                            $where = "AND inv_date = '".$tgl_1."' ";
                        }
                        if($tgl_1 == "" && $tgl_2 !== ""){
                            $where = "AND inv_date = '".$tgl_2."' ";
                        }
                        if ($tgl_1 !== "" && $tgl_2 !== "") {
                            $where = "AND inv_date BETWEEN '".$tgl_1."' and '".$tgl_2."' ";
                        }
                    
                    $no = 1;
                    // $where_branch = "";
                    // if($get_branch !== "pusat"){
                    //     $where_branch = " AND i.id_branch = '".$get_branch."' ";
                    // }
                    $query_get_po=mysqli_query($conn,"SELECT distinct i.id_inv_in,
                                                                i.inv_date,
                                                                b.nama_cabang
                                                            FROM inv_product_in i
                                                            LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
                                                            where id_inv_in <> 'new'
                                                            AND i.status = 'Approved'
                                                            ".$where." 
                                                            ".$where_branch."
                                                            ORDER BY i.inv_in_id desc");
                        
                    // echo $query_get_po;
                                                         
                    while($row_po = mysqli_fetch_array($query_get_po)){
                        $branch = $row_po['nama_cabang'];
                            if($branch == "" && $get_branch == "pusat" ){
                                $branch = "Pusat";
                            }
                        
                    echo'                               
                        <tr>
                            <td style="font-size:11px;">'.$no.'</td>
                            <td style="font-size:11px;">'.$row_po['id_inv_in'].'</td>
                            
                            <td style="font-size:11px;">'.date('d/m/Y', strtotime($row_po['inv_date'])).'</td>
                            <td style="font-size:11px;">
                            ';
                            $query_get_product=mysqli_query($conn,"SELECT p.nm_product,p.uom_product,i.stock_in
                                                            FROM m_product p
                                                            JOIN inv_product_in i ON i.id_product = p.m_product_id
                                                            where i.id_inv_in = '".$row_po['id_inv_in']."'
                                                            ORDER BY p.nm_product asc");
                                                    while ($row_product = mysqli_fetch_array($query_get_product)) {
                                                        echo'
                                                            <span>'.$row_product['nm_product'].' = '.$row_product['stock_in'].' '.$row_product['uom_product'].'</span> <br>
                                                        ';
                                                    }
                                                    echo'
                                                    </td>
                            <td style="font-size:11px;">'.$branch.'</td>
                            
                        </tr>
                        ';
                        $no++;
                    }
                }
            }
                        ?>
                    </tbody>
                </table>

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
    <script src="../vendors/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendors/popper.js/dist/umd/popper.min.js"></script>
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Slimscroll JavaScript -->
    <script src="../dist/js/jquery.slimscroll.js"></script>



    <!-- Data Table JavaScript -->
    <script src="../vendors/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../vendors/datatables.net-dt/js/dataTables.dataTables.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../vendors/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../vendors/jszip/dist/jszip.min.js"></script>
    <script src="../vendors/pdfmake/build/pdfmake.min.js"></script>
    <script src="../vendors/pdfmake/build/vfs_fonts.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../dist/js/dataTables-data.js"></script>

    <!-- FeatherIcons JavaScript -->
    <script src="../dist/js/feather.min.js"></script>

    <!-- Fancy Dropdown JS -->
    <script src="../dist/js/dropdown-bootstrap-extended.js"></script>

    <!-- Toggles JavaScript -->
    <script src="../vendors/jquery-toggles/toggles.min.js"></script>
    <script src="../dist/js/toggle-data.js"></script>

    <!-- Daterangepicker JavaScript -->
    <script src="../vendors/moment/min/moment.min.js"></script>
    <script src="../vendors/daterangepicker/daterangepicker.js"></script>
    <script src="../dist/js/daterangepicker-data.js"></script>

    <!-- Select2 JavaScript -->
    <script src="../vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="../dist/js/select2-data.js"></script>

    <!-- Init JavaScript -->
    <script src="../dist/js/init.js"></script>


    <script type="text/javascript">
        $(document).ready(function () {

            $(document).on("click", '.hapus_button', function (e) {
                var id_hapus = $(this).data('id_hapus');
                $(".id_hapus").val(id_hapus);
            });
        });
    </script>


</body>

</html>