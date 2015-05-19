<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
        function hamed_pdate($str)
        {
                $out=jdate('d / m / Y',strtotime($str));
		$out .= "&nbsp;&nbsp;&nbsp;".date('F d',strtotime($str));
                return enToPerNums($out);
        }
        
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
		$ar=array(0=>'ADL',1=>'CHD',2=>'INF');
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
			$out = $parvaz->shomare."( ".loadCity($parvaz->mabda_id)." -> ".loadCity($parvaz->maghsad_id)." )".hamed_pdate($parvaz->tarikh);
		}
		return($out);
	}
	function loadPrint($inp)
	{
		$out = (int)$inp;
		$out = "<u><span style='cursor:pointer;color:firebrick;' onclick=\"wopen('eticket.php?shomare=$out','',900,600)\" >$out <br/> چاپ Eticket </span></u>";
		return $out;
	}
	function loadCustomerName($inp)
	{
		$inp = (int)$inp;
		$customer = new customer_class($inp);
		return($customer->cod);
	}
	function zeroise($inp)
	{
		$inp = (int)$inp;
		$out = "$inp";
		if($inp < 1000)
		{
			while(strlen($out)<4)
			{
				$out = "0".$out;
			}
		}
		return($out);
	}
	function loadRadif($inp)
	{
		$out = $GLOBALS["radif"];
		$GLOBALS["radif"]+=2;
		return($out);
	}
	function loadCode($inp)
	{
		$inp = (int)$inp;
		return(ticket_class::rahgiriToCode($inp,conf::rahgiri));
	}
	function loadGender($inp)
        {
                $inp = (int)$inp;
                if($inp == 1)
                        return('مرد');
                else
                        return('زن');
        }
        function loadSherkat($inp)
        {
        	$out = '';
		$inp = (int)$inp;
        	mysql_class::ex_sql('select `name` from `sherkat` where `id` = '.$inp,$q);
        	if($r = mysql_fetch_array($q))
        		$out = $r['name'];
        	return($out);
        }
        $parvaz_det_id = ((isset($_REQUEST['parvaz_det_id']))?$_REQUEST["parvaz_det_id"]:-1);
        $parvaz_det = new parvaz_det_class($parvaz_det_id);
	$wer = ' `en`<>-1 and `parvaz_det_id`='.$parvaz_det_id;
	mysql_class::ex_sql("select `id` from `ticket` where $wer  and `shomare` % 2 = 1 order by `shomare`",$q);
	$tmp = mysql_num_rows($q);
	$q = null;
	$gr = 1;
	$gr1 = 0;
	mysql_class::ex_sql("select `id` from `ticket` where $wer  and `shomare` % 2 = 0 order by `shomare`",$q);
	if(mysql_num_rows($q)>$tmp)
	{
		$gr = 0;
		$gr1 = 1;
	}
	$q = null;
	$GLOBALS["radif"] = 1;
	$grid = new jshowGrid_new('ticket','girid1');
	$grid->index_width = "50px";
	$grid->whereClause = $wer.' and `shomare` % 2 = '.$gr.' order by `shomare`';
	$grid->fieldList[1] = $grid->fieldList[9];
	$grid->columnHeaders[0] = "ردیف";
	$grid->columnFunctions[0] = "loadRadif";
	$grid->columnHeaders[1]="شماره بلیت";
	$grid->columnFunctions[1] = "zeroise";
	$grid->columnHeaders[2]="نام و نام خانوادگی";	
	$grid->columnHeaders[3]=null;
	$grid->columnHeaders[4]="AGE";
	$grid->columnFunctions[4] = "loadAdl";
	$grid->columnHeaders[5]=null;
	$grid->columnHeaders[6]=null;
	//$grid->columnFunctions[6] = "loadParvazInfo";
	$grid->columnHeaders[7]="آژانس";
	$grid->columnFunctions[7] = "loadCustomerName";
	$grid->columnHeaders[8]=null;
	$grid->columnHeaders[9]=null;
	$grid->columnHeaders[10]=null;
	$grid->columnHeaders[11]=null;
	$grid->columnHeaders[12]=null;
	$grid->columnHeaders[13]=null;
	$grid->columnHeaders[14]=null;
        $grid->columnHeaders[15]=null;
	$grid->columnFunctions[15] = 'loadGender';
	$grid->pageCount = 200;
