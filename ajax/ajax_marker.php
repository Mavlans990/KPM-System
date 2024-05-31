<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
session_start();

if (isset($_POST['add_list'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['add_list']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    // $id_bahan_kain = mysqli_real_escape_string($conn, $_POST['id_bahan_kain']);
    $nama_sku = mysqli_real_escape_string($conn, $_POST['nama_sku']);
    $qty_sku = mysqli_real_escape_string($conn, $_POST['qty_sku']);
    // $shrinkage = mysqli_real_escape_string($conn, $_POST['shrinkage']);

    if ($id_transaksi == "new") {

        $filter = $_SESSION['id_user'];

        // if ($id_tambah != "") {
        //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_work_order WHERE no_id = '" . $id_tambah . "' AND id_bahan_kain = '". $id_bahan_kain ."'"));
        // }else {
        //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_work_order"));
        // }

        $id_bahan = explode(" - ", $nama_sku);
        $id_bahan = $id_bahan[0];

        $no_id = id_gen_work_order();

        $valid = 0;

        $insert = "INSERT INTO tb_work_order(
                no_id,
                id_transaksi,
                id_sku,
                qty_sku
            ) VALUES(
                '" . $no_id . "',
                '" . $_SESSION['id_user'] . "',
                '" . $id_bahan . "',
                '" . $qty_sku . "'

            )";

        $query = mysqli_query($conn, $insert);
    } else {
        $filter = $id_transaksi;

        // if ($id_tambah != "") {
        //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE no_id = '" . $id_tambah . "' AND id_bahan_kain = '". $id_bahan_kain ."'"));
        //     $lot_masuk = $c_masuk['lot'];
        // }else {
        //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk"));
        // }
        $id_bahan = explode(" - ", $nama_sku);
        $id_bahan = $id_bahan[0];

        $no_id = id_gen_work_order();

        $valid = 0;

        $insert = "INSERT INTO tb_work_order(
                no_id,
                id_transaksi,
                id_sku,
                qty_sku
            ) VALUES(
                '" . $no_id . "',
                '" . $id_transaksi . "',
                '" . $id_bahan . "',
                '" . $qty_sku . "'
            )";

        $query = mysqli_query($conn, $insert);
    }
    $hasil = '';

    $hasil = $hasil . '
        <h6>RENCANA PENGERJAAN</h6>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">Nama Item</th>
                        <th class="text-center">Rasio</th>
                        <th class="text-center">Rasio Real</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                ';
    $no_id = "";
    $id_sku = "";
    $ksl_qty_sku = 0;
    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku != ''");
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
                            <td class="text-center" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_rasio_real_kurang rasio_real_k_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-minus"></i></button>
                                <input type="number" name="rasio_real_' . $no_id . '" style="display:inline;width:150px;" class="form-control form-control-sm ubah_data_barang_tembak rasio_real_' . $no_id . ' " id="" data-no_id="' . $no_id . '" value="' . $rasio_real . '" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_rasio_real_tambah rasio_real_t_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                            </td>
                        </tr>
                    ';
        $ksl_qty_sku += $qty_sku;
    }
    $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ksl_qty_sku . '</td>
                        <td class="text-center" style="color:red"></td>
                        <td></td>
                    </tr>
                    <tr class="">
                        <td class="text-center">
                            <input type="text" name="nama_sku_' . $no_id . '" id="" class="form-control form-control-sm nama_sku nama_sku_' . $no_id . '" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                            <datalist id="list_barang_2" class="list_barang_2">
                            </datalist>
                        </td>
                        <td class="text-center"><input type="text" name="qty_sku_' . $no_id . '" class="form-control form-control-sm qty_sku_' . $no_id . '"></td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
                        </td>
                        <td></td>
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




    $delete = "DELETE FROM tb_work_order WHERE no_id = '" . $id_hapus . "'";
    $query = mysqli_query($conn, $delete);

    $hasil = "";

    $hasil = $hasil . '
        <h6>RENCANA PENGERJAAN</h6>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">Nama Item</th>
                        <th class="text-center">Rasio</th>
                        <th class="text-center">Rasio Real</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                ';
    $no_id = "";
    $id_sku = "";
    $ksl_qty_sku = 0;
    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_hapus_transaksi . "' AND id_sku != ''");
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
                            <td class="text-center" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_rasio_real_kurang rasio_real_k_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-minus"></i></button>
                                <input type="number" name="rasio_real_' . $no_id . '" style="display:inline;width:150px;" class="form-control form-control-sm ubah_data_barang_tembak rasio_real_' . $no_id . ' " id="" data-no_id="' . $no_id . '" value="' . $rasio_real . '" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_rasio_real_tambah rasio_real_t_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                            </td>
                        </tr>
                    ';
        $ksl_qty_sku += $qty_sku;
    }
    $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ksl_qty_sku . '</td>
                        <td class="text-center" style="color:red"></td>
                        <td></td>
                    </tr>
                    <tr class="">
                        <td class="text-center">
                            <input type="text" name="nama_sku_' . $no_id . '" id="" class="form-control form-control-sm nama_sku nama_sku_' . $no_id . '" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                            <datalist id="list_barang_2" class="list_barang_2">
                            </datalist>
                        </td>
                        <td class="text-center"><input type="text" name="qty_sku_' . $no_id . '" class="form-control form-control-sm qty_sku_' . $no_id . '"></td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
                        </td>
                        <td></td>
                    </tr>
                ';
    // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
    $hasil = $hasil . '
                </tbody>
            </table>
        ';

    echo $hasil;
}

