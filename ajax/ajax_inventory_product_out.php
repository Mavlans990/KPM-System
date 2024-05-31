<?php
ob_start();
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";


if (isset($_POST['add'])) {
    $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);
    $id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
    $id_inv = mysqli_real_escape_string($conn, $_POST['add']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $pemasangan = mysqli_real_escape_string($conn, $_POST['pemasangan']);
    $paket = mysqli_real_escape_string($conn, $_POST['paket']);
    $stat_cus = mysqli_real_escape_string($conn, $_POST['stat_cus']);
    $part = mysqli_real_escape_string($conn, $_POST['part']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $keterangan_part = mysqli_real_escape_string($conn, $_POST['keterangan_part']);

    $total = $price * $amount;

    $exec = mysqli_query($conn, "insert into inv_product_out ( id_inv_out,
                                                    inv_date,
                                                    id_product,
                                                    id_branch,
                                                    stock_out,
                                                    price,
                                                    total,
                                                    pemasangan,
                                                    paket,
                                                    stat_cus,
                                                    part,
                                                    keterangan,
                                                    keterangan_part,
                                                    tgl_pasang,
											        create_date,
                                                    create_by                                       
											    ) values(										            
                                                    '" . $id_inv . "',
                                                    '" . date("Y-m-d") . "',
                                                    '" . $id_product . "',
                                                    '" . $id_branch . "',
                                                    '" . $amount . "',
                                                    '" . $price . "',
                                                    '" . $total . "',
                                                    '" . $pemasangan . "',
                                                    '" . $paket . "',
                                                    '" . $stat_cus . "',
                                                    '" . $part . "',
                                                    '" . $keterangan . "',
                                                    '" . $keterangan_part . "',										
                                                    '" . $date . "',
                                                    '" . date('Y-m-d h:i:s') . "',
                                                    '" . $_SESSION['id_user'] . "'
                                                )");
    if ($id_inv !== $_SESSION['id_user']) {
        $query_get_product = mysqli_query($conn, "SELECT b.stock,
                                                a.type_product 
                                            FROM m_branch_stock b 
                                                LEFT JOIN m_product a ON b.id_product = a.m_product_id 
                                            WHERE b.id_product = '" . $id_product . "' and b.id_branch = '" . $id_branch . "' ");
        if ($row_stock = mysqli_fetch_array($query_get_product)) {
            if ($row_stock['type_product'] == "Ingredients") {
                $stock = intval($row_stock['stock']);
                $total_stock = $stock - $amount;
                mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_stock . "' WHERE id_product = '" . $id_product . "' and id_branch = '" . $id_branch . "' ");
            }
        } else {
            $query_get_product = mysqli_query($conn, "SELECT i.id_ingredients,
                                                    p.stock,
                                                    i.amount_ingredients 
                                                FROM m_branch_stock p 
                                                    LEFT JOIN m_product a ON p.id_product = a.m_product_id 
                                                    LEFT JOIN m_ingredients i ON i.id_ingredients = p.id_product 
                                                WHERE i.id_product = '" . $id_product . "' and p.id_branch = '" . $id_branch . "' ");
            while ($row_ingredients = mysqli_fetch_array($query_get_product)) {
                $stock_ingredients = $row_ingredients['stock'];
                $amount_product = $row_ingredients['amount_ingredients'];
                $total_out = $amount * $amount_product;
                $id_ingredients = $row_ingredients['id_ingredients'];
                $total_stock = $stock_ingredients - $total_out;
                mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_stock . "' WHERE id_product = '" . $id_ingredients . "' and id_branch = '" . $id_branch . "' ");
            }
        }
    }



    $no = 0;
    $query_get = mysqli_query($conn, "SELECT i.*,p.nm_product,p.uom_product,p.type_product 
                                FROM inv_product_out i
                                LEFT JOIN m_product p on i.id_product = p.m_product_id 
                                WHERE i.id_inv_out = '" . $id_inv . "'
                                and i.id_branch = '" . $id_branch . "'
                                ORDER BY inv_out_id desc");
    while ($row_product = mysqli_fetch_array($query_get)) {
        $id_inv = $row_product['inv_out_id'];
        $nm_product = $row_product['nm_product'];
        $id_branch = $row_product['id_branch'];
        $uom_product = $row_product['uom_product'];
        $amount = $row_product['stock_out'];
        $prices = $row_product['price'];

        $no++;

        $pemasangan = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['pemasangan'] == "pasang_sendiri") {
                $pemasangan = "<p>Pemasangan : Pasang Sendiri</p>";
            } else {
                $pemasangan = "<p>Pemasangan : " . ucfirst($row_product['pemasangan']) . "</p>";
            }
        }

        $paket = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['pemasangan'] == "dipasangkan") {
                if ($row_product['paket'] == "express") {
                    $paket = "<p>Paket : Express</p>";
                } else if ($row_product['paket'] == "dirumah") {
                    $paket = "<p>Paket : Pasang Dirumah</p>";
                } else {
                    $paket = "<p>Paket : Regular</p>";
                }
            }
        }

        $tanggal_pasang = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['pemasangan'] == "dipasangkan") {
                $tanggal_pasang = "<p>Tanggal Pasang : " . date("d M Y", strtotime($row_product['tgl_pasang'])) . "</p>";
            }
        }

        $cabang = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['pemasangan'] == "dipasangkan") {
                $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_product['id_branch'] . "'";
                $query_cabang = mysqli_query($conn, $select_cabang);
                $data_cabang = mysqli_fetch_array($query_cabang);
                if ($row_product['paket'] !== "dirumah") {
                    $cabang = "<p>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                }
            }
        }

        $stat_cus = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['stat_cus'] == "yes") {
                $stat_cus = "<p>Body Custom : Ya</p>";
            } else {
                $stat_cus = "<p>Body Custom : Tidak</p>";
            }
        }

        $part = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['part'] == "yes") {
                $part = "<p>Cat ulang / baret : Ya</p>";
            } else {
                $part = "<p>Cat ulang / baret : Tidak</p>";
            }
        }

        $keterangan = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['keterangan'] !== "") {
                if ($row_product['stat_cus'] == "yes" || $row_product['part'] == "yes") {
                    $keterangan = "<p>Keterangan body custom : " . $row_product['keterangan'] . "</p>";
                }
            }
        }

        $keterangan_part = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['keterangan_part'] !== "") {
                if ($row_product['part'] == "yes") {
                    $keterangan_part = "<p>Keterangan body cat ulang / baret : " . $row_product['keterangan_part'] . "</p>";
                }
            }
        }

        echo '
        <tr>
            <td style="font-size:11px;">' . $no . '</td>
            <td style="font-size:11px;">' . $nm_product . $pemasangan . $paket . $tanggal_pasang . $cabang . $stat_cus . $part . $keterangan . $keterangan_part . ' </td>
            <td style="font-size:11px;">
            <div class="input-group input-group-sm"><input type="number" class="form-control form-control-sm prices price_' . $id_inv . '"
                    name="price_' . $id_inv . '" value="' . $prices . '" id="price_' . $id_inv . '" data-id_inv="' . $id_inv . '">
                    <span
                    class="form-control filled-input form-control-sm input-group-text "
                    id="inputGroup-sizing-sm"> IDR </span>
                    </div></td>
            <td style="font-size:11px;"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm amounts amount_' . $id_inv . '"
                    name="nm_product_' . $id_inv . '" value="' . $amount . '" id="amount_' . $id_inv . '" data-id_inv="' . $id_inv . '" data-amount="' . $amount . '">
                    <span
                    class="form-control filled-input form-control-sm input-group-text "
                    id="inputGroup-sizing-sm"> ' . $uom_product . ' </span>
                    </div></td>
                    <td>
                    <a role="button" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list" data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                        <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                    <a role="button" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list" data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                        <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                    </a>
                </td>

        </tr>
        ';
    }
}

