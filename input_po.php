<?php
  session_start();
  include "lib/koneksi.php";
  include "lib/appcode.php";
  include "lib/format.php";

    if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
    {
        header('Location:login.php');
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
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet"
        type="text/css" />

    <!-- select2 CSS -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this page -->
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto:500,500i&display=swap" rel="stylesheet">
  <style type="text/css">
    .print {
      display: none;
      font-family: 'Roboto', sans-serif;
    }
    @media print {
      .non-print { display: none; }
      .print { display: block; }
    }
  </style>
</head>

<body id="page-top">
  <div class="non-print">

  <!-- Page Wrapper -->
  <div class="hk-wrapper hk-vertical-nav">
    <?php include "modul/input_po_mod.php"; ?>
    <!-- Sidebar -->
    <?php include "header.php"; ?>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div class="hk-wrapper hk-vertical-nav">

      <!-- Main Content -->
       <div class="hk-pg-wrapper">

        <!-- Topbar -->
       <?php //include "part/topbar.php"; ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
          <!-- Page Heading -->
          <?php if (isset($_GET['id_po'])&&$_GET['mode']=='view') { } else { echo '<h1 class="h3 mb-2 text-gray-800">Input Purchase Order</h1>'; }?>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-body">
              <div class="form-group form-inline" <?php if (isset($_GET['id_po'])&&$_GET['mode']=='view') { echo "hidden"; }?> >
                <input list="product" name="product" class="form-control product" placeholder="Product Name">
                  <datalist id="product">
                    <?php
                    $sql=mysql_query("select * from product order by nm_product");
                    while ($row=mysql_fetch_array($sql)) {
                      echo '
                    <option value="'.$row['nm_product'].'">
                      ';
                    }
                    ?>
                  </datalist>
                <input type="number" class="form-control qty" name="qty" id="qty" placeholder="QTY" min="1">
                <input type="text" class="form-control harga" name="harga" id="harga" placeholder="Harga">
                <input type="number" class="form-control disc_nominal" name="disc_nominal" id="disc_nominal" placeholder="Disc (Rp)">
                <input type="number" class="form-control disc_persen" name="disc_persen" id="disc_persen" placeholder="Disc (%)">
                <input type="submit" class="btn btn-success" name="add" id="add" value="Tambah">
                <div class="info"></div>
              </div>
              <?php
              if (isset($_GET['id_po'])&&$_GET['mode']=='view') {
                echo '
                <table>
                  <tr>
                    <td><b>ID PO </b>&nbsp;&nbsp;</td>
                    <td>: '.$id_po1.'</td>
                  </tr>
                  <tr>
                    <td><b>Supplier </b>&nbsp;&nbsp;</td>
                    <td>: '.$nm_supp.'</td>
                  </tr>
                  <tr>
                    <td><b>Tanggal </b>&nbsp;&nbsp;</td>
                    <td>: '.date('d-m-Y',strtotime($tgl_po)).'</td>
                  </tr>
                  <tr>
                    <td><b>Dibuat Oleh </b>&nbsp;&nbsp;</td>
                    <td>: '.$dibuat_oleh.'</td>
                  </tr>
                  <tr>
                    <td><b>Note </b>&nbsp;&nbsp;</td>
                    <td>: '.$ket.'</td>
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
                      <th>Product Name</th>
                      <th>QTY</th>
                      <th>Price</th>
                      <th>Disc (Rp)</th>
                      <th>Disc (%)</th>
                      <th>Total</th>
                      <?php if (isset($_GET['id_po'])&&$_GET['mode']=='view') { } else { echo '<th>Action</th>'; }?>

                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($_GET['id_po'])) {
                      $query=mysql_query("select r.*,p.nm_product from po2 r join product p on r.id_bahan=p.id_product where r.id_po1='".$_GET['id_po']."'");
                      while ($row=mysql_fetch_array($query)) {
                        echo '
                      <tr>
                        <td width=300px>'.$row['nm_product'].'</td>
                        <td width=50px align="center">'.floatval($row['qty']).'</td>
                        <td width=150px align="right">'.money_idr($row['harga']).'</td>
                        <td width=150px align="right">'.money_idr($row['disc_nominal']).'</td>
                        <td width=80px align="right">'.$row['disc_persen'].' %</td>
                        <td width=200px align="right">'.money_idr($row['total']).'</td>
                        ';
                        if ($_GET['mode']=='ubah') {
                          echo '
                        <td width=150px>
                          <a href="input_po.php?mode=hapus&id_po2='.$row['id_po2'].'" class="btn btn-small text-danger hapus_button"><i class="fa fa-trash"></i> Hapus</a>
                        </td>
                      ';
                        }
                        echo '
                      </tr>
                        ';
                      }
                    }
                    $query=mysql_query("select t.*,p.nm_product from temp_ro2 t join product p on t.id_bahan=p.id_product where t.id_user='".$_SESSION['id_user']."'");
                    while ($row=mysql_fetch_array($query)) {
                      echo '
                    <tr>
                      <td width=300px>'.$row['nm_product'].'</td>
                      <td width=50px align="center">'.floatval($row['qty']).'</td>
                      <td width=150px align="right">'.money_idr($row['harga']).'</td>
                      <td width=150px align="right">'.money_idr($row['disc_nominal']).'</td>
                      <td width=80px align="right">'.$row['disc_persen'].' %</td>
                      <td width=200px align="right">'.money_idr($row['total']).'</td>
                      <td width=150px>
                        <a href="input_po.php?mode=hapus&id_ro2temp='.$row['id_ro2temp'].'" class="btn btn-small text-danger hapus_button"><i class="fa fa-trash"></i> Hapus</a>
                      </td>
                    </tr>
                      ';
                    }
                    ?>
                    <tr id="separator">
                      <td colspan="7" bgcolor="#dddfeb" style="padding:5px"></td>
                    </tr>
                    <tr>
                      <td colspan="5" align="right"><b>Total</b><input type="hidden" class="total_val" name="total_val" id="total_val" value="<?php echo ($total); ?>"></td>
                      <td colspan="2" align="left"><b>Rp. </b><input type="text" class="totalbelanja" name="totalbelanja" id="totalbelanja" value="<?php echo number_format($total); ?>" readonly style="color:#858796;outline:none !important;border:0px;font-weight:bolder;width:100px;text-align:right;"></td>
                    </tr>
                    <tr>
                      <td colspan="5" align="right"><b>Disc (Rp)</b></td>
                      <td colspan="2" align="left"><b>Rp. </b><input type="text" class="totaldiscnom" name="totaldiscnom" id="totaldiscnom" value="<?php echo ($disc_nominal); ?>" style="color:#858796;outline:none !important;border:0px;font-weight:bolder;width:100px;text-align:right;" <?php if (isset($_GET['id_po'])&&$_GET['mode']=='view') { echo "readonly"; }?> ></td>
                    </tr>
                    <tr>
                      <td colspan="5" align="right"><b>Disc (%)</b></td>
                      <td colspan="2" align="left"><input type="text" class="totaldiscprc" name="totaldiscprc" id="totaldiscprc" style="color:#858796;outline:none !important;border:0px;font-weight:bolder;width:30px;text-align:right;" value="<?php echo ($disc_persen); ?>" <?php if (isset($_GET['id_po'])&&$_GET['mode']=='view') { echo "readonly"; }?>><b> %</b></td>
                    </tr>
                    <tr hidden>
                      <td colspan="5" align="right"><b>PPn (10%)</b><input type="hidden" class="ppn_val" name="ppn_val" id="ppn_val" value="0"></td>
                      <td colspan="2" align="left"><b>Rp. </b><input type="text" class="ppn" name="ppn" id="ppn" value="<?php echo $ppn_persen; ?>" style="color:#858796;outline:none !important;border:0px;font-weight:bolder;width:100px;text-align:right;" <?php if (isset($_GET['id_po'])) { echo "readonly"; }?>></td>
                    </tr>
                    <tr>
                      <td colspan="5" align="right"><b>Net Total</b></td>
                      <td colspan="2" align="left"><b>Rp. </b><input type="text" class="net_total" name="net_total" id="net_total" value="<?php echo ($net_total); ?>" readonly  style="color:#858796;outline:none !important;border:0px;font-weight:bolder;width:100px;text-align:right;"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
               <?php if (isset($_GET['id_po'])&&$_GET['mode']=='view') { echo '<a href="javascript:window.print()" class="btn btn-primary">Print</a>'; } else { echo '<button class="btn btn-info" data-toggle="modal" data-target="#newproductModal">Save</button>'; }?>

            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <?php include "part/footer.php"; ?>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->
</div>
  <!-- Scroll to Top Button-->
  <?php include "part/scrolltop.php"; ?>

  <!-- Logout Modal-->
  <?php include "part/modal.php"; ?>
  <!-- NEW product MODAL -->
  <div class="modal fade" id="newproductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">PO Data</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control" name="id_po1" value="<?php echo $id_po1; ?>" required />
            <input type="hidden" class="form-control id_cust" name="id_cust">
            <input type="hidden" class="form-control totaldiscnom" name="totaldiscnom" value="0">
            <input type="hidden" class="form-control totaldiscprc" name="totaldiscprc" value="0">
            <input type="hidden" class="form-control ppn_s" name="ppn_s" value="10">
            <input type="hidden" class="form-control net_num" name="net_num" value="0">
            <div class="form-group">
              <label>Supplier</label>
              <input list="supp" name="supp" class="form-control supp" placeholder="ID Supplier" value="<?php echo $id_supp; ?>" required>
                <datalist id="supp">
                  <?php
                  $sql=mysql_query("select * from tb_supp order by nm_supp asc");
                  while ($row=mysql_fetch_array($sql)) {
                    echo '
                  <option value="'.$row['id_supp'].'">'.$row['nm_supp'].'</option>
                    ';
                  }
                  ?>
                </datalist>
            </div>
            <div class="form-group">
              <label>Tanggal</label>
              <input type="date" class="form-control tgl" name="tgl" value="<?php echo $tgl_po; ?>">
            </div>
            <div class="form-group">
              <label>Note</label>
              <textarea class="form-control ket" name="ket" placeholder="Note"><?php echo $ket; ?></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <input type="submit" class="btn btn-primary" name="save" value="Simpan">
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="DeleteproductModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Hapus PO</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_hapus" name="id_hapus">
            Hapus PO ini?
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <input type="submit" class="btn btn-danger" name="delete" value="Hapus">
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php include "print/input_po_print.php"; ?>

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
    $(document).ready(function() {
      refresh_detail();
    });
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('.product').blur(function(){
      var id_prod = $(".product").val();

      var dataString = 'product='+ id_prod;
      
      $.ajax({
        type: "POST",
        url: "ajax/ajax_cek_harga.php",
        data: dataString,
        cache: true,
        success: function(result){
          $("#harga").val(result);
          $("#qty").val('1');
        }  
      });
    });
  });