if (isset($_POST['ubah_data_rasio_real_kurang'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_rasio_real_kurang']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    // $type = mysqli_real_escape_string($conn, $_POST['type']);
    // $uom = mysqli_real_escape_string($conn, $_POST['uom']);
    // $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    // $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    // $ttl_subtotal = 0;
    // $sql_get_ttl_subtotal = mysqli_query($conn, "SELECT harga FROM tb_barang_keluar WHERE no_id = '" . $no_id . "'");
    // if ($row_ttl_subtotal = mysqli_fetch_array($sql_get_ttl_subtotal)) {
    //     $ttl_subtotal = $row_ttl_subtotal['harga'];
    // }

    // $subtotal = ($qty * $ttl_subtotal);

    $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT rasio_real FROM tb_work_order WHERE no_id = '" . $no_id . "'"));
    $rasio_real = $sql['rasio_real'] - 1;
    $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_work_order
            SET
            rasio_real = '" . $rasio_real . "'
            WHERE
                no_id = '" . $no_id . "'
        ");

    $hasil = "";

    $hasil = $hasil . '
        <h6>RENCANA PENGERJAAN</h6>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">Nama Item</th>
                        <th class="text-center">Rasio</th>
                        <th class="text-center">Rasio Real</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                ';
    $no_id = "";
    $id_sku = "";
    $ksl_qty_sku = 0;
    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku != ''");
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
                            <td class="text-center" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_rasio_real_kurang rasio_real_k_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-minus"></i></button>
                                <input type="number" name="rasio_real_' . $no_id . '" style="display:inline;width:150px;" class="form-control form-control-sm ubah_data_barang_tembak rasio_real_' . $no_id . ' " id="" data-no_id="' . $no_id . '" value="' . $rasio_real . '" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_rasio_real_tambah rasio_real_t_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                            </td>
                        </tr>
                    ';
        $ksl_qty_sku += $qty_sku;
    }
    $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ksl_qty_sku . '</td>
                        <td class="text-center" style="color:red"></td>
                        <td></td>
                    </tr>
                    <tr class="">
                        <td class="text-center">
                            <input type="text" name="nama_sku_' . $no_id . '" id="" class="form-control form-control-sm nama_sku nama_sku_' . $no_id . '" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                            <datalist id="list_barang_2" class="list_barang_2">
                            </datalist>
                        </td>
                        <td class="text-center"><input type="text" name="qty_sku_' . $no_id . '" class="form-control form-control-sm qty_sku_' . $no_id . '"></td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
                        </td>
                        <td></td>
                    </tr>
                ';
    // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
    $hasil = $hasil . '
                </tbody>
            </table>
        ';

    echo $hasil;
}

if (isset($_POST['ubah_data_rasio_real_tambah'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_rasio_real_tambah']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    // $type = mysqli_real_escape_string($conn, $_POST['type']);
    // $uom = mysqli_real_escape_string($conn, $_POST['uom']);
    // $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    // $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    // $ttl_subtotal = 0;
    // $sql_get_ttl_subtotal = mysqli_query($conn, "SELECT harga FROM tb_barang_keluar WHERE no_id = '" . $no_id . "'");
    // if ($row_ttl_subtotal = mysqli_fetch_array($sql_get_ttl_subtotal)) {
    //     $ttl_subtotal = $row_ttl_subtotal['harga'];
    // }

    // $subtotal = ($qty * $ttl_subtotal);

    $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT rasio_real FROM tb_work_order WHERE no_id = '" . $no_id . "'"));
    $rasio_real = $sql['rasio_real'] + 1;
    $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_work_order
            SET
            rasio_real = '" . $rasio_real . "'
            WHERE
                no_id = '" . $no_id . "'
        ");

    $hasil = "";

    $hasil = $hasil . '
        <h6>RENCANA PENGERJAAN</h6>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">Nama Item</th>
                        <th class="text-center">Rasio</th>
                        <th class="text-center">Rasio Real</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                ';
    $no_id = "";
    $id_sku = "";
    $ksl_qty_sku = 0;
    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku != ''");
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
                            <td class="text-center" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_rasio_real_kurang rasio_real_k_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-minus"></i></button>
                                <input type="number" name="rasio_real_' . $no_id . '" style="display:inline;width:150px;" class="form-control form-control-sm ubah_data_barang_tembak rasio_real_' . $no_id . ' " id="" data-no_id="' . $no_id . '" value="' . $rasio_real . '" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_rasio_real_tambah rasio_real_t_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                            </td>
                        </tr>
                    ';
        $ksl_qty_sku += $qty_sku;
    }
    $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ksl_qty_sku . '</td>
                        <td class="text-center" style="color:red"></td>
                        <td></td>
                    </tr>
                    <tr class="">
                        <td class="text-center">
                            <input type="text" name="nama_sku_' . $no_id . '" id="" class="form-control form-control-sm nama_sku nama_sku_' . $no_id . '" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                            <datalist id="list_barang_2" class="list_barang_2">
                            </datalist>
                        </td>
                        <td class="text-center"><input type="text" name="qty_sku_' . $no_id . '" class="form-control form-control-sm qty_sku_' . $no_id . '"></td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
                        </td>
                        <td></td>
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
    // $type = mysqli_real_escape_string($conn, $_POST['type']);
    $rasio_real = mysqli_real_escape_string($conn, $_POST['rasio_real']);
    // $uom = mysqli_real_escape_string($conn, $_POST['uom']);
    // $berat = mysqli_real_escape_string($conn, $_POST['berat']);
    // $harga = mysqli_real_escape_string($conn, $_POST['harga']);

    // $ttl_subtotal = 0;
    // $sql_get_ttl_subtotal = mysqli_query($conn, "SELECT harga FROM tb_barang_keluar WHERE no_id = '" . $no_id . "'");
    // if ($row_ttl_subtotal = mysqli_fetch_array($sql_get_ttl_subtotal)) {
    //     $ttl_subtotal = $row_ttl_subtotal['harga'];
    // }

    // $subtotal = ($qty * $ttl_subtotal);

    $sql_update_barang_keluar = mysqli_query($conn, "
        UPDATE
            tb_work_order
        SET
            rasio_real = '" . $rasio_real . "'
        WHERE
            no_id = '" . $no_id . "'
    ");

    $hasil = "";

    $hasil = $hasil . '
        <h6>RENCANA PENGERJAAN</h6>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">Nama Item</th>
                        <th class="text-center">Rasio</th>
                        <th class="text-center">Rasio Real</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                ';
    $no_id = "";
    $id_sku = "";
    $ksl_qty_sku = 0;
    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_sku != ''");
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
                            <td class="text-center" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_rasio_real_kurang rasio_real_k_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-minus"></i></button>
                                <input type="number" name="rasio_real_' . $no_id . '" style="display:inline;width:150px;" class="form-control form-control-sm ubah_data_barang_tembak rasio_real_' . $no_id . ' " id="" data-no_id="' . $no_id . '" value="' . $rasio_real . '" >
                                <button href="javascript:void(0);" type="button" name="rasio_real_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_rasio_real_tambah rasio_real_t_' . $no_id . '" data-no_id="' . $no_id . '" ><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                            </td>
                        </tr>
                    ';
        $ksl_qty_sku += $qty_sku;
    }
    $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ksl_qty_sku . '</td>
                        <td class="text-center" style="color:red"></td>
                        <td></td>
                    </tr>
                    <tr class="">
                        <td class="text-center">
                            <input type="text" name="nama_sku_' . $no_id . '" id="" class="form-control form-control-sm nama_sku nama_sku_' . $no_id . '" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                            <datalist id="list_barang_2" class="list_barang_2">
                            </datalist>
                        </td>
                        <td class="text-center"><input type="text" name="qty_sku_' . $no_id . '" class="form-control form-control-sm qty_sku_' . $no_id . '"></td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
                        </td>
                        <td></td>
                    </tr>
                ';
    // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
    $hasil = $hasil . '
                </tbody>
            </table>
        ';

    echo $hasil;
}
