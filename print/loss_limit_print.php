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
 
	header("Content-Disposition: attachment; filename=LOSS LIMIT.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>No</th>
            <th>Nilai Pertanggungan yang Dijamin</th>
            <th>Total Premium</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no=1;
            $query_get_loss_limit=mysqli_query($conn,"SELECT nilai_pertanggungan,total_premi from m_loss_limit order by nilai_pertanggungan desc ");
            while ($row = mysqli_fetch_array($query_get_loss_limit)) {
            echo '
                <tr>
                    <td>'.$no.'</td>
                    <td>'.number_format($row['nilai_pertanggungan'], 2).'%</td>
                    <td>'.number_format($row['total_premi'], 2).'%</td>
                </tr>
            ';
            $no++;
            }
        ?>
    </tbody>
</table>