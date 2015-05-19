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
	if(isset($_REQUEST['shomare']) && isset($_REQUEST['id']) )
	{
		mysql_class::ex_sql('select `id` from `ticket` where `shomare`='.(int)$_REQUEST["shomare"],$qs);
		if(mysql_num_rows($qs)==1)
		{
			$r = mysql_fetch_array($qs);
			$ticket = new ticket_class((int)$r['id']);
			$customer = new customer_class($ticket->customer_id);
			$parvaz = new parvaz_det_class($ticket->parvaz_det_id);
			$parvaz2 = null;
		}
		if(mysql_num_rows($qs)>1)
		{
			$id = (int)$_REQUEST['id'];
			$ticket = new ticket_class($id);
			//echo (int)ticket_class::loadBargasht(23);
			$ticket2 = new ticket_class((int)ticket_class::loadBargasht($id));
			$customer = new customer_class($ticket->customer_id);
			$parvaz = new parvaz_det_class($ticket->parvaz_det_id);
			$parvaz2 = new parvaz_det_class($ticket2->parvaz_det_id);
		}	
	}
	function loadAdl($inp)
	{
		$inp = (int)$inp;
		$out ='ADL';
		if($inp ==1)
			$out = 'CHD';
		if($inp ==2)
			$out = 'INF';
		return $out;
	}
	function loadCity($inp)
	{
		$inp = (int)$inp;
		$out = "";
		mysql_class::ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$out = $r["name"];
		}
		return($out);
	}
	function loadHavapeima($inp)
	{
		$inp = (int)$inp;
		$out = "";
		mysql_class::ex_sql("select `name` from `havapeima` where `id` = '$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$out = $r["name"];
		}
		return($out);
	}
	function loadSherkat($inp)
	{
		$inp = (int)$inp;
		$out = "";
		mysql_class::ex_sql("select `name` from `sherkat` where `id` = '$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$out = $r["name"];
		}
		return($out);
	}
	function hamed_pdate1($str)
        {
                $out=enToPerNums(jdate('d / m / Y',strtotime($str)));
		$out .= "<br/>".date('F d',strtotime($str));
                return $out;
        }
	function loadUser($inp)
	{
		$inp = (int)$inp;
		$out = "";
		mysql_class::ex_sql("select `fname`,`lname` from `user` where `id` = '$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$out = $r["fname"].' '.$r["lname"];
		}
		return($out);
	}


?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<link type="text/css" href="css/style.css" rel="stylesheet" />
		<link type="text/css" href="css/hamed.css" rel="stylesheet" />
		<link type="text/css" href="css/mehrdad.css" rel="stylesheet" />
		<link rel="stylesheet" type="text/css" media="all" href="css/skins/aqua/theme.css" title="Aqua" />
	<title>
		چاپ بلیت الکترونیک
	<?php echo $customer->name; ?>
	</title>
	<style>
	td {text-align:center;border-width:1px;border-spacing:0px;}
	</style>
	</head>
<body style="background-image: none;background-color: white;direction:ltr;font-family:tahoma,Tahoma;font-size:10px;" >

<div align="center" style='width:21cm;height:29.5cm;'>
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%;" border="0" >
			<tbody>
				<tr>
					<td style="width:7cm;text-align:left;font-size:11px;" >
						<?php echo (int)$_REQUEST["shomare"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td style="font-size:7px;direction:ltr;text-align:right;"  >
					Serial(Voucher/ Passenger No):
					</td>
				</tr>
			</tbody>
		</table>
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2" >
		<tbody>
		<tr>
			<td rowspan="3" colspan="4" style="width:4cm;height:3cm;" >
				<table width="100%">
					<tr>
						<td style="font-size:7px;text-align:right;vertical-align:top;">
							تاریخ و محل صدور :
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:top;">
							DATE AND PLACE OF ISSUE
						</td>
					</tr>
					<tr>
						<td colspan="3" style="direction:ltr;">
							<?php echo $ticket->regtime; ?>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<b>   <?php echo $customer->name;  ?></b>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<?php echo loadUser($ticket->user_id); ?>
						</td>
					</tr>
					<tr>
						<td style="font-size:7px;text-align:right;vertical-align:bottom;">
							صادر کننده 
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:bottom;">
							AGENT 
						</td>
					</tr>
				</table>
			</td>
			<td colspan="3" style="height:0.8cm;font-size:7px;"  >
			<b>
			ROUTE 1<br />
		مسیر ۱
			</b>
			</td>
			<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
				ORIGIN/DESTINATION
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="6">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width="20px" >
									<br/>گستره‌ارتباطات‌شرق
						</td>
						<td>
								<img src='../img/arm_gray.png' width="25px" ><br/>
								رادان
						</td>
						<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
							ISSUED BY
						</td>
						<td style="font-size: 6px;width:30%;text-align:left;" >
							PASSENGER<br/>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK
						</td>					
					</tr>
				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="12" style="height:0.8cm;">
				&nbsp;
			</td>
		</tr>
		<tr>		
			<td style="text-align: center;font-size:13px;" colspan="10">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: right;font-size:7px;vertical-align:top;">
							نام مسافر(غیر قابل انتقال) :
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NAME OF PASSENGER :
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<b><?php echo $ticket->fname." ".$ticket->lname; ?></b>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							&nbsp;
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="top" style="height:0.8cm;" >
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">بار کنترل شده  ‌BAGGAEG<br/>بار کنترل نشده CK/UNCK
</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">ALLOW<br/>بارمجاز</span></p>
			</td>


‬
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br/>AFTER<br/>
‫فاقد اعتبار بعد از
</span></p>
			</td>




			<td >
			<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br/>BEFORE<br/>‫فاقد اعتبار قبل از‬</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">FARE BASIS<br/>مبنای نرخ‬‫</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">STATUS<br/>وضعیت‬‫</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">TIME<br/>زمان‬‫</span></p>
			</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">DATE<br/>تاریخ</span></p>
			</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">FLIGHT/CLASS<br/>پرواز/کلاس</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">CARRIER<br/>حمل کننده</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">‬‫
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: right;font-size:7px;vertical-align:top;">
						برای مسافرت معتبر نیست
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NOT GOOD FOR PASSAGE
						</td>
					</tr>
				</table>
			</span>
			</p>
			</td>
			<td>
				<p style="text-align: center;"><span style="font-size: 7px;">
					X/O
				</p>
			</td>

		</tr>
		<tr style="height:0.8cm;" >
			<td>
				<table width="100%">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;"><b>20</b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><b>OK</b></td>
			<td><b><?php echo $parvaz->saat; ?></b></td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b><?php echo perToEnNums(jdate("m/d",strtotime($parvaz->tarikh))); ?></b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo date("d M",strtotime($parvaz->tarikh)); ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b>Y</b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo $parvaz->shomare; ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td><b><?php echo loadSherkat($parvaz->sherkat_id); ?></b></td>
			<td >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			از:
						</td>
						<td style="direction:ltr;text-align:left" >
			from:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->mabda_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr align="center" style="height:0.8cm;" >
			<td>
				<table width="100%">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="2">
&nbsp;
			</td>
			<td colspan="2">
&nbsp;
			</td>
			<td>&nbsp;</td>
			<td >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->maghsad_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr style="height:0.8cm;" >
			<td style="text-align: center;" colspan="8" rowspan="3">
				<?php
					$b = new barcode_class(ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri));
					echo '<img src="../img/barcodes/'.ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri).'.png" alt="'.ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri).'" />';
				?>
			</td>
			<td colspan="4">
				<table style="text-align: center;font-size: 6px;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;" >
			کد گروه:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;" >
			TOUR CODE:
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b>VOID</b>
						</td>
					</tr>
				</table>	
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table style="text-align: center;font-size: 6px;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;" >
			طرز پرداخت:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;" >
			FORM OF PAYMENT:
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" style="font-size: 12px;">
							<b>CASH</b>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<p align="left">
				<span style="font-size:10px;text-align:left;"><b>TOTAL FARE </b></span>
				<br/>
				</p>
				<p align="center">
				----
				</p>
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="5">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		رفرنس
					</td>
					<td style="direction:ltr;text-align:left" >
		PNR
					</td>
				</tr>
				<tr>
					<td colspan='2' style="font-size: 12px;" >
					<b><?php echo ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri); ?></b>					
					</td>
				</tr>
			</table>
		</td>
		<td>
			&nbsp;
		</td>
		</tr>
		</tbody>
		</table>
