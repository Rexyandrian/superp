<?php   
	include_once("../kernel.php");
	include_once("../class/nusoap.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
	//include_once ("../class/nusoap.php");
	$user_id = $_SESSION[$conf->app.'_user_id'];
	function flightZarfiat($parvaz)
	{
		$conf = new conf;
		$out = 0;
		$se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
		$isAdmin = $se->detailAuth('all');
		if(!$isAdmin && $parvaz->getZarfiat($_SESSION[$conf->app.'_customer_id'])>=9)
			$out = 9;
		else if(!$isAdmin)
			$out = $parvaz->getZarfiat($_SESSION[$conf->app.'_customer_id']);
		else if($isAdmin)
			$out = $parvaz->getZarfiat();
		return($out);
	}
	function bargashtHast($selectedParvaz,$parvaz)
	{
                $out = TRUE;
                $jids = $parvaz->loadJid();
                if($parvaz->j_id >0 && $jids==null)
                {
                        $out = FALSE;
                        foreach($selectedParvaz as $tmp)
                                if($tmp->mabda_id == $parvaz->maghsad_id && $parvaz->mabda_id = $tmp->maghsad_id)
                                        $out = TRUE;
                }
                else if($parvaz->j_id >0 && $jids!=null)
                {
                        $out = FALSE;
                        foreach($selectedParvaz as $tmp)
                                for($i = 0;$i < count($jids);$i++)
                                        if($jids[$i] == $tmp->getId())
                                                $out = TRUE;
                }
                return($out);
	}
	function loadCity($inp)
        {
		$mysql = new mysql_class;
                $inp = (int)$inp;
                $out = "";
                $mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
                if(isset($q[0]))
                {
                        $out = $q[0]["name"];
                }
                return($out);
        }
	function loadSherkatName($inp)
        {
		$mysql = new mysql_class;
                $inp = (int)$inp;
                $out = "";
                $mysql->ex_sql("select `name` from `sherkat` where `id` = '$inp'",$q);
                if(isset($q[0]))
                {
                        $out = $q[0]["name"];
                }
                return($out);
        }
	function loadShomare($inp)
        {
                $inp = (int)$inp;
                $out = "";
                $par = new parvaz_det_class($inp);
                return(enToPerNums($par->shomare));
        }
	function hamed_pdate($str)
        {
                $out=jdate('l Y/n/j',strtotime($str));
                return enToPerNums($out);
        }
	function saat($inp)
        {
                $inp = substr($inp,0,-3);
                return enToPerNums($inp);
        }
	function poorsant($inp)
        {
		$conf = new conf;
                $par = new parvaz_det_class((int)$inp);
                $customer_id = $_SESSION[$conf->app."_customer_id"];
                $cust = new customer_class($customer_id);
                $out = ($cust->getPoorsant($inp)* ($par->ghimat) /100 );
                return enToPerNums(monize($out));
        }
	function loadMabda($inp)
        {
                $inp = (int)$inp;
                $out = "";
                $par = new parvaz_det_class($inp);
                return(loadCity($par->mabda_id));
        }
        function loadMaghsad($inp)
        {
                $inp = (int)$inp;
                $out = "";
                $par = new parvaz_det_class($inp);
                return(loadCity($par->maghsad_id));
        }

	function loadSherkat($inp)
        {
                $inp = (int)$inp;
                $out = "";
                $par = new parvaz_det_class($inp);
                return(loadSherkatName($par->sherkat_id));
        }
	$msg = "";
	$info_ticket = array();
	$redirect = '';
        $adl = abs((int)$_REQUEST["adl"]);
        $chd = abs((int)$_REQUEST["chd"]);
        $inf = abs((int)$_REQUEST["inf"]);
	$tedad = $adl + $chd;
        $ticket_type = (int)$_REQUEST["ticket_type"];
	$kharid_typ = ((isset($_REQUEST['kharid_typ']))?$_REQUEST['kharid_typ']:'');
	$selected_parvaz = $_REQUEST["selected_parvaz"];
	$tmp = explode(",",$selected_parvaz);
	foreach($tmp as $parvaz_id)
        {
        	$tmp_parvaz = new parvaz_det_class((int)$parvaz_id);
                $selectedParvaz[] = $tmp_parvaz;
        }
	$customer = new customer_class((int)$_SESSION[$conf->app."_customer_id"]);

	$out = "";
	$adults = "";
	$childs = "";
	$infants = "";
	$tmp_id = array();
	if($_REQUEST["mod"] == "save" )
	{
		$empty_tickets = 0;
		$tmp_id = explode(",",$_REQUEST["tmp_id"]);
		$mysql  =new mysql_class;
		$mysql->ex_sql("select `id` from `reserve_tmp` where `id` = ".$tmp_id[0],$qqq);
		if(!(isset($qqq[0])))
			die('<script>alert("reserve_tmp is died");</script>');
                $sanad_record_id = 200;
		$mysql->ex_sql("select MAX(`sanad_record_id`) as `sss` from `customer_daryaft`",$q);
		if(isset($q[0]))
		{
			$sanad_record_id = (((int)$q[0]["sss"]>199)?(int)$q[0]["sss"]:199);
			$sanad_record_id ++;
		}
		//$ticket->clearTickets();
		$ok = TRUE;
		$jam_ghimat1 = 0;
		$index = 0;
		$domasire_ast = FALSE;
		if(count($selectedParvaz) == 2 && parvaz_det_class::check_raft_bargasht($selectedParvaz[0]->getId(),$selectedParvaz[1]->getId()))
			$domasire_ast = TRUE;
		$p_i = 0;
		$adl_last = array();
		$chd_last = array();
		$inf_last = array();
		$ghimat_kharid = 0;
		foreach($selectedParvaz as $parvaz)
		{
			$zarib = (100 - $customer->getPoorsant($parvaz->getId()))/100;
			for($i = 0;$i < $adl;$i++)
			{
				$ticket = new ticket_class;
				//$ticket->fname = $_REQUEST["adl_fname_$i"];
				$ticket->lname = $_REQUEST["adl_lname_$i"];
				$ticket->tel = ((isset($_REQUEST["adl_tel_$i"]))?$_REQUEST["adl_tel_$i"]:'');
				$ticket->adult = 0;						
				$ticket->parvaz_det_id = $parvaz->getId();
				$ticket->mablagh = $parvaz->ghimat*$zarib;
				$ticket->poorsant = $customer->getPoorsant($parvaz->getId());
				$ticket->customer_id = $customer->getId();
				$ticket->user_id = (int)$_SESSION[$conf->app."_user_id"];
				$ticket->typ = $ticket_type;
				$ticket->gender = $_REQUEST["adl_gender_$i"];
				if($ticket->lname != "")
					$empty_tickets++;
				$ticket->en = 1;
				$ticket->email_addr = trim($_REQUEST["email_addr"]);
				$ticket->sites_id = (int)$_REQUEST["sites_id"];
				$ticket->sanad_record_id = $sanad_record_id;				
				$j = 0;
				$shomare = -1;
				if(isset($_REQUEST["adl_shomare_$i"]))
					$shomare = (int)$_REQUEST["adl_shomare_$i"];
                                //if($customer->ticketNumberExists($shomare)>-1 && $ticket_type == 1)
                                if($ticket_type == 1)
                                {
                                        $ticket->shomare = $shomare;
                                        $customer->deleteTicketNumber($shomare);
					if($kharid_typ == 'etebari')
                                        	$ok = $ok and $ticket->add($tmp_id[$index],$noth);
					else
						$info_ticket[] = $ticket;
					$ghimat_kharid += $parvaz->mablagh_kharid;
                                }
                                else if($ticket_type == 0 )
                                {
					if($p_i == 0)
					{
						$ticket->shomare = $customer->decTicketCount();
						$adl_last[$i] = $ticket->shomare;
					}
					else if($domasire_ast)
					{
						 $ticket->shomare = $adl_last[$i];
					}
                                        if($kharid_typ == 'etebari')
                                        	$ok = $ok and $ticket->add($tmp_id[$index],$noth);
					else
						$info_ticket[] = $ticket;
                                        $ghimat_kharid += $parvaz->mablagh_kharid;
                                }
                                /*
                                else if($ticket_type == 1 && $customer->ticketNumberExists($shomare)<=-1)
                                {
                                        $ticket->en = 0;
                                        $ok = $ok and $ticket->add($tmp_id[$index]);
                                }
                                */				
			}
			for($i = 0;$i < $chd;$i++)
                        {
				$ticket = new ticket_class;
                                //$ticket->fname = $_REQUEST["chd_fname_$i"];
                                $ticket->lname = $_REQUEST["chd_lname_$i"];
                                $ticket->tel = "";
                                $ticket->adult = 1;
                                $ticket->parvaz_det_id = $parvaz->getId();
                                $ticket->mablagh = $parvaz->ghimat*$zarib;
                                $ticket->poorsant = $customer->getPoorsant($parvaz->getId());
                                $ticket->customer_id = $customer->getId();
                                $ticket->user_id = (int)$_SESSION[$conf->app."_user_id"];
                                $ticket->typ = $ticket_type;
				$ticket->gender = $_REQUEST["chd_gender_$i"];
                                if($ticket->lname != "")
					$empty_tickets++;
                                $ticket->en = 1;
				$ticket->email_addr = trim($_REQUEST["email_addr"]);
				$ticket->sites_id = (int)$_REQUEST["sites_id"];
                                $ticket->sanad_record_id = $sanad_record_id;
                                $j = 0;
                                $shomare = -1;
                                if(isset($_REQUEST["chd_shomare_$i"]))
	                                $shomare = (int)$_REQUEST["chd_shomare_$i"];
                                if($ticket_type == 1)
                                {
                                        $ticket->shomare = $shomare;
                                        $customer->deleteTicketNumber($shomare);
                                        if($kharid_typ == 'etebari')
                                        	$ok = $ok and $ticket->add($tmp_id[$index],$noth);
					else
						$info_ticket[] = $ticket;
                                        $ghimat_kharid += $parvaz->mablagh_kharid;
                                }
                                else if($ticket_type == 0)
                                {
                                        if($p_i == 0)
                                        {
                                                $ticket->shomare = $customer->decTicketCount();
                                                $chd_last[$i] = $ticket->shomare;
                                        }
                                        else if($domasire_ast)
                                        {
                                                $ticket->shomare = $chd_last[$i];
                                        }
                                        if($kharid_typ == 'etebari')
                                        	$ok = $ok and $ticket->add($tmp_id[$index],$noth);
					else
						$info_ticket[] = $ticket;
                                        $ghimat_kharid += $parvaz->mablagh_kharid;
                                }
				
                        }
                        for($i = 0;$i < $inf;$i++)
                        {
				$ticket = new ticket_class;
                                //$ticket->fname = $_REQUEST["inf_fname_$i"];
                                $ticket->lname = $_REQUEST["inf_lname_$i"];
                                $ticket->tel = "";
                                $ticket->adult = 2;
                                $ticket->parvaz_det_id = $parvaz->getId();
                                $ticket->mablagh = $parvaz->ghimat*$zarib;
                                $ticket->poorsant = $customer->getPoorsant($parvaz->getId());
                                $ticket->customer_id = $customer->getId();
                                $ticket->user_id = (int)$_SESSION[$conf->app."_user_id"];
                                $ticket->typ = $ticket_type;
				$ticket->gender = $_REQUEST["inf_gender_$i"];
                                $ticket->en = 1;
				$ticket->email_addr = trim($_REQUEST["email_addr"]);
				$ticket->sites_id = (int)$_REQUEST["sites_id"];
                                $ticket->sanad_record_id = $sanad_record_id;
                                $j = 0;
                                $shomare = -1;
                                if(isset($_REQUEST["inf_shomare_$i"]))
	                                $shomare = (int)$_REQUEST["inf_shomare_$i"];
                                if($ticket_type == 1)
                                {
                                        $ticket->shomare = $shomare;
                                        $customer->deleteTicketNumber($shomare);
                                        if($kharid_typ == 'etebari')
                                        	$ok = $ok and $ticket->add($tmp_id[$index],$noth);
					else
						$info_ticket[] = $ticket;
                                        $ghimat_kharid += 200000;//$parvaz->mablagh_kharid;
                                }
				else if($ticket_type == 0)
				{
                                        if($p_i == 0)
                                        {
                                                $ticket->shomare = $customer->decTicketCount();
                                                $inf_last[$i] = $ticket->shomare;
                                        }
                                        else if($domasire_ast)
                                        {
                                                $ticket->shomare = $inf_last[$i];
                                        }
					if($kharid_typ == 'etebari')
                                        	$ok = $ok and $ticket->add($tmp_id[$index],$noth);
					else
						$info_ticket[] = $ticket;
                                        $ghimat_kharid += 200000;$parvaz->mablagh_kharid;
				} 
                        }
			if($kharid_typ=='etebari')
				$mysql->ex_sqlx("delete from `reserve_tmp` where `id` = ".$tmp_id[$index]);
			$jam_ghimat1 += $zarib*$tedad*$parvaz->ghimat+$zarib*$inf*$parvaz->ghimat/10;
			$index ++;
			$p_i++;
		}
		if($kharid_typ == 'naghdi')
		{
			$bool = TRUE;
			$tarikh_now = date("Y-m-d H:i:s");
			for($k=0;$k<count($tmp_id);$k++)
				if(!ticket_class::updateTmp($tmp_id[$k],$info_ticket))
					$bool = FALSE;
			if($bool)
			{
				$pardakht_id =  pardakht_class::add(implode(',',$tmp_id),$tarikh_now,$jam_ghimat1);
				$pardakht = new pardakht_class($pardakht_id);
				$mysql = new mysql_class;
				$rahgiri = $pardakht->getBarcode();
				if($conf->ps === TRUE)
					$pay_code = pay_class::ps_pay($pardakht_id,$jam_ghimat1);
				else if($conf->payline === TRUE)
				{
					$pay_code = pay_class::pl_pay($pardakht_id,$jam_ghimat1);
					$mysql->ex_sqlx("update `pardakht` set `bank_out` = '$pay_code' where `id` = $pardakht_id");
				}
				else
					$pay_code = pay_class::pay($pardakht_id,$jam_ghimat1);
				$tmpo = explode(',',$pay_code);
				if(count($tmpo)==2 && $tmpo[0]==0 && $conf->ps !== 'TRUE')
					$redirect = "<script language=\"javascript\">alert(\"کد رهگیری خود را یادداشت نمایید \\n $rahgiri \\n سپس به بانک هدایت می شوید\");postRefId('".$tmpo[1]."');</script>";
				else if($conf->payline === TRUE)
				{
					//$pay_code = "$pardakht_id,$jam_ghimat1,$pay_code";
					$redirect = "<script language=\"javascript\">alert(\"کد رهگیری شما  \\n $rahgiri \\n می باشد . آن را یادداشت کنید.\");pl_postRefId('$pay_code');</script>";
				}
				else if($conf->ps === TRUE)
				{
					$redirect = "<script language=\"javascript\">alert(\"کد رهگیری شما  \\n $rahgiri \\n میباشد . آن را یادداشت کنید.\");ps_postRefId('$pay_code');</script>";
				}
				else
				{
					die('خطا در ارتباط با بانک');
				}
			}
			else
				die("err");
		}
		else if($kharid_typ=='etebari')
		{
			$customer->buyTicket($sanad_record_id,$jam_ghimat1);
			if($parvaz->is_shenavar)
				parvaz_det_class::sanad_shenavar_kharid($parvaz,$adl+$chd,$sanad_record_id,$user_id);
			if($ok)
				echo( "<div style='font-size:130%;height:100px;padding:50px;' class='msg detail_div' onclick=\"printTicket('$sanad_record_id');\" >ثبت با موفقیت انجام شد جهت مشاهده بلیت‌ها <span class='notice' >اینجا</span> کلیک کنید</div>");
			else
				die ("<script language=\"javascript\">alert('ثبت ناموفق');</script>");
		}
	}
	$gname = 'grid_checkflight';
	$input =array($gname=>array('table'=>'parvaz_det','div'=>'grid_checkflight_div'));
	$xgrid = new xgrid($input);
	//$xgrid->eRequest[$gname] = array('adl'=>$adl,'chd'=>$chd,'inf'=>$inf,'selected_parvaz'=>$selected_parvaz,'ticket_type'=>$ticket_type,'kharid_typ'=>$kharid_typ);
	/*
	$xgrid->whereClause[$gname] = "`id`='$selected_parvaz'";
	$xgrid->column[$gname][0]['name'] ='شماره';
	$xgrid->column[$gname][0]['cfunction'] = array('loadShomare');
	$xgrid->column[$gname][1]['name'] ='';
	$xgrid->column[$gname][2]['name'] ='تاریخ';
	$xgrid->column[$gname][2]['cfunction'] = array('hamed_pdate');
	$xgrid->column[$gname][3]['name'] ='ساعت';
	$xgrid->column[$gname][2]['cfunction'] = array('saat');
	$xgrid->column[$gname][4]['name'] ='';
	$xgrid->column[$gname][5]['name'] ='قیمت بلیت"."(ریال)';
	$xgrid->column[$gname][5]['cfunction'] = array('monize');
	$xgrid->column[$gname][6]['name'] ='';
	$xgrid->column[$gname][7]['name'] ='';
	$xgrid->column[$gname][8]['name'] ='';
	$xgrid->column[$gname][9]['name'] ='';
	$xgrid->column[$gname][10]['name'] ='';
	$xgrid->column[$gname][11]['name'] ='';
	$xgrid->column[$gname][12]['name'] ='';
	$xgrid->column[$gname][13]['name'] ='';
	$xgrid->column[$gname][] =array('name'=>'کمیسیون(ریال)','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('poorsant'));
	$xgrid->column[$gname][] =array('name'=>'هواپیمایی','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadSherkat'));
	$xgrid->column[$gname][] =array('name'=>'مقصد','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadMaghsad'));
	$xgrid->column[$gname][] =array('name'=>'مبدأ','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadMabda'));
	$xgrid->addFunction[$gname] ='add_item';
	$xgrid->canAdd[$gname] = TRUE;	
	*/
	$outxgrid =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($outxgrid);
	
//-----------------------------------------
?>
<script>
        function postRefId (refIdValue)
        {
                var form = document.createElement("form");
	        form.setAttribute("method", "POST");
        	form.setAttribute("action", "<?php echo $conf->mellat_payPage; ?>");         
                form.setAttribute("target", "_self");
	        var hiddenField = document.createElement("input");              
        	hiddenField.setAttribute("name", "RefId");
                hiddenField.setAttribute("value", refIdValue);
	        form.appendChild(hiddenField);
        	document.body.appendChild(form);         
                form.submit();
	        document.body.removeChild(form);
        }
	function pl_postRefId(inp)
        {
/*
                var result = jQuery.parseJSON(inp);
                //mehrdadDump(result);
                var form = document.createElement("form");
                form.setAttribute("method", "POST");
                //form.setAttribute("action", "<?php echo $conf->ps_payPage; ?>");         
                form.setAttribute("action", "http://payline.ir/payment/gateway-");
                //form.setAttribute("action", "pstest.php");
                form.setAttribute("target", "_self");
                var hiddenField;
                for(i in result)
                {
                        hiddenField = document.createElement("input");              
                        hiddenField.setAttribute("name", i);
                        hiddenField.setAttribute("value", result[i]);
                        form.appendChild(hiddenField);
                        document.body.appendChild(form);
                }
                form.submit();
                document.body.removeChild(form);
*/
		window.location = "<?php echo $conf->payline_pay; ?>"+inp;//"http://payline.ir/payment-test/gateway-"+inp;
        }
	function ps_postRefId(inp)
	{
		var result = jQuery.parseJSON(inp);
		//mehrdadDump(result);
		var form = document.createElement("form");
                form.setAttribute("method", "POST");
                form.setAttribute("action", "<?php echo $conf->ps_payPage; ?>");         
		//form.setAttribute("action", "‫‪http://payline.ir/payment/gateway-send‬‬");
		form.setAttribute("action", "pstest.php");
                //form.setAttribute("target", "_self");
		var hiddenField;
		for(i in result)
		{
	                hiddenField = document.createElement("input");              
        	        hiddenField.setAttribute("name", i);
                	hiddenField.setAttribute("value", result[i]);
	                form.appendChild(hiddenField);
        	        document.body.appendChild(form);
		}
                form.submit();
                document.body.removeChild(form);
	}
	function printTicket(sanad_record_id)
	{
		closeDialog();
		openDialog("finalticket.php?sanad_record_id="+sanad_record_id+"&ticket_type="+<?php echo $ticket_type; ?>+"&","بلیت‌ها",{'minWidth':750,'minHeight':400},true);
	}
</script>

<div align="center" >
	<?php
		if($redirect!='')
			echo $redirect;	
	?> 
	<input type="hidden" name="api" value="‫7654421‪adxcv-zzadq-polkjsad-opp13opoz-1sdf455aadzmck‬‬" />
	        <input type="hidden" name="‫‪amount‬‬" value="1500" />
	        <input type="hidden" name="‫‪redirect‬‬" value="<?php $conf->ps_redirectAddress; ?>" />
		
		<input type="hidden" name="adl" value="<?php echo $adl; ?>" />
	        <input type="hidden" name="chd" value="<?php echo $chd; ?>" />
	        <input type="hidden" name="inf" value="<?php echo $inf; ?>" />
	        <input type="hidden" name="ticket_type" value="<?php echo $ticket_type; ?>" />
	        <input type="hidden" name="selected_parvaz" value="<?php echo $selected_parvaz; ?>" />
		<input type="hidden" name="tmp_id" value = "<?php echo implode(",",$tmp_id); ?>" />
		<input type="hidden" name="epass" value = "<?php //echo $epass; ?>" />
		<input type="hidden" name="kharid_typ" value="<?php echo $kharid_typ; ?>">
		<input type="hidden" id="mod" name="mod" value="" />
</div>
