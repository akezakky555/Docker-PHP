<?php
	function replace_category($find, $replace, $array) {
		$arr = array();
		foreach ($array as $key => $value) {
			if ($key == $find) {
				$arr[$key] = $replace[$value];
			} else {
				$arr[$key] = $value;
			}
		}
		return $arr;
	}

	function replace_address_en($find, $array) {
		$arr = array();
		foreach ($array as $key => $value) {
			if ($key == $find) {
				if ($array[$key] == null){
					echo $array[$key];
					$arr[$key] = $array['tambon_e'].','.$array['amphoe_e'].','.$array['province_e'];
				} else {
					$arr[$key] = $array[$key];
				}
			} else {
				$arr[$key] = $value;
			}
		}
		return $arr;
	}


	function replace_address_th($find, $array) {
		$arr = array();
		foreach ($array as $key => $value) {
			if ($key == $find) {
				if ($array[$key] == null){
					$arr[$key] = $array['tambon_t'].','.$array['amphoe_t'].','.$array['province_t'];
				} else {
					$arr[$key] = $array[$key];
				}
			} else {
				$arr[$key] = $value;
			}
		}
		return $arr;
	}
?>
