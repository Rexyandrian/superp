<?php
	include_once "../kernel.php";
	
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
		<table style="border-style: solid;border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 7cm;" border="2" >
		<tbody>
		<tr>
			<td rowspan="3" colspan="3" style="width:4cm;height:3cm;" >
			<b>   <?php echo $customer->name;  ?></b>
			<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
			</td>
			<td colspan="3" style="height:0.8cm;font-size:7px;"  >
			<b>
			ROUTE 1<br />
		مسیر ۱
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
								<img src='../img/arm_amr.jpg' width="20px" ><br/>
								امیرتوس
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
				
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></span>
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
						<td colspan='2'  style="font-size: 12px;" >
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
<center>---------------------------------------------------------------------------------------------------</center>
<br/>
<?php 
		if($parvaz2==null)
		{
?>
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
								<img src='../img/arm_amr.jpg' width="20px" ><br/>
								امیرتوس
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
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></span>
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
<center>---------------------------------------------------------------------------------------------------</center>
<br/>
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
								<img src='../img/arm_amr.jpg' width="20px" ><br/>
								امیرتوس
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
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></span>
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
<?php 
	}
	else
	{
?>
<!--           پرواز یک طرفه باشد      -->
</table>
		<table style="width:90%; height: 7cm;" >
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
								<img src='../img/arm_amr.jpg' width="20px" ><br/>
								امیرتوس
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
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></span>
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
</div>
<?php
	}
