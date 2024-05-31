<?php
	date_default_timezone_set("Asia/Bangkok");
	
	function money_idr($input)
	{
		$output="IDR ".number_format( $input, 0 , '' , '.' )."";
		return $output;
	}
	
	function money($input)
	{
		$output=number_format( $input, 0 , '' , '.' );
		return $output;
	}
	

	function curr_replace($input)
	{
	
		$search = ",";
		$replace = "#";
		$output=str_replace($search,$replace,$input);
		
		$search = ".";
		$replace = ",";
		$output=str_replace($search,$replace,$output);
	
		$search = "#";
		$replace = ".";
		$output=str_replace($search,$replace,$output);
	
		return $output;
	}
