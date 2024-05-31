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
 
	header("Content-Disposition: attachment; filename=Flexas Level 1.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>Kode Okupasi (2 digit)</th>
            <th>Level I</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas1
            $query_get=mysqli_query($conn,"SELECT mf.level1_code, 
                                            mf.level1_desc,
                                            mf.id_flexas1
                                        FROM m_flexas1 mf
                                            WHERE mf.parent <> 'new'
                                        ORDER BY mf.level1_code asc");
            while ($row_get = mysqli_fetch_array($query_get)) {
        
            $style="";
            
        echo'
            <tr '.$style.'>
            <td style="width:250px;">'.$row_get['level1_code'].'</td>
            <td style="font-size:11px;width:450px;">'.$row_get['level1_desc'].'</td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>