if (isset($_POST['edit'])) {
    $id_inv = mysqli_real_escape_string($conn, $_POST['edit']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);
    $id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
    $id_inv_filter = mysqli_real_escape_string($conn, $_POST['inv_filter']);

    if ($id_inv_filter !== $_SESSION['id_user']) {
        $query_get_inv = mysqli_query($conn, "SELECT id_product,stock_out,id_branch FROM inv_product_out WHERE inv_out_id = '" . $id_inv . "' ");
        if ($row_inv = mysqli_fetch_array($query_get_inv)) {
            $id_product = $row_inv['id_product'];
            $stock_inv = $row_inv['stock_out'];
            $id_branch = $row_inv['id_branch'];
            $query_get_stock = mysqli_query($conn, "SELECT b.stock,
                                                a.type_product 
                                            FROM m_branch_stock b 
                                                LEFT JOIN m_product a ON b.id_product = a.m_product_id 
                                            WHERE b.id_product = '" . $id_product . "' and b.id_branch = '" . $id_branch . "' ");
            if ($row_stock = mysqli_fetch_array($query_get_stock)) {
                $stock_origin = $row_stock['stock'];
                if ($row_stock['type_product'] == "Ingredients") {
                    $total_stock = $stock_origin + $stock_inv;
                    $query_set_product = mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_stock . "' WHERE id_product = '" . $id_product . "' and id_branch = '" . $id_branch . "' ");
                    $total_amount = $total_stock - $amount;
                    mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_amount . "' WHERE id_product = '" . $id_product . "' and id_branch = '" . $id_branch . "' ");
                }
            } else {
                $query_get_product = mysqli_query($conn, "SELECT i.id_ingredients,
                                                        p.stock,
                                                        i.amount_ingredients 
                                                    FROM m_branch_stock p 
                                                        LEFT JOIN m_product a ON p.id_product = a.m_product_id 
                                                        LEFT JOIN m_ingredients i ON i.id_ingredients = p.id_product 
                                                    WHERE i.id_product = '" . $id_product . "' and p.id_branch = '" . $id_branch . "' ");
                while ($row_ingredients = mysqli_fetch_array($query_get_product)) {
                    $stock_ingredients = $row_ingredients['stock'];
                    $amount_product = $row_ingredients['amount_ingredients'];
                    $id_ingredients = $row_ingredients['id_ingredients'];
                    $total_out = $stock_inv * $amount_product;
                    $total_stock = $stock_ingredients + $total_out;
                    mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_stock . "' WHERE id_product = '" . $id_ingredients . "' and id_branch = '" . $id_branch . "' ");
                    $total_out_amount = $amount * $amount_product;
                    $total_amount = $total_stock - $total_out_amount;
                    mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_amount . "' WHERE id_product = '" . $id_ingredients . "' and id_branch = '" . $id_branch . "' ");
                }
            }
            $total = $price * $amount;
            $exec = mysqli_query($conn, "UPDATE inv_product_out 
            SET stock_out ='" . $amount . "',
                price = '" . $price . "',
                total = '" . $total . "',
                change_date = '" . date('Y-m-d h:i:s') . "',
                change_by = '" . $_SESSION['id_user'] . "'                                     
            WHERE inv_out_id = '" . $id_inv . "' and id_branch = '" . $id_branch . "' ");
        }
    } else {

        $exec = mysqli_query($conn, "UPDATE inv_product_out 
                        SET stock_out ='" . $amount . "',
                            price = '" . $price . "',
                            total = '" . $total . "',
							change_date = '" . date('Y-m-d h:i:s') . "',
                            change_by = '" . $_SESSION['id_user'] . "'                                     
                        WHERE inv_out_id = '" . $id_inv . "' and id_branch = '" . $id_branch . "' ");
    }
}

