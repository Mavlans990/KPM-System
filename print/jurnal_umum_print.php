<?php
	session_start();
	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";
  
	if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
	{
	    header('Location:../index.php'); 
    }

    $id_transaksi_filter = mysqli_real_escape_string($conn,$_GET['id_jurnal']);
    
?>

<style type="text/css">
	
    .d-block {
        display: block;
    }

    .container {
        margin-top :5rem;
        margin-left:3rem;
        margin-right:3rem;
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
  border-collapse: collapse;
  width: 100%;
  
}

.table th, .table td {
  text-align: left;
  padding: 8px;
  
}

.table tr:nth-child(even){background-color: #edf7fe }

.table th {
  background-color: #0092ee;
  color: white;
}

.table_1 {
    margin-top : 5rem;
  border-collapse: collapse;
  width: 100%;
  border: 1px solid black;
}

.table_1 th, .table_1 td {
    border: 1px solid black;  
}

.table_1 th {
  text-align:center;
  color: black;
}

.justify-content-end{
    display: flex;
    justify-content: flex-end;
}
</style>

<style media="print">
 @page {
  size: auto;
  margin: 1rem;
       }
</style>
<?php
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
                
echo'

<div class="container" >

<hr>
    <h2 class="text-bold center">JURNAL UMUM</h2>
<hr>
<div class="justify-content-end">
            <p>No. Transaksi &nbsp</p><p> : '.$no_transaksi.'</p>
</div>
<div class="justify-content-end">
            <p>Tanggal &nbsp</p><p> : '.$tgl_transaksi.'</p>
</div>
<table class="table">
    <thead>
        <tr style="background:blue;" >
            <th>Akun</th>
            <th>Deskripsi</th>
            <th>Debit</th>
            <th>Kredit</th>
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
                       <td>'.$kode_akun.' '.$nm_akun.'</td>
                       <td>'.$deskripsi.'</td>
                       <td>'.money($debit).'</td>
                       <td>'.money($kredit).'</td>
                       </tr>
                       ';

               }
        echo'
                <tr style="background-color: #0092ee; color: white;">
                       
                    <td colspan="2" style="text-align:right;">Total</td>
                    <td>'.money($total_debit).'</td>
                    <td>'.money($total_kredit).'</td>
                </tr>
    </tbody>
</table>

';
}
?>

<table class="table_1">
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
</table>
</div>

<script>
    window.print();
</script>