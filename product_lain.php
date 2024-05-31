<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
  header('Location:index.php');
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
  <script src="ckeditor5/ckeditor.js"></script>
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
          <h1 class="h3 mb-2 text-gray-800">Master Produk Lain</h1>

          <!-- DataTales Example -->

          <?php
          if (isset($_GET['act'])) {
            if ($_GET['act'] == 'add' || $_GET['act'] == 'edit') {
              $id_product = "";
              $nm_product = "";
              $kategori = "";
              $berat = 0;
              $deskripsi = "";
              $harga = 0;
              $qty = 0;
              $tag = "";

              if ($_GET['act'] == 'edit') {
                $query = mysqli_query($conn, "SELECT * FROM product_lain WHERE id_product='" . mysqli_real_escape_string($conn, $_GET['id']) . "'");
                while ($row = mysqli_fetch_array($query)) {
                  $id_product = $row['id_product'];
                  $nm_product = $row['nm_product'];
                  $kategori = $row['kategori'];
                  $berat = $row['berat'];
                  $deskripsi = $row['deskripsi'];
                  $harga = $row['harga'];
                  $qty = $row['qty'];
                  $tag = $row['tag'];
                }
              }
          ?>
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Produk</h6>
                </div>
                <div class="card-body">
                  <form action="modul/product_lain_module.php" enctype="multipart/form-data" method="POST">

                    <div class="form-group">
                      <label>Gambar</label>
                      <input type="file" name="gambar[]" id="" class="form-control" accept="image/*" multiple>
                    </div>

                    <div class="form-group">
                      <label>Nama Produk</label>
                      <input type="text" name="nm_product" id="" class="form-control" value="<?php echo $nm_product; ?>">
                    </div>

                    <div class="form-group">
                      <label>Kategori</label>
                      <select name="kategori" id="" class="form-control">
                        <?php
                        $select_kategori = "SELECT * FROM category ORDER BY id ASC";
                        $query_kategori = mysqli_query($conn, $select_kategori);
                        while ($row_kategori = mysqli_fetch_array($query_kategori)) {
                          $select = "";
                          if ($row_kategori['filter'] == $kategori) {
                            $select = "selected";
                          }
                          echo '<option value="' . $row_kategori['filter'] . '" ' . $select . '>' . $row_kategori['kategori'] . '</option>';
                        }
                        ?>
                      </select>
                    </div>

                    <!-- <div class="form-group">
                      <label>Berat / Gram</label>
                      <input type="number" name="berat" id="" class="form-control" value="<?php echo $berat; ?>">
                    </div> -->

                    <div class="form-group">
                      <label>Deskripsi</label>
                      <textarea name="deskripsi" id="" cols="30" rows="10" class="form-control"><?php echo $deskripsi; ?></textarea>
                    </div>

                    <div class="form-group">
                      <label>Harga</label>
                      <input type="number" name="harga" id="" class="form-control" value="<?php echo $harga; ?>">
                    </div>

                    <div class="form-group">
                      <label>Qty</label>
                      <input type="number" name="qty" id="" class="form-control" value="<?php echo $qty; ?>">
                    </div>

                    <div class="form-group">
                      <label>Tag</label>
                      <input type="text" name="tag" id="" class="form-control" value="<?php echo $tag; ?>">
                    </div>

                    <input type="hidden" name="id" value="<?php echo $id_product; ?>">
                    <input type="submit" name="save" class="btn btn-primary" value="Simpan">
                    <a href="product.php" class="btn btn-danger">Batal</a>
                  </form>
                </div>
              </div>
            <?php
            }
          } else {
            ?>
            <div class="card shadow mb-4">
              <?php
              if (isset($_SESSION['grup'])) {
                if ($_SESSION['grup'] == "super") {
                  echo '
                    <div class="card-header py-3">
                      <a href="product_lain.php?act=add"><i class="fa fa-plus"></i> Tambah Baru</a>&nbsp&nbsp;
                    </div>
                    ';
                }
              }
              ?>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datable_1">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <?php
                        if (isset($_SESSION['grup'])) {
                          if ($_SESSION['grup'] == "super") {
                            echo '
                                <th>Aksi</th>
                              ';
                          }
                        }
                        ?>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $n = 1;
                      $sql = mysqli_query($conn, "SELECT * FROM product_lain ORDER BY id_product ASC");
                      while ($row = mysqli_fetch_array($sql)) {
                        echo '
                    <tr>
                      <td>' . $n . '</td>
                      <td align="center">';

                        $gambar = "";
                        $x = 0;
                        $array = explode('|', $row['gambar']);
                        foreach ($array as $my_Array) {
                          if ($my_Array !== "") {
                            echo '
                          <img src="../' . $my_Array . '" style="max-width: 200px"><br><br>
                          ';
                          }
                        }
                        echo '
                        </td>
                      <td>' . $row['nm_product'] . '</td>
                      ';
                        $select_kategori = "SELECT * FROM category WHERE filter = '" . $row['kategori'] . "'";
                        $query_kategori = mysqli_query($conn, $select_kategori);
                        $data_kategori = mysqli_fetch_array($query_kategori);
                        echo '
                      <td>' . $data_kategori['kategori'] . '</td>
                      <td>Rp. ' . number_format($row['harga']) . '</td>
                      ';
                        if (isset($_SESSION['grup'])) {
                          if ($_SESSION['grup'] == "super") {
                            echo '
                            <td>
                              <a href="product_lain.php?act=edit&id=' . $row['id_product'] . '" class="btn btn-small text-warning">
                              <i class="fa fa-edit"></i> Edit</a>
                              <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#modalHapus" data-id="' . $row['id_product'] . '">
                              <i class="fa fa-trash"></i> Delete</a>
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
          <?php } ?>
        </div>
        <!-- End of Main Content -->

        <!-- Footer -->


      </div>
      <!-- End of Content Wrapper -->

    </div>


    <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document" style="max-width: 400px">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Hapus Produk</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <form action="modul/product_lain_module.php" method="POST">
            <div class="modal-body">
              <input type="hidden" class="form-control id_hapus" name="id_hapus">
              Anda yakin ingin menghapus produk ini ?
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
              <input type="submit" class="btn btn-danger" name="delete" value="Hapus">
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="modalStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document" style="max-width: 400px">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Update Stock via CSV</h5>
            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <form action="modul/update_stok.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
              <input type="file" name="file" required>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
              <input type="submit" class="btn btn-success" name="product" value="Submit">
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <link rel="stylesheet" href="chosen/docsupport/prism.css">
    <link rel="stylesheet" href="chosen/chosen.css">
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
    <script src="chosen/chosen.jquery.js" type="text/javascript"></script>
    <script src="chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
    <script>
      ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
          console.error(error);
        });

      var allEditors = document.querySelectorAll('.editor');
      for (var i = 0; i < allEditors.length; ++i) {
        ClassicEditor.create(allEditors[i]);
      }
    </script>

</body>

</html>
<script type="text/javascript">
  $(document).on("click", '.hapus_button', function(e) {
    var id = $(this).data('id');
    $(".id_hapus").val(id);
  });

  var config = {
    '.chosen-select': {},
    '.chosen-select-deselect': {
      allow_single_deselect: true
    },
    '.chosen-select-no-single': {
      disable_search_threshold: 10
    },
    '.chosen-select-no-results': {
      no_results_text: 'Oops, nothing found!'
    },
    '.chosen-select-width': {
      width: "95%"
    }
  }
  for (var selector in config) {
    $(selector).chosen(config[selector]);
  }

  var element = document.getElementById("mstr");
  element.classList.add("active");
</script>