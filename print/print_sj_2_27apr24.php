<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";
session_start();

function penyebut($nilai)
{
    $nilai = abs($nilai);
    $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $temp = "";
    if ($nilai < 12) {
        $temp = " " . $huruf[$nilai];
    } else if ($nilai < 20) {
        $temp = penyebut($nilai - 10) . " Belas";
    } else if ($nilai < 100) {
        $temp = penyebut($nilai / 10) . " Puluh" . penyebut($nilai % 10);
    } else if ($nilai < 200) {
        $temp = " Seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
        $temp = penyebut($nilai / 100) . " Ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
        $temp = " Seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
        $temp = penyebut($nilai / 1000) . " Ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
        $temp = penyebut($nilai / 1000000) . " Juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
        $temp = penyebut($nilai / 1000000000) . " Milyar" . penyebut(fmod($nilai, 1000000000));
    } else if ($nilai < 1000000000000000) {
        $temp = penyebut($nilai / 1000000000000) . " Trilyun" . penyebut(fmod($nilai, 1000000000000));
    }
    return $temp;
}

$id_transaksi = mysqli_real_escape_string($conn, $_GET['id_invoice']);

$tanggal = "";
$nm_customer = "";
$alamat = "";
$tgl_jatuh_tempo = "";
$tgl_jatuh_tempo = "";
$keterangan = "";
$nm_user = "";
$sql_get_barang_keluar = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_invoice = '" . $id_transaksi . "'");
if ($row_barang_keluar = mysqli_fetch_array($sql_get_barang_keluar)) {
    $tanggal = date("d - F - Y", strtotime($row_barang_keluar['tgl_kirim']));
    $tgl = $row_barang_keluar['tgl_kirim'];
    $penerima = $row_barang_keluar['penerima'];
    // $id_customer = $row_barang_keluar['id_customer'];
    // $sql_get_customer = mysqli_query($conn, "SELECT * FROM tb_customer WHERE id_customer = '" . $id_customer . "'");
    // if ($row_customer = mysqli_fetch_array($sql_get_customer)) {
    //     $nm_customer = $row_customer['nama_customer'];
    //     $alamat = $row_customer['alamat_lengkap'];
    // }
    // $tgl_jatuh_tempo = date("d-F-y", strtotime($row_barang_keluar['tgl_jatuh_tempo']));
    // $jatuh_tempo = $row_barang_keluar['jatuh_tempo'];
    // $keterangan = $row_barang_keluar['keterangan'];
    // $ppn = $row_barang_keluar['ppn'];
    // $dibuat_oleh = $row_barang_keluar['dibuat_oleh'];
    // $jenis_user = $row_barang_keluar['by_user_pajak'];

    // $sql_get_karyawan = mysqli_query($conn, "SELECT nama_lengkap FROM tb_karyawan WHERE user_id = '" . $dibuat_oleh . "'");
    // if ($row_karyawan = mysqli_fetch_array($sql_get_karyawan)) {
    //     $nm_user = $row_karyawan['nama_lengkap'];
    // }

}

