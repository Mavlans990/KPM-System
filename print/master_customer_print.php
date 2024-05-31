<?php
	session_start();
	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";
  
	if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
	{
	    header('Location:../login.php'); 
	}

	header("Content-type: application/vnd-ms-excel");
 
	header("Content-Disposition: attachment; filename=Master Customer Corporate.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>Customer ID</th>
            <th>Holding Group</th>
            <th>Group Customer</th>
            <th>Customer Name</th>
            <th>Telephone 1</th>
            <th>Telephone 2</th>
            <th>Fax</th>
            <th>Email</th>
            <th>Activation</th>
            <th>Country</th>
            <th>Province</th>
            <th>City</th>
            <th>Address</th>
            <th>Zip Code</th>
            <th>Longtitude</th>
            <th>Latitude</th>
            <th>Customer Start Date</th>
            <th>Marketing Group</th>
            <th>Marketing</th>
            <th>Credit Limit (IDR)</th>
            <th>Remark</th>
            <th>NPWP Number</th>
            <th>NPWP Name</th>
            <th>NPWP Address</th>
            <th>PIC Finance</th>
            <th>Day invoice receive</th>
            <th>Receipt Time Start</th>
            <th>Receipt Time End</th>
            <th>Invoice Address</th>
            <th>Virtual Acc. Number</th>
            <th>Payment Requirements</th>
            <th>Others</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.cust
            $query_get=mysqli_query($conn,"SELECT c.nm_cust,
                                            c.telp1,
                                            c.telp2,
                                            c.address,
                                            c.status,
                                            c.id_cust,
                                            c.id_group,
                                            c.id_holding,
                                            c.fax,
                                            c.email,
                                            c.id_country,
                                            c.id_kota,
                                            c.zip_code,
                                            c.longtitude,
                                            c.latitude,
                                            c.cust_tgl,
                                            c.credit,
                                            c.remark,
                                            c.group_marketing,
                                            c.npwp_no,
                                            c.npwp_nm,
                                            c.npwp_address,
                                            c.pic_finance,
                                            c.day_invoice,
                                            c.receipt_start,
                                            c.receipt_end,
                                            c.invoice_address,
                                            c.acc_no,
                                            c.payment,
                                            c.ket,
                                            g.nm_group,
                                            h.nm_holding,
                                            n.nm_country,
                                            k.provinsi,
                                            k.kota
                                        FROM m_cust c
                                        left join m_group_cust g on g.id_group = c.id_group
                                        left join m_holding h on h.id_holding = c.id_holding
                                        left join m_country n on n.id_country = c.id_country
                                        left join m_kota k on k.id_kota = c.id_kota
                                        where c.individu = ''
                                        ORDER BY c.id_cust asc");
                    if(!$query_get){
                        die(mysqli_error());
                    }
            while ($row_get = mysqli_fetch_array($query_get)) {
        
            $style="";
            
            echo'
                <tr '.$style.'>
                    <td style="font-size:13px;">'.$row_get['id_cust'].'</td>
                    <td style="font-size:13px;">( '.$row_get['id_holding'].' ) '.$row_get['nm_holding'].'</td>
                    <td style="font-size:13px;">( '.$row_get['id_group'].' ) '.$row_get['nm_group'].'</td>
                    <td style="font-size:13px;">'.$row_get['nm_cust'].'</td>
                    <td style="font-size:13px;">'.$row_get['telp1'].'</td>
                    <td style="font-size:13px;">'.$row_get['telp2'].'</td>
                    <td style="font-size:13px;">'.$row_get['fax'].'</td>
                    <td style="font-size:13px;">'.$row_get['email'].'</td>
                    <td style="font-size:13px;">'.$row_get['status'].'</td>
                    <td style="font-size:13px;">'.$row_get['nm_country'].'</td>
                    <td style="font-size:13px;">'.$row_get['provinsi'].'</td>
                    <td style="font-size:13px;">'.$row_get['kota'].'</td>
                    <td style="font-size:13px;">'.$row_get['address'].'</td>
                    <td style="font-size:13px;">'.$row_get['zip_code'].'</td>
                    <td style="font-size:13px;">'.$row_get['longtitude'].'</td>
                    <td style="font-size:13px;">'.$row_get['latitude'].'</td>
                    <td style="font-size:13px;">'.$row_get['cust_tgl'].'</td>
            ';
            
            // Panggil m.group_marketing
            $query_get_group=mysqli_query($conn,"SELECT gm.id_group_marketing,
                                            ky.nama_karyawan
                                        FROM m_group_marketing gm
                                        left join m_karyawan ky on ky.id_karyawan = gm.head_group
                                        where gm.id_group_marketing = '".$row_get['group_marketing']."' ");
                    if(!$query_get_group){
                        die(mysqli_error());
                    }
            $row_get_group = mysqli_fetch_array($query_get_group);
                echo'
                    <td style="font-size:13px;">( '.$row_get_group['id_group_marketing'].' ) '.$row_get_group['nama_karyawan'].'</td>
                    <td style="font-size:13px;">( '.$row_get_group['id_group_marketing'].' ) '.$row_get_group['nama_karyawan'].'</td>
                ';

            echo'
                    <td style="font-size:13px;">'.$row_get['credit'].'</td>
                    <td style="font-size:13px;">'.$row_get['remark'].'</td>
                    <td style="font-size:13px;">'.$row_get['npwp_no'].'</td>
                    <td style="font-size:13px;">'.$row_get['npwp_nm'].'</td>
                    <td style="font-size:13px;">'.$row_get['npwp_address'].'</td>
                    <td style="font-size:13px;">'.$row_get['pic_finance'].'</td>
                    <td style="font-size:13px;">'.$row_get['day_invoice'].'</td>
                    <td style="font-size:13px;">'.$row_get['receipt_start'].'</td>
                    <td style="font-size:13px;">'.$row_get['receipt_end'].'</td>
                    <td style="font-size:13px;">'.$row_get['invoice_address'].'</td>
                    <td style="font-size:13px;">'.$row_get['acc_no'].'</td>
                    <td style="font-size:13px;">'.$row_get['payment'].'</td>
                    <td style="font-size:13px;">'.$row_get['ket'].'</td>
                </tr>
            ';
        }
        ?>
    </tbody>
</table>