<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
  header('Location:index.php');
}

if (isset($_GET['order'])) {
  $query = mysqli_query($conn, "SELECT * FROM sj1 WHERE id_sj1='" . mysqli_real_escape_string($conn, $_GET['order']) . "'");
  $sts = mysqli_fetch_array($query);
  $status = $sts['status_sj'];
  $ongkir = $sts['ongkir'];
  $disc = $sts['disc'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
  <title>Admin Reydecal</title>
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
  <!-- Custom styles for this page -->
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

  <style type="text/css">
    .print {
      display: none;
    }

    @media print {
      .non-print {
        display: none;
      }

      .print {
        display: block;
        font-family: 'PT Mono', monospace;
      }
    }

    del {
      color: red;
    }
  </style>
  <link href="https://fonts.googleapis.com/css?family=PT+Mono" rel="stylesheet">
</head>

<body id="page-top">
  <div class="non-print">

    <!-- Page Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">
      <!-- Sidebar -->
      <?php include "header.php"; ?>
      <!-- End of Sidebar -->

      <!-- Content Wrapper -->
      <div class="hk-wrapper hk-vertical-nav">

        <!-- Main Content -->
        <div class="hk-pg-wrapper">

          <!-- Topbar -->
          <?php //include "part/topbar.php"; 
          ?>
          <!-- End of Topbar -->

          <!-- Begin Page Content -->
          <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
            <!-- Page Heading -->
            <!-- <h1 class="h3 mb-2 text-gray-800">
              <?php
              if ($status == 'n') {
                echo "Order Received, Waiting for Payment";
              } elseif ($status == 'c') {
                echo "Payment Received, Waiting for Shipping";
              } elseif ($status == 's') {
                echo "Order has been Shipped";
              }
              ?>
            </h1> -->

            <!-- DataTales Example -->
            <div class="card shadow mb-4">
              <div class="card-body">
                <?php
                if (isset($_GET['order'])) {
                  $query = mysqli_query($conn, "SELECT * FROM sj1 WHERE id_sj1='" . mysqli_real_escape_string($conn, $_GET['order']) . "'");
                  $data = mysqli_fetch_array($query);
                  $disc_nominal = $data['disc'];
                  if ($data['id_sj1'] !== "") {
                    $resi = '(' . $data['id_sj1'] . ')';
                  } else {
                    $resi = "";
                  }

                  echo '
                <table>
                  <tr>
                    <td><b>INVOICE </b>&nbsp;&nbsp;</td>
                    <td>: ' . $data['id_sj1'] . '</td>
                  </tr>
                  <tr>
                    <td><b>Tanggal Transaksi </b>&nbsp;&nbsp;</td>
                    <td>: ' . date('d-m-Y H.i.s', strtotime($data['tgl_sj'])) . '</td>
                  </tr>
                  <tr>
                    <td><b>Pelanggan </b>&nbsp;&nbsp;</td>
                    <td>: ' . $data['nm_cust'] . '</td>
                  </tr>
                  <tr>
                    <td><b>Email </b>&nbsp;&nbsp;</td>
                    <td>: ' . $data['id_cust'] . '</td>
                  </tr>
                  <tr>
                    <td><b>No. Telepon </b>&nbsp;&nbsp;</td>
                    <td>: ' . $data['telp_cust'] . '</td>
                  </tr>
                  <tr>
                    <td><b>Alamat </b>&nbsp;&nbsp;</td>
                    <td>: ' . $data['alamat'] . '</td>
                  </tr>
                  <tr>
                    <td><b>Kode Pos </b>&nbsp;&nbsp;</td>
                    <td>: ' . $data['kode_pos'] . '</td>
                  </tr>
                  <tr>
                    <td><b>Catatan </b>&nbsp;&nbsp;</td>
                    <td>: ' . $data['notes'] . '</td>
                  </tr>
                  
                  <tr>
                    <td><b>Ongkir </b>&nbsp;&nbsp;</td>
                    <td>: Rp. ' . number_format($data['ongkir']) . '</td>
                  </tr>
                </table>
                ';
                }
                ?>
              </div>
            </div>

            <div class="card shadow mb-4">
              <div class="card-body">
                <div style="overflow-x:auto;">
                  <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                    <thead>
                      <tr>
                        <th>Nama Produk</th>
                        <th>Tipe</th>
                        <th>QTY</th>
                        <th>Harga</th>
                        <th></th>
                        <th>Total</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (isset($_GET['order'])) {
                        $total = 0;
                        if   ($_SESSION['grup'] !== "super") {
                          $where_branch = " AND id_cabang = '" . $_SESSION['branch'] . "'";
                        } else {
                          $where_branch = "";
                        }
                        $query = mysqli_query($conn, "SELECT * FROM sj2 WHERE id_sj1 = '" . mysqli_real_escape_string($conn, $_GET['order']) . "'" . $where_branch);
                        while ($row = mysqli_fetch_array($query)) {
                          if ($row['no_seri'] == "decal") {
                            $select_product = "SELECT * FROM product WHERE id_product = '" . $row['id_bahan'] . "'";
                            $query_product = mysqli_query($conn, $select_product);
                            $data_product = mysqli_fetch_array($query_product);

                            $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row['id_cabang'] . "'";
                            $query_cabang = mysqli_query($conn, $select_cabang);
                            $data_cabang = mysqli_fetch_array($query_cabang);

                            $select_motor = "SELECT * FROM tb_motor WHERE filter = '" . $data_product['jenis_motor'] . "'";
                            $query_motor = mysqli_query($conn, $select_motor);
                            $data_motor = mysqli_fetch_array($query_motor);

                            $select_bahan = "SELECT * FROM tb_bahan WHERE filter = '" . $row['bahan'] . "'";
                            $query_bahan = mysqli_query($conn, $select_bahan);
                            $data_bahan = mysqli_fetch_array($query_bahan);

                            $harga = $data_bahan['harga'] + $data_motor['harga_plus'] - $data_motor['harga_minus'];

                            $bahan = "<p>Bahan : " . $data_bahan['nama_bahan'] . "</p>";
                            if ($row['id_cabang'] !== "") {
                              if ($row['express'] !== "dirumah") {
                                $cabang = "<p>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                              }
                              $pasang = "<p>Pemasangan : Dipasangkan</p>";
                              $tanggal_pasang = "<p>Tanggal Pasang : " . date("D, d M Y", strtotime($row['tgl_pasang'])) . "</p>";
                            } else {
                              $cabang = "";
                              $pasang = "<p>Pemasangan : Pasang Sendiri</p>";
                              $tanggal_pasang = "";
                            }

                            if ($row['stat_cus'] == "yes") {
                              $stat_cus = "<p>Body Custom : Ya</p>";
                            } else {
                              $stat_cus = "<p>Body Custom : Tidak</p>";
                            }

                            if ($row['part'] == "yes") {
                              $part = "<p>Body cat ulang / baret : Ya</p>";
                            } else {
                              $part = "<p>Body cat ulang / baret : Tidak</p>";
                            }

                            if ($row['express'] == "noexpress") {
                              $paket = " <p>Paket : Regular</p>";
                            } else if ($row['express'] == "express") {
                              $paket = "<p>Paket : Express (+ Rp. " . number_format(350000) . ")</p>";
                            } else if ($row['express'] == "dirumah") {
                              $paket = "<p>Paket : Pasang Dirumah (+ Rp. " . number_format(500000) . ")</p>";
                            } else {
                              $paket = "";
                            }
                          } else {
                            $select_product = "SELECT * FROM product_lain WHERE id_product = '" . $row['id_bahan'] . "'";
                            $query_product = mysqli_query($conn, $select_product);
                            $data_product = mysqli_fetch_array($query_product);

                            $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row['id_cabang'] . "'";
                            $query_cabang = mysqli_query($conn, $select_cabang);
                            $data_cabang = mysqli_fetch_array($query_cabang);

                            $harga = $data_product['harga'];

                            if ($row['id_cabang'] !== "") {
                              $stat_cus = "";
                              $part = "";
                              $custom = "";
                              $bahan = "";
                              $cabang = "<p>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                              $pasang = "";
                              $paket = "<p>Paket : Dipasangkan</p>";
                              $tanggal_pasang = "<p>Tanggal Pasang : " . date("D, d M Y", strtotime($row['tgl_pasang'])) . "</p>";
                            } else {
                              $stat_cus = "";
                              $part = "";
                              $custom = "";
                              $bahan = "";
                              $pasang = "";
                              $paket = "";
                              $tanggal_pasang = "";
                              $cabang = "";
                            }
                          }



                          if ($row['stat_cus'] == "yes") {
                            if ($row['custom'] !== "") {
                              $custom = "<p>Keterangan body custom : " . $row['custom'] . "</p>";
                            } else {
                              $custom = "";
                            }
                          } else {
                            $custom = "";
                          }

                          if ($row['part'] == "yes") {
                            if ($row['custom_part'] !== "") {
                              $part_reason = "<p>Keterangan body cat ulang / baret : " . $row['custom_part'] . "</p>";
                            } else {
                              $part_reason = "";
                            }
                          } else {
                            $part_reason = "";
                          }

                          $tipe = $row['no_seri'];
                          if ($row['no_seri'] == "other") {
                            $tipe = "Lainnya";
                          }

                          $query_product = mysqli_query($conn, $select_product);
                          $data_product = mysqli_fetch_array($query_product);
                          echo '
                      <tr>
                        <td><b style="font-weight:bold;">
                        ' . $data_product['nm_product'] . '</b> 
                        ' . $bahan . $pasang .  $paket . $cabang . $tanggal_pasang . $stat_cus . $part . $custom . $part_reason . '</td>
                        <td align="center">' . ucfirst($tipe) . '</td>
                        <td align="center">' . floatval($row['qty']) . '</td>
                        <td align="left">' . money_idr($harga) . '</td>
                        <td align="left"></td>
                        <td align="left">' . money_idr($row['total']) . '</td>
                        ';
                          $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row['id_cabang'] . "'";
                          $query_cabang = mysqli_query($conn, $select_cabang);
                          $data_cabang = mysqli_fetch_array($query_cabang);
                          echo '
                       
                        
                      </tr>
                        ';

                          $total = $total + $row['total'];
                        }
                      }

                      ?>
                      <tr id="separator">
                        <td colspan="6" bgcolor="#dddfeb" style="padding:5px"></td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><b>Subtotal</b><input type="hidden" class="total_val" name="total_val" id="total_val" value="<?php echo number_format($total); ?>"></td>
                        <td align="left"><b>Rp. </b><input type="text" class="totalbelanja" name="totalbelanja" id="totalbelanja" value="<?php echo number_format($total); ?>" readonly style="color:#858796;outline:none !important;border:0px;font-weight:bolder;width:100px;text-align:right;"></td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><b>Ongkir</b><input type="hidden" class="total_val" name="total_val" id="total_val" value="<?php echo number_format($ongkir); ?>"></td>
                        <td align="left"><b>Rp. </b><input type="text" class="totalbelanja" name="totalbelanja" id="totalbelanja" value="<?php echo number_format($ongkir); ?>" readonly style="color:#858796;outline:none !important;border:0px;font-weight:bolder;width:100px;text-align:right;"></td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><b>Diskon</b><input type="hidden" class="total_val" name="total_val" id="total_val" value="<?php echo number_format($disc); ?>"></td>
                        <td align="left"><b>Rp. </b><input type="text" class="totalbelanja" name="totalbelanja" id="totalbelanja" value="<?php echo number_format($disc); ?>" readonly style="color:#858796;outline:none !important;border:0px;font-weight:bolder;width:100px;text-align:right;"></td>
                      </tr>
                      <tr>
                        <td colspan="5" align="right"><b>Total</b><input type="hidden" class="total_val" name="total_val" id="total_val" value="<?php echo number_format($total + $ongkir - $disc); ?>"></td>
                        <td align="left"><b>Rp. </b><input type="text" class="totalbelanja" name="totalbelanja" id="totalbelanja" value="<?php echo number_format($total); ?>" readonly style="color:#858796;outline:none !important;border:0px;font-weight:bolder;width:100px;text-align:right;"></td>
                      </tr>
                    </tbody>
                  </table>
                </div>

              </div>
            </div>

          </div>
          <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->



        <div class="modal fade" id="approve" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Payment Confirmation</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <form action="modul/order.php" method="POST">
                <div class="modal-body">
                  <input type="hidden" class="form-control appr" name="appr">
                  If the payment confirmation email has entered, you can confirm the payment.
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                  <input type="submit" class="btn btn-info" name="konfirmasi" value="Konfirmasi">
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="modal fade" id="resi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Send Receipt</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <form action="modul/order.php" method="POST">
                <div class="modal-body">
                  <input type="hidden" class="form-control sendresi" name="sendresi">
                  <div class="form-group">
                    <label>Receipt Number</label>
                    <input type="text" class="form-control noresi" name="noresi" required>
                  </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                  <input type="submit" class="btn btn-info" name="resionly" value="Kirim Resi">
                  <input type="submit" class="btn btn-success" name="paket" value="Kirim Resi & Pesanan">
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="modal fade" id="kirim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Send Order</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <form action="modul/order.php" method="POST">
                <div class="modal-body">
                  <input type="hidden" class="form-control send" name="send">
                  Send this order ?<br>
                  The order status will change to <b>Sended</b> if you have sent a receipt & confirmation send the order.
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                  <input type="submit" class="btn btn-success" name="pesanan" value="Kirim Pesanan">
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="modal fade" id="deleteorder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Product</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
              </div>
              <form action="modul/order.php" method="POST">
                <div class="modal-body">
                  <input type="hidden" class="form-control id_hapus" name="id_hapus">
                  Remove this product from the transaction?
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                  <input type="submit" class="btn btn-danger" name="delete" value="Delete">
                </div>
              </form>
            </div>
          </div>
        </div>


        <div class="print" style="color:black">

          <center>
            <?php
            $query = mysqli_query($conn, "select p.id_so1,u.nm_user from so1 p join tb_user u on p.dibuat_oleh=u.id_user where p.id_so1='" . $_GET['order'] . "'");
            while ($row = mysqli_fetch_array($query)) {
              $invoice = $row['id_so1'];
              $id_user = $row['nm_user'];
            }
            ?>
            <span style="font-size:35pt;font-weight:bold;">

              <img src="img/23596073_128932111154427_7817681454791393280_n2.jpg" width="700px" /> <br><br>
              SNS SNEAKERS<br>
              JL. Mangga Raya No.11 <br>(sebelah holland bakery)<br>

              <?php echo date('d-m-y H.i.s'); ?><br>
              <?php echo $invoice; ?> <BR>
              CASHIER : <?php echo $id_user; ?> <br>

              -----------------------------------<BR>

            </span>
            <hr>
            <table border="0" cellpadding="0px" style="font-size:35pt;">
              <tbody>
                <?php
                $total = 0;
                $query = mysqli_query($conn, "select p.qty,b.nm_product,p.harga,p.total from so2 p join product b on p.id_bahan=b.id_product where p.id_so1='" . $_GET['order'] . "'");
                while ($row = mysqli_fetch_array($query)) {
                  echo '
      <tr>
        <td>' . $row['qty'] . '&nbsp;&nbsp;x&nbsp;&nbsp;</td>
        <td>' . $row['nm_product'] . '</td>
      
        <td>' . number_format($row['total']) . '</td>
      </tr>
      ';
                  $total += $row['total'];
                }
                echo '
      <tr><td colspan="4"> -----------------------------------</td></tr>
      <tr>
        <td style="font-weight:bold;" colspan="2">TOTAL : </td>
        <td style="font-weight:bold;">' . number_format($total) . '</td>
      </tr>
      ';
                ?>
              </tbody>
            </table>
            <br>

            <span style="font-size:35pt;font-weight:bold;">
              THANKS FOR SHOPPING <br>

              <img src="img/33447.png" width="50px"> 0858-6368-9287
              <img src="img/33447.png" width="50px"> 0812-8625-4620 <br>
              <img src="img/87390.png" width="50px"> sns.sneakers
              <img src="img/87390.png" width="50px"> sns.apparels <br>
              <img src="img/87386.png" width="50px"> @sns.sneakers
              <br>
            </span>
          </center>
        </div>

        <!-- Bootstrap core JavaScript-->
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

        <!-- Page level plugins -->
        <script src="vendor/datatables/jquery.dataTables.min.js"></script>
        <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

        <!-- Page level custom scripts -->
        <script src="js/demo/datatables-demo.js"></script>

</body>

</html>
<script type="text/javascript">
  $(document).on("click", '.hapus_button', function(e) {
    var id_hapus = $(this).data('id');
    $(".id_hapus").val(id_hapus);
  });

  $(document).on("click", '.approve', function(e) {
    var approve = $(this).data('approve');
    $(".appr").val(approve);
  });

  $(document).on("click", '.resi', function(e) {
    var sendresi = $(this).data('sendresi');
    $(".sendresi").val(sendresi);
  });

  $(document).on("click", '.kirim', function(e) {
    var send = $(this).data('send');
    $(".send").val(send);
  });

  $(document).on("click", '.resnd', function(e) {
    var sendresi = $(this).data('resend');
    $(".sendresi").val(sendresi);
  });
</script>