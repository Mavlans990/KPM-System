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
    $sql_get_nama_supplier = mysqli_query($conn, "SELECT id_supplier,nama_supplier FROM tb_supplier WHERE nama_supplier LIKE '%" . $nama_supplier . "%' ORDER BY nama_supplier ASC LIMIT 50");
    while ($row_nama_supplier = mysqli_fetch_array($sql_get_nama_supplier)) {
        $hasil = $hasil . '
            <option value="' . $row_nama_supplier['id_supplier'] . ' | ' . $row_nama_supplier['nama_supplier'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_lot'])) {
    $nama_lot = mysqli_real_escape_string($conn, $_POST['cari_lot']);

    $hasil = "";

    $sql_get_nama_lot = mysqli_query($conn, "SELECT * FROM tb_lot WHERE nama_lot LIKE '%" . $nama_lot . "%' ORDER BY nama_lot ASC LIMIT 100");
    while ($row_nama_lot = mysqli_fetch_array($sql_get_nama_lot)) {
        $hasil = $hasil . '
            <option value="' . $row_nama_lot['nama_lot'] . '">
        ';
    }

    echo $hasil;
}

if (isset($_POST['cari_barcode'])) {
    $barcode = mysqli_real_escape_string($conn, $_POST['cari_barcode']);

    $hasil = "";

    $sql_get_barcode = mysqli_query($conn, "SELECT barcode FROM tb_barang_masuk WHERE barcode LIKE '%" . $barcode . "%' ORDER BY barcode ASC LIMIT 100");
    while ($row_barcode = mysqli_fetch_array($sql_get_barcode)) {
        $hasil = $hasil . '
            <option value="' . $row_barcode['barcode'] . '">
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

if (isset($_POST['filter_bahan'])) {
    $id_bahan = explode(" | ", $_POST['filter_bahan']);
    $id_bahan = $id_bahan[1];

    $select_bahan = "SELECT uom FROM tb_bahan WHERE id_bahan = '" . $id_bahan . "'";
    $query_bahan = mysqli_query($conn, $select_bahan);
    $data_bahan = mysqli_fetch_array($query_bahan);

    echo $data_bahan['uom'];
}

if (isset($_POST['add_list'])) {
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['add_list']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    // $qty_barang = mysqli_real_escape_string($conn, $_POST['qty_barang']);
    // $harga_barang = mysqli_real_escape_string($conn, $_POST['harga_barang']);
    // $berat_barang = mysqli_real_escape_string($conn, $_POST['berat_barang']);
    // $uom_barang = mysqli_real_escape_string($conn, $_POST['uom_barang']);

    // $total = $harga_barang * $qty_barang * $berat_barang;

    if ($id_transaksi == "new") {

        $no_id = id_gen_barang_masuk();
        $barcode = rand(1000000, 9999999);
        $clear = '';
        while ($clear == '') {
            $sql_barcode = mysqli_query($conn, "SELECT barcode FROM tb_barang_masuk WHERE barcode = '" . $barcode . "'");
            // echo "SELECT barcode FROM tb_barang_masuk WHERE barcode = '".$barcode."'";
            if ($row = mysqli_fetch_array($sql_barcode)) {
                $clear = '';
            } else {
                $clear = '1';
            }
        }

        $filter = $_SESSION['id_user'];


        $valid = 0;

        $id_bahan = explode(" - ", $nama_barang);
        $id_bahan = $id_bahan[0];

        $insert = "INSERT INTO tb_barang_masuk(
            no_id,
            id_transaksi,
            tgl_transaksi,
            id_bahan_kain,
            dibuat_oleh,
            dibuat_tgl,
            barcode
        ) VALUES(
            '" . $no_id . "',
            '" . $_SESSION['id_user'] . "',
            '" . date("Y-m-d") . "',
            '" . $id_bahan . "',
            '" . $_SESSION['id_user'] . "',
            '" . date("Y-m-d") . "',
            '" . $barcode . "'
        )";
        // echo $insert;

        $query = mysqli_query($conn, $insert);
    } else {
        $filter = $id_transaksi;

        $no_id = id_gen_barang_masuk();
        $barcode = rand(1000000, 9999999);
        $clear = '';
        while ($clear == '') {
            $sql_barcode = mysqli_query($conn, "SELECT barcode FROM tb_barang_masuk WHERE barcode = '" . $barcode . "'");
            // echo "SELECT barcode FROM tb_barang_masuk WHERE barcode = '".$barcode."'";
            if ($row = mysqli_fetch_array($sql_barcode)) {
                $clear = '';
            } else {
                $clear = '1';
            }
        }

        $select_barang_masuk = "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $id_transaksi . "' GROUP BY id_transaksi";
        $query_barang_masuk = mysqli_query($conn, $select_barang_masuk);
        $data_barang_masuk = mysqli_fetch_array($query_barang_masuk);


        $valid = 0;

        $id_bahan = explode(" - ", $nama_barang);
        $id_bahan = $id_bahan[0];


        $insert = "INSERT INTO tb_barang_masuk(
            no_id,
            id_transaksi,
            tgl_transaksi,
            id_bahan_kain,
            dibuat_oleh,
            dibuat_tgl,
            barcode
        ) VALUES(
            '" . $no_id . "',
            '" . $id_transaksi . "',
            '" . date("Y-m-d") . "',
            '" . $id_bahan . "',
            '" . $_SESSION['id_user'] . "',
            '" . date("Y-m-d") . "',
            '" . $barcode . "'
        )";
        // echo $insert;

        $query = mysqli_query($conn, $insert);
    }
    $hasil = '';

    $ksl_bruto = 0;
    $ksl_netto = 0;
    $ksl_susut = 0;
    $ksl_shrinkage = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $filter . "' GROUP BY id_bahan_kain");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_bahan_kain = $row['id_bahan_kain'];
        $keterangan = $row['keterangan'];
        $penerima = $row['penerima'];
        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
        $hasil = $hasil . '
        <br>
        <p>' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</p>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins">
            <thead>
                <tr>
                    <th class="text-center">Lot</th>
                    <th class="text-center">Roll</th>
                    <th class="text-center">Bruto</th>
                    <th class="text-center">Netto</th>
                    <th class="text-center">Susut</th>
                    <th class="text-center">Shrinkage</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            ';
        $pecahan_lot = explode(" | ", $row['lot']);
        $pecahan_lot = $pecahan_lot[0];
        $sql_lot = mysqli_query($conn, "SELECT SUBSTRING(lot, 1, 5) AS lot_group FROM tb_barang_masuk WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "' AND id_transaksi = '" . $filter . "' GROUP By lot_group");
        while ($lot_row = mysqli_fetch_assoc($sql_lot)) {
            $ttl_bruto = 0;
            $ttl_netto = 0;
            $ttl_susut = 0;
            $ttl_shrinkage = 0;

            $pecahan_lot2 = explode(" | ", $row['lot']);
            $pecahan_lot2 = $pecahan_lot2[0];
            $select_barang_masuk_3 = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "' AND id_transaksi = '" . $filter . "' AND lot LIKE '%" . $lot_row['lot_group'] . "%' ORDER BY lot ASC ");
            while ($row3 = mysqli_fetch_assoc($select_barang_masuk_3)) {
                $no_id = $row3['no_id'];
                $lot = $row3['lot'];
                $bruto = $row3['bruto'];
                $netto = $row3['netto'];
                $susut = $row3['susut'];
                $shrinkage = $row3['shrinkage'];
                error_reporting(0);
                $lot_baru = explode(" | ", $row3['lot']);
                $hasil = $hasil . '
                        <tr>
                            <td class="text-center">' . $lot_baru[0] . '</td>
                            <td class="text-center">' . $lot_baru[1] . '</td>
                            <td class="text-center">' . $bruto . '</td>
                            <td class="text-center">' . $netto . '</td>
                            <td class="text-center">' . $susut . '</td>
                            <td class="text-center">' . $shrinkage . ' %</td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $row3['no_id'] . '" data-id="' . $row3['no_id'] . '"><i class="fa fa-trash-o"></i></a>
                                <a href="invbarcodeinbound.php?id_lot=' . $row3['no_id'] . '" class="btn btn-xs btn-info print_list print_list_' . $row3['no_id'] . '" data-id="' . $row3['no_id'] . '" target="_blank"><i class="fa fa-print"></i></a>
                            </td>
                        </tr>
                    ';
                $ttl_bruto += $bruto;
                $ttl_netto += $netto;
                $ttl_susut += $susut;
                $ttl_shrinkage += $shrinkage;
                $ksl_bruto += $bruto;
                $ksl_netto += $netto;
                $ksl_susut += $susut;
                $ksl_shrinkage += $shrinkage;
            }
            $hasil = $hasil . '
                <tr>
                    <td class="text-center" colspan="2" style="color:red">Total</td>
                    <td class="text-center" style="color:red">' . $ttl_bruto . '</td>
                    <td class="text-center" style="color:red">' . $ttl_netto . '</td>
                    <td class="text-center" style="color:red">' . $ttl_susut . '</td>
                    <td class="text-center" style="color:red">'. $ttl_shrinkage .' %</td>
                    <td class="text-center">
                    </td>
                </tr>
                ';
        }

        $hasil = $hasil . '
                <tr>
                    <td class="text-center">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LOT : </span>
                            </div>
                            <input type="number" name="lot_' . $no_id . '" class="form-control form-control-sm lot_' . $no_id . '"  ' . $readonly . ' >
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">ROLL : </span>
                            </div>
                            <input type="number" name="roll_' . $no_id . '" class="form-control form-control-sm roll_' . $no_id . '" ' . $readonly . ' >
                        </div>
                    </td>
                    <td class="text-center"><input type="number" name="bruto_' . $no_id . '" class="form-control form-control-sm bruto_' . $no_id . '" step="0.001"></td>
                    <td class="text-center"><input type="number" name="netto_' . $no_id . '" class="form-control form-control-sm netto_' . $no_id . '" step="0.001"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center">
                        <a href="javascript:void(0);" class="btn btn-xs btn-success add_list_2 add_list_2_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '"><i class="fa fa-plus"></i></a>
                    </td>
                </tr>
            ';
        $hasil = $hasil . '
            </tbody>
        </table>
        ';
    }
    $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_bruto . '" readonly>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3 d-none">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL NETTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_celup" value="' . $ksl_netto . '" readonly >
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3 d-none">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL SUSUT</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_celup" value="' . $ksl_susut . '" readonly >
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3 d-none">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL SHRINKAGE</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_celup" value="' . $ksl_shrinkage . '" readonly >
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Penerima</span>
                            </div>
                            <input type="text" name="penerima" id="" class="form-control form-control-sm penerima" value="' . $penerima . '">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Keterangan</span>
                            </div>
                            <textarea name="keterangan" id="" style="height:33px;" class="form-control form-control-sm keterangan " autocomplete="off" onclick="this.select();">' . $keterangan . '</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    ';
    echo $hasil;
}

