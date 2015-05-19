<?php
include('../kernel.php');
require_once('../class/nusoap.php');
/*
$client = new soapclient_nu("http://91.98.31.190/Moghim24Scripts/Moghim24Services.svc?wsdl");
$param = array(
    'fd'=>'94/04/15',
    'ld'=>'94/05/10',
    'cust'=>'1005',
    'pass'=>'123'
);
$res = $client->call("openTempfllist",$param);
var_dump($res);
 * 
 */

$cl = new SoapClient("http://91.98.31.190/Moghim24Scripts/Moghim24Services.svc?wsdl");
$param = array(
    'fd'=>'94/04/15',
    'ld'=>'94/05/10',
    'cust'=>'1005',
    'pass'=>'123'
);
$res = $cl->openTempfllist($param);
$pattern = '/<xs:schema.*<\/xs:schema>/';
$xml = preg_replace($pattern, '', $res->openTempfllistResult->any);

$response = simplexml_load_string($xml);
var_dump($response);