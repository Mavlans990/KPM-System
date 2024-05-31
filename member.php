<?php
  session_start();
  include "lib/koneksi.php";
  include "lib/appcode.php";
  include "lib/format.php";
  
  if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
  {
      header('Location:login.php'); 
  }
  $member=generate_idmember();

  if(isset($_POST['vip']))
  {
    $query_member = mysql_query("SELECT * FROM member WHERE id_member = '".mysql_real_escape_string($_POST['id'])."'");
    $data_member = mysql_fetch_array($query_member);
    if($data_member['status'] == 1)
    {
      echo "<script>alert('This account is already VIP');</script>";
    }
    else
    {
      $update_member = mysql_query("UPDATE member SET status = '1' WHERE id_member = '".mysql_real_escape_string($_POST['id'])."'");
      if($update_member)
      {
        echo "<script>alert('Account has been updated');</script>";
      }
      else{
        echo "<script>alert('Error')</script>";
      }
    }
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
          <h1 class="h3 mb-2 text-gray-800">Master Member</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <a href="#" data-toggle="modal" class="tambah" data-target="#newBrandModal"><i class="fa fa-plus"></i> Add New</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Phone</th>
                      <th>Address</th>
                      <th>Status</th>
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
                    $sql=mysql_query("select * from member order by nm_member asc");
                    while ($row = mysql_fetch_array($sql)) {
                      if($row['status'] == 0)
                      {
                        $status = "Casual";
                      }
                      else
                      {
                        $status = "VIP";
                      }
                      echo'
                    <tr>
                      <td>'.$row['id_member'].'</td>
                      <td>'.$row['nm_member'].'</td>
                      <td>'.$row['hp1'].'</td>
                      <td>';
                        $query_address = mysql_query("select * from tb_address where id_user = '".$row['id_member']."'");
                        while($row_address = mysql_fetch_array($query_address)){
                          echo "<ul>
                            <li>".$row_address['address']." - ".$row_address['post_code']." - ".$row_address['city']."</li>
                          </ul>";
                        }
                      '</td>
                      ';
                      echo "<td>".$status."</td>";

                      if(isset($_SESSION['grup'])) {
                        if ($_SESSION['grup']=='super') {
                          echo '
                      <td>
                        <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal" data-id_member = "'.$row['id_member'].'"">
                        <i class="fa fa-edit"></i> Edit</a>
                        <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                          data-id_hapus="'.$row['id_member'].'">
                        <i class="fa fa-trash"></i> Hapus</a>
                      </td>
                          ';
                        }
                      }
                      
                      echo '
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
  <!-- NEW BRAND MODAL -->
  <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Member</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="" id="modal_form" method="POST">
          <div class="modal-body">
            <input type="hidden" class="id_member" name="id">
            <p>Are you sure make this account as VIP ?</p>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <input type="submit" class="btn btn-primary" name="vip" value="Submit">
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Delete member</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/member_module.php" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_hapus" name="id_hapus">
            Delete this member?
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
  $(document).on( "click", '.tambah',function(e) {
    $('#modal_form')[0].reset();
    $(".pass").prop('required',true);
    $(".pass").attr("placeholder", "Password Account");
  });

  $(document).on( "click", '.edit_button',function(e) {
    $(".pass").prop('required',false);
    $(".pass").attr("placeholder", "Diisi jika ingin dirubah");

    var id_member = $(this).data('id_member');
    var nm_member = $(this).data('nm_member');
    var id_parent = $(this).data('id_parent');
    var hp1 = $(this).data('hp1');
    var hp2 = $(this).data('hp2');
    var email = $(this).data('email');
    var alamat = $(this).data('alamat');
    
    $(".id_member").val(id_member);
    $(".nm_member").val(nm_member);
    $(".id_parent").val(id_parent);
    $(".hp1").val(hp1);
    $(".hp2").val(hp2);
    $(".email").val(email);
    $(".alamat").val(alamat);
  });

  $(document).on( "click", '.hapus_button',function(e) {
    var id_hapus = $(this).data('id_hapus');
    $(".id_hapus").val(id_hapus);
  });
</script>

<script type="text/javascript">
  $(document).ready(function(){
  $('#id_parent').blur(function(){
    $('#pesan').html('<img style="margin-left:10px; width:20px" src="loading.gif">');
    var id_parent = $(this).val();
    var id_member = $(".id_member").val();

    var dataString = 'id_member='+id_member +'&id_parent='+id_parent;

    $.ajax({
      type  : 'POST',
      url   : 'ajax/cekparent_ajax.php',
      data  : dataString,
      success : function(data){
        $('#pesan').html(data);
      }
    })

  });
});
  var element = document.getElementById("mstr");
  element.classList.add("active");
</script>