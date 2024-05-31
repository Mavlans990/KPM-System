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
 
	header("Content-Disposition: attachment; filename=Master Account Executive.xls");
?>
<style type="text/css">
	th {
		font-weight: bold;
	}
</style>

<table border="1" style="border: 1px solid black">
    <thead>
        <tr>
            <th>ID Marketing</th>
            <th>Nama Marketing</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $query_get_cust = "SELECT DISTINCT c.group_marketing ,k.nama_karyawan
                                        from m_cust c
                                        join m_group_marketing g on g.id_group_marketing = c.group_marketing
                                        join m_karyawan k on k.id_karyawan = g.head_group
                                        where c.group_marketing <> ''
                                        and  c.group_marketing <> 'new' ";
            $sql = mysqli_query($conn,$query_get_cust);
            while ($row = mysqli_fetch_array($sql)) {
        
            
                echo '
                <tr style="font-size:13px;padding:0px !important;">
                    <td>'.$row['group_marketing'].'</td>
                    <td>'.$row['nama_karyawan'].'</td>
                </tr>
                ';
            
            }
        
        ?>
    </tbody>
</table>