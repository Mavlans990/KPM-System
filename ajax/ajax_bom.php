<?php
include "../lib/koneksi.php";
include "../lib/appcode.php";
session_start();

if (isset($_POST['cari_kode_barang_sku'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['cari_kode_barang_sku']);

    $hasil = "";
    $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_sku WHERE kode_sku LIKE '%" . $nama_barang . "%' OR nama_sku LIKE '%" . $nama_barang . "%' ORDER BY id_sku ASC LIMIT 50");
    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
        $hasil = $hasil . '
            <option value="' . $row_barang['kode_sku'] . ' | ' . $row_barang['nama_sku'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_nama_barang_sku'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['cari_nama_barang_sku']);

    $hasil = "";
    $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_sku WHERE nama_sku LIKE '%" . $nama_barang . "%' OR warna LIKE '%" . $nama_barang . "%' ORDER BY id_sku ASC LIMIT 50");
    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
        $hasil = $hasil . '
            <option value="' . $row_barang['nama_sku'] . ' - ' . $row_barang['warna'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_kode_bahan_kain'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['cari_kode_bahan_kain']);

    $hasil = "";
    $sql_get_barang = mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE kode_bahan_kain LIKE '%" . $nama_barang . "%' OR nama_kain LIKE '%" . $nama_barang . "%' ORDER BY id_bahan_kain ASC LIMIT 50");
    while ($row_barang = mysqli_fetch_array($sql_get_barang)) {
        $hasil = $hasil . '
                <option value="' . $row_barang['kode_bahan_kain'] . ' | ' . $row_barang['nama_kain'] . '">
            ';
    }

    echo $hasil;
}

if (isset($_POST['add_list'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['add_list']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $id_transaksi_2 = mysqli_real_escape_string($conn, $_POST['id_transaksi_2']);

    $kode_bahan_kain = mysqli_real_escape_string($conn, $_POST['kode_bahan_kain']);
    $bahan_kain = explode(" | ", $kode_bahan_kain);

    $uom_kain = mysqli_real_escape_string($conn, $_POST['uom_kain']);
    $qty_kain = mysqli_real_escape_string($conn, $_POST['qty_kain']);

    if ($_POST['harga'] != "") {
        $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    } else {
        $harga = 0;
    }

    $total_harga = $harga * $qty_kain;
    // $shrinkage = mysqli_real_escape_string($conn, $_POST['shrinkage']);

    if ($id_transaksi == "new") {


        $filter = $_SESSION['id_user'];
        $q_bom = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bom WHERE no_id = '" . $id_tambah . "' AND id_transaksi_2 = '" . $id_transaksi_2 . "' "));
        $no_id = id_gen_bom();
        $valid = 0;

        if ($q_bom['kode_bahan_kain'] == "" && $q_bom['no_id'] != "") {
            $update = "UPDATE tb_bom SET
                kode_bahan_kain = '" . $bahan_kain[0] . "',
                jenis_kain = '" . $bahan_kain[1] . "',
                uom_kain = '" . $uom_kain . "',
                qty_kain = '" . $qty_kain . "',
                harga = '" . $harga . "',
                total_harga = '" . $total_harga . "' WHERE no_id = '" . $id_tambah . "';
            ";

            $query = mysqli_query($conn, $update);
        } else {
            $insert = "INSERT INTO tb_bom(
                    no_id,
                    id_transaksi,
                    id_transaksi_2,
                    kode_bahan_kain,
                    jenis_kain,
                    uom_kain,
                    qty_kain,
                    harga,
                    total_harga
                ) VALUES(
                    '" . $no_id . "',
                    '" . $_SESSION['id_user'] . "',
                    '" . $id_transaksi_2 . "',
                    '" . $bahan_kain[0] . "',
                    '" . $bahan_kain[1] . "',
                    '" . $uom_kain . "',
                    '" . $qty_kain . "',
                    '" . $harga . "',
                    '" . $total_harga . "'
    
                )";

            $query = mysqli_query($conn, $insert);
        }
    } else {
        $filter = $id_transaksi;
        $q_bom = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bom WHERE no_id = '" . $id_tambah . "' AND id_transaksi_2 = '" . $id_transaksi_2 . "' "));
        $no_id = id_gen_bom();
        $valid = 0;

        if ($q_bom['kode_bahan_kain'] == "" && $q_bom['no_id'] != "") {
            $update = "UPDATE tb_bom SET
                kode_bahan_kain = '" . $bahan_kain[0] . "',
                jenis_kain = '" . $bahan_kain[1] . "',
                uom_kain = '" . $uom_kain . "',
                qty_kain = '" . $qty_kain . "',
                harga = '" . $harga . "',
                total_harga = '" . $total_harga . "' WHERE no_id = '" . $id_tambah . "';
            ";

            $query = mysqli_query($conn, $update);
        } else {
            $insert = "INSERT INTO tb_bom(
                    no_id,
                    id_transaksi,
                    id_transaksi_2,
                    kode_bahan_kain,
                    jenis_kain,
                    uom_kain,
                    qty_kain,
                    harga,
                    total_harga
                ) VALUES(
                    '" . $no_id . "',
                    '" . $id_transaksi . "',
                    '" . $id_transaksi_2 . "',
                    '" . $bahan_kain[0] . "',
                    '" . $bahan_kain[1] . "',
                    '" . $uom_kain . "',
                    '" . $qty_kain . "',
                    '" . $harga . "',
                    '" . $total_harga . "'
    
                )";

            $query = mysqli_query($conn, $insert);
        }
    }
    $hasil = '';

    $id_transaksi_2 = generate_barang_masuk_key("DBM", "DBM", date("m"), date("y"));

    $q_bom = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi = '" . $filter . "'");
    $jum_data = mysqli_num_rows($q_bom);
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi = '" . $filter . "' GROUP BY id_transaksi_2 ");
    if ($jum_data < 1) {
        echo '
        <br>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">Kode Barang</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">UOM</th>
                    <th class="text-center">QTY</th>  
                    <th class="text-center">Harga</th>
                    <th class="text-center">Total</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <tr >
                    <td colspan="2" class="text-center">
                        <input type="text" name="kode_bahan_kainnew" class="form-control form-control-sm kode_bahan_kain kode_bahan_kain_new" autocomplete="off" list="list_kode_bahan_kain" onclick="this.select()" >
                        <datalist id="list_kode_bahan_kain" class="list_kode_bahan_kain">
                        </datalist>
                    </td>
                    <td class="text-center d-none"><input type="text" name="jenis_kain" class="form-control form-control-sm jenis_kain_new" ></td>
                    <td class="text-center"><input type="text" name="uom_kain" class="form-control form-control-sm uom_kain_new" ></td>
                    <td class="text-center"><input type="number" name="qty_kain" class="form-control form-control-sm qty_kain_new" step="0.001"></td>
                    <td class="text-center"><input type="number" name="harga" class="form-control form-control-sm harga_new" ></td>
                    <td colspan="2" class="text-center">
                        <a href="javascript:void(0);"  class="btn btn-xs btn-success add_list add_list_new" data-id="new" data-id_transaksi_2="' . $id_transaksi_2 . '"><i class="fa fa-plus"></i></a>
                    </td>
                </tr>
            ';
        echo '
            </tbody>
        </table>
        ';
    } else {
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $no_id = $row['no_id'];
            $kode_bahan_kain = $row['kode_bahan_kain'];
            // $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE kode_bahan_kain = '" . $row['kode_bahan_kain'] . "'"));
            // <p>' . $q_kain['jenis_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</p>
            // <p>' . $row['id_transaksi_2'] . '</p>
            echo '
            <br>
            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">Kode Barang</th>
                        <th class="text-center">Nama Barang</th>
                        <th class="text-center">UOM</th>
                        <th class="text-center">QTY</th>  
                        <th class="text-center">Harga</th>
                        <th class="text-center">Total</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                ';
            $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi_2 = '" . $row['id_transaksi_2'] . "' AND id_transaksi = '" . $filter . "'");
            while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                $kode_bahan_kain_2 = $row2['kode_bahan_kain'];
                $jenis_kain = $row2['jenis_kain'];
                $uom_kain = $row2['uom_kain'];
                $qty_kain = $row2['qty_kain'];
                $harga = $row2['harga'];
                $total = $row2['total_harga'];

                $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE kode_bahan_kain = '" . $kode_bahan_kain_2 . "'"));
                echo '
                    <tr>
                        <td class="text-center">' . $kode_bahan_kain_2 . '</td>
                        <td class="text-center">' . $q_kain['nama_kain'] . '</td>
                        <td class="text-center">' . $uom_kain . '</td>
                        <td class="text-center">' . $qty_kain . '</td>
                        <td class="text-center">' . number_format($harga) . '</td>
                        <td class="text-center">' . number_format($total) . '</td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                ';
            }
            echo '
                    <tr >
                        <td colspan="2" class="text-center">
                            <input type="text" name="kode_bahan_kain' . $no_id . '" class="form-control form-control-sm kode_bahan_kain kode_bahan_kain_' . $no_id . '" autocomplete="off" list="list_kode_bahan_kain" onclick="this.select()" >
                            <datalist id="list_kode_bahan_kain" class="list_kode_bahan_kain">
                            </datalist>
                        </td>
                        <td class="text-center d-none"><input type="text" name="jenis_kain_' . $no_id . '" class="form-control form-control-sm jenis_kain_' . $no_id . '" ></td>
                        <td class="text-center"><input type="text" name="uom_kain_' . $no_id . '" class="form-control form-control-sm uom_kain_' . $no_id . '" ></td>
                        <td class="text-center"><input type="number" name="qty_kain_' . $no_id . '" class="form-control form-control-sm qty_kain_' . $no_id . '" step="0.001"></td>
                        <td class="text-center"><input type="number" name="harga_' . $no_id . '" class="form-control form-control-sm harga_' . $no_id . '" ></td>
                        <td colspan="2" class="text-center">
                            <a href="javascript:void(0);"  class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_transaksi_2="' . $row['id_transaksi_2'] . '"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                ';
            echo '
                </tbody>
            </table>
            ';
        }
        echo '
                <a href="javascript:void(0);" class="btn btn-sm btn-success add_list_2 "data-id_transaksi_2="' . $id_transaksi_2 . '">Save</a>
                <br><br>
            ';
    }
    echo $hasil;
}

