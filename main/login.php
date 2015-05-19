<?php
	include_once('../kernel.php');
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	session_destroy();
        $SESSION = new session_class;	
	register_shutdown_function('session_write_close');
	session_start();
	$_SESSION[$conf->app."_login"] = "1";
	$content = '';
?>
<html>
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	        <title> 
			<?php echo lang_fa_class::title; ?>
	        </title>
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
		<script src="../js/jquery.js"></script>
		<script src="../js/jquery.corner.js"></script>

	<!--	<link type="text/css" href="../css/style.css" rel="stylesheet" />	-->
		<style>
			body
			{background-color:#1c2e43;background-size:100% 900px;background-repeat:repeat-x;background-image:-moz-linear-gradient(#325376,#1c2e43 900px);background-image:-webkit-gradient(linear,0 0,0 100%,from(#325376),to(#1c2e43));background-image:-o-linear-gradient(#325376,#1c2e43 900px);background-image:-ms-linear-gradient(#325376,#1c2e43 900px);-cp-background-image:linear-gradient(#325376,#1c2e43 900px);background-image:linear-gradient(#325376,#1c2e43 900px);font-family:tahoma,arial,sans-serif;direction:rtl;}
			.textbox
			{width:254px;height:22px;margin:5px 0;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;-moz-box-shadow:inset 0 10px 10px#C1E5EF;-webkit-box-shadow:inset 0 10px 10px #C1E5EF;box-shadow:inset 0 10px 10px #C1E5EF;border:2px solid #024257;color:#000;font-family:tahoma;font-size:13px;height:30px;}
			.login_but
			{
				color:#fff;
				background: #02526f url("../img/button_bg.png");
				font-family:tahoma;
				width:100px;
				font-weight:bold;
				border: 1px solid #333;
				height: 25px;
				font-size:11px;
				cursor:pointer;
			}
			#login_tb
			{
				font-weight:bold;
				font-family:tahoma;
				font-size:12px;
				color:#000000;
			}
			#all_div
			{
				width:360px;
				height:330px;
				border: 1px solid #62bad7;
				margin-top:70px;
				margin-left:auto;
				margin-right:auto;
				-webkit-border-radius: 5px;
				-moz-border-radius: 5px;
				-o-border-radius: 5px;
				-ms-border-radius: 5px;
				border-radius: 5px;
			}
			#logo_div
			{
				background:#cccccc;
				margin:3px;
				width:350px;
				height:75px;
				border: 1px solid #333;
			}
			#login_div
			{
				background:url("../img/light.png");
				margin:3px;
				width:350px;
				height:240px;
				border: 1px solid #999;
			}
			#login_tb
			{
				color:#fff;
				margin-top: 15px;
				height:200px;
			}
			#login_tb td
			{
				text-align:right;
			}
			.shadow
			{
			    -moz-box-shadow: 3px 3px 4px #999;
			    -webkit-box-shadow: 3px 3px 4px #999;
			    box-shadow: 3px 3px 4px #999; /* For IE 8 */
			    -ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#999999')"; /* For IE 5.5 - 7 */
			    filter: progid:DXImageTransform.Microsoft.Shadow(Strength = 4, Direction = 135, Color = '#999999');
			}
			a:link {color:#f3ea59;}
			a:visited {color:#f3ea59;}
			.login-whisp{min-height:460px;}
			.login-whisp:not(#old_ie){background:url("../img/watter1.png") no-repeat center;}
		</style>	
		<script language="javascript">
			$(document).ready(function(){
				jQuery.fn.center = function () {
				    this.css("position","absolute");
				    this.css("top", Math.max(0, (($(window).height() - this.outerHeight()) / 2) + 
								                $(window).scrollTop()) + "px");
				    this.css("left", Math.max(0, (($(window).width() - this.outerWidth()) / 2) + 
								                $(window).scrollLeft()) + "px");
				    return this;
				}
				$("#logo_div").corner();
				$("#login_div").corner();
				$(".login_but").corner("round 3px").addClass('shadow');
				//$("#all_div").center();
			});
			function onEnterpress(e)

				{
				    var KeyPress  ;
				    if(e && e.which)
				    {
					e = e;		     
					KeyPress = e.which ;
				    }

				    else
				    {
					e = event;
					KeyPress = e.keyCode;
				    }
				    if(KeyPress == 13)
				    {
					document.getElementById('frm1').submit();
					return false     
				    }
				    else
				    {
					return true
				    }

				}
			function guistLogin()
			{
				var user = document.getElementById('uname');
				var pass = document.getElementById('pass');
				var form = document.getElementById('frm1');
				user.value = 'test';
				pass.value = 'test';
				form.submit();
			}
			
		</script>
	</head>
	<body>
	<div id="login-wrapper" class="login-whisp">
		<form action="index.php" id="frm1" method="post">
		<center>
			<br/>
			<?php
				if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')!==FALSE)
					echo 'جهت استفاده بهینه از این سامانه لطفاً از مرورگر <a target="_blank" href="http://www.firefox.com">FireFox</a> استفاده کنید.(<a target="_blank" href="http://www.firefox.com">DOWNLOAD</a>)';
			?>
			<br/>
		</center>
		<div align="center">
			<div id="all_div" >
				<div id="logo_div" >
					<table>
						<tr >
							<td>
								<img src="../img/arm.png" height="65px" >
							</td>
							<td align="center" colspan="2">
								<img src="../img/radan.png" height="70px" >
								<br/>
							</td>								
						</tr>
					</table>
				</div>
				<div id="login_div" >
					<table id="login_tb" >
						<tr>
							<td>
								 نام کاربری:
							</td>
						</tr>
						<tr>
							<td>
								<input name="user" id="uname" type="text" value="" class="textbox" onkeydown="onEnterpress(event);" placeholder="نام کاربری خود را وارد نمایید">
							</td>
						</tr>
						<tr>
							<td>
								رمز عبور:
							</td>
						</tr>
						<tr>
							<td>
								<input name="pass" id="pass" type="password" value="" class="textbox"  onkeydown="onEnterpress(event);" placeholder="رمز عبور خود را وارد نمایید">
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<table width="100%">
									<tr>
										<td align="center" valign="buttom" width="50%">
											<input type="submit" value="ورود" class="inp" style="display:none;"/>		
											<button class="login_but" onclick="if(document.getElementById('uname').value!='' && document.getElementById('pass').value!=''){document.getElementById('frm1').submit();}else{alert('لطفاً نام کاربری و رمز عبور را وارد کنید');}"/>
ورود			
											</button>
										</td>
										<td align="center" width="50%">	
											<button class="login_but" src="../img/public1.png" onclick="guistLogin();"/>
	عمومی
											</button>
										</td>
										<td align="center" style="width:10%">
								<?php 
									if(isset($_REQUEST["stat"])){
										switch($_REQUEST["stat"]){
											case "wrong_user":
											case "wrong_pass":						
												//echo "<span style=\"color:#ffffff;\">نام کاربری یا رمز عبور اشتباه است</span>";						
												break;
											case "session_error":			                        
									//echo "<span style=\"color:#ffffff;\">نشست کاربری شما خاتمه یافته است ، لطفاً مجدداً وارد شوید</span>";			                        
												break;
											case "exit":
									//echo "<span style=\"color:#ffffff;\">خروج با موفقیت انجام شد</span>";
												break;
										}
									}
								?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
				
					</table>
				</div>
			</div>
		<div>
		<div style="font-size:12px;color:#fff;margin:10px;" >
جهت پیگیری رزرو نقدی خود <a href="rahgiri_naghdi.php" > اینجا </a> کلیک کنید
		
			<div style="margin:10px;"><?php echo $conf->kharidar_link;?></div>
			<?php echo $conf->contact; ?>
			
		</div>
			
		</form>
	</div>
		<script language="javascript">
			document.getElementById("uname").focus();
		</script>
	</body>
</html>
