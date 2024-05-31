<?php
	ob_start();
	session_start();

	include "../lib/koneksi.php";
	include "../lib/appcode.php";
	include "../lib/format.php";
  
    if (isset($_POST['add_list'])) {
        $id_transaksi = mysqli_real_escape_string($conn,$_POST['add_list']);
        $tgl_transaksi = mysqli_real_escape_string($conn,$_POST['tgl_jurnal']);
        $str=mysqli_real_escape_string($conn,$_POST['akun']);
        $deskripsi=mysqli_real_escape_string($conn,$_POST['deskripsi']);
        $debit=mysqli_real_escape_string($conn,$_POST['debit']);
        $kredit=mysqli_real_escape_string($conn,$_POST['kredit']);
        $total_debit=mysqli_real_escape_string($conn,$_POST['total_debit']);
        $total_kredit=mysqli_real_escape_string($conn,$_POST['total_kredit']);
        $memo=mysqli_real_escape_string($conn,$_POST['memo']);

        $string = preg_replace("/[^a-zA-Z0-9-]/"," ", $str);
        $explode = explode(" ",$string);
        $kode_akun = $explode[1];
        

        $query_add_list = "INSERT INTO tb_jurnal_umum (
                                                        jurnal_umum_id,
                                                        no_transaksi,
                                                        tgl_jurnal_umum,
                                                        kode_akun,
                                                        description,
                                                        debit,
                                                        kredit,
                                                        total_debit,
                                                        total_kredit,
                                                        memo,
                                                        dibuat_oleh,
                                                        dibuat_tgl
                                                        )
                                        VALUES (
                                                null,
                                                '".$id_transaksi."',
                                                '".$tgl_transaksi."',
                                                '".$kode_akun."',
                                                '".$deskripsi."',
                                                '".$debit."',
                                                '".$kredit."',
                                                '".$total_debit."',
                                                '".$total_kredit."',
                                                '".$memo."',
                                                '".$_SESSION['id_user']."',
										        '".date('Y-m-d h:i:s')."'
                                        )
                                                        
                            ";
        $add_list = mysqli_query($conn,$query_add_list);
        
        $query_set_jurnal = "UPDATE tb_jurnal_umum 
                            SET memo = '".$memo."',
                                total_debit = '".$total_debit."',
                                total_kredit = '".$total_kredit."'
                            WHERE no_transaksi = '".$id_transaksi."'
                        ";
        $set_jurnal = mysqli_query($conn,$query_set_jurnal);

	                $query_get_akun = "SELECT   b.jurnal_umum_id,
	                                            a.kode_akun,
                                                a.nm_akun,
                                                b.description,
                                                b.debit,
                                                b.kredit
                                        FROM m_akun a
                                        LEFT JOIN tb_jurnal_umum b on a.kode_akun = b.kode_akun
                                        WHERE b.no_transaksi = '".$id_transaksi."'
                                        order by jurnal_umum_id desc limit 1
                                        ";
                    $sql_get_akun = mysqli_query($conn,$query_get_akun);
                    if($row_akun = mysqli_fetch_array($sql_get_akun)) {
                        $jurnal_umum_id = $row_akun['jurnal_umum_id'];
                        $kode_akun = $row_akun['kode_akun'];
                        $nm_akun = $row_akun['nm_akun'];
                        $deskripsi = $row_akun['description'];
                        $debit = $row_akun['debit'];
                        $kredit = $row_akun['kredit'];
                    echo'
                    <div class="div-list'.$jurnal_umum_id.'">
                    <div class="row mt-5">
                        <div class="col-sm-3">
                        ';  echo'
                            <div class="input-group input-group-sm">                                
                                <input autocomplete="off" list="akun_list'.$jurnal_umum_id.'" type="text" name="akun" id="akun'.$jurnal_umum_id.'" class="form-control form-control-sm" value="('.$kode_akun.') '.$nm_akun.'" placeholder="-- Pilih Akun --">
                                <datalist id="akun_list'.$jurnal_umum_id.'">
                                ';
                                $query_get_list = "SELECT   kode_akun,
                                                            nm_akun
                                                    FROM m_akun
                                                    ";
                                $sql_get_list = mysqli_query($conn,$query_get_list);
                                while ($row_list = mysqli_fetch_array($sql_get_list)) {

                                    echo'
                                        
                                        <option value="('.$row_list['kode_akun'].') '.$row_list['nm_akun'].' ">    
                                    ';
                                }
                                
                                echo'
                                    <option value="">
                                </datalist>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="input-group input-group-sm">
                                <textarea type="text" rows="1" name="deskripsi" id="deskripsi'.$jurnal_umum_id.'" class="form-control form-control-sm"  placeholder="-- Deskripsi --">'.$deskripsi.'</textarea>
                            </div>
                        </div>
                        <div class="col-sm-4 d-flex">
                            <div class="input-group input-group-sm mr-30">
                                <input autocomplete="off" type="text" data-id_class="debit" data-id_akun="'.$jurnal_umum_id.'"  id="debit_moneyfield_'.$jurnal_umum_id.'" class="kredit'.$jurnal_umum_id.' form-control form-control-sm" value="'.money($debit).'" placeholder="-- Kredit --" '.$readonly.'> 
                                <input autocomplete="off" type="hidden" name="debit" id="debit0'.$jurnal_umum_id.'" value="'.$debit.'" placeholder="-- Debit --">
                            </div>
                            <div class="input-group input-group-sm">
                                <input autocomplete="off" type="text" data-id_class="kredit" data-id_akun="'.$jurnal_umum_id.'"  id="kredit_moneyfield_'.$jurnal_umum_id.'" class="hitung-kredit form-control form-control-sm" value="'.money($kredit).'" placeholder="-- Kredit --" '.$readonly.'> 
                                <input autocomplete="off" type="hidden" name="kredit" id="kredit0'.$jurnal_umum_id.'" class="kredit kredit'.$jurnal_umum_id.' " value="'.$kredit.'" placeholder="-- Kredit --"> 
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="input-group input-group-sm button'.$jurnal_umum_id.'">
                                <a data-set_list="'.$jurnal_umum_id.'"
                                    class="set_list btn btn-warning btn-xs mr-2" id="set_list"
                                    style="color:white;"><i class="fa fa-pencil"></i></a>
                                <a data-del_list="'.$jurnal_umum_id.'" class="del_list btn btn-danger btn-xs"
                                        name="del_list" id="del_list" style="color:white;"><i
                                        class="fa fa-times"></i></a>
                            </div>
                        </div>
                    </div>
                    ';
                    }
    }
    
    if (isset($_POST['set_list'])) {
        $id_list = mysqli_real_escape_string($conn,$_POST['set_list']);
        $id_transaksi = mysqli_real_escape_string($conn,$_POST['id_jurnal']);
        $tgl_transaksi = mysqli_real_escape_string($conn,$_POST['tgl_jurnal']);
        $str=mysqli_real_escape_string($conn,$_POST['akun']);
        $deskripsi=mysqli_real_escape_string($conn,$_POST['deskripsi']);
        $debit=mysqli_real_escape_string($conn,$_POST['debit']);
        $kredit=mysqli_real_escape_string($conn,$_POST['kredit']);
        $total_debit=mysqli_real_escape_string($conn,$_POST['total_debit']);
        $total_kredit=mysqli_real_escape_string($conn,$_POST['total_kredit']);
        $memo=mysqli_real_escape_string($conn,$_POST['memo']);

        $string = preg_replace("/[^a-zA-Z0-9-]/"," ", $str);
        $explode = explode(" ",$string);
        $kode_akun = $explode[1];
        

        $query_set_list = "UPDATE tb_jurnal_umum 
                            SET kode_akun = '".$kode_akun."',
                                description = '".$deskripsi."',
                                debit = '".$debit."',
                                kredit = '".$kredit."',
                                diubah_oleh = '".$_SESSION['id_user']."',
                                diubah_tgl = '".date('Y-m-d h:i:s')."'
                            WHERE jurnal_umum_id = '".$id_list."'
                            ";
                        echo $query_set_list;
        $set_list = mysqli_query($conn,$query_set_list);
        
        $query_set_jurnal = "UPDATE tb_jurnal_umum 
                            SET memo = '".$memo."',
                                total_debit = '".$total_debit."',
                                total_kredit = '".$total_kredit."'
                            WHERE no_transaksi = '".$id_transaksi."'
                        ";
        $set_jurnal = mysqli_query($conn,$query_set_jurnal);
        
        }
        
    if (isset($_POST['del_list'])) {
        $id_list = mysqli_real_escape_string($conn,$_POST['del_list']);
        $id_transaksi = mysqli_real_escape_string($conn,$_POST['id_jurnal']);
        

        $query_del_list = "DELETE FROM tb_jurnal_umum 
                            WHERE jurnal_umum_id = '".$id_list."'
                            ";
        $del_list = mysqli_query($conn,$query_del_list);
                    $total_debit = "0";
                    $total_kredit = "0";
        
	                $query_get_total = "SELECT   SUM(debit) as debit_total,
	                                            SUM(kredit) as kredit_total
                                        FROM  tb_jurnal_umum 
                                        WHERE no_transaksi = '".$id_transaksi."'
                                        ";
                    $sql_get_total = mysqli_query($conn,$query_get_total);
                    if ($row_total = mysqli_fetch_array($sql_get_total)) {
                        $total_debit = $row_total['debit_total'];
                        $total_kredit = $row_total['kredit_total'];
                        
                        $query_set_jurnal = "UPDATE tb_jurnal_umum 
                                total_debit = '".$total_debit."',
                                total_kredit = '".$total_kredit."'
                            WHERE no_transaksi = '".$id_transaksi."'
                        ";
                        $set_jurnal = mysqli_query($conn,$query_set_jurnal);
                    }
        
                    echo'
                        <div class="total_jumlah">
                            <div class="row">
                            <div class="col-sm-6">
                                <p class="text-dark">Total Debit</p>
                                <input type="hidden" id="total_debit" value="'.$total_debit.'">
                                <p id="display_debit" >'.money($total_debit).'</p>
                            </div>
                            
                            <div class="col-sm-6">
                                <p class="text-dark">Total Kredit</p>
                                <input type="hidden" id="total_kredit" value="'.$total_kredit.'">
                                <p id="display_kredit">'.money($total_kredit).'</p>
                            </div>
                            </div>
                        </div>
                    ';
                    
        }
ob_flush();
?>