<center>---------------------------------------------------------------------------------------------------</center>
<br/>
<?php 
		if($parvaz2!=null)
		{
?>
<!--
				<table style="border-style: solid; border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2">
		<tbody>
		<tr>
			<td rowspan="3" colspan="3" style="width:4cm;height:3cm;" >
			<b>   <?php echo $customer->name;  ?></b>
			<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
			</td>
			<td colspan="3" style="height:0.8cm;font-size: 7px;"  >
			<b>
			ROUTE 2<br />
		مسیر ۲
			</b>
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="7">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width="20px" >
									<br/>گستره‌ارتباطات‌شرق
						</td>
						<td>
								<img src='../img/arm_gray.png' width="20px" ><br/>
								رادان
						</td>
						<td style="font-size: 6px;width:30%;" >
							PASSENGER TICKET<br/>AND<br/>BAGGAGE CHECK
						</td>					
					</tr>
				</table>			
			</td>
		</tr>
		<tr  >
			<td rowspan='2' colspan='2' style="height:2cm;font-size:19px;" ><b><?php echo loadAdl($ticket->adult); ?></b></td>
			<td colspan="4" ><span style="font-size:12px;">کدرهگیری:
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri); ?></b></span>
			</td>
			<td colspan="6" style="text-align:right;height:0.8cm;font-size: 12px;" ><span style="font-size: 12px;">شماره بلیت:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="font-size: 13px;"> <b><?php echo (int)$_REQUEST["shomare"]; ?></b></td>
		</tr>
		<tr style="height:0.8cm;" >
		
		<td style="text-align: center;font-size:13px;" colspan="10"><b><?php echo $ticket->fname." ".$ticket->lname; ?></b></td>

		</tr>
		<tr valign="top" style="height:0.8cm;" >
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">وضعیت<br/>RES.
	<br/>STATUS</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">ساعت<br/>TIME</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">تاریخ<br/>DATE</span></p>
			</td>
			<td colspan="2" >
			<p style="text-align: center;"><span style="font-size: 7px;">شماره پرواز <br/>FLIGHT No./ CLASS</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">حمل کننده<br/>CARRIR‬‫</span></p>
			</td>
			<td colspan="3" >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			از:
						</td>
						<td style="direction:ltr;text-align:left" >
			from:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz2->mabda_id); ?></b>
						</td>
					</tr>
				</table>		
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">وزن <br/>WEIGHT</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">تعداد<br>PIECES</span></p>
			</td>
			<td colspan="1" ><span style="font-size: 7px;">توشه مجانی <br />FREE BAGGAGE<br /> ALLOW</span></td>
		</tr>
		<tr style="height:0.8cm;" >
			<td><b>OK</b></td>
			<td style="text-align: center;"><b><?php echo $parvaz2->saat; ?></b></td>
			<td><b><?php echo enToPerNums(hamed_pdate1($parvaz2->tarikh)); ?></b></td>
			<td>Y</td>
			<td><b><?php echo $parvaz2->shomare; ?></b></td>
			<td><b><?php echo loadSherkat($parvaz2->sherkat_id); ?></b></td>
			<td colspan="3" >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz2->maghsad_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>--</td>
			<td>--</td>
			<td colspan="2" style="text-align: center;">20KG</td>
		</tr>
		<tr align="center" style="height:0.8cm;" >
			<td>--</td>
			<td>--</td>
			<td>--</td>
			<td>--</td>
			<td>--</td>
			<td>--</td>
			<td colspan="3" >--</td>
			<td>--</td>
			<td>--</td>
			<td colspan="2" >VOID</td>
		</tr>
		<tr style="height:0.8cm;" >
			<td style="text-align: center;" colspan="6" rowspan="2">اینجا محل تبلیغات شما است</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			طرزپرداخت:
						</td>
						<td style="direction:ltr;text-align:left" >
			FORM OF PAYMAENT:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 10px;" ><b>				
							نقدی CASH
											</b>						
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;" colspan="5">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			بهای بلیت
						</td>
						<td style="direction:ltr;text-align:left" >
			FARE:
						</td>
					</tr>
					<tr>
						<td colspan='2' >
							---
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="7">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		توضیحات:
					</td>
					<td style="direction:ltr;text-align:left" >
		REMARKS:
					</td>
				</tr>
				<tr>
					<td colspan='2' style="font-size: 8px;" >
					--						
					</td>
				</tr>
			</table>
		</td>
		</tr>
		</tbody>
		</table>
