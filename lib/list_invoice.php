<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

$id_bahan = generate_bahan();

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

if (isset($_POST['terima_bayar'])) {

    $query = mysqli_query($conn, "insert into inv_payment(id_inv,metode,pay,tgl_terima,tgl_pay) 
    values ('" . $_POST['id_inv'] . "','" . $_POST['metode'] . "','" . $_POST['pay'] . "','" . $_POST['tgl_terima'] . "','" . date('Y-m-d H:i:s') . "')  ");
    $bayar = $_POST['pay'];
    $hitung_ongkos = 0;
    $hitung_diskon = 0;
    $ongkos = 0;
    $diskon = 0;
    $total = 0;
    $subtotal = 0;
    $piutang = 0;
    $akun = '';
    $query_sql = mysqli_query($conn, "SELECT a.disc_invoice,b.metode,a.ongkos_kirim,SUM(a.subtotal) as subtotal,SUM(a.total) as total FROM tb_barang_keluar a JOIN inv_payment b ON b.id_inv = a.id_invoice  WHERE a.id_invoice = '" . $_POST['id_inv'] . "'");
    if($row_sql = mysqli_fetch_array($query_sql)){
        $ongkos = $row_sql['ongkos_kirim'];
        $diskon = $row_sql['total'] * ($row_sql['disc_invoice']/100);
        $total = $row_sql['total'];
        $subtotal = $row_sql['subtotal'];
        $akun = $row_sql['metode'];
        $hitung_ongkos = $bayar / $total * $ongkos;
        $hitung_diskon = $bayar / $total * $diskon;
        $piutang = $bayar / $total * $subtotal;
    }
    if ($query) {
        if($diskon == 0 && $ongkos == 0){
        $jurnal_1 = add_jurnal('',  $_POST['metode'], '1-1011', $_POST['pay'], $_POST['tgl_terima'], "Penerimaan Pembayaran " . $_POST['id_inv'] . "", $_SESSION['id_user']);
        // $id_jurnal = explode("|", $jurnal_1);
        // $sts_jurnal = $id_jurnal[0];
        // $id_jurnal = $id_jurnal[1];
        }elseif($diskon != 0 && $ongkos == 0){
            $jurnal_1 = add_jurnal_2debit('',  $_POST['metode'],'6-6028','1-1011', $bayar,$hitung_diskon,$piutang, $_POST['tgl_terima'], "Penerimaan Pembayaran ".$_POST['id_inv']."", $_SESSION['id_user']);
        }elseif($ongkos != 0 && $diskon == 0){
            $jurnal_1 = add_jurnal_2debit('',  $_POST['metode'],'6-6023','1-1011', $bayar,$hitung_ongkos,$piutang, $_POST['tgl_terima'], "Penerimaan Pembayaran ".$_POST['id_inv']."", $_SESSION['id_user']);
        }elseif($ongkos != 0 && $diskon != 0){
            $jurnal_1 = add_jurnal_3debit('',  $_POST['metode'],'6-6023','1-1011','6-6028', $bayar,$hitung_ongkos,$piutang,$hitung_diskon, $_POST['tgl_terima'], "Penerimaan Pembayaran ".$_POST['id_inv']."", $_SESSION['id_user']);
        }
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
    $invoice_id = mysqli_real_escape_string($conn, $_POST['invoice_id']);
    $tgl_jatuh_tempo = mysqli_real_escape_string($conn, $_POST['tgl_jatuh_tempo']);
    $cust_nm = mysqli_real_escape_string($conn, $_POST['cust_nm']);
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
                                <h3 class="mr-2">List Invoice</h3>
                                <a href="pelunasan_piutang.php" class="btn btn-primary form-control-sm mr-2 ml--5"><i class="fa fa-plus"></i> Buat Invoice Baru</a>
                                <!-- <a href="javascript:void(0);" class="btn btn-success form-control-sm" data-toggle="modal" data-target="#ImportModal"><i class="fa fa-download"></i> Import CSV</a> -->
                            </div>
                            <form action="<?= $_SERVER['REQUEST_URI']; ?>" method="post">
                            <table class="mt-15">
                                <tr>
                                    <td style="width:540px;">Tanggal Invoice :</td>
                                    <td>Tanggal Penagihan :</td>
                                </tr>
                            </table>
                                <div class="form-group form-inline">
                                    <input type="date" name="tgl_from" id="" class="form-control form-control-sm" value="<?= $tgl_from; ?>">
                                    <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                    <input type="date" name="tgl_to" id="" class="form-control form-control-sm" value="<?= $tgl_to; ?>">
                                    <input type="text" name="invoice_id" id="id_invoice" class="form-control form-control-sm mt--5" placeholder="ID Invoice" value="<?= $invoice_id; ?>">
                                    <input type="date" name="tgl_jatuh_tempo" id="tgl_jatuh_tempo" class="form-control form-control-sm mt--5" placeholder="Tgl Jatuh Tempo" value="<?= $tgl_jatuh_tempo; ?>">
                                    <input type="text" name="cust_nm" id="nama_customer" class="form-control form-control-sm mt--5" placeholder="Nama Customer" list="list_cust" value="<?= $cust_nm; ?>">
                                    <datalist id="list_cust">
                                        <?php
                                        $sql_get_cust = mysqli_query($conn, "SELECT * FROM tb_customer ORDER BY nama_customer ASC");
                                        while ($row_cust = mysqli_fetch_array($sql_get_cust)) {
                                            echo '
                                                    <option value="' . $row_cust['nama_customer'] . ' | ' . $row_cust['id_customer'] . '">
                                                ';
                                        }
                                        ?>
                                    </datalist>
                                    <button type="submit" class="btn btn-sm btn-primary ml-5" name="search"><i class="fa fa-search"></i> Cari</button>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                                    <thead>
                                        <tr style="font-size:9px;">
                                            <th>NO</th>
                                            <th>NO INVOICE</th>
                                            <th>TGL INVOICE</th>
                                            <th>JATUH TEMPO</th>
                                            <th>KEPADA</th>
                                            <th>SUBTOTAL</th>
                                            <th>DISC</th>
                                            <th>DISC ONGKOS</th>
                                            <th>TOTAL</th>
                                            <th>PENERIMAAN</th>
                                            <th>ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $where_tgl = "";
                                        $where_invoice_id = "";
                                        $where_jatuh_tempo = "";
                                        $where_cust_nm = "";
                                        if (isset($_POST['search'])) {
                                            if ($tgl_from !== "" && $tgl_to !== "") {
                                                $where_tgl = " AND tgl_invoice BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "'";
                                            }
                                            if ($invoice_id !== "") {
                                                $where_invoice_id = " AND id_invoice = '" . $invoice_id . "'";
                                            }
                                            if ($tgl_jatuh_tempo !== "") {
                                                $where_jatuh_tempo = " AND tgl_jatuh_tempo = '" . $tgl_jatuh_tempo . "'";
                                            }
                                            if ($where_cust_nm !== "") {
                                                if (strpos($cust_nm, " | ") !== FALSE) {
                                                    $id_cust = explode(" | ", $cust_nm);
                                                    $id_cust = $id_cust[1];
                                                    $where_cust_nm = " AND cust_invoice = '" . $id_cust . "'";
                                                }
                                            }
                                        }
                                        $nom = 1;
                                        $sql = mysqli_query($conn, "select 
                                        id_invoice,tgl_invoice,subtotal_invoice,total_invoice,disc_invoice,ongkos,cust_invoice,tgl_jatuh_tempo
                                        from tb_barang_keluar
                                        where id_invoice <> '' and id_invoice <> 'temp' 
                                        " . $where_tgl . "
                                        " . $where_invoice_id . "
                                        " . $where_jatuh_tempo . "
                                        " . $where_cust_nm . "
                                        group by id_invoice
                                        ");
                                        while ($row = mysqli_fetch_array($sql)) {

                                            $penerimaan = 0;
                                            $detail_payment = "";
                                            $sql2 = mysqli_query($conn, "select *
                                            from inv_payment a
                                            join m_akun b on b.kode_akun = a.metode 
                                            where id_inv = '" . $row['id_invoice'] . "' 
                                          
                                            ");
                                            while ($row2 = mysqli_fetch_array($sql2)) {
                                                $penerimaan = $penerimaan + $row2['pay'];
                                                $detail_payment = $detail_payment . $row2['tgl_terima'] . " / " . $row2['nm_akun'] . " / " . money($row2['pay']) . "<br>";
                                            }
                                            $detail_payment = "<p style='font-size:10px;'>" . $detail_payment . "</p>";

                                            $sisa_bayar = $row['total_invoice'] - $penerimaan;

                                            $nama_customer = "";
                                            $sql_get_customer = mysqli_query($conn, "SELECT * FROM tb_customer WHERE id_customer = '" . $row['cust_invoice'] . "'");
                                            if ($data_customer = mysqli_fetch_array($sql_get_customer)) {
                                                $nama_customer = $data_customer['nama_customer'];
                                            }

                                            echo '
                                                <tr>
                                                <td>' . $nom . '</td>
                                                <td>' . $row['id_invoice'] . '</td>
                                                <td>' .  date('d/m/Y', strtotime($row['tgl_invoice'])) . '</td>
                                                <td>' .  date('d/m/Y', strtotime($row['tgl_jatuh_tempo'])) . '</td>
                                                <td>
                                                ' . $nama_customer . '</td>
                                                <td style="text-align:right;">' . money($row['subtotal_invoice']) . '</td>
                                                <td style="text-align:right;">' . $row['disc_invoice'] . '%</td>
                                                <td style="text-align:right;">' . $row['ongkos'] . '</td>
                                                <td  style="text-align:right;">' . money($row['total_invoice']) . '</td>
                                              
                                                <td  style="text-align:right;">' . $detail_payment . " Total : " . money($penerimaan) . '</td>
                                                
                                                <td>
                                                    <a href="pelunasan_piutang.php?id_invoice=' . $row['id_invoice'] . '&view=detail" class="btn btn-xs btn-info" target="_blank">
                                                    <i class="fa fa-table"></i> view </a>
                                                ';

                                            if ($sisa_bayar <= 0) {
                                                echo '<i class="fa fa-check"></i> Lunas';
                                            } else {
                                                echo '<button data-sisa_bayar="' . $sisa_bayar . '" data-id_invoice="' . $row['id_invoice'] . '" class="btn btn-xs btn-success terima_pembayaran" data-toggle="modal" data-target="#terima_pembayaran">
                                                    Terima Pembayaran </button>';
                                            }



                                            echo '    
                                                </td>
                                            </tr>
                                            ';
                                            $nom++;
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
    $(document).on("click", '.terima_pembayaran', function(e) {
        var id_invoice = $(this).data('id_invoice');
        var sisa_bayar = $(this).data('sisa_bayar');
        $(".id_inv").val(id_invoice);
        $(".pay").val(sisa_bayar);
    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>