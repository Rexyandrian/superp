<?php
    date_default_timezone_set('Asia/Tehran');
    //$pathTmp = explode(DIRECTORY_SEPARATOR,getcwd());
	//die($pathTmp);
    //if($pathTmp[count($pathTmp)]=='main')
        //unset($pathTmp[count($pathTmp)]);
    //$path = implode(DIRECTORY_SEPARATOR,$pathTmp);
    //$path .= DIRECTORY_SEPARATOR;
    $path = '/home/admin/domains/superparvaz.com/public_html/flight/';
    include($path.'class'.DIRECTORY_SEPARATOR.'conf.php');
    include($path.'class'.DIRECTORY_SEPARATOR.'mysql_class.php');
    include($path.'class'.DIRECTORY_SEPARATOR.'audit_class.php');
    include($path.'inc.php');
    include($path.'jdf.php');
    require_once($path.'class'.DIRECTORY_SEPARATOR.'nusoap.php');
$my = new mysql_class;
if (!function_exists('mb_str_replace')) {
    function mb_str_replace($search, $replace, $subject, &$count = 0) {
            if (!is_array($subject)) {
                    // Normalize $search and $replace so they are both arrays of the same length
                    $searches = is_array($search) ? array_values($search) : array($search);
                    $replacements = is_array($replace) ? array_values($replace) : array($replace);
                    $replacements = array_pad($replacements, count($searches), '');
                    foreach ($searches as $key => $search) {
                            $parts = mb_split(preg_quote($search), $subject);
                            $count += count($parts) - 1;
                            $subject = implode($replacements[$key], $parts);
                    }
            } else {
                    // Call mb_str_replace for each subject in array, recursively
                    foreach ($subject as $key => $value) {
                            $subject[$key] = mb_str_replace($search, $replace, $value, $count);
                    }
            }
            return $subject;
    }
}
function removeGran()
{
    $my = new mysql_class;
    $repeat = array();
    $my->ex_sql("SELECT tarikh from parvaz_det group by tarikh order by tarikh ",$q);
    foreach($q as $r)
    {
        $my->ex_sql("SELECT customer_id,flnum,id,zarfiat,ghimat from parvaz_det where tarikh='".$r['tarikh']."' ORDER BY  `parvaz_det`.`flnum` ASC",$p);
        //echo "SELECT customer_id,flnum,id,zarfiat,ghimat from parvaz_det where tarikh='".$r['tarikh']."' ORDER BY  `parvaz_det`.`flnum` ASC <br/>";
        $hs_tmp = '';
        $hs_ghimat=0;
        $hs_id=0;
        $hs_zarfiat=0;
        foreach($p as $t)
        {
            if($hs_tmp!=$t['flnum'])
            {
                $hs_tmp = $t['flnum'];
                $hs_ghimat = $t['ghimat'];
                $hs_id = $t['id'];
                $hs_zarfiat = $t['zarfiat'];
                //echo $hs_tmp."<br/>";
            }
            else
            {
                //echo "customer_id:".$t['customer_id'].' id:'.$t['id'].' flnum:'.$t['flnum']." ghimat:".$t['ghimat']." hs_ghimat:$hs_ghimat zarfiat:".$t['zarfiat']."<br/>";
                if($hs_ghimat<$t['ghimat'])
                {
                    $repeat[]=$t['id'];
                }
                elseif($hs_ghimat==$t['ghimat'])
                {
                    if($hs_zarfiat<$t['zarfiat'])
                    {    
                        //echo "hs_id:".$hs_id.' id:'.$t['id']."<br/>";
                        $repeat[]=$hs_id;
                        $hs_id = $t['id'];
                        $hs_ghimat = $t['ghimat'];
                        $hs_zarfiat = $t['zarfiat'];
                    }
                    else
                    {
                        $repeat[]=$t['id'];
                    }    
                }    
                else
                {
                    $repeat[]=$hs_id;
                    $hs_id = $t['id'];
                    $hs_ghimat = $t['ghimat'];
                    $hs_zarfiat = $t['zarfiat'];
                }    
            }
        }      
    }
    if(count($repeat)>0)
    {    
        $my->ex_sqlx("update parvaz_det set en=0 where id in (".implode(',', $repeat).")");
    }
}

function arabicToPersian($inp)
{
    $out = str_replace("ي","ی",$inp);
    $out = str_replace("ك","ک",$out);
    $out = ($out=='امام خمینی')?'تهران':$out;
    $out = mb_str_replace(array("ـ"," "),"",$out);
    if($out =='خرمآباد' || $out =='خرماباد')
        $out = 'خرم‌آباد';
    return($out);
}
function hs_airline($inp)
{
    $out = str_replace("ي","ی",$inp);
    $out = str_replace("ك","ک",$out);
    $out = str_replace('هواپیمایی','',$out);
    $out = str_replace('شرکت','',$out);
    $out = str_replace("ـ","",$out);
    switch ($out) {
        case 'CASPIAN':
            $out ='کاسپین';
            break;
        case 'ZAGROS':
            $out ='زاگرس';
            break;
    }
    return($out);
}
function loadghimatArr()
{
    $out = array();
    $my = new mysql_class;
    $my->ex_sql("select `moghim_code`, `upghimat` from agency", $q);
    foreach ($q as $r)
    {
        $out[$r['moghim_code']] = $r['upghimat'];
    }
    return($out);
}
function loadActiveAgency()
{
    $out = array();
    $my = new mysql_class;
    $my->ex_sql("select `moghim_code` from agency where en=1", $q);
    foreach ($q as $r)
    {
        $out[] = $r['moghim_code'];
    }
    return($out);
}
$upghimatArr = loadghimatArr();
$activeAgency = loadActiveAgency();
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
$qu = "insert into parvaz_det (`id`, `parvaz_id`, `tarikh`, `saat`, `zarfiat`, `ghimat`, `flnum`, `subflid`, `strsource`, `strdest`, `alname`, `flclass`, `typ`, `saat_kh`, `en`, `customer_id`,selrate,ghimat_origin) values ";
$tmp = '';
$i=0;
$my->ex_sqlx("truncate table parvaz_det");
foreach($tt as $val)
{
    foreach($val as $flight)
    {
        if((string)$flight->reservable=='true' && (string)$flight->reservekind=='1' && in_array((string)$flight->AgencyCode, $activeAgency))
        {    
            $i++;
            $tarikh =hamed_pdateBack((string)$flight->fldate);
            $saat = (string) $flight->outtime;
            $zarfiat =(string) $flight->showcapnet;
            $ghimat = (string) $flight->adlprice1+ (isset($upghimatArr[(string)$flight->AgencyCode])?$upghimatArr[(string)$flight->AgencyCode]:100000);
            $strsource =arabicToPersian((string)$flight->strsource);
            $strdest =arabicToPersian((string)$flight->strdest);
            $alname =hs_airline((string)$flight->alname);
            $tmp.=($tmp==''?'':',') .'('.(string)$flight->ID.",-1,'$tarikh','$saat',$zarfiat,$ghimat,'".(string)$flight->flnum."',".(string)$flight->subflid.",'$strsource','$strdest','$alname','".(string)$flight->flclass."',0,'00:00:00',1,'".(string)$flight->AgencyCode."',".(string)$flight->selrate.",'".(string) $flight->adlprice1."')";
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
removeGran();
//echo "insert into shahr (name) values $city_qu ";
//---------------------------------------------------------------------------------


//echo "ok";