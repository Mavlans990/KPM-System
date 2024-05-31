<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
session_start();

if (isset($_POST['cari_lot'])) {
    $nama_lot = mysqli_real_escape_string($conn, $_POST['cari_lot']);
    $id_bahan = mysqli_real_escape_string($conn, $_POST['id_bahan']);

    $hasil = "";
    $sql_get_nama_lot = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE lot LIKE '%" . $nama_lot . "%' AND id_bahan_kain = '" . $id_bahan . "' ORDER BY lot ASC LIMIT 10");
    while ($row_nama_lot = mysqli_fetch_array($sql_get_nama_lot)) {
        $hasil = $hasil . '
            <option value="' . $row_nama_lot['lot'] . ' : ' . $row_nama_lot['no_id'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_nama_barang'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['cari_nama_barang']);

    $hasil = "";
    $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan_material WHERE id_bahan_material LIKE '%" . $nama_barang . "%' ORDER BY id_bahan_material ASC LIMIT 50");
    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
        $hasil = $hasil . '
            <option value="' . $row_barang['id_bahan_material'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['add_list_3'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['add_list_3']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $nama_material = mysqli_real_escape_string($conn, $_POST['nama_material']);
    $qty_material = mysqli_real_escape_string($conn, $_POST['qty_material']);
    $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    $oleh = mysqli_real_escape_string($conn, $_POST['oleh']);
    // $id_bahan_kain = mysqli_real_escape_string($conn, $_POST['id_bahan_kain']);
    // $shrinkage = mysqli_real_escape_string($conn, $_POST['shrinkage']);

    // $select_barang_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_bahan_kain FROM tb_barang_masuk WHERE no_id = '" . $barcode . "'"));
    // $id_bahan = $select_barang_masuk['id_bahan_kain'];

    $query_wo = mysqli_query($conn, "SELECT * FROM tb_cutting WHERE id_transaksi = '" . $id_transaksi . "'");
    $jum = mysqli_num_rows($query_wo);
    $row_query_wo = mysqli_fetch_assoc($query_wo);

    if ($jum > 0 && $jum < 2 && $row_query_wo['id_bahan_material'] == "") {
        $update = "UPDATE tb_cutting SET
        id_bahan_material = '" . $nama_material . "',
        qty_material = '" . $qty_material . "',
        berat = '" . $berat . "',
        oleh = '" . $oleh . "'
        WHERE id_transaksi = '" . $id_transaksi . "'
    ";
        $query = mysqli_query($conn, $update);
    } else {
        $sql_update_barang_keluar = mysqli_query($conn, "INSERT INTO tb_cutting(
            id_transaksi,
            id_bahan_material,
            qty_material,
            berat,
            oleh
        ) VALUES(
            '" . $id_transaksi . "',
            '" . $nama_material . "',
            '" . $qty_material . "',
            '" . $berat . "',
            '" . $oleh . "'
    
        )");
    }


    $hasil = "";


    $hasil = $hasil . '
    <h6>RESULT MATERIAL</h6>
    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">BUNDLE</th>
                    <th class="text-center">Nama Item</th>
                    <th class="text-center">QTY</th>
                    <th class="text-center">BERAT</th>
                    <th class="text-center">OLEH</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
            ';

    $no_id = "";
    $id_bahan_material = "";
    $total_qty_materail = 0;
    $berat_materail = 0;
    $no = 1;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_cutting WHERE id_transaksi = '" . $id_transaksi . "'");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $no_id = $row['no_id'];
        $id_bahan_material = $row['id_bahan_material'];
        $q_material = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_material WHERE id_bahan_material = '" . $row['id_bahan_material'] . "'"));
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center">' . $no++ . '</td>
                        <td class="text-center">' . $q_material['id_bahan_material'] .'-'. $q_material['nama_item'].'</td>
                        <td class="text-center">' . $row['qty_material'] . '</td>
                        <td class="text-center">' . $row['berat'] . ' Kg</td>
                        <td class="text-center">' . $row['oleh'] . '</td>
                        <td class="text-center" >
                            <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                ';
        // <td class="text-center" >
        // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
        // </td>
        $total_qty_materail += $row['qty_material'];
        $berat_materail += $row['berat'];
    }
    // <td class="text-center" style="color:red"></td>
    $hasil = $hasil . '
                <tr >
                    <td class="text-center" style="color:red">Total</td>
                    <td class="text-center" style="color:red"></td>
                    <td class="text-center" style="color:red">' . $total_qty_materail . '</td>
                    <td class="text-center" style="color:red">' . $berat_materail . ' Kg</td>
                    <td class="text-center" style="color:red"></td>
                    <td class="text-center" style="color:red"></td>
                    
                </tr>
                <tr >
                <td class="text-center"></td>
                    <td class="text-center">
                        <input type="text" name="nama_material_' . $no_id . '" id="" class="form-control form-control-sm nama_material nama_material_' . $no_id . '" autocomplete="off" list="list_barang" onclick="this.select()" >
                        <datalist id="list_barang" class="list_barang">
                        </datalist>
                    </td>
                    <td class="text-center"><input type="number" name="qty_material_' . $no_id . '" class="form-control form-control-sm qty_material_' . $no_id . '" ></td>
                    <td class="text-center"><input type="number" name="berat_' . $no_id . '" class="form-control form-control-sm berat_' . $no_id . '" ></td>
                    <td class="text-center"><input type="text" name="oleh_' . $no_id . '" class="form-control form-control-sm oleh_' . $no_id . '" ></td>
                    <td class="text-center">
                        <a href="javascript:void(0);"  class="btn btn-xs btn-success add_list_3 add_list_3_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_material="' . $id_bahan_material . '"><i class="fa fa-plus"></i></a>
                    </td>
                </tr>
            ';
    // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
    $hasil = $hasil . '
            </tbody>
        </table>
    ';

    echo $hasil;
}

