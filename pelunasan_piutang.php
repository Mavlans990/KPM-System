<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

$id_bahan = generate_bahan();

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$tgl1 = date("Y-m-d");
$tgl2 = date("Y-m-d");
if (isset($_POST['cari'])) {
    $tgl1 = mysqli_real_escape_string($conn, $_POST['tgl_1']);
    $tgl2 = mysqli_real_escape_string($conn, $_POST['tgl_2']);
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
}


if (isset($_GET['del_sj'])) {
    $sql_update = "update tb_barang_keluar set id_invoice = '',
    tgl_invoice='',
    subtotal_invoice='',
    total_invoice='',
    disc_invoice='',
    cust_invoice=''
    where id_transaksi ='" . mysqli_real_escape_string($conn, $_GET['del_sj']) . "' ";
    $data = mysqli_query($conn, $sql_update);
}

if (isset($_POST['add_sj'])) {
    $buatsj = $_POST['buatsj'];
    for ($i = 0; $i < count($buatsj); $i++) {
        //echo "<script>alert('".$buatsj[$i]."');</script>";
        $sql_update = "update tb_barang_keluar set id_invoice = '" . mysqli_real_escape_string($conn, $_POST['id_invoice']) . "' 
        where id_transaksi ='" . $buatsj[$i] . "' ";
        $data = mysqli_query($conn, $sql_update);
    }
}

// if(isset($_POST['add_sj'])){
//     $customer = mysqli_real_escape_string($conn, $_POST['customer']);
// }


if (isset($_POST['otorisasi'])) {
    if ($_POST['id_invoice'] == "temp") {
        $id_invoice = generate_transaction_key('INV', 'INV', date('m'), date('Y'));
    } else {
        $id_invoice = $_POST['id_invoice'];
    }

    
        $id_customer = mysqli_real_escape_string($conn, $_POST['cust_invoice']);
        $id_cust = "";
        if ($id_customer !== "") {
            $ex_customer = explode(" | ", $id_customer);
            $id_cust = $ex_customer[0];
        }

    $sql_update = "update tb_barang_keluar set 
        id_invoice = '" . $id_invoice . "',
        tgl_invoice='" . mysqli_real_escape_string($conn, $_POST['tgl_invoice']) . "',
        subtotal_invoice='" . mysqli_real_escape_string($conn, $_POST['subtotal_invoice']) . "',
        total_invoice='" . mysqli_real_escape_string($conn, $_POST['total_invoice']) . "',
        disc_invoice='" . mysqli_real_escape_string($conn, $_POST['disc_invoice']) . "',
        ongkos='" . mysqli_real_escape_string($conn, $_POST['ongkos']) . "',
        cust_invoice='" . $id_cust . "'
        where id_invoice ='" . mysqli_real_escape_string($conn, $_POST['id_invoice']) . "' ";
    $data = mysqli_query($conn, $sql_update);

    header('Location: pelunasan_piutang.php?id_invoice=' . $id_invoice);
}


$tgl_invoice = "";
$nama_customer = "";
$subtotal_invoice = "";
$total_invoice = "";
$disc_invoice = 0;
$cabang_invoice = "";
$cust_invoice = "";
$nama_invoice = "";
// $ongkos_kirim = 0;
// $totaldiscongkos = 0;

