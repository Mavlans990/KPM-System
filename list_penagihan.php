<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

$id_bahan = generate_bahan();
$total_belum_bayar_2 = '';
if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

if (isset($_POST['terima_bayar'])) {

    $query = mysqli_query($conn, "insert into inv_payment(id_inv,metode,pay,tgl_terima,tgl_pay) 
                                values ('" . $_POST['id_inv'] . "','" . $_POST['metode'] . "','" . $_POST['pay'] . "','" . $_POST['tgl_terima'] . "','" . date('Y-m-d H:i:s') . "')  ");

    if ($query) {
        $jurnal_1 = add_jurnal('',  $_POST['metode'], '1-1011', $_POST['pay'], $_POST['tgl_terima'], "Penerimaan Pembayaran " . $_POST['id_inv'] . "", $_SESSION['id_user']);
        $id_jurnal = explode("|", $jurnal_1);
        $sts_jurnal = $id_jurnal[0];
        $id_jurnal = $id_jurnal[1];
    }
}

if (isset($_POST['delete'])) {
    $valid = 1;
    if ($valid == 1) {
        $query = mysqli_query($conn, "DELETE FROM po2 WHERE id_po1='" . $_POST['id_hapus'] . "'");
        if (!$query) {
            $valid = 0;
            $msg = "ERROR : Delete PO2 Failed";
        }
    }
    if ($valid == 1) {
        $query = mysqli_query($conn, "DELETE FROM po1 WHERE id_po1='" . $_POST['id_hapus'] . "'");
        if (!$query) {
            $valid = 0;
            $msg = "ERROR : Delete PO1 Failed";
        }
    }
    if ($valid == 1) {
        $query = mysqli_query($conn, "DELETE FROM tb_stock WHERE id_transaksi = '" . $_POST['id_hapus'] . "'");
    }

    if ($valid == 0) {
        rollback();
    } else {
        commit();
        $msg = "Delete Data Success";
    }

    echo "<script type='text/javascript'>alert('" . $msg . "');window.location.href='stock_in.php';</script>";
}

$tgl_from = "";
$tgl_to = "";
$invoice_id = "";
$tgl_jatuh_tempo = "";
$cust_nm = "";
if (isset($_POST['search'])) {
    $tgl_from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $tgl_to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
}

$where_tgl = "";
$where_invoice_id = "";
$where_jatuh_tempo = "";
$where_cust_nm = "";
// $cari_belum = mysqli_fetch_array(mysqli_query($conn, "SELECT SUM(pay) AS pay FROM inv_payment a WHERE a.id_transaksi LIKE %INV% "));
if (isset($_POST['search'])) {
    if ($tgl_from !== "" && $tgl_to !== "") {
        $where_tgl = " AND tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "'";
    }
    if($_POST['nama_customer'] != ''){
        $ex_customer = explode(" | ", $nama_customer);
        $id_customer = $ex_customer[0];
        $where_customer = "AND a.id_customer ='" . $id_customer . "' ";
        $cari_customer =  $id_customer; 
    }else{
        $cari_customer =  ""; 
        $where_customer = "";
    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Invoice System</title>
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
                                
                                <h3 class="mr-2">Laporan Piutang</h3>
                        
                                <!-- <a href="javascript:void(0);" class="btn btn-success form-control-sm" data-toggle="modal" data-target="#ImportModal"><i class="fa fa-download"></i> Import CSV</a> -->
                            </div>
                            <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
                            <table class="mt-15">
                                <tr>
                                    <td style="width:540px;">Tanggal Faktur :</td>
                                </tr>
                            </table>
                                <div class="form-group form-inline">
                                    <input type="date" name="tgl_from" id="" class="form-control form-control-sm" value="<?= $tgl_from; ?>">
                                    <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                    <input type="date" name="tgl_to" id="" class="form-control form-control-sm" value="<?= $tgl_to; ?>">
                                    <input type="text" name="nama_customer" id="" class="form-control form-control-sm nama_customer" value="" autocomplete="off" list="list_customer" onclick="this.select();">
                                    <datalist id="list_customer" class="list_customer">
                                    </datalist>
                                    <button type="submit" class="btn btn-sm btn-primary ml-5" name="search"><i class="fa fa-search"></i> Cari</button>
                                    <?php
                                            if (isset($_POST['search'])) {
                                                echo '
                                                        <a href="print/list_penagihan_print_baru.php?tgl_from=' . $tgl_from . '&tgl_to=' . $tgl_to . '&nama_customer=' . $cari_customer . '" class="btn btn-success text-white form-control-sm mt--5 ml-1" target="_blank"><i class="fa fa-print"></i> Print</a>
                                                        <a href="print/print_excel_laporan_piutang.php?tgl_from=' . $tgl_from . '&tgl_to=' . $tgl_to . '&nama_customer=' . $cari_customer . '" target="-blank" class="btn btn-warning text-white form-control-sm mt--5 ml-1"><i class="fa fa-list"></i> Download Excel</a>
                                                    ';
                                            }
                                            // '&nama_customer=' . $cari_customer . 
                                            ?>
                                </div>
                            </form>
                        </div>
                        <?php
                        if (isset($_POST['search'])) {
                            echo'
                        <div class="card-body">';
                                    $total_keseluruhan_belum_bayar = 0;
                                    $sql_get_customer = "select a.id_transaksi,a.id_customer,c.nama_customer from tb_barang_keluar a JOIN tb_customer c on c.id_customer = a.id_customer
                                    WHERE id_transaksi LIKE '%SO%' ".$where_tgl." " . $where_customer . "
                                    group by a.id_customer 
                                    order by id_transaksi asc
                                    ";
                                        $data = mysqli_query($conn, $sql_get_customer);
                                        while ($row_customer = mysqli_fetch_array($data)) {
                                            $penerimaan = 0;
                                            $cek_utang = 0;
                                            $cek_bayar = 0;
                                        $sql_cek = mysqli_query($conn, "SELECT SUM(total) AS total,id_invoice FROM tb_barang_keluar WHERE id_customer = '".$row_customer['id_customer']."' ".$where_tgl." GROUP BY id_invoice");
                                        while($row_cek = mysqli_fetch_array($sql_cek)){
                                            $cek_utang += $row_cek['total'];
                                            $sql2 = mysqli_query($conn, "select SUM(pay) AS bayar
                                            from inv_payment a
                                            where id_inv = '" . $row_cek['id_invoice'] . "' 
                                            ");
                                            while($row_sql = mysqli_fetch_array($sql2)){
                                                $cek_bayar += $row_sql['bayar'];
                                            }
                                        }
                                        // echo $cek_utang;
                                        // echo "  ";
                                        // echo $cek_bayar;
                                        $dnone = '';
                                        if($cek_utang == $cek_bayar){
                                            $dnone = 'd-none';
                                        }
                                   ?> 
                            <div class="table-responsive <?= $dnone ?>">
                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                <thead>
                                        <tr style="font-size:9px;">
                                            <th>CUSTOMER</th>
                                            <th colspan="3">FAKTUR YANG BELUM DITAGIH</th>
                                        </tr>
                                        <tr style="font-size:9px;">
                                            <th><?= $row_customer['nama_customer']?></th>
                                            <th>NO FAKTUR</th>
                                            <th>TGL FAKTUR</th>
                                            <th>TOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                       $total_sudah = 0 ;
                                       $total_penerimaan = 0;
                                       $coba = 0;    
                                       $total_belum = 0;
                                       $total_belum_bayar = 0;
                                       $total_all_belum_bayar = 0;
                                       $sql_get_piutang = "select a.id_transaksi,a.tgl_transaksi,a.dibuat_oleh,sum(total) as total from tb_barang_keluar a JOIN tb_customer c on c.id_customer = a.id_customer
                                       WHERE  id_invoice = '' AND id_transaksi LIKE '%SO%' AND a.id_customer = '".$row_customer['id_customer']."' ".$where_tgl." " . $where_customer . "
                                       GROUP by id_transaksi asc
                                       ";
                                           $data4 = mysqli_query($conn, $sql_get_piutang);
                                           while ($row_piutang = mysqli_fetch_array($data4)) {
                                            $total_belum += $row_piutang['total'];
                                            $coba += $row_piutang['total'];
                                            echo '
                                                <tr>
                                                <td></td>
                                                <td>' . $row_piutang['id_transaksi'] . '</td>
                                                <td>' .  date('d/m/Y', strtotime($row_piutang['tgl_transaksi'])) . '</td>
                                                <td  style="text-align:right;">' . money($row_piutang['total']) . '</td>
                                            </tr>
                                            ';
                                        }
                                        echo '
                                        <tr>
                                                <td colspan="3" style="text-align:right;">Grand Total</td>
                                                <td  style="text-align:right;">' . money($total_belum) . '</td>
                                            </tr>
                                        ';
                                        ?>
                                    </tbody>
                                    <thead>
                                        <tr style="font-size:9px;">
                                        <th></th>
                                            <th colspan="9">FAKTUR YANG SUDAH DITAGIH</th>
                                        </tr>
                                        <tr style="font-size:9px;">
                                            <th></th>
                                            <th>NO INVOICE</th>
                                            <th>NO FAKTUR</th>
                                            <th>TGL FAKTUR</th>
                                            <th>NILAI FAKTUR</th>
                                            <th>SUBTOTAL</th>
                                            <th>DISC</th>
                                            <th>TOTAL</th>
                                            <th>BELUM BAYAR</th>
                                            <th>SUDAH BAYAR</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_sudah = 0 ;
                                        $total_penerimaan = 0;
                                        $total_faktur = 0;
                                        $total_diskon = 0;
                                       $sql_total = mysqli_query($conn, "select 
                                       a.id_invoice,a.disc_invoice,a.total,a.tgl_transaksi,SUM(total) as subtotal
                                       from tb_barang_keluar a
                                       JOIN tb_customer c on c.id_customer = a.id_customer
                                       where a.id_invoice <> '' and a.id_invoice <> 'temp' AND a.id_customer = '".$row_customer['id_customer']."' ".$where_tgl." " . $where_customer . "
                                       GROUP BY id_invoice
                                       ");
                                       while ($row_total = mysqli_fetch_array($sql_total)) {
                                        $id_transaksi = '';
                                        $total = '';
                                        $tgl_faktur = '';
                                        $detail_payment = "";
                                        $total_belum_bayar = 0;
                                        $total_diskon=($row_total['disc_invoice']/100)*$row_total['subtotal'];
                                        $total_faktur = $row_total['subtotal'] - $total_diskon;
                                        $sql2 = mysqli_query($conn, "select *
                                        from inv_payment a
                                        join m_akun b on b.kode_akun = a.metode 
                                        where id_inv = '" . $row_total['id_invoice'] . "' 
                                      
                                        ");
                                        $ppt = 0;
                                        $bayar = 0;
                                        $total_belum = 0;
                                        
                                        while ($row2 = mysqli_fetch_array($sql2)) {
                                            $penerimaan = $row2['pay'];
                                            $ppt += $row2['pay'];
                                            
                                        }
                                        

                                        
                                        if($ppt < $total_faktur){
                                        $total_sudah += $total_faktur;
                                        $total_penerimaan += $ppt;
                                        $total_sudah_2 = $total_faktur;
                                        $total_penerimaan_2 = $ppt;
                                        $total_belum_bayar_2 += $total_sudah_2 - $total_penerimaan_2;
                                        $total_belum_bayar += $total_sudah - $total_penerimaan;
                                        $total_all_belum_bayar += $total_belum_bayar_2;
                                        $total_belum = $row_total['subtotal'] - $ppt;
                                        $bayar += $ppt;
                                        echo' 
                                        <tr>
                                                <td></td>
                                                <td>' . $row_total['id_invoice'] . '</td>';
                                                $sql = mysqli_query($conn, "select 
                                                a.id_invoice,a.subtotal_invoice,a.total_invoice,a.disc_invoice,a.cust_invoice,a.tgl_jatuh_tempo,a.id_transaksi,a.tgl_transaksi,a.total
                                                from tb_barang_keluar a
                                                JOIN tb_customer c on c.id_customer = a.id_customer
                                                where a.id_invoice = '".$row_total['id_invoice']."' AND a.id_customer = '".$row_customer['id_customer']."' ".$where_tgl." " . $where_customer . "
                                                ");
                                                    while ($row = mysqli_fetch_array($sql)) {
                                                        $id_transaksi = $id_transaksi.$row['id_transaksi'] . '<br>';
                                                        $total = $total.money($row['total']). '<br>';
                                                        $tgl_faktur = $tgl_faktur. date('d/m/Y', strtotime($row['tgl_transaksi'])). '<br>';
                                                    }
                                                     echo '
                                                <td>' .  $id_transaksi . '</td>
                                                <td>' .  $tgl_faktur . '</td>
                                                <td>' .  $total . '</td>
                                                <td>' .  money($row_total['subtotal']) . '</td>
                                                <td>' .  round($row_total['disc_invoice'],2) . '%</td>
                                                <td style="text-align:right;">' . money($total_faktur) . '</td>
                                                <td  style="text-align:right;">' . money($total_belum) . '</td>
                                                <td  style="text-align:right;">' . money($bayar) . '</td>
                                                </tr>
                                                ';
                                                
                                            
                                            $total_keseluruhan_belum_bayar = $total_all_belum_bayar;
                                        }
                                    }
                                            echo '                                        
                                        <tr>
                                        <td colspan="7" style="text-align:right;">Grand Total</td>
                                        <td  style="text-align:right;">' . money($total_sudah) . '</td>
                                        <td  style="text-align:right;">' . money($total_sudah-$total_penerimaan) . '</td>
                                        <td  style="text-align:right;">' . money($total_penerimaan) . '</td>
                                        </tr>';
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php }}?>
                            <?php
                                if (isset($_POST['search'])) {
                            ?>
                            <div class="table-responsive">
                                <table class="table-bordered table table-sm w-100 display tb_jps_re_ins tb_jps_ins  mt-15">
                                    <thead>
                                        <tr>
                                            <th style="text-align:right;font-size:16px;">Total Belum Bayar</th>  
                                            <th style="text-align:right;"><?= money($total_keseluruhan_belum_bayar) ?></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <?php } ?>
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

    <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Purchase Order</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Delete this Purchase Order?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-danger" name="delete" value="Delete">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="terima_pembayaran" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Terima Pembayaran</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">

                        <input type="text" readonlye class="form-control filled-input id_inv" name="id_inv" readonly>

                        <input type="date" class="form-control tgl_terima" name="tgl_terima" value="<?php echo date('Y-m-d'); ?>">

                        <select name="metode" id="" class="form-control form-control-sm metode" required>
                            <?php
                            $sql_get_akun = mysqli_query($conn, "
                                                        SELECT 
                                                            kode_akun,
                                                            nm_akun
                                                        FROM
                                                            m_akun
                                                        WHERE
                                                            kat_akun = '3'
                                                        ORDER BY m_akun_id ASC
                                                    ");
                            while ($row_akun = mysqli_fetch_array($sql_get_akun)) {
                                $kode_akun = $row_akun['kode_akun'];
                                $nm_akun = $row_akun['nm_akun'];
                                echo '
                                                            <option value="' . $kode_akun . '">' . $nm_akun . '</option>
                                                        ';
                            }
                            ?>
                        </select>

                        <input type="number" class="form-control pay" name="pay" placeholder="Jumlah Diterima">

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <input type="submit" class="btn btn-success" name="terima_bayar" value="Terima Pembayaran">
                    </div>
                </form>
            </div>
        </div>
    </div>

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

// $(document).on("click", ".choose_branch", function() {
//     var branch = $(".branch_modal").val();
//     window.location.replace("master_barang_keluar_edit.php?id_transaksi=new&id_cabang=" + branch);
// });

// $(document).on("click", ".hapus_button", function() {
//     var id_transaksi = $(this).data('id_transaksi');
//     $(".id_hapus").val(id_transaksi);
// });

$(document).on("keyup", ".nama_customer", function() {
    var nama_customer = $(this).val();
    $.ajax({
        type: "POST",
        url: "ajax/ajax_barang_keluar.php",
        data: {
            "cari_nama_customer_piutang": nama_customer
        },
        cache: true,
        success: function(result) {
            $(".list_customer").html(result);
        }
    });
});

});
</script>