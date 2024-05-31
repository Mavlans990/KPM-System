<?php
ob_start();
session_start();

include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (isset($_POST['product_list'])) {
        $product = mysqli_real_escape_string($conn, $_POST['product_list']);

        $query_get_product = " SELECT nm_product,m_product_id
                            FROM    m_product
                            WHERE  nm_product like '%" . $product . "%' and type_product = 'ingredients' or m_product_id like '%" . $product . "%' and type_product = 'ingredients' ";
        $sql_get_product = mysqli_query($conn, $query_get_product);
        echo '
            <datalist id="product_list" class="product_list">
            ';
        while ($row_product = mysqli_fetch_array($sql_get_product)) {
                echo '
                        <option value="' . $row_product['nm_product'] . ' | ' . $row_product['m_product_id'] . '">
                        ';
        }
        echo '
            </datalist>
            ';
}

if (isset($_POST['product_in'])) {
        $product = mysqli_real_escape_string($conn, $_POST['product_in']);

        $query_get_product = " SELECT nama_bahan,id_bahan
                            FROM tb_bahan
                            WHERE nama_bahan like '%" . $product . "%'  or id_bahan like '%" . $product . "%'";
        $sql_get_product = mysqli_query($conn, $query_get_product);
        echo '
            <datalist id="product_list" class="product_list">
            ';
        while ($row_product = mysqli_fetch_array($sql_get_product)) {
                echo '
                        <option value="' . $row_product['nama_bahan'] . ' | ' . $row_product['id_bahan'] . '">
                        ';
        }
        echo '
            </datalist>
            ';
}

if (isset($_POST['product_out'])) {
        $product = mysqli_real_escape_string($conn, $_POST['product_out']);

        // $query_get_type = mysqli_query($conn,"SELECT type_product FROM m_product_id WHERE nm_product like '%".$product."%' or m_product_id like '%".$product."%' ");
        // if($row_type = mysqli_fetch_array($query_get_type)){
        //     echo $query_get_type;
        //     $where_type = "";
        //     if($row_type['type_product'] == "Ingredients"){
        //         $where_type = "WHERE  nm_product like '%".$product."%' and stock_product > 0 or m_product_id like '%".$product."%' and stock_product > 0 ";
        //     }else{
        $where_type = "WHERE  nm_product like '%" . $product . "%' or m_product_id like '%" . $product . "%' ";
        // }
        $query_get_product = " SELECT nm_product,m_product_id
                                FROM    m_product
                                " . $where_type . " ";
        $sql_get_product = mysqli_query($conn, $query_get_product);

        echo '
            <datalist id="product_list" class="product_list">
            ';
        while ($row_product = mysqli_fetch_array($sql_get_product)) {
                echo '
                        <option value="' . $row_product['nm_product'] . ' | ' . $row_product['m_product_id'] . '">
                        ';
        }
        echo '
            </datalist>
            ';

        // }
}


if (isset($_POST['cust_list'])) {
        $product = mysqli_real_escape_string($conn, $_POST['cust_list']);

        $query_get_product = " SELECT nm_user,id_user
                            FROM    tb_user
                            WHERE  (nm_user like '%" . $product . "%' or id_user like '%" . $product . "%') AND grup = 'customer'";
        $sql_get_product = mysqli_query($conn, $query_get_product);
        echo '
            <datalist id="cust_list" class="cust_list">
            ';
        while ($row_product = mysqli_fetch_array($sql_get_product)) {
                echo '
                        <option value="' . $row_product['id_user'] . '">
                        ';
        }
        echo '
            </datalist>
            ';
}




ob_flush();
