<?php
ob_start();
session_start();

include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (isset($_POST['pilih_cabang'])) {
    $cabang = mysqli_real_escape_string($conn, $_POST['pilih_cabang']);

    $filter_cabang = "";
    if ($cabang !== "") {
        $filter_cabang = " WHERE id_cabang = '" . $cabang . "'";
    }

    $hasil = "";

    $select_cabang = "
        SELECT *
        FROM
            tb_cabang
        " . $filter_cabang . "
        ORDER BY id_cabang ASC
    ";
    $query_cabang = mysqli_query($conn, $select_cabang);
    while ($row_cabang = mysqli_fetch_array($query_cabang)) {
        $pendapatan_hari_ini = 0;
        $select_hari_ini = "SELECT * FROM tb_barang_keluar WHERE tgl_transaksi = '" . date("Y-m-d") . "' AND id_cabang = '" . $row_cabang['id_cabang'] . "' AND status_keluar = 's'";
        $query_hari_ini = mysqli_query($conn, $select_hari_ini);
        while ($row_hari_ini = mysqli_fetch_array($query_hari_ini)) {
            $ppn = $row_hari_ini['ppn'] * $row_hari_ini['total'] / 100;
            $pendapatan_hari_ini = $pendapatan_hari_ini + $row_hari_ini['total'] + $ppn;
        }

        $pendapatan_mutasi_hari_ini = 0;
        $select_mutasi_hari_ini = "
                            SELECT * FROM inv_adjust_out WHERE inv_date = '" . date("Y-m-d") . "' AND id_branch = '" . $row_cabang['id_cabang'] . "' AND is_sell = '1'
                        ";
        $query_mutasi_hari_ini = mysqli_query($conn, $select_mutasi_hari_ini);
        while ($row_mutasi_hari_ini = mysqli_fetch_array($query_mutasi_hari_ini)) {
            $pendapatan_mutasi_hari_ini += $row_mutasi_hari_ini['biaya'];
        }

        $pendapatan_bulan_ini = 0;
        $select_bulan_ini = "SELECT * FROM tb_barang_keluar WHERE tgl_transaksi >= '" . date("Y-m") . "-1' AND tgl_transaksi <= '" . date("Y-m-d") . "' AND id_cabang = '" . $row_cabang['id_cabang'] . "' AND status_keluar = 's'";
        $query_bulan_ini = mysqli_query($conn, $select_bulan_ini);
        while ($row_bulan_ini = mysqli_fetch_array($query_bulan_ini)) {
            $ppn = $row_bulan_ini['ppn'] * $row_bulan_ini['total'] / 100;
            $pendapatan_bulan_ini = $pendapatan_bulan_ini + $row_bulan_ini['total'] + $ppn;
        }

        $pendapatan_mutasi_bulan_lalu = 0;
        $select_mutasi_bulan_lalu = "
                            SELECT * FROM inv_adjust_out WHERE inv_date >= '" . date("Y-m", strtotime("-1 month")) . "-1' AND inv_date < '" . date("Y-m") . "-1' AND id_branch = '" . $row_cabang['id_cabang'] . "' AND is_sell = '1'
                        ";
        $query_mutasi_bulan_lalu = mysqli_query($conn, $select_mutasi_bulan_lalu);
        while ($row_mutasi_bulan_lalu = mysqli_fetch_array($query_mutasi_bulan_lalu)) {
            $pendapatan_mutasi_bulan_lalu += $row_mutasi_bulan_lalu['biaya'];
        }

        $pendapatan_bulan_lalu = 0;
        $select_bulan_lalu = "SELECT * FROM tb_barang_keluar WHERE tgl_transaksi >= '" . date("Y-m", strtotime("-1 month")) . "-1' AND tgl_transaksi < '" . date("Y-m") . "-1' AND id_cabang = '" . $row_cabang['id_cabang'] . "' AND status_keluar = 's'";
        $query_bulan_lalu = mysqli_query($conn, $select_bulan_lalu);
        while ($row_bulan_lalu = mysqli_fetch_array($query_bulan_lalu)) {
            $ppn = $row_bulan_lalu['ppn'] * $row_bulan_lalu['total'] / 100;
            $pendapatan_bulan_lalu = $pendapatan_bulan_lalu + $row_bulan_lalu['total'] + $ppn;
        }

        $pendapatan_mutasi_bulan_ini = 0;
        $select_mutasi_bulan_ini = "
                            SELECT * FROM inv_adjust_out WHERE inv_date >= '" . date("Y-m") . "-1' AND inv_date <= '" . date("Y-m-d") . "' AND id_branch = '" . $row_cabang['id_cabang'] . "' AND is_sell = '1'
                        ";
        $query_mutasi_bulan_ini = mysqli_query($conn, $select_mutasi_bulan_ini);
        while ($row_mutasi_bulan_ini = mysqli_fetch_array($query_mutasi_bulan_ini)) {
            $pendapatan_mutasi_bulan_ini += $row_mutasi_bulan_ini['biaya'];
        }

        $total_transaksi_bulan_ini = 0;
        $select_transaksi_bulan_ini = "
                            SELECT * FROM tb_barang_keluar WHERE tgl_transaksi >= '" . date("Y-m") . "-1' AND tgl_transaksi <= '" . date("Y-m-d") . "' AND id_cabang = '" . $row_cabang['id_cabang'] . "' AND status_keluar = 's' GROUP BY id_transaksi
                        ";
        $query_transaksi_bulan_ini = mysqli_query($conn, $select_transaksi_bulan_ini);
        $jum_transaksi_bulan_ini = mysqli_num_rows($query_transaksi_bulan_ini);
        $total_transaksi_bulan_ini = $jum_transaksi_bulan_ini;

        $pendapatan_tahun_ini = 0;
        $select_tahun_ini = "SELECT * FROM tb_barang_keluar WHERE tgl_transaksi >= '" . date("Y") . "-1-1' AND tgl_transaksi <= '" . date("Y-m-d") . "' AND id_cabang = '" . $row_cabang['id_cabang'] . "' AND status_keluar = 's'";
        $query_tahun_ini = mysqli_query($conn, $select_tahun_ini);
        while ($row_tahun_ini = mysqli_fetch_array($query_tahun_ini)) {
            $ppn = $row_tahun_ini['ppn'] * $row_tahun_ini['total'] / 100;
            $pendapatan_tahun_ini = $pendapatan_tahun_ini + $row_tahun_ini['total'] + $ppn;
        }

        $pendapatan_mutasi_tahun_ini = 0;
        $select_mutasi_tahun_ini = "
                            SELECT * FROM inv_adjust_out WHERE inv_date >= '" . date("Y") . "-1' AND inv_date <= '" . date("Y-m-d") . "' AND id_branch = '" . $row_cabang['id_cabang'] . "' AND is_sell = '1'
                        ";
        $query_mutasi_tahun_ini = mysqli_query($conn, $select_mutasi_tahun_ini);
        while ($row_mutasi_tahun_ini = mysqli_fetch_array($query_mutasi_tahun_ini)) {
            $pendapatan_mutasi_tahun_ini += $row_mutasi_tahun_ini['biaya'];
        }

        $hasil = $hasil . '
                    <div class="mb-5">
                    <h5 class="mb-5"> - ' . $row_cabang['nama_cabang'] . ' </h5>
                    <div class="row">
                        <div class="col-md-12 col-sm-2">
                            <div class="card shadow text-primary border-primary">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-uppercase mb-10">Penjualan Hari ini</span>
                                    <div class="d-flex justify-content-end align-items-center justify-content-between position-relative">
                                        <div>
                                            <span class="d-block display-7 font-weight-400">
                                                 ' . number_format($pendapatan_hari_ini + $pendapatan_mutasi_hari_ini) . '
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                   
                        <div class="col-md-12 col-sm-2">
                            <div class="card shadow text-success border-success">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-uppercase mb-10">Penjualan Bulan ini</span>
                                    <div class="d-flex justify-content-end align-items-center justify-content-between position-relative">
                                        <div>
                                            <span class="d-block display-7 font-weight-400">
                                                 ' . number_format($pendapatan_bulan_ini + $pendapatan_mutasi_bulan_ini) . '
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-2">
                            <div class="card shadow text-danger border-danger">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-uppercase mb-10">Transaksi Barang Keluar Bulan Ini</span>
                                    <div class="d-flex justify-content-end align-items-center justify-content-between position-relative">
                                        <div>
                                            <span class="d-block display-7 font-weight-400">
                                                 ' . number_format($total_transaksi_bulan_ini) . '
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-2">
                            <div class="card shadow text-warning border-warning">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-uppercase mb-10">Pendapatan Bulan Lalu</span>
                                    <div class="d-flex justify-content-end align-items-center justify-content-between position-relative">
                                        <div>
                                            <span class="d-block display-7 font-weight-400">
                                                 ' . number_format($pendapatan_bulan_lalu + $pendapatan_mutasi_bulan_lalu) . '
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-2">
                            <div class="card shadow text-info border-info">
                                <div class="card-body">
                                    <span class="d-block font-11 font-weight-500 text-uppercase mb-10">Penjualan Tahun ini</span>
                                    <div class="d-flex justify-content-end align-items-center justify-content-between position-relative">
                                        <div>
                                            <span class="d-block display-7 font-weight-400">
                                                 ' . number_format($pendapatan_tahun_ini + $pendapatan_mutasi_tahun_ini) . '
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                    </div>
                    </div>
                        ';
    }
    echo $hasil;
}



ob_flush();
