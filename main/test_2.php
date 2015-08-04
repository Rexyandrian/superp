<?php
include('../kernel.php');
$res = moghim_class::printEticket(112705);
var_dump($res);
/*
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
                        echo "hs_id:".$hs_id.' id:'.$t['id']."<br/>";
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
removeGran();
 * 
 */