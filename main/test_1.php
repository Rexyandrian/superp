<?php
include('../kernel.php');
//$reserve_tmp = new reserve_tmp_class();
//$res = moghim_class::reservefl($reserve_tmp);
$my = new mysql_class;
$q = null;
$flnums = '-1';
$tmp=array();
$my->ex_sql("SELECT tarikh from parvaz_det group by tarikh",$q);
$my->ex_sql("SELECT tarikh,customer_id,flnum,id from parvaz_det ",$p);
foreach($q as $r)
{
    foreach($p as $t)
    {
        if($r['tarikh']==$t['tarikh'])
        {
            $tmp[$r['tarikh']][]=array($t['customer_id'],$t['flnum'],$t['id']);
        }
    }    
}
var_dump($tmp);
