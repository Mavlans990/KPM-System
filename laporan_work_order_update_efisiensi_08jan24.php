<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

$id_beban = generate_beban();



if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

$select_masuk = "SELECT a.* FROM tb_barang_masuk a WHERE a.id_transaksi = '" . $_SESSION['id_user'] . "' AND a.status = '' ";
$query_masuk = mysqli_query($conn, $select_masuk);
while ($row_masuk = mysqli_fetch_array($query_masuk)) {

    $delete = mysqli_query($conn, "DELETE FROM tb_barang_masuk WHERE id_transaksi = '" . $_SESSION['id_user'] . "' AND status = ''");
}

if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
    $tambah_baru = '<a href="javascript:void(0);" class="btn btn-primary form-control-sm" data-toggle="modal" data-target="#ChooseModal"><i class="fa fa-plus"></i> Tambah Baru</a>';
} else {
    $tambah_baru = '<a href="master_barang_masuk_edit.php?id_transaksi=new&id_cabang=' . $_SESSION['branch'] . '"  class="btn btn-primary form-control-sm"><i class="fa fa-plus"></i> Tambah Baru</a>';
}

if (isset($_POST['delete'])) {
    $total = 0;
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus']);
    $select_masuk = "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "'";
    $query_masuk = mysqli_query($conn, $select_masuk);
    $data_masuk = mysqli_fetch_array($query_masuk);

    // if ($data_masuk['status'] == "s") {
    //     $total = 0;
    //     $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "'";
    //     $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
    //     while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {

    //         $select_stock = "SELECT * FROM tb_stock WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "' AND uom = 'YARD'";
    //         $query_stock = mysqli_query($conn, $select_stock);
    //         $data_stock = mysqli_fetch_array($query_stock);

    //         $total_stock = $data_stock['stock'] - 1;
    //         $total_berat = ($total_stock * $data_stock['berat']);

    //         $update = mysqli_query($conn, "UPDATE tb_stock SET 
    //         stock = '" . $total_stock . "' WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND berat = '" . $row_barang_masuk['berat'] . "' AND uom = 'YARD'
    //         ");

    //         $total_qty_barang = 0;
    //         $total_berat_barang = 0;
    //         $sql_summ_barang = mysqli_query($conn, "SELECT total_qty,total_berat FROM tb_bahan WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "'");
    //         if ($row_summ_barang = mysqli_fetch_array($sql_summ_barang)) {
    //             $total_qty_barang = $row_summ_barang['total_qty'];
    //             $total_berat_barang = $row_summ_barang['total_berat'];
    //         }

    //         $update_summ_barang = mysqli_query($conn, "
    //             UPDATE
    //                 tb_bahan
    //             SET
    //                 total_qty = total_qty - '" . $row_barang_masuk['qty'] . "',
    //                 total_berat = total_berat - '" . ($row_barang_masuk['qty'] * $row_barang_masuk['berat']) . "'
    //             WHERE
    //                 id_bahan = '" . $row_barang_masuk['id_product'] . "'
    //         ");
    //     }
    // }

    $delete = mysqli_query($conn, "DELETE FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "'");
    $delete2 = mysqli_query($conn, "DELETE FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "'");
    $delete3 = mysqli_query($conn, "DELETE FROM tb_marker WHERE id_transaksi = '" . $id_transaksi . "'");
    $delete4 = mysqli_query($conn, "DELETE FROM tb_cutting WHERE id_transaksi = '" . $id_transaksi . "'");
    $delete5 = mysqli_query($conn, "DELETE FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "'");
    $delete6 = mysqli_query($conn, "DELETE FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "'");
    $delete7 = mysqli_query($conn, "DELETE FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "'");
    if ($delete) {
        $msg = "Hapus Invoice berhasil";
    } else {
        $msg = "Hapus Invoice gagal";
    }

    echo '
        <script>alert("' . $msg . '");window.location.href="master_work_order.php";</script>
    ';
}

$filter_cabang = "";
if ($_SESSION['group'] == "franchise") {
    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
}

$hidden = "style='display:none;'";
if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
    $hidden = "";
}

$from = date("Y-m-d");
$to = date("Y-m-d");
$cabang = "";
$nama_customer = "";
$source_customer = "";
if (isset($_POST['search'])) {
    $from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
    $to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
    // $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    // $source_customer = mysqli_real_escape_string($conn, $_POST['source_customer']);
    // Error Gatau
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Inventory System</title>
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

    <style>
        .fs-11 {
            font-size: 11px !important;
        }
    </style>
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




                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3" style="text-transform: none;">
                            <div class="form-inline">
                                <h3 class="mr-2">Laporan Work Order</h3>
                                <!-- <a href="master_work_order_edit.php?id_transaksi=new" class="btn btn-primary form-control-sm"><i class="fa fa-plus"></i> Tambah Baru</a> -->
                            </div>
                        </div>

                        <div class="card-header py-1">
                            <div class="">
                                <form action="" method="post">
                                    <br>
                                    <div class="row no-gutters">
                                        <!-- <div class="form-group form-inline">
                                            <input type="date" name="tgl_from" id="" class="form-control form-control-sm " value="<?= $from; ?>" required>
                                            <span class="mt--5 mb--5 w--100 text-center">S/D</span>
                                            <input type="date" name="tgl_to" id="" class="form-control form-control-sm " value="<?= $to; ?>" required>


                                            <div class="input-group-prepend">
                                                    <span style="width:120px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Supplier</span>
                                                </div>
                                                <input type="text" name="nama_customer" id="" class="form-control form-control-sm nama_customer" value="<?= $nama_customer; ?>" autocomplete="off" list="list_customer" onclick="this.select();">
                                                <datalist id="list_customer" class="list_customer">
                                                </datalist>


                                            <button type="submit" class="mt--5 btn btn-primary form-control-sm ml-1" name="search"><i class="fa fa-search"></i> Cari</button>
                                        </div> -->
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm w-100 display tb_jps_re_ins tb_jps_ins mt-15">
                                    <thead>
                                        <tr>
                                            <th>No. WO</th>
                                            <th>Tanggal</th>
                                            <th>Target</th>
                                            <th>Warna</th>
                                            <th>Category</th>
                                            <th>Remaining</th>
                                            <th>Eficiency</th>
                                            <!-- <th>Inspek</th>
                                            <th>Marker</th>
                                            <th>Cutting</th>
                                            <th>Sewing</th>
                                            <th>Finishing</th>
                                            <th>Packing</th>
                                            <th>Status</th> -->
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $n = 1;
                                        // $where_branch = "WHERE a.status != '' AND a.id_transaksi LIKE '%PO%' AND a.by_user_pajak = '" . $_SESSION['jenis_pajak'] . "'";

                                        // $tgl_from = "";
                                        // $tgl_to = "";
                                        // $cabang = "";
                                        // $limit = " LIMIT 30 ";
                                        // if (isset($_POST['search'])) {
                                        //     $tgl_from = mysqli_real_escape_string($conn, $_POST['tgl_from']);
                                        //     $tgl_to = mysqli_real_escape_string($conn, $_POST['tgl_to']);
                                        //     // $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
                                        //     $where_branch = " WHERE a.tgl_transaksi BETWEEN '" . $tgl_from . "' AND '" . $tgl_to . "' AND a.status != '' AND a.by_user_pajak = '" . $_SESSION['by_user_pajak'] . "'";
                                        //     $limit = "";
                                        // }
                                        $where_tgl = "";
                                        $where_customer = "";
                                        $id_customer = "";
                                        $ex_customer = "";
                                        if (isset($_POST['search'])) {
                                            $where_tgl = " AND a.tgl_transaksi BETWEEN '" . $from . "' AND '" . $to . "'";
                                            if ($_POST['nama_customer'] != '') {
                                                $ex_customer = explode(" | ", $nama_customer);
                                                $id_customer = $ex_customer[0];
                                                $where_customer = "AND a.id_supplier ='" . $id_customer . "' ";
                                            }
                                        }
                                        // " . $where_tgl . " " . $where_customer . " 
                                        $sql = mysqli_query($conn, "SELECT a.*,m.efisiensi FROM tb_work_order a LEFT JOIN tb_marker m ON a.id_transaksi = m.id_transaksi WHERE a.id_transaksi LIKE '%WO%' GROUP BY a.id_transaksi ORDER BY id_transaksi DESC");
                                        // echo "SELECT a.*,m.efisiensi FROM tb_work_order a JOIN tb_marker m ON a.id_transaksi = m.id_transaksi WHERE a.id_transaksi LIKE '%WO%' GROUP BY a.id_transaksi ORDER BY id_transaksi DESC";
                                        // $sql = mysqli_query($conn, "SELECT a.*FROM tb_work_order a WHERE a.id_transaksi LIKE '%WO%'" . $where_tgl . " " . $where_customer . " GROUP BY a.id_transaksi ORDER BY id_transaksi DESC");
                                        while ($row = mysqli_fetch_array($sql)) {

                                            if ($row['status'] == "d") {
                                                $status = "<p class='text-danger'>Draft</p>";
                                                $ubah = '<a href="laporan_work_order_edit.php?id_transaksi=' . $row['id_transaksi'] . '&detail=view" class="btn btn-sm btn-success">
                                              <i class="fa fa-edit"></i> Ubah</a>';
                                            } else {
                                                $status = "<p class='text-success'>Close</p>";
                                                $ubah = '<a href="laporan_work_order_edit.php?id_transaksi=' . $row['id_transaksi'] . '&detail=view" class="btn btn-sm btn-primary">
                                              <i class="fa fa-edit"></i> Lihat</a>';
                                            }

                                            if ($row['sts_inspek'] == '') {
                                                $sks_inspek = "";
                                            } elseif ($row['sts_inspek'] == 'd') {
                                                $sks_inspek = "<a href='master_inspek_edit.php?id_transaksi=" . $row['id_transaksi'] . "' style='color:red;'>Draft</a>";
                                            } else {
                                                $sks_inspek = "<a href='master_inspek_edit.php?id_transaksi=" . $row['id_transaksi'] . "&detail=view' style='color:green;'>Done</a>";
                                            }

                                            if ($row['sts_marker'] == '') {
                                                $sks_marker = "";
                                            } elseif ($row['sts_marker'] == 'd') {
                                                $sks_marker = "<a href='master_marker_edit.php?id_transaksi=" . $row['id_transaksi'] . "' style='color:red;'>Draft</a>";
                                            } else {
                                                $sks_marker = "<a href='master_marker_edit.php?id_transaksi=" . $row['id_transaksi'] . "&detail=view' style='color:green;'>Done</a>";
                                            }

                                            if ($row['sts_cutting'] == '') {
                                                $sks_cutting = "";
                                            } elseif ($row['sts_cutting'] == 'd') {
                                                $sks_cutting = "<a href='master_cutting_edit.php?id_transaksi=" . $row['id_transaksi'] . "' style='color:red;'>Draft</a>";
                                            } else {
                                                $sks_cutting = "<a href='master_cutting_edit.php?id_transaksi=" . $row['id_transaksi'] . "&detail=view' style='color:green;'>Done</a>";
                                            }

                                            if ($row['sts_sewing'] == '') {
                                                $sks_sewing = "";
                                            } elseif ($row['sts_sewing'] == 'd') {
                                                $sks_sewing = "<a href='master_sewing_edit.php?id_transaksi=" . $row['id_transaksi'] . "' style='color:red;'>Draft</a>";
                                            } else {
                                                $sks_sewing = "<a href='master_sewing_edit.php?id_transaksi=" . $row['id_transaksi'] . "&detail=view' style='color:green;'>Done</a>";
                                            }

                                            if ($row['sts_finish'] == '') {
                                                $sks_finish = "";
                                            } elseif ($row['sts_finish'] == 'd') {
                                                $sks_finish = "<a href='master_finishing_edit.php?id_transaksi=" . $row['id_transaksi'] . "' style='color:red;'>Draft</a>";
                                            } else {
                                                $sks_finish = "<a href='master_finishing_edit.php?id_transaksi=" . $row['id_transaksi'] . "&detail=view' style='color:green;'>Done</a>";
                                            }

                                            if ($row['sts_packing'] == '') {
                                                $sks_packing = "";
                                            } elseif ($row['sts_packing'] == 'd') {
                                                $sks_packing = "<a href='master_packing_edit.php?id_transaksi=" . $row['id_transaksi'] . "' style='color:red;'>Draft</a>";
                                            } else {
                                                $sks_packing = "<a href='master_packing_edit.php?id_transaksi=" . $row['id_transaksi'] . "&detail=view' style='color:green;'>Done</a>";
                                            }

                                            echo '
                                                <tr>
                                                    <td class="fs-11" style="vertical-align:top;">' . $row['id_transaksi'] . '</td>
                                                    <td class="fs-11" style="vertical-align:top;">' . $row['tgl_awal'] . '</td>
                                                    <td class="fs-11" style="vertical-align:top;">' . $row['tgl_target'] . '</td>
                                                    <td class="fs-11" style="vertical-align:top;">' . $row['warna'] . '</td>
                                                    <td class="fs-11" style="vertical-align:top;">' . $row['kategori'] . '</td>
                                                    <td class="fs-11" style="vertical-align:top;">' . $row['remaining'] . ' Days</td> ';
                                                    if($row['efisiensi'] == ''){
                                                        echo '<td class="fs-11" style="vertical-align:top;">-</td>';
                                                    }else{
                                                        echo '<td class="fs-11" style="vertical-align:top;">' . $row['efisiensi'] . '%</td>';
                                                    }
                                                    echo '
                                                    <td class="fs-11" style="vertical-align:top;">' . $status . '</td>
                                                    ';


                                            echo '
                                                <td class="fs-11" style="vertical-align:top;text-align:center;">
                                                ' . $ubah . '
                                                ';

                                            // if ($_SESSION['group'] == "super" || $_SESSION['group'] == "franchise") {
                                            //     echo '
                                            //         <a href="#" class="btn btn-sm btn-danger hapus_button" data-toggle="modal" data-target="#DeleteBrandModal"
                                            //         data-id_hapus="' . $row['id_transaksi'] . '">
                                            //       <i class="fa fa-trash"></i> Hapus</a>
                                            //         ';
                                            // }

                                            echo '
                                              </td>
                                               
                                            </tr>';
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


        </div>
        <!-- End of Content Wrapper -->

    </div>

    <div class="modal fade" id="newBrandModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Master Beban</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>ID Beban</label>
                            <input type="text" name="id_beban" id="" class="form-control filled-input id_beban" value="<?php echo $id_beban; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nama Beban</label>
                            <input type="text" name="nama_beban" id="" class="form-control nama_beban" required>
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
                    <h5 class="modal-title" id="exampleModalLabel">Hapus Invoice</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" class="form-control id_hapus" name="id_hapus">
                        Anda yakin ingin invoice ini ?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                        <input type="submit" class="btn btn-danger" name="delete" value="Hapus">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ChooseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Pilih Cabang</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <div class="modal-body">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span style="" class="form-control filled-input form-control-sm text-dark" id="inputGroup-sizing-sm">Cabang</span>
                            </div>

                            <select id="" class="form-control form-control-sm custom-select custom-select-sm branch_modal" name="branch">
                                <?php

                                $where_branch = "";
                                if ($_SESSION['group'] == "franchise") {
                                    $filter_cabang = str_replace(",", "','", $_SESSION['branch']);
                                    $where_branch = " WHERE id_cabang IN ('" . $filter_cabang . "')";
                                }

                                $query_get_branch = mysqli_query($conn, "SELECT id_cabang,nama_cabang FROM tb_cabang " . $where_branch . " ORDER BY id_cabang ASC");
                                // echo $query_get_branch;
                                while ($row_branch = mysqli_fetch_array($query_get_branch)) {
                                    $id_branch = $row_branch['id_cabang'];
                                    $nama_cabang = $row_branch['nama_cabang'];
                                    echo '
                                            <option value="' . $id_branch . '" >' . $nama_cabang . '</option>
                                        ';
                                }


                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <a class="choose_branch btn btn-success btn-sm"><span class="text-white">Choose</span></a>
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
    $(document).ready(function() {

        $(document).on("click", ".choose_branch", function() {
            var branch = $(".branch_modal").val();
            window.location.replace("master_barang_masuk_edit.php?id_transaksi=new&id_cabang=" + branch);
        });

        $(document).on("click", ".hapus_button", function() {
            var id_hapus = $(this).data('id_hapus');
            $(".id_hapus").val(id_hapus);
        });

        $(document).on("keyup", ".nama_customer", function() {
            var nama_customer = $(this).val();
            $.ajax({
                type: "POST",
                url: "ajax/ajax_barang_masuk.php",
                data: {
                    "cari_nama_supplier": nama_customer
                },
                cache: true,
                success: function(result) {
                    $(".list_customer").html(result);
                }
            });
        });
    });
</script>