?>
<!--------------فیش آژانسها -->
<div align="center" style='width:21cm;height:27cm;'>
<?php 
		if($parvaz2!=null)
		{
?>	
	<br/><br/><br/><br/><br/><br/><br/><br/>	
	<table style="border-style: solid; border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 10cm;" border="2">
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
			<br />
		کوپن مشتری
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
								<img src='../img/arm_amr.jpg' width="20px" ><br/>
								امیرتوس
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
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></span>
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
			<td style="text-align: center;" colspan="1" rowspan="2">محاسبات</td>
			<td colspan="7">
			<p style="text-align: center;"><span style="font-size: 7px;">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			مبلغ خریداری شده:
						</td>
						<td style="direction:ltr;text-align:left" >
			&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan='3' style="font-size: 12px;" ><b>				
							<?php echo monize($ticket->mablagh); ?>
											</b>						
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;" colspan="6">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			مبلغ کمیسیون :
						</td>
						<td style="direction:ltr;text-align:left" >
			&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan='2' style="text-align: center;font-weight:bold;font-size: 12px;width:100%;" >
							<?php echo monize($ticket->mablagh * ($ticket->poorsant/100)); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="12">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		مبلغ قابل پرداخت :
					</td>
					<td style="direction:ltr;text-align:left" >
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan='2' style="text-align:center;font-weight:bold;font-size: 12px;width:100%;" >
					<?php echo monize($ticket->mablagh * (1-$ticket->poorsant/100)) ; ?>						
					</td>
				</tr>
			</table>
		</td>
		</tr>
		</tbody>
		</table>
		<table style="width:90%; height: 7%;" >
			<tr>
				<td>
		-------------------------------------------------------------------
				</td>
			</tr>
		</table>
		<br />
				<table style="border-style: solid; border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 10cm;" border="2">
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
			<br />
		کوپن مدیریت
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
								<img src='../img/arm_amr.jpg' width="20px" ><br/>
								امیرتوس
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
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></span>
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
			<td style="text-align: center;" colspan="1" rowspan="2">محاسبات</td>
			<td colspan="7">
			<p style="text-align: center;"><span style="font-size: 7px;">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			مبلغ خریداری شده:
						</td>
						<td style="direction:ltr;text-align:left" >
			&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan='3' style="font-size: 12px;" ><b>				
							<?php echo monize($ticket->mablagh); ?>
											</b>						
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;" colspan="6">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			مبلغ کمیسیون :
						</td>
						<td style="direction:ltr;text-align:left" >
			&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan='2' style="text-align: center;font-weight:bold;font-size: 12px;width:100%;" >
							<?php echo monize($ticket->mablagh * ($ticket->poorsant/100)); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="12">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		مبلغ قابل پرداخت :
					</td>
					<td style="direction:ltr;text-align:left" >
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan='2' style="text-align:center;font-weight:bold;font-size: 12px;width:100%;" >
					<?php echo monize($ticket->mablagh * (1-$ticket->poorsant/100)) ; ?>						
					</td>
				</tr>
			</table>
		</td>
		</tr>
		</tbody>
		</table>
<?php
		}
		else
		{
?>	
		<table style="border-style: solid; border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 10cm;" border="2">
		<tbody>
		<tr>
			<td rowspan="3" colspan="3" style="width:4cm;height:3cm;" >
			<b>   <?php echo $customer->name;  ?></b>
			<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
			</td>
			<td colspan="2" style="height:0.8cm;font-size: 7px;"  >
			<b>
			<br />
		کوپن مشتری
			</b>
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="8">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width="20px" >
									<br/>گستره‌ارتباطات‌شرق
						</td>
						<td>
								<img src='../img/arm_amr.jpg' width="20px" ><br/>
								امیرتوس
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
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></span>
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
			<td style="text-align: center;" colspan="1" rowspan="2">محاسبات</td>
			<td colspan="7">
			<p style="text-align: center;"><span style="font-size: 7px;">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			مبلغ خریداری شده:
						</td>
						<td style="direction:ltr;text-align:left" >
			&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan='3' style="font-size: 12px;" ><b>				
							<?php echo monize($ticket->mablagh); ?>
											</b>						
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;" colspan="6">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			مبلغ کمیسیون :
						</td>
						<td style="direction:ltr;text-align:left" >
			&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan='2' style="text-align: center;font-weight:bold;font-size: 12px;width:100%;" >
							<?php echo monize($ticket->mablagh * ($ticket->poorsant/100)); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="12">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		مبلغ قابل پرداخت :
					</td>
					<td style="direction:ltr;text-align:left" >
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan='2' style="text-align:center;font-weight:bold;font-size: 12px;width:100%;" >
					<?php echo monize($ticket->mablagh * (1-$ticket->poorsant/100)) ; ?>						
					</td>
				</tr>
			</table>
		</td>
		</tr>
		</tbody>
		</table>
		<table style="width:90%; height: 7%;" >
			<tr>
				<td>
		------------------------------------------------------------------------------------------
				</td>
			</tr>
		</table>
			<table style="border-style: solid; border-collapse: collapse; font-size: 12px; direction: rtl; width:90%; height: 10cm;" border="2">
		<tbody>
		<tr>
			<td rowspan="3" colspan="3" style="width:4cm;height:3cm;" >
			<b>   <?php echo $customer->name;  ?></b>
			<br>
			<?php echo hamed_pdate1($ticket->regtime); ?>
			<br> <?php echo loadUser($ticket->user_id); ?>
			</td>
			<td colspan="2" style="height:0.8cm;font-size: 7px;"  >
			<b>
			<br />
		کوپن مدیریت
			</b>
			</td>
			<td style="text-align: left;height:0.8cm;" colspan="8">
				<table style="font-size: 6px;width:100%;">
					<tr>
						<td>
								<img src='../img/arm_gcom.png' width="20px" >
									<br/>گستره‌ارتباطات‌شرق
						</td>
						<td>
								<img src='../img/arm_amr.jpg' width="20px" ><br/>
								امیرتوس
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
				
				<b><?php echo ticket_class::rahgiriToCode($ticket->sanad_record_id,conf::rahgiri); ?></b></span>
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
			<td style="text-align: center;" colspan="1" rowspan="2">محاسبات</td>
			<td colspan="7">
			<p style="text-align: center;"><span style="font-size: 7px;">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			مبلغ خریداری شده:
						</td>
						<td style="direction:ltr;text-align:left" >
			&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan='3' style="font-size: 12px;" ><b>				
							<?php echo monize($ticket->mablagh); ?>
											</b>						
						</td>
					</tr>
				</table>
			</td>
			<td style="text-align: center;" colspan="6">
				<table style="text-align: center;font-size: 7px;width:100%;" >
					<tr>
						<td style="direction:rtl;text-align:right" >
			مبلغ کمیسیون :
						</td>
						<td style="direction:ltr;text-align:left" >
			&nbsp;
						</td>
					</tr>
					<tr>
						<td colspan='2' style="text-align: center;font-weight:bold;font-size: 12px;width:100%;" >
							<?php echo monize($ticket->mablagh * ($ticket->poorsant/100)); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr style="height:0.7cm;" >
		<td colspan="12">
			<table style="text-align: center;font-size: 6px;width:100%;" >
				<tr>
					<td style="direction:rtl;text-align:right" >
		مبلغ قابل پرداخت :
					</td>
					<td style="direction:ltr;text-align:left" >
						&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan='2' style="text-align:center;font-weight:bold;font-size: 12px;width:100%;" >
					<?php echo monize($ticket->mablagh * (1-$ticket->poorsant/100)) ; ?>						
					</td>
				</tr>
			</table>
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
