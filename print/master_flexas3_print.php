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
 
	header("Content-Disposition: attachment; filename=Flexas Level 3.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>Okupasi Lv3 (4-6 digit)</th>
            <th>Level I</th>
            <th>Level II</th>
            <th>Level III</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas3
            $query_get=mysqli_query($conn,"SELECT mf.id_flexas1, 
                                            mf.id_flexas2, 
                                            mf.id_flexas3, 
                                            mf.level3_desc,
                                            mf.level3_code,
                                            mf2.level2_desc,
                                            mf2.level2_code,
                                            mf1.level1_desc,
                                            mf1.level1_code
                                        FROM m_flexas3 mf
                                        JOIN m_flexas1 mf1 ON mf.id_flexas1 = mf1.id_flexas1
                                        JOIN m_flexas2 mf2 ON mf.id_flexas2 = mf2.id_flexas2
                                        ORDER BY mf1.level1_code asc,mf2.level2_code asc ,mf.level3_code asc");
                                        if($query_get == false){
                                            die(mysqli_error());
                                        }
            while ($row_get = mysqli_fetch_array($query_get)) {
        
            $style="";

            echo'
                <tr '.$style.'>
                    <td style="width:250px;font-size:12px;">'.$row_get['level3_code'].'</td>
                    <td style="width:30%;font-size:11px;width:450px"> ( '.$row_get['level1_code'].' ) '.$row_get['level1_desc'].' </td>
                    <td style="width:30%;font-size:11px;width:450px"> ( '.$row_get['level2_code'].' ) '.$row_get['level2_desc'].' </td>
                    <td style="width:30%;font-size:11px;width:450px" valign="top"> ( '.$row_get['level3_code'].' ) '.$row_get['level3_desc'].'</td>
                </tr>
            ';
        }
        ?>
    </tbody>
</table>