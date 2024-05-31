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
 
	header("Content-Disposition: attachment; filename=Master Group Customer.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>Holding Group</th>
            <th>Group Customer ID</th>
            <th>Group Customer Name</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas
            $query_get=mysqli_query($conn,"SELECT h.id_group, 
                                            h.id_holding, 
                                            h.nm_group,
                                            gc.nm_holding
                                        FROM m_group_cust h
                                        JOIN m_holding gc ON h.id_holding = gc.id_holding
                                        ORDER BY h.id_group asc");
                                        if($query_get == false){
                                            die(mysqli_error());
                                        }
            while ($row_get = mysqli_fetch_array($query_get)) {
        
            $style="";

            echo'
                <tr '.$style.'>
                    <td style="font-size:13px;">( '.$row_get['id_holding'].' ) '.$row_get['nm_holding'].'</td>
                    <td style="font-size:13px;">'.$row_get['id_group'].'</td>
                    <td style="font-size:13px;">'.$row_get['nm_group'].'</td>
                </tr>
            ';
        }
        ?>
    </tbody>
</table>