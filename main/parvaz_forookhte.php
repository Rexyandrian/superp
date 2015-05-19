<?php   include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	
	function loadTyp($inp)
	{
		$inp = (int)$inp;
		$out = 'نامشخص';
		switch ($inp)
		{
			case 0:
				$out = 'ADL';
				break;
			case 1:
				$out = 'CHD';
				break;
			case 2:
				$out = 'INF';
				break;
		}
		return $out;	
	}
	function loadCustomer($inp)
        {
                $cust = new customer_class((int)$inp);
                return($cust->name);
        }
	function loadUser($inp)
        {
                $cust = new user_class((int)$inp);
                return($cust->fname.' '.$cust->lname);
        }
	function loadGhimat($inp)
	{
		return perToEnNums(monize($inp));
	}
	function loadPoor($inp)
	{
		return ($inp.'%');
	}
	function loadTicket($id)
	{
		$str = '';
		$conf = new conf;
		$ref = '';
		$my = new mysql_class;
		$my->ex_sql("select `shomare`,`sanad_record_id` from `ticket` where `id`='$id' ",$q);
		if(isset($q[0]))
		{
			$ref = ticket_class::rahgiriToCode($q[0]['sanad_record_id'],$conf->rahgiri);
			$str='eticket.php?shomare='.$q[0]['shomare'].'&id='.$id;
		}
		return '<span class=\'msg detail_div\'  onclick="wopen(\''.$str.'\',\'\',800,500)" >چاپ('.$ref.')</span>';
	}
	function hamed_pdate($inp)
        {
                return(audit_class::hamed_pdate($inp));
        }
	function delete_item($table,$inp,$gname)
	{
		$my = new mysql_class;
		$my->ex_sqlx("update `$table` set `tarikh` = now() - interval (`timeout` + 1) minute where `id` in ($inp)");
		ticket_class::clearTickets();
		return TRUE;
	}
	function loadCust($id)
	{
		$c = new customer_class((int)$id);
		return($c->name);
	}
	function loadSite($inp)
	{
		$out = '---';
		if($inp>0)
		{
			$si = new sites_class($inp);
			$out = isset($si->name)?'<a target="_blank" href="'.$si->url_addr.'" >'.$si->name.'</a>':'نامشخص';
		}
		return($out);
	}
        $parvaz_det_id = ((isset($_REQUEST["parvaz_det_id"]))?(int)$_REQUEST["parvaz_det_id"]:-1);
        $parvaz_det = new parvaz_det_class($parvaz_det_id);
	$gname = 'grid_reserve_tmp';
	$gname2 = 'grid_ticket';
	$input =array($gname=>array('table'=>'reserve_tmp','div'=>'reserve_tmp_div'),$gname2=>array('table'=>'ticket','div'=>'ticket_div'));
	$xgrid = new xgrid($input);
	unset($xgrid->column[$gname][5]);
	unset($xgrid->column[$gname][6]);
	unset($xgrid->column[$gname][7]);
	unset($xgrid->column[$gname][8]);
	//$xgrid->alert = TRUE;
	$xgrid->whereClause[$gname] = "`parvaz_det_id`='$parvaz_det_id'";
	$xgrid->whereClause[$gname2] = "`parvaz_det_id`='$parvaz_det_id' and `en`='1'";
	$xgrid->eRequest[$gname] = array('parvaz_det_id'=>$parvaz_det_id );
	$xgrid->eRequest[$gname2] = array('parvaz_det_id'=>$parvaz_det_id );
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='';
	$xgrid->column[$gname][2]['name'] ='تاریخ';
	$xgrid->column[$gname][2]['cfunction'] =array('hamed_pdate');
	$xgrid->column[$gname][3]['name'] ='تعداد';
	$xgrid->column[$gname][4]['name'] ='مشتری';
	$xgrid->column[$gname][4]['cfunction'] = array('loadCust');
	//$xgrid->column[$gname][5]['name'] ='';
	//$xgrid->column[$gname][6]['name'] ='';
	//$xgrid->column[$gname][7]['name'] ='';
	//$xgrid->column[$gname][8]['name'] ='';
	//$xgrid->canEdit[$gname] = TRUE;
	//$xgrid->canAdd[$gname] = TRUE;
	$xgrid->canDelete[$gname] = TRUE;
	$xgrid->deleteFunction[$gname] = 'delete_item';
	
	//$xgrid->start[$gname2] = FALSE;

	$xgrid->column[$gname2][0]['name'] ='';
	$xgrid->column[$gname2][1]['name'] ='';
	$xgrid->column[$gname2][2]['name'] ='نام';
	$xgrid->column[$gname2][3]['name'] ='تلفن';
	$xgrid->column[$gname2][4]['name'] ='نوع';
	$xgrid->column[$gname2][4]['cfunction'] =array('loadTyp');
	$xgrid->column[$gname2][4]['access'] ='a';
	$xgrid->column[$gname2][5]['name'] ='';
	$xgrid->column[$gname2][6]['name'] ='';
	$xgrid->column[$gname2][7]['name'] ='مشتری';
	$xgrid->column[$gname2][7]['cfunction'] =array('loadCustomer');
	$xgrid->column[$gname2][7]['access'] ='a';
	$xgrid->column[$gname2][8]['name'] ='کاربر';
	$xgrid->column[$gname2][8]['cfunction'] =array('loadUser');
	$xgrid->column[$gname2][8]['access'] ='a';
	$xgrid->column[$gname2][9]['name'] ='شماره';
	$xgrid->column[$gname2][10]['name'] ='';
	$xgrid->column[$gname2][11]['name'] ='';
	$xgrid->column[$gname2][12]['name'] ='';
	$xgrid->column[$gname2][13]['name'] ='قیمت';
	$xgrid->column[$gname2][13]['cfunction'] =array('loadGhimat');
	$xgrid->column[$gname2][13]['access'] ='a';
	$xgrid->column[$gname2][14]['name'] ='ک.م.';
	$xgrid->column[$gname2][14]['cfunction'] =array('loadPoor');
	$xgrid->column[$gname2][14]['access'] ='a';
	$xgrid->column[$gname2][15]['name'] ='';
	$xgrid->column[$gname2][16]['name'] ='';
	$xgrid->column[$gname2][17]['name'] ='سایت';
	$xgrid->column[$gname2][17]['cfunction'] =array('loadSite');
	$xgrid->column[$gname2][17]['access']='a';
	$xgrid->column[$gname2][18]['name'] ='ایمیل';
	$xgrid->column[$gname2][19]['name'] ='وضعیت';
	$xgrid->column[$gname2][19]['clist'] =array('0'=>'خیر','1'=>'بله');
/*	
	$xgrid->column[$gname2][20] = $xgrid->column[$gname2][0];
	$xgrid->column[$gname2][20]['name'] ='بلیت';
	$xgrid->column[$gname2][20]['cfunction'] =array('loadticket');
	$xgrid->column[$gname2][20]['access'] ='a';
*/	
	$xgrid->canEdit[$gname2] = TRUE;
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out)
?>
<script>
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		intialGrid(args);
	});
	$("#manifest").click(function(){
		wopen("manifest2.php?parvaz_det_id="+<?php echo $parvaz_det_id; ?>,'',900,550);
	});
</script>
<div class="msg detail_div" id="manifest" >
	برای مشاهده منیفست اینجا کلیک کنید
</div>
<div id="ticket_div" >
</div>
<div id="reserve_tmp_div" >
</div>
