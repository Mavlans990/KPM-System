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

// if (isset($_POST['add_list'])) {
//     $id_tambah = mysqli_real_escape_string($conn, $_POST['add_list']);
//     $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);

//     $id_packing = generate_id_finishing("PKG", "PKG", date("m"), date("y"));
//     $update = "UPDATE tb_packing SET
//         id_packing = '" . $id_packing . "' WHERE id_packing = '" . $id_tambah . "';
//     ";
//     $query = mysqli_query($conn, $update);

//     $hasil = "";

//     $hasil = $hasil . '
//         <h6>PACKING LIST</h6>
//     ';
//     $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing LIKE '%PKG%' GROUP BY id_packing");
//     while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
//         $id_packing = $row['id_packing'];
//         $tgl_kirim = $row['tgl_kirim'];
//         $pengirim = $row['pengirim'];
//         $penerima = $row['penerima'];
//         $hasil = $hasil . '
//         <br>
//         <div class="row" style="margin-right:0px;">
//             <div class="col-md-5">
//                 <div class="row no-gutter">
//                     <div class="col-12">
//                         <div class="input-group mb-3">
//                             <div class="input-group-prepend">
//                                 <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">NO.PACKING LIST</span>
//                             </div>
//                             <input type="text" name="id_packing" id="" class="form-control form-control-sm ubah_data_input_2 id_packing_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="'. $id_packing .'" readonly required>
//                         </div>
//                     </div>
//                     <div class="col-12">
//                         <div class="input-group mb-3">
//                             <div class="input-group-prepend">
//                                 <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENGIRIM</span>
//                             </div>
//                             <input type="text" name="pengirim" id="" class="form-control form-control-sm ubah_data_input_2 pengirim_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="'. $pengirim .'" readonly required>
//                         </div>
//                     </div>
//                     <div class="col-12">
//                         <div class="input-group mb-3">
//                             <div class="input-group-prepend">
//                                 <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENERIMA</span>
//                             </div>
//                             <input type="text" name="penerima" id="" class="form-control form-control-sm ubah_data_input_2 penerima_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="'. $penerima .'" readonly required>
//                         </div>
//                     </div>
//                     <div class="col-12">
//                         <div class="input-group mb-3">
//                             <div class="input-group-prepend">
//                                 <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL KIRIM</span>
//                             </div>
//                             <input type="date" name="tgl_kirim" id="" class="form-control form-control-sm ubah_data_input_2 tgl_kirim_' . $id_packing . '" data-id_packing="' . $id_packing . '"  value="'. $tgl_kirim .'" readonly required>
//                         </div>
//                     </div>
//                 </div>
//             </div>
//             <div class="col-md-5">
//                 <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_2 del_list_2_' . $id_packing . '" data-id_packing="' . $id_packing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
//             </div>
//             <div class="col-md-7">
//                 <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
//                     <thead>
//                         <tr>
//                             <th class="text-center">NO</th>
//                             <th class="text-center">ITEM BAJU</th>
//                             <th class="text-center">POLYBAG</th>
//                             <th class="text-center">PCS</th>
//                         </tr>
//                     </thead>
//                     <tbody>
//                     ';

//                     $no_id = "";
//                     $ttl_polybag = 0;
//                     $ttl_pcs = 0;
//                     $no = 1;
//                     $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing = '". $id_packing ."'");
//                     while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {                                                
//                         $no_id = $row2['no_id'];
//                         $id_sku = $row2['id_sku'];
//                         $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
//                         $hasil = $hasil . '
//                             <tr >
//                                 <td class="text-center">' . $no++ . '</td>
//                                 <td class="text-center" >'.$q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
//                                 <td class="text-center">' . $row2['polybag'] . '</td>
//                                 <td class="text-center">' . $row2['pcs'] . '</td>
//                                 </tr>
//                         ';
//                         $ttl_polybag += $row2['polybag'];
//                         $ttl_pcs += $row2['pcs'];
//                     }
//                     // <td class="text-center" style="color:red"></td>
//                     $hasil = $hasil . '
//                         <tr >
//                             <td class="text-center" style="color:red"></td>
//                             <td class="text-center" style="color:red">Total</td>
//                             <td class="text-center" style="color:red">'. $ttl_polybag .'</td>
//                             <td class="text-center" style="color:red">'. $ttl_pcs .'</td>
//                         </tr>
//                     ';
//                     // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" '.$readonly.'></td>
//                     $hasil = $hasil . '
//                     </tbody>
//                 </table>
//             </div>
//         </div>
//         ';
//     }

