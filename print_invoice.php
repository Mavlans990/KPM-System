<?php
session_start();
include "lib/koneksi.php";
include "lib/format.php";
include "lib/appcode.php";

if (!isset($_SESSION['id_user']) && !isset($_SESSION['grup'])) {
    header('Location:index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, akun-scalable=no" />
    <title>Inventory System</title>


    <style type="text/css">
        .print {
            display: none;
        }

        @media print {
            .non-print {
                display: none;
            }

            .print {
                display: block;
                font-family: 'PT Mono', monospace;
                font-size: 18px;
            }

            .pagebreak {
                clear: both;
                page-break-after: always;
            }

        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12pt;
        }


        .tabelsj {
            border: 1px solid;
            border-collapse: collapse;
            padding: 2px;
        }
    </style>
</head>

<body id="page-top">





    <?php

    $id_invoice = $_GET['id_invoice'];
    $pay = "";
    $sql2 = mysqli_query($conn, "select sum(pay) as pay from inv_payment where id_inv = '".$id_invoice."'");
    $row2 = mysqli_fetch_assoc($sql2);
    $pay = $row2['pay'];

    $sql = mysqli_query($conn, "select a.id_invoice,a.tgl_invoice,a.subtotal_invoice,a.ongkos,a.total_invoice,a.disc_invoice,a.cust_invoice from tb_barang_keluar a where id_invoice = '" . $id_invoice . "' ");
    while ($row = mysqli_fetch_array($sql)) {

        $tgl_invoice = $row['tgl_invoice'];
        $subtotal_invoice = $row['subtotal_invoice'];
        $total_invoice = $row['total_invoice'] ;
        $disc_invoice = $row['disc_invoice'];
        $ongkos = $row['ongkos'];
        $cust_invoice = "";
        $sql_get_cust = mysqli_query($conn, "SELECT nama_customer FROM tb_customer WHERE id_customer = '" . $row['cust_invoice'] . "'");
        if ($row_cust = mysqli_fetch_array($sql_get_cust)) {
            $cust_invoice = $row_cust['nama_customer'];
        }
        $id_invoice = $row['id_invoice'];
    }
    


    echo '<h3><center>INVOICE</center></h3>';

    echo '<table width="100%" style="border:none;">';
    echo '<tr>';
    echo '<td width="50%">';
    echo '<b>NO. INVOICE :</b>' . $id_invoice . '<br>';
    echo '<b>TANGGAL :</b>' . $tgl_invoice . ' <br>';
    echo '</td>';
    echo '<td width="50%">';
    echo '<b>CUSTOMER :</b>' . $cust_invoice . '<br>';
    echo '</td>';
    echo '</tr>';
    echo '</table><br>';

    ?>


    <table width="100%" class="tabelsj">
        <thead>
            <tr>
                <th class="tabelsj" width="170px"> SURAT JALAN</th>
                <!-- <th class="tabelsj" width="170px"> <?= money($total) ?></th> -->

                <th class="tabelsj">ITEM DETAIL</th>
                <th class="tabelsj">TOTAL</th>


            </tr>
        </thead>
        <tbody>
            <?php

            $sql_get_piutang = "select 
                                        id_transaksi,tgl_transaksi,berat,uom,dibuat_oleh,sum(subtotal) as total from tb_barang_keluar 
                                        where  id_invoice = '" . $id_invoice . "'
                                        group by id_transaksi 
                                        order by id_transaksi asc
                                    
                                        ";
            $subtotal = 0;
            $data4 = mysqli_query($conn, $sql_get_piutang);
            while ($row_piutang = mysqli_fetch_array($data4)) {


                $list_item = "<table class='tabelsj' width='100%' height='100%' >";
                $sql_item = "select * from tb_barang_keluar where id_transaksi = '" . $row_piutang['id_transaksi'] . "' and harga <> 0 ";
                $data_item = mysqli_query($conn, $sql_item);
                while ($row_item = mysqli_fetch_array($data_item)) {
                    $nm_barang = "";
                    $sql_get_barang = mysqli_query($conn, "SELECT nama_bahan FROM tb_bahan WHERE id_bahan = '" . $row_item['id_bahan'] . "'");
                    if ($row_barang = mysqli_fetch_array($sql_get_barang)) {
                        $nm_barang = $row_barang['nama_bahan'];
                    }
                    $jns = "";
                    if($row_item["jenis_transaksi"] == "penjualan"){
                        $jns = "(Penjualan) ";
                    }elseif($row_item["jenis_transaksi"] == "return"){
                        $jns = "(Return) ";
                    }else{
                        $jns = " ";
                    }
                    $list_item = $list_item . "<tr>
                                                <td class='tabelsj' style='width:60%;'>" . $nm_barang . " (Berat " . $row_item['berat'] . " " . $row_item['uom'] . ")</td>
                                                <td class='tabelsj' style='text-align:right;width:10%;'>" . money($row_item['harga']) . "</td>
                                                <td class='tabelsj' style='text-align:right;width:10%'>" . $row_item['qty'] . " <font size='1pt'>Roll</font></td>
                                                </tr>";
                }
                $list_item = $list_item . "</table>";

                echo '
                                                <tr>
                                                    <td class="tabelsj">
                                                    <br>
                                                    Tgl SO : ' .  date('d/m/Y', strtotime($row_piutang['tgl_transaksi'])) . '
                                                    <br>
                                                    ' . $jns . '
                                                    </td>
                                               
                                                    <td class="tabelsj" style="padding:0px;">' . $list_item . '</td>
                                                    <td class="tabelsj" style="text-align:right;">' . money($row_piutang['total']) . '</td>
                                                   
                                        
                                                </tr>
                                            ';
                $subtotal += $row_piutang['total'];
            }
            ?>
        </tbody>
    </table>


    <?php

    echo '
    <table width="100%">
    <tr class="tabelsj">';
    echo '<td class="tabelsj" width="20%" ><b>SUBTOTAL : ' . money($subtotal_invoice) . ' </b> </td>';
    echo '<td class="tabelsj" width="20%" ><b>DISCOUNT : ' . $disc_invoice . ' % </b></td>';
    echo '<td class="tabelsj" width="20%" ><b>DISC ONGKOS : ' . money($ongkos) . '  </b></td>';
    echo '<td class="tabelsj" width="20%" ><b>SUDAH BAYAR : ' . money($pay) . '  </b></td>';
    echo '<td class="tabelsj" width="20%" style="text-align:right;"><b>SISA : ' . money($total_invoice - $pay) . '</b> </td>';
    echo '</tr>';
    echo '</table>';


    echo '<table width="100%" style="margin-top:20px;">
    <tr>
        <td><center>Dibuat Oleh</center></td>
        <td><center>Diterima Oleh</center></td>
        
    </tr>
    <tr>
        <td><center><br><br><br>(.......................................)</center></td>
        <td><center><br><br><br>(.......................................)</center></td>
        
    </tr>
    </table>

    <div class="pagebreak"></div>
';




    ?>


    <!-- DataTales Example -->


</body>

</html>
<script type="text/javascript">
        window.print();
</script>