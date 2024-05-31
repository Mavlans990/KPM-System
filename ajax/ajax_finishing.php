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
    $sql_get_pic = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE pic LIKE '%" . $nama_pic . "%' GROUP BY pic ORDER BY pic ASC LIMIT 50");
    while ($row_pic = mysqli_fetch_array($sql_get_pic)) {
        $hasil = $hasil . '
            <option value="' . $row_pic['pic'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_nama_anggota'])) {
    $nama_anggota = mysqli_real_escape_string($conn, $_POST['cari_nama_anggota']);

    $hasil = "";
    // $sql_get_anggota = mysqli_query($conn, "SELECT * FROM tb_sewing WHERE pic LIKE '%" . $nama_anggota . "%' GROUP BY pic ORDER BY pic ASC LIMIT 50");
    $sql_get_anggota = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE anggota LIKE '%" . $nama_anggota . "%' GROUP BY anggota ORDER BY anggota ASC LIMIT 50");
    while ($row_anggota = mysqli_fetch_array($sql_get_anggota)) {
        $hasil = $hasil . '
            <option value="' . $row_anggota['anggota'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_nama_wo'])) {
    $nama_wo = mysqli_real_escape_string($conn, $_POST['cari_nama_wo']);

    $hasil = "";
    $sql_get_wo = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi LIKE '%" . $nama_wo . "%' GROUP BY id_transaksi ORDER BY id_transaksi ASC LIMIT 50");
    while ($row_wo = mysqli_fetch_array($sql_get_wo)) {
        $hasil = $hasil . '
            <option value="' . $row_wo['id_transaksi'] . '">
        ';
    }

    echo $hasil;
}

//TABEL GOSOK
if (isset($_POST['id_hapus_2'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus_2']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $delete = "DELETE FROM tb_finishing WHERE id_finishing = '" . $id_hapus . "' AND tabel = 2";
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
            <h6>TABEL GOSOK</h6>
        ';
        $ksl_qty_bbgl = 0;
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $filter . "' AND tabel = 2 GROUP BY id_finishing");
        $jum_data = mysqli_num_rows($select_barang_masuk);
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $id_finishing = $row['id_finishing'];
            $tgl_qc = $row['tgl_qc'];
            $jam_mulai = $row['jam_mulai'];
            $jam_selesai = $row['jam_selesai'];
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
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL GOSOK</span>
                                </div>
                                <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_2 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                                </div>
                                <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_2 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                                <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_2 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                                </div>
                                <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_2 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                                </div>
                                <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_2 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_2 del_list_2_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
                </div>
                <div class="col-md-7">
                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                        <thead>
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">BB GOSOK</th>
                                <th class="text-center">QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                        ';

            $no_id = "";
            $ttl_qty_bbgl = 0;
            $no = 1;
            $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $filter . "' AND id_finishing = '" . $id_finishing . "'");
            while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                $no_id = $row2['no_id'];
                $id_sku = $row2['id_sku'];
                $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                $hasil = $hasil . '
                                <tr >
                                    <td class="text-center">' . $no++ . '</td>
                                    <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                    <td class="text-center">
                                        <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                        <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                        <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                    </td>
                                </tr>
                            ';
                $ttl_qty_bbgl += $row2['qty_bbgl'];
                $ksl_qty_bbgl += $row2['qty_bbgl'];
            }
            // <td class="text-center" style="color:red"></td>
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center" style="color:red"></td>
                                <td class="text-center" style="color:red">Total</td>
                                <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                            </tr>
                        ';
            // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
            $hasil = $hasil . '
                        </tbody>
                    </table>
                </div>
            </div>
            ';
        }
        if ($jum_data >= 1) {
            $hasil = $hasil . '
        <br>
        <div class="col-sm">
            <div class="row">
                <div class="col-md-6">
                    <div class="row no-gutter">
                        <div class="col-12 px-0">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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
}