if (isset($_POST['delete'])) {
    $id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
    $id_inv_filter = mysqli_real_escape_string($conn, $_POST['inv_filter']);
    $id_inv = mysqli_real_escape_string($conn, $_POST['delete']);
    $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);

    if ($id_inv_filter !== $_SESSION['id_user']) {
        $query_get_inv = mysqli_query($conn, "SELECT stock_out FROM inv_product_out WHERE inv_out_id = '" . $id_inv . "' ");
        if ($row_amount = mysqli_fetch_array($query_get_inv)) {
            $amount_origin = intval($row_amount['stock_out']);
            $query_get_product = mysqli_query($conn, "SELECT b.stock,
                                                a.type_product 
                                            FROM m_branch_stock b 
                                                LEFT JOIN m_product a ON b.id_product = a.m_product_id 
                                            WHERE b.id_product = '" . $id_product . "' and b.id_branch = '" . $id_branch . "' ");
            if ($row_stock = mysqli_fetch_array($query_get_product)) {
                if ($row_stock['type_product'] == "Ingredients") {
                    $stock = intval($row_stock['stock']);

                    $total_stock = $stock + $amount_origin;
                    mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_stock . "' WHERE id_product = '" . $id_product . "' and id_branch = '" . $id_branch . "' ");
                }
            } else {
                $query_get_product = mysqli_query($conn, "SELECT i.id_ingredients,
                                                        p.stock,
                                                        i.amount_ingredients 
                                                    FROM m_branch_stock p 
                                                        LEFT JOIN m_product a ON p.id_product = a.m_product_id 
                                                        LEFT JOIN m_ingredients i ON i.id_ingredients = p.id_product 
                                                    WHERE i.id_product = '" . $id_product . "' and p.id_branch = '" . $id_branch . "'  ");
                while ($row_ingredients = mysqli_fetch_array($query_get_product)) {
                    $stock_ingredients = $row_ingredients['stock'];
                    $amount_product = $row_ingredients['amount_ingredients'];
                    $total_out = $amount_origin * $amount_product;
                    $id_ingredients = $row_ingredients['id_ingredients'];
                    $total_stock = $stock_ingredients + $total_out;
                    mysqli_query($conn, "UPDATE m_branch_stock SET stock = '" . $total_stock . "' WHERE id_product = '" . $id_ingredients . "' and id_branch = '" . $id_branch . "' ");
                }
            }
        }
    }

    $exec = mysqli_query($conn, "DELETE FROM inv_product_out WHERE inv_out_id = '" . $id_inv . "' and id_branch = '" . $id_branch . "' ");

    $no = 0;
    $query_get = mysqli_query($conn, "SELECT i.*,p.nm_product,p.uom_product,p.type_product 
                                FROM inv_product_out i
                                LEFT JOIN m_product p on i.id_product = p.m_product_id 
                                WHERE i.id_inv_out = '" . $id_inv_filter . "'
                                and i.id_branch = '" . $id_branch . "'
                                ORDER BY inv_out_id desc");
    while ($row_product = mysqli_fetch_array($query_get)) {
        $id_inv = $row_product['inv_out_id'];
        $nm_product = $row_product['nm_product'];
        $uom_product = $row_product['uom_product'];
        $amount = $row_product['stock_out'];
        $prices = $row_product['price'];
        $no++;

        $pemasangan = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['pemasangan'] == "pasang_sendiri") {
                $pemasangan = "<p>Pemasangan : Pasang Sendiri</p>";
            } else {
                $pemasangan = "<p>Pemasangan : " . ucfirst($row_product['pemasangan']) . "</p>";
            }
        }

        $paket = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['pemasangan'] == "dipasangkan") {
                if ($row_product['paket'] == "express") {
                    $paket = "<p>Paket : Express</p>";
                } else if ($row_product['paket'] == "dirumah") {
                    $paket = "<p>Paket : Pasang Dirumah</p>";
                } else {
                    $paket = "<p>Paket : Regular</p>";
                }
            }
        }

        $tanggal_pasang = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['pemasangan'] == "dipasangkan") {
                $tanggal_pasang = "<p>Tanggal Pasang : " . date("d M Y", strtotime($row_product['tgl_pasang'])) . "</p>";
            }
        }

        $cabang = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['pemasangan'] == "dipasangkan") {
                $select_cabang = "SELECT * FROM tb_cabang WHERE id_cabang = '" . $row_product['id_branch'] . "'";
                $query_cabang = mysqli_query($conn, $select_cabang);
                $data_cabang = mysqli_fetch_array($query_cabang);
                if ($row_product['paket'] !== "dirumah") {
                    $cabang = "<p>Cabang : " . $data_cabang['nama_cabang'] . "</p>";
                }
            }
        }

        $stat_cus = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['stat_cus'] == "yes") {
                $stat_cus = "<p>Body Custom : Ya</p>";
            } else {
                $stat_cus = "<p>Body Custom : Tidak</p>";
            }
        }

        $part = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['part'] == "yes") {
                $part = "<p>Cat ulang / baret : Ya</p>";
            } else {
                $part = "<p>Cat ulang / baret : Tidak</p>";
            }
        }

        $keterangan = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['keterangan'] !== "") {
                if ($row_product['stat_cus'] == "yes") {
                    $keterangan = "<p>Keterangan body custom : " . $row_product['keterangan'] . "</p>";
                }
            }
        }

        $keterangan_part = "";
        if ($row_product['type_product'] == "Product") {
            if ($row_product['keterangan_part'] !== "") {
                if ($row_product['part'] == "yes") {
                    $keterangan_part = "<p>Keterangan body cat ulang / baret : " . $row_product['keterangan_part'] . "</p>";
                }
            }
        }

        echo '
        <tr>
            <td style="font-size:11px;">' . $no . '</td>
            <td style="font-size:11px;">' . $nm_product . $pemasangan . $paket . $tanggal_pasang . $cabang . $stat_cus . $part . $keterangan . $keterangan_part . ' </td>
            <td style="font-size:11px;"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm price_' . $id_inv . '"
                    name="nm_product_' . $id_inv . '" value="' . $prices . '" id="price_' . $id_inv . '" >
                    <span
                    class="form-control filled-input form-control-sm input-group-text "
                    id="inputGroup-sizing-sm"> IDR </span>
                    </div></td>
            <td style="font-size:11px;"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm amount_' . $id_inv . '"
                    name="nm_product_' . $id_inv . '" value="' . $amount . '" id="amount_' . $id_inv . '" >
                    <span
                    class="form-control filled-input form-control-sm input-group-text "
                    id="inputGroup-sizing-sm"> ' . $uom_product . ' </span>
                    </div></td>
                    <td>
                    <a role="button" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list" data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                        <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                    <a role="button" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list" data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                        <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                    </a>
                </td>

        </tr>
        ';
    }
}

