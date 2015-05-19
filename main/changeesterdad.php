<?php   
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
     /*   if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = $conf->auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);*/
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = $conf->auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
	$id = ((isset($_REQUEST["id"]))?(int)$_REQUEST["id"]:-1);
	$ester = ((isset($_REQUEST["ester"]))?(int)$_REQUEST["ester"]:1);
	$ester = (($ester == 0)?1:0);
	$mysql = new mysql_class;
	$mysql->ex_sqlx("update `parvaz_det` set `can_esterdad` = $ester where `id` = $id");
?>
<script>window.opener.location.reload(true);window.close();</script>