if (!isset($_GET['id_invoice'])) {
    $id_invoice = "temp";
    $sql = mysqli_query($conn, "select tgl_invoice,SUM(subtotal) AS subtotal,ongkos_kirim,ppn,disc_invoice,cust_invoice
     from tb_barang_keluar where id_invoice = '" . $id_invoice . "' ");
     while ($row = mysqli_fetch_array($sql)) {
        // $totaldiscongkos += $row["ongkos_kirim"];
        // $ongkos_kirim = $totaldiscongkos;
    }
} else {
    $id_invoice = $_GET['id_invoice'];
    $sql = mysqli_query($conn, "select a.id_customer,a.tgl_invoice,SUM(subtotal) AS subtotal,a.ongkos_kirim,a.ppn,a.disc_invoice,a.cust_invoice,b.nama_customer
     from tb_barang_keluar a
     join tb_customer as b on a.id_customer = b.id_customer
     where id_invoice = '" . $id_invoice . "' ");
    while ($row = mysqli_fetch_array($sql)) {

        $tgl_invoice = $row['tgl_invoice'];
        $subtotal_invoice = $row['subtotal'];
        $total_invoice = $row['subtotal'];
        $disc_invoice = $row['disc_invoice'];
        $cust_invoice = $row['cust_invoice'];
        $nama_invoice = $row['nama_customer'];
        // $totaldiscongkos += $row["ongkos_kirim"];
        // $ongkos_kirim = $totaldiscongkos;
    }
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
            font-size: 14px !important;
        }

        th {
            color: black;
        }

        td {
            color: black;
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

                    <?php
                    $hide_tab_tagih = "";
                    if (isset($_GET['view'])) {
                        $hide_tab_tagih = "d-none";
                    }
                    ?>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4 <?= $hide_tab_tagih; ?>" style="background:#fff7f2;">
                        <div class="card-header py-3" style="text-transform: none;">
                            <div class="form-inline">
                                <h5 class="mr-2">Invoice Yang Belum Ditagih</h5>
                                <!-- <a href="javascript:void(0);" class="add_new btn btn-primary form-control-sm mr-2 ml--5" data-toggle="modal" data-target="#newBarangModal"><i class="fa fa-plus"></i> Add New</a> -->
                                <!-- <a href="javascript:void(0);" class="btn btn-success form-control-sm" data-toggle="modal" data-target="#ImportModal"><i class="fa fa-download"></i> Import CSV</a> -->
                            </div>
                        </div>
                        <div class="card-body" style="height:500px;overflow-y: auto;">
                            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="date" name="tgl_1" id="" class="form-control form-control-sm" value="<?php echo $tgl1; ?>" required>
                                        </div>
                                    </div>
                                    <p class="mt-10">S/D</p>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <input type="date" name="tgl_2" id="" class="form-control form-control-sm" value="<?php echo $tgl2; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Customer</span>
                                            </div>
                                            <input type="text" name="nama_customer" id="" class="form-control form-control-sm  nama_customer" value="<?= $nama_customer; ?>" autocomplete="off" list="list_customer" onclick="this.select();">
                                            <datalist id="list_customer" class="list_customer">
                                            </datalist>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <button type="submit" class="btn btn-sm btn-primary" name="cari"><i class="fa fa-search"></i> Cari</button>
                                    </div>
                                </div>
                            </form>

                            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                        <thead>
                                            <tr>
                                                <th class="fs-11">No Invoice</th>
                                                <th class="fs-11">Tgl Invoice</th>
                                                <th class="fs-11">Item</th>
                                                <th class="fs-11">Subtotal</th>
                                                <th class="fs-11">Disc Ongkos</th>
                                                <!-- <th class="fs-11">Total</th> -->

                                                <th class="text-center">Checklist</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $filter = "";
                                            $filter_customer = "";
                                            if (isset($_POST['cari'])) {
                                                $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
                                                $filter_customer = "";
                                                if ($nama_customer !== "") {
                                                    $ex_customer = explode(" | ", $nama_customer);
                                                    $nama_cust = $ex_customer[1];
                                                    $filter_customer = " AND nama_customer = '" . $nama_cust . "' ";
                                                }
                                                    $filter = "and a.tgl_transaksi>='" . mysqli_real_escape_string($conn, $_POST['tgl_1']) . "'  and a.tgl_transaksi<='" . mysqli_real_escape_string($conn, $_POST['tgl_2']) . "' ";

                                                // $filter = "and a.tgl_transaksi>='" . mysqli_real_escape_string($conn, $_POST['tgl_1']) . "'  and a.tgl_transaksi<='" . mysqli_real_escape_string($conn, $_POST['tgl_2']) . "' and c.nama_customer LIKE '%'". mysqli_real_escape_string($conn, $_POST['nama_customer']) ."'%' ";
                                            
                                        }


                                            $sql_get_piutang = "select a.id_transaksi,a.tgl_transaksi,a.dibuat_oleh,a.jenis_transaksi,a.ongkos_kirim,sum(total) as total,c.nama_customer from tb_barang_keluar a JOIN tb_customer c on c.id_customer = a.id_customer
                                        WHERE  id_invoice = '' AND id_transaksi LIKE '%SO%'
                                        " . $filter_customer . "
                                        " . $filter . "
                                        group by id_transaksi 
                                        order by id_transaksi asc
                                        ";
                                            $data4 = mysqli_query($conn, $sql_get_piutang);
                                            while ($row_piutang = mysqli_fetch_array($data4)) {

                                                $ttl_disc_ongkos = $row_piutang['total'] - $row_piutang['ongkos_kirim'];
                                                if($row_piutang["jenis_transaksi"] == "barang_masuk"){
                                                    $jns = " ";
                                                }elseif($row_piutang["jenis_transaksi"] == "return"){
                                                    $jns = " - (Return) ";
                                                }else{
                                                    $jns = " ";
                                                }

                                                $list_item = "";
                                                $sql_item = "select a.*,b.nama_bahan from tb_barang_keluar a JOIN tb_bahan b ON b.id_bahan = a.id_bahan where a.id_transaksi = '" . $row_piutang['id_transaksi'] . "' and a.harga <> 0 ";
                                                $data_item = mysqli_query($conn, $sql_item);
                                                while ($row_item = mysqli_fetch_array($data_item)) {
                                                    $list_item = $list_item . $row_item['nama_bahan'] . " (" . $row_item['berat'] . " " . $row_item['uom'] . ")" . " x " . money($row_item['qty']) . " <br>";
                                                }
                                                // <td class="fs-11">' . money($ttl_disc_ongkos). '</td>
                                                echo '
                                                <tr>
                                                    <td class="fs-11">' . $row_piutang['id_transaksi'] . $jns . '<br>' . $row_piutang['nama_customer'] . '</td>
                                                    <td class="fs-11">' . $row_piutang['tgl_transaksi'] . '</td>
                                                    <td class="fs-11">' . $list_item . '</td>
                                                    <td class="fs-11">' . money($row_piutang['total']) . '</td>
                                                    <td class="fs-11">' . money($row_piutang['ongkos_kirim']) . '</td>
                                                    
                                                   
                                                   
                                                    <td class="text-center fs-11">
                                                    <input type="checkbox" name="buatsj[]" class="check_so" id="" value="' . $row_piutang['id_transaksi'] . '" data-id_so="' . $row_piutang['id_transaksi'] . '" >
                                                    
                                                    </td>
                                                </tr>
                                            ';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-right">
                                    <input type="submit" name="add_sj" class="btn btn-sm btn-secondary btn_bayar mt-15" value="Tambahkan Sebagai Penagihan" />
                                </div>


                        </div>
                    </div>



                    <!-- DataTales Example -->
                    <div class="card shadow mb-4" style="background:#f7fff2;">
                        <div class="card-header" style="text-transform: none;">

                            <h5 class="mr-2">Invoice Yang Akan Ditagih</h5>

                        </div>
                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                    <thead>
                                        <tr>
                                            <th class="fs-11">No Invoice</th>
                                            <th class="fs-11">Tgl Invoice</th>
                                            <th class="fs-11">Items</th>
                                            <th class="fs-11">Subtotal</th>
                                            <th class="fs-11">Disc Ongkos</th>
                                            <!-- <th class="fs-11">Total</th> -->
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $filter = "";
                                        if (isset($_POST['cari'])) {
                                            $filter = "and tgl_transaksi>='" . mysqli_real_escape_string($conn, $_POST['tgl_1']) . "'  and tgl_transaksi<='" . mysqli_real_escape_string($conn, $_POST['tgl_2']) . "' ";
                                        }


                                        $sql_get_piutang = "select 
                                        a.id_transaksi,a.tgl_transaksi,a.dibuat_oleh,a.jenis_transaksi,a.ongkos_kirim,sum(a.subtotal) as total,c.nama_customer 
                                        from tb_barang_keluar a
                                        JOIN tb_customer c on c.id_customer = a.id_customer
                                        where id_invoice = '" . $id_invoice . "' AND id_transaksi LIKE '%SO%'
                                        " . $filter . "
                                        group by id_transaksi 
                                        order by id_transaksi asc
                                    
                                        ";
                                        $ongkos_kirim = 0;
                                        $subtotal = 0;
                                        $data4 = mysqli_query($conn, $sql_get_piutang);
                                        while ($row_piutang = mysqli_fetch_array($data4)) {

                                            $ttl_disc_ongkos = $row_piutang['total'] - $row_piutang['ongkos_kirim'];
                                            if($row_piutang["jenis_transaksi"] == "barang_masuk"){
                                                $jns = " ";
                                            }elseif($row_piutang["jenis_transaksi"] == "return"){
                                                $jns = " - (Return) ";
                                            }else{
                                                $jns = " ";
                                            }

                                            $list_item = "";
                                            $sql_item = "select a.*,b.nama_bahan
                                            from tb_barang_keluar a 
                                            JOIN tb_bahan b ON b.id_bahan = a.id_bahan 
                                   
                                            where a.id_transaksi = '" . $row_piutang['id_transaksi'] . "' and a.harga <> 0 ";
                                            $data_item = mysqli_query($conn, $sql_item);
                                            while ($row_item = mysqli_fetch_array($data_item)) {
                                                $list_item = $list_item . $row_item['nama_bahan'] . " (Berat " . $row_item['berat'] . " " . $row_item['uom'] . ") @" . money($row_item['harga']) . " x " . $row_item['qty'] . " pcs<br>";
                                            }

                                            $hide_rem = "";
                                            if (isset($_GET['view'])) {
                                                $hide_rem = "d-none";
                                            }
                                            // <td class="fs-11">' . money($ttl_disc_ongkos). '</td>
                                            echo '
                                                <tr>
                                                    <td class="fs-11">' . $row_piutang['id_transaksi'] . $jns . ' <br>' . $row_piutang['nama_customer'] . '</td>
                                                    <td class="fs-11">' . $row_piutang['tgl_transaksi'] . '</td>
                                                    <td class="fs-11">' . $list_item . '</td>
                                                    <td class="fs-11">' . money($row_piutang['total']) . '</td>
                                                    <td class="fs-11">' . money($row_piutang['ongkos_kirim']) . '</td>
                                                    
                                                    <td class="text-center fs-11">
                                                        <a class="btn btn-xs btn-danger ' . $hide_rem . '" href="pelunasan_piutang.php?id_invoice=' . $id_invoice . '&del_sj=' . $row_piutang['id_transaksi'] . '">Remove</a>
                                                    </td>
                                                </tr>
                                            ';
                                            $subtotal += $row_piutang['total'];
                                            $ongkos_kirim += $row_piutang['ongkos_kirim'];
                                        }
                                        ?>
                                    </tbody>
                                </table>

                                <table>
                                    <?php
                                    if(isset($_POST["add_sj"])){
                                        $req = "required";    
                                    }else{
                                        $req = " ";
                                    }
                                    // if(!isset($_POST["add_sj"])){
                                    //     $req = "required";    
                                    // }else{
                                    //     $req = " ";
                                    // }
                                    ?>
                                    <tr>
                                        <td height="40px;">NO INVOICE</td>
                                        <td><input type="text" readonly name="id_invoice" style="width:270px" value="<?php echo $id_invoice; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td height="40px;">CUSTOMER </td>
                                        <td>
                                        <div class="input-group mb-3">
                                            <input type="text" name="cust_invoice" id="" class="form-control form-control-sm  nama_customer" value="<?= $nama_invoice; ?>" autocomplete="off" list="list_customer" onclick="this.select();" <?= $req ?>>
                                            <datalist id="list_customer" class="list_customer">
                                            </datalist>
                                        </div>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td height="40px;">TANGGAL INVOICE </td>
                                        <td>
                                            <input type="date" name="tgl_invoice" value="<?php echo $tgl_invoice; ?>" style="width:270px" <?= $req ?>>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td height="40px;">SUBTOTAL</td>
                                        <td><input type="text" name="subtotal_invoice" class="subtotal_invoice" value="<?php echo $subtotal; ?>" style="width:270px"></td>
                                    </tr>

                                    <tr>
                                        <td height="40px;">DISCOUNT</td>
                                        <td><input type="text" name="disc_invoice" class="disc_invoice" value="<?php echo $disc_invoice; ?>" value="0" style="width:250px;text-align:right;"> % </td>
                                    </tr>
                                    
                                    <tr>
                                        <td height="40px;">DISC ONGKOS</td>
                                        <td><input type="text" name="ongkos" class="ongkos_kirim" value="<?php echo $ongkos_kirim; ?>" value="0" style="width:250px;text-align:right;">  </td>
                                    </tr>
                                    
                                    <tr>
                                        <td height="40px;">TOTAL</td>
                                        <td><input type="text" name="total_invoice" class="total_invoice" value="<?php echo $subtotal - ($subtotal * $disc_invoice / 100) - $ongkos_kirim; ?>" style="width:270px"></td>
                                    </tr>

                                </table>
                                <br>
                                <input type="submit" name="otorisasi" class="btn btn-lg btn-success <?= $hide_tab_tagih; ?>" value="Simpan Penagihan">

                                <a class="btn btn-lg btn-info " href="print_invoice.php?id_invoice=<?php echo $id_invoice; ?>" target="_blank"> <i class="fa fa-print"></i> PRINT PENAGIHAN</a>
                                <BR>
                                * jika melakukan edit terhadap List Surat Jalan / Data invoice, wajib tekan tombol "SIMPAN INVOICE" menyimpan ulang
                            </div>



                        </div>
                    </div>



                    </form>


                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->


        </div>
        <!-- End of Content Wrapper -->

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

        $(document).on("click", ".check_so", function() {
            var id_so = $(this).data('id_so');
            var check_so = $(".check_so:checked").length;
            if (check_so > 0) {
                $(".btn_bayar").removeClass("btn-secondary");
                $(".btn_bayar").addClass("btn-primary");
                $(".btn_bayar").addClass("bayar_btn");
            } else {
                $(".btn_bayar").removeClass("btn-primary");
                $(".btn_bayar").removeClass("bayar_btn");
                $(".btn_bayar").addClass("btn-secondary");
            }
        });

        $(document).on("change", ".disc_invoice", function() {
            var subtotal_invoice = parseInt($(".subtotal_invoice").val());
            var disc_invoice = parseInt($(".disc_invoice").val());
            var ongkos_kirim = parseInt($(".ongkos_kirim").val());
            var total_invoice = subtotal_invoice - (subtotal_invoice * disc_invoice / 100) - ongkos_kirim;
            $(".total_invoice").val(total_invoice);
        });

        $(document).on("change", ".ongkos_kirim", function() {
            var subtotal_invoice = parseInt($(".subtotal_invoice").val());
            var disc_invoice = parseInt($(".disc_invoice").val());
            var ongkos_kirim = parseInt($(".ongkos_kirim").val());
            var total_invoice1 = (subtotal_invoice - (subtotal_invoice * disc_invoice / 100)) - ongkos_kirim;
            $(".total_invoice").val(total_invoice1);
        });

        $(document).on("keyup", ".nama_customer", function() {
            var nama_customer = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_keluar.php",
                data: {
                    "cari_nama_customer": nama_customer
                },
                cache: true,
                success: function(result) {
                    $(".list_customer").html(result);
                }
            });
        });
        

    });

    var element = document.getElementById("mstr");
    element.classList.add("active");
</script>