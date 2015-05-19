<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$GLOBALS['sec'] = $se;
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return($out);
        }
	function loadCustomer($customer_id)
	{
		$out = '<option value=\'-1\' ></option>';
		$cust_id = (int)$_SESSION[conf::app.'_customer_id'];
		$qu = "select `name`,`id` from `customers` where `id`='$cust_id' and `en`=1 ";
		$se = $GLOBALS['sec'];
		if($se->detailAuth('all'))
			$qu = 'select `name`,`id` from `customers` where `en`=1 order by `name`';
		mysql_class::ex_sql($qu,$q);
		while($r=mysql_fetch_array($q))
		{
			$sel = '';
			if($customer_id ==(int)$r['id']) 
				$sel = 'selected=\'selected\'';
			$out.="<option value=\"".$r['id']."\" $sel >".$r['name']."</option>\n";
		}
		if($se->detailAuth('all')) 
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
			$out = $r["name"];
		return($out);
	}
	function loadSherkat($inp)
	{
		$inp = (int)$inp;
		$out = "";
		mysql_class::ex_sql("select `name` from `sherkat` where `id` = '$inp'",$q);
		if($r = mysql_fetch_array($q))
			$out = $r["name"];
		return($out);
	}
	function loadParvazInfo($inp)
	{
		$out = "&nbsp;";
		$inp = (int)$inp;
		$parvaz = new parvaz_det_class($inp);
		if($parvaz->getId() >0)
			$out = $parvaz->shomare."( ".loadCity($parvaz->mabda_id)." -> ".loadCity($parvaz->maghsad_id)." ) ".loadSherkat($parvaz->sherkat_id)." <br/>".hamed_pdate($parvaz->tarikh);
		return($out);
	}
	function loadPrint($inp)
	{
		$out = '';
		$inp = (int)$inp;
		mysql_class::ex_sql("select `shomare`,`typ` from ticket where en='1' and id=$inp",$qu);
		if($r=mysql_fetch_array($qu))
		{
			$shomare = $r["shomare"];
			if($r["typ"] == 0)			
				$out = "<u><span style='cursor:pointer;color:firebrick;' onclick=\"wopen('eticket.php?shomare=$shomare&id=$inp','',900,600)\" > چاپ Eticket </span></u>";
		}
		return $out;
	}
	function loadCustomer1($inp)
	{
		$inp = (int)$inp;
		$ticket = new ticket_class($inp);
		//$parvaz_det = new parvaz_det_class($ticket->parvaz_det_id);		
		$customer = new customer_class($ticket->customer_id);
		return( $customer->name." ".monize($ticket->mablagh * (1-$ticket->poorsant/100)) );
	}
	function loadRahgiri($inp)
	{
		$inp = (int)$inp;
		return ticket_class::rahgiriToCode($inp,conf::rahgiri);
	}
	function loadGender($inp)
	{
		$inp = (int)$inp;
		if($inp == 1)
			return('مرد');
		else
			return('زن');
	}
	function loadSale($inp)
	{
		$inp = (int)$inp;
		$ticket = new ticket_class($inp);
		$parvaz_det = new parvaz_det_class($ticket->parvaz_det_id);
		$customer = new customer_class($parvaz_det->customer_id);
		$out = "مبلغ خرید ".monize($parvaz_det->mablagh_kharid)." از ".(($customer->name=='')?'Unknown':$customer->name);
		return($out);
	}
	function loadShomare($inp)
	{
		$inp = (int)$inp;
		$ticket = new ticket_class($inp);
		return($ticket->shomare);
	}
	$today = date("Y-m-d");
	$befortomarrow = date("Y-m-d",strtotime($today." - 1 month"));
	$saztarikh =((isset($_REQUEST["saztarikh"]))?hamed_pdateBack(enToPerNums($_REQUEST["saztarikh"])):$befortomarrow);
	$statarikh = ((isset($_REQUEST["statarikh"]))?hamed_pdateBack(enToPerNums($_REQUEST["statarikh"])):$today);
	$wer = " `regtime` >= '$saztarikh 00:00:00' and `regtime` <='$statarikh 23:59:59' "; 
	$customer_id = ((isset($_REQUEST['customer_id']))?$_REQUEST["customer_id"]:-1);
	if(isset($_REQUEST['sanad_record_id']) && $_REQUEST['sanad_record_id']!="" )
	{
		$sanad_record_id =(int)$_REQUEST['sanad_record_id'];
		$wer = " `sanad_record_id`='$sanad_record_id'";
	}
	//if(!($customer_id==2 && $_SESSION[conf::app."_typ"]==0))
	if($customer_id>0)
		$wer.= " and `customer_id`='$customer_id'";
	else if($customer_id==-2)
		$wer.= " and 1=1";
	else
		$wer.= " and 1=0";
	$grid = new jshowGrid_new('ticket','girid1');
	$grid->index_width = "50px";
	$grid->whereClause = $wer;
	$grid->columnHeaders[0] = "شماره بلیت";
	$grid->columnFunctions[0] = 'loadShomare';
	$grid->columnHeaders[1]=null;
	$grid->columnHeaders[2]="نام و نام خانوادگی";	
	$grid->columnHeaders[3]=null;
	$grid->columnHeaders[4]="بزرگسال";
	$grid->columnFunctions[4] = "loadAdl";
	$grid->columnHeaders[5]=null;
	$grid->columnHeaders[6]="مشخصات پرواز";
	$grid->columnFunctions[6] = "loadParvazInfo";
	$grid->columnHeaders[7]=null;
	$grid->columnHeaders[8]=null;
	$grid->columnHeaders[9]=null;
	$grid->columnHeaders[10]=null;
	$grid->columnHeaders[11]=null;
	$grid->columnHeaders[12]=null;
	$grid->columnHeaders[13]=null;
	$grid->columnHeaders[14]=null;
	$grid->columnHeaders[15]=null;
	$grid->width = '99%';
	$grid->addFeild('id');
	$grid->columnHeaders[16]="جزئیات";
	$grid->columnFunctions[16] = "loadPrint";
	$grid->addFeild('id');
	$grid->columnHeaders[17]="مشخصات خرید";
	$grid->columnFunctions[17] = "loadSale";
	$grid->addFeild('id');
	$grid->columnHeaders[18]="آژانس خریدار";
	$grid->columnFunctions[18] = "loadCustomer1";
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->pageCount = 200;
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
			<form id="filter" method="get">
			<table>
			<tr>
			<td>
			انتخاب مشتری :
			<select id="customer_id" name="customer_id" class="inp1">
                        	<?php echo loadCustomer($customer_id); ?>
			</select>
			<input readonly="readonly" class="inp1" style="direction:ltr;" type="text" id="saztarikh" name="saztarikh" value="<?php echo hamed_pdate($saztarikh); ?>"/>
				<img id="mehrdad_date_btn_9" src="../img/cal.png" style="vertical-align: top;" />
				<script type="text/javascript">
					Calendar.setup({
						inputField  : "saztarikh",   // id of the input field
						button      : "mehrdad_date_btn_9",   // trigger for the calendar (button ID)
					ifFormat    : "%Y/%m/%d",       // format of the input field
					showsTime   : true,
					dateType	: 'jalali',
					showOthers  : true,
					langNumbers : true,
					weekNumbers : true
					});
				</script>
			<input readonly="readonly" class="inp1" style="direction:ltr;" type="text" id="statarikh" name="statarikh" value="<?php echo hamed_pdate($statarikh); ?>"/>
				<img id="mehrdad_date_btn_1" src="../img/cal.png" style="vertical-align: top;" />
				<script type="text/javascript">
					Calendar.setup({
						inputField  : "statarikh",   // id of the input field
						button      : "mehrdad_date_btn_1",   // trigger for the calendar (button ID)
					ifFormat    : "%Y/%m/%d",       // format of the input field
					showsTime   : true,
					dateType	: 'jalali',
					showOthers  : true,
					langNumbers : true,
					weekNumbers : true
					});
				</script>
			<!-- <input type="submit" value="مشاهده" class="inp" /> -->
			<input type="hidden" name="set_max_tedad" value ="-1" id="set_max_tedad" >
			</td>
			<td style="display:none;" >
	کدرهگیری:
				<input  type="text" class="inp1" name="sanad_record_id" value ="<?php echo ((isset($_REQUEST['sanad_record_id']))?$_REQUEST['sanad_record_id']:'') ?>" id="sanad_record_id" >
			</td>
			<td>
				<input type="button" value="جستجو " class="inp1" style="width:auto;" onclick="document.getElementById('filter').submit();" />
			</td>
			</form>
			<br/>
			<?php echo $out;  ?>
		</div>
	</body>
</html>
