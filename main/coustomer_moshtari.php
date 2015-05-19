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
	$u = new user_class((int)$_SESSION[$conf->app.'_user_id']);


	function sabtPardakhti($inp)
	{
		$inp = (int)$inp;		
		$out = "<u><span style=\"cursor:pointer;color:firebrick;\" onclick=\"openDialog('pardakht_moshtari.php?customer_id=$inp','ثبت پرداختی');\" >ثبت پرداختی</span></u>";
		return ($out);
	}
	if (isset($_SESSION[$conf->app.'_customer_id']))
		$id_cu = (int)$_SESSION[$conf->app.'_customer_id'];
	else
		$id_cu = -1;
	$typ = array();
	$typ["1"] = "اعتباری";
	$typ["2"] = "صندوق";
	$typ["3"] = "نقدی";
	$can_sms = array();
	$can_sms["1"] = "بلی";
	$can_sms["0"] = "خیر";
	$gname = 'grid_customers';
	$input =array($gname=>array('table'=>'customers','div'=>'main_div_customers'));
	$xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] = "`id`='$id_cu' and `en` = 1 ";
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='نام شرکت';
	$xgrid->column[$gname][2]['name'] ='';
	/*$xgrid->column[$gname][2]['name'] ='نوع';
	$xgrid->column[$gname][2]['clist'] = $typ;
	$xgrid->column[$gname][2]['access'] = 'a';*/
	$xgrid->column[$gname][3]['name'] ='اعتبارمالی- ریال';
	//$xgrid->column[$gname][3]['cfunction'] = array('monize');
	$xgrid->column[$gname][4]['name'] ='';
	$xgrid->column[$gname][5]['name'] ='';
	$xgrid->column[$gname][6]['name'] ='';
	$xgrid->column[$gname][7]['name'] ='شروع شماره<br>بلیت';
	$xgrid->column[$gname][8]['name'] ='پایان شماره<br>بلیت';
	$xgrid->column[$gname][9]['name'] ='';
	$xgrid->column[$gname][10]['name'] ='کد';
	$xgrid->column[$gname][8]['access'] = '';
	$xgrid->column[$gname][11]['name'] ='رمز<br />ETicket';
	$xgrid->column[$gname][12]['name'] ='ارسال پیام کوتاه';
	$xgrid->column[$gname][12]['clist'] = $can_sms;
	$xgrid->column[$gname][13]['name'] ='';
	$xgrid->column[$gname][13]['clist'] = 'NO';
	for ($i=0;$i<count($xgrid->column[$gname]);$i++)
		$xgrid->column[$gname][$i]['access'] = 'a';
	unset($xgrid->column[$gname][1]['access']);
	unset($xgrid->column[$gname][10]['access']);
	//$xgrid->column[$gname][] =array('name'=>'پرداختی','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('sabtPardakhti'));
	$xgrid->canAdd[$gname] = FALSE;
	$xgrid->canDelete[$gname] = FALSE;
	$xgrid->canEdit[$gname] = TRUE;
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);
?>
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
<div id="main_div_customers" style="overflow:auto;border:1px dotted #bbb;paddin:5px;" >

</div>
