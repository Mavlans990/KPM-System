<?php 
session_start();
include "../lib/koneksi.php";
include "../lib/format.php";
include "../lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$tgl_from = "";
$tgl_to = "";

    $tgl_from = $_GET['tgl_from'];
    $tgl_to = $_GET['tgl_to'];


$where_tgl = "";
$where_invoice_id = "";
$where_jatuh_tempo = "";
$where_cust_nm = "";

    if ($tgl_from !== "" && $tgl_to !== "") {
        $where_tgl = " AND tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "'";
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
    <link rel="shortcut icon" href="../favicon.ico">
    <link rel="icon" href="../favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="../vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="../vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- select2 CSS -->
    <link href="../vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="../vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="../vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="../dist/css/style.css" rel="stylesheet" type="text/css">
</head>

<body id="page-top">

    <!-- Page Wrapper -->


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
                        </div>
                        <?php
                            echo'
                            <div class="card-body">';

                            $sql_get_customer = "select a.id_transaksi,a.id_customer,c.nama_customer from tb_barang_keluar a JOIN tb_customer c on c.id_customer = a.id_customer
                            WHERE  id_invoice = '' AND id_transaksi LIKE '%SO%' ".$where_tgl."
                            group by a.id_customer 
                            order by id_transaksi asc
                            ";
                                $data = mysqli_query($conn, $sql_get_customer);
                                while ($row_customer = mysqli_fetch_array($data)) {
                                    $penerimaan = 0;
                           ?> 
                    <div class="table-responsive">
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

                               $total_belum = 0;
                               $sql_get_piutang = "select a.id_transaksi,a.tgl_transaksi,a.dibuat_oleh,sum(total) as total from tb_barang_keluar a JOIN tb_customer c on c.id_customer = a.id_customer
                               WHERE  id_invoice = '' AND id_transaksi LIKE '%SO%' AND a.id_customer = '".$row_customer['id_customer']."' ".$where_tgl." 
                               order by id_transaksi asc
                               ";
                                   $data4 = mysqli_query($conn, $sql_get_piutang);
                                   while ($row_piutang = mysqli_fetch_array($data4)) {
                                    $total_belum += $row_piutang['total'];
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
                                    <th colspan="8">FAKTUR YANG SUDAH DITAGIH</th>
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
                               id_invoice,disc_invoice,total,tgl_transaksi,SUM(total) as subtotal
                               from tb_barang_keluar
                               where id_invoice <> '' and id_invoice <> 'temp' AND id_customer = '".$row_customer['id_customer']."' ".$where_tgl."
                               GROUP BY id_invoice
                               ");
                               while ($row_total = mysqli_fetch_array($sql_total)) {
                                $id_transaksi = '';
                                $total = '';
                                $tgl_faktur = '';
                                $detail_payment = "";
                                $total_diskon=($row_total['disc_invoice']/100)*$row_total['subtotal'];
                                $total_faktur = $row_total['subtotal'] - $total_diskon;
                                $total_sudah += $total_faktur;
                                $sql2 = mysqli_query($conn, "select *
                                from inv_payment a
                                join m_akun b on b.kode_akun = a.metode 
                                where id_inv = '" . $row_total['id_invoice'] . "' 
                              
                                ");
                                while ($row2 = mysqli_fetch_array($sql2)) {
                                    $penerimaan = $row2['pay'];
                                    $total_penerimaan += $penerimaan;
                                }
                                       echo' 
                                        <tr>
                                        <td></td>
                                        <td>' . $row_total['id_invoice'] . '</td>';
                                        $sql = mysqli_query($conn, "select 
                                        id_invoice,subtotal_invoice,total_invoice,disc_invoice,cust_invoice,tgl_jatuh_tempo,id_transaksi,tgl_transaksi,total
                                        from tb_barang_keluar
                                        where id_invoice = '".$row_total['id_invoice']."' AND id_customer = '".$row_customer['id_customer']."' ".$where_tgl."
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
                                        <td  style="text-align:right;">' . money($penerimaan) . '</td>
                                    </tr>
                                    ';
                                }
                                echo '                                        
                                <tr>
                                <td colspan="7" style="text-align:right;">Grand Total</td>
                                <td  style="text-align:right;">' . money($total_sudah) . '</td>
                                <td  style="text-align:right;">' . money($total_penerimaan) . '</td>
                            </tr>';
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php }?>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->



    <!-- Bootstrap core JavaScript-->
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

    <!-- Select2 JavaScript -->
    <script src="../vendors/select2/dist/js/select2.full.min.js"></script>
    <script src="../dist/js/select2-data.js"></script>

    <!-- Init JavaScript -->
    <script src="../dist/js/init.js"></script>

    <!-- Page level plugins -->
    <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../js/demo/datatables-demo.js"></script>
</body>

</html>
<script type="text/javascript">
        window.print();
</script>