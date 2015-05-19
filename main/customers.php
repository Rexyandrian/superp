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

	$isAdmin = ($u->user == 'mehrdad');
	function sabtDariafti($inp)
	{
		$inp = (int)$inp;		
		$out = "<u><span style=\"cursor:pointer;color:firebrick;\" onclick=\"openDialog('daryaft.php?customer_id=$inp','ثبت دریافتی');\" >ثبت دریافتی</span></u>";
		return ($out);
	}
	function add_item($gname,$table,$fields,$col)
	{
		$conf = new conf;
		$mysql = new mysql_class;
		function fieldToId($col,$fieldName)
		{
			$out = -1;
			foreach($col as $id=>$f)
			{
				if(strpos($id,"new_") === 0 && $id != "new_id" && $id != "new_en")
	                                $fields[substr($id,4)] = $f;       
			}
		}
		$min_ticket = 0;
                $mysql->ex_sql("select MAX(`max_ticket`) as `minticket` from `customers` where `en` = 1",$q);
                if(isset($q[0]))
                        $min_ticket = (int)$q[0]["minticket"];
                $min_ticket++;
		$max_ticket = 0;
		$max_ticket = $min_ticket + 999;
		$fields["min_ticket"] = $min_ticket;
		$fields["max_ticket"] = $max_ticket;
                $fi = "(";
                $va = "(";
		foreach($fields as $key => $value)
                {
                        $fi .= "`$key`,";
                        $va .= "'$value',";
                }
                $fi = substr($fi,0,-1);
                $va = substr($va,0,-1);
                $fi .= ")";
                $va .= ")";
                $query="insert into `$table` $fi values $va";
		$ln = $mysql->ex_sqlx($query,FALSE);
		$out = $mysql->insert_id($ln);
		$mysql->close($ln);
		$ret =($out>0)?TRUE:FALSE;
		return $ret;
	}
	function incTicket($id)
	{
		$out = '';
		$id =(int)$id;
		$cust = new customer_class($id);
		if(isset($cust->id) && (int)$cust->id > 0 && $cust->max_ticket == $cust->min_ticket)
			$out = "<input type=\"text\" id=\"cust_count_$id\" /><button onclick=\"incTicketNums($id,\$('#cust_count_$id').val());\">افزایش</button>";
		return($out);
	}
	function delete_item($table,$id,$gname)
	{
		$id = (int)$id;
		$c = new customer_class($id);
		$mysql = new mysql_class;
		$out = FALSE;
		if(!$c->protected)
			$out = ($mysql->ex_sqlx("update `$table` set `en` = '0' where `id` = '$id'")=="ok");
		return($out);
	}
	if(isset($_REQUEST['cust_id']))
	{
		$out = 'true';
		$cust_id = (int)$_REQUEST['cust_id'];
		$co = (isset($_REQUEST['co']) && (int)$_REQUEST['co']>0)?(int)$_REQUEST['co']:1000;
		$cust = new customer_class($cust_id);
		$cust->incTicketNums($co);
		die($out);
	}
	$typ = array();
	$typ["1"] = "اعتباری";
	$typ["2"] = "صندوق";
	$typ["3"] = "نقدی";
	$gname = 'grid_customers';
	$input =array($gname=>array('table'=>'customers','div'=>'main_div_customers'));
	$xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] = " `en` = 1 ";
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='نام شرکت';
	$xgrid->column[$gname][2]['name'] ='';
	//$xgrid->column[$gname][2]['name'] ='نوع';
	//$xgrid->column[$gname][2]['clist'] = loadType();
	$xgrid->column[$gname][3]['name'] ='اعتبارمالی- ریال';
	//$xgrid->column[$gname][3]['cfunction'] = array('monize');
	$xgrid->column[$gname][4]['name'] ='';
	$xgrid->column[$gname][5]['name'] ='';
	$xgrid->column[$gname][6]['name'] ='';
	$xgrid->column[$gname][7]['name'] ='شروع شماره<br>بلیت';
	$xgrid->column[$gname][7]['access'] = 'a';
	$xgrid->column[$gname][8]['name'] ='پایان شماره<br>بلیت';
	$xgrid->column[$gname][8]['access'] = 'a';
	$xgrid->column[$gname][9]['name'] ='';
	$xgrid->column[$gname][10]['name'] ='کد';
	$xgrid->column[$gname][11]['name'] ='رمز<br />ETicket';
	$xgrid->column[$gname][13]['name'] =(($isAdmin)?'Protected':'');
	$xgrid->column[$gname][13]['clist'] = (($isAdmin)?array(0=>"NO",1=>"YES"):null);
	//$xgrid->column[$gname][] =array('name'=>'دریافتی','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('sabtDariafti'));
	$xgrid->column[$gname][] =array('name'=>'افزایش بلیت','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('incTicket'));
	if($conf->enableSms)

		$xgrid->column[$gname][12]['name'] = 'امکان ارسال پیام کوتاه';
	else
		$xgrid->column[$gname][12]['name'] = '';
	$xgrid->addFunction[$gname] ='add_item';
	$xgrid->deleteFunction[$gname] ='delete_item';
	$xgrid->canAdd[$gname] = TRUE;
	$xgrid->canEdit[$gname] = TRUE;
	$xgrid->canDelete[$gname] = TRUE;	
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);
?>
<script type="text/javascript" >
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		intialGrid(args);
	});
	function incTicketNums(cust_id,co)
	{
		$.get("customers.php?cust_id="+cust_id+"&co="+co+"&r="+Math.random()+"&",function(result){
			if(result == 'true')
			{
				alert('بلیط با موفقیت افزایش یافت');
				var ggname ='<?php echo $gname; ?>';
				grid[ggname].init(gArgs[ggname]);
			}
			else
				alert('خطا در بروزرسانی');
		});
	}
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
