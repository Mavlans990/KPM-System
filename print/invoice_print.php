<?php
  session_start();
  include "../lib/koneksi.php";
  include "../lib/appcode.php";
  include "../lib/format.php";
  
    if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
    {
        header('Location:../login.php'); 
    }

  function penyebut($nilai) {
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
      $temp = " ". $huruf[$nilai];
    } else if ($nilai <20) {
      $temp = penyebut($nilai - 10). " belas";
    } else if ($nilai < 100) {
      $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
    } else if ($nilai < 200) {
      $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
      $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
      $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
      $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
      $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
      $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
    } else if ($nilai < 1000000000000000) {
      $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
    }     
    return $temp;
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>RocketAdmin</title>

  <!-- Custom fonts for this template-->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <!-- Custom styles for this page -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:500,500i&display=swap" rel="stylesheet">
  <style type="text/css">
    .row { 
      font-family: 'Roboto', sans-serif;
    }
    table thead tr th {
      text-align:center;
    }
    .list-item tr td {
      padding: 0 5px;
    }
    @media print {
      .button {
        display: none;
      }
    }
  </style>
</head>

<body id="page-top">
  <div class="row" style="color:black">
    <?php
    $query=mysql_query("SELECT o.id_order,o.tgl_order,m.nm_member,p.telp,p.email,p.alamat,o.ongkir,o.amount FROM order_main o JOIN member m ON o.id_cust=m.id_member JOIN pengiriman p ON o.id_order=p.id_order WHERE o.id_order='".mysql_real_escape_string($_GET['order'])."'");
    $data=mysql_fetch_array($query);
    $invoice=$data['id_order'];
    $member=$data['nm_member'];
    $tgl=$data['tgl_order'];
    $telp=$data['telp'];
    $email=$data['email'];
    $alamat=$data['alamat'];
    $ongkir=$data['ongkir'];
    $amount=$data['amount'];
    ?>
    <div class="col-lg-12 button" style="margin-top: 10px">
      <a href="../stock-out.php?kat=sended" class="btn btn-danger">< Kembali</a>
      <a href="#" onclick="window.print()" class="btn btn-info">Print</a>
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
      <h2><i>ROCKET DIMSUM</i></h2>
      PT. MAHAKARYA CIPTA RASA<br>
      Komp. Akita 2 No 18 E<br>
      Bojongsoang, Kab. Bandung<br>
      WA 082299991916
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
      <img src="../img/23596073_128932111154427_7817681454791393280_n.jpg" width="150" style="float:right;vertical-align:bottom;">
    </div>
    <div class="col-lg-12" style="border-top:2px solid;margin-bottom:10px"></div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
      <table>
        <tr>
          <td>Kepada</td>
          <td style="padding: 0 5px 0 15px"> : </td>
          <td><?php echo $member; ?></td>
        </tr>
        <tr>
          <td>Nomor HP</td>
          <td style="padding: 0 5px 0 15px"> : </td>
          <td><?php echo $telp; ?></td>
        </tr>
        <tr>
          <td>Email</td>
          <td style="padding: 0 5px 0 15px"> : </td>
          <td><?php echo $email; ?></td>
        </tr>
        <tr>
          <td>Alamat</td>
          <td style="padding: 0 5px 0 15px"> : </td>
          <td><?php echo $alamat; ?></td>
        </tr>
        <tr>
          <td colspan="3" style="padding-top:10px"></td>
        </tr>
      </table>
    </div>
    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-xs-6">
      <table>
        <tr>
          <td>Nomor</td>
          <td style="padding: 0 5px 0 15px"> : </td>
          <td><?php echo $invoice; ?></td>
        </tr>
        <tr>
          <td>Tanggal</td>
          <td style="padding: 0 5px 0 15px"> : </td>
          <td><?php echo date('d/m/Y', strtotime($tgl)); ?></td>
        </tr>
        <tr>
          <td>Sales</td>
          <td style="padding: 0 5px 0 15px"> : </td>
          <td><?php  ?></td>
        </tr>
        <tr>
          <td>No. PO</td>
          <td style="padding: 0 5px 0 15px"> : </td>
          <td><?php  ?></td>
        </tr>
        <tr>
          <td>Jatuh Tempo</td>
          <td style="padding: 0 5px 0 15px"> : </td>
          <td><?php  ?></td>
        </tr>
        <tr>
          <td colspan="3" style="padding-top:10px"></td>
        </tr>
      </table>
      <div style="width: 100%;padding: 5px;border:1px solid;text-align: center;">
        <i>FAKTUR PENJUALAN</i>
      </div>
    </div>
    <div class="col-lg-12" style="margin-top:20px">
      <table width="100%" border="1">
        <thead>
          <tr>
            <th>NO</th>
            <th>KODE BARANG</th>
            <th>NAMA BARANG</th>
            <th>JUMLAH</th>
            <th>HARGA SATUAN</th>
            <th>SUB TOTAL</th>
          </tr>
        </thead>
        <tbody class="list-item">
          <?php
          $x=1;
          $sub=0;
          $sql=mysql_query("SELECT d.id_product,p.nm_product,d.qty,d.harga_final,d.total FROM order_detail d JOIN product p ON d.id_product=p.id_product WHERE d.id_order = '".mysql_real_escape_string($_GET['order'])."'");
          while ($row=mysql_fetch_array($sql)) {
            $sub+=$row['total'];
            echo '
          <tr>
            <td align="center">'.$x.'</td>
            <td>'.$row['id_product'].'</td>
            <td>'.$row['nm_product'].'</td>
            <td align="center">'.$row['qty'].'</td>
            <td align="right">'.money_idr($row['harga_final']).'</td>
            <td align="right">'.money_idr($row['total']).'</td>
          </tr>
            ';
            $x++;
          } ?>
          <tr>
            <td colspan="5" style="vertical-align: top;">TERBILANG :<span style="font-size: 20px"><?php echo ucwords(penyebut($amount)); ?></span></td>
            <td align="right"><?php echo money_idr($sub); ?></td>
          </tr>
          <tr>
            <td colspan="5" align="right">ONGKIR</td>
            <td align="right"><?php echo money_idr($ongkir); ?></td>
          </tr>
          <tr>
            <td colspan="5" align="right">TOTAL</td>
            <td align="right" ><b><?php echo money_idr($amount); ?></b></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>