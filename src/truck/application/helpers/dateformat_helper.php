<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

		function fulltime_format($value="" )
		{
	 
				$namemonth["01"] = "January";
				$namemonth["02"] = "February";
				$namemonth["03"] = "March";
				$namemonth["04"] = "April";		
				$namemonth["05"] = "May";
				$namemonth["06"] = "June";
				$namemonth["07"] = "July";
				$namemonth["08"] = "August";	
				$namemonth["09"] = "September";
				$namemonth["10"] = "October";
				$namemonth["11"] = "November";
				$namemonth["12"] = "December";	


			$timeformat = explode(" ",$value);

			$oldformat = explode("-",$timeformat[0]);
			$year = $oldformat[0]; 
			$month = $oldformat[1]; 
			$day = $oldformat[2]; 
			
			if ($day=="00" || $day == "" || $day== ""){
				return "-";
			} else {
		  		return $day." ".$namemonth[$month]." ".$year." ".$timeformat[1];
		   }
		}  


		function monthyear_format($value="",$type="")
		{
			if ($type=="full"){ 
				$namemonth["01"] = "January";
				$namemonth["02"] = "February";
				$namemonth["03"] = "March";
				$namemonth["04"] = "April";		
				$namemonth["05"] = "May";
				$namemonth["06"] = "June";
				$namemonth["07"] = "July";
				$namemonth["08"] = "August";	
				$namemonth["09"] = "September";
				$namemonth["10"] = "October";
				$namemonth["11"] = "November";
				$namemonth["12"] = "December";	
			} else {
				$namemonth["01"] = "Jan";
				$namemonth["02"] = "Feb";
				$namemonth["03"] = "Mar";
				$namemonth["04"] = "Apr";		
				$namemonth["05"] = "May";
				$namemonth["06"] = "June";
				$namemonth["07"] = "July";
				$namemonth["08"] = "Aug";	
				$namemonth["09"] = "Sept";
				$namemonth["10"] = "Oct";
				$namemonth["11"] = "Nov";
				$namemonth["12"] = "Dec";	
			}
			$newformat = $value;
			$oldformat = explode("-",$value);
			$year = $oldformat[0]; 
			$month = $oldformat[1]; 
			if ($month=="00" || $month == "0" || $month== ""){
				return "-";
			} else {
			   return $namemonth[$month]." ".$year;
		   }
		}  
