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
 
	header("Content-Disposition: attachment; filename=Master Asuransi.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>Asuransi ID</th>
            <th>Asuransi Name</th>
            <th>Jenis Asuransi</th>
            <th>Telephone 1</th>
            <th>Telephone 2</th>
            <th>Fax</th>
            <th>Email</th>
            <th>Country</th>
            <th>Province</th>
            <th>City</th>
            <th>Address</th>
            <th>Zip Code</th>
            <th>NPWP Number</th>
            <th>NPWP Name</th>
            <th>NPWP Address</th>
            <th>PIC Finance</th>
            <th>Virtual Acc. Number</th>
            <th>Payment Requirements</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.asuransi
            $query_get=mysqli_query($conn,"SELECT c.nm_asuransi,
                                            c.telp1,
                                            c.telp2,
                                            c.address,
                                            c.id_asuransi,
                                            c.jenis,
                                            c.fax,
                                            c.email,
                                            c.zip_code,
                                            n.nm_country,
                                            k.kota,
                                            k.provinsi,
                                            c.npwp_no,
                                            c.npwp_nm,
                                            c.npwp_address,
                                            c.pic_finance,
                                            c.acc_no,
                                            c.payment
                                        FROM m_asuransi c
                                        join m_country n on n.id_country = c.id_country
                                        join m_kota k on k.id_kota = c.id_kota
                                        ORDER BY c.id_asuransi asc");
            while ($row_get = mysqli_fetch_array($query_get)) {
                $jenis=$row_get['jenis'];

            $style="";
            
            echo'
                <tr '.$style.'>
                    <td style="font-size:13px;">'.$row_get['id_asuransi'].'</td>
                    <td style="font-size:13px;">'.$row_get['nm_asuransi'].'</td>
                    <td style="font-size:13px;">'.$jenis.'</td>
                    <td style="font-size:13px;">'.$row_get['telp1'].'</td>
                    <td style="font-size:13px;">'.$row_get['telp2'].'</td>
                    <td style="font-size:13px;">'.$row_get['fax'].'</td>
                    <td style="font-size:13px;">'.$row_get['email'].'</td>
                    <td style="font-size:13px;">'.$row_get['nm_country'].'</td>
                    <td style="font-size:13px;">'.$row_get['provinsi'].'</td>
                    <td style="font-size:13px;">'.$row_get['kota'].'</td>
                    <td style="font-size:13px;">'.$row_get['address'].'</td>
                    <td style="font-size:13px;">'.$row_get['zip_code'].'</td>
                    <td style="font-size:13px;">'.$row_get['npwp_no'].'</td>
                    <td style="font-size:13px;">'.$row_get['npwp_nm'].'</td>
                    <td style="font-size:13px;">'.$row_get['npwp_address'].'</td>
                    <td style="font-size:13px;">'.$row_get['pic_finance'].'</td>
                    <td style="font-size:13px;">'.$row_get['acc_no'].'</td>
                    <td style="font-size:13px;">'.$row_get['payment'].'</td>
                </tr>
            ';
        }
        ?>
    </tbody>
</table>