<?php
	include_once("../kernel.php");
	function loadAdl($inp)
	{
		$ar=array(0=>"بزرگسال",1=>"کودک",2=>"نوزاد");
		return $ar[$inp];
	}
	function loadCity($inp)
	{
		$inp = (int)$inp;
		$out = "";
		$mysql = new mysql_class;
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]['name'];
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
			$out = $parvaz->shomare."( ".loadCity($parvaz->mabda_id)." -> ".loadCity($parvaz->maghsad_id)." )<br/>".hamed_pdate($parvaz->tarikh);
		return($out);
	}
	function loadPrint($inp)
	{
		$inp = (int)$inp;
		$ticket = new ticket_class($inp);
		$id = $ticket->getId();
		if($ticket->typ == 0)
			$out = "<div class='msg' style='cursor:pointer;' onclick=\"wopen('eticket.php?online=1&print=1&shomare=".$ticket->shomare."&id=$inp&','',900,600)\" > ".$ticket->shomare." <br> چاپ </div>";
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
		$mysql = new mysql_class;
		$mysql->ex_sql("select `parvaz_det_id` from `ticket` where `parvaz_det_id` <> '".$ticket->parvaz_det_id."' and `shomare` = '".$ticket->shomare."' and `regtime`>='".$ticket->regtime."' and `regtime`<DATE_ADD('".$ticket->regtime."',interval 1 minute) ",$q);
		if(isset($q[0]))
		{
			$parvaz = new parvaz_det_class((int)$r["parvaz_det_id"]);
			$out = $parvaz->shomare."( ".loadCity($parvaz->mabda_id)." -> ".loadCity($parvaz->maghsad_id)." )<br/>".hamed_pdate($parvaz->tarikh);
		}
		return($out);	
	}
	function rahgiri($inp)
	{
		$conf = new conf;
		$inp = (int) $inp;
		$inp = ticket_class::rahgiriToCode($inp,$conf->rahgiri);
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
	$out = '';
	if(isset($_GET['rahgiri']) && isset($_GET["sanad_record_id"]) && isset($_GET["ticket_type"]))
	{
		$sanad_recoed_id = $_GET["sanad_record_id"];
		if(isset($_REQUEST['SaleReferenceId']))
			$SaleReferenceId = $_REQUEST['SaleReferenceId'];
		else
			$SaleReferenceId = pardakht_class::loadBySanad_record_id((int)$sanad_recoed_id);
		$rahgiri = trim($_GET['rahgiri']);
		$pardakht_id = pardakht_class::barcode($rahgiri);
		$par = new pardakht_class($pardakht_id);
		if($par->sanad_record_id==$sanad_recoed_id)
		{
			$ticket_type = (int)$_GET["ticket_type"];
			$gname = 'ticket_print';
			$input =array($gname=>array('table'=>'ticket','div'=>'ticket_print_div'));
			$xgrid = new xgrid($input);
			$xgrid->eRequest[$gname] = array('rahgiri'=>$rahgiri,'sanad_record_id'=>$sanad_recoed_id,'ticket_type'=>'0');
			$xgrid->whereClause[$gname] ="sanad_record_id=$sanad_recoed_id and en=1 group by `shomare`";
			$xgrid->column[$gname][0]['name'] = '';
			$xgrid->column[$gname][1]['name'] = '';
			$xgrid->column[$gname][2]['name'] = 'نام و نام خانوادگی';
			$xgrid->column[$gname][3]['name'] = 'تلفن';
			$xgrid->column[$gname][4]['name'] = 'رده سنی';
			$xgrid->column[$gname][4]['cfunction'] = array("loadAdl");
			$xgrid->column[$gname][5]['name'] = 'کد رهگیری';
			$xgrid->column[$gname][5]['cfunction'] = array("rahgiri");
			$xgrid->column[$gname][6]['name'] ='مشخصات پرواز رفت';
			$xgrid->column[$gname][6]['cfunction'] = array("loadParvazInfo");
			$xgrid->column[$gname][7] = $xgrid->column[$gname][0];
			$xgrid->column[$gname][7]['name'] = 'مشخصات پرواز برگشت';
			$xgrid->column[$gname][7]['cfunction'] = array("loadBargasht");
			$xgrid->column[$gname][8]['name'] = '';
			//$xgrid->column[$gname][9]['name'] ='شماره پیگیری بلیط';
			//-----------disable for cant print tickets
			
			if($ticket_type==1)
				$xgrid->column[$gname][9]['name']="شماره بلیت";
			else
			{
				$xgrid->column[$gname][9] = $xgrid->column[$gname][0];
				$xgrid->column[$gname][9]['name'] = 'شماره بلیت';
				$xgrid->column[$gname][9]['cfunction'] = array("loadPrint");
			}
			
			$xgrid->column[$gname][10]['name'] = '';
			$xgrid->column[$gname][11]['name'] = '';
			$xgrid->column[$gname][12]['name'] = '';
			$xgrid->column[$gname][13]['name'] = '';
			$xgrid->column[$gname][14]['name'] = '';
			$xgrid->column[$gname][15]['name'] = 'جنسیت';
			$xgrid->column[$gname][15]['cfunction'] = array("loadGender");
			$xgrid->column[$gname][16]['name'] = '';
			$xgrid->column[$gname][17]['name'] = '';
			$xgrid->column[$gname][18]['name'] = '';
			$xgrid->column[$gname][19]['name'] = '';
			$out1 =$xgrid->getOut($_REQUEST);
			if($xgrid->done)
				die($out1);
			/*
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
			*/
		}
		else
			$out = '.اطلاعات وارد شده صحیح نمی باشد';
	}
	else
		$out = 'اطلاعات وارد شده صحیح نمی باشد';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
			سامانه فروش پرواز بهار
		</title>
		<link rel="stylesheet" href="../css/xgrid.css" type="text/css">
		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/grid.js"></script>
	</head>
	<body>
		<script>
			$(document).ready(function(){
				var args=<?php echo $xgrid->arg; ?>;
				intialGrid(args);
			});
		</script>
		<div class="notice"  >
			کاربر گرامی بلیط نهایی بزودی برای شما ایمیل خواهد شد
		</div>
		<div align="center" style="margin:10px;">
			<div style="font-size:15px;color:red;font-weight:bold;">
				کدرهگیری پرداخت بانکی:
				<?php echo $SaleReferenceId; ?>
			</div>
			<div id="ticket_print_div" ></div>
			<input type='button' value='چاپ تمامی بلیت‌ها' class='inp' style='width:auto;' onclick="wopen('eticket_all.php?sanad_record_id=<?php echo $sanad_recoed_id; ?>','',800,600);" > 
			<input type='button' value='بازگشت به صفحه اصلی' class='inp' onclick="window.location='index.php';" >
		</div>
		
	</body>
</html>
