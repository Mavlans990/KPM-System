<?php
	include "../lib/koneksi.php";
	
	if(isset($_POST['product']))
	{
		$filename = $_FILES['file']['name'];
		$file_tmp = $_FILES['file']['tmp_name'];
	
	// The nested array to hold all the arrays
	
	move_uploaded_file($file_tmp, 'script/'.$filename);
	// Open the file for reading
	if (($h = fopen("script/{$filename}", "r")) !== FALSE) 
	{
	  // Each line in the file is converted into an individual array that we call $data
	  // The items of the array are comma separated
	   
	  while (($data = fgetcsv($h, 1000, ",")) !== FALSE) 
	  {
		// Each individual array is being pushed into the nested array
			$string = trim(strtoupper($data[12]));
			if($string == 'KG')
			{
				$qty = $data[11] * 1000;
			}
			else
			{
				$qty = $data[11];
			}
		
            $query = mysql_query("update product set qty='".$qty."' where sku = '".$data[0]."'");
			if($query)
			{
				$status = "success";
			}
			else
			{
				$status = "failed";
			}
        	
	  }

	  // Close the file
	  fclose($h);
	}
	else
	{
		echo "<script>alert('invalid input');</script>";
		$status = "";
	}
	echo "<script>alert('".$status."');window.location='../product.php';</script>";
	// Display the code in a readable format
	
	}
?>