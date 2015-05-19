<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$GLOBALS['sec'] = $se;
	$GLOBALS["jam_bed"] = 0;
	$GLOBALS["jam_bes"] = 0;
	$GLOBALS["jam_man"] = 0;
	function loadCustomers($cust = -1)
	{
		$cust = (int)$cust;
		$out = null;
		mysql_class::ex_sql("select * from `customers` ".(($cust>0)?"where `id`='$cust'":"")."  order by `name`",$q);
		while($r = mysql_fetch_array($q))
			$out[$r["name"]] = (int)$r["id"];
		return($out);
	}
	function add_item()
	{
                $fields = array();
                foreach($_REQUEST as $key => $value)
                {
                        if(strpos($key,"new_") === 0 && $key != "new_id" && $key != "new_en")
                        {
                                $fields[substr($key,4)] = $value;
                        }
                }
		$customer = new customer_class((int)$fields["customer_id"]);
		$customer->daryaft((int)$fields["mablagh"],(int)$_SESSION[conf::app."_user_id"],$fields["tozihat"],hamed_pdateBack($fields["tarikh"]));
		
	}
	function delete_item($id)
	{
		$amount = 0;
		$customer_id = -1;
		mysql_class::ex_sql("select `mablagh`,`customer_id` from `customer_daryaft` where `id` = '$id'",$q);
		if($r = mysql_fetch_array($q))
		{
			$amount = (int)$r["mablagh"];
			$customer_id = (int)$r["customer_id"];
		}
		mysql_class::ex_sqlx("delete from `customer_daryaft` where `id` = '$id'");
		mysql_class::ex_sqlx("update `customers` set `max_amount`=`max_amount`-$amount where `id` = '$customer_id'");
	}
	function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                return ($out);
        }
	function loadMablagh($id)
	{
		mysql_class::ex_sql("select `mablagh`,`typ` from `customer_daryaft` where `id`='$id'",$q);
		if($r = mysql_fetch_array(q))
		{
			
		}
	}
	function loadBes($inp)
	{
        	$inp = (int)$inp;
                $out = 0;
                mysql_class::ex_sql("select `mablagh`,`typ` from `customer_daryaft` where `id`='$inp'",$q);
                if($r = mysql_fetch_array($q))
                {
			$mab = (int)$r["typ"] * (int)$r["mablagh"];
			$out =(($mab>0)?abs($mab):"---");
			$GLOBALS["jam_bes"] = $GLOBALS["jam_bes"] + abs((int)$out);
			$GLOBALS["jam_man"] = $GLOBALS["jam_bed"] - $GLOBALS["jam_bes"];
 		}
		return monize($out);
	}
	function loadBed($inp)
	{
        	$inp = (int)$inp;
                $out = 0;
                mysql_class::ex_sql("select `mablagh`,`typ` from `customer_daryaft` where `id`='$inp'",$q);
                if($r = mysql_fetch_array($q))
                {
			$mab = (int)$r["typ"] * (int)$r["mablagh"];
			$out =(($mab<0)?abs($mab):"---");
			$GLOBALS["jam_bed"] = $GLOBALS["jam_bed"] + abs((int)$out);
 		}
		return monize($out);
	}
	function loadMande($inp)
	{
                $inp = (int)$inp;
                $out = 0;
                $out = $GLOBALS["jam_man"];
		if($out == 0)
			$out = "۰";
		if($out<0)
			$out = "بستانکار <br/>".enToPerNums(monize(abs($out)));
		else if($out>0)
			$out = "بدهکار <br/>".enToPerNums(monize(abs($out)));
                return($out);
	}
	function loadBedehkar ($inp)
	{
		$out = (int)$inp;
		if($out == 0)
			$out = "۰";
		if($out>0)
			$out = '----';
		else if($out<0)
			$out = "بدهکار <br/>".enToPerNums(monize(abs($out)));
                return($out);
	}
	function loadBestankar ($inp)
	{
		$out = (int)$inp;
		if($out == 0)
			$out = "۰";
		if($out<0)
			$out = '----';
		else if($out>0)
			$out = "بستانکار <br/>".enToPerNums(monize(abs($out)));
                return($out);
	}
	function loadMandeh ($inp)
        {
                $out = (int)$inp;
                if($out == 0)
                        $out = "۰";
                if($out<0)
                        $out = "بدهکار <br/>".enToPerNums(monize(abs($out)));
                else if($out>0)
                        $out = "بستانکار <br/>".enToPerNums(monize(abs($out)));
                return($out);
        }
	function hamed_pdate1($str)
        {
                $out=jdate('d / m / Y',strtotime($str));
                return enToPerNums($out);
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
		/*
		if($_SESSION[conf::app.'_customer_typ']==2 && $_SESSION[conf::app.'_typ'] == 0) 
		{
			if($customer_id==-2)
				$sel = 'selected=\'selected\''; 
			$out .= "<option value='-2' $sel >همه</option>";
		}
		*/
		return $out;
	}
	function loadTozihat($id)
	{
		$out = '';
		$out1 ='';
		$customer_id = -1;
		$id = (int)$id;
		$sanad_typ = 0;
		$tozihat = -1;
		mysql_class::ex_sql("select `sanad_typ`,`tozihat` from `customer_daryaft` where `id` = $id",$qq);
		if($rr = mysql_fetch_array($qq))
		{
			$sanad_typ = (int)$rr["sanad_typ"];
			$tozihat = $rr["tozihat"];
		}
		if($sanad_typ == 0 )
		{
			mysql_class::ex_sql("select * from `ticket` where `sanad_record_id` in (select `sanad_record_id` from `customer_daryaft` where `id` = '$id')",$q);
			if($r = mysql_fetch_array($q))
			{
				$customer_id = (int)$r["customer_id"];
				
				$par = new parvaz_det_class($r['parvaz_det_id']);
				$out = 'مسافر: <b>'.$r['lname'].'</b> پرواز: <b>'.enToPerNums($par->shomare).'</b> تاریخ: <b>'.hamed_pdate1($par->tarikh).'</b>';	
				$out1 = "<u><span onclick=\"wopen('report2.php?customer_id=$customer_id&sanad_record_id=".(int)$r["sanad_record_id"]."&','',700,300);\" style=\"cursor:pointer;color:firebrick;\">$out</span></u>";
			}
		}
		else if($sanad_typ == 1)
		{
			$ticket = new ticket_class((int)$tozihat);
			return("بابت استرداد بلیت شماره ".$ticket->shomare);
			
		}
		else if ($sanad_typ == 2)
		{
			return $tozihat;
		}
		return($out1);
	}
	$typ = (int)$_SESSION[conf::app."_customer_typ"];
	$today = date("Y-m-d");
	//$befortomarrow = date("Y-m-d",strtotime($today." - 1 month"));
	$saztarikh =((isset($_REQUEST["saztarikh"]))?hamed_pdateBack(enToPerNums($_REQUEST["saztarikh"])):$today);
	$statarikh = ((isset($_REQUEST["statarikh"]))?hamed_pdateBack(enToPerNums($_REQUEST["statarikh"])):$today);
	$max_tedad = ((isset($_REQUEST['set_max_tedad']))?(int)$_REQUEST['set_max_tedad']:-1);
	if($se->detailAuth('all'))
		$customer_id = ((isset($_REQUEST["customer_id"]))?(int)$_REQUEST["customer_id"]:-1);
	else
		$customer_id = (int)$_SESSION[conf::app."_customer_id"];
	$report_type = ((isset($_REQUEST["report_type"]))?(int)$_REQUEST["report_type"]:0);
        $grid = new jshowGrid_new("customer_daryaft","grid1");
	switch($report_type)
	{
		case 0:
	//-------mohasebe mande az ghabl------------
			$mande_azghabl = "SELECT SUM(`mablagh`*`typ`) as `mande`  FROM `customer_daryaft` where DATE(`tarikh`) < '$saztarikh' and  `typ`=-1 ";
			if(isset($_REQUEST["customer_id"]) && (int)$_REQUEST["customer_id"] != -2)
		                $mande_azghabl .= " and `customer_id` = '".(int)$_REQUEST["customer_id"]."' ";
			mysql_class::ex_sql($mande_azghabl,$qmand);
			$mande_ghabl_value = 0;
			if($rmand = mysql_fetch_array($qmand))
				$mande_ghabl_value = $rmand['mande'];
			$grid->addFeild("id");
                        $grid->addFeild("id");
			$grid->addFeild("id");
			$grid->fieldList[5] = "id";
			$grid->index_width= '30px';
			$grid->width= '99%';
		        $grid->columnHeaders[0] = null;
		        $grid->columnHeaders[1] = "مشتری(آژانس)";
		        $grid->columnHeaders[2] = null;
		        $grid->columnHeaders[3] = null;
		        $grid->columnHeaders[4] = "تاریخ";
		        $grid->columnHeaders[5] = "شرح سند";
		        $grid->columnHeaders[6] = "شماره سند";
			$grid->columnFunctions[6] = 'enToPerNums';
		        $grid->columnHeaders[7] = null;
			$grid->columnHeaders[8] = null;
			$grid->columnHeaders[9] = "بدهکار";
			$grid->columnHeaders[10] = "بستانکار";
			$grid->columnHeaders[11] = 'مانده';
		        $grid->columnLists[1] = loadCustomers($customer_id);
			$grid->columnFunctions[5] = "loadTozihat";
		        $grid->columnFunctions[9] = "loadBed";
			$grid->columnFunctions[10] = "loadBes";
			$grid->columnFunctions[11] = "loadMande";
		        $grid->columnFunctions[4] = "hamed_pdate";
		        $grid->columnCallBackFunctions[4] = "hamed_pdateBack";
		        $grid->columnAccesses[1] = 0;
			$grid->whereClause ='1=0';
			if(isset($_REQUEST["saztarikh"]))
				$grid->whereClause = " `typ`=-1 and `tarikh` >= '$saztarikh 00:00:00' and `tarikh` <='$statarikh 23:59:59'";        
		        if(isset($_REQUEST["customer_id"]) && (int)$_REQUEST["customer_id"] != -2)
		                $grid->whereClause .= " and `customer_id` = '".(int)$_REQUEST["customer_id"]."' ";
			$grid->whereClause .= " order by `tarikh`";
			$grid->pageCount = 30;
			if($max_tedad==1)
			{
				mysql_class::ex_sql('select `id` from `customer_daryaft` where'.$grid->whereClause,$tedad);
				$grid->pageCount=(mysql_num_rows($tedad));
			}
			$grid->footer = "<td class=\"showgrid_row_odd\" align=\"left\" colspan=\"7\" >مانده از قبل:</td><td id=\"mande_ghabl\" class=\"showgrid_row_odd\"  align=\"center\">".perToEnNums(loadMandeh($mande_ghabl_value))."</td>"; 
			$grid->footer .= "</tr><tr><td class=\"showgrid_row_even\"><input name=\"txttedad\"type=\"hidden\" value=\"1\"/>";
			$grid->footer .="<input class=\"inp1\" type=\"button\" name=\"tedad\" value=\"مشاهده همه\" onclick='document.getElementById(\"set_max_tedad\").value=1;document.getElementById(\"filter\").submit();'  /></td><td colspan='4' class=\"showgrid_row_even\" align='left' >جمع:</td><td class=\"showgrid_row_even\"  id='jam_bed1' align='center'  ></td><td class=\"showgrid_row_even\"  id='jam_bes1' align='center'  ></td><td id=\"tafazol\" align=\"center\" class=\"showgrid_row_even\" ></td>";
			$grid->footer .= "</tr><tr><td class=\"showgrid_row_odd\" align=\"left\" colspan=\"7\" >مانده  کل:</td><td id=\"mande_kol\" class=\"showgrid_row_odd\"  align=\"center\"></td>"; 
			break;
		case 1:
			$saztarikh = "$saztarikh 00:00:00";
			$statarikh = "$statarikh 23:59:59";
			$grid->query = "SELECT `customer_id` as `cid`,(select -1*SUM(`mablagh`) from `customer_daryaft` where `customer_id` = `cid` and `typ`=-1 and `tarikh` >= '$saztarikh' and `tarikh` <= '$statarikh' ) as `bede`,(select SUM(`mablagh`) from `customer_daryaft` where `customer_id` = `cid` and `typ`=1 and `tarikh` >= '$saztarikh' and `tarikh` <= '$statarikh' ) as `best`,SUM(`mablagh`*`typ`) as `mande`  FROM `customer_daryaft` where `tarikh` >= '$saztarikh' and `tarikh` <= '$statarikh'  group by `customer_id`";
			$grid->loadQueryField = TRUE;
			for($i=0;$i<count($grid->columnHeaders);$i++)
				$grid->columnHeaders[$i] = null;
			$grid->columnHeaders[0]="مشتری";
			$grid->columnLists[0] = loadCustomers();
		        $grid->columnHeaders[2]="بستانکار";
			$grid->columnHeaders[1]="بدهکار";
			$grid->columnHeaders[3]="مانده";
			$grid->columnFunctions[2] = "loadBestankar";
			$grid->columnFunctions[1] = "loadBedehkar";
			$grid->columnFunctions[3] = "loadMandeh";
			/*
                        $grid->footer="<td colspan='3' class=\"showgrid_row_odd\" align='left' >جمع:</td><td class=\"showgrid_row_odd\"  id='jam_bed1' align='center'  ></td><td class=\"showgrid_row_odd\"  id='jam_bes1' align='center'  ></td><td id=\"tafazol\" align=\"center\" class=\"showgrid_row_odd\"></td>";
			*/
			break;
	}
	
	$grid->canAdd = FALSE;
	$grid->canEdit = FALSE;
	$grid->canDelete = FALSE;
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
        $customer = new customer_class($customer_id);
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
			انتخاب حساب :
			<select id="customer_id" name="customer_id" class="inp1" style="width:auto;">
                        <?php
				echo loadCustomer($customer_id);
			/*
				if($typ == 2 && $_SESSION[conf::app."_typ"]==0)
					echo $grid->columnListToCombo(loadCustomers());
				else
					echo $grid->columnListToCombo(array($customer->name => $customer_id),$customer_id);
			*/ 
			?>
			<?php
			if($se->detailAuth('all'))
			//if($typ == 2 && $_SESSION[conf::app."_typ"]==0)
			{
			?>
			<option value="-2"<?php echo (($customer_id == -2)?" selected=\"selected\"":""); ?>>
			همه
			</option>
			<?php
			}
			?>
			</select>
			نوع گزارش : 
			<select id="report_type" name="report_type" class="inp1">
				<option value = "0"<?php echo (($report_type == 0)?" selected=\"selected\"":""); ?>>
					جزئی
				</option>
			<?php
			//if($typ == 2 && $_SESSION[conf::app."_typ"]==0)
			if($se->detailAuth('all'))
			{
			?>
				<option value = "1"<?php echo (($report_type == 1)?" selected=\"selected\"":""); ?>>
					کلی
                                </option>
<?php
}
?>
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
			<input type="submit" value="مشاهده" class="inp1" />
			<input type="hidden" name="set_max_tedad" value ="-1" id="set_max_tedad" >
			<input type="hidden" id="jam_bed" name="jam_bed" value="<?php echo $GLOBALS["jam_bed"]; ?>" />
			<input type="hidden" id="jam_bes" name="jam_bes" value="<?php echo $GLOBALS["jam_bes"]; ?>" />
			</form>
			<br/>
			<?php echo $out;  ?>
			<input  class="inp" type="button" value="چاپ"  id="chap" onclick="document.getElementById('chap').style.display='none';window.print();">
		</div>
		<script>
			var bed = parseInt(document.getElementById('jam_bed').value,10);
			var bes = parseInt(document.getElementById('jam_bes').value,10);
			var sh = String(document.getElementById('mande_ghabl').innerHTML);
			sh = sh.split('<br>');
			var typ = 1;
			if(sh.length == 2)
			{
				if(sh[0]=='بدهکار ')
					typ = -1;
				sh = sh[1];
			}
			
			var mande = typ*parseInt(unFixNums(umonize(sh)),10);
			var tafazol = bes-bed;
			var stat = ((tafazol<0)?"بدهکار<br/>":"بستانکار<br/>");
			if(tafazol == 0)
				stat = "";
			var statkol = ((tafazol+mande<0)?"بدهکار<br/>":"بستانکار<br/>");
			if(tafazol+mande == 0)
				statkol = "";

			document.getElementById('jam_bed1').innerHTML="&nbsp;"+FixNums(monize2(bed))+"&nbsp;&nbsp;";
			document.getElementById('jam_bes1').innerHTML="&nbsp;"+FixNums(monize2(bes))+"&nbsp;&nbsp;";
			document.getElementById('tafazol').innerHTML =stat+"&nbsp;"+FixNums(monize2(Math.abs(tafazol)));
			document.getElementById('mande_kol').innerHTML =statkol+"&nbsp;"+FixNums(monize2(Math.abs(tafazol+mande)));
		</script>
	</body>
</html>
