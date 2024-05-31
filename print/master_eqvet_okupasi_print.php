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
 
	header("Content-Disposition: attachment; filename=Grup Okupasi EQVET.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
    .center {
        text-align : center;
    }
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>No</th>
            <th>Grup Okupasi</th>
            <th>Kode Okupasi</th>
            <th>Level 1</th>
            <th>Level 2</th>
            <th>Level 3</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas
            $no = 0;
            $query_get = mysqli_query($conn,"  SELECT  a.grup_okupansi,
                                                d.level1_code,
                                                c.level2_code,
                                                b.level3_code,
                                                d.level1_desc,
                                                c.level2_desc,
                                                b.level3_desc
                                        from m_okupansi_gempa a
                                            join m_flexas3 b on  a.id_flexas3 = b.id_flexas3
                                            join m_flexas2 c on  b.id_flexas2 = c.id_flexas2
                                            join m_flexas1 d on  c.id_flexas1 = d.id_flexas1
                                        where grup_okupansi <> 'new' ");
            while ($row_get = mysqli_fetch_array($query_get)) {
                $kode_okupasi=$row_get['level3_code'];
                if($row_get['level3_code'] == 'none'){
                    $kode_okupasi = $row_get['level2_code'];
                }
                if ($row_get['level3_code'] == 'none' && $row_get['level2_code'] == 'none') {
                    $kode_okupasi = $row_get['level1_code'];
                }

            $no++;
            $style = "";
            
        echo'
            <tr '.$style.'>
            <td>'.$no.' </td>
            <td class="center"> '.$row_get['grup_okupansi'].' </td>
            <td class="center"> '.$kode_okupasi.' </td>
            <td style="font-size:12px; width:400px;"> '.$row_get['level1_desc'].' </td>
            <td style="font-size:12px; width:400px;"> '.$row_get['level2_desc'].' </td>
            <td style="font-size:12px; width:400px;"> '.$row_get['level3_desc'].' </td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>