<?php

function __autoload($class) {
    $class_break = explode("_", $class);
    if($class_break['0'] == CLASS_PREFIX) require_once 'models/'.$class.'.php';
    else require_once $class. '.php';
}

global $l3;
global $l3_controller;

$l3 = new l3();

global $_UNPARSED_REQUEST;
global $_PARSED_REQUEST;
global $_CONTROLLER;
global $_CONTROLLER_CLASS;
global $_CLEAN_REQUEST;

//RETREIVING THE REQUEST
$_UNPARSED_REQUEST = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);

//CLEANING THE REQUEST
if(strlen($_UNPARSED_REQUEST) <= 1) $_UNPARSED_REQUEST = NULL;
else if(substr($_UNPARSED_REQUEST, 0, 1) == '/') $_UNPARSED_REQUEST = substr($_UNPARSED_REQUEST, 1);

//PROCESSING THE REQUEST
if(!empty($_UNPARSED_REQUEST)) $_PARSED_REQUEST = explode('/', $_UNPARSED_REQUEST);

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
$l3_controller = new $_CONTROLLER_CLASS;

//CALLING THE RIGHT CONTROLLER METHOD
if(isset($_PARSED_REQUEST[1]) && method_exists($l3_controller, $_PARSED_REQUEST[1])) $l3_controller->$_PARSED_REQUEST[1]();
else $l3_controller->call_method_fallback();
?>