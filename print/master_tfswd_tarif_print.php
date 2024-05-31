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
 
	header("Content-Disposition: attachment; filename=Tarif TFSWD.xls");
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
            <th>Zona</th>
            <th>Keterangan</th>
            <th>Tarif Bawah</th>
            <th>Tarif Atas</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas
            $no = 0;
            $query_get = mysqli_query($conn,"  SELECT  kode_area,
                                                zona_banjir,
                                                keterangan,
                                                tarif_banjir_bawah,
                                                tarif_banjir_atas
                                        from m_tfswd_tarif
                                        ");
            while ($row_get = mysqli_fetch_array($query_get)) {

            $no++;
            $style = "";
            
        echo'
            <tr '.$style.'>
            <td class="center"> '.$row_get['kode_area'].' </td>
            <td class="center">Zona '.$row_get['zona_banjir'].' </td>
            <td style="font-size:12px; width:400px;" >'.$row_get['keterangan'].' </td>
            <td class="center"> '.number_format($row_get['tarif_banjir_bawah'],3).'%</td>
            <td class="center"> '.number_format($row_get['tarif_banjir_atas'],3).'%</td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>