<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
session_start();

if (isset($_POST['get_barang'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['get_barang']);
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
    $ex_barang = explode(" | ", $nama_barang);
    $id_barang = $ex_barang[0];

    $id_customer = explode(" | ", $nama_customer);
    $id_customer = $id_customer[0];

    $list_berat = "";
    $list_uom = "";
    $harga_terakhir="";
    $sql_get_berat = mysqli_query($conn, "SELECT harga FROM tb_barang_keluar WHERE id_bahan = '" . $id_barang . "' AND id_customer = '".$id_customer."' order by tgl_transaksi desc limit 1 ");
    while ($row_berat = mysqli_fetch_array($sql_get_berat)) {
        $harga_terakhir =  $row_berat['harga'];
    }

    $sql_get_berat = mysqli_query($conn, "SELECT berat FROM tb_stock WHERE id_bahan = '" . $id_barang . "' AND stock > 0 GROUP BY berat");
    while ($row_berat = mysqli_fetch_array($sql_get_berat)) {
        $list_berat = $list_berat . '
            <option value="' . $row_berat['berat'] . '">' . $row_berat['berat'] . '</option>
        ';
    }

    $sql_get_uom = mysqli_query($conn, "SELECT uom FROM tb_stock WHERE id_bahan = '" . $id_barang . "' AND stock > 0 GROUP BY uom");
    while ($row_uom = mysqli_fetch_array($sql_get_uom)) {
        $list_uom = $list_uom . '
            <option value="' . $row_uom['uom'] . '">' . $row_uom['uom'] . '</option>
        ';
    }

    echo $list_berat . "|" . $list_uom ."|".$harga_terakhir;
}

if (isset($_POST['cari_nama_customer'])) {
    $nama_customer = mysqli_real_escape_string($conn, $_POST['cari_nama_customer']);

    $hasil = "";
    $sql_get_nama_customer = mysqli_query($conn, "SELECT id_customer,nama_customer FROM tb_customer WHERE nama_customer LIKE '%" . $nama_customer . "%' ORDER BY nama_customer ASC LIMIT 50");
    while ($row_nama_customer = mysqli_fetch_array($sql_get_nama_customer)) {
        $hasil = $hasil . '
            <option value="' . $row_nama_customer['id_customer'] . ' | ' . $row_nama_customer['nama_customer'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_nama_barang'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['cari_nama_barang']);

    $hasil = "";
    $sql_get_barang = mysqli_query($conn, "SELECT id_bahan,nama_bahan FROM tb_bahan WHERE nama_bahan LIKE '%" . $nama_barang . "%' ORDER BY nama_bahan ASC LIMIT 50");
    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
        $hasil = $hasil . '
            <option value="' . $row_barang['id_bahan'] . ' | ' . $row_barang['nama_bahan'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['add_barang'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['add_barang']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $ex_nama_barang = explode(" | ", $nama_barang);
    $id_barang = $ex_nama_barang[0];

    $qty_barang = mysqli_real_escape_string($conn, $_POST['qty_barang']);
    $non_stock = mysqli_real_escape_string($conn, $_POST['non_stock']);
    $berat_barang = mysqli_real_escape_string($conn, $_POST['berat_barang']);
    $uom_barang = mysqli_real_escape_string($conn, $_POST['uom_barang']);
    $harga_barang = mysqli_real_escape_string($conn, $_POST['harga_barang']);
    //$tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $valid = 0;
    $sql_check_stock = mysqli_query($conn, "SELECT * FROM tb_stock WHERE id_bahan = '" . $id_barang . "' AND berat = '" . $berat_barang . "' AND uom = '" . $uom_barang . "' and stock >= '".$qty_barang."' ");
    if ($row_check_stock = mysqli_fetch_array($sql_check_stock)) {
        $valid = 1;
        $qty_stock = $row_check_stock['stock'];
    }

    if($non_stock ==1)
    {
        $valid = 1;
    }


    if ($valid == 1) {
        $id_no = id_gen_barang_keluar();

            if($non_stock=='1')
            {
                $subtotal = $harga_barang *$berat_barang;
            }
            else{
                $subtotal = $harga_barang * $qty_barang*$berat_barang;
            }
            
            $ppn = 0;
            $sql_ppn_barang_keluar = mysqli_query($conn, "SELECT ppn FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND ppn > 0 ORDER BY no_id ASC LIMIT 1");
            if ($data_ppn_barang_keluar = mysqli_fetch_array($sql_ppn_barang_keluar)) {
                $ppn = $data_ppn_barang_keluar['ppn'];
            }

            $sql_insert_barang = mysqli_query($conn, "
                INSERT INTO tb_barang_keluar(
                    no_id,
                    id_transaksi,
                    tgl_transaksi,
                    id_bahan,
                    qty,
                    berat,
                    uom,
                    harga,
                    subtotal,
                    total,
                    ppn,
                    dibuat_oleh,
                    dibuat_tgl
                ) VALUES(
                    '" . $id_no . "',
                    '" . $id_transaksi . "',
                    '" . date("Y-m-d") . "',
                    '" . $id_barang . "',
                    '" . $qty_barang . "',
                    '" . $berat_barang . "',
                    '" . $uom_barang . "',
                    '" . $harga_barang . "',
                    '" . $subtotal . "',
                    '" . $subtotal . "',
                    '" . $ppn . "',
                    '" . $_SESSION['id_user'] . "',
                    '" . date("Y-m-d") . "'
                )
            ");
    } 

    $hasil = "";

    $sql_get_transaksi = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' ORDER BY no_id ASC");
    $jum_transaksi = mysqli_num_rows($sql_get_transaksi);
    while ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
        $no_id = $row_transaksi['no_id'];
        $id_bahan = $row_transaksi['id_bahan'];
        $qty = $row_transaksi['qty'];
        $berat = $row_transaksi['berat'];
        $uom = $row_transaksi['uom'];
        $harga = $row_transaksi['harga'];
        $subtotal = $row_transaksi['subtotal'];

        $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
        $data_barang = mysqli_fetch_array($sql_get_barang);

        $hasil = $hasil . '
            <tr>
                <td class="text-center">' . $id_bahan . '</td>
                <td class="text-center">' . $data_barang['nama_bahan'] . '</td>
                <td class="text-center">' . $qty . '</td>
                <td class="text-center">' . round($berat) . '</td>
                <td class="text-center">' . $uom . '</td>
                <td class="text-center">' . number_format($harga) . '</td>
                <td class="text-center">' . number_format($subtotal) . '</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger del_barang" data-no_id="' . $no_id . '"><i class="fa fa-trash-o"></i></button>
                </td>
            </tr>
        ';
    }

    if ($jum_transaksi > 0) {
        $sql_get_transaksi = mysqli_query($conn, "SELECT SUM(subtotal) AS ttl_subtotal,ppn FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'");
        if ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
            $ttl_subtotal = $row_transaksi['ttl_subtotal'];
            $ppn_persen = $row_transaksi['ppn'];

            $ppn = $ttl_subtotal * $ppn_persen / 100;

            $hasil = $hasil . '
                <tr>
                    <td class="text-right" colspan="6">Total</td>
                    <td class="text-right" colspan="2">' . number_format($ttl_subtotal) . '</td>
                </tr>
                ';

            if ($_SESSION['jenis_pajak'] == 1) {
                $hasil = $hasil . '
                    <tr>
                        <td class="text-right" colspan="6">PPN <input type="number" name="" id="" class="text-right ppn_input" style="max-width:50px;" value="' . $ppn_persen . '"> %</td>
                        <td class="text-right" colspan="2">' . number_format($ppn) . '</td>
                    </tr>
                    ';
            }

            $hasil = $hasil . '
                <tr>
                    <td class="text-right" colspan="6">Grand Total</td>
                    <td class="text-right" colspan="2">' . number_format($ttl_subtotal + $ppn) . '</td>
                </tr>
            ';
        }
    }

    echo $valid . "|" . $hasil ."|".$non_stock;
}




if (isset($_POST['add_barang_po'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['add_barang_po']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $ex_nama_barang = explode(" | ", $nama_barang);
    $id_barang = $ex_nama_barang[0];

    $qty_barang = mysqli_real_escape_string($conn, $_POST['qty_barang']);
    $berat_barang = mysqli_real_escape_string($conn, $_POST['berat_barang']);
    $uom_barang = mysqli_real_escape_string($conn, $_POST['uom_barang']);
    $harga_barang = mysqli_real_escape_string($conn, $_POST['harga_barang']);
    //$tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $valid = 1;

        $id_no = id_gen_barang_po();

       
            $subtotal = 0;

          

            $sql_insert_barang = mysqli_query($conn, "
                INSERT INTO tb_barang_po(
                    no_id,
                    id_transaksi,
                    tgl_transaksi,
                    id_bahan,
                    qty,
                    berat,
                    uom,
                    harga,
                    subtotal,
                    total,
                    ppn,
                    dibuat_oleh,
                    dibuat_tgl
                ) VALUES(
                    '" . $id_no . "',
                    '" . $id_transaksi . "',
                    '" . date("Y-m-d") . "',
                    '" . $id_barang . "',
                    '" . $qty_barang . "',
                    '',
                    'ROLL',
                    '',
                    '',
                    '',
                    '',
                    '" . $_SESSION['id_user'] . "',
                    '" . date("Y-m-d") . "'
                )
            ");
      


    $hasil = "";
    $total_roll=0;
    $sql_get_transaksi = mysqli_query($conn, "SELECT * FROM tb_barang_po WHERE id_transaksi = '" . $id_transaksi . "' ORDER BY no_id ASC");
    $jum_transaksi = mysqli_num_rows($sql_get_transaksi);
    while ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
        $no_id = $row_transaksi['no_id'];
        $id_bahan = $row_transaksi['id_bahan'];
        $qty = $row_transaksi['qty'];
        $uom = $row_transaksi['uom'];

        $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
        $data_barang = mysqli_fetch_array($sql_get_barang);

        $hasil = $hasil . '
            <tr>
                <td class="text-center">' . $id_bahan . '</td>
                <td class="text-center">' . $data_barang['nama_bahan'] . '</td>
                <td class="text-center">' . $qty . '</td>
                <td class="text-center"></td>

                <td class="text-center">' . $uom . '</td>
               
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger del_barang" data-no_id="' . $no_id . '"><i class="fa fa-trash-o"></i></button>
                </td>
            </tr>
        ';

        $total_roll+=$qty;
    }

    if ($jum_transaksi > 0) {

            $hasil = $hasil . '
                <tr>
                    <td class="text-right" colspan="4">Total Roll</td>
                    <td class="text-right" colspan="2">' . number_format($total_roll) . '</td>
                </tr>
                ';
    }

    echo $valid . "|" . $hasil;
}




if (isset($_POST['del_barang_po'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['del_barang_po']);
    $no_id = mysqli_real_escape_string($conn, $_POST['no_id']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $sql_del_barang = mysqli_query($conn, "DELETE FROM tb_barang_po WHERE no_id = '" . $no_id . "'");

    $hasil = "";
    $total_roll=0;

    $sql_get_transaksi = mysqli_query($conn, "SELECT * FROM tb_barang_po WHERE id_transaksi = '" . $id_transaksi . "' ORDER BY no_id ASC");
    $jum_transaksi = mysqli_num_rows($sql_get_transaksi);
    while ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
        $no_id = $row_transaksi['no_id'];
        $id_bahan = $row_transaksi['id_bahan'];
        $qty = $row_transaksi['qty'];
        $uom = $row_transaksi['uom'];

        $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
        $data_barang = mysqli_fetch_array($sql_get_barang);

        $hasil = $hasil . '
            <tr>
                <td class="text-center">' . $id_bahan . '</td>
                <td class="text-center">' . $data_barang['nama_bahan'] . '</td>
                <td class="text-center">' . $qty . '</td>
                <td class="text-center"></td>

                <td class="text-center">' . $uom . '</td>
            
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger del_barang" data-no_id="' . $no_id . '"><i class="fa fa-trash-o"></i></button>
                </td>
            </tr>
        ';
        $total_roll+=$qty;
    }

    if ($jum_transaksi > 0) {

        $hasil = $hasil . '
            <tr>
                <td class="text-right" colspan="4">Total Roll</td>
                <td class="text-right" colspan="2">' . number_format($total_roll) . '</td>
            </tr>
            ';
    }

    echo $hasil;
}


if (isset($_POST['del_barang'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['del_barang']);
    $no_id = mysqli_real_escape_string($conn, $_POST['no_id']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $sql_del_barang = mysqli_query($conn, "DELETE FROM tb_barang_keluar WHERE no_id = '" . $no_id . "'");

    $hasil = "";

    $sql_get_transaksi = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' ORDER BY no_id ASC");
    $jum_transaksi = mysqli_num_rows($sql_get_transaksi);
    while ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
        $no_id = $row_transaksi['no_id'];
        $id_bahan = $row_transaksi['id_bahan'];
        $qty = $row_transaksi['qty'];
        $berat = $row_transaksi['berat'];
        $uom = $row_transaksi['uom'];
        $harga = $row_transaksi['harga'];
        $subtotal = $row_transaksi['subtotal'];

        $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
        $data_barang = mysqli_fetch_array($sql_get_barang);

        $hasil = $hasil . '
            <tr>
                <td class="text-center">' . $id_bahan . '</td>
                <td class="text-center">' . $data_barang['nama_bahan'] . '</td>
                <td class="text-center">' . $qty . '</td>
                <td class="text-center">' . $berat . '</td>
                <td class="text-center">' . $uom . '</td>
                <td class="text-center">' . number_format($harga) . '</td>
                <td class="text-center">' . number_format($subtotal) . '</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger del_barang" data-no_id="' . $no_id . '"><i class="fa fa-trash-o"></i></button>
                </td>
            </tr>
        ';
    }

    if ($jum_transaksi > 0) {
        $sql_get_transaksi = mysqli_query($conn, "SELECT SUM(subtotal) AS ttl_subtotal,ppn FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'");
        if ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
            $ttl_subtotal = $row_transaksi['ttl_subtotal'];
            $ppn_persen = $row_transaksi['ppn'];

            $ppn = $ttl_subtotal * $ppn_persen / 100;

            $hasil = $hasil . '
                <tr>
                    <td class="text-right" colspan="6">Total</td>
                    <td class="text-right" colspan="2">' . number_format($ttl_subtotal) . '</td>
                </tr>
                ';

            if ($_SESSION['jenis_pajak'] == 1) {
                $hasil = $hasil . '
                    <tr>
                        <td class="text-right" colspan="6">PPN <input type="number" name="" id="" class="text-right ppn_input" style="max-width:50px;" value="' . $ppn_persen . '"> %</td>
                        <td class="text-right" colspan="2">' . number_format($ppn) . '</td>
                    </tr>
                    ';
            }

            $hasil = $hasil . '
                <tr>
                    <td class="text-right" colspan="6">Grand Total</td>
                    <td class="text-right" colspan="2">' . number_format($ttl_subtotal + $ppn) . '</td>
                </tr>
            ';
        }
    }

    echo $hasil;
}

if (isset($_POST['id_ppn'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_ppn']);
    $ppn_input = mysqli_real_escape_string($conn, $_POST['ppn_input']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $sql_update_ppn = mysqli_query($conn, "
        UPDATE
            tb_barang_keluar
        SET
            ppn = '" . $ppn_input . "'
        WHERE
            id_transaksi = '" . $id_transaksi . "'
    ");

    $hasil = "";

    $sql_get_transaksi = mysqli_query($conn, "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' ORDER BY no_id ASC");
    $jum_transaksi = mysqli_num_rows($sql_get_transaksi);
    while ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
        $no_id = $row_transaksi['no_id'];
        $id_bahan = $row_transaksi['id_bahan'];
        $qty = $row_transaksi['qty'];
        $berat = $row_transaksi['berat'];
        $uom = $row_transaksi['uom'];
        $harga = $row_transaksi['harga'];
        $subtotal = $row_transaksi['subtotal'];

        $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'");
        $data_barang = mysqli_fetch_array($sql_get_barang);

        $hasil = $hasil . '
            <tr>
                <td class="text-center">' . $id_bahan . '</td>
                <td class="text-center">' . $data_barang['nama_bahan'] . '</td>
                <td class="text-center">' . $qty . '</td>
                <td class="text-center">' . $berat . '</td>
                <td class="text-center">' . $uom . '</td>
                <td class="text-center">' . number_format($harga) . '</td>
                <td class="text-center">' . number_format($subtotal) . '</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger del_barang" data-no_id="' . $no_id . '"><i class="fa fa-trash-o"></i></button>
                </td>
            </tr>
        ';
    }

    if ($jum_transaksi > 0) {
        $sql_get_transaksi = mysqli_query($conn, "SELECT SUM(subtotal) AS ttl_subtotal,ppn FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "'");
        if ($row_transaksi = mysqli_fetch_array($sql_get_transaksi)) {
            $ttl_subtotal = $row_transaksi['ttl_subtotal'];
            $ppn_persen = $row_transaksi['ppn'];

            $ppn = $ttl_subtotal * $ppn_persen / 100;

            $hasil = $hasil . '
                <tr>
                    <td class="text-right" colspan="6">Total</td>
                    <td class="text-right" colspan="2">' . number_format($ttl_subtotal) . '</td>
                </tr>
                ';

            if ($_SESSION['jenis_pajak'] == 1) {
                $hasil = $hasil . '
                    <tr>
                        <td class="text-right" colspan="6">PPN <input type="number" name="" id="" class="text-right ppn_input" style="max-width:50px;" value="' . $ppn_persen . '"> %</td>
                        <td class="text-right" colspan="2">' . number_format($ppn) . '</td>
                    </tr>
                    ';
            }

            $hasil = $hasil . '
                <tr>
                    <td class="text-right" colspan="6">Grand Total</td>
                    <td class="text-right" colspan="2">' . number_format($ttl_subtotal + $ppn) . '</td>
                </tr>
            ';
        }
    }

    echo $hasil;
}

if (isset($_POST['nama_customer_change'])) {
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer_change']);

    $id_customer = explode(" | ", $nama_customer);
    $id_customer = $id_customer[1];

    $select_customer = "SELECT * FROM tb_customer WHERE id_customer = '" . $id_customer . "'";
    $query_customer = mysqli_query($conn, $select_customer);
    $data_customer = mysqli_fetch_array($query_customer);

    $hp = $data_customer['no_telp'];
    $email = $data_customer['email'];
    $kota = $data_customer['kota'];
    $alamat = $data_customer['alamat_lengkap'];

    echo $hp . "|" . $email . "|" . $kota . "|" . $alamat;
}

if (isset($_POST['add_list'])) {
    $add_list = mysqli_real_escape_string($conn, $_POST['add_list']);
    $no_polisi = mysqli_real_escape_string($conn, $_POST['no_polisi']);
    $kode_garansi = mysqli_real_escape_string($conn, $_POST['kode_garansi']);
    $tipe_mobil = mysqli_real_escape_string($conn, $_POST['tipe_mobil']);
    $subtotal = mysqli_real_escape_string($conn, $_POST['subtotal']);
    $est_harga = mysqli_real_escape_string($conn, $_POST['est_harga']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    $qty_barang = mysqli_real_escape_string($conn, $_POST['qty_barang']);
    $nama_bahan = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $jenis_input = mysqli_real_escape_string($conn, $_POST['jenis_input']);
    $tipe_baru = mysqli_real_escape_string($conn, $_POST['tipe_baru']);

    if ($add_list == "new") {
        $id_transaksi = $_SESSION['id_user'];
    } else {
        $id_transaksi = $add_list;
    }

    $total_subtotal = $subtotal;
    // $filter = " AND no_polisi = 'Product'";
    // if($jenis_input !== "product"){
    //     $filter = " AND no_polisi != 'Product'";
    // }
    // $select_subtotal = "SELECT subtotal FROM tb_barang_keluar
    //                     WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' ".$filter." GROUP BY subtotal";
    // $query_subtotal = mysqli_query($conn, $select_subtotal);
    // $jum_subtotal = mysqli_num_rows($query_subtotal);

    // if ($jum_subtotal > 0) {
    //     while ($row_subtotal = mysqli_fetch_array($query_subtotal)) {
    //         $total_subtotal = $total_subtotal + $row_subtotal['subtotal'];
    //     }
    // }



    // $total_subtotal = $total_subtotal + $subtotal;

    if ($jenis_input == "kaca_film") {
        if ($tipe_baru < 1) {
            $id_tipe_mobil = $tipe_mobil;
        } else {
            $id_mobil = generate_tipe_mobil();

            $insert_mobil = mysqli_query($conn, "INSERT INTO tb_tipe_mobil(
                id_tipe,
                tipe_mobil
            ) VALUES(
                '" . $id_mobil . "',
                '" . $tipe_mobil . "'
            )");

            $id_tipe_mobil = $id_mobil;
        }


        $bagian_mobil = ["Kaca Depan", "Kaca SKKB", "Kaca Satuan"];

        foreach ($bagian_mobil as $mobil) {

            $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $id_tipe_mobil . "'";
            $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
            $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

            $butuh = 0;

            if ($mobil == "Kaca Depan") {
                $butuh = $data_tipe_mobil['kaca_depan'];
            }
            if ($mobil == "Kaca SKKB") {
                $butuh = $data_tipe_mobil['kaca_skkb'];
            }

            $hpp = 0;

            $total = ($butuh / $data_tipe_mobil['total'] * $subtotal);
            if ($butuh > 0) {
                $harga = round($total / $butuh);
            } else {
                $harga = 0;
            }

            $insert = "INSERT INTO tb_barang_keluar(
            id_transaksi,
            tgl_transaksi,
            no_polisi,
            kode_garansi,
            tipe_mobil,
            bagian_mobil,
            id_bahan,
            qty,
            uom,
            harga,
            hpp,
            subtotal,
            total,
            est_harga,
            id_cabang,
            dibuat_oleh,
            dibuat_tgl
        ) VALUES(
            '" . $id_transaksi . "',
            '" . date("Y-m-d") . "',
            '" . $no_polisi . "',
            '" . $kode_garansi . "',
            '" . $id_tipe_mobil . "',
            '" . $mobil . "',
            '',
            '" . $butuh . "',
            '',
            '" . $harga . "',
            '" . $hpp . "',
            '" . $total_subtotal . "',
            '" . $total . "',
            '" . $est_harga . "',
            '" . $cabang . "',
            '" . $_SESSION['id_user'] . "',
            '" . date("Y-m-d") . "'
        )";
            $query_insert = mysqli_query($conn, $insert);
        }
    } else if ($jenis_input == "kaca_satuan") {
        if ($tipe_baru < 1) {
            $id_tipe_mobil = $tipe_mobil;
        } else {
            $id_mobil = generate_tipe_mobil();

            $insert_mobil = mysqli_query($conn, "INSERT INTO tb_tipe_mobil(
                id_tipe,
                tipe_mobil
            ) VALUES(
                '" . $id_mobil . "',
                '" . $tipe_mobil . "'
            )");

            $id_tipe_mobil = $id_mobil;
        }

        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $id_tipe_mobil . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        $mobil = "Kaca Satuan";
        $butuh = 0;


        // $select_hpp = "SELECT hpp FROM tb_stock_cabang WHERE id_bahan = '" . $id_bahan . "' AND id_cabang = '" . $cabang . "'";
        // $query_hpp = mysqli_query($conn, $select_hpp);
        // $jum_hpp = mysqli_num_rows($query_hpp);
        // if ($jum_hpp > 0) {
        //     $data_hpp = mysqli_fetch_array($query_hpp);
        //     $hpp = $data_hpp['hpp'];
        // } else {
        //     $hpp = 0;
        // }

        $hpp = 0;

        $total = $butuh / $data_tipe_mobil['total'] * $subtotal;
        if ($butuh > 0) {
            $harga = round($total / $butuh);
        } else {
            $harga = 0;
        }

        $insert = "INSERT INTO tb_barang_keluar(
            id_transaksi,
            tgl_transaksi,
            no_polisi,
            kode_garansi,
            tipe_mobil,
            bagian_mobil,
            id_bahan,
            qty,
            uom,
            harga,
            hpp,
            subtotal,
            total,
            est_harga,
            id_cabang,
            dibuat_oleh,
            dibuat_tgl
        ) VALUES(
            '" . $id_transaksi . "',
            '" . date("Y-m-d") . "',
            '" . $no_polisi . "',
            '" . $kode_garansi . "',
            '" . $id_tipe_mobil . "',
            '" . $mobil . "',
            '',
            '" . $butuh . "',
            '',
            '" . $harga . "',
            '" . $hpp . "',
            '" . $subtotal . "',
            '" . $total . "',
            '" . $est_harga . "',
            '" . $cabang . "',
            '" . $_SESSION['id_user'] . "',
            '" . date("Y-m-d") . "'
        )";

        $query_insert = mysqli_query($conn, $insert);
    } else {

        $id_bahan = explode(" | ", $nama_bahan);
        $id_bahan = $id_bahan[1];

        $select_bahan = "SELECT * FROM tb_bahan
                        WHERE id_bahan = '" . $id_bahan . "'";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $data_bahan = mysqli_fetch_array($query_bahan);

        $total_qty = 0;

        $select_total_qty = "SELECT qty FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'";
        $query_total_qty = mysqli_query($conn, $select_total_qty);
        while ($row_total_qty = mysqli_fetch_array($query_total_qty)) {
            $total_qty = $total_qty + $row_total_qty['qty'];
        }

        $subtotal_produk = 0;

        $select_hpp = "
            SELECT
                hpp
            FROM
                tb_stock_cabang
            WHERE
                id_bahan = '" . $id_bahan . "' AND
                id_cabang = '" . $cabang . "'
        ";
        $query_hpp = mysqli_query($conn, $select_hpp);
        $data_hpp = mysqli_fetch_array($query_hpp);
        $hpp = $data_hpp['hpp'];

        $harga = $hpp;

        $total = $hpp * $qty_barang;

        $insert = "INSERT INTO tb_barang_keluar(
            id_transaksi,
            tgl_transaksi,
            no_polisi,
            kode_garansi,
            tipe_mobil,
            bagian_mobil,
            id_bahan,
            qty,
            uom,
            harga,
            hpp,
            subtotal,
            total,
            est_harga,
            id_cabang,
            dibuat_oleh,
            dibuat_tgl
        ) VALUES(
            '" . $id_transaksi . "',
            '" . date("Y-m-d") . "',
            'Product',
            '-',
            '-',
            '-',
            '" . $id_bahan . "',
            '" . $qty_barang . "',
            '" . $data_bahan['uom'] . "',
            '" . $hpp . "',
            '" . $hpp . "',
            '" . $total . "',
            '" . $total . "',
            '" . $total . "',
            '" . $cabang . "',
            '" . $_SESSION['id_user'] . "',
            '" . date("Y-m-d") . "'
        )";
        $query_insert = mysqli_query($conn, $insert);
    }

    // $update_subtotal = mysqli_query($conn, "UPDATE tb_barang_keluar SET 
    //                                         subtotal = '" . $total_subtotal . "'
    //                                         WHERE id_transaksi = '" . $id_transaksi     . "' AND id_cabang = '" . $cabang . "'");

    // $total_qty = 0;

    // $select_total_qty = "SELECT qty FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'";
    // $query_total_qty = mysqli_query($conn, $select_total_qty);
    // while ($row_total_qty = mysqli_fetch_array($query_total_qty)) {
    //     $total_qty = $total_qty + $row_total_qty['qty'];
    // }



    // $select_transaksi = "SELECT * FROM tb_barang_keluar
    //                         WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'";
    // $query_transaksi = mysqli_query($conn, $select_transaksi);
    // while ($row_transaksi = mysqli_fetch_array($query_transaksi)) {
    //     $total = $row_transaksi['qty'] / $total_qty * $row_transaksi['subtotal'];
    //     if ($row_transaksi['qty'] > 0) {
    //         $harga = round($total / $row_transaksi['qty']);
    //     } else {
    //         $harga = 0;
    //     }

    //     $update = mysqli_query($conn, "UPDATE tb_barang_keluar SET
    //                                 harga = '" . $harga . "',
    //                                 total = '" . $total . "' WHERE no_id = '" . $row_transaksi['no_id'] . "'");
    // }

    // $plus_subtotal = 0;
    // $select_transaksi = "SELECT SUM() FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '".$cabang."'";
    // $query_transaksi = mysqli_query($conn, $select_transaksi);
    // $jum_transaksi = mysqli_fetch_array($query_transaksi);
    // if ($jum_transaksi > 0) {
    //     $data_transaksi = mysqli_fetch_array($query_transaksi);
    //     $plus_subtotal = $subtotal + $data_transaksi['subtotal'];

    //     $update_subtotal = mysqli_query($conn, "UPDATE tb_barang_keluar SET 
    //             subtotal = '" . $plus_subtotal . "' WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '".$cabang."'");
    // }

    $total = 0;
    $hasil = "";

    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY no_polisi";
    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);
    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {


        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_barang_keluar['tipe_mobil'] . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $data_bahan = mysqli_fetch_array($query_bahan);

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi = '" . $row_barang_keluar['no_polisi'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $jum_keluar = mysqli_num_rows($query_keluar);

        $hasil = $hasil . '<tr>';
        if ($row_barang_keluar['no_polisi'] !== "Product") {
            $hasil = $hasil . '
           <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '" data-no_id="' . $row_barang_keluar['no_id'] . '"><i class="fa fa-trash-o"></i> </a> ' . $row_barang_keluar['no_polisi'] . ' ' . $row_barang_keluar['kode_garansi'] . ' ' . $data_tipe_mobil['tipe_mobil'] . ' <br><a href="javascript:void(0);" class="text-success input_satuan" 
                                                data-no_polisi = "' . $row_barang_keluar['no_polisi'] . '"
                                                data-kode_garansi = "' . $row_barang_keluar['kode_garansi'] . '"
                                                data-tipe_mobil = "' . $row_barang_keluar['tipe_mobil'] . '"
                                                
                                                ><i class="fa fa-list"></i> Kaca Satuan</a></td>
    ';
        } else {
            $hasil = $hasil . '
            <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '"><i class="fa fa-trash-o"></i> </a> Product </td>
    ';
        }
        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
            $hasil = $hasil . '
                    
            <td><a href="javascript:void(0);" class="text-danger del_item del_item_' . $row_keluar['no_id'] . '" data-nopol="' . $row_keluar['no_polisi'] . '" data-no_id="' . $row_keluar['no_id'] . '" ><i class="fa fa-trash-o"></i> </a>
                                                        <select name="bahan_ubah" class="bahan_ubah bahan_ubah_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '">
                                                        <option value="">-- Pilih Bahan --</option>
                                                            ';

            if ($row_barang_keluar['no_polisi'] == "Product") {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = 'Product' ORDER BY nama_bahan ASC";
            } else {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan != 'Product' ORDER BY nama_bahan ASC";
            }
            $query_bahan = mysqli_query($conn, $select_bahan);
            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                $selected = "";
                if ($row_bahan['id_bahan'] == $row_keluar['id_bahan']) {
                    $selected = "selected";
                }

                $hasil = $hasil . '<option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>';
            }

            $hasil = $hasil . '
                                                        </select>
                                                    </td>
            <td>' . $row_keluar['bagian_mobil'] . '</td>
           <td><input type="number" name="" class="qty_field qty_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-width:50px;max-height:21px;" min="0" value="' . $row_keluar['qty'] . '" autocomplete="off" onclick="this.select();"></td>
            <td class="text-center">' . $row_keluar['uom'] . '</td>
            ';

            if ($row_keluar['no_polisi'] == "Product") {
                $hasil = $hasil . '<td class="text-right">
                    <input type="text" name="" class="text-right harga_product harga_product_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" value="' . number_format($row_keluar['harga']) . '">
                    <input type="hidden" name="" class="harga_product_hidden_' . $row_keluar['no_id'] . '" value="' . $row_keluar['harga'] . '">
                </td>';
            } else {
                $hasil = $hasil . '<td class="text-right">' . number_format($row_keluar['harga']) . '</td>';
            }

            $hasil = $hasil . '
            <td class="text-right">' . number_format($row_keluar['total']) . '</td>
            </tr>
                ';
        }
    }

    if ($jum_barang_keluar > 0) {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY id_transaksi";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $select_total_keluar = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
        $query_total_keluar = mysqli_query($conn, $select_total_keluar);
        $data_total_keluar = mysqli_fetch_array($query_total_keluar);

        $total_keluar = $data_total_keluar['total'];

        $select_total_product = "
            SELECT SUM(total) AS total_product
            FROM
                tb_barang_keluar
            WHERE
                id_transaksi = '" . $id_transaksi . "' AND
                id_cabang = '" . $cabang . "' AND
                no_polisi = 'Product'
        ";
        $query_total_product = mysqli_query($conn, $select_total_product);
        $data_total_product = mysqli_fetch_array($query_total_product);

        $total_keluar_product = $data_total_product['total_product'];
        $ppn = $data_keluar['ppn'] * ($total_keluar + $total_keluar_product) / 100;


        $hasil = $hasil . '
    <tr>
        <th colspan="6" class="text-right">Subtotal Product</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar_product) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Subtotal Kaca Film</th>
        <td colspan="2"><input type="text" class="w-100 text-right change_subtotal" name="change_subtotal" id="" min="0" value="' . number_format($total_keluar) . '">
        <input type="hidden" name="" class="change_subtotal_hidden"></td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">PPN <input type="number" class="text-right ppn_input" style="max-width:50px;max-height:21px;" min="0" max="100" value="' . $data_keluar['ppn'] . '" autocomplete="off" onclick="this.select();"> %</th>
        <td colspan="2" class="text-right">' . number_format($ppn) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Grand Total</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar + $total_keluar_product + $ppn) . '</td>
    </tr>
                    ';
    }
    echo $hasil;
}

if (isset($_POST['id_ppn'])) {
    $id_ppn = mysqli_real_escape_string($conn, $_POST['id_ppn']);
    $ppn = mysqli_real_escape_string($conn, $_POST['ppn']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);

    if ($id_ppn == "new") {
        $id_transaksi = $_SESSION['id_user'];
    } else {
        $id_transaksi = $id_ppn;
    }

    $update = "UPDATE tb_barang_keluar SET ppn = '" . $ppn . "' WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'";
    $query_update = mysqli_query($conn, $update);

    $hasil = "";

    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY no_polisi";
    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);
    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {


        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_barang_keluar['tipe_mobil'] . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $data_bahan = mysqli_fetch_array($query_bahan);

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi = '" . $row_barang_keluar['no_polisi'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $jum_keluar = mysqli_num_rows($query_keluar);

        $hasil = $hasil . '<tr>';
        if ($row_barang_keluar['no_polisi'] !== "Product") {
            $hasil = $hasil . '
           <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '" data-no_id="' . $row_barang_keluar['no_id'] . '"><i class="fa fa-trash-o"></i> </a> ' . $row_barang_keluar['no_polisi'] . ' ' . $row_barang_keluar['kode_garansi'] . ' ' . $data_tipe_mobil['tipe_mobil'] . ' <br><a href="javascript:void(0);" class="text-success input_satuan" 
                                                data-no_polisi = "' . $row_barang_keluar['no_polisi'] . '"
                                                data-kode_garansi = "' . $row_barang_keluar['kode_garansi'] . '"
                                                data-tipe_mobil = "' . $row_barang_keluar['tipe_mobil'] . '"
                                                
                                                ><i class="fa fa-list"></i> Kaca Satuan</a></td>
    ';
        } else {
            $hasil = $hasil . '
            <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '"><i class="fa fa-trash-o"></i> </a> Product </td>
    ';
        }

        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
            $hasil = $hasil . '
                    
            <td><a href="javascript:void(0);" class="text-danger del_item del_item_' . $row_keluar['no_id'] . '" data-nopol="' . $row_keluar['no_polisi'] . '" data-no_id="' . $row_keluar['no_id'] . '" ><i class="fa fa-trash-o"></i> </a>
                                                        <select name="bahan_ubah" class="bahan_ubah bahan_ubah_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '">
                                                            <option value="">-- Pilih Bahan --</option>
                                                            ';

            if ($row_keluar['no_polisi'] !== "Product") {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan != 'Product' ORDER BY nama_bahan ASC";
            } else {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = 'Product' ORDER BY nama_bahan ASC";
            }
            $query_bahan = mysqli_query($conn, $select_bahan);
            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                $selected = "";
                if ($row_bahan['id_bahan'] == $row_keluar['id_bahan']) {
                    $selected = "selected";
                }

                $hasil = $hasil . '<option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>';
            }

            $hasil = $hasil . '
                                                        </select>
                                                    </td>
            <td>' . $row_keluar['bagian_mobil'] . '</td>
           <td><input type="number" name="" class="qty_field qty_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-width:50px;max-height:21px;" min="0" value="' . $row_keluar['qty'] . '" autocomplete="off" onclick="this.select();"></td>
            <td class="text-center">' . $row_keluar['uom'] . '</td>
            ';

            if ($row_keluar['no_polisi'] == "Product") {
                $hasil = $hasil . '<td class="text-right">
                    <input type="text" name="" class="text-right harga_product harga_product_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" value="' . number_format($row_keluar['harga']) . '">
                    <input type="hidden" name="" class="harga_product_hidden_' . $row_keluar['no_id'] . '" value="' . $row_keluar['harga'] . '">
                </td>';
            } else {
                $hasil = $hasil . '<td class="text-right">' . number_format($row_keluar['harga']) . '</td>';
            }

            $hasil = $hasil . '
            <td class="text-right">' . number_format($row_keluar['total']) . '</td>
            </tr>
                ';
        }
    }

    if ($jum_barang_keluar > 0) {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY id_transaksi";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $select_total_keluar = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
        $query_total_keluar = mysqli_query($conn, $select_total_keluar);
        $data_total_keluar = mysqli_fetch_array($query_total_keluar);

        $total_keluar = $data_total_keluar['total'];

        $select_total_product = "
            SELECT SUM(total) AS total_product
            FROM
                tb_barang_keluar
            WHERE
                id_transaksi = '" . $id_transaksi . "' AND
                id_cabang = '" . $cabang . "' AND
                no_polisi = 'Product'
        ";
        $query_total_product = mysqli_query($conn, $select_total_product);
        $data_total_product = mysqli_fetch_array($query_total_product);

        $total_keluar_product = $data_total_product['total_product'];
        $ppn = $data_keluar['ppn'] * ($total_keluar + $total_keluar_product) / 100;


        $hasil = $hasil . '
    <tr>
        <th colspan="6" class="text-right">Subtotal Product</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar_product) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Subtotal Kaca Film</th>
        <td colspan="2"><input type="text" class="w-100 text-right change_subtotal" name="change_subtotal" id="" min="0" value="' . number_format($total_keluar) . '">
        <input type="hidden" name="" class="change_subtotal_hidden"></td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">PPN <input type="number" class="text-right ppn_input" style="max-width:50px;max-height:21px;" min="0" max="100" value="' . $data_keluar['ppn'] . '" autocomplete="off" onclick="this.select();"> %</th>
        <td colspan="2" class="text-right">' . number_format($ppn) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Grand Total</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar + $total_keluar_product + $ppn) . '</td>
    </tr>
                    ';
    }
    echo $hasil;
}

