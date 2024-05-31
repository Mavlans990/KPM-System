<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";
session_start();

$id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);



function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " trilyun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}

function terbilang($nilai)
{
    if ($nilai < 0) {
        $hasil = "minus " . trim(penyebut($nilai));
    } else {
        $hasil = trim(penyebut($nilai));
    }
    return $hasil;
}

//CHECK APAKAH ADA INVOICE SEBELUMNYA YANG MASIH HUTANG DAN LEWAT TEMPO +7
$status = 0;



//onload="window.print();"

$company = "";
$disc_tot = 0;
$cashback = 0;
$alamat = "";
$telp = "";
$sales = "";
$tanggal_transaksi = "";
$tgl_jatuh_tempo = "";
$totalall = 0;
$ppn_persen = 0;
$ppn = 0;
$bank = '';
$akun = '';
$rek = '';
$nm_bank = '';
$tempo = '';
$sql_get_barang_keluar = mysqli_query($conn, "SELECT a.*,SUM(a.subtotal) AS ttl_subtotal,b.nm_akun FROM tb_barang_keluar a LEFT JOIN m_akun b ON b.kode_akun = a.akun WHERE a.id_transaksi = '" . $id_transaksi . "' GROUP BY a.id_transaksi ORDER BY a.id_transaksi ASC LIMIT 1");
if ($data_barang_keluar = mysqli_fetch_array($sql_get_barang_keluar)) {
    $customer = explode(" | ", $data_barang_keluar['id_customer']);
    $id_customer = $customer[0];
    $dibuat_oleh = $data_barang_keluar['dibuat_oleh'];
    $tanggal_transaksi = $data_barang_keluar['tgl_transaksi'];
    $tgl_jatuh_tempo = $data_barang_keluar['tgl_jatuh_tempo'];
    $totalall = $data_barang_keluar['ttl_subtotal'];
    $ppn_persen = $data_barang_keluar['ppn'];
    $bank = $data_barang_keluar['nm_akun'];
    $akun = $data_barang_keluar['akun'];
    $tempo = $data_barang_keluar['jatuh_tempo'];
    $ppn = $totalall * $ppn_persen / 100;
    if($akun == '1-1002'){
        $rek = '4273055358';
        $bank = 'CV Rumah Bahan';
        $nm_bank = 'BCA';
    }elseif($akun == '1-1005'){
        $rek = '0953133030';
        $bank = 'Oei Agus Haryanto';
        $nm_bank = 'BCA';
    }elseif($akun == '1-1004'){
        $rek = '4273028377';
        $bank = 'Margareta Rinta Kusumawa';
        $nm_bank = 'BCA';
    }elseif($akun == '1-1003'){
        $rek = '7380818188';
        $bank = 'Viryan Haryanto';
        $nm_bank = 'BCA';
    }

    $sql_get_customer = mysqli_query($conn, "SELECT * FROM tb_customer WHERE id_customer = '" . $id_customer . "'");
    if ($data_customer = mysqli_fetch_array($sql_get_customer)) {
        $company = $data_customer['nama_customer'];
        $alamat = $data_customer['alamat_lengkap'];
        $telp = $data_customer['no_telp'];
    }

    $sql_get_user = mysqli_query($conn, "SELECT * FROM tb_karyawan WHERE user_id = '" . $dibuat_oleh . "'");
    if ($data_user = mysqli_fetch_array($sql_get_user)) {
        $sales = $data_user['nama_lengkap'];
    }
}



$judul = "
				<span style='font-size:17px;font-weight:bold;'>
				<center>
                <span style='font-size:20px;font-weight:bold;'>
				CV RUMAH BAHAN
                </span> 
                <BR>Jalan Panduraya no 163 RT 03 /RW 16 kel.Tegal Gundil Kec Bogor Utara 
                Kota Bogor 16152  <br>Telp. 0251-834-8819 / 0878-7316-0077
				<BR><U> 
                <span style='font-size:20px;font-weight:bold;'>
                INVOICE PENJUALAN
                </span>
                </U>
				</center>
				</span>
				";


?>



<!DOCTYPE html>
<html>

