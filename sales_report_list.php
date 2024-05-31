<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";


if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
  header('Location:index.php');
}

$branch_id = $_SESSION['branch'];


if($branch_id == "pusat"){
    $display = "";
}else{
    $display = "filled-input";
} 
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
  <title>Admin Reydecal</title>
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

  <!-- Custom CSS -->
  <link href="dist/css/style.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this page -->
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <style type="text/css">
    table thead tr th {
      text-align: center;
    }

    hr {
      margin: 2px 0;
    }
  </style>
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div class="hk-wrapper hk-vertical-nav">

    <!-- Sidebar -->
    <?php include "header.php"; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

      <!-- Main Content -->
      <div class="hk-pg-wrapper">

        <!-- Topbar -->
        <?php //include "part/topbar.php"; 
        ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">

          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Laporan Purchase - Sales
            <?php
              $tgl1 = '';
              $tgl2 = '';
              if (isset($_POST['tgl1'])) {
                // echo "dari " . date('d/m/Y', strtotime($_POST['tgl1'])) . " sampai " . date('d/m/Y', strtotime($_POST['tgl2']));
                
                $branch = mysqli_real_escape_string($conn,$_POST['branch']);
                $tgl1 = $_POST['tgl1'];
                $tgl2 = $_POST['tgl2'];
              }
              ?>

          </h1>
             <?php 
                    echo'
          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="form-group form-inline" style="margin-bottom:0px">
                <form method="POST" action="'.$_SERVER['PHP_SELF'].'">
                  <input type="date" class="form-control form-control-sm tgl1" name="tgl1" value="'.$tgl1.'" required>
                  <span style="margin:0px 10px">Sampai</span>
                  <input type="date" class="form-control form-control-sm tgl2" name="tgl2" value="'.$tgl2.'" required>
                  <select id="" class="form-control '.$display.' form-control-md custom-select custom-select-sm id_cabang" name="branch">
                   
                    <option value="all" ';if($branch_id == "all"){echo'selected';}echo' >-- All Branch --</option>
                        ';        
                    // Untuk Pusat dan superadmin
                    if ($_SESSION['branch'] == "pusat") {
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
                    }
                    else
                    {
                         $query_get_branch = mysqli_query($conn,"SELECT id_cabang,nama_cabang FROM tb_cabang WHERE id_cabang = '".$branch_id."' ");
                        // echo $query_get_branch;
                        if($row_branch = mysqli_fetch_array($query_get_branch)){
                            $id_branch = $row_branch['id_cabang'];
                            $nama_cabang = $row_branch['nama_cabang'];
                            echo'
                                <option value="'.$id_branch.'" selected readonly>'.$nama_cabang .'</option>
                            ';
                        }   
                    }
                    echo'
                    </select>
                  <input type="submit" class="btn btn-success btn-sm" name="search" id="search" value="Cari">
                  
                    ';
                    ?>
                </form>
                <?php if (isset($_POST['tgl1'])) { ?>
                  <a href="print/sales_report_print.php?tgl1=<?php echo $tgl1; ?>&tgl2=<?php echo $tgl2; ?>" class="btn btn-info" style="margin-left:5px;"><i class="fa fa-download fa-sm text-white-50"></i> Unduh Laporan</a>
                <?php } ?>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <!--<thead>-->
                  <!--  <tr>-->
                  <!--    <th>No</th>-->
                  <!--    <th>Tanggal Transaksi</th>-->
                  <!--    <th>Invoice</th>-->
                  <!--    <th>Nama Pelanggan</th>-->
                  <!--    <th>Cabang</th>-->
                  <!--    <th>Produk</th>-->
                  <!--    <th>Total</th>-->
                  <!--  </tr>-->
                  <!--</thead>-->
                  <tbody>
                    <?php
                    if(isset($_POST['search'])){
                        $tgl_1 = mysqli_real_escape_string($conn,$_POST['tgl1']);
                        $tgl_2 = mysqli_real_escape_string($conn,$_POST['tgl2']);
                        $branch = mysqli_real_escape_string($conn,$_POST['branch']);
                        if($tgl_1 !== "" || $tgl_2 !== ""){
                            $where = "AND inv_date = '".date("Y-m-d")."' ";
                        }if($tgl_1 !== "" && $tgl_2 == ""){
                            $where = "AND inv_date = '".$tgl_1."' ";
                        }
                        if($tgl_1 == "" && $tgl_2 !== ""){
                            $where = "AND inv_date = '".$tgl_2."' ";
                        }
                        if ($tgl_1 !== "" && $tgl_2 !== "") {
                            $where = "AND inv_date BETWEEN '".$tgl_1."' and '".$tgl_2."' ";
                        }
                        
                        $where_branch = "and id_branch = '".$branch."'";
                        if($branch == "all")
                        {
                            $where_branch = "";
                            $branch = "";
                        }
                        
                        echo '
                        <tr>
                            <td colspan="7"> Purchase : '.date("d F Y",strtotime($tgl_1)).' s/d '.date("d F Y",strtotime($tgl_2)).'</td>
                        </tr>
                        <tr>
                            <td>No</td>
                            <td>Tanggal Transaksi</td>
                            <td>No Transaksi</td>
                            <td>Cabang</td>
                            <td>Produk</td>
                            <td>Jumlah Stok</td>
                            <td>Total</td>
                        </tr>
                        ';
                        $no = 1;
                        $all_harga_masuk = 0;
                        $query_get_pemasukan = mysqli_query($conn,"SELECT * FROM inv_product_in WHERE NOT id_inv_in IN (SELECT id_user FROM m_user) ".$where_branch." ".$where." "); 
                        while($row_pemasukan = mysqli_fetch_array($query_get_pemasukan))
                        {
                            $id_cabang = $row_pemasukan['id_branch'];
                            $inv_date = $row_pemasukan['inv_date'];
                            $no_inv = $row_pemasukan['id_inv_in'];
                            $id_stock = $row_pemasukan['id_product'];
                            $jmlh_stock = $row_pemasukan['stock_in'];
                            
                            $nm_stock = 0;
                            $price_stock = 0;
                            $query_get_stock = mysqli_query($conn,"SELECT nm_product,price,uom_product FROM m_product WHERE m_product_id = '".$id_stock."'");
                            if($row_stock = mysqli_fetch_array($query_get_stock))
                            {
                                $nm_stock = $row_stock['nm_product'];
                                $price_stock = $row_stock['price'];
                                $uom = $row_stock['uom_product'];
                            }
                            
                            $query_get_cabang = mysqli_query($conn,"SELECT nama_cabang FROM tb_cabang WHERE id_cabang = '".$id_cabang."'");
                            if($row_cabang = mysqli_fetch_array($query_get_cabang))
                            {
                                $nm_cabang = $row_cabang['nama_cabang'];
                            }
                            
                            
                            $total_harga = $jmlh_stock * $price_stock;
                            
                            echo '
                            <tr>
                                <td>'.$no.'</td>
                                <td>'.date("d/m/Y",strtotime($inv_date)).'</td>
                                <td>'.$no_inv.'</td>
                                <td>'.$nm_cabang.'</td>
                                <td>'.$nm_stock.'</td>
                                <td class="text-right">'.round($jmlh_stock,1).' '.$uom.'</td>
                                <td class="text-right">Rp.'.number_format($total_harga).'</td>
                            </tr>
                            ';
                            
                            $all_harga_masuk = $all_harga_masuk + $total_harga;
                            $no++;
                        }
                        
                        
                        
                        echo '
                        <tr>
                            <td colspan="6" class="text-right"><strong>Total Purchase</strong></td>
                            <td class="text-right"><strong>Rp.'.number_format($all_harga_masuk).'</strong></td>
                        <tr>
                        <tr>
                            <td colspan="7"></td>
                        </tr>
                        <tr>
                            <td colspan="7"> Sales : '.date("d F Y",strtotime($tgl_1)).' s/d '.date("d F Y",strtotime($tgl_2)).'</td>
                        </tr>
                        <tr>
                            <td>No</td>
                            <td>Tanggal Transaksi</td>
                            <td>No Transaksi</td>
                            <td>Cabang</td>
                            <td>Produk</td>
                            <td>Jumlah Stok</td>
                            <td>Total</td>
                        </tr>
                        ';
                        $all_harga_keluar = 0;
                        $no = 1;
                        $total_harga = "0";
                        $query_get_pemasukan = mysqli_query($conn,"SELECT * FROM inv_product_out WHERE NOT id_inv_out IN (SELECT id_user FROM m_user) ".$where_branch." ".$where." "); 
                        while($row_pemasukan = mysqli_fetch_array($query_get_pemasukan))
                        {
                            $id_cabang = $row_pemasukan['id_branch'];
                            $inv_date = $row_pemasukan['inv_date'];
                            $no_inv = $row_pemasukan['id_inv_out'];
                            $id_stock = $row_pemasukan['id_product'];
                            $jmlh_stock = $row_pemasukan['stock_out'];
                            
                            $nm_stock = 0;
                            $price_stock = 0;
                            $query_get_stock = mysqli_query($conn,"SELECT nm_product,price,uom_product  FROM m_product WHERE m_product_id = '".$id_stock."'");
                            if($row_stock = mysqli_fetch_array($query_get_stock))
                            {
                                $nm_stock = $row_stock['nm_product'];
                                $price_stock = $row_stock['price'];
                                $uom = $row_stock['uom_product'];
                            }
                            $query_get_cabang = mysqli_query($conn,"SELECT nama_cabang FROM tb_cabang WHERE id_cabang = '".$id_cabang."'");
                            if($row_cabang = mysqli_fetch_array($query_get_cabang))
                            {
                                $nm_cabang = $row_cabang['nama_cabang'];
                            }
                            
                            
                            $total_harga = $jmlh_stock * $price_stock;
                            
                            echo '
                            <tr>
                                <td>'.$no.'</td>
                                <td>'.date("d/m/Y",strtotime($inv_date)).'</td>
                                <td>'.$no_inv.'</td>
                                <td>'.$nm_cabang.'</td>
                                <td>'.$nm_stock.'</td>
                                <td class="text-right">'.round($jmlh_stock,1).' '.$uom.'</td>
                                <td class="text-right">Rp.'.number_format($total_harga).'</td>
                            </tr>
                            ';
                            
                            $all_harga_keluar = $all_harga_keluar + $total_harga;
                            $no++;
                        }
                        
                        $selisih_all = $all_harga_keluar - $all_harga_masuk;
                        
                        echo '
                        <tr>
                            <td colspan="6" class="text-right"><strong>Total Sales</strong></td>
                            <td class="text-right">Rp. '.number_format($all_harga_keluar).'</td>
                        <tr>
                        <tr>
                            <td colspan="6"class="text-right"><strong>Selisih Total</strong></td>
                            <td class="text-right"><strong>Rp. '.number_format($selisih_all).'</strong></td>
                        <tr>
                        
                        ';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->



    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->


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

  <!-- Select2 JavaScript -->
  <script src="vendors/select2/dist/js/select2.full.min.js"></script>
  <script src="dist/js/select2-data.js"></script>

  <!-- Init JavaScript -->
  <script src="dist/js/init.js"></script>

  <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/datatables-demo.js"></script>

</body>

</html>