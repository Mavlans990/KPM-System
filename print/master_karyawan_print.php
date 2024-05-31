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
 
	header("Content-Disposition: attachment; filename=Karyawan.xls");
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
            <th>ID Karyawan</th>
            <th>Nama Karyawan</th>
            <th>Tanggal Masuk Kerja</th>
            <th>Departemen</th>
            <th>Posisi</th>
        </tr>
    </thead>
    <tbody>
        <?php
            // Panggil m.flexas
            $no = 0;
            $query_get = mysqli_query($conn,"  SELECT  mk.id_karyawan,
                                                mk.nama_karyawan,
                                                mk.tgl_mulai_kerja,
                                                md.nama_departemen,
                                                md.posisi
                                        from    m_karyawan mk
                                            left join m_departemen md USING(m_departemen_id)
                                        ORDER BY mk.id_karyawan ASC                                        
                                        ");
            while ($row_get = mysqli_fetch_array($query_get)) {

            $no++;
            $style = "";
            
        echo'
            <tr '.$style.'>
            <td>'.$row_get['id_karyawan'].' </td>
            <td>'.$row_get['nama_karyawan'].' </td>
            <td>'.$row_get['tgl_mulai_kerja'].' </td>
            <td>'.$row_get['nama_departemen'].' </td>
            <td>'.$row_get['posisi'].' </td>
        </tr>
            ';
        }
        ?>
    </tbody>
</table>