-->

		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%;" border="0" >
			<tbody>
				<tr>
					<td style="width:7cm;text-align:left;font-size:11px;" >
						<?php echo (int)$_REQUEST["shomare"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td style="font-size:7px;direction:ltr;text-align:right;"  >
					Serial(Voucher/ Passenger No):
					</td>
				</tr>
			</tbody>
		</table>
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2" >
		<tbody>
		<tr>
			<td rowspan="3" colspan="4" style="width:4cm;height:3cm;" >
				<table width="100%">
					<tr>
						<td style="font-size:7px;text-align:right;vertical-align:top;">
							تاریخ و محل صدور :
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:top;">
							DATE AND PLACE OF ISSUE
						</td>
					</tr>
					<tr>
						<td colspan="3" style="direction:ltr;">
							<?php echo $ticket->regtime; ?>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<b>   <?php echo $customer->name;  ?></b>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<?php echo loadUser($ticket->user_id); ?>
						</td>
					</tr>
					<tr>
						<td style="font-size:7px;text-align:right;vertical-align:bottom;">
							صادر کننده 
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:bottom;">
							AGENT 
						</td>
					</tr>
				</table>
			</td>
			<td colspan="3" style="height:0.8cm;font-size:7px;"  >
			<b>
			ROUTE 2<br />
		مسیر ۲
			</b>
			</td>
			<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
				ORIGIN/DESTINATION
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="6">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width="20px" >
									<br/>گستره‌ارتباطات‌شرق
						</td>
						<td>
								<img src='../img/arm_gray.png' width="25px" ><br/>
								رادان
						</td>
						<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
							ISSUED BY
						</td>
						<td style="font-size: 6px;width:30%;text-align:left;" >
							PASSENGER<br/>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK
						</td>					
					</tr>
				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="12" style="height:0.8cm;">
				&nbsp;
			</td>
		</tr>
		<tr>		
			<td style="text-align: center;font-size:13px;" colspan="10">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: right;font-size:7px;vertical-align:top;">
							نام مسافر(غیر قابل انتقال) :
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NAME OF PASSENGER :
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<b><?php echo $ticket->fname." ".$ticket->lname; ?></b>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							&nbsp;
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="top" style="height:0.8cm;" >
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">بار کنترل شده  ‌BAGGAEG<br/>بار کنترل نشده CK/UNCK
</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">ALLOW<br/>بارمجاز</span></p>
			</td>


‬
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br/>AFTER<br/>
‫فاقد اعتبار بعد از
</span></p>
			</td>




			<td >
			<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br/>BEFORE<br/>‫فاقد اعتبار قبل از‬</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">FARE BASIS<br/>مبنای نرخ‬‫</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">STATUS<br/>وضعیت‬‫</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">TIME<br/>زمان‬‫</span></p>
			</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">DATE<br/>تاریخ</span></p>
			</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">FLIGHT/CLASS<br/>پرواز/کلاس</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">CARRIER<br/>حمل کننده</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">‬‫
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: right;font-size:7px;vertical-align:top;">
						برای مسافرت معتبر نیست
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NOT GOOD FOR PASSAGE
						</td>
					</tr>
				</table>
			</span>
			</p>
			</td>
			<td>
				<p style="text-align: center;"><span style="font-size: 7px;">
					X/O
				</p>
			</td>

		</tr>
		<tr style="height:0.8cm;" >
			<td>
				<table width="100%">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;"><b>20</b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><b>OK</b></td>
			<td><b><?php echo $parvaz2->saat; ?></b></td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b><?php echo perToEnNums(jdate("m/d",strtotime($parvaz2->tarikh))); ?></b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo date("d M",strtotime($parvaz2->tarikh)); ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b>Y</b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo $parvaz2->shomare; ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td><b><?php echo loadSherkat($parvaz2->sherkat_id); ?></b></td>
			<td >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			از:
						</td>
						<td style="direction:ltr;text-align:left" >
			from:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz2->mabda_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr align="center" style="height:0.8cm;" >
			<td>
				<table width="100%">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="2">
&nbsp;
			</td>
			<td colspan="2">
&nbsp;
			</td>
			<td>&nbsp;</td>
			<td >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz2->maghsad_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr style="height:0.8cm;" >
			<td style="text-align: center;" colspan="8" rowspan="3">
				<?php
					$b = new barcode_class(ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri));
					echo '<img src="../img/barcodes/'.ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri).'.png" alt="'.ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri).'" />';
				?>
			</td>
			<td colspan="4">
				<table style="text-align: center;font-size: 6px;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;" >
			کد گروه:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;" >
			TOUR CODE:
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b>VOID</b>
						</td>
					</tr>
				</table>	
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table style="text-align: center;font-size: 6px;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;" >
			طرز پرداخت:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;" >
			FORM OF PAYMENT:
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" style="font-size: 12px;">
							<b>CASH</b>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<p align="left">
				<span style="font-size:10px;text-align:left;"><b>TOTAL FARE </b></span>
				<br/>
				</p>
				<p align="center">
				----
				</p>
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="5">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		رفرنس
					</td>
					<td style="direction:ltr;text-align:left" >
		PNR
					</td>
				</tr>
				<tr>
					<td colspan='2' style="font-size: 12px;" >
					<b><?php echo ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri); ?></b>					
					</td>
				</tr>
			</table>
		</td>
		<td>
			&nbsp;
		</td>
		</tr>
		</tbody>
		</table>
<center>---------------------------------------------------------------------------------------------------</center>
<br/>
<!--
				<table style="border-style: solid; border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2">
		<tbody>
		<tr>
			<td rowspan="3" colspan="3" style="width:4cm;height:3cm;" >
			<b>   <?php echo $customer->name;  ?></b>
			<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
			</td>
			<td colspan="3" style="height:0.8cm;font-size: 7px;"  >
			<b>
			PASSENGER COUPON<br />
		کوپن مسافر
			</b>
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="7">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width="20px" >
									<br/>گستره‌ارتباطات‌شرق
						</td>
						<td>
								<img src='../img/arm_gray.png' width="20px" ><br/>
								رادان
						</td>
						<td style="font-size: 6px;width:30%;" >
							PASSENGER TICKET<br/>AND<br/>BAGGAGE CHECK
						</td>					
					</tr>
				</table>			
			</td>
		</tr>
		<tr  >
			<td rowspan='2' colspan='2' style="height:2cm;font-size:19px;" ><b><?php echo loadAdl($ticket->adult); ?></b></td>
			<td colspan="4" ><span style="font-size:12px;">کدرهگیری:
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri); ?></b></span>
			</td>
			<td colspan="6" style="text-align:right;height:0.8cm;font-size: 12px;" ><span style="font-size: 12px;">شماره بلیت:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="font-size: 13px;"> <b><?php echo (int)$_REQUEST["shomare"]; ?></b></td>
		</tr>
		<tr style="height:0.8cm;" >
		
		<td style="text-align: center;font-size:13px;" colspan="10"><b><?php echo $ticket->fname." ".$ticket->lname; ?></b></td>

		</tr>
		<tr valign="top" style="height:0.8cm;" >
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">وضعیت<br/>RES.
	<br/>STATUS</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">ساعت<br/>TIME</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">تاریخ<br/>DATE</span></p>
			</td>
			<td colspan="2" >
			<p style="text-align: center;"><span style="font-size: 7px;">شماره پرواز <br/>FLIGHT No./ CLASS</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">حمل کننده<br/>CARRIR‬‫</span></p>
			</td>
			<td colspan="3" >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			از:
						</td>
						<td style="direction:ltr;text-align:left" >
			from:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->mabda_id); ?></b>
						</td>
					</tr>
				</table>		
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">وزن <br/>WEIGHT</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">تعداد<br/>PIECES</span></p>
			</td>
			<td colspan="2" ><span style="font-size: 7px;">توشه مجانی <br />FREE BAGGAGE<br /> ALLOW</span></td>
		</tr>
		<tr style="height:0.8cm;" >
			<td><b>OK</b></td>
			<td style="text-align: center;"><b><?php echo $parvaz->saat; ?></b></td>
			<td><b><?php echo hamed_pdate1($parvaz->tarikh); ?></b></td>
			<td>Y</td>
			<td><b><?php echo $parvaz->shomare; ?></b></td>
			<td><b><?php echo loadSherkat($parvaz->sherkat_id); ?></b></td>
			<td colspan="3" >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->maghsad_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>--</td>
			<td>--</td>
			<td colspan="2" style="text-align: center;">20KG</td>
		</tr>
				<tr style="height:0.8cm;" >
			<td><b>OK</b></td>
			<td style="text-align: center;"><b><?php echo $parvaz2->saat; ?></b></td>
			<td><b><?php echo enToPerNums(hamed_pdate1($parvaz2->tarikh)); ?></b></td>
			<td>Y</td>
			<td><b><?php echo $parvaz2->shomare; ?></b></td>
			<td><b><?php echo loadSherkat($parvaz2->sherkat_id); ?></b></td>
			<td colspan="3" >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz2->maghsad_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>--</td>
			<td>--</td>
			<td colspan="2" style="text-align: center;">20KG</td>
		</tr>
		<tr style="height:0.8cm;" >
			<td style="text-align: center;" colspan="6" rowspan="2">اینجا محل تبلیغات شما است</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			طرزپرداخت:
						</td>
						<td style="direction:ltr;text-align:left" >
			FORM OF PAYMAENT:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 10px;" ><b>				
							نقدی CASH
											</b>						
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;" colspan="5">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			بهای بلیت
						</td>
						<td style="direction:ltr;text-align:left" >
			FARE:
						</td>
					</tr>
					<tr>
						<td colspan='2' >
							---
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="7">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		توضیحات:
					</td>
					<td style="direction:ltr;text-align:left" >
		REMARKS:
					</td>
				</tr>
				<tr>
					<td colspan='2' style="font-size: 8px;" >
					--						
					</td>
				</tr>
			</table>
		</td>
		</tr>
		</tbody>
		</table>
