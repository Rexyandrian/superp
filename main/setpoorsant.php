<?php   
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
	function hamed_pdate($str)
	{
		$out=jdate('Y/n/j',strtotime($str));
                return enToPerNums($out);
	}
	function loadCust()
	{
		$out = '<select id="customer_id" ><option value="-1" >همه</option>'."\n";
		$mysql = new mysql_class;
		$mysql->ex_sql("select `id`,`name` from `customers` order by `name` ",$q);
		foreach($q as $r)
			$out.='<option value="'.$r['id'].'" >'.$r['name'].'</option>'."\n";
		$out .='</select>';
		return $out;
	}
	function del_item($table,$id,$gname)
	{
		$mysql = new mysql_class;
		$re = $mysql->ex_sqlx("update `customer_parvaz` set `poorsant`='0' where `id`='$id'");
		return ( ($re=='ok')?TRUE:FALSE );
	}
	function loadMoshtari($inp)
	{
		$out = '';
		$mysql = new mysql_class;
		$mysql->ex_sql("select `name` from `customers` where `id`='$inp'",$q);
		if(isset($q[0]))
			$out =$q[0]['name'] ;
		return $out;
	}
	function loadUser($inp)
	{
		$out = '';
		$mysql = new mysql_class;
		$mysql->ex_sql("select `fname`,`lname` from `user` where `id`='$inp'",$q);
		if(isset($q[0]))
			$out =$q[0]['fname'].' '.$q[0]['lname'] ;
		return $out;
	}
	$parvaz_det_id = ((isset($_REQUEST["parvaz_det_id"]))?(int)$_REQUEST["parvaz_det_id"]:-1);
	$parvaz = new parvaz_det_class($parvaz_det_id);
	if(isset($_REQUEST["poorsant"]) && isset($_REQUEST['customer_id']))
	{
		$customer_id = (int)$_REQUEST['customer_id'];
		if($customer_id>0)
		{
			$cust = new customer_class($customer_id);
			$cust->setPoorsant($parvaz_det_id,(int)$_REQUEST["poorsant"]);
		}
		else
		{
			$mysql = new mysql_class;
			$mysql->ex_sqlx("update `parvaz_det` set `poor_def`='".((int)$_REQUEST["poorsant"])."' where `id`='$parvaz_det_id'");
		}
		$arg["toz"]="مقدار ".$_REQUEST["poorsant"]." پورسانت برای پرواز شماره".
$parvaz->shomare.' تاریخ '.$parvaz->tarikh." ثبت گردید." ;
                $arg["user_id"]=$_SESSION[$conf->app."_user_id"];
                $arg["host"]=$_SERVER["REMOTE_ADDR"];
                $arg["page_address"]=$_SERVER["SCRIPT_NAME"];
                $arg["typ"]=6;
                log_class::add($arg);	
		die ("ok");
	}
	$gname = 'customer_parvaz_poor';
	$input =array($gname=>array('table'=>'customer_parvaz','div'=>'customer_parvaz_poor_div'));
	$xgrid = new xgrid($input);
	//$xgrid->alert = TRUE;
	$xgrid->eRequest[$gname] = array('parvaz_det_id'=>$parvaz_det_id);
	$xgrid->whereClause[$gname] = " `parvaz_det_id`=$parvaz_det_id and `poorsant`<>0 ";
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='مشتری';
	$xgrid->column[$gname][1]['cfunction']=array('loadMoshtari');
	$xgrid->column[$gname][2]['name'] ='';
	$xgrid->column[$gname][3]['name'] ='کمیسیون';
	$xgrid->column[$gname][4]['name'] ='';
	$xgrid->column[$gname][5]['name'] ='کاربر ثبت کننده';
	$xgrid->column[$gname][5]['cfunction']=array('loadUser');
	$xgrid->column[$gname][6]['name'] ='';
	$xgrid->column[$gname][7]['name'] ='';
	$xgrid->column[$gname][8]['name'] ='';
	$xgrid->deleteFunction[$gname] = 'del_item';
	//$xgrid->canEdit[$gname] = TRUE;
	//$xgrid->canAdd[$gname] = TRUE;
	$xgrid->canDelete[$gname] = TRUE;
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out)
?>
<script>
	$(document).ready(function(){
		$("#customer_id").change(function(){
			var tmp = $("#customer_id").val();
			var werc = 'where 1=1';
			if(parseInt(tmp,10)!=-1)
				werc = "where `customer_id`='"+tmp+"'";
			var ggname ='<?php echo $gname; ?>';
			whereClause[ggname] = encodeURIComponent(werc);
			grid[ggname].init(gArgs[ggname]);
		});
		var args=<?php echo $xgrid->arg; ?>;
		intialGrid(args);
	});
	function sabtPoorsant()
	{
		var poor = $("#poorsant").val();
		var customer_id = $("#customer_id").val();
		$("#msg_div").hide();
		$("#msg_div").html('<img src="../img/status_fb.gif" >');
		$("#msg_div").fadeIn(300);
		if(parseInt(poor,10)>=0)
		{
			$.get("setpoorsant.php?parvaz_det_id=<?php echo $parvaz_det_id; ?>&poorsant="+poor+"&customer_id="+customer_id+"&r="+Math.random()+"&",function(result){
				result = trim(result);
				if(result=='ok')
				{
					$("#msg_div").html('');
					$("#msg_div").hide();
					$("#msg_div").html('ثبت با موفقیت انجام شد');
					$("#msg_div").addClass('msg');
					$("#msg_div").fadeIn(1500,function(){
						$("#msg_div").fadeOut(2500);
					});
				}
				else
				{
					$("#msg_div").html('');
					$("#msg_div").hide();
					$("#msg_div").html('خطا در ثبت');
					$("#msg_div").addClass('notice');
					$("#msg_div").fadeIn(1500,function(){
						$("#msg_div").fadeOut(2500);
					});
				}
				var ggname ='<?php echo $gname; ?>';
				grid[ggname].init(gArgs[ggname]);
			});	
		}
		else
			alert('مقدار را درست وارد کنید');
	}
</script>
<div align="center" >
	<h2>
		اختصاص کمیسیون به 
	 <?php echo loadCust(); ?>
	جهت پرواز 
	<?php echo $parvaz->shomare." به تاریخ ".hamed_pdate($parvaz->tarikh); ?>

	</h2>	
	<p>
	میزان کمیسیون به درصد:
		<input type="text" id="poorsant" name="poorsant" value="<?php echo $parvaz->poor_def; ?>" style="width:30px;" >
		<input type="hidden" id="tmp" >
		<button onclick="sabtPoorsant();" >ذخیره</button>
	</p>
</div>
<div id="msg_div" align="center" >
</div>
<div id="customer_parvaz_poor_div" >
</div>
