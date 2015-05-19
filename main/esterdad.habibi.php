<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$out = '';
	if (isset($_SESSION[conf::app.'_user_id']) && isset($_SESSION[conf::app.'_typ']))
	{
	}
	else
	{
			die("<center><h1>شما به این صفحه دسترسی ندارید</h1></center>");
	}
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
                mysql_class::ex_sql("select MAX(`max_ticket`) as `minticket` from `customers`",$q);
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
		mysql_class::ex_sqlx("update `customers` set `en` = '0' where `id` = '$id'");
	}
	function loadParvazDate($parvaz_det_id)
	{
		$parvaz_det_id = perToEnNums($parvaz_det_id);
		$out = jdate("d / m / Y",strtotime($parvaz_det_id));
		return($out." 00:00:00");
	}
	function loadParvazDateBack($pdate)
	{
		$pdate = str_replace(" ","",$pdate);
		$out = hamed_pdateBack($pdate);
		return($out);
	}
	$sanad_record_id = ((isset($_REQUEST["sanad_record_id"]))?$_REQUEST["sanad_record_id"]:0);
	$sanad_record_id = ticket_class::codeToRahgiri($sanad_record_id,conf::rahgiri); 
	$shomare = ((isset($_REQUEST["shomare"]))?(int)$_REQUEST["shomare"]:0);
	$ticket = null;
	$ticket_found = FALSE;
	$msg = '';
	mysql_class::ex_sql("select `id` from `ticket` where `en`=1 and `sanad_record_id` = $sanad_record_id and `shomare` = $shomare",$q);
	$tickets = array();
	while($r = mysql_fetch_array($q))
	{
		$tickets[] = (int)$r["id"];
		$ticket = new ticket_class((int)$r["id"]);
		$ticket_found = TRUE;
	}
	if(isset($_REQUEST["ticket_id"]))
	{
		$tickets= explode(",",$_REQUEST["ticket_id"]);
		$jarime = (int)$_REQUEST["jarime"];
		if(isset($_REQUEST["standard"]))
			$jarime = -1;
		for($i = 0;$i < count($tickets);$i++)
		{
			$ticket = new ticket_class((int)$tickets[$i]);
			if($jarime == -1)
			{
				$parvaz = new parvaz_det_class($ticket->parvaz_det_id);
				$today = strtotime(date("Y-m-d H:i:s"));
				$p_date = strtotime($parvaz->tarikh);
				$hours = round(($p_date-$today)/60/60);
				if($hours >= 240)
					$jarime = 10;
				else if($hours >= 72)
					$jarime = 30;
				else if($hours >= 48)
					$jarime = 50;
				else
					$jarime = 100;
			}
			if($jarime < 100 && !(isset($_REQUEST["deleteTicket"])))
				customer_class::esterdad((int)$tickets[$i],$jarime);
			else if(isset($_REQUEST["deleteTicket"]))
			{
				if(customer_class::deleteTicket((int)$tickets[$i]))
					$msg = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('بلیت با موفقیت پاک گردید');window.parent.location = window.parent.location;</script></body></html>";
				else
					$msg = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('تاریخ پرواز بلیت گذشته و امکان پاک کردن آن نیست');window.parent.location = window.parent.location;</script></body></html>";
			}
		}
		if($jarime < 100)
		{
			die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('عملیات استرداد موفقیت آمیز بود');window.parent.location = window.parent.location;</script></body></html>");
		}
		else
		{
			die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('عملیات استرداد ناموفق بود');window.parent.location = window.parent.location;</script></body></html>");
		}
		if($msg !='')
			die($msg);
	}
	$out ='';
	if($ticket_found)
	{
		$par = new parvaz_det_class($ticket->parvaz_det_id);
		$out ='<table style="width:90%" ><tr><th class="showgrid_header" >نام و نام خانوادگی </th>';
		$out .='<th class="showgrid_header" > شماره پرواز</th><th class="showgrid_header" > دومسیره</th></tr>';
		$out.='<tr class="showgrid_row_even" ><td>'.$ticket->lname."</td><td>".$par->shomare."</td><td>".((count($tickets)>1)?"دوطرفه":"معمولی").'</td></tr></table><br/>';
		$out.="<form id=\"est\">\n<input class=\"inp\" type=\"hidden\" name=\"ticket_id\" value=\"".implode(",",$tickets)."\" readonly=\"readonly\"/>\n";
		if((int)$_SESSION[conf::app."_customer_typ"]==2 && (int)$_SESSION[conf::app."_typ"]==0)
		{
			$out.= "<input class=\"inp\" type=\"text\" name=\"jarime\" value=\"0\" />\n";
			$out.= "جریمه به روش معمول محاسبه شود : <input type=\"checkbox\" name=\"standard\" checked=\"checked\"/>";
			$out.= "بلیط کاملاً پاک شود : <input type=\"checkbox\" name=\"deleteTicket\" />";
		}
		else
			$out.= "<input type=\"hidden\" name=\"jarime\" value=\"-1\" class=\"inp\" />\n";
		$out.= "<input class=\"inp\" type=\"submit\" value=\"استرداد\" />\n</form>\n";
	}
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
			<form id="frm" method="get">
				کد رهگیری : 
<input class="inp" type="text" id="sanad_record_id" name="sanad_record_id" value="<?php echo ((isset($_REQUEST['sanad_record_id']))?$_REQUEST['sanad_record_id']:0); ?>" />
				شماره بلیط : <input type="text" class="inp" id="shomare" name="shomare" value="<?php echo $shomare; ?>" />
				<input type="submit" value="جستجو" class="inp" />
			</form>
			<?php
				//echo $out;
				//customer_class::esterdad(29,0); 
				echo $out;
			?>
		</div>
	</body>
</html>
