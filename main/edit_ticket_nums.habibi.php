<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$msg="";
	if (isset($_SESSION[conf::app.'_user_id']) && isset($_SESSION[conf::app.'_typ']))
	{
		if (!audit_class::isAdmin($_SESSION[conf::app.'_typ']))
		{
			die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
		}
	}
	else
	{
			die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
	}
	$costumer_id = ((isset($_REQUEST["id"]))?(int)$_REQUEST["id"]:-1);
	$cust = new customer_class($costumer_id );
	if(isset($_REQUEST["aval"]))
	{
		$aval = (int)$_REQUEST["aval"];
		$akhar = ((isset($_REQUEST["akhar"]))?(int)$_REQUEST["akhar"]:-1);
		$cust->addTicketNumber($aval,$akhar);
		$msg ="با موفقیت افزوده شد";
	}
	
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
		<script type="text/javascript" src="../js/tavanir.js"></script>
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
			<br/>
			<form method="POST" >
				<table style="color:#000000;" >
					<tr>
						<th colspan="3" >ورود شماره تیکت های مربوط به <?php echo $cust->name; ?></th>
					</tr>
					<tr>
						<td>
							<br/>
							<br/>
							 از شماره:
							<input type="text" value="" name="aval" class="inp1" >
						</td>
						<td>
							<br/> 
							<br/>
							تا شماره:
							<input type="text" value="" name="akhar" class="inp1" >
						</td>
						<td> 
							<br/>
                                                        <br/>
							<input type="submit" value="ثبت"  class="inp1" >
						</td>
					</tr>
				</table>
				<br><br><br>
				<table style="color:#000000;background:#FFF8DC;" >
					<tr>
						<th cospan="2" > شماره تیکت های فعلی مربوط به <?php echo $cust->name; ?></th>
					</tr>
					<tr>
						<td>
							<?php 
								$shom = $cust->ticket_numbers;
								$out = "";
								foreach($shom as $i=>$shomare)
									{	
										$out .= $shomare." و " ;
									}
								$out = substr($out,0,-3);
								echo $out;
							?>
						</td>
					</tr>
				</table>			
			</form>
		</div>
	<?php echo $msg; ?>
	</body>
</html>
