<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

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
          <h1 class="h3 mb-2 text-gray-800">Master Kupon</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <a href="#" data-toggle="modal" data-target="#newBrandModal"><i class="fa fa-plus"></i> Tambah Baru</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <thead>
                    <tr>
                      <th>Kode Kupon</th>
                      <th>Disc (Rp)</th>
                      <th>Disc (%)</th>
                      <th>Mulai</th>
                      <th>Selesai</th>
                      <th>Min Order</th>
                      <?php
                      if (isset($_SESSION['grup'])) {
                        if ($_SESSION['grup'] == 'super') {
                          echo '<th>Aksi</th>';
                        }
                      }
                      ?>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = mysqli_query($conn, "SELECT * FROM coupon ORDER BY mulai DESC");
                    while ($row = mysqli_fetch_array($sql)) {
                      echo '
                    <tr>
                      <td>' . $row['kode'] . '</td>
                      <td>' . $row['disc_rp'] . '</td>
                      <td>' . $row['disc_prs'] . '</td>
                      <td>' . $row['mulai'] . '</td>
                      <td>' . $row['expired'] . '</td>
                      <td>' . $row['min_order'] . '</td>
                      ';

                      if (isset($_SESSION['grup'])) {
                        if ($_SESSION['grup'] == 'super') {
                          echo '
                      <td>
                        <a href="#" class="btn btn-small text-info edit_button" data-toggle="modal" data-target="#newBrandModal"
                          data-kode="' . $row['kode'] . '"
                          data-include="' . $row['include'] . '"
                          data-exclude="' . $row['exclude'] . '"
                          data-disc_rp="' . $row['disc_rp'] . '"
                          data-disc_prs="' . $row['disc_prs'] . '"
                          data-min_order="' . $row['min_order'] . '"
                          data-kurir="' . $row['kurir'] . '"
                          data-ongkir="' . $row['ongkir'] . '"
                          data-freeongkir="' . $row['freeongkir'] . '"
                          data-min_qty="' . $row['min_qty'] . '"
                          data-jml_pakai="' . $row['jml_pakai'] . '"
                          data-untuk="' . $row['untuk'] . '"
                          data-mulai="' . $row['mulai'] . '"
                          data-expired="' . $row['expired'] . '"
                          data-deskripsi="' . $row['deskripsi'] . '">
                        <i class="fa fa-edit"></i> Ubah</a>
                        <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                          data-id_hapus="' . $row['kode'] . '">
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



    </div>
    <!-- End of Content Wrapper -->

  </div>

  <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Kupon</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/coupon_module.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Kode Kupon</label>
                  <input type="hidden" class="form-control pk" name="pk">
                  <input type="text" class="form-control kode" name="kode" style="text-transform:uppercase;" required>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Minimum Order (Rp)</label>
                  <input type="number" class="form-control min_order" name="min_order">
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Diskon (Rp)</label>
                  <input type="number" class="form-control disc_rp" name="disc_rp">
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Diskon (%)</label>
                  <input type="number" class="form-control disc_prs" name="disc_prs">
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Include</label>
              <select class="chosen-select include" name="include[]" placeholder="include" id="include" multiple style="width: 100%">
                <?php
                $sql = mysqli_query($conn, "SELECT id_product,nm_product FROM product WHERE tipe = 'decal' ORDER BY nm_product ASC");
                while ($row = mysqli_fetch_array($sql)) {
                  echo '
                <option value="' . $row['id_product'] . '">' . $row['nm_product'] . '</option>
                  ';
                }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label>Exclude</label>
              <select class="chosen-select exclude" name="exclude[]" placeholder="exclude" id="exclude" multiple style="width: 100%">
                <?php
                $sql = mysqli_query($conn, "SELECT id_product,nm_product FROM product WHERE tipe = 'decal' ORDER BY nm_product ASC");
                while ($row = mysqli_fetch_array($sql)) {
                  echo '
                <option value="' . $row['id_product'] . '">' . $row['nm_product'] . '</option>
                  ';
                }
                ?>
              </select>
            </div>
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Minimum Order (QTY)</label>
                  <input type="text" class="form-control min_qty" name="min_qty" value="1" min="1">
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Kurir</label>
                  <!--<input type="text" class="form-control kurir" name="kurir">-->
                  <select name="kurir" class="form-control kurir" required>
                    <option value="JNE-REG">JNE Regular</option>
                    <option value="JNE-YES">JNE YES</option>
                    <option value="SICEPAT-REG">SICEPAT REG</option>
                    <option value="SICEPAT-PRIORITY">SICEPAT PRIORITY</option>
                    <option value="JNT-EX">JNT EXPRESS</option>
                    <option value="JNT-SD">JNT SAME DAY</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Kuota Ongkir</label>
                  <input type="text" class="form-control ongkir" name="ongkir">
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Freeongkir</label>
                  <!--<input type="text" class="form-control freeongkir" name="freeongkir">-->
                  <select class="form-control freeongkir" name="freeongkir">
                    <option value="n">Tidak</option>
                    <option value="y">Ya</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Jumlah Pakai</label>
                  <input type="text" class="form-control jml_pakai" name="jml_pakai">
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Untuk</label>
                  <!--<input type="text" class="form-control untuk" name="untuk">-->
                  <select class="chosen-select untuk" name="untuk" style="width: 100%">
                    <option value="all">Untuk Semua Customer</option>
                    <?php
                    $sql = mysqli_query($conn, "SELECT id_user,nm_user FROM tb_user ORDER BY nm_user ASC");
                    while ($row = mysqli_fetch_array($sql)) {
                      echo '
                    <option value="' . $row['id_user'] . '">' . $row['nm_user'] . '</option>
                      ';
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Mulai Berlaku</label>
                  <input type="date" class="form-control mulai" name="mulai" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Sampai</label>
                  <input type="date" class="form-control expired" name="expired" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label>Deskripsi</label>
              <input type="text" class="form-control deskripsi" name="deskripsi" placeholder="Description" required>
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
          <h5 class="modal-title" id="exampleModalLabel">Hapus Kupon</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/coupon_module.php" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_hapus" name="id_hapus">
            Hapus kupon ini?
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
  <script src="chosen/chosen.jquery.js" type="text/javascript"></script>
  <script src="chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
  <!-- Page level custom scripts -->
  <script src="js/demo/datatables-demo.js"></script>

</body>

</html>
<script type="text/javascript">
  $(document).on("click", '.edit_button', function(e) {
    //deselect multiple select
    var elements = document.getElementById("include").options;
    for (var i = 0; i < elements.length; i++) {
      elements[i].selected = false;
    }
    var elementss = document.getElementById("exclude").options;
    for (var j = 0; j < elementss.length; j++) {
      elementss[j].selected = false;
    }

    var kode = $(this).data('kode');
    var include = $(this).data('include');
    var exclude = $(this).data('exclude');
    var disc_rp = $(this).data('disc_rp');
    var disc_prs = $(this).data('disc_prs');
    var min_order = $(this).data('min_order');
    var kurir = $(this).data('kurir');
    var ongkir = $(this).data('ongkir');
    var freeongkir = $(this).data('freeongkir');
    var min_qty = $(this).data('min_qty');
    var jml_pakai = $(this).data('jml_pakai');
    var untuk = $(this).data('untuk');
    var mulai = $(this).data('mulai');
    var expired = $(this).data('expired');
    var deskripsi = $(this).data('deskripsi');

    $(".pk").val(kode);
    $(".kode").val(kode);
    //$(".include").val(include);
    $.each(include.split("|"), function(i, e) {
      $("#include option[value='" + e + "']").prop("selected", true);
    });
    $.each(exclude.split("|"), function(i, e) {
      $("#exclude option[value='" + e + "']").prop("selected", true);
    });
    //$(".exclude").val(exclude);
    $(".disc_rp").val(disc_rp);
    $(".disc_prs").val(disc_prs);
    $(".min_order").val(min_order);
    $(".kurir").val(kurir);
    $(".ongkir").val(ongkir);
    $(".freeongkir").val(freeongkir);
    $(".min_qty").val(min_qty);
    $(".jml_pakai").val(jml_pakai);
    $(".untuk").val(untuk);
    $(".mulai").val(mulai);
    $(".expired").val(expired);
    $(".deskripsi").val(deskripsi);
  });

  $(document).on("click", '.hapus_button', function(e) {
    var id_hapus = $(this).data('id_hapus');
    $(".id_hapus").val(id_hapus);
  });

  $('#newBrandModal').on('shown.bs.modal', function() {
    $('.chosen-select', this).chosen('destroy').chosen();
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