<?php
	session_start();
	include_once ("../kernel.php");
	$pass=((isset($_REQUEST['pass']))?$_REQUEST['pass']:"");
	$user=((isset($_REQUEST['user']))?$_REQUEST['user']:"");
	ticket_class::clearTickets();
	if (((isset($_SESSION['user_id']) && isset($_SESSION['typ']))))
	{
//	 die("<script>window.location='login.php';</script>");

	}	
	date_default_timezone_set("Asia/Tehran");
	$firstVisit = (isset($_SESSION["login"]) && ($_SESSION["login"] == 1) && isset($_REQUEST["user"]));
	if($firstVisit ||(isset($_SESSION["user_id"]))){	
	function loadUserById($id){
		$out = 'تعریف نشده';
		mysql_class::ex_sql("select fname,lname from user where id=$id",$qq);
		if($r= mysql_fetch_array($qq,MYSQL_ASSOC))
		{
			$out = $r["fname"]." ".$r["lname"];
			
		}
		return $out;
	}
	function isOdd($inp){
		$out = TRUE;
		if((int)$inp % 2==0){
			$out = FALSE;
		}
		return $out;
	}
	function selectParvaz($inp)
	{
		$inp = (int)$inp;
		$today=strtotime(date('Y-m-d'));
		$tarikh = $today;
		$saat = strtotime(date('H:i:s'));
		$time_new = $saat;
		$ismodir = (((isset($_SESSION["customer_typ"])) && ((int)$_SESSION["customer_typ"]==2))?TRUE:FALSE);
		$parvazismine = TRUE;
		$typ = -1;
		mysql_class::ex_sql("select `tarikh`,`saat`,`typ` from `parvaz_det` where `id`=$inp",$q);
		if($r=mysql_fetch_array($q))
		{
			$tarikh = strtotime($r['tarikh']);
			$saat = strtotime($r['saat']);
			$typ = (int)$r['typ'];
		}
		$out = '&nbsp;';
		if( (($tarikh>$today) || ($tarikh==$today && $saat>$time_new)) && ($ismodir || ($typ==0) || (($typ==1) && ($parvazismine))) )
			$out = "<input type=\"checkbox\" id=\"parvaz_$inp\" name=\"parvaz_$inp\" onclick=\"selectParvaz(this,$inp);\" />";
		else if($typ == 2)
			$out = "تلفنی";
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
        function loadCityMabda($inp)
        {
                $inp = (int)$inp;
                $parvaz = new parvaz_class($inp);
                $out = "&nbsp;";
                if($parvaz->getId() >0)
                {
                        $out = loadCity($parvaz->mabda_id);
                }
                return($out);
        }
        function loadCityMaghsad($inp)
        {
                $inp = (int)$inp;
                $parvaz = new parvaz_class($inp);
                $out = "&nbsp;";
                if($parvaz->getId() >0)
                {
                        $out = loadCity($parvaz->maghsad_id);
                }
                return($out);
        }
	function shomareParvaz($inp)
	{
                $out = "&nbsp;";
                $inp = (int)$inp;
                $parvaz = new parvaz_class($inp);
                if($parvaz->getId() >0)
                {
                        $out = $parvaz->shomare;
                }
                return(enToPerNums($out));
	}
	function loadHavapeima($inp)
	{
		$inp = (int)$inp;
		$out = "&nbsp;";
		mysql_class::ex_sql("select `name` from `havapeima` where `id` ='$inp'",$q);
		if($r = mysql_fetch_array($q))
		{
			$out = $r["name"];
		}
		return($out);
	}
        function loadSherkat($inp)
        {
                $inp = (int)$inp;
                $out = "&nbsp;";
                mysql_class::ex_sql("select `name` from `sherkat` where `id` ='$inp'",$q);
                if($r = mysql_fetch_array($q))
                {
                        $out = $r["name"];
                }
                return($out);
        }
/*	function zarfiat($inp)
        {
                $inp = (int)$inp;
                $out = er($inp);
                return($out);
        }*/
	function loadParvazInfo($inp)
	{
		$out = "&nbsp;";
		$inp = (int)$inp;
		$parvaz = new parvaz_class($inp);
		if($parvaz->getId() >0)
		{
			$out = loadHavapeima($parvaz->havapiema_id)."<br/>".loadSherkat($parvaz->sherkat_id);
		}
		return($out);
	}
	function hamed_pdate($str)
        {
                $out=jdate('d / m / Y',strtotime($str));
		$out .= "<br/>".date('F d',strtotime($str));
                return enToPerNums($out);
        }
	function hamed_pdate_day($str)
	{
		$out=jdate('l',strtotime($str));
                return enToPerNums($out);
	}
	function hamed_pdate1($str)
	{
		$out=jdate('Y/n/j',strtotime($str));
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
	function zarfiat1($inp)
        {
                return enToPerNums($inp);
        }
	function loadZarfiat($inp)
	{
		$inp = (int)$inp;
		$par = new parvaz_det_class($inp);
		if((int)$_SESSION['customer_typ']==2 )
		{
			$out = $par->getZarfiat();
		}
		else if((int)$_SESSION['customer_typ']==1)
		{
			$out = $par->getZarfiat((int)$_SESSION['customer_id']);
		}
		$zarfiat = enToPerNums($out);
		$color = "blue";
		if($par->zarfiat == 0)
		{
			$color = "red";
			$zarfiat = "CLOSED";
		}
		$out = "<u><span ".(((int)$_SESSION['customer_typ']==2)?" style=\"color:$color;cursor:pointer;\" onclick=\"wopen('zarfiat.php?parvaz_det_id=$inp&','',600,400);\"":"").">$zarfiat</span></u>";
		return $out;
		
	}
	function parvaz_typ()
	{
		$out['عمومی'] = 0;
		$out['کاربران'] = 1;
		$out['تلفنی'] = 2;
		return $out;
	}
	function domasore()
	{
		$out['عادی'] = 0;
		$out['دومسیره'] = 1;
		return $out;
	}
	function poorsant($inp)
	{
		$customer_id = $_SESSION["customer_id"];
		$cust = new customer_class($customer_id);
		$out = "<u><span style=\"color:firebrick;cursor:pointer;\" onclick=\"wopen('setpoorsant.php?parvaz_det_id=$inp&','',600,200);\">".enToPerNums($cust->getPoorsant($inp))."%</span></u>";
		return ($out);
	}
	function poorsant1($inp)
	{
		$customer_id = $_SESSION["customer_id"];
		$cust = new customer_class($customer_id);
		$out = enToPerNums($cust->getPoorsant($inp));
		return ($out).'%';
	}
	function loadCities($smabda_id = -1)
	{
		$smabda_id = (int)$smabda_id;
		$out ="<option value=\"-1\">\nهمه\n</option>\n";
		mysql_class::ex_sql("select * from `shahr` order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$out .= "<option value=\"".(int)$r["id"]."\" ".(((int)$r["id"]==$smabda_id)?"selected=\"selected\"":"")." >\n";
			$out .= $r["name"]."\n";
			$out .= '</option>\n';
		}
		return($out);
	}
	function loadCust()
	{
		$out = '';
		mysql_class::ex_sql("select id,name from customers order by name ",$q);
		while($r = mysql_fetch_array($q))
		{
			if((int)$r["id"]==(int)$_SESSION["customer_id"])
			{
				$sel = "selected=\"selected\"";
			}
			else
			{
				$sel ='';
			}
			$out.="<option $sel value=\"".$r["id"]."\">".$r["name"]."</option>\n";
		}
		return $out;
	}
	function parvaz_detail($inp)
	{
		$inp = (int)$inp;
		mysql_class::ex_sql("select `id` from `ticket` where `en`='1' and `adult`<>'2' and parvaz_det_id=$inp",$q);
		$out = mysql_num_rows($q);
		if($out==0)	
			$out ='0';
		$out .="&nbsp;<u><span style=\"color:firebrick;cursor:pointer;\" onclick=\"wopen('parvaz_forookhte.php?parvaz_det_id=$inp&','',600,120);\"> جزئیات</span></u>";
		$out .='<br />'.esterdad($inp).'<br />';
		$out .="&nbsp;<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('manifest.php?parvaz_det_id=$inp&','',800,600);\"> فهرست</span></u>";
		return $out;
	}
	function loadCustomerZakhire($inp)
	{
		//$out = "ذخیره $inp";
		$inp = (int)$inp;
		$parvaz_det = new parvaz_det_class($inp);
		$zakhire = enToPerNums($parvaz_det->zarfiat - $parvaz_det->getZarfiat());
		$out = "<u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('zakhire.php?parvaz_det_id=$inp&','',600,400);\">$zakhire</span></u>";
		return($out);
	}
	function loadDomasire($inp)
	{
		$inp = (int)$inp;
		$out = "";
		if($inp == 0)
			$out ="عمومی";
		else if($inp == 1)
			$out = "دومسیره";
		return($out);	
	}
	function binToBool($inp)
	{
		$inp = (int)$inp;
		return((($inp!=0)?"قابل استرداد":"غیرقابل استرداد"));
	}
	function esterdad($inp)
	{
		$inp = (int)$inp;
		$ester = 1;
		mysql_class::ex_sql("select `can_esterdad` from `parvaz_det` where `id`=$inp",$q);
		if($r = mysql_fetch_array($q))
			$ester = (int)$r["can_esterdad"];
		$out = binToBool($ester);
		$color = "red";
		if($ester == 1)
			$color = "blue";
		$out = "<u><span style=\"color:$color;cursor:pointer;\" onclick=\"window.open('changeesterdad.php?id=$inp&ester=$ester&');\">$out</span></u>";
		return($out);
	}
	if($firstVisit){
		//echo "+++++++first+++++++";
		$is_modir  = FALSE;
		mysql_class::ex_sql("select * from user where user = '".$user."'",$q);
		
		if($r_u = mysql_fetch_array($q,MYSQL_ASSOC)){
			if($pass == $r_u["pass"] ){
				$_SESSION["typ"]=(int)$r_u["typ"];
				$_SESSION["user_id"] = (int)$r_u["id"];
				$cust = new customer_class((int)$r_u["customer_id"]);
				$_SESSION["customer_typ"] = $cust->typ;
				$_SESSION["customer_id"] = (int)$r_u["customer_id"];
			}else{
				die("<script>window.location = 'login.php?stat=wrong_pass&';</script>");
			}
		}else{
			die("<script>window.location = 'login.php?stat=wrong_user&';</script>");
		}
	}
	$user_id = (int)$_SESSION["user_id"];
	if(isset($_REQUEST["cust_id"]))
	{
		$_SESSION["customer_id"] = (int) $_REQUEST["cust_id"];
	}
	$today = date("Y-m-d");
	$time_now = date("H:i:s",strtotime(date("H:i:s")." + 1 hour"));
	$aftertomarrow = date("Y-m-d",strtotime($today." + 1 month"));
	$smabda_id = ((isset($_REQUEST["smabda_id"]))?$_REQUEST["smabda_id"]:-1);
	$smaghsad_id = ((isset($_REQUEST["smaghsad_id"]))?$_REQUEST["smaghsad_id"]:-1);
	$domasire = FALSE;
	$domasire = ((isset($_REQUEST["domasire"]) && $_REQUEST["domasire"])?TRUE:FALSE);
	$saztarikh =((isset($_REQUEST["saztarikh"]))?$_REQUEST["saztarikh"]:hamed_pdate1($today));
	$statarikh = ((isset($_REQUEST["statarikh"]))?$_REQUEST["statarikh"]:hamed_pdate1($aftertomarrow));
	if($_SESSION['customer_typ']!=2 &&  strtotime(hamed_pdateBack(perToEnNums($saztarikh)))<strtotime($today) )
	{
		$saztarikh=hamed_pdate1($today);
	}
	$whereClause = " (`tarikh` > '".hamed_pdateBack(perToEnNums($saztarikh))."' and `tarikh` <= '".hamed_pdateBack(perToEnNums($statarikh))."') or (`tarikh`='".hamed_pdateBack(perToEnNums($saztarikh))."' and `saat`>='$time_now')";
	//and  parvaz_id in (select id from `parvaz` where
	$whereClause1 = "";
	if($smabda_id > 0)
	{
		$whereClause1 .= " (`mabda_id` = '$smabda_id'";
		if($domasire)
			$whereClause1 .= " or `maghsad_id` = '$smabda_id'";
		$whereClause1 .= ")";
	}
        if($smaghsad_id > 0)
        {
                $whereClause1 .= " and (`maghsad_id` = '$smaghsad_id'";
                if($domasire)
                        $whereClause1 .= " or `mabda_id` = '$smaghsad_id'";
                $whereClause1 .= ")";
        }
	if($whereClause1 != "")
	{
		$whereClause .= "and  parvaz_id in (select id from `parvaz` where $whereClause1)";
	}
	$whereClause .= " order by `tarikh`";
	$forms = null;
	$out = "";
	$grid = new jshowGrid_new("parvaz_det","grid1");
	$grid->width = "99%";
	$grid->columnHeaders[1]="هواپیمایی";
	$grid->columnFunctions[1] ="loadParvazInfo";
/*
        $grid->columnHeaders[1]="روز";
        $grid->columnFunctions[1] = "hamed_pdate_day";
*/
	$grid->columnHeaders[2]="تاریخ";
	$grid->columnFunctions[2] = "hamed_pdate";
	$grid->columnHeaders[3]="ساعت";
	$grid->columnFunctions[3] = "saat";
	$grid->fieldList[4] = 'id';
	$grid->columnHeaders[4]="ظرفیت";
	$grid->columnFunctions[4] = "loadZarfiat";
	//$grid->columnAccesses[4] = 0;
	$grid->columnHeaders[5]="قیمت";
	$grid->columnFunctions[5] = "monize";
	$grid->columnHeaders[8] = "نوع";
	$grid->whereClause  =$whereClause;
	for($indx = 0;$indx < count($grid->columnHeaders);$indx++)
	{
		$grid->columnAccesses[$indx] = 0;
	}
	switch ($_SESSION["customer_typ"])
	{
		case 0 : 
			$grid->columnHeaders[6] = null;
			$grid->columnHeaders[7]	= null;
			$grid->columnFunctions[8] = "loadDomasire";
			$grid->columnHeaders[9] = null;
			$grid->columnHeaders[10] = null;
			$grid->columnFunctions[4] = "loadZarfiat";
			$grid->canDelete = FALSE;
		break;
		case 1 : 
			$grid->columnHeaders[6] = null;
			$grid->columnHeaders[7]	= null;
			$grid->columnFunctions[4] = "loadZarfiat";
			$grid->columnFunctions[8] = "loadDomasire";
			$grid->columnHeaders[9] = null;
			$grid->columnHeaders[10] = null;
			$grid->columnHeaders[11] = null;
			$grid->columnHeaders[12] = "قابل استرداد";
			$grid->columnFunctions[12] = "binToBool";
			$grid->addFeild("id");
			$grid->columnAccesses[count($grid->columnHeaders)-1] = 0;
			$grid->columnHeaders[13]="کمیسیون<br>(درصد)";
			$grid->columnFunctions[13] = "poorsant1";
			$grid->canDelete = FALSE;
		break;
		case 2 : 
			$grid->columnHeaders[6] = "&nbsp;&nbsp;نوع&nbsp;&nbsp;";
			$grid->columnLists[6] = parvaz_typ();
			$grid->fieldList[7] = "id";
			$grid->columnHeaders[7]	= "ذخیره";
			$grid->columnFunctions[7] = "loadCustomerZakhire";
			$grid->columnAccesses[7] = 0;
			$grid->columnHeaders[8] = "دومسیره";
			$grid->columnLists[8] = domasore();
			$grid->columnHeaders[9] = "کمیسون<br> پایه(٪)";
			$grid->columnFunctions[9] = "zarfiat1";
			$grid->columnHeaders[10] = 'مبلغ<br>خرید';
			$grid->columnFunctions[10] = "monize";
			$grid->columnHeaders[11] = null;
			$grid->columnHeaders[12] = null;
/*
			$grid->fieldList[12] = 'id';
			$grid->columnAccesses[12] = 0;
                        $grid->columnHeaders[12] = "قابل استرداد";
                        $grid->columnFunctions[12] = "esterdad";
*/
			$grid->addFeild("id");
			$grid->addFeild("id");
                        $grid->columnAccesses[count($grid->columnHeaders)-1] = 0;
			$grid->columnHeaders[13]="کمیسیون <br>درصد";
			$grid->columnFunctions[13] = "poorsant";
			$grid->columnHeaders[14]="جزئیات فروش";
			$grid->columnFunctions[14] = "parvaz_detail";
			$grid->columnAccesses[3] = 1;
			$grid->columnAccesses[4] = 0;
			$grid->columnAccesses[5] = 1;
			$grid->columnAccesses[6] = 1;
			$grid->columnAccesses[7] = 0;
                        $grid->columnAccesses[8] = 1;
                        $grid->columnAccesses[9] = 1;
			$grid->columnAccesses[10] = 1;
			$grid->canDelete = TRUE;
		break;
	}
//	$grid->columnHeaders[1] = "نام مشتری";
//grid->addFunction = "add_item";
//	$grid->deleteFunction = "delete_item";
	$grid->columnHeaders[0] = "انتخاب <br>پرواز";
	$grid->columnFunctions[0] = "selectParvaz";
        $grid->addFeild("tarikh",2);
	$grid->columnHeaders[2] = "روز";
	$grid->columnFunctions[2] = "hamed_pdate_day";
        $grid->addFeild("parvaz_id",2);
        $grid->columnHeaders[2] = "پرواز";
        $grid->columnFunctions[2] = "shomareParvaz";
        $grid->addFeild("parvaz_id",6);
        $grid->columnHeaders[6] = "مقصد";
        $grid->columnFunctions[6] = "loadCityMaghsad";
        $grid->addFeild("parvaz_id",6);
        $grid->columnHeaders[6] = "مبدأ";
        $grid->columnFunctions[6] = "loadCityMabda";
	$grid->addFeild("saat_kh",6);
	$grid->columnHeaders[6] = "ساعت<br />ورود";
	if((int)$_SESSION["customer_typ"]==2)
		$grid->columnAccesses[6] = 1;
	$grid->columnFunctions[6] = "saat";
	$grid->canAdd = FALSE;
	$grid->canEdit = TRUE;
	$grid->pageCount = 200;
	$grid->index_width = "30px";
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
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
    <script type="text/javascript" >
	function searchFlight()
	{
		 document.getElementById('search_form').submit();
	}
	function selectParvaz(obj,id)
	{
		var list = document.getElementById("selected_parvaz").value;
		if(obj.checked)
		{
			list = addToList(list,id);
		}
		else
		{
			list = removeFromList(list,id);
		}
		document.getElementById("selected_parvaz").value = list;
	}
	function addToList(list,inp)
	{
		var tmp = String(list).split(',');
		var out = Array();
		var ok = true;
		for(var i=0;i<tmp.length;i++)
		{
			if(tmp[i] == inp)
				ok = false;
			if(tmp[i] != '' && tmp[i] != null)
				out[i] = tmp[i];
		}
		if(ok)
			out[out.length] = inp;

		return(out.toString());
		
	}
	function removeFromList(list,inp)
	{
		var tmp = String(list).split(',');
		var out = Array();
		var indx = 0;
                for(var i=0;i<tmp.length;i++)
                {
                        if(tmp[i] != inp)
			{
				out[indx] = tmp[i];
				indx++;
			}
                }
		return(out.toString());
	}	
   </script>
</head>
<body>
   <div id="header" style="display:none;" style="background: #D2B48C;">
        
        <h1 align="center" ><a href="#"><?php echo lang_fa_class::title; ?></a></h1>
    </div>
    
    <div id="main" style="background: #D2B48C;"><div id="main2" style="background: #D2B48C;">	
            <div id="sidebar" style="background: #FFF8DC;">
		
                <div id="center" >
		<table width="100%" border="1" >
		<tr>
		      <td colspan="2" align="center" style="background: #F6A255;" >
				<table width='99%' >
					<tr>
						<td align='right' width='50%' >
							
								<form method="POST" id="cust_frm" >
								<b> <?php echo lang_fa_class::title." <br> ".lang_fa_class::all_manegment; ?></b>
									<?php if((int)$_SESSION["customer_typ"]==2){ ?>			
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; انتخاب مشتری:		
									<select id="cust_id" name="cust_id" class="inp" onchange="document.getElementById('cust_frm').submit();" style="width:auto;" >
										<?php echo loadCust(); ?>
									</select>
									<?php 
									}
									else if((int)$_SESSION["customer_typ"]==1)
									{
										$mosh = new customer_class($_SESSION['customer_id']);
										echo '<b>شرکت '.$mosh->name.'</b>';
									}
									 ?>
								</form>
							
						</td>
						<td width='50%' >			
							<form id="search_form" method="get">	
							<table style="background: #F6A255;color:#000000;width:99%;">
								<tr>
									<td>
										مبدأ :
									</td>
									<td>
										<select id="smabda_id" name="smabda_id" class="inp" >
										<?php
											echo loadCities($smabda_id);
										?>
										</select>
									</td>
									<td>
										مقصد :
									</td>
									<td>
										<select id="smaghsad_id" name="smaghsad_id" class="inp" >
										<?php
											echo loadCities($smaghsad_id);
										?>
										</select>
									</td>
									<td align="left" >
										دوطرفه :
									</td>
									<td>
										<input type="checkbox" id="domasire" name="domasire" <?php echo (($domasire)?"checked=\"checked\"":""); ?> />
									</td>
								</tr>
								<tr>
									<td>
										از تاریخ :
									</td>
									<td>
										<input readonly="readonly" class="inp" style="direction:ltr;" type="text" id="saztarikh" name="saztarikh" value="<?php echo $saztarikh; ?>"/>
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
									</td>
									<td>
										تا تاریخ :
									</td>
									<td>
										<input readonly="readonly" class="inp" style="direction:ltr;" type="text" id="statarikh" name="statarikh" value="<?php echo $statarikh; ?>"/>
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
									</td>
									<td colspan = "2">
										<input class="inp1" type="button" value="نمایش و بروزرسانی" onclick="searchFlight();" style="width:auto;font-weight:bold;" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			    </form>
		      </td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="background:  #FFF8DC;" >
			<form id="reserve_frm" >	
				<table width='99%' >
					<tr>
						<td align='right' width='10%' >
							<b>رزرو</b>
						</td>
						<td align="center">تعداد افراد بزرگسال:<select class="inp"  id="hamed_adl" name="hamed_adl" class="textbox"><option value="0" selected="selected">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select>
						</td>
						<td>تعداد کودکان ۲ تا ۱۱ سال:<select class="inp" id="hamed_chd" name="hamed_chd" class="textbox"><option value="0" selected="selected">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select>
						</td>
						<td>تعداد کودکان زیر ۲ سال:<select class="inp"  id="hamed_inf" name="hamed_inf" class="textbox"><option value="0" selected="selected">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select>
						</td>
						<td align="left" >
							نوع بلیت:
						</td>
						<td>
							<select class="inp"  id="ticket_type" name="ticket_type" style="width:auto;" >
								<option value="0" selected="selected">بلیط الکترونیکی<br>Eticket</option>
								<!-- <option value="1">بلیط چاپ شده</option> -->
							</select>
						</td>
						<td>
							<input  value="رزرو موقت" class="inp" id="reserve" type="button" style="width:auto;font-weight:bold;">
						<input type="hidden" id="selected_parvaz" name="selected_parvaz" value="" />
						</td>
					</tr>
				</table>
			</form>
			</td>	
		</tr>
		<tr style="vertical-align:top;" >
		<td>
	                <table width="100%" border="0">
<!--				<tr style="cursor:pointer" >
                                        <td id="grp_customer" align="center" >
                                                <table>
                                                <tr>
                                                        <td align="center">
                                                                <img src="../img/agent.png" width="64" ></img>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th>
                                                                        <?php  echo lang_fa_class::grp_customer; ?>
                                                                        </th>
                                                                </tr>
                                                                </table>
                                        </td>
                                </tr>	-->
	                	<tr style="cursor:pointer" >
	                		<td id="grp_manage" align="center" >
	                			<table>
	                			<tr>
	                				<td align="center">
	                					<img src="../img/agent.png" width="64" ></img>	
	                				</td>
	                			</tr>
	                			<tr>
	                				<th>
									<?php  echo lang_fa_class::grp_user; ?>
									</th>
								</tr>
								</table>
	                		</td>
				</tr>
				<tr style="cursor:pointer" >
					<?php
						if((int)$_SESSION['customer_typ'] == 2)
						{
					?>					
					<td id="grp_customer" align="center" >
                                                <table>
                                                <tr>
                                                        <td align="center">
                                                                <img src="../img/agent.png" width="64" ></img>
                                                        </td>
                                                </tr>
                                                <tr>
                                                        <th>
								<?php
                                                                              echo lang_fa_class::grp_customer;
								?>
                                                                        </th>
                                                                </tr>
                                                                </table>
                                        </td>
					<?php
						}
					?>
				</tr>
		                <tr style="cursor:pointer" >
	                		<td id="user_manage" align="center"  >
	                			<table>
		                			<tr>
		                				<td align="center">
		                					<img src="../img/user-icon.png" width="64" ></img>	
		                				</td>
		                			</tr>
		                			<tr>
		                				<th>
										<?php   if((int)$_SESSION['customer_typ'] == 2)
												echo lang_fa_class::user;
											else if ((int)$_SESSION['customer_typ'] == 1)
												echo 'تغییر رمز';
										?>
										</th>
									</tr>
								</table>          		
	                		</td>
				</tr>
		                <tr style="cursor:pointer" >			
	                		<td id="report2" align="center" >
	                			<table>
		                			<tr>
		                				<td align="center">
		                					<img src="../img/backup.png" width="64" ></img>	
		                				</td>
		                			</tr>
		                			<tr>
		                				<th>
										<?php  echo "گزارش روزانه"; //lang_fa_class::bandwidth; ?>
										</th>
									</tr>
								</table>    
	                		
	                		</td>
	                	</tr>
<?php if($_SESSION["customer_typ"]==1) { ?>
				<tr style="cursor:pointer;" >  		

                                        <td id="daryaft" align="center"  >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/total_report.png" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                     ثبت پرداختی 
                                                                                </th>
                                                                        </tr>
                                                                </table>
                                        </td>
	                	</tr>
				<tr style="cursor:pointer;" >  		

                                        <td id="refund" align="center"  >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/refund.jpg" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                     استرداد 
                                                                </th>
                                                        </tr>
                                               </table>
                                        </td>
	                	</tr>
<?php } ?>
<?php if($_SESSION["customer_typ"]==2) { ?>
				<tr>
					<td style="background:firebrick;color:#ffffff;" align="center" >
						اطلاعات پایه
					</td>
				</tr>
				<tr style="cursor:pointer;" >  	
					<td id="parvaz_manage" align="center">
                                        <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/plane.png" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                        <?php  echo lang_fa_class::parvaz_manage; ?>
                                                                                </th>
                                                                        </tr>
                                        </table>
                                        </td>
				</tr>
				<tr style="cursor:pointer;" >
					<td id="customers" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/hotel.jpg" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::customers; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
				</tr>
				<tr style="cursor:pointer;" >  		

                                        <td id="daryaft" align="center"  >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/total_report.png" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                     ثبت دریافتی 
                                                                                </th>
                                                                        </tr>
                                                                </table>
                                        </td>

	                	</tr>
		                <tr style="cursor:pointer;" >  
					<td id="shahr" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/kol.png" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::shahr; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
				</tr>
		                <tr style="cursor:pointer;" >  		

					<td id="sherkat_parvaz" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/moeen.jpg" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::sherkat_parvaz; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>

				</tr>
				<tr style="cursor:pointer;" >
	                		<td id="havapeima" align="center" >
	                			<table>
		                			<tr>
		                				<td align="center">
		                					<img src="../img/sabt.png" width="16" ></img>	
		                				</td>
		                			</tr>
		                			<tr>
		                				<th>
										<?php  echo lang_fa_class::havapeima; ?>
										</th>
									</tr>
								</table>	                			                		
	                		</td>
				</tr>
				<tr style="cursor:pointer;" >  		

                                        <td id="refund" align="center"  >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/refund.jpg" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                     استرداد 
                                                                </th>
                                                        </tr>
                                               </table>
                                        </td>
	                	</tr>
		                <tr style="cursor:pointer;" >  		

					<td id="main_txt" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/moeen.jpg" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo "ثبت اطلاعیه عمومی"; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>

				</tr>
				<?php } ?>
				<tr style="cursor:pointer;display:none;" >
					<td id="m_hotel" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/hotel.jpg" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::hotel; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
				</tr>
		                <tr style="cursor:pointer;display:none" >  		

		<!--			 <td id="h_grooh" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/account.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::grooh; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>-->
					<td id="h_kol" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/kol.png" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::kol; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
				</tr>
				<tr style="cursor:pointer;display:none;" >
					<td id="h_moeen" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/moeen.jpg" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::moeen; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
<!--					<td id="h_tafzili1" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/tafzili1.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::tafzili1; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
					<td id="h_tafzili2" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/tafzili2.jpg" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::tafzili2; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
				</tr>
	                	<tr>
					<td id="h_tafzilishenavar1" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/shenavar1.png" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                             <?php  echo lang_fa_class::shenavar1; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>

					<td id="h_tafzilishenavar2" align="center" >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/shenavar2.png" width="75" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::shenavar2; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>-->
	                		<td id="filter" align="center" >
	                			<table>
		                			<tr>
		                				<td align="center">
		                					<img src="../img/sabt.png" width="16" ></img>	
		                				</td>
		                			</tr>
		                			<tr>
		                				<th>
										<?php  echo "test";//lang_fa_class::filter; ?>
										</th>
									</tr>
								</table>	                			                		
	                		</td>
				</tr>
		                <tr style="cursor:pointer;display:none" >  		

				<!--	<td id="download" align="center"  >
                                                <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/gozaresh.png" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                        <?php  echo lang_fa_class::download; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>
                                        </td>-->
					<td id="backup" align="center">
                                        <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/backup.png" width="16" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                        <?php  echo lang_fa_class::backup; ?>
                                                                                </th>
                                                                        </tr>
                                        </table>
                                        </td>
				</tr>
				<tr>
					<td style="background:firebrick;color:#ffffff;" align="center" >
						برون رفت
					</td>
				</tr>
				<tr style="cursor:pointer" >
					<td id="exit" align="center">
                                        <table>
                                                        <tr>
                                                                <td align="center">
                                                                        <img src="../img/Log-Out-icon.png" width="64" ></img>
                                                                </td>
                                                        </tr>
                                                        <tr>
                                                                <th>
                                                                                        <?php  echo lang_fa_class::logout; ?>
                                                                                </th>
                                                                        </tr>
                                        </table>
                                        </td>
				</tr>
	                	<tr style="cursor:pointer" >
	                		<td  align="center"  >
					 
					 </td>
	                		<td id="progress"  align="center" style="display:none;" >
                                       		 <img src='../img/progress.gif' width='16' ></img>
                                        </td> 
	                	</tr>
	                </table>
		</td>
		<td width="90%" >
			<table width="100%" border="1">
				<tr>
	                		<td align="center" width="100%" height="500px"  >
                                       		 <?php echo $out; ?>
                                        </td> 
	                	</tr>
			</table>
		</td>
		</tr>
		</table>		
                </div>
            </div>  	              
                               
            <div class="clearing">&nbsp;</div>   
    </div></div><!-- main --><!-- main2 -->
    <div id="footer" style="display:none;" >
	<center>
        طراحی و ساخت <a href="http://www.gcom.ir/">گستره ارتباطات شرق</a><img src="../img/gcom.png" width="32" >
	</center>
    </div>
<script>
$(document).ready(function(){

    $("#grp_manage").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::grp_user; ?>",
                width: 900,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "report1.php"
        });
    });

    $("#grp_customer").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::grp_customer; ?>",
                width: 900,
                height: 600,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "customergroup.php"
        });
    });
    $("#user_manage").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت کاربران",
		showModal: true,
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "user.php"
	});
    });

    $("#reserve").click(function () {
      //$("ul").slideToggle("slow");
	var adl = document.getElementById("hamed_adl").options[document.getElementById("hamed_adl").selectedIndex].value;
	var chd = document.getElementById("hamed_chd").options[document.getElementById("hamed_chd").selectedIndex].value;
	var inf = document.getElementById("hamed_inf").options[document.getElementById("hamed_inf").selectedIndex].value;
	var ticket_type = document.getElementById("ticket_type").options[document.getElementById("ticket_type").selectedIndex].value;
	var ran = Math.random();
	var selected_parvaz = document.getElementById("selected_parvaz").value;
	window.location = "ticket_check.php?adl="+adl+"&chd="+chd+"&inf="+inf+"&selected_parvaz="+selected_parvaz+"&ticket_type="+ticket_type+"&r="+ran;     
/*
	$.window({
                title: "<?php  echo lang_fa_class::filter; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                //url: "checkflight.php?adl="+adl+"&chd="+chd+"&inf="+inf+"&selected_parvaz="+selected_parvaz+"&ticket_type="+ticket_type+"&r="+ran
		url: "ticket_check.php?adl="+adl+"&chd="+chd+"&inf="+inf+"&selected_parvaz="+selected_parvaz+"&ticket_type="+ticket_type+"&r="+ran
        });
*/
    });

    $("#customers").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "<?php  echo lang_fa_class::customers; ?>",
		width: 1000,
         	height: 500,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 50,
	        y: 30,
		url: "customers.php"
	});
    });

	$("#sherkat_parvaz").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::sherkat_parvaz; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "sherkat.php"
        });
    });

	$("#shahr").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::shahr; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "shahr.php"
        });
    });

	$("#havapeima").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::havapeima; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "havapeima.php"
        });
    });

	$("#parvaz_manage").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::parvaz_manage; ?>",
                width: 900,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "parvaz.php"
        });
    });
	$("#report2").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::bandwidth; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "report2.php"
        });
    });

	$("#main_txt").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo 'پیغام عمومی'; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "edit_main.php"
        });
    });
	$("#refund").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "استرداد",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "esterdad.php"
        });
    });

	$("#h_tafzilishenavar2").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::shenavar2; ?>",
                width: 1000,
                height: 500,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "tafzili_shenavar2.php"
        });
    });
    $("#filter").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "<?php  echo lang_fa_class::filter; ?>",
                width: 1000,
                height: 650,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 50,
                y: 30,
                url: "sanad_new.php"
        });
    });

    $("#download").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "گزارش",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 50,
	        y: 30,
		url: "gozaresh.php"
	});
    });
    $("#daryaft").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "ثیت دریافتی",
		width: 900,
         	height: 400,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:0, y:0},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 50,
	        y: 30,
		url: "daryaft.php"
	});
    });
    $("#modiryate_isargar").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت وضعیت ایثارگر",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 50,
	        y: 30,
		url: "isargar.php"
	});
    });
    $("#modiryate_sotoohe_arzyabi").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت سطوح ارزیابی",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 100,
	        y: 60,
		url: "level.php"
	});
    });
    $("#modiryate_parameter").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت پارامترهای ارزیابی",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 150,
	        y: 90,
		url: "parameter.php"
	});
    });
    $("#city").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت شهر",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 100,
	        y: 60,
		url: "city.php"
	});
    });
    $("#state").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت استان",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 100,
	        y: 60,
		url: "state.php"
	});
    });
    $("#form_asli").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "فرم ارزشیابی",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		/*url: "variable.php"*/
		url: "arzeshyabi.php"
	});
    });
    //modiryate_masadigh
    $("#modiryate_masadigh").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت مصادیق پارامترهای ارزیابی",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "masadigh.php"
	});
    });    
    //parameter_weight_admin
    $("#parameter_weight_admin").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت وزن تأثیر پارامترهای عمومی",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 200,
	        y: 120,
		url: "vazn.php"
	});
    });
   //  تعریف دوره ارزشیابی  
  $("#dore_arzeshyabi").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "مدیریت دوره‌ارزشیابی",
		width: 600,
         	height: 300,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 200,
	        y: 120,
		url: "dore_arzeshyabi.php"
	});
    });
	//گزارش  
  $("#gozaresh").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "گزارش",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "gozaresh.php"
	});
    });
 	$("#natije").click(function () {
      //$("ul").slideToggle("slow");
	$.window({
		title: "نتیجه",
		width: 1000,
         	height: 650,
		content: $("#window_block8"),
	        containerClass: "my_container",
	        headerClass: "my_header",
	        frameClass: "my_frame",
	        footerClass: "my_footer",
	        selectedHeaderClass: "my_selected_header",
	        createRandomOffset: {x:200, y:150},
	        showFooter: false,
	        showRoundCorner: true,
	        x: 0,
	        y: 0,
		url: "natije.php"
	});
    });
    $("#restore").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "بروزرسانی نسخه پشتیبان",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "restore.php"
        });
    });
 $("#backup").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "پشتیبان گیری",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "backup.php"
        });
    });
    $("#changepass").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "تغییر رمز عبور",
                width: 600,
                height: 300,
                content: $("#window_block8"),
                containerClass: "my_container",
                headerClass: "my_header",
                frameClass: "my_frame",
                footerClass: "my_footer",
                selectedHeaderClass: "my_selected_header",
                createRandomOffset: {x:200, y:150},
                showFooter: false,
                showRoundCorner: true,
                x: 200,
                y: 120,
                url: "changepass.php"
        });
    });
    $("#exit").click(function () {
    		if(confirm("آیا مایل به خروج هستید؟")){window.location = "login.php?stat=exit&";}
    });

  });
</script>
<?php 
	}
	else
	{
		header("Location: login.php");
	}
?>
</body>
</html>