-->
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%;" border="0" >
			<tbody>
				<tr>
					<td style="width:7cm;text-align:left;font-size:11px;" >
						<?php echo (int)$_REQUEST["shomare"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td style="font-size:7px;direction:ltr;text-align:right;"  >
					Serial(Voucher/ Passenger No):
					</td>
				</tr>
			</tbody>
		</table>
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2" >
		<tbody>
		<tr>
			<td rowspan="3" colspan="4" style="width:4cm;height:3cm;" >
				<table width="100%">
					<tr>
						<td style="font-size:7px;text-align:right;vertical-align:top;">
							تاریخ و محل صدور :
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:top;">
							DATE AND PLACE OF ISSUE
						</td>
					</tr>
					<tr>
						<td colspan="3" style="direction:ltr;">
							<?php echo $ticket->regtime; ?>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<b>   <?php echo $customer->name;  ?></b>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<?php echo loadUser($ticket->user_id); ?>
						</td>
					</tr>
					<tr>
						<td style="font-size:7px;text-align:right;vertical-align:bottom;">
							صادر کننده 
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:bottom;">
							AGENT 
						</td>
					</tr>
				</table>
			</td>
			<td colspan="3" style="height:0.8cm;font-size:7px;"  >
			<b>
			Passenger Coupon<br />
		کوپن مسافر
			</b>
			</td>
			<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
				ORIGIN/DESTINATION
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="6">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width="20px" >
									<br/>گستره‌ارتباطات‌شرق
						</td>
						<td>
								<img src='../img/arm_gray.png' width="25px" ><br/>
								رادان
						</td>
						<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
							ISSUED BY
						</td>
						<td style="font-size: 6px;width:30%;text-align:left;" >
							PASSENGER<br/>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK
						</td>					
					</tr>
				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="12" style="height:0.8cm;">
				&nbsp;
			</td>
		</tr>
		<tr>		
			<td style="text-align: center;font-size:13px;" colspan="10">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: right;font-size:7px;vertical-align:top;">
							نام مسافر(غیر قابل انتقال) :
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NAME OF PASSENGER :
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<b><?php echo $ticket->fname." ".$ticket->lname; ?></b>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							&nbsp;
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="top" style="height:0.8cm;" >
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">بار کنترل شده  ‌BAGGAEG<br/>بار کنترل نشده CK/UNCK
</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">ALLOW<br/>بارمجاز</span></p>
			</td>


‬
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br/>AFTER<br/>
‫فاقد اعتبار بعد از
</span></p>
			</td>




			<td >
			<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br/>BEFORE<br/>‫فاقد اعتبار قبل از‬</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">FARE BASIS<br/>مبنای نرخ‬‫</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">STATUS<br/>وضعیت‬‫</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">TIME<br/>زمان‬‫</span></p>
			</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">DATE<br/>تاریخ</span></p>
			</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">FLIGHT/CLASS<br/>پرواز/کلاس</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">CARRIER<br/>حمل کننده</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">‬‫
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: right;font-size:7px;vertical-align:top;">
						برای مسافرت معتبر نیست
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NOT GOOD FOR PASSAGE
						</td>
					</tr>
				</table>
			</span>
			</p>
			</td>
			<td>
				<p style="text-align: center;"><span style="font-size: 7px;">
					X/O
				</p>
			</td>

		</tr>
		<tr style="height:0.8cm;" >
			<td>
				<table width="100%">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td><b>20</b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><b>OK</b></td>
			<td><b><?php echo $parvaz->saat; ?></b></td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b><?php echo perToEnNums(jdate("m/d",strtotime($parvaz->tarikh))); ?></b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo date("d M",strtotime($parvaz->tarikh)); ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b>Y</b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo $parvaz->shomare; ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td><b><?php echo loadSherkat($parvaz->sherkat_id); ?></b></td>
			<td >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			از:
						</td>
						<td style="direction:ltr;text-align:left" >
			from:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->mabda_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr align="center" style="height:0.8cm;" >
			<td>
				<table width="100%">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td><b>20</b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><b>OK</b></td>
			<td><b><?php echo $parvaz2->saat; ?></td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b><?php echo perToEnNums(jdate("m/d",strtotime($parvaz2->tarikh))); ?></b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo date("d M",strtotime($parvaz2->tarikh)); ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b>Y</b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo $parvaz->shomare; ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td><b><?php echo loadSherkat($parvaz2->sherkat_id); ?></b></td>
			<td >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->maghsad_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr style="height:0.8cm;" >
			<td style="text-align: center;" colspan="8" rowspan="3">
				<?php
					$b = new barcode_class(ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri));
					echo '<img src="../img/barcodes/'.ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri).'.png" alt="'.ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri).'" />';
				?>
			</td>
			<td colspan="4">
				<table style="text-align: center;font-size: 6px;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;" >
			کد گروه:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;" >
			TOUR CODE:
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->mabda_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table style="text-align: center;font-size: 6px;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;" >
			طرز پرداخت:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;" >
			FORM OF PAYMENT:
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" style="font-size: 12px;">
							<b>CASH</b>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<p align="left">
				<span style="font-size:10px;text-align:left;"><b>TOTAL FARE </b></span>
				<br/>
				</p>
				<p align="center">
				----
				</p>
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="5">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		رفرنس
					</td>
					<td style="direction:ltr;text-align:left" >
		PNR
					</td>
				</tr>
				<tr>
					<td colspan='2' style="font-size: 12px;" >
					<b><?php echo ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri); ?></b>					
					</td>
				</tr>
			</table>
		</td>
		<td>
			&nbsp;
		</td>
		</tr>
		</tbody>
		</table>






<!--
		</br>
		<table style='width:90%;border-style:solid;border-collapse:collapse;' border='1' >
			<tr>
				<td style='font-size:6px;text-align:right;border-width:1px'  >
				مراجعه مسافران 2 ساعت قبل از پرواز در فرودگاه به کانتر پرواز الزامی می باشد
				</td>
				<td style='font-size:6px;text-align:right;border-width:1px' >
					پرواز ها چارتر و شامل قوانین چارتری می باشد
				</td>
				<td colspan='2' style='font-size:6px;text-align:right;border-width:1px'  >
					تغییر نام مسافر یا انتقال به غیر مجاز نبوده و قابل پیگرد قانونی می باشد
				</td>
			</tr>
			<tr>
				<td style='font-size:6px;text-align:right;border-width:1px'  >
				حمل و نقل مزبور مشمول مقررات مربوط به مسئولیت مقرر در کنوانسیون ورشو 1929 می باشد
				</td>
				<td style='font-size:6px;text-align:right;border-width:1px'  >
					جهت کسب اطلاعات بیشتر به سایت زیر مراجعه نمائید www.amirtous.com
				</td>
				<td style='font-size:6px;text-align:right;border-width:1px' >
					کلیه پروازهای داخلی ایران ایر ، ایران ایرتور و آتا از ترمینال 2 فرودگاه مهر آباد انجام می گردد	
				</td>
				<td style='font-size:6px;text-align:right;border-width:1px' >
					در زمان دریافت بلیت کلیه مندرجات آن را کنترل نمائید
				</td>
			</tr>

		</table>
