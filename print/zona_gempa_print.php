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
 
	header("Content-Disposition: attachment; filename=Zona EQVET.xls");
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
            <th>Nama Provinsi</th>
            <th>Nama Kota</th>
            <th>Zona</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas
            $no = 0;
            $query_get = mysqli_query($conn,"  SELECT  provinsi, 
                                                kota,
                                                zona_gempa
                                        FROM    m_kota
                                        ORDER BY provinsi asc");
            while ($row_get = mysqli_fetch_array($query_get)) {
        
            $no++;
            $style = "";
            
        echo'
            <tr '.$style.'>
            <td >'.$no.' </td>
            <td>  '.$row_get['provinsi'].' </td>
            <td>  '.$row_get['kota'].' </td>
            <td class="center"> Zona '.$row_get['zona_gempa'].' </td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>