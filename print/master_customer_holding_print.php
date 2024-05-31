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
 
	header("Content-Disposition: attachment; filename=Master Holding Group.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>Holding Group ID</th>
            <th>Holding Group Name</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.holding
            $query_get=mysqli_query($conn,"SELECT h.nm_holding, 
                                            h.id_holding
                                        FROM m_holding h
                                        ORDER BY h.id_holding asc");
            while ($row_get = mysqli_fetch_array($query_get)) {
        
            $style="";
            
            echo'
                <tr '.$style.'>
                    <td style="font-size:13px;">'.$row_get['id_holding'].'</td>
                    <td style="font-size:13px;">'.$row_get['nm_holding'].'</td>
                </tr>
            ';
        }
        ?>
    </tbody>
</table>