<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('location:index.php');
}

$kode_akun_filter = mysqli_real_escape_string($conn, $_GET['id_akun']);
$id_user = $_SESSION['id_user'];
$date = date("Y-m-d H:i:s");

if (isset($_POST['finish'])) {

    $id_akun = mysqli_real_escape_string($conn, $_POST['m_akun_id']);
    $nm_akun = mysqli_real_escape_string($conn, $_POST['nama_akun']);
    $kode_akun = mysqli_real_escape_string($conn, $_POST['kode_akun']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kat_akun']);
    $detail_akun = mysqli_real_escape_string($conn, $_POST['detail_akun']);
    $head_akun = mysqli_real_escape_string($conn, $_POST['sub_akun']);
    $pajak_akun = mysqli_real_escape_string($conn, $_POST['pajak_akun']);
    $id_bank = mysqli_real_escape_string($conn, $_POST['nama_bank']);
    $saldo_awal = mysqli_real_escape_string($conn, $_POST['saldo_akun']);
    $desc_akun = mysqli_real_escape_string($conn, $_POST['desc_akun']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    if ($saldo_awal == "") {
        $saldo_awal = "0.00";
    }
    if ($kode_akun == "") {
        $kode = "";
        $query = "";
        $awal = "0";

        $query_get_kategori = "SELECT * FROM m_kategori_akun WHERE kat_akun_id = '" . $kategori . "'";
        $sql_get_kategori = mysqli_query($conn, $query_get_kategori);
        if ($row_kat = mysqli_fetch_array($sql_get_kategori)) {
            $kode = $row_kat['kode_kategori'];
            $query = "kode_akun like '" . $kode . "%' ";
        }

        $kode_akun = "";
        $query_kode = mysqli_query($conn, "SELECT max(kode_akun) as max_id FROM m_akun WHERE " . $query . " ");
        if ($row_kode = mysqli_fetch_array($query_kode)) {
            $id = $row_kode['max_id'];
            $urut = (int) substr($id, 5, 2);
            $urut++;
            $kode_akun = $kode . $urut;
        } else {
            $kode_akun = $kode . $awal;
        }
    }

    if ($detail_akun == "none") {
        $head_akun = "none";
        $query_sub_akun = mysqli_query($conn, "UPDATE m_akun SET head_akun = 'none' , detail_akun = 'none' WHERE head_akun = '" . $id_akun . "'");
    }


    if ($kode_akun_filter !== "new") {

        $query_update_akun = "UPDATE m_akun 
                                    set nm_akun = '" . $nm_akun . "',
                                        kode_akun = '" . $kode_akun . "',
                                        kat_akun = '" . $kategori . "',
                                        detail_akun = '" . $detail_akun . "',
                                        head_akun = '" . $head_akun . "',
                                        pajak_akun = '" . $pajak_akun . "',
                                        saldo_akun = '" . $saldo_awal . "',
                                        id_bank = '" . $id_bank . "',
                                        desc_akun = '" . $desc_akun . "',
                                        id_cabang = '" . $cabang . "',
                                        diubah_oleh = '" . $id_user . "',
                                        diubah_tgl = '" . $date . "'
                                    where m_akun_id = '" . $id_akun . "'";
        // echo $query_update_akun;
        if (mysqli_query($conn, $query_update_akun)) {

            if (!empty($_POST['head_akun'])) {
                $query_update = mysqli_query($conn, "UPDATE m_akun set head_akun = 'none' ,detail_akun = 'none'  WHERE head_akun = '" . $id_akun . "' ");
                $sub = "";
                foreach ($_POST['head_akun'] as $sub) {
                    $query_set_akses = mysqli_query($conn, " UPDATE m_akun SET head_akun = '" . $id_akun . "' , detail_akun = 'Sub-Akun dari' where m_akun_id = '" . $sub . "' ");
                }
                echo "<script type='text/javascript'>alert('Edit akun Berhasil!')</script>";
            }
            header('location:master_akun_accounting.php');
        }
    }

    // input jika memang input barus baru
    if ($kode_akun_filter == "new") {
        $query_akun = mysqli_query($conn, "SELECT * FROM m_akun WHERE kode_akun = '" . $id_akun . "' ");
        $akun_check = mysqli_num_rows($query_akun);

        if ($akun_check > 0) {

            echo "<script type='text/javascript'>alert('Kode akun Tidak Boleh Sama!')</script>";
        } else {


            $query_add_akun = "INSERT INTO m_akun (     m_akun_id,
                                                                nm_akun,
                                                                kode_akun,
                                                                kat_akun,
                                                                detail_akun,
                                                                head_akun,
                                                                pajak_akun,
                                                                id_bank,
                                                                saldo_akun,
                                                                desc_akun,
                                                                id_cabang,
                                                                dibuat_oleh,
                                                                dibuat_tgl,
                                                                diubah_oleh,
                                                                diubah_tgl
                                                                )
                                                        VALUES (
                                                                null,
                                                                '" . $nm_akun . "',
                                                                '" . $kode_akun . "',
                                                                '" . $kategori . "',
                                                                '" . $detail_akun . "',
                                                                '" . $head_akun . "',
                                                                '" . $pajak_akun . "',
                                                                '" . $id_bank . "',
                                                                '" . $saldo_awal . "',
                                                                '" . $desc_akun . "',     
                                                                '" . $cabang . "',                                                          
                                                                '" . $_SESSION['nm_user'] . "',
                                                                '" . date('Y-m-d h:i:s') . "',
                                                                '',
                                                                null                                   
                                                                )
                            ";
            if (mysqli_query($conn, $query_add_akun)) {
                if (!empty($_POST['head_akun'])) {
                    $query_get_akun = "SELECT m_akun_id FROM m_akun WHERE kode_akun = '" . $kode_akun . "' ";
                    $sql_get_akun = mysqli_query($conn, $query_get_akun);
                    $row_akun = mysqli_fetch_array($sql_get_akun);

                    $sub = "";
                    foreach ($_POST['head_akun'] as $sub) {
                        $query_set_sub = mysqli_query($conn, " UPDATE m_akun SET head_akun = '" . $row_akun['m_akun_id'] . "' , detail_akun = 'Sub-Akun dari' where m_akun_id = '" . $sub . "' ");
                    }
                    echo "<script type='text/javascript'>alert('Save Berhasil!')</script>";
                }
                header('location:master_akun_accounting.php');
            }
        }
    }
}





$nm_akun = "";
$nomor = "";
$kategori = "";
$detail_akun = "";
$head_akun = "";
$sub_akun = "";
$pajak_akun = "";
$bank_akun = "";
$saldo_awal = "0";
$desc_akun = "";
$readonly = "";
$required = "required";
$visible_sub = 'style="display:none;"';
$visible_head = 'style="display:none;"';
$display_bank = 'style="display:none;"';



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
    <title>Daftar Akun Baru</title>
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

<body>


    <!-- HK Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

        <?php include "header.php"; ?>

        <!-- Main Content -->
        <div class="hk-pg-wrapper">
            <!-- Container -->
            <h5 class="hk-sec-title card-header px-15 py-15">Buat Akun Baru </h5>
            <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
                <!-- Row -->
                <div class="row">
                    <div class="col">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id_akun=<?php echo $kode_akun_filter; ?>" method="POST">
                            <section class="hk-sec-wrapper">

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="row justify-content-center">
                                            <div class="col-md-6">
                                                <?php

                                                $query_get_akun = "SELECT * FROM m_akun a left join m_kategori_akun k on k.kat_akun_id = a.kat_akun WHERE a.m_akun_id = '" . $kode_akun_filter . "'  ";
                                                $sql_get_akun = mysqli_query($conn, $query_get_akun);
                                                if ($row_akun = mysqli_fetch_array($sql_get_akun)) {
                                                    $m_akun_id = $row_akun['m_akun_id'];
                                                    $nm_akun = $row_akun['nm_akun'];
                                                    $nomor = $row_akun['kode_akun'];
                                                    $kategori = $row_akun['kat_akun'];
                                                    $nm_kat = $row_akun['nm_kategori'];
                                                    $detail_akun = $row_akun['detail_akun'];
                                                    $head_akun = $row_akun['head_akun'];
                                                    $pajak = $row_akun['pajak_akun'];
                                                    $bank_akun = $row_akun['id_bank'];
                                                    $saldo_awal = $row_akun['saldo_akun'];
                                                    $desc = $row_akun['desc_akun'];
                                                    $nm_sub = "";
                                                    $kode_sub = "";
                                                    $head_sub = "";

                                                    if ($detail_akun == "Sub-Akun dari") {
                                                        $visible_sub = '';
                                                        $visible_head = 'style="display:none;"';
                                                    }
                                                    if ($detail_akun == "Akun Header dari") {
                                                        $visible_head = '';
                                                        $visible_sub = 'style="display:none;"';
                                                    }
                                                    if ($nm_kat == "Kas & Bank") {
                                                        $display_bank = '';
                                                    }
                                                }

                                                if ($nomor == "") {

                                                    $query_kode = mysqli_query($conn, "SELECT max(kode_akun) as max_id FROM m_akun WHERE kode_akun like '1-101%' ");
                                                    if ($row_kode = mysqli_fetch_array($query_kode)) {
                                                        $id = $row_kode['max_id'];
                                                        $urut = (int) substr($id, 5, 2);
                                                        $urut++;
                                                        $nomor = "1-101" . $urut;
                                                    }
                                                }

                                                echo '
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Nama Akun</span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="nama_akun"
                                                            id="nama_akun" class="form-control form-control-sm" value="' . $nm_akun . '"
                                                            required>
                                                        <input type="hidden" class="filter_id_akun" name="m_akun_id" value="' . $kode_akun_filter . '">
                                                    </div>
                                                </div>
                                                <div class="form-group kode_akun">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm">Nomor</span>
                                                        </div>
                                                        <input autocomplete="off" type="text" name="kode_akun"
                                                            id="kode_akun" class="kode_akun form-control form-control-sm" value="' . $nomor . '"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Kategori</span>
                                                        </div>
                                                        <select name="kat_akun" id="kategori_akun"
                                                            class=" form-control form-control-sm custom-select custom-select">
                                                            ';
                                                $query_get_kategori = "SELECT * FROM m_kategori_akun ";
                                                $sql_get_kategori = mysqli_query($conn, $query_get_kategori);
                                                while ($row_kategori = mysqli_fetch_array($sql_get_kategori)) {
                                                    echo '
                                                                    <option value="' . $row_kategori['kat_akun_id'] . '" ';
                                                    if ($kategori == $row_kategori['kat_akun_id']) {
                                                        echo ' selected ';
                                                    }
                                                    echo '>' . $row_kategori['nm_kategori'] . '</option>
                                                                ';
                                                }
                                                echo '
                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Detail</span>
                                                        </div>
                                                        <select name="detail_akun" id="detail_akun"
                                                            class="custom-select custom-select form-control form-control-sm"
                                                            value="">
                                                            <option value="none" ';
                                                if ($detail_akun == "none") {
                                                    echo 'selected';
                                                }
                                                echo ' >none</option>
                                                            <option value="Sub-Akun dari" ';
                                                if ($detail_akun == "Sub-Akun dari") {
                                                    echo 'selected';
                                                }
                                                echo '>Sub-Akun dari:</option>
                                                            <option value="Akun Header dari" ';
                                                if ($detail_akun == "Akun Header dari") {
                                                    echo 'selected';
                                                }
                                                echo '>Akun Header dari:</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group sub-div" ' . $visible_sub . ' >
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Sub-Akun dari </span>
                                                        </div>
                                                        <select name="sub_akun" style="width:150px;" class="sub_akun form-control form-control-sm custom-select custom-select-sm" id="sub_akun">
                                                        ';
                                                $query_get_other_akun = "SELECT kode_akun,nm_akun,m_akun_id from m_akun WHERE kat_akun = '" . $kategori . "' and not m_akun_id = '" . $kode_akun_filter . "' and not head_akun = '" . $kode_akun_filter . "' or detail_akun = 'Akun Header dari' ";

                                                $sql_other = mysqli_query($conn, $query_get_other_akun);
                                                while ($row_head = mysqli_fetch_array($sql_other)) {
                                                    $nm_head = $row_head['nm_akun'];
                                                    $kode_head = $row_head['kode_akun'];
                                                    $id = $row_head['m_akun_id'];

                                                    if ($head_akun == $id) {
                                                        echo '
                                                                    <option value="' . $id . '" selected>(' . $kode_head . ') ' . $nm_head . '</option>
                                                                ';
                                                    } else {
                                                        echo '
                                                                    <option value="' . $id . '">(' . $kode_head . ') ' . $nm_head . '</option>
                                                                ';
                                                    }
                                                }
                                                echo '
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group head-div" ' . $visible_head . ' >
                                                
                                                    <div class="input-group input-group-sm" >
                                                            <span style=""
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Akun Header dari :</span>
                                                        <select name="head_akun[]" id="head_akun"  class="head_akun select2 select2-multiple" multiple="multiple" >
                                                        ';
                                                $query_get_sub_akun = "SELECT kode_akun,nm_akun,m_akun_id,head_akun from m_akun WHERE kat_akun = '" . $kategori . "' and  head_akun = 'none' or head_akun = '" . $kode_akun_filter . "'";
                                                $sql_sub = mysqli_query($conn, $query_get_sub_akun);
                                                while ($row_sub = mysqli_fetch_array($sql_sub)) {
                                                    $nm_sub = $row_sub['nm_akun'];
                                                    $kode_sub = $row_sub['kode_akun'];
                                                    $head_sub = $row_sub['m_akun_id'];
                                                    $head = $row_sub['head_akun'];
                                                    if ($kode_akun_filter == $head) {
                                                        echo '
                                                                    <option value="' . $head_sub . '" selected>(' . $kode_sub . ') ' . $nm_sub . '</option>
                                                                ';
                                                    } else if ($head_akun !== $kode_akun_filter && $head_akun !== "none") {
                                                        echo '
                                                                    <option style="display:none;" value="' . $head_sub . '">(' . $kode_sub . ') ' . $nm_sub . '</option>
                                                                ';
                                                    } else {
                                                        echo '
                                                                    <option value="' . $head_sub . '">(' . $kode_sub . ') ' . $nm_sub . '</option>
                                                                ';
                                                    }
                                                }
                                                echo '
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Pajak</span>
                                                        </div>                                                        
                                                        <select name="pajak_akun" id="pajak_akun"
                                                            class="custom-select custom-select form-control form-control-sm">
                                                            <option value="">-- Pilih Pajak --</option>
                                                            <option value="PPN" ';
                                                if ($pajak == "PPN") {
                                                    echo 'selected';
                                                }
                                                echo '>PPN</option>
                                                        </select>
                                                    </div>
                                                </div> -->
                                                <div class="form-group display_bank" ' . $display_bank . ' >
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Nama Bank </span>
                                                        </div>
                                                        <select name="nama_bank"
                                                            id="nama_bank" class="form-control custom-select custom-select-sm form-control-sm">
                                                            <option value="">-- Pilih Nama Bank --</option>
                                                            ';
                                                $query_get_bank = mysqli_query($conn, "SELECT id_bank,nm_bank FROM m_bank");
                                                while ($row_bank = mysqli_fetch_array($query_get_bank)) {
                                                    $id_bank = $row_bank['id_bank'];
                                                    $nm_bank = $row_bank['nm_bank'];
                                                    if ($id_bank == $bank_akun) {
                                                        $selected = "selected";
                                                    } else {
                                                        $selected = "";
                                                    }
                                                    echo '
                                                                            <option value="' . $id_bank . '" ' . $selected . '>' . $nm_bank . '</option>        
                                                                        ';
                                                }
                                                echo '
                                                            </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Saldo Awal</span>
                                                        </div>
                                                        <input autocomplete="off" type="text" style="text-align:right;"
                                                            class="form-control saldo_moneyfield form-control-sm"
                                                            value="' . money($saldo_awal) . '">
                                                        <input type="hidden" value="' . $saldo_awal . '" name="saldo_akun" id="saldo_akun" > 
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Deskripsi</span>
                                                        </div>
                                                        <textarea name="desc_akun" id="desc_akun"
                                                            class="form-control form-control-sm">' . $desc . '</textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="input-group input-group-sm">
                                                        <div class="input-group-prepend">
                                                            <span style="width:150px;"
                                                                class="form-control filled-input form-control-sm input-group-text "
                                                                id="inputGroup-sizing-sm"> Cabang</span>
                                                        </div>
                                                        ';

                                                if ($_SESSION['group'] !== "super") {
                                                    $readonly_cabang = "readonly";
                                                    $filled_input_cabang = "filled-input";
                                                } else {
                                                    $readonly_cabang = "";
                                                    $filled_input_cabang = "";
                                                }

                                                echo '
                                                        <select name="cabang" id="" class="form-control form-control-sm ' . $filled_input_cabang . '" ' . $readonly_cabang . '>
                                                        ';
                                                $where_branch = " ORDER BY id_cabang ASC";
                                                if ($_SESSION['group'] !== "super") {
                                                    $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                                }
                                                $select_cabang = "SELECT * FROM tb_cabang" . $where_branch;
                                                $query_cabang = mysqli_query($conn, $select_cabang);
                                                while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                                    echo '
                                                                <option value="' . $row_cabang['id_cabang'] . '">' . $row_cabang['nama_cabang'] . '</option>
                                                            ';
                                                }

                                                echo ' 
                                                        </select>
                                                    </div>
                                                </div>
                                            ';
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-7">
                                    </div>
                                    <div class="col-5">
                                        <div class="row">
                                            <button type="submit" class="btn btn-success btn-sm" style="width:5vw;" name="finish"> Save </button>
                                            &nbsp;&nbsp;
                                            <a href="master_akun_accounting.php" class="btn btn-sm btn-danger" style="width:5vw;">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </section>
                    </div>
                </div>
                <!-- /Row -->


                </form>
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
        function koma(nStr) {
            nStr += '';
            var x = nStr.split(',');

            var x1 = x[0];
            var x2 = x.length > 1 ? ',' + x[1] : '';
            var rgx = /(\d+)(\d{3})/;
            while (rgx.test(x1)) {
                x1 = x1.replace(rgx, '$1' + '.' + '$2');
            }
            var balikan = x1 + x2;

            return balikan;
            //return output;
        }


        $(document).on("click", '.hapus_button', function(e) {
            var id_hapus = $(this).data('id_hapus');
            $(".id_hapus").val(id_hapus);
        });

        $('.select-all').click(function(event) {
            if (this.checked) {
                $(':checkbox').prop('checked', true);
            } else {
                $(':checkbox').prop('checked', false);
            }
        });

        $('#detail_akun').change(function() {

            var kategori_akun = $("#kategori_akun").val();

            var detail = $(this).val();


            var id_akun = $(".filter_id_akun").val();

            var dataString = 'detail=' + detail +
                '&kat_akun=' + kategori_akun +
                '&kode_akun=' + id_akun;
            $.ajax({
                type: "POST",
                url: "ajax/ajax_get_kode_akun.php",
                data: dataString,
                cache: true,
                success: function(x) {
                    if (detail == "Sub-Akun dari") {
                        $('.sub-div').show(200);
                        $('.head-div').hide(200);
                        $(".sub_akun").html(x);

                    }
                    if (detail == "Akun Header dari") {
                        $('.sub-div').hide(200);
                        $('.head-div').show(200);
                        $(".head_akun").html(x);
                    }
                    if (detail == "none") {
                        $('.sub-div').hide(200);
                        $('.head-div').hide(200);
                    }
                }
            });
        });

        $('#kategori_akun').change(function() {
            var kategori_akun = $(this).val();
            var dataString = 'kategori=' + kategori_akun;
            var id_bank = $("#kategori_akun option:selected").text();

            if (id_bank == "Kas & Bank") {
                $('.display_bank').show();
            } else {
                $('.display_bank').hide();
            }
            $.ajax({
                type: "POST",
                url: "ajax/ajax_get_kode_akun.php",
                data: dataString,
                cache: true,
                success: function(x) {
                    $(".kode_akun").html(x);
                }
            });
        });

        $(".saldo_moneyfield").keyup(function(e) {
            //alert('tes');
            var name = "saldo_akun";
            var value = $(this).val();
            value = value.replace(/\D/g, "");
            $("#" + name).val(value);
            var value2 = $("#" + name).val();
            $(this).val(koma(value2));
        });
    </script>


</body>

</html>