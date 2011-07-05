<?php

class control_index extends l3_controller {

	public function __construct() {
		parent::__construct();
	}
	
	public function index() {
		global $_CLEAN_REQUEST;
		var_dump($_CLEAN_REQUEST);
	}
	
	public function fallback() {
		var_dump("This is a custom defined fallback method.");
	}
}
?>