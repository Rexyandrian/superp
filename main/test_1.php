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
    'subflid'=>5923,
    'AgencyCode'=>126,
    'adl'=>1,
    'chd'=>0,
    'inf'=>0,
    'cust'=>'1005',
    'pass'=>'123'
);
$res = moghim_class::Flightlastdata(14594, 120);
//$pattern = '/<xs:schema.*<\/xs:schema>/';
//$xml = preg_replace($pattern, '', $res->FlightlastdataResult->any);

//$response = simplexml_load_string($xml);
//$res = $cl->checkselection($param);
var_dump($res);
//var_dump($res->adlprice);
//var_dump($cl->__getFunctions());
//var_dump($response->NewDataSet->publicselectsp);
