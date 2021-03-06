<?php
/**
 * Description of moghim_class
 *
 * @author hamed
 */
class moghim_class {
    public static function checkselection($subflid,$customer_id,$adl,$chd,$inf)
    {
        $conf = new conf;
        $cl = new SoapClient($conf->moghim_wsdl);
        $param = array(
            'rep'=>&$rep,
            'netlog'=>&$netlog,
            'rwaitlog'=>&$rwaitlog,
            'totalprice'=>&$totalprice,
            'adlprice'=>&$adlprice,
            'chdprice'=>&$chdprice,
            'infprice'=>&$infprice,
            'selrate'=>&$selrate,
            'subflid'=>$subflid,
            'AgencyCode'=>$customer_id,
            'adl'=>$adl,
            'chd'=>$chd,
            'inf'=>$inf,
            'cust'=>$conf->moghim_cust,
            'pass'=>$conf->moghim_pass
        );
        return($cl->checkselection($param));
    }
/*
    public static function checkselection2($subflid,$customer_id,$adl,$chd,$inf)
    {
        $conf = new conf;
        $cl = new SoapClient($conf->moghim_wsdl);
        $param = array(
            'rep'=>&$rep,
            'netlog'=>&$netlog,
            'rwaitlog'=>&$rwaitlog,
            'totalprice'=>&$totalprice,
            'adlprice'=>&$adlprice,
            'chdprice'=>&$chdprice,
            'infprice'=>&$infprice,
            'selrate'=>&$selrate,
            'subflid'=>$subflid,
            'AgencyCode'=>$customer_id,
            'adl'=>$adl,
            'chd'=>$chd,
            'inf'=>$inf,
            'cust'=>$conf->moghim_cust,
            'pass'=>$conf->moghim_pass
        );
        $out = new stdClass();
        $out->checkselectionResult = TRUE;
        $out->netlog = '222500';
        $out->rwaitlog = '122443';
        $out->adlprice = '500';
        $out->chdprice = '400';
        $out->infprice = '100';
        return($out);
    }
*/
    public static function Flightlastdata($subflid,$customer_id)
    {
        $conf = new conf;
        $cl = new SoapClient($conf->moghim_wsdl);
        $param = array(
            'subflid'=>$subflid,
            'AgencyCode'=>$customer_id,
            'cust'=>$conf->moghim_cust,
            'pass'=>$conf->moghim_pass
        );
        $res = $cl->Flightlastdata($param);
        $pattern = '/<xs:schema.*<\/xs:schema>/';
        $xml = preg_replace($pattern, '', $res->FlightlastdataResult->any);
        $response = simplexml_load_string($xml);
        return($response->NewDataSet->publicselectsp);
    }
/*
    public static function reservefl1($reserve_tmp)
    {
        $conf = new conf;
        $moghim_out = moghim_class::loadReserveParam($reserve_tmp);
        $cl = new SoapClient($conf->moghim_wsdl);
        $param = array(
            'rep'=>&$rep,
            'refer'=>&$refer,
            'seldate'=>&$seldate,
            'netlog'=>$reserve_tmp->netlog,
            'rwaitlog'=>$reserve_tmp->rwaitlog,
            'sexkind'=>$moghim_out['sexkind'],
            'fname'=>$moghim_out['fname'],
            'lname'=>$moghim_out['lname'],
            'passkind'=>$moghim_out['passkind'],
            'mellicode'=>$moghim_out['mellicode'],
            'passport'=>$moghim_out['passport'],
            'remark'=>'',
            'mobile'=>$moghim_out['mobile'],
            'cust'=>$conf->moghim_cust,
            'pass'=>$conf->moghim_pass
        );
        return($cl->reservefl($param));
    }
*/
    public static function reservefl($pardakht)
    {
        $conf = new conf;
        $tt = json_decode($pardakht->log_text);
        $moghim_out = moghim_class::loadReserveParam($tt);
        $cl = new SoapClient($conf->moghim_wsdl);
        $param = array(
            'rep'=>&$rep,
            'refer'=>&$refer,
            'seldate'=>&$seldate,
            'netlog'=>$tt->netlog,
            'rwaitlog'=>$tt->rwaitlog,
            'sexkind'=>$moghim_out['sexkind'],
            'fname'=>$moghim_out['fname'],
            'lname'=>$moghim_out['lname'],
            'passkind'=>$moghim_out['passkind'],
            'mellicode'=>$moghim_out['mellicode'],
            'passport'=>$moghim_out['passport'],
            'remark'=>'',
            'mobile'=>'09125245831',
            'cust'=>$conf->moghim_cust,
            'pass'=>$conf->moghim_pass
        );
/*
        $out = new stdClass();
        $out->reserveflResult = TRUE;
        $out->refer = 'PDYPIQ';
        $out->seldate = '94/05/27';
        $out->rep = '';
        return($out);
*/
        return($cl->reservefl($param));
    }
    public static function loadReserveParam($info)
    {
        //$info = json_decode($inp);
        $fnames='';
        $lnames='';
        $sexkind='';
        $passkind='';
        $mellicode='';
        $passport ='';
        $mobile ='';
        $i=0;
        foreach($info->ticket as $r)
        {

            $fnames.= (($fnames=='')?'':'|').$r->fname;
            $lnames.= (($lnames=='')?'':'|').$r->lname;
            $sexkind.= (($sexkind=='')?'':'|'). ((int)$r->gender==1?'MR':'MRS');
            switch ($r->adult) {
                case 0:
                    $tmp_passkind = 'ADL';
                    break;
                case 1:
                    $tmp_passkind = 'CHD';
                    break;
                case 2:
                    $tmp_passkind = 'INF';
                    break;
                default:
                    break;
            }
            $passkind.= (($passkind=='')?'':'|').$tmp_passkind;
            $mellicode.= (strlen($r->code_melli)==10?$r->code_melli:'').'|';
            $passport.= (strlen($r->code_melli)!=10?$r->code_melli:'').'|';
            
            if($i==0)
            {    
                $mobile =$r->tel;
            }    
            $i++;
        }
        $mellicode = substr($mellicode, 0, -1);
        $passport = substr($passport, 0, -1);
        $out = array('fname'=>$fnames,'lname'=>$lnames,'sexkind'=>$sexkind,'passkind'=>$passkind,'mellicode'=>$mellicode,'passport'=>$passport,'mobile'=>$mobile);
        return($out);
    }
    public static function printEticket($rwaitlog)
    {
        $conf = new conf;
        $cl = new SoapClient($conf->moghim_wsdl);
        $param = array(
            'rwaitlog'=>$rwaitlog,
            'cust'=>$conf->moghim_cust,
            'pass'=>$conf->moghim_pass
        );
        return($cl->printEticket($param));
    }        
}