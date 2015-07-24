<?php   include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
	$isAdmin = $se->detailAuth('all');
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function flightZarfiat($parvaz)
	{
		$out = 0;
		$conf = new conf;
		$se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
		$isAdmin = $se->detailAuth('all');
		if(!$isAdmin && $parvaz->getZarfiat($_SESSION[$conf->app.'_customer_id'])>=9)
			$out = 9;
		else if(!$isAdmin)
			$out = $parvaz->getZarfiat($_SESSION[$conf->app.'_customer_id']);
		else if($isAdmin)
			$out = $parvaz->getZarfiat();
		return($out);
	}
	function bargashtHast($selectedParvaz,$parvaz)
	{
		$out = TRUE;
		$jids = $parvaz->loadJid();
		if($parvaz->j_id >0 && $jids==null)
		{
			$out = FALSE;
			foreach($selectedParvaz as $tmp)
			{				
				if($tmp->mabda_id == $parvaz->maghsad_id && $parvaz->mabda_id = $tmp->maghsad_id)
					$out = TRUE;
			}
		}
		else if($parvaz->j_id >0 && $jids!=null)
		{
			$out = FALSE;
			foreach($selectedParvaz as $tmp)
                        {
				for($i = 0;$i < count($jids);$i++)
					if($jids[$i] == $tmp->getId())
						$out = TRUE;
                        }
		}
		return($out);
	}
	function loadCity($inp)
	{
		$inp = (int)$inp;
		$out = "";
		$mysql  = new mysql_class;
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
	function loadRoute($inp)
	{
		$inp = (int)$inp;
		$out = "";
		$par = new parvaz_det_class($inp);
		return (loadCity($par->mabda_id).'--'.loadCity($par->maghsad_id));
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
		$mysql  = new mysql_class;
		$mysql->ex_sql("select `name` from `sherkat` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
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
		$conf = new conf;
		$par = new parvaz_det_class((int)$inp);
		$customer_id = $_SESSION[$conf->app."_customer_id"];
		$cust = new customer_class($customer_id);
		$out = ($cust->getPoorsant($inp)* ($par->ghimat+$par->tour_mablagh) /100 );
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
	function epass($cid)
	{
		$mysql = new mysql_class;
		$out = "";
		$mysql->ex_sql("select `epass` from `customers` where `id`='$cid'",$q);
		if(isset($q[0]))
			$out = $q[0]["epass"];
		return($out);
	}
	if(isset($_REQUEST['epass']))
	{
		$out = 'false';
		$epass = trim($_REQUEST['epass']);
		$customer_id = $_SESSION[$conf->app.'_customer_id'];
		$tm = new customer_class($customer_id);
		if($epass==epass($customer_id) || $tm->typ==3)
			$out = 'ok';
		die($out);
	}
	if(!isset($_SESSION[$conf->app."_user_id"]) || !isset($_REQUEST["adl"]))
		die("<script>window.opener.location = window.opener.location; window.close();</script>");
	if((int)$_REQUEST["adl"] <= 0 )
		die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('حداقل یک بزرگسال باید انتخاب شود');window.location = 'index.php';window.close();</script></body></html>");
	$selected_parvaz = $_REQUEST["selected_parvaz"];
        $tmp = explode(",",$selected_parvaz);
	if(count($tmp)>2)
	{
		die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('حداکثر دو پرواز می توانید انتخاب کنید');window.location = 'index.php';window.close();</script></body></html>");	
	}
	else if(count($tmp)==2)
	{
		if(!check_raft_bargasht($tmp[0],$tmp[1]))
			die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('پروازهای انتخابی باید رفت وبرگشت باشد');window.location = 'index.php';window.close();</script></body></html>");	
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
	$customer = new customer_class((int)$_SESSION[$conf->app."_customer_id"]);
	$isAdmin = $se->detailAuth('all');
	$tedad = $adl + $chd;
	$jam_ghimat = 0;
	$tedad_ok = TRUE;
        foreach($tmp as $parvaz_id)
        {
	        $tmp_parvaz = new parvaz_det_class((int)$parvaz_id);
                if(flightZarfiat($tmp_parvaz) < $tedad)
        	        $tedad_ok = FALSE;
                $jam_ghimat += ($tedad * $tmp_parvaz->ghimat);
                $jam_ghimat += $inf * 200000;//($inf * $tmp_parvaz->ghimat)/10;
        }
	if(!isset($_REQUEST["mod"]))
	{
		$paravaz_tedad  = ((count($tmp)>0)?TRUE:FALSE);
		$domasire_ok = TRUE;
		if(count($tmp) == 1 && (int)$tmp[0] <=0)
			$paravaz_tedad = FALSE;
		foreach($selectedParvaz as $tmp)
		{
			$bar = bargashtHast($selectedParvaz,$tmp);
			$domasire_ok = ($bar && $domasire_ok);
		}
		$customer_etebar_ok = TRUE;
		$customer_tedad_ok = TRUE;
		$customer_shomare_ok = TRUE;
		if( !$isAdmin && $customer->max_amount < $jam_ghimat )
			$customer_etebar_ok = FALSE;
		if(($customer->max_ticket + 1 - $customer->min_ticket)< $tedad)
			$customer_tedad_ok = FALSE;
//---------Flight Selctetion Problems------
		$msg = "";	
		if(!$tedad_ok)
			$msg = " ظرفیت پرواز کم است ";
		if(!$paravaz_tedad)
			$msg .= " هیچ پروازی انتخاب نشده است ";
		if(!$domasire_ok && !$isAdmin)
			$msg .= " پرواز دومسیره است مسیر دوم را انتخاب کنید ";
//-----------------------------------------
//---------Customer Problems---------------
		//if(!$customer_etebar_ok)
			//$msg .= " اعتبار مشتری کافی نیست";
		if(!$customer_tedad_ok)
			$msg .= " سقف تعداد خرید مشتری کافی نیست";
		if(!$customer_shomare_ok)
			$msg .= " تعداد شماره تیکت مشتری کافی نیست";
		$out = "";
		$adults = "";
		$childs = "";
		$infants = "";
		$jam_pardakhti = 0;
		if($msg != "")
			die('Erro Select');
			//Craete Verify Here
		$where= "1=0 ";
		
		//var_dump($selectedParvaz);
		$jam_kol = 0;
		$jam_kom = 0;
		for($i =0 ;$i<count($selectedParvaz);$i++)
		{
			$where .="or `id`='".$selectedParvaz[$i]->getId()."'"; 
			$jam_kom +=((($adl+$chd)* ($selectedParvaz[$i]->ghimat+$selectedParvaz[$i]->tour_mablagh) + $inf*200000) *$customer->getPoorsant($selectedParvaz[$i]->getId()) /100);
			$jam_kol+=($adl+$chd)* ($selectedParvaz[$i]->ghimat+$selectedParvaz[$i]->tour_mablagh)+$inf*200000;
		}
		$gname = 'grid_ticket_parvaz_det_div';
		$input =array($gname=>array('table'=>'parvaz_det','div'=>'ticket_parvaz_det_div'));
		$xgrid = new xgrid($input);
		//$xgrid->alert = TRUE;
		$xgrid->eRequest[$gname] = array('adl'=>$adl,'chd'=>$chd,'inf'=>$inf,'selected_parvaz'=>$selected_parvaz,'ticket_type'=>$ticket_type);
		$xgrid->whereClause[$gname] = $where;
		$gid = $xgrid->column[$gname][0];
		$gGhimat = $xgrid->column[$gname][5];
		$xgrid->column[$gname][0]['name'] = '';
                $xgrid->column[$gname][1]['name'] = '';
                $xgrid->column[$gname][2]['name'] = 'تاریخ';
                $xgrid->column[$gname][2]['cfunction'] = array('hamed_pdate');
                $xgrid->column[$gname][3]['name'] = 'ساعت';
                $xgrid->column[$gname][3]['cfunction'] = array('saat');
                $xgrid->column[$gname][4]['name'] = 'ظرفیت';
                $xgrid->column[$gname][4]['cfunction'] = array('monize');
                $xgrid->column[$gname][5]['name'] = 'قیمت';
                $xgrid->column[$gname][5]['cfunction'] = array('monize');
                $xgrid->column[$gname][6]['name'] = 'شماره';
                $xgrid->column[$gname][7]['name'] = '';
                $xgrid->column[$gname][8]['name'] = 'مبدأ';
                $xgrid->column[$gname][9]['name'] = 'مقصد';
                $xgrid->column[$gname][10]['name'] = 'ایرلاین';
                $xgrid->column[$gname][11]['name'] = 'کلاس';
                $xgrid->column[$gname][12]['name'] = '';
                $xgrid->column[$gname][13]['name'] = '';
                $xgrid->column[$gname][14]['name'] = '';
                $xgrid->column[$gname][15]['name'] = '';
                $xgrid->column[$gname][16]['name'] = '';
                $xgrid->column[$gname][17]['name'] = '';
                $xgrid->column[$gname][18]['name'] = '';
                $xgrid->column[$gname][19]['name'] = '';
                $xgrid->column[$gname][20]['name'] = '';
                $xgrid->column[$gname][21]['name'] = '';
                $xgrid->column[$gname][22]['name'] = '';
                $xgrid->column[$gname][23]['name'] = '';
                $xgrid->column[$gname][24]['name'] = '';
		$out =$xgrid->getOut($_REQUEST);
		if($xgrid->done)
			die($out);
		/*
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
		$grid->columnHeaders[13] = null;
		$grid->columnHeaders[14] = null;
		$grid->addFeild("id");
		$grid->columnHeaders[15] = "کمیسیون(ریال)";
		$grid->columnFunctions[15] = "poorsant";
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
		*/
		$jam_pardakhti= $jam_kol-$jam_kom;
			
		$tm = new user_class($_SESSION[$conf->app."_user_id"]);
		$tm = new customer_class($tm->customer_id);
		$c_typ = $tm->typ;
		$etebar = ($customer->max_amount>=$jam_pardakhti);
		if(!$etebar && !$conf->naghdi)
			die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><h1 align="center">اعتبار جهت رزرو کافی نیست</h1><br/><center><a href="index.php">بازگشت</a></center></body></html>');
		$out_kharid = '<table><tr>'."\n";
		if(!$isAdmin && $ticket_type==0 && $tm->typ!=3)
			$out_kharid .= '<td>رمز بلیت الکترونیک-eticket:<input type="password" name="epass" id="epass" value="" class="inp"  /> </td>';
		$out_kharid .= '
					<td>
						<button id="reserve_movaghat" >ثبت جهت رزرو موقت</button>
					</td>
					<td>
						<button onclick="closeDialog();">انصراف</button>
						<div id="msg_div" ></div>
					</td>
				</tr>';
		if($etebar)
		{
		
			$out_kharid .='<tr>
					<td colspan="2" >
						<input type="radio" checked="checked" class="kharid" name="kharid_typ" value="etebari" >پرداخت اعتباری
					</td>
				</tr>';
		}
		$out_kharid .='<tr>
			<td colspan="2" >
				<input '.(($conf->naghdi)?'':'disabled="disabled"').' type="radio" '.((!$etebar)?'checked="checked"':'').' name="kharid_typ" class="kharid" value="naghdi" >پرداخت نقدی
			</td>
			<td rowsoan="2" >
				<img src="../img/shetab.gif">
			</td>
		</tr>
';
		if($conf->naghdi)
		{
			if($conf->ps|| $conf->payline)
				$out_kharid .='<tr>
							<td colspan="4" >
								<img width="500px" src="../img/ps.jpg">
							</td>
						</tr>
			';
			else
			{
				$out_kharid .='<tr>
							<td colspan="4" >
								<img width="200px" src="../img/mellat.png">
							</td>
						</tr>
			';
			}
		}
		$out_kharid .='</table>';
	}
//-----------------------------------------

?>
<script>
	$("#reserve_movaghat").click(function(){
		var isAdmin =<?php echo ($isAdmin)?'true':'false'; ?>;
		if(!isAdmin)
		{
			var epass = $("#epass").val();
			$("#msg_div").hide();
			$("#msg_div").html('<img src="../img/status_fb.gif" >');
			$("#msg_div").show();
			$.get("ticket_check.php?epass="+epass+"&r="+Math.random()+"&",function(result){	
				result = trim(result);
				$("#msg_div").hide();
				if(result=="ok")
				{
					var kharid_typ = 'etebari';
					$.each($(".kharid"),function(id,field){
						if($(field).attr("checked"))
							kharid_typ = $(field).val();
					});
					var adl = <?php echo $adl; ?>;
					var chd = <?php echo $chd; ?>;
					var inf = <?php echo $inf; ?>;
					var ticket_type =<?php echo $ticket_type; ?>;
					var selected_parvaz = '<?php echo $selected_parvaz; ?>';
					
					closeDialog();
					//console.log("checkflight.php?selected_parvaz="+selected_parvaz+"&ticket_type="+ticket_type+"&adl="+adl+"&chd="+chd+"&inf="+inf+"&kharid_typ="+kharid_typ+"&");
					openDialog("checkflight.php?selected_parvaz="+selected_parvaz+"&ticket_type="+ticket_type+"&adl="+adl+"&chd="+chd+"&inf="+inf+"&kharid_typ="+kharid_typ+"&","رزرو نهایی",{'minWidth':850,'minHeight':550},false);
				}
				else
				{
					$("#epass").val('');
					alert('رمز بلیت الکترونیک را درست وارد کنید');
				}
			});
		}
		else
		{
			var kharid_typ = 'etebari';
			$.each($(".kharid"),function(id,field){
				if($(field).attr("checked"))
					kharid_typ = $(field).val();
			});
			var adl = <?php echo $adl; ?>;
			var chd = <?php echo $chd; ?>;
			var inf = <?php echo $inf; ?>;
			var ticket_type =<?php echo $ticket_type; ?>;
			var selected_parvaz = '<?php echo $selected_parvaz; ?>';
			closeDialog();
			openDialog("checkflight.php?selected_parvaz="+selected_parvaz+"&ticket_type="+ticket_type+"&adl="+adl+"&chd="+chd+"&inf="+inf+"&kharid_typ="+kharid_typ+"&","رزرو نهایی",{'minWidth':950,'minHeight':550},false);
		}
	});
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		intialGrid(args);
	});
</script>
<div id="ticket_parvaz_det_div" >
</div>
<div align="center" >
	<?php
		echo (($msg != "")?"<script>alert('$msg');window.location = 'index.php';</script>":$out);
	?>
	<br>
	<span style="color:#ffffff;font-size:18px;font-family:arial;" >
	
	</span>
	<table style="border-color:#5CACEE #5CACEE;background-color:#eee;font-weight:bold;" border='0'>
		<tr></tr>
		<tr></tr>
		<tr></tr>
		<tr></tr>

		<tr style="background-color:#C6E2FF;" >
			<td style="width:80px;" align="left" >
				تعدادبزرگسال:
			</td>
			<td  style="width:60px;"  >
				<?php echo "<input readonly='readonly' style='background:#C6E2FF;width:30px;' value='".enToPerNums($adl)."'" ?>
			</td>
			<td style="width:60px;" align="left" >
				تعدادکودک:
			</td  >
			<td style="width:60px;" >
				<?php echo "<input readonly='readonly' style='background:#C6E2FF;width:30px;' value='".monize($chd)."'" ?>
			</td>
			<td style="width:60px;" align="left" >
				تعدادنوزاد:
			</td>
			<td style="width:60px;" >
				<?php echo "<input readonly='readonly' style='background:#C6E2FF;width:30px;' value='".monize($inf)."'" ?>
			</td>
			<td style="width:60px;" align="left" >
				تعدادکل:
			</td>
			<td style="width:60px;" >
				<?php echo "<input readonly='readonly' style='background:#C6E2FF;width:30px;' value='".monize($chd+$adl)."'" ?>
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
		<tr style="background-color:#C6E2FF;" >
			<td style="width:100px;" align="left" >
				کمیسیون:
			</td>
			<td  style="width:100px;"  >
				<?php echo "<input readonly='readonly' style='background:#C6E2FF;width:150px;' value='".monize($jam_kom)." ریال '" ?>
			</td>
			<td style="width:100px;" align="left" >
				مبلغ کل بلیت:
			</td  >
			<td style="width:100px;" >
				<?php echo "<input readonly='readonly' style='background:#C6E2FF;width:150px;' value='".monize($jam_kol)." ریال'" ?>
			</td>
			<td style="width:100px;" align="left" >
				مبلغ قابل پرداخت:
			</td>
			<td style="width:200px;" colspan='3' >
				<?php echo "<input readonly='readonly' style='background:#C6E2FF;width:150px;' value='".monize($jam_pardakhti)." ریال '" ?>
			</td>
		</tr>
		<tr>
			<td colspan='6' style='color:#000000;' >
				<br>
				<p>لطفا اطلاعات فوق را کنترل کرده و سپس کلید تایید را کلیک نمایید
	توجه داشته باشد پس از تایید اطلاعات فوق جهت ورود اسامی 5 دقیقه فرصت دارید</p>
			</td>
		</tr>
	</table>
	<br>
		<input type="hidden" name="adl" value="<?php echo $adl; ?>" />
		<input type="hidden" name="chd" value="<?php echo $chd; ?>" />
		<input type="hidden" name="inf" value="<?php echo $inf; ?>" />
		<input type="hidden" name="ticket_type" value="<?php echo $ticket_type; ?>" />
		<input type="hidden" name="selected_parvaz" value="<?php echo $selected_parvaz; ?>" />
		<?php echo $out_kharid; ?>
</div>
