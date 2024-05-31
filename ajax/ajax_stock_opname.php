<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";
session_start();

if (isset($_POST['cari_nama_barang'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['cari_nama_barang']);

    $hasil = "";
    if($nama_barang == ''){
        $filter = '';
    }else{
        $filter = "AND nama_kain LIKE '%" . $nama_barang . "%' OR id_bahan_kain LIKE '%" . $nama_barang . "%' OR kode_bahan_kain LIKE '%" . $nama_barang . "%'" ;
    }
    
    $sql_get_barang = mysqli_query($conn, "SELECT id_bahan_kain,kode_bahan_kain,nama_kain FROM tb_bahan_kain WHERE kategori LIKE '%KAIN JADI%' " . $filter . " ORDER BY nama_kain ASC LIMIT 50");
    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
        $hasil = $hasil . '
            <option value="' . $row_barang['id_bahan_kain'] . ' | ' . $row_barang['kode_bahan_kain'] . ' | ' . $row_barang['nama_kain'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['get_stock_berat'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['get_stock_berat']);

    $ex_barang = explode(" | ", $nama_barang);
    $id_barang = $ex_barang[0];

    $hasil = "";
    $hasil_uom = "";
    $sql_get_stock_berat = mysqli_query($conn, "SELECT barcode,keterangan,terpakai FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_barang . "' OR terpakai LIKE '%" . $id_barang . "%' OR keterangan LIKE '%" . $id_barang . "%'");
    while ($row_stock_berat = mysqli_fetch_array($sql_get_stock_berat)) {
        if($row_stock_berat['keterangan'] != ''){
            $hasil = $hasil . '
                <option value="' . $row_stock_berat['barcode'] . '">' . $row_stock_berat['keterangan'] . '</option>
            ';
        }else{
            $hasil = $hasil . '
                <option value="' . $row_stock_berat['barcode'] . '">Terpakai di ' . $row_stock_berat['terpakai'] . '</option>
            ';
        }
    }
    $sql_get_stock_uom = mysqli_query($conn, "SELECT DISTINCT uom FROM tb_stock WHERE id_bahan = '" . $id_barang . "'");
    while ($row_stock_uom = mysqli_fetch_array($sql_get_stock_uom)) {
        $hasil_uom = $hasil_uom . '
            <option value="' . $row_stock_uom['uom'] . '">' . $row_stock_uom['uom'] . '</option>
        ';
    }



    echo $hasil . "|" . $hasil_uom;
}

if (isset($_POST['add_list'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['add_list']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $ex_barang = explode(" | ", $nama_barang);
    $id_barang = $ex_barang[0];
    $berat_barang = mysqli_real_escape_string($conn, $_POST['berat_barang']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $query = "";
    $sql_get_barang_stock = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $id_barang . "' AND barcode = '" . $berat_barang . "'");
    if ($row_barang_stock = mysqli_fetch_array($sql_get_barang_stock)) {
        $id_opname = id_gen_opname();
        $netto = $row_barang_stock['bruto'];

        $sql_insert_opname = mysqli_query($conn, "
            INSERT INTO inv_adjust_in(
                inv_in_id,
                id_inv_in,
                inv_date,
                status,
                id_product,
                barcode,
                stock_awal,
                stock_change,
                create_by,
                create_date
            ) VALUES(
                '" . $id_opname . "',
                '" . $id_transaksi . "',
                '" . date("Y-m-d") . "',
                'd',
                '" . $id_barang . "',
                '" . $berat_barang . "',
                '" . $netto . "',
                '" . $netto . "',
                '" . $_SESSION['id_user'] . "',
                '" . date("Y-m-d H:i:s") . "'
            )
        ");

        $query = "
            INSERT INTO inv_adjust_in(
                inv_in_id,
                id_inv_in,
                inv_date,
                status,
                id_product,
                barcode,
                stock_awal,
                stock_change,
                create_by,
                create_date
            ) VALUES(
                '" . $id_opname . "',
                '" . $id_transaksi . "',
                '" . date("Y-m-d") . "',
                'd',
                '" . $id_barang . "',
                '" . $berat_barang . "',
                '" . $netto . "',
                '" . $netto . "',
                '" . $_SESSION['id_user'] . "',
                '" . date("Y-m-d H:i:s") . "'
            )
        ";

        $valid = 1;
    } else {
        $valid = 0;
    }

    $hasil = "";
    $sql_get_opname = mysqli_query($conn, "SELECT * FROM inv_adjust_in WHERE id_inv_in = '" . $id_transaksi . "'");
    while ($row_opname = mysqli_fetch_array($sql_get_opname)) {
        $inv_in_id = $row_opname['inv_in_id'];
        $id_product = $row_opname['id_product'];
        $barcode = $row_opname['barcode'];
        $nm_barang = "";
        $sql_get_barang = mysqli_query($conn, "SELECT nama_kain FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_product . "'");
        if ($row_barang = mysqli_fetch_array($sql_get_barang)) {
            $nm_barang = $row_barang['nama_kain'];
        }
        $stock_awal = $row_opname['stock_awal'];
        $stock_change = $row_opname['stock_change'];
        $stock_opname = $row_opname['stock_opname'];
        $barcode = $row_opname['barcode'];

        $hasil = $hasil . '
            <tr>
                <td class="text-center">' . $id_product . '</td>
                <td class="text-center">' . $nm_barang . '</td>
                <td class="text-center">' . $barcode . '</td>
                <td class="text-center">' . $stock_awal . '</td>
                <td>
                    <input type="number" class="form-control form-control-sm opname_input text-right" data-inv_in_id="' . $inv_in_id . '" value="' . $stock_change . '">
                </td>
                <td class="text-center">' . $stock_opname . '</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger del_opname" data-inv_in_id="' . $inv_in_id . '"><i class="fa fa-trash-o"></i> Hapus</button>
                </td>
            </tr>
        ';
    }

    echo $valid . "|" . $hasil . "|" . $query;
}

if (isset($_POST['opname_input'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['opname_input']);
    $inv_in_id = mysqli_real_escape_string($conn, $_POST['inv_in_id']);
    $input_opname = mysqli_real_escape_string($conn, $_POST['input_opname']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $stock_awal = 0;
    $sql_get_stock_opname = mysqli_query($conn, "SELECT stock_awal FROM inv_adjust_in WHERE inv_in_id = '" . $inv_in_id . "'");
    if ($row_stock_opname = mysqli_fetch_array($sql_get_stock_opname)) {
        $stock_awal = $row_stock_opname['stock_awal'];
    }

    $stock_opname = $input_opname - $stock_awal;

    $sql_update_opname = mysqli_query($conn, "
        UPDATE
            inv_adjust_in   
        SET
            stock_change = '" . $input_opname . "',
            stock_opname = '" . $stock_opname . "'
        WHERE
            inv_in_id = '" . $inv_in_id . "'
    ");

    $hasil = "";
    $sql_get_opname = mysqli_query($conn, "SELECT * FROM inv_adjust_in WHERE id_inv_in = '" . $id_transaksi . "'");
    while ($row_opname = mysqli_fetch_array($sql_get_opname)) {
        $inv_in_id = $row_opname['inv_in_id'];
        $id_product = $row_opname['id_product'];
        $nm_barang = "";
        $sql_get_barang = mysqli_query($conn, "SELECT nama_kain FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_product . "'");
        if ($row_barang = mysqli_fetch_array($sql_get_barang)) {
            $nm_barang = $row_barang['nama_kain'];
        }
        $stock_awal = $row_opname['stock_awal'];
        $stock_change = $row_opname['stock_change'];
        $stock_opname = $row_opname['stock_opname'];
        $barcode = $row_opname['barcode'];

        $hasil = $hasil . '
            <tr>
                <td class="text-center">' . $id_product . '</td>
                <td class="text-center">' . $nm_barang . '</td>
                <td class="text-center">' . $barcode . '</td>
                <td class="text-center">' . $stock_awal . '</td>
                <td>
                    <input type="number" class="form-control form-control-sm opname_input text-right" data-inv_in_id="' . $inv_in_id . '" value="' . $stock_change . '">
                </td>
                <td class="text-center">' . $stock_opname . '</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger del_opname" data-inv_in_id="' . $inv_in_id . '"><i class="fa fa-trash-o"></i> Hapus</button>
                </td>
            </tr>
        ';
    }

    echo $hasil;
}

if (isset($_POST['del_opname'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['del_opname']);
    $inv_in_id = mysqli_real_escape_string($conn, $_POST['inv_in_id']);

    if ($id_transaksi == "new") {
        $id_transaksi = $_SESSION['id_user'];
    }

    $sql_del_opname = mysqli_query($conn, "DELETE FROM inv_adjust_in WHERE inv_in_id = '" . $inv_in_id . "'");

    $hasil = "";
    $sql_get_opname = mysqli_query($conn, "SELECT * FROM inv_adjust_in WHERE id_inv_in = '" . $id_transaksi . "'");
    while ($row_opname = mysqli_fetch_array($sql_get_opname)) {
        $id_inv = $row_opname['inv_in_id'];
        $id_product = $row_opname['id_product'];
        $nm_barang = "";
        $sql_get_barang = mysqli_query($conn, "SELECT nama_kain FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_product . "'");
        if ($row_barang = mysqli_fetch_array($sql_get_barang)) {
            $nm_barang = $row_barang['nama_kain'];
        }
        $stock_awal = $row_opname['stock_awal'];
        $stock_change = $row_opname['stock_change'];
        $stock_opname = $row_opname['stock_opname'];
        $barcode = $row_opname['barcode'];

        $hasil = $hasil . '
            <tr>
                <td class="text-center">' . $id_product . '</td>
                <td class="text-center">' . $nm_barang . '</td>
                <td class="text-center">' . $barcode . '</td>
                <td class="text-center">' . $stock_awal . '</td>
                <td>
                    <input type="number" class="form-control form-control-sm opname_input text-right" data-inv_in_id="' . $id_inv . '" value="' . $stock_change . '">
                </td>
                <td class="text-center">' . $stock_opname . '</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-danger del_opname" data-inv_in_id="' . $id_inv . '"><i class="fa fa-trash-o"></i> Hapus</button>
                </td>
            </tr>
        ';
    }

    echo $hasil;
}
