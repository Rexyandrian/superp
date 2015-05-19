<?php   
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
       	if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
	require_once( '../barcode/BCGFontFile.php');
        require_once( '../barcode/BCGColor.php');
        require_once( '../barcode/BCGDrawing.php');
        require_once('../barcode/BCGcode39.barcode.php');
	if ( !( isset($_REQUEST['sanad_record_id']) )   )
		die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
	$user_id = (int)$_SESSION[$conf->app.'_user_id'];
/*
	$tmp = $_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"];
	$tmp = substr($tmp,0,-15);
*/
	$out = '';
	$sanad_record_id =(int) $_REQUEST['sanad_record_id'];
	$mysql = new mysql_class;	
	$mysql->ex_sql("select `id`,`shomare` from `ticket` where `sanad_record_id`='$sanad_record_id' and `en`=1 group by `shomare` ",$q);
	foreach($q as $rr)
	{
		$mysql->ex_sql('select `id` from `ticket` where `shomare`='.(int)$rr['shomare'],$qs);
		$ticket_ids = array();
		foreach($qs as $r)
			$ticket_ids[] = (int)$r['id'];
		$out .= "<div align='center' style='width:21cm;height:31cm;' >";
		for($i=0;$i<count($ticket_ids);$i++)
		{
			$et = new eticket_class($ticket_ids[$i]);
			if($i>0)
				$et->route ='ROUTE 2<br/>'.'مسیر۲';
			$et->user_id = $user_id;
			$et->customer_logo_text = $conf->app_fa;
			$out .= $et->get().'---------------------------------------------------------------------------------<br />';
		}
		$et = new eticket_class($ticket_ids[0]);
		$et->isAdmin = $se->detailAuth('all');
		$et->isCopon = TRUE;
		$et->user_id = $user_id;
		$et->customer_logo_text = $conf->app_fa;
		$et->route = 'Passengar Coupon <br />کوپن مسافر';
		$out .= $et->get().$conf->eticket_footer;
		$out .= '</div>';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link type="text/css" href="css/style.css" rel="stylesheet" />
		<link type="text/css" href="css/hamed.css" rel="stylesheet" />
		<link type="text/css" href="css/mehrdad.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" media="all" href="css/skins/aqua/theme.css" title="Aqua" />
		<style>
			td {text-align:center;border-width:1px;border-spacing:0px;}
		</style>
		<title>
		چاپ بلیط الکترونیکی
		</title>
	</head>
	<body style="background-image: none;background-color: white;direction:ltr;font-family:tahoma,Tahoma;font-size:10px;" >
<?php echo $out; ?>
	</body>
</html>
