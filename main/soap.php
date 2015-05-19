<?php
//call library
include('../kernel.php');
require_once('../class/nusoap.php');
$URL       = "192.168.0.77";
$namespace = $URL . '?wsdl';
//using soap_server to create server object
$server    = new soap_server;
$server->configureWSDL('hellotesting', $namespace);

//register a function that works on server
$server->register('hello');

// create the function
function hello($name)
{
/*
    if (!$name) {
        return new soap_fault('Client', '', 'Put your name!');
    }
*/
    $result = "Hello, ";
    return $result;
}
// create HTTP listener
$server->service(isset($HTTP_RAW_POST_DATA)?$HTTP_RAW_POST_DATA:'');
exit();
?>
