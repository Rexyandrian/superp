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
}
