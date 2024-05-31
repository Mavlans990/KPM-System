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
 
	header("Content-Disposition: attachment; filename=Departemen.xls");
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
            <th>ID Departemen</th>
            <th>Nama Departemen</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas
            $no = 0;
            $query_get = mysqli_query($conn,"  SELECT  DISTINCT id_departemen,
                                                nama_departemen
                                        from    m_departemen
                                        ORDER BY id_departemen ASC                                        
                                        ");
            while ($row_get = mysqli_fetch_array($query_get)) {

            $no++;
            $style = "";
            
        echo'
            <tr '.$style.'>
            <td>'.$row_get['id_departemen'].' </td>
            <td>'.$row_get['nama_departemen'].' </td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>