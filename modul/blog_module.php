<?php
session_start();
include "../lib/koneksi.php";
include "../lib/appcode.php";
include "../lib/format.php";

if (isset($_POST['save'])) {
	if ($_POST['id']=="") {
		$path="";
	    $ekstensi_diperbolehkan = array('png','jpg','bmp','jpeg','gif','PNG','JPG','BMP','JPEG','GIF');
	    $gambar = $_FILES['path']['name'];
	    $x = explode('.', $gambar);
	    $ekstensi = strtolower(end($x));
	    $ukuran = $_FILES['path']['size'];
	    $file_tmp = $_FILES['path']['tmp_name'];   

	    if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
	        if($ukuran < 2044070){          
	            move_uploaded_file($file_tmp, '../../image/blog/'.$gambar);
	            $path="image/blog/".$gambar;

	        }else{
	            echo "<script type='text/javascript'>alert('UKURAN FILE TERLALU BESAR, MAX 2 MB')</script>";
	        }
	    }
	    $query=mysql_query("INSERT INTO blog(id_blog, judul, isi, path, dibuat_tgl, jenis) VALUES ('','".mysql_real_escape_string($_POST['judul'])."','".mysql_real_escape_string($_POST['isi'])."','".$path."','".date('Y-m-d')."','".mysql_real_escape_string($_POST['jenis'])."')");
	    
	    if ($query) {
	    	commit();
	        echo "<script type='text/javascript'>alert('Insert Success');window.location.href = '../blog.php';</script>";
	    } else {
	    	rollback();
	        echo "<script type='text/javascript'>alert('Insert Failed');window.location.href = '../blog.php';</script>";
	    }

	} else {
		$jml=0;
	    if($_FILES['path']['name']=="") {
	        $jml=1;
	    }
	    $ekstensi_diperbolehkan = array('png','jpg','bmp','jpeg','gif','PNG','JPG','BMP','JPEG','GIF');
	    $gambar = $_FILES['path']['name'];
	    $x = explode('.', $gambar);
	    $ekstensi = strtolower(end($x));
	    $ukuran = $_FILES['path']['size'];
	    $file_tmp = $_FILES['path']['tmp_name'];   

	    if ($jml==0) {
	    	if(in_array($ekstensi, $ekstensi_diperbolehkan) === true){
		        if($ukuran < 2044070){          
		            move_uploaded_file($file_tmp, '../../images/blog/'.$gambar);
		            $path="images/blog/".$gambar;
		        }else{
		            echo "<script type='text/javascript'>alert('UKURAN FILE TERLALU BESAR, MAX 2 MB');window.location.href = '../blog.php';</script>";
		        }
		    }
	    }
	    
	    if ($jml==0) {
	        $path2=" path='".$path."',";
	    } else {
	        $path2="";
	    }
	            
	    $query=mysql_query("UPDATE blog SET judul='".mysql_real_escape_string($_POST['judul'])."',isi='".mysql_real_escape_string($_POST['isi'])."',".$path2."dibuat_tgl='".date('Y-m-d')."',jenis='".mysql_real_escape_string($_POST['jenis'])."' WHERE id_blog='".mysql_real_escape_string($_POST['id'])."'");

	    if ($query) {
	        echo "<script type='text/javascript'>alert('Update Success');window.location.href = '../blog.php';</script>";
	    } else {
	        echo "<script type='text/javascript'>alert('Update Failed');window.location.href = '../blog.php';</script>";
	    }
	}
}

if (isset($_POST['delete'])) {
	$valid=1;
	$query=mysql_query("DELETE FROM blog WHERE id_blog='".mysql_real_escape_string($_POST['id_hapus'])."'");
	if (!$query) {
		$valid=0;
		$msg="ERROR : Delete Data Failed";
	}

	if($valid==0) {  
		rollback();
		echo "<script type='text/javascript'>alert('".$msg."');window.location.href = '../blog.php';</script>";
	} else { 
		commit();
		$msg="Delete Data Success";
		echo "<script type='text/javascript'>alert('".$msg."');window.location.href = '../blog.php';</script>";
	}
}
?>