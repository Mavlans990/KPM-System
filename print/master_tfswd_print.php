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
 
	header("Content-Disposition: attachment; filename=Zona TFSWD.xls");
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
            <th>Kode Area</th>
            <th>Nama Provinsi</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas
            $query_get = mysqli_query($conn,"  SELECT  kode_area,
                                                provinsi
                                        from m_tfswd_zona
                                        where kode_area <> 'new' ");
            while ($row_get = mysqli_fetch_array($query_get)) {

            $style = "";
            
        echo'
            <tr '.$style.'>
            <td class="center"> '.$row_get['kode_area'].' </td>
            <td > '.$row_get['provinsi'].' </td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>