if (isset($_POST['del_by_nopol'])) {
    $del_by_nopol = mysqli_real_escape_string($conn, $_POST['del_by_nopol']);
    $nopol = mysqli_real_escape_string($conn, $_POST['nopol']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);

    if ($del_by_nopol == "new") {
        $id_transaksi = $_SESSION['id_user'];
    } else {
        $id_transaksi = $del_by_nopol;
    }

    $delete = "DELETE FROM tb_barang_keluar WHERE id_transaksi = '" . $del_by_nopol . "' AND no_polisi = '" . $nopol . "'";
    $query_delete = mysqli_query($conn, $delete);

    // $total_qty = 0;
    // if($nopol !== "Product"){
    //     $filter = " AND no_polisi != 'Product'";
    // }else{
    //     $filter = " AND no_polisi = 'Product'";
    // }
    // $select_total_qty = "SELECT qty FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'".$filter;
    // $query_total_qty = mysqli_query($conn, $select_total_qty);
    // while ($row_total_qty = mysqli_fetch_array($query_total_qty)) {
    //     $total_qty = $total_qty + $row_total_qty['qty'];
    // }

    // $select_transaksi = "SELECT * FROM tb_barang_keluar
    //                     WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'".$filter;
    // $query_transaksi = mysqli_query($conn, $select_transaksi);
    // while ($row_transaksi = mysqli_fetch_array($query_transaksi)) {
    //     $total = $row_transaksi['qty'] / $total_qty * $row_transaksi['subtotal'];
    //     if ($row_transaksi['qty'] > 0) {
    //         $harga = round($total / $row_transaksi['qty']);
    //     } else {
    //         $harga = 0;
    //     }

    //     $update = mysqli_query($conn, "UPDATE tb_barang_keluar SET
    //                             harga = '" . $harga . "',
    //                             total = '" . $total . "' WHERE no_id = '" . $row_transaksi['no_id'] . "'");
    // }

    $hasil = "";

    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY no_polisi";
    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);
    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {


        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_barang_keluar['tipe_mobil'] . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        // $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
        // $query_bahan = mysqli_query($conn, $select_bahan);
        // $data_bahan = mysqli_fetch_array($query_bahan);

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi = '" . $row_barang_keluar['no_polisi'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $jum_keluar = mysqli_num_rows($query_keluar);

        $hasil = $hasil . '<tr>';
        if ($row_barang_keluar['no_polisi'] !== "Product") {
            $hasil = $hasil . '
           <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '" data-no_id="' . $row_barang_keluar['no_id'] . '"><i class="fa fa-trash-o"></i> </a> ' . $row_barang_keluar['no_polisi'] . ' ' . $row_barang_keluar['kode_garansi'] . ' ' . $data_tipe_mobil['tipe_mobil'] . ' <br><a href="javascript:void(0);" class="text-success input_satuan" 
                                                data-no_polisi = "' . $row_barang_keluar['no_polisi'] . '"
                                                data-kode_garansi = "' . $row_barang_keluar['kode_garansi'] . '"
                                                data-tipe_mobil = "' . $row_barang_keluar['tipe_mobil'] . '"
                                                
                                                ><i class="fa fa-list"></i> Kaca Satuan</a></td>
    ';
        } else {
            $hasil = $hasil . '
            <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '"><i class="fa fa-trash-o"></i> </a> Product </td>
    ';
        }

        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
            $hasil = $hasil . '
                    
            <td><a href="javascript:void(0);" class="text-danger del_item del_item_' . $row_keluar['no_id'] . '" data-nopol="' . $row_keluar['no_polisi'] . '" data-no_id="' . $row_keluar['no_id'] . '" ><i class="fa fa-trash-o"></i> </a>
                                                        <select name="bahan_ubah" class="bahan_ubah bahan_ubah_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '">
                                                            <option value="">-- Pilih Bahan --</option>
                                                            ';

            if ($row_keluar['no_polisi'] !== "Product") {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan != 'Product' ORDER BY nama_bahan ASC";
            } else {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = 'Product' ORDER BY nama_bahan ASC";
            }
            $query_bahan = mysqli_query($conn, $select_bahan);
            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                $selected = "";
                if ($row_bahan['id_bahan'] == $row_keluar['id_bahan']) {
                    $selected = "selected";
                }

                $hasil = $hasil . '<option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>';
            }

            $hasil = $hasil . '
                                                        </select>
                                                    </td>
            <td>' . $row_keluar['bagian_mobil'] . '</td>
           <td><input type="number" name="" class="qty_field qty_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-width:50px;max-height:21px;" min="0" value="' . $row_keluar['qty'] . '" autocomplete="off" onclick="this.select();"></td>
            <td class="text-center">' . $row_keluar['uom'] . '</td>
            ';

            if ($row_keluar['no_polisi'] == "Product") {
                $hasil = $hasil . '<td class="text-right">
                    <input type="text" name="" class="text-right harga_product harga_product_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" value="' . number_format($row_keluar['harga']) . '">
                    <input type="hidden" name="" class="harga_product_hidden_' . $row_keluar['no_id'] . '" value="' . $row_keluar['harga'] . '">
                </td>';
            } else {
                $hasil = $hasil . '<td class="text-right">' . number_format($row_keluar['harga']) . '</td>';
            }

            $hasil = $hasil . '
            <td class="text-right">' . number_format($row_keluar['total']) . '</td>
            </tr>
                ';
        }
    }

    if ($jum_barang_keluar > 0) {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY id_transaksi";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $select_total_keluar = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
        $query_total_keluar = mysqli_query($conn, $select_total_keluar);
        $data_total_keluar = mysqli_fetch_array($query_total_keluar);

        $total_keluar = $data_total_keluar['total'];

        $select_total_product = "
            SELECT SUM(total) AS total_product
            FROM
                tb_barang_keluar
            WHERE
                id_transaksi = '" . $id_transaksi . "' AND 
                id_cabang = '" . $cabang . "' AND 
                no_polisi = 'Product'
        ";
        $query_total_product = mysqli_query($conn, $select_total_product);
        $data_total_product = mysqli_fetch_array($query_total_product);

        $total_product = $data_total_product['total_product'];
        $ppn = $data_keluar['ppn'] * ($total_keluar + $total_product) / 100;


        $hasil = $hasil . '
    <tr>
        <th colspan="6" class="text-right">Subtotal Product</th>
        <td colspan="2" class="text-right">' . number_format($total_product) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Subtotal Kaca Film</th>
        <td colspan="2"><input type="text" class="w-100 text-right change_subtotal" name="change_subtotal" id="" min="0" value="' . $total_keluar . '">
        <input type="hidden" name="" class="change_subtotal_hidden"></td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">PPN <input type="number" class="text-right ppn_input" style="max-width:50px;max-height:21px;" min="0" max="100" value="' . $data_keluar['ppn'] . '" autocomplete="off" onclick="this.select();"> %</th>
        <td colspan="2" class="text-right">' . number_format($ppn) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Grand Total</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar + $total_product + $ppn) . '</td>
    </tr>
                    ';
    }
    echo $hasil;
}