if (isset($_POST['add_list_2'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $id_transaksi_2 = mysqli_real_escape_string($conn, $_POST['id_transaksi_2']);
    // var_dump($id_transaksi_2);

    // $shrinkage = mysqli_real_escape_string($conn, $_POST['shrinkage']);

    if ($id_transaksi == "new") {

        $filter = $_SESSION['id_user'];
        $no_id = id_gen_bom();
        $valid = 0;

        $insert = "INSERT INTO tb_bom(
                no_id,
                id_transaksi,
                id_transaksi_2
            ) VALUES(
                '" . $no_id . "',
                '" . $_SESSION['id_user'] . "',
                '" . $id_transaksi_2 . "'

            )";

        $query = mysqli_query($conn, $insert);
    } else {

        $filter = $id_transaksi;
        $no_id = id_gen_bom();
        $valid = 0;

        $insert = "INSERT INTO tb_bom(
                no_id,
                id_transaksi,
                id_transaksi_2
            ) VALUES(
                '" . $no_id . "',
                '" . $id_transaksi . "',
                '" . $id_transaksi_2 . "'

            )";

        $query = mysqli_query($conn, $insert);
    }
    $hasil = '';

    $id_transaksi_2 = generate_barang_masuk_key("DBM", "DBM", date("m"), date("y"));

    $q_bom = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi = '" . $filter . "'");
    $jum_data = mysqli_num_rows($q_bom);
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi = '" . $filter . "' GROUP BY id_transaksi_2 ");
    if ($jum_data < 1) {
        echo '
        <br>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
            <thead>
                <tr>
                    <th class="text-center">Kode Barang</th>
                    <th class="text-center">Nama Barang</th>
                    <th class="text-center">UOM</th>
                    <th class="text-center">QTY</th>  
                    <th class="text-center">Harga</th>
                    <th class="text-center">Total</th>
                    <th class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <tr >
                    <td colspan="2" class="text-center">
                        <input type="text" name="kode_bahan_kainnew" class="form-control form-control-sm kode_bahan_kain kode_bahan_kain_new" autocomplete="off" list="list_kode_bahan_kain" onclick="this.select()" >
                        <datalist id="list_kode_bahan_kain" class="list_kode_bahan_kain">
                        </datalist>
                    </td>
                    <td class="text-center d-none"><input type="text" name="jenis_kain" class="form-control form-control-sm jenis_kain_new" ></td>
                    <td class="text-center"><input type="text" name="uom_kain" class="form-control form-control-sm uom_kain_new" ></td>
                    <td class="text-center"><input type="number" name="qty_kain" class="form-control form-control-sm qty_kain_new" step="0.001"></td>
                    <td class="text-center"><input type="number" name="harga" class="form-control form-control-sm harga_new" ></td>
                    <td colspan="2" class="text-center">
                        <a href="javascript:void(0);"  class="btn btn-xs btn-success add_list add_list_new" data-id="new" data-id_transaksi_2="' . $id_transaksi_2 . '"><i class="fa fa-plus"></i></a>
                    </td>
                </tr>
            ';
        echo '
            </tbody>
        </table>
        ';
    } else {
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $no_id = $row['no_id'];
            $kode_bahan_kain = $row['kode_bahan_kain'];
            $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE kode_bahan_kain = '" . $row['kode_bahan_kain'] . "'"));
            // <p>' . $q_kain['jenis_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</p>
            // <p>' . $row['id_transaksi_2'] . '</p>
            echo '
            <br>
            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">Kode Barang</th>
                        <th class="text-center">Nama Barang</th>
                        <th class="text-center">UOM</th>
                        <th class="text-center">QTY</th>  
                        <th class="text-center">Harga</th>
                        <th class="text-center">Total</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                ';
            $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi_2 = '" . $row['id_transaksi_2'] . "' AND id_transaksi = '" . $filter . "'");
            while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                $kode_bahan_kain_2 = $row2['kode_bahan_kain'];
                $jenis_kain = $row2['jenis_kain'];
                $uom_kain = $row2['uom_kain'];
                $qty_kain = $row2['qty_kain'];
                $harga = $row2['harga'];
                $total = $row2['total_harga'];

                $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE kode_bahan_kain = '" . $kode_bahan_kain_2 . "'"));

                echo '
                    <tr>
                        <td class="text-center">' . $kode_bahan_kain_2 . '</td>
                        <td class="text-center">' . $q_kain['nama_kain'] . '</td>
                        <td class="text-center">' . $uom_kain . '</td>
                        <td class="text-center">' . $qty_kain . '</td>
                        <td class="text-center">' . number_format($harga) . '</td>
                        <td class="text-center">' . number_format($total) . '</td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                ';
            }
            echo '
                    <tr >
                        <td colspan="2" class="text-center">
                            <input type="text" name="kode_bahan_kain' . $no_id . '" class="form-control form-control-sm kode_bahan_kain kode_bahan_kain_' . $no_id . '" autocomplete="off" list="list_kode_bahan_kain" onclick="this.select()" >
                            <datalist id="list_kode_bahan_kain" class="list_kode_bahan_kain">
                            </datalist>
                        </td>
                        <td class="text-center d-none"><input type="text" name="jenis_kain_' . $no_id . '" class="form-control form-control-sm jenis_kain_' . $no_id . '" ></td>
                        <td class="text-center"><input type="text" name="uom_kain_' . $no_id . '" class="form-control form-control-sm uom_kain_' . $no_id . '" ></td>
                        <td class="text-center"><input type="number" name="qty_kain_' . $no_id . '" class="form-control form-control-sm qty_kain_' . $no_id . '" step="0.001"></td>
                        <td class="text-center"><input type="number" name="harga_' . $no_id . '" class="form-control form-control-sm harga_' . $no_id . '" ></td>
                        <td colspan="2" class="text-center">
                            <a href="javascript:void(0);"  class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_transaksi_2="' . $row['id_transaksi_2'] . '"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                ';
            echo '
                </tbody>
            </table>
            ';
        }
        echo '
                <a href="javascript:void(0);" class="btn btn-sm btn-success add_list_2 "data-id_transaksi_2="' . $id_transaksi_2 . '">Save</a>
                <br><br>
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




    $delete = "DELETE FROM tb_bom WHERE no_id = '" . $id_hapus . "'";
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

        $id_transaksi_2 = generate_barang_masuk_key("DBM", "DBM", date("m"), date("y"));

        $q_bom = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi = '" . $filter . "'");
        $jum_data = mysqli_num_rows($q_bom);
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi = '" . $filter . "' GROUP BY id_transaksi_2 ");
        if ($jum_data < 1) {
            echo '
            <br>
            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                <thead>
                    <tr>
                        <th class="text-center">Kode Barang</th>
                        <th class="text-center">Nama Barang</th>
                        <th class="text-center">UOM</th>
                        <th class="text-center">QTY</th>  
                        <th class="text-center">Harga</th>
                        <th class="text-center">Total</th>
                        <th class="text-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr >
                        <td colspan="2" class="text-center">
                            <input type="text" name="kode_bahan_kainnew" class="form-control form-control-sm kode_bahan_kain kode_bahan_kain_new" autocomplete="off" list="list_kode_bahan_kain" onclick="this.select()" >
                            <datalist id="list_kode_bahan_kain" class="list_kode_bahan_kain">
                            </datalist>
                        </td>
                        <td class="text-center d-none"><input type="text" name="jenis_kain" class="form-control form-control-sm jenis_kain_new" ></td>
                        <td class="text-center"><input type="text" name="uom_kain" class="form-control form-control-sm uom_kain_new" ></td>
                        <td class="text-center"><input type="number" name="qty_kain" class="form-control form-control-sm qty_kain_new" step="0.001"></td>
                        <td class="text-center"><input type="number" name="harga" class="form-control form-control-sm harga_new" ></td>
                        <td colspan="2" class="text-center">
                            <a href="javascript:void(0);"  class="btn btn-xs btn-success add_list add_list_new" data-id="new" data-id_transaksi_2="' . $id_transaksi_2 . '"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                ';
            echo '
                </tbody>
            </table>
            ';
        } else {
            while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
                $no_id = $row['no_id'];
                $kode_bahan_kain = $row['kode_bahan_kain'];
                $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE kode_bahan_kain = '" . $row['kode_bahan_kain'] . "'"));
                // <p>' . $q_kain['jenis_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</p>
                // <p>' . $row['id_transaksi_2'] . '</p>
                echo '
                <br>
                <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins ">
                    <thead>
                        <tr>
                            <th class="text-center">Kode Barang</th>
                            <th class="text-center">Nama Barang</th>
                            <th class="text-center">UOM</th>
                            <th class="text-center">QTY</th>  
                            <th class="text-center">Harga</th>
                            <th class="text-center">Total</th>
                            <th class="text-center"></th>
                        </tr>
                    </thead>
                    <tbody>
                    ';
                $select_barang_masuk_2 = mysqli_query($conn, "SELECT * FROM tb_bom WHERE id_transaksi_2 = '" . $row['id_transaksi_2'] . "' AND id_transaksi = '" . $filter . "'");
                while ($row2 = mysqli_fetch_assoc($select_barang_masuk_2)) {
                    $kode_bahan_kain_2 = $row2['kode_bahan_kain'];
                    $jenis_kain = $row2['jenis_kain'];
                    $uom_kain = $row2['uom_kain'];
                    $qty_kain = $row2['qty_kain'];
                    $harga = $row2['harga'];
                    $total = $row2['total_harga'];

                    $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE kode_bahan_kain = '" . $kode_bahan_kain_2 . "'"));
                    echo '
                        <tr>
                            <td class="text-center">' . $kode_bahan_kain_2 . '</td>
                            <td class="text-center">' . $q_kain['nama_kain'] . '</td>
                            <td class="text-center">' . $uom_kain . '</td>
                            <td class="text-center">' . $qty_kain . '</td>
                            <td class="text-center">' . number_format($harga) . '</td>
                            <td class="text-center">' . number_format($total) . '</td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $no_id . '" data-id="' . $no_id . '"><i class="fa fa-trash-o"></i></a>
                            </td>
                        </tr>
                    ';
                }
                echo '
                        <tr >
                            <td colspan="2" class="text-center">
                                <input type="text" name="kode_bahan_kain' . $no_id . '" class="form-control form-control-sm kode_bahan_kain kode_bahan_kain_' . $no_id . '" autocomplete="off" list="list_kode_bahan_kain" onclick="this.select()" >
                                <datalist id="list_kode_bahan_kain" class="list_kode_bahan_kain">
                                </datalist>
                            </td>
                            <td class="text-center d-none"><input type="text" name="jenis_kain_' . $no_id . '" class="form-control form-control-sm jenis_kain_' . $no_id . '" ></td>
                            <td class="text-center"><input type="text" name="uom_kain_' . $no_id . '" class="form-control form-control-sm uom_kain_' . $no_id . '" ></td>
                            <td class="text-center"><input type="number" name="qty_kain_' . $no_id . '" class="form-control form-control-sm qty_kain_' . $no_id . '" step="0.001"></td>
                            <td class="text-center"><input type="number" name="harga_' . $no_id . '" class="form-control form-control-sm harga_' . $no_id . '" ></td>
                            <td colspan="2" class="text-center">
                                <a href="javascript:void(0);"  class="btn btn-xs btn-success add_list add_list_' . $no_id . '" data-id="' . $no_id . '" data-id_transaksi_2="' . $row['id_transaksi_2'] . '"><i class="fa fa-plus"></i></a>
                            </td>
                        </tr>
                    ';
                echo '
                    </tbody>
                </table>
                ';
            }
            echo '
                <a href="javascript:void(0);" class="btn btn-sm btn-success add_list_2 "data-id_transaksi_2="' . $id_transaksi_2 . '">Save</a>
                <br><br>
            ';
        }
    }
    echo $hasil;
}
