<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

// $id_transaksi_filter = mysqli_real_escape_string($conn,$_GET['id_jurnal']);
$id_user = $_SESSION['id_user'];
$date = date("Y-m-d H:i:s");

$tgl_1 = date("Y-m-d");
$tgl_2 = date("Y-m-d");

$where = "";
if(isset($_POST['cari_tgl'])){
    $tgl_1 = mysqli_real_escape_string($conn,$_POST['tgl_1']);
    $tgl_2 = mysqli_real_escape_string($conn,$_POST['tgl_2']);
}

if(isset($_POST['delete'])){
    $msg = "Delete Jurnal Umum Gagal";
    $id_hapus = mysqli_real_escape_string($conn,$_POST['id_hapus']);
    $sql_del = "DELETE FROM tb_jurnal_umum WHERE no_transaksi = '".$id_hapus."' ";
    if($query_del = mysqli_query($conn,$sql_del)){
        $msg = "Delete Jurnal Umum Berhasil";
    }
        echo "<script type='text/javascript'>alert('".$msg."')</script>";
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
    <title>Neraca Saldo</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet"
        type="text/css" />

    <!-- select2 CSS -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

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

            <!-- Header -->
            <div class="card-header bg-green-light-4">
                <!-- Akuntan -->
                <h5 class="hk-sec-title text-dark-light-3"> Neraca Saldo
                     
                </h5>
            </div>
            <!-- /Header -->

            <!-- Container -->

            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
                    <div class="row" style="">
                    
                        <div class="col-xl-7 d">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span style="" class="mt-20 mr-1 text-dark" id="inputGroup-sizing-sm">Cari Tanggal</span>
                                </div>
                                <input autocomplete="off" type="date"
                                    id="cari_tanggal_1" name="tgl_1" class="form-control form-control-sm mt-15 mr-2" value="<?php echo $tgl_1 ?>" >
                                    <span style="" class="mt-20 mr-1 text-dark"> To </span>
                                <input autocomplete="off" type="date"
                                    id="cari_tanggal_2" name="tgl_2" class="form-control form-control-sm mt-15 mr-2" value="<?php echo $tgl_2 ?>" >
                                <input type="submit" name="cari_tgl" class="btn btn-info btn-xs mt-15" value=" Cari ">
                            </div>
                        </div>
                        <div class="col-xl-5 d mt-15" >
                            <div class="float-right">
                                <a href="print/neraca_saldo_print.php?from=<?= $tgl_1; ?>&to=<?= $tgl_2; ?>" class="btn btn-xs btn-info btn-wth-icon"><span class="icon-label"><i class="fa fa-print"></i></span><span class="btn-text">Print Neraca Saldo</span></a>
                            </div>
                        </div>
                    </div>
                </form>

                <?php
                    $no = 1;
                echo'
                <table class="table table-bordered table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                    <thead class="bg-green-light-1 ">
                        <tr >
                            <th style="font-size:11px;" rowspan="2" class="text-center text-white">
                                Daftar Akun
                            </th>
                            <th style="font-size:11px;" class="text-center text-white" colspan="2">
                                Saldo Awal
                            </th>
                            <th style="font-size:11px;" class="text-center text-white" colspan="2">
                                Pergerakan
                            </th>
                            <th style="font-size:11px;" class="text-center text-white" colspan="2">
                                Saldo Akhir
                            </th>
                        </tr>
                        <tr style="font-size:11px;" class="text-center text-white">
                            <th style="font-size:11px;" class="text-white">Debit</th>
                            <th style="font-size:11px;" class="text-white">Kredit</th>
                            <th style="font-size:11px;" class="text-white">Debit</th>
                            <th style="font-size:11px;" class="text-white">Kredit</th>
                            <th style="font-size:11px;" class="text-white">Debit</th>
                            <th style="font-size:11px;" class="text-white">Kredit</th>                                  
                        </tr>
                    </thead>
                    <tbody class="">
                        ';
                        $group = 1;
                        while ($group <= 5) {
                            if($group == 1){
                                $group_akun = "Asset";
                                $akun_grup = "(1,2,3,4,5,6,7)";
                            }

                            if($group == 2){
                                $group_akun = "Kewajiban";
                                $akun_grup = "(8,9,10,11)";
                            }

                            if($group == 3){
                                $group_akun = "Ekuitas";
                                $akun_grup = "(12)";
                            }
                            if($group == 4){
                                $group_akun = "Pendapatan";
                                $akun_grup = "(13,14)";
                            }
                            if($group == 5){
                                $group_akun = "Beban";
                                $akun_grup = "(15,16,17)";
                            }


                            echo '
                                <tr>
                                    <td style="font-size:11px;" colspan="7"><strong>'.$group_akun.'</strong></td>
                                </tr>
                            ';
                            
                            $grand_total_debit = 0;
                            $grand_total_kredit = 0;
                            $query_get_akun = mysqli_query($conn,"SELECT DISTINCT a.nm_akun,a.kode_akun,a.kat_akun FROM tb_jurnal_umum ju JOIN m_akun a ON a.kode_akun = ju.kode_akun WHERE a.kat_akun IN ".$akun_grup." ");

                            while($row_akun = mysqli_fetch_array($query_get_akun)){
                                $kode_akun = $row_akun['kode_akun'];
                                $nm_akun = $row_akun['nm_akun'];

                                // START Hitung Debit & Kredit Saldo Awal 
                                $saldo_awal_akun = 0;
                                $debet = 0;
                                $kredit = 0;
                                $total_debet_awal = 0;
                                $total_kredit_awal = 0;
                                $sisa_saldo = 0;
                                $sql_det = mysqli_query($conn,"SELECT  j.no_transaksi as nomor, 
                                                                j.memo as ket,
                                                                j.tgl_jurnal_umum as tgl,
                                                                j.debit as debet,
                                                                j.kredit as kredit, 
                                                                j.kode_akun as kode, 
                                                                a.kat_akun as kat 
                                                        FROM tb_jurnal_umum j 
                                                            JOIN m_akun a ON a.kode_akun = j.kode_akun 
                                                        WHERE j.kode_akun = '".$kode_akun."' 
                                                            and j.tgl_jurnal_umum < '".$tgl_1."' ");
                                
                                while($row_det = mysqli_fetch_array($sql_det)){
                                    $nomor = $row_det['nomor'];
                                    $ket = $row_det['ket'];
                                    $kode = $row_det['kode'];
                                    $kat_akun = $row_det['kat'];
                                    $date = $row_det['tgl'];
                                    $debet = $row_det['debet'];
                                    $kredit = $row_det['kredit'];
                                
                                    $total_debet_awal += $row_det['debet'] ;
                                    $total_kredit_awal += $row_det['kredit'] ;
                                }
                                // END Hitung Debit & Kredit Saldo Awal

                                // START Hitung Debit & Kredit Pergerakan                                
                                $saldo_pergerakan_akun = 0;
                                $debet = 0;
                                $kredit = 0;
                                $total_debet_pergerakan = 0;
                                $total_kredit_pergerakan = 0;
                                $sisa_saldo = 0;
                                $sql_det = mysqli_query($conn,"SELECT  j.no_transaksi as nomor, 
                                                                j.memo as ket,
                                                                j.tgl_jurnal_umum as tgl,
                                                                j.debit as debet,
                                                                j.kredit as kredit, 
                                                                j.kode_akun as kode, 
                                                                a.kat_akun as kat 
                                                        FROM tb_jurnal_umum j 
                                                            JOIN m_akun a ON a.kode_akun = j.kode_akun 
                                                        WHERE j.kode_akun = '".$kode_akun."' 
                                                            AND j.tgl_jurnal_umum BETWEEN '".$tgl_1."' AND '".$tgl_2."'  ");
                                
                                while($row_det = mysqli_fetch_array($sql_det)){
                                    $nomor = $row_det['nomor'];
                                    $ket = $row_det['ket'];
                                    $kode = $row_det['kode'];
                                    $kat_akun = $row_det['kat'];
                                    $date = $row_det['tgl'];
                                    $debet = $row_det['debet'];
                                    $kredit = $row_det['kredit'];
                                
                                    $total_debet_pergerakan += $row_det['debet'] ;
                                    $total_kredit_pergerakan += $row_det['kredit'] ;
                                }
                                // END Hitung Debit & Kredit Pergerakan

                                // START Hitung Debit & Kredit Pergerakan
                                $saldo_akhir_akun = 0;
                                $debet = 0;
                                $kredit = 0;
                                $total_debet_akhir = 0;
                                $total_kredit_akhir = 0;
                                $sisa_saldo = 0;
                                $sql_det = mysqli_query($conn,"SELECT  j.no_transaksi as nomor, 
                                                                j.memo as ket,
                                                                j.tgl_jurnal_umum as tgl,
                                                                j.debit as debet,
                                                                j.kredit as kredit, 
                                                                j.kode_akun as kode, 
                                                                a.kat_akun as kat 
                                                        FROM tb_jurnal_umum j 
                                                            JOIN m_akun a ON a.kode_akun = j.kode_akun 
                                                        WHERE j.kode_akun = '".$kode_akun."' 
                                                            and j.tgl_jurnal_umum <= '".$tgl_2."' ");
                                
                                while($row_det = mysqli_fetch_array($sql_det)){
                                    $nomor = $row_det['nomor'];
                                    $ket = $row_det['ket'];
                                    $kode = $row_det['kode'];
                                    $kat_akun = $row_det['kat'];
                                    $date = $row_det['tgl'];
                                    $debet = $row_det['debet'];
                                    $kredit = $row_det['kredit'];
                                
                                    $total_debet_akhir += $row_det['debet'];
                                    $total_kredit_akhir += $row_det['kredit'];
                                }
                                // END Hitung Debit & Kredit Pergerakan

                                echo'
                                    <tr>
                                        <td style="font-size:11px;">
                                            ('.$kode_akun.')  '.$nm_akun.'
                                        </td>
                                        <td style="font-size:11px;" class="text-right">
                                            '.number_format($total_debet_awal).'
                                        </td>
                                        <td style="font-size:11px;" class="text-right">
                                            '.number_format($total_kredit_awal).'
                                        </td>
                                        <td style="font-size:11px;" class="text-right">
                                            '.number_format($total_debet_pergerakan).'
                                        </td>
                                        <td style="font-size:11px;" class="text-right">
                                            '.number_format($total_kredit_pergerakan).'
                                        </td>
                                        <td style="font-size:11px;" class="text-right">
                                            '.number_format($total_debet_akhir).'
                                        </td>
                                        <td style="font-size:11px;" class="text-right">
                                            '.number_format($total_kredit_akhir).'
                                        </td>
                                    </tr>
                                ';
                            }
                                $group++;
                        }
                        echo'
                    </tbody>
                </table>
                ';
                ?>
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
     <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Delete Jurnal Umum</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="modal-body">
                <input type="hidden" class="form-control id_hapus" name="id_hapus">
                 Akan menghapus data transaksi Jurnal Umum, Yakin?
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <input type="submit" class="btn btn-danger" name="delete" value="Delete">
            </div>
            </form>
        </div>
        </div>
    </div>

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