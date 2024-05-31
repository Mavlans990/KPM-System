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
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $in_stk = mysqli_real_escape_string($conn, $_POST['in_stk']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);

    $exec = mysqli_query($conn, "insert into inv_adjust ( id_inv_adj,
                                                    inv_date,
                                                    id_product,
                                                    id_branch,
                                                    stock_adj,
                                                    actual_stock,
                                                    in_stock,
											        create_date,
                                                    create_by                                       
											    ) values(										            
                                                    '" . $id_inv . "',
                                                    '" . $date . "',
                                                    '" . $id_product . "',
                                                    '" . $id_branch . "',
										            '" . $amount . "',	
                                                    '" . $stock . "',	
                                                    '" . $in_stk . "',                                                    										
                                                    '" . date('Y-m-d h:i:s') . "',
                                                    '" . $_SESSION['id_user'] . "'
                                                )");
    if ($id_inv !== $_SESSION['id_user']) {
        $query_get_stock = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $id_branch . "'  ");
        if ($row_stock = mysqli_fetch_array($query_get_stock)) {
            $stock_origin = $row_stock['stock'];

            $total_stock = $stock_origin + $amount;
            mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $id_branch . "' ");
        } else {
            mysqli_query($conn, "insert into tb_stock_cabang (id_bahan,
                                                            id_cabang,
                                                            stock                                    
                                                        ) values(		
                                                            '" . $id_product . "',
                                                            '" . $id_branch . "',
                                                            '" . $amount . "'
                                                        )");
        }
    }

    $no = 0;
    $query_get_product = mysqli_query($conn, "SELECT i.*,p.nama_bahan,p.uom
                                FROM inv_adjust i
                                LEFT JOIN tb_bahan p on i.id_product = p.id_bahan 
                                WHERE i.id_inv_adj = '" . $id_inv . "'
                                and i.id_branch = '" . $id_branch . "'
                                ORDER BY inv_adj_id desc");
    while ($row_product = mysqli_fetch_array($query_get_product)) {
        $no++;
        $id_inv = $row_product['inv_adj_id'];
        $id_product = $row_product['id_product'];
        $id_branch = $row_product['id_branch'];
        $nm_product = $row_product['nama_bahan'];
        $actual = $row_product['actual_stock'];
        $amount = $row_product['stock_adj'];
        $uom_product = $row_product['uom'];
        $in_stock = $row_product['in_stock'];
        echo '
    <tr>
        <td style="font-size:11px;">' . $no . '</td>
        <td style="font-size:11px;">' . $nm_product . ' </td>
        <td style="font-size:11px;">
            <input type="number" class="form-control filled-input form-control-sm stock_' . $id_inv . '" value="' . $in_stock . '" id="stock_' . $id_inv . '" readonly autocomplete="off" onclick="this.select();">
        </td>
        <td style="font-size:11px;">
            <input type="number" class="form-control form-control-sm actual_' . $id_inv . '" value="' . $actual . '" data-id_key="' . $id_inv . '" id="actual_' . $id_inv . '" autocomplete="off" onclick="this.select();">
        </td>
        <td style="font-size:11px;">
            <input type="number" class="form-control filled-input form-control-sm adjust_' . $id_inv . '" value="' . $amount . '" id="adjust_' . $id_inv . '" readonly autocomplete="off" onclick="this.select();">
        </td>
        <td>
            <a role="button"
                class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list"
                data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                <span class="btn-icon-wrap"><i
                        class="fa fa-pencil"></i></span></a>
            <a role="button"
                class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list"
                data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                <span class="btn-icon-wrap"><i
                        class="icon-trash"></i></span>
            </a>
        </td>
    </tr>
    ';
    }
}

if (isset($_POST['edit'])) {
    $id_inv = mysqli_real_escape_string($conn, $_POST['edit']);
    $in_stk = mysqli_real_escape_string($conn, $_POST['in_stk']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);
    $id_inv_filter = mysqli_real_escape_string($conn, $_POST['inv_filter']);

    if ($id_inv_filter !== $_SESSION['id_user']) {
        $query_get_inv = mysqli_query($conn, "SELECT id_product,stock_adj FROM inv_adjust WHERE inv_adj_id = '" . $id_inv . "' ");
        if ($row_inv = mysqli_fetch_array($query_get_inv)) {
            $id_product = $row_inv['id_product'];
            $stock_adj = $row_inv['stock_adj'];
            $query_get_stock = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $id_branch . "'  ");
            if ($row_stock = mysqli_fetch_array($query_get_stock)) {
                $stock_origin = $row_stock['stock'];

                $total_stock = $stock_origin - $stock_adj;
                $query_set_product = mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $id_branch . "' ");
                $total_amount = $total_stock + $amount;

                $exec = mysqli_query($conn, "UPDATE inv_adjust 
                        SET stock_adj ='" . $amount . "',
							change_date = '" . date('Y-m-d h:i:s') . "',
                            change_by = '" . $_SESSION['id_user'] . "'                                     
                        WHERE inv_adj_id = '" . $id_inv . "' and id_branch = '" . $id_branch . "' ");

                mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_amount . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $id_branch . "' ");
            } else {
                mysqli_query($conn, "insert into tb_stock_cabang (id_bahan,
                                                            id_cabang,
                                                            stock                                    
                                                        ) values(		
                                                            '" . $id_product . "',
                                                            '" . $id_branch . "',
                                                            '" . $amount . "'
                                                        )");
            }
        }
    } else {
        $exec = mysqli_query($conn, "UPDATE inv_adjust 
                        SET stock_adj ='" . $amount . "',
                            actual_stock ='" . $stock . "',
							change_date = '" . date('Y-m-d h:i:s') . "',
                            change_by = '" . $_SESSION['id_user'] . "'                                     
                        WHERE inv_adj_id = '" . $id_inv . "' and id_branch = '" . $id_branch . "' ");
    }
}

