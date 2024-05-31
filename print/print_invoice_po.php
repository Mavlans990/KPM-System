<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";
session_start();

$id_transaksi = mysqli_real_escape_string($conn, $_GET['id_transaksi']);
$tanggal_po = mysqli_real_escape_string($conn, $_GET['tanggal_po']);



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
$totalall = 0;
$ppn_persen = 0;
$ppn = 0;
$sql_get_barang_keluar = mysqli_query($conn, "SELECT a.*,SUM(a.subtotal) AS ttl_subtotal FROM tb_barang_po a WHERE a.id_transaksi = '" . $id_transaksi . "' GROUP BY a.id_transaksi ORDER BY a.id_transaksi ASC LIMIT 1");
if ($data_barang_keluar = mysqli_fetch_array($sql_get_barang_keluar)) {
    $id_customer = $data_barang_keluar['id_customer'];
    $dibuat_oleh = $data_barang_keluar['dibuat_oleh'];
    $tanggal_transaksi = $data_barang_keluar['tgl_transaksi'];
    $tgl_jatuh_tempo = $data_barang_keluar['tgl_jatuh_tempo'];
    $totalall = $data_barang_keluar['ttl_subtotal'];
    $ppn_persen = $data_barang_keluar['ppn'];
    $ppn = $totalall * $ppn_persen / 100;


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
				<span style='font-size:13px;font-weight:bold;'>
				<center>
				Rumah Bahan
				<BR>Jalan Panduraya no 163 RT 03/RW 16 Kel.Tegal Gundul kec Bogor Utara 16152 Telp.(021)-11111111
				<BR><U> PURCHASE ORDER</U>
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
            <td colspan="2" style="padding-top:12px;">
                    <tr>
                        <td  style='font-size:16px;font-weight:bold; width:10%;'>
                        <b>NO PO : </b>
                        </td>
                        <td  style='font-size:13px;'>
                            <p><?php echo $id_transaksi; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td  style='font-size:16px;font-weight:bold; width:10%;'>
                        <b>TGL PO : </b>
                        </td>
                        <td  style='font-size:13px;'>
                            <p><?php echo date("d/m/Y", strtotime($tanggal_po)) ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td style='font-size:16px;font-weight:bold;  width:10%;'>
                            <b>DIBUAT OLEH :</b>
                        </td>
                        <td style='font-size:13px;'>
                            <p><?php echo strtoupper($sales); ?></p>
                        </td>
                    </tr>

                    
            </td>


                


                <table class="bordered" width="99%" style="">
                    <tr style='font-size:16px;font-weight:bold;'>

                        <td class="bordered" colspan=2 width="50%"><b>
                                <center>DESKRIPSI</center>
                            </b></td>
                        <td class="bordered" width="12%"><b>
                                <center>JML ROLL</center>
                            </b>
                        </td>
                    </tr>


                    <?php
                    $x = 0;
                    $total = 0;
                    $jatah = 6;


                    $query = "SELECT * FROM tb_barang_po WHERE id_transaksi = '" . $id_transaksi . "'";

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
                        echo '<td  style="padding-top:0px;padding-bottom:0px;" > ' . strtoupper($nama_barang) . ' ' . $uom . ' </td>';
                        echo '<td style="padding-top:0px;"><center>' . number_format($row1['qty']) . '</center></td>';

                        echo '</tr>';
                        $x++;
                    }

                    if ($jatah - $x > 0) {
                        for ($i = 1; $i <= ($jatah - $x); $i++) {
                            echo '<tr  style="height:22px;font-size:13px;"><td colspan="3"></td></tr>';
                        }
                    }



                    ?>


                </table>


                <table width="100%">
                    <tr>
                        
                        <td width="50%" style="font-size:16px;font-weight:bold;" valign="top">

                            <table style="margin-right:9px;margin-top:-2px;">


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