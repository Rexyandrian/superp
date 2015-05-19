<?php   include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        ticket_class::clearTickets();
	$GLOBALS['parvaz_id'] = -1;
	$GLOBALS['obj'] = array();
        if(!$se->can_view)
                die($conf->access_deny);
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
	function loadCityMab($inp)
	{
		$inp = (int)$inp;
		$mysql = new mysql_class;
		$mysql->ex_sql("select `mabda_id`,`maghsad_id`,`shomare`,`havapiema_id` from `parvaz` where `id`='$inp' ",$p);
		$GLOBALS['parvaz_id'] = $inp;
		$GLOBALS['obj'] = $p;
		$inp =-1;
		if(isset($p[0]))
			$inp =(int) $p[0]['mabda_id'];
		$out = '';
		$q = array();
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
	function loadCityMagh($inp)
	{
		$inp = (int)$inp;
		$out = '';
		if($GLOBALS['parvaz_id']==$inp)
			$inp = $GLOBALS['obj'][0]['maghsad_id'];
		else
		{
			$parvaz = new parvaz_class($inp);
			$inp = $parvaz->maghsad_id;
		}
		$mysql = new mysql_class;
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
	function loadShomare($inp)
	{
		$inp = (int)$inp;
		if($GLOBALS['parvaz_id']==$inp)
			$inp = $GLOBALS['obj'][0]['shomare'];
		else
		{
			$parvaz = new parvaz_class($inp);
			$inp = $parvaz->shomare;
		}
		return($inp);
	}
	function loadPlane($inp)
	{
		$inp = (int)$inp;
		if($GLOBALS['parvaz_id']==$inp)
			$inp = $GLOBALS['obj'][0]['havapiema_id'];
		else
		{
			$parvaz = new parvaz_class($inp);
			$inp = $parvaz->havapiema_id;
		}
		$mysql = new mysql_class;
		$out = '';
		$mysql->ex_sql("select `name` from `havapeima` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
	function hamed_pdate($str)
        {
                $out=jdate('d / m ',strtotime($str));
		$out .= "<br/>".date('M d',strtotime($str));
                return enToPerNums($out);
        }
	function hamed_pdate_day($str)
	{
		$out=jdate('l',strtotime($str));
                return enToPerNums($out);
	}
	function poorsant($inp)
	{
		$conf = new conf;
		$customer_id =(int) $_SESSION[$conf->app."_customer_id"];
		$cust = new customer_class($customer_id);
		$out = $cust->getPoorsant($inp);
		return ($out).'%';
	}
	function saat($inp)
	{
		$inp = substr($inp,0,-3);
		return enToPerNums($inp);
	}
	function loadCheckBox($id)
	{
		$my = new mysql_class;
		$my->ex_sql("select `typ`,`tarikh`,`j_id`,`saat` from `parvaz_det` where `id`='$id' ",$q);
		$name='';
		$jIcon = '';
		if(isset($q[0]) && (int)$q[0]['j_id']==1)
		{
			$my->ex_sql("select `jid` from `parvaz_jid` where `parvaz_det_id`='$id' ",$p);
			$jid = array();
			foreach($p as $t)
				$jid[] =$t['jid'];
			$name =(count($jid)>0)?implode('-',$jid):0;
			$jIcon = '<br/><img src="../img/twoway.png" >';
		}
		$out = "<input type='checkbox' class='sel' name='$name' id='ch_$id'  >";
		if(isset($q[0]) && strtotime($q[0]['tarikh'].' '.$q[0]['saat'])<strtotime(date("Y-m-d H:i:s")))
			$out='';
		if(isset($q[0]) && (int)$q[0]['typ']==2)
			$out = '<img src="../img/tel.png" >';
		$out.=$jIcon;
		return $out;
	}
	function zarfiat($inp)
	{
		$inp = (int)$inp;
		$conf = new conf;
		$par = new parvaz_det_class($inp);
		$inp = $par->getZarfiat((int)$_SESSION[$conf->app.'_customer_id']);
		if($inp>9)
			$inp = 9;
		$out = $inp;
		if($out==0)
			$out = '<span class="notice" >CLOSED</span>';
		return ($out);
	}
	function loadGhimat($inp)
	{
		return perToEnNums(monize($inp));
	}
	function colorFunc($inp)
	{
		//var_dump($inp);
		function loadClassCss($parvaz_id,&$parvazTunck)
		{
			$my = new mysql_class;
			$rang = '';
			$my->ex_sql("select `rang` from `parvaz` where `id`='$parvaz_id' ",$q);
			if(isset($q[0]))
				$rang =  $q[0]['rang'];
			$parvazTunck[$parvaz_id] = $rang;
			return $rang;
		}
		$parvazTunck = array();
		$rows = $inp['rows'];
		$tmpRow =array();
		foreach($rows as $id=>$row)
		{
			$parvaz_id = $row['cell'][16]['value'];
			$row['css'] = (isset($parvazTunck[$parvaz_id]))?$parvazTunck[$parvaz_id]:loadClassCss($parvaz_id,$parvazTunck);
			$tmpRow[$id] = $row;
		}
		$inp['rows'] = $tmpRow;
		return $inp;
	}
	$domasire = TRUE;
	$pageRows = 10;
	$now = date("Y-m-d H:i:s");
	$my = new mysql_class;
	$my->ex_sql("select count(`id`) as `co` from `parvaz_det` where `tarikh`>='".date("Y-m-d")."' and `saat`>='".date("H:i:s")."'",$l);
	if(isset($l[0]))
		$pageRows =(int) $l[0]['co'];
	$gname = 'parvaz_det';
	$input =array($gname=>array('table'=>'parvaz_det','div'=>'parvaz_det_div'));
	$xgrid = new xgrid($input);
	$xgrid->disableRowColor[$gname] = TRUE;
	$xgrid->afterCreateFunction[$gname] = 'colorFunc';
	//$xgrid->echoQuery = TRUE;
	//$xgrid->alert = TRUE;
	$xgrid->whereClause[$gname] = " (`tarikh`='".date("Y-m-d")."' and `saat`>='".date("H:i:s")."') or (`tarikh`>'".date("Y-m-d")."') order by `tarikh`,`saat`";
	$xgrid->pageRows[$gname]= $pageRows;
	$id = $xgrid->column[$gname][0];
	$parvaz_id = $xgrid->column[$gname][1];
	$tarikh = $xgrid->column[$gname][2];
	$saat = $xgrid->column[$gname][3];
	$zarfiat = $xgrid->column[$gname][4];
	$ghimat = $xgrid->column[$gname][5];
	$typ = $xgrid->column[$gname][6];
	$zakhire = $xgrid->column[$gname][7];
	$j_id = $xgrid->column[$gname][8];
	$poor_def =  $xgrid->column[$gname][9];
	$mablagh_kharid = $xgrid->column[$gname][10];
	$saat_kh = $xgrid->column[$gname][11];
	$can_esterdad = $xgrid->column[$gname][12];
	$en = $xgrid->column[$gname][13];
	$customer_id = $xgrid->column[$gname][14];
	$tour_mablagh =  $xgrid->column[$gname][15];
	$toz = $xgrid->column[$gname][16];

	$xgrid->column[$gname][0]['name'] = 'انتخاب';
	$xgrid->column[$gname][0]['cfunction'] = array('loadCheckBox');
	$xgrid->column[$gname][1] = $ghimat;
	$xgrid->column[$gname][1]['name'] = 'قیمت';
	$xgrid->column[$gname][1]['cfunction'] = array('loadGhimat');

	$xgrid->column[$gname][2] =$id;
	$xgrid->column[$gname][2]['name'] = 'ظرفیت';
	$xgrid->column[$gname][2]['cfunction'] = array('zarfiat');

	$xgrid->column[$gname][3] = array('name'=>'مبدأ','fieldname'=>'parvaz_id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadCityMab'));
	$xgrid->column[$gname][4] = array('name'=>'مقصد','fieldname'=>'parvaz_id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadCityMagh'));
	$xgrid->column[$gname][5] = $parvaz_id;
	$xgrid->column[$gname][5]['name'] = 'شماره';
	$xgrid->column[$gname][5]['cfunction'] = array('loadShomare');

	$xgrid->column[$gname][6] = array('name'=>'هواپیما','fieldname'=>'parvaz_id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadPlane'));
	$xgrid->column[$gname][7] = $tarikh;
	$xgrid->column[$gname][7]['name'] = 'تاریخ';
	$xgrid->column[$gname][7]['cfunction'] = array('hamed_pdate');

	$xgrid->column[$gname][8] = $saat;
	$xgrid->column[$gname][8]['name'] = 'خروج';
	$xgrid->column[$gname][8]['cfunction'] = array('saat');

	$xgrid->column[$gname][9] = $saat_kh;
	$xgrid->column[$gname][9]['name'] = 'ورود';
	$xgrid->column[$gname][9]['cfunction'] = array('saat');

	$xgrid->column[$gname][10] = $id;
	$xgrid->column[$gname][10]['name'] = 'کمیسیون';
	$xgrid->column[$gname][10]['cfunction'] = array('poorsant');

	$xgrid->column[$gname][11] = $toz;
	$xgrid->column[$gname][11]['name']='ملاحظات';
	$xgrid->column[$gname][12]['name']='';
	$xgrid->column[$gname][13]['name']='';
	$xgrid->column[$gname][14]['name']='';
	$xgrid->column[$gname][15]['name']='';
	$xgrid->column[$gname][16] =$parvaz_id;
	$xgrid->column[$gname][16]['name'] = '';
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);
?>
<script>
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		args['<?php echo $gname; ?>']['afterLoad'] = initReserve;
		intialGrid(args);
	});
	function searchFlight()
	{
		var werc ='';
		var ser ='ser';
		$.each($('.'+ser),function(id,field)
		{
			var fi = $("#"+field.id).val();
			if(field.id=='saztarikh' && fi!='' )
			{
				tmpTr = jToM(fi);
				werc +=((werc=='')?' where ':' and ')+" (date(`tarikh`) >= '"+tmpTr+"') ";
			}
			else if(field.id=='statarikh' && fi!='')
			{
				tmpTr = jToM(fi);
				werc +=((werc=='')?' where ':' and ')+" (date(`tarikh`) <= '"+tmpTr+"') ";
			}
			else if(field.id=='smabda_id' && parseInt(fi,10)!=-1)
				werc +=((werc=='')?' where ':' and ')+" ( `parvaz_id` in(select `id` from `parvaz` WHERE `mabda_id`='"+fi+"' ))  ";
			else if(field.id=='smaghsad_id' && parseInt(fi,10)!=-1)
				werc +=((werc=='')?' where ':' and ')+" ( `parvaz_id` in(select `id` from `parvaz` WHERE `maghsad_id`='"+fi+"' ))  ";
		});
		//alert(werc);
		var ggname ='<?php echo $gname; ?>';
		whereClause[ggname] = encodeURIComponent(werc);
		grid[ggname].init(gArgs[ggname]);
	}
