<div class="print" style="color:black">
  <?php
  $query=mysql_query("select p.id_po1,u.nm_user from po1 p join tb_user u on p.dibuat_oleh=u.id_user where p.id_po1='".$_GET['id_po']."'");
  while ($row=mysql_fetch_array($query)) {
    $invoice=$row['id_po1'];
    $id_user=$row['nm_user'];
  }
  ?>
  <span style="font-size:7pt;font-weight:bold;">
    <center> 
    DAILY DAISY<br>
    ALAMAT TOKO<br>
    <?php echo date('d-m-y H.i.s'); ?><br>
    CASHIER : <?php echo $id_user; ?> <br>
    <?php echo $invoice; ?> 
    </center>
  </span>
  <hr>
  <table border="0" cellpadding="0px" style="font-size:7pt;margin:0 auto;">
    <tbody>
      <?php
      $total=0;
      $query=mysql_query("select p.qty,b.nm_product,p.harga,p.total from po2 p join product b on p.id_bahan=b.id_product where p.id_po1='".$_GET['id_po']."'");
      while ($row=mysql_fetch_array($query)) {
        echo '
      <tr>
        <td>'.$row['qty'].'&nbsp;&nbsp;x&nbsp;&nbsp;</td>
        <td>'.$row['nm_product'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>'.number_format($row['harga']).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td>'.number_format($row['total']).'</td>
      </tr>
      ';
      $total+=$row['total'];
      }
      echo '
      <tr><td colspan="4"><hr></td></tr>
      <tr>
        <td style="font-weight:bold;" colspan="3">TOTAL : </td>
        <td style="font-weight:bold;">'.number_format($total).'</td>
      </tr>
      ';
      ?>
    </tbody>
  </table>
  <br><br>
  <span style="font-size:7pt;">
    <center> 
    Terima Kasih atas kunjungan anda<br>
    Kami tunggu kedatangan anda kembali
    </center>
  </span>
</div>