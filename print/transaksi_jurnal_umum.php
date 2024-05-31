<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
    {
        header('location:index.php'); 
    }

    
    $hide_form = '';
    $hide_display = 'style="display:none;"';
    $readonly = "";

$id_transaksi_filter = mysqli_real_escape_string($conn,$_GET['id_jurnal']);
$id_user = $_SESSION['id_user'];
$date = date("Y-m-d H:i:s");



if(isset($_POST['add_jurnal'])){
    $no_transaksi = mysqli_real_escape_string($conn,$_POST['id_jurnal_filter']);
    $memo = mysqli_real_escape_string($conn,$_POST['memo']);
    $total_kredit = mysqli_real_escape_string($conn,$_POST['total_kredit']);
    $total_debit = mysqli_real_escape_string($conn,$_POST['total_debit']);
    
    if($id_transaksi_filter == "new" || $id_transaksi_filter == $id_user){
        
        $urutan = "0";
        $query = mysqli_query($conn,"SELECT max(no_transaksi) as kodeTerbesar FROM tb_jurnal_umum WHERE NOT no_transaksi = '".$id_user."' ");
        if($data = mysqli_fetch_array($query)){
            $urutan = number($data['kodeTerbesar']);
            $urutan++;
        }
        if($urutan == "0" || $urutan == ""){
            $urutan = "1";
        }
        $no_transaksi = $urutan;
        
        
        $query_set_jurnal = "UPDATE tb_jurnal_umum SET no_transaksi = '".$no_transaksi."' WHERE no_transaksi = '".$id_user."'";
        if($set_jurnal = mysqli_query($conn,$query_set_jurnal)){
            echo "<script type='text/javascript'>alert('Simpan Data Berhasil')</script>";

            header ("location:transaksi_jurnal_umum.php?view=".$no_transaksi."");
        }
    }

    if($id_transaksi_filter !== "new" || $id_transaksi_filter !== $id_user){

        $query_set_jurnal = "UPDATE tb_jurnal_umum SET memo = '".$memo."' WHERE no_transaksi = '".$no_transaksi."'";
        if($set_jurnal = mysqli_query($conn,$query_set_jurnal)){
            echo "<script type='text/javascript'>alert('Simpan Data Berhasil')</script>";

            header ("location:transaksi_jurnal_umum.php?view=".$no_transaksi."");
        }
    }
    
}


if(isset($_GET['view'])){
    $hide_form = 'style="display:none;"';
    $hide_display = '';
    $readonly = "readonly";
    $id_transaksi_filter = mysqli_real_escape_string($conn,$_GET['view']);
    $no_transaksi = $id_transaksi_filter;
}

$tgl_transaksi = date("Y-m-d");

if($id_transaksi_filter == "new"){
    $id_transaksi_filter = $id_user;
}