if (isset($_POST['ubah_data_barang_tembak_2'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_barang_tembak_2']);
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
    if (isset($_POST['qty_bbgl'])) {
        $qty_bbgl = mysqli_real_escape_string($conn, $_POST['qty_bbgl']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                qty_bbgl = '" . $qty_bbgl . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL GOSOK</h6>
    ';
    $ksl_qty_bbgl = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 2 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL GOSOK</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_2 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_2 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_2 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_2 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_2 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_2 del_list_2_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
            <div class="col-md-7">
                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">BB GOSOK</th>
                            <th class="text-center">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        $no_id = "";
        $ttl_qty_bbgl = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        ';
            $ttl_qty_bbgl += $row2['qty_bbgl'];
            $ksl_qty_bbgl += $row2['qty_bbgl'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                        </tr>
                    ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
        $hasil = $hasil . '
                    </tbody>
                </table>
            </div>
        </div>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 px-0">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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

if (isset($_POST['ubah_data_input_2'])) {
    $id_finishing = mysqli_real_escape_string($conn, $_POST['ubah_data_input_2']);
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
    if (isset($_POST['tgl_qc'])) {
        $tgl_qc = mysqli_real_escape_string($conn, $_POST['tgl_qc']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                tgl_qc = '" . $tgl_qc . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['jam_mulai'])) {
        $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                jam_mulai = '" . $jam_mulai . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['jam_selesai'])) {
        $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                jam_selesai = '" . $jam_selesai . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['anggota'])) {
        $anggota = mysqli_real_escape_string($conn, $_POST['anggota']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                anggota = '" . $anggota . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['pic'])) {
        $pic = mysqli_real_escape_string($conn, $_POST['pic']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                pic = '" . $pic . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    }




    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL GOSOK</h6>
    ';
    $ksl_qty_bbgl = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 2 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL GOSOK</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_2 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_2 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_2 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_2 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_2 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_2 del_list_2_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
            <div class="col-md-7">
                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">BB GOSOK</th>
                            <th class="text-center">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        $no_id = "";
        $ttl_qty_bbgl = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        ';
            $ttl_qty_bbgl += $row2['qty_bbgl'];
            $ksl_qty_bbgl += $row2['qty_bbgl'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                        </tr>
                    ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
        $hasil = $hasil . '
                    </tbody>
                </table>
            </div>
        </div>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 px-0">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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

if (isset($_POST['ubah_data_qty_bbgl_kurang_2'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_qty_bbgl_kurang_2']);
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
    $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT qty_bbgl FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
    $qty_bbgl = $sql['qty_bbgl'] - 1;
    $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
            tb_finishing
            SET
            qty_bbgl = '" . $qty_bbgl . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    // }

    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL GOSOK</h6>
    ';
    $ksl_qty_bbgl = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 2 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL GOSOK</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_2 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_2 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_2 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_2 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_2 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_2 del_list_2_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
            <div class="col-md-7">
                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">BB GOSOK</th>
                            <th class="text-center">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        $no_id = "";
        $ttl_qty_bbgl = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        ';
            $ttl_qty_bbgl += $row2['qty_bbgl'];
            $ksl_qty_bbgl += $row2['qty_bbgl'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                        </tr>
                    ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
        $hasil = $hasil . '
                    </tbody>
                </table>
            </div>
        </div>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 px-0">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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

if (isset($_POST['ubah_data_qty_bbgl_tambah_2'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_qty_bbgl_tambah_2']);
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
    $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT qty_bbgl FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
    $qty_bbgl = $sql['qty_bbgl'] + 1;
    $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
            tb_finishing
            SET
            qty_bbgl = '" . $qty_bbgl . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    // }

    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL GOSOK</h6>
    ';
    $ksl_qty_bbgl = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 2 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL GOSOK</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_2 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_2 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_2 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_2 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_2 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_2 del_list_2_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
            <div class="col-md-7">
                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">BB GOSOK</th>
                            <th class="text-center">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        $no_id = "";
        $ttl_qty_bbgl = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_2 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        ';
            $ttl_qty_bbgl += $row2['qty_bbgl'];
            $ksl_qty_bbgl += $row2['qty_bbgl'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                        </tr>
                    ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
        $hasil = $hasil . '
                    </tbody>
                </table>
            </div>
        </div>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 px-0">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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

//TABEL LIPAT
if (isset($_POST['id_hapus_3'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus_3']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $delete = "DELETE FROM tb_finishing WHERE id_finishing = '" . $id_hapus . "' AND tabel = 3";
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
            <h6>TABEL LIPAT</h6>
        ';
        $ksl_qty_bbgl = 0;
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $filter . "' AND tabel = 3 GROUP BY id_finishing");
        $jum_data = mysqli_num_rows($select_barang_masuk);
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $id_finishing = $row['id_finishing'];
            $tgl_qc = $row['tgl_qc'];
            $jam_mulai = $row['jam_mulai'];
            $jam_selesai = $row['jam_selesai'];
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
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL LIPAT</span>
                                </div>
                                <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_3 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                                </div>
                                <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_3 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                                <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_3 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                                </div>
                                <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_3 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                                </div>
                                <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_3 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_3 del_list_3_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
                </div>
                <div class="col-md-7">
                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                        <thead>
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">BB LIPAT</th>
                                <th class="text-center">QTY</th>
                            </tr>
                        </thead>
                        <tbody>
                        ';

            $no_id = "";
            $ttl_qty_bbgl = 0;
            $no = 1;
            $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $filter . "' AND id_finishing = '" . $id_finishing . "'");
            while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                $no_id = $row2['no_id'];
                $id_sku = $row2['id_sku'];
                $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                $hasil = $hasil . '
                                <tr >
                                    <td class="text-center">' . $no++ . '</td>
                                    <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                    <td class="text-center">
                                        <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                        <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                        <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                    </td>
                                </tr>
                            ';
                $ttl_qty_bbgl += $row2['qty_bbgl'];
                $ksl_qty_bbgl += $row2['qty_bbgl'];
            }
            // <td class="text-center" style="color:red"></td>
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center" style="color:red"></td>
                                <td class="text-center" style="color:red">Total</td>
                                <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                            </tr>
                        ';
            // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
            $hasil = $hasil . '
                        </tbody>
                    </table>
                </div>
            </div>
            ';
        }
        if ($jum_data >= 1) {
            $hasil = $hasil . '
        <br>
        <div class="col-sm">
            <div class="row">
                <div class="col-md-6">
                    <div class="row no-gutter">
                        <div class="col-12 px-0">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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
}

if (isset($_POST['ubah_data_barang_tembak_3'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_barang_tembak_3']);
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
    if (isset($_POST['qty_bbgl'])) {
        $qty_bbgl = mysqli_real_escape_string($conn, $_POST['qty_bbgl']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                qty_bbgl = '" . $qty_bbgl . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL LIPAT</h6>
    ';
    $ksl_qty_bbgl = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 3 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL LIPAT</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_3 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_3 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_3 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_3 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_3 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_3 del_list_3_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
            <div class="col-md-7">
                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">BB LIPAT</th>
                            <th class="text-center">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        $no_id = "";
        $ttl_qty_bbgl = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        ';
            $ttl_qty_bbgl += $row2['qty_bbgl'];
            $ksl_qty_bbgl += $row2['qty_bbgl'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                        </tr>
                    ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
        $hasil = $hasil . '
                    </tbody>
                </table>
            </div>
        </div>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 px-0">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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

if (isset($_POST['ubah_data_input_3'])) {
    $id_finishing = mysqli_real_escape_string($conn, $_POST['ubah_data_input_3']);
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
    if (isset($_POST['tgl_qc'])) {
        $tgl_qc = mysqli_real_escape_string($conn, $_POST['tgl_qc']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                tgl_qc = '" . $tgl_qc . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['jam_mulai'])) {
        $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                jam_mulai = '" . $jam_mulai . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['jam_selesai'])) {
        $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                jam_selesai = '" . $jam_selesai . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['anggota'])) {
        $anggota = mysqli_real_escape_string($conn, $_POST['anggota']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                anggota = '" . $anggota . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['pic'])) {
        $pic = mysqli_real_escape_string($conn, $_POST['pic']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                pic = '" . $pic . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    }




    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL LIPAT</h6>
    ';
    $ksl_qty_bbgl = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 3 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL LIPAT</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_3 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_3 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_3 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_3 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_3 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_3 del_list_3_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
            <div class="col-md-7">
                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">BB LIPAT</th>
                            <th class="text-center">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        $no_id = "";
        $ttl_qty_bbgl = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        ';
            $ttl_qty_bbgl += $row2['qty_bbgl'];
            $ksl_qty_bbgl += $row2['qty_bbgl'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                        </tr>
                    ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
        $hasil = $hasil . '
                    </tbody>
                </table>
            </div>
        </div>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 px-0">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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

if (isset($_POST['ubah_data_qty_bbgl_kurang_3'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_qty_bbgl_kurang_3']);
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
    $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT qty_bbgl FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
    $qty_bbgl = $sql['qty_bbgl'] - 1;
    $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
            tb_finishing
            SET
            qty_bbgl = '" . $qty_bbgl . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    // }

    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL LIPAT</h6>
    ';
    $ksl_qty_bbgl = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 3 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL LIPAT</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_3 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_3 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_3 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_3 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_3 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_3 del_list_3_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
            <div class="col-md-7">
                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">BB LIPAT</th>
                            <th class="text-center">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        $no_id = "";
        $ttl_qty_bbgl = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        ';
            $ttl_qty_bbgl += $row2['qty_bbgl'];
            $ksl_qty_bbgl += $row2['qty_bbgl'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                        </tr>
                    ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
        $hasil = $hasil . '
                    </tbody>
                </table>
            </div>
        </div>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 px-0">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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

if (isset($_POST['ubah_data_qty_bbgl_tambah_3'])) {
    $no_id = mysqli_real_escape_string($conn, $_POST['ubah_data_qty_bbgl_tambah_3']);
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
    $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT qty_bbgl FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
    $qty_bbgl = $sql['qty_bbgl'] + 1;
    $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
            tb_finishing
            SET
            qty_bbgl = '" . $qty_bbgl . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    // }

    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL LIPAT</h6>
    ';
    $ksl_qty_bbgl = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 3 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL LIPAT</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input_3 tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input_3 jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input_3 jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input_3 anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input_3 pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_3 del_list_3_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
            <div class="col-md-7">
                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">BB LIPAT</th>
                            <th class="text-center">QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

        $no_id = "";
        $ttl_qty_bbgl = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_qty_bbgl_kurang_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:70px" name="qty_bbgl_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['qty_bbgl'] . '">
                                    <button href="javascript:void(0);" type="button" name="qty_bbgl_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_qty_bbgl_tambah_3 qty_bbgl_' . $no_id . '" data-no_id="' . $no_id . '"><i class="fa fa-plus"></i></button>
                                </td>
                            </tr>
                        ';
            $ttl_qty_bbgl += $row2['qty_bbgl'];
            $ksl_qty_bbgl += $row2['qty_bbgl'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_qty_bbgl . '</td>
                        </tr>
                    ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
        $hasil = $hasil . '
                    </tbody>
                </table>
            </div>
        </div>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 px-0">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_qty_bbgl . '" readonly>
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

//TABEL QC
if (isset($_POST['id_hapus'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $delete = "DELETE FROM tb_finishing WHERE id_finishing = '" . $id_hapus . "' AND tabel = 1";
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
            <h6>TABEL QC</h6>
        ';
        $ksl_total = 0;
        $ksl_bs = 0;
        $ksl_cuci = 0;
        $ksl_kotor = 0;
        $ksl_turun = 0;
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $filter . "' AND tabel = 1 GROUP BY id_finishing");
        $jum_data = mysqli_num_rows($select_barang_masuk);
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $id_finishing = $row['id_finishing'];
            $tgl_qc = $row['tgl_qc'];
            $jam_mulai = $row['jam_mulai'];
            $jam_selesai = $row['jam_selesai'];
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
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL QC</span>
                                </div>
                                <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                                </div>
                                <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                                <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                                </div>
                                <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                                </div>
                                <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
                </div>
            </div>
            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">NO</th>
                            <th class="text-center">QC</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">BS</th>
                            <th class="text-center">CUCI</th>
                            <th class="text-center">KOTOR</th>
                            <th class="text-center">TURUN SIZE</th>
                            <th class="text-center">KET</th>
                        </tr>
                    </thead>
                    <tbody>
                    ';

            $no_id = "";
            $ttl_total = 0;
            $ttl_bs = 0;
            $ttl_cuci = 0;
            $ttl_kotor = 0;
            $ttl_turun = 0;
            $no = 1;
            $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $filter . "' AND id_finishing = '" . $id_finishing . "'");
            while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                $no_id = $row2['no_id'];
                $id_sku = $row2['id_sku'];
                $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                $hasil = $hasil . '
                            <tr >
                                <td class="text-center">' . $no++ . '</td>
                                <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="total_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang total_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:30%" name="total_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak total_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['total'] . '">
                                    <button href="javascript:void(0);" type="button" name="total_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah total_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                                </td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:30%" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                    <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                                </td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="cuci_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang cuci_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:30%" name="cuci_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak cuci_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['cuci'] . '">
                                    <button href="javascript:void(0);" type="button" name="cuci_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah cuci_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                                </td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['kotor'] . '">
                                    <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                                </td>
                                <td class="text-center">
                                    <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="5"><i class="fa fa-minus"></i></button>
                                    <input type="number" style="display:inline;width:30%" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                    <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                                </td>
                                <td class="text-center">
                                    <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row2['ket'] . '</textarea>
                                </td>
                            </tr>
                        ';
                $ttl_total += $row2['total'];
                $ttl_bs += $row2['bs'];
                $ttl_cuci += $row2['cuci'];
                $ttl_kotor += $row2['kotor'];
                $ttl_turun += $row2['turun'];
                $ksl_total += $row2['total'];
                $ksl_bs += $row2['bs'];
                $ksl_cuci += $row2['cuci'];
                $ksl_kotor += $row2['kotor'];
                $ksl_turun += $row2['turun'];
            }
            // <td class="text-center" style="color:red"></td>
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center" style="color:red"></td>
                            <td class="text-center" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_total . '</td>
                            <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                            <td class="text-center" style="color:red">' . $ttl_cuci . '</td>
                            <td class="text-center" style="color:red">' . $ttl_kotor . '</td>
                            <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                            <td class="text-center" style="color:red"></td>
                        </tr>
                    ';
            // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
            $hasil = $hasil . '
                    </tbody>
                </table>
            ';
        }
        if ($jum_data >= 1) {
            $hasil = $hasil . '
        <br>
        <div class="col-sm">
            <div class="row">
                <div class="col-md-6">
                    <div class="row no-gutter">
                        <div class="col-12 ">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_total . '" readonly>
                            </div>
                        </div>
                        <div class="col-12 ">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BS</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_bs . '" readonly>
                            </div>
                        </div>
                        <div class="col-12 ">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL CUCI</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_cuci . '" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row no-gutter">
                        <div class="col-12 ">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL KOTOR</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_kotor . '" readonly>
                            </div>
                        </div>
                        <div class="col-12 ">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL TURUN SIZE</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_turun . '" readonly>
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
    if (isset($_POST['total'])) {
        $total = mysqli_real_escape_string($conn, $_POST['total']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                total = '" . $total . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['bs'])) {
        $bs = mysqli_real_escape_string($conn, $_POST['bs']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                bs = '" . $bs . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['cuci'])) {
        $cuci = mysqli_real_escape_string($conn, $_POST['cuci']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
            cuci = '" . $cuci . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['kotor'])) {
        $kotor = mysqli_real_escape_string($conn, $_POST['kotor']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
            kotor = '" . $kotor . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['turun'])) {
        $turun = mysqli_real_escape_string($conn, $_POST['turun']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
            turun = '" . $turun . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['ket'])) {
        $ket = mysqli_real_escape_string($conn, $_POST['ket']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
            ket = '" . $ket . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL QC</h6>
    ';
    $ksl_total = 0;
    $ksl_bs = 0;
    $ksl_cuci = 0;
    $ksl_kotor = 0;
    $ksl_turun = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 1 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL QC</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
        </div>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">QC</th>
                        <th class="text-center">TOTAL</th>
                        <th class="text-center">BS</th>
                        <th class="text-center">CUCI</th>
                        <th class="text-center">KOTOR</th>
                        <th class="text-center">TURUN SIZE</th>
                        <th class="text-center">KET</th>
                    </tr>
                </thead>
                <tbody>
                ';

        $no_id = "";
        $ttl_total = 0;
        $ttl_bs = 0;
        $ttl_cuci = 0;
        $ttl_kotor = 0;
        $ttl_turun = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="total_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang total_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="total_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak total_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['total'] . '">
                                <button href="javascript:void(0);" type="button" name="total_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah total_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="cuci_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang cuci_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="cuci_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak cuci_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['cuci'] . '">
                                <button href="javascript:void(0);" type="button" name="cuci_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah cuci_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['kotor'] . '">
                                <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="5"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row2['ket'] . '</textarea>
                            </td>
                        </tr>
                    ';
            $ttl_total += $row2['total'];
            $ttl_bs += $row2['bs'];
            $ttl_cuci += $row2['cuci'];
            $ttl_kotor += $row2['kotor'];
            $ttl_turun += $row2['turun'];
            $ksl_total += $row2['total'];
            $ksl_bs += $row2['bs'];
            $ksl_cuci += $row2['cuci'];
            $ksl_kotor += $row2['kotor'];
            $ksl_turun += $row2['turun'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ttl_total . '</td>
                        <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                        <td class="text-center" style="color:red">' . $ttl_cuci . '</td>
                        <td class="text-center" style="color:red">' . $ttl_kotor . '</td>
                        <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                        <td class="text-center" style="color:red"></td>
                    </tr>
                ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
                </tbody>
            </table>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_total . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BS</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_bs . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL CUCI</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_cuci . '" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL KOTOR</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_kotor . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL TURUN SIZE</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_turun . '" readonly>
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

if (isset($_POST['ubah_data_input'])) {
    $id_finishing = mysqli_real_escape_string($conn, $_POST['ubah_data_input']);
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
    if (isset($_POST['tgl_qc'])) {
        $tgl_qc = mysqli_real_escape_string($conn, $_POST['tgl_qc']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                tgl_qc = '" . $tgl_qc . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['jam_mulai'])) {
        $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                jam_mulai = '" . $jam_mulai . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['jam_selesai'])) {
        $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                jam_selesai = '" . $jam_selesai . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['anggota'])) {
        $anggota = mysqli_real_escape_string($conn, $_POST['anggota']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                anggota = '" . $anggota . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    } elseif (isset($_POST['pic'])) {
        $pic = mysqli_real_escape_string($conn, $_POST['pic']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                pic = '" . $pic . "'
            WHERE
                id_finishing = '" . $id_finishing . "'
        ");
    }




    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL QC</h6>
    ';
    $ksl_total = 0;
    $ksl_bs = 0;
    $ksl_cuci = 0;
    $ksl_kotor = 0;
    $ksl_turun = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 1 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL QC</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
        </div>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">QC</th>
                        <th class="text-center">TOTAL</th>
                        <th class="text-center">BS</th>
                        <th class="text-center">CUCI</th>
                        <th class="text-center">KOTOR</th>
                        <th class="text-center">TURUN SIZE</th>
                        <th class="text-center">KET</th>
                    </tr>
                </thead>
                <tbody>
                ';

        $no_id = "";
        $ttl_total = 0;
        $ttl_bs = 0;
        $ttl_cuci = 0;
        $ttl_kotor = 0;
        $ttl_turun = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="total_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang total_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="total_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak total_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['total'] . '">
                                <button href="javascript:void(0);" type="button" name="total_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah total_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="cuci_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang cuci_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="cuci_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak cuci_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['cuci'] . '">
                                <button href="javascript:void(0);" type="button" name="cuci_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah cuci_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['kotor'] . '">
                                <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="5"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row2['ket'] . '</textarea>
                            </td>
                        </tr>
                    ';
            $ttl_total += $row2['total'];
            $ttl_bs += $row2['bs'];
            $ttl_cuci += $row2['cuci'];
            $ttl_kotor += $row2['kotor'];
            $ttl_turun += $row2['turun'];
            $ksl_total += $row2['total'];
            $ksl_bs += $row2['bs'];
            $ksl_cuci += $row2['cuci'];
            $ksl_kotor += $row2['kotor'];
            $ksl_turun += $row2['turun'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ttl_total . '</td>
                        <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                        <td class="text-center" style="color:red">' . $ttl_cuci . '</td>
                        <td class="text-center" style="color:red">' . $ttl_kotor . '</td>
                        <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                        <td class="text-center" style="color:red"></td>
                    </tr>
                ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
                </tbody>
            </table>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_total . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BS</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_bs . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL CUCI</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_cuci . '" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL KOTOR</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_kotor . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL TURUN SIZE</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_turun . '" readonly>
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
    if (isset($_POST['total'])) {
        // $bolong = mysqli_real_escape_string($conn, $_POST['bolong']);
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT total FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $total = $sql['total'] - 1;

        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                total = '" . $total . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['bs'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bs FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $bs = $sql['bs'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                bs = '" . $bs . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['cuci'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT cuci FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $cuci = $sql['cuci'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                cuci = '" . $cuci . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['kotor'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT kotor FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $kotor = $sql['kotor'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                kotor = '" . $kotor . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['turun'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT turun FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $turun = $sql['turun'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                turun = '" . $turun . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL QC</h6>
    ';
    $ksl_total = 0;
    $ksl_bs = 0;
    $ksl_cuci = 0;
    $ksl_kotor = 0;
    $ksl_turun = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 1 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL QC</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
        </div>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">QC</th>
                        <th class="text-center">TOTAL</th>
                        <th class="text-center">BS</th>
                        <th class="text-center">CUCI</th>
                        <th class="text-center">KOTOR</th>
                        <th class="text-center">TURUN SIZE</th>
                        <th class="text-center">KET</th>
                    </tr>
                </thead>
                <tbody>
                ';

        $no_id = "";
        $ttl_total = 0;
        $ttl_bs = 0;
        $ttl_cuci = 0;
        $ttl_kotor = 0;
        $ttl_turun = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="total_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang total_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="total_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak total_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['total'] . '">
                                <button href="javascript:void(0);" type="button" name="total_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah total_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="cuci_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang cuci_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="cuci_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak cuci_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['cuci'] . '">
                                <button href="javascript:void(0);" type="button" name="cuci_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah cuci_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['kotor'] . '">
                                <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="5"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row2['ket'] . '</textarea>
                            </td>
                        </tr>
                    ';
            $ttl_total += $row2['total'];
            $ttl_bs += $row2['bs'];
            $ttl_cuci += $row2['cuci'];
            $ttl_kotor += $row2['kotor'];
            $ttl_turun += $row2['turun'];
            $ksl_total += $row2['total'];
            $ksl_bs += $row2['bs'];
            $ksl_cuci += $row2['cuci'];
            $ksl_kotor += $row2['kotor'];
            $ksl_turun += $row2['turun'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ttl_total . '</td>
                        <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                        <td class="text-center" style="color:red">' . $ttl_cuci . '</td>
                        <td class="text-center" style="color:red">' . $ttl_kotor . '</td>
                        <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                        <td class="text-center" style="color:red"></td>
                    </tr>
                ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
                </tbody>
            </table>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_total . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BS</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_bs . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL CUCI</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_cuci . '" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL KOTOR</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_kotor . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL TURUN SIZE</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_turun . '" readonly>
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
    if (isset($_POST['total'])) {
        // $bolong = mysqli_real_escape_string($conn, $_POST['bolong']);
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT total FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $total = $sql['total'] + 1;

        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                total = '" . $total . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['bs'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bs FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $bs = $sql['bs'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                bs = '" . $bs . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['cuci'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT cuci FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $cuci = $sql['cuci'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                cuci = '" . $cuci . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['kotor'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT kotor FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $kotor = $sql['kotor'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                kotor = '" . $kotor . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['turun'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT turun FROM tb_finishing WHERE no_id = '" . $no_id . "'"));
        $turun = $sql['turun'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_finishing
            SET
                turun = '" . $turun . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $hasil = $hasil . '
        <h6>TABEL QC</h6>
    ';
    $ksl_total = 0;
    $ksl_bs = 0;
    $ksl_cuci = 0;
    $ksl_kotor = 0;
    $ksl_turun = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND tabel = 1 GROUP BY id_finishing");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_finishing = $row['id_finishing'];
        $tgl_qc = $row['tgl_qc'];
        $jam_mulai = $row['jam_mulai'];
        $jam_selesai = $row['jam_selesai'];
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
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL QC</span>
                            </div>
                            <input type="date" name="tgl_qc" id="" class="form-control form-control-sm ubah_data_input tgl_qc_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '"  value="' . $tgl_qc . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm"></span>
                            </div>
                            <input type="time" name="jam_mulai" id="" class="form-control form-control-sm ubah_data_input jam_mulai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_mulai . '"  required>
                            <input type="time" name="jam_selesai" id="" class="form-control form-control-sm ubah_data_input jam_selesai_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $jam_selesai . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">JUMLAH ANGGOTA</span>
                            </div>
                            <input type="number" name="anggota" id="" class="form-control form-control-sm ubah_data_input anggota_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $anggota . '"  required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PIC</span>
                            </div>
                            <input type="text" name="pic" id="" class="form-control form-control-sm ubah_data_input pic_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" value="' . $pic . '"  required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $id_finishing . '" data-id_finishing="' . $id_finishing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
            </div>
        </div>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">NO</th>
                        <th class="text-center">QC</th>
                        <th class="text-center">TOTAL</th>
                        <th class="text-center">BS</th>
                        <th class="text-center">CUCI</th>
                        <th class="text-center">KOTOR</th>
                        <th class="text-center">TURUN SIZE</th>
                        <th class="text-center">KET</th>
                    </tr>
                </thead>
                <tbody>
                ';

        $no_id = "";
        $ttl_total = 0;
        $ttl_bs = 0;
        $ttl_cuci = 0;
        $ttl_kotor = 0;
        $ttl_turun = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $id_transaksi . "' AND id_finishing = '" . $id_finishing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="total_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang total_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="1"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="total_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak total_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['total'] . '">
                                <button href="javascript:void(0);" type="button" name="total_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah total_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="bs_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bs_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="2"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '">
                                <button href="javascript:void(0);" type="button" name="bs_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bs_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="cuci_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang cuci_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="3"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="cuci_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak cuci_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['cuci'] . '">
                                <button href="javascript:void(0);" type="button" name="cuci_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah cuci_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="4"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['kotor'] . '">
                                <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <button href="javascript:void(0);" type="button" name="turun_k_' . $no_id . '" class="btn btn-xs btn-danger ubah_data_barang_kurang turun_k_' . $no_id . '" data-no_id="' . $no_id . '" data-tes="5"><i class="fa fa-minus"></i></button>
                                <input type="number" style="display:inline;width:30%" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '">
                                <button href="javascript:void(0);" type="button" name="turun_t_' . $no_id . '" class="btn btn-xs btn-warning ubah_data_barang_tambah turun_t_' . $no_id . '" data-no_id="' . $no_id . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                            </td>
                            <td class="text-center">
                                <textarea name="ket_' . $no_id . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id . '" data-no_id="' . $no_id . '" >' . $row2['ket'] . '</textarea>
                            </td>
                        </tr>
                    ';
            $ttl_total += $row2['total'];
            $ttl_bs += $row2['bs'];
            $ttl_cuci += $row2['cuci'];
            $ttl_kotor += $row2['kotor'];
            $ttl_turun += $row2['turun'];
            $ksl_total += $row2['total'];
            $ksl_bs += $row2['bs'];
            $ksl_cuci += $row2['cuci'];
            $ksl_kotor += $row2['kotor'];
            $ksl_turun += $row2['turun'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" style="color:red"></td>
                        <td class="text-center" style="color:red">Total</td>
                        <td class="text-center" style="color:red">' . $ttl_total . '</td>
                        <td class="text-center" style="color:red">' . $ttl_bs . '</td>
                        <td class="text-center" style="color:red">' . $ttl_cuci . '</td>
                        <td class="text-center" style="color:red">' . $ttl_kotor . '</td>
                        <td class="text-center" style="color:red">' . $ttl_turun . '</td>
                        <td class="text-center" style="color:red"></td>
                    </tr>
                ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
                </tbody>
            </table>
        ';
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_total . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BS</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_bs . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL CUCI</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_cuci . '" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL KOTOR</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_kotor . '" readonly>
                        </div>
                    </div>
                    <div class="col-12 ">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL TURUN SIZE</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_turun . '" readonly>
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
