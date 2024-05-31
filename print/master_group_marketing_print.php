<?php
	session_start();
	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";
  
	if(!isset($_SESSION['id_user']) && !isset($_SESSION['grup']))
	{
	    header('location:../login.php'); 
	}

	header("Content-type: application/vnd-ms-excel");
 
	header("Content-Disposition: attachment; filename=Group Marketing.xls");
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
            <th>ID Group Marketing</th>
            <th>Head Group Marketing</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas
            $no = 0;
            $query_get = mysqli_query($conn,"  SELECT  mg.id_group_marketing,
                                                mk.nama_karyawan
                                        from    m_group_marketing mg
                                            left join m_karyawan mk USING(id_group_marketing)
                                        ORDER BY mg.id_group_marketing ASC                                        
                                        ");
            while ($row_get = mysqli_fetch_array($query_get)) {

            $no++;
            $style = "";
            
        echo'
            <tr '.$style.'>
            <td>'.$row_get['id_group_marketing'].' </td>
            <td>'.$row_get['nama_karyawan'].' </td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>