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
 
	header("Content-Disposition: attachment; filename=Flexas Level 2.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>Okupasi Lv2 (3 digit)</th>
            <th>Level I</th>
            <th>Level II</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas2
            $query_get=mysqli_query($conn,"SELECT mf.id_flexas1, 
                                            mf.level2_code, 
                                            mf.id_flexas2, 
                                            mf.level2_desc,
                                            mf1.level1_desc,
                                            mf1.level1_code
                                        FROM m_flexas2 mf
                                        JOIN m_flexas1 mf1 ON mf.id_flexas1 = mf1.id_flexas1
                                        ORDER BY mf.level2_code asc");
                                        if($query_get == false){
                                            die(mysqli_error());
                                        }
            while ($row_get = mysqli_fetch_array($query_get)) {
        
            $style="";

            echo'
                <tr '.$style.'>
                    <td style="width:250px;">'.$row_get['level2_code'].'</td>
                    <td style="font-size:11px;width:450px;"> ( '.$row_get['level1_code'].' ) '.$row_get['level1_desc'].' </td>
                    <td style="font-size:11px;width:450px;"> ( '.$row_get['level2_code'].' ) '.$row_get['level2_desc'].'</td>
                </tr>
            ';
        }
        ?>
    </tbody>
</table>