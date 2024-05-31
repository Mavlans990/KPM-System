<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
session_start();

if (isset($_POST['cari_lot'])) {
    $nama_lot = mysqli_real_escape_string($conn, $_POST['cari_lot']);
    $id_bahan = mysqli_real_escape_string($conn, $_POST['id_bahan']);

    $hasil = "";
    // $sql_get_nama_lot = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE lot LIKE '%" . $nama_lot . "%' AND id_bahan_kain = '" . $id_bahan . "' AND terpakai = '' ORDER BY lot ASC LIMIT 10");
    $sql_get_nama_lot = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE lot LIKE '%" . $nama_lot . "%' AND id_bahan_kain = '" . $id_bahan . "' AND terpakai = '' ORDER BY no_id ASC");
    while ($row_nama_lot = mysqli_fetch_array($sql_get_nama_lot)) {
        $hasil = $hasil . '
            <option value="' . $row_nama_lot['lot'] . ' : ' . $row_nama_lot['barcode'] . '">
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
    
    if (preg_match("/:/i", $lot)) {
        $barcode = explode(" : ", $lot);
        $barcode = $barcode[1];
    }else {
        $barcode = $lot;
    }

    $select_barang_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_bahan_kain,terpakai FROM tb_barang_masuk WHERE barcode = '" . $barcode . "'"));
    $id_bahan = $select_barang_masuk['id_bahan_kain'];
    $terpakai = $select_barang_masuk['terpakai'];

    if($terpakai == ''){
    $sql_update_barang_keluar = mysqli_query($conn, "INSERT INTO tb_inspek(
        id_transaksi,
        id_bahan_kain,
        barcode
    ) VALUES(
        '" . $id_transaksi . "',
        '" . $id_bahan . "',
        '" . $barcode . "'

    )");
    }else{
        echo '<script>alert("Bahan Ini Sudah Pernah Dipakai Sebelumnya");</script>';
    }

    $hasil = "";


    $ksl_bruto = 0;
    $ksl_netto = 0;
    $ksl_susut = 0;
    $ksl_shrinkage = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain LIKE '%BHN%' GROUP BY id_bahan_kain");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_bahan_kain = $row['id_bahan_kain'];
        $no_id = $row['no_id'];
        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "'"));
        $hasil = $hasil . '
        <br>
        <p>' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . ' : ' . $row['qty_kain'] . ' ROLL</p>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">Barcode</th>
                    <th class="text-center">Bolong</th>
                    <th class="text-center">Kotor</th>
                    <th class="text-center">Patah Jarum</th>
                    <th class="text-center">Belang</th>
                    <th class="text-center">Garis</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Ket</th>
                    <th class="text-center">Bruto</th>
                    <th ></th>
                </tr>
            </thead>
            <tbody>
            ';
        $select_inspek = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain = '" . $row['id_bahan_kain'] . "' ");
        while ($row2 = mysqli_fetch_assoc($select_inspek)) {
            $select_terima_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE barcode = '" . $row2['barcode'] . "'"));
            $no_id2 = $row2['no_id'];
            $lot = $select_terima_kain['lot'];
            $bruto = $select_terima_kain['bruto'];
            $total_ksl = $row2['bolong'] + $row2['kotor'] + $row2['patah'] + $row2['belang'] + $row2['garis'];

            $hasil = $hasil . '
                <tr>
                    <td class="text-center">' . $lot . '  ' . $row2['barcode'] . '</td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="bolong_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bolong_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="1"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="bolong_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak bolong_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['bolong'] . '" >
                        <button href="javascript:void(0);" type="button" name="bolong_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bolong_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="2"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['kotor'] . '" >
                        <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="patah_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang patah_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="3"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="patah_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak patah_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['patah'] . '" >
                        <button href="javascript:void(0);" type="button" name="patah_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah patah_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="belang_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang belang_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="4"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="belang_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak belang_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['belang'] . '" >
                        <button href="javascript:void(0);" type="button" name="belang_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah belang_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="garis_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang garis_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="5"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="garis_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak garis_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['garis'] . '" >
                        <button href="javascript:void(0);" type="button" name="garis_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah garis_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">' . $total_ksl . '</td>
                    <td class="text-center">
                        <textarea name="ket_' . $no_id2 . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id2 . '" data-no_id="' . $no_id2 . '">' . $row2['ket'] . '</textarea>
                    </td>
                    <td class="text-center" >' . $bruto . '</td>
                    <td class="text-center"><a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id2 . '" data-id="' . $no_id2 . '"><i class="fa fa-trash-o"></i></a></td>
                </tr>
                ';
        }
        $hasil = $hasil . '
                <tr>
                    <td class="text-center" colspan="2">
                    <input type="text" name="lot_' . $no_id . '"  class="form-control form-control-sm lot lot_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '" autocomplete="off" list="list_lot" onclick="this.select();">
                    <datalist id="list_lot" class="list_lot">
                    </datalist>
                    </td>
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
    }


    echo $hasil;
}

