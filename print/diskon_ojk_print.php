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
 
	header("Content-Disposition: attachment; filename=MAX DISKON OJK.xls");
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
            <th>Nilai Pertanggungan Yang Di jamin</th>
            <th>Kurs</th>
            <th>Max Disc (%)</th>
        </tr>
    </thead>
    <tbody>
    <?php
        $n=1;
        $filter="";
        $query_get_m_tarif_ojk=mysqli_query($conn,"SELECT * from m_tarif_ojk order by kurs asc");
        while ($row = mysqli_fetch_array($query_get_m_tarif_ojk)) {
            if($row['notasi'] == "Lebih dari"){
                $filter=">";
            }
            else if($row['notasi'] == "Kurang dari"){
                $filter="<";
            }
            else if($row['notasi'] == "Lebih dari sama dengan"){
                $filter=">=";
            }
            else if($row['notasi'] == "Kurang dari sama dengan"){
                $filter="<=";
            }
            else if($row['notasi'] == "Sama dengan"){
                $filter="=";
            }                        
            echo '
        <tr>
            <td>'.$n.'</td>
            <td>'.$filter.' '.number_format($row['nilai'], 2, ".", ",").'</td>
            <td>'.$row['kurs'].' </td>
            <td>'.floatval($row['max_disc']).'%</td>
        </tr>
            ';
            $n++;
        }
    ?>
    </tbody>
</table>