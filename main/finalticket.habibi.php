<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	if (!(isset($_REQUEST["sanad_record_id"]) ) )
		die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
	function loadAdl($inp)
	{
		$ar=array(0=>"بزرگسال",1=>"کودک",2=>"نوزاد");
		return $ar[$inp];
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
        function hamed_pdate($str)
        {
                $out=jdate('l d / m / Y',strtotime($str));
                return enToPerNums($out);
        }
	function loadParvazInfo($inp)
	{
		$out = "&nbsp;";
		$inp = (int)$inp;
		$parvaz = new parvaz_det_class($inp);
		if($parvaz->getId() >0)
		{
			$out = $parvaz->shomare."( ".loadCity($parvaz->mabda_id)." -> ".loadCity($parvaz->maghsad_id)." )<br/>".hamed_pdate($parvaz->tarikh);
		}
		return($out);
	}
	function loadPrint($inp)
	{
		$inp = (int)$inp;
		$ticket = new ticket_class($inp);
		$id = $ticket->getId();
		if($ticket->typ == 0)
			$out = "<u><span style='cursor:pointer;color:blue;' onclick=\"wopen('eticket.php?print=1&shomare=".$ticket->shomare."&id=$inp&','',900,600)\" > ".$ticket->shomare." <br> چاپ </span></u>";
		else
			$out = "<span style='cursor:pointer;color:blue;' > ".$ticket->shomare."</span>";
		return $out;
	}
	function loadBargasht($inp)
	{
		$out = "&nbsp;";
                $inp = (int)$inp;
		$ticket = new ticket_class($inp);
//`id`, `fname`, `lname`, `tel`, `adult`, `sanad_record_id`, `parvaz_det_id`, `customer_id`, `user_id`, `shomare`, `typ`, `en`, `regtime`, `mablagh`, `poorsant`
		mysql_class::ex_sql("select `parvaz_det_id` from `ticket` where `parvaz_det_id` <> '".$ticket->parvaz_det_id."' and `shomare` = '".$ticket->shomare."' and `regtime`>='".$ticket->regtime."' and `regtime`<DATE_ADD('".$ticket->regtime."',interval 1 minute) ",$q);
		if($r = mysql_fetch_array($q))
		{
			$parvaz = new parvaz_det_class((int)$r["parvaz_det_id"]);
			$out = $parvaz->shomare."( ".loadCity($parvaz->mabda_id)." -> ".loadCity($parvaz->maghsad_id)." )<br/>".hamed_pdate($parvaz->tarikh);
		}
		return($out);	
	}
	function rahgiri($inp)
	{
		$inp = (int) $inp;
		$inp = ticket_class::rahgiriToCode($inp,conf::rahgiri);
		return $inp;
	}
	function loadGender($inp)
	{
		$inp = (int)$inp;
		if($inp == 1)
			return('مرد');
		else
			return('زن');
	}
	$sanad_recoed_id = $_REQUEST["sanad_record_id"];
	$ticket_type = (int)$_REQUEST["ticket_type"];
	$grid = new jshowGrid_new("ticket","grid1");
	$grid->whereClause = "sanad_record_id=$sanad_recoed_id and en=1 group by `shomare`";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]=null;
	$grid->columnHeaders[2]="نام و نام خانوادگی";	
	$grid->columnHeaders[3]="تلفن";
	$grid->columnHeaders[4]="بزرگسال";
	$grid->columnFunctions[4] = "loadAdl";
	$grid->columnHeaders[5]="کد رهگیری";
	$grid->columnHeaders[6]="مشخصات پرواز رفت";
	$grid->columnFunctions[5] = "rahgiri";
	$grid->columnFunctions[6] = "loadParvazInfo";
	$grid->columnHeaders[7]=null;
	$grid->columnHeaders[8]=null;
        if($ticket_type==1)
        {
                $grid->columnHeaders[9]="شماره بلیت";
        }
        else
        {
		$grid->fieldList[9] = "id";
                $grid->columnHeaders[9] = 'شماره بلیت';
                $grid->columnFunctions[9] = "loadPrint";
        }
	$grid->columnHeaders[10]=null;
	$grid->columnHeaders[11]=null;
	$grid->columnHeaders[12]=null;
	$grid->columnHeaders[13]=null;
	$grid->columnHeaders[14]=null;
	$grid->columnHeaders[15]='جنسیت';
	$grid->columnFunctions[15] = 'loadGender';
	$grid->addFeild("id",7);
	$grid->columnHeaders[7]="مشخصات پرواز برگشت";
	$grid->columnFunctions[7] = "loadBargasht";
	$grid->canAdd = FALSE;
	$grid->canDelete = FALSE;
	$grid->canEdit = FALSE;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
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
		<script type="text/javascript" src="../js/jalali.js"></script>
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
			<?php echo $out;  ?>
			<input type='button' value='چاپ تمامی بلیت‌ها' class='inp' style='width:auto;' onclick="wopen('eticket_all.php?sanad_record_id=<?php echo $sanad_recoed_id; ?>','',800,600);" >
			<input type='button' value='بازگشت به صفحه اصلی' class='inp' onclick="window.location='index.php';" >
		</div>
	</body>
</html>