-->
		<br/>
		<table style="font-family:b zar,zar,tahoma;font-size:14px;direction:rtl;">
			<tr>
				<td>
					
*کلیه پروازهای ایران ایر ، ایرتور و آتا از مبدا تهران از ترمینال ۲ فرودگاه مهرآباد انجام می‌شود.
				</td>
			</tr>
			<tr>
				<td>
*پرواز چارتر می‌باشد.
				</td>
			</tr>
			<tr>
				<td>
*حضور ۲ ساعت قبل از پرواز در فرودگاه الزامی می‌باشد.
				</td>
			</tr>
			<tr>
				<td>
					* حمل و نقل مزبور مشمول مقررات مربوط به مسئولیت مقرر در کنوانسیون ورشو ۱۹۲۹ می‌باشد. جهت کسب اطلاعات بیشتر به http://www.radanseir.com مراجعه کنید.
				</td>
			</tr>
		</table>
<?php 
	}
	else
	{
?>
<!--           پرواز یک طرفه باشد      -->
</table>
<!--
		<table style="width:90%; height: 22%;" >
			<tr>
				<td>
				</td>
			</tr>
		</table>
		<table style="border-style: solid; border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2">
		<tbody>
		<tr>
			<td rowspan="3" colspan="3" style="width:4cm;height:3cm;" >
			<b>   <?php echo $customer->name;  ?></b>
			<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
			</td>
			<td colspan="3" style="height:0.8cm;font-size: 7px;"  >
			<b>
			PASSENGER COUPON<br />
		کوپن مسافر
			</b>
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="7">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width="20px" >
									<br/>گستره‌ارتباطات‌شرق
						</td>
						<td>
								<img src='../img/arm_gray.png' width="20px" ><br/>
								رادان
						</td>
						<td style="font-size: 6px;width:30%;" >
							PASSENGER TICKET<br/>AND<br/>BAGGAGE CHECK
						</td>					
					</tr>
				</table>			
			</td>
		</tr>
		<tr  >
			<td rowspan='2' colspan='2' style="height:2cm;font-size:19px;" ><b><?php echo loadAdl($ticket->adult); ?></b></td>
			<td colspan="4" ><span style="font-size:12px;">کدرهگیری:
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri); ?></b></span>
			</td>
			<td colspan="6" style="text-align:right;height:0.8cm;font-size: 12px;" ><span style="font-size: 12px;">شماره بلیت:</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="font-size: 13px;"> <b><?php echo (int)$_REQUEST["shomare"]; ?></b></td>
		</tr>
		<tr style="height:0.8cm;" >
		
		<td style="text-align: center;font-size:13px;" colspan="10"><b><?php echo $ticket->fname." ".$ticket->lname; ?></b></td>

		</tr>
		<tr valign="top" style="height:0.8cm;" >
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">وضعیت<br/>RES.
	<br/>STATUS</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">ساعت<br/>TIME</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">تاریخ<br/>DATE</span></p>
			</td>
			<td colspan="2" >
			<p style="text-align: center;"><span style="font-size: 7px;">شماره پرواز <br/>FLIGHT No./ CLASS</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">حمل کننده<br/>CARRIR‬‫</span></p>
			</td>
			<td colspan="3" >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			از:
						</td>
						<td style="direction:ltr;text-align:left" >
			from:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->mabda_id); ?></b>
						</td>
					</tr>
				</table>		
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">وزن <br/>WEIGHT</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">تعداد<br>PIECES</span></p>
			</td>
			<td colspan="2" ><span style="font-size: 7px;">توشه مجانی <br />FREE BAGGAGE<br /> ALLOW</span></td>
		</tr>
		<tr style="height:0.8cm;" >
			<td><b>OK</b></td>
			<td style="text-align: center;"><b><?php echo $parvaz->saat; ?></b></td>
			<td><b><?php echo hamed_pdate1($parvaz->tarikh); ?></b></td>
			<td>Y</td>
			<td><b><?php echo $parvaz->shomare; ?></b></td>
			<td><b><?php echo loadSherkat($parvaz->sherkat_id); ?></b></td>
			<td colspan="3" >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->maghsad_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>--</td>
			<td style="text-align: center;">--</td>
			<td colspan="2"style="text-align: center;">20KG</td>
		</tr>
		<tr align="center" style="height:0.8cm;" >
			<td>--</td>
			<td>--</td>
			<td>--</td>
			<td>--</td>
			<td>--</td>
			<td>--</td>
			<td colspan="3" >--</td>
			<td>-</td>
			<td >--</td>
			<td colspan="2" >VOID</td>
		</tr>
		<tr style="height:0.8cm;" >
			<td style="text-align: center;" colspan="6" rowspan="2">اینجا محل تبلیغات شما است</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			طرزپرداخت:
						</td>
						<td style="direction:ltr;text-align:left" >
			FORM OF PAYMAENT:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 10px;" ><b>				
							نقدی CASH
											</b>						
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;" colspan="5">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			بهای بلیت
						</td>
						<td style="direction:ltr;text-align:left" >
			FARE:
						</td>
					</tr>
					<tr>
						<td colspan='2' >
							---
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="7">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		توضیحات:
					</td>
					<td style="direction:ltr;text-align:left" >
		REMARKS:
					</td>
				</tr>
				<tr>
					<td colspan='2' style="font-size: 8px;" >
					--						
					</td>
				</tr>
			</table>
		</td>
		</tr>
		</tbody>
		</table>
		</table>
-->
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%;" border="0" >
			<tbody>
				<tr>
					<td style="width:7cm;text-align:left;font-size:11px;" >
						<?php echo (int)$_REQUEST["shomare"]; ?>&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td style="font-size:7px;direction:ltr;text-align:right;"  >
					Serial(Voucher/ Passenger No):
					</td>
				</tr>
			</tbody>
		</table>
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2" >
		<tbody>
		<tr>
			<td rowspan="3" colspan="4" style="width:4cm;height:3cm;" >
				<table width="100%">
					<tr>
						<td style="font-size:7px;text-align:right;vertical-align:top;">
							تاریخ و محل صدور :
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:top;">
							DATE AND PLACE OF ISSUE
						</td>
					</tr>
					<tr>
						<td colspan="3" style="direction:ltr;">
							<?php echo $ticket->regtime; ?>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<b>   <?php echo $customer->name;  ?></b>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<?php echo loadUser($ticket->user_id); ?>
						</td>
					</tr>
					<tr>
						<td style="font-size:7px;text-align:right;vertical-align:bottom;">
							صادر کننده 
						</td>
						<td>
							&nbsp;
						</td>
						<td style="font-size:7px;text-align:left;vertical-align:bottom;">
							AGENT 
						</td>
					</tr>
				</table>
			</td>
			<td colspan="3" style="height:0.8cm;font-size:7px;"  >
			<b>
			Passenger Coupon<br />
		کوپون مسافر
			</b>
			</td>
			<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
				ORIGIN/DESTINATION
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="6">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width="20px" >
									<br/>گستره‌ارتباطات‌شرق
						</td>
						<td>
								<img src='../img/arm_gray.png' width="25px" ><br/>
								رادان
						</td>
						<td style="font-size: 6px;width:10%;text-align:left;vertical-align:top;">
							ISSUED BY
						</td>
						<td style="font-size: 6px;width:30%;text-align:left;" >
							PASSENGER<br/>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK
						</td>					
					</tr>
				</table>			
			</td>
		</tr>
		<tr>
			<td colspan="12" style="height:0.8cm;">
				&nbsp;
			</td>
		</tr>
		<tr>		
			<td style="text-align: center;font-size:13px;" colspan="10">
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: right;font-size:7px;vertical-align:top;">
							نام مسافر(غیر قابل انتقال) :
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NAME OF PASSENGER :
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<b><?php echo $ticket->fname." ".$ticket->lname; ?></b>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							&nbsp;
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="top" style="height:0.8cm;" >
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">بار کنترل شده  ‌BAGGAEG<br/>بار کنترل نشده CK/UNCK
</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">ALLOW<br/>بارمجاز</span></p>
			</td>