</script>
<table style="width:22cm;border-width:1px;border-style:dashed;border-collapse:collapse;border-color:#BCBCBC;">
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
							
							<td>
								&nbsp;
							</td>
						</tr>
						<tr >
							<td>
								<select id="smabda_id" name="smabda_id" class="ser" >
								<?php
									echo loadCities(-1);
								?>
								</select>
							</td>
							<td>
								<select id="smaghsad_id" name="smaghsad_id" class="ser" >
								<?php
									echo loadCities(-1);
								?>
								</select>
							</td>	
							<td>
								<input onblur="correctDate(this);" autocomplete="off" class="ser dateValue" style="direction:ltr;" type="text" id="saztarikh" name="saztarikh" value="<?php echo jdate('Y/m/d',strtotime(date("Y-m-d"))); ?>"/>
							</td>
							<td>
								<input onblur="correctDate(this);" autocomplete="off" class="ser dateValue" style="direction:ltr;" type="text" id="statarikh" name="statarikh" value="<?php echo jdate('Y/m/d',strtotime(date("Y-m-d")." +15 day ")); ?>"/>
							</td>
							<td>
								<button onclick="searchFlight();" >نمایش و بروز رسانی</button>
							</td>
						</tr>
					</table>
<div id="parvaz_det_div" style="overflow:auto;border:1px dotted #bbb;paddin:5px;" >
</div>
<div id="reserve_div" class="show_div" >
	<table>
		<tr >
		
			<td align="center"  >بزرگسال</td>
			<td align="center"  > کودک </td>
			<td align="center"  >نوزاد</td>
		</tr>
		<tr>
			<td align="center">
				<select  style="width:50px;"  id="reserve_adl" name="reserve_adl" class="textbox">
					<option value="1">1</option>
					<option value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
					<option value="6">6</option>
					<option value="7">7</option>
					<option value="8">8</option>
					<option value="9">9</option>
				</select>
			</td>
			<td align="center"><select  style="width:50px;" id="reserve_chd" name="reserve_chd" class="textbox"><option value="0" selected="selected">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select>
			</td>
			<td align="center"><select  style="width:50px;" id="reserve_inf" name="reserve_inf" class="textbox"><option value="0" selected="selected">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select>
			</td>
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
</div>