if (isset($_POST['del_item'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['del_item']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    $no_polisi = mysqli_real_escape_string($conn, $_POST['nopol']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $select_subtotal = "
        SELECT 
            SUM(subtotal) AS subtotal
        FROM
            tb_barang_keluar
        WHERE
            id_transaksi = '" . $id_transaksi . "' AND
            no_polisi = '" . $no_polisi . "'
    ";
    $query_subtotal = mysqli_query($conn, $select_subtotal);
    $data_subtotal = mysqli_fetch_array($query_subtotal);

    $subtotal = $data_subtotal['subtotal'];

    $delete = "DELETE FROM tb_barang_keluar WHERE no_id = '" . $no_id . "'";
    $query_delete = mysqli_query($conn, $delete);

    $total_qty = 0;
    if ($no_polisi !== "Product") {
        $filter = " AND no_polisi = '" . $no_polisi . "'";
        $select_total_qty = "SELECT qty FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'" . $filter;
        $query_total_qty = mysqli_query($conn, $select_total_qty);
        while ($row_total_qty = mysqli_fetch_array($query_total_qty)) {
            $total_qty = $total_qty + $row_total_qty['qty'];
        }

        $select_transaksi = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'" . $filter;
        $query_transaksi = mysqli_query($conn, $select_transaksi);
        while ($row_transaksi = mysqli_fetch_array($query_transaksi)) {

            $total = $row_transaksi['qty'] / $total_qty * $row_transaksi['subtotal'];
            if ($row_transaksi['qty'] > 0) {
                $harga = round($total / $row_transaksi['qty']);
            } else {
                $harga = 0;
            }

            $update = mysqli_query($conn, "UPDATE tb_barang_keluar SET 
                    harga = '" . $harga . "',
                    total = '" . $total . "' WHERE no_id = '" . $row_transaksi['no_id'] . "'
                ");
        }
    }



    $hasil = "";

    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'  GROUP BY no_polisi";
    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);
    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {


        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_barang_keluar['tipe_mobil'] . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        // $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
        // $query_bahan = mysqli_query($conn, $select_bahan);
        // $data_bahan = mysqli_fetch_array($query_bahan);

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi = '" . $row_barang_keluar['no_polisi'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $jum_keluar = mysqli_num_rows($query_keluar);

        $hasil = $hasil . '<tr>';
        if ($row_barang_keluar['no_polisi'] !== "Product") {
            $hasil = $hasil . '
           <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '" data-no_id="' . $row_barang_keluar['no_id'] . '"><i class="fa fa-trash-o"></i> </a> ' . $row_barang_keluar['no_polisi'] . ' ' . $row_barang_keluar['kode_garansi'] . ' ' . $data_tipe_mobil['tipe_mobil'] . ' <br><a href="javascript:void(0);" class="text-success input_satuan" 
                                                data-no_polisi = "' . $row_barang_keluar['no_polisi'] . '"
                                                data-kode_garansi = "' . $row_barang_keluar['kode_garansi'] . '"
                                                data-tipe_mobil = "' . $row_barang_keluar['tipe_mobil'] . '"
                                                
                                                ><i class="fa fa-list"></i> Kaca Satuan</a></td>
    ';
        } else {
            $hasil = $hasil . '
            <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '"><i class="fa fa-trash-o"></i> </a> Product </td>
    ';
        }

        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
            $hasil = $hasil . '
                    
            <td><a href="javascript:void(0);" class="text-danger del_item del_item_' . $row_keluar['no_id'] . '" data-nopol="' . $row_keluar['no_polisi'] . '" data-no_id="' . $row_keluar['no_id'] . '" ><i class="fa fa-trash-o"></i> </a>
                                                        <select name="bahan_ubah" class="bahan_ubah bahan_ubah_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '">
                                                            <option value="">-- Pilih Bahan --</option>
                                                            ';

            if ($row_keluar['no_polisi'] !== "Product") {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan != 'Product' ORDER BY nama_bahan ASC";
            } else {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = 'Product' ORDER BY nama_bahan ASC";
            }
            $query_bahan = mysqli_query($conn, $select_bahan);
            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                $selected = "";
                if ($row_bahan['id_bahan'] == $row_keluar['id_bahan']) {
                    $selected = "selected";
                }

                $hasil = $hasil . '<option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>';
            }

            $hasil = $hasil . '
                                                        </select>
                                                    </td>
            <td>' . $row_keluar['bagian_mobil'] . '</td>
           <td><input type="number" name="" class="qty_field qty_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-width:50px;max-height:21px;" min="0" value="' . $row_keluar['qty'] . '" autocomplete="off" onclick="this.select();"></td>
            <td class="text-center">' . $row_keluar['uom'] . '</td>
            ';

            if ($row_keluar['no_polisi'] == "Product") {
                $hasil = $hasil . '<td class="text-right">
                    <input type="text" name="" class="text-right harga_product harga_product_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" value="' . number_format($row_keluar['harga']) . '">
                    <input type="hidden" name="" class="harga_product_hidden_' . $row_keluar['no_id'] . '" value="' . $row_keluar['harga'] . '">
                </td>';
            } else {
                $hasil = $hasil . '<td class="text-right">' . number_format($row_keluar['harga']) . '</td>';
            }

            $hasil = $hasil . '
            <td class="text-right">' . number_format($row_keluar['total']) . '</td>
            </tr>
                ';
        }
    }

    if ($jum_barang_keluar > 0) {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY id_transaksi";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $select_total_keluar = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
        $query_total_keluar = mysqli_query($conn, $select_total_keluar);
        $data_total_keluar = mysqli_fetch_array($query_total_keluar);

        $total_keluar = $data_total_keluar['total'];

        $select_total_product = "
            SELECT SUM(total) AS total_product
            FROM
                tb_barang_keluar
            WHERE
                id_transaksi = '" . $id_transaksi . "' AND 
                id_cabang = '" . $cabang . "' AND 
                no_polisi = 'Product'
        ";
        $query_total_product = mysqli_query($conn, $select_total_product);
        $data_total_product = mysqli_fetch_array($query_total_product);

        $total_product = $data_total_product['total_product'];
        $ppn = $data_keluar['ppn'] * ($total_keluar + $total_product) / 100;


        $hasil = $hasil . '
    <tr>
        <th colspan="6" class="text-right">Subtotal Product</th>
        <td colspan="2" class="text-right">' . number_format($total_product) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Subtotal Kaca Film</th>
        <td colspan="2"><input type="text" class="w-100 text-right change_subtotal" name="change_subtotal" id="" min="0" value="' . $total_keluar . '">
        <input type="hidden" name="" class="change_subtotal_hidden"></td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">PPN <input type="number" class="text-right ppn_input" style="max-width:50px;max-height:21px;" min="0" max="100" value="' . $data_keluar['ppn'] . '" autocomplete="off" onclick="this.select();"> %</th>
        <td colspan="2" class="text-right">' . number_format($ppn) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Grand Total</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar + $total_product + $ppn) . '</td>
    </tr>
                    ';
    }
    echo $hasil;
}

if (isset($_POST['id_change_qty'])) {
    $id_change_qty = mysqli_real_escape_string($conn, $_POST['id_change_qty']);
    $qty = mysqli_real_escape_string($conn, $_POST['qty']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $select_keluar = "SELECT * FROM tb_barang_keluar WHERE no_id = '" . $id_change_qty . "'";
    $query_keluar = mysqli_query($conn, $select_keluar);
    $data_keluar = mysqli_fetch_array($query_keluar);

    $update = "UPDATE tb_barang_keluar SET 
    qty = '" . $qty . "' WHERE no_id = '" . $id_change_qty . "'";
    $query_update = mysqli_query($conn, $update);


    if ($data_keluar['no_polisi'] !== "Product") {
        $total_qty = 0;
        $filter = " AND no_polisi = '" . $data_keluar['no_polisi'] . "'";
        $select_total_qty = "SELECT qty FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'" . $filter;
        $query_total_qty = mysqli_query($conn, $select_total_qty);
        while ($row_total_qty = mysqli_fetch_array($query_total_qty)) {
            $total_qty = $total_qty + $row_total_qty['qty'];
        }

        $select_sub = "SELECT no_id,qty,subtotal FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND subtotal > 0 " . $filter;
        $query_sub = mysqli_query($conn, $select_sub);
        $data_sub = mysqli_fetch_array($query_sub);

        $subtotal = $data_sub['subtotal'];

        $select_subtotal = "SELECT no_id,qty,subtotal FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'" . $filter;
        $query_subtotal = mysqli_query($conn, $select_subtotal);
        while ($row_subtotal = mysqli_fetch_array($query_subtotal)) {
            if ($row_subtotal['subtotal'] > 0) {
                $subtotal = $row_subtotal['subtotal'];
            }
            $total = $row_subtotal['qty'] / $total_qty * $subtotal;
            $harga = $total / $row_subtotal['qty'];

            $update_harga = "UPDATE tb_barang_keluar SET
                harga = '" . $harga . "',
                total = '" . $total . "' WHERE no_id = '" . $row_subtotal['no_id'] . "'
            ";
            $query_update = mysqli_query($conn, $update_harga);
        }
    } else {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE no_id = '" . $id_change_qty . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $harga = $data_keluar['hpp'];
        $total = $harga * $data_keluar['qty'];

        $update = mysqli_query($conn, "
            UPDATE
                tb_barang_keluar
            SET
                total = '" . $total . "'
            WHERE
                no_id = '" . $id_change_qty . "'
        ");
    }



    $hasil = "";

    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY no_polisi";
    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);
    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {


        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_barang_keluar['tipe_mobil'] . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $data_bahan = mysqli_fetch_array($query_bahan);

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi = '" . $row_barang_keluar['no_polisi'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $jum_keluar = mysqli_num_rows($query_keluar);

        $hasil = $hasil . '<tr>';
        if ($row_barang_keluar['no_polisi'] !== "Product") {
            $hasil = $hasil . '
           <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '" data-no_id="' . $row_barang_keluar['no_id'] . '"><i class="fa fa-trash-o"></i> </a> ' . $row_barang_keluar['no_polisi'] . ' ' . $row_barang_keluar['kode_garansi'] . ' ' . $data_tipe_mobil['tipe_mobil'] . ' <br><a href="javascript:void(0);" class="text-success input_satuan" 
                                                data-no_polisi = "' . $row_barang_keluar['no_polisi'] . '"
                                                data-kode_garansi = "' . $row_barang_keluar['kode_garansi'] . '"
                                                data-tipe_mobil = "' . $row_barang_keluar['tipe_mobil'] . '"
                                                
                                                ><i class="fa fa-list"></i> Kaca Satuan</a></td>
    ';
        } else {
            $hasil = $hasil . '
            <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '"><i class="fa fa-trash-o"></i> </a> Product </td>
    ';
        }

        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
            $hasil = $hasil . '
                    
            <td><a href="javascript:void(0);" class="text-danger del_item del_item_' . $row_keluar['no_id'] . '" data-nopol="' . $row_keluar['no_polisi'] . '" data-no_id="' . $row_keluar['no_id'] . '" ><i class="fa fa-trash-o"></i> </a>
                                                        <select name="bahan_ubah" class="bahan_ubah bahan_ubah_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '">
                                                        <option value="">-- Pilih Bahan --</option>
                                                            ';

            if ($row_keluar['no_polisi'] !== "Product") {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan != 'Product' ORDER BY nama_bahan ASC";
            } else {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = 'Product' ORDER BY nama_bahan ASC";
            }
            $query_bahan = mysqli_query($conn, $select_bahan);
            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                $selected = "";
                if ($row_bahan['id_bahan'] == $row_keluar['id_bahan']) {
                    $selected = "selected";
                }

                $hasil = $hasil . '<option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>';
            }

            $hasil = $hasil . '
                                                        </select>
                                                    </td>
            <td>' . $row_keluar['bagian_mobil'] . '</td>
           <td><input type="number" name="" class="qty_field qty_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-width:50px;max-height:21px;" min="0" value="' . $row_keluar['qty'] . '" autocomplete="off" onclick="this.select();"></td>
            <td class="text-center">' . $row_keluar['uom'] . '</td>
            ';

            if ($row_keluar['no_polisi'] == "Product") {
                $hasil = $hasil . '<td class="text-right">
                    <input type="text" name="" class="text-right harga_product harga_product_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" value="' . number_format($row_keluar['harga']) . '">
                    <input type="hidden" name="" class="harga_product_hidden_' . $row_keluar['no_id'] . '" value="' . $row_keluar['harga'] . '">
                </td>';
            } else {
                $hasil = $hasil . '<td class="text-right">' . number_format($row_keluar['harga']) . '</td>';
            }

            $hasil = $hasil . '
            <td class="text-right">' . number_format($row_keluar['total']) . '</td>
            </tr>
                ';
        }
    }

    if ($jum_barang_keluar > 0) {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY id_transaksi";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $select_total_keluar = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
        $query_total_keluar = mysqli_query($conn, $select_total_keluar);
        $data_total_keluar = mysqli_fetch_array($query_total_keluar);

        $total_keluar = $data_total_keluar['total'];

        $select_total_product = "
            SELECT SUM(total) AS total_product
            FROM
                tb_barang_keluar
            WHERE
                id_transaksi = '" . $id_transaksi . "' AND 
                id_cabang = '" . $cabang . "' AND 
                no_polisi = 'Product'
        ";
        $query_total_product = mysqli_query($conn, $select_total_product);
        $data_total_product = mysqli_fetch_array($query_total_product);

        $total_product = $data_total_product['total_product'];
        $ppn = $data_keluar['ppn'] * ($total_keluar + $total_product) / 100;


        $hasil = $hasil . '
    <tr>
        <th colspan="6" class="text-right">Subtotal Product</th>
        <td colspan="2" class="text-right">' . number_format($total_product) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Subtotal Kaca Film</th>
        <td colspan="2"><input type="text" class="w-100 text-right change_subtotal" name="change_subtotal" id="" min="0" value="' . number_format($total_keluar) . '">
        <input type="hidden" name="" class="change_subtotal_hidden"></td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">PPN <input type="number" class="text-right ppn_input" style="max-width:50px;max-height:21px;" min="0" max="100" value="' . $data_keluar['ppn'] . '" autocomplete="off" onclick="this.select();"> %</th>
        <td colspan="2" class="text-right">' . number_format($ppn) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Grand Total</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar + $total_product + $ppn) . '</td>
    </tr>
                    ';
    }
    echo $hasil;
}

