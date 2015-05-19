<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function hamed_pdate($str)
        {
                $out=jdate('d / m / Y H:i:s',strtotime($str));
		$out .= "<br/>".date('F d',strtotime($str));
                return enToPerNums($out);
        }
	$parvaz_det_id = ((isset($_REQUEST['parvaz_det_id']))?(int)$_REQUEST["parvaz_det_id"]:-1);
	$tozihat = ((isset($_REQUEST['tozihat']))?$_REQUEST["tozihat"]:'');
	if(isset($_REQUEST['tozihat']))
	{
		mysql_class::ex_sqlx("insert into `parvaz_tozihat` (`id`,`parvaz_det_id`,`tozihat`) values (null,$parvaz_det_id,'$tozihat')");
		mysql_class::ex_sqlx("update `parvaz_tozihat` set `tozihat` = '$tozihat' where `parvaz_det_id` = $parvaz_det_id");
		echo "<script> window.close();</script>";
	}
	mysql_class::ex_sql("select * from `parvaz_tozihat` where `parvaz_det_id` = $parvaz_det_id",$q);
	if($r = mysql_fetch_array($q))
		$tozihat = $r['tozihat'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه مدیریت آژانس مسافرتی
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
				<form>
					<input type="text" name="tozihat" value ="<?php echo $tozihat; ?>" />
					<input type="hidden" name="parvaz_det_id" value="<?php echo $parvaz_det_id; ?>" />
					<input type="submit" value="ثبت" />
				</form>
			<br/>
		</div>
	</body>
</html>
