<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

$id_beban = generate_beban();



if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$select_masuk = "SELECT a.* FROM tb_barang_masuk a WHERE a.id_transaksi = '" . $_SESSION['id_user'] . "' AND a.status = '' ";
$query_masuk = mysqli_query($conn, $select_masuk);
while ($row_masuk = mysqli_fetch_array($query_masuk)) {

    $delete = mysqli_query($conn, "DELETE FROM tb_barang_masuk WHERE id_transaksi = '" . $_SESSION['id_user'] . "' AND status = ''");
}

if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
    $tambah_baru = '<a href="javascript:void(0);" class="btn btn-primary form-control-sm" data-toggle="modal" data-target="#ChooseModal"><i class="fa fa-plus"></i> Tambah Baru</a>';
} else {
    $tambah_baru = '<a href="master_barang_masuk_edit.php?id_transaksi=new&id_cabang=' . $_SESSION['branch'] . '"  class="btn btn-primary form-control-sm"><i class="fa fa-plus"></i> Tambah Baru</a>';
}


$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
}

$hidden = "style='display:none;'";
if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
    $hidden = "";
}

$from = date("Y-m-d");
$to = date("Y-m-d");
$line = "";
$namaPic = "";
$namaWo = "";
$source_barang = "";
if (isset($_POST['search'])) {
    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    // $line = mysqli_real_escape_string($conn, $_POST['line']);
    // $namaPic = mysqli_real_escape_string($conn, $_POST['nama_pic']);
    // $namaWo = mysqli_real_escape_string($conn, $_POST['nama_wo']);
    // $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    // $source_customer = mysqli_real_escape_string($conn, $_POST['source_customer']);
    // Error Gatau
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Inventory System</title>
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

    <style>
        .fs-11 {
            font-size: 11px !important;
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




                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3" style="text-transform: none;">
                            <div class="form-inline">
                                <h3 class="mr-2">Laporan Mutasi Stok</h3>
                                <!-- <a href="master_work_order_edit.php?id_transaksi=new" class="btn btn-primary form-control-sm"><i class="fa fa-plus"></i> Tambah Baru</a> -->
                            </div>
                        </div>

                        <div class="card-header py-1">
                            <div class="">
                                <form action="" method="post">
                                    <br>
                                    <div class="row no-gutters">
                                        <div class="col-md-6" style="display: contents;">
                                            <div class="col-md-8">
                                                <div class="form-group form-inline">

                                                    <div class="input-group-prepend">
                                                        <span class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Cari Tanggal</span>
                                                    </div>
                                                    <input type="date" name="tgl_from" id="" class="form-control form-control-sm" value="<?= $from; ?>" required>
                                                    <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                                    <input type="date" name="tgl_to" id="" class="form-control form-control-sm" style="margin-right: 5px;" value="<?= $to; ?>" required>
                                                    <button type="submit" class="mt--5 btn btn-primary form-control-sm ml-1" name="search"><i class="fa fa-search"></i> Cari</button>
                                                </div>
                                            </div>
                                        </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Item</th>
                                            <th>Nama Barang</th>
                                            <th>Satuan</th>
                                            <th>Qty Awal</th>
                                            <th>Qty Masuk</th>
                                            <th>Qty Keluar</th>
                                            <th>Qty Akhir</th>
                                            <!-- <th>BTJ</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $n = 1;
                                        // $where_branch = "WHERE a.status != '' AND a.id_transaksi LIKE '%PO%' AND a.by_user_pajak = '" . $_SESSION['jenis_pajak'] . "'";

                                        // $tgl_from = "";
                                        // $tgl_to = "";
                                        // $cabang = "";
                                        // $limit = " LIMIT 30 ";
                                        // if (isset($_POST['search'])) {
                                        //     $tgl_from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
                                        //     $tgl_to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
                                        //     // $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
                                        //     $where_branch = " WHERE a.tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND a.status != '' AND a.by_user_pajak = '" . $_SESSION['by_user_pajak'] . "'";
                                        //     $limit = "";
                                        // }
                                        // $where_line = "";
                                        $where_tgl = "";
                                        $where_tgl1 = "";
                                        $where_tgl2 = "";
                                        // $where_pic = "";
                                        // $where_wo = "";
                                        if (isset($_POST['search'])) {
                                            // if ($_POST['line'] != '') {
                                            //     $where_line = "AND a.line ='" . $line . "' ";
                                            // }
                                            if ($_POST['tgl_from'] != '' && $_POST['tgl_to'] != '') {
                                                $where_tgl = " AND tgl_transaksi <= '" . $from . "'";
                                                $where_tgl1 = " AND tgl_transaksi >= '" . $from . "'";
                                                $where_tgl2 = " AND tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "'";
                                            }
                                            // if ($_POST['nama_pic'] != '') {
                                            //     $where_pic = "AND a.pic ='" . $namaPic . "' ";
                                            // }
                                            // if ($_POST['nama_wo'] != '') {
                                            //     $where_wo = "AND a.id_transaksi ='" . $namaWo . "' ";
                                            // }
                                        }

                                        $ttlQtyjadi = 0;
                                        $ttlBs = 0;
                                        $no = 1;
                                        $ttlTurun = 0;
                                        $ttlBtj = 0;
                                        $sql = mysqli_query($conn, "SELECT id_bahan_kain,kode_bahan_kain,nama_kain FROM tb_bahan_kain WHERE kategori LIKE '%KAIN JADI%' ");
                                        // $sql = mysqli_query($conn, "SELECT a.* FROM tb_sewing a WHERE a.id_transaksi LIKE '%WO%' " . $where_line . " " . $where_tgl . " " . $where_pic . " " . $where_wo . " ORDER BY no_id ASC LIMIT 50");
                                        while ($row = mysqli_fetch_array($sql)) {
                                            $id_bahan = $row['id_bahan_kain'];
                                            // $id_transaksi = $row['id_transaksi'];
                                            
                                            $row_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(netto) stock FROM tb_barang_masuk WHERE id_bahan_kain = '".$id_bahan."' " . $where_tgl . " AND terpakai = ''"));
                                            $row_pakai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(netto) pakai FROM tb_barang_masuk WHERE id_bahan_kain = '".$id_bahan."' " . $where_tgl1 . " AND terpakai != ''"));
                                            $row_stock1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(netto) masuk FROM tb_barang_masuk WHERE id_bahan_kain = '".$id_bahan."' " . $where_tgl2 . " "));
                                            $row_pakai1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(netto) keluar FROM tb_barang_masuk WHERE id_bahan_kain = '".$id_bahan."' " . $where_tgl2 . " AND terpakai != ''"));
                                            // echo "SELECT SUM(netto) FROM tb_barang_masuk WHERE id_bahan_kain = '".$id_bahan."' " . $where_tgl . " AND terpakai = '' <br>";
                                            // $q_work = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_bahan_kain = '$id_transaksi'"));
                                            // if ($row_stock['stock'] > 0) {
                                            //     $stok = number_format($row_stock['stock']);
                                            //     $pakai = number_format($row_pakai['pakai']);
                                            //     $qty_awal = $stok - $pakai;
                                        
                                            //     echo '
                                            //         <tr>
                                            //             <td class="fs-11" style="vertical-align:top;">' . $no . '</td>
                                            //             <td class="fs-11" style="vertical-align:top;">' . $row['kode_bahan_kain'] . ' - ' . $row['id_bahan_kain'] . '</td>
                                            //             <td class="fs-11" style="vertical-align:top;">' . $row['nama_kain'] . '</td>
                                            //             <td class="fs-11 text-center" style="vertical-align:top;"> KG </td>';
                                        
                                            //     if ($qty_awal <= 0) {
                                            //         echo "<td class='fs-11 text-center' style='vertical-align:top;'>" . $stok . " - " . $pakai . "</td>";
                                            //     } else {
                                            //         echo "<td class='fs-11 text-center' style='vertical-align:top;'>".number_format($qty_awal)."</td>";
                                            //     }
                                            //     echo '</tr>';
                                            // $no++;

                                            // }
                                            $stok = round($row_stock['stock']);
                                            $pakai = round($row_pakai['pakai']);
                                            $qty_awal = $stok - $pakai;
                                            // echo 'Qty awal '.$row['nama_kain'].' = '.$stok.' - '.$pakai.' = '.$qty_awal.'<br>';
                                            if($qty_awal <= 0){
                                                $qty_awal = 0;
                                            } 
                                            // echo $qty_awal.'<br>';
                                            $qty_masuk = round($row_stock1['masuk']);
                                            $qty_keluar = round($row_pakai1['keluar']);
                                            $qty_akhir = ($qty_awal + $qty_masuk) - $qty_keluar;
                                            // echo '('.$qty_awal.' + '.$qty_masuk.') - '.$qty_keluar.' = ' . $qty_akhir . '<br><br>';
                                            echo '
                                                <tr>
                                                    <td class="fs-11" style="vertical-align:top;">' . $no . '</td>
                                                    <td class="fs-11" style="vertical-align:top;">' . $row['kode_bahan_kain'] . ' - ' . $row['id_bahan_kain'] . '</td>
                                                    <td class="fs-11" style="vertical-align:top;">' . $row['nama_kain'] . '</td>
                                                    <td class="fs-11 text-center" style="vertical-align:top;"> KG </td> ';
                                                    if($qty_awal <= 0){
                                                        echo "<td class='fs-11 text-center' style='vertical-align:top;'> 0 </td>";
                                                    }else{
                                                        echo "<td class='fs-11 text-center' style='vertical-align:top;'>".number_format($qty_awal)."</td>";
                                                    }
                                                    if($qty_masuk <= 0){
                                                        echo "<td class='fs-11 text-center' style='vertical-align:top;'> 0 </td>";
                                                    }else{
                                                        echo "<td class='fs-11 text-center' style='vertical-align:top;'>".number_format($qty_masuk)."</td>";
                                                    }
                                                    if($qty_keluar <= 0){
                                                        echo "<td class='fs-11 text-center' style='vertical-align:top;'> 0 </td>";
                                                    }else{
                                                        echo "<td class='fs-11 text-center' style='vertical-align:top;'>" . number_format($qty_keluar) . "</td>";
                                                    }
                                                    if($qty_akhir <= 0){
                                                        echo "<td class='fs-11 text-center' style='vertical-align:top;'> 0 </td>";
                                                    }else{
                                                        echo "<td class='fs-11 text-center' style='vertical-align:top;'>".number_format($qty_akhir)."</td>";
                                                    }
                                                    
                                                    // if( $row_pakai['keluar'] == ''){
                                                    //     echo "<td class='fs-11' style='vertical-align:top;'> 0 </td>";
                                                    // }else{
                                                    //     echo "<td class='fs-11' style='vertical-align:top;'>".number_format($row_pakai['keluar'])."</td>";
                                                    // }
                                                    
                                                    // if($row_stock1['stock1'] == ''){
                                                    //     echo "<td class='fs-11' style='vertical-align:top;'> 0 </td>";
                                                    // }else{
                                                    //     echo "<td class='fs-11' style='vertical-align:top;'>".number_format($row_stock1['stock1'])."</td>";
                                                    // }
                                                    
                                                    // if($row_pakai1['keluar1'] == '' ){
                                                    //     echo "<td class='fs-11' style='vertical-align:top;'> 0 </td>";
                                                    // }else{
                                                    //     echo "<td class='fs-11' style='vertical-align:top;'>".number_format($row_pakai1['keluar1'])."</td>";
                                                    // }
                                                    
                                            echo '
                                                    </tr>
                                                    ';
                                                    // <td class="fs-11" style="vertical-align:top;">' . $row['pic'] . '</td>
                                                    // <td class="fs-11" style="vertical-align:top;">' . $row['line'] . '</td>
                                                    // <td class="fs-11" style="vertical-align:top;">' . $querySku['nama_sku'] . ' - ' . $querySku['warna'] . ' - ' . $querySku['ukuran'] . '</td>
                                                    // <td class="fs-11" style="vertical-align:top;">' . $row['qty_jadi'] . '</td>
                                                    // <td class="fs-11" style="vertical-align:top;">' . $row['bs'] . '</td>
                                                    // <td class="fs-11" style="vertical-align:top;">' . $row['turun'] . '</td>
                                                    // <td class="fs-11" style="vertical-align:top;">' . $row['btj'] . '</td>
                                            // $ttlQtyjadi += $row['qty_jadi'];
                                            // $ttlBs += $row['bs'];
                                            // $ttlTurun += $row['turun'];
                                            // $ttlBtj += $row['btj'];
                                            $no++;
                                        }
                                        // echo '
                                        //         <tr>
                                        //             <td class="fs-11" colspan="5" style="text-align:end;font-weight:bold;">Total : </td>
                                        //             <td class="fs-11" style="text-align:start;font-weight:bold;">' . $ttlQtyjadi . '</td>
                                        //             <td class="fs-11" style="text-align:start;font-weight:bold;">' . $ttlBs . '</td>
                                        //             <td class="fs-11" style="text-align:start;font-weight:bold; ">' . $ttlTurun . '</td>
                                        //             <td class="fs-11" style="text-align:start;font-weight:bold; ">' . $ttlBtj . '</td>
                                        //         </tr>
                                        //     ';
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

            <!-- Footer -->


        </div>
        <!-- End of Content Wrapper -->

    </div>

    <!-- <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Master Beban</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>ID Beban</label>
                            <input type="text" name="id_beban" id="" class="form-control filled-input id_beban" value="<?php echo $id_beban; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nama Beban</label>
                            <input type="text" name="nama_beban" id="" class="form-control nama_beban" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-primary" name="save" value="Simpan">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Invoice</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin invoice ini ?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-danger" name="delete" value="Hapus">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ChooseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Cabang</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span style="" class="form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Cabang</span>
                            </div>

                            <select id="" class="form-control form-control-sm custom-select custom-select-sm branch_modal" name="branch">
                                <?php

                                $where_branch = "";
                                if ($_SESSION['group'] == "franchise") {
                                    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
                                    $where_branch = " WHERE id_cabang IN ('" . $filter_cabang . "')";
                                }

                                $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang " . $where_branch . " ORDER BY id_cabang ASC");
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
    </div> -->

    <!-- Bootstrap core JavaScript-->
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
<script type="text/javascript">
    $(document).ready(function() {

        $(document).on("click", ".choose_branch", function() {
            var branch = $(".branch_modal").val();
            window.location.replace("master_barang_masuk_edit.php?id_transaksi=new&id_cabang=" + branch);
        });

        $(document).on("click", ".hapus_button", function() {
            var id_hapus = $(this).data('id_hapus');
            $(".id_hapus").val(id_hapus);
        });

        // $(document).on("keyup", ".nama_barang", function() {
        //     var nama_barang = $(this).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_barang_masuk.php",
        //         data: {
        //             "cari_nama_barang": nama_barang
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".list_barang").html(result);
        //         }
        //     });
        // });

        // $(document).on("keyup", ".barcode", function() {
        //     var barcode = $(this).val();
        //     $.ajax({
        //         type: "POST",
        //         url: "ajax/ajax_barang_masuk.php",
        //         data: {
        //             "cari_barcode": barcode
        //         },
        //         cache: true,
        //         success: function(result) {
        //             $(".list_barcode").html(result);
        //             console.log(result);
        //         }
        //     });
        // });
        $(document).on("keyup", ".nama_pic", function() {
            var list_pic = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_sewing.php",
                data: {
                    "cari_nama_pic": list_pic
                },
                cache: true,
                success: function(result) {
                    $(".list_pic").html(result);
                }
            });
        });

        $(document).on("keyup", ".nama_wo", function() {
            var list_wo = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_sewing.php",
                data: {
                    "cari_nama_wo": list_wo
                },
                cache: true,
                success: function(result) {
                    $(".list_wo").html(result);
                }
            });
        });

        $(document).on("keyup", ".line", function() {
            var list_line = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_sewing.php",
                data: {
                    "cari_nama_line": list_line
                },
                cache: true,
                success: function(result) {
                    $(".list_line").html(result);
                }
            });
        });
    });
</script>