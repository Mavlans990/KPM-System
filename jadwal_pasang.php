<?php
session_start();
include "lib/koneksi.php";
include "lib/appcode.php";
include "lib/format.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}

if (isset($_POST['search'])) {
    $_SESSION['tgl1'] = $_POST['tgl1'];
    $_SESSION['tgl2'] = $_POST['tgl2'];
    header("location:jadwal_pasang.php");
}

if (isset($_POST['hapus_session'])) {
    unset($_SESSION['tgl1']);
    unset($_SESSION['tgl2']);
    header("location:jadwal_pasang.php");
}

if (isset($_POST['ubah_pengerjaan'])) {
    $update_sj2 = "UPDATE sj2 SET is_sinkron = 'c' WHERE id_sj2 = '" . mysqli_real_escape_string($conn, $_POST['id_pengerjaan']) . "'";
    $query_update_sj2 = mysqli_query($conn, $update_sj2);
    if ($query_update_sj2) {
        $status = "Ubah status berhasil";
    } else {
        $status = "Ubah status gagal";
    }

    echo '<script>alert("' . $status . '");window.location="jadwal_pasang.php"</script>';
}

if (isset($_POST['ubah_selesai'])) {
    $update_sj2 = "UPDATE sj2 SET is_sinkron = 's' WHERE id_sj2 = '" . mysqli_real_escape_string($conn, $_POST['id_selesai']) . "'";
    $query_update_sj2 = mysqli_query($conn, $update_sj2);

    if ($query_update_sj2) {
        $status = "Update status berhasil";
    } else {
        $status = "Update status gagal";
    }

    echo '<script>alert("' . $status . '");window.location="jadwal_pasang.php"</script>';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Reydecal Admin</title>
    <meta name="description" content="A responsive bootstrap 4 admin dashboard template by hencework" />

    <!-- select2 CSS -->
    <link href="vendors/select2/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Data Table CSS -->
    <link href="vendors/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="vendors/datatables.net-responsive-dt/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />

    <!-- Toggles CSS -->
    <link href="vendors/jquery-toggles/css/toggles.css" rel="stylesheet" type="text/css">
    <link href="vendors/jquery-toggles/css/themes/toggles-light.css" rel="stylesheet" type="text/css">

    <!-- Custom CSS -->
    <link href="dist/css/style.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style type="text/css">
        table thead tr th {
            text-align: center;
        }

        hr {
            margin: 2px 0;
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
                    <h1 class="h3 mb-2 text-gray-800">Jadwal Pasang <?php
                                                                    $tgl1 = '';
                                                                    $tgl2 = '';
                                                                    if (isset($_SESSION['tgl1'])) {
                                                                        echo "from " . date('d/m/Y', strtotime($_SESSION['tgl1'])) . " to " . date('d/m/Y', strtotime($_SESSION['tgl2']));

                                                                        $tgl1 = $_SESSION['tgl1'];
                                                                        $tgl2 = $_SESSION['tgl2'];
                                                                    }
                                                                    ?>

                    </h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="form-group form-inline" style="margin-bottom:0px;">
                                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                    <input type="date" class="form-control tgl1" name="tgl1" value="<?php echo $tgl1; ?>" required>
                                    <span style="margin:0px 10px">TO</span>
                                    <input type="date" class="form-control tgl2" name="tgl2" value="<?php echo $tgl2; ?>" required>
                                    <input type="submit" class="btn btn-success" name="search" id="search" value="Search">
                                </form>
                                <?php
                                if (isset($_SESSION['tgl1'])) {
                                ?>
                                    <form action="" method="post">
                                        <input type="submit" class="btn btn-danger" name="hapus_session" value="Reset">
                                    </form>
                                <?php
                                }
                                ?>

                            </div>
                        </div>
                    </div>

                    <div class="card shadow mb-4">

                        <?php
                        if (isset($_SESSION['tgl1'])) {
                        ?>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered tb_jps_ins tb_jps_re_ins" id="myTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Pasang</th>
                                                <?php
                                                $select_cabang = "SELECT * FROM tb_cabang WHERE stat != 'no' ORDER BY id_cabang ASC";
                                                $query_cabang = mysqli_query($conn, $select_cabang);
                                                while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                                    echo '<th>' . $row_cabang['nama_cabang'] . '</th>';
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $select_sj2 = "SELECT * FROM inv_product_out WHERE (tgl_pasang != '' OR tgl_pasang != '0000-00-00') AND tgl_pasang >= '" . $_SESSION['tgl1'] . "' AND tgl_pasang <= '" . $_SESSION['tgl2'] . "' GROUP BY tgl_pasang";
                                            $query_sj2 = mysqli_query($conn, $select_sj2);
                                            while ($row_sj2 = mysqli_fetch_array($query_sj2)) {
                                                echo '<tr>
                                                    <td>' . date("D, d M Y", strtotime($row_sj2['tgl_pasang'])) . '</td>
                                                    ';
                                                $select_cabang2 = "SELECT * FROM tb_cabang WHERE stat != 'no' ORDER BY id_cabang ASC";
                                                $query_cabang2 = mysqli_query($conn, $select_cabang2);
                                                while ($row_cabang2 = mysqli_fetch_array($query_cabang2)) {
                                                    echo '<td>';

                                                    $select_sj22 = "SELECT * FROM inv_product_out WHERE tgl_pasang = '" . $row_sj2['tgl_pasang'] . "' AND id_branch = '" . $row_cabang2['id_cabang'] . "' GROUP BY id_inv_out";
                                                    $query_sj22 = mysqli_query($conn, $select_sj22);
                                                    $jum_sj22 = mysqli_num_rows($query_sj22);
                                                    if ($jum_sj22 > 0) {
                                                        while ($row_sj22 = mysqli_fetch_array($query_sj22)) {
                                                            $hasil = "";

                                                            $select_user = "SELECT * FROM tb_user WHERE id_user = '" . $row_sj22['nm_cust'] . "'";
                                                            $query_user = mysqli_query($conn, $select_user);
                                                            $data_user = mysqli_fetch_array($query_user);


                                                            $link = '<a href="inventory_product_out.php?branch=' . $row_sj22['id_branch'] . '&id_inv_out=' . $row_sj22['id_inv_out'] . '&view=detail" style="font-weight:bold;">No. Invoice : ' . $row_sj22['id_inv_out'] . '</a><br>
                                                                <p>Nama Pelanggan : ' . $row_sj22['nm_cust'] . '</p>
                                                                <p>Email : ' . $row_sj22['id_user'] . '</p>
                                                                <p>No. Telp : ' . $row_sj22['telp_cust'] . '</p>';

                                                            $select_inv = "SELECT * FROM inv_product_out WHERE id_inv_out = '" . $row_sj22['id_inv_out'] . "'";
                                                            $query_inv = mysqli_query($conn, $select_inv);
                                                            while ($row_inv = mysqli_fetch_array($query_inv)) {
                                                                $select_product = "SELECT * FROM m_product WHERE m_product_id = '" . $row_inv['id_product'] . "'";
                                                                $query_product = mysqli_query($conn, $select_product);
                                                                $data_product = mysqli_fetch_array($query_product);

                                                                $pemasangan = "";
                                                                if ($data_product['type_product'] == "Product") {
                                                                    if ($row_sj22['pemasangan'] == "pasang_sendiri") {
                                                                        $pemasangan = "<p>Pemasangan : Pasang Sendiri</p>";
                                                                    } else {
                                                                        $pemasangan = "<p>Pemasangan : " . ucfirst($row_sj22['pemasangan']) . "</p>";
                                                                    }
                                                                }

                                                                $paket = "";
                                                                if ($data_product['type_product'] == "Product") {
                                                                    if ($row_sj22['pemasangan'] == "dipasangkan") {
                                                                        if ($row_sj22['paket'] == "express") {
                                                                            $paket = "<p>Paket : Express</p>";
                                                                        } else if ($row_sj22['paket'] == "dirumah") {
                                                                            $paket = "<p>Paket : Pasang Dirumah</p>";
                                                                        } else {
                                                                            $paket = "<p>Paket : Regular</p>";
                                                                        }
                                                                    }
                                                                }

                                                                $tanggal_pasang = "";
                                                                if ($data_product['type_product'] == "Product") {
                                                                    if ($row_sj22['pemasangan'] == "dipasangkan") {
                                                                        $tanggal_pasang = "<p>Tanggal Pasang : " . date("d M Y", strtotime($row_sj22['tgl_pasang'])) . "</p>";
                                                                    }
                                                                }

                                                                $cabang = "";
                                                                if ($data_product['type_product'] == "Product") {
                                                                    if ($row_sj22['pemasangan'] == "dipasangkan") {
                                                                        $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_sj22['id_branch'] . "'";
                                                                        $query_cabang = mysqli_query($conn, $select_cabang);
                                                                        $data_cabang = mysqli_fetch_array($query_cabang);
                                                                        if ($row_sj22['paket'] !== "dirumah") {
                                                                            $cabang = "<p>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                                                                        }
                                                                    }
                                                                }

                                                                $stat_cus = "";
                                                                if ($data_product['type_product'] == "Product") {
                                                                    if ($row_sj22['stat_cus'] == "yes") {
                                                                        $stat_cus = "<p>Body Custom : Ya</p>";
                                                                    } else {
                                                                        $stat_cus = "<p>Body Custom : Tidak</p>";
                                                                    }
                                                                }

                                                                $part = "";
                                                                if ($data_product['type_product'] == "Product") {
                                                                    if ($row_sj22['part'] == "yes") {
                                                                        $part = "<p>Cat ulang / baret : Ya</p>";
                                                                    } else {
                                                                        $part = "<p>Cat ulang / baret : Tidak</p>";
                                                                    }
                                                                }

                                                                $keterangan = "";
                                                                if ($data_product['type_product'] == "Product") {
                                                                    if ($row_sj22['keterangan'] !== "") {
                                                                        if ($row_sj22['stat_cus'] == "yes") {
                                                                            $keterangan = "<p>Keterangan body custom : " . $row_sj22['keterangan'] . "</p>";
                                                                        }
                                                                    }
                                                                }

                                                                $keterangan_part = "";
                                                                if ($data_product['type_product'] == "Product") {
                                                                    if ($row_sj22['keterangan_part'] !== "") {
                                                                        if ($row_sj22['part'] == "yes") {
                                                                            $keterangan_part = "<p>Keterangan body cat ulang / baret : " . $row_sj22['keterangan_part'] . "</p>";
                                                                        }
                                                                    }
                                                                }

                                                                $hasil = $hasil . "<br><p style='font-weight:bold;'>" . $data_product['nm_product'] . " x " . $row_inv['stock_out'] . "</p>" . $pemasangan . $paket . $tanggal_pasang . $cabang . $stat_cus . $part . $keterangan . $keterangan_part;
                                                            }
                                                            echo $link . $hasil;
                                                        }
                                                    }
                                                    echo '</td>';
                                                }
                                                echo '</tr>
                                                ';
                                            }

                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php
                        } else {
                        ?>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered tb_jps_ins tb_jps_re_ins" id="myTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Pasang</th>
                                                <?php
                                                if ($_SESSION['group'] == "super") {
                                                    $where_branch = " WHERE stat != 'no'";
                                                } else {
                                                    $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                                }

                                                $select_cabang = "SELECT * FROM tb_cabang " . $where_branch . " ORDER BY id_cabang ASC";
                                                $query_cabang = mysqli_query($conn, $select_cabang);
                                                while ($row_cabang = mysqli_fetch_array($query_cabang)) {
                                                    echo '<th>' . $row_cabang['nama_cabang'] . '</th>';
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            for ($k = -14; $k <= 14; $k++) {

                                                if ($k < 0) {
                                                    $deta = $k . " days";
                                                } else {
                                                    $deta = "+" . $k . " days";
                                                }

                                                $ka = date("Y-m-d", strtotime($deta));

                                                if ($_SESSION['group'] == "super") {
                                                    $cabang = "";
                                                } else {
                                                    $cabang = " AND id_branch = '" . $_SESSION['branch'] . "'";
                                                }

                                                $select_sj2 = "SELECT * FROM inv_product_out WHERE tgl_pasang = '" . $ka . "' " . $cabang . "  GROUP BY tgl_pasang";
                                                $query_sj2 = mysqli_query($conn, $select_sj2);
                                                while ($row_sj2 = mysqli_fetch_array($query_sj2)) {
                                                    echo '<tr>
                                                    <td class="text-center">' . date("D, d M Y", strtotime($deta)) . '</td>
                                                    ';


                                                    if ($_SESSION['group'] == "super") {
                                                        $where_branch = " WHERE stat != 'no'";
                                                    } else {
                                                        $where_branch = " WHERE id_cabang = '" . $_SESSION['branch'] . "'";
                                                    }

                                                    $select_cabang2 = "SELECT * FROM tb_cabang " . $where_branch . " ORDER BY id_cabang ASC";
                                                    $query_cabang2 = mysqli_query($conn, $select_cabang2);
                                                    while ($row_cabang2 = mysqli_fetch_array($query_cabang2)) {

                                                        echo '<td style="max-width:30%;">';
                                                        $select_sj22 = "SELECT * FROM inv_product_out WHERE tgl_pasang = '" . $ka . "' AND id_branch = '" . $row_cabang2['id_cabang'] . "' GROUP BY id_inv_out";
                                                        $query_sj22 = mysqli_query($conn, $select_sj22);

                                                        $hasil = "";

                                                        while ($row_sj22 = mysqli_fetch_array($query_sj22)) {




                                                            $link = '<a href="inventory_product_out.php?branch=' . $row_sj22['id_branch'] . '&id_inv_out=' . $row_sj22['id_inv_out'] . '&view=detail" style="font-weight:bold;">No. Invoice : ' . $row_sj22['id_inv_out'] . '</a><br>
                                                                <p>Nama Pelanggan : ' . $row_sj22['nm_cust'] . '</p>
                                                                <p>Email : ' . $row_sj22['id_user'] . '</p>
                                                                <p>No. Telp : ' . $row_sj22['telp_cust'] . '</p>';

                                                            $select_inv = "SELECT * FROM inv_product_out WHERE id_inv_out = '" . $row_sj22['id_inv_out'] . "'";
                                                            $query_inv = mysqli_query($conn, $select_inv);
                                                            while ($row_inv = mysqli_fetch_array($query_inv)) {



                                                                $select_product = "SELECT * FROM m_product WHERE m_product_id = '" . $row_inv['id_product'] . "' ORDER BY type_product DESC";
                                                                $query_product = mysqli_query($conn, $select_product);
                                                                $row_product = mysqli_fetch_array($query_product);

                                                                $pemasangan = "";
                                                                if ($row_product['type_product'] == "Product") {
                                                                    if ($row_sj22['pemasangan'] == "pasang_sendiri") {
                                                                        $pemasangan = "<p>Pemasangan : Pasang Sendiri</p>";
                                                                    } else {
                                                                        $pemasangan = "<p>Pemasangan : " . ucfirst($row_sj22['pemasangan']) . "</p>";
                                                                    }
                                                                }

                                                                $paket = "";
                                                                if ($row_product['type_product'] == "Product") {
                                                                    if ($row_sj22['pemasangan'] == "dipasangkan") {
                                                                        if ($row_sj22['paket'] == "express") {
                                                                            $paket = "<p>Paket : Express</p>";
                                                                        } else if ($row_sj22['paket'] == "dirumah") {
                                                                            $paket = "<p>Paket : Pasang Dirumah</p>";
                                                                        } else {
                                                                            $paket = "<p>Paket : Regular</p>";
                                                                        }
                                                                    }
                                                                }

                                                                $tanggal_pasang = "";
                                                                if ($row_product['type_product'] == "Product") {
                                                                    if ($row_sj22['pemasangan'] == "dipasangkan") {
                                                                        $tanggal_pasang = "<p>Tanggal Pasang : " . date("d M Y", strtotime($row_sj22['tgl_pasang'])) . "</p>";
                                                                    }
                                                                }

                                                                $cabang = "";
                                                                if ($row_product['type_product'] == "Product") {
                                                                    if ($row_sj22['pemasangan'] == "dipasangkan") {
                                                                        $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_sj22['id_branch'] . "'";
                                                                        $query_cabang = mysqli_query($conn, $select_cabang);
                                                                        $data_cabang = mysqli_fetch_array($query_cabang);
                                                                        if ($row_sj22['paket'] !== "dirumah") {
                                                                            $cabang = "<p>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                                                                        }
                                                                    }
                                                                }

                                                                $stat_cus = "";
                                                                if ($row_product['type_product'] == "Product") {
                                                                    if ($row_sj22['stat_cus'] == "yes") {
                                                                        $stat_cus = "<p>Body Custom : Ya</p>";
                                                                    } else {
                                                                        $stat_cus = "<p>Body Custom : Tidak</p>";
                                                                    }
                                                                }

                                                                $part = "";
                                                                if ($row_product['type_product'] == "Product") {
                                                                    if ($row_sj22['part'] == "yes") {
                                                                        $part = "<p>Cat ulang / baret : Ya</p>";
                                                                    } else {
                                                                        $part = "<p>Cat ulang / baret : Tidak</p>";
                                                                    }
                                                                }

                                                                $keterangan = "";
                                                                if ($row_product['type_product'] == "Product") {
                                                                    if ($row_sj22['keterangan'] !== "") {
                                                                        if ($row_sj22['stat_cus'] == "yes" || $row_sj22['part'] == "yes") {
                                                                            $keterangan = "<p>Keterangan body custom : " . $row_sj22['keterangan'] . "</p>";
                                                                        }
                                                                    }
                                                                }

                                                                $keterangan_part = "";
                                                                if ($row_product['type_product'] == "Product") {
                                                                    if ($row_sj22['keterangan_part'] !== "") {
                                                                        if ($row_sj22['part'] == "yes") {
                                                                            $keterangan_part = "<p>Keterangan body cat ulang / baret : " . $row_sj22['keterangan_part'] . "</p>";
                                                                        }
                                                                    }
                                                                }


                                                                $hasil = $hasil . "<br><p style='font-weight:bold;'>" . $row_product['nm_product'] . " x " . $row_inv['stock_out'] . "</p>" . $pemasangan . $paket . $tanggal_pasang . $cabang . $stat_cus . $part . $keterangan . $keterangan_part;
                                                            }
                                                            $link = $link . $hasil;
                                                            echo $link;
                                                        }


                                                        echo '</td>';
                                                    }
                                                    echo '</tr>
                                                ';
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php //include "part/footer.php"; 
            ?>
            <!-- End of Footer -->
            <div class="modal fade" tabindex="-1" role="dialog" id="modal_pengerjaan">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ubah Status</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Apa anda yakin ingin mengubah status ke dalam pengerjaan ?</p>
                            <form action="" method="post">
                                <input type="hidden" name="id_pengerjaan" class="id_pengerjaan">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="ubah_pengerjaan">Ubah</button>
                            </form>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" tabindex="-1" role="dialog" id="modal_selesai">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ubah Status</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Apa anda yakin ingin mengubah status ke selesai ?</p>
                            <form action="" method="post">
                                <input type="hidden" name="id_selesai" class="id_selesai">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" name="ubah_selesai">Ubah</button>
                            </form>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->


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
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
            $(document).on("click", ".modal_pengerjaan", function() {
                var id_sj2 = $(this).data('id_sj2');
                $(".id_pengerjaan").val(id_sj2);
            });

            $(document).on("click", ".modal_selesai", function() {
                var id_sj2 = $(this).data('id_sj2');
                $(".id_selesai").val(id_sj2);
            });

        });
    </script>
</body>

</html>