if (isset($_POST['delete'])) {
    $id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
    $id_inv_filter = mysqli_real_escape_string($conn, $_POST['inv_filter']);
    $id_inv = mysqli_real_escape_string($conn, $_POST['delete']);
    $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);

    if ($id_inv_filter !== $_SESSION['id_user']) {
        $query_get_inv = mysqli_query($conn, "SELECT stock_adj FROM inv_adjust WHERE inv_adj_id = '" . $id_inv . "' ");
        if ($row_amount = mysqli_fetch_array($query_get_inv)) {
            $amount_origin = intval($row_amount['stock_adj']);
            $query_get_product = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $id_branch . "'  ");
            if ($row_amount = mysqli_fetch_array($query_get_product)) {
                $stock = intval($row_amount['stock']);

                $total_stock = $stock - $amount_origin;
                mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $id_branch . "'");
            }
        }
    }

    $exec = mysqli_query($conn, "DELETE FROM inv_adjust WHERE inv_adj_id = '" . $id_inv . "' and id_branch = '" . $id_branch . "' ");

    $no = 0;
    $query_get_product = mysqli_query($conn, "SELECT i.*,p.nama_bahan,p.uom
                                FROM inv_adjust i
                                LEFT JOIN tb_bahan p on i.id_product = p.id_bahan 
                                WHERE i.id_inv_adj = '" . $id_inv_filter . "'
                                and i.id_branch = '" . $id_branch . "'
                                ORDER BY inv_adj_id desc");
    while ($row_product = mysqli_fetch_array($query_get_product)) {
        $no++;
        $id_inv = $row_product['inv_adj_id'];
        $id_product = $row_product['id_product'];
        $id_branch = $row_product['id_branch'];
        $nm_product = $row_product['nama_bahan'];
        $actual = $row_product['actual_stock'];
        $amount = $row_product['stock_adj'];
        $uom_product = $row_product['uom'];
        $in_stock = $row_product['in_stock'];
        echo '
    <tr>
        <td style="font-size:11px;">' . $no . '</td>
        <td style="font-size:11px;">' . $nm_product . ' </td>
        <td style="font-size:11px;">
            <input type="number" class="form-control filled-input form-control-sm stock_' . $id_inv . '" value="' . $in_stock . '" id="stock_' . $id_inv . '" readonly autocomplete="off" onclick="this.select();">
        </td>
        <td style="font-size:11px;">
            <input type="number" class="form-control form-control-sm actual_' . $id_inv . '" value="' . $actual . '" data-id_key="' . $id_inv . '" id="actual_' . $id_inv . '" autocomplete="off" onclick="this.select();">
        </td>
        <td style="font-size:11px;">
            <input type="number" class="form-control filled-input form-control-sm adjust_' . $id_inv . '" value="' . $amount . '" id="adjust_' . $id_inv . '" readonly autocomplete="off" onclick="this.select();">
        </td>
        <td>
            <a role="button"
                class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list"
                data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                <span class="btn-icon-wrap"><i
                        class="fa fa-pencil"></i></span></a>
            <a role="button"
                class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list"
                data-id_inv="' . $id_inv . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                <span class="btn-icon-wrap"><i
                        class="icon-trash"></i></span>
            </a>
        </td>
    </tr>
    ';
    }
}

if (isset($_POST['product'])) {
    $product = 0;
    $id = mysqli_real_escape_string($conn, $_POST['product']);
    $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);
    $query_get = mysqli_query($conn, "SELECT stock
                                FROM tb_stock_cabang
                                WHERE id_bahan = '" . $id . "' and id_cabang = '" . $id_branch . "' ");
    if ($row_get = mysqli_fetch_array($query_get)) {
        $product = $row_get['stock'];
    }

    echo $product;
}

if (isset($_POST['actual_stock'])) {
    $id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $amount = mysqli_real_escape_string($conn, $_POST['actual_stock']);
    $id_inv_filter = mysqli_real_escape_string($conn, $_POST['id_inv']);
    $branch = mysqli_real_escape_string($conn, $_POST['branch']);
    $add = "add_list";
    $btn_color = "btn-success";
    $query_get_stock = mysqli_query($conn, "SELECT 
        s.id_bahan,
        s.id_cabang,
        s.stock,
        b.uom
        FROM tb_stock_cabang s
        JOIN tb_bahan b ON b.id_bahan = s.id_bahan
        WHERE s.id_bahan = '" . $id . "' AND s.id_cabang = '" . $branch . "'");
    if ($row_stock = mysqli_fetch_array($query_get_stock)) {
        $stock = $row_stock['stock'];
        $uom = $row_stock['uom'];

        $border = "";
        if ($amount <= 0 || $amount == "") {
            $border = "border border-danger border-3";
            $add  = "";
            $tooltip = 'data-toggle="tooltip" data-placement="top" title="" ';
            $btn_color = "btn-secondary";
        }
    }
    echo '
                <a role="button" class="btn btn-sm ' . $btn_color . ' ' . $add . ' w-100 button-block button-plus " data-id_inv="' . $id_inv_filter . '" data-id_branch="' . $branch . '"> 
                    <span class="text-white middle">Add Adjustments</span>
                <a> 
        ';
}


ob_flush();
