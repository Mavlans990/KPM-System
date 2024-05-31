<?php
	session_start();
	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";
  
	if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
	{
	    header('Location:../login.php'); 
    }

    $id_transaksi_filter = mysqli_real_escape_string($conn,$_GET['id_jurnal']);
    
?>
<html>
<!-- Favicon -->
<link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="../vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="../vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet"
        type="text/css" />

    <!-- select2 CSS -->
    <link href="../vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="../vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="../vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="../dist/css/style.css" rel="stylesheet" type="text/css">
<style type="text/css">
	th {
		font-weight: bold;
	}
    .center {
        text-align : center;
    }
</style>
<style media="print">
 @page {
  size: auto;
  margin: 0;
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

<body class="bg-white">
<div style="width:85vw;" class="">
<hr>
<h4 class="text-blue center">Jurnal Umum</h4>
<hr>
<div class="d-flex justify-content-end">
    <div class="input-group input-group-sm">
        <p class="mr-2">No. Transaksi </p><p> : '.$no_transaksi.'</p>
    </div>
    <div class="input-group input-group-sm">        
        <p class="mr-2">Tanggal       </p><p> : '.$tgl_transaksi.'</p>
    </div>
</div>
<div class="row bg-blue-light-4 p-1" style="background:blue;">
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">
                                <span style="" class="text-white" id="inputGroup-sizing-sm">Akun</span> 
                            </div>
                        </div>
                        <div class="col-sm-4">
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
                    <div class="row mt-5">
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">                                
                                <span class="text-dark">'.$kode_akun.' '.$nm_akun.' </span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group input-group-sm">
                                <span class="text-dark">'.$deskripsi.'</span>
                            </div>
                        </div>
                        <div class="col-sm-4 d-flex">
                            <div class="input-group input-group-sm mr-30">
                                <span class="text-dark">'.money($debit).'</span>
                            </div>
                            <div class="input-group input-group-sm">
                                <span classs="text-dark">'.money($kredit).'</span>
                            </div>
                        </div>
                    </div>
                    ';
                    }
                    echo'

                    <div class="row mt-5 bg-blue-light-4" style="background:blue;">
                         <div class="col-sm-8">
                            <div class="input-group input-group-sm justify-content-end">
                                <span class="text-white">Total </span>
                            </div>
                        </div>
                        <div class="col-sm-4 d-flex">
                            <div class="input-group input-group-sm mr-30">
                                <span class="text-white">'.money($total_debit).'</span>
                            </div>
                            <div class="input-group input-group-sm">
                                <span classs="text-white">'.money($total_kredit).'</span>
                            </div>
                        </div>
                    </div>
                    ';
                    

    }
?>

<table style="border-style:1px; solid-black" class="table table-hover border">
    <thead class="border">
        <tr>
            <td>Dibuat Oleh</td>
            <td>Diperiksa Oleh</td>
            <td>Disetujui Oleh</td>
            <td>Diterima Oleh</td>
        </tr>
    </thead>
    <tbody>
        <tr class="border">
            <td height="100px"></td>
            <td height="100px"></td>
            <td height="100px"></td>
            <td height="100px"></td>
        </tr>
    </tbody>
</table>
</div>
</body>
</html>
<script>
	window.print();
</script>