<?php   session_start();
        include_once("../kernel.php");
	/*
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	*/
	function loadCustomer($customer_id)
	{
		$out = '<option value=\'-1\' ></option>';
		$cust_id = (int)$_SESSION[conf::app.'_customer_id'];
		$qu = "select `name`,`id` from `customers` where `id`='$cust_id'";
		if($_SESSION[conf::app.'_customer_typ']==2)
			$qu = 'select `name`,`id` from `customers` order by `name`';
		mysql_class::ex_sql($qu,$q);
		while($r=mysql_fetch_array($q))
		{
			$sel = '';
			if($customer_id ==(int)$r['id']) 
				$sel = 'selected=\'selected\'';
			$out.="<option value=".$r['id']." $sel >".$r['name']."</option>\n";
		}
		if($_SESSION[conf::app.'_customer_typ']==2) 
		{
			if($customer_id==-2)
				$sel = 'selected=\'selected\''; 
			$out .= "<option value='-2' $sel >همه</option>";
		}
		return $out;
	}
	function loadAdl($inp)
	{
		$ar=array(0=>'بزرگسال',1=>'کودک',2=>'نوزاد');
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
	function loadParvazInfo($inp)
	{
		$out = "&nbsp;";
		$inp = (int)$inp;
		$parvaz = new parvaz_det_class($inp);
		if($parvaz->getId() >0)
		{
			$out = $parvaz->shomare."( ".loadCity($parvaz->mabda_id)." -> ".loadCity($parvaz->maghsad_id)." )".jdate("j / m / Y",strtotime($parvaz->tarikh)).'<br />'.date("F d",strtotime($parvaz->tarikh)) ;
		}
		return($out);
	}
	function loadPrint($inp)
	{
		$out = (int)$inp;
		$tick = new ticket_class($out);
		if($tick->typ == 0)
			$out = "<u><span style='cursor:pointer;color:firebrick;' onclick=\"wopen('eticket.php?shomare=".$tick->shomare."&id=$out','',900,600)\" >".$tick->shomare."<br/> چاپ Eticket </span></u>";
		else
			$out = "<span 'color:firebrick;'>".$tick->shomare."</span>";
		//$out = $tick->typ;
		return $out;
	}
	function loadCustomerName()
	{
		$out = null;
		mysql_class::ex_sql('select `name`,`id` from `customers` order by `name`',$q);
		while($r = mysql_fetch_array($q))
		{
			$out[$r['name']] = (int)$r['id'];
		}
		return($out);
	}
	function loadMablagh($id)
	{
		$id = (int)$id;
		$tick = new ticket_class($id);
		$out = $tick->mablagh * (1 - $tick->poorsant/100 );
		return monize($out);
	}
	function hamed_pdate($str)
        {
                $out=jdate('H:i:s d / m / Y ',strtotime($str));
		$out .= "<br/>".date('F d',strtotime($str));
                return enToPerNums($out);
        }
	function rahgiri($inp)
	{
		$inp = ticket_class::rahgiriToCode((int)$inp,conf::rahgiri);
		return $inp;
	}
	$parvaz_det_id = ((isset($_REQUEST['parvaz_det_id']))?$_REQUEST["parvaz_det_id"]:-1);
	$wer = ' en<>-1 and `parvaz_det_id`='.$parvaz_det_id;
	$grid = new jshowGrid_new('ticket','girid1');
	$grid->index_width = "50px";
	$grid->whereClause = $wer;
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]=null;
	$grid->columnHeaders[2]="نام و نام خانوادگی";	
	$grid->columnHeaders[3]='توضیحات';
	$grid->columnHeaders[4]=null;
	$grid->columnFunctions[4] = "loadAdl";
	$grid->columnHeaders[5]="کد رهگیری";
	$grid->columnFunctions[5] = "rahgiri";
	$grid->columnHeaders[6]=null;
	//$grid->columnFunctions[6] = "loadParvazInfo";
	$grid->columnHeaders[7]="آژانس خریدار";
	$grid->columnLists[7] = loadCustomerName();
	$grid->columnHeaders[8]=null;
	$grid->columnHeaders[9]=null;
	$grid->columnHeaders[10]=null;
	$grid->columnHeaders[11]=null;
	$grid->columnHeaders[12]='تاریخ صدور';
	$grid->columnFunctions[12] = "hamed_pdate";
	$grid->columnHeaders[13]=null;
	$grid->columnHeaders[14]=null;
	$grid->addFeild('id',9);
	$grid->columnHeaders[9] = 'شماره بلیت';
	$grid->columnFunctions[9] = 'loadPrint';
	$grid->addFeild('id');
	$grid->columnHeaders[17]='قیمت<br>فروخته شده';
	$grid->columnFunctions[17] = "loadMablagh";
	$grid->width = '99%';
	$grid->columnHeaders[16]=null;
//	$grid->addFeild('id');
//	$grid->columnHeaders[15]="جزئیات";
	$grid->columnFunctions[9] = "loadPrint";
	for($j=0;$j < count($grid->columnHeaders);$j++)
		$grid->columnAccesses[$j] = 0;
	$grid->columnAccesses[2] = 1;
	$grid->columnAccesses[7] = 0;
	//$grid->columnAccesses[9] = 1;
	$grid->canAdd = FALSE;
	$grid->canEdit = TRUE;
	$grid->canDelete = FALSE;
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
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
		<title>
		سامانه مدیریت آژانس مسافرتی
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<h3>منیفست پرواز:</h3><br />
			<?php 	
				echo "<b>".loadParvazInfo($parvaz_det_id)." <u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('manifest2.php?parvaz_det_id=$parvaz_det_id','',900,500);\">چاپ منیفست</span></u></b><br /><br />"; 
				echo $out;
			?>
		</div>
	</body>
</html>
