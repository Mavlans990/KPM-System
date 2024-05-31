<?php
	ob_start();
	session_start();
	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";

    
if(isset($_POST['add'])){
    $id_branch=mysqli_real_escape_string($conn,$_POST['id_branch']);
    $id_product=mysqli_real_escape_string($conn,$_POST['id_product']);
	$id_inv=mysqli_real_escape_string($conn,$_POST['add']);
    $amount=mysqli_real_escape_string($conn,$_POST['amount']);
    $date=mysqli_real_escape_string($conn,$_POST['date']);
    $status=mysqli_real_escape_string($conn,$_POST['status']);

	$exec=mysqli_query($conn,"insert into inv_product_in ( id_inv_in,
                                                    inv_date,
                                                    id_product,
                                                    id_branch,
                                                    stock_in,
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
    // if($id_inv !== $_SESSION['id_user'] && $status == "1"){
    //     $query_get_stock = mysqli_query($conn,"SELECT stock FROM m_branch_stock WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
    //         if($row_stock = mysqli_fetch_array($query_get_stock)){
    //             $stock_origin = $row_stock['stock'];

    //             $total_stock = $stock_origin + $amount;
    //             mysqli_query($conn,"UPDATE m_branch_stock SET stock = '".$total_stock."' WHERE id_product = '".$id_product."' ");
    //         }else{
    //             mysqli_query($conn,"insert into m_branch_stock (   id_product,
    //                                                         id_branch,
    //                                                         stock                                    
    //                                                     ) values(		
    //                                                         '".$id_product."',
    //                                                         '".$id_branch."',
    //                                                         '".$amount."'
    //                                                     )");
    //         }
    //     }

        if($id_inv !== $_SESSION['id_user']){
            $query_get_stock = mysqli_query($conn,"SELECT stock FROM m_branch_stock WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
                if($row_stock = mysqli_fetch_array($query_get_stock)){
                    $stock_origin = $row_stock['stock'];
    
                    $total_stock = $stock_origin + $amount;
                    mysqli_query($conn,"UPDATE m_branch_stock SET stock = '".$total_stock."' WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
                }else{
                    mysqli_query($conn,"insert into m_branch_stock (   id_product,
                                                                id_branch,
                                                                stock                                    
                                                            ) values(		
                                                                '".$id_product."',
                                                                '".$id_branch."',
                                                                '".$amount."'
                                                            )");
                }
            }
    
	
    $no = 0;
    $query_get=mysqli_query($conn,"SELECT i.*,p.nm_product,p.uom_product 
                                FROM inv_product_in i
                                LEFT JOIN m_product p on i.id_product = p.m_product_id 
                                WHERE i.id_inv_in = '".$id_inv."'
                                and i.id_branch = '".$id_branch."'
                                ORDER BY inv_in_id desc");
    while ($row_product = mysqli_fetch_array($query_get)) {
        $id_inv = $row_product['inv_in_id'];
        $nm_product = $row_product['nm_product'];
        $id_branch = $row_product['id_branch'];
        $uom_product = $row_product['uom_product'];
        $amount = $row_product['stock_in'];
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
    $id_inv=mysqli_real_escape_string($conn,$_POST['edit']);
    $id_inv_filter=mysqli_real_escape_string($conn,$_POST['inv_filter']);
    $amount=mysqli_real_escape_string($conn,$_POST['amount']);
    $id_branch=mysqli_real_escape_string($conn,$_POST['id_branch']);
    $status=mysqli_real_escape_string($conn,$_POST['status']);
    // $id_product=mysqli_real_escape_string($conn,$_POST['id_product']);
    
    if($id_inv_filter !== $_SESSION['id_user'] ){
        $query_get_inv = mysqli_query($conn,"SELECT id_product,stock_in FROM inv_product_in WHERE inv_in_id = '".$id_inv."' ");
        if($row_inv = mysqli_fetch_array($query_get_inv)){
            $id_product = $row_inv['id_product'];
            $stock_inv = $row_inv['stock_in'];
            $query_get_stock = mysqli_query($conn,"SELECT stock FROM m_branch_stock WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
            if($row_stock = mysqli_fetch_array($query_get_stock)){
                $stock_origin = $row_stock['stock'];

                $total_stock = $stock_origin - $stock_inv;
                $query_set_product = mysqli_query($conn,"UPDATE m_branch_stock SET stock = '".$total_stock."' WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
                $total_amount = $total_stock + $amount;

                $exec=mysqli_query($conn,"UPDATE inv_product_in
                        SET stock_in ='".$amount."',
							change_date = '".date('Y-m-d h:i:s')."',
                            change_by = '".$_SESSION['id_user']."'                                     
                        WHERE inv_in_id = '".$id_inv."' and id_branch = '".$id_branch."' ");

                mysqli_query($conn,"UPDATE m_branch_stock SET stock = '".$total_amount."' WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."'  ");
            }else{
                mysqli_query($conn,"insert into m_branch_stock (   id_product,
                                                            id_branch,
                                                            stock                                    
                                                        ) values(		
                                                            '".$id_product."',
                                                            '".$id_branch."',
                                                            '".$amount."'
                                                        )");
            }
        }
    }else{

	$exec=mysqli_query($conn,"UPDATE inv_product_in 
                        SET stock_in ='".$amount."',
							change_date = '".date('Y-m-d h:i:s')."',
                            change_by = '".$_SESSION['id_user']."'                                     
                        WHERE inv_in_id = '".$id_inv."' and id_branch = '".$id_branch."' ");
    }
}

if(isset($_POST['delete'])){
    $id_product=mysqli_real_escape_string($conn,$_POST['id_product']);
    $id_inv_filter=mysqli_real_escape_string($conn,$_POST['inv_filter']);
    $id_inv=mysqli_real_escape_string($conn,$_POST['delete']);
    $id_branch=mysqli_real_escape_string($conn,$_POST['id_branch']);
    $status=mysqli_real_escape_string($conn,$_POST['status']);
    
    if($id_inv_filter !== $_SESSION['id_user'] ){
        $query_get_inv = mysqli_query($conn,"SELECT stock_in FROM inv_product_in WHERE inv_in_id = '".$id_inv."' ");
        if($row_amount = mysqli_fetch_array($query_get_inv)){
            $amount_origin = intval($row_amount['stock_in']);
            $query_get_product = mysqli_query($conn,"SELECT stock FROM m_branch_stock WHERE id_product = '".$id_product."' and id_branch = '".$id_branch."'  ");
            if($row_amount = mysqli_fetch_array($query_get_product)){
                $stock = intval($row_amount['stock']);
    
                $total_stock = $stock - $amount_origin;
                mysqli_query($conn,"UPDATE m_branch_stock SET stock = '".$total_stock."' WHERE  id_product = '".$id_product."' and id_branch = '".$id_branch."' ");
            }
        }
    }

	$exec=mysqli_query($conn,"DELETE FROM inv_product_in WHERE inv_in_id = '".$id_inv."' and id_branch = '".$id_branch."' ");
	
    $no = 0;
    $query_get=mysqli_query($conn,"SELECT i.*,p.nm_product,p.uom_product  
                                FROM inv_product_in i
                                LEFT JOIN m_product p on i.id_product = p.m_product_id 
                                WHERE i.id_inv_in = '".$id_inv_filter."'
                                and i.id_branch = '".$id_branch."'
                                ORDER BY inv_in_id desc");
    while ($row_product = mysqli_fetch_array($query_get)) {
        $id_inv = $row_product['inv_in_id'];
        $nm_product = $row_product['nm_product'];
        $uom_product = $row_product['uom_product'];
        $amount = $row_product['stock_in'];
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
    $id=mysqli_real_escape_string($conn,$_POST['product']);
    $query_get=mysqli_query($conn,"SELECT uom_product
                                FROM m_product 
                                WHERE m_product_id = '".$id."' ");
    if ($row_get = mysqli_fetch_array($query_get)) {
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
    $id=mysqli_real_escape_string($conn,$_POST['product_id']);
    $amount=mysqli_real_escape_string($conn,$_POST['amount_product']);
    $id_inv_filter=mysqli_real_escape_string($conn,$_POST['id_inv']);
    $id_branch=mysqli_real_escape_string($conn,$_POST['branch']);
    $add = "add_list";
    $btn_color = "btn-success";
    $query_get_stock = mysqli_query($conn,"SELECT * FROM m_product WHERE m_product_id = '".$id."' ");
    if($row_stock = mysqli_fetch_array($query_get_stock)){
        // $stock = $row_stock['stock_product'];
        $uom = $row_stock['uom_product'];
        $type = $row_stock['type_product'];
        $border = "";
            if($amount == 0 || $amount == "" || $amount < 0){
                $border = "border border-danger border-3";
                $add  = "";
                $tooltip = 'data-toggle="tooltip" data-placement="top" title="" ';
                $btn_color = "btn-secondary";
            }
        
        
    }
    echo'
                <a role="button" class="btn btn-sm btn-block '.$btn_color.' '.$add.' button-plus" data-id_inv="'.$id_inv_filter.'" data-id_branch="'.$id_branch.'"> 
                    <span class=""><i class="fa fa-plus text-white"></i></span>
                <a> 
        ';
}


ob_flush();
?>