‬
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br/>AFTER<br/>
‫فاقد اعتبار بعد از
</span></p>
			</td>




			<td >
			<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br/>BEFORE<br/>‫فاقد اعتبار قبل از‬</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">FARE BASIS<br/>مبنای نرخ‬‫</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">STATUS<br/>وضعیت‬‫</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">TIME<br/>زمان‬‫</span></p>
			</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">DATE<br/>تاریخ</span></p>
			</td>
			<td colspan="2">
			<p style="text-align: center;"><span style="font-size: 7px;">FLIGHT/CLASS<br/>پرواز/کلاس</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">CARRIER<br/>حمل کننده</span></p>
			</td>
			<td>
			<p style="text-align: center;"><span style="font-size: 7px;">‬‫
				<table width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td style="text-align: right;font-size:7px;vertical-align:top;">
						برای مسافرت معتبر نیست
						</td>		
						<td>
							&nbsp;
						</td>		
						<td style="text-align:left;direction:ltr;font-size:7px;vertical-align:top;">
							NOT GOOD FOR PASSAGE
						</td>
					</tr>
				</table>
			</span>
			</p>
			</td>
			<td>
				<p style="text-align: center;"><span style="font-size: 7px;">
					X/O
				</p>
			</td>

		</tr>
		<tr style="height:0.8cm;" >
			<td>
				<table width="100%">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;"><b>20</b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td><b>OK</b></td>
			<td><b><?php echo $parvaz->saat; ?></b></td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b><?php echo perToEnNums(jdate("m/d",strtotime($parvaz->tarikh))); ?></b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo date("d M",strtotime($parvaz->tarikh)); ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td colspan="2">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<b>Y</b>
						</td>
						<td style="border-right-style:solid;">
							<b><?php echo $parvaz->shomare; ?></b>
						</td>
					</tr>
				</table>
			</td>
			<td><b><?php echo loadSherkat($parvaz->sherkat_id); ?></b></td>
			<td >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			از:
						</td>
						<td style="direction:ltr;text-align:left" >
			from:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->mabda_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr align="center" style="height:0.8cm;" >
			<td>
				<table width="100%">
					<tr>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
						<td style="text-align: left;font-size: 6px;">
						PCS
						<br/>
						تعداد
						</td>
					</tr>
					<tr>
						<td style="text-align: right;font-size: 6px;">
						وزن
							<br/>
							WT
						</td>
						<td style="text-align: left;font-size: 6px;">
						&nbsp;
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="2">
&nbsp;
			</td>
			<td colspan="2">
&nbsp;
			</td>
			<td>&nbsp;</td>
			<td >
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b><?php echo loadCity($parvaz->maghsad_id); ?></b>
						</td>
					</tr>
				</table>	
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr style="height:0.8cm;" >
			<td style="text-align: center;" colspan="8" rowspan="3">
				<?php
					$b = new barcode_class(ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri));
					echo '<img src="../img/barcodes/'.ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri).'.png" alt="'.ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri).'" />';
				?>
			</td>
			<td colspan="4">
				<table style="text-align: center;font-size: 6px;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;" >
			کد گروه:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;" >
			TOUR CODE:
						</td>
					</tr>
				</table>
			</td>
			<td>
				<table style="text-align: center;font-size: 6px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			به:
						</td>
						<td style="direction:ltr;text-align:left" >
			to:
						</td>
					</tr>
					<tr>
						<td colspan='2' style="font-size: 12px;" >
							<b>VOID</b>
						</td>
					</tr>
				</table>	
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table style="text-align: center;font-size: 6px;width:100%;height:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right;vertical-align:top;" >
			طرز پرداخت:
						</td>
						<td style="direction:ltr;text-align:left;vertical-align:top;" >
			FORM OF PAYMENT:
						</td>
					</tr>
					<tr>
						<td colspan="2" align="center" style="font-size: 12px;">
							<b>CASH</b>
						</td>
					</tr>
				</table>
			</td>
			<td>
				<p align="left">
				<span style="font-size:10px;text-align:left;"><b>TOTAL FARE </b></span>
				<br/>
				</p>
				<p align="center">
				----
				</p>
			</td>
			<td>
			&nbsp;
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="5">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		رفرنس
					</td>
					<td style="direction:ltr;text-align:left" >
		PNR
					</td>
				</tr>
				<tr>
					<td colspan='2' style="font-size: 12px;" >
					<b><?php echo ticket_class::rahgiriToCode($ticket->shomare,conf::rahgiri); ?></b>					
					</td>
				</tr>
			</table>
		</td>
		<td>
			&nbsp;
		</td>
		</tr>
		</tbody>
		<br/>
		<table style="font-family:b zar,zar,tahoma;font-size:14px;direction:rtl;">
			<tr>
				<td>
					
*کلیه پروازهای ایران ایر ، ایرتور و آتا از مبدا تهران از ترمینال ۲ فرودگاه مهرآباد انجام می‌شود.
				</td>
			</tr>
			<tr>
				<td>
*پرواز چارتر می‌باشد.
				</td>
			</tr>
			<tr>
				<td>
*حضور ۲ ساعت قبل از پرواز در فرودگاه الزامی می‌باشد.
				</td>
			</tr>
			<tr>
				<td>
					* حمل و نقل مزبور مشمول مقررات مربوط به مسئولیت مقرر در کنوانسیون ورشو ۱۹۲۹ می‌باشد. جهت کسب اطلاعات بیشتر به http://www.radanseir.com مراجعه کنید.
				</td>
			</tr>
		</table>

<?php
	}
