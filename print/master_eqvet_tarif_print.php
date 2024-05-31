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
 
	header("Content-Disposition: attachment; filename=Tarif EQVET.xls");
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
            <th rowspan="2">No</th>
            <th rowspan="2">Grup Okupasi</th>
            <th rowspan="2">Kelas Konstruksi</th>
            <th colspan="5">Tarif Premi</th>
        </tr>
        <tr>
            <th>Zona 1</th>
            <th>Zona 2</th>
            <th>Zona 3</th>
            <th>Zona 4</th>
            <th>Zona 5</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas
            $no = 0;
            $query_get = mysqli_query($conn,"  SELECT  grup_okupansi,
                                                kelas_konstruksi,
                                                zona_1,
                                                zona_2,
                                                zona_3,
                                                zona_4,
                                                zona_5
                                        from m_eqvet_tarif
                                        where grup_okupansi <> 'new' ");
            while ($row_get = mysqli_fetch_array($query_get)) {

            $no++;
            $style = "";
            
        echo'
            <tr '.$style.'>
            <td>'.$no.' </td>
            <td class="center"> '.$row_get['grup_okupansi'].' </td>
            <td > '.$row_get['kelas_konstruksi'].' </td>
            <td class="center"> '.number_format($row_get['zona_1'],2).'%</td>
            <td class="center"> '.number_format($row_get['zona_2'],2).'%</td>
            <td class="center"> '.number_format($row_get['zona_3'],2).'%</td>
            <td class="center"> '.number_format($row_get['zona_4'],2).'%</td>
            <td class="center"> '.number_format($row_get['zona_5'],2).'%</td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>