<?php
session_start();
include "../lib/koneksi.php";
include "../lib/format.php";
include "../lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
	header('Location:index.php');
}

$tgl_from = "";
$tgl_to = "";
$supp = "";
$bah = "";
$nama_customer = '';
$nama_bahan = '';
$non_stock = "";
$where_tgl = "";
$total_belum_bayar_2 = '';
if (isset($_GET['tgl_from'])) {
	$tgl_from = mysqli_real_escape_string($conn, $_GET['tgl_from']);
	$tgl_to = mysqli_real_escape_string($conn, $_GET['tgl_to']);
	$nama_customer = mysqli_real_escape_string($conn, $_GET['nama_customer']);
	// $supp = mysqli_real_escape_string($conn, $_GET['supplier']);
	// $bah = mysqli_real_escape_string($conn, $_GET['bahan']);
	// $non_stock = mysqli_real_escape_string($conn, $_GET['nonstock']);

	// if ($supp != '') {
	// 	$select_supplier = "SELECT nama_customer FROM tb_customer where id_customer = '" . $supp . "' ";
	// 	$query_supplier = mysqli_query($conn, $select_supplier);
	// 	$row_supplier = mysqli_fetch_array($query_supplier);
	// 	$nama_customer = $row_supplier['nama_customer'];
	// }

	// if ($bah != '') {
	// 	$select_bahan = "SELECT nama_bahan FROM tb_bahan where id_bahan = '" . $bah . "' ";
	// 	$query_bahan = mysqli_query($conn, $select_bahan);
	// 	$row_bahan = mysqli_fetch_array($query_bahan);
	// 	$nama_bahan = $row_bahan['nama_bahan'];
	// } else {
	// 	$nama_bahan = "All";
	// }

    // $where_tgl = " AND a.tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "'";
    // $where_tgl1 = " AND tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "'";
    // if($nama_customer != ''){
    //     $ex_customer = explode(" | ", $nama_customer);
    //     $id_customer = $ex_customer[0];
    //     $where_customer = "AND a.id_customer ='" . $id_customer . "' ";
    // }else{
    //     $where_customer = "AND a.id_customer ='" . $id_customer . "'";
    // }

    if ($tgl_from !== "" && $tgl_to !== "") {
        $where_tgl = " AND a.tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "'";
    }
    if($nama_customer != ''){
        // $ex_customer = explode(" | ", $nama_customer);
        // $id_customer = $ex_customer[0];
        $where_customer = "AND a.id_customer ='" . $nama_customer . "' ";
        $cari_customer =  $id_customer; 
    }else{
        $cari_customer =  ""; 
        $where_customer = "";
    }

    // if($_POST['nama_customer'] != ''){
    //     $ex_customer = explode(" | ", $nama_customer);
    //     $id_customer = $ex_customer[0];
    //     $where_customer = "AND a.id_customer ='" . $id_customer . "' ";
    //     $cari_customer =  $id_customer; 
    // }else{
    //     $cari_customer =  ""; 
    //     $where_customer = "";
    // }
}

?>

<!DOCTYPE html>
<html>

<head>
	<title>Print Excel</title>
</head>

<body>
	<style type="text/css">
		body {
			font-family: sans-serif;
		}

		table {
			margin: 20px auto;
			border-collapse: collapse;
		}

		table th,
		table td {
			border: 1px solid #3c3c3c;
			padding: 3px 8px;

		}

		a {
			background: blue;
			color: #fff;
			padding: 8px 10px;
			text-decoration: none;
			border-radius: 2px;
		}
	</style>

	<?php
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=Laporan Piutang.xls");
	?>
    
	<h3>LAPORAN PIUTANG</h3>
	<h6><?= date('d/m/Y', strtotime($tgl_from)) ?> Sampai <?= date('d/m/Y', strtotime($tgl_to)) ?></h6>
    <?php
