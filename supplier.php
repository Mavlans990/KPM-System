<?php
  session_start();
  include "lib/koneksi.php";
  include "lib/appcode.php";
  include "lib/format.php";
  
    if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
    {
        header('Location:login.php'); 
    }

    if (isset($_POST['save'])) {
      $valid=1;
      $id=generate_idsupp();
      if ($_POST['id_supp']=="") {
        $query=mysql_query("INSERT INTO tb_supp(id_supp, nm_supp, hp, email, alamat, dibuat_tgl) VALUES ('".$id."','".$_POST['nm_supp']."','".$_POST['hp']."','".$_POST['email']."','".$_POST['alamat']."','".date('Y-m-d')."')");
        if (!$query) {
          $valid=0;
          $msg="ERROR : Insert Data Failed";
        }
      } else {
        $query=mysql_query("UPDATE tb_supp SET nm_supp='".$_POST['nm_supp']."',hp='".$_POST['hp']."',email='".$_POST['email']."',alamat='".$_POST['alamat']."',diubah_tgl='".date('Y-m-d')."' WHERE id_supp='".$_POST['id_supp']."'");
        if (!$query) {
          $valid=0;
          $msg="ERROR : Update Data Failed";
        }
      }

      if($valid==0) {  
        rollback();
      } else { 
        commit();
        $msg="Save Data Success";
      }
      
      echo "<script type='text/javascript'>alert('".$msg."')</script>";
    }

    if (isset($_POST['delete'])) {
      $valid=1;
      $query=mysql_query("DELETE FROM tb_supp WHERE id_supp='".$_POST['id_hapus']."'");
      if (!$query) {
        $valid=0;
        $msg="ERROR : Delete Data Failed";
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
          <h1 class="h3 mb-2 text-gray-800">Master Supplier</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <a href="#" data-toggle="modal" data-target="#newBrandModal"><i class="fa fa-plus"></i> Add New</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Name</th>
                      <th>Phone</th>
                      <th>Address</th>
                      <?php
                      if(isset($_SESSION['grup'])) {
                        if ($_SESSION['grup']=='super') {
                          echo '<th>Action</th>';
                        }
                      }
                      ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $n=1;
                    $sql=mysql_query("select * from tb_supp order by nm_supp asc");
                    while ($row = mysql_fetch_array($sql)) {
                      echo'
                    <tr>
                      <td>'.$n.'</td>
                      <td>'.$row['nm_supp'].'</td>
                      <td>'.$row['hp'].'</td>
                      <td>'.$row['alamat'].'</td>
                      ';
                      
                      if(isset($_SESSION['grup'])) {
                        if ($_SESSION['grup']=='super') {
                          echo '
                      <td>
                        <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal"
                          data-id_supp="'.$row['id_supp'].'" 
                          data-nm_supp="'.$row['nm_supp'].'" 
                          data-hp="'.$row['hp'].'" 
                          data-email="'.$row['email'].'" 
                          data-alamat="'.$row['alamat'].'">
                        <i class="fa fa-edit"></i> Edit</a>
                        <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                          data-id_hapus="'.$row['id_supp'].'">
                        <i class="fa fa-trash"></i> Hapus</a>
                      </td>
                          ';
                        }
                      }

                      echo '
                    </tr>
                      ';
                      $n++;
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
  <!-- NEW BRAND MODAL -->
  <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Supplier</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_supp" name="id_supp">
            <div class="form-group">
              <label>Supplier Name</label>
              <input type="text" class="form-control nm_supp" name="nm_supp" placeholder="Supplier Name" required>
            </div>
            <div class="form-group">
              <label>Phone Number</label>
              <input type="text" class="form-control hp" name="hp" placeholder="Phone Number/Whatsapp">
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" class="form-control email" name="email" placeholder="Email">
            </div>
            <div class="form-group">
              <label>Address</label>
              <textarea class="form-control alamat" name="alamat" placeholder="Address"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <input type="submit" class="btn btn-primary" name="save" value="Save">
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Delete Supplier</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_hapus" name="id_hapus">
            Delete this Supplier?
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
  $(document).on( "click", '.edit_button',function(e) {
    var id_supp = $(this).data('id_supp');
    var nm_supp = $(this).data('nm_supp');
    var hp = $(this).data('hp');
    var email = $(this).data('email');
    var alamat = $(this).data('alamat');
    
    $(".id_supp").val(id_supp);
    $(".nm_supp").val(nm_supp);
    $(".hp").val(hp);
    $(".email").val(email);
    $(".alamat").val(alamat);
  });

  $(document).on( "click", '.hapus_button',function(e) {
    var id_hapus = $(this).data('id_hapus');
    $(".id_hapus").val(id_hapus);
  });
</script>
