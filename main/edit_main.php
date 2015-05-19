<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if(isset($_REQUEST['main_txt']))
	{
		//$file = file('main.txt');
		$fp=fopen('main.txt', 'w+');
		$line = $_REQUEST['main_txt'];
		$line = str_replace("\n","<br/>",$line);
		fwrite($fp,$line); 
	        fclose($fp);
	}
	$filename = 'main.txt';
	$fp=fopen($filename, 'r');
	$content = fread($fp, filesize($filename));
	$content = str_replace("<br/>","\n",$content);
        fclose($fp);
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
			<br />
			<h2>
			ورود اطلاعات اطلاعیه عمومی صفحه اول
			</h2>
			<br/>
			<form>
				<textarea style="direction:rtl;font-family:tahoma,Tahoma;font-size:12px;" name="main_txt" id="main_txt" rows="25" cols="100" ><?php  echo $content; ?></textarea>
			<br />
			<input type="submit" value="ذخیره تغییرات" class="inp1" >
			</form>
		</div>
	</body>
</html>
