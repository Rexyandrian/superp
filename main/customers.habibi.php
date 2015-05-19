<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$u = new user_class((int)$_SESSION[conf::app.'_user_id']);
	$isAdmin = ($u->user == 'mehrdad');
	function loadType()
	{
		$out = array();
		$out["دیگران"] = 3;
		$out["مشتری"] = 1;
		$out["مدیر"] = 2;
		return $out;
	}
	function loadNumbers($inp)
	{
		$out ="";
		$id = $inp;
		$cust = new customer_class($inp);
		$inp = $cust->ticket_numbers;
		//$inp = ((unserialize($inp))?unserialize($inp):array()) ;
		$j=0;
		foreach($inp as $i=>$shomare)
		{
			if($j==0)  
			{
				$out = $shomare ." تا ";
			}
			if($j==count($inp)-1)
			{
				$out .= $shomare ;
			}
			$j++;
		}
		$out = (($out=="")?"---":$out);
		$out1 = "<u><span style=\"cursor:pointer;color:firebrick;\" onclick=\"wopen('edit_ticket_nums.php?id=$id','',500,300);\" >$out</span></u>";
		return ($out1);
	}
        function add_item()
        {
                $fields = array();
                foreach($_REQUEST as $key => $value)
                {
                        if(strpos($key,"new_") === 0 && $key != "new_id" && $key != "new_en")
                        {
                                $fields[substr($key,4)] = $value;
                        }
                }
                $min_ticket = 0;
                mysql_class::ex_sql("select MAX(`max_ticket`) as `minticket` from `customers` where `en` = 1",$q);
                if($r = mysql_fetch_array($q))
                        $min_ticket = (int)$r["minticket"];
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
                $query = "insert into `customers` $fi values $va";
                mysql_class::ex_sqlx($query);
        }
	function sabtDariafti($inp)
	{
		$inp = (int)$inp;
		$out = "<u><span style=\"cursor:pointer;color:firebrick;\" onclick=\"wopen('daryaft.php?customer_id=$inp','',700,300);\" >ثبت دریافتی</span></u>";
		return ($out);
	}
	function delete_item($id)
	{
		$c = new customer_class($id);
		if(!$c->protected)
			mysql_class::ex_sqlx("update `customers` set `en` = '0' where `id` = '$id'");
	}
	function loadSms()
	{
		$out['ندارد'] = 0;
		$out['دارد'] = 1;
		return($out);
	}
	$grid = new jshowGrid_new("customers","grid1");
	$grid->whereClause = " `en` = 1 ";
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1]="نام شرکت";
	$grid->columnFilters[1] = TRUE;
	$grid->columnHeaders[2] = "نوع";
	$grid->columnLists[2] = loadType();
	$grid->columnFilters[2] = TRUE;
	$grid->columnHeaders[3] = "اعتبارمالی- ریال";
	$grid->columnFunctions[3] = "monize";
	$grid->columnHeaders[4] = null;
	$grid->columnHeaders[5] = null;
	$grid->columnHeaders[6] = null;
	//$grid->columnHeaders[6] = null;
	$grid->columnHeaders[7] = "شروع شماره<br>بلیت";
	$grid->columnAccesses[7] = 0;
	$grid->columnHeaders[8] = "پایان شماره<br>بلیت";
	$grid->columnAccesses[8] = 0;
	$grid->addFeild("id");
	$grid->columnHeaders[9] = null;
	$grid->columnHeaders[10] = 'کد';
	$grid->columnHeaders[11] = 'رمز<br />ETicket';
	$grid->columnHeaders[13] = (($isAdmin)?'Protected':null);
	$grid->columnLists[13] = (($isAdmin)?array("NO"=>0,"YES"=>1):null);
	if(conf::enableSms)
	{
		$grid->columnHeaders[12] = 'امکان ارسال پیام کوتاه';
		$grid->columnLists[12] = loadSms();
		$grid->columnHeaders[14] = "دریافتی";
		$grid->columnFunctions[14] = "sabtDariafti";
	}
	else
	{
		$grid->columnHeaders[12] = null;
		$grid->columnHeaders[14] = "دریافتی";
                $grid->columnFunctions[14] = "sabtDariafti";
	}
	$grid->addFunction = "add_item";
	$grid->deleteFunction = "delete_item";
	$grid->pageCount = 5;
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
		<script type="text/javascript" src="../js/tavanir.js"></script>
		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه مدیریت آژانس مسافرتی
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<?php echo $out;  ?>
		</div>
	</body>
</html>