if(isset($_POST['ubah_tgl'])){
    $update_tgl = "UPDATE tb_packing SET tgl_kirim = '".$_POST['tgl_ubah']."' WHERE id_invoice = '" .$_POST['id_sj'] . "'";
    // echo $update_tgl.'<br>';
    // echo "<script>alert(".$update_tgl.")</script>";
    mysqli_query($conn,$update_tgl);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Faktur</title>
    <style>
        body {
            font-family: Arial;
            font-size: 8pt;
        }

        @media print {
			.non-print {
				display: none;
			}
        }
    </style>
</head>

<body onload="window.print();">
    <div style="display:flex;">
        <div style="width:50%;">
            <table style="width:100%;" border="0">
                <thead>
                    <tr>
                        <th style="text-align:left;font-size:10pt;vertical-align:middle;width:50%;">
                            <img src="logo_tangan.jpg" alt="" width="30px">

                        </th>

                    </tr>
                </thead>
            </table>
            <table style="width:100%;" border="0">
                <tr>
                    <th style="text-align:left;font-size:9pt;vertical-align:middle;width:20%;">
                        No SJ
                    </th>
                    <th>:</th>
                    <th style="text-align:left;font-size:9pt;"><?= str_replace("SO", "SJ", $id_transaksi); ?></th>
                </tr>
            </table>
        </div>
        <div style="width:50%;">
            <table style="width:100%;" border="0">
                <tr>
                    <th style="text-align:left;">Tanggal</th>
                    <th style="text-align:center;">:</th>
                    <th style="text-align:left;"><?= $tanggal; ?></th>
                </tr>
                <tr>
                    <!-- <th style="text-align:left;">Kepada</th>
                    <th style="text-align:center;">:</th>
                    <th style="text-align:left;"><?= $penerima; ?></th> -->
                </tr>
                <tr>
                    <!-- <th style="text-align:left;"></th>
                    <th style="text-align:center;"></th>
                    <th style="text-align:left;">Jl. Dr. Muwardi I no 15A, Grogol, Jakarta Barat, 11450</th> -->
                </tr>
            </table>
        </div>
    </div>
    <table style="width:100%;">
        <thead>
            <tr>
                <th style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;border-left:1px solid #ccc;">No.</th>
                <th style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;border-left:1px solid #ccc;">No Work Order</th>
                <th style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;">Kode Item</th>
                <th style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;">Nama</th>
                <th style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;">Polybag</th>
                <th style="border-top:1px solid #ccc;border-bottom:1px solid #ccc;text-align:center;">Pcs</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $subtotal = 0;
            $ttl_polybag = 0;
            $ttl_pcs = 0;
            $sql_hitung = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_invoice = '" . $id_transaksi . "' ");
            while ($row_hitung = mysqli_fetch_array($sql_hitung)) {
                $ttl_polybag += $row_hitung['polybag'];
                $ttl_pcs += $row_hitung['pcs'];
            }

            $total_polybag = 0;
            $total_pcs = 0;
            // $sql_get_barang_keluar = mysqli_query($conn, "SELECT polybag,COUNT(*) AS total_polybag, pcs,COUNT(*) AS total_pcs, id_sku  FROM tb_packing WHERE id_invoice = '" . $id_transaksi . "' GROUP BY id_sku");
            $sql_get_barang_keluar = mysqli_query($conn, "SELECT SUM(polybag)  AS polybag, SUM(pcs)  AS pcs, id_sku , id_transaksi FROM tb_packing WHERE id_invoice = '" . $id_transaksi . "' GROUP BY id_sku");
            while ($row_barang_keluar = mysqli_fetch_array($sql_get_barang_keluar)) {
                $id_sku = $row_barang_keluar['id_sku'];
                $id_wo = $row_barang_keluar['id_transaksi'];

                $nm_bahan = "";
                $polybag = "";
                $pcs = "";
                $sql_get_bahan = mysqli_query($conn, "SELECT id_sku,polybag,SUM(pcs) AS pcs FROM tb_packing WHERE id_sku = '" . $id_sku . "'");
                
                if ($row_bahan = mysqli_fetch_array($sql_get_bahan)) {
                    $polybag = $row_barang_keluar['polybag'];
                    $pcs = $row_barang_keluar['pcs'];
                }
                
                $sql_get_bahan_2 = mysqli_query($conn, "SELECT kode_sku,nama_sku FROM tb_sku WHERE id_sku = '" . $id_sku . "'");
                if ($row_bahan = mysqli_fetch_array($sql_get_bahan_2)) {
                    $kode_sku = $row_bahan['kode_sku'];
                    $nm_bahan = $row_bahan['nama_sku'];
                }

                $sql_get_bahan_3 = mysqli_query($conn, "SELECT keterangan FROM tb_bom WHERE nama_bom = '" . $kode_sku . "'");
                if ($row_bahan = mysqli_fetch_array($sql_get_bahan_3)) {
                    $nm_bahan = $row_bahan['keterangan'];
                }
                
                // var_dump($pcs);
                // $harga = $row_barang_keluar['harga'];

                echo '
                    <tr>
                        <td style="text-align:center;">' . $no . '</td>
                        <td style="text-align:center;">' . $id_wo . '</td>
                        <td style="text-align:center;">' . $kode_sku . '</td>
                        <td style="text-align:center;">' . $nm_bahan . '</td>
                        <td style="text-align:center;">' . $polybag . '</td>
                        <td style="text-align:center;">' . $pcs . '</td>
                    </tr>
                ';
                $total_polybag += $polybag;
                $total_pcs += $pcs;
                // $subtotal += ($harga * $qty);
                $no++;
            }


            // $nilai_ppn = ($subtotal * $ppn / 100);
            ?>
        </tbody>
    </table>
    <table style="width:100%;">
        <tr>
            <th style="text-align:right;border-top:1px solid #ccc;width:72%">Total Polybag:</th>
            <th style="text-align:left;border-top:1px solid #ccc;"><?= $total_polybag ?></th>
        </tr>
        <tr>
            <th style="text-align:right;border-bottom:1px solid #ccc">Total PCS:</th>
            <th style="text-align:left;border-bottom:1px solid #ccc;"><?= $total_pcs ?></th>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <th style="text-align:center;">Tanda Terima,</th>
            <th style="text-align:center;">Pengirim,</th>
            <th style="text-align:center;">Mengetahui,</th>
            <th style="text-align:center;">Hormat Kami,</th>
        </tr>
        <tr>
            <th style="text-align:center;"></th>
            <th style="text-align:center;"></th>
            <th style="text-align:center;"></th>
            <?php
            echo '<th style="text-align:center;padding-top:35px;">KPM</th>';
            ?>
        </tr>
    </table>

    <div class="non-print">
        <form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
    <table style="width:15%;">
    <?php
    // echo $tanggal.'<br>';
    ?>
        <tr>
            <td width="50px" style="text-align:center; font-size:15px;">Tanggal </td>
            <td style="text-align:center;">:</td>
            <td style="text-align:center;"><input type="date" name="tgl_ubah" id="" class="form-control form-control-sm " value="<?= $tgl; ?>"></td>
            <td style="text-align:center;"><input type="hidden" name="id_sj" id="" class="form-control form-control-sm " value="<?= $_GET['id_invoice'] ?>"></td>
            <td style="text-align:center;"><button type="submit" name="ubah_tgl"> Ubah </button></td>
        </tr>
    </table>
    </form>
    </div>
</body>

</html>