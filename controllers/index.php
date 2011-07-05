<?php

class control_index extends l3_controller {

	public function __construct() {
		$this->method_fallback = 'fallback'; 
		parent::__construct();
	}
	
	public function index() {
		var_dump($this);
	}
	
	public function fallback() {
		var_dump("This is a custom defined fallback method.");
	}
}
?>