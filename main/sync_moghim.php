<?php
include('../kernel.php');
require_once('../class/nusoap.php');
$my = new mysql_class;


function arabicToPersian($inp)
{
    $out = str_replace("ي","ی",$inp);
    $out = str_replace("ك","ک",$out);
    $out = ($out=='امام خمینی')?'تهران':$out;
    $out = str_replace(array(" ","_"),"",$out);
    if($out =='خرمآباد' || $out =='خرماباد')
        $out = 'خرم‌آباد';
    return($out);
 }
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
            $strsource =arabicToPersian((string)$flight->strsource);
            $strdest =arabicToPersian((string)$flight->strdest);
            $alname =str_replace(array("_"),"",(string)$flight->alname);
            $tmp.=($tmp==''?'':',') .'('.(string)$flight->ID.",-1,'$tarikh','$saat',$zarfiat,$ghimat,'".(string)$flight->flnum."',".(string)$flight->subflid.",'$strsource','$strdest','$alname','".(string)$flight->flclass."',0,'00:00:00',1,'".(string)$flight->AgencyCode."',".(string)$flight->selrate.')';
            if($i%500==0)
            {
                $my->ex_sqlx($qu.$tmp);
                $tmp='';
            }    
        }
    }    
}
$my->ex_sqlx($qu.$tmp);

$my->ex_sql("select strdest from parvaz_det group by strdest", $q);
$city = array();
foreach($q as $r)
{
    $city[] = $r['strdest'];
}    
$my->ex_sql("select strsource from parvaz_det group by strsource", $p);
foreach($p as $r)
{
    if(!in_array($r['strsource'], $city))
    {        
        $city[] = $r['strsource'];
    }    
}
$city_qu='';
for($i=0;$i<count($city);$i++)
{
    $city_qu .= ($city_qu==''?'':',') ."('".$city[$i]."')";
}
$my->ex_sqlx("truncate table shahr");
$my->ex_sqlx("insert into shahr (name) values $city_qu ");
//echo "insert into shahr (name) values $city_qu ";
echo "ok";