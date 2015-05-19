<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$id = ((isset($_REQUEST["id"]))?(int)$_REQUEST["id"]:-1);
	$ester = ((isset($_REQUEST["ester"]))?(int)$_REQUEST["ester"]:1);
	$ester = (($ester == 0)?1:0);
	mysql_class::ex_sqlx("update `parvaz_det` set `can_esterdad` = $ester where `id` = $id");
?>
<script>window.opener.location.reload(true);window.close();</script>
