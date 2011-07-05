<?php

class l3 {

	function view($view_name, $view_data) {
		global $data;
		$data = $view_data;
		require_once 'views/'.$view_name.'.php';
	}

}

?>