if (isset($_POST['add_list_2'])) {
    $id_tambah = mysqli_real_escape_string($conn, $_POST['add_list_2']);
    $id_transaksi = mysqli_real_escape_string($conn, $_POST['id_transaksi']);
    $id_bahan_kain = mysqli_real_escape_string($conn, $_POST['id_bahan_kain']);
    $lot = mysqli_real_escape_string($conn, $_POST['lot']);
    $roll = mysqli_real_escape_string($conn, $_POST['roll']);
    $bruto = mysqli_real_escape_string($conn, $_POST['bruto']);
    $netto = mysqli_real_escape_string($conn, $_POST['netto']);
    // $susut = mysqli_real_escape_string($conn, $_POST['susut']);
    // $shrinkage = mysqli_real_escape_string($conn, $_POST['shrinkage']);
    $lot_baru = 'LOT-' . $lot . ' | ' . 'ROLL-' . $roll;
    $susut = $bruto - $netto;
    $shrinkage = $susut / $bruto * 100;
    $rand = mt_rand(0, 99999999999);

    if ($id_transaksi == "new") {

        $filter = $_SESSION['id_user'];

        if ($id_tambah != "") {
            $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE no_id = '" . $id_tambah . "' AND id_bahan_kain = '" . $id_bahan_kain . "'"));
            $lot_masuk = $c_masuk['lot'];
        } else {
            $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk"));
        }


        if ($lot_masuk == "") {
            $update = "UPDATE tb_barang_masuk SET
                lot = '" . $lot_baru . "',
                bruto = '" . $bruto . "',
                netto = '" . $netto . "',
                susut = '" . $susut . "',
                shrinkage = '" . $shrinkage . "' WHERE no_id = '" . $id_tambah . "';
            ";

            $query = mysqli_query($conn, $update);
        } else {
            $no_id = id_gen_barang_masuk();
            $barcode = rand(1000000, 9999999);
            $clear = '';
            while ($clear == '') {
                $sql_barcode = mysqli_query($conn, "SELECT barcode FROM tb_barang_masuk WHERE barcode = '" . $barcode . "'");
                // echo "SELECT barcode FROM tb_barang_masuk WHERE barcode = '".$barcode."'";
                if ($row = mysqli_fetch_array($sql_barcode)) {
                    $clear = '';
                } else {
                    $clear = '1';
                }
            }


            $valid = 0;

            $insert = "INSERT INTO tb_barang_masuk(
                no_id,
                id_transaksi,
                tgl_transaksi,
                id_bahan_kain,
                dibuat_oleh,
                dibuat_tgl,
                lot,
                bruto,
                netto,
                susut,
                shrinkage,
                barcode
            ) VALUES(
                '" . $no_id . "',
                '" . $_SESSION['id_user'] . "',
                '" . date("Y-m-d") . "',
                '" . $id_bahan_kain . "',
                '" . $_SESSION['id_user'] . "',
                '" . date("Y-m-d") . "',
                '" . $lot_baru . "',
                '" . $bruto . "',
                '" . $netto . "',
                '" . $susut . "',
                '" . $shrinkage . "',
                '" . $barcode . "'

            )";

            $query = mysqli_query($conn, $insert);
        }
    } else {
        $filter = $id_transaksi;

        if ($id_tambah != "") {
            $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE no_id = '" . $id_tambah . "' AND id_bahan_kain = '" . $id_bahan_kain . "'"));
            $lot_masuk = $c_masuk['lot'];
        } else {
            $c_masuk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_barang_masuk"));
        }


        if ($lot_masuk == "") {
            $update = "UPDATE tb_barang_masuk SET
                lot = '" . $lot_baru . "',
                bruto = '" . $bruto . "',
                netto = '" . $netto . "',
                susut = '" . $susut . "',
                shrinkage = '" . $shrinkage . "' WHERE no_id = '" . $id_tambah . "';
            ";

            $query = mysqli_query($conn, $update);
        } else {
            $no_id = id_gen_barang_masuk();
            $barcode = rand(1000000, 9999999);
            $clear = '';
            while ($clear == '') {
                $sql_barcode = mysqli_query($conn, "SELECT barcode FROM tb_barang_masuk WHERE barcode = '" . $barcode . "'");
                // echo "SELECT barcode FROM tb_barang_masuk WHERE barcode = '".$barcode."'";
                if ($row = mysqli_fetch_array($sql_barcode)) {
                    $clear = '';
                } else {
                    $clear = '1';
                }
            }

            $valid = 0;

            $insert = "INSERT INTO tb_barang_masuk(
                no_id,
                id_transaksi,
                tgl_transaksi,
                id_bahan_kain,
                dibuat_oleh,
                dibuat_tgl,
                lot,
                bruto,
                netto,
                susut,
                shrinkage,
                barcode
            ) VALUES(
                '" . $no_id . "',
                '" . $id_transaksi . "',
                '" . date("Y-m-d") . "',
                '" . $id_bahan_kain . "',
                '" . $_SESSION['id_user'] . "',
                '" . date("Y-m-d") . "',
                '" . $lot_baru . "',
                '" . $bruto . "',
                '" . $netto . "',
                '" . $susut . "',
                '" . $shrinkage . "',
                '" . $barcode . "'

            )";

            $query = mysqli_query($conn, $insert);
        }
    }
    $hasil = '';

    $ksl_bruto = 0;
    $ksl_netto = 0;
    $ksl_susut = 0;
    $ksl_shrinkage = 0;
    $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $filter . "' GROUP BY id_bahan_kain");
    while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
        $id_bahan_kain = $row['id_bahan_kain'];
        $keterangan = $row['keterangan'];
        $penerima = $row['penerima'];
        $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
        $hasil = $hasil . '
        <br>
        <p>' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</p>
        <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins">
            <thead>
                <tr>
                    <th class="text-center">Lot</th>
                    <th class="text-center">Roll</th>
                    <th class="text-center">Bruto</th>
                    <th class="text-center">Netto</th>
                    <th class="text-center">Susut</th>
                    <th class="text-center">Shrinkage</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            ';
        $pecahan_lot = explode(" | ", $row['lot']);
        $pecahan_lot = $pecahan_lot[0];
        $sql_lot = mysqli_query($conn, "SELECT SUBSTRING(lot, 1, 5) AS lot_group FROM tb_barang_masuk WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "' AND id_transaksi = '" . $filter . "' GROUP By lot_group");
        while ($lot_row = mysqli_fetch_assoc($sql_lot)) {
            $ttl_bruto = 0;
            $ttl_netto = 0;
            $ttl_susut = 0;
            $ttl_shrinkage = 0;

            $pecahan_lot2 = explode(" | ", $row['lot']);
            $pecahan_lot2 = $pecahan_lot2[0];
            $select_barang_masuk_3 = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "' AND id_transaksi = '" . $filter . "' AND lot LIKE '%" . $lot_row['lot_group'] . "%' ORDER BY lot ASC ");
            while ($row3 = mysqli_fetch_assoc($select_barang_masuk_3)) {
                $no_id = $row3['no_id'];
                $lot = $row3['lot'];
                $bruto = $row3['bruto'];
                $netto = $row3['netto'];
                $susut = $row3['susut'];
                $shrinkage = $row3['shrinkage'];
                error_reporting(0);
                $lot_baru = explode(" | ", $row3['lot']);
                $hasil = $hasil . '
                        <tr>
                            <td class="text-center">' . $lot_baru[0] . '</td>
                            <td class="text-center">' . $lot_baru[1] . '</td>
                            <td class="text-center">' . $bruto . '</td>
                            <td class="text-center">' . $netto . '</td>
                            <td class="text-center">' . $susut . '</td>
                            <td class="text-center">' . $shrinkage . ' %</td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $row3['no_id'] . '" data-id="' . $row3['no_id'] . '"><i class="fa fa-trash-o"></i></a>
                                <a href="invbarcodeinbound.php?id_lot=' . $row3['no_id'] . '" class="btn btn-xs btn-info print_list print_list_' . $row3['no_id'] . '" data-id="' . $row3['no_id'] . '" target="_blank"><i class="fa fa-print"></i></a>
                            </td>
                        </tr>
                    ';
                $ttl_bruto += $bruto;
                $ttl_netto += $netto;
                $ttl_susut += $susut;
                $ttl_shrinkage += $shrinkage;
                $ksl_bruto += $bruto;
                $ksl_netto += $netto;
                $ksl_susut += $susut;
                $ksl_shrinkage += $shrinkage;
            }
            $hasil = $hasil . '
                <tr>
                    <td class="text-center" colspan="2" style="color:red">Total</td>
                    <td class="text-center" style="color:red">' . $ttl_bruto . '</td>
                    <td class="text-center" style="color:red">' . $ttl_netto . '</td>
                    <td class="text-center" style="color:red">' . $ttl_susut . '</td>
                    <td class="text-center" style="color:red">'. $ttl_shrinkage .' %</td>
                    <td class="text-center">
                    </td>
                </tr>
                ';
        }

        $hasil = $hasil . '
                <tr>
                    <td class="text-center">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LOT : </span>
                            </div>
                            <input type="number" name="lot_' . $no_id . '" class="form-control form-control-sm lot_' . $no_id . '"  ' . $readonly . '>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span style="" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">ROLL : </span>
                            </div>
                            <input type="number" name="roll_' . $no_id . '" class="form-control form-control-sm roll_' . $no_id . '" ' . $readonly . ' >
                        </div>
                    </td>
                    <td class="text-center"><input type="number" name="bruto_' . $no_id . '" class="form-control form-control-sm bruto_' . $no_id . '" step="0.001"></td>
                    <td class="text-center"><input type="number" name="netto_' . $no_id . '" class="form-control form-control-sm netto_' . $no_id . '" step="0.001"></td>
                    <td class="text-center"></td>
                    <td class="text-center"></td>
                    <td class="text-center">
                        <a href="javascript:void(0);" class="btn btn-xs btn-success add_list_2 add_list_2_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '"><i class="fa fa-plus"></i></a>
                    </td>
                </tr>
            ';
        $hasil = $hasil . '
            </tbody>
        </table>
        ';
    }
    $hasil = $hasil . '
    <br>
    <div class="col-sm">
        <div class="row">
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_bruto . '" readonly>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3 d-none">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL NETTO</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_celup" value="' . $ksl_netto . '" readonly >
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3 d-none">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL SUSUT</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_celup" value="' . $ksl_susut . '" readonly >
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3 d-none">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL SHRINKAGE</span>
                            </div>
                            <input type="text" name="" id="" class="form-control form-control-sm no_sj_celup" value="' . $ksl_shrinkage . '" readonly >
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row no-gutter">
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Penerima</span>
                            </div>
                            <input type="text" name="penerima" id="" class="form-control form-control-sm penerima" value="' . $penerima . '">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Keterangan</span>
                            </div>
                            <textarea name="keterangan" id="" style="height:33px;" class="form-control form-control-sm keterangan " autocomplete="off" onclick="this.select();">' . $keterangan . '</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    ';
    echo $hasil;
}

