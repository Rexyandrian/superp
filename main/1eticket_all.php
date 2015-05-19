<?php
	session_start();
	include_once("../kernel.php");
	if ( !( isset($_SESSION[conf::app.'_user_id']) && isset($_SESSION[conf::app.'_typ']) && isset($_REQUEST['sanad_record_id']) )   )
	{
		die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
	}
	$tmp = $_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"];
	$tmp = substr($tmp,0,-15);
	$buffer =array();
	$sanad_record_id =(int) $_REQUEST['sanad_record_id'];
	mysql_class::ex_sql("select `id`,`shomare` from `ticket` where `sanad_record_id`='$sanad_record_id' and `en`=1 group by `shomare` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$curl_handle=curl_init();
			curl_setopt($curl_handle,CURLOPT_URL,"http://$tmp/eticket.php?shomare=".$r['shomare']."&id=".$r['id']);
			curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
			curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
			$buffer[]= curl_exec($curl_handle);
			curl_close($curl_handle);
		}

		foreach($buffer as $key=>$value)
		{
			echo $value;
		}
?>
<script>
	window.print();
</script>