if (isset($_POST['id_change_harga'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $id_change_harga = mysqli_real_escape_string($conn, $_POST['id_change_harga']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $select_keluar = "SELECT * FROM tb_barang_keluar WHERE no_id = '" . $id_change_harga . "'";
    $query_keluar = mysqli_query($conn, $select_keluar);
    $data_keluar = mysqli_fetch_array($query_keluar);

    $grand_total = $data_keluar['qty'] * $harga;

    $update = "UPDATE tb_barang_keluar SET
        harga = '" . $harga . "',
        total = '" . $grand_total . "' WHERE no_id = '" . $id_change_harga . "'
    ";
    $query_update = mysqli_query($conn, $update);

    $hasil = "";

    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY no_polisi";
    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);
    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {


        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_barang_keluar['tipe_mobil'] . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $data_bahan = mysqli_fetch_array($query_bahan);

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi = '" . $row_barang_keluar['no_polisi'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $jum_keluar = mysqli_num_rows($query_keluar);

        $hasil = $hasil . '<tr>';
        if ($row_barang_keluar['no_polisi'] !== "Product") {
            $hasil = $hasil . '
           <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '" data-no_id="' . $row_barang_keluar['no_id'] . '"><i class="fa fa-trash-o"></i> </a> ' . $row_barang_keluar['no_polisi'] . ' ' . $row_barang_keluar['kode_garansi'] . ' ' . $data_tipe_mobil['tipe_mobil'] . ' <br><a href="javascript:void(0);" class="text-success input_satuan" 
                                                data-no_polisi = "' . $row_barang_keluar['no_polisi'] . '"
                                                data-kode_garansi = "' . $row_barang_keluar['kode_garansi'] . '"
                                                data-tipe_mobil = "' . $row_barang_keluar['tipe_mobil'] . '"
                                                
                                                ><i class="fa fa-list"></i> Kaca Satuan</a></td>
    ';
        } else {
            $hasil = $hasil . '
            <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '"><i class="fa fa-trash-o"></i> </a> Product </td>
    ';
        }

        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
            $hasil = $hasil . '
                    
            <td><a href="javascript:void(0);" class="text-danger del_item del_item_' . $row_keluar['no_id'] . '" data-nopol="' . $row_keluar['no_polisi'] . '" data-no_id="' . $row_keluar['no_id'] . '" ><i class="fa fa-trash-o"></i> </a>
                                                        <select name="bahan_ubah" class="bahan_ubah bahan_ubah_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '">
                                                            <option value="">-- Pilih Bahan --</option>
                                                            ';

            if ($row_barang_keluar['no_polisi'] == "Product") {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = 'Product' ORDER BY nama_bahan ASC";
            } else {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan != 'Product' ORDER BY nama_bahan ASC";
            }
            $query_bahan = mysqli_query($conn, $select_bahan);
            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                $selected = "";
                if ($row_bahan['id_bahan'] == $row_keluar['id_bahan']) {
                    $selected = "selected";
                }

                $hasil = $hasil . '<option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>';
            }

            $hasil = $hasil . '
                                                        </select>
                                                    </td>
            <td>' . $row_keluar['bagian_mobil'] . '</td>
           <td><input type="number" name="" class="qty_field qty_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-width:50px;max-height:21px;" min="0" value="' . $row_keluar['qty'] . '" autocomplete="off" onclick="this.select();"></td>
            <td class="text-center">' . $row_keluar['uom'] . '</td>
            <td><input type="number" name="" class="harga_field harga_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-height:21px;max-width:100px;" min="0" value="' . $row_keluar['harga'] . '"></td>
            <td>' . number_format($row_keluar['total']) . '</td>
            </tr>
                ';
        }
    }

    if ($jum_barang_keluar > 0) {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY id_transaksi";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $select_total_keluar = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'";
        $query_total_keluar = mysqli_query($conn, $select_total_keluar);
        $data_total_keluar = mysqli_fetch_array($query_total_keluar);

        $total_keluar = $data_total_keluar['total'];

        $ppn = $data_keluar['ppn'] * $total_keluar / 100;

        $hasil = $hasil . '
                        <tr>
        <th colspan="6" class="text-right">Subtotal</th>
        <td colspan="2"><input type="text" class="w-100 text-right change_subtotal" name="change_subtotal" id="" min="0" value="' . $total_keluar . '">
        <input type="hidden" name="" class="change_subtotal_hidden"></td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">PPN <input type="number" class="text-right ppn_input" style="max-width:50px;max-height:21px;" min="0" max="100" value="' . $data_keluar['ppn'] . '" autocomplete="off" onclick="this.select();"> %</th>
        <td colspan="2" class="text-right">' . number_format($ppn) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Grand Total</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar + $ppn) . '</td>
    </tr>
                    ';
    }
    echo $hasil;
}