if (isset($_POST['id_hapus'])) {
    $valid = 0;
    $hasil = "";
    $id_hapus = mysqli_real_escape_string($conn, $_POST['id_hapus']);
    $id_hapus_transaksi = mysqli_real_escape_string($conn, $_POST['id_hapus_transaksi']);
    // $id_cabang = mysqli_real_escape_string($conn, $_POST['id_cabang']);




    $delete = "DELETE FROM tb_barang_masuk WHERE no_id = '" . $id_hapus . "'";
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
        $keterangan = "";
        $penerima = "";
        $select_barang_masuk = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_transaksi = '" . $filter . "' GROUP BY id_bahan_kain");
        $jum_data = mysqli_num_rows($select_barang_masuk);
        while ($row = mysqli_fetch_assoc($select_barang_masuk)) {
            $id_bahan_kain = $row['id_bahan_kain'];
            $keterangan = $row['keterangan'];
            $penerima = $row['penerima'];
            $q_kain = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM tb_bahan_kain WHERE id_bahan_kain = '" . $id_bahan_kain . "'"));
            $hasil = $hasil . '
            <br>
            <p>' . $q_kain['nama_kain'] . ' - ' . $q_kain['warna'] . ' - ' . $q_kain['setting'] . ' - ' . $q_kain['gramasi'] . '</p>
            <table class="table table-bordered table-sm w-100 display tb_jps_re_ins tb_jps_ins">
                <thead>
                    <tr>
                        <th class="text-center">Lot</th>
                        <th class="text-center">Roll</th>
                        <th class="text-center">Bruto</th>
                        <th class="text-center">Netto</th>
                        <th class="text-center">Susut</th>
                        <th class="text-center">Shrinkage</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                ';
            $pecahan_lot = explode(" | ", $row['lot']);
            $pecahan_lot = $pecahan_lot[0];
            $sql_lot = mysqli_query($conn, "SELECT SUBSTRING(lot, 1, 5) AS lot_group FROM tb_barang_masuk WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "' AND id_transaksi = '" . $filter . "' GROUP By lot_group");
            while ($lot_row = mysqli_fetch_assoc($sql_lot)) {
                $ttl_bruto = 0;
                $ttl_netto = 0;
                $ttl_susut = 0;
                $ttl_shrinkage = 0;
                $select_barang_masuk_3 = mysqli_query($conn, "SELECT * FROM tb_barang_masuk WHERE id_bahan_kain = '" . $row['id_bahan_kain'] . "' AND id_transaksi = '" . $filter . "' AND lot LIKE '%" . $lot_row['lot_group'] . "%' ORDER BY lot ASC ");
                while ($row3 = mysqli_fetch_assoc($select_barang_masuk_3)) {
                    $no_id = $row3['no_id'];
                    $lot = $row3['lot'];
                    $bruto = $row3['bruto'];
                    $netto = $row3['netto'];
                    $susut = $row3['susut'];
                    $shrinkage = $row3['shrinkage'];
                    error_reporting(0);
                    $lot_baru = explode(" | ", $row3['lot']);
                    $hasil = $hasil . '
                                <tr>
                                    <td class="text-center">' . $lot_baru[0] . '</td>
                                    <td class="text-center">' . $lot_baru[1] . '</td>
                                    <td class="text-center">' . $bruto . '</td>
                                    <td class="text-center">' . $netto . '</td>
                                    <td class="text-center">' . $susut . '</td>
                                    <td class="text-center">' . $shrinkage . ' %</td>
                                    <td class="text-center">
                                        <a href="javascript:void(0);" class="btn btn-xs btn-danger del_list del_list_' . $row3['no_id'] . '" data-id="' . $row3['no_id'] . '"><i class="fa fa-trash-o"></i></a>
                                        <a href="invbarcodeinbound.php?id_lot=' . $row3['no_id'] . '" class="btn btn-xs btn-info print_list print_list_' . $row3['no_id'] . '" data-id="' . $row3['no_id'] . '" target="_blank"><i class="fa fa-print"></i></a>
                                    </td>
                                </tr>
                            ';
                    $ttl_bruto += $bruto;
                    $ttl_netto += $netto;
                    $ttl_susut += $susut;
                    $ttl_shrinkage += $shrinkage;
                    $ksl_bruto += $bruto;
                    $ksl_netto += $netto;
                    $ksl_susut += $susut;
                    $ksl_shrinkage += $shrinkage;
                }
                $hasil = $hasil . '
                        <tr>
                            <td class="text-center" colspan="2" style="color:red">Total</td>
                            <td class="text-center" style="color:red">' . $ttl_bruto . '</td>
                            <td class="text-center" style="color:red">' . $ttl_netto . '</td>
                            <td class="text-center" style="color:red">' . $ttl_susut . '</td>
                            <td class="text-center" style="color:red">'. $ttl_shrinkage .' %</td>
                            <td class="text-center">
                            </td>
                        </tr>
                        ';
            }
            $hasil = $hasil . '
                    <tr>
                        <td class="text-center">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span style="" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">LOT : </span>
                                </div>
                                <input type="number" name="lot_' . $no_id . '" class="form-control form-control-sm lot_' . $no_id . '"  ' . $readonly . ' >
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span style="" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">ROLL : </span>
                                </div>
                                <input type="number" name="roll_' . $no_id . '" class="form-control form-control-sm roll_' . $no_id . '" ' . $readonly . ' >
                            </div>
                        </td>
                        <td class="text-center"><input type="number" name="bruto_' . $no_id . '" class="form-control form-control-sm bruto_' . $no_id . '" step="0.001"></td>
                        <td class="text-center"><input type="number" name="netto_' . $no_id . '" class="form-control form-control-sm netto_' . $no_id . '" step="0.001"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center">
                            <a href="javascript:void(0);" class="btn btn-xs btn-success add_list_2 add_list_2_' . $no_id . '" data-id="' . $no_id . '" data-id_bahan_kain="' . $id_bahan_kain . '"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                ';
            $hasil = $hasil . '
                </tbody>
            </table>
            ';
        }
    }
    if ($jum_data >= 1) {
        $hasil = $hasil . '
        <br>
        <div class="col-sm">
            <div class="row">
                <div class="col-md-6">
                    <div class="row no-gutter">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL BRUTO</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_greige" value="' . $ksl_bruto . '" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3 d-none">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL NETTO</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_celup" value="' . $ksl_netto . '" readonly >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3 d-none">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL SUSUT</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_celup" value="' . $ksl_susut . '" readonly >
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3 d-none">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">TOTAL SHRINKAGE</span>
                                </div>
                                <input type="text" name="" id="" class="form-control form-control-sm no_sj_celup" value="' . $ksl_shrinkage . '" readonly >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row no-gutter">
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Penerima</span>
                                </div>
                                <input type="text" name="penerima" id="" class="form-control form-control-sm penerima" value="' . $penerima . '">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span style="width:150px;" class="form-control form-control-sm input-group-text " id="inputGroup-sizing-sm">Keterangan</span>
                                </div>
                                <textarea name="keterangan" id="" style="height:33px;" class="form-control form-control-sm keterangan " autocomplete="off" onclick="this.select();">' . $keterangan . '</textarea>
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
