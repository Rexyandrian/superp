<?php
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
     /*   if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = $conf->auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);*/
	function flightZarfiat($parvaz)
	{
		$conf = new conf;
		$out = 0;
		$customer_typ = (int)$_SESSION[$conf->app."_customer_typ"];
		if($customer_typ != 2 && $parvaz->getZarfiat($_SESSION[$conf->app.'_customer_id'])>=9)
			$out = 9;
		else if($customer_typ != 2)
			$out = $parvaz->getZarfiat($_SESSION[$conf->app.'_customer_id']);
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
		$mysql = new mysql_class;
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
		{
			$out = $q["name"];
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
		$mysql = new mysql_class;
		$mysql->ex_sql("select `name` from `sherkat` where `id` = '$inp'",$q);
		if(isset($q[0]))
		{
			$out = $q[0]["name"];
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
		$conf = new conf;
		$par = new parvaz_det_class((int)$inp);
		$customer_id = $_SESSION[$conf->app."_customer_id"];
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
	$customer_typ = (int)$_SESSION[$conf->app."_customer_typ"];
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
			$gname = 'grid1';
			$input =array($gname=>array('table'=>'parvaz_det','div'=>'main_div'));
			$xgrid = new xgrid($input);
			$xgrid->whereClause[$gname] = $where;
			$xgrid->column[$gname][0]['name'] ='شماره';
			$xgrid->column[$gname][0]['cfunction'] = array('loadShomare');
			$xgrid->column[$gname][1]['name'] ='';
			$xgrid->column[$gname][2]['name'] ='تاریخ';
			$xgrid->column[$gname][2]['cfunction'] = array('hamed_pdate');
			$xgrid->column[$gname][3]['name'] ='ساعت';
			$xgrid->column[$gname][2]['cfunction'] = array('saat');
			$xgrid->column[$gname][4]['name'] ='';
			$xgrid->column[$gname][5]['name'] ='قیمت بلیت"."(ریال)';
			$xgrid->column[$gname][5]['cfunction'] = array('monize');
			$xgrid->column[$gname][6]['name'] ='';
			$xgrid->column[$gname][7]['name'] ='';
			$xgrid->column[$gname][8]['name'] ='';
			$xgrid->column[$gname][9]['name'] ='';
			$xgrid->column[$gname][10]['name'] ='';
			$xgrid->column[$gname][11]['name'] ='';
			$xgrid->column[$gname][12]['name'] ='';
			$xgrid->column[$gname][13]['name'] ='';
			$xgrid->column[$gname][] =array('name'=>'کمیسیون(ریال)','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('poorsant'));
			$xgrid->column[$gname][] =array('name'=>'هواپیمایی','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadSherkat'));
			$xgrid->column[$gname][] =array('name'=>'مقصد','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadMaghsad'));
			$xgrid->column[$gname][] =array('name'=>'مبدأ','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadMabda'));
			$xgrid->addFunction[$gname] ='add_item';
			$xgrid->canAdd[$gname] = TRUE;	
			$out =$xgrid->getOut($_REQUEST);
			if($xgrid->done)
				die($out);
		}
	}
//-----------------------------------------

?>
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
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/grid.js"></script>
<script type="text/javascript" >
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		intialGrid(args);
	});
	function changeWerc(ser)
	{
		var werc ='';
		$.each($('.'+ser),function(id,field)
		{
			werc +=((werc=='')?' where ':' and ')+" (`"+field.id+"` like '|"+trim(field.value)+"|') ";
		});

		var ggname ='<?php echo $gname; ?>';
		whereClause[ggname] = encodeURIComponent(werc);
		grid[ggname].init(gArgs[ggname]);
	}
</script>
	<div align="center" >
		<?php
			echo (($msg != "")?"<script>alert('$msg');window.location = 'index.php';</script>":$out);
		?>
		<br>
		<span style="color:#ffffff;font-size:18px;font-family:arial;" >
			
		</span>
		<table style="background-color:#F6D5A2;font-weight:bold;" border='0'>
			<tr style="background-color:#FFA858;" >
				<td style="width:80px;" align="left" >
					تعدادبزرگسال:
				</td>
				<td  style="width:60px;"  >
					<?php echo "<input readonly='readonly' style='width:30px;font-family:tahoma,Tahoma;' value='".enToPerNums($adl)."'" ?>
				</td>
				<td style="width:60px;" align="left" >
					تعدادکودک:
				</td  >
				<td style="width:60px;" >
					<?php echo "<input readonly='readonly' style='width:30px;font-family:tahoma,Tahoma;' value='".monize($chd)."'" ?>
				</td>
				<td style="width:60px;" align="left" >
					تعدادنوزاد:
				</td>
				<td style="width:60px;" >
					<?php echo "<input readonly='readonly' style='width:30px;font-family:tahoma,Tahoma;' value='".monize($inf)."'" ?>
				</td>
				<td style="width:60px;" align="left" >
					تعدادکل:
				</td>
				<td style="width:60px;" >
					<?php echo "<input readonly='readonly' style='width:30px;font-family:tahoma,Tahoma;' value='".monize($chd+$adl)."'" ?>
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
					<?php echo "<input readonly='readonly' style='width:150px;font-family:tahoma,Tahoma;' value='".monize($jam_kom)." ریال '" ?>
				</td>
				<td style="width:100px;" align="left" >
					مبلغ کل بلیت:
				</td  >
				<td style="width:100px;" >
					<?php echo "<input readonly='readonly' style='width:150px;font-family:tahoma,Tahoma;' value='".monize($jam_kol)." ریال'" ?>
				</td>
				<td style="width:100px;" align="left" >
					مبلغ قابل پرداخت:
				</td>
				<td style="width:200px;" colspan='3' >
					<?php $jam_pardakhti= $jam_kol-$jam_kom; echo "<input readonly='readonly' style='width:150px;font-family:tahoma,Tahoma;' value='".monize($jam_pardakhti)." ریال '" ?>
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
				if($_SESSION[$conf->app.'_customer_typ']!=2 && $ticket_type==0)
					echo 'رمز بلیت الکترونیک-eticket:<input type="password" name="epass" class="inp"  />';
			?>
			<input type="submit" value="ثبت جهت رزرو موقت" class="inp1" style="width:auto" >
			<input type="button" value="انصراف" class="inp1" style="width:auto"  onclick="window.location = 'index.php';" />
		</form>
	</div>

<div id="main_div" style="overflow:auto;border:1px dotted #bbb;paddin:5px;" >

</div>
