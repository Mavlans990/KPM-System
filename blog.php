<?php
  session_start();
  include "lib/koneksi.php";
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
  <script src="ckeditor5/ckeditor.js"></script>
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
          <h1 class="h3 mb-2 text-gray-800">Master Artikel</h1>

          <!-- DataTales Example -->
          
          <?php
          if (isset($_GET['act'])) {
            if ($_GET['act']=='add'||$_GET['act']=='edit') {
              $id_blog='';
              $judul='';
              $isi='';
              $path='';
              $jenis='';

              if ($_GET['act']=='edit') {
                $query=mysql_query("SELECT * FROM blog WHERE id_blog='".mysql_real_escape_string($_GET['id'])."'");
                while ($row=mysql_fetch_array($query)) {
                  $id_blog=$row['id_blog'];
                  $judul=$row['judul'];
                  $path=$row['path'];
                  $isi=$row['isi'];
                  $jenis=$row['jenis'];
                }
              }
          ?>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Artikel</h6>
            </div>
            <div class="card-body">
              <form action="modul/blog_module.php" enctype="multipart/form-data" method="POST">
                <div class="form-group">
                  <label>Jenis</label>
                  <select class="form-control jenis" name="jenis">
                    <option value="blog" <?php if($jenis=='blog') { echo "selected"; } ?> >Blog</option>
                    <option value="testi" <?php if($jenis=='testi') { echo "selected"; } ?> >Testimonials</option>
                  </select>
                </div>
                <div class="form-group">
                  <label>Gambar</label>
                  <input type="file" name="path" class="form-control path" accept="image/*">
                </div>
                <div class="form-group">
                  <label>Judul</label>
                  <input type="input" name="judul" class="form-control judul" value="<?php echo $judul; ?>">
                </div>
                <div class="form-group">
                  <label>Isi</label>
                  <textarea id="editor" style="height: 500px" name="isi"><?php echo $isi; ?></textarea>
                </div>
                <input type="hidden" name="id" value="<?php echo $id_blog; ?>">
                <input type="submit" name="save" class="btn btn-primary" value="Simpan">
                <a href="blog.php" class="btn btn-danger">Batal</a>
              </form>
            </div>
          </div>
            <?php
              }
            } else { 
            ?>
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary"><a href="blog.php?act=add">Tambah Baru</a></h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <thead>
                    <tr>
                      <th>NO</th>
                      <th>Image</th>
                      <th>Title</th>
                      <th>Desc</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $n=1;
                    $sql=mysql_query("SELECT * FROM blog ORDER BY dibuat_tgl DESC");
                    while ($row = mysql_fetch_array($sql)) {
                      $str=str_replace('<p>', '', $row['isi']);
                      $string=str_replace('</p>', '', $str);
                      $string = strip_tags($string);
                      if (strlen($string) > 500) {
                        $stringCut = substr($string, 0, 500);
                        $endPoint = strrpos($stringCut, ' ');

                        $string = $endPoint? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
                        $string .= '...';
                      }
                      echo '
                    <tr>
                      <td>'.$n.'</td>
                      <td><img src="../'.$row['path'].'" style="max-width: 350px"></td>
                      <td>'.$row['judul'].'</td>
                      <td>'.$string.'</td>
                      <td>
                        <a href="blog.php?act=edit&id='.$row['id_blog'].'" class="btn btn-small text-warning">
                        <i class="fa fa-edit"></i> Ubah</a>
                        <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#modalHapus" data-id="'.$row['id_blog'].'">
                        <i class="fa fa-trash"></i> Hapus</a>
                      </td>
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
          <?php } ?>
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

  <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 400px">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Hapus Artikel?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/blog_module.php" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_hapus" name="id_hapus">
            Hapus Artikel ini?
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <input type="submit" class="btn btn-danger" name="delete" value="Hapus">
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
  <script>
    ClassicEditor
      .create( document.querySelector( '#editor' ) )
      .catch( error => {
        console.error( error );
      } );
  </script>
  <!-- Page level plugins -->

</body>

</html>
<script type="text/javascript">
  $(document).on( "click", '.hapus_button',function(e) {
    var id = $(this).data('id');
    $(".id_hapus").val(id);
  });
  
  var element = document.getElementById("mstr");
  element.classList.add("active");
</script>