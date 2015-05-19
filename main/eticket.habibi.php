<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
        require_once( '../barcode/BCGFontFile.php');
        require_once( '../barcode/BCGColor.php');
        require_once( '../barcode/BCGDrawing.php');
        require_once('../barcode/BCGcode39.barcode.php');
	$user_id = (int) $_SESSION[conf::app."_user_id"];
	$ticket_ids = array();
	$out = '';
	if(isset($_REQUEST['shomare']) && isset($_REQUEST['id']) )
	{
		mysql_class::ex_sql('select `id` from `ticket` where `shomare`='.(int)$_REQUEST["shomare"],$qs);
		while($r = mysql_fetch_array($qs))
			$ticket_ids[] = (int)$r['id'];
	}
	for($i=0;$i<count($ticket_ids);$i++)
	{
		$et = new eticket_class($ticket_ids[$i]);
		if($i>0)
			$et->route ='ROUTE 2<br/>'.'مسیر۲';
		$et->user_id = $user_id;
		$et->customer_logo_text = conf::app_fa;
		$out .= $et->get().'---------------------------------------------------------------------------------<br />';
	}
	$et = new eticket_class($ticket_ids[0]);
	$et->isCopon = TRUE;
	$et->user_id = $user_id;
	$et->customer_logo_text = conf::app_fa;
	$et->route = 'Passengar Coupon <br />کوپن مسافر';
	$out .= $et->get();
?>
	<!--<img src='../img/barcodes/123.png' />-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link type="text/css" href="css/style.css" rel="stylesheet" />
		<link type="text/css" href="css/hamed.css" rel="stylesheet" />
		<link type="text/css" href="css/mehrdad.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" media="all" href="css/skins/aqua/theme.css" title="Aqua" />
	<title>
		چاپ بلیت الکترونیک
	</title>
	<style>
		td {text-align:center;border-width:1px;border-spacing:0px;}
	</style>
	</head>
	<body style="background-image: none;background-color: white;direction:ltr;font-family:tahoma,Tahoma;font-size:10px;" >
		<div align="center" style='width:21cm;height:20.5cm;'>
			<?php echo $out.conf::eticket_footer; ?>
		</div>
	</body>
</html>

