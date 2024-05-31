<?php
  session_start();
  include "lib/koneksi.php";
  include "lib/appcode.php";
  include "lib/format.php";
  
  if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
  {
    header('Location:login.php'); 
  }

  if (isset($_GET['from'])&&isset($_GET['to'])) {
    if ($_GET['from']!==""&&$_GET['to']!=="") {
      $from=mysql_real_escape_string($_GET['from']);
      $to=mysql_real_escape_string($_GET['to']);
    } else {
      $from='';
      $to='';
    }
  } else {
    $from='';
    $to='';
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
</head>

<body id="page-top">

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
       <?php //include "part/topbar.php"; ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <?php if (isset($_GET['id'])) { if ($_GET['id']!=='') { ?>
        <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
          <!-- Page Heading -->
          <?php
          $sql=mysql_query("SELECT nm_member FROM member WHERE id_member = '".mysql_real_escape_string($_GET['id'])."'");
          $data=mysql_fetch_array($sql);
          ?>
          <h1 class="h3 mb-2 text-gray-800">Riwayat Point <b><?php echo $data['nm_member']; ?></b></h1>
          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Riwayat Poin</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Invoice/ID Redeem</th>
                      <th>Nama</th>
                      <th>In</th>
                      <th>Out</th>
                      <th>Saldo</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($_GET['from'])&&isset($_GET['to'])) {
                      $n=1;
                      $total=0;
                      $sql=mysql_query("SELECT p.id_order AS id, p.tgl AS tgl, m1.nm_member AS nama, p.nominal AS masuk, 0 keluar FROM point p JOIN member m1 on p.id_user=m1.id_member WHERE p.id_user = '".mysql_real_escape_string($_GET['id'])."'
                      UNION
                      SELECT r.id_redeem AS id, r.tgl AS tgl, m2.nm_member AS nama, 0 masuk, r.total AS keluar FROM redeem r JOIN member m2 on r.id_member=m2.id_member WHERE r.id_member = '".mysql_real_escape_string($_GET['id'])."'
                      order by tgl asc");
                      while ($row = mysql_fetch_array($sql)) {
                        $total=$total+$row['masuk']-$row['keluar'];
                        echo'
                      <tr>
                        <td align="center">'.$n.'</td>
                        <td>'.$row['id'].'</td>
                        <td>'.$row['nama'].'</td>
                        <td align="center">'.$row['masuk'].'</td>
                        <td align="center">'.$row['keluar'].'</td>
                        <td align="center">'.$total.'</td>
                      </tr>
                        ';
                        $n++;
                      }
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php } } else { ?>
        <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Member Point</h1>
          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET">
                <div class="form-group form-inline" style="margin-bottom: 0">
                  <h6 class="m-0 font-weight-bold text-primary" style="padding-right:10px;">Periode</h6>
                  <input type="date" name="from" class="form-control" value="<?php echo $from; ?>" required>
                  <h6 class="m-0 font-weight-bold text-primary" style="padding:0 10px;">sampai</h6>
                  <input type="date" name="to" class="form-control" value="<?php echo $to; ?>" required style="margin-right:10px;">
                  <input type="submit" class="btn btn-info">
                </div>
              </form>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>ID Member</th>
                      <th>Nama</th>
                      <th>In</th>
                      <th>Out</th>
                      <th>Saldo</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($_GET['from'])&&isset($_GET['to'])) {
                      $n=1;
                      $sql=mysql_query("SELECT id_member,nm_member FROM member ORDER BY id_member ASC");
                      while ($row = mysql_fetch_array($sql)) {
                        echo'
                      <tr>
                        <td align="center">'.$n.'</td>
                        <td>'.$row['id_member'].'</td>
                        <td>'.$row['nm_member'].'</td>
                        ';
                        $query=mysql_query("SELECT sum(nominal) masuk FROM point WHERE id_user='".$row['id_member']."' AND tgl BETWEEN '".mysql_real_escape_string($_GET['from'])."' AND '".mysql_real_escape_string($_GET['to'])."' ");
                        while ($row2=mysql_fetch_array($query)) {
                          $masuk=$row2['masuk'];
                          
                          if ($masuk=="") {
                            echo '
                        <td align="center">0</td>
                            ';
                          } else {
                            echo '
                        <td align="center">'.$row2['masuk'].'</td>
                            ';
                          }
                        }
                        $query=mysql_query("SELECT sum(total) keluar FROM redeem WHERE id_member='".$row['id_member']."'");
                        while ($row2=mysql_fetch_array($query)) {
                          $keluar=$row2['keluar'];
                          
                          if ($keluar=="") {
                            echo '
                        <td align="center">0</td>
                            ';
                          } else {
                            echo '
                        <td align="center">'.$row2['keluar'].'</td>
                            ';
                          }
                        }
                        $hasil=$masuk-$keluar;
                        echo '
                        <td align="center">'.$hasil.'</td>
                        <td align="center">
                          <a href="'.basename($_SERVER['REQUEST_URI']).'&id='.$row['id_member'].'" class="btn btn-small text-info view-btn" >
                          <i class="fa fa-table"></i> Detail</a>
                          <a href="#" class="btn btn-small text-warning view-btn redeem" data-toggle="modal" data-target="#redeemModal" data-id_member="'.$row['id_member'].'">
                          <i class="fa fa-edit"></i> Edit</a>
                        </td>
                      </tr>
                        ';
                        $n++;
                      }
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <?php } ?>
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

  <!-- Scroll to Top Button-->
  <?php include "part/scrolltop.php"; ?>

  <!-- Logout Modal-->
  <?php include "part/modal.php"; ?>
  <!-- NEW BRAND MODAL -->
  <div class="modal fade" id="redeemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Slider</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/redeem_point.php" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id" name="id">
            <input type="hidden" class="form-control tgl" name="tgl" value="<?php echo $from; ?>">
            <input type="hidden" class="form-control tgl2" name="tgl2" value="<?php echo $to; ?>">
            <div class="form-group">
              <label>Total Potongan</label>
              <input type="number" class="form-control total" name="total" min="0" value="0">
            </div>
            <div class="form-group">
              <label>Keterangan Redeem</label>
              <textarea class="form-control ket" name="ket"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <input type="submit" class="btn btn-primary" name="save" value="Potong">
          </div>
        </form>
      </div>
    </div>
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

  <script type="text/javascript">
    $(document).on( "click", '.redeem',function(e) {
    var id_member = $(this).data('id_member');
    
    $(".id").val(id_member);
  });
  </script>
</body>

</html>