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
 
	header("Content-Disposition: attachment; filename=IDEMNITY PERIOD.xls");
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
            <th>Masa Pertanggungan (Bulan)</th>
            <th>Premi (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $no=1;
            $query_get_idemnity_period=mysqli_query($conn,"SELECT masa_pertanggungan,persen_premi from m_idemnity order by masa_pertanggungan asc");
            while ($row = mysqli_fetch_array($query_get_idemnity_period)) {
            echo '
            <tr>
                <td>'.$no.'</td>
                <td>'.$row['masa_pertanggungan'].' Bulan </td>
                <td>'.floatval($row['persen_premi']).'% </td>
            </tr>
            ';
            $no++;
        }
        ?>
    </tbody>
</table>