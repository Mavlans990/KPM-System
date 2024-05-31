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
      if ($_POST['pass']=="") {
        $query=mysql_query("UPDATE tb_user SET id_user='".$_POST['id_user']."', nm_user='".$_POST['nm_user']."' WHERE id_user='".$_SESSION['id_user']."'");
        if (!$query) {
          $valid=0;
          $msg="ERROR : Update Data Failed";
        } else {
          $_SESSION['id_user']=$_POST['id_user'];
          $_SESSION['nm_user']=$_POST['nm_user'];
        }
      } else {
        $query=mysql_query("UPDATE tb_user SET id_user='".$_POST['id_user']."', pass_user='".md5($_POST['pass'])."',nm_user='".$_POST['nm_user']."' WHERE id_user='".$_SESSION['id_user']."'");
        if (!$query) {
          $valid=0;
          $msg="ERROR : Update Data Failed";
        } else {
          $_SESSION['id_user']=$_POST['id_user'];
          $_SESSION['nm_user']=$_POST['nm_user'];
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
          <h1 class="h3 mb-2 text-gray-800">My Profile</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary"><?php echo $_SESSION['nm_user']; ?></h6>
            </div>
            <div class="card-body">
              <div class="col-lg-6 col-md-6 col-xs-12">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                  <div class="form-group">
                    <label>User ID (for login system)</label>
                    <input type="text" class="form-control id_user" name="id_user" id="id_user" value="<?php echo $_SESSION['id_user']; ?>" required>
                    <span id="pesan"></span>
                  </div>
                  <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control pass" name="pass">
                  </div>
                  <hr>
                  <div class="form-group">
                    <label>User Name</label>
                    <input type="text" class="form-control nm_user" name="nm_user" value="<?php echo $_SESSION['nm_user']; ?>" required>
                  </div>
                  <input type="submit" class="btn btn-primary" name="save" value="Save">
                </form>
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
  $(document).ready(function(){
  $('#id_user').blur(function(){
    $('#pesan').html('<img style="margin-left:10px; width:20px" src="loading.gif">');
    var username = $(this).val();

    $.ajax({
      type  : 'POST',
      url   : 'ajax/ajax_users.php',
      data  : 'username='+username,
      success : function(data){
        $('#pesan').html(data);
      }
    })

  });
});
</script>