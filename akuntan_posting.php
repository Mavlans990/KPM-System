<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

// $id_transaksi_filter = mysqli_real_escape_string($conn,$_GET['id_jurnal']);
$id_user = $_SESSION['id_user'];
$date = date("Y-m-d H:i:s");

$tgl_1 = "2020-01-01";
$tgl_2 = date("Y-m-d");
$query_get_posting = mysqli_query($conn,"SELECT tgl_posting FROM m_posting_jurnal ORDER BY tgl_posting DESC");
if($row_posting = mysqli_fetch_array($query_get_posting)){
    $tgl_1 = $row_posting['tgl_posting'];
    $tgl_2 = date('Y-m-d', strtotime('+1 days', strtotime($tgl_1))); //operasi penjumlahan tanggal sebanyak 6 hari
}

$where = "";

if(isset($_POST['posting'])){
    $tgl_1 = "2020-01-01";

    $query_get_tgl = mysqli_query($conn,"SELECT tgl_posting FROM m_posting_jurnal ORDER BY tgl_posting desc");
    if($row_tgl = mysqli_fetch_array($query_get_tgl)){
        $tgl_1 = $row_tgl['tgl_posting'];
    }
    $tgl_2 = mysqli_real_escape_string($conn,$_POST['tgl_2']);
    if($tgl_1 !== "" || $tgl_2 !== ""){
        
        $query_get_akun = mysqli_query($conn,"SELECT DISTINCT a.nm_akun,a.kode_akun,a.kat_akun FROM tb_jurnal_umum ju JOIN m_akun a ON a.kode_akun = ju.kode_akun ");
        while($row_akun = mysqli_fetch_array($query_get_akun)){
            $kat_akun = $row_akun['kat_akun'];
            $kode_akun = $row_akun['kode_akun'];
            // echo"UPDATE tb_jurnal_umum SET status_jurnal = 'posted' WHERE tgl_jurnal_umum BETWEEN '".$tgl_1."' AND '".$tgl_2."'";
            mysqli_query($conn,"UPDATE tb_jurnal_umum SET status_jurnal = 'Posted' WHERE tgl_jurnal_umum BETWEEN '".$tgl_1."' AND '".$tgl_2."'");

            $sisa = 0;
            $debet = 0;
            $nomor = "";
            $kredit = 0;
            $total_debet = 0;
            $total_kredit = 0;
            // echo"SELECT no_transaksi as nomor,debit as debet,kredit as kredit FROM tb_jurnal_umum WHERE kode_akun = '".$kat_akun."' and WHERE tgl_jurnal_umum BETWEEN '".$tgl_1."' AND '".$tgl_2."' and status_jurnal = '' ORDER BY tgl_jurnal_umum ASC ";
            $sql_det = mysqli_query($conn,"SELECT no_transaksi as nomor,debit as debet,kredit as kredit FROM tb_jurnal_umum WHERE kode_akun = '".$kode_akun."' and tgl_jurnal_umum BETWEEN '".$tgl_1."' AND '".$tgl_2."' and status_jurnal = 'posted' ORDER BY tgl_jurnal_umum ASC ");
            while($row_det = mysqli_fetch_array($sql_det)){

                $nomor = $row_det['nomor'];
                
                $total_debet += $row_det['debet'] ;
                $total_kredit += $row_det['kredit'] ;
                if( $kat_akun == "1" || $kat_akun == "2" || 
                    $kat_akun == "3" || $kat_akun == "4" || 
                    $kat_akun == "5" || $kat_akun == "6" ||
                    $kat_akun == "15" || $kat_akun == "16" ||
                    $kat_akun == "17" ){
                    $sisa = $sisa + $row_det['debet'] - $row_det['kredit'];
                }else{
                    $sisa = $sisa + $row_det['debet'] - $row_det['kredit'];
                }

                mysqli_query($conn,"UPDATE m_akun SET kredit_akun = '".$total_kredit."', debit_akun = '".$total_debet."' WHERE kode_akun = '".$kode_akun."' ");
            }
            mysqli_query($conn,"INSERT INTO m_posting_jurnal (id_jurnal,
                                                        kode_akun,
                                                        total_jurnal,
                                                        tgl_posting,
                                                        dibuat_oleh,
                                                        dibuat_tgl) 
                                                VALUES ('".$nomor."',
                                                        '".$kode_akun."',
                                                        '".$sisa."',
                                                        '".$tgl_2."',
                                                        '".$id_user."',
                                                        '".date("Y-m-d H:i:s")."')                                                        
                        ");
        }
    }
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
    <title>Posting Jurnal</title>
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
                <h5 class="hk-sec-title text-dark-light-3"> Posting Jurnal
                     
                </h5>
            </div>
            <!-- /Header -->

            <!-- Container -->

            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
                <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post">
                    <div class="row" style="">
                        <div class="col-xl-2 mt-15 mb-15">
                            <div class="button-group">
                                <a href="javacript:void(0)" class="btn btn-success btn-sm btn-block" data-toggle="modal" data-target="#PostingModal" >Posting</a>
                            </div>
                        </div>
                    </div>
                
                </form>

                    <?php
                            echo'
                            <table class="table table-bordered table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins " id="datable_1">
                            <thead>
                                <tr class="">
                                    <th>Tgl Posting</th>
                                    <th>Di Posting Oleh</th>                      
                                </tr>
                            </thead>
                            <tbody class="">
                            ';
                                $query_get_akun = mysqli_query($conn,"SELECT DISTINCT pj.tgl_posting,a.nm_user FROM m_posting_jurnal pj JOIN tb_user a ON a.id_user = pj.dibuat_oleh ");

                                while($row_akun = mysqli_fetch_array($query_get_akun)){
                                    $tgl_posting = $row_akun['tgl_posting'];
                                    $posting_oleh = $row_akun['nm_user'];
                                    echo'
                                        <tr>
                                            <td>'.date('d/m/Y', strtotime($tgl_posting)).'</td>
                                            <td>'.$posting_oleh.'</td>
                                        </tr>
                                    ';
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
     <div class="modal fade" id="PostingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Posting Jurnal</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <div class="modal-body"> 
                <input type="hidden" class="form-control id_hapus" name="id_hapus">
                <div class="form-group">
                    <label for="">Pilih Tanggal </label>
                    <input type="date" class="form-control form-control-sm" id="cari_tanggal_2" value="<?= $tgl_2;?>" name="tgl_2" placeholder=""
                    onkeypress="InputNumber(event)" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <input type="submit" class="btn btn-success" name="posting" value="Posting">
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
            $(document).on("change",'#cari_tanggal_2',function(e) {
                
                var tgl_2 = $(this).val();
                var tgl_1 = "<?= $tgl_1; ?>";

                
                if(tgl_2 == tgl_1 || tgl_2 < tgl_1){

                    var day = Math.round(1 + 24 * 60 * 60 * 1000 ); // year*hours*minutes*seconds*milliseconds
                    var tanggal_mulai = new Date(tgl_1);
                    var hitung_tanggal = new Date(Math.round(tanggal_mulai.getTime() +
                    day));
                    var tgl_2 = hitung_tanggal.toISOString().substr(0, 10);
                    
                }
                $(this).val(tgl_2);                
            });
        });

    </script>


</body>

</html>