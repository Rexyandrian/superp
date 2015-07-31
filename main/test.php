<?php
include('../kernel.php');
require_once('../class/nusoap.php');
$cl = new SoapClient("http://91.98.31.190/Moghim24Scripts/Moghim24Services.svc?wsdl");
$param = array(
    'rep'=>&$rep,
    'netlog'=>&$netlog,
    'rwaitlog'=>&$rwaitlog,
    'totalprice'=>&$totalprice,
    'adlprice'=>&$adlprice,
    'chdprice'=>&$chdprice,
    'infprice'=>&$infprice,
    'selrate'=>&$selrate,
    'subflid'=>13941,
    'AgencyCode'=>120,
    'adl'=>1,
    'chd'=>0,
    'inf'=>0,
    'cust'=>'1005',
    'pass'=>'123'
);
try {
    //$res = moghim_class::checkselection(23392, 146, 1, 0, 0);
//$res = $cl->checkselection($param);
//var_dump($res);
//var_dump($res->adlprice);
var_dump($cl->__getFunctions());
var_dump($cl->__getTypes()); 
    //var_dump($res);
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}