if (isset($_POST['id_hapus'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);
    
    $sql_barcode = mysqli_query($conn,"SELECT barcode FROM tb_inspek WHERE id_transaksi = '".$id_hapus_transaksi."' AND no_id = '".$id_hapus."'");
    while($row_barcode = mysqli_fetch_array($sql_barcode)){
        $update = mysqli_query($conn,"UPDATE tb_barang_masuk SET terpakai = '' WHERE barcode = '".$row_barcode['barcode']."' ");
    }

    

    $delete = "DELETE FROM tb_inspek WHERE no_id = '" . $id_hapus . "'";
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

        $ksl_bruto = 0;
        $ksl_netto = 0;
        $ksl_susut = 0;
        $ksl_shrinkage = 0;
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $filter . "' AND id_bahan_kain LIKE '%BHN%' GROUP BY id_bahan_kain");
        $jum_data = mysqli_num_rows($select_barang_masuk);
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $id_bahan_kain = $row['id_bahan_kain'];
            $no_id = $row['no_id'];
            $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "'"));
            $hasil = $hasil . '
        <br>
        <p>' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . ' : ' . $row['qty_kain'] . ' ROLL</p>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">Barcode</th>
                    <th class="text-center">Bolong</th>
                    <th class="text-center">Kotor</th>
                    <th class="text-center">Patah Jarum</th>
                    <th class="text-center">Belang</th>
                    <th class="text-center">Garis</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Ket</th>
                    <th class="text-center">Bruto</th>
                    <th ></th>
                </tr>
            </thead>
            <tbody>
            ';
            $select_inspek = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $filter . "' AND id_bahan_kain = '" . $row['id_bahan_kain'] . "' ");
            while ($row2 = mysqli_fetch_assoc($select_inspek)) {
                $select_terima_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE barcode = '" . $row2['barcode'] . "'"));
                $no_id2 = $row2['no_id'];
                $lot = $select_terima_kain['lot'];
                $bruto = $select_terima_kain['bruto'];
                $total_ksl = $row2['bolong'] + $row2['kotor'] + $row2['patah'] + $row2['belang'] + $row2['garis'];

                $hasil = $hasil . '
                <tr>
                    <td class="text-center">' . $lot . '  ' . $row2['barcode'] . '</td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="bolong_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bolong_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="1"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="bolong_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak bolong_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['bolong'] . '" >
                        <button href="javascript:void(0);" type="button" name="bolong_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bolong_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="2"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['kotor'] . '" >
                        <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="patah_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang patah_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="3"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="patah_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak patah_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['patah'] . '" >
                        <button href="javascript:void(0);" type="button" name="patah_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah patah_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="belang_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang belang_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="4"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="belang_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak belang_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['belang'] . '" >
                        <button href="javascript:void(0);" type="button" name="belang_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah belang_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="garis_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang garis_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="5"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="garis_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak garis_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['garis'] . '" >
                        <button href="javascript:void(0);" type="button" name="garis_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah garis_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">' . $total_ksl . '</td>
                    <td class="text-center">
                        <textarea name="ket_' . $no_id2 . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id2 . '" data-no_id="' . $no_id2 . '">' . $row2['ket'] . '</textarea>
                    </td>
                    <td class="text-center" >' . $bruto . '</td>
                    <td class="text-center"><a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id2 . '" data-id="' . $no_id2 . '"><i class="fa fa-trash-o"></i></a></td>
                </tr>
                ';
            }
            $hasil = $hasil . '
                <tr>
                    <td class="text-center" colspan="2">
                    <input type="text" name="lot_' . $no_id . '"  class="form-control form-control-sm lot lot_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '" autocomplete="off" list="list_lot" onclick="this.select();">
                    <datalist id="list_lot" class="list_lot">
                    </datalist>
                    </td>
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
        }
    }
    echo $hasil;
}