if (isset($_POST['id_hapus'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $delete = "DELETE FROM tb_cutting WHERE no_id = '" . $id_hapus . "'";
    $query = mysqli_query($conn, $delete);

    if ($query) {
        $valid = 1;
    }

    if ($valid == 1) {

        if ($id_hapus_transaksi == "new") {
            $filter = $_SESSION['id_user'];
        } else {
            $filter = $id_hapus_transaksi;
        }

        $hasil = $hasil . '
        <h6>RESULT MATERIAL</h6>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">BUNDLE</th>
                        <th class="text-center">Nama Item</th>
                        <th class="text-center">QTY</th>
                        <th class="text-center">BERAT</th>
                        <th class="text-center">OLEH</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                ';

        $no_id = "";
        $id_bahan_material = "";
        $total_qty_materail = 0;
        $berat_materail = 0;
        $no = 1;
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_cutting WHERE id_transaksi = '" . $filter . "'");
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $no_id = $row['no_id'];
            $id_bahan_material = $row['id_bahan_material'];
            $q_material = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_material WHERE id_bahan_material = '" . $row['id_bahan_material'] . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center">' . $q_material['id_bahan_material'].'-'.$q_material['nama_item'] .  '</td>
                            <td class="text-center">' . $row['qty_material'] . '</td>
                            <td class="text-center">' . $row['berat'] . ' Kg</td>
                            <td class="text-center">' . $row['oleh'] . '</td>
                            <td class="text-center" >
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                            </td>
                        </tr>
                    ';
            // <td class="text-center" >
            // <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
            // </td>
            $total_qty_materail += $row['qty_material'];
            $berat_materail += $row['berat'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red">' . $total_qty_materail . '</td>
                        <td class="text-center" style="color:red">' . $berat_materail . ' Kg</td>
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red"></td>
                        
                    </tr>
                    <tr >
                    <td class="text-center"></td>
                        <td class="text-center">
                            <input type="text" name="nama_material_' . $no_id . '" id="" class="form-control form-control-sm nama_material nama_material_' . $no_id . '" autocomplete="off" list="list_barang" onclick="this.select()" >
                            <datalist id="list_barang" class="list_barang">
                            </datalist>
                        </td>
                        <td class="text-center"><input type="number" name="qty_material_' . $no_id . '" class="form-control form-control-sm qty_material_' . $no_id . '" ></td>
                        <td class="text-center"><input type="number" name="berat_' . $no_id . '" class="form-control form-control-sm berat_' . $no_id . '" ></td>
                        <td class="text-center"><input type="text" name="oleh_' . $no_id . '" class="form-control form-control-sm oleh_' . $no_id . '" ></td>
                        <td class="text-center">
                            <a href="javascript:void(0);"  class="btn btn-xs btn-success add_list_3 add_list_3_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_material="' . $id_bahan_material . '"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
        $hasil = $hasil . '
                </tbody>
            </table>
        ';
    }
    echo $hasil;
}

if (isset($_POST['add_list'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['add_list']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    // $id_bahan_kain = mysqli_real_escape_string($conn, $_POST['id_bahan_kain']);
    $lot = mysqli_real_escape_string($conn, $_POST['lot']);
    // $shrinkage = mysqli_real_escape_string($conn, $_POST['shrinkage']);

    if ($id_transaksi == "new") {

        $filter = $_SESSION['id_user'];

        // if ($id_tambah != "") {
        //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_work_order WHERE no_id = '" . $id_tambah . "' AND id_bahan_kain = '". $id_bahan_kain ."'"));
        // }else {
        //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_work_order"));
        // }

            $id_bahan = explode(" - ", $nama_kain);
            $id_bahan = $id_bahan[0];

            $no_id = id_gen_work_order();

            $valid = 0;

            $insert = "INSERT INTO tb_work_order(
                no_id,
                id_transaksi,
                id_bahan_kain,
                qty_kain
            ) VALUES(
                '" . $no_id . "',
                '" . $_SESSION['id_user'] . "',
                '" . $id_bahan . "',
                '" . $qty_kain . "'

            )";

            $query = mysqli_query($conn, $insert);


    } 
    else {
        $filter = $id_transaksi;

        // if ($id_tambah != "") {
        //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE no_id = '" . $id_tambah . "' AND id_bahan_kain = '". $id_bahan_kain ."'"));
        //     $lot_masuk = $c_masuk['lot'];
        // }else {
        //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk"));
        // }
            $id_bahan = explode(" - ", $nama_kain);
            $id_bahan = $id_bahan[0];

            $no_id = id_gen_work_order();

            $valid = 0;

            $insert = "INSERT INTO tb_work_order(
                no_id,
                id_transaksi,
                id_bahan_kain,
                qty_kain
            ) VALUES(
                '" . $no_id . "',
                '" . $id_transaksi . "',
                '" . $id_bahan . "',
                '" . $qty_kain . "'

            )";

            $query = mysqli_query($conn, $insert);

    }
    $hasil = '';

    $hasil = $hasil . '
    <h6>BAHAN BAKU</h6>
    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">Nama Kain</th>
                    <th class="text-center">QTY</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            ';
            $no_id = "";
            $id_bahan_kain = "";
            $ksl_qty_kain = 0;
            $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $filter . "' AND id_bahan_kain LIKE '%BHN%' ORDER BY id_bahan_kain ASC");
            while ($row = mysqli_fetch_assoc($select_barang_masuk)) {                                                
                $no_id = $row['no_id'];
                $id_bahan_kain = $row['id_bahan_kain'];
                $qty_kain = $row['qty_kain'];
                $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
                $hasil = $hasil . '
                    <tr >
                        <td class="text-center" >'.$q_kain['jenis_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] .'</td>
                        <td class="text-center" >'. $qty_kain .' ROLL</td>
                        <td class="text-center" >
                            <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                ';
                $ksl_qty_kain += $qty_kain;
            }
            $hasil = $hasil . '
                <tr >
                    <td class="text-center" style="color:red">Total</td>
                    <td class="text-center" style="color:red">'. $ksl_qty_kain .' ROLL</td>
                    <td class="text-center" style="color:red"></td>
                </tr>
                <tr>
                    <td class="text-center">
                        <input type="text" name="nama_kain_'.$no_id.'" id="" class="form-control form-control-sm nama_kain nama_kain_'.$no_id.'" autocomplete="off" list="list_barang" onclick="this.select()" >
                        <datalist id="list_barang" class="list_barang">
                        </datalist>
                    </td>
                    <td class="text-center"><input type="text" name="qty_kain_'.$no_id.'" class="form-control form-control-sm qty_kain_'.$no_id.'" ></td>
                    <td class="text-center">
                        <a href="javascript:void(0);" class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '"><i class="fa fa-plus"></i></a>
                    </td>
                </tr>
            ';
            // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
            $hasil = $hasil . '
            </tbody>
        </table>
    ';
    echo $hasil;
}

if (isset($_POST['ubah_data_barang_tembak'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_barang_tembak']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    // $bolong = mysqli_real_escape_string($conn, $_POST['bolong']);
    // $kotor = mysqli_real_escape_string($conn, $_POST['kotor']);
    // $patah = mysqli_real_escape_string($conn, $_POST['patah']);
    // $belang = mysqli_real_escape_string($conn, $_POST['belang']);
    // $garis = mysqli_real_escape_string($conn, $_POST['garis']);
    // $uom = mysqli_real_escape_string($conn, $_POST['uom']);
    // $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    // $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    // $ttl_subtotal = 0;
    // $sql_get_ttl_subtotal = mysqli_query($conn, "SELECT harga FROM tb_barang_keluar WHERE no_id = '" . $no_id . "'");
    // if ($row_ttl_subtotal = mysqli_fetch_array($sql_get_ttl_subtotal)) {
    //     $ttl_subtotal = $row_ttl_subtotal['harga'];
    // }

    // $subtotal = ($qty * $ttl_subtotal);
    if (isset($_POST['qty'])) {
        $qty = mysqli_real_escape_string($conn, $_POST['qty']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_work_order
            SET
                qty = '" . $qty . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['b_body'])) {
        $b_body = mysqli_real_escape_string($conn, $_POST['b_body']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_work_order
            SET
                b_body = '" . $b_body . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['b_tangan'])) {
        $b_tangan = mysqli_real_escape_string($conn, $_POST['b_tangan']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_work_order
            SET
            b_tangan = '" . $b_tangan . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['bs'])) {
        $bs = mysqli_real_escape_string($conn, $_POST['bs']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_work_order
            SET
            bs = '" . $bs . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $hasil = $hasil . '
    <h6>RENCANA PENGERJAAN</h6>
    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">Nama Item</th>
                    <th class="text-center">Rasio</th>
                    <th class="text-center">QTY</th>
                    <th class="text-center">Bun.Body</th>
                    <th class="text-center">Bun.Tangan</th>
                    <th class="text-center">BS</th>
                </tr>
            </thead>
            <tbody>
            ';
    $no_id = "";
    $id_sku = "";
    $ksl_qty_sku = 0;
    $ksl_qty = 0;
    $ksl_qty_bb = 0;
    $ksl_qty_bt = 0;
    $ksl_qty_bs = 0;
    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku LIKE '%SKU%' ORDER BY id_sku ASC");
    while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
        $no_id = $row['no_id'];
        $id_sku = $row['id_sku'];
        $qty_sku = $row['qty_sku'];
        $rasio_real = $row['rasio_real'];
        $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                        <td class="text-center" >' . $qty_sku . '</td>
                        <td class="text-center"><input type="number" style="display:inline;width:70px" name="qty_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak qty_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row['qty'] . '" ></td>
                        <td class="text-center"><input type="number" style="display:inline;width:70px" name="b_body_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak b_body_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row['b_body'] . '" ></td>
                        <td class="text-center"><input type="number" style="display:inline;width:70px" name="b_tangan_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak b_tangan_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row['b_tangan'] . '" ></td>
                        <td class="text-center"><input type="number" style="display:inline;width:70px" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row['bs'] . '" ></td>
                    </tr>
                ';
        // <td class="text-center" >
        //     <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
        // </td>
        $ksl_qty_sku += $qty_sku;
        $ksl_qty += $row['qty'];
        $ksl_qty_bb += $row['b_body'];
        $ksl_qty_bt += $row['b_tangan'];
        $ksl_qty_bs += $row['bs'];
    }
    $hasil = $hasil . '
        <tr >
            <td class="text-center" style="color:red">Total</td>
            <td class="text-center" style="color:red">' . $ksl_qty_sku . '</td>
            <td class="text-center" style="color:red">' . $ksl_qty . '</td>
            <td class="text-center" style="color:red">' . $ksl_qty_bb . '</td>
            <td class="text-center" style="color:red">' . $ksl_qty_bt . '</td>
            <td class="text-center" style="color:red">' . $ksl_qty_bs . '</td>
        </tr>
    ';
    // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
    $hasil = $hasil . '
            </tbody>
        </table>
    ';

    echo $hasil;
}

if (isset($_POST['ubah_data_qty_sp'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_qty_sp']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    // $bolong = mysqli_real_escape_string($conn, $_POST['bolong']);
    // $kotor = mysqli_real_escape_string($conn, $_POST['kotor']);
    // $patah = mysqli_real_escape_string($conn, $_POST['patah']);
    // $belang = mysqli_real_escape_string($conn, $_POST['belang']);
    // $garis = mysqli_real_escape_string($conn, $_POST['garis']);
    // $uom = mysqli_real_escape_string($conn, $_POST['uom']);
    // $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    // $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    // $ttl_subtotal = 0;
    // $sql_get_ttl_subtotal = mysqli_query($conn, "SELECT harga FROM tb_barang_keluar WHERE no_id = '" . $no_id . "'");
    // if ($row_ttl_subtotal = mysqli_fetch_array($sql_get_ttl_subtotal)) {
    //     $ttl_subtotal = $row_ttl_subtotal['harga'];
    // }

    // $subtotal = ($qty * $ttl_subtotal);
    // if (isset($_POST['qty'])) {
    $qty_sp = mysqli_real_escape_string($conn, $_POST['qty_sp']);
    $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_work_order
            SET
                qty_sp = '" . $qty_sp . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    // }

    $hasil = "";

    $hasil = $hasil . '
    <h6>SISA POTONG</h6>
    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">BAHAN KAIN</th>
                    <th class="text-center">QTY</th>
                </tr>
            </thead>
            <tbody>
            ';
    $no_id = "";
    $id_sku = "";
    $total_qty_sp = 0;
    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain LIKE '%BHN%' ORDER BY id_bahan_kain ASC");
    while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
        $no_id = $row['no_id'];
        $id_bahan_kain = $row['id_bahan_kain'];
        $qty_sp = $row['qty_sp'];
        $q_kain_sisa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" >' . $q_kain_sisa['jenis_kain'] . ' - ' . $q_kain_sisa['warna'] . ' - ' . $q_kain_sisa['setting'] . ' - ' . $q_kain_sisa['gramasi'] . '</td>
                        <td class="text-center"><input type="number" name="qty_sp_' . $no_id . '" style="display:inline;width:65px" class="form-control form-control-sm ubah_data_qty_sp qty_sp_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $qty_sp . '"></td>
                    </tr>
                ';
        // <td class="text-center" >
        //     <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
        // </td>
        $total_qty_sp += $qty_sp;
    }
    $hasil = $hasil . '
                <tr >
                    <td class="text-center" style="color:red">Total</td>
                    <td class="text-center" style="color:red">' . $total_qty_sp . ' Kg</td>
                </tr>
            </tbody>
        </table>
    ';

    echo $hasil;
}

if (isset($_POST['ubah_data_qty_bsp'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_qty_bsp']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    // $bolong = mysqli_real_escape_string($conn, $_POST['bolong']);
    // $kotor = mysqli_real_escape_string($conn, $_POST['kotor']);
    // $patah = mysqli_real_escape_string($conn, $_POST['patah']);
    // $belang = mysqli_real_escape_string($conn, $_POST['belang']);
    // $garis = mysqli_real_escape_string($conn, $_POST['garis']);
    // $uom = mysqli_real_escape_string($conn, $_POST['uom']);
    // $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    // $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    // $ttl_subtotal = 0;
    // $sql_get_ttl_subtotal = mysqli_query($conn, "SELECT harga FROM tb_barang_keluar WHERE no_id = '" . $no_id . "'");
    // if ($row_ttl_subtotal = mysqli_fetch_array($sql_get_ttl_subtotal)) {
    //     $ttl_subtotal = $row_ttl_subtotal['harga'];
    // }

    // $subtotal = ($qty * $ttl_subtotal);
    // if (isset($_POST['qty'])) {
    $qty_bsp = mysqli_real_escape_string($conn, $_POST['qty_bsp']);
    $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_work_order
            SET
                qty_bsp = '" . $qty_bsp . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    // }

    $hasil = "";

    $hasil = $hasil . '
    <h6>BS POTONG</h6>
    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">BAHAN KAIN</th>
                    <th class="text-center">QTY</th>
                </tr>
            </thead>
            <tbody>
            ';
    $no_id = "";
    $id_sku = "";
    $total_qty_bsp = 0;
    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku LIKE '%SKU%' ORDER BY id_sku ASC");
    while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
        $no_id = $row['no_id'];
        $id_sku = $row['id_sku'];
        $qty_bsp = $row['qty_bsp'];
        $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                        <td class="text-center"><input type="number" name="qty_bsp_' . $no_id . '" style="display:inline;width:65px" class="form-control form-control-sm ubah_data_qty_bsp qty_bsp_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $qty_bsp . '"></td>
                    </tr>
                ';
        // <td class="text-center" >
        //     <a href="javascript:void(0);" '. $hide .' class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
        // </td>
        $total_qty_bsp += $qty_bsp;
    }
    $hasil = $hasil . '
                <tr >
                    <td class="text-center" style="color:red">Total</td>
                    <td class="text-center" style="color:red">' . $total_qty_bsp . '</td>
                </tr>
            </tbody>
        </table>
    ';

    echo $hasil;
}
