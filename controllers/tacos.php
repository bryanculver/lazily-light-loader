<?php

class control_tacos extends l3_controller {
	
	function __construct() {
		parent::__construct();
		
		$this->method_fallback = 'meaty';
	}
	
	function meaty() {
		echo "We offer meaty tacos!";
	}
	
	function cheese() {
		echo "We also offer cheesy tacos!";
	}
	
	function spicy() {
		global $l3;
		
		$promotions = array(
			2 => 1,
			5 => 3,
			10 => 6,
		);
		
		$l3->view('tacos-spicy', array('promotions' => $promotions)); 
	}
}

?>