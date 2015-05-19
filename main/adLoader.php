<?php
        include_once("../kernel.php");
        $SESSION = new session_class;
        register_shutdown_function('session_write_close');
        session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die('error');
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die('error');
	$adIndx = (isset($_REQUEST['indx']))?(int)$_REQUEST['indx']-1:0;
	$mysql = new mysql_class;
	$icount = 0;
	$mysql->ex_sql("select count(`id`) as `ac` from `admin`",$q);
	if(isset($q[0]))
		$icount = (int)$q[0]['ac'];
	$rindx = ($icount>0)?($adIndx % $icount):0;
	$out = '----';
	$q = null;
	$mysql->ex_sql("select * from `admin`",$q);
	if(isset($q[$rindx]) > 0)
		$out = $q[$rindx]['content'];
	echo $out;
?>