if (isset($_POST['simpan'])) {
    $hasil = 0;
    $valid = 1;

    $id_cust = generate_customer();

    $simpan = mysqli_real_escape_string($conn, $_POST['simpan']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jenis_penjualan = mysqli_real_escape_string($conn, $_POST['jenis_penjualan']);
    $source_customer = mysqli_real_escape_string($conn, $_POST['source_customer']);

    if ($simpan == "new") {
        $id_trf = $_SESSION['id_user'];
    } else {
        $id_trf = $simpan;
    }

    $total_belanja = 0;
    $ppn = 0;
    $select_keluar_total = "SELECT total,ppn FROM tb_barang_keluar WHERE id_transaksi = '" . $id_trf . "'";
    $query_keluar_total = mysqli_query($conn, $select_keluar_total);
    while ($row_keluar_total = mysqli_fetch_array($query_keluar_total)) {
        $ppn = $row_keluar_total['ppn'];
        $total_belanja += $row_keluar_total['total'];
    }

    $total_ppn = $total_belanja * $ppn / 100;
    $total_belanja = $total_belanja + $total_ppn;

    if ($total_belanja <= 0) {
        $valid = 1;
    } else {
        $select_bayar = "SELECT * FROM tb_pembayaran WHERE id_pembayaran = '" . $id_trf . "'";
        $query_bayar = mysqli_query($conn, $select_bayar);
        $jum_bayar = mysqli_num_rows($query_bayar);

        if ($jum_bayar > 0) {
            $select_bayar = "SELECT SUM(nominal) AS total FROM tb_pembayaran WHERE id_pembayaran = '" . $id_trf . "'";
            $query_bayar = mysqli_query($conn, $select_bayar);
            $data_bayar = mysqli_fetch_array($query_bayar);

            $total_bayar = $data_bayar['total'];
            $total_beli = 0;
            $ppn = 0;

            $select_beli = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_trf . "'";
            $query_beli = mysqli_query($conn, $select_beli);
            while ($row_beli = mysqli_fetch_array($query_beli)) {
                $ppn = $row_beli['ppn'];
                $total_beli += $row_beli['total'];
            }

            $total_ppn = round($total_beli * $ppn / 100);
            $total_beli = round($total_beli + $total_ppn);

            if ($total_bayar < 1) {
                $valid = 1;
            } else {
                if ($total_bayar == $total_beli) {
                    $valid = 1;
                } else if ($total_bayar > $total_beli) {
                    $valid = 2;
                } else {
                    $valid = 3;
                }
            }
        } else {
            $valid = 0;
        }
    }

    if ($valid == 1) {
        if (strpos($nama_customer, " | ") == true) {
            $id_customer = explode(" | ", $nama_customer);
            $id_customer = $id_customer[1];
        } else {
            $insert_customer = "INSERT INTO tb_customer(
                id_customer,
                nama_customer,
                kota,
                alamat_lengkap,
                no_telp,
                email
            ) VALUES(
                '" . $id_cust . "',
                '" . $nama_customer . "',
                '" . $kota . "',
                '" . $alamat . "',
                '" . $no_telp . "',
                '" . $email . "'
            )";

            $query = mysqli_query($conn, $insert_customer);

            $id_customer = $id_cust;
        }

        if ($simpan == "new") {
            $id_keluar = generate_barang_keluar_key("BK" . $cabang, "BK" . $cabang, date("m"), date("y"));
            $id_transaksi = $_SESSION['id_user'];
            $update_id = "id_transaksi = '" . $id_keluar . "',";
        } else {
            $id_keluar = $simpan;
            $id_transaksi = $simpan;
            $update_id = "";
        }

        $select_transaksi = "SELECT * FROM tb_barang_keluar
                            WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'";
        $query_transaksi = mysqli_query($conn, $select_transaksi);
        while ($row_transaksi = mysqli_fetch_array($query_transaksi)) {
            if ($row_transaksi['id_bahan'] == "") {
                $hasil = 2;
            }
        }

        if ($hasil !== 2) {
            $update = "UPDATE tb_barang_keluar SET
           " . $update_id . "
            id_customer = '" . $id_customer . "',
            no_telp = '" . $no_telp . "',
            email = '" . $email . "',
            kota = '" . $kota . "',
            alamat = '" . $alamat . "',
            keterangan = '" . $keterangan . "',
            status_keluar = 'd',
            jenis = '" . $jenis_penjualan . "',
            source_customer='" . $source_customer . "' WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "';
        ";

            $query_update = mysqli_query($conn, $update);

            if ($query_update) {

                $update_bayar = "UPDATE tb_pembayaran SET
                id_pembayaran = '" . $id_keluar . "' WHERE id_pembayaran = '" . $id_trf . "'
            ";

                $query = mysqli_query($conn, $update_bayar);

                $hasil = 1;
            }
        }
    }

    echo $hasil . "|" . $valid . "|" . number_format($total_beli) . "|" . number_format($total_bayar);
}


