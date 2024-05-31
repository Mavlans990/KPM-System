<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
  header('Location:index.php');
}

if (isset($_SESSION['jenis'])) {
  if ($_SESSION['jenis'] !== "") {
    $jenis = $_SESSION['jenis'];
  } else {
    $jenis = "";
  }
} else {
  $jenis = "";
}

if (isset($_POST['submit_jenis'])) {
  $_SESSION['jenis'] = $_POST['jenis'];
  header("location:testimoni.php");
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
          <h1 class="h3 mb-2 text-gray-800">Master Testimoni</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="row align-items-center">

                <div class="col-6">
                  <form action="" method="post">
                    <div class="form-group form-inline">
                      <?php
                      if (isset($_SESSION['grup'])) {
                        if ($_SESSION['grup'] == "super") {
                          echo '<a href="#" data-toggle="modal" class="add mr-3" data-target="#newBrandModal"><i class="fa fa-plus"></i> Tambah Baru</a>';
                        }
                      }
                      ?>
                      <select name="jenis" id="" class="form-control">
                        <?php
                        $motor = "";
                        $mobil = "";
                        $velg = "";
                        if (isset($_SESSION['jenis'])) {
                          if ($_SESSION['jenis'] == "motor") {
                            $motor = "selected";
                          }
                          if ($_SESSION['jenis'] == "mobil") {
                            $mobil = "selected";
                          }
                          if ($_SESSION['jenis'] == "cat_velg") {
                            $velg = "selected";
                          }
                        }
                        ?>
                        <option value="">-- Kategori --</option>
                        <option value="motor" <?php echo $motor; ?>>Motor</option>
                        <option value="mobil" <?php echo $mobil; ?>>Mobil</option>
                        <option value="cat_velg" <?php echo $velg; ?>>Velg</option>
                      </select>
                      <button type="submit" class="btn btn-primary ml-1" name="submit_jenis">Pilih</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Gambar</th>
                      <th>Kategori</th>

                      <?php
                      if (isset($_SESSION['grup'])) {
                        if ($_SESSION['grup'] == 'super') {
                          echo '<th>Action</th>';
                        }
                      }
                      ?>
                    </tr>
                  </thead>
                  <?php
                  if (isset($_SESSION['jenis'])) {
                  ?>
                    <tbody class="testi_list">
                      <?php
                      $n = 1;
                      $sql = mysqli_query($conn, "SELECT * FROM product WHERE merk = '" . $_SESSION['jenis'] . "' AND jenis_motor = '" . $_SESSION['jenis'] . "' AND tipe = 'testimoni' ORDER BY id_product ASC LIMIT 40");
                      $sql2 = mysqli_query($conn, "SELECT * FROM product WHERE merk = '" . $_SESSION['jenis'] . "' AND jenis_motor = '" . $_SESSION['jenis'] . "' AND tipe = 'testimoni'");
                      $jum_testi = mysqli_num_rows($sql2);
                      while ($row = mysqli_fetch_array($sql)) {

                        if ($row['merk'] == "cat_velg") {
                          $kategori = "Velg";
                        } else {
                          $kategori = ucfirst($row['merk']);
                        }

                        $gambar = "";
                        $x = 0;
                        $array = explode('|', $row['gambar']);
                        foreach ($array as $my_Array) {
                          if ($my_Array != "") {
                            $gambar = $my_Array;
                            $x++;
                          }
                        }

                        echo '
                    <tr>
                      <td>' . $n . '</td>
                      <td><img src="../' . $gambar . '" width="100px"></td>
                      <td>' . $kategori . '</td>
                        ';


                        if (isset($_SESSION['grup'])) {
                          if ($_SESSION['grup'] == 'super') {
                            echo '
                      <td>
                        <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal"
                          data-id="' . $row['id_product'] . '"
                          data-kategori="' . $row['merk'] . '"
                          data-warna="' . $row['warna'] . '">
                        <i class="fa fa-edit"></i> Ubah</a>
                        <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                          data-id_hapus="' . $row['id_product'] . '">
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
                  <?php
                  }
                  ?>
                </table>
                <?php
                if (isset($_SESSION['jenis'])) {
                  if ($_SESSION['jenis'] !== "") {
                    echo '
                    <div class="text-center loadmore_div">
                      <button type="button" class="btn btn-primary loadmore_btn">Lebih Banyak</button>
                    </div>
                    ';
                  }
                }
                ?>

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

  <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Testimoni</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/testimoni_module.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <input type="hidden" class="form-control id" name="id">
            <div class="form-group">
              <input type="file" name="image" class="form-control image" accept="image/*">
            </div>
            <div class="form-group">
              <label>Kategori</label>
              <select name="kategori" id="" class="form-control kategori">
                <option value="">-- Kategori -- </option>
                <option value="motor">Motor</option>
                <option value="mobil">Mobil</option>
                <option value="cat_velg">Velg</option>
              </select>
            </div>
            <div class="form-group input_warna" style="display:none;">
              <label>Warna</label>
              <select name="warna" id="" class="form-control warna">
                <option value="">-- Pilih Warna --</option>
                <option value="black">Hitam</option>
                <option value="silver">Silver</option>
                <option value="red">Merah</option>
                <option value="blue">Biru</option>
                <option value="yellow">Kuning</option>
                <option value="orange">Orange</option>
              </select>
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

  <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Hapus Testimoni</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/testimoni_module.php" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_hapus" name="id_hapus">
            Apa anda yakin ingin menghapus testimoni ini ?
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

  <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/datatables-demo.js"></script>

</body>

</html>
<script type="text/javascript">
  var offset = 40;
  $(document).on("click", ".loadmore_btn", function() {
    var jenis = "<?php echo $jenis; ?>";
    var limit = "<?php echo $jum_testi; ?>";
    $.ajax({
      type: "POST",
      url: "ajax/ajax_loadmore_testimoni.php",
      data: {
        "offset": offset,
        "jenis": jenis
      },
      cache: true,
      success: function(result) {
        $(".testi_list").append(result);
        offset = offset + 40;
        if (limit < offset) {
          $(".loadmore_div").hide();
        }
      }
    })
  });

  $(document).on("change", ".kategori", function() {
    var kategori = $(".kategori").val();
    if (kategori == "cat_velg") {
      $(".input_warna").show(500);
      $(".warna").attr("required", true);
    } else {
      $(".input_warna").hide(500);
      $(".warna").attr("required", false);
    }
  });

  $(document).on("click", '.edit_button', function(e) {
    var id = $(this).data('id');
    var kategori = $(this).data('kategori');
    var warna = $(this).data('warna');

    $(".id").val(id);
    $(".kategori").val(kategori);
    $(".warna").val(warna);

    if (kategori == "cat_velg") {
      $(".input_warna").show(500);
      $(".warna").attr("required", true);
    } else {
      $(".input_warna").hide(500);
      $(".warna").attr("required", false);
    }
  });

  $(document).on("click", '.add', function(e) {


    $(".id").val("");
    $(".input_warna").hide(500);
    $(".warna").attr("required", false);

    $(".kategori").val("");

  });

  $(document).on("click", '.hapus_button', function(e) {
    var id_hapus = $(this).data('id_hapus');
    $(".id_hapus").val(id_hapus);
  });

  var element = document.getElementById("mstr");
  element.classList.add("active");
</script>