// if (isset($_POST['add_list'])) {
//     $id_tambah = mysqli_real_escape_string($conn, $_POST['add_list']);
//     $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
//     // $id_bahan_kain = mysqli_real_escape_string($conn, $_POST['id_bahan_kain']);
//     $lot = mysqli_real_escape_string($conn, $_POST['lot']);
//     // $shrinkage = mysqli_real_escape_string($conn, $_POST['shrinkage']);

//     if ($id_transaksi == "new") {

//         $filter = $_SESSION['id_user'];

//         // if ($id_tambah != "") {
//         //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_work_order WHERE no_id = '" . $id_tambah . "' AND id_bahan_kain = '". $id_bahan_kain ."'"));
//         // }else {
//         //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_work_order"));
//         // }

//             $id_bahan = explode(" - ", $nama_kain);
//             $id_bahan = $id_bahan[0];

//             $no_id = id_gen_work_order();

//             $valid = 0;

//             $insert = "INSERT INTO tb_work_order(
//                 no_id,
//                 id_transaksi,
//                 id_bahan_kain,
//                 qty_kain
//             ) VALUES(
//                 '" . $no_id . "',
//                 '" . $_SESSION['id_user'] . "',
//                 '" . $id_bahan . "',
//                 '" . $qty_kain . "'

//             )";

//             $query = mysqli_query($conn, $insert);


//     } 
//     else {
//         $filter = $id_transaksi;

//         // if ($id_tambah != "") {
//         //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE no_id = '" . $id_tambah . "' AND id_bahan_kain = '". $id_bahan_kain ."'"));
//         //     $lot_masuk = $c_masuk['lot'];
//         // }else {
//         //     $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk"));
//         // }
//             $id_bahan = explode(" - ", $nama_kain);
//             $id_bahan = $id_bahan[0];

//             $no_id = id_gen_work_order();

//             $valid = 0;

//             $insert = "INSERT INTO tb_work_order(
//                 no_id,
//                 id_transaksi,
//                 id_bahan_kain,
//                 qty_kain
//             ) VALUES(
//                 '" . $no_id . "',
//                 '" . $id_transaksi . "',
//                 '" . $id_bahan . "',
//                 '" . $qty_kain . "'

//             )";

//             $query = mysqli_query($conn, $insert);

//     }
//     $hasil = '';

