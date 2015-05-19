<?php
	session_start();
	include_once ("../kernel.php");
	function flightZarfiat($parvaz)
	{
		$out = 0;
		$customer_typ = (int)$_SESSION["customer_typ"];
		if($customer_typ != 2 && $parvaz->getZarfiat($_SESSION['customer_id'])>=9)
			$out = 9;
		else if($customer_typ != 2)
			$out = $parvaz->getZarfiat($_SESSION['customer_id']);
		else if($customer_typ == 2)
			$out = $parvaz->getZarfiat();
		return($out);
	}
	function bargashtHast($selectedParvaz,$parvaz)
	{
		$out = TRUE;
		if($parvaz->j_id >0)
		{
			$out = FALSE;
			foreach($selectedParvaz as $tmp)
			{				
				if($tmp->mabda_id == $parvaz->maghsad_id && $parvaz->mabda_id = $tmp->maghsad_id)
					$out = TRUE;
			}
		}
		return($out);
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
	function loadMabda($inp)
	{
		$inp = (int)$inp;
		$out = "";
		$par = new parvaz_det_class($inp);
		return(loadCity($par->mabda_id));
	}
	function loadMaghsad($inp)
	{
		$inp = (int)$inp;
		$out = "";
		$par = new parvaz_det_class($inp);
		return(loadCity($par->maghsad_id));
	}
	function loadShomare($inp)
	{
		$inp = (int)$inp;
		$out = "";
		$par = new parvaz_det_class($inp);
		return(enToPerNums($par->shomare));
	}
	function loadSherkatName($inp)
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
	function loadSherkat($inp)
	{
		$inp = (int)$inp;
		$out = "";
		$par = new parvaz_det_class($inp);
		return(loadSherkatName($par->sherkat_id));
	}
	
	function hamed_pdate($str)
        {
                $out=jdate('l Y/n/j',strtotime($str));
                return enToPerNums($out);
        }
	function saat($inp)
	{
		$inp = substr($inp,0,-3);
		return enToPerNums($inp);
	}
	function zarfiat($inp)
	{
		if($inp>9)
		{
			$inp = 9;
		}
  
		return enToPerNums($inp);
	}
	function poorsant($inp)
	{
		$par = new parvaz_det_class((int)$inp);
		$customer_id = $_SESSION["customer_id"];
		$cust = new customer_class($customer_id);
		$out = ($cust->getPoorsant($inp)* ($par->ghimat) /100 );
		return enToPerNums(monize($out));
	}
	function check_raft_bargasht($p1,$p2)
	{
		$out = FALSE;
		$p1 = new parvaz_det_class($p1);
		$p2 = new parvaz_det_class($p2);
		if( $p1->mabda_id==$p2->maghsad_id )
		{
			$out = TRUE;
		}
		return $out;
	}
	if(!isset($_SESSION["user_id"]) || !isset($_REQUEST["adl"]))
		die("<script>window.opener.location = window.opener.location; window.close();</script>");
	if((int)$_REQUEST["adl"] <= 0 )
		die("<script>alert('حداقل یک بزرگسال باید انتخاب شود');window.parent.location = window.parent.location;window.close();</script>");
	$selected_parvaz = $_REQUEST["selected_parvaz"];
        $tmp = explode(",",$selected_parvaz);
	if(count($tmp)>2)
	{
		die("<script>alert('حداکثر دو پرواز می توانید انتخاب کنید');window.parent.location = window.parent.location;window.close();</script>");	
	}
	else if(count($tmp)==2)
	{
		if (!check_raft_bargasht($tmp[0],$tmp[1]))
		die("<script>alert('پروازهای انتخابی باید رفت وبرگشت باشد');window.parent.location = window.parent.location;window.close();</script>");	
	}
        $adl = abs((int)$_REQUEST["adl"]);
        $chd = abs((int)$_REQUEST["chd"]);
        $inf = abs((int)$_REQUEST["inf"]);
        $ticket_type = (int)$_REQUEST["ticket_type"];
	foreach($tmp as $parvaz_id)
        {
        	$tmp_parvaz = new parvaz_det_class((int)$parvaz_id);
                $selectedParvaz[] = $tmp_parvaz;
        }
	$customer = new customer_class((int)$_SESSION["customer_id"]);
	$customer_typ = (int)$_SESSION["customer_typ"];
	$tedad = $adl + $chd;
	$jam_ghimat = 0;
	$tedad_ok = TRUE;
        foreach($tmp as $parvaz_id)
        {
	        $tmp_parvaz = new parvaz_det_class((int)$parvaz_id);
                if(flightZarfiat($tmp_parvaz) < $tedad)
        	        $tedad_ok = FALSE;
                $jam_ghimat += ($tedad * $tmp_parvaz->ghimat);
                $jam_ghimat += ($inf * $tmp_parvaz->ghimat)/10;
        }
	if(!isset($_REQUEST["mod"]))
	{
		$paravaz_tedad  = ((count($tmp)>0)?TRUE:FALSE);
		$domasire_ok = TRUE;
		if(count($tmp) == 1 && (int)$tmp[0] <=0)
			$paravaz_tedad = FALSE;
		foreach($selectedParvaz as $tmp)
		{
			$domasire_ok = bargashtHast($selectedParvaz,$tmp) and $domasire_ok;
		}
		$customer_etebar_ok = TRUE;
		$customer_tedad_ok = TRUE;
		$customer_shomare_ok = TRUE;
		if($customer_typ != 2 && $customer->max_amount < $jam_ghimat )
			$customer_etebar_ok = FALSE;
		if(($customer->max_ticket + 1 - $customer->min_ticket)< $tedad)
			$customer_tedad_ok = FALSE;
		if($ticket_type == 1 && count($customer->ticket_numbers) < $tedad)
			$customer_shomare_ok = FALSE;
//---------Flight Selctetion Problems------
		$msg = "";	
		if(!$tedad_ok)
			$msg = "Zarfiate parvaz kam ast";
		if(!$paravaz_tedad)
			$msg .= " parvaz entekhab nashode";
		if(!$domasire_ok && $customer_typ != 2)
			$msg .= " paravz domasire dorost entekhab nashode";
//-----------------------------------------
//---------Customer Problems---------------
		if(!$customer_etebar_ok)
			$msg .= " اعتبار مشتری کافی نیست";
		if(!$customer_tedad_ok)
			$msg .= " سقف تعداد خرید مشتری کافی نیست";
		if(!$customer_shomare_ok)
			$msg .= " تعداد شماره تیکت مشتری کافی نیست";
		$out = "";
		$adults = "";
		$childs = "";
		$infants = "";
		if($msg == "")
		{
			//Craete Verify Here
			$where= "1=0 ";
			
			//var_dump($selectedParvaz);
			$jam_kol = 0;
			$jam_kom = 0;
			for($i =0 ;$i<count($selectedParvaz);$i++)
			{
				$where .="or `id`='".$selectedParvaz[$i]->getId()."'"; 
				$jam_kom +=($adl+$chd+$inf/10)* ($selectedParvaz[$i]->ghimat *$customer->getPoorsant($selectedParvaz[$i]->getId()) /100);
				$jam_kol+=($adl+$chd+$inf/10)* ($selectedParvaz[$i]->ghimat);
			}
			$grid = new jshowGrid_new("parvaz_det","grid1");
			$grid->index_width = '30px';
			$grid->width = '820px';
			$grid->whereClause = $where;
			$grid->columnHeaders[1] = null;
			$grid->columnHeaders[0]="شماره";
			$grid->columnFunctions[0] ="loadShomare";
			$grid->columnHeaders[2]="تاریخ";
			$grid->columnFunctions[2] = "hamed_pdate";
			$grid->columnHeaders[3]="ساعت";
			$grid->columnFunctions[3] = "saat";
			$grid->columnHeaders[4]=null;
			$grid->columnHeaders[5]="قیمت بلیت"."(ریال)";
			$grid->columnFunctions[5] = "monize";
			$grid->columnHeaders[6] = null;
			$grid->columnHeaders[7] = null;
			$grid->columnHeaders[8] = null;
			$grid->columnHeaders[9] = null;
			$grid->columnHeaders[10] = null;
			$grid->columnHeaders[11] = null;
			$grid->columnHeaders[12] = null;
			$grid->addFeild("id");
			$grid->columnHeaders[13] = "کمیسیون(ریال)";
			$grid->columnFunctions[13] = "poorsant";
			$grid->addFeild("id",2);
			$grid->columnHeaders[2]="هواپیمایی";
			$grid->columnFunctions[2] ="loadSherkat";
			$grid->addFeild("id",2);
			$grid->columnHeaders[2]="مقصد";
			$grid->columnFunctions[2] ="loadMaghsad";
			$grid->addFeild("id",2);
			$grid->columnHeaders[2]="مبدأ";
			$grid->columnFunctions[2] ="loadMabda";
			$grid->canAdd = FALSE;
			$grid->canDelete = FALSE;
			$grid->canEdit = FALSE;			
			$grid->intial();
			$grid->executeQuery();
			$out = $grid->getGrid();
		}
	}
//-----------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo lang_fa_class::title; ?></title>
    <link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
	<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link type="text/css" href="../css/style.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" media="all" href="../css/skins/aqua/theme.css" title="Aqua" />
	<style type="text/css">
		.calendar {
			direction: rtl;
		}
	
		#flat_calendar_1, #flat_calendar_2{
			width: 200px;
		}
		.example {
			padding: 10px;
		}
	
		.display_area {
			background-color: #FFFF88
		}
	</style>
	<script type="text/javascript" src="../js/tavanir.js"></script>
	<script type="text/javascript" src="../js/jalali.js"></script>
	<script type="text/javascript" src="../js/calendar.js"></script>
	<script type="text/javascript" src="../js/calendar-setup.js"></script>
	<script type="text/javascript" src="../js/lang/calendar-fa.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
	<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script> 
