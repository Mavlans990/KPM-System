<?php
	session_start();
	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";
  
	if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
	{
	    header('Location:../index.php'); 
    }

    // $id_transaksi_filter = mysqli_real_escape_string($conn,$_GET['id_jurnal']);
    $tgl_1 = mysqli_real_escape_string($conn,$_GET['from']);                                
            $tgl_2 = mysqli_real_escape_string($conn,$_GET['to']);
    
?>

<style type="text/css">
 .d-block {
        display: block;
    }

    .container {
        margin-top :5rem;
        margin-left:2rem;
        margin-right:2rem;
        margin-bottom : 5rem;
    }


    /* .grid-container {
  display: grid;
  grid-template-columns: 100px 100px;
  grid-gap: 10px;
  padding: 10px;
}

.grid-container > div {
  text-align: left;
  padding: 10px 0;
  font-size: 15px;
} */

    .center {
        text-align : center;
        }

.table {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

.table td , .table th {
  border: 1px solid #ddd;
  padding: 8px;
}

.table tr:nth-child(even){background-color: #f2f2f2;}

.table tr:hover {background-color: #ddd;}

.table th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  
}

.justify-content-end{
    display: flex;
    justify-content: flex-end;
}
</style>

<style media="print">
    /* @media print{

    } */

 @page {
    size:A4 landscape;
    margin-top: 0.5cm;
    margin-bottom: 0.5cm;
    margin-left: 0.5cm;
    margin-right: 0.5cm;
       }
</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Besar <?php echo'('.date("d-m-Y",strtotime($tgl_1)).'/'.date("d-m-Y",strtotime($tgl_2)).')'; ?></title>
</head>
<body style="padding-left:0; padding-right:0; -webkit-print-color-adjust:exact;" >
<?php
// $query_get_jurnal = "SELECT DISTINCT no_transaksi,
//                             tgl_jurnal_umum,
//                             memo,
//                             SUM(debit) as debit_total,
//                             SUM(kredit) as kredit_total
//                     FROM tb_jurnal_umum
//                     where no_transaksi = '".$id_transaksi_filter."'
//                     order by jurnal_umum_id desc
//                     ";
// $sql_get_jurnal = mysqli_query($conn,$query_get_jurnal);
// if($row_jurnal = mysqli_fetch_array($sql_get_jurnal)){
//     $no_transaksi = $row_jurnal['no_transaksi'];
//     $tgl_transaksi = $row_jurnal['tgl_jurnal_umum'];
//     $total_debit = $row_jurnal['debit_total'];
//     $total_kredit = $row_jurnal['kredit_total'];
//     $memo = $row_jurnal['memo'];
                
echo'

<div class="container" >

    <h4 class="center">
        <span>JAYA PROTEKSINDO SAKTI</span><br>
        <span class="text-bold center">Neraca Saldo</span><br>
        <span class="center">'.date("d/m/Y",strtotime($tgl_1)).' - '.date("d/m/Y",strtotime($tgl_2)).'</span><br>
        <span class="center" style="font-size:15px;">(dalam IDR)</span><br>
    </h4>
<br>
';  

echo'
    <table class="table">
        <thead >
            <tr class="center" style="background-color: #4CAF50;">
                <th style="font-size:11px;  color: white; text-align:center;" rowspan="2" >
                    Daftar Akun
                </th>
                <th style="font-size:11px; text-align:center; color: white;"  colspan="2">
                    Saldo Awal
                </th>
                <th style="font-size:11px; text-align:center; color: white;"  colspan="2">
                    Pergerakan
                </th>
                <th style="font-size:11px; text-align:center; color: white;"  colspan="2">
                    Saldo Akhir
                </th>
            </tr>
            <tr style="font-size:11px; background-color: #4CAF50;">
                <th style="font-size:11px; text-align:center; color: white;">Debit</th>
                <th style="font-size:11px; text-align:center; color: white;">Kredit</th>
                <th style="font-size:11px; text-align:center; color: white;">Debit</th>
                <th style="font-size:11px; text-align:center; color: white;">Kredit</th>
                <th style="font-size:11px; text-align:center; color: white;">Debit</th>
                <th style="font-size:11px; text-align:center; color: white;">Kredit</th>                                  
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
                            <td style="font-size:11px; text-align:right;" class="text-right">
                                '.number_format($total_debet_awal).'
                            </td>
                            <td style="font-size:11px; text-align:right;" class="text-right">
                                '.number_format($total_kredit_awal).'
                            </td>
                            <td style="font-size:11px; text-align:right;" class="text-right">
                                '.number_format($total_debet_pergerakan).'
                            </td>
                            <td style="font-size:11px; text-align:right;" class="text-right">
                                '.number_format($total_kredit_pergerakan).'
                            </td>
                            <td style="font-size:11px; text-align:right;" class="text-right">
                                '.number_format($total_debet_akhir).'
                            </td>
                            <td style="font-size:11px; text-align:right;" class="text-right">
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


<!-- <table class="table_1">
    <thead>
        <tr>
            <td>Dibuat Oleh</td>
            <td>Diperiksa Oleh</td>
            <td>Disetujui Oleh</td>
            <td>Diterima Oleh</td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td height="100px"></td>
            <td height="100px"></td>
            <td height="100px"></td>
            <td height="100px"></td>
        </tr>
    </tbody>
</table> -->
</div>
    
</body>
</html>

<script>
    window.print();
</script>