//     $hasil = $hasil . '
//     <h6>BAHAN BAKU</h6>
//     <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
//             <thead>
//                 <tr>
//                     <th class="text-center">Nama Kain</th>
//                     <th class="text-center">QTY</th>
//                     <th></th>
//                 </tr>
//             </thead>
//             <tbody>
//             ';
//             $no_id = "";
//             $id_bahan_kain = "";
//             $ksl_qty_kain = 0;
//             $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $filter . "' AND id_bahan_kain LIKE '%BHN%' ORDER BY id_bahan_kain ASC");
//             while ($row = mysqli_fetch_assoc($select_barang_masuk)) {                                                
//                 $no_id = $row['no_id'];
//                 $id_bahan_kain = $row['id_bahan_kain'];
//                 $qty_kain = $row['qty_kain'];
//                 $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
//                 $hasil = $hasil . '
//                     <tr >
//                         <td class="text-center" >'.$q_kain['jenis_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] .'</td>
//                         <td class="text-center" >'. $qty_kain .' ROLL</td>
//                         <td class="text-center" >
//                             <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
//                         </td>
//                     </tr>
//                 ';
//                 $ksl_qty_kain += $qty_kain;
//             }
//             $hasil = $hasil . '
//                 <tr >
//                     <td class="text-center" style="color:red">Total</td>
//                     <td class="text-center" style="color:red">'. $ksl_qty_kain .' ROLL</td>
//                     <td class="text-center" style="color:red"></td>
//                 </tr>
//                 <tr>
//                     <td class="text-center">
//                         <input type="text" name="nama_kain_'.$no_id.'" id="" class="form-control form-control-sm nama_kain nama_kain_'.$no_id.'" autocomplete="off" list="list_barang" onclick="this.select()" >
//                         <datalist id="list_barang" class="list_barang">
//                         </datalist>
//                     </td>
//                     <td class="text-center"><input type="text" name="qty_kain_'.$no_id.'" class="form-control form-control-sm qty_kain_'.$no_id.'" ></td>
//                     <td class="text-center">
//                         <a href="javascript:void(0);" class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '"><i class="fa fa-plus"></i></a>
//                     </td>
//                 </tr>
//             ';
//             // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
//             $hasil = $hasil . '
//             </tbody>
//         </table>
//     ';
//     echo $hasil;
// }

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
    if (isset($_POST['bolong'])) {
        $bolong = mysqli_real_escape_string($conn, $_POST['bolong']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
                bolong = '" . $bolong . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['kotor'])) {
        $kotor = mysqli_real_escape_string($conn, $_POST['kotor']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
                kotor = '" . $kotor . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['patah'])) {
        $patah = mysqli_real_escape_string($conn, $_POST['patah']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            patah = '" . $patah . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['belang'])) {
        $belang = mysqli_real_escape_string($conn, $_POST['belang']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            belang = '" . $belang . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['garis'])) {
        $garis = mysqli_real_escape_string($conn, $_POST['garis']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            garis = '" . $garis . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['ket'])) {
        $ket = mysqli_real_escape_string($conn, $_POST['ket']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            ket = '" . $ket . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $ksl_bruto = 0;
    $ksl_netto = 0;
    $ksl_susut = 0;
    $ksl_shrinkage = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain LIKE '%BHN%' GROUP BY id_bahan_kain");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_bahan_kain = $row['id_bahan_kain'];
        $no_id = $row['no_id'];
        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "'"));
        $hasil = $hasil . '
        <br>
        <p>' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . ' : ' . $row['qty_kain'] . ' ROLL</p>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">Barcode</th>
                    <th class="text-center">Bolong</th>
                    <th class="text-center">Kotor</th>
                    <th class="text-center">Patah Jarum</th>
                    <th class="text-center">Belang</th>
                    <th class="text-center">Garis</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Ket</th>
                    <th class="text-center">Bruto</th>
                    <th ></th>
                </tr>
            </thead>
            <tbody>
            ';
        $select_inspek = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain = '" . $row['id_bahan_kain'] . "' ");
        while ($row2 = mysqli_fetch_assoc($select_inspek)) {
            $select_terima_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE barcode = '" . $row2['barcode'] . "'"));
            $no_id2 = $row2['no_id'];
            $lot = $select_terima_kain['lot'];
            $bruto = $select_terima_kain['bruto'];
            $total_ksl = $row2['bolong'] + $row2['kotor'] + $row2['patah'] + $row2['belang'] + $row2['garis'];

            $hasil = $hasil . '
                <tr>
                    <td class="text-center">' . $lot . '  ' . $row2['barcode'] . '</td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="bolong_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bolong_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="1"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="bolong_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak bolong_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['bolong'] . '" >
                        <button href="javascript:void(0);" type="button" name="bolong_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bolong_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="2"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['kotor'] . '" >
                        <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="patah_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang patah_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="3"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="patah_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak patah_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['patah'] . '" >
                        <button href="javascript:void(0);" type="button" name="patah_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah patah_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="belang_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang belang_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="4"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="belang_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak belang_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['belang'] . '" >
                        <button href="javascript:void(0);" type="button" name="belang_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah belang_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="garis_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang garis_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="5"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="garis_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak garis_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['garis'] . '" >
                        <button href="javascript:void(0);" type="button" name="garis_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah garis_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">' . $total_ksl . '</td>
                    <td class="text-center">
                        <textarea name="ket_' . $no_id2 . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id2 . '" data-no_id="' . $no_id2 . '">' . $row2['ket'] . '</textarea>
                    </td>
                    <td class="text-center" >' . $bruto . '</td>
                    <td class="text-center"><a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id2 . '" data-id="' . $no_id2 . '"><i class="fa fa-trash-o"></i></a></td>
                </tr>
                ';
        }
        $hasil = $hasil . '
                <tr>
                    <td class="text-center" colspan="2">
                    <input type="text" name="lot_' . $no_id . '"  class="form-control form-control-sm lot lot_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '" autocomplete="off" list="list_lot" onclick="this.select();">
                    <datalist id="list_lot" class="list_lot">
                    </datalist>
                    </td>
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
    if (isset($_POST['bolong'])) {
        // $bolong = mysqli_real_escape_string($conn, $_POST['bolong']);
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bolong FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $bolong = $sql['bolong'] - 1;

        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
                bolong = '" . $bolong . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['kotor'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT kotor FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $kotor = $sql['kotor'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
                kotor = '" . $kotor . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['patah'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT patah FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $patah = $sql['patah'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            patah = '" . $patah . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['belang'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT belang FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $belang = $sql['belang'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            belang = '" . $belang . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['garis'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT garis FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $garis = $sql['garis'] - 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            garis = '" . $garis . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $ksl_bruto = 0;
    $ksl_netto = 0;
    $ksl_susut = 0;
    $ksl_shrinkage = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain LIKE '%BHN%' GROUP BY id_bahan_kain");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_bahan_kain = $row['id_bahan_kain'];
        $no_id = $row['no_id'];
        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "'"));
        $hasil = $hasil . '
        <br>
        <p>' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . ' : ' . $row['qty_kain'] . ' ROLL</p>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">Barcode</th>
                    <th class="text-center">Bolong</th>
                    <th class="text-center">Kotor</th>
                    <th class="text-center">Patah Jarum</th>
                    <th class="text-center">Belang</th>
                    <th class="text-center">Garis</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Ket</th>
                    <th class="text-center">Bruto</th>
                    <th ></th>
                </tr>
            </thead>
            <tbody>
            ';
        $select_inspek = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain = '" . $row['id_bahan_kain'] . "' ");
        while ($row2 = mysqli_fetch_assoc($select_inspek)) {
            $select_terima_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE barcode = '" . $row2['barcode'] . "'"));
            $no_id2 = $row2['no_id'];
            $lot = $select_terima_kain['lot'];
            $bruto = $select_terima_kain['bruto'];
            $total_ksl = $row2['bolong'] + $row2['kotor'] + $row2['patah'] + $row2['belang'] + $row2['garis'];

            $hasil = $hasil . '
                <tr>
                    <td class="text-center">' . $lot . '  ' . $row2['barcode'] . '</td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="bolong_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bolong_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="1"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="bolong_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak bolong_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['bolong'] . '" >
                        <button href="javascript:void(0);" type="button" name="bolong_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bolong_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="2"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['kotor'] . '" >
                        <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="patah_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang patah_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="3"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="patah_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak patah_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['patah'] . '" >
                        <button href="javascript:void(0);" type="button" name="patah_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah patah_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="belang_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang belang_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="4"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="belang_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak belang_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['belang'] . '" >
                        <button href="javascript:void(0);" type="button" name="belang_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah belang_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="garis_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang garis_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="5"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="garis_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak garis_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['garis'] . '" >
                        <button href="javascript:void(0);" type="button" name="garis_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah garis_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">' . $total_ksl . '</td>
                    <td class="text-center">
                        <textarea name="ket_' . $no_id2 . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id2 . '" data-no_id="' . $no_id2 . '">' . $row2['ket'] . '</textarea>
                    </td>
                    <td class="text-center" >' . $bruto . '</td>
                    <td class="text-center"><a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id2 . '" data-id="' . $no_id2 . '"><i class="fa fa-trash-o"></i></a></td>
                </tr>
                ';
        }
        $hasil = $hasil . '
                <tr>
                    <td class="text-center" colspan="2">
                    <input type="text" name="lot_' . $no_id . '"  class="form-control form-control-sm lot lot_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '" autocomplete="off" list="list_lot" onclick="this.select();">
                    <datalist id="list_lot" class="list_lot">
                    </datalist>
                    </td>
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
    if (isset($_POST['bolong'])) {
        // $bolong = mysqli_real_escape_string($conn, $_POST['bolong']);
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bolong FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $bolong = $sql['bolong'] + 1;

        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
                bolong = '" . $bolong . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['kotor'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT kotor FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $kotor = $sql['kotor'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
                kotor = '" . $kotor . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['patah'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT patah FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $patah = $sql['patah'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            patah = '" . $patah . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['belang'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT belang FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $belang = $sql['belang'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            belang = '" . $belang . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['garis'])) {
        $sql = mysqli_fetch_assoc(mysqli_query($conn, "SELECT garis FROM tb_inspek WHERE no_id = '" . $no_id . "'"));
        $garis = $sql['garis'] + 1;
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_inspek
            SET
            garis = '" . $garis . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $ksl_bruto = 0;
    $ksl_netto = 0;
    $ksl_susut = 0;
    $ksl_shrinkage = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain LIKE '%BHN%' GROUP BY id_bahan_kain");
    $jum_data = mysqli_num_rows($select_barang_masuk);
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_bahan_kain = $row['id_bahan_kain'];
        $no_id = $row['no_id'];
        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "'"));
        $hasil = $hasil . '
        <br>
        <p>' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . ' : ' . $row['qty_kain'] . ' ROLL</p>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">Barcode</th>
                    <th class="text-center">Bolong</th>
                    <th class="text-center">Kotor</th>
                    <th class="text-center">Patah Jarum</th>
                    <th class="text-center">Belang</th>
                    <th class="text-center">Garis</th>
                    <th class="text-center">Total</th>
                    <th class="text-center">Ket</th>
                    <th class="text-center">Bruto</th>
                    <th ></th>
                </tr>
            </thead>
            <tbody>
            ';
        $select_inspek = mysqli_query($conn, "SELECT * FROM tb_inspek WHERE id_transaksi = '" . $id_transaksi . "' AND id_bahan_kain = '" . $row['id_bahan_kain'] . "' ");
        while ($row2 = mysqli_fetch_assoc($select_inspek)) {
            $select_terima_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE barcode = '" . $row2['barcode'] . "'"));
            $no_id2 = $row2['no_id'];
            $lot = $select_terima_kain['lot'];
            $bruto = $select_terima_kain['bruto'];
            $total_ksl = $row2['bolong'] + $row2['kotor'] + $row2['patah'] + $row2['belang'] + $row2['garis'];

            $hasil = $hasil . '
                <tr>
                    <td class="text-center">' . $lot . '  ' . $row2['barcode'] . '</td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="bolong_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang bolong_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="1"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="bolong_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak bolong_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['bolong'] . '" >
                        <button href="javascript:void(0);" type="button" name="bolong_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah bolong_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="1"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="kotor_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang kotor_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="2"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="kotor_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['kotor'] . '" >
                        <button href="javascript:void(0);" type="button" name="kotor_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah kotor_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="2"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="patah_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang patah_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="3"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="patah_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak patah_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['patah'] . '" >
                        <button href="javascript:void(0);" type="button" name="patah_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah patah_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="3"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="belang_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang belang_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="4"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="belang_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak belang_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['belang'] . '" >
                        <button href="javascript:void(0);" type="button" name="belang_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah belang_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="4"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">
                        <button href="javascript:void(0);" type="button" name="garis_k_' . $no_id2 . '" class="btn btn-xs btn-danger ubah_data_barang_kurang garis_k_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes="5"><i class="fa fa-minus"></i></button>
                        <input type="number" style="display:inline;width:30%" name="garis_' . $no_id2 . '" class="form-control form-control-sm ubah_data_barang_tembak garis_' . $no_id2 . '" data-no_id="' . $no_id2 . '" value="' . $row2['garis'] . '" >
                        <button href="javascript:void(0);" type="button" name="garis_t_' . $no_id2 . '" class="btn btn-xs btn-warning ubah_data_barang_tambah garis_t_' . $no_id2 . '" data-no_id="' . $no_id2 . '" data-tes2="5"><i class="fa fa-plus"></i></button>
                    </td>
                    <td class="text-center">' . $total_ksl . '</td>
                    <td class="text-center">
                        <textarea name="ket_' . $no_id2 . '" id="" style="height:33px;" class="form-control form-control-sm ubah_data_barang_tembak ket_' . $no_id2 . '" data-no_id="' . $no_id2 . '">' . $row2['ket'] . '</textarea>
                    </td>
                    <td class="text-center" >' . $bruto . '</td>
                    <td class="text-center"><a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id2 . '" data-id="' . $no_id2 . '"><i class="fa fa-trash-o"></i></a></td>
                </tr>
                ';
        }
        $hasil = $hasil . '
                <tr>
                    <td class="text-center" colspan="2">
                    <input type="text" name="lot_' . $no_id . '"  class="form-control form-control-sm lot lot_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '" autocomplete="off" list="list_lot" onclick="this.select();">
                    <datalist id="list_lot" class="list_lot">
                    </datalist>
                    </td>
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
    }

    echo $hasil;
}
