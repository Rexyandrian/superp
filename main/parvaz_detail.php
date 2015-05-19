<?php   include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
	if(!$se->can_view)
                die(lang_fa_class::access_deny);
			
	function hamed_pdate($str)
	{
		$out=jdate('Y/n/j',strtotime($str));
                return enToPerNums($out);
	}
	function loadSherkat($customer_id)
	{
		$out = '<select id="customer_id" >';//<option value="-1">ندارد</option>';
		$my = new mysql_class;
		$my->ex_sql("select `id`,`name` from `customers` order by `name`",$q);
		foreach($q as $r)
			$out.='<option '.(((int)$r['id']==$customer_id)?'selected="selected"':'').' value="'.$r['id'].'" >'.$r['name'].'</option>'."\n";
		$out .='</select>';
		return $out;
	}
	function loadEsterdad($can)
	{
		$can = (int)$can;
		$out = '<select id="can_esterdad" >';
		$out .='<option '.(($can==0)?'selected="selected"':'').' value="0">نیست</option>';
		$out .='<option '.(($can==1)?'selected="selected"':'').' value="1">است</option>';
		$out .='</select>';
		return $out;
	}
	function loadDomasire($parvaz_det)
	{
		$j_id = (int)$parvaz_det->j_id;
		$out = '<select id="j_id" >';
		$out .='<option '.(($j_id==0)?'selected="selected"':'').' value="0">نیست</option>';
		$out .='<option '.(($j_id==1)?'selected="selected"':'').' value="1">است</option>';
		$out .='</select>';
		return $out;
	}
	function loadParvazDets()
	{
		$out = array();
		$no = date("Y-m-d H:i:s");
		$my = new mysql_class;
		if(!isset($_REQUEST['parvaz_det_id']))
			return $out;
		$parvaz_det_id = (int)$_REQUEST['parvaz_det_id'];
		$parvaz_det = new parvaz_det_class((int)$parvaz_det_id);
		$parvaz = new parvaz_class($parvaz_det->parvaz_id);
		$maghsad_id = $parvaz->maghsad_id;
		$mabda_id = $parvaz->mabda_id;
		$my->ex_sql("select `id` from `parvaz` where `mabda_id`='$maghsad_id' and `maghsad_id`='$mabda_id' group by `id`",$q);
		$ids = array();
		foreach($q as $r)
			$ids[]=$r['id'];
		if(count($ids)>0)
		{
			$q = array();
			$ids = implode(',',$ids);
			$my->ex_sql("select `id`,`parvaz_id`,`tarikh` from `parvaz_det` where `parvaz_id` in ($ids) and `tarikh`>='$no' order by `tarikh` ",$q);
			foreach($q as $r)
			{
				$par = new parvaz_class((int)$r['parvaz_id']);
				$tarikh = jdate('j / n / Y',strtotime($r['tarikh']));
				$out[$r['id']] ='پرواز شماره '.$par->shomare.' تاریخ '.$tarikh ;
			}
		}
		return($out);
	}
	function loadCustomerZakhire($inp)
	{
		$inp = (int)$inp;
		$parvaz_det = new parvaz_det_class($inp);
		$zakhire = ($parvaz_det->zarfiat - $parvaz_det->getZarfiat());
		$out = '<span class="msg pointer"  onclick="setZakhire('.$inp.');" >'.$zakhire.'</span>';
		if($zakhire>0)
			$out = " <span class='notice pointer' style='font-family:tahoma;cursor:pointer;' onclick=\"setZakhire($inp);\">$zakhire</span> ";
		return($out);
	}
	function poorsant($inp)
	{
		//$customer_id = $_SESSION[$conf->app."_customer_id"];
		//$cust = new customer_class($customer_id);
		//$out = "<span style=\"color:firebrick;cursor:pointer;\" onclick=\"wopen('setpoorsant.php?parvaz_det_id=$inp&','',600,200);\">".$cust->getPoorsant($inp)."%</span>";
		$my = new mysql_class;
		$my->ex_sql("select `poor_def` from `parvaz_det` where `id`='$inp'",$q);
		$poor = 0;
		if(isset($q[0]))
			$poor = $q[0]['poor_def'];
		$out = "<span class='msg pointer'  onclick='setPoorsant($inp);' >$poor%</span>";
		return ($out);
	}
	
	function add($gname,$table,$fields,$col)
	{
		$conf = new conf;
		function fieldToId($col,$fieldName)
		{
			$out = -1;
			foreach($col as $id=>$f)
				if($f['fieldname']==$fieldName)
					$out = $id;
			return $out;
		}
		//$rang = $fields['rang'];
		//$rtmp = explode('#',$rang);
		//if($rtmp[0]!='#')
		$fields["parvaz_det_id"] = (int)$_REQUEST['parvaz_det_id'];
		$fi = "(";
                $valu="(";
                foreach ($fields as $field => $value)
                {
			$f_id = fieldToId($col,$field);
			$fn = (isset($col[$f_id]['cfunction']) && isset($col[$f_id]['cfunction'][1]))?$col[$f_id]['cfunction'][1]:'';
                        $fi.="`$field`,";
                        $valu .="'".(($fn!='')?$fn($value):$value)."',";
                }
                $fi=substr($fi,0,-1);
                $valu=substr($valu,0,-1);
                $fi.=")";
                $valu.=")";
		$query="insert into `$table` $fi values $valu";
		$mysql = new mysql_class;		
		$ln = $mysql->ex_sqlx($query,FALSE);
		$out = $mysql->insert_id($ln);
		$mysql->close($ln);
		$ret = FALSE;
		if($out>0)
			$ret = TRUE;
		return $ret;
	}
	function load_gh($inp)
	{
		return($inp);
	}
	function sh_type()
	{
		$out = array();
		$out[0] ='عمومی'; 
		$out[1] ='خصوصی'; 
		return($out);
	}
	
	function saat($inp)
	{
		$inp =substr($inp,0,-3);
		return ($inp);
	}
	function saat1($inp)
	{
		$ms=new mysql_class;
		$ms->ex_sql("select saat from parvaz_det where id='$inp'",$s_res);		
		if(count($s_res)>0)
			$out=$s_res[0]['saat'];
		else
			$out='00:00:00';
		
		return ($out);
	}
	function loadZarfiat($inp)
	{
		$inp = (int)$inp;
		$par = new parvaz_det_class($inp);
		$out = $par->getZarfiat();
		$zarfiat = $out;
		$id = 'zarfiat_'.$inp;
		$onclick = "onclick=\"sendZarfiat($inp)\"";
		$out = '<span class="msg pointer" '.$onclick.' >'.$zarfiat.'</span>';
		if($zarfiat == 0)
			$out = '<span  class=\'notice pointer\'  '.$onclick.' >CLOSED</span>';
		return $out;
	}
	if(isset($_POST['ghimat']))
	{
		$ghimat = (int)$_POST['ghimat'];
		$typ = (int)$_POST['typ'];
		$saat = trim($_POST['saat']);
		$parvaz_det_id =(int)$_REQUEST['parvaz_det_id_update'];
		$my = new mysql_class;
		die($my->ex_sqlx("update parvaz_det set ghimat=$ghimat,saat='$saat',typ=$typ where id =$parvaz_det_id "));
	}
	if(isset($_REQUEST['parvaz_det_id']))
	{
		$parvaz_det_id =(int)$_REQUEST['parvaz_det_id'];
		$parvaz_det = new parvaz_det_class($parvaz_det_id );
		$parvaz = new parvaz_class($parvaz_det->parvaz_id);
		$p_id=$_REQUEST['parvaz_det_id'];
		$ms=new mysql_class;
		$ms->ex_sql("select * from parvaz_det where id='$p_id'",$rec_res);
		if(count($rec_res)>0)
		{
			$par_gh=($rec_res[0]['ghimat']!='' ?$rec_res[0]['ghimat'] : 0) ;
			$par_za= loadZarfiat($p_id);
			$par_sa=saat(($rec_res[0]['saat']!= '' ? $rec_res[0]['saat'] :0));
			$par_co=poorsant($p_id);
			$par_zakh=loadCustomerZakhire($p_id);
			$par_no=($rec_res[0]['typ']!='' ? $rec_res[0]['typ'] : 0);
		}
		$com_typ="<select class='parav' id='typ' ><option value='-1'></option>";
		$com_typ.="<option value='0' ".($par_no==0 ? "selected='selected'" : ''). " >عمومی</option>";
		$com_typ.="<option value='1' ".($par_no==1 ? "selected='selected'" : ''). " >خصوصی</option>";
		$com_typ.="</select>";
	}
	else
		$parvaz_det_id=-1;
	if(isset($_REQUEST['mablagh_kharid']))
	{
		$mablagh_kharid = $_REQUEST['mablagh_kharid'];
		$lastvalue = $parvaz_det->mablagh_kharid;
		if($lastvalue !=$mablagh_kharid)
		{
			$parvaz_det->mablagh_kharid = $mablagh_kharid - $lastvalue ;
			$parvaz_det->kharidParvaz($parvaz_det->zarfiat,'بابت تغییر قیمت خرید پرواز از '.$lastvalue.'  به '.$mablagh_kharid );
		}
		$customer_id = (int)$_REQUEST['customer_id'];
		if($parvaz_det->customer_id!=$customer_id)
		{
			$par = $parvaz_det;
			$par->mablagh_kharid = -1 * $par->mablagh_kharid;
			$cust1 = new customer_class($par->customer_id);
			$cust1 = (($par->customer_id>0)?$cust1->name:'مدیریت');
			$cust2 = new customer_class($customer_id);
                        $cust2 = (($customer_id>0)?$cust2->name:'مدیریت');
                        $par->kharidParvaz($par->zarfiat,'بابت تغییر فروشنده از '.$cust1.' به '.$cust2);
			$par->mablagh_kharid = -1 * $par->mablagh_kharid;
			$par->customer_id = $customer_id;
			$par->kharidParvaz($par->zarfiat,'بابت تغییر فروشنده از '.$cust1.' به '.$cust2);
		}
		$can_esterdad = $_REQUEST['can_esterdad'];
		$tour_mablagh = $_REQUEST['tour_mablagh'];
		$toz = $_REQUEST['toz'];
		$my = new mysql_class;
		$out = $my->ex_sqlx("update `parvaz_det` set `mablagh_kharid`='$mablagh_kharid',`customer_id`='$customer_id',`can_esterdad`='$can_esterdad',`tour_mablagh`='$tour_mablagh',`toz`='$toz' where `id`='$parvaz_det_id' ");
		die($out);
	}
	if(isset($_REQUEST['setj_id']))
	{
		$j_id = (int)$_REQUEST['setj_id'];
		$my = new mysql_class;
		$my->ex_sqlx("update `parvaz_det` set `j_id`='$j_id' where `id`='$parvaz_det_id' ");
		if($j_id==0)
			$my->ex_sqlx("delete from `parvaz_jid` where `parvaz_det_id`='$parvaz_det_id' ");
		die("$j_id");
	}
	$gname = 'grid_parvaz_jid';
	$input =array($gname=>array('table'=>'parvaz_jid','div'=>'parvaz_jid_div'));
	$xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] = "`parvaz_det_id`='$parvaz_det_id'";
	$xgrid->eRequest[$gname] = array('parvaz_det_id'=>$parvaz_det_id );
	$id = $xgrid->column[$gname][0];
	//$xgrid->alert = TRUE;
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='';
	$xgrid->column[$gname][2]['name'] ='پرواز';
	$xgrid->column[$gname][2]['clist'] =loadParvazDets();
	
	$xgrid->canEdit[$gname] = TRUE;
	$xgrid->canAdd[$gname] = TRUE;
	$xgrid->canDelete[$gname] = TRUE;
	$xgrid->addFunction[$gname] = 'add';
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out)
?>
<script>
var parvaz_det_id = <?php echo $parvaz_det_id; ?>;
	$("#sabt").click(function(){
		var mablagh_kharid = $("#mablagh_kharid").val();
		var customer_id = $("#customer_id").val();
		var can_esterdad = $("#can_esterdad").val();
		var tour_mablagh = $("#tour_mablagh").val();
		var toz = $("#toz").val();
		$("#sabt").attr('disabled','disabled');
		$("#msg_div").removeClass();
		$("#msg_div").show();
		$("#msg_div").html('<img src="../img/status_fb.gif" >');
		var tmp ="parvaz_det_id="+parvaz_det_id+"&mablagh_kharid="+mablagh_kharid+"&customer_id="+customer_id+"&can_esterdad="+can_esterdad+"&tour_mablagh="+tour_mablagh+"&toz="+toz;
		//alert("parvaz_detail.php?"+tmp+"&r="+Math.random());
		$.get("parvaz_detail.php?"+tmp+"&r="+Math.random(),function(result){
			result = trim(result);
			if(result=='ok')
			{
				$("#msg_div").html('');
				$("#msg_div").hide();
				$("#msg_div").html('ثبت با موفقیت انجام شد');
				$("#msg_div").addClass('msg');
				$("#msg_div").fadeIn(1500);
			}
			else 
			{
				$("#msg_div").html('');
				$("#msg_div").hide();
				$("#msg_div").html('خطا در ثبت');
				$("#msg_div").addClass('notice');
				$("#msg_div").fadeIn(1500);
			}
		});
		$("#sabt").removeAttr('disabled');
	});
	$("#msg_div").click(function(){
		$("#msg_div").fadeOut(1500);
	});
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		intialGrid(args);
	});
	$("#j_id").change(function(){
		var tmp = "parvaz_det_id="+parvaz_det_id+"&setj_id="+$("#j_id").val();
		$("#progress").html('<img src="../img/status_fb.gif" >');
		$.get("parvaz_detail.php?"+tmp+"&r="+Math.random(),function(result){
		$st = trim(result);
		$("#progress").html('');
		if(parseInt($st)==0)
			$("#grid_div").hide('slow');
		else if(parseInt($st)==1)
			$("#grid_div").show('slow');
		});
	});
	function isNumber(n) 
	{   
		return /^[0-9]+$/.test(n); 
	}
	function sendZarfiat(id)
	{
		openDialog("zarfiat.php?parvaz_det_id="+id,"تغییر ظرفیت",{'minWidth':300,'minHeight':200});
	}
	 
	function  edit_rec()
	{
		data={
		'parvaz_det_id_update':parvaz_det_id
};
		$.each($(".parav"),function(id,field){
			data[$(field).prop('id')] = $(field).val();
		});
		$("#khoon").html('<img src="../img/status_fb.gif" >');	
		$.post("parvaz_detail.php",data,function(result){
			$("#khoon").html('');
			if($.trim(result)=='ok')
			{
				searchFlight();
				alert('ثبت با موفقیت انجام شد');
			}
			else
				alert('خطا در بروز رسانی اطلاعات');
		});
	}
	function setPoorsant(inp)
	{
		openDialog("setpoorsant.php?parvaz_det_id="+inp+"&","تعریف کمیسیون",{'minWidth':500,'minHeight':200},false);
	}
	function setZakhire(inp)
	{
		openDialog("zakhire.php?parvaz_det_id="+inp+"&","ذخیره",{'minWidth':750,'minHeight':400},false);	
	}
	
