<?php
	include_once('../kernel.php');
	session_start();
	$user_id = ((isset($_SESSION[conf::app.'_user_id']))?(int)$_SESSION[conf::app.'_user_id']:-1);
        $user = new user_class($user_id);
        $user->sabt_khorooj();
	$user->logout();
	session_destroy();
	session_start();
	$_SESSION[conf::app."_login"] = "1";
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
		<link type="text/css" href="../css/style.css" rel="stylesheet" />	
		<style>
		</style>	
		<script language="javascript">
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
	<body dir="ltr"  style="color:#000000;background:#ffffff;font-weight:strong;font-family:tahoma,Tahoma;background-image: url('../img/watter.png');background-repeat:no-repeat;background-position:center">
		<form action="index.php" id="frm1" method="post">
		<center>
			<br/>
			<?php
				if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')!==FALSE)
					echo 'جهت استفاده بهینه از این سامانه لطفاً از مرورگر <a target="_blank" href="http://www.firefox.com">FireFox</a> استفاده کنید.(<a target="_blank" href="http://www.firefox.com">DOWNLOAD</a>)';
			?>
			<br/><br/>
			<table border="0" style="width:1000px;font-weight:bold;">
				<tr>
					<td style="width:0%;vertical-align:top;background:#FFF8DC;display:none;"  >
					</td>
					<td align="center"  valign="top" >
<!--						<table border="0" style="background-image: url('../img/header2.png');background-color:#FFF8DC;font-size:12px;color:#ffffff;" cellpadding="0" cellspacing="0" width="342px" height="232px">-->
						<table border="0" style="font-weight:bold;font-family:tahoma;font-size:12px;color:#000000;border-color:#000;border-style:dashed;border-width:1px;" cellpadding="0" cellspacing="0" width="342px" height="300px" >
							<tr>
								<td>
									<img src="../img/arm.png" style="width:140px;border-width:0px;"  >
								</td>
								<td align="center" colspan="2">
									<img src="../img/radan.png" width="120px" >
									<br/>
									<span style="font-size:10px;" >
									
									<br/>
									
									</span>
								</td>								
							</tr>
							
							<tr>
								<td align="left" style="border-style:dashed;border-width:1px;border-color:#000000;border-bottom-style:none;border-right-style:none;height:35px;">
									<input name="user" id="uname" type="text" value="" class="inp" style="height:20px;border-width:1px;width:117px;border-style:solid;border-color:#999;direction:ltr;" onkeydown="onEnterpress(event);" >&nbsp;&nbsp;&nbsp;
								</td>
								<td align="center" style="border-top-style:dashed;border-top-width:1px;border-top-color:#000000;">
									 : Username
								</td>
							</tr>
							<tr>
								<td align="left" style="border-style:dashed;border-width:1px;border-color:#000000;border-bottom-style:none;border-right-style:none;height:35px;">
									<input name="pass" id="pass" type="password" value="" class="inp" style="height:20px;border-width:1px;width:117px;border-style:solid;border-color:#999;direction:ltr;" onkeydown="onEnterpress(event);" >&nbsp;&nbsp;&nbsp;
								</td>
								<td align="center" style="border-top-style:dashed;border-top-width:1px;border-top-color:#000000;">
									: Password
								</td>
							</tr>
                                                        <tr>
                                                                <td  align="left" style="border-style:dashed;border-width:1px;border-color:#000000;border-right-style:none;height:35px;">
                                                         
                                                                        <select disabled="disabled" style="width:117px;border-style:solid;border-color:#999;direction:ltr;">
                                                                        	<option>Farsi</option>
                                                                        </select>&nbsp;&nbsp;&nbsp;
                                                                        
                                                                </td>
                                                                <td align="center" style="border-style:dashed;border-width:1px;border-color:#000000;border-right-style:none;border-left-style:none;">
									: Language
								</td>
                                                        </tr>
							<tr>
								<td colspan="3">
									<table width="100%">
										<tr>
											<td align="center" valign="buttom" width="50%">
												<input type="submit" value="ورود" class="inp" style="display:none;"/>		
												<img src="../img/enter1.png" alt="ورود" style="cursor:pointer;" onclick="if(document.getElementById('uname').value!='' && document.getElementById('pass').value!=''){document.getElementById('frm1').submit();}else{alert('لطفاً نام کاربری و رمز عبور را وارد کنید');}"/>					
											</td>
											<td align="center" width="50%">	
												<img src="../img/public1.png" alt="ﻭﺭﻭﺩ عمومی" style="cursor:pointer;" onclick="guistLogin();"/>
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
					</td>
				</tr>
				<tr>
				</tr>
			</table>
		</center>
		<br/>
		<br/>
		<div align="center" >
جهت پیگیری رزرو نقدی خود <a href="rahgiri_naghdi.php" > اینجا </a> کلیک کنید
		<br/><br/><br/>
		
			<?php echo conf::contact; ?>
		</div>
		</form>
		<script language="javascript">
			document.getElementById("uname").focus();
		</script>
	</body>
</html>