if (isset($_POST['product'])) {
    $id = mysqli_real_escape_string($conn, $_POST['product']);
    $hasil = "";
    $uom_product = "(UOM)";
    $product = "";
    $query_get = mysqli_query($conn, "SELECT uom_product,type_product
                                FROM m_product 
                                WHERE m_product_id = '" . $id . "' ");
    if ($row_get = mysqli_fetch_array($query_get)) {
        $uom_product = $row_get['uom_product'];
        $product = $row_get['type_product'];
    }
    $hasil = $hasil . '
        <div class="input-group input-group-sm">
        <span 
        class="form-control filled-input form-control-sm input-group-text "
        id="inputGroup-sizing-sm"> ' . $uom_product . ' </span>
        </div>
        ';

    echo $hasil . "|" . $product;
}

if (isset($_POST['amount_product'])) {
    $id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount_product']);
    $id_inv_filter = mysqli_real_escape_string($conn, $_POST['id_inv']);
    $id_branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $add = "add_list";
    $btn_color = "btn-success";
    $stock = 0;
    $uom = "(UOM)";
    $type = "";
    $query_get_stock = mysqli_query($conn, "SELECT * FROM m_product a LEFT JOIN m_branch_stock b ON b.id_product = a.m_product_id WHERE a.m_product_id = '" . $id . "' and b.id_branch = '" . $id_branch . "' ");
    if ($row_stock = mysqli_fetch_array($query_get_stock)) {
        $stock = $row_stock['stock'];
        $uom = $row_stock['uom_product'];
        $type = $row_stock['type_product'];
        $border = "";
    } else {
        $query_get_stock = mysqli_query($conn, "SELECT * FROM m_product a WHERE a.m_product_id = '" . $id . "' ");
        if ($row_stock = mysqli_fetch_array($query_get_stock)) {
            $stock = 0;
            $uom = $row_stock['uom_product'];
            $type = $row_stock['type_product'];
            $border = "";
        }
    }

    if ($type == "Ingredients") {
        if ($amount > $stock || $amount == 0 || $amount == "" || $amount < 0) {
            $border = "border border-danger border-3";
            $add  = "";
            $tooltip = 'data-toggle="tooltip" data-placement="top" title="" ';
            $btn_color = "btn-secondary";
        }
    } else {

        $query_get_ingredients = mysqli_query($conn, "SELECT i.id_ingredients,
                                                     i.amount_ingredients 
                                              FROM m_ingredients i 
                                              WHERE i.id_product = '" . $id . "' ");
        while ($row_ingredients = mysqli_fetch_array($query_get_ingredients)) {
            $id_ingredients = $row_ingredients['id_ingredients'];
            $amount_ingredients = $row_ingredients['amount_ingredients'];
            $amount_ingredients = $amount_ingredients * $amount;
            $query_get_stock = mysqli_query($conn, "SELECT stock 
                                            FROM m_branch_stock 
                                            WHERE id_product = '" . $id_ingredients . "' 
                                                and id_branch = '" . $id_branch . "' ");
            if ($row_stock = mysqli_fetch_array($query_get_stock)) {
                $stock = $row_stock['stock'];
            } else {
                $stock = 0;
            }

            $amount_ingredients = $stock - $amount_ingredients;

            if ($amount_ingredients < 0 || $amount == 0 || $amount == "" || $amount < 0) {
                $border = "border border-danger border-3";
                $add  = "";
                $tooltip = 'data-toggle="tooltip" data-placement="top" title="" ';
                $btn_color = "btn-secondary";
            }
        }
        // if($amount == 0 || $amount == "" || $amount < 0){
        //     $border = "border border-danger border-3";
        //     $add  = "";
        //     $tooltip = 'data-toggle="tooltip" data-placement="top" title="" ';
        //     $btn_color = "btn-secondary";
        // }
    }


    echo '
                <a role="button" class="btn btn-sm btn-block ' . $btn_color . ' ' . $add . ' button-plus " data-id_inv="' . $id_inv_filter . '" data-id_branch="' . $id_branch . '"> 
                    <span class=""><i class="fa fa-plus text-white"></i></span>
                <a> 
        ';
}


ob_flush();
