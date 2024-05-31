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
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $biaya = mysqli_real_escape_string($conn, $_POST['biaya']);
    $hpp = mysqli_real_escape_string($conn, $_POST['hpp']);

    if ($id_inv !== "new") {
        $query_get_mutation = mysqli_query($conn, "SELECT distinct i.id_inv_out, 
                                                            i.inv_date, i.id_branch as 'from', 
                                                            o.id_branch as 'to' 
                                            FROM inv_adjust_out i 
                                                LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
                                                LEFT JOIN inv_adjust_in o on o.id_inv_in = i.id_inv_out 
                                                LEFT JOIN tb_cabang m on m.id_cabang = o.id_branch 
                                            where i.id_inv_out = '" . $id_inv . "' ");
        $data_get_mutation = mysqli_fetch_array($query_get_mutation);

        // $branch = $data_get_mutation['to'];
    } else {
        $branch = "";
    }

    $qty_stock = 0;
    $sql_qty_now = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' AND id_cabang = '" . $id_branch . "'");
    if ($row_qty_now = mysqli_fetch_array($sql_qty_now)) {
        $qty_stock = $row_qty_now['stock'];
    }

    $qty_pending = 0;
    $sql_pending = mysqli_query($conn, "
        SELECT ai.stock_in as masuk,'0' as keluar FROM inv_adjust_in ai JOIN tb_bahan p on ai.id_product = p.id_bahan  WHERE p.id_bahan = '" . $id_product . "' and ai.id_inv_in LIKE 'SM-%' AND ai.id_branch = '" . $id_branch . "' AND ai.status_terima != 's' 

        UNION ALL

        SELECT '0' as masuk ,ao.stock_out as keluar  FROM inv_adjust_out ao JOIN tb_bahan p on ao.id_product = p.id_bahan  WHERE p.id_bahan = '" . $id_product . "' and ao.id_inv_out LIKE 'SM-%' AND ao.id_branch = '" . $id_branch . "' AND ao.status_terima != 's'
    ");
    while ($row_pending = mysqli_fetch_array($sql_pending)) {
        $qty_pending += ($row_pending['keluar'] - $row_pending['masuk']);
    }

    $ttl_qty = $qty_stock - $qty_pending;

    $add_bahan = 1;
    if ($ttl_qty >= $amount) {
        $exec = mysqli_query($conn, "insert into inv_adjust_out ( id_inv_out,
                                                    inv_date,
                                                    id_product,
                                                    stock_out,
                                                    biaya,
                                                    hpp,
                                                    id_branch,
											        create_date,
                                                    create_by                                       
											    ) values(										            
                                                    '" . $id_inv . "',
                                                    '" . $date . "',
                                                    '" . $id_product . "',
                                                    '" . $amount . "',
                                                    '" . $biaya . "',
                                                    '" . $hpp . "',
										            '" . $id_branch . "',										
                                                    '" . date('Y-m-d h:i:s') . "',
                                                    '" . $_SESSION['id_user'] . "'
                                                )");
    } else {
        $add_bahan = 0;
    }

    // $exec_in = mysqli_query($conn, "insert into inv_adjust_in ( id_inv_in,
    //                                                 inv_date,
    //                                                 id_product,
    //                                                 stock_in,
    //                                                 biaya,
    //                                                 hpp,
    // 										        create_date,
    //                                                 create_by                                       
    // 										    ) values(										            
    //                                                 '" . $id_inv . "',
    //                                                 '" . $date . "',
    //                                                 '" . $id_product . "',					
    //                                                 '" . $amount . "',
    //                                                 '" . $biaya . "',
    //                                                 '" . $hpp . "',
    //                                                 '" . date('Y-m-d h:i:s') . "',
    //                                                 '" . $_SESSION['id_user'] . "'
    //                                             )");

    // if ($id_inv !== $_SESSION['id_user']) {
    //     $query_get_mutation = mysqli_query($conn, "SELECT distinct i.id_inv_out, 
    //                                                         i.inv_date,i.biaya,i.stock_out, i.id_branch as 'from', 
    //                                                         o.id_branch as 'to' 
    //                                         FROM inv_adjust_out i 
    //                                             LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
    //                                             LEFT JOIN inv_adjust_in o on o.id_inv_in = i.id_inv_out 
    //                                             LEFT JOIN tb_cabang m on m.id_cabang = o.id_branch 
    //                                         where i.id_inv_out = '" . $id_inv . "' ");
    //     if ($row_mutation = mysqli_fetch_array($query_get_mutation)) {
    //         $from = $row_mutation['from'];
    //         $to = $row_mutation['to'];
    //         $stock_inv = $row_mutation['stock_out'];
    //         $hpp = (0 + $row_inv['biaya']) / (0 + $stock_inv);
    //         // From Branch
    //         $query_get_stock = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $from . "' ");
    //         if ($row_stock = mysqli_fetch_array($query_get_stock)) {
    //             $stock_origin = $row_stock['stock'];

    //             $total_stock = $stock_origin - $amount;
    //             mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $from . "' ");

    //             // To Branch
    //             $query_get_stock = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $to . "' ");
    //             if ($row_stock = mysqli_fetch_array($query_get_stock)) {
    //                 $stock_origin_in = $row_stock['stock'];

    //                 $total_stock_in = $stock_origin_in + $amount;
    //                 $hpp = (($row_stock_in['hpp'] * $stock_origin_in) + $row_mutation['biaya']) / $total_stock_in;

    //                 mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock_in . "',hpp= '" . $hpp . "'  WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $to . "' ");
    //             } else {
    //                 mysqli_query($conn, "insert into tb_stock_cabang (id_bahan,
    //                                                                 id_cabang,
    //                                                                 stock,
    //                                                                 hpp                                    
    //                                                             ) values(		
    //                                                                 '" . $id_product . "',
    //                                                                 '" . $to . "',
    //                                                                 '" . $amount . "',
    //                                                                 '" . $hpp . "'
    //                                                             )");
    //             }
    //         }
    //     }
    // }

    $hasil = "";
    $no = 0;
    $query_get = mysqli_query($conn, "SELECT DISTINCT i.*, p.*
                                FROM inv_adjust_out i 
                                left join tb_bahan p on p.id_bahan = i.id_product
                                WHERE i.id_inv_out = '" . $id_inv . "' and i.id_branch = '" . $id_branch . "'
                                ORDER BY i.inv_out_id desc");
    while ($row_product = mysqli_fetch_array($query_get)) {
        $id_inv_out = $row_product['inv_out_id'];
        $nm_product = $row_product['nama_bahan'];
        $id_branch = $row_product['id_branch'];
        $uom_product = $row_product['uom'];
        $amount = $row_product['stock_out'];
        $price = $row_product['biaya'];
        $no++;
        $date_out = $row_product['inv_date'];
        $query_get_inv_in = mysqli_query($conn, "SELECT * FROM inv_adjust_out WHERE id_inv_out = '" . $id_inv . "' and inv_date = '" . $date_out . "' ");
        if ($row_inv_in = mysqli_fetch_array($query_get_inv_in)) {
            $id_inv_in = $row_inv_in['inv_out_id'];
            $hasil = $hasil . '
                <tr>
                    <td style="font-size:11px;">' . $no . '</td>
                    <td style="font-size:11px;">' . $nm_product . ' </td>
                    <td style="font-size:11px;">Rp. ' . number_format($price) . ' </td>
                    <td style="font-size:11px;"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm amount_' . $id_inv_out . '"
                            name="nm_product_' . $id_inv_out . '" value="' . $amount . '" id="amount_' . $id_inv_out . '" autocomplete="off" onclick="this.select();">
                            <span
                            class="form-control filled-input form-control-sm input-group-text "
                            id="inputGroup-sizing-sm" > ' . $uom_product . ' </span>
                            </div></td>
                             <td style="font-size:11px;">' . number_format((float)$amount / 2900, 2, '.', '') . '</td>
                            <td>
                            <a role="button" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list" data-id_inv="' . $id_inv_out . '" data-id_inv_in="' . $id_inv_in . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                                <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                            <a role="button" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list" data-id_inv="' . $id_inv_out . '" data-id_inv_in="' . $id_inv_in . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                                <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                            </a>
                        </td>

                </tr>
            ';
        }
    }

    echo $add_bahan . "|" . $hasil;
}

if (isset($_POST['edit'])) {
    $id_inv = mysqli_real_escape_string($conn, $_POST['edit']);
    $id_inv_in = mysqli_real_escape_string($conn, $_POST['edit_in']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);
    $id_inv_filter = mysqli_real_escape_string($conn, $_POST['inv_filter']);


    // if ($id_inv_filter !== $_SESSION['id_user']) {
    //     $query_get_inv = mysqli_query($conn, "SELECT a.id_product,b.stock_out,b.biaya,a.stock_in FROM inv_adjust_out b LEFT JOIN inv_adjust_in a ON b.id_inv_out = a.id_inv_in  WHERE b.inv_out_id = '" . $id_inv_in . "' and a.inv_in_id = '" . $id_inv_in . "' ");
    //     if ($row_inv = mysqli_fetch_array($query_get_inv)) {
    //         $id_product = $row_inv['id_product'];
    //         $stock_inv = $row_inv['stock_out'];
    //         $stock_inv_in = $row_inv['stock_in'];
    //         $hpp = (0 + $row_inv['biaya']) / (0 + $stock_inv);
    //         $query_get_mutation = mysqli_query($conn, "SELECT distinct i.id_inv_out, 
    //                                                         i.inv_date, i.id_branch as 'from', 
    //                                                         o.id_branch as 'to' 
    //                                         FROM inv_adjust_out i 
    //                                             LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
    //                                             LEFT JOIN inv_adjust_in o on o.id_inv_in = i.id_inv_out 
    //                                             LEFT JOIN tb_cabang m on m.id_cabang = o.id_branch 
    //                                         where i.id_inv_out = '" . $id_inv_filter . "' ");
    //         if ($row_mutation = mysqli_fetch_array($query_get_mutation)) {
    //             $from = $row_mutation['from'];
    //             $to = $row_mutation['to'];

    //             // From Branch
    //             $query_get_stock = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $from . "'  ");
    //             if ($row_stock = mysqli_fetch_array($query_get_stock)) {
    //                 $stock_origin = $row_stock['stock'];

    //                 $total_stock = $stock_origin + $stock_inv;
    //                 $query_set_product = mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $from . "' ");
    //                 $total_amount = $total_stock - $amount;

    //                 $exec = mysqli_query($conn, "UPDATE inv_adjust_out 
    //                         SET stock_out ='" . $amount . "',
    // 	    					change_date = '" . date('Y-m-d h:i:s') . "',
    //                             change_by = '" . $_SESSION['id_user'] . "'                                     
    //                         WHERE inv_out_id = '" . $id_inv_in . "' ");

    //                 mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_amount . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $from . "' ");

    //                 // To Branch
    //                 $query_get_stock_in = mysqli_query($conn, "SELECT stock,hpp FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $to . "'  ");
    //                 if ($row_stock_in = mysqli_fetch_array($query_get_stock_in)) {
    //                     $stock_origin_in = $row_stock_in['stock'];

    //                     $total_stock_in = $stock_origin_in - $stock_inv_in;


    //                     // $query_set_product = mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock_in . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $to . "' ");
    //                     $total_amount_in = $total_stock_in + $amount;
    //                     $hpp = (($row_stock_in['hpp'] * $total_stock_in) + $row_inv['biaya']) / $total_amount_in;
    //                     $hpp_less = $hpp - $row_stock_in['hpp'];
    //                     $hpp_fix = $hpp_less + $row_stock_in['hpp'];

    //                     $exec = mysqli_query($conn, "UPDATE inv_adjust_in 
    //                             SET stock_in ='" . $amount . "',
    // 	    	    				change_date = '" . date('Y-m-d h:i:s') . "',
    //                                 change_by = '" . $_SESSION['id_user'] . "'                                     
    //                             WHERE inv_in_id = '" . $id_inv_in . "' ");

    //                     mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_amount_in . "',hpp='" . $hpp_fix . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $to . "' ");
    //                 } else {
    //                     mysqli_query($conn, "insert into tb_stock_cabang (id_bahan,
    //                                                                 id_cabang,
    //                                                                 stock,
    //                                                                 hpp                                    
    //                                                             ) values(		
    //                                                                 '" . $id_product . "',
    //                                                                 '" . $to . "',
    //                                                                 '" . $amount_origin . "',
    //                                                                 '" . $hpp . "'
    //                                                             )");
    //                 }
    //             }
    //         }
    //     }
    // } else {

    $exec_set = mysqli_query($conn, "UPDATE inv_adjust_out 
                        SET stock_out ='" . $amount . "',
							change_date = '" . date('Y-m-d h:i:s') . "',
                            change_by = '" . $_SESSION['id_user'] . "'                                     
                        WHERE inv_out_id = '" . $id_inv_in . "' ");

    // $exec_set_in = mysqli_query($conn, "UPDATE inv_adjust_in 
    //                     SET stock_in ='" . $amount . "',
    // 						change_date = '" . date('Y-m-d h:i:s') . "',
    //                         change_by = '" . $_SESSION['id_user'] . "'                                     
    //                     WHERE inv_in_id = '" . $id_inv_in . "' ");
    // }
}

if (isset($_POST['delete'])) {
    $id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
    $id_inv_filter = mysqli_real_escape_string($conn, $_POST['inv_filter']);
    $id_inv = mysqli_real_escape_string($conn, $_POST['delete']);
    $id_inv_in = mysqli_real_escape_string($conn, $_POST['delete_in']);
    $id_branch = mysqli_real_escape_string($conn, $_POST['id_branch']);


    // if ($id_inv_filter !== $_SESSION['id_user']) {
    //     $query_get_inv = mysqli_query($conn, "SELECT stock_out FROM inv_adjust_out WHERE inv_out_id = '" . $id_inv . "' ");
    //     if ($row_amount = mysqli_fetch_array($query_get_inv)) {
    //         $amount_origin = intval($row_amount['stock_out']);
    //         $query_get_mutation = mysqli_query($conn, "SELECT distinct i.id_inv_out, 
    //                                                         i.inv_date, i.id_branch as 'from', 
    //                                                         o.id_branch as 'to' 
    //                                         FROM inv_adjust_out i 
    //                                             LEFT JOIN tb_cabang b on b.id_cabang = i.id_branch 
    //                                             LEFT JOIN inv_adjust_in o on o.id_inv_in = i.id_inv_out 
    //                                         where i.id_inv_out = '" . $id_inv_filter . "' ");
    //         if ($row_mutation = mysqli_fetch_array($query_get_mutation)) {
    //             $from = $row_mutation['from'];
    //             $to = $row_mutation['to'];
    //             $query_get_product = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $from . "'  ");
    //             if ($row_amount_out = mysqli_fetch_array($query_get_product)) {
    //                 $stock = intval($row_amount_out['stock']);

    //                 $total_stock = $stock + $amount_origin;
    //                 mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $from . "'");

    //                 $query_get_product_in = mysqli_query($conn, "SELECT stock FROM tb_stock_cabang WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $to . "'  ");
    //                 if ($row_amount_in = mysqli_fetch_array($query_get_product_in)) {
    //                     $stock_in = intval($row_amount_in['stock']);
    //                     $total_stock_in = $stock_in - $amount_origin;
    //                     mysqli_query($conn, "UPDATE tb_stock_cabang SET stock = '" . $total_stock_in . "' WHERE id_bahan = '" . $id_product . "' and id_cabang = '" . $to . "'");
    //                 } else {
    //                     mysqli_query($conn, "insert into tb_stock_cabang (id_bahan,
    //                                                                 id_cabang,
    //                                                                 stock                                    
    //                                                             ) values(		
    //                                                                 '" . $id_product . "',
    //                                                                 '" . $to . "',
    //                                                                 '" . $amount_origin . "'
    //                                                             )");
    //                 }
    //             }
    //         }
    //     }
    // }

    $exec_del = mysqli_query($conn, "DELETE FROM inv_adjust_out WHERE inv_out_id = '" . $id_inv . "'  ");

    // $exec_del_in = mysqli_query($conn, "DELETE FROM inv_adjust_in WHERE inv_in_id = '" . $id_inv_in . "' ");


    $no = 0;
    $query_get = mysqli_query($conn, "SELECT DISTINCT i.*, p.*
                                FROM inv_adjust_out i 
                                left join tb_bahan p on p.id_bahan = i.id_product
                                WHERE i.id_inv_out = '" . $id_inv_filter . "' and i.id_branch = '" . $id_branch . "'
                                ORDER BY inv_out_id desc");
    while ($row_product = mysqli_fetch_array($query_get)) {
        $id_inv_out = $row_product['inv_out_id'];
        $nm_product = $row_product['nama_bahan'];
        $id_branch = $row_product['id_branch'];
        $uom_product = $row_product['uom'];
        $amount = $row_product['stock_out'];
        $price = $row_product['biaya'];
        $no++;
        $date_out = $row_product['inv_date'];
        $query_get_inv_in = mysqli_query($conn, "SELECT * FROM inv_adjust_in WHERE id_inv_in = '" . $id_inv_filter . "' and inv_date = '" . $date_out . "' ");
        if ($row_inv_in = mysqli_fetch_array($query_get_inv_in)) {
            $id_inv_in = $row_inv_in['inv_in_id'];
            echo '
                <tr>
                    <td style="font-size:11px;">' . $no . '</td>
                    <td style="font-size:11px;">' . $nm_product . ' </td>
                    <td style="font-size:11px;">Rp. ' . number_format($price) . ' </td>
                    <td style="font-size:11px;"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm amount_' . $id_inv_out . '"
                            name="nm_product_' . $id_inv_out . '" value="' . $amount . '" id="amount_' . $id_inv_out . '" autocomplete="off" onclick="this.select();">
                            <span
                            class="form-control filled-input form-control-sm input-group-text "
                            id="inputGroup-sizing-sm" > ' . $uom_product . ' </span>
                            </div></td>
                             <td style="font-size:11px;">' . number_format((float)$amount / 2900, 2, '.', '') . '</td>
                            <td>
                            <a role="button" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list" data-id_inv="' . $id_inv_out . '" data-id_inv_in="' . $id_inv_in . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                                <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                            <a role="button" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list" data-id_inv="' . $id_inv_out . '" data-id_inv_in="' . $id_inv_in . '" data-id_product="' . $id_product . '" data-id_branch="' . $id_branch . '">
                                <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                            </a>
                        </td>

                </tr>
            ';
        }
    }
    // echo $to;
}

if (isset($_POST['product'])) {
    $id = mysqli_real_escape_string($conn, $_POST['product']);
    $query_get = mysqli_query($conn, "SELECT uom_product
                                FROM m_product 
                                WHERE m_product_id = '" . $id . "' ");
    if ($row_get = mysqli_fetch_array($query_get)) {
        $uom_product = $row_get['uom_product'];
        echo '
        <div class="input-group input-group-sm">
        <span 
        class="form-control filled-input form-control-sm input-group-text "
        id="inputGroup-sizing-sm"> ' . $uom_product . ' </span>
        </div>
        ';
    }
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
    $query_get_stock = mysqli_query($conn, "SELECT * FROM tb_bahan a LEFT JOIN tb_stock_cabang b ON b.id_bahan = a.id_bahan WHERE a.id_bahan = '" . $id . "' and b.id_cabang = '" . $id_branch . "' ");
    if ($row_stock = mysqli_fetch_array($query_get_stock)) {
        $stock = $row_stock['stock'];
        $uom = $row_stock['uom'];
        $type = $row_stock['jenis_bahan'];
        $border = "";
    } else {
        $query_get_stock = mysqli_query($conn, "SELECT * FROM tb_bahan a WHERE a.id_bahan = '" . $id . "' ");
        if ($row_stock = mysqli_fetch_array($query_get_stock)) {
            $stock = 0;
            $uom = $row_stock['uom'];
            $type = $row_stock['jenis_bahan'];
            $border = "";
        }
    }


    if ($amount > $stock || $amount == 0 || $amount == "" || $amount < 0) {
        $border = "border border-danger border-3";
        $add  = "";
        $tooltip = 'data-toggle="tooltip" data-placement="top" title="" ';
        $btn_color = "btn-secondary";
    }


    echo '
                <a role="button" class="btn btn-sm btn-block ' . $btn_color . ' ' . $add . ' button-plus" data-id_inv="' . $id_inv_filter . '" data-id_branch="' . $id_branch . '"> 
                    <span><i class="fa fa-plus text-white"></i></span>
                <a> 
        ';
}


ob_flush();
