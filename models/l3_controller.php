<?php

class l3_controller {
	
	protected $method_fallback = 'index';
	
	public function __construct()
	{
		
	}
	
	public function method_fallback() { return $this->method_fallback; }
	
	public function call_method_fallback() { $method = $this->method_fallback(); return $this->$method(); }
	
	public function index() {
		echo "This controller's index method hasn't been defined yet.";
	}
}

?>