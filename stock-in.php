<?php
  session_start();
  include "lib/koneksi.php";
  include "lib/appcode.php";
  include "lib/format.php";
  
    if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
    {
        header('Location:login.php'); 
    }

    if (isset($_POST['delete'])) {
      $valid=1;
      $sql=mysql_query("SELECT id_bahan,qty from po2 where id_po1='".$_POST['id_hapus']."'");
      while ($row=mysql_fetch_array($sql)) {
        $queries=mysql_query("UPDATE product set qty = qty - ".$row['qty']." where id_product = '".$row['id_bahan']."' ");
        if(!$queries) {
          $valid=0;
          $process_status="ERROR : Update Stock Failed";
        }
      }
      if ($valid==1) {
        $query=mysql_query("DELETE FROM po2 WHERE id_po1='".$_POST['id_hapus']."'");
        if (!$query) {
          $valid=0;
          $msg="ERROR : Delete PO2 Failed";
        }
      }
      if ($valid==1) {
        $query=mysql_query("DELETE FROM po1 WHERE id_po1='".$_POST['id_hapus']."'");
        if (!$query) {
          $valid=0;
          $msg="ERROR : Delete PO1 Failed";
        }
      }

      if($valid==0) {  
        rollback();
      } else { 
        commit();
        $msg="Delete Data Success";
      }
      
      echo "<script type='text/javascript'>alert('".$msg."')</script>";
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
        <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">

          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">List Stock In</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <a href="input_po.php?mode=add"><i class="fa fa-plus"></i> Add New</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                  <thead>
                    <tr>
                      <th>NO PO</th>
                      <th>Date</th>
                      <th>Supplier</th>
                      <th>Incoming Goods</th>
                      <th>Made By</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql=mysql_query("select p.*,s.nm_supp,u.nm_user from po1 p join tb_supp s on p.id_supplier=s.id_supp join tb_user u on p.dibuat_oleh=u.id_user order by p.tgl_po desc");
                    while ($row = mysql_fetch_array($sql)) {
                      echo '
                    <tr>
                      <td>'.$row['id_po1'].'</td>
                      <td>'.date('d-m-Y',strtotime($row['tgl_po'])).'</td>
                      <td>'.$row['nm_supp'].'</td>
                      <td>
                      ';
                      $query=mysql_query("select o.qty,p.nm_product from po2 o join product p on o.id_bahan=p.id_product where o.id_po1='".$row['id_po1']."'");
                      while ($cell=mysql_fetch_array($query)) {
                        echo $cell['qty'].' x '.$cell['nm_product'].'<br>';
                      }
                      echo '
                      </td>
                      <td>'.$row['nm_user'].'</td>
                      <td>
                        <a href="input_po.php?mode=view&id_po='.$row['id_po1'].'" class="btn btn-small text-info">
                        <i class="fa fa-table"></i> View</a>
                      ';
                        
                      if(isset($_SESSION['grup'])) {
                        if ($_SESSION['grup']=='super') {
                          echo '
                        <a href="modul/up_stok_po.php?mode=ubah&id_po='.$row['id_po1'].'" class="btn btn-small text-warning">
                        <i class="fa fa-edit"></i> Edit</a>
                        <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                          data-id_hapus="'.$row['id_po1'].'">
                        <i class="fa fa-trash"></i> Delete</a>
                        ';
                        }
                      }

                        echo '
                      </td>
                    </tr>
                      ';
                    }
                    ?>
                  </tbody>
                </table>
              </div>
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

  <!-- Scroll to Top Button-->
  <?php include "part/scrolltop.php"; ?>

  <!-- Logout Modal-->
  <?php include "part/modal.php"; ?>
  <!-- DELETE MODAL -->
  <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Delete Purchase Order</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_hapus" name="id_hapus">
            Delete this Purchase Order?
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <input type="submit" class="btn btn-danger" name="delete" value="Delete">
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

</body>

</html>
<script type="text/javascript">
  $(document).on( "click", '.hapus_button',function(e) {
    var id_hapus = $(this).data('id_hapus');
    $(".id_hapus").val(id_hapus);
  });

  var element = document.getElementById("in");
  element.classList.add("active");
</script>