$total_keseluruhan_belum_bayar = 0;
$sql_get_customer = "select a.id_transaksi,a.id_customer,c.nama_customer from tb_barang_keluar a JOIN tb_customer c on c.id_customer = a.id_customer
WHERE id_transaksi LIKE '%SO%' ".$where_tgl." " . $where_customer . "
group by a.id_customer 
order by id_transaksi asc
";
    $data = mysqli_query($conn, $sql_get_customer);
    while ($row_customer = mysqli_fetch_array($data)) {
        $penerimaan = 0;
        $cek_utang = 0;
        $cek_bayar = 0;
    $sql_cek = mysqli_query($conn, "SELECT SUM(total) AS total,id_invoice FROM tb_barang_keluar a WHERE id_customer = '".$row_customer['id_customer']."' ".$where_tgl." GROUP BY id_invoice");
    while($row_cek = mysqli_fetch_array($sql_cek)){
        $cek_utang += $row_cek['total'];
        $sql2 = mysqli_query($conn, "select SUM(pay) AS bayar
        from inv_payment a
        where id_inv = '" . $row_cek['id_invoice'] . "' 
        ");
        while($row_sql = mysqli_fetch_array($sql2)){
            $cek_bayar += $row_sql['bayar'];
        }
    }
    // echo $cek_utang;
    // echo "  ";
    // echo $cek_bayar;
    $dnone = '';
    if($cek_utang == $cek_bayar){
        $dnone = 'd-none';
    }
?> 
<div class="table-responsive <?= $dnone ?>">
<table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
<thead>
    <tr style="font-size:9px;">
        <th>CUSTOMER</th>
        <th colspan="3">FAKTUR YANG BELUM DITAGIH</th>
    </tr>
    <tr style="font-size:9px;">
        <th><?= $row_customer['nama_customer']?></th>
        <th>NO FAKTUR</th>
        <th>TGL FAKTUR</th>
        <th>TOTAL</th>
    </tr>
</thead>
<tbody>
    <?php
   $total_sudah = 0 ;
   $total_penerimaan = 0;
   $coba = 0;    
   $total_belum = 0;
   $total_belum_bayar = 0;
   $total_all_belum_bayar = 0;
   $sql_get_piutang = "select a.id_transaksi,a.tgl_transaksi,a.dibuat_oleh,sum(total) as total from tb_barang_keluar a JOIN tb_customer c on c.id_customer = a.id_customer
   WHERE  id_invoice = '' AND id_transaksi LIKE '%SO%' AND a.id_customer = '".$row_customer['id_customer']."' ".$where_tgl." " . $where_customer . "
   GROUP by id_transaksi asc
   ";
       $data4 = mysqli_query($conn, $sql_get_piutang);
       while ($row_piutang = mysqli_fetch_array($data4)) {
        $total_belum += $row_piutang['total'];
        $coba += $row_piutang['total'];
        echo '
            <tr>
            <td></td>
            <td>' . $row_piutang['id_transaksi'] . '</td>
            <td>' .  date('d/m/Y', strtotime($row_piutang['tgl_transaksi'])) . '</td>
            <td  style="text-align:right;">' . number_format($row_piutang['total']) . ' &nbsp;</td>
        </tr>
        ';
    }
    echo '
    <tr>
            <td colspan="3" style="text-align:right;">Grand Total</td>
            <td  style="text-align:right;">' . money($total_belum) . '</td>
        </tr>
    ';
    ?>
</tbody>
<thead>
    <tr style="font-size:9px;">
    <th></th>
        <th colspan="9">FAKTUR YANG SUDAH DITAGIH</th>
    </tr>
    <tr style="font-size:9px;">
        <th></th>
        <th>NO INVOICE</th>
        <th>NO FAKTUR</th>
        <th>TGL FAKTUR</th>
        <th>NILAI FAKTUR</th>
        <th>SUBTOTAL</th>
        <th>DISC</th>
        <th>TOTAL</th>
        <th>BELUM BAYAR</th>
        <th>SUDAH BAYAR</th>
    </tr>
</thead>
<tbody>
    <?php
    $total_sudah = 0 ;
    $total_penerimaan = 0;
    $total_faktur = 0;
    $total_diskon = 0;
   $sql_total = mysqli_query($conn, "select 
   a.id_invoice,a.disc_invoice,a.total,a.tgl_transaksi,SUM(total) as subtotal
   from tb_barang_keluar a
   JOIN tb_customer c on c.id_customer = a.id_customer
   where a.id_invoice <> '' and a.id_invoice <> 'temp' AND a.id_customer = '".$row_customer['id_customer']."' ".$where_tgl." " . $where_customer . "
   GROUP BY id_invoice
   ");
   while ($row_total = mysqli_fetch_array($sql_total)) {
    $id_transaksi = '';
    $total = '';
    $tgl_faktur = '';
    $detail_payment = "";
    $total_belum_bayar = 0;
    $total_diskon=($row_total['disc_invoice']/100)*$row_total['subtotal'];
    $total_faktur = $row_total['subtotal'] - $total_diskon;
    $sql2 = mysqli_query($conn, "select *
    from inv_payment a
    join m_akun b on b.kode_akun = a.metode 
    where id_inv = '" . $row_total['id_invoice'] . "' 
  
    ");
    $ppt = 0;
    $bayar = 0;
    $total_belum = 0;
    
    while ($row2 = mysqli_fetch_array($sql2)) {
        $penerimaan = $row2['pay'];
        $ppt += $row2['pay'];
        
    }
    

    
    if($ppt < $total_faktur){
    $total_sudah += $total_faktur;
    $total_penerimaan += $ppt;
    $total_sudah_2 = $total_faktur;
    $total_penerimaan_2 = $ppt;
    $total_belum_bayar_2 += $total_sudah_2 - $total_penerimaan_2;
    $total_belum_bayar += $total_sudah - $total_penerimaan;
    $total_all_belum_bayar += $total_belum_bayar_2;
    $total_belum = $row_total['subtotal'] - $ppt;
    $bayar += $ppt;
    echo' 
    <tr>
            <td></td>
            <td>' . $row_total['id_invoice'] . '</td>';
            $sql = mysqli_query($conn, "select 
            a.id_invoice,a.subtotal_invoice,a.total_invoice,a.disc_invoice,a.cust_invoice,a.tgl_jatuh_tempo,a.id_transaksi,a.tgl_transaksi,a.total
            from tb_barang_keluar a
            JOIN tb_customer c on c.id_customer = a.id_customer
            where a.id_invoice = '".$row_total['id_invoice']."' AND a.id_customer = '".$row_customer['id_customer']."' ".$where_tgl." " . $where_customer . "
            ");
                while ($row = mysqli_fetch_array($sql)) {
                    $id_transaksi = $id_transaksi.$row['id_transaksi'] . '<br>';
                    $total = $total.money($row['total']). '<br>';
                    $tgl_faktur = $tgl_faktur. date('d/m/Y', strtotime($row['tgl_transaksi'])). '<br>';
                }
                 echo '
            <td>' .  $id_transaksi . '</td>
            <td>' .  $tgl_faktur . '</td>
            <td>' .  $total . '</td>
            <td>' .  money($row_total['subtotal']) . '</td>
            <td>' .  round($row_total['disc_invoice'],2) . '%</td>
            <td style="text-align:right;">' . money($total_faktur) . '</td>
            <td  style="text-align:right;">' . money($total_belum) . '</td>
            <td  style="text-align:right;">' . money($bayar) . '</td>
            </tr>
            ';
            
        
        $total_keseluruhan_belum_bayar = $total_all_belum_bayar;
    }
}
        echo '                                        
    <tr>
    <td colspan="7" style="text-align:right;">Grand Total</td>
    <td  style="text-align:right;">' . money($total_sudah) . '</td>
    <td  style="text-align:right;">' . money($total_sudah-$total_penerimaan) . '</td>
    <td  style="text-align:right;">' . money($total_penerimaan) . '</td>
    </tr>';
                    
            ?>
            </tbody>
        </table>
        <br>
    <?php } ?>
        <table>
            <thead>
                <tr>
                    <th colspan="9" style="text-align:right;">Total Belum Bayar</th>  
                    <th style="text-align:right;"><?= money($total_keseluruhan_belum_bayar) ?></th>
                </tr>
            </thead>
        </table>
</body>

</html>