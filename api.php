<?php
require_once(__DIR__.'/config/config.php');
require_once(__DIR__.'/config/autoloader.php');


$ajaxRequest = $_POST['getAjaxData'];
$api = new API_REQUEST($ajaxRequest);
$api->invoke_api_method()->show_api_response();
