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

.perbatasan {
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
    size:auto;
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
        <span class="text-bold center">Buku Besar</span><br>
        <span class="center">'.date("d/m/Y",strtotime($tgl_1)).' - '.date("d/m/Y",strtotime($tgl_2)).'</span><br>
    </h4>
<br>
';  
    $no = 1;
    $grand_total_debit = 0;
    $grand_total_kredit = 0;
    $query_get_akun = mysqli_query($conn,"SELECT DISTINCT a.nm_akun,a.kode_akun,a.kat_akun FROM tb_jurnal_umum ju JOIN m_akun a ON a.kode_akun = ju.kode_akun ");

    while($row_akun = mysqli_fetch_array($query_get_akun)){
        $kat_akun = $row_akun['kode_akun'];
        echo'    
        <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
        <thead class="">
            <tr>
                <th colspan="7" clas="text-left" style="margin-top:1rem;">
                    <div>
                        <span><strong>('.$kat_akun.') '.$row_akun['nm_akun'].'</strong></span>
                    </div>  
                </th>
            </tr>
            <tr class="perbatasan" style="background-color: #4CAF50; color: white;">
                <th class="perbatasan" style="width:100px; min-width:60px; max-width:100px;">Tanggal</th>
                <th class="perbatasan" style="width:200px; min-width:100px; max-width:200px;">Transaksi</th>
                <th class="perbatasan" style="width:100px; min-width:80px; max-width:100px;">Nomor</th>
                <th class="perbatasan" style="width:300px; min-width:200px; max-width:400px;">Keterangan</th>
                <th class="perbatasan" style="width:150px; min-width:130px; max-width:150px;">Debet</th>
                <th class="perbatasan" style="width:150px; min-width:130px; max-width:150px;">Kredit</th>
                <th class="perbatasan" style="width:150px; min-width:130px; max-width:150px;">Saldo</th>                                  
            </tr>
        </thead>
        <tbody class="table-bordered border-solid">
            ';   
            $no_urut = 1;
            
            if($tgl_2 !== "" || $tgl_1 !== ""){
                $where = "AND tgl_jurnal_umum BETWEEN '".$tgl_1."' AND '".$tgl_2."' ";
            }  

            $sisa = 0;
            $sisa_hasil = 0;
            $debet = 0;
            $kredit = 0;
            $total_debet = 0;
            $total_kredit = 0;
            $sisa_saldo = 0;
            $result = 0;
            $total_saldo_awal = 0;
            // Hitung ulang saldo awal
            // echo "SELECT no_transaksi as nomor, memo as ket,tgl_jurnal_umum as tgl,debit as debet,kredit as kredit, kode_akun as kode FROM tb_jurnal_umum WHERE kode_akun = '".$kat_akun."' and tgl_jurnal_umum < '".$tgl_1."' and status_jurnal = 'Posted' ORDER BY tgl_jurnal_umum ASC";
            $sql_det = mysqli_query($conn,"SELECT no_transaksi as nomor, memo as ket,tgl_jurnal_umum as tgl,debit as debet,kredit as kredit, kode_akun as kode FROM tb_jurnal_umum WHERE kode_akun = '".$kat_akun."' and tgl_jurnal_umum < '".$tgl_1."' and status_jurnal = 'Posted' ORDER BY tgl_jurnal_umum ASC ");

            while($row_det = mysqli_fetch_array($sql_det)){
                $nomor = $row_det['nomor'];
                $ket = $row_det['ket'];
                $kode = $row_det['kode'];
                $date = $row_det['tgl'];
                $debet = $row_det['debet'];
                $kredit = $row_det['kredit'];
                

                $total_debet += $row_det['debet'] ;
                $total_kredit += $row_det['kredit'] ;
                if( $kat_akun == "1" || $kat_akun == "2" || 
                    $kat_akun == "3" || $kat_akun == "4" || 
                    $kat_akun == "5" || $kat_akun == "6" ||
                    $kat_akun == "15" || $kat_akun == "16" ||
                    $kat_akun == "17" ){
                    $sisa_saldo = $sisa_saldo + $row_det['debet'] - $row_det['kredit'];
                }else{
                    $sisa_saldo = $sisa_saldo + $row_det['kredit'] - $row_det['debet'];
                }

                $total_saldo_awal = $sisa_saldo;
                if(strpos($total_saldo_awal,"-") > -1){
                    
                    $hasil = preg_replace("/[^a-zA-Z0-9\,]/", "", $total_saldo_awal);
                    $hasil = number_format($hasil);
                    $total_saldo_awal = "(".$hasil.")";
                }

                
            }
            $tgl_saldo_awal = date('d/m/Y', strtotime('-1 days', strtotime($tgl_1))); 

            $hidden = "";
            if($total_saldo_awal == 0){
                $hidden = "hidden";
            }
            echo'
                <tr '.$hidden.' class="perbatasan">
                    <td class="perbatasan">'.$tgl_saldo_awal.'</td>
                    <td class="perbatasan">Saldo Awal</td>
                    <td class="perbatasan"></td>
                    <td class="perbatasan"></td>
                    <td class="perbatasan"></td>
                    <td class="perbatasan"></td>
                    <td class="perbatasan" style="text-align:right;">'.$total_saldo_awal.'</td>
                </tr>

                ';
            
            // Hitung ulang sesuai tanggal yang di pilih
            $sisa = 0;
            $debet = 0;
            $kredit = 0;
            $total_debet = 0;
            $total_kredit = 0;
            $sisaan = 0;
            $result = 0;
            // echo"SELECT no_transaksi as nomor, memo as ket,tgl_jurnal_umum as tgl,debit as debet,kredit as kredit FROM tb_jurnal_umum WHERE kode_akun = '".$kat_akun."' ".$where." and status_jurnal = 'Posted' ORDER BY tgl_jurnal_umum ASC ";
            $sql_det = mysqli_query($conn,"SELECT no_transaksi as nomor, memo as ket,tgl_jurnal_umum as tgl,debit as debet,kredit as kredit, kode_akun as kode FROM tb_jurnal_umum WHERE kode_akun = '".$kat_akun."' ".$where." and status_jurnal = 'Posted' ORDER BY tgl_jurnal_umum ASC ");
            while($row_det = mysqli_fetch_array($sql_det)){
                $nomor = $row_det['nomor'];
                $ket = $row_det['ket'];
                $kode = $row_det['kode'];
                $date = $row_det['tgl'];
                $debet = $row_det['debet'];
                $kredit = $row_det['kredit'];
                

                $total_debet += $row_det['debet'] ;
                $total_kredit += $row_det['kredit'] ;
                if( $kat_akun == "1" || $kat_akun == "2" || 
                    $kat_akun == "3" || $kat_akun == "4" || 
                    $kat_akun == "5" || $kat_akun == "6" ||
                    $kat_akun == "15" || $kat_akun == "16" ||
                    $kat_akun == "17" ){
                    $sisa = $sisa + $row_det['debet'] - $row_det['kredit'];
                }else{
                    $sisa = $sisa + $row_det['kredit'] - $row_det['debet'];
                }

                if(strpos($kredit,"-") > -1){  
                    // $kredit = preg_replace("/[^a-zA-Z0-9\,]/", "", $kredit);
                    $kredit = number_format($kredit);
                    $kredit = "(".$kredit.")";
                }else{
                    $kredit = number_format($kredit);
                }

                $sisa_hasil = $sisa;
                if(strpos($sisa_hasil,"-") > -1){
                    
                    $hasil = preg_replace("/[^a-zA-Z0-9\,]/", "", $sisa_hasil);
                    $hasil = number_format($hasil);
                    $sisa_hasil = "(".$hasil.")";
                }

                echo'
                <tr class="perbatasan">
                    <td class="perbatasan">'.date('d/m/Y', strtotime($date)).'</td>
                    <td class="perbatasan">Jurnal Umum</td>
                    <td class="perbatasan center">'.$nomor.'</td>
                    <td class="perbatasan">'.$ket.'</td>
                    <td class="perbatasan" style="text-align:right;">'.number_format($row_det['debet']).'</td>
                    <td class="perbatasan" style="text-align:right;">'.$kredit.'</td>
                    <td class="perbatasan" style="text-align:right;">'.$sisa_hasil.'</td>
                </tr>

                ';
            }
            
            $result = $sisa_saldo + $sisa;
            if(strpos($result,"-") > -1){
                $result = preg_replace("/[^a-zA-Z0-9\,]/", "", $result);
                $result = number_format($result);
                $result = "(".$result.")";
            }
            echo'
                <tr class="perbatasan" >
                    <td class="perbatasan" colspan="4" style="text-align:right;" ><strong>SUMMARY</strong></td>                                            
                    <td class="perbatasan" style="align-text:right; text-align:right;"><strong >'.number_format($total_debet).'</strong></td>
                    <td class="perbatasan" style="align-text:right; text-align:right;"><strong >'.number_format($total_kredit).'</strong></td>
                    <td class="perbatasan" style="align-text:right; text-align:right;"><strong >'.$result.'</strong></td>
                </tr>
            </tbody>';
            $no++; 

            $grand_total_debit = $grand_total_debit + $total_debet;
            $grand_total_kredit = $grand_total_kredit + $total_kredit;
        }
        echo'
        <tbody mt-15>
        <tr class="perbatasan">                                
            <td class="perbatasan" colspan="4" style="text-align:right;">Grand Total</td>                                            
            <td class="perbatasan" class="text-info text-right" style="text-align:right;"><strong>'.number_format($grand_total_debit).'</strong></td>
            <td class="perbatasan" class="text-info text-right" style="text-align:right;"><strong>'.number_format($grand_total_kredit).'</strong></td>
            <td class="perbatasan"></td>
        </tr>
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