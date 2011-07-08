<?php

function __autoload($class) {
    $class_break = explode("_", $class);
    if($class_break['0'] == CLASS_PREFIX) require_once 'models/'.$class.'.php';
    else require_once $class. '.php';
}

global $l3;

$l3 = new l3();


?>