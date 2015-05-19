<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$GLOBALS['se'] = $se;
	function loadCustomers($cust = -1)
	{
		$cust = (int)$cust;
		$out = null;
		mysql_class::ex_sql("select * from `customers` where ".(($cust>0)?"`id`='$cust' and":"")." `en` = 1  order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$out[$r["name"]] = (int)$r["id"];
		}
		return($out);
	}
	function add_item()
	{
		$typ = $GLOBALS["typ"];
                $fields = array();
                foreach($_REQUEST as $key => $value)
                {
                        if(strpos($key,"new_") === 0 && $key != "new_id" && $key != "new_en")
                        {
                                $fields[substr($key,4)] = $value;
                        }
                }
		$customer = new customer_class((int)$fields["customer_id"]);
		$customer->daryaft((int)umonize($fields["mablagh"]),(int)$_SESSION[conf::app."_user_id"],$fields["tozihat"],$typ,hamed_pdateBack($fields["tarikh"]));
		
	}
	function delete_item($id)
	{
		$amount = 0;
		$customer_id = -1;
		mysql_class::ex_sql("select `mablagh`,`customer_id` from `customer_daryaft` where `id` = '$id'",$q);
		if($r = mysql_fetch_array($q))
		{
			$amount = (int)$r["mablagh"];
			$customer_id = (int)$r["customer_id"];
		}
		mysql_class::ex_sqlx("delete from `customer_daryaft` where `id` = '$id'");
		mysql_class::ex_sqlx("update `customers` set `max_amount`=`max_amount`-$amount where `id` = '$customer_id'");
	}
	function hamed_pdate($str)
        {
                $out=jdate('d / m / Y',strtotime($str));
		$out .= "<br/>".date('F d',strtotime($str));
                return enToPerNums($out);
        }
	function loadVaziat($id)
	{
		$out = "&nbsp;";
		$typ = 0;
		$id = (int)$id;
		$sanad_typ = 0;
		mysql_class::ex_sql("select `typ`,`sanad_typ` from `customer_daryaft` where `id`='$id'",$q);
		if($r=mysql_fetch_array($q))
		{
			$typ =(int) $r['typ'];
			$sanad_typ = (int) $r['sanad_typ'];
		}
//		if((int)$_SESSION[conf::app.'_customer_typ']!=2 || $sanad_typ == -1)
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
		if($feild == "id")
		{
			mysql_class::ex_sql("select * from `customer_daryaft` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				if((int)$r["sanad_typ"]!=-1)
				{
					echo ("update `customer_daryaft` set `tozihat` = '$value' where `id` = $id");
					mysql_class::ex_sqlx("update `customer_daryaft` set `tozihat` = '$value' where `id` = $id");
				}
			}
		}
		else if($feild == "mablagh")
		{
			mysql_class::ex_sqlx("update `customer_daryaft` set `mablagh` = '".umonize($value)."' where `id` = $id");
		}
		else
			mysql_class::ex_sqlx("update `customer_daryaft` set `$feild` = '$value' where `id` = $id");
	}
	function loadEster($inp)
	{
		$inp = (int)$inp;
		$out = "&nbsp;";
		mysql_class::ex_sql("select * from `customer_daryaft` where `id` = $inp",$q);
		if($r = mysql_fetch_array($q))
		{
			$sanad_typ = (int)$r["sanad_typ"];
			$typ = (int)$r["typ"];
			$tozihat = (int)$r["tozihat"];
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
	if(!isset($_REQUEST["customer_id"]) && (int)$_SESSION[conf::app."_customer_typ"] != 2)
	{
		$customer_id = (int)$_SESSION[conf::app."_customer_id"];
		$typ = 0;
	}
	if(isset($_REQUEST["changetyp"]) && (int)$_SESSION[conf::app."_customer_typ"] == 2)
	{
		$changetyp = (int)$_REQUEST["changetyp"];
		$typ = (int)$_REQUEST["typ"];
		mysql_class::ex_sqlx("update `customer_daryaft` set `typ`=$typ where `id` =$changetyp");
	}
	$GLOBALS["typ"] = $typ;
	$customer = new customer_class($customer_id);
	$grid = new jshowGrid_new("customer_daryaft","grid1");
	$grid->width='99%';
	$grid->index_width='20px';
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = "مشتری(آژانس)";
	//$grid->columnFilters[1] = TRUE;
	$grid->columnHeaders[2] = null;
	$grid->columnHeaders[3] = "مبلغ";
	$grid->columnHeaders[4] = "تاریخ";
	$grid->columnHeaders[5] = "توضیحات";
	$grid->columnHeaders[6] = 'شماره سند';
	$grid->columnHeaders[7] = null;
	$grid->columnHeaders[8] = null;
	$grid->columnAccesses[4] = 0;
	$grid->columnAccesses[6] = 0;
	$grid->columnJavaScript[3] = " onkeyup=\"monize(this);\" ";
	$grid->addFeild('id',9);
	$grid->columnHeaders[9] = "وضعیت پرداختی";
	$grid->columnFunctions[9] = "loadVaziat";
	$grid->columnLists[1] = loadCustomers($customer_id);
	$grid->columnFunctions[3] = "monize";
	$grid->columnFunctions[4] = "hamed_pdate";
	$grid->columnCallBackFunctions[4] = "hamed_pdateBack";
	$grid->columnAccesses[1] = 0;
	$grid->columnFunctions[5] = "loadTozihat";
	$grid->columnAccesses[5] = 0;
	$grid->addFeild('id',6);
	$grid->columnHeaders[6] = "استردادی";
	$grid->columnFunctions[6] = "loadEster";
	$grid->addFunction = "add_item";
	$grid->deleteFunction = "delete_item";
//	$grid->editFunction = "edit_item";

	$ww = " `typ`<>-1 ";
	if(isset($_REQUEST["customer_id"]) || (int)$_SESSION[conf::app."_customer_typ"] != 2)
	{
		$ww .= " and `customer_id` = $customer_id ";
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
	}
	$grid->whereClause = $ww;
/*
	if((int)$_SESSION[conf::app."_customer_typ"] != 2)
	{
		$grid->canEdit = FALSE;
		$grid->canDelete = FALSE;
	}
*/
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه مدیریت آژانس مسافرتی
		</title>
		<script language="javascript">
			function setAll(obj)
			{
				alert('hello');
				var inps = document.getElementsByTagName('input');
				for(var i = 0; i<inps.length;i++)
				{
					if(inps[i].id == id)
					{
						alert(inps[i].id+" = "+inps[i].value);
						inps[i].value = value;
					}
				}
			}	
		</script>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<?php echo "<h2>ثبت وجه دریافتی از ".(($customer_id == -1)?"مشتریان":$customer->name)."</h2>";
				echo "<br/>";
				echo $out;  ?>
		</div>
		<script language="javascript">
			document.getElementById('new_sanad_record_id').style.display = 'none';
                        var ids = document.getElementsByName("new_id");
			for(var i=0;i<ids.length;i++)
				ids[i].style.display="none";
		</script>
	</body>
</html>
