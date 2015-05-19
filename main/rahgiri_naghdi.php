<?php   
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	$msg = '';
	if(isset($_POST['rahgiri']) && trim($_POST['rahgiri'])!='')
	{
		$rahgiri = trim($_POST['rahgiri']);
		$pardakht_id = pardakht_class::barcode($rahgiri);
		$par = new pardakht_class($pardakht_id);
		$msg = '<table ><tr><td style="color:#000000;font-weight:bold;background-color:#ffffff;margin:20px;padding:10px;" >';
		if($par->is_tmp)
			$msg .="رزرو شما انجام نگرفته است در صورت پرداخت وجه ، مبلغ به حساب شما باز گشت داده شده است";
		else
			$msg .= "کد رهگیری  $rahgiri رزرو شما با موفقیت انجام شده است جهت مشاهده بلیت های خود <a href='finalticket2.php?rahgiri=$rahgiri&ticket_type=0&sanad_record_id=".($par->sanad_record_id)."&'>اینجا</a> کلیک کنید";
		$msg .= '</td></tr></table>';
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه مدیریت آژانس مسافرتی - رهگیری رزرو های نقدی
		</title>
		<style>
			body
			{background-color:#023149;background-size:100% 900px;background-repeat:repeat-x;background-image:-moz-linear-gradient(#0594be,#023149 900px);background-image:-webkit-gradient(linear,0 0,0 100%,from(#0594be),to(#023149));background-image:-o-linear-gradient(#0594be,#023149 900px);background-image:-ms-linear-gradient(#0594be,#023149 900px);-cp-background-image:linear-gradient(#0594be,#023149 900px);background-image:linear-gradient(#0594be,#023149 900px);font-family:tahoma,arial,sans-serif;}
			std_textbox
			{width:254px;height:22px;margin:5px 0;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-moz-box-shadow:inset 0 10px 10px#C1E5EF;-webkit-box-shadow:inset 0 10px 10px #C1E5EF;box-shadow:inset 0 10px 10px #C1E5EF;border:2px solid #024257;color:#000;font-family:tahoma;font-size:13px;}
		</style>
	</head>
	<body>
		<div align="center" style="margin:20px;padding:10px;" >
			<form method="POST" >
			<table >
				<tr>
					<td style="color:#fff;">
<b>						کد رهگیری:</b>
					</td>
				</tr>
				<tr>
					<td>
						<input name="rahgiri" id="rahgiri" style="margin:10px 0px 10px 10px;" placeholder="کد رهگیری خود را وارد کنید" class="std_textbox" type="text"  tabindex="1" required >
					</td>
				</tr>
				<tr>
					<td>
						<button style="margin:10px 0px 10px 10px;" name="search" type="submit" class="inp" tabindex="3">جستجو</button>
						<button style="margin:10px 0px 10px 10px;" name="back" type="button" class="inp" tabindex="3" onclick="window.location='login.php';" >بازگشت</button>
					</td>
				</tr>
			</table>
			</form>
			<?php echo $msg; ?>
		</div>
	</body>
</html>
