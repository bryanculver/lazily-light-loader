<?php

class l3 {
	
	var $controller;
	var $db;

	function view($view_name, $view_data) {
		global $data;
		$data = $view_data;
		require_once 'views/'.$view_name.'.php';
	}
	
	function __construct() {
		
		$this->db = new l3_db();
		
		//RETREIVING THE REQUEST
		$_UNPARSED_REQUEST = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);

		//CLEANING THE REQUEST
		if(strlen($_UNPARSED_REQUEST) <= 1) $_UNPARSED_REQUEST = NULL;
		else if(substr($_UNPARSED_REQUEST, 0, 1) == '/') $_UNPARSED_REQUEST = substr($_UNPARSED_REQUEST, 1);

		//PROCESSING THE REQUEST
		if(!empty($_UNPARSED_REQUEST)) $_PARSED_REQUEST = explode('/', $_UNPARSED_REQUEST);
		else $_PARSED_REQUEST = array();
		
		//GRABBING THE RIGHT CONTROLLER
		if(!empty($_PARSED_REQUEST) && file_exists('controllers/'.$_PARSED_REQUEST[0].'.php')) $_CONTROLLER = $_PARSED_REQUEST[0];
		else $_CONTROLLER = 'index';
		$_CONTROLLER_CLASS = 'control_'.$_CONTROLLER;

		//REMOVING CONTROLLER FROM THE REQUEST
		$_CLEAN_REQUEST = $_PARSED_REQUEST;
		if(isset($_CLEAN_REQUEST[0])) unset($_CLEAN_REQUEST[0]);
		if(isset($_CLEAN_REQUEST[1])) unset($_CLEAN_REQUEST[1]);
		$_CLEAN_REQUEST = array_values($_CLEAN_REQUEST);

		//INSTANTIATING THE CONTROLLER
		require_once 'controllers/'.$_CONTROLLER.'.php';
		$this->controller = new $_CONTROLLER_CLASS;
		$this->controller->db = $this->db;
		$this->controller->request = $_CLEAN_REQUEST;
		
		//CALLING THE RIGHT CONTROLLER METHOD
		if(isset($_PARSED_REQUEST[1]) && method_exists($this->controller, $_PARSED_REQUEST[1])) $this->controller->$_PARSED_REQUEST[1]();
		else $this->controller->call_method_fallback();
	}

}

?>