<head>
    <title>Print INVOICE</title>
    <style type="text/css">
        @font-face {
            font-family: Verdana, Geneva, sans-serif;
        }


        body {
            font-family: Verdana, Geneva, sans-serif;
            letter-spacing: 0.09em;
        }

        @media print {


            html,
            body {

                display: block;
                font-family: Verdana, Geneva, sans-serif;
                letter-spacing: 0.09em;

            }

            fieldset {
                border: 1px solid black;
            }

        }




        table {
            border-collapse: collapse;
        }

        .bordered {
            border: 1px solid black;
        }
    </style>
</head>

<body>





    <table border=0 width="1000px" style="border:none;">

        <tr>
            <td colspan="2">
                <?php echo $judul; ?>
                <br>
            </td>
        </tr>
        <tr>
            <td width="50%">

                <fieldset style="width: 92%;height:130px;">
                    <b style='font-size:16px;font-weight:bold;'>Sales :</b>
                    <span style='font-size:13px;'><?php echo strtoupper($sales); ?><br>
                    <b style='font-size:16px;font-weight:bold;'>No. Invoice :</b>
                    <span style='font-size:13px;'><?php echo $id_transaksi; ?><br>
                    <b style='font-size:16px;font-weight:bold;'>Tanggal :</b>
                    <span style='font-size:13px;'><?php echo date("d/m/Y", strtotime($tanggal_transaksi)); ?><br>
                    <b style='font-size:16px;font-weight:bold;'>Jatuh Tempo :</b>
                    <span style='font-size:13px;'><?php echo date("d/m/Y", strtotime($tgl_jatuh_tempo)); ?><br>
                    <b style='font-size:16px;font-weight:bold;'>Telepon :</b>
                    <span style='font-size:13px;'>0251-834-8819 / 0878-7316-0077
                    </span><br>
                    <b style='font-size:16px;font-weight:bold;'>Alamat :</b>
                    <span style='font-size:13px;'>Jalan Panduraya no 163 RT 03 /RW 16 kel.Tegal Gundil Kec Bogor Utara 
                Kota Bogor 16152<br></span>
                </fieldset>


            </td>
            <td width="50%">

                <fieldset style="width: 92%;height:130px;">

                    <legend><b style='font-size:13px;font-weight:bold;'>Kepada :</b></legend>
                    <b style='font-size:16px;font-weight:bold;'><?php echo strtoupper($company); ?></b>
                    <span style='font-size:13px;'>
                        <p><?php echo strtoupper($alamat); ?></p><br>
                        <b>Telp.</b> <?php echo $telp; ?>
                    </span>
                </fieldset>

            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:12px;">

                <table class="bordered" style="clear: both;" style="border:1px solid black;margin-top:6px;" width="99%">

                    


                </table>


                <table class="bordered" width="99%" style="">
                    <tr style='font-size:16px;font-weight:bold;'>

                        <td class="bordered" colspan=2 width="50%"><b>
                                <center>DESKRIPSI</center>
                            </b></td>
                        <td class="bordered" width="12%"><b>
                                <center>JML ROLL</center>
                            </b></td>
                        <td class="bordered" width="17%"><b>
                                <center>HARGA</center>
                            </b></td>
                        <td class="bordered" width="21%"><b>
                                <center>SUB TOTAL</center>
                            </b></td>
                    </tr>


                    <?php
                    $x = 0;
                    $total = 0;
                    $jatah = 6;


                    $query = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'";

                    $get_barang = mysqli_query($conn, $query);
                    while ($row1 = mysqli_fetch_array($get_barang)) {

                        $id_bahan = $row1['id_bahan'];
                        $berat = $row1['berat'];
                        $uom = $row1['uom'];


                        $nama_barang = "";
                        $sql_get_bahan = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
                        if ($data_bahan = mysqli_fetch_array($sql_get_bahan)) {
                            $nama_barang = $data_bahan['nama_bahan'];
                        }

                        echo '<tr  style="height:20px;font-size:13px;">';

                        echo '<td>' . ($x + 1) . ' &nbsp;</td>';
                        echo '<td  style="padding-top:0px;padding-bottom:0px;" > ' . strtoupper($nama_barang) . ' @ ' . round($berat) . ' ' . $uom . ' </td>';
                        echo '<td style="padding-top:0px;"><center>' . number_format($row1['qty']) . '</center></td>';
                        echo '<td style="padding-top:0px;padding-bottom:0px;"><center>' . money($row1['harga']) . '</center></td>';
                        echo '<td style="padding-top:0px;padding-bottom:0px;"><center>' . money($row1['subtotal']) . '</center></td>';

                        echo '</tr>';
                        $x++;
                    }

                    if ($jatah - $x > 0) {
                        for ($i = 1; $i <= ($jatah - $x); $i++) {
                            echo '<tr  style="height:22px;font-size:13px;"><td colspan=5></td></tr>';
                        }
                    }



                    ?>


                </table>


                <table width="100%">
                    <tr>
                        <td width="60%" style="font-size:13px;font-weight:bold;">
                            <b><u>Terbilang</u></b>
                            <br>
                            # <?php echo terbilang($totalall + $ppn); ?> rupiah #

                            <br><br>
                            <fieldset style="width: 80%; height: 100px; margin-bottom: 1%; clear: both;">
                                <?php 
                                if($tempo == 0){
                                    echo "<b>".$nm_bank." ".$bank." (Lunas)</b>";
                                }else{
                                    echo " <legend><b>Bank :</b></legend>
                                <!--
							<b>
								<br>
								<br>
								No. Acc &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : 
								<br>
								Atas Nama &nbsp;: 
							</b>
						-->
                                <b>
                                    No. Acc &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : ".strtoupper($rek)."
                                    <br>
                                    Nama Bank : ".strtoupper($nm_bank)."
                                    <br>
                                    Atas Nama &nbsp;: ".strtoupper($bank)."
                                </b>
";
                                }
                                
                                ?>

                             </fieldset>
                            <br>
                        </td>
                        <td width="50%" style="font-size:16px;font-weight:bold;" valign="top">

                            <table style="margin-right:9px;margin-top:-2px;">

                                <tr>
                                    <td style="text-align:right;width:350px;">Total &nbsp;&nbsp;&nbsp; </td>
                                    <td style="text-align:right;width:150px;" class="bordered"><?php echo money($totalall); ?></td>
                                </tr>

                                <?php

                                if ($disc_tot > 0) {
                                ?>

                                    <tr>
                                        <td style="text-align:right;width:250px;">Disc &nbsp;&nbsp;&nbsp; </td>
                                        <td style="text-align:right;width:150px;" class="bordered"><?php if ($curr == "USD") {
                                                                                                        echo money($disc_tot);
                                                                                                    } else {
                                                                                                        echo money($disc_tot);
                                                                                                    }  ?></td>
                                    </tr>

                                <?php
                                }

                                if ($ppn_persen !== '0.00') {
                                ?>


                                    <tr>
                                        <td style="text-align:right;">Sales Tax <?php echo round($ppn_persen); ?>% &nbsp;&nbsp;&nbsp;</td>
                                        <td style="text-align:right;" class="bordered"><?php echo money($ppn); ?></td>
                                    </tr>




                                <?php
                                }
                                if ($cashback > 0) {
                                ?>
                                    <tr>
                                        <td style="text-align:right;width:350px;">Cashback &nbsp;&nbsp;&nbsp; <?php echo $curr; ?></td>
                                        <td style="text-align:right;width:150px;" class="bordered"><?php echo money($cashback); ?></td>
                                    </tr>
                                <?php
                                }
                                ?>

                                <tr>
                                    <td style="text-align:right;width:250px;">Grand Total &nbsp;&nbsp;&nbsp;</td>
                                    <td style="text-align:right;width:150px;" class="bordered"><?php echo money($totalall + $ppn); ?></td>
                                </tr>
                            </table>
                            <br><br>
                            <table>
                                <tr>
                                    <td>Dibuat Oleh</td>
                                    <td width="60px"></td>
                                    <td>Diterima Oleh</td>
                                </tr>
                                <tr>
                                    <td height="120px">(..............)</td>
                                    <td width="60px"></td>
                                    <td>(..............)</td>
                                </tr>
                            </table>



                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>