<?php   
	include_once('../kernel.php');
	$tmp_files = scandir('../web_service');
	$modules = array();
	$functions = array();
	$functions_def = array();
	foreach($tmp_files as $fn)
		if(strpos($fn,'.')!==0 && $fn!='')
		{
			$modules[] = $fn;
			$tmp = explode('.',$fn);
			$functions[] = $tmp[0];
			$functions_def[] = $tmp[0].'_def';
		}
	require_once('../class/nusoap.php');
	foreach($modules as $module)
		require_once('../web_service/'.$module);
	$server = new soap_server();
	$server->configureWSDL('test_wsdl', 'urn:test_wsdl');
	$server->soap_defencoding = 'UTF-8';
	foreach($functions as $i=>$function)
	{
		$pars = $functions_def[$i]();
		$server->register($function,$pars[0],$pars[1],$pars[2],$pars[3],$pars[4],$pars[5],$pars[6]);
	}
	$request = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
	$server->service($request);
?>
