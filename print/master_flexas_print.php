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
 
	header("Content-Disposition: attachment; filename=Tarif Flexas.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th rowspan="2" style="font-size:10px;">Kode Okupasi (4-6 digit)</th>
            <th rowspan="2" style="font-size:9px;">Level 1</th>
            <th rowspan="2" style="font-size:9px;">Level 2</th>
            <th rowspan="2" style="font-size:9px;">Level 3</th>
            <th colspan="2" style="font-size:10px;">Konstruksi Kelas I</th>
            <th colspan="2" style="font-size:10px;">Konstruksi Kelas II</th>
            <th colspan="2" style="font-size:10px;">Konstruksi Kelas III</th>
        </tr>
        <tr>
            <th style="font-size:10px;width:55px !important;">Tarif Bawah (%)</th>
            <th style="font-size:10px;">Tarif Atas (%)</th>
            <th style="font-size:10px;">Tarif Bawah (%)</th>
            <th style="font-size:10px;">Tarif Atas (%)</th>
            <th style="font-size:10px;">Tarif Bawah (%)</th>
            <th style="font-size:10px;">Tarif Atas (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas3
            $query_get=mysqli_query($conn,"SELECT mf.tarif_bawah_1, 
                                            mf.tarif_bawah_2,
                                            mf.tarif_bawah_3,
                                            mf.tarif_atas_1,
                                            mf.tarif_atas_2,
                                            mf.tarif_atas_3,
                                            mf.level3_desc,
                                            mf2.level2_desc,
                                            mf1.level1_desc,
                                            mf.level3_code,
                                            mf2.level2_code,
                                            mf1.level1_code,
                                            mf.id_flexas3,
                                            mf.id_flexas2,
                                            mf.id_flexas1
                                        FROM m_flexas3 mf
                                        LEFT JOIN m_flexas2 mf2 ON mf.id_flexas2=mf2.id_flexas2
                                        LEFT JOIN m_flexas1 mf1 ON mf.id_flexas1=mf1.id_flexas1
                                        
                                        ORDER BY mf1.level1_code asc");
            while ($row_get = mysqli_fetch_array($query_get)) {
        
        $code=$row_get['level3_code'];
        $desc=' ( '.$row_get['level3_code'].' ) '.$row_get['level3_desc'];
        if($row_get['level3_code']=="none")
        {
            $code=$row_get['level2_code'];    
            $desc="-";
        }
        
        echo'
        <tr>
            <td style="font-size:10px;">'.$code.'</td>
            <td style="font-size:10px;width:300px;"> ( '.$row_get['level1_code'].' ) '.$row_get['level1_desc'].'</td>
            <td style="font-size:10px;width:300px;"> ( '.$row_get['level2_code'].' ) '.$row_get['level2_desc'].'</td>
            <td style="font-size:10px;width:300px;">'.$desc.'</td>

            <td style="font-size:11px;padding:0px !important;width:55px !important;">
                '.number_format($row_get['tarif_bawah_1'], 4).'
            </td>
            <td style="font-size:11px;padding:0px !important;width:55px;">
                '.number_format($row_get['tarif_atas_1'], 4).'
            </td>
            <td style="font-size:11px;padding:0px !important;width:55px;">
                '.number_format($row_get['tarif_bawah_2'], 4).'
            </td>
            <td style="font-size:11px;padding:0px !important;width:55px;">
                '.number_format($row_get['tarif_atas_2'], 4).'
            </td>
            <td style="font-size:11px;padding:0px !important;width:55px;">
                '.number_format($row_get['tarif_bawah_3'], 4).'
            </td>
            <td style="font-size:11px;padding:0px !important;width:55px;">
                '.number_format($row_get['tarif_atas_3'], 4).'
            </td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>