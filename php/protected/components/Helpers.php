<?php

class Helpers {
	
	function datatime_feed($t) {
		if (gettype($t) == 'string')
			$t = strtotime($t);
			
		if (date('Y') == date('Y', $t) && date('z') == date('z', $t))
			return date('h:i A', $t);

		elseif (date('Y') == date('Y', $t) && date('z') - date('z', $t) == 1)
			return 'Yesterday '.date('h:i A', $t);
			
		else
			return date('M j, Y', $t);
	}
	
	function datatime_trim($t) {
		if (gettype($t) == 'string')
			$t = strtotime($t);
			
		if (date('Y') == date('Y', $t) && date('z') == date('z', $t))
			return 'Today '.date('h:i A', $t);
		elseif (date('Y') == date('Y',$t) && date('z') + 1 == date('z', $t))
			return 'Tomorrow '.date('h:i A', $t);
		else
			return date('m/d h:i A', $t);;
		
	}
	
	function datatime_short($t) {
		if (gettype($t) == 'string')
			$t = strtotime($t);
			
		return date('m/d', $t);;
		
	}
	
	function string_len($str, $len=220) {
		if (strlen($str) > $len )
			$str = substr($str, 0, $len-3).'...';
		return $str;
	}
	
}