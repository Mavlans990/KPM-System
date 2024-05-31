<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
session_start();

if (isset($_POST['get_barang'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['get_barang']);
    $ex_barang = explode(" | ", $nama_barang);
    $id_barang = $ex_barang[0];

    $uom = "";
    $sql_uom_barang = mysqli_query($conn, "SELECT uom FROM tb_bahan WHERE id_bahan = '" . $id_barang . "'");
    if ($row_uom_barang = mysqli_fetch_array($sql_uom_barang)) {
        $uom = $row_uom_barang['uom'];
    }

    echo $uom;
}

if (isset($_POST['cari_nama_supplier'])) {
    $nama_supplier = mysqli_real_escape_string($conn, $_POST['cari_nama_supplier']);

    $hasil = "";
    $sql_get_nama_supplier = mysqli_query($conn, "SELECT id_customer,nama_customer FROM tb_customer WHERE nama_customer LIKE '%" . $nama_supplier . "%' ORDER BY nama_customer ASC LIMIT 50");
    while ($row_nama_supplier = mysqli_fetch_array($sql_get_nama_supplier)) {
        $hasil = $hasil . '
            <option value="' . $row_nama_supplier['id_customer'] . ' | ' . $row_nama_supplier['nama_customer'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_nama_barang'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['cari_nama_barang']);

    $hasil = "";
    $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE kode_bahan_kain LIKE '%" . $nama_barang . "%' OR nama_kain LIKE '%" . $nama_barang . "%' ORDER BY jenis_kain ASC LIMIT 50");
    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
        $hasil = $hasil . '
            <option value="' . $row_barang['id_bahan_kain'] . ' - ' . $row_barang['kode_bahan_kain'] . ' - ' . $row_barang['nama_kain'] . ' - ' . $row_barang['warna'] . ' - ' . $row_barang['setting'] . ' - ' . $row_barang['gramasi'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_nama_barang_2'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['cari_nama_barang_2']);

    $hasil = "";
    $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_sku WHERE kode_sku LIKE '%" . $nama_barang . "%' OR nama_sku LIKE '%" . $nama_barang . "%' ORDER BY nama_sku ASC LIMIT 50");
    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
        $hasil = $hasil . '
            <option value="' . $row_barang['id_sku'] . ' - ' . $row_barang['kode_sku'] . ' - ' . $row_barang['nama_sku'] . ' - ' . $row_barang['ukuran'] . ' - ' . $row_barang['warna'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['filter_bahan'])) {
    $id_bahan = explode(" | ", $_POST['filter_bahan']);
    $id_bahan = $id_bahan[1];

    $select_bahan = "SELECT uom FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'";
    $query_bahan = mysqli_query($conn, $select_bahan);
    $data_bahan = mysqli_fetch_array($query_bahan);

    echo $data_bahan['uom'];
}

if (isset($_POST['add_list'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['add_list']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    // $id_bahan_kain = mysqli_real_escape_string($conn, $_POST['id_bahan_kain']);
    $nama_kain = mysqli_real_escape_string($conn, $_POST['nama_kain']);
    $qty_kain = mysqli_real_escape_string($conn, $_POST['qty_kain']);
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
    } else {
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
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $filter . "' AND id_bahan_kain != ''");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $no_id = $row['no_id'];
        $id_bahan_kain = $row['id_bahan_kain'];
        $qty_kain = $row['qty_kain'];
        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" >' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</td>
                        <td class="text-center" >' . $qty_kain . ' ROLL</td>
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
                    <td class="text-center" style="color:red">' . $ksl_qty_kain . ' ROLL</td>
                    <td class="text-center" style="color:red"></td>
                </tr>
                <tr>
                    <td class="text-center">
                        <input type="text" name="nama_kain_' . $no_id . '" id="" class="form-control form-control-sm nama_kain nama_kain_' . $no_id . '" autocomplete="off" list="list_barang" onclick="this.select()" >
                        <datalist id="list_barang" class="list_barang">
                        </datalist>
                    </td>
                    <td class="text-center"><input type="text" name="qty_kain_' . $no_id . '" class="form-control form-control-sm qty_kain_' . $no_id . '" ></td>
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

if (isset($_POST['id_hapus'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $delete = "DELETE FROM tb_work_order WHERE no_id = '" . $id_hapus . "'";
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
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $filter . "' AND id_bahan_kain != ''");
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $no_id = $row['no_id'];
            $id_bahan_kain = $row['id_bahan_kain'];
            $qty_kain = $row['qty_kain'];
            $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center" >' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</td>
                            <td class="text-center" >' . $qty_kain . ' ROLL</td>
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
                        <td class="text-center" style="color:red">' . $ksl_qty_kain . ' ROLL</td>
                        <td class="text-center" style="color:red"></td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <input type="text" name="nama_kain_' . $no_id . '" id="" class="form-control form-control-sm nama_kain nama_kain_' . $no_id . '" autocomplete="off" list="list_barang" onclick="this.select()" >
                            <datalist id="list_barang" class="list_barang">
                            </datalist>
                        </td>
                        <td class="text-center"><input type="text" name="qty_kain_' . $no_id . '" class="form-control form-control-sm qty_kain_' . $no_id . '" ></td>
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

if (isset($_POST['add_list_2'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['add_list_2']);
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
                    <th></th>
                </tr>
            </thead>
            <tbody>
            ';
    $no_id = "";
    $id_sku = "";
    $ksl_qty_sku = 0;
    $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $filter . "' AND id_sku != ''");
    while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
        $no_id = $row['no_id'];
        $id_sku = $row['id_sku'];
        $qty_sku = $row['qty_sku'];
        $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
        $hasil = $hasil . '
                    <tr >
                        <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                        <td class="text-center" >' . $qty_sku . '</td>
                        <td class="text-center" >
                            <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
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
                </tr>
                <tr>
                    <td class="text-center">
                        <input type="text" name="nama_sku_' . $no_id . '" id="" class="form-control form-control-sm nama_sku nama_sku_' . $no_id . '" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                        <datalist id="list_barang_2" class="list_barang_2">
                        </datalist>
                    </td>
                    <td class="text-center"><input type="text" name="qty_sku_' . $no_id . '" class="form-control form-control-sm qty_sku_' . $no_id . '"></td>
                    <td class="text-center">
                        <a href="javascript:void(0);" class="btn btn-xs btn-success add_list_2 add_list_2_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
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

if (isset($_POST['id_hapus_2'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus_2']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $delete = "DELETE FROM tb_work_order WHERE no_id = '" . $id_hapus . "'";
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
        <h6>RENCANA PENGERJAAN</h6>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">Nama Item</th>
                        <th class="text-center">Rasio</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                ';
        $no_id = "";
        $id_sku = "";
        $ksl_qty_sku = 0;
        $select_barang_masuk2 = mysqli_query($conn, "SELECT * FROM tb_work_order WHERE id_transaksi = '" . $filter . "' AND id_sku != ''");
        while ($row = mysqli_fetch_assoc($select_barang_masuk2)) {
            $no_id = $row['no_id'];
            $id_sku = $row['id_sku'];
            $qty_sku = $row['qty_sku'];
            $q_sku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_sku WHERE id_sku = '" . $id_sku . "'"));
            $hasil = $hasil . '
                        <tr >
                            <td class="text-center" >' . $q_sku['nama_sku'] . ' - ' . $q_sku['warna'] . ' - ' . $q_sku['ukuran'] . '</td>
                            <td class="text-center" >' . $qty_sku . '</td>
                            <td class="text-center" >
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list_2 del_list_2_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
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
                    </tr>
                    <tr>
                        <td class="text-center">
                            <input type="text" name="nama_sku_' . $no_id . '" id="" class="form-control form-control-sm nama_sku nama_sku_' . $no_id . '" autocomplete="off" list="list_barang_2" onclick="this.select()" >
                            <datalist id="list_barang_2" class="list_barang_2">
                            </datalist>
                        </td>
                        <td class="text-center"><input type="text" name="qty_sku_' . $no_id . '" class="form-control form-control-sm qty_sku_' . $no_id . '"></td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-xs btn-success add_list_2 add_list_2_' . $no_id . '" data-id="' . $no_id . '" data-id_sku="' . $id_sku . '"><i class="fa fa-plus"></i></a>
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

if (isset($_POST['id_edit'])) {
    $hasil = "";
    $id_edit = mysqli_real_escape_string($conn, $_POST['id_edit']);
    $id_edit_transaksi = mysqli_real_escape_string($conn, $_POST['id_edit_transaksi']);
    $qty = mysqli_real_escape_string($conn, $_POST['qty']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $total = $harga * $qty;

    $update = "UPDATE tb_barang_masuk SET
        qty = '" . $qty . "',
        harga = '" . $harga . "',
        total = '" . $total . "' WHERE no_id = '" . $id_edit . "';
    ";

    $query = mysqli_query($conn, $update);

    if ($id_edit_transaksi == "new") {
        $filter = $_SESSION['id_user'];
    } else {
        $filter = $id_edit_transaksi;
    }


    $total = 0;

    $select_barang_masuk = "SELECT a.*,b.id_bahan,b.nama_bahan,b.uom FROM tb_barang_masuk a JOIN tb_bahan b ON b.id_bahan = a.id_product WHERE a.id_transaksi = '" . $filter . "' AND a.id_cabang = '" . $id_cabang . "'";
    $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
    $jum_barang_masuk = mysqli_num_rows($query_barang_masuk);
    while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
        $total = $total + $row_barang_masuk['total'];
        $hasil = $hasil . '
        <tr>
            <td class="text-center">' . $row_barang_masuk['id_bahan'] . '</td>
            <td class="text-center">' . $row_barang_masuk['nama_bahan'] . '</td>
            <td class="text-center">' . $row_barang_masuk['qty'] . '</td>
            <td class="text-center">' . $row_barang_masuk['berat'] . '</td>
            <td class="text-center">' . $row_barang_masuk['uom'] . '</td>
            <td class="text-center">' . number_format($row_barang_masuk['harga']) . '</td>
            <td class="text-center"> ' . number_format($row_barang_masuk['total']) . '</td>
            <td>
                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $row_barang_masuk['no_id'] . '" data-id="' . $row_barang_masuk['no_id'] . '"><i class="fa fa-trash-o"></i></a>
            </td>
        </tr>
            ';
    }
    if ($jum_barang_masuk > 0) {
        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $filter . "' AND id_cabang = '" . $id_cabang . "'";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        $data_barang_masuk = mysqli_fetch_array($query_barang_masuk);

        $ppn = $total * $data_barang_masuk['ppn'] / 100;

        $hasil = $hasil . '
        <tr>
            <th class="text-right" colspan="6">Subtotal</th>
            <th class="text-right" colspan="2"> ' . number_format($total) . '</th>
        </tr>
        ';

        if ($_SESSION['jenis_pajak'] == 1) {
            $hasil = $hasil . '
                <tr>
                    <th class="text-right" colspan="6">PPN <input type="number" style="max-width:50px;" name="" class="text-right ppn_input" data-id_transaksi="' . $id_transaksi . '" min="0" max="100" value="' . $data_barang_masuk['ppn'] . '" autocomplete="off" onclick="this.select();"> % </th>
                    <th class="text-right" colspan="2"> ' . number_format($ppn) . '</th>
                </tr>
            ';
        }

        $hasil = $hasil . '
        <tr>
            <th class="text-right" colspan="6">Grand Total</th>
            <th class="text-right" colspan="2"> ' . number_format($total + $ppn) . '</th>
        </tr>
        ';
    }

    echo $hasil;
}

if (isset($_POST['simpan'])) {
    $simpan = mysqli_real_escape_string($conn, $_POST['simpan']);
    $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);



    if ($simpan == "new") {
        $hasil = 0;
        $id_transaksi = generate_barang_masuk_key("BM", "BM", date("m"), date("y"));



        $update = "UPDATE tb_barang_masuk SET
            id_transaksi = '" . $id_transaksi . "',
            id_supplier = '" . $nama_supplier . "',
            keterangan = '" . $keterangan . "',
            status = 'd' WHERE id_transaksi = '" . $_SESSION['id_user'] . "'
        ";

        $query = mysqli_query($conn, $update);

        if ($query) {
            $hasil = 1;
        }
        echo $hasil;
    } else {
        $hasil = 0;
        $id_transaksi = generate_barang_masuk_key("BM", "BM", date("m"), date("y"));
        $update = "UPDATE tb_barang_masuk SET
            id_supplier = '" . $nama_supplier . "',
            keterangan = '" . $keterangan . "',
            status = 'd' WHERE id_transaksi = '" . $simpan . "'
        ";

        $query = mysqli_query($conn, $update);

        if ($query) {
            $hasil = 1;
        }
        echo $hasil;
    }
}

if (isset($_POST['simpan_oto'])) {
    $simpan_oto = mysqli_real_escape_string($conn, $_POST['simpan_oto']);
    $nama_supplier = mysqli_real_escape_string($conn, $_POST['nama_supplier']);
    $tanggal_transaksi = mysqli_real_escape_string($conn, $_POST['tanggal_transaksi']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);


    $total = 0;

    if ($simpan_oto == "new") {
        $hasil = 0;
        $id_transaksi = generate_barang_masuk_key("BM", "BM", date("m"), date("y"));



        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $_SESSION['id_user'] . "'";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
            $select_stock = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND id_cabang = '" . $row_barang_masuk['id_cabang'] . "'";
            $query_stock = mysqli_query($conn, $select_stock);
            $data_stock = mysqli_fetch_array($query_stock);
            $jum_stock = mysqli_num_rows($query_stock);

            if ($jum_stock < 1) {
                $ppn = $row_barang_masuk['total'] * $row_barang_masuk['ppn'] / 100;
                $total = $row_barang_masuk['total'];
                $hpp_return = (0 + $total) / (0 + $row_barang_masuk['qty']);

                $insert_stock = mysqli_query($conn, "INSERT INTO tb_stock_cabang(
                    id,
                    id_bahan,
                    id_cabang,
                    stock,
                    hpp
                ) VALUES(
                    '',
                    '" . $row_barang_masuk['id_product'] . "',
                    '" . $row_barang_masuk['id_cabang'] . "',
                    '" . $row_barang_masuk['qty'] . "',
                    '" . $hpp_return . "'
                )");
                $update_hpp = mysqli_query($conn, "UPDATE tb_barang_masuk SET hpp = '" . $hpp_return . "' WHERE no_id = '" . $row_barang_masuk['no_id'] . "'");
            } else {
                $total_stock = $data_stock['stock'] + $row_barang_masuk['qty'];

                $ppn = $row_barang_masuk['total'] * $row_barang_masuk['ppn'] / 100;
                $total = $row_barang_masuk['total'];
                $hpp = (($data_stock['hpp'] * $data_stock['stock']) + $total) / $total_stock;
                $hpp_return = $hpp - $data_stock['hpp'];


                $update_stock = mysqli_query($conn, "UPDATE tb_stock_cabang SET
                    stock = '" . $total_stock . "',hpp = '" . $hpp . "' WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND id_cabang = '" . $row_barang_masuk['id_cabang'] . "'
                ");
                $update_hpp = mysqli_query($conn, "UPDATE tb_barang_masuk SET hpp = '" . $hpp_return . "' WHERE no_id = '" . $row_barang_masuk['no_id'] . "'");
            }
        }


        $update = "UPDATE tb_barang_masuk SET
            id_transaksi = '" . $id_transaksi . "',
            id_supplier = '" . $nama_supplier . "',
            keterangan = '" . $keterangan . "',
            status = 's' WHERE id_transaksi = '" . $_SESSION['id_user'] . "'
        ";

        $query = mysqli_query($conn, $update);

        if ($query) {
            $hasil = 1;
        }
        echo $hasil;
    } else {
        $hasil = 0;
        $id_transaksi = generate_barang_masuk_key("BM", "BM", date("m"), date("y"));

        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $simpan_oto . "'";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
            $select_stock = "SELECT * FROM tb_stock_cabang WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND id_cabang = '" . $row_barang_masuk['id_cabang'] . "'";
            $query_stock = mysqli_query($conn, $select_stock);
            $data_stock = mysqli_fetch_array($query_stock);
            $jum_stock = mysqli_num_rows($query_stock);

            if ($jum_stock < 1) {

                $ppn = $row_barang_masuk['total'] * $row_barang_masuk['ppn'] / 100;
                $total = $row_barang_masuk['total'];
                $hpp_return = (0 + $total) / (0 + $row_barang_masuk['qty']);

                $insert_stock = mysqli_query($conn, "INSERT INTO tb_stock_cabang(
                    id,
                    id_bahan,
                    id_cabang,
                    stock,
                    hpp
                ) VALUES(
                    '',
                    '" . $row_barang_masuk['id_product'] . "',
                    '" . $row_barang_masuk['id_cabang'] . "',
                    '" . $row_barang_masuk['qty'] . "',
                    '" . $hpp_return . "'
                )");
                $update_hpp = mysqli_query($conn, "UPDATE tb_barang_masuk SET hpp = '" . $hpp_return . "' WHERE no_id = '" . $row_barang_masuk['no_id'] . "'");
            } else {
                $total_stock = $data_stock['stock'] + $row_barang_masuk['qty'];

                $ppn = $row_barang_masuk['total'] * $row_barang_masuk['ppn'] / 100;
                $total = $row_barang_masuk['total'];
                $hpp = (($data_stock['hpp'] * $data_stock['stock']) + $total) / $total_stock;
                $hpp_return = $hpp - $data_stock['hpp'];


                $update_stock = mysqli_query($conn, "UPDATE tb_stock_cabang SET
                    stock = '" . $total_stock . "',hpp='" . $hpp . "' WHERE id_bahan = '" . $row_barang_masuk['id_product'] . "' AND id_cabang = '" . $row_barang_masuk['id_cabang'] . "'
                ");
                $update_hpp = mysqli_query($conn, "UPDATE tb_barang_masuk SET hpp = '" . $hpp_return . "' WHERE no_id = '" . $row_barang_masuk['no_id'] . "'");
            }
        }

        $update = "UPDATE tb_barang_masuk SET
            id_supplier = '" . $nama_supplier . "',
            keterangan = '" . $keterangan . "',
            status = 's' WHERE id_transaksi = '" . $simpan_oto . "'
        ";

        $query = mysqli_query($conn, $update);

        if ($query) {
            $hasil = 1;
        }
        echo $hasil;
    }
}

if (isset($_POST['id_ppn'])) {
    $id_ppn = mysqli_real_escape_string($conn, $_POST['id_ppn']);
    $ppn_input = mysqli_real_escape_string($conn, $_POST['ppn_input']);
    $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);

    if ($id_ppn == "new") {
        $hasil = 0;
        $filter = $_SESSION['id_user'];

        $update = "UPDATE tb_barang_masuk SET
        ppn = '" . $ppn_input . "' WHERE id_transaksi = '" . $_SESSION['id_user'] . "';
        ";
        $query = mysqli_query($conn, $update);
    } else {
        $hasil = 0;
        $filter = $id_ppn;

        $update = "UPDATE tb_barang_masuk SET
        ppn = '" . $ppn_input . "' WHERE id_transaksi = '" . $id_ppn . "';
        ";
        $query = mysqli_query($conn, $update);
    }

    $total = 0;
    $hasil = '';

    $select_barang_masuk = "SELECT a.*,b.id_bahan,b.nama_bahan FROM tb_barang_masuk a JOIN tb_bahan b ON b.id_bahan = a.id_product WHERE a.id_transaksi = '" . $filter . "'";
    $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
    $jum_barang_masuk = mysqli_num_rows($query_barang_masuk);
    while ($row_barang_masuk = mysqli_fetch_array($query_barang_masuk)) {
        $total = $total + $row_barang_masuk['total'];
        $hasil = $hasil . '
            <tr>
                <td class="text-center">' . $row_barang_masuk['id_bahan'] . '</td>
                <td class="text-center">' . $row_barang_masuk['nama_bahan'] . '</td>
                <td class="text-center">' . $row_barang_masuk['qty'] . '</td>
                <td class="text-center">' . $row_barang_masuk['berat'] . '</td>
                <td class="text-center">' . $row_barang_masuk['uom'] . '</td>
                <td class="text-center">' . number_format($row_barang_masuk['harga']) . '</td>
                <td class="text-center"> ' . number_format($row_barang_masuk['total']) . '</td>
                <td>
                    <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $row_barang_masuk['no_id'] . '" data-id="' . $row_barang_masuk['no_id'] . '"><i class="fa fa-trash-o"></i></a>
                </td>
            </tr>
            ';
    }

    if ($jum_barang_masuk > 0) {
        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $filter . "'";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        $data_barang_masuk = mysqli_fetch_array($query_barang_masuk);

        $ppn = $total * $data_barang_masuk['ppn'] / 100;

        $hasil = $hasil . '
        <tr>
            <th class="text-right" colspan="6">Subtotal</th>
            <th class="text-right" colspan="2"> ' . number_format($total) . '</th>
        </tr>
        <tr>
        <th class="text-right" colspan="6">PPN <input type="number" style="max-width:50px;" name="" class="text-right ppn_input" data-id_transaksi="' . $id_ppn . '" min="0" max="100" value="' . $data_barang_masuk['ppn'] . '" autocomplete="off" onclick="this.select();"> % </th>
        <th class="text-right" colspan="2"> ' . number_format($ppn) . '</th>
    </tr>
        <tr>
            <th class="text-right" colspan="6">Grand Total</th>
            <th class="text-right" colspan="2"> ' . number_format($total + $ppn) . '</th>
        </tr>
        ';
    }

    echo $hasil;
}