</head>
<body>
	<div align="center" >
		<?php
			echo (($msg != "")?"<script>alert('$msg');window.parent.location = window.parent.location;</script>":$out);
		?>
		<table style="background-color:#F6D5A2;font-weight:bold;" border='0'>
			<tr style="background-color:#FFA858;" >
				<td style="width:80px;" align="left" >
					تعدادبزرگسال:
				</td>
				<td  style="width:60px;"  >
					<?php echo "<input readonly='readonly' style='width:30px;font-family:tahoma,Tahoma;' value='".enToPerNums($adl)."' >" ?>
				</td>
				<td style="width:60px;" align="left" >
					تعدادکودک:
				</td  >
				<td style="width:60px;" >
					<?php echo "<input readonly='readonly' style='width:30px;font-family:tahoma,Tahoma;' value='".monize($chd)."' >" ?>
				</td>
				<td style="width:60px;" align="left" >
					تعدادنوزاد:
				</td>
				<td style="width:60px;" >
					<?php echo "<input readonly='readonly' style='width:30px;font-family:tahoma,Tahoma;' value='".monize($inf)."' >" ?>
				</td>
				<td style="width:60px;" align="left" >
					تعدادکل:
				</td>
				<td style="width:60px;" >
					<?php echo "<input readonly='readonly' style='width:30px;font-family:tahoma,Tahoma;' value='".monize($chd+$adl)."' >" ?>
				</td>
			</tr>
			<tr>
				<td colspan='15' >&nbsp;</td>
			</tr>
			<tr>
				<td colspan='15' >&nbsp;</td>
			</tr>
			<tr>
				<td colspan='15' >&nbsp;</td>
			</tr>
			<tr style="background-color:#FF7040;" >
				<td style="width:100px;" align="left" >
					کمیسیون:
				</td>
				<td  style="width:100px;"  >
					<?php echo "<input readonly='readonly' style='width:150px;font-family:tahoma,Tahoma;' value='".monize($jam_kom)." ریال ' >" ?>
				</td>
				<td style="width:100px;" align="left" >
					مبلغ کل بلیت:
				</td  >
				<td style="width:100px;" >
					<?php echo "<input readonly='readonly' style='width:150px;font-family:tahoma,Tahoma;' value='".monize($jam_kol)." ریال' >" ?>
				</td>
				<td style="width:100px;" align="left" >
					مبلغ قابل پرداخت:
				</td>
				<td style="width:200px;" colspan='3' >
					<?php $jam_pardakhti= $jam_kol-$jam_kom; echo "<input readonly='readonly' style='width:150px;font-family:tahoma,Tahoma;' value='".monize($jam_pardakhti)." ریال ' >" ?>
				</td>
			</tr>
			<tr>
				<td colspan='6' style='color:red;' >
					<br>
					<p>لطفا اطلاعات فوق را کنترل کرده و سپس کلید تایید را کلیک نمایید
توجه داشته باشد پس از تایید اطلاعات فوق جهت ورود اسامی 5 دقیقه فرصت دارید</p>
				</td>
			</tr>
		</table>
		<br>
		<form id="blit_data" action="checkflight.php" >
			<input type="hidden" name="adl" value="<?php echo $adl; ?>" />
		        <input type="hidden" name="chd" value="<?php echo $chd; ?>" />
		        <input type="hidden" name="inf" value="<?php echo $inf; ?>" />
		        <input type="hidden" name="ticket_type" value="<?php echo $ticket_type; ?>" />
		        <input type="hidden" name="selected_parvaz" value="<?php echo $selected_parvaz; ?>" />
			<?php 
				if($_SESSION['customer_typ']!=2 && $ticket_type==0)
					echo 'رمز بلیت الکترونیک-eticket:<input type="text" name="epass" class="inp"  />';
			?>
			<input type="submit" value="ثبت جهت رزرو موقت" class="inp1" style="width:auto" >
		</form>
	</div>
</body>
</html>