?>
</div>
<!--------------فیش آژانسها -->
<div align="center" style='width:21cm;height:29.5cm;display:none;'>
<?php 
		if($parvaz2!=null)
		{
?>	
				<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2">
		<tbody>
		<tr>
		<td rowspan="3">
		<b>   <?php echo $customer->name;  ?></b>
		<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
		</td>
		<td colspan="2">
		<b>
		کوپن خریدار اول
		</b>
		</td>
		<td><span style="font-size: 7px;">ORIGIN/DESTINATION</span></td>
		<td style="text-align: left;" colspan="9"><span style="font-size: 7px;">PASSENGER<br>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK</span></td>
		</tr>
		<tr>
		<td colspan="12" style="text-align:left;" ><span style="font-size: 7px;">TICKET NUMBER:</span><b><?php echo (int)$_REQUEST["shomare"]; ?></b></td>
		</tr>
		<tr>
		<td><b><?php echo loadAdl($ticket->adult); ?></b></td>
		<td style="text-align: center;font-size:13px;" colspan="11"><b><?php echo $ticket->fname." ".$ticket->lname; ?></b></td>

		</tr>
		<tr>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">بار کنترل شده        BAGGAGE<br />
		بار کنترل نشده        CK/UNCK</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">ALLOW<br>بار مجاز</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br>AFTER<br>فاقد اعتباربعد از</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">FARE BASIS<br>مبنای نرخ</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">STATUS<br>وضعیت</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">TIME<br>زمان</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">DATE<br>شمسی / میلادی</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">FLIGHT/CLASS<br>کلاس پرواز</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">CARRIER<br>حمل کننده</span></p>
		</td>
		<td colspan="3"><span style="font-size: 7px;">برای مسافرت معتبر نیست</span><br /></td>
		</tr>
		<tr>
		<td><span style="font-size: 7px;">pcs/تعداد</span><br /></td>
		<td style="text-align: center;">۲۰</td>
		<td>--</td>
		<td>--</td>
		<td><b>OK</b></td>
		<td><b><?php echo enToPerNums($parvaz->saat); ?></b></td>
		<td><b><?php echo hamed_pdate1($parvaz->tarikh); ?></b></td>
		<td><b><?php echo enToPerNums($parvaz->shomare); ?></b></td>
		<td style="text-align: center;"><b><?php echo loadSherkat($parvaz->sherkat_id); ?></b></td>
		<td style="text-align: center;" colspan="3"><span style="font-size: 7px;">از /from</span><br><b><?php echo loadCity($parvaz->mabda_id); ?></b></td>
		</tr>
		<tr align="center" valign="bottom">
		<td><span style="font-size: 7px;">pcs/تعداد</span><br /></td>
		<td>۲۰</td>
		<td>--</td>
		<td>--</td>
		<td><b>OK</b></td>
		<td><b><?php echo enToPerNums($parvaz2->saat); ?></b></td>
		<td><b><?php echo enToPerNums(hamed_pdate1($parvaz2->tarikh)); ?></b></td>
		<td><b><?php echo enToPerNums($parvaz2->shomare); ?></b></td>
		<td><b><?php echo loadSherkat($parvaz2->sherkat_id); ?></b></td>
		<td colspan="3"><span style="font-size: 7px;">به /to</span><br><b><?php echo loadCity($parvaz->maghsad_id); ?></b></td>
		</tr>
		<tr>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>

		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td style="text-align: center;" colspan="3"><span style="font-size: 7px;">به /to</span><br><b><?php echo loadCity($parvaz2->maghsad_id); ?></b></td>
		</tr>
		<tr>
		<td style="text-align: center;" colspan="6" rowspan="2">اینجا محل تبلیغات شما است</td>
		<td colspan="4">
		<p style="text-align: center;"><span style="font-size: 7px;">طرز پرداخت/FORM OF PAYMENT</span><br/>CASH</p>
		</td>
		<td style="text-align: center;" colspan="2">TOTAL FARE</td>
		</tr>
		<tr>
		<td colspan="6">
		<p style="text-align: center;"><span style="font-size: 7px;">رفرنس                                                                                                                                                              PNR</span><br/><b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></p>
		</td>
		</tr>
		</tbody>
		</table>
		<table style="width:90%; height: 22%;" >
			<tr>
				<td>
				</td>
			</tr>
		</table>
		<center> ------------------------------------------------------------------------------------------</center>
		<br />
				<table style="border-style: solid; border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 22%;" border="2">
		<tbody>
		<tr>
		<td rowspan="3">
		<b>   <?php echo $customer->name;  ?></b>
		<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
		</td>
		<td colspan="2">
		<b>
		کوپن مدیریت
		</b>
		</td>
		<td><span style="font-size: 7px;">ORIGIN/DESTINATION</span></td>
		<td style="text-align: left;" colspan="9"><span style="font-size: 7px;">PASSENGER<br>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK</span></td>
		</tr>
		<tr>
		<td colspan="12" style="text-align:left;" ><span style="font-size: 7px;">TICKET NUMBER:</span><b><?php echo (int)$_REQUEST["shomare"]; ?></b></td>
		</tr>
		<tr>
		<td><b><?php echo loadAdl($ticket->adult); ?></b></td>
		<td style="text-align: center;font-size:13px;" colspan="11"><b><?php echo $ticket->fname." ".$ticket->lname; ?></b></td>

		</tr>
		<tr>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">بار کنترل شده        BAGGAGE<br />
		بار کنترل نشده        CK/UNCK</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">ALLOW<br>بار مجاز</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br>AFTER<br>فاقد اعتباربعد از</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">FARE BASIS<br>مبنای نرخ</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">STATUS<br>وضعیت</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">TIME<br>زمان</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">DATE<br>شمسی / میلادی</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">FLIGHT/CLASS<br>کلاس پرواز</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">CARRIER<br>حمل کننده</span></p>
		</td>
		<td colspan="3"><span style="font-size: 7px;">برای مسافرت معتبر نیست</span><br /></td>
		</tr>
		<tr>
		<td><span style="font-size: 7px;">pcs/تعداد</span><br /></td>
		<td style="text-align: center;">۲۰</td>
		<td>--</td>
		<td>--</td>
		<td><b>OK</b></td>
		<td><b><?php echo enToPerNums($parvaz->saat); ?></b></td>
		<td><b><?php echo hamed_pdate1($parvaz->tarikh); ?></b></td>
		<td><b><?php echo enToPerNums($parvaz->shomare); ?></b></td>
		<td style="text-align: center;"><b><?php echo loadSherkat($parvaz->sherkat_id); ?></b></td>
		<td style="text-align: center;" colspan="3"><span style="font-size: 7px;">از /from</span><br><b><?php echo loadCity($parvaz->mabda_id); ?></b></td>
		</tr>
		<tr align="center" valign="bottom">
		<td><span style="font-size: 7px;">pcs/تعداد</span><br /></td>
		<td>۲۰</td>
		<td>--</td>
		<td>--</td>
		<td><b>OK</b></td>
		<td><b><?php echo enToPerNums($parvaz2->saat); ?></b></td>
		<td><b><?php echo enToPerNums(hamed_pdate1($parvaz2->tarikh)); ?></b></td>
		<td><b><?php echo enToPerNums($parvaz2->shomare); ?></b></td>
		<td><b><?php echo loadSherkat($parvaz2->sherkat_id); ?></b></td>
		<td colspan="3"><span style="font-size: 7px;">به /to</span><br><b><?php echo loadCity($parvaz->maghsad_id); ?></b></td>
		</tr>
		<tr>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>

		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td style="text-align: center;" colspan="3"><span style="font-size: 7px;">به /to</span><br><b><?php echo loadCity($parvaz2->maghsad_id); ?></b></td>
		</tr>
		<tr>
		<td style="text-align: center;" colspan="6" rowspan="2">باراینجا محل تبلیغات شما است</td>
		<td colspan="4">
		<p style="text-align: center;"><span style="font-size: 7px;">طرز پرداخت/FORM OF PAYMENT</span><br/>CASH</p>
		</td>
		<td style="text-align: center;" colspan="2">TOTAL FARE</td>
		</tr>
		<tr>
		<td colspan="6">
		<p style="text-align: center;"><span style="font-size: 7px;">رفرنس                                                                                                                                                              PNR</span><br/><b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></p>
		</td>
		</tr>
		</tbody>
		</table>
<?php
		}
		else
		{
?>
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2">
		<tbody>
		<tr>
		<td rowspan="3">
		<b>   <?php echo $customer->name;  ?></b>
		<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
		</td>
		<td colspan="2">
		<b>
		کوپن خریداراول
		</b>
		</td>
		<td><span style="font-size: 7px;">ORIGIN/DESTINATION</span></td>
		<td style="text-align: left;" colspan="9"><span style="font-size: 7px;">PASSENGER<br>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK</span></td>
		</tr>
		<tr>
		<td colspan="12" style="text-align:left;" ><span style="font-size: 7px;">TICKET NUMBER:</span><b><?php echo (int)$_REQUEST["shomare"]; ?></b></td>
		</tr>
		<tr>
		<td><b><?php echo loadAdl($ticket->adult); ?></b></td>
		<td style="text-align: center;font-size:13px;" colspan="11"><b><?php echo $ticket->fname." ".$ticket->lname; ?></b></td>

		</tr>
		<tr>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">بار کنترل شده        BAGGAGE<br />
		بار کنترل نشده        CK/UNCK</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">ALLOW<br>بار مجاز</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br>AFTER<br>فاقد اعتباربعد از</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">FARE BASIS<br>مبنای نرخ</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">STATUS<br>وضعیت</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">TIME<br>زمان</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">DATE<br>شمسی / میلادی</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">FLIGHT/CLASS<br>کلاس پرواز</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">CARRIER<br>حمل کننده</span></p>
		</td>
		<td colspan="3"><span style="font-size: 7px;">برای مسافرت معتبر نیست</span><br /></td>
		</tr>
		<tr>
		<td><span style="font-size: 7px;">pcs/تعداد</span><br /></td>
		<td style="text-align: center;">۲۰</td>
		<td>--</td>
		<td>--</td>
		<td><b>OK</b></td>
		<td><b><?php echo enToPerNums($parvaz->saat); ?></b><br /></td>
		<td><b><?php echo hamed_pdate1($parvaz->tarikh); ?></b></td>
		<td><b><?php echo enToPerNums($parvaz->shomare); ?></b></td>
		<td style="text-align: center;"><b><?php echo loadSherkat($parvaz->sherkat_id); ?></b></td>
		<td style="text-align: center;" colspan="3"><span style="font-size: 7px;">از /from</span><br><b><?php echo loadCity($parvaz->mabda_id); ?></b></td>
		</tr>
		<tr align="center" valign="bottom">
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>---</td>
		<td>---</td>
		<td>---</td>
		<td>---</td>
		<td colspan="3"><span style="font-size: 7px;">به /to</span><br><b><?php echo loadCity($parvaz->maghsad_id); ?></b></td>
		</tr>
		<tr>
		<td colspan="6" ></td>


		<td colspan="3" >TOUR CODE/کدگروه</td>
		<td style="text-align: center;" colspan="3">VIOD</td>
		</tr>
		<tr>
		<td style="text-align: center;" colspan="6" rowspan="2">اینجا محل تبلیغات شما است</td>
		<td colspan="4">
		<p style="text-align: center;"><span style="font-size: 7px;">طرز پرداخت/FORM OF PAYMENT</span><br/>
		CASH</p>
		</td>
		<td style="text-align: center;" colspan="2">TOTAL FARE</td>
		</tr>
		<tr>
		<td colspan="6">
		<p style="text-align: center;"><span style="font-size: 7px;">رفرنس                                                                                                                                                              PNR</span><br><b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></p>
		</td>
		</tr>
		</tbody>
		</table>
		<br />
		<table style="width:90%; height: 22%;" >
			<tr>
				<td>
				</td>
			</tr>
		</table>
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2">
		<tbody>
		<tr>
		<td rowspan="3">
		<b>   <?php echo $customer->name;  ?></b>
		<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
		</td>
		<td colspan="2">
		<b>
		کوپن مدیریت
		</b>
		</td>
		<td><span style="font-size: 7px;">ORIGIN/DESTINATION</span></td>
		<td style="text-align: left;" colspan="9"><span style="font-size: 7px;">PASSENGER<br>AIR/GROUND<br/>SERVICES<br/>VOUCHER AND<br/>BAGGAGE CHECK</span></td>
		</tr>
		<tr>
		<td colspan="12" style="text-align:left;" ><span style="font-size: 7px;">TICKET NUMBER:</span><b><?php echo (int)$_REQUEST["shomare"]; ?></b></td>
		</tr>
		<tr>
		<td><b><?php echo loadAdl($ticket->adult); ?></b></td>
		<td style="text-align: center;font-size:13px;" colspan="11"><b><?php echo $ticket->fname." ".$ticket->lname; ?></b></td>

		</tr>
		<tr>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">بار کنترل شده        BAGGAGE<br />
		بار کنترل نشده        CK/UNCK</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">ALLOW<br>بار مجاز</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">NOT VALID<br>AFTER<br>فاقد اعتباربعد از</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">FARE BASIS<br>مبنای نرخ</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">STATUS<br>وضعیت</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">TIME<br>زمان</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">DATE<br>شمسی / میلادی</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">FLIGHT/CLASS<br>کلاس پرواز</span></p>
		</td>
		<td>
		<p style="text-align: center;"><span style="font-size: 7px;">CARRIER<br>حمل کننده</span></p>
		</td>
		<td colspan="3"><span style="font-size: 7px;">برای مسافرت معتبر نیست</span><br /></td>
		</tr>
		<tr>
		<td><span style="font-size: 7px;">pcs/تعداد</span><br /></td>
		<td style="text-align: center;">۲۰</td>
		<td>--</td>
		<td>--</td>
		<td><b>OK</b></td>
		<td><b><?php echo enToPerNums($parvaz->saat); ?></b><br /></td>
		<td><b><?php echo hamed_pdate1($parvaz->tarikh); ?></b></td>
		<td><b><?php echo enToPerNums($parvaz->shomare); ?></b></td>
		<td style="text-align: center;"><b><?php echo loadSherkat($parvaz->sherkat_id); ?></b></td>
		<td style="text-align: center;" colspan="3"><span style="font-size: 7px;">از /from</span><br><b><?php echo loadCity($parvaz->mabda_id); ?></b></td>
		</tr>
		<tr align="center" valign="bottom">
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>--</td>
		<td>---</td>
		<td>---</td>
		<td>---</td>
		<td>---</td>
		<td colspan="3"><span style="font-size: 7px;">به /to</span><br><b><?php echo loadCity($parvaz->maghsad_id); ?></b></td>
		</tr>
		<tr>
		<td colspan="6" ></td>


		<td colspan="3" >TOUR CODE/کدگروه</td>
		<td style="text-align: center;" colspan="3">VIOD</td>
		</tr>
		<tr>
		<td style="text-align: center;" colspan="6" rowspan="2">اینجا محل تبلیغات شما است</td>
		<td colspan="4">
		<p style="text-align: center;"><span style="font-size: 7px;">طرز پرداخت/FORM OF PAYMENT</span><br/>
		CASH</p>
		</td>
		<td style="text-align: center;" colspan="2">TOTAL FARE</td>
		</tr>
		<tr>
		<td colspan="6">
		<p style="text-align: center;"><span style="font-size: 7px;">رفرنس                                                                                                                                                              PNR</span><br><b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></p>
		</td>
		</tr>
		</tbody>
		</table>		
<?php
		}
?>
</div>
	<?php if(isset($_REQUEST['print'])) { ?>
	<script>
		window.print();
	</script>
	<?php } ?>
</body>
</html>