//     echo $hasil;
// }

//TABEL 2
if (isset($_POST['id_hapus_2'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus_2']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $delete = "DELETE FROM tb_packing WHERE id_packing = '" . $id_hapus . "' ";
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
            <h6>PACKING LIST</h6>
        ';
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $filter . "' AND id_packing LIKE '%PKG%' GROUP BY id_packing");
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $id_packing = $row['id_packing'];
            $tgl_kirim = $row['tgl_kirim'];
            $pengirim = $row['pengirim'];
            $penerima = $row['penerima'];
            $nama_sj = $row['nama_sj'];
            $hasil = $hasil . '
            <br>
            <div class="row" style="margin-right:0px;">
                <div class="col-md-5">
                    <div class="row no-gutter">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">NO.PACKING LIST</span>
                                </div>
                                <input type="text" name="id_packing" id="" class="form-control form-control-sm ubah_data_input_2 id_packing_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $id_packing . '" readonly required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENGIRIM</span>
                                </div>
                                <input type="text" name="pengirim" id="" class="form-control form-control-sm ubah_data_input_2 pengirim_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $pengirim . '" readonly required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENERIMA</span>
                                </div>
                                <input type="text" name="penerima" id="" class="form-control form-control-sm ubah_data_input_2 penerima_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $penerima . '" readonly required>
                            </div>
                        </div>
                        <div class="col-12">
                    <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama SJ</span>
                    </div>
                    <input type="text" name="nama" id="" class="form-control form-control-sm ubah_data_input keterangan_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $nama . '" ' . $readonly . '>
                </div>
            </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL KIRIM</span>
                                </div>
                                <input type="date" name="tgl_kirim" id="" class="form-control form-control-sm ubah_data_input_2 tgl_kirim_' . $id_packing . '" data-id_packing="' . $id_packing . '"  value="' . $tgl_kirim . '" readonly required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_2 del_list_2_' . $id_packing . '" data-id_packing="' . $id_packing . '" style="margin-top:150px" ><i class="fa fa-trash-o"></i> Hapus Tabel</a>
                </div>
                <div class="col-md-7">
                    <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                        <thead>
                            <tr>
                                <th class="text-center">NO</th>
                                <th class="text-center">ITEM BAJU</th>
                                <th class="text-center">POLYBAG</th>
                                <th class="text-center">PCS</th>
                                </tr>
                            </thead>
                            <tbody>
                            ';

            $no_id = "";
            $ttl_polybag = 0;
            $ttl_pcs = 0;
            $no = 1;
            $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $filter . "' AND id_packing = '" . $id_packing . "'");
            while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                $no_id = $row2['no_id'];
                $id_sku = $row2['id_sku'];
                $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
                $hasil = $hasil . '
                                    <tr >
                                        <td class="text-center">' . $no++ . '</td>
                                        <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                                        <td class="text-center">' . $row2['polybag'] . '</td>
                                        <td class="text-center">' . $row2['pcs'] . '</td>
                                        </tr>
                                ';
                $ttl_polybag += $row2['polybag'];
                $ttl_pcs += $row2['pcs'];
            }
            // <td class="text-center" style="color:red"></td>
            $hasil = $hasil . '
                                <tr >
                                    <td class="text-center" style="color:red"></td>
                                    <td class="text-center" style="color:red">Total</td>
                                    <td class="text-center" style="color:red">' . $ttl_polybag . '</td>
                                    <td class="text-center" style="color:red">' . $ttl_pcs . '</td>
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
        echo $hasil;
    }
}