if (isset($_POST['simpan_oto'])) {

    $valid = 0;
    $hasil = 0;

    $id_cust = generate_customer();

    $simpan_oto = mysqli_real_escape_string($conn, $_POST['simpan_oto']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    $nama_customer = mysqli_real_escape_string($conn, $_POST['nama_customer']);
    $no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $kota = mysqli_real_escape_string($conn, $_POST['kota']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jenis_penjualan = mysqli_real_escape_string($conn, $_POST['jenis_penjualan']);
    $source_customer = mysqli_real_escape_string($conn, $_POST['source_customer']);


    if ($simpan_oto == "new") {
        $id_trf = $_SESSION['id_user'];
    } else {
        $id_trf = $simpan_oto;
    }



    if ($simpan_oto == "new") {
        $id_keluar = generate_barang_keluar_key("BK" . $cabang, "BK" . $cabang, date("m"), date("y"));
        $id_transaksi = $_SESSION['id_user'];
        $update_id = "id_transaksi = '" . $id_keluar . "',";
    } else {
        $id_keluar = $simpan_oto;
        $id_transaksi = $simpan_oto;
        $update_id = "";
    }

    $total_belanja = 0;

    $select_keluar_total = "SELECT total,ppn FROM tb_barang_keluar WHERE id_transaksi = '" . $id_trf . "'";
    $query_keluar_total = mysqli_query($conn, $select_keluar_total);
    while ($row_keluar_total = mysqli_fetch_array($query_keluar_total)) {
        $ppn = round($row_keluar_total['total'] * $row_keluar_total['ppn'] / 100);
        $total_belanja = round($total_belanja + ($row_keluar_total['total'] + $ppn));
    }

    if ($total_belanja <= 0) {
        $valid = 1;
        $hasil = 1;
    } else {
        $select_bayar = "SELECT * FROM tb_pembayaran WHERE id_pembayaran = '" . $id_trf . "'";
        $query_bayar = mysqli_query($conn, $select_bayar);
        $jum_bayar = mysqli_num_rows($query_bayar);

        if ($jum_bayar > 0) {
            $select_bayar = "SELECT SUM(nominal) AS total FROM tb_pembayaran WHERE id_pembayaran = '" . $id_trf . "'";
            $query_bayar = mysqli_query($conn, $select_bayar);
            $data_bayar = mysqli_fetch_array($query_bayar);

            $total_bayar = round($data_bayar['total']);
            $total_beli = 0;

            $select_beli = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_trf . "'";
            $query_beli = mysqli_query($conn, $select_beli);
            while ($row_beli = mysqli_fetch_array($query_beli)) {
                $ppn = round($row_beli['ppn'] * $row_beli['total'] / 100);
                $total_beli = round($total_beli + $row_beli['total'] + $ppn);
            }

            if ($total_bayar < $total_beli) {
                $valid = 3;
            } else if ($total_bayar > $total_beli) {
                $valid = 2;
            } else {
                $hasil = 1;
                $valid = 1;
            }
        } else {
            $valid = 0;
        }
    }

    $select_transaksi = "SELECT * FROM tb_barang_keluar
        WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'";
    $query_transaksi = mysqli_query($conn, $select_transaksi);
    while ($row_transaksi = mysqli_fetch_array($query_transaksi)) {
        if ($row_transaksi['id_bahan'] == "") {
            $hasil = 3;
        }
    }


    if ($hasil == 1) {
        $select_keluar_bahan = "SELECT id_bahan,SUM(qty) AS qty,id_cabang FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != '' GROUP BY id_bahan ORDER BY id_bahan ASC";
        $query_keluar_bahan = mysqli_query($conn, $select_keluar_bahan);
        $jum_keluar_bahan = mysqli_num_rows($query_keluar_bahan);
        if ($jum_keluar_bahan > 0) {
            while ($row_keluar_bahan = mysqli_fetch_array($query_keluar_bahan)) {
                $select_stock = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_keluar_bahan['id_bahan'] . "' AND id_cabang = '" . $row_keluar_bahan['id_cabang'] . "'";
                $query_stock = mysqli_query($conn, $select_stock);
                $data_stock = mysqli_fetch_array($query_stock);
                $jum_stock = mysqli_num_rows($query_stock);

                if ($jum_stock > 0) {
                    if ($data_stock['stock'] < $row_keluar_bahan['qty']) {
                        $hasil = 2;
                    }
                } else {

                    $hasil = 2;
                }
            }
        }
    }

    if ($hasil == 1) {
        $select_keluar_bahan = "SELECT id_bahan,SUM(qty) AS qty,id_cabang FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != '' GROUP BY id_bahan ORDER BY id_bahan DESC";
        $query_keluar_bahan = mysqli_query($conn, $select_keluar_bahan);
        while ($row_keluar_bahan = mysqli_fetch_array($query_keluar_bahan)) {
            $select_stock = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_keluar_bahan['id_bahan'] . "' AND id_cabang = '" . $row_keluar_bahan['id_cabang'] . "'";
            $query_stock = mysqli_query($conn, $select_stock);
            $data_stock = mysqli_fetch_array($query_stock);

            $total_stock = $data_stock['stock'] - $row_keluar_bahan['qty'];

            $update_stock = "UPDATE tb_stock_cabang SET 
        stock = '" . $total_stock . "' WHERE id_bahan = '" . $row_keluar_bahan['id_bahan'] . "' AND id_cabang = '" . $row_keluar_bahan['id_cabang'] . "';
    ";
            $query_update_stock = mysqli_query($conn, $update_stock);

            if ($query_update_stock) {
                $hasil = 1;
            } else {
                $hasil = 0;
            }

            $select_keluar = "
                SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_bahan
            ";
            $query_keluar = mysqli_query($conn, $select_keluar);
            while ($row_keluar = mysqli_fetch_array($query_keluar)) {
                $select_stock = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_keluar['id_bahan'] . "' AND id_cabang = '" . $row_keluar['id_cabang'] . "'";
                $query_stock = mysqli_query($conn, $select_stock);
                $data_stock = mysqli_fetch_array($query_stock);

                if ($data_stock['stock'] < 1) {
                    $query_update_hpp = mysqli_query($conn, "
                        UPDATE tb_stock_cabang SET hpp = '0' WHERE id_bahan = '" . $row_keluar['id_bahan'] . "' AND id_cabang = '" . $row_keluar['id_cabang'] . "'
                    ");
                }
            }
        }
    }



    if ($hasil == 1) {

        if (strpos($nama_customer, " | ") == true) {
            $id_customer = explode(" | ", $nama_customer);
            $id_customer = $id_customer[1];
        } else {
            $insert_customer = "INSERT INTO tb_customer(
                id_customer,
                nama_customer,
                kota,
                alamat_lengkap,
                no_telp,
                email
            ) VALUES(
                '" . $id_cust . "',
                '" . $nama_customer . "',
                '" . $kota . "',
                '" . $alamat . "',
                '" . $no_telp . "',
                '" . $email . "'
            )";

            $query = mysqli_query($conn, $insert_customer);

            $id_customer = $id_cust;
        }

        $update = "UPDATE tb_barang_keluar SET
        " . $update_id . "
        id_customer = '" . $id_customer . "',
        no_telp = '" . $no_telp . "',
        email = '" . $email . "',
        kota = '" . $kota . "',
        alamat = '" . $alamat . "',
        keterangan = '" . $keterangan . "',
        status_keluar = 's',
        jenis = '" . $jenis_penjualan . "',
        source_customer = '" . $source_customer . "' WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "'
        ";

        $query_update = mysqli_query($conn, $update);
        if ($query_update) {

            $update_bayar = "UPDATE tb_pembayaran SET
                id_pembayaran = '" . $id_keluar . "' WHERE id_pembayaran = '" . $id_trf . "'
            ";

            $query = mysqli_query($conn, $update_bayar);

            $hasil = 1;
        }
    }




    if ($hasil == 1) {
        if ($jenis_penjualan == "new") {
            $select_penjualan2 = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_keluar . "' AND no_polisi != 'Product' GROUP BY no_polisi";
            $query_penjualan2 = mysqli_query($conn, $select_penjualan2);
            while ($row_penjualan2 = mysqli_fetch_array($query_penjualan2)) {

                $depan = "";
                $samping = "";
                $belakang = "";
                $masa_berlaku_depan = date("Y-m-d");
                $masa_berlaku_samping = date("Y-m-d");

                $no_garansi = "";
                $nama = "";
                $type_mobil = "";
                $no_plat = "";

                $select_penjualan = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $row_penjualan2['id_transaksi'] . "' AND no_polisi != 'Product'";
                $query_penjualan = mysqli_query($conn, $select_penjualan);
                while ($row_penjualan = mysqli_fetch_array($query_penjualan)) {



                    $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_penjualan['id_bahan'] . "'";
                    $query_bahan = mysqli_query($conn, $select_bahan);
                    $data_bahan = mysqli_fetch_array($query_bahan);

                    $select_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_penjualan['tipe_mobil'] . "'";
                    $query_mobil = mysqli_query($conn, $select_mobil);
                    $data_mobil = mysqli_fetch_array($query_mobil);

                    $select_cust = "SELECT * FROM tb_customer WHERE id_customer = '" . $row_penjualan['id_customer'] . "'";
                    $query_cust = mysqli_query($conn, $select_cust);
                    $data_cust = mysqli_fetch_array($query_cust);

                    $no_garansi = $no_garansi . "" . $row_penjualan['kode_garansi'];
                    $nama = $nama . "" . $data_cust['nama_customer'];
                    $type_mobil = $type_mobil . "" . $data_mobil['tipe_mobil'];
                    $no_plat = $no_plat . "" . $row_penjualan['no_polisi'];

                    if ($row_penjualan['bagian_mobil'] == "Kaca Depan") {
                        $depan =  $data_bahan['nama_bahan'];
                        $masa_berlaku_depan = date("Y-m-d", strtotime("+" . $data_bahan['masa_berlaku'] . " years"));
                    }
                    if ($row_penjualan['bagian_mobil'] == "Kaca SKKB") {
                        $samping =  $data_bahan['nama_bahan'];
                        $masa_berlaku_samping = date("Y-m-d", strtotime("+" . $data_bahan['masa_berlaku'] . " years"));
                    }
                }

                $select_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_penjualan2['tipe_mobil'] . "'";
                $query_mobil = mysqli_query($conn, $select_mobil);
                $data_mobil = mysqli_fetch_array($query_mobil);

                $select_customer = "SELECT * FROM tb_customer WHERE id_customer = '" . $row_penjualan2['id_customer'] . "'";
                $query_customer = mysqli_query($conn, $select_customer);
                $data_customer = mysqli_fetch_array($query_customer);

                $conn_other = mysqli_connect('203.161.184.26', 'coolplu1_warranty', 'stevsoft14*', 'coolplu1_warranty');

                $insert = "INSERT INTO invoice(
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
                pemasangan,
                cabang                 
            ) VALUES(
                '" . $row_penjualan2['kode_garansi'] . "',
                '" . $data_customer['nama_customer'] . "',
                '" . $data_mobil['tipe_mobil'] . "',
                '" . $row_penjualan2['no_polisi'] . "',
                '" . $depan . "',
                '" . $samping . "',
                '" . $samping . "',
                '" . date("Y-m-d") . "',
                '" . $masa_berlaku_depan . "',
                '" . $masa_berlaku_samping . "',
                '" . $masa_berlaku_samping . "',
                'mobil',
                '" . $cabang . "'
            )";

                $queryInsert = mysqli_query($conn_other, $insert);
            }
        }
    }



    echo $hasil . "|" . $valid . "|" . $id_trf;
}
if (isset($_POST['bahan_ubah'])) {
    $bahan_ubah = mysqli_real_escape_string($conn, $_POST['bahan_ubah']);
    $bahan = mysqli_real_escape_string($conn, $_POST['bahan']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }



    $uom = "";
    $hpp = 0;
    if ($bahan !== "") {
        $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $bahan . "'";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $data_bahan = mysqli_fetch_array($query_bahan);

        $select_stock = "SELECT hpp FROM tb_stock_cabang WHERE id_bahan = '" . $bahan . "' AND id_cabang = '" . $cabang . "'";
        $query_stock = mysqli_query($conn, $select_stock);
        $jum_stock = mysqli_num_rows($query_stock);
        $data_stock = mysqli_fetch_array($query_stock);
        if ($jum_stock > 0) {
            $hpp = $data_stock['hpp'];
        }

        $uom = $data_bahan['uom'];
    }

    $select_keluar = "SELECT * FROM tb_barang_keluar WHERE no_id = '" . $bahan_ubah . "'";
    $query_bahan = mysqli_query($conn, $select_keluar);
    $data_keluar = mysqli_fetch_array($query_bahan);

    if ($data_keluar['no_polisi'] == "Product") {
        $update = "UPDATE tb_barang_keluar SET
            id_bahan = '" . $bahan . "',
            uom = '" . $uom . "',
            hpp = '" . $hpp . "',
            harga = '" . $hpp . "',
            total = '" . ($hpp * $data_keluar['qty']) . "',
            subtotal = '" . ($hpp * $data_keluar['qty']) . "' WHERE no_id = '" . $bahan_ubah . "'
        ";
    } else {
        $update = "UPDATE tb_barang_keluar SET
            id_bahan = '" . $bahan . "',
            uom = '" . $uom . "',
            hpp = '" . $hpp . "' WHERE no_id = '" . $bahan_ubah . "'
        ";
    }

    $query_update = mysqli_query($conn, $update);

    $hasil = "";

    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY no_polisi";
    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);
    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {


        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_barang_keluar['tipe_mobil'] . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $data_bahan = mysqli_fetch_array($query_bahan);

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi = '" . $row_barang_keluar['no_polisi'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $jum_keluar = mysqli_num_rows($query_keluar);

        $hasil = $hasil . '<tr>';
        if ($row_barang_keluar['no_polisi'] !== "Product") {
            $hasil = $hasil . '
           <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '" ><i class="fa fa-trash-o"></i> </a> ' . $row_barang_keluar['no_polisi'] . ' ' . $row_barang_keluar['kode_garansi'] . ' ' . $data_tipe_mobil['tipe_mobil'] . ' <br><a href="javascript:void(0);" class="text-success input_satuan" 
                                                data-no_polisi = "' . $row_barang_keluar['no_polisi'] . '"
                                                data-kode_garansi = "' . $row_barang_keluar['kode_garansi'] . '"
                                                data-tipe_mobil = "' . $row_barang_keluar['tipe_mobil'] . '"
                                                ><i class="fa fa-list"></i> Kaca Satuan</a></td>
    ';
        } else {
            $hasil = $hasil . '
            <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '"><i class="fa fa-trash-o"></i> </a> Product </td>
    ';
        }

        while ($row_keluar = mysqli_fetch_array($query_keluar)) {

            $hpp = 0;
            $select_hpp = "SELECT hpp FROM tb_stock_cabang WHERE id_bahan = '" . $row_keluar['id_bahan'] . "' AND id_cabang = '" . $row_keluar['id_cabang'] . "'";
            $query_hpp = mysqli_query($conn, $select_hpp);
            $data_hpp = mysqli_fetch_array($query_hpp);
            $jum_hpp = mysqli_num_rows($query_hpp);
            if ($jum_hpp > 0) {
                $hpp = $data_hpp['hpp'];
            }

            $hasil = $hasil . '
                    
            <td><a href="javascript:void(0);" class="text-danger del_item del_item_' . $row_keluar['no_id'] . '" data-nopol="' . $row_keluar['no_polisi'] . '" data-no_id="' . $row_keluar['no_id'] . '" ><i class="fa fa-trash-o"></i> </a>
                                                        <select name="bahan_ubah" class="bahan_ubah bahan_ubah_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '">
                                                        <option value="">-- Pilih Bahan --</option>
                                                            ';

            if ($row_keluar['no_polisi'] !== "Product") {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan != 'Product' ORDER BY nama_bahan ASC";
            } else {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = 'Product' ORDER BY nama_bahan ASC";
            }
            $query_bahan = mysqli_query($conn, $select_bahan);
            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                $selected = "";
                if ($row_bahan['id_bahan'] == $row_keluar['id_bahan']) {
                    $selected = "selected";
                }

                $hasil = $hasil . '<option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>';
            }

            $hasil = $hasil . '
                                                        </select>
                                                    </td>
            <td>' . $row_keluar['bagian_mobil'] . '</td>
           <td><input type="number" name="" class="qty_field qty_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-width:50px;max-height:21px;" min="0" value="' . $row_keluar['qty'] . '" autocomplete="off" onclick="this.select();"></td>
            <td class="text-center">' . $row_keluar['uom'] . '</td>
            ';

            if ($row_keluar['no_polisi'] == "Product") {
                $hasil = $hasil . '<td class="text-right">
                    <input type="text" name="" class="text-right harga_product harga_product_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" value="' . number_format($row_keluar['harga']) . '">
                    <input type="hidden" name="" class="harga_product_hidden_' . $row_keluar['no_id'] . '" value="' . $row_keluar['harga'] . '">
                </td>';
            } else {
                $hasil = $hasil . '<td class="text-right">' . number_format($row_keluar['harga']) . '</td>';
            }

            $hasil = $hasil . '
            <td class="text-right">' . number_format($row_keluar['total']) . '</td>
            </tr>
                ';
        }
    }

    if ($jum_barang_keluar > 0) {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY id_transaksi";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $select_total_keluar = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
        $query_total_keluar = mysqli_query($conn, $select_total_keluar);
        $data_total_keluar = mysqli_fetch_array($query_total_keluar);

        $total_keluar = $data_total_keluar['total'];

        $select_total_product = "
            SELECT SUM(total) AS total_product
            FROM
                tb_barang_keluar
            WHERE
                id_transaksi = '" . $id_transaksi . "' AND
                id_cabang = '" . $cabang . "' AND
                no_polisi = 'Product'
        ";
        $query_total_product = mysqli_query($conn, $select_total_product);
        $data_total_product = mysqli_fetch_array($query_total_product);

        $total_keluar_product = $data_total_product['total_product'];
        $ppn = $data_keluar['ppn'] * ($total_keluar + $total_keluar_product) / 100;


        $hasil = $hasil . '
    <tr>
        <th colspan="6" class="text-right">Subtotal Product</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar_product) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Subtotal Kaca Film</th>
        <td colspan="2"><input type="text" class="w-100 text-right change_subtotal" name="change_subtotal" id="" min="0" value="' . number_format($total_keluar) . '">
        <input type="hidden" name="" class="change_subtotal_hidden"></td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">PPN <input type="number" class="text-right ppn_input" style="max-width:50px;max-height:21px;" min="0" max="100" value="' . $data_keluar['ppn'] . '" autocomplete="off" onclick="this.select();"> %</th>
        <td colspan="2" class="text-right">' . number_format($ppn) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Grand Total</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar + $total_keluar_product + $ppn) . '</td>
    </tr>
                    ';
    }
    echo $hasil;
}

