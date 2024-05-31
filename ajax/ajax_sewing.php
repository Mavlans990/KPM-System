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

if (isset($_POST['cari_nama_pic'])) {
    $nama_pic = mysqli_real_escape_string($conn, $_POST['cari_nama_pic']);

    $hasil = "";
    $sql_get_pic = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE pic LIKE '%" . $nama_pic . "%' GROUP BY pic ORDER BY pic ASC LIMIT 50");
    while ($row_pic = mysqli_fetch_array($sql_get_pic)) {
        $hasil = $hasil . '
            <option value="' . $row_pic['pic'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_nama_line'])) {
    $nama_line = mysqli_real_escape_string($conn, $_POST['cari_nama_line']);

    $hasil = "";
    // $sql_get_line = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE pic LIKE '%" . $nama_line . "%' GROUP BY pic ORDER BY pic ASC LIMIT 50");
    $sql_get_line = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE line LIKE '%" . $nama_line . "%' GROUP BY line ORDER BY line ASC LIMIT 50");
    while ($row_line = mysqli_fetch_array($sql_get_line)) {
        $hasil = $hasil . '
            <option value="' . $row_line['line'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_nama_wo'])) {
    $nama_wo = mysqli_real_escape_string($conn, $_POST['cari_nama_wo']);

    $hasil = "";
    $sql_get_wo = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi LIKE '%" . $nama_wo . "%' GROUP BY id_transaksi ORDER BY id_transaksi ASC LIMIT 50");
    while ($row_wo = mysqli_fetch_array($sql_get_wo)) {
        $hasil = $hasil . '
            <option value="' . $row_wo['id_transaksi'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['id_hapus'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $delete = "DELETE FROM tb_sewing WHERE id_sewing = '" . $id_hapus . "'";
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

        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $filter . "' GROUP BY id_sewing");
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $id_sewing = $row['id_sewing'];
            $tgl_sewing = $row['tgl_sewing'];
            $jam_mulai = $row['jam_mulai'];
            $jam_selesai = $row['jam_selesai'];
            $line = $row['line'];
            $anggota = $row['anggota'];
            $pic = $row['pic'];
            $hasil = $hasil . '
            <br>
            <div class="row" style="margin-right:0px;">
                <div class="col-md-5">
                    <div class="row no-gutter">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL SEWING</span>
                                </div>
                                <input type="date" name="tgl_sewing" id="" class="form-control form-control-sm ubah_data_input tgl_sewing_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '"  value="' . $tgl_sewing . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                                </div>
                                <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_mulai . '"  required>
                                <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_selesai . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LINE SEWING</span>
                                </div>
                                <input type="number" name="line" id="" class="form-control form-control-sm ubah_data_input line_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $line . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                                </div>
                                <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $anggota . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                                </div>
                                <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $pic . '"  required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" style="margin-top:196px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
                </div>
            </div>
            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">ITEM BARANG jADI</th>
                            <th class="text-center">QTY</th>
                            <th class="text-center">BS</th>
                            <th class="text-center">TURUN SIZE</th>
                            <th class="text-center">BTJ</th>
                            <th class="text-center">KET</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

            $no_id = "";
            $ttl_qty_jadi = 0;
            $ttl_bs = 0;
            $ttl_turun = 0;
            $ttl_btj = 0;
            $no = 1;
            $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $filter . "' AND id_sewing = '" . $id_sewing . "'");
            while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                $no_id = $row2['no_id'];
                $id_sku = $row2['id_sku'];
                $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="qty_jadi_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang qty_jadi_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="qty_jadi_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak qty_jadi_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_jadi'] . '">
                                    <button href="javascript:void(0);" type="button" name="qty_jadi_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah qty_jadi_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                                </td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                    <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                                </td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                    <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                                </td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="btj_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang btj_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="btj_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak btj_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['btj'] . '">
                                    <button href="javascript:void(0);" type="button" name="btj_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah btj_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                                </td>
                                <td class="text-center">
                                    <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row['ket'] . '</textarea>
                                </td>
                            </tr>
                        ';
                $ttl_qty_jadi += $row2['qty_jadi'];
                $ttl_bs += $row2['bs'];
                $ttl_turun += $row2['turun'];
                $ttl_btj += $row2['btj'];
            }
            // <td class="text-center" style="color:red"></td>
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_qty_jadi . '</td>
                            <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                            <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                            <td class="text-center" style="color:red">' . $ttl_btj . '</td>
                            <td class="text-center" style="color:red"></td>
                        </tr>
                    ';
            // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
            $hasil = $hasil . '
                    </tbody>
                </table>
            ';
        }
        echo $hasil;
    }
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
    if (isset($_POST['qty_jadi'])) {
        $qty_jadi = mysqli_real_escape_string($conn, $_POST['qty_jadi']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                qty_jadi = '" . $qty_jadi . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['bs'])) {
        $bs = mysqli_real_escape_string($conn, $_POST['bs']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                bs = '" . $bs . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['turun'])) {
        $turun = mysqli_real_escape_string($conn, $_POST['turun']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
            turun = '" . $turun . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['btj'])) {
        $btj = mysqli_real_escape_string($conn, $_POST['btj']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
            btj = '" . $btj . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['ket'])) {
        $ket = mysqli_real_escape_string($conn, $_POST['ket']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
            ket = '" . $ket . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_sewing");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_sewing = $row['id_sewing'];
        $tgl_sewing = $row['tgl_sewing'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
        $line = $row['line'];
        $anggota = $row['anggota'];
        $pic = $row['pic'];
        $hasil = $hasil . '
        <br>
        <div class="row" style="margin-right:0px;">
            <div class="col-md-5">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL SEWING</span>
                            </div>
                            <input type="date" name="tgl_sewing" id="" class="form-control form-control-sm ubah_data_input tgl_sewing_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '"  value="' . $tgl_sewing . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LINE SEWING</span>
                            </div>
                            <input type="number" name="line" id="" class="form-control form-control-sm ubah_data_input line_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $line . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" style="margin-top:196px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
        </div>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">ITEM BARANG jADI</th>
                        <th class="text-center">QTY</th>
                        <th class="text-center">BS</th>
                        <th class="text-center">TURUN SIZE</th>
                        <th class="text-center">BTJ</th>
                        <th class="text-center">KET</th>
                    </tr>
                </thead>
                <tbody>
                ';

        $no_id = "";
        $ttl_qty_jadi = 0;
        $ttl_bs = 0;
        $ttl_turun = 0;
        $ttl_btj = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' AND id_sewing = '" . $id_sewing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="qty_jadi_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang qty_jadi_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="qty_jadi_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak qty_jadi_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_jadi'] . '">
                                <button href="javascript:void(0);" type="button" name="qty_jadi_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah qty_jadi_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="btj_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang btj_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="btj_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak btj_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['btj'] . '">
                                <button href="javascript:void(0);" type="button" name="btj_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah btj_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row['ket'] . '</textarea>
                            </td>
                        </tr>
                    ';
            $ttl_qty_jadi += $row2['qty_jadi'];
            $ttl_bs += $row2['bs'];
            $ttl_turun += $row2['turun'];
            $ttl_btj += $row2['btj'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ttl_qty_jadi . '</td>
                        <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                        <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                        <td class="text-center" style="color:red">' . $ttl_btj . '</td>
                        <td class="text-center" style="color:red"></td>
                    </tr>
                ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
                </tbody>
            </table>
        ';
    }

    echo $hasil;
}

if (isset($_POST['ubah_data_barang_kurang'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_barang_kurang']);
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
    if (isset($_POST['qty_jadi'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT qty_jadi FROM tb_sewing WHERE no_id = '" . $no_id . "'"));
        $qty_jadi = $sql['qty_jadi'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                qty_jadi = '" . $qty_jadi . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['bs'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bs FROM tb_sewing WHERE no_id = '" . $no_id . "'"));
        $bs = $sql['bs'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                bs = '" . $bs . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['turun'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT turun FROM tb_sewing WHERE no_id = '" . $no_id . "'"));
        $turun = $sql['turun'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
            turun = '" . $turun . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['btj'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT btj FROM tb_sewing WHERE no_id = '" . $no_id . "'"));
        $btj = $sql['btj'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
            btj = '" . $btj . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_sewing");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_sewing = $row['id_sewing'];
        $tgl_sewing = $row['tgl_sewing'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
        $line = $row['line'];
        $anggota = $row['anggota'];
        $pic = $row['pic'];
        $hasil = $hasil . '
        <br>
        <div class="row" style="margin-right:0px;">
            <div class="col-md-5">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL SEWING</span>
                            </div>
                            <input type="date" name="tgl_sewing" id="" class="form-control form-control-sm ubah_data_input tgl_sewing_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '"  value="' . $tgl_sewing . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LINE SEWING</span>
                            </div>
                            <input type="number" name="line" id="" class="form-control form-control-sm ubah_data_input line_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $line . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" style="margin-top:196px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
        </div>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">ITEM BARANG jADI</th>
                        <th class="text-center">QTY</th>
                        <th class="text-center">BS</th>
                        <th class="text-center">TURUN SIZE</th>
                        <th class="text-center">BTJ</th>
                        <th class="text-center">KET</th>
                    </tr>
                </thead>
                <tbody>
                ';

        $no_id = "";
        $ttl_qty_jadi = 0;
        $ttl_bs = 0;
        $ttl_turun = 0;
        $ttl_btj = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' AND id_sewing = '" . $id_sewing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="qty_jadi_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang qty_jadi_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="qty_jadi_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak qty_jadi_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_jadi'] . '">
                                <button href="javascript:void(0);" type="button" name="qty_jadi_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah qty_jadi_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="btj_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang btj_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="btj_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak btj_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['btj'] . '">
                                <button href="javascript:void(0);" type="button" name="btj_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah btj_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row['ket'] . '</textarea>
                            </td>
                        </tr>
                    ';
            $ttl_qty_jadi += $row2['qty_jadi'];
            $ttl_bs += $row2['bs'];
            $ttl_turun += $row2['turun'];
            $ttl_btj += $row2['btj'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ttl_qty_jadi . '</td>
                        <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                        <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                        <td class="text-center" style="color:red">' . $ttl_btj . '</td>
                        <td class="text-center" style="color:red"></td>
                    </tr>
                ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
                </tbody>
            </table>
        ';
    }

    echo $hasil;
}

if (isset($_POST['ubah_data_barang_tambah'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_barang_tambah']);
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
    if (isset($_POST['qty_jadi'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT qty_jadi FROM tb_sewing WHERE no_id = '" . $no_id . "'"));
        $qty_jadi = $sql['qty_jadi'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                qty_jadi = '" . $qty_jadi . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['bs'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bs FROM tb_sewing WHERE no_id = '" . $no_id . "'"));
        $bs = $sql['bs'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                bs = '" . $bs . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['turun'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT turun FROM tb_sewing WHERE no_id = '" . $no_id . "'"));
        $turun = $sql['turun'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
            turun = '" . $turun . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['btj'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT btj FROM tb_sewing WHERE no_id = '" . $no_id . "'"));
        $btj = $sql['btj'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
            btj = '" . $btj . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_sewing");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_sewing = $row['id_sewing'];
        $tgl_sewing = $row['tgl_sewing'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
        $line = $row['line'];
        $anggota = $row['anggota'];
        $pic = $row['pic'];
        $hasil = $hasil . '
        <br>
        <div class="row" style="margin-right:0px;">
            <div class="col-md-5">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL SEWING</span>
                            </div>
                            <input type="date" name="tgl_sewing" id="" class="form-control form-control-sm ubah_data_input tgl_sewing_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '"  value="' . $tgl_sewing . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LINE SEWING</span>
                            </div>
                            <input type="number" name="line" id="" class="form-control form-control-sm ubah_data_input line_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $line . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" style="margin-top:196px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
        </div>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">ITEM BARANG jADI</th>
                        <th class="text-center">QTY</th>
                        <th class="text-center">BS</th>
                        <th class="text-center">TURUN SIZE</th>
                        <th class="text-center">BTJ</th>
                        <th class="text-center">KET</th>
                    </tr>
                </thead>
                <tbody>
                ';

        $no_id = "";
        $ttl_qty_jadi = 0;
        $ttl_bs = 0;
        $ttl_turun = 0;
        $ttl_btj = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' AND id_sewing = '" . $id_sewing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="qty_jadi_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang qty_jadi_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="qty_jadi_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak qty_jadi_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_jadi'] . '">
                                <button href="javascript:void(0);" type="button" name="qty_jadi_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah qty_jadi_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="btj_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang btj_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="btj_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak btj_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['btj'] . '">
                                <button href="javascript:void(0);" type="button" name="btj_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah btj_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row['ket'] . '</textarea>
                            </td>
                        </tr>
                    ';
            $ttl_qty_jadi += $row2['qty_jadi'];
            $ttl_bs += $row2['bs'];
            $ttl_turun += $row2['turun'];
            $ttl_btj += $row2['btj'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ttl_qty_jadi . '</td>
                        <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                        <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                        <td class="text-center" style="color:red">' . $ttl_btj . '</td>
                        <td class="text-center" style="color:red"></td>
                    </tr>
                ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
                </tbody>
            </table>
        ';
    }

    echo $hasil;
}

if (isset($_POST['ubah_data_input'])) {
    $id_sewing = mysqli_real_escape_string($conn, $_POST['ubah_data_input']);
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
    if (isset($_POST['tgl_sewing'])) {
        $tgl_sewing = mysqli_real_escape_string($conn, $_POST['tgl_sewing']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                tgl_sewing = '" . $tgl_sewing . "'
            WHERE
                id_sewing = '" . $id_sewing . "'
        ");
    } elseif (isset($_POST['jam_mulai'])) {
        $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                jam_mulai = '" . $jam_mulai . "'
            WHERE
                id_sewing = '" . $id_sewing . "'
        ");
    } elseif (isset($_POST['jam_selesai'])) {
        $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                jam_selesai = '" . $jam_selesai . "'
            WHERE
                id_sewing = '" . $id_sewing . "'
        ");
    } elseif (isset($_POST['line'])) {
        $line = mysqli_real_escape_string($conn, $_POST['line']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                line = '" . $line . "'
            WHERE
                id_sewing = '" . $id_sewing . "'
        ");
    } elseif (isset($_POST['anggota'])) {
        $anggota = mysqli_real_escape_string($conn, $_POST['anggota']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                anggota = '" . $anggota . "'
            WHERE
                id_sewing = '" . $id_sewing . "'
        ");
    } elseif (isset($_POST['pic'])) {
        $pic = mysqli_real_escape_string($conn, $_POST['pic']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_sewing
            SET
                pic = '" . $pic . "'
            WHERE
                id_sewing = '" . $id_sewing . "'
        ");
    }




    $hasil = "";

    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_sewing");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_sewing = $row['id_sewing'];
        $tgl_sewing = $row['tgl_sewing'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
        $line = $row['line'];
        $anggota = $row['anggota'];
        $pic = $row['pic'];
        $hasil = $hasil . '
        <br>
        <div class="row" style="margin-right:0px;">
            <div class="col-md-5">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL SEWING</span>
                            </div>
                            <input type="date" name="tgl_sewing" id="" class="form-control form-control-sm ubah_data_input tgl_sewing_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '"  value="' . $tgl_sewing . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LINE SEWING</span>
                            </div>
                            <input type="number" name="line" id="" class="form-control form-control-sm ubah_data_input line_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $line . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_sewing . '" data-id_sewing="' . $id_sewing . '" style="margin-top:196px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
        </div>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">ITEM BARANG jADI</th>
                        <th class="text-center">QTY</th>
                        <th class="text-center">BS</th>
                        <th class="text-center">TURUN SIZE</th>
                        <th class="text-center">BTJ</th>
                        <th class="text-center">KET</th>
                    </tr>
                </thead>
                <tbody>
                ';

        $no_id = "";
        $ttl_qty_jadi = 0;
        $ttl_bs = 0;
        $ttl_turun = 0;
        $ttl_btj = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE id_transaksi = '" . $id_transaksi . "' AND id_sewing = '" . $id_sewing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="qty_jadi_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang qty_jadi_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="qty_jadi_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak qty_jadi_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_jadi'] . '">
                                <button href="javascript:void(0);" type="button" name="qty_jadi_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah qty_jadi_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="btj_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang btj_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:70px" name="btj_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak btj_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['btj'] . '">
                                <button href="javascript:void(0);" type="button" name="btj_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah btj_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row['ket'] . '</textarea>
                            </td>
                        </tr>
                    ';
            $ttl_qty_jadi += $row2['qty_jadi'];
            $ttl_bs += $row2['bs'];
            $ttl_turun += $row2['turun'];
            $ttl_btj += $row2['btj'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ttl_qty_jadi . '</td>
                        <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                        <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                        <td class="text-center" style="color:red">' . $ttl_btj . '</td>
                        <td class="text-center" style="color:red"></td>
                    </tr>
                ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
                </tbody>
            </table>
        ';
    }

    echo $hasil;
}
