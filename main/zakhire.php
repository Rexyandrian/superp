<?php  include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadMoshtari()
	{
		$conf = new conf;
		$out=null;
		if((int)$_SESSION[$conf->app.'_customer_typ']==2)
		{
			mysql_class::ex_sql("select `name`,`id` from `customers` order by `id`",$q);
		}
		else
		{
			mysql_class::ex_sql("select `name`,`id` from `customers` where `id`='".(int)$_SESSION[$conf->app.'_customer_id']."' order by id",$q);
		}
		while($r=mysql_fetch_array($q,MYSQL_ASSOC))
		{
			$out[$r["name"]]=(int)$r["id"];
		}
		return $out;
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
		$tmp = new parvaz_det_class($inp);
		$parvaz = new parvaz_class($tmp->parvaz_id);
		if($parvaz->getId() >0)
			$out = loadHavapeima($parvaz->havapiema_id)."<br/>".loadSherkat($parvaz->sherkat_id);
		return($out);
	}
	function loadUser($inp)
	{
		$inp = (int) $inp;
		$out ='';
		mysql_class::ex_sql('select `fname`,`lname` from user where `id`='.$inp,$q);
		if($r = mysql_fetch_array($q))
		{
			$out = $r['fname'].' '.$r['lname'];
		}
		return $out;
	}
	function hamed_pdate($str)
        {
                $out=jdate('d / m / Y',strtotime($str));
                return ($out);
        }
	function timeToMin($inp)
	{
		$out = (int)$inp;
		$tmp = explode(":",$inp);
		if(count($tmp)==2)
			$out = (int)$tmp[0]*60 + (int)$tmp[1];
		return($out);
	}
	function add_item()
        {
		$parvaz_det_id = ((isset($_REQUEST['parvaz_det_id']))?(int)$_REQUEST["parvaz_det_id"]:-1);
                $fields = array();
                foreach($_REQUEST as $key => $value)
                {
                        if(strpos($key,'new_') === 0 && $key != 'new_id' && $key != 'new_z_user_id' && $key != 'new_regtime')
                        {
                                $fields[substr($key,4)] = $value;
                        }
                }
		
                $fi = "(";
                $va = "(";
		$fields['z_user_id'] = (int)$_SESSION[$conf->app.'_user_id'];
		$fields['parvaz_det_id'] = (int)$parvaz_det_id;
		$cust_id = (int)$fields["customer_id"];
		$fields["deadtime"] = timeToMin($fields["deadtime"]);
		$poorsant = -1;
		$fields['poorsant'] = $poorsant;
                foreach($fields as $key1 => $value1)
                {
                        $fi .= "`$key1`,";
                        $va .= "'$value1',";
                }
                $fi = substr($fi,0,-1);
                $va = substr($va,0,-1);
                $fi .= ")";
                $va .= ")";
		$zarfiat = 0;
		$par = new parvaz_det_class($parvaz_det_id);
		$cust = new customer_class($cust_id);
		mysql_class::ex_sql("select `zarfiat` from `parvaz_det` where `id` = $parvaz_det_id",$q);
		if($r = mysql_fetch_array($q))
			$zarfiat = (int)$r["zarfiat"];
		$arg["toz"]="به تعداد ".$fields['zakhire']." از پرواز شماره ".$par->shomare.' تاریخ '.jdate("d / m / Y",strtotime($par->tarikh)).' برای آژانس '.$cust->name.' ذخیره گردید ';
	        $arg["user_id"]=$_SESSION[$conf->app."_user_id"];
	        $arg["host"]=$_SERVER["REMOTE_ADDR"];
	        $arg["page_address"]=$_SERVER["SCRIPT_NAME"];
	        $arg["typ"]=5;
	        log_class::add($arg);
                $query = "insert into `customer_parvaz` $fi values $va";
		mysql_class::ex_sql("select `id` from `customer_parvaz` where `customer_id`='$cust_id' and `parvaz_det_id`=$parvaz_det_id",$sq);
		if(!($r = mysql_fetch_array($sq)) && (int)$fields["zakhire"]<=$zarfiat)
                	mysql_class::ex_sqlx($query);
        }
	function loadDefTime($inp)
	{
		$inp = (int)$inp;
		$out = "0";
		$curtime = date("H:i");
		$deadtime = 0;
		$regtime = "0:0";
		mysql_class::ex_sql("select `regtime`,`deadtime` from `customer_parvaz` where `id`=$inp",$q);
		if($r = mysql_fetch_array($q))
		{
			$deadtime = (int)$r["deadtime"];
			$regtime = strtotime($r["regtime"]." + $deadtime minutes");
			$regtime = date("H:i",$regtime);
			$tmp = explode(":",$regtime);
			$reg = 60*(int)$tmp[0]+(int)$tmp[1];
			$tmp = explode(":",$curtime);
			$no = 60*(int)$tmp[0]+(int)$tmp[1];
			$out = (($reg-$no>0)?$reg-$no:"0");
			//echo "regtime = $regtime <br/>\nNow = $curtime";
		}
		//if($out==0) 
			//$out ='نامحدود';
		$out = $out;//enToPerNums($out);
		return($out);
	}
	function delete_item($id)
	{
		$cust_par = new customer_parvaz_class;
		$cust_par = $cust_par->loadField($id,array('zakhire','parvaz_det_id','customer_id'));
		if($cust_par!==FALSE)
		{
			$par = new parvaz_det_class($cust_par['parvaz_det_id']);
			$cust = new customer_class;
			$cust = $cust->loadField($cust_par['customer_id'],array('name'));
			$arg["toz"]="به تعداد ".$cust_par['zakhire']." از پرواز شماره ".$par->shomare.' تاریخ '.jdate("d / m / Y",strtotime($par->tarikh)).' از آژانس '.$cust['name'].' ذخیره حذف گردید ';
			$arg["user_id"]=$_SESSION[$conf->app."_user_id"];
			$arg["host"]=$_SERVER["REMOTE_ADDR"];
			$arg["page_address"]=$_SERVER["SCRIPT_NAME"];
			$arg["typ"]=5;
			log_class::add($arg);
			mysql_class::ex_sqlx('delete from `customer_parvaz` where `id`= '.$id);
		}
	}
	function add_zakhire($gname,$table,$fields,$column)
	{
		$conf = new conf;
		$mysql = new mysql_class;
		$customer_id = (int)$_REQUEST['customer_id'];
		$parvaz_det_id = (int)$_REQUEST['parvaz_det_id'];
		$p = new parvaz_det_class($parvaz_det_id);
		$dt = date("Y-m-d H:i:s");
		$user_id = (int)$_SESSION[$conf->app.'_user_id'];
		$out = FALSE;
		$zakh = 0;
		$mysql->ex_sql("select sum(`zakhire`) as `za` from `customer_parvaz` where `parvaz_det_id` = $parvaz_det_id and `customer_id` <> $customer_id",$q);
		if(isset($q[0]))
			$zakh = (int)$q[0]['za'];
		$q = null;
		if(($customer_id > 0) && ($p->zarfiat >= $zakh+(int)$fields['zakhire']))
		{
			$mysql->ex_sql("select `id` from `customer_parvaz` where `customer_id` = $customer_id and `parvaz_det_id` = $parvaz_det_id",$q);
			if(isset($q[0]))
				$mysql->ex_sqlx("update `customer_parvaz` set `zakhire` = ".((int)$fields['zakhire']).", `deadtime` = ".((int)$fields['deadtime'])." where `id` = ".$q[0]['id']);
			else
				$mysql->ex_sqlx("insert into `$table` (`customer_id`, `parvaz_det_id`, `poorsant`, `zakhire`, `z_user_id`, `regtime`, `deadtime`) values ($customer_id,$parvaz_det_id,0,".((int)$fields['zakhire']).",$user_id,'$dt',".((int)$fields['deadtime']).")");
			$out = TRUE;
		}
		return($out);
	}
	function delete_zakhire($table,$id,$gname)
	{
		$out= FALSE;
		$id = (int)$id;
		$mysql = new mysql_class;
		$mysql->ex_sql("select `poorsant` from `customer_parvaz` where `id`='$id'",$q);
		if(isset($q[0]))
		{
			if((int)$q[0]['poorsant']!=-1)
				$mysql->ex_sqlx("update `$table` set `zakhire` = 0 where `id` = $id");
			else
				$mysql->ex_sqlx("delete from `customer_parvaz` where `id` = $id");
			$out = TRUE;
		}
		return($out);
	}
        function loadCust()
        {
                $out = '<select id="customer_id" ><option value="-1" >همه</option>'."\n";
                $mysql = new mysql_class;
                $mysql->ex_sql("select `id`,`name` from `customers` where `en` = 1 order by `name` ",$q);
                foreach($q as $r)
                        $out.='<option value="'.$r['id'].'" >'.$r['name'].'</option>'."\n";
                $out .='</select>';
                return $out;
        }
	function loadCustomerName($id)
	{
		$id = (int)$id;
		$cu = new customer_class($id);
		return($cu->name);
	}
	function loadUserName($id)
	{
		$id = (int)$id;
                $cu = new user_class($id);
                return($cu->fname." ".$cu->lname);
	}
	function loadPDate($dt)
	{
		if($dt != '0000-00-00 00:00:00')
			$out = jdate("H:i:s d / m / Y",strtotime($dt));
		else
			$out = '';
		return($out);
	}
	$parvaz_det_id = -1;
	$custome_id = -1;
	if(isset($_REQUEST['parvaz_det_id']))
	{
		$parvaz_det_id =(int)$_REQUEST["parvaz_det_id"];
		$parvaz_det = new parvaz_det_class($parvaz_det_id);
		$parvaz = new parvaz_class($parvaz_det->parvaz_id);
		$custome_id = isset($_REQUEST["customer_id"])?(int)$_REQUEST["customer_id"]:-1;
	}
	$mysql = new mysql_class;
	$mysql->ex_sqlx("update `customer_parvaz` set `regtime`='0000-00-00 00:00:00' where  date_add(`regtime`,interval `deadtime` minute)<now() ");
	$mysql->ex_sqlx("delete from `customer_parvaz` where `regtime`='0000-00-00 00:00:00'  and `poorsant`=-1");
	$gname = 'customer_parvaz';
	$input =array($gname=>array('table'=>'customer_parvaz','div'=>'customer_parvaz_div'));
	$xgrid = new xgrid($input);
	$xgrid->eRequest[$gname] = array('parvaz_det_id'=>$parvaz_det_id);
	$xgrid->whereClause[$gname] = " `parvaz_det_id`=$parvaz_det_id and `zakhire` > 0 ".(($custome_id > 0)?" and `customer_id`  = $custome_id":'');
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='مشتری';
	$xgrid->column[$gname][1]['cfunction'] = array('loadCustomerName');
	$xgrid->column[$gname][1]['access'] = 'a';
	$xgrid->column[$gname][2]['name'] ='';
	$xgrid->column[$gname][3]['name'] ='';
	$xgrid->column[$gname][4]['name'] ='تعداد';
	$xgrid->column[$gname][5]['name'] ='';
	$xgrid->column[$gname][6]['name'] ='کاربر';
	$xgrid->column[$gname][6]['cfunction'] = array('loadUserName');
	$xgrid->column[$gname][6]['access'] = 'e';
	$xgrid->column[$gname][7]['name'] ='زمان ثبت';
	$xgrid->column[$gname][7]['cfunction'] = array('loadPDate');
	$xgrid->column[$gname][7]['access'] = 'e';
	$xgrid->column[$gname][8]['name'] ='زمان باقیمانده';
	$xgrid->addFunction[$gname] = 'add_zakhire';
	$xgrid->deleteFunction[$gname] = 'delete_zakhire';
	$xgrid->canEdit[$gname] = TRUE;
	$xgrid->canAdd[$gname] = TRUE;
	$xgrid->canDelete[$gname] = TRUE;
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);
?>
<script>
	$(document).ready(function(){
		$("#customer_id").change(function(){
			gArgs['customer_parvaz']['eRequest']['customer_id'] = $(this).val();
			grid['customer_parvaz'].init(gArgs['customer_parvaz']);
		});
		var args=<?php echo $xgrid->arg; ?>;
/*
		args['customer_parvaz']['beforeLoad'] = beforeLoadF;
		args['customer_parvaz']['afterLoad'] = afterLoadF;
		args['customer_parvaz']['beforeAdd'] = beforeAddF;
                args['customer_parvaz']['afterAdd'] = afterAddF;
		args['customer_parvaz']['beforeEdit'] = beforeEditF;
                args['customer_parvaz']['afterEdit'] = afterEditF;
		args['customer_parvaz']['beforeDelete'] = beforeDeleteF;
                args['customer_parvaz']['afterDelete'] = afterDeleteF;
*/
		intialGrid(args);
	});
</script>
<div align="center">
	<h2>
	اختصاص ذخیره جهت پرواز 
	<?php echo $parvaz->shomare." به تاریخ ".hamed_pdate($parvaz_det->tarikh)."<br/>".loadCust(); ?>
	
	</h2>
</div>
<div id="customer_parvaz_div" >
</div>


