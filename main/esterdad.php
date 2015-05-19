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
	$isAdmin = ($u->typ == 1);
	$out = '';
	$mysql = new mysql_class();
	$shomare = 0;
	$sanad_record_id = 0;
	if(isset($_REQUEST["sanad_record_id"]))
	{
		$sanad_record_id = $_REQUEST["sanad_record_id"];
		if(isset($_REQUEST["shomare"]))
		{
			$shomare = $_REQUEST["shomare"];
		}
		$sanad_record_id = ticket_class::codeToRahgiri($sanad_record_id,$conf->rahgiri); 
		$ticket = null;
		$ticket_found = FALSE;
		$msg = '';
		$mysql->ex_sql("select `id` from `ticket` where `en`=1 and `sanad_record_id` = $sanad_record_id and `shomare` = $shomare",$q);
		$tickets = array();
		foreach($q as $r)
		{
			$tickets[] = (int)$r["id"];
			$ticket = new ticket_class((int)$r["id"]);
			$ticket_found = TRUE;
		}		
		$out ='';
		
		if($ticket_found)
		{
			$par = new parvaz_det_class($ticket->parvaz_det_id);
			$out ='<table style="width:90%" ><tr><th class="showgrid_header" >نام و نام خانوادگی </th>';
			$out .='<th class="showgrid_header" > شماره پرواز</th><th class="showgrid_header" > دومسیره</th></tr>';
			$out.='<tr class="showgrid_row_even" ><td><input type="checkbox" name="shomare_ticket" checked="checked" class="esterdad" value="'.$par->shomare.'"/></td><td>'.$ticket->lname."</td><td>".$par->shomare."</td><td>".((count($tickets)>1)?"دوطرفه":"معمولی").'</td></tr></table><br/>';
			$out.="\n<input class=\"inp esterdad\" type=\"hidden\" name=\"ticket_id\" id=\"ticket_id\" value=\"".implode(",",$tickets)."\" readonly=\"readonly\"/>\n";
			
			if($isAdmin)
			{
				$out.= "<input class=\"inp esterdad\" type=\"text\" name=\"jarime\" value=\"0\" />\n";
				$out.= "جریمه به روش معمول محاسبه شود : <input type=\"checkbox\" name=\"standard\" checked=\"checked\" class=\"esterdad\"/>";
				$out.= "بلیط کاملاً پاک شود : <input class=\"esterdad\" type=\"checkbox\" name=\"deleteTicket\" />";
			}
			else
				$out.= "<input type=\"hidden\" name=\"jarime\" value=\"-1\" class=\"inp esterdad\" />\n";
			$out.= "<input class=\"inp\" type=\"button\" onclick=\"send_esterdad();\" value=\"استرداد\" />\n";
		}	
		die($out);
	}
	$msg = "";
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
					//	$msg = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('بلیت با موفقیت پاک گردید');window.parent.location = window.parent.location;</script></body></html>";
						$msg = 'بلیت با موفقیت پاک گردید';
					else
						//$msg = "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('تاریخ پرواز بلیت گذشته و امکان پاک کردن آن نیست');window.parent.location = window.parent.location;</script></body></html>";
						$msg = 'تاریخ پرواز بلیت گذشته و امکان پاک کردن آن نیست';
				}
			}
			if($jarime < 100)
			{
				//die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('عملیات استرداد موفقیت آمیز بود');window.parent.location = window.parent.location;</script></body></html>");
				$msg_jarime = 'عملیات استرداد موفقیت آمیز بود';
			}
			else
			{
				//die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('عملیات استرداد ناموفق بود');window.parent.location = window.parent.location;</script></body></html>");
				$msg_jarime = 'عملیات استرداد ناموفق بود';
			}
			//if($msg !='')
				$total_msg = $msg."<br/>".$msg_jarime;
				die($total_msg);
		}	
?>
<script>
function send_ok()
	{
		
		var inps = {};
		var tmp,val,nam;
		$.each($(".tagInfo"),function (id,field){
			val = $(field).val();
			nam = $(field).prop("name");
			inps[nam] = val;
		});
		tmp = $.param(inps);
		$.get("esterdad.php?"+tmp+"&r="+Math.random(),function(result){
			result = trim(result);
			if(result!='')
				$("#natije_search").html(result);
			else
				alert('کد رهگیری یا شماره بلیط اشتباه وارد شده است');
			//closeDialog();
		});
		
	}
function send_esterdad()
	{
		
		var inps = {};
		var tmp,val,nam;
		$.each($(".esterdad"),function (id,field){
			if(($(field).prop("type")=="checkbox") && ($(field).prop("checked")) || ($(field).prop("type")!="checkbox"))
			{				
				val = $(field).val();
				nam = $(field).prop("name");
				inps[nam] = val;
			}
		});
		tmp = $.param(inps);	
		$.get("esterdad.php?"+tmp+"&r="+Math.random(),function(result){
			result = trim(result);
			if(result!='')
				$("#natije_esterdad").html(result);
			else
				alert('کد رهگیری یا شماره بلیط اشتباه وارد شده است');
			//closeDialog();
		});
		
	}
</script>
<div align="center">
	<br/>
	<br/>
		رفرنس : 
		<input class="inp tagInfo" type="text" id="sanad_record_id" name="sanad_record_id" value="<?php echo ((isset($_REQUEST['sanad_record_id']))?$_REQUEST['sanad_record_id']:0); ?>" />
شماره بلیط :
		<input type="text" class="inp tagInfo" id="shomare" name="shomare" value="<?php echo $shomare; ?>" />
		<input type="button" onclick="send_ok();" value="جستجو" class="inp" />
	<?php
		//echo $out;
	?>
	<div id="natije_search">
	</div>
	<div id="natije_esterdad">
	</div>
</div>
