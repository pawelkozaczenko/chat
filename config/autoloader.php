<?php

/********Class Autoloader options********/

function __autoload($Class_Or_Interface_Name) {
    require_once __DIR__.'/../lib/'.$Class_Or_Interface_Name.'.php';
}

/*****************************************/
