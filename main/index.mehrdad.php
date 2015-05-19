<?php
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	$admin_msg = '';
	$mysql = new mysql_class;
	$mysql->ex_sql('select `content` from `admin` where `app` = \''.$conf->app.'\'',$qqqq);
	if(isset($q[0]))
		$admin_msg = $q[0]['content'];
	$pass=((isset($_REQUEST['pass']))?$_REQUEST['pass']:"");
	$user=((isset($_REQUEST['user']))?$_REQUEST['user']:"");
	ticket_class::clearTickets();
	if (((isset($_SESSION[$conf->app.'_user_id']) && isset($_SESSION[$conf->app.'_typ']))))
	{

	}	
	date_default_timezone_set("Asia/Tehran");
	$firstVisit = (isset($_SESSION[$conf->app."_login"]) && ($_SESSION[$conf->app."_login"] == 1) && isset($_REQUEST["user"]));
	if($firstVisit ||(isset($_SESSION[$conf->app."_user_id"]))){	
	function loadUserById($id){
		$mysql = new mysql_class;
		$out = 'تعریف نشده';
		$mysql->ex_sql("select fname,lname from user where id=$id",$qq);
		if(isset($qq[0]))
			$out = $qq[0]["fname"]." ".$qq[0]["lname"];
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
		$ismodir = (((isset($_SESSION[$conf->app."_customer_typ"])) && ((int)$_SESSION[$conf->app."_customer_typ"]==2) && ((int)$_SESSION[$conf->app."_typ"]==0))?TRUE:FALSE);
		$parvazismine = TRUE;
		$typ = -1;
		$jid = 0;
		$mysql = new mysql_class;
		$mysql->ex_sql("select `tarikh`,`saat`,`typ`,`j_id` from `parvaz_det` where `id`=$inp",$q);
		if(isset($q[0]))
		{
			$r = $q[0];
			$tarikh = strtotime($r['tarikh']);
			$saat = strtotime($r['saat']);
			$typ = (int)$r['typ'];
			$jid = (int)$r['j_id'];
		}
		$out = '&nbsp;';
		if( (($tarikh>$today) || ($tarikh==$today && $saat>$time_new)) && ($ismodir || ($typ==0) || (($typ==1) && ($parvazismine))) )
		{
			$out = "<input type=\"checkbox\" id=\"parvaz_$inp\" name=\"parvaz_$inp\" onclick=\"selectParvaz(this,$inp);\" />";
			if($ismodir && $jid == 1)
				$out .= "<br/><img style=\"cursor:pointer;\" onclick=\"openjid('$inp');\" src=\"../img/twoway.png\" alt=\"ﺶﻣﺎﻫﺪﻫ پﺭﻭﺍﺯ ﺏﺭگﺶﺗ\" width=\"20px\" />";
		}
		else if($typ == 2)
			$out = "<img src=\"../img/tel.png\" alt=\"تلفنی\" width=\"20px\" />";
		return($out);
	}
	function loadCity($inp)
	{
		$inp = (int)$inp;
		$out = "";
		$mysql = new mysql_class;
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
        function loadCityMabda($inp)
        {
                $inp = (int)$inp;
                $parvaz = new parvaz_class($inp);
                $out = "&nbsp;";
                if($parvaz->getId() >0)
                        $out = loadCity($parvaz->mabda_id);
                return($out);
        }
        function loadCityMaghsad($inp)
        {
                $inp = (int)$inp;
                $parvaz = new parvaz_class($inp);
                $out = "&nbsp;";
                if($parvaz->getId() >0)
                        $out = loadCity($parvaz->maghsad_id);
                return($out);
        }
	function shomareParvaz($inp)
	{
                $out = "&nbsp;";
                $inp = (int)$inp;
                $parvaz = new parvaz_class($inp);
                if($parvaz->getId() >0)
                        $out = $parvaz->shomare;
                return($out);
	}
	function loadHavapeima($inp)
	{
		$inp = (int)$inp;
		$out = "&nbsp;";
		$mysql = new mysql_class;
		$mysql->ex_sql("select `name` from `havapeima` where `id` ='$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
        function loadSherkat($inp)
        {
                $inp = (int)$inp;
                $out = "&nbsp;";
		$mysql = new mysql_class;
                $mysql->ex_sql("select `name` from `sherkat` where `id` ='$inp'",$q);
                if(isset($q[0]))
                        $out = $q[0]["name"];
                return($out);
        }
	function loadParvazInfo($inp)
	{
		$out = "&nbsp;";
		$inp = (int)$inp;
		$parvaz = new parvaz_class($inp);
		if($parvaz->getId() >0)
			$out = loadHavapeima($parvaz->havapiema_id)."<br/>".loadSherkat($parvaz->sherkat_id);
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
			$inp = 9;
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
		if((int)$_SESSION[$conf->app.'_customer_typ']==2 && (int)$_SESSION[$conf->app.'_typ']==0 )
		{
			$color = "blue";
			$out = $par->getZarfiat();
		}
		else
		{
			$color = "black";
			$out = (($par->getZarfiat((int)$_SESSION[$conf->app.'_customer_id'])<10)?$par->getZarfiat((int)$_SESSION[$conf->app.'_customer_id']):9);
		}
		$zarfiat = enToPerNums($out);
		if($out == 0)
		{
			$color = "red";
			$zarfiat = "CLOSED";
		}
		$out = "<span style=\"color:$color;cursor:pointer;\" ".(((int)$_SESSION[$conf->app.'_customer_typ']==2)?" onclick=\"wopen('zarfiat.php?parvaz_det_id=$inp&','',600,400);\"":"").">$zarfiat</span>";
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
		$customer_id = $_SESSION[$conf->app."_customer_id"];
		$cust = new customer_class($customer_id);
		$out = "<span style=\"color:firebrick;cursor:pointer;\" onclick=\"wopen('setpoorsant.php?parvaz_det_id=$inp&','',600,200);\">".enToPerNums($cust->getPoorsant($inp))."%</span>";
		return ($out);
	}
	function poorsant1($inp)
	{
		$customer_id = $_SESSION[$conf->app."_customer_id"];
		$cust = new customer_class($customer_id);
		$out = enToPerNums($cust->getPoorsant($inp));
		return ($out).'%';
	}
	function loadCities($smabda_id = -1)
	{
		$smabda_id = (int)$smabda_id;
		$out ="<option value=\"-1\">\nهمه\n</option>\n";
		$mysql = new mysql_class;
		$mysql->ex_sql("select * from `shahr` order by `name`",$q);
		foreach($q as $r)
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
		$mysql = new mysql_class;
		$mysql->ex_sql("select id,name from customers where `en` = 1 and `typ` <> 3 order by name ",$q);
		foreach($q as $r)
			if((int)$r["id"]==(int)$_SESSION[$conf->app."_customer_id"])
			{
				$sel = "selected=\"selected\"";
			}
			else
			{
				$sel ='';
			}
			$out.="<option $sel value=\"".$r["id"]."\">".$r["name"]."</option>\n";
		return $out;
	}
	function parvaz_detail($inp)
	{
		$inp = (int)$inp;
		$toz = loadTozih($inp);
		if($toz == '')
			$toz="«»";
		$movaghat = 0;
		$mysql = new mysql_class;
		$mysql->ex_sql("select SUM(`tedad`) as `jam` from `reserve_tmp` where `parvaz_det_id`=$inp ",$qq);
		if(isset($q[0]))
			$movaghat = (int) $q[0]['jam'];
		$mysql->ex_sql("select `id` from `ticket` where `en`='1' and `adult`<>'2' and parvaz_det_id=$inp",$q);
		$out = count($q);
		if($out==0)	
			$out ='0';
		$out .="&nbsp;<span style=\"color:firebrick;cursor:pointer;\" onclick=\"wopen('parvaz_forookhte.php?parvaz_det_id=$inp&','',600,120);\"> جزئیات</span>&nbsp;&nbsp;$movaghat";
		$out .='<br />'.esterdad($inp).'<br />';
		$out .="&nbsp;<span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('manifest.php?parvaz_det_id=$inp&','',800,600);\"> فهرست</span>";
		if($conf->tozihatEnabled)
			$out .= "<br/><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('tozih.php?parvaz_det_id=$inp&','',800,600);\">$toz"."</span>";
		return $out;
	}
	function loadCustomerZakhire($inp)
	{
		//$out = "ذخیره $inp";
		$inp = (int)$inp;
		$parvaz_det = new parvaz_det_class($inp);
		$zakhire = enToPerNums($parvaz_det->zarfiat - $parvaz_det->getZarfiat());
		$out = " <span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('zakhire.php?parvaz_det_id=$inp&','',600,400);\">$zakhire</span> ";
		return($out);
	}
	function loadDomasire($inp)
	{
		$inp = (int)$inp;
		$out = "";
		if($inp == 0)
			$out ='';
		else if($inp == 1)
			$out = "<img src=\"../img/twoway.png\" alt=\"دومسیره\" title=\"دومسیره\" />";
		return($out);	
	}
	function binToBool($inp)
	{
		$inp = (int)$inp;
		//return((($inp!=0)?"قابل استرداد":"غیرقابل استرداد"));
		return((($inp!=0)?"<img src=\"../img/refund1.png\" alt=\"قابل استرداد\" title=\"قابل استرداد\"/>":"<img src=\"../img/norefund.png\" alt=\"غیر قابل استرداد\" title=\"غیر قابل استرداد\"/>"));
	}
	function loadDomasireBin($id)
	{
		$parvaz_det = new parvaz_det_class((int)$id);
		$out = binToBool($parvaz_det->can_esterdad);
		$out.= "<span style=\"cursor:pointer;\" onclick=\"openjid('$id');\" >".loadDomasire($parvaz_det->j_id)."</span>";
		$out.= '<br/>'.loadTozih($id);
		return($out);
	}
	function esterdad($inp)
	{
		$inp = (int)$inp;
		$ester = 1;
		$mysql = new mysql_class;
		$mysql->ex_sql("select `can_esterdad` from `parvaz_det` where `id`=$inp",$q);
		if(isset($q[0]))
			$ester = (int)$r["can_esterdad"];
		$out = binToBool($ester);
		$color = "red";
		if($ester == 1)
			$color = "blue";
		$out = " <span style=\"color:$color;cursor:pointer;\" onclick=\"window.open('changeesterdad.php?id=$inp&ester=$ester&');\">$out</span> ";
		return($out);
	}
	function loadParvazRangs()
	{
		$out = "";
		$mysql = new mysql_class;
		$mysql->ex_sql("select `id`,`rang` from `parvaz`",$q);
		foreach($q as $r)
			if($r["rang"] != "")
				$out .= $r["id"].":\"".$r["rang"]."\",";
		if(strlen($out)>0)
			$out = substr($out,0,-1);
		$out = "var par_array = { $out };";
		return($out);
	}
	function delete_item($id)
	{
		$mysql = new mysql_class;
		$mysql->ex_sqlx("update `parvaz_det` set `en` = 0 where `id` = $id");
	}
	function edit_item($id,$field,$value)
        {
		$lastvalue="";
		$mysql = new mysql_class;
		$mysql->ex_sql("select `$field` from `parvaz_det` where `id`='$id'",$q);
		if (isset($q[0]))
			$lastvalue=$q[0][$field];
                $arg["toz"]="اصلاح فیلد $field از id $id به مقدار $value از مقدار $lastvalue";
		$arg["user_id"]=$_SESSION[$conf->app."_user_id"];
		$arg["host"]=$_SERVER["REMOTE_ADDR"];
		$arg["page_address"]=$_SERVER["SCRIPT_NAME"];
		$arg["typ"]=2;
		log_class::add($arg);
		switch($field)
		{
			case 'mablagh_kharid':
				$par = new parvaz_det_class((int)$id);
				$par->mablagh_kharid = $value - $lastvalue;
				$par->kharidParvaz($par->zarfiat,'بابت تغییر قیمت خرید پرواز از '.$lastvalue.'  به '.$value);
				break;
			case 'customer_id':
				$par = new parvaz_det_class((int)$id);
				$par->mablagh_kharid = -1 * $par->mablagh_kharid;
				$cust1 = new customer_class($par->customer_id);
				$cust1 = (($par->customer_id>0)?$cust1->name:'مدیریت');
				$cust2 = new customer_class($value);
                                $cust2 = (($value>0)?$cust2->name:'مدیریت');
                                $par->kharidParvaz($par->zarfiat,'بابت تغییر فروشنده از '.$cust1.' به '.$cust2);
				$par->mablagh_kharid = -1 * $par->mablagh_kharid;
				$par->customer_id = $value;
				$par->kharidParvaz($par->zarfiat,'بابت تغییر فروشنده از '.$cust1.' به '.$cust2);
				break;
			case 'j_id':
				if($lastvalue == 0 && $value == 1)
				{
					$GLOBALS['extraScript'] = "openjid('$id');";
				}
				break;
		}
		$mysql->ex_sqlx("update `parvaz_det` set `$field`='$value' where `id`='$id'");
        }
	function loadCust1()
	{
		$out = null;
		$mysql = new mysql_class;
		$mysql->ex_sql('select `name`,`id` from `customers` order by `name`',$q);
		foreach($q as $r)
			$out[$r['name']] = (int)$r['id'];
		return($out);
	}
        function loadTozih($inp)
        {
                $toz = '';
		$mysql = new mysql_class;
                $mysql->ex_sql("select `tozihat` from `parvaz_tozihat` where `parvaz_det_id`=$inp",$q);
                if(isset($q[0]))
                        $toz = '«'.$q[0]['tozihat'].'»';
                return($toz);
        }
	if($firstVisit){
		//echo "+++++++first+++++++";
		$is_modir  = FALSE;
		$mysql->ex_sql("select * from user where user = '".$user."'",$q);
		
		if(isset($q[0]))
		{
			$r_u = $q[0];
			if($pass == $r_u["pass"] ){
				$_SESSION[$conf->app."_typ"]=(int)$r_u["typ"];
				$_SESSION[$conf->app."_user_id"] = (int)$r_u["id"];
				$_SESSION[$conf->app."_app"] = $conf->app;
				$user = new user_class((int)$r_u['id']);
				$user->setOnline(session_id());
                                $user->sabt_vorood();				
				$cust = new customer_class((int)$r_u["customer_id"]);
				if(($_SESSION[$conf->app."_typ"] == 0 || $_SESSION[$conf->app."_typ"] == 2) && $cust->typ == 2)
					$_SESSION[$conf->app."_customer_typ"] = $cust->typ;
				else
					$_SESSION[$conf->app."_customer_typ"] = 1;
				$_SESSION[$conf->app."_customer_id"] = (int)$r_u["customer_id"];
			}else{
				die("<script>window.location = 'login.php?stat=wrong_pass&';</script>");
			}
		}else{
			die("<script>window.location = 'login.php?stat=wrong_user&';</script>");
		}
	}
	$user_id = (int)$_SESSION[$conf->app."_user_id"];
	if(isset($_REQUEST["cust_id"]))
		$_SESSION[$conf->app."_customer_id"] = (int) $_REQUEST["cust_id"];
	$today = date("Y-m-d");
	$time_now = date("H:i:s",strtotime(date("H:i:s")." + 1 hour"));
	$aftertomarrow = date("Y-m-d",strtotime($today.' + '.$conf->duration_time));
	$smabda_id = ((isset($_REQUEST["smabda_id"]))?$_REQUEST["smabda_id"]:-1);
	$smaghsad_id = ((isset($_REQUEST["smaghsad_id"]))?$_REQUEST["smaghsad_id"]:-1);
	$domasire = FALSE;
	$customer = new customer_class((int)$_SESSION[$conf->app."_customer_id"]);
	$domasire = ((isset($_REQUEST["domasire"]) && $_REQUEST["domasire"])?TRUE:FALSE);
	$saztarikh =((isset($_REQUEST["saztarikh"]))?$_REQUEST["saztarikh"]:hamed_pdate1($today));
	$statarikh = ((isset($_REQUEST["statarikh"]))?$_REQUEST["statarikh"]:hamed_pdate1($aftertomarrow));
	if($_SESSION[$conf->app.'_customer_typ']!=2 &&  strtotime(hamed_pdateBack(perToEnNums($saztarikh)))<strtotime($today) )
		$saztarikh=hamed_pdate1($today);
	$whereClause = " ((`tarikh` >= '".hamed_pdateBack(perToEnNums($saztarikh))."' and `tarikh` <= '".hamed_pdateBack(perToEnNums($statarikh))."') or (`tarikh`='".hamed_pdateBack(perToEnNums($saztarikh))."' and `saat`>='$time_now'))";
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
		$whereClause .= "and  parvaz_id in (select id from `parvaz` where $whereClause1)";
	$whereClause .= " and `en` = 1 order by `tarikh`,`saat`,`parvaz_id`";
	if(!isset($_REQUEST['smabda_id']))
		$whereClause = '1=0';
	$forms = null;
	$out = "";
/*
	$grid = new jshowGrid_new("parvaz_det","grid1");
	$grid->width = "99%";
	$grid->columnHeaders[1]="هواپیمایی";
	$grid->columnFunctions[1] ="loadParvazInfo";
	$grid->columnHeaders[2]="تاریخ";
	$grid->columnFunctions[2] = "hamed_pdate";
	$grid->columnHeaders[3]="ساعت";
	$grid->columnFunctions[3] = "saat";
	$grid->fieldList[4] = 'id';
	$grid->columnHeaders[4]="ظرفیت";
	$grid->columnFunctions[4] = "loadZarfiat";
	$grid->columnHeaders[5]="قیمت";
	$grid->columnFunctions[5] = "monize";
	$grid->columnHeaders[8] = "ملاحظات";
	$grid->whereClause  =$whereClause;
	for($indx = 0;$indx < count($grid->columnHeaders);$indx++)
	{
		$grid->columnAccesses[$indx] = 0;
	}
	switch ($_SESSION[$conf->app."_customer_typ"])
	{
		case 0 : 
			$grid->columnHeaders[6] = null;
			$grid->columnHeaders[7]	= null;
			$grid->columnFunctions[8] = "loadDomasireBin";
			$grid->columnHeaders[9] = null;
			$grid->columnHeaders[10] = null;
			$grid->columnFunctions[4] = "loadZarfiat";
			$grid->canDelete = FALSE;
		break;
		case 1 : 
			$grid->columnHeaders[6] = null;
			$grid->columnHeaders[7]	= null;
			$grid->columnFunctions[4] = "loadZarfiat";
			$grid->fieldList[8] = 'id';
			$grid->columnFunctions[8] = "loadDomasireBin";
			$grid->columnHeaders[9] = null;
			$grid->columnHeaders[10] = null;
			$grid->columnHeaders[11] = null;
			$grid->columnHeaders[14] = null;
			$grid->columnHeaders[12] = null;
			$grid->columnHeaders[13] = null;
			$grid->addFeild("id",8);
			$grid->columnAccesses[count($grid->columnHeaders)-1] = 0;
			$grid->columnHeaders[8]="کمیسیون";
			$grid->columnFunctions[8] = "poorsant1";
			$grid->canDelete = FALSE;
		break;
		case 2 :
			$grid->columnHeaders[6] = "&nbsp;&nbsp;نوع&nbsp;&nbsp;";
			$grid->columnLists[6] = parvaz_typ();
			$grid->columnHeaders[7]	= null;
			$grid->columnHeaders[8] = "محدودیت";
			$grid->columnLists[8] = domasore();
//			$grid->columnJavaScript[8] = ' ondblclick="domasireWindow(this);" ';
			$grid->columnHeaders[9] = null;
			$grid->columnHeaders[10] = 'مبلغ<br>خرید';
			$grid->columnFunctions[10] = "monize";
			$grid->columnHeaders[11] = null;
			$grid->columnHeaders[12] = null;
                        $grid->columnHeaders[13] = null;
			$grid->addFeild("id");
		//	$grid->addFeild("id");
                        $grid->columnAccesses[count($grid->columnHeaders)-1] = 0;
			$grid->columnHeaders[14]="فروشنده";
			$grid->columnLists[14] = loadCust1();
			$grid->columnHeaders[15]="جزئیات فروش";
			$grid->columnFunctions[15] = "parvaz_detail";
			$grid->columnAccesses[3] = 1;
			$grid->columnAccesses[4] = 0;
			$grid->columnAccesses[5] = 1;
			$grid->columnAccesses[6] = 1;
                        $grid->columnAccesses[8] = 1;
                        $grid->columnAccesses[9] = 1;
			$grid->columnAccesses[10] = 1;
			$grid->columnAccesses[14] = 1;
			$grid->canDelete = TRUE;
		break;
	}
//	$grid->columnHeaders[1] = "نام مشتری";
//grid->addFunction = "add_item";
	$grid->deleteFunction = "delete_item";
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
	if((int)$_SESSION[$conf->app."_customer_typ"]==2)
	{
	        $grid->addFeild("id",11);
        	$grid->columnHeaders[11] = "ذخیره";
	        $grid->columnFunctions[11] = "loadCustomerZakhire";
        	$grid->columnAccesses[11] = 0;
		$grid->addFeild("poor_def",12);
		$grid->columnHeaders[12] = "کمیسیون<br/>پایه(٪)";
		$grid->columnFunctions[12] = "zarfiat1";
		$grid->columnAccesses[12] = 1;
		$grid->columnJavaScript[12] = "onkeyup=\"findOthers(this);\"";
		$grid->addFeild("id",13);
		$grid->columnHeaders[13]="کمیسیون<br/>درصد";
		$grid->columnFunctions[13] = "poorsant";
	}
	if((int)$_SESSION[$conf->app."_customer_typ"]==2)
		$grid->columnAccesses[6] = 1;
	$grid->columnFunctions[6] = "saat";
	$grid->columnJavaScript[6] = "onkeyup=\"findOthers(this);\"";
	$grid->canAdd = FALSE;
	$grid->canEdit = TRUE;
	$grid->pageCount = 200;
	$grid->index_width = "30px";
	$grid->echoQuery = FALSE;
	$grid->editFunction="edit_item";
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
*/
	$out = '';
	$tm = new user_class($_SESSION[$conf->app."_user_id"]);
	$tm = new customer_class($tm->customer_id);
	$c_typ = $tm->typ;
	$customer_info =((int)$c_typ==3)?'':" اعتبار مالی : ".enToPerNums(monize(($customer->max_amount)))." ریال<br/> مهلت پرداخت : نامحدود";
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
	function findOthers(obj)
	{
		var inps = document.getElementsByName(obj.name);
		for(var i=0;i < inps.length;i++)
			inps[i].value = obj.value;
	}
	function checkEticket(self_obj,obj)
	{
		if(self_obj.checked==true)
		{
			obj.selectedIndex = 0;
		}
		else
		{
			obj.selectedIndex = 1;	
		}
	}
    function openjid(id)
    {
        $.window({
                title: "تغیین پرواز برگشتی",
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
                url: "setjid.php?parvaz_det_id="+id+"&r="+Math.random()+"&"
        });
    }
	var t = setTimeout("incr();",1000);
	function incr()
	{
		var tim = document.getElementById('tim');
		var noe = tim.value;
		var tmp = noe.split(':');
		var m = parseInt(tmp[0],10);
		var s = parseInt(tmp[1],10);
		if(s < 59)
			s++;
		else
		{
			s = 0;
			m++;
		}
		tim.value = m+":"+s;
		t = setTimeout("incr();",1000);
	}
   </script>
	<style type="text/css">
		table.sample {
			border-width: 1px;
			border-spacing: 2px;
			border-style: none;
			border-color: gray;
			border-collapse: separate;
			background-color: white;
		}
		table.sample th {
			border-width: 1px;
			padding: 1px;
			border-style: dashed;
			border-color: gray;
			background-color: white;
			-moz-border-radius: ;
		}
		table.sample td {
			border-width: 1px;
			padding: 1px;
			border-style: dashed;
			border-color: gray;
			background-color: white;
			-moz-border-radius: ;
			text-align: right;
		}
	</style>
	</head>
<body style="background-color:#ffffff;">
   <div id="header" style="display:none;" >
        
        <h1 align="center" ><a href="#"><?php echo lang_fa_class::title; ?></a></h1>
    </div>
    
    <div id="main" ><div id="main2" >	
            <div id="sidebar" >
		
                <div id="center" >
		
		<table width="100%" border="0" >
		<tr>
		      <td colspan="2" align="center" style="background: #fff;" ><!-- #76B8F6; -->
				<table width='99%' >
					<tr>
						<td width='5%'>
							<img width="150px" src="../img/arm.png" style="vertical-align: top;" />
						</td>
						<td align='center' width='75%' >
							
								<form method="POST" id="cust_frm" >
								<img width="150px" src="../img/radan.png" style="vertical-align: top;" />
									<?php if((int)$_SESSION[$conf->app."_customer_typ"]==2 && (int)$_SESSION[$conf->app."_typ"]==0){ ?>			
									<br/>انتخاب مشتری:		
									<select id="cust_id" name="cust_id" class="inp" onchange="document.getElementById('cust_frm').submit();" style="width:auto;" >
										<?php echo loadCust(); ?>
									</select>
									<?php 
									}
									else if((int)$_SESSION[$conf->app."_customer_typ"]==1)
									{
										$mosh = new customer_class($_SESSION[$conf->app.'_customer_id']);
										echo '<br/><b>'.$mosh->name.'</b>';
									}
									 ?>
								</form>
							
						</td>
						<td width='20%' align="left" valign="top" ><b>
							<span style="font-size:13px;">
								<?php echo $customer_info; ?>
							</span></b>
						</td>
					</tr>
				</table>
		      </td>
		</tr>
		<tr>
					<td width="100%" >
	                <table cellpadding="0" border="1" width="100%" style="border-style:solid;border-width:1px;border-color:#ffffff;" >

	                	<tr style="cursor:pointer" >
					<?php
						if($c_typ != 3)
						{
					?>
	                		<td id="grp_manage" align="center" class="topmenu" >
	                			<table class="topmenu" >
	                			<tr>
	                				<th>
									<?php  echo lang_fa_class::grp_user; ?>
									</th>
								</tr>
								</table>
	                		</td>
					<?php
						if((int)$_SESSION[$conf->app.'_customer_typ'] == 2)
						{
					?>					
<!--
					<td id="grp_customer" align="center" class="topmenu" >
                                                <table>
                                                <tr>
                                                        <th>
								<?php
                                                                              echo lang_fa_class::grp_customer;
								?>
                                                                        </th>
                                                                </tr>
                                                                </table>
                                        </td>
-->
					<?php
						}
					?>
	                		<td id="user_manage" align="center"  class="topmenu"  >
	                			<table>
		                			<tr>
		                				<th>
										<?php   if((int)$_SESSION[$conf->app.'_customer_typ'] == 2)
												echo lang_fa_class::user;
											else if ((int)$_SESSION[$conf->app.'_customer_typ'] == 1)
												echo 'تغییر‌رمز';
										?>
										</th>
									</tr>
								</table>          		
	                		</td>
	                		<td id="report2" align="center"  class="topmenu"  >
	                			<table>
		                			<tr>
		                				<th>
										<?php  echo "گزارش‌روزانه"; //lang_fa_class::bandwidth; ?>
										</th>
									</tr>
								</table>    
	                		
	                		</td>
                                        <td id="sanad_gozaresh" align="center"  class="topmenu"  >
                                                <table>
                                                        <tr>
                                                                <th>
									گزارش‌اسناد
                                                                </th>
                                                        </tr>
                                                </table>

                                        </td>
					<?php
					if($_SESSION[$conf->app."_customer_typ"]==1) 
					{ 
					?>
                                        <td id="daryaft" align="center"  class="topmenu"  >
                                                <table>
                                                        <tr>
                                                                <th>
                                                                                     ثبت‌پرداختی 
                                                                                </th>
                                                                        </tr>
                                                                </table>
                                        </td> 		

                                        <td id="refund" align="center"  class="topmenu"  >
                                                <table>
                                                        <tr>
                                                                <th>
                                                                                     استرداد 
                                                                </th>
                                                        </tr>
                                               </table>
                                        </td>
<?php } ?>
<?php if($_SESSION[$conf->app."_customer_typ"]==2) { ?>
					<td id="parvaz_manage" align="center"  class="topmenu"  >
                                        <table>
                                                        <tr>
                                                                <th>
                                                                                        <?php  echo lang_fa_class::parvaz_manage; ?>
                                                                                </th>
                                                                        </tr>
                                        </table>
                                        </td>
					<td id="customers" align="center"  class="topmenu"  >
                                                <table>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::customers; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>		

                                        <td id="daryaft" align="center"  class="topmenu"  >
                                                <table>
                                                        <tr>
                                                                <th>
                                                                                     ثبت‌دریافتی 
                                                                                </th>
                                                                        </tr>
                                                                </table>
                                        </td> 
					<td id="shahr" align="center"  class="topmenu" >
                                                <table>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::shahr; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>
				
					<td id="sherkat_parvaz" align="center"  class="topmenu" >
                                                <table>
                                                        <tr>
                                                                <th>
                                                                                <?php  echo lang_fa_class::sherkat_parvaz; ?>
                                                                                </th>
                                                                        </tr>
                                                                </table>

                                        </td>

					<td id="havapeima" align="center" class="topmenu"  >
	                			<table>
		                			<tr>
		                				<th>
										<?php  echo lang_fa_class::havapeima; ?>
										</th>
									</tr>
								</table>	                			                		
	                		</td>
                                        <td id="refund" align="center" class="topmenu"   >
                                                <table>
                                                        <tr>
                                                                <th>
                                                                                     استرداد 
                                                                </th>
                                                        </tr>
                                               </table>
                                        </td>
					<td id="addv" align="center" class="topmenu"   >
                                                <table>
                                                        <tr>
                                                                <th>
                                                                                ثبت‌تبلیغات 
                                                                </th>
                                                        </tr>
                                               </table>
                                        </td>
				<?php }} ?>
					<td id="exit" align="center" class="topmenu" >
                                        <table>
                                                        <tr>
                                                                <th>
                                                                                        <?php  echo lang_fa_class::logout; ?>
                                                                </th>
                                                        </tr>
                                        </table>
                                        </td>
                <script>
                </script>
                                        <td class="topmenu" style="cursor:default;" width="100%">
                                        &nbsp;
				                زمان آخرین بروزرسانی : <input id="tim" style="color:#9f2f2f;width:70px;font-size:10px;border-style:none;" class="topmenu" readonly="readonly" value="0:0" />
                                       	</td>
	                	</tr>
	                </table>
		</td>
		</tr>
		<tr>
			<td width='100%'>
				<input type="hidden" id="index" value="0" />
				<div id="aj">
				</div>
				
				<script language="javascript">
					setTimeout('loadslide();',5000);
				</script>
			</td>
		</tr>
		<tr height='60px'>
				<td width='60%' colspan="2" align="center" height="70px" >			
							<form id="search_form" method="get">	
							<table style="width:22cm;border-width:1px;border-style:dashed;border-collapse:collapse;border-color:#BCBCBC;">
								<br/>
								<br/>
								<br/>
								<tr  style="background-color:#EEEEEE;">
									<th>
										مبدأ :
									</th>
									
									<th>
										مقصد :
									</th>
																
									<th>
										از تاریخ :
									</th>
									
									<th>
										تا تاریخ :
									</th>
									
									<td align="right" >
										دوطرفه :
										<input type="checkbox" checked="checked" id="domasire" name="domasire" <?php echo (($domasire)?"checked=\"checked\"":""); ?> />
									</td>
								</tr>
								<tr >
									<td>
										<select id="smabda_id" name="smabda_id" class="inp" >
										<?php
											echo loadCities($smabda_id);
										?>
										</select>
									</td>
									<td>
										<select id="smaghsad_id" name="smaghsad_id" class="inp" >
										<?php
											echo loadCities($smaghsad_id);
										?>
										</select>
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
									<td>
										<img src="../img/search.jpg" style="cursor:pointer;" onclick="searchFlight();" > 
										<input style="display:none;" type="button" value="نمایش و بروزرسانی"  />
									</td>
								</tr>
							</table>
							<br/>
							<br/>
							<br/>
						</form>
						</td>
		</tr>
		<tr style="vertical-align:top;" >

		<td width="90%" colspan="2" >
			<table width="100%" border="1">
				<tr>
	                		<td align="center" width="100%"   >
                                       		 <?php 
							if(isset($_REQUEST['smabda_id'])) 
								echo $out;
							else
								echo '<br/><br/><br/><br/>';
						 ?>
                                        </td> 
	                	</tr>
			</table>
		</td>
		</tr>
		<tr>
			<td colspan="2" align="center" style="background-color: #FFFFFF;" >
			<form id="reserve_frm" >
				<br/>	
				<table width='30%' style="border-width:1px;border-style:dashed;border-color:gray"  >
<!-- style="border-width:1px;border-style:dashed;border-collapse:collapse;border-color:#BCBCBC;" -->
					<tr >
						
						<td align="center"  >تعدادبزرگسال</td>
						<td align="center"  >تعداد کودک </td>
						<td align="center"  >تعدادنوزاد</td>
					</tr>
					<tr>
						<?php
							if((int)$_SESSION[$conf->app.'_customer_typ'] == 2 &&  (int)$_SESSION[$conf->app.'_typ'] == 0)
							{
						?>
						<td align="center"><input class="inp" style="width:50px;"  id="hamed_adl" name="hamed_adl" class="textbox"/>
						</td>
						<td align="center"><input class="inp" style="width:50px;"  id="hamed_chd" name="hamed_chd" class="textbox"/>
						</td>
						<td align="center"><input class="inp" style="width:50px;"  id="hamed_inf" name="hamed_inf" class="textbox"/>
						</td>
						<?php		
							}
							else
							{
						?>
						<td align="center"><select class="inp" style="width:50px;"  id="hamed_adl" name="hamed_adl" class="textbox"><option value="0" selected="selected">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select>
						</td>
						<td align="center"><select class="inp" style="width:50px;" id="hamed_chd" name="hamed_chd" class="textbox"><option value="0" selected="selected">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select>
						</td>
						<td align="center"><select class="inp"  style="width:50px;" id="hamed_inf" name="hamed_inf" class="textbox"><option value="0" selected="selected">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select>
						</td>
						<?php
							}
						?>
					</tr>
					<tr>
						<td align="center" colspan="6" >
							بلیط الکترونیکی-Eticket
							<input style="display:none"  type="checkbox" id="ticket_checkbox"  name="ticket_checkbox" checked="checked" onclick="checkEticket(this,document.getElementById('ticket_type'));" >
							<select class="inp"  id="ticket_type" name="ticket_type" style="display:none;" >
								<option value="0" selected="selected">بلیط الکترونیکی<br>Eticket</option>
								<option value="1">بلیط چاپ شده</option>
							</select> 
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center"  >
							<!-- <input  value="رزرو پرواز" class="dokme" id="reserve" type="button" style="width:auto;font-weight:bold;"> -->
						<img src="../img/reserve.png" id="reserve" style="cursor:pointer;" >
						<input type="hidden" id="selected_parvaz" name="selected_parvaz" value="" />
						</td>
					</tr>
				</table>
			</form>
			</td>	
		</tr>
		<tr>
			<td align="center" >
				<br/>
				<table class="sample" >
		                        <tr  > 
		                                <td style="background-color:#eee;" ><b>راهنما</b> </td>
		                                <td style="background-color:#eee;" >
		                                        &nbsp;
		                                </td>
		                        </tr>
		                        <tr>
						<td  >
		                                        <img src="../img/twoway.png"/>
		                                </td>
		
		                                <td >
							نشانگر اجبار فروش دو طرفه ی پرواز می باشد. جهت دریافت اطلاعات بیشتر، برروی علامت دو طرفه کلیک کنید
		                                </td>
		                        </tr>
		                        <tr >
						<td>
		                                        <img src="../img/tel.png" height="20px" width="20px"/>
		                                </td>
		
		                                <td>
							نشانگر فروش پرواز فقط به صورت تلفنی می باشد 
		                                </td>
		                        </tr>
		                        <tr>
						<td>
		                                        <img src="../img/refund1.png"/>
		                                </td>
		
		                                <td>
		                                        قابل استرداد
		                                </td>
		                        </tr>
		                        <tr>
						 <td >
		                                        <img src="../img/norefund.png"/>
		                                </td>
		
		                                <td>
		                                        غیر قابل استرداد
		                                </td>
		                        </tr>
					<tr>
						<td >
							<img src="../img/star.png"/>
						</td>
						<td>
							BusinessClass
						</td>
					</tr>
		
		                </table>
			</td>
		</tr>
	</table>		
                </div>
            </div>  	              
                               
    </div></div>		
	<br/>
	<br/>
	<div align="center">
	<?php echo $conf->contact; ?>
			<?php echo 'تاریخ امروز:&nbsp;'.perToEnNums(jdate('l , d , m , Y',strtotime(date("Y-m-d"))));echo '&nbsp;'.date("D ,M j, Y");
				if( (int)$_SESSION[$conf->app.'_customer_typ'] ==2 && (int)$_SESSION[$conf->app.'_typ']==0 )
					echo " تعداد کاربران آنلاین : ".count(user_class::getOnlines());
			 ?>
	</div>
<script>
$(document).ready(function(){
<?php
        if(isset($GLOBALS['extraScript']))
                echo $GLOBALS['extraScript'];
?>
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
    $("#sanad_gozaresh").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "گزارش اسناد",
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
                url: "sanad_gozaresh.php"
        });
    });
    $("#reserve").click(function () {
      //$("ul").slideToggle("slow");
      if(document.getElementById("hamed_adl").options)
      {
	var adl = document.getElementById("hamed_adl").options[document.getElementById("hamed_adl").selectedIndex].value;
	var chd = document.getElementById("hamed_chd").options[document.getElementById("hamed_chd").selectedIndex].value;
	var inf = document.getElementById("hamed_inf").options[document.getElementById("hamed_inf").selectedIndex].value;
	var ticket_type = document.getElementById("ticket_type").options[document.getElementById("ticket_type").selectedIndex].value;
	}
	else
	{
		var adl = document.getElementById("hamed_adl").value;
		var chd = document.getElementById("hamed_chd").value;
		var inf = document.getElementById("hamed_inf").value;
		var ticket_type = document.getElementById("ticket_type").options[document.getElementById("ticket_type").selectedIndex].value;
	}
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
//addv
 $("#addv").click(function () {
      //$("ul").slideToggle("slow");
        $.window({
                title: "ثبت تبلیغات",
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
                url: "admin.php"
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
	var inps = document.getElementsByTagName('input');
	var id = '';
//-------PHP Generated Part
<?php
	//var par_array = {1:"red",2:"blue"};
	echo loadParvazRangs();
?>
//-------------------------
	for(var indx=0;indx<inps.length;indx++)
	{
		id = inps[indx].id;
		id = String(id).split('_');
		if(id[2]=='parvaz' && id[3]=='id' && id.length==4 && par_array[parseInt(inps[indx].value,10)])
			inps[indx].parentNode.parentNode.style.backgroundColor=par_array[parseInt(inps[indx].value,10)];
	}
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