if (isset($_POST['ubah_input'])) {
    $ubah_input = mysqli_real_escape_string($conn, $_POST['ubah_input']);

    $hasil = "";

    if ($ubah_input == "product") {
        $filter = $ubah_input;
    } else {
        $filter = "kaca_film";
    }

    $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = '" . $filter . "' ORDER BY nama_bahan ASC";
    $query_bahan = mysqli_query($conn, $select_bahan);
    while ($row_bahan = mysqli_fetch_array($query_bahan)) {
        $hasil = $hasil . '<option value="' . $row_bahan['nama_bahan'] . ' | ' . $row_bahan['id_bahan'] . '">';
    }
    echo $hasil;
}

if (isset($_POST['bank'])) {

    $bank = mysqli_real_escape_string($conn, $_POST['bank']);
    $nominal = mysqli_real_escape_string($conn, $_POST['nominal_bayar']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);

    if ($id_transaksi == "new") {
        $id_trf = $_SESSION['id_user'];
    } else {
        $id_trf = $id_transaksi;
    }

    $select_bayar = "SELECT * FROM tb_pembayaran WHERE id_pembayaran = '" . $id_trf . "' AND metode = '" . $bank . "'";
    $query_bayar = mysqli_query($conn, $select_bayar);
    $jum_bayar = mysqli_num_rows($query_bayar);

    if ($jum_bayar < 1) {
        $insert_bayar = "INSERT INTO tb_pembayaran(
            no_id,
            id_pembayaran,
            metode,
            nominal
        ) VALUES(
            '',
            '" . $id_trf . "',
            '" . $bank . "',
            '" . $nominal . "'
        )";

        $query = mysqli_query($conn, $insert_bayar);

        if ($query) {
            $valid = 1;
        } else {
            $valid = 0;
        }
    } else {
        $valid = 2;
    }

    $hasil = "";

    $select_pembayaran = "SELECT * FROM tb_pembayaran WHERE id_pembayaran = '" . $id_trf . "'";
    $query_pembayaran = mysqli_query($conn, $select_pembayaran);
    $jum_pembayaran = mysqli_num_rows($query_pembayaran);
    if ($jum_pembayaran > 0) {
        while ($row_pembayaran = mysqli_fetch_array($query_pembayaran)) {
            $hasil = $hasil . '
                                                    <tr>
                                                        <td>
                                                            <select name="" id="" class="form-control form-control-sm bank_' . $row_pembayaran['no_id'] . '">
                                                            ';

            $select_akun = "SELECT * FROM tb_akun WHERE status_sj = 'aktif' ORDER BY bank ASC";
            $query_akun = mysqli_query($conn, $select_akun);
            while ($row_akun = mysqli_fetch_array($query_akun)) {
                $selected = "";
                if ($row_akun['id_akun'] == $row_pembayaran['metode']) {
                    $selected = "selected";
                }
                $hasil = $hasil . '
                                                    <option value="' . $row_akun['id_akun'] . '" ' . $selected . '>' . $row_akun['bank'] . '</option>
                                                ';
            }

            $hasil = $hasil . '
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="" id="" class="text-right form-control form-control-sm nominal_terbayar nominal_terbayar_' . $row_pembayaran['no_id'] . '" min="0" data-no_id="' . $row_pembayaran['no_id'] . '" value="' . $row_pembayaran['nominal'] . '"><input type="hidden" name="" class="nominal_terbayar_hidden nominal_terbayar_hidden_' . $row_pembayaran['no_id'] . '" value="' . $row_pembayaran['nominal'] . '"></td>
                                                        <td>
                                                        <button type="button" class="btn btn-warning ubah_pembayaran ubah_pembayaran_' . $row_pembayaran['no_id'] . '" data-no_id="' . $row_pembayaran['no_id'] . '"><i class="fa fa-pencil"></i></button>
                                                        <button type="button" class="btn btn-danger hapus_pembayaran hapus_pembayaran_' . $row_pembayaran['no_id'] . '" data-no_id="' . $row_pembayaran['no_id'] . '"><i class="fa fa-trash-o"></i></button>
                                                        </td>
                                                    </tr>
                                                ';
        }
        $hasil = $hasil . '
                                            <tr>
                                                <td>
                                                    <select name="" id="" class="form-control form-control-sm bank_bayar">
                                                    ';

        $select_akun = "SELECT * FROM tb_akun WHERE status_sj = 'aktif' ORDER BY bank ASC";
        $query_akun = mysqli_query($conn, $select_akun);
        while ($row_akun = mysqli_fetch_array($query_akun)) {

            $hasil = $hasil . '
                                                    <option value="' . $row_akun['id_akun'] . '">' . $row_akun['bank'] . '</option>
                                                ';
        }

        $hasil = $hasil . '
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="" id="" class="text-right form-control form-control-sm nominal_bayar" min="0" value="0">
                                                    <input type="hidden" name="" class="nominal_bayar_hidden" value="0"></td>
                                                    <td>
                                                        <button type="button" class="btn btn-secondary btn_bayar"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                </tr>
                                            ';
    } else {
        $hasil = $hasil . '
                                            <tr>
                                                <td>
                                                    <select name="" id="" class="form-control form-control-sm bank_bayar">
                                                    ';

        $select_akun = "SELECT * FROM tb_akun WHERE status_sj = 'aktif' ORDER BY bank ASC";
        $query_akun = mysqli_query($conn, $select_akun);
        while ($row_akun = mysqli_fetch_array($query_akun)) {

            $hasil = $hasil . '
                                                    <option value="' . $row_akun['id_akun'] . '">' . $row_akun['bank'] . '</option>
                                                ';
        }

        $hasil = $hasil . '
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="" id="" class="form-control form-control-sm nominal_bayar" min="0" value="0">
                                                    <input type="hidden" name="" class="nominal_bayar"></td>
                                                    <td>
                                                        <button type="button" class="btn btn-secondary btn_bayar"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                </tr>
                                            ';
    }
    echo $valid . "|" . $hasil;
}

if (isset($_POST['ubah_pembayaran'])) {
    $ubah_pembayaran = mysqli_real_escape_string($conn, $_POST['ubah_pembayaran']);
    $bank = mysqli_real_escape_string($conn, $_POST['bank_ubah']);
    $nominal = mysqli_real_escape_string($conn, $_POST['nominal_ubah']);


    $valid = 0;

    $update = "UPDATE tb_pembayaran SET 
        metode = '" . $bank . "',
        nominal = '" . $nominal . "' WHERE no_id = '" . $ubah_pembayaran . "';
    ";

    $query = mysqli_query($conn, $update);

    if ($query) {
        $valid = 1;
    }
    echo $valid;
}

if (isset($_POST['hapus_pembayaran'])) {
    $hapus_pembayaran = mysqli_real_escape_string($conn, $_POST['hapus_pembayaran']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);

    if ($id_transaksi == "new") {
        $id_trf = $_SESSION['id_user'];
    } else {
        $id_trf = $id_transaksi;
    }

    $valid = 0;

    $delete = "DELETE FROM tb_pembayaran WHERE no_id = '" . $hapus_pembayaran . "'";
    $query = mysqli_query($conn, $delete);

    if ($query) {
        $valid = 1;
    }

    $hasil = "";

    $select_pembayaran = "SELECT * FROM tb_pembayaran WHERE id_pembayaran = '" . $id_trf . "'";
    $query_pembayaran = mysqli_query($conn, $select_pembayaran);
    $jum_pembayaran = mysqli_num_rows($query_pembayaran);
    if ($jum_pembayaran > 0) {
        while ($row_pembayaran = mysqli_fetch_array($query_pembayaran)) {
            $hasil = $hasil . '
                                                    <tr>
                                                        <td>
                                                            <select name="" id="" class="form-control form-control-sm bank_' . $row_pembayaran['no_id'] . '">
                                                            ';

            $select_akun = "SELECT * FROM tb_akun WHERE status_sj = 'aktif' ORDER BY bank ASC";
            $query_akun = mysqli_query($conn, $select_akun);
            while ($row_akun = mysqli_fetch_array($query_akun)) {
                $selected = "";
                if ($row_akun['id_akun'] == $row_pembayaran['metode']) {
                    $selected = "selected";
                }
                $hasil = $hasil . '
                                                    <option value="' . $row_akun['id_akun'] . '" ' . $selected . '>' . $row_akun['bank'] . '</option>
                                                ';
            }

            $hasil = $hasil . '
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="" id="" class="text-right form-control form-control-sm nominal_terbayar nominal_terbayar_' . $row_pembayaran['no_id'] . '" min="0" data-no_id="' . $row_pembayaran['no_id'] . '" value="' . $row_pembayaran['nominal'] . '"><input type="hidden" name="" class="nominal_terbayar_hidden nominal_terbayar_hidden_' . $row_pembayaran['no_id'] . '" value="' . $row_pembayaran['nominal'] . '"></td>
                                                        <td>
                                                        <button type="button" class="btn btn-warning ubah_pembayaran ubah_pembayaran_' . $row_pembayaran['no_id'] . '" data-no_id="' . $row_pembayaran['no_id'] . '"><i class="fa fa-pencil"></i></button>
                                                        <button type="button" class="btn btn-danger hapus_pembayaran hapus_pembayaran_' . $row_pembayaran['no_id'] . '" data-no_id="' . $row_pembayaran['no_id'] . '"><i class="fa fa-trash-o"></i></button>
                                                        </td>
                                                    </tr>
                                                ';
        }
        $hasil = $hasil . '
                                            <tr>
                                                <td>
                                                    <select name="" id="" class="form-control form-control-sm bank_bayar">
                                                    ';

        $select_akun = "SELECT * FROM tb_akun WHERE status_sj = 'aktif' ORDER BY bank ASC";
        $query_akun = mysqli_query($conn, $select_akun);
        while ($row_akun = mysqli_fetch_array($query_akun)) {

            $hasil = $hasil . '
                                                    <option value="' . $row_akun['id_akun'] . '">' . $row_akun['bank'] . '</option>
                                                ';
        }

        $hasil = $hasil . '
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="" id="" class="text-right form-control form-control-sm nominal_bayar" min="0" value="0">
                                                    <input type="hidden" name="" class="nominal_bayar_hidden" value="0"></td>
                                                    <td>
                                                        <button type="button" class="btn btn-secondary btn_bayar"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                </tr>
                                            ';
    } else {
        $hasil = $hasil . '
                                            <tr>
                                                <td>
                                                    <select name="" id="" class="form-control form-control-sm bank_bayar">
                                                    ';

        $select_akun = "SELECT * FROM tb_akun WHERE status_sj = 'aktif' ORDER BY bank ASC";
        $query_akun = mysqli_query($conn, $select_akun);
        while ($row_akun = mysqli_fetch_array($query_akun)) {

            $hasil = $hasil . '
                                                    <option value="' . $row_akun['id_akun'] . '">' . $row_akun['bank'] . '</option>
                                                ';
        }

        $hasil = $hasil . '
                                                        </select>
                                                    </td>
                                                    <td><input type="text" name="" id="" class="text-right form-control form-control-sm nominal_bayar" min="0" value="0">
                                                    <input type="hidden" name="" class="nominal_bayar_hidden" value="0"></td></td>
                                                    <td>
                                                        <button type="button" class="btn btn-secondary btn_bayar"><i class="fa fa-plus"></i></button>
                                                    </td>
                                                </tr>
                                            ';
    }
    echo $valid . "|" . $hasil;
}

if (isset($_POST['input_satuan'])) {
    $mobil = mysqli_real_escape_string($conn, $_POST['tipe_mobil']);

    $select_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $mobil . "'";
    $query_mobil = mysqli_query($conn, $select_mobil);
    $data_mobil = mysqli_fetch_array($query_mobil);

    $hasil = '
    <select name="tipe_mobil" class="form-control form-control-sm tipe_mobil">
    ';
    $select_mobil = "SELECT id_tipe,tipe_mobil FROM tb_tipe_mobil ORDER BY tipe_mobil ASC";
    $query_mobil = mysqli_query($conn, $select_mobil);
    while ($row_mobil = mysqli_fetch_array($query_mobil)) {
        $selected = "";
        if ($row_mobil['id_tipe'] == $data_mobil['id_tipe']) {
            $selected = "selected";
        }
        $hasil = $hasil . '
                <option value="' . $row_mobil['id_tipe'] . '" ' . $selected . '>' . $row_mobil['tipe_mobil'] . '</option>
            ';
    }
    echo $hasil;
}

