<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
  header('Location:index.php');
}

if (isset($_POST['update_pasang'])) {
  $update = "UPDATE sj2 SET is_sinkron = 's' WHERE id_sj2 = '" . mysqli_real_escape_string($conn, $_POST['id_pasang']) . "'";
  $query_update = mysqli_query($conn, $update);
  if ($query_update) {
    echo "<script>alert('Ubah data berhasil');</script>";
  } else {
    echo "<script>alert('Ubah data gagal');</script>";
  }
}

if (isset($_POST['delete'])) {
  $valid = 1;
  $sql = mysqli_query($conn, "SELECT id_bahan,qty FROM sj2 WHERE id_sj1='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
  while ($row = mysqli_fetch_array($sql)) {
    if ($row['no_seri'] == "decal") {
      $queries = mysqli_query($conn, "UPDATE product SET qty = qty + " . $row['qty'] . " WHERE id_product = '" . $row['id_bahan'] . "' ");
    } else {
      $queries = mysqli_query($conn, "UPDATE product_lain SET qty = qty + " . $row['qty'] . " WHERE id_product = '" . $row['id_bahan'] . "' ");
    }

    if (!$queries) {
      $valid = 0;
      $process_status = "ERROR : Ubah stok gagal";
    }
  }
  if ($valid == 1) {
    $query = mysqli_query($conn, "DELETE FROM sj2 WHERE id_sj1='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
    if (!$query) {
      $valid = 0;
      $msg = "ERROR : Hapus SO2 gagal";
    }
  }
  if ($valid == 1) {
    $query = mysqli_query($conn, "DELETE FROM sj1 WHERE id_sj1='" . mysqli_real_escape_string($conn, $_POST['id_hapus']) . "'");
    if (!$query) {
      $valid = 0;
      $msg = "ERROR : Hapus nomor pesanan gagal";
    }
  }

  if ($valid == 0) {
    rollback();
  } else {
    commit();
    $msg = "Hapus data berhasil";
  }

  echo "<script type='text/javascript'>alert('" . $msg . "')</script>";
}
$time = date("Y-m-d H:i:s");
if (isset($_POST['suspend'])) {
  $select_suspend = mysqli_query($conn, "SELECT * FROM sj1 WHERE (midtrans_transaction_status = '' OR midtrans_transaction_status = 'pending') AND jatuh_tempo < '" . $time . "' ");
  while ($row_suspend = mysqli_fetch_array($select_suspend)) {
    $query_sj2 = mysqli_query($conn, "SELECT
        j.id_sj1,
        j.id_bahan,
        j.qty,
        p.id_product,
        p.berat,
        p.unit
        FROM sj2 j JOIN product p ON j.id_bahan=p.id_product
        WHERE j.id_sj1 = '" . $row_suspend['id_sj1'] . "'");
    while ($data_sj2 = mysqli_fetch_array($query_sj2)) {
      $qty_total = $data_sj2['qty'] * $data_sj2['berat'];

      $query_update_stok = mysqli_query($conn, "UPDATE product 
          SET qty = qty + '" . $qty_total . "' WHERE id_product='" . $data_sj2['id_product'] . "'");
    }

    $query_update_status = mysqli_query($conn, "UPDATE sj1 SET
        status_sj = 'x' WHERE id_sj1 = '" . $row_suspend['id_sj1'] . "'");
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
          <h1 class="h3 mb-2 text-gray-800">Pesanan Web</h1>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-12">
                  <select class="form-control jenis" id="jenis">

                    <option value="confirm" <?php if (isset($_GET['kat'])) {
                                              if ($_GET['kat'] == 'confirm') {
                                                echo "selected";
                                              }
                                            } ?>>Pesanan Baru</option>
                    <option value="sended" <?php if (isset($_GET['kat'])) {
                                              if ($_GET['kat'] == 'sended') {
                                                echo "selected";
                                              }
                                            } ?>>Dikirim</option>
                    <option value="progress" <?php if (isset($_GET['kat'])) {
                                                if ($_GET['kat'] == 'progress') {
                                                  echo "selected";
                                                }
                                              } ?>>Dalam Pengerjaan</option>
                    <option value="done" <?php if (isset($_GET['kat'])) {
                                            if ($_GET['kat'] == 'done') {
                                              echo "selected";
                                            }
                                          } ?>>Selesai</option>
                  </select>

                </div>

              </div>

            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15" id="datatables1">
                  <thead>
                    <tr>
                      <th>INVOICE</th>
                      <th>Produk</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="tbody">
                    <?php
                    $warna = "";
                    if (isset($_GET['kat'])) {
                      if ($_GET['kat'] !== "sended" && $_GET['kat'] !== "progress") {
                        if (isset($_GET['kat'])) {
                          if ($_GET['kat'] == 'pending') {
                            $filter = "WHERE (i.midtrans_transaction_status = '' or i.midtrans_transaction_status = 'pending') and (i.status_sj = 'n' or i.status_sj = 'x')";
                          } elseif ($_GET['kat'] == 'confirm') {
                            $filter = "WHERE i.midtrans_transaction_status = 'success' and i.status_sj = 'c'";
                          } elseif ($_GET['kat'] == 'done') {
                            $filter = "WHERE i.status_sj ='s'";
                          } elseif ($_GET['kat'] == 'suspended') {
                            $warna = "style='background:#ff6600;'";
                            $filter = "WHERE i.status_sj='x'";
                          } else {
                            $filter = "";
                          }
                        } else {
                          $filter = "";
                        }

                        if ($_SESSION['branch'] !== "pusat") {
                          if ($_SESSION['branch'] == "1") {
                            $where_branch = " AND (w.id_cabang = '" . $_SESSION['branch'] . "' OR w.id_cabang = '')";
                          } else {
                            $where_branch = " AND w.id_cabang = '" . $_SESSION['branch'] . "'";
                          }
                        } else {
                          $where_branch = "";
                        }

                        $sql = mysqli_query($conn, "SELECT i.*,w.id_cabang FROM sj1 i LEFT JOIN sj2 w ON w.id_sj1 = i.id_sj1 " . $filter . $where_branch);
                        while ($row = mysqli_fetch_array($sql)) {
                          $status = "";
                          if ($row['status_sj'] == 'n') {
                            $status = "Menunggu pembayaran";
                          } elseif ($row['status_sj'] == 'c') {
                            $status = "Pesanan sedang di proses";
                          } elseif ($row['status_sj'] == 's') {
                            $status = "Pesanan sudah selesai";
                          } elseif ($row['status_sj'] == 'x') {
                            $status = "Pesanan Ditangguhkan";
                          }
                          echo '
                        <tr>
                          <td>
                          ' . $row['id_sj1'] . '<br>
                          ' . date('d-m-Y H:i:s', strtotime($row['tgl_sj'])) . '<br>
                          ' . $row['nm_cust'] . '<br>
                          ' . $row['id_cust'] . '
                          </td>
                          <td>
                          ';
                          if ($_SESSION['grup'] !== "super" && $_SESSION['branch'] !== "pusat") {
                            if ($_SESSION['branch'] == "1") {
                              $where_branch = " AND (id_cabang = '" . $_SESSION['branch'] . "' OR id_cabang = '')";
                            } else {
                              $where_branch = " AND id_cabang = '" . $_SESSION['branch'] . "'";
                            }
                          } else {
                            $where_branch = "";
                          }
                          $select_sj2 = "SELECT * FROM sj2 WHERE id_sj1 = '" . $row['id_sj1'] . "' " . $where_branch . "";
                          $query_sj2 = mysqli_query($conn, $select_sj2);
                          while ($row_sj2 = mysqli_fetch_array($query_sj2)) {
                            if ($row_sj2['no_seri'] == "decal") {
                              if ($row_sj2['id_cabang'] !== "") {
                                $decal = "<p>Pemasangan : Dipasangkan</p>";
                                $tanggal_pasang = "<p>Tanggal Pemasangan : " . date('D, d M Y', strtotime($row_sj2['tgl_pasang'])) . "</p>";
                              } else {
                                $decal = "<p>Pemasangan : Pasang Sendiri</p>";
                                $tanggal_pasang = "";
                              }

                              if ($row_sj2['express'] == "noexpress") {
                                $paket = " <p>Paket : Regular</p>";
                              } else if ($row_sj2['express'] == "express") {
                                $paket = "<p>Paket : Express</p>";
                              } else if ($row_sj2['express'] == "dirumah") {
                                $paket = "<p>Paket : Pasang Dirumah</p>";
                              } else {
                                $paket = "";
                              }

                              if ($row_sj2['no_seri'] == "decal") {
                                if ($row_sj2['stat_cus'] == "yes") {
                                  $status_custom = "<p>Body Custom : Ya</p>";
                                } else {
                                  $status_custom = "<p>Body Custom : Tidak</p>";
                                }

                                if ($row_sj2['part'] == "yes") {
                                  $part = "<p>Body cat ulang / Baret : Ya</p>";
                                } else {
                                  $part = "<p>Body cat ulang / Baret : Tidak</p>";
                                }
                              } else {
                                $part = "";
                                $status_custom = "";
                              }

                              $select_bahan = "SELECT * FROM tb_bahan WHERE filter = '" . $row_sj2['bahan'] . "'";
                              $query_bahan = mysqli_query($conn, $select_bahan);
                              $data_bahan = mysqli_fetch_array($query_bahan);

                              $bahan = "<p>Bahan : " . $data_bahan['nama_bahan'] . "</p>";

                              $select_product = "SELECT * FROM product WHERE id_product = '" . $row_sj2['id_bahan'] . "'";
                            } else {
                              $bahan = "";
                              $decal = "";
                              $paket = "";

                              if ($row_sj2['id_cabang'] !== "") {
                                $decal = "<p>Pemasangan : Dipasangkan</p>";
                                $tanggal_pasang = "<p>Tanggal Pemasangan : " . date('D, d M Y', strtotime($row_sj2['tgl_pasang'])) . "</p>";
                              } else {
                                $decal = "<p>Pemasangan : Pasang Sendiri</p>";
                                $tanggal_pasang = "";
                              }

                              $select_product = "SELECT * FROM product_lain WHERE id_product = '" . $row_sj2['id_bahan'] . "'";
                              $part = "";
                              $status_custom = "";
                            }

                            if ($row_sj2['stat_cus'] == "yes") {
                              if ($row_sj2['custom'] !== "") {
                                $custom = " <p>Keterangan body custom : " . $row_sj2['custom'] . "</p> ";
                              } else {
                                $custom = "";
                              }
                            } else {
                              $custom = "";
                            }

                            if ($row_sj2['part'] == "yes") {
                              if ($row_sj2['custom_part'] !== "") {
                                $part_reason = "<p>Keterangan body cat ulang / baret : " . $row_sj2['custom_part'] . "</p>";
                              } else {
                                $part_reason = "";
                              }
                            } else {
                              $part_reason = "";
                            }

                            $query_product = mysqli_query($conn, $select_product);
                            $data_product = mysqli_fetch_array($query_product);


                            if ($row_sj2['id_cabang'] !== "") {
                              $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_sj2['id_cabang'] . "'";
                              $query_cabang = mysqli_query($conn, $select_cabang);
                              $data_cabang = mysqli_fetch_array($query_cabang);

                              if ($row_sj2['express'] !== "dirumah") {
                                $cabang = "<p>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                              } else {
                                $cabang = "";
                              }
                            } else {
                              $cabang = "";
                            }



                            echo "<b style='font-weight:bold;'>" . $row_sj2['qty'] . " x " . $data_product['nm_product'] . "</b>" . $decal . $bahan . $paket . $cabang . $tanggal_pasang . $status_custom . $part . $custom . $part_reason . "<br><br>";
                          }
                          $query_total = mysqli_query($conn, "SELECT a.id_sj1,a.ongkir,a.kode_kupon,a.disc,a.diskon_kupon,a.persen_kupon,b.id_sj1,sum(b.total) AS total FROM sj1 a JOIN sj2 b ON a.id_sj1=b.id_sj1 WHERE a.id_sj1 = '" . $row['id_sj1'] . "'");
                          $data_total = mysqli_fetch_array($query_total);

                          if ($data_total['ongkir'] <= 0) {
                            $ongkir = "Free";
                          } else {
                            $ongkir = number_format($data_total['ongkir']);
                          }
                          echo '
                              <b>Subtotal : Rp. ' . number_format($data_total['total']) . '</b><br>
                              <b>Shipment : Rp. ' . $ongkir . '</b><br>
                              <b>Grand Total : Rp. ' . number_format($data_total['total'] + $row['ongkir'] - $data_total['disc']) . '</b><br>
                          ';
                          echo '
                          </td>
                          <td>' . $status . '</td>
                          <td>
                            <a href="order.php?order=' . $row['id_sj1'] . '" class="btn btn-small text-info">
                            <i class="fa fa-table"></i> Lihat</a>
                          ';
                          if (isset($_GET['kat'])) {
                            if ($_GET['kat'] == 'pending') {
                              if ($row['status_sj'] == 'n') {
                                echo '
                                  <a href="#" class="btn btn-small text-success approve" data-toggle="modal" data-target="#approve" data-approve="' . $row['id_sj1'] . '"><i class="fa fa-check"></i> Approve</a>
                                ';
                              }
                            } elseif ($_GET['kat'] == 'confirm') {
                              echo '
                            <a href="#" class="btn btn-small text-success send" data-toggle="modal" data-target="#kirim" data-send="' . $row['id_sj1'] . '"><i class="fa fa-refresh"></i> Update</a>
                              ';
                            } elseif ($_GET['kat'] == 'sended') {
                              echo '
                            <a href="#" class="btn btn-small text-success resend" data-toggle="modal" data-target="#resi" data-resend="' . $row['id_sj1'] . '"><i class="fa fa-reply-all"></i> Send Receipt Again</a>
                              ';
                            }
                          }
                          /*
                          echo'
                            <a href="modul/up_stok_so.php?mode=ubah&id_so='.$row['id_order'].'" class="btn btn-small text-success"><i class="fa fa-check"></i> Approve</a>
                          ';
                          */

                          echo '
                            <a href="#" class="btn btn-small text-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                              data-id_hapus="' . $row['id_sj1'] . '">
                            <i class="fa fa-trash"></i> Hapus</a>
                            ';


                          echo '
                          </td>
                        </tr>
                          ';
                        }
                      } else {
                        if ($_GET['kat'] == "sended") {
                          $select_sj2 = "SELECT * FROM sj2 WHERE no_seri != '' AND (tgl_pasang = '0000-00-00' OR  tgl_pasang IS NULL) AND is_sinkron = 'c'";
                        }
                        if ($_GET['kat'] == "progress") {
                          $select_sj2 = "SELECT * FROM sj2 WHERE no_seri = 'decal' AND tgl_pasang != '0000-00-00' AND is_sinkron = 'c'";
                        }
                        $query_sj2 = mysqli_query($conn, $select_sj2);
                        while ($row_sj2 = mysqli_fetch_array($query_sj2)) {

                          echo '
                          <tr>
                            <td>' . $row_sj2['id_sj1'] . '</td>
                            <td>
                          ';



                          if ($row_sj2['no_seri'] == "decal") {
                            $select_product = "SELECT * FROM product WHERE id_product = '" . $row_sj2['id_bahan'] . "'";
                          } else {
                            $select_product = "SELECT * FROM product_lain WHERE id_product = '" . $row_sj2['id_bahan'] . "'";
                          }

                          if ($row_sj2['no_seri'] == "decal") {
                            if ($row_sj2['id_cabang'] !== "") {
                              $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_sj2['id_cabang'] . "'";
                              $query_cabang = mysqli_query($conn, $select_cabang);
                              $data_cabang = mysqli_fetch_array($query_cabang);

                              $pasang = "<p>Pemasangan : Dipasangkan</p>";
                              $cabang = "<p>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                              $tanggal = "<p>Tanggal Pasang : " . date("D, d M Y", strtotime($row_sj2['tgl_pasang'])) . "</p>";
                            } else {
                              $cabang = "";
                              $tanggal = "";
                              $pasang = "<p>Pemasangan : Pasang Sendiri</p>";
                            }

                            if ($row_sj2['express'] == "noexpress") {
                              $paket = " <p>Paket : Regular</p>";
                            } else if ($row_sj2['express'] == "express") {
                              $paket = "<p>Paket : Express</p>";
                            } else if ($row_sj2['express'] == "dirumah") {
                              $paket = "<p>Paket : Pasang Dirumah</p>";
                            } else {
                              $paket = "";
                            }

                            $select_bahan = "SELECT * FROM tb_bahan WHERE filter = '" . $row_sj2['bahan'] . "'";
                            $query_bahan = mysqli_query($conn, $select_bahan);
                            $data_bahan = mysqli_fetch_array($query_bahan);

                            $bahan = "<p>Bahan : " . $data_bahan['nama_bahan'] . "</p>";
                          } else {
                            $bahan = "";

                            if ($row_sj2['express'] == "noexpress") {
                              $paket = " <p>Paket : Regular</p>";
                            } else if ($row_sj2['express'] == "express") {
                              $paket = "<p>Paket : Express</p>";
                            } else if ($row_sj2['express'] == "dirumah") {
                              $paket = "<p>Paket : Pasang Dirumah</p>";
                            } else {
                              $paket = "";
                            }
                          }

                          $stat_cus = "";
                          $part = "";
                          if ($row_sj2['stat_cus'] == "yes") {
                            $stat = "<p>Body custom : Ya</p>";
                          }

                          if ($row_sj2['part'] == "yes") {
                            $part = "<p>Body cat ulang / baret : Ya</p>";
                          }

                          if ($row_sj2['custom'] !== "") {
                            $custom = "<p>Keterangan : " . $row_sj2['custom'] . "</p>";
                          } else {
                            $custom = "";
                          }

                          $query_product = mysqli_query($conn, $select_product);
                          $data_product = mysqli_fetch_array($query_product);

                          echo "<b style='font-weight:bold;'>" . $data_product['nm_product'] . " x " . $row_sj2['qty'] . "</b><br>";
                          echo $pasang;
                          echo $bahan;
                          echo $paket;
                          echo $cabang;
                          echo $tanggal;
                          echo $stat_cus;
                          echo $part;
                          echo $custom;


                          if ($row_sj2['is_sinkron'] == "c") {
                            $status = "Terkonfimasi";
                          }
                          if ($row_sj2['is_sinkron'] == "s") {
                            $status = "Selesai";
                          }

                          $button = "";
                          if ($row_sj2['is_sinkron'] == "c") {
                            $button = "<a href='javascript:void(0);' class='update_to_done' data-toggle='modal' data-target='#ok' data-id='" . $row_sj2['id_sj2'] . "'>Ubah ke Selesai</a>";
                          }
                          echo '
                          </td>
                          <td>' . $status . '</td>
                          <td>' . $button . '</td>
                          ';
                        }
                      }
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


  <div class="modal fade" id="approve" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Payment Confirmation</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/order.php" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control appr" name="appr">
            If the payment confirmation email has entered, you can confirm the payment
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <input type="submit" class="btn btn-info" name="konfirmasi" value="Confirm">
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="ok" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ubah Status</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"></span>
          </button>
        </div>
        <form action="" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_pasang" name="id_pasang">
            Apa anda yakin ingin ubah status pesanan ini ?
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <input type="submit" class="btn btn-info" name="update_pasang" value="Konfirmasi">
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="resi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Send Receipt</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/order.php" method="POST">
          <div class="modal-body">
            <input type="text" class="form-control sendresi" name="sendresi">
            <div class="form-group">
              <label>Receipt Number</label>
              <input type="text" class="form-control noresi" name="noresi">
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
            <input type="submit" class="btn btn-info" name="resionly" value="Send Receipt">
            <input type="submit" class="btn btn-success" name="paket" value="Send Receipt & Orders">
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="kirim" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ubah Status Pesanan</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="modul/order.php" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control send" name="send">
            Ubah status pesanan ini<br>
            Status pesanan ini akan berubah menjadi <b>Terkirim</b>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
            <input type="submit" class="btn btn-success" name="pesanan" value="Ubah">
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="DeleteBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Hapus Pesanan</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="<?php echo basename($_SERVER['PHP_SELF']) . '?kat=' . htmlspecialchars($_GET['kat']); ?>" method="POST">
          <div class="modal-body">
            <input type="hidden" class="form-control id_hapus" name="id_hapus">
            Apa anda yakin ingin menghapus pesanan ini ? <br>
            Pesanan yang sudah dihapus tidak bisa di kembalikan.
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
  $(document).on("click", ".update_to_done", function() {
    var id_pasang = $(this).data('id');
    $(".id_pasang").val(id_pasang);
  });

  $(document).on("click", '.hapus_button', function(e) {
    var id_hapus = $(this).data('id_hapus');
    $(".id_hapus").val(id_hapus);
  });

  $(function() {
    $('#jenis').change(function() {
      var jenis = $('#jenis').val();

      window.location.href = '?kat=' + jenis;
    });
  });

  $(document).on("click", '.approve', function(e) {
    var appr = $(this).data('approve');
    $(".appr").val(appr);
  });
  $(document).on("click", '.resi', function(e) {
    var appr = $(this).data('sendresi');
    $(".sendresi").val(appr);
  });
  $(document).on("click", '.send', function(e) {
    var appr = $(this).data('send');
    $(".send").val(appr);
  });
  $(document).on("click", '.resend', function(e) {
    var appr = $(this).data('resend');
    $(".resend").val(appr);
  });
</script>