<?php   
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
       	if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        //if(!$se->can_view)
                //die(lanf_fa_class::access_deny);
	//if (!(isset($_REQUEST["sanad_record_id"]) ) )
		//die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
	function loadAdl($inp)
	{
		$ar = array();
		$ar["2"] = "نوزاد";
		$ar["1"] = "کودک";
		$ar["0"] = "بزرگسال";
		return $ar[$inp];
	}
	function loadCity($inp)
	{
		$inp = (int)$inp;
		$out = "";
		$mysql = new mysql_class;
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
		{
			$out = $q[0]["name"];
		}
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
		{
			$out = loadCity($parvaz->mabda_id)."--".loadCity($parvaz->maghsad_id).' شماره '.$parvaz->shomare." <br/>".hamed_pdate($parvaz->tarikh);
		}
		return($out);
	}
	function loadPrint($inp)
	{
		$inp = (int)$inp;
		$ticket = new ticket_class($inp);
		$id = $ticket->getId();
		if($ticket->typ == 0)
			$out = "<div class='msg detail_div' onclick=\"wopen('eticket.php?print=1&shomare=".$ticket->shomare."&id=$inp&','',750,550)\" > ".$ticket->shomare." <br> چاپ </div>";
		else
			$out = "<span style='cursor:pointer;color:blue;' > ".$ticket->shomare."</span>";
		return $out;
	}
	function loadBargasht($inp)
	{
		$out = 'ندارد';
                $inp = (int)$inp;
		$mysql = new mysql_class;
		$ticket = new ticket_class($inp);
//`id`, `fname`, `lname`, `tel`, `adult`, `sanad_record_id`, `parvaz_det_id`, `customer_id`, `user_id`, `shomare`, `typ`, `en`, `regtime`, `mablagh`, `poorsant`
		$mysql->ex_sql("select `parvaz_det_id` from `ticket` where `parvaz_det_id` <> '".$ticket->parvaz_det_id."' and `shomare` = '".$ticket->shomare."' and `regtime`>='".$ticket->regtime."' and `regtime`<DATE_ADD('".$ticket->regtime."',interval 1 minute) ",$q);
		if(isset($q[0]))
		{
			$parvaz = new parvaz_det_class((int)$q[0]["parvaz_det_id"]);
			$out = $parvaz->shomare."( ".loadCity($parvaz->mabda_id)." -> ".loadCity($parvaz->maghsad_id)." )<br/>".hamed_pdate($parvaz->tarikh);
		}
		return($out);	
	}
	function rahgiri($inp)
	{
		$inp = (int) $inp;
		$conf = new conf;
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
	$sanad_record_id = $_REQUEST["sanad_record_id"];
	$ticket_type =  $_REQUEST["ticket_type"];
	$gname = 'grid_ticket_get1';
	$input =array($gname=>array('table'=>'ticket','div'=>'main_div_ticket1'));
	$xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] = "`sanad_record_id`=$sanad_record_id and `en`=1 group by `shomare`";
	$xgrid->eRequest[$gname] = array('sanad_record_id'=>$sanad_record_id,'ticket_type'=>$ticket_type);
	$id = $xgrid->column[$gname][0];
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='';
	$xgrid->column[$gname][2]['name'] ='نام و نام خانوادگی';
	$xgrid->column[$gname][3]['name'] ='تلفن';
	$xgrid->column[$gname][4]['name'] ='AGE';
	$xgrid->column[$gname][4]['cfunction'] = array('loadAdl');
	$xgrid->column[$gname][5]['name'] ='کد رهگیری';
	$xgrid->column[$gname][5]['cfunction'] = array('rahgiri');
	$xgrid->column[$gname][6]['name'] ='پرواز رفت';
	$xgrid->column[$gname][6]['cfunction'] = array('loadParvazInfo');
	$xgrid->column[$gname][7] = $id;
	$xgrid->column[$gname][7]['name'] ='پرواز برگشت';
	$xgrid->column[$gname][7]['cfunction'] = array('loadBargasht');
	$xgrid->column[$gname][8]['name'] ='';
	if($ticket_type!=1)
        {
		$xgrid->column[$gname][9] = $id;
		$xgrid->column[$gname][9]['name'] ='شماره بلیت';
                //$xgrid->column[$gname][9]['cfunction'] = array('loadPrint');
        }	
	$xgrid->column[$gname][10]['name'] ='';
	$xgrid->column[$gname][11]['name'] ='';
	$xgrid->column[$gname][12]['name'] ='';
	$xgrid->column[$gname][13]['name'] ='';
	$xgrid->column[$gname][14]['name'] ='';
	$xgrid->column[$gname][15]['name'] = 'جنسیت';
	$xgrid->column[$gname][15]['cfunction'] = array('loadGender');
	$xgrid->column[$gname][16]['name'] ='مبلغ تور';
	$xgrid->column[$gname][17]['name'] ='';
	$xgrid->column[$gname][18]['name'] ='ایمیل';
	$xgrid->column[$gname][19]['name']='';
	//$xgrid->column[$gname][] =array('name'=>'','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadBargasht'));
	$out1 =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out1);
?>
<script>
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		intialGrid(args);
	});
</script>
<div align="center">
	<br/>
	<!-- <input type='button' value='چاپ تمامی بلیت‌ها' class='inp' style='width:auto;' onclick="wopen('eticket_all.php?sanad_record_id=<?php //echo $sanad_record_id; ?>','',800,550);" > -->
	<input type='button' value='بازگشت به صفحه اصلی' onclick="closeDialog();" >
</div>
<div id="main_div_ticket1" style="overflow:auto;border:1px dotted #bbb;paddin:5px;" >

</div>
