<?php

class control_index extends l3_controller {

	public function __construct() {
		parent::__construct();
	}
	
	public function index() {
		var_dump($this->db);
		var_dump($this->request);
		
		var_dump($this->db->select(NULL, 'email_sig'));
	}
	
	public function fallback() {
		var_dump("This is a custom defined fallback method.");
	}
}
?>