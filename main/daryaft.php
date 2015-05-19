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
	$GLOBALS['se'] = $se;
	function loadCustomers($cust = -1)
	{
		$mysql = new mysql_class;
		$cust = (int)$cust;
		$out = null;
		$mysql->ex_sql("select * from `customers` where ".(($cust>0)?"`id`='$cust' and":"")." `en` = 1  order by `name`",$q);
		foreach($q as $r)
		{
			$out[(int)$r["id"]] = $r["name"];
		}
		return($out);
	}
	
	function add_item($gname,$table,$fields,$col)
	{
		$typ = $GLOBALS["typ"];
		$conf = new conf;
		$mysql = new mysql_class;
		foreach($fields as $key => $value)
                {
			if(strpos($key,"new_") === 0 && $key != "new_id" && $key != "new_en")
                                $fields[substr($key,4)] = $value;
                }
		$customer = new customer_class((int)$fields["customer_id"]);
		$customer->daryaft((int)umonize($fields["mablagh"]),(int)$_SESSION[$conf->app."_user_id"],$fields["tozihat"],$typ,hamed_pdateBack($fields["tarikh"]));
		return $ret;
	}
	function delete_item($table,$id,$gname)
	{
		$mysql = new mysql_class;
		$out = FALSE;
		$id = (int)$id;
		$amount = 0;
		$customer_id = -1;
		$mysql->ex_sql("select `mablagh`,`customer_id` from `$table` where `id` = '$id'",$q);
		if(isset($q[0]))
		{
			$amount = (int)$q[0]["mablagh"];
			$customer_id = (int)$q[0]["customer_id"];
		}
		$out = ($mysql->ex_sqlx("delete from `$table` where `id` = '$id'")=="ok");
		$mysql->ex_sqlx("update `$table` set `max_amount`=`max_amount`-$amount where `id` = '$customer_id'");		
		return($out);
	}	
	function hamed_pdate($str)
        {
                $out=jdate('d / m / Y',strtotime($str));
		$out .= "<br/>".date('F d',strtotime($str));
                return enToPerNums($out);
        }
	function loadVaziat($id)
	{
		$mysql = new mysql_class;
		$out = "&nbsp;";
		$typ = 0;
		$id = (int)$id;
		$sanad_typ = 0;
		$mysql->ex_sql("select `typ`,`sanad_typ` from `customer_daryaft` where `id`='$id'",$q);
		if(isset($q[0]))
		{
			$typ =(int) $q[0]['typ'];
			$sanad_typ = (int) $q[0]['sanad_typ'];
		}
		$se = $GLOBALS['se'];
		if(!$se->detailAuth('all'))
		{
			if($typ == 0)
				$out = "<span style=\"color:firebrick;\">تایید نشده</span>";
			else if($typ == 1)
				$out = "<span style=\"color:green;\">تایید شده</span>";
		}
		else
		{
			$customer_id = ((isset($_REQUEST["customer_id"]))?(int)$_REQUEST["customer_id"]:-1);
			if($typ == 0)
				$out = "<u><span style=\"color:firebrick;cursor:pointer;\" onclick=\"window.location ='daryaft.php?customer_id=$customer_id&changetyp=$id&typ=1&'\">تایید نشده</span></u>";
			else if($typ == 1)
				$out = "<u><span style=\"color:green;cursor:pointer;\" onclick=\"window.location ='daryaft.php?customer_id=$customer_id&changetyp=$id&typ=0&'\">تایید شده</span></u>";
		}
		return($out);
	}
	function loadTozihat($inp)
	{
		$addad = (int)$inp;
		if("$addad" == $inp)
			$out = "&nbsp;";
		else
			$out = $inp;
		return($out);
	}
	function loadTozihatBack($inp)
	{
		return($inp);
	}
	function edit_item($id,$feild,$value)
	{
		$mysql = new mysql_class;
		if($feild == "id")
		{
			$mysql->ex_sql("select * from `customer_daryaft` where `id` = $id",$q);
			if(isset($q[0]))
			{
				if((int)$q[0]["sanad_typ"]!=-1)
				{
					echo ("update `customer_daryaft` set `tozihat` = '$value' where `id` = $id");
					$mysql->ex_sqlx("update `customer_daryaft` set `tozihat` = '$value' where `id` = $id");
				}
			}
		}
		else if($feild == "mablagh")
		{
			$mysql->ex_sqlx("update `customer_daryaft` set `mablagh` = '".umonize($value)."' where `id` = $id");
		}
		else
			$mysql->ex_sqlx("update `customer_daryaft` set `$feild` = '$value' where `id` = $id");
	}
	function loadEster($inp)
	{
		$inp = (int)$inp;
		$out = "&nbsp;";
		$mysql->ex_sql("select * from `customer_daryaft` where `id` = $inp",$q);
		if(isset($q[0]))
		{
			$sanad_typ = (int)$q[0]["sanad_typ"];
			$typ = (int)$q[0]["typ"];
			$tozihat = (int)$q[0]["tozihat"];
			if($sanad_typ == -1)
			{
				$ti = new ticket_class($tozihat);
				$out = "استردادی ".$ti->shomare;
			}
		}
		return($out);
	}
	$customer_id = ((isset($_REQUEST["customer_id"]))?(int)$_REQUEST["customer_id"]:-1);
	$typ = 1;
	$customer_id = -1;
	if(!isset($_REQUEST["customer_id"]) && (int)$_SESSION[$conf->app."_customer_typ"] != 2)
	{
		$customer_id = (int)$_SESSION[$conf->app."_customer_id"];
		$typ = 0;
	}
	if(isset($_REQUEST["changetyp"]) && (int)$_SESSION[$conf->app."_customer_typ"] == 2)
	{
		$changetyp = (int)$_REQUEST["changetyp"];
		$typ = (int)$_REQUEST["typ"];
		$mysql->ex_sqlx("update `customer_daryaft` set `typ`=$typ where `id` =$changetyp");
	}
	$GLOBALS["typ"] = $typ;
	$gname = 'grid_customer_daryaft';
	$input =array($gname=>array('table'=>'customer_daryaft','div'=>'main_div_customer_daryaft'));
	$xgrid = new xgrid($input);
	/*$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='مشتری(آژانس)';
	$xgrid->column[$gname][1]['clist'] = loadCustomers($customer_id);
	$xgrid->column[$gname][1]['access'] = 'a';
	$xgrid->column[$gname][2]['name'] ='';
	$xgrid->column[$gname][3]['name'] ='مبلغ';
	$xgrid->column[$gname][3]['cfunction'] = array('monize');
	$xgrid->column[$gname][4]['name'] ='تاریخ';
	$xgrid->column[$gname][4]['cfunction'] = array('hamed_pdate','hamed_pdateBack');
	$xgrid->column[$gname][4]['access'] = 'a';
	$xgrid->column[$gname][5]['name'] ='توضیحات';
	$xgrid->column[$gname][5]['cfunction'] = array('loadTozihat');
	$xgrid->column[$gname][5]['access'] = 'a';
	$xgrid->column[$gname][6]['name'] ='شماره سند';
	$xgrid->column[$gname][6]['access'] = 'a';
	$xgrid->column[$gname][7]['name'] ='مبلغ';
	$xgrid->column[$gname][8]['name'] ='';
	$xgrid->column[$gname][] =array('name'=>'وضعیت پرداختی','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadVaziat'));
	$xgrid->column[$gname][] =array('name'=>'استردادی','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadEster'));
	$ww = " `typ`<>-1 ";
	if(isset($_REQUEST["customer_id"]) || (int)$_SESSION[$conf->app."_customer_typ"] != 2)
	{
		$ww .= " and `customer_id` = $customer_id ";
		$xgrid->canEdit = FALSE;
		$xgrid->canDelete = FALSE;
	}
	$xgrid->whereClause[$gname] = $ww;
	$xgrid->addFunction[$gname] ='add_item';
	$xgrid->canAdd[$gname] = TRUE;	*/
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
<div align="center">
	<br/>
	<br/>
	<?php echo "<h2>ثبت وجه دریافتی از ".(($customer_id == -1)?"مشتریان":$customer->name)."</h2>";
		echo "<br/>"; ?>
</div>
<div id="main_div_customer_daryaft" style="overflow:auto;border:1px dotted #bbb;paddin:5px;" >

</div>