</script>
<script type="text/javascript">
  function addThousandsSeparator(input) {
    var output = input
    if (parseFloat(input)) {
      input = new String(input); // so you can perform string operations
      var parts = input.split("."); // remove the decimal part
      parts[0] = parts[0].split("").reverse().join("").replace(/(\d{3})(?!$)/g, "$1,").split("").reverse().join("");
      output = parts.join(".");
    }
    return output;
  }

  function refresh_detail() {
    var total = parseInt($(".total_val").val());
    var distotnom = parseInt($(".totaldiscnom").val());
    var ppn_persen = parseInt($(".ppn_val").val());
    var distotpersen = parseInt($(".totaldiscprc").val());

    $(".totaldiscnom").val(distotnom);
    $(".totaldiscprc").val(distotpersen);

    var dpp = (total - (distotnom) - (total*distotpersen/100) );
    var ppn =  parseInt(dpp) * parseInt(ppn_persen) / 100 ;
    var net = parseInt(dpp) + parseInt(ppn);

    $(".ppn").val(addThousandsSeparator(ppn));
    $(".net_total").val(addThousandsSeparator(net));
    $(".net_num").val(net);
  }

  $("#add").click(function() {
    $(".info").html("");

    var product = $(".product").val();
    var qty = $("#qty").val();
    var harga = $("#harga").val();
    var disc_nominal = $("#disc_nominal").val();
    var disc_persen = $("#disc_persen").val();
    var total_awal = $(".total_val").val();

    if(product=="" || qty==""  || qty<1 || harga == "") {
      $(".info").html("");
      $(".info").append("<center><font color=red>Pastikan Nama Barang, QTY dan Harga sudah terisi</font></center>");
    } else {
      var total_val = parseInt(total_awal) + harga * qty -(disc_nominal*qty)-(qty*harga*disc_persen/100);
      var total_num = addThousandsSeparator(parseInt(total_awal) + harga * qty -(disc_nominal*qty)-(qty*harga*disc_persen/100));

      if(disc_nominal=="")
      {disc_nominal=0;}

      if(disc_persen=="")
      {disc_persen=0;}

      var dataString = 'product='+ product +'&qty='+qty+'&disc_nominal='+disc_nominal+'&disc_persen='+disc_persen+'&harga='+harga;

      $.ajax({
        type: "POST",
        url: "ajax/ajax_input_po.php",
        data: dataString,
        cache: true,
        success: function(html){
          $("#separator").before(html);
          $("#totalbelanja").val(total_num);
          $("#total_val").val(total_val);
          $(".product").val('');
          $("#qty").val('');
          $("#harga").val('');
          $("#disc_nominal").val('');
          $("#disc_persen").val('');
          $(".product").focus();
          refresh_detail();
        }
      });
    }
  });

  $(document).on( "click", '.hapus_button',function(e) {
    var id_hapus = $(this).data('id_hapus');
    $(".id_hapus").val(id_hapus);
  });
</script>
<script type="text/javascript">
  $(".totaldiscnom").change(function() {
    refresh_detail();
  });

  $(".totaldiscprc").change(function() {
    refresh_detail();
  });

  $(".ppn").change(function() {
    refresh_detail();
  });
</script>