</script>
	<div>
		<table >
			<tr>
				<td> قیمت</td><td>
				<input type="text" class="parav"  id="ghimat" value="<?php  echo $par_gh; ?>">
				 </td>
				<td> نوع</td>
				<td><?php  echo $com_typ; ?> </td>
				<td> ساعت</td><td>
					<input type="text" class="parav"  id="saat" value="<?php  echo $par_sa; ?>" >
				</td>
			</tr>
			<tr>
				<td>کمیسیون </td><td> 
					<?php  echo $par_co; ?>
				</td>
				<td>ذخیره </td><td>
					<?php  echo $par_zakh; ?>
				</td>
				<td> ظرفیت</td><td> 
					<?php  echo $par_za; ?>
				</td>
				
			</tr>
	
			<tr>
				<td>
					<input type="button" onclick="edit_rec();" value="ویرایش " >
					<span id="khoon" ></span>
				</td>
			</tr>
			
			
		</table>
	</div>
 <table class="detailTB" >
	<tr>
		<th colspan="2" >
	تعیین جزئیات جهت پرواز 
		<?php echo $parvaz->shomare." به تاریخ ".hamed_pdate($parvaz_det->tarikh); ?>
		</th>
	</tr>
	<tr>
		<td>
		مبلغ خرید:
			<input value="<?php echo $parvaz_det->mablagh_kharid; ?>" placeholder="مبلغ خرید" id="mablagh_kharid" >			
		</td>
		<td>
				شرکت فروشنده:
			<?php echo loadSherkat($parvaz_det->customer_id); ?>
		</td>
	</tr>
	<tr>
		<td>
				قابل استرداد:
			<?php echo loadEsterdad($parvaz_det->can_esterdad); ?>
		</td>
		<td>
			مبلغ اضافه بابت تور:
			<input value="<?php echo $parvaz_det->tour_mablagh; ?>" placeholder="مبلغ تور" id="tour_mablagh" >	
		</td>
	</tr>
	<tr>
		<td valign="top" >
			توضیحات:
			<textarea id="toz" ><?php echo $parvaz_det->toz; ?></textarea>
		</td>
		<td>
			<button id="sabt" >ذخیره</button>
		</td>
	</tr>
</table>
<div id="msg_div" align="center" style="cursor:pointer;" >
</div>
----------------------------------------------------------------------------------------------------
<p>
			دومسیره:
			<?php echo loadDomasire($parvaz_det);
				$display = 'style="display:none"';
				if((int)$parvaz_det->j_id==1)
					$display= '';
			 ?>
</p>
<div id="progress" >
</div>
<div <?php echo $display; ?> id="grid_div" > 
	<div id="parvaz_jid_div" >
	</div>
</div>