if (isset($_POST['change_subtotal'])) {
    $change_subtotal = mysqli_real_escape_string($conn, $_POST['change_subtotal']);
    $subtotal = mysqli_real_escape_string($conn, $_POST['subtotal']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);

    if ($change_subtotal == "new") {
        $id_transaksi = $_SESSION['id_user'];
    } else {
        $id_transaksi = $change_subtotal;
    }

    $update_subtotal = mysqli_query($conn, "UPDATE tb_barang_keluar SET
                                    subtotal = '" . $subtotal . "'
                                    WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'");


    $total_qty = 0;

    $select_total_qty = "SELECT qty FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
    $query_total_qty = mysqli_query($conn, $select_total_qty);
    while ($row_total_qty = mysqli_fetch_array($query_total_qty)) {
        $total_qty = $total_qty + $row_total_qty['qty'];
    }



    $select_transaksi = "SELECT * FROM tb_barang_keluar
                            WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
    $query_transaksi = mysqli_query($conn, $select_transaksi);
    while ($row_transaksi = mysqli_fetch_array($query_transaksi)) {
        $total = $row_transaksi['qty'] / $total_qty * $row_transaksi['subtotal'];
        if ($row_transaksi['qty'] > 0) {
            $harga = round($total / $row_transaksi['qty']);
        } else {
            $harga = 0;
        }

        $update = mysqli_query($conn, "UPDATE tb_barang_keluar SET
                                    harga = '" . $harga . "',
                                    total = '" . $total . "' WHERE no_id = '" . $row_transaksi['no_id'] . "'");
    }

    $hasil = "";

    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY no_polisi";
    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);
    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {


        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_barang_keluar['tipe_mobil'] . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $data_bahan = mysqli_fetch_array($query_bahan);

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi = '" . $row_barang_keluar['no_polisi'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $jum_keluar = mysqli_num_rows($query_keluar);

        $hasil = $hasil . '<tr>';
        if ($row_barang_keluar['no_polisi'] !== "Product") {
            $hasil = $hasil . '
           <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '" data-no_id="' . $row_barang_keluar['no_id'] . '"><i class="fa fa-trash-o"></i> </a> ' . $row_barang_keluar['no_polisi'] . ' ' . $row_barang_keluar['kode_garansi'] . ' ' . $data_tipe_mobil['tipe_mobil'] . ' <br><a href="javascript:void(0);" class="text-success input_satuan" 
                                                data-no_polisi = "' . $row_barang_keluar['no_polisi'] . '"
                                                data-kode_garansi = "' . $row_barang_keluar['kode_garansi'] . '"
                                                data-tipe_mobil = "' . $row_barang_keluar['tipe_mobil'] . '"
                                                
                                                ><i class="fa fa-list"></i> Kaca Satuan</a></td>
    ';
        } else {
            $hasil = $hasil . '
            <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '"><i class="fa fa-trash-o"></i> </a> Product </td>
    ';
        }

        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
            $hasil = $hasil . '
                    
            <td><a href="javascript:void(0);" class="text-danger del_item del_item_' . $row_keluar['no_id'] . '" data-nopol="' . $row_keluar['no_polisi'] . '" data-no_id="' . $row_keluar['no_id'] . '" ><i class="fa fa-trash-o"></i> </a>
                                                        <select name="bahan_ubah" class="bahan_ubah bahan_ubah_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '">
                                                            <option value="">-- Pilih Bahan --</option>
                                                            ';

            if ($row_barang_keluar['no_polisi'] == "Product") {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = 'Product' ORDER BY nama_bahan ASC";
            } else {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan != 'Product' ORDER BY nama_bahan ASC";
            }
            $query_bahan = mysqli_query($conn, $select_bahan);
            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                $selected = "";
                if ($row_bahan['id_bahan'] == $row_keluar['id_bahan']) {
                    $selected = "selected";
                }

                $hasil = $hasil . '<option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>';
            }

            $hasil = $hasil . '
                                                        </select>
                                                    </td>
            <td>' . $row_keluar['bagian_mobil'] . '</td>
           <td><input type="number" name="" class="qty_field qty_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-width:50px;max-height:21px;" min="0" value="' . $row_keluar['qty'] . '" autocomplete="off" onclick="this.select();"></td>
            <td class="text-center">' . $row_keluar['uom'] . '</td>
            ';

            if ($row_keluar['no_polisi'] == "Product") {
                $hasil = $hasil . '<td class="text-right">
                    <input type="text" name="" class="text-right harga_product harga_product_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" value="' . number_format($row_keluar['harga']) . '">
                    <input type="hidden" name="" class="harga_product_hidden_' . $row_keluar['no_id'] . '" value="' . $row_keluar['harga'] . '">
                </td>';
            } else {
                $hasil = $hasil . '<td class="text-right">' . number_format($row_keluar['harga']) . '</td>';
            }

            $hasil = $hasil . '
            <td class="text-right">' . number_format($row_keluar['total']) . '</td>
            </tr>
                ';
        }
    }

    if ($jum_barang_keluar > 0) {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY id_transaksi";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $select_total_keluar = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
        $query_total_keluar = mysqli_query($conn, $select_total_keluar);
        $data_total_keluar = mysqli_fetch_array($query_total_keluar);

        $total_keluar = $data_total_keluar['total'];

        $select_total_product = "
            SELECT SUM(total) AS total_product
            FROM
                tb_barang_keluar
            WHERE
                id_transaksi = '" . $id_transaksi . "' AND
                id_cabang = '" . $cabang . "' AND
                no_polisi = 'Product'
        ";
        $query_total_product = mysqli_query($conn, $select_total_product);
        $data_total_product = mysqli_fetch_array($query_total_product);

        $total_keluar_product = $data_total_product['total_product'];
        $ppn = $data_keluar['ppn'] * ($total_keluar + $total_keluar_product) / 100;


        $hasil = $hasil . '
    <tr>
        <th colspan="6" class="text-right">Subtotal Product</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar_product) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Subtotal Kaca Film</th>
        <td colspan="2"><input type="text" class="w-100 text-right change_subtotal" name="change_subtotal" id="" min="0" value="' . number_format($total_keluar) . '">
        <input type="hidden" name="" class="change_subtotal_hidden"></td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">PPN <input type="number" class="text-right ppn_input" style="max-width:50px;max-height:21px;" min="0" max="100" value="' . $data_keluar['ppn'] . '" autocomplete="off" onclick="this.select();"> %</th>
        <td colspan="2" class="text-right">' . number_format($ppn) . '</td>
    </tr>
    <tr>
        <th colspan="6" class="text-right">Grand Total</th>
        <td colspan="2" class="text-right">' . number_format($total_keluar + $total_keluar_product + $ppn) . '</td>
    </tr>
                    ';
    }
    echo $hasil;
}

if (isset($_POST['ubah_harga_product'])) {
    $ubah_harga_product = mysqli_real_escape_string($conn, $_POST['ubah_harga_product']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $harga_product = mysqli_real_escape_string($conn, $_POST['harga_product']);
    $cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $select_keluar = "
        SELECT *
        FROM
            tb_barang_keluar
        WHERE
            no_id = '" . $ubah_harga_product . "'
    ";
    $query_keluar = mysqli_query($conn, $select_keluar);
    $data_keluar = mysqli_fetch_array($query_keluar);

    $total = $data_keluar['qty'] * $harga_product;

    $update_harga = "
        UPDATE
            tb_barang_keluar
        SET
            harga = '" . $harga_product . "',
            total = '" . $total . "',
            subtotal = '" . $total . "'
        WHERE
            no_id = '" . $ubah_harga_product . "'
    ";
    $query_update_harga = mysqli_query($conn, $update_harga);

    $hasil = "";

    $select_barang_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY no_polisi";
    $query_barang_keluar = mysqli_query($conn, $select_barang_keluar);
    $jum_barang_keluar = mysqli_num_rows($query_barang_keluar);
    while ($row_barang_keluar = mysqli_fetch_array($query_barang_keluar)) {


        $select_tipe_mobil = "SELECT * FROM tb_tipe_mobil WHERE id_tipe = '" . $row_barang_keluar['tipe_mobil'] . "'";
        $query_tipe_mobil = mysqli_query($conn, $select_tipe_mobil);
        $data_tipe_mobil = mysqli_fetch_array($query_tipe_mobil);

        $select_bahan = "SELECT * FROM tb_bahan WHERE id_bahan = '" . $row_barang_keluar['id_bahan'] . "'";
        $query_bahan = mysqli_query($conn, $select_bahan);
        $data_bahan = mysqli_fetch_array($query_bahan);

        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi = '" . $row_barang_keluar['no_polisi'] . "'";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $jum_keluar = mysqli_num_rows($query_keluar);

        $hasil = $hasil . '<tr>';
        if ($row_barang_keluar['no_polisi'] !== "Product") {
            $hasil = $hasil . '
           <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '" data-no_id="' . $row_barang_keluar['no_id'] . '"><i class="fa fa-trash-o"></i> </a> ' . $row_barang_keluar['no_polisi'] . ' ' . $row_barang_keluar['kode_garansi'] . ' ' . $data_tipe_mobil['tipe_mobil'] . ' <br><a href="javascript:void(0);" class="text-success input_satuan" 
                                                data-no_polisi = "' . $row_barang_keluar['no_polisi'] . '"
                                                data-kode_garansi = "' . $row_barang_keluar['kode_garansi'] . '"
                                                data-tipe_mobil = "' . $row_barang_keluar['tipe_mobil'] . '"
                                                
                                                ><i class="fa fa-list"></i> Kaca Satuan</a></td>
    ';
        } else {
            $hasil = $hasil . '
            <td rowspan="' . $jum_keluar . '"><a href="javascript:void(0);" class="text-danger del_by_nopol del_by_nopol_' . $row_barang_keluar['no_id'] . '" data-id_transaksi="' . $id_transaksi . '" data-nopol="' . $row_barang_keluar['no_polisi'] . '"><i class="fa fa-trash-o"></i> </a> Product </td>
    ';
        }

        while ($row_keluar = mysqli_fetch_array($query_keluar)) {
            $hasil = $hasil . '
                    
            <td><a href="javascript:void(0);" class="text-danger del_item del_item_' . $row_keluar['no_id'] . '" data-nopol="' . $row_keluar['no_polisi'] . '" data-no_id="' . $row_keluar['no_id'] . '" ><i class="fa fa-trash-o"></i> </a>
                                                        <select name="bahan_ubah" class="bahan_ubah bahan_ubah_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '">
                                                            <option value="">-- Pilih Bahan --</option>
                                                            ';

            if ($row_barang_keluar['no_polisi'] == "Product") {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan = 'Product' ORDER BY nama_bahan ASC";
            } else {
                $select_bahan = "SELECT * FROM tb_bahan WHERE jenis_bahan != 'Product' ORDER BY nama_bahan ASC";
            }
            $query_bahan = mysqli_query($conn, $select_bahan);
            while ($row_bahan = mysqli_fetch_array($query_bahan)) {
                $selected = "";
                if ($row_bahan['id_bahan'] == $row_keluar['id_bahan']) {
                    $selected = "selected";
                }

                $hasil = $hasil . '<option value="' . $row_bahan['id_bahan'] . '" ' . $selected . '>' . $row_bahan['nama_bahan'] . '</option>';
            }

            $hasil = $hasil . '
                                                        </select>
                                                    </td>
            <td>' . $row_keluar['bagian_mobil'] . '</td>
           <td><input type="number" name="" class="qty_field qty_change_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" style="max-width:50px;max-height:21px;" min="0" value="' . $row_keluar['qty'] . '" autocomplete="off" onclick="this.select();"></td>
            <td class="text-center">' . $row_keluar['uom'] . '</td>
            ';

            if ($row_keluar['no_polisi'] == "Product") {
                $hasil = $hasil . '<td class="text-right">
                    <input type="text" name="" class="text-right harga_product harga_product_' . $row_keluar['no_id'] . '" data-no_id="' . $row_keluar['no_id'] . '" value="' . number_format($row_keluar['harga']) . '">
                    <input type="hidden" name="" class="harga_product_hidden_' . $row_keluar['no_id'] . '" value="' . $row_keluar['harga'] . '">
                </td>';
            } else {
                $hasil = $hasil . '<td class="text-right">' . number_format($row_keluar['harga']) . '</td>';
            }

            $hasil = $hasil . '
            <td class="text-right">' . number_format($row_keluar['total']) . '</td>
            </tr>
                ';
        }
    }

    if ($jum_barang_keluar > 0) {
        $select_keluar = "SELECT * FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' GROUP BY id_transaksi";
        $query_keluar = mysqli_query($conn, $select_keluar);
        $data_keluar = mysqli_fetch_array($query_keluar);

        $select_total_keluar = "SELECT SUM(total) AS total FROM tb_barang_keluar WHERE id_transaksi = '" . $id_transaksi . "' AND id_cabang = '" . $cabang . "' AND no_polisi != 'Product'";
        $query_total_keluar = mysqli_query($conn, $select_total_keluar);
        $data_total_keluar = mysqli_fetch_array($query_total_keluar);

        $total_keluar = $data_total_keluar['total'];

        $select_total_product = "
            SELECT SUM(total) AS total_product
            FROM
                tb_barang_keluar
            WHERE
                id_transaksi = '" . $id_transaksi . "' AND
                id_cabang = '" . $cabang . "' AND
                no_polisi = 'Product'
        ";
        $query_total_product = mysqli_query($conn, $select_total_product);
        $data_total_product = mysqli_fetch_array($query_total_product);

        $total_keluar_product = $data_total_product['total_product'];
        $ppn = $data_keluar['ppn'] * ($total_keluar + $total_keluar_product) / 100;


        $hasil = $hasil . '
        <tr>
            <th colspan="6" class="text-right">Subtotal Product</th>
            <td colspan="2" class="text-right">' . number_format($total_keluar_product) . '</td>
        </tr>
        <tr>
            <th colspan="6" class="text-right">Subtotal Kaca Film</th>
            <td colspan="2"><input type="text" class="w-100 text-right change_subtotal" name="change_subtotal" id="" min="0" value="' . number_format($total_keluar) . '">
            <input type="hidden" name="" class="change_subtotal_hidden"></td>
        </tr>
        <tr>
            <th colspan="6" class="text-right">PPN <input type="number" class="text-right ppn_input" style="max-width:50px;max-height:21px;" min="0" max="100" value="' . $data_keluar['ppn'] . '" autocomplete="off" onclick="this.select();"> %</th>
            <td colspan="2" class="text-right">' . number_format($ppn) . '</td>
        </tr>
        <tr>
            <th colspan="6" class="text-right">Grand Total</th>
            <td colspan="2" class="text-right">' . number_format($total_keluar + $total_keluar_product + $ppn) . '</td>
        </tr>
                        ';
    }
    echo $hasil;
}

if (isset($_POST['tipe_baru'])) {
    $tipe_baru = mysqli_real_escape_string($conn, $_POST['tipe_baru']);


    $hasil = '
    <select name="tipe_mobil" class="form-control form-control-sm tipe_mobil">
    ';
    $select_mobil = "SELECT id_tipe,tipe_mobil FROM tb_tipe_mobil ORDER BY tipe_mobil ASC";
    $query_mobil = mysqli_query($conn, $select_mobil);
    while ($row_mobil = mysqli_fetch_array($query_mobil)) {
        $hasil = $hasil . '
                <option value="' . $row_mobil['id_tipe'] . '">' . $row_mobil['tipe_mobil'] . '</option>
            ';
    }

    $hasil = $hasil . '
    </select>
    ';
    if ($tipe_baru > 0) {
        $hasil = '
        <input type="text" name="tipe_mobil" class="form-control form-control-sm tipe_mobil" autocomplete="off" onclick="this.select();">
    ';
    }
    echo $hasil;
}
