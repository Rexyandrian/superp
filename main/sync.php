<?php
	date_default_timezone_set('Asia/Tehran');
	$pathTmp = explode(DIRECTORY_SEPARATOR,getcwd());
	if($pathTmp[count($pathTmp)-1]=='main')
		unset($pathTmp[count($pathTmp)-1]);
	$path = implode(DIRECTORY_SEPARATOR,$pathTmp);
	$path .= DIRECTORY_SEPARATOR;
	include($path.'class'.DIRECTORY_SEPARATOR.'conf.php');
	include($path.'class'.DIRECTORY_SEPARATOR.'mysql_class.php');
	include($path.'class'.DIRECTORY_SEPARATOR.'audit_class.php');
	include($path.'class'.DIRECTORY_SEPARATOR.'c_class.php');
	include($path.'class'.DIRECTORY_SEPARATOR.'shahr_class.php');
	include($path.'class'.DIRECTORY_SEPARATOR.'parvaz_class.php');
	include($path.'class'.DIRECTORY_SEPARATOR.'parvaz_det_tmp_class.php');
	include($path.'class'.DIRECTORY_SEPARATOR.'sites_class.php');
	include($path.'class'.DIRECTORY_SEPARATOR.'havapeima_class.php');
	include($path.'jdf.php');
//-----------------------------------------------------------------------------
	$forbidden_colors = array(
	        7=>array('#CCFFCC')
        );
	$sites = sites_class::load();
	foreach($sites as $i=>$site)
	{
		$c = new c_class($site['url']);
		$c->forbidden_colors = isset($forbidden_colors[(int)$site['id']])?$forbidden_colors[(int)$site['id']]:array();
		//echo "data from '".$site['url']."'\n";
		$inp=$c->findFlights((int)$site['is_old']>0?2:0);
		//var_dump($inp);
		//var_dump($inp);
		parvaz_det_tmp_class::add($inp,$site['id'],$site['ghimat']);
	}
/*
	$inp=array();
	$inp[] = array('date'=>"2014-5-19","saat"=>"23:59","tedad"=>"9","ghimat"=>1200000,"karmozd"=>"0","parvaz"=>28,"tozihat"=>"");
	$inp[] = array('date'=>"2014-5-18","saat"=>"15:30","tedad"=>"4","ghimat"=>900000,"karmozd"=>"3","parvaz"=>30,"tozihat"=>"ندارد");
	parvaz_det_tmp_class::add($inp,2,0);
	$inp=array();
	$inp[] = array('date'=>"2014-5-18","saat"=>"15:10","tedad"=>"6","ghimat"=>900000,"karmozd"=>"2","parvaz"=>30,"tozihat"=>"ﻥﺩﺍﺭﺩ");
	$inp[] = array('date'=>"2014-5-18","saat"=>"15:10","tedad"=>"6","ghimat"=>900000,"karmozd"=>"2","parvaz"=>31,"tozihat"=>"ﻥﺩﺍﺭﺩ");
	parvaz_det_tmp_class::add($inp,1,0);	
*/
?>
