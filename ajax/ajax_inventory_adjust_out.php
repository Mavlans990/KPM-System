<?php
	ob_start();
	session_start();
	include "../../lib/koneksi.php";
	include "../../lib/appcode.php";
	include "../../lib/format.php";

    
if(isset($_POST['add'])){
    $id_branch=mysql_real_escape_string($_POST['id_branch']);
    $id_product=mysql_real_escape_string($_POST['id_product']);
	$id_inv=mysql_real_escape_string($_POST['add']);
    $amount=mysql_real_escape_string($_POST['amount']);
    $date=mysql_real_escape_string($_POST['date']);

	$exec=mysql_query("insert into inv_adjust_out ( id_inv_out,
                                                    inv_date,
                                                    id_product,
                                                    id_branch,
                                                    stock_out,
											        create_date,
                                                    create_by                                       
											    ) values(										            
                                                    '".$id_inv."',
                                                    '".$date."',
                                                    '".$id_product."',
                                                    '".$id_branch."',
										            '".$amount."',										
                                                    '".date('Y-m-d h:i:s')."',
                                                    '".$_SESSION['id_user']."'
                                                )");

    if($id_inv !== $_SESSION['id_user']){
        $query_get_stock = mysql_query("SELECT stock FROM m_branch_stock WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
            if($row_stock = mysql_fetch_array($query_get_stock)){
                $stock_origin = $row_stock['stock'];

                $total_stock = $stock_origin - $amount;
                mysql_query("UPDATE m_branch_stock SET stock = '".$total_stock."' WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
            }
        }
	
    $no = 0;
    $query_get=mysql_query("SELECT * 
                                FROM inv_adjust_out i
                                LEFT JOIN m_product p on i.id_product = p.m_product_id 
                                WHERE i.id_inv_out = '".$id_inv."'
                                and i.id_branch = '".$id_branch."'
                                ORDER BY inv_out_id desc");
    while ($row_product = mysql_fetch_array($query_get)) {
        $id_inv = $row_product['inv_out_id'];
        $nm_product = $row_product['nm_product'];
        $id_branch = $_SESSION['branch'];
        $uom_product = $row_product['uom_product'];
        $amount = $row_product['stock_out'];
        $no++;

    echo'
        <tr>
            <td style="font-size:11px;">'.$no.'</td>
            <td style="font-size:11px;">'.$nm_product.' </td>
            <td style="font-size:11px;"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm amount_'.$id_inv.'"
                    name="nm_product_'.$id_inv.'" value="'.$amount.'" id="amount_'.$id_inv.'" >
                    <span
                    class="form-control filled-input form-control-sm input-group-text "
                    id="inputGroup-sizing-sm"> '.$uom_product.' </span>
                    </div></td>
                    <td>
                    <a role="button" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list" data-id_inv="'.$id_inv.'" data-id_product="'.$id_product.'" data-id_branch="'.$id_branch.'">
                        <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                    <a role="button" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list" data-id_inv="'.$id_inv.'" data-id_product="'.$id_product.'" data-id_branch="'.$id_branch.'">
                        <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                    </a>
                </td>

        </tr>
        ';  
    }
}

if(isset($_POST['edit'])){
	$id_inv=mysql_real_escape_string($_POST['edit']);
    $amount=mysql_real_escape_string($_POST['amount']);
    $id_branch=mysql_real_escape_string($_POST['id_branch']);
    $id_inv_filter=mysql_real_escape_string($_POST['inv_filter']);


    if($id_inv_filter !== $_SESSION['id_user']){
        $query_get_inv = mysql_query("SELECT id_product,stock_out FROM inv_adjust_out WHERE inv_out_id = '".$id_inv."' ");
        if($row_inv = mysql_fetch_array($query_get_inv)){
            $id_product = $row_inv['id_product'];
            $stock_inv = $row_inv['stock_out'];
            $query_get_stock = mysql_query("SELECT stock FROM m_branch_stock WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
            if($row_stock = mysql_fetch_array($query_get_stock)){
                $stock_origin = $row_stock['stock'];

                $total_stock = $stock_origin + $stock_inv;
                mysql_query("UPDATE m_branch_stock SET stock = '".$total_stock."' WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."'");
                $total_amount = $total_stock - $amount;
                
                $exec=mysql_query("UPDATE inv_adjust_out 
                        SET stock_out ='".$amount."',
							change_date = '".date('Y-m-d h:i:s')."',
                            change_by = '".$_SESSION['id_user']."'                                     
                        WHERE inv_out_id = '".$id_inv."' and id_branch = '".$id_branch."' ");

                mysql_query("UPDATE m_branch_stock SET stock = '".$total_amount."' WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
            }
        }
    }else{
	$exec=mysql_query("UPDATE inv_adjust_out 
                        SET stock_out ='".$amount."',
							change_date = '".date('Y-m-d h:i:s')."',
                            change_by = '".$_SESSION['id_user']."'                                     
                        WHERE inv_out_id = '".$id_inv."' and id_branch = '".$id_branch."' ");
    }
	
}

if(isset($_POST['delete'])){
    $id_product=mysql_real_escape_string($_POST['id_product']);
    $id_inv_filter=mysql_real_escape_string($_POST['inv_filter']);
    $id_inv=mysql_real_escape_string($_POST['delete']);
    $id_branch=mysql_real_escape_string($_POST['id_branch']);
    
    if($id_inv_filter !== $_SESSION['id_user']){
        $query_get_inv = mysql_query("SELECT stock_out FROM inv_adjust_out WHERE inv_out_id = '".$id_inv."' ");
        if($row_amount = mysql_fetch_array($query_get_inv)){
            $amount_origin = intval($row_amount['stock_out']);
            $query_get_product = mysql_query("SELECT stock FROM m_branch_stock WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
            if($row_amount = mysql_fetch_array($query_get_product)){
                $stock = intval($row_amount['stock']);
    
                $total_stock = $stock + $amount_origin;
                mysql_query("UPDATE m_branch_stock SET stock = '".$total_stock."' WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."'");
            }
        }
    }

	$exec=mysql_query("DELETE FROM inv_adjust_out WHERE inv_out_id = '".$id_inv."' and id_branch = '".$id_branch."' ");
	
    $no = 0;
    $query_get=mysql_query("SELECT * 
                                FROM inv_adjust_out i
                                LEFT JOIN m_product p on i.id_product = p.m_product_id 
                                WHERE i.id_inv_out = '".$id_inv_filter."'
                                and i.id_branch = '".$id_branch."'
                                ORDER BY inv_out_id desc");
    while ($row_product = mysql_fetch_array($query_get)) {
        $id_inv = $row_product['inv_out_id'];
        $nm_product = $row_product['nm_product'];
        $uom_product = $row_product['uom_product'];
        $amount = $row_product['stock_out'];
        $no++;

    echo'
        <tr>
            <td style="font-size:11px;">'.$no.'</td>
            <td style="font-size:11px;">'.$nm_product.' </td>
            <td style="font-size:11px;"><div class="input-group input-group-sm"><input type="text" class="form-control form-control-sm amount_'.$id_inv.'"
                    name="nm_product_'.$id_inv.'" value="'.$amount.'" id="amount_'.$id_inv.'" >
                    <span
                    class="form-control filled-input form-control-sm input-group-text "
                    id="inputGroup-sizing-sm"> '.$uom_product.' </span>
                    </div></td>
                    <td>
                    <a role="button" class="btn btn-xs btn-icon btn-warning btn-icon-style-1 set_list" data-id_inv="'.$id_inv.'" data-id_product="'.$id_product.'" data-id_branch="'.$id_branch.'">
                        <span class="btn-icon-wrap"><i class="fa fa-pencil"></i></span></a>
                    <a role="button" class="btn btn-xs btn-icon btn-danger btn-icon-style-1 del_list" data-id_inv="'.$id_inv.'" data-id_product="'.$id_product.'" data-id_branch="'.$id_branch.'">
                        <span class="btn-icon-wrap"><i class="icon-trash"></i></span>
                    </a>
                </td>

        </tr>
        ';  
    }
}

if(isset($_POST['product'])){
    $id=mysql_real_escape_string($_POST['product']);
    $query_get=mysql_query("SELECT uom_product
                                FROM m_product 
                                WHERE m_product_id = '".$id."' ");
    if ($row_get = mysql_fetch_array($query_get)) {
        $uom_product = $row_get['uom_product'];
        echo'
        <div class="input-group input-group-sm">
        <span 
        class="form-control filled-input form-control-sm input-group-text "
        id="inputGroup-sizing-sm"> '.$uom_product.' </span>
        </div>
        ';  
    
    }
    
}

if(isset($_POST['amount_product'])){
    $id=mysql_real_escape_string($_POST['product_id']);
    $amount=mysql_real_escape_string($_POST['amount_product']);
    $id_inv_filter=mysql_real_escape_string($_POST['id_inv']);
    $id_branch=mysql_real_escape_string($_SESSION['branch']);
    $add = "add_list";
    $btn_color = "btn-success";
    $query_get_stock = mysql_query("SELECT * FROM m_branch_stock b LEFT JOIN m_product a ON b.id_product = a.m_product_id WHERE b.id_product = '".$id."' and b.id_branch = '".$id_branch."'");
    if($row_stock = mysql_fetch_array($query_get_stock)){
        $stock = $row_stock['stock'];
        $uom = $row_stock['uom_product'];
        $type = $row_stock['type_product'];
        $border = "";
            if($amount > $stock || $amount == 0 || $amount == "" || $amount < 0){
                $border = "border border-danger border-3";
                $add  = "";
                $tooltip = 'data-toggle="tooltip" data-placement="top" title="" ';
                $btn_color = "btn-secondary";
            }
        
    }
    echo'
                <a role="button" class="btn btn-xs btn-icon '.$btn_color.' btn-icon-style-1 '.$add.' button-plus" data-id_inv="'.$id_inv_filter.'" data-id_branch="'.$_SESSION['branch'].'"> 
                    <span class="btn-icon-wrap"><i class="fa fa-plus text-white"></i></span>
                <a> 
        ';
}


ob_flush();
?>