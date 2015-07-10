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
$today =substr(perToEnNums(jdate('Y/m/d',strtotime(date("Y-m-d H:i:s")))),2);// 
$onem =substr(perToEnNums(jdate('Y/m/d',strtotime(date("Y-m-d H:i:s",time()+ 30*24*60*60)))),2);// date("Y-m-d H:i:s",time()+ 30*24*60*60);
$cl = new SoapClient("http://91.98.31.190/Moghim24Scripts/Moghim24Services.svc?wsdl");
$param = array(
    'fd'=>$today,
    'ld'=>$onem,//'94/05/21',
    'cust'=>'1005',
    'pass'=>'123'
);
$res = $cl->openTempfllist($param);
$pattern = '/<xs:schema.*<\/xs:schema>/';
$xml = preg_replace($pattern, '', $res->openTempfllistResult->any);

$response = simplexml_load_string($xml);
$tt = $response->NewDataSet;
$qu = "insert into parvaz_det (`id`, `parvaz_id`, `tarikh`, `saat`, `zarfiat`, `ghimat`, `flnum`, `subflid`, `strsource`, `strdest`, `alname`, `flclass`, `typ`, `saat_kh`, `en`, `customer_id`,selrate) values ";
$tmp = '';
$i=0;
$my = new mysql_class;
$my->ex_sqlx("truncate table parvaz_det");
foreach($tt as $val)
{
    foreach($val as $flight)
    {
        if((string)$flight->reservable=='true' && (string)$flight->reservekind=='1')
        {    
            $i++;
            $tarikh =hamed_pdateBack((string)$flight->fldate);
            $saat = (string) $flight->outtime;
            $zarfiat =(string) $flight->showcapnet;
            $ghimat = (string) $flight->adlprice1;
            $tmp.=($tmp==''?'':',') .'('.(string)$flight->ID.",-1,'$tarikh','$saat',$zarfiat,$ghimat,'".(string)$flight->flnum."',".(string)$flight->subflid.",'".(string)$flight->strsource."','".(string)$flight->strdest."','".(string)$flight->alname."','".(string)$flight->flclass."',0,'00:00:00',1,'".(string)$flight->AgencyCode."',".(string)$flight->selrate.')';
            
            if($i%500==0)
            {
                $my->ex_sqlx($qu.$tmp);
                $tmp='';
            }    
        }
    }    
}
$my->ex_sqlx($qu.$tmp);
echo "ok";