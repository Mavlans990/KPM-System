<?php
    include "../lib/koneksi.php";
    include "../lib/appcode.php";
    include "../lib/format.php";
  
if(isset($_POST['kategori'])){
    $kategori=mysqli_real_escape_string($conn,$_POST['kategori']);
    $kode = "";
    $query = "";
    $awal = "0";
    
    $query_get_kategori = "SELECT * FROM m_kategori_akun WHERE kat_akun_id = '".$kategori."'";
        $sql_get_kategori = mysqli_query($conn,$query_get_kategori);
        if($row_kat = mysqli_fetch_array($sql_get_kategori)){
            $kode = $row_kat['kode_kategori'];
            $query = "kode_akun like '".$kode."%' ";
        }
    
    $kode_akun = "";
    $query_kode = mysqli_query($conn,"SELECT max(kode_akun) as max_id FROM m_akun WHERE ".$query." ");
    if($row_kode=mysqli_fetch_array($query_kode)){
        $id = $row_kode['max_id'];     
        $urut = (int) substr($id, 5, 2);
        $urut++;
        $kode_akun = $kode.$urut;
    }
    else 
    {
        $kode_akun = $kode.$awal;
    } 
    echo'
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span style="width:150px;"
                    class="form-control filled-input form-control-sm input-group-text "
                    id="inputGroup-sizing-sm">Nomor</span>
            </div>
            <input autocomplete="off" type="text" name="kode_akun"
                id="kode_akun" class="kode_akun form-control form-control-sm" value="'.$kode_akun.'"
                required>
        </div>
    ';
}

    if(isset($_POST['detail'])){
        $kat_akun=mysqli_real_escape_string($conn,$_POST['kat_akun']);
        $detail = mysqli_real_escape_string($conn,$_POST['detail']);
        $kode_akun_filter = mysqli_real_escape_string($conn,$_POST['kode_akun']);
        $query_get_akun ="";
        $sql_get_akun = "";
        $row_akun = "";
        if($detail == "Sub-Akun dari"){
            $query_get_akun = "SELECT kode_akun,nm_akun,m_akun_id from m_akun WHERE kat_akun = '".$kat_akun."' and not m_akun_id = '".$kode_akun_filter."' ";
            $sql_get_akun = mysqli_query($conn,$query_get_akun);
            while ($row_akun = mysqli_fetch_array($sql_get_akun)) {
                $nm_head = $row_akun['nm_akun'];
                $kode_head =$row_akun['kode_akun'];
                $id_head = $row_akun['m_akun_id'];  
                            
                echo'
                    <option value="'.$row_akun['m_akun_id'].'">('.$row_akun['kode_akun'].') '.$row_akun['nm_akun'].'</option>
                ';
            }
        }
        if($detail == "Akun Header dari"){
            $query_get_akun_head = "SELECT kode_akun,nm_akun,m_akun_id,head_akun from m_akun WHERE kat_akun = '".$kat_akun."' or  head_akun = 'none' or head_akun = '".$kode_akun_filter."' ";
            $sql_get_akun_head = mysqli_query($conn,$query_get_akun_head);
            while ($row_akun_head = mysqli_fetch_array($sql_get_akun_head)) {
                $nm_sub = $row_akun_head['nm_akun'];
                $kode_sub =$row_akun_head['kode_akun'];
                $id_sub = $row_akun_head['m_akun_id'];
                $head = $row_akun_head['head_akun'];
                if($head !== "none" || $head !== $kode_akun_filter){
                    echo'
                        <option style="display:none;" value="'.$row_akun_head['m_akun_id'].'">('.$row_akun_head['kode_akun'].') '.$row_akun_head['nm_akun'].'</option>
                    ';                
                }else{
                    echo'
                        <option value="'.$row_akun_head['m_akun_id'].'">('.$row_akun_head['kode_akun'].') '.$row_akun_head['nm_akun'].'</option>
                    ';
                }
            }
        }

        
        
    }
    
?>