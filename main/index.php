<?php
        include_once("../kernel.php");
        $SESSION = new session_class;
        register_shutdown_function('session_write_close');
        session_start();
	$pass=((isset($_REQUEST['pass']))?$_REQUEST['pass']:"");
	$user=((isset($_REQUEST['user']))?$_REQUEST['user']:"");
	$kelid = ((isset($_REQUEST['kelid']))?$_REQUEST['kelid']:-1);
	if(!security_class::firstVisit($_SESSION,$_REQUEST))
		die("<script>window.location = 'login.php?stat=wrong_userk1&';</script>");
	if(isset($_SESSION[$conf->app.'_user_id']))
		$se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
			خرید بلیط هواپیما | خرید بلیط اینترنتی هواپیما |  بلیط هواپیما ارزان | <?php echo $conf->title; ?>
		</title>
		<meta name="keywords" content="بلیط هواپیما,قیمت بلیط هواپیما,خرید بلیط هواپیما,رزرو بلیط هواپیما,خرید اینترنتی بلیط هواپیما,کیش آنلاین,بليط هواپيما,بلیت هواپیما,فروش بلیط هواپیما,بلیط کیش,رزرو بلیط,بلیط الکترونیکی,ایتیکت,بلیط مشهد, هواپیمایی , هواپیمایی ماهان , هواپیما, بلیط" />
		<meta name="description" content="خرید اینترنتی بلیط هواپیما ارزان" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--
	<link href="../css/xgrid.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" media="all" href="../js/cal/skins/aqua/theme.css" title="Aqua" />
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="../js/grid.js"></script>
	<script type="text/javascript" src="../js/date.js"></script>
        <script type="text/javascript" src="../js/cal/jalali.js"></script>
        <script type="text/javascript" src="../js/cal/calendar.js"></script>
        <script type="text/javascript" src="../js/cal/calendar-setup.js"></script>
        <script type="text/javascript" src="../js/cal/lang/calendar-fa.js"></script>
-->
		<script type="text/javascript" src="../main/index.js.php"></script>
                <script src="../js/jquery-1.8.3.min.js"></script>
                <script src="../js/select2.min.js"></script>
                <script src="../js/bootstrap-select.min.js"></script>
                
                <script type="text/javascript" src="../js/date.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
                <script src="../js/grid.js"></script>
		<script src="../js/stickyfloat.min.js"></script>
                <script src="../js/ui/jquery-ui.js"></script>
                <script type="text/javascript" src="../js/cal/jalali.js"></script>
                <script type="text/javascript" src="../js/cal/calendar.js"></script>
                <script type="text/javascript" src="../js/cal/calendar-setup.js"></script>
                <script type="text/javascript" src="../js/cal/lang/calendar-fa.js"></script>
                <link rel="stylesheet" href="../css/bootstrap.min.css" >
                <link rel="stylesheet" href="../css/bootstrap-rtl.min.css" >
                    <link rel="stylesheet" href="../css/select2.css" >
                <link rel="stylesheet" type="text/css" media="all" href="../js/cal/skins/aqua/theme.css" title="Aqua" />
                <link rel="stylesheet" href="../css/jquery-ui.css">
                <link rel="stylesheet" href="../css/style.css">
                <link rel="stylesheet" href="../css/jquery.tooltip.css" type="text/css">
                <link rel="stylesheet" href="../css/xgrid.css" type="text/css">
		<link rel="stylesheet" media="screen" type="text/css" href="../css/colorpicker.css" />
		<script type="text/javascript" src="../js/colorpicker.js"></script>
		<script type="text/javascript" >
			$(document).ready(function(){
				init();
			});
		</script>
	</head>
	<body dir="rtl">
		<div id="gcom" style="padding-right:4%;">
			<div style="color:#ffffff;">
				<?php //echo $conf->gcom_link; ?>
				<br/>
				<?php echo $conf->kharidar_link; ?>
			</div>
		</div>
		<div align="center">
			<div id="header">
			</div>
			<center>
			<div id="grandMain" >
				<div class="menuDiv" align="right" >
				</div>
				<div class="adHolder" >
					<div class="ad">
					</div>
				</div>
				<div class="body" id="body" >
				</div>    
				      
			</div>
			</center>
			<div id="footer" >
			</div>
			<div class="log" style="display:none;">
			</div>
			<div id="dialog"></div>
		</div>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38582824-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>


	</body>
</html>