$no_transaksi = "new";
$total_debit = "";
$total_kredit = "";
$memo = "";

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
    <title>Transaksi Jurnal Umum</title>
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
            <!-- Container -->
            <div class="card-header bg-green-light-4">
                Transaksi
                <h5 class="hk-sec-title text-dark-light-3"> Jurnal Umum </h5>
                
            </div>
            <?php
            echo'

            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5" '.$hide_display.'>
            <div class="my-2">
                <a href="transaksi_jurnal_umum_view.php" class="text-primary" style="font-size:15px;"> << Kembali </a>
            </div>
                ';
             $query_get_jurnal = "SELECT DISTINCT no_transaksi,
                                            tgl_jurnal_umum,
                                            memo,
                                            SUM(debit) as debit_total,
	                                        SUM(kredit) as kredit_total
                                    FROM tb_jurnal_umum
                                    where no_transaksi = '".$id_transaksi_filter."'
                                    order by jurnal_umum_id desc
                                    ";
                $sql_get_jurnal = mysqli_query($conn,$query_get_jurnal);
                if($row_jurnal = mysqli_fetch_array($sql_get_jurnal)){
                    $no_transaksi = $row_jurnal['no_transaksi'];
                    $tgl_transaksi = $row_jurnal['tgl_jurnal_umum'];
                    $total_debit = $row_jurnal['debit_total'];
                    $total_kredit = $row_jurnal['kredit_total'];
                    $memo = $row_jurnal['memo'];
                }
                if($no_transaksi == $id_user || $no_transaksi == ""){
                    $no_transaksi = "new";
                }
                
                if($tgl_transaksi == ""){
                    $tgl_transaksi = date("Y-m-d");
                }
                echo'
                
               <div class="">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span style="" class="form-control filled-input form-control-sm input-group-text " id="inputGroup-sizing-sm">No. Transaksi</span>
                                </div>
                                <input autocomplete="off" type="text" name="no_jurnal" id="no_jurnal" class="form-control filled-input form-control-sm" readonly value="'.$no_transaksi.'" >
                                </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span style="" class="form-control filled-input form-control-sm input-group-text " id="inputGroup-sizing-sm">Tgl Transaksi</span>
                                </div>
                                <input autocomplete="off" readonly type="date" name="tgl_jurnal" id="tgl_jurnal" class="form-control filled-input form-control-sm" value="'.$tgl_transaksi.'" >
                            </div>
                        </div>
                    </div>
               </div>
               <div class="bg-primary-10 mt-10">
               <table class="table table-hover table-sm w-100 display mt-15 table-border" id="datable_2" style="width:100vw;">
               <thead>
               <tr>
                       <td>Akun</td>
                       <td>Deskripsi</td>
                       <td>Debit</td>
                       <td>Kredit</td>
                       </tr>
               </thead>
               <tbody>
               ';

                    $query_get_akun = "SELECT   b.jurnal_umum_id,
                                                a.kode_akun,
                                                a.nm_akun,
                                                b.description,
                                                b.debit,
                                                b.kredit
                                        FROM m_akun a
                                        LEFT JOIN tb_jurnal_umum b on a.kode_akun = b.kode_akun
                                        WHERE b.no_transaksi = '".$id_transaksi_filter."'
                                        ";
                    $sql_get_akun = mysqli_query($conn,$query_get_akun);
                    while ($row_akun = mysqli_fetch_array($sql_get_akun)) {
                        $jurnal_umum_id = $row_akun['jurnal_umum_id'];
                        $kode_akun = $row_akun['kode_akun'];
                        $nm_akun = $row_akun['nm_akun'];
                        $deskripsi = $row_akun['description'];
                        $debit = $row_akun['debit'];
                        $kredit = $row_akun['kredit'];

               echo'     
               <tr>
                       <td style="font-size:11px;">('.$kode_akun.') '.$nm_akun.'</td>
                       <td style="font-size:11px;">'.$deskripsi.'</td>
                       <td style="font-size:11px;">'.$debit.'</td>
                       <td style="font-size:11px;">'.$kredit.'</td>
                       </tr>
                       ';

               }
               echo'
               </tbody>
               </table>
                    <div class="row mt-20">
                        <div class="col-sm-4">
                            <div class="form-group form-group-sm">
                                <span style="" class="text-dark" id="inputGroup-sizing-sm">Memo :</span> 
                                <textarea rows="5" autocomplete="off" type="text" name="memo" id="memo" class="form-control form-control-sm" placeholder="-- Tulis Memo --" '.$readonly.'>'.$memo.'</textarea>
                            </div>
                        </div>
                        <div class="col-sm-3">
                        </div>
                        <div class="col-sm-5">
                        <div class="">
                            <div class="row">
                            <div class="col-sm-6">
                                <p class="text-dark">Total Debit</p>
                                <p>'.money($total_debit).'</p>
                            </div>
                            
                            <div class="col-sm-6">
                                <p class="text-dark">Total Kredit</p>
                                <p>'.money($total_kredit).'</p>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-10 justify-content-end">
                        <div class="col-sm-8">
                            <a href="print/jurnal_umum_print.php?id_jurnal='.$row_jurnal['no_transaksi'].'" target="_BLANK" class="btn btn-xs btn-info btn-wth-icon"><span class="icon-label"><i class="fa fa-print"></i></span><span class="btn-text">Print Transaksi</span></a>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group form-group-sm d-flex">
                            <a href="transaksi_jurnal_umum.php?id_jurnal='.$row_jurnal['no_transaksi'].'" class="btn btn-xs btn-warning btn-wth-icon mr-sm-2"><span class="icon-label"><i class="fa fa-pencil"></i></span><span class="btn-text">Ubah Transaksi</span></a>
                           <a href="#" class="btn btn-xs btn-danger btn-wth-icon hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                               data-id_hapus="'.$row_jurnal['no_transaksi'].'">
                               <span class="icon-label"><i class="icon-trash"></i></span><span class="btn-text">Hapus Transaksi</span>
                           </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5" '.$hide_form.'>
            <div class="my-2">
            <a href="transaksi_jurnal_umum_view.php" class="text-primary" style="font-size:15px;"> << Kembali </a>
        </div>
                <form action="'.$_SERVER['PHP_SELF'].'?id_jurnal='.$no_transaksi.'" method="POST">
                ';
                    
                $query_get_jurnal = "SELECT DISTINCT no_transaksi,
                                            tgl_jurnal_umum,
                                            memo,
                                            SUM(debit) as debit_total,
	                                        SUM(kredit) as kredit_total
                                    FROM tb_jurnal_umum
                                    where no_transaksi = '".$id_transaksi_filter."'
                                    order by jurnal_umum_id desc
                                    ";
                $sql_get_jurnal = mysqli_query($conn,$query_get_jurnal);
                if($row_jurnal = mysqli_fetch_array($sql_get_jurnal)){
                    $no_transaksi = $row_jurnal['no_transaksi'];
                    $tgl_transaksi = $row_jurnal['tgl_jurnal_umum'];
                    $total_debit = $row_jurnal['debit_total'];
                    $total_kredit = $row_jurnal['kredit_total'];
                    $memo = $row_jurnal['memo'];
                }
                if($no_transaksi == $id_user || $no_transaksi == ""){
                    $no_transaksi = "new";
                }
                
                if($tgl_transaksi == ""){
                    $tgl_transaksi = date("Y-m-d");
                }
                echo'
                
               <div class="bg-primary-10">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span style="" class="form-control filled-input form-control-sm input-group-text " id="inputGroup-sizing-sm">No. Transaksi</span>
                                </div>
                                <input autocomplete="off" type="text" name="no_jurnal" id="no_jurnal" class="form-control filled-input form-control-sm" readonly value="'.$no_transaksi.'" >
                                <input autocomplete="off" type="hidden" id="id_jurnal_filter" name="id_jurnal_filter" value="'.$id_transaksi_filter.'" >
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span style="" class="form-control filled-input form-control-sm input-group-text " id="inputGroup-sizing-sm">Tgl Transaksi</span>
                                </div>
                                <input autocomplete="off" readonly type="date" name="tgl_jurnal" id="tgl_jurnal" class="form-control filled-input form-control-sm" value="'.$tgl_transaksi.'" >
                            </div>
                        </div>
                    </div>
               </div>

               <div class="bg-primary-10 mt-10">
                    <div class="row bg-green-light-2 p-1">
                        <div class="col-sm-3">
                            <div class="input-group input-group-sm">
                                <span style="" class="text-white" id="inputGroup-sizing-sm">Akun</span> 
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group input-group-sm">
                                <span style="" class="text-white" id="inputGroup-sizing-sm">Deskripsi</span> 
                            </div>
                        </div>
                        <div class="col-sm-4 d-flex">
                            <div class="input-group input-group-sm mr-30">
                                <span style="" class="text-white" id="inputGroup-sizing-sm">Debit</span> 
                            </div>
                            <div class="input-group input-group-sm">
                                <span style="" class="text-white" id="inputGroup-sizing-sm">Kredit</span> 
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="input-group input-group-sm">
                                <span style="" class="text-white text-sm" id="inputGroup-sizing-sm">Action</span> 
                            </div>
                        </div>
                    </div>
                    ';
                    $query_get_akun = "SELECT   b.jurnal_umum_id,
                                                a.kode_akun,
                                                a.nm_akun,
                                                b.description,
                                                b.debit,
                                                b.kredit
                                        FROM m_akun a
                                        LEFT JOIN tb_jurnal_umum b on a.kode_akun = b.kode_akun
                                        WHERE b.no_transaksi = '".$id_transaksi_filter."'
                                        ";
                    $sql_get_akun = mysqli_query($conn,$query_get_akun);
                    while ($row_akun = mysqli_fetch_array($sql_get_akun)) {
                        $jurnal_umum_id = $row_akun['jurnal_umum_id'];
                        $kode_akun = $row_akun['kode_akun'];
                        $nm_akun = $row_akun['nm_akun'];
                        $deskripsi = $row_akun['description'];
                        $debit = $row_akun['debit'];
                        $kredit = $row_akun['kredit'];
                    echo'
                    <div class="div-list'.$jurnal_umum_id.'">
                    <div class="row mt-5">
                        <div class="col-sm-3">
                            <div class="input-group input-group-sm">                                
                                <input autocomplete="off" list="akun_list'.$kode_akun.'" type="text" name="akun" id="akun'.$jurnal_umum_id.'" class="form-control form-control-sm" value="('.$kode_akun.') '.$nm_akun.'" placeholder="-- Pilih Akun --" '.$readonly.'>
                                <datalist id="akun_list'.$kode_akun.'">
                                ';
                                $query_get_list = "SELECT   kode_akun,
                                                            nm_akun
                                                    FROM m_akun
                                                    ";
                                $sql_get_list = mysqli_query($conn,$query_get_list);
                                while ($row_list = mysqli_fetch_array($sql_get_list)) {

                                    echo'
                                        
                                        <option value="('.$row_list['kode_akun'].') '.$row_list['nm_akun'].' ">    
                                    ';
                                }
                                
                                echo'
                                    <option value="">
                                </datalist>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group input-group-sm">
                                <textarea type="text" rows="1" name="deskripsi" id="deskripsi'.$jurnal_umum_id.'" class="form-control form-control-sm" placeholder="-- Deskripsi --" '.$readonly.'>'.$deskripsi.'</textarea>
                            </div>
                        </div>
                        <div class="col-sm-4 d-flex">
                            <div class="input-group input-group-sm mr-30">
                                <input autocomplete="off" type="text" data-id_class="debit" data-id_akun="'.$jurnal_umum_id.'"  id="debit_moneyfield_'.$jurnal_umum_id.'" class="kredit'.$jurnal_umum_id.' form-control form-control-sm" value="'.money($debit).'" placeholder="-- Kredit --" '.$readonly.'> 
                                <input autocomplete="off" type="hidden" name="debit" id="debit0'.$jurnal_umum_id.'" value="'.$debit.'" placeholder="-- Debit --">
                            </div>
                            <div class="input-group input-group-sm">
                                <input autocomplete="off" type="text" data-id_class="kredit" data-id_akun="'.$jurnal_umum_id.'"  id="kredit_moneyfield_'.$jurnal_umum_id.'" class="hitung-kredit form-control form-control-sm" value="'.money($kredit).'" placeholder="-- Kredit --" '.$readonly.'> 
                                <input autocomplete="off" type="hidden" name="kredit" id="kredit0'.$jurnal_umum_id.'" class="kredit kredit'.$jurnal_umum_id.' " value="'.$kredit.'" placeholder="-- Kredit --"> 
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="input-group input-group-sm">
                                <a data-set_list="'.$jurnal_umum_id.'"
                                    class="set_list btn btn-warning btn-xs mr-2" id="set_list"
                                    style="color:white;"><i class="fa fa-pencil"></i></a>
                                <a data-del_list="'.$jurnal_umum_id.'" class="del_list btn btn-danger btn-xs"
                                        name="del_list" id="del_list" style="color:white;"><i
                                        class="fa fa-times"></i></a>
                            </div>
                        </div>
                    </div>
                    </div>
                    ';
                    }
                    echo'
                    <div class="view_list"></div>
                    <div class="row mt-5">
                        <div class="col-sm-3">
                            <div class="input-group input-group-sm">
                                <input autocomplete="off" list="akun_list0" type="text" name="akun" id="akun" class="akun akun0 form-control form-control-sm" value="" placeholder="-- Pilih Akun --">
                                <datalist id="akun_list0" >
                                    ';
                                    $query_get_list = "SELECT   kode_akun,
                                                                nm_akun
                                                        FROM m_akun
                                                        ";
                                    $sql_get_list = mysqli_query($conn,$query_get_list);
                                    while ($row_list = mysqli_fetch_array($sql_get_list)) {

                                        echo'
                                            
                                            <option value="('.$row_list['kode_akun'].') '.$row_list['nm_akun'].' ">    
                                        ';
                                    }
                                    
                                    echo'
                                </datalist>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group input-group-sm">
                                <textarea type="text" rows="1" name="deskripsi" id="deskripsi" class="form-control form-control-sm" value="" placeholder="-- Deskripsi --"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-4 d-flex">
                            <div class="input-group input-group-sm mr-30">
                                <input autocomplete="off" type="text" data-id_class="debit" data-id_akun="0"  id="debit_moneyfield_0" class="hitung_debit form-control form-control-sm" value="" placeholder="-- Debit --"> 
                                <input autocomplete="off" type="hidden" name="debit" id="debit00" data-debit="0" class="debit debit0" value="" placeholder="-- Debit --">
                            </div>
                            <div class="input-group input-group-sm">
                                <input autocomplete="off" type="text" data-id_class="kredit" data-id_akun="0"  id="kredit_moneyfield_0" class="hitung-kredit form-control form-control-sm" value="" placeholder="-- Kredit --"> 
                                <input autocomplete="off" type="hidden" name="kredit" id="kredit00" data-kredit="0" class="kredit kredit0" value="" placeholder="-- Kredit --"> 
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="input-group input-group-sm">
                                <a data-add_list="'.$id_transaksi_filter.'" class="add_list btn btn-success btn-xs"
                                        name="add_list" id="add_list" style="color:white;"><i
                                        class="fa fa-plus"></i></a>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-20">
                        <div class="col-sm-4">
                            <div class="form-group form-group-sm">
                                <span style="" class="text-dark" id="inputGroup-sizing-sm">Memo :</span> 
                                <textarea rows="5" autocomplete="off" type="text" name="memo" id="memo" class="form-control form-control-sm" placeholder="-- Tulis Memo --" '.$readonly.'>'.$memo.'</textarea>
                            </div>
                        </div>
                        <div class="col-sm-2">
                        </div>
                        <div class="col-sm-4">
                        <div class="total_jumlah">
                            <div class="row">
                            <div class="col-sm-6">
                                <p class="text-dark">Total Debit</p>
                                <input type="hidden" id="total_debit" value="'.$total_debit.'">
                                <p id="display_debit" >'.money($total_debit).'</p>
                            </div>
                            
                            <div class="col-sm-6">
                                <p class="text-dark">Total Kredit</p>
                                <input type="hidden" id="total_kredit" value="'.$total_kredit.'">
                                <p id="display_kredit">'.money($total_kredit).'</p>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-10 justify-content-end">
                        <div class="col-sm-2">
                            <div class="form-group form-group-sm">
                            <button data-add_jurnal="0" type="submit" class="add_jurnal btn btn-success btn-sm" style="width:10vw;"
                                        name="add_jurnal" id="add_jurnal" style="color:white;">Save</button>
                            </div>
                        </div>
                    </div>
               </div>
               
                </form>
            </div>
            
            ';
            ?>
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

            function koma(nStr) {
                nStr += '';
                var x = nStr.split('.');

                var x1 = x[0];
                var x2 = x.length > 1 ? ',' + x[1] : '';
                var rgx = /(\d+)(\d{3})/;
                while (rgx.test(x1)) {
                    x1 = x1.replace(rgx, '$1' + ',' + '$2');
                }
                var balikan = x1 + x2;

                return balikan;
                //return output;
            }

            $(document).on("keyup", "input[id*='moneyfield']", function (e) {

                var name = $(this).data("id_class");
                var id = $(this).data("id_akun");
                var sum = 0;
                var value = $(this).val();
                value = value.replace(/,/g, "");

                $("#" + name + "0" + id).val(value);
                var value2 = $("#" + name + "0" + id).val();

                $(this).val(koma(value2));

                $("input[id*='" + name + "0']").each(function () {
                    value = Number($(this).val());
                    if (isNaN(value)) value = 0;
                    sum += value;
                });
                $("#total_" + name).val(sum);
                document.getElementById("display_" + name).innerText = koma(sum);
            });



            $('#add_list').click(function () {

                var id_jurnal = $(this).data("add_list");
                var tgl_jurnal = $("#tgl_jurnal").val();
                var akun = $("#akun").val();
                var deskripsi = $("#deskripsi").val();
                var debit = $("#debit00").val();
                var kredit = $("#kredit00").val();
                var memo = $("#memo").val();
                var total_debit = $("#total_debit").val();
                var total_kredit = $("#total_kredit").val();
                var dataString = 'add_list=' + id_jurnal +
                    '&tgl_jurnal=' + tgl_jurnal +
                    '&akun=' + akun +
                    '&deskripsi=' + deskripsi +
                    '&debit=' + debit +
                    '&kredit=' + kredit +
                    '&memo=' + memo +
                    '&total_debit=' + total_debit +
                    '&total_kredit=' + total_kredit;
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_input_list.php",
                    data: dataString,
                    cache: true,
                    success: function (html) {
                        $(".view_list").append(html);
                        $("#akun").val("");
                        $("#deskripsi").val("");
                        $("#debit00").val("");
                        $("#kredit00").val("");
                        $("#debit_moneyfield_0").val("");
                        $("#kredit_moneyfield_0").val("");
                    }
                });

            });

            $(document).on("click", '.set_list', function (e) {
                
                var id_list = $(this).data("set_list");
                var id_jurnal = $("#id_jurnal_filter").val();
                var tgl_jurnal = $("#tgl_jurnal").val();
                var akun = $("#akun" + id_list).val();
                var deskripsi = $("#deskripsi" + id_list).val();
                var debit = $("#debit0" + id_list).val();
                var kredit = $("#kredit0" + id_list).val();
                var memo = $("#memo").val();
                var total_debit = $("#total_debit").val();
                var total_kredit = $("#total_kredit").val();
                var dataString = 'set_list=' + id_list +
                    '&id_jurnal=' + tgl_jurnal +
                    '&tgl_jurnal=' + tgl_jurnal +
                    '&akun=' + akun +
                    '&deskripsi=' + deskripsi +
                    '&debit=' + debit +
                    '&kredit=' + kredit +
                    '&memo=' + memo +
                    '&total_debit=' + total_debit +
                    '&total_kredit=' + total_kredit;
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_input_list.php",
                    data: dataString,
                    cache: true,
                    success: function (html) {
                        alert("Update akun list berhasil");
                    }
                });

            });

            $(document).on("click", '.del_list', function (e) {

                var id_list = $(this).data("del_list");
                var id_jurnal = $("#id_jurnal_filter").val();
                var dataString = 'del_list=' + id_list +
                    '&id_jurnal=' + id_jurnal;
                    alert (dataString);
                $.ajax({
                    type: "POST",
                    url: "ajax/ajax_input_list.php",
                    data: dataString,
                    cache: true,
                    success: function (html) {
                        $(".div-list" + id_list).hide();
                        $("#debit_moneyfield_"+id_list).val("0");
                        $("#kredit_moneyfield_"+id_list).val("0");
                        $("#debit0"+id_list).val("0");
                        $("#kredit0"+id_list).val("0");
                        $(".total_jumlah").html(html);
                    }
                });

            });

        });
    </script>


</body>

</html>