//	$grid->addFeild('id');
//	$grid->columnHeaders[15]="جزئیات";
//	$grid->columnFunctions[9] = "loadPrint";
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
        $grid->showIndex = FALSE;
        $grid->width = '100%';
	$grid->cssClass = 're';
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();

        $GLOBALS["radif"] = 2;
	$grid1 = new jshowGrid_new('ticket','grid2');
        $grid1->index_width = "50px";
	$grid1->showIndex = FALSE;
        $grid1->whereClause = $wer.' and `shomare` % 2 = '.$gr1.' order by `shomare`';
        $grid1->fieldList[1] = $grid->fieldList[9];
        $grid1->columnHeaders[0] = "ردیف";
        $grid1->columnFunctions[0] = "loadRadif";
        $grid1->columnHeaders[1]="شماره‌بلیت";
        $grid1->columnFunctions[1] = "zeroise";
        $grid1->columnHeaders[2]="نام و خانوادگی";
        $grid1->columnHeaders[3]=null;
        $grid1->columnHeaders[4]="AGE";
        $grid1->columnFunctions[4] = "loadAdl";
        $grid1->columnHeaders[5]=null;
        $grid1->columnHeaders[6]=null;
        //$grid1->columnFunctions[6] = "loadParvazInfo";
        $grid1->columnHeaders[7]="آژانس";
        $grid1->columnFunctions[7] = "loadCustomerName";
        $grid1->columnHeaders[8]=null;
        $grid1->columnHeaders[9]=null;
        $grid1->columnHeaders[10]=null;
        $grid1->columnHeaders[11]=null;
        $grid1->columnHeaders[12]=null;
        $grid1->columnHeaders[13]=null;
        $grid1->columnHeaders[14]=null;
        $grid1->columnHeaders[15]=null;
	$grid1->columnFunctions[15] = 'loadGender';
	$grid1->pageCount = 200;
//      $grid1->addFeild('id');
//      $grid1->columnHeaders[15]="ﺝﺰﺋیﺎﺗ";
//      $grid1->columnFunctions[9] = "loadPrint";
        $grid1->canAdd = FALSE;
        $grid1->canEdit = FALSE;
        $grid1->canDelete = FALSE;
        $grid1->width = '100%';
	$grid1->cssClass = 're';
        $grid1->intial();
        $grid1->executeQuery();
        $out2 = $grid1->getGrid();

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
	<body "width:25cm;">
		<div align="center">
			<table style="border-style:solid;border-width:1px;border-color:black;font-family:tahoma;font-size:12px;width:95%;">
				<tr>
					<td>
						<?php
							$cus = new customer_class((int)$_SESSION[conf::app.'_customer_id']);
							echo $cus->name;
						?>
					</td>
					<td>
						سیستم رزرواسیون بهار ۱
                                        </td>
                                        <td align="left">
						تاریخ گزارش‌گیری :
                                        </td>
					<td>
						<?php echo enToPerNums(jdate("d / m / Y")); ?>
					</td>
					<td align="left">
						ساعت گزارش‌گیری :
					</td>
					<td>
						<?php echo enToPerNums(jdate("H:i")); ?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:15px;">
						<b>
						لیست مسافرین
						</b>
					</td>
				</tr>
				<tr>
					<td>
						شماره پرواز : <?php echo enToPerNums($parvaz_det->shomare).'&nbsp;&nbsp;'.loadSherkat($parvaz_det->sherkat_id); ?>
					</td>
					<td>
						تاریخ : <?php echo jdate("d / m / Y",strtotime($parvaz_det->tarikh)); ?>
					</td>
					<td>
						ساعت خروج از مبدأ : <?php echo enToPerNums($parvaz_det->saat); ?>
					</td>
					<td>
						مبدأ : <?php echo loadCity($parvaz_det->mabda_id); ?>
					</td>
					<td>
                                                مقصد : <?php echo loadCity($parvaz_det->maghsad_id); ?>
                                        </td>

				</tr>
			</table>
			<table style='width:95%;' cellspacing="0" >
			<tr>
				<td style='width:50%;vertical-align:top;'>
					<?php 
						//echo '<b>'.loadParvazInfo($parvaz_det_id).'</b><br /><br />'; 
						echo $out;
					?>
				</td>
                                <td style='width:50%;vertical-align:top;'>
                                        <?php
                                                //echo '<b>'.loadParvazInfo($parvaz_det_id).'</b><br /><br />'; 
                                                echo $out2;
                                        ?>
                                </td>
			</tr>
		</div>
	</body>
</html>
