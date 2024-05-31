<?php
ob_start();
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";


if (isset($_POST['add'])) {
    $id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
    $id_ingredients = mysqli_real_escape_string($conn, $_POST['add']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);

    $exec = mysqli_query($conn, "insert into m_ingredients ( id_product,
											id_ingredients,
											amount_ingredients,
											create_date,
                                            create_by                                       
											) 
									values(
										'" . $id_product . "',
										'" . $id_ingredients . "',
										'" . $amount . "',										
                                        '" . date('Y-m-d h:i:s') . "',
                                        '" . $_SESSION['nm_user'] . "'
										)");

    $no = 0;
    $query_get = mysqli_query($conn, "SELECT * 
                                FROM m_ingredients i
                                LEFT JOIN m_product p on i.id_ingredients = p.m_product_id 
                                WHERE i.id_product = '" . $id_product . "'
                                ORDER BY m_ingredients_id desc");
    while ($row_ingredients = mysqli_fetch_array($query_get)) {
        $id_ingredients = $row_ingredients['m_ingredients_id'];
        $nm_ingredients = $row_ingredients['nm_product'];
        $uom_ingredients = $row_ingredients['uom_product'];
        $amount = $row_ingredients['amount_ingredients'];
        $no++;

        echo '
        <tr>
            <td style="font-size:11px;">' . $no . '</td>
            <td style="font-size:11px;">' . $nm_ingredients . ' </td>
            <td style="font-size:11px;"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm"
                    name="nm_ingredients_' . $id_ingredients . '" value="' . $amount . '" id="amount_' . $id_ingredients . '"
                    class="amount_' . $id_ingredients . '">
                    <span style="80px"
                    class="form-control filled-input form-control-sm input-group-text "
                    id="inputGroup-sizing-sm"> ' . $uom_ingredients . ' </span>
                    </div></td>
                    <td>
                    <a role="button" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list" data-id_ingredients="' . $id_ingredients . '" data-id_product="' . $id_product . '">
                        <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                    <a role="button" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list" data-id_ingredients="' . $id_ingredients . '" data-id_product="' . $id_product . '">
                        <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                    </a>
                </td>

        </tr>
        ';
    }
}

if (isset($_POST['edit'])) {
    // $id_product=mysqli_real_escape_string($conn,$_POST['id_product']);
    $id_ingredients = mysqli_real_escape_string($conn, $_POST['edit']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);

    $exec = mysqli_query($conn, "UPDATE m_ingredients 
                        SET amount_ingredients ='" . $amount . "',
							change_date = '" . date('Y-m-d h:i:s') . "',
                            change_by = '" . $_SESSION['id_user'] . "'                                     
						WHERE m_ingredients_id = '" . $id_ingredients . "' ");
}

if (isset($_POST['delete'])) {
    $id_product = mysqli_real_escape_string($conn, $_POST['id_product']);
    $id_ingredients = mysqli_real_escape_string($conn, $_POST['delete']);

    $exec = mysqli_query($conn, "DELETE FROM m_ingredients WHERE m_ingredients_id = '" . $id_ingredients . "' ");

    $no = 0;
    $query_get = mysqli_query($conn, "SELECT * 
                                FROM m_ingredients i
                                LEFT JOIN m_product p on i.id_ingredients = p.m_product_id 
                                WHERE i.id_product = '" . $id_product . "'
                                ORDER BY m_ingredients_id desc");
    while ($row_ingredients = mysqli_fetch_array($query_get)) {
        $id_ingredients = $row_ingredients['m_ingredients_id'];
        $nm_ingredients = $row_ingredients['nm_product'];
        $uom_ingredients = $row_ingredients['uom_product'];
        $amount = $row_ingredients['amount_ingredients'];
        $no++;

        echo '
        <tr>
            <td style="font-size:11px;">' . $no . '</td>
            <td style="font-size:11px;">' . $nm_ingredients . ' </td>
            <td style="font-size:11px;"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm"
                    name="nm_ingredients_' . $id_ingredients . '" value="' . $amount . '" id="amount_' . $id_ingredients . '"
                    class="amount_' . $id_ingredients . '">
                    <span style="80px"
                    class="form-control filled-input form-control-sm input-group-text "
                    id="inputGroup-sizing-sm"> ' . $uom_ingredients . ' </span>
                    </div></td>
                    <td>
                    <a role="button" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list" data-id_ingredients="' . $id_ingredients . '" data-id_product="' . $id_product . '">
                        <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                    <a role="button" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list" data-id_ingredients="' . $id_ingredients . '" data-id_product="' . $id_product . '">
                        <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                    </a>
                </td>

        </tr>
        ';
    }
}

if (isset($_POST['product'])) {
    $hasil = "";
    $hpp = 0;
    $stock = 0;
    $id = mysqli_real_escape_string($conn, $_POST['product']);
    $cabang = mysqli_real_escape_string($conn, $_POST['cabang']);
    $query_get = mysqli_query($conn, "SELECT id_bahan,uom
                                FROM tb_bahan
                                WHERE id_bahan = '" . $id . "' ");
    if ($row_get = mysqli_fetch_array($query_get)) {
        $uom_ingredients = $row_get['uom'];

        $query_stock = mysqli_query($conn, "
            SELECT hpp,stock
            FROM tb_stock_cabang 
            WHERE id_bahan = '" . $row_get['id_bahan'] . "' AND id_cabang = '" . $cabang . "'
        ");
        $data_stock = mysqli_fetch_array($query_stock);
        $hpp = $hpp + $data_stock['hpp'];
        $stock = $stock + $data_stock['stock'];
    }
    $hasil = $hasil . '
        <div class="input-group input-group-sm">
        <span 
        class="form-control filled-input form-control-sm input-group-text "
        id="inputGroup-sizing-sm"> ' . $uom_ingredients . ' </span>
        </div>|' . $hpp . "|" . $stock;
    echo $hasil;
}

if (isset($_POST['cust'])) {
    $id = mysqli_real_escape_string($conn, $_POST['cust']);
    $query_get = mysqli_query($conn, "SELECT telp_user,nm_user
                                FROM tb_user
                                WHERE id_user = '" . $id . "' ");
    if ($row_get = mysqli_fetch_array($query_get)) {
        $uom_ingredients = $row_get['telp_user'];
        $nm_cust = $row_get['nm_user'];
        echo $uom_ingredients . "|" . $nm_cust;
    }
}


ob_flush();