//TABEL 1
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
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_finishing WHERE id_transaksi = '" . $filter . "' AND tabel = 1 GROUP BY id_finishing");
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
                                <td class="text-center"><input type="number" style="display:inline;width:70px" name="total_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak total_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['total'] . '" ></td>
                                <td class="text-center"><input type="number" style="display:inline;width:70px" name="bs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak bs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['bs'] . '" ></td>
                                <td class="text-center"><input type="number" style="display:inline;width:70px" name="cuci_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak cuci_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['cuci'] . '" ></td>
                                <td class="text-center"><input type="number" style="display:inline;width:70px" name="kotor_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak kotor_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['kotor'] . '" ></td>
                                <td class="text-center"><input type="number" style="display:inline;width:70px" name="turun_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak turun_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['turun'] . '" ></td>
                            </tr>
                        ';
                $ttl_total += $row2['total'];
                $ttl_bs += $row2['bs'];
                $ttl_cuci += $row2['cuci'];
                $ttl_kotor += $row2['kotor'];
                $ttl_turun += $row2['turun'];
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
    if (isset($_POST['polybag'])) {
        $polybag = mysqli_real_escape_string($conn, $_POST['polybag']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_packing
            SET
                polybag = '" . $polybag . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    } elseif (isset($_POST['pcs'])) {
        $pcs = mysqli_real_escape_string($conn, $_POST['pcs']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_packing
            SET
                pcs = '" . $pcs . "'
            WHERE
                no_id = '" . $no_id . "'
        ");
    }


    $hasil = "";

    $hasil = $hasil . '
        <h6>BUAT PACKING LIST BARU</h6>
    ';
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing = '" . $_SESSION['id_user'] . "' GROUP BY id_packing");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_packing = $row['id_packing'];
        $tgl_kirim = $row['tgl_kirim'];
        $pengirim = $row['pengirim'];
        $penerima = $row['penerima'];
        $nama_sj = $row['nama_sj'];
        $hasil = $hasil . '
        <br>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">NO</th>
                    <th class="text-center">ITEM BAJU</th>
                    <th class="text-center">POLYBAG</th>
                    <th class="text-center">PCS</th>
                </tr>
            </thead>
            <tbody>
            ';

        $no_id = "";
        $ttl_polybag = 0;
        $ttl_pcs = 0;
        $ttl_cuci = 0;
        $ttl_kotor = 0;
        $ttl_turun = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing = '" . $id_packing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                    <tr >
                        <td class="text-center">' . $no++ . '</td>
                        <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                        <td class="text-center"><input type="number" style="display:inline;width:70px" name="polybag_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak polybag_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['polybag'] . '" ></td>
                        <td class="text-center"><input type="number" style="display:inline;width:70px" name="pcs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak pcs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['pcs'] . '" ></td>
                    </tr>
                ';
            $ttl_polybag += $row2['polybag'];
            $ttl_pcs += $row2['pcs'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                <tr >
                    <td class="text-center" style="color:red"></td>
                    <td class="text-center" style="color:red">Total</td>
                    <td class="text-center" style="color:red">' . $ttl_polybag . '</td>
                    <td class="text-center" style="color:red">' . $ttl_pcs . '</td>
                </tr>
            ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
            </tbody>
        </table>
        <div class="row" style="margin-right:0px;">
            <div class="col-md-5">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">NO.PACKING LIST</span>
                            </div>
                            <input type="text" name="id_packing" id="" class="form-control form-control-sm ubah_data_input id_packing_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $id_packing . '" readonly required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENGIRIM</span>
                            </div>
                            <input type="text" name="pengirim" id="" class="form-control form-control-sm ubah_data_input pengirim_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $pengirim . '"  >
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENERIMA</span>
                            </div>
                            <input type="text" name="penerima" id="" class="form-control form-control-sm ubah_data_input penerima_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $penerima . '"  >
                        </div>
                    </div>
                    <div class="col-12">
                    <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama SJ</span>
                    </div>
                    <input type="text" name="nama" id="" class="form-control form-control-sm ubah_data_input keterangan_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $nama . '" ' . $readonly . '>
                </div>
            </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL KIRIM</span>
                            </div>
                            <input type="date" name="tgl_kirim" id="" class="form-control form-control-sm ubah_data_input tgl_kirim_' . $id_packing . '" data-id_packing="' . $id_packing . '"  value="' . $tgl_kirim . '"  >
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <input type="hidden" name="id_packing" value="' . $id_packing . '">
                <button type="submit" class="btn btn-success btn-xs" style="width:15vw;" name="save_packing" style="color:white;"><i class="fa fa-plus"></i> Simpan Packing List</button>
            </div>
        </div>
        ';
    }

    echo $hasil;
}

if (isset($_POST['ubah_data_input'])) {
    $id_packing = mysqli_real_escape_string($conn, $_POST['ubah_data_input']);
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
    if (isset($_POST['pengirim'])) {
        $pengirim = mysqli_real_escape_string($conn, $_POST['pengirim']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_packing
            SET
                pengirim = '" . $pengirim . "'
            WHERE
                id_packing = '" . $id_packing . "'
        ");
    } elseif (isset($_POST['penerima'])) {
        $penerima = mysqli_real_escape_string($conn, $_POST['penerima']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_packing
            SET
                penerima = '" . $penerima . "'
            WHERE
                id_packing = '" . $id_packing . "'
        ");
    } elseif (isset($_POST['tgl_kirim'])) {
        $tgl_kirim = mysqli_real_escape_string($conn, $_POST['tgl_kirim']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_packing
            SET
                tgl_kirim = '" . $tgl_kirim . "'
            WHERE
                id_packing = '" . $id_packing . "'
        ");
    } elseif (isset($_POST['keterangan'])) {
        $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
        $sql_update_barang_keluar = mysqli_query($conn, "
            UPDATE
                tb_packing
            SET
                nama_sj = '" . $keterangan . "'
            WHERE
                id_packing = '" . $id_packing . "'
        ");
    }




    $hasil = "";

    $hasil = $hasil . '
        <h6>BUAT PACKING LIST BARU</h6>
    ';
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing = '" . $_SESSION['id_user'] . "' GROUP BY id_packing");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_packing = $row['id_packing'];
        $tgl_kirim = $row['tgl_kirim'];
        $pengirim = $row['pengirim'];
        $penerima = $row['penerima'];
        $nama = $row['nama_sj'];
        $hasil = $hasil . '
        <br>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">NO</th>
                    <th class="text-center">ITEM BAJU</th>
                    <th class="text-center">POLYBAG</th>
                    <th class="text-center">PCS</th>
                </tr>
            </thead>
            <tbody>
            ';

        $no_id = "";
        $ttl_polybag = 0;
        $ttl_pcs = 0;
        $ttl_cuci = 0;
        $ttl_kotor = 0;
        $ttl_turun = 0;
        $no = 1;
        $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_packing WHERE id_transaksi = '" . $id_transaksi . "' AND id_packing = '" . $id_packing . "'");
        while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
            $no_id = $row2['no_id'];
            $id_sku = $row2['id_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                    <tr >
                        <td class="text-center">' . $no++ . '</td>
                        <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                        <td class="text-center"><input type="number" style="display:inline;width:70px" name="polybag_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak polybag_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['polybag'] . '" ></td>
                        <td class="text-center"><input type="number" style="display:inline;width:70px" name="pcs_' . $no_id . '" class="form-control form-control-sm ubah_data_barang_tembak pcs_' . $no_id . '" data-no_id="' . $no_id . '" value="' . $row2['pcs'] . '" ></td>
                    </tr>
                ';
            $ttl_polybag += $row2['polybag'];
            $ttl_pcs += $row2['pcs'];
        }
        // <td class="text-center" style="color:red"></td>
        $hasil = $hasil . '
                <tr >
                    <td class="text-center" style="color:red"></td>
                    <td class="text-center" style="color:red">Total</td>
                    <td class="text-center" style="color:red">' . $ttl_polybag . '</td>
                    <td class="text-center" style="color:red">' . $ttl_pcs . '</td>
                </tr>
            ';
        // <td class="text-center"><input type="text" name="shrinkage_'.$no_id.'" class="form-control form-control-sm shrinkage_'.$no_id.'" ></td>
        $hasil = $hasil . '
            </tbody>
        </table>
        <div class="row" style="margin-right:0px;">
            <div class="col-md-5">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">NO.PACKING LIST</span>
                            </div>
                            <input type="text" name="id_packing" id="" class="form-control form-control-sm ubah_data_input id_packing_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $id_packing . '" readonly required>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENGIRIM</span>
                            </div>
                            <input type="text" name="pengirim" id="" class="form-control form-control-sm ubah_data_input pengirim_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $pengirim . '"  >
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">PENERIMA</span>
                            </div>
                            <input type="text" name="penerima" id="" class="form-control form-control-sm ubah_data_input penerima_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $penerima . '"  >
                        </div>
                    </div>
                    <div class="col-12">
                    <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Nama SJ</span>
                    </div>
                    <input type="text" name="nama" id="" class="form-control form-control-sm ubah_data_input keterangan_' . $id_packing . '" data-id_packing="' . $id_packing . '" value="' . $nama . '" ' . $readonly . '>
                </div>
            </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TGL KIRIM</span>
                            </div>
                            <input type="date" name="tgl_kirim" id="" class="form-control form-control-sm ubah_data_input tgl_kirim_' . $id_packing . '" data-id_packing="' . $id_packing . '"  value="' . $tgl_kirim . '"  >
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <input type="hidden" name="id_packing" value="' . $id_packing . '">
                <button type="submit" class="btn btn-success btn-xs" style="width:15vw;" name="save_packing" style="color:white;"><i class="fa fa-plus"></i> Simpan Packing List</button>
            </div>
        </div>
        ';
    }

    echo $hasil;
}
