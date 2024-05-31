<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

// $id_transaksi_filter = mysqli_real_escape_string($conn,$_GET['id_jurnal']);
$id_user = $_SESSION['id_user'];
$date = date("Y-m-d H:i:s");

if(isset($_POST['save_tgl'])){
    $tgl_akun = mysqli_real_escape_string($conn,$_POST['tgl_awal']);
    $sql_update_tgl = "UPDATE m_saldo_awal SET tgl_awal = '".$tgl_akun."' ";
    if(mysqli_query($conn,$sql_update_tgl)){
        header("Location:akuntan_saldo_awal.php?set_date=".$tgl_akun."");
    }
}
    // Process Save Saldo Awal
    if(isset($_POST['save_awal']))
    {
        // Start Process Get Kode Akun
        $query_get_akun = mysqli_query($conn,"
                                        SELECT kode_akun 
                                        FROM m_akun
                                    ");
        while ($row_akun = mysqli_fetch_array($query_get_akun)) 
        {
            // Start Data Get Kode akun
            $kode_akun = $row_akun['kode_akun'];
            $debit_awal = mysqli_real_escape_string($conn,$_POST['debit_akun'.$kode_akun.'']);
            $kredit_awal = mysqli_real_escape_string($conn,$_POST['kredit_akun'.$kode_akun.'']);
            // END data Get Kode Akun

                // Start Process Get Debit and Kredit
                $query_get_awal = mysqli_query($conn,"
                                                SELECT debit,
                                                        kredit 
                                                FROM m_saldo_awal 
                                                WHERE kode_akun = '".$kode_akun."' 
                                            ");
                $row_awal = mysqli_fetch_array($query_get_awal);
                $debit_awalan = $row_awal['debit'];
                $kredit_awalan = $row_awal['kredit'];
                // End Process Get Debit and Kredit

                // Start Process Get Saldo Akun
                $query_get_saldo = mysqli_query($conn,"
                                                SELECT debit_akun,
                                                        kredit_akun 
                                                FROM m_akun 
                                                WHERE kode_akun = '".$kode_akun."'
                                            ");
                if($row_saldo = mysqli_fetch_array($query_get_saldo)){

                    // Start Data Get Saldo Akun
                    $debit_akun = $row_saldo['debit_akun'];
                    $kredit_akun = $row_saldo['kredit_akun'];

                    $total_debit_akun = $debit_akun - $debit_awalan;
                    $total_kredit_akun = $kredit_akun - $kredit_awalan;

                    $hasil_debit_akun = $total_debit_akun + $debit_awal;
                    $hasil_kredit_akun = $total_kredit_akun + $kredit_awal;                    
                    // End Data Get Saldo Akun

                    // Start Process Set Saldo Akun
                    $sql_set_saldo = "
                                        UPDATE m_akun                                
                                        SET debit_akun = '".$hasil_debit_akun."' , 
                                            kredit_akun = '".$hasil_kredit_akun."' 
                                        WHERE kode_akun = '".$kode_akun."'
                                    ";
                    if(mysqli_query($conn,$sql_set_saldo)){
                        mysqli_query($conn,"
                                        UPDATE m_saldo_awal                                
                                        SET debit = '".$hasil_debit_akun."' , 
                                            kredit = '".$hasil_kredit_akun."' 
                                        WHERE kode_akun = '".$kode_akun."'
                                    ");
                        $msg = '<script language="javascript">alert("Berhasil Simpan Saldo Awal")</script>';
                    }else{
                        $msg = '<script language="javascript">alert("Gagal Simpan Saldo Awal")</script>';
                    }
                    // End Process Set Saldo Akun
                }
                // End Process Get Saldo Akun
            }
            // End Process Set Saldo Awal
        echo $msg;
    }
    // End Process Save Saldo Awal

$hidden_table = "";
$hidden_tanggal = "";
if(isset($_GET['set_date']))
{
    if($_GET['set_date'] == "set")
    {
        $hidden_table = "hidden";
    }
    else
    {
        $hidden_tanggal = "hidden";
    }
}else{
    header("Location:master_akun.php");
}

$query_get_tgl_akun = mysqli_query($conn,"SELECT DISTINCT tgl_awal FROM m_saldo_awal");
if($row_tgl_akun = mysqli_fetch_array($query_get_tgl_akun)){
    $tgl_1 = $row_tgl_akun['tgl_awal'];
    if($tgl_1 == "0000-00-00"){
        $tgl_1 = date("Y");
        $tgl_1 = $tgl_1 - 1;
        $tgl_1 = $tgl_1."-12-01";
        $tgl_1 = date("Y-m-t",strtotime($tgl_1));
    }
}

?>

<style>
    .row .order-1{
        order:2;
    }

    .row .order-2{
        order:1;
    }
    
@media (max-width: 768px) {
    .row .order-1{
        order:1;
    }

    .row .order-2{
        order:2;
    }

}
</style>
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
    <title>Atur Saldo Awal</title>
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
                <h5 class="hk-sec-title text-dark-light-3"> 
                    Akun Saldo Awal 
                </h5>
            </div>
            <!-- /Header -->

            <!-- Container Table -->
            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5" style="" <?= $hidden_table ?> >
                <div class="row justify-content-center">
                    <div class="col-sm-10 col-md-6">
                        <span class="text-right"><h6>Silahkan masukan saldo awal per tanggal <a href="akuntan_saldo_awal.php?set_date=set"><?= date("d/m/Y",strtotime($_GET['set_date'])) ?></a></h6></span>
                    </div>
                </div>
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">

                <?php
                $no = 1;
                echo'
                <div style="display:block; width:100%;  overflow-x:auto;">
                    <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15 saldo_awal" >
                        <thead class="bg-green-light-1 ">
                            <tr >
                                <th style="" class="text-left text-white">
                                    Akun
                                </th>
                                <th style="min-width:100px; max-width:110px;" class="text-right text-white">
                                    <span class="mr-2" >Debit</span><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Nilai yang buram menandakan saldo negatif"></i>
                                </th>
                                <th style="min-width:100px; max-width:110px;" class="text-right text-white">
                                    <span class="mr-2" >Kredit</span><i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="Nilai yang buram menandakan saldo negatif"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="">
                            ';
                            $group = 1;
                            while ($group <= 5) {
                                if($group == 1){
                                    $group_akun = "Aset";
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
                                        <td style="" colspan="7"><strong>'.$group_akun.'</strong></td>
                                    </tr>
                                ';
                                
                                $grand_total_debit = 0;
                                $grand_total_kredit = 0;
                                $query_get_akun = mysqli_query($conn,"SELECT * FROM m_akun WHERE kat_akun IN ".$akun_grup." ");
                                $no_urut = 1;
                                while($row_akun = mysqli_fetch_array($query_get_akun)){
                                    $kode_akun = $row_akun['kode_akun'];
                                    $nm_akun = $row_akun['nm_akun'];
                                    $kat_akun = $row_akun['kat_akun'];
                                    $debit = $row_akun['debit_akun'];
                                    $kredit = $row_akun['kredit_akun'];
                                    if($kat_akun == 1  || $kat_akun == 2 || 
                                       $kat_akun == 3  || $kat_akun == 4 || 
                                       $kat_akun == 5  || $kat_akun == 6 || 
                                       $kat_akun == 15 || $kat_akun == 16|| 
                                       $kat_akun == 17 ){
                                        $text_debit = "text-dark";
                                        $text_kredit = "text-muted";
                                    }else{
                                        $text_kredit = "text-dark";
                                        $text_debit = "text-muted";
                                    }
                                    echo'
                                        <tr>
                                            <td style="">
                                                ('.$kode_akun.')  '.$nm_akun.'
                                            </td>
                                            <td style="min-width:130px; max-width:130px;" class="text-right">
                                                <span class="hide_debit hide_debit'.$kode_akun.' '.$text_debit.'" data-kode_akun="'.$kode_akun.'" >Rp.'.number_format($debit).'</span>
                                                <input style="display:none;" name="debit_akun'.$kode_akun.'"  class="text-right form-control form-control-sm input_debit input_debit'.$no_urut.' input_debit'.$kode_akun.'" data-kode_akun="'.$kode_akun.'" value="'.$debit.'">
                                            </td>
                                            <td style="min-width:130px; max-width:130px;" class="text-right">
                                                <span class="hide_kredit hide_kredit'.$kode_akun.' '.$text_kredit.'" data-kode_akun="'.$kode_akun.'">Rp.'.number_format($kredit).'</span>
                                                <input style="display:none;" name="kredit_akun'.$kode_akun.'"  class="text-right form-control form-control-sm input_kredit input_kredit'.$no_urut.' input_kredit'.$kode_akun.'" data-kode_akun="'.$kode_akun.'" value="'.$kredit.'">
                                            </td>
                                        </tr>
                                    ';
                                    $no_urut++;
                                }
                                    $group++;
                            }
                            echo'
                            <tr>
                                <td class="text-left"><strong>Total</strong></td>
                                <td class="text-right total_debit">Rp.0</td>
                                <td class="text-right total_kredit">Rp.0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                ';
                ?>
                <div class="row justify-content-end">
                    <div class="col-md-2 order-2 mt-10">
                        <a href="akuntan_saldo_awal.php?set_date=set" class="btn btn-sm btn-block btn-danger" >Kembali</a>
                    </div>
                    <div class="col-md-2 order-1 mt-10">                                    
                        <button type="submit" name="save_awal" class="btn btn-sm btn-block btn-success">Simpan</button>
                    </div>
                </div>
                </form>
            </div>
            <!-- / Container Table -->

            <!-- Container Tanggal -->
            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5" <?= $hidden_tanggal ?> >
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">

                <?php
                echo'
                    <div class="row justify-content-center mt-15">
                        <div class="col-md-5">
                            <span>Silahkan masukkan tanggal migrasi atau tanggal mulai pencatatan</span>
                            <div class="input-group mt-10">
                                <div class="input-group-prepend">
                                    <span class="form-control filled-input form-control-sm">Tanggal Konversi</span>
                                </div>
                                <input class="form-control form-control-sm" name="tgl_awal" type="date" value="'.$tgl_1.'">
                            </div>
                            <div class="row justify-content-end">
                                <div class="col-sm-3 order-2 mt-10">
                                    <a href="master_akun.php" class="btn btn-sm btn-block btn-danger" >Kembali</a>
                                </div>
                                <div class="col-sm-3 order-1 mt-10">                                    
                                    <button type="submit" name="save_tgl" class="btn btn-sm btn-block btn-success">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                ';
                ?>
    
                </form>
            </div>
            <!-- /Container Tanggal -->

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

    <!-- Toggles JavaScript -->
    <script src="vendors/jquery-toggles/toggles.min.js"></script>
    <script src="dist/js/toggle-data.js"></script>

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
       function koma(nStr) {
            nStr += '';
            var x = nStr.split(',');
            var x1 = x[0];
            var x2 = x.length > 1 ? '.' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
            }
            return x1 + x2;
        }

        $(document).ready(function () {
            var total = 0;
            var total_kredit = 0;
            var table = $(".saldo_awal tr").length;
		    for(var t = 1; t < table; t++)
		    {
                var debit = parseInt($(".input_debit"+t).val());
                if(isNaN(debit)) {debit = 0;}
			    total = total + debit;

                var kredit = parseInt($(".input_kredit"+t).val());
                if(isNaN(kredit)) {kredit = 0;}
			    total_kredit = total_kredit + kredit;
		    }
		    $(".total_debit").text("Rp."+koma(total));
            $(".total_kredit").text("Rp."+koma(total_kredit));

            // Start Function Show input field
            $(document).on("click", '.hide_debit', function (e) {
                $(".hide_debit").show();
                $(".hide_kredit").show();
                $(".input_debit").hide();
                $(".input_kredit").hide();

                var kode_akun = $(this).data("kode_akun");
                $(".hide_debit"+kode_akun).hide();
                $(".input_debit"+kode_akun).show();
                $(".input_debit"+kode_akun).focus();
                
            });

            $(document).on("click", '.hide_kredit', function (e) {
                $(".hide_debit").show();
                $(".hide_kredit").show();
                $(".input_debit").hide();
                $(".input_kredit").hide();

                var kode_akun = $(this).data("kode_akun");
                $(".hide_kredit"+kode_akun).hide();
                $(".input_kredit"+kode_akun).show();
                $(".input_kredit"+kode_akun).focus();
            });
            // End Function Show input field

            // Start Function Hitung input field
            $(document).on("keyup", '.input_debit', function (e) {

                var kode_akun = $(this).data("kode_akun");
                var debit = $(".input_debit"+kode_akun).val();

                $(".hide_debit"+kode_akun).text("Rp."+koma(debit));
                
            });

            $(document).on("keyup", '.input_kredit', function (e) {

                var kode_akun = $(this).data("kode_akun");
                var kredit = $(".input_kredit"+kode_akun).val();

                $(".hide_kredit"+kode_akun).text("Rp."+koma(kredit));
            });
            // End Function Hitung input field

            // Start Function Blur and Hide input field
            $(document).on("blur", '.input_debit', function (e) {

                var kode_akun = $(this).data("kode_akun");
                $(".hide_debit"+kode_akun).show();
                $(".input_debit"+kode_akun).hide();

                var kredit = $(".input_kredit"+kode_akun).val();
                if(kredit !== 0){
                    $(".input_kredit"+kode_akun).val("0");
                    $(".hide_kredit"+kode_akun).text("Rp.0");
                }

                var table = $(".saldo_awal tr").length , total = 0;
		        for(var t = 1; t < table; t++)
		        {
                    var debit = parseInt($(".input_debit"+t).val());
                    if(isNaN(debit)) {debit = 0;}
			        total = total + debit;
		        }
		        $(".total_debit").text("Rp."+koma(total));

            });

            $(document).on("blur", '.input_kredit', function (e) {
                var kode_akun = $(this).data("kode_akun");
                $(".hide_kredit"+kode_akun).show();
                $(".input_kredit"+kode_akun).hide();

                var debit = $(".input_debit"+kode_akun).val();
                if(debit !== 0){
                    $(".input_debit"+kode_akun).val("0");
                    $(".hide_debit"+kode_akun).text("Rp.0");
                }


                var table = $(".saldo_awal tr").length , total = 0;
		        for(var t = 1; t < table; t++)
		        {
                    var kredit = parseInt($(".input_kredit"+t).val());
                    if(isNaN(kredit)) {kredit = 0;}
			        total = total + kredit;
		        }
		        $(".total_kredit").text("Rp."+koma(total));

            });
            // End Function Blur and Hide input field
        });
    </script>


</body>

</html>