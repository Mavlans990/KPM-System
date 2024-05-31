<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
  header('Location:index.php');
}

if (isset($_POST['save'])) {
  $valid = 1;
  $sql = mysqli_query($conn, "select id_user from tb_user where id_user='" . $_POST['id_user'] . "' ");
  $num = mysqli_num_rows($conn, $sql);
  if ($num == 0) {
    $query = mysqli_query($conn, "INSERT INTO tb_user(id_user, pass_user, nm_user, grup) VALUES ('" . $_POST['id_user'] . "','" . md5($_POST['pass']) . "','" . $_POST['nm_user'] . "','" . $_POST['grup'] . "')");
    if (!$query) {
      $valid = 0;
      $msg = "ERROR : Insert Data Failed";
    }
  } else {
    if ($_POST['status'] == 'edit') {
      if ($_POST['pass'] == "") {
        $query = mysqli_query($conn, "UPDATE tb_user SET nm_user='" . $_POST['nm_user'] . "',grup='" . $_POST['grup'] . "' WHERE id_user='" . $_POST['id_user'] . "'");
        if (!$query) {
          $valid = 0;
          $msg = "ERROR : Update Data Failed";
        }
      } else {
        $query = mysqli_query($conn, "UPDATE tb_user SET pass_user='" . md5($_POST['pass']) . "',nm_user='" . $_POST['nm_user'] . "',grup='" . $_POST['grup'] . "' WHERE id_user='" . $_POST['id_user'] . "'");
        if (!$query) {
          $valid = 0;
          $msg = "ERROR : Update Data Failed";
        }
      }
    } else {
      $valid = 0;
      $msg = "User ID already exists, use a different id";
    }
  }

  if ($valid == 0) {
    rollback();
  } else {
    commit();
    $msg = "Save Data Success";
  }

  echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}

if (isset($_POST['delete'])) {
  $valid = 1;
  $query = mysqli_query($conn, "DELETE FROM tb_user WHERE id_user='" . $_POST['id_hapus'] . "'");
  if (!$query) {
    $valid = 0;
    $msg = "ERROR : Delete Data Failed";
  }

  if ($valid == 0) {
    rollback();
  } else {
    commit();
    $msg = "Delete Data Success";
  }

  echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
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
        <?php //include "part/topbar.php"; 
        ?>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid mt-xl-15 mt-sm-30 mt-5">

          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">User Management</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <!-- <div class="card-header py-3">
              <a href="#" data-toggle="modal" data-target="#newBrandModal"><i class="fa fa-plus"></i> Add New</a>
            </div> -->
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>ID</th>
                      <th>Nama</th>
                      <th>No. Telp</th>
                      <th>Grup</th>
                      <th>Total Belanja</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $n = 1;
                    $sql = mysqli_query($conn, "select * from tb_user  where pass_user != '' AND grup ='customer' order by nm_user asc ");
                    while ($row = mysqli_fetch_array($sql)) {

                      $total = 0;

                      $select_sj1 = "SELECT * FROM sj1 WHERE id_cust = '" . $row['id_user'] . "'";
                      $query_sj1 = mysqli_query($conn, $select_sj1);
                      while ($row_sj1 = mysqli_fetch_array($query_sj1)) {
                        $select_sj2 = "SELECT * FROM sj2 WHERE id_sj1 = '" . $row_sj1['id_sj1'] . "'";
                        $query_sj2 = mysqli_query($conn, $select_sj2);
                        $data_sj2 = mysqli_fetch_array($query_sj2);

                        $total = $total + $data_sj2['total'];
                      }

                      echo '
                    <tr>
                      <td>' . $n . '</td>
                      <td>' . $row['id_user'] . '</td>
                      <td>' . $row['nm_user'] . '</td>
                      <td>' . $row['telp_user'] . '</td>
                      <td>' . $row['grup'] . '</td>
                      <td>Rp. ' . number_format($total) . '</td>
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



    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->


  <!-- NEW BRAND MODAL -->
  <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Customer</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <div class="modal-body">
            <input type="hidden" name="id_user" class="id_user">
            <div class="form-group">
              <label>Fullname</label>
              <input type="text" name="fullname" id="" class="form-control fullname" required>
            </div>
            <div class="form-group">
              <label for="">Email</label>
              <input type="email" name="email" id="" class="form-control email" required>
            </div>
            <div class="form-group">
              <label for="">Phone Number</label>
              <input type="number" name="phone" id="" class="form-control phone" required>
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
          <h5 class="modal-title" id="exampleModalLabel">Delete User</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_hapus" name="id_hapus">
            Delete this User?
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
  $(document).on("click", '.edit_button', function(e) {
    var status = $(this).data('status');
    var id_user = $(this).data('id_user');
    var nm_user = $(this).data('nm_user');
    var grup = $(this).data('grup');

    $(".status").val(status);
    $(".id_user").val(id_user);
    $(".nm_user").val(nm_user);
    $(".grup").val(grup);
  });

  $(document).on("click", '.hapus_button', function(e) {
    var id_hapus = $(this).data('id_hapus');
    $(".id_hapus").val(id_hapus);
  });
</script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#id_user').blur(function() {
      $('#pesan').html('<img style="margin-left:10px; width:20px" src="loading.gif">');
      var username = $(this).val();

      $.ajax({
        type: 'POST',
        url: 'ajax/ajax_users.php',
        data: 'username=' + username,
        success: function(data) {
          $('#pesan').html(data);
        }
      })

    });
  });
</script>