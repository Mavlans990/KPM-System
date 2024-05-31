<?php
include "lib/koneksi.php";
session_start();
$conn_other = mysqli_connect('203.161.184.26', 'coolplu1_warranty', 'stevsoft14*', 'coolplu1_warranty');

$xs = 0;
$tes = 0;
$select_keluar = "
        SELECT *
        FROM
            tb_barang_keluar
        WHERE 
            subtotal != '0' AND 
            no_polisi != 'Product' AND 
            jenis = 'new'
        GROUP BY kode_garansi
    ";
$query_keluar = mysqli_query($conn, $select_keluar);
$jum_keluar = mysqli_num_rows($query_keluar);
while ($row_keluar = mysqli_fetch_array($query_keluar)) {
    $select_garansi = "
        SELECT *
        FROM
            invoice
        WHERE 
            no_garansi = '" . $row_keluar['kode_garansi'] . "'
        GROUP BY no_garansi
    ";
    $query_garansi = mysqli_query($conn_other, $select_garansi);
    $data_garansi = mysqli_fetch_array($query_garansi);
    $jum_garansi = mysqli_num_rows($query_garansi);
    $tes += $jum_garansi;
    if ($jum_garansi < 1) {

        echo $xs . ". " . $row_keluar['kode_garansi'] . "<br>";

        $kode_garansi = "";
        $tgl_transaksi = "";
        $kaca_depan = "";
        $tahun_kaca_depan = date("Y-m-d");
        $kaca_belakang = "";
        $tahun_kaca_belakang = date("Y-m-d");
        $customer = "";
        $no_polisi = "";
        $tipe_mobil = "";

        $select_keluar2 = "
            SELECT *
            FROM
                tb_barang_keluar
            WHERE 
                kode_garansi = '" . $row_keluar['kode_garansi'] . "'";
        $query_keluar2 = mysqli_query($conn, $select_keluar2);
        while ($row_keluar2 = mysqli_fetch_array($query_keluar2)) {


            $select_cust = "
                SELECT *
                FROM
                    tb_customer
                WHERE
                    id_customer = '" . $row_keluar2['id_customer'] . "'
            ";
            $query_cust = mysqli_query($conn_other, $select_cust);
            $data_cust = mysqli_fetch_array($query_cust);

            $select_mobil = "
                SELECT *
                FROM
                    tb_tipe_mobil
                WHERE
                    id_tipe = '" . $row_keluar2['tipe_mobil'] . "'
            ";
            $query_mobil = mysqli_query($conn, $select_mobil);
            $data_mobil = mysqli_fetch_array($query_mobil);

            $kode_garansi = $row_keluar2['kode_garansi'];
            $no_polisi = $row_keluar2['no_polisi'];
            $tgl_transaksi = $row_keluar2['tgl_transaksi'];
            $customer = $data_cust['nama_customer'];
            $tipe_mobil = $data_mobil['tipe_mobil'];

            if ($row_keluar2['bagian_mobil'] == "Kaca Depan") {
                $select_kaca_depan = "
                    SELECT *
                    FROM
                        tb_bahan
                    WHERE
                        id_bahan = '" . $row_keluar2['id_bahan'] . "'
                ";
                $query_kaca_depan = mysqli_query($conn, $select_kaca_depan);
                $data_kaca_depan = mysqli_fetch_array($query_kaca_depan);

                $kaca_depan = $data_kaca_depan['nama_bahan'];
                $tahun_kaca_depan = date("Y-m-d", strtotime("+" . $data_kaca_depan['masa_berlaku'] . " years"));
            }

            if ($row_keluar2['bagian_mobil'] == "Kaca SKKB") {
                $select_kaca_belakang = "
                    SELECT *
                    FROM
                        tb_bahan
                    WHERE
                        id_bahan = '" . $row_keluar2['id_bahan'] . "'
                ";
                $query_kaca_belakang = mysqli_query($conn, $select_kaca_belakang);
                $data_kaca_belakang = mysqli_fetch_array($query_kaca_belakang);

                $kaca_belakang = $data_kaca_belakang['nama_bahan'];
                $tahun_kaca_belakang = date("Y-m-d", strtotime("+" . $data_kaca_belakang['masa_berlaku'] . " years"));
            }
        }

        $insert = "
            INSERT INTO invoice(
                id,
                no_garansi,
                nama,
                type_mobil,
                no_plat,
                seri_kaca_depan,
                seri_kaca_samping,
                seri_kaca_belakang,
                tgl_pasang,
                grnsi_kacadepan,
                grns_kacabelakang,
                grns_kacasamping,
                pemasangan
            ) VALUES(
                '',
                '" . $kode_garansi . "',
                'Raid Abdul',
                '" . $tipe_mobil . "',
                '" . $no_polisi . "',
                '" . $kaca_depan . "',
                '" . $kaca_belakang . "',
                '" . $kaca_belakang . "',
                '" . $tgl_transaksi . "',
                '" . $tahun_kaca_depan . "',
                '" . $tahun_kaca_belakang . "',
                '" . $tahun_kaca_belakang . "',
                'mobil'
            )
        ";
        $query_insert = mysqli_query($conn_other, $insert);

        echo $insert . "<br>";
    }

    $xs++;
}

echo "<br>";
echo $jum_keluar . "<br>";
echo $tes . "<br>";
