<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	include_once ("../class/nusoap.php");
	$user_id = $_SESSION[conf::app.'_user_id'];
	function flightZarfiat($parvaz)
	{
		$out = 0;
		$customer_typ = (int)$_SESSION[conf::app."_customer_typ"];
		if($customer_typ != 2 && $parvaz->getZarfiat($_SESSION[conf::app.'_customer_id'])>=9)
			$out = 9;
		else if($customer_typ != 2)
			$out = $parvaz->getZarfiat($_SESSION[conf::app.'_customer_id']);
		else if($customer_typ == 2)
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
                        {
                                if($tmp->mabda_id == $parvaz->maghsad_id && $parvaz->mabda_id = $tmp->maghsad_id)
                                        $out = TRUE;
                        }
                }
                else if($parvaz->j_id >0 && $jids!=null)
                {
                        $out = FALSE;
                        foreach($selectedParvaz as $tmp)
                        {
                                for($i = 0;$i < count($jids);$i++)
                                        if($jids[$i] == $tmp->getId())
                                                $out = TRUE;
                        }
                }
                return($out);
	}
	function loadCity($inp)
        {
                $inp = (int)$inp;
                $out = "";
                mysql_class::ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
                if($r = mysql_fetch_array($q))
                {
                        $out = $r["name"];
                }
                return($out);
        }
	function loadSherkatName($inp)
        {
                $inp = (int)$inp;
                $out = "";
                mysql_class::ex_sql("select `name` from `sherkat` where `id` = '$inp'",$q);
                if($r = mysql_fetch_array($q))
                {
                        $out = $r["name"];
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
                $par = new parvaz_det_class((int)$inp);
                $customer_id = $_SESSION[conf::app."_customer_id"];
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

	function epass($cid)
	{
		$out = "";
		mysql_class::ex_sql("select `epass` from `customers` where `id`='$cid'",$q);
		if($r=mysql_fetch_array($q))
		{
			$out = $r["epass"];
		}
		return($out);
	}
	$msg = "";
	$out = "";
	$info_ticket = array();
	$redirect = '';
	$grid = new jshowGrid_new("parvaz_det","grid1");
	if(!isset($_SESSION[conf::app."_user_id"]) || !isset($_REQUEST["adl"]))
		die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">window.location = 'index.php';</script></body></html>");
	if((int)$_REQUEST["adl"] <= 0)
		die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('حداقل یک بزرگسال باید انتخاب شود');window.location='index.php';</script></body></html>");
	$customer_typ = (int)$_SESSION[conf::app.'_customer_typ'];
        $adl = abs((int)$_REQUEST["adl"]);
        $chd = abs((int)$_REQUEST["chd"]);
        $inf = abs((int)$_REQUEST["inf"]);
        $ticket_type = (int)$_REQUEST["ticket_type"];
	$kharid_typ = ((isset($_REQUEST['kharid_typ']))?$_REQUEST['kharid_typ']:'');
	$selected_parvaz = $_REQUEST["selected_parvaz"];
	$epass = ((isset($_REQUEST["epass"]))?$_REQUEST["epass"]:"");
	if($customer_typ != 2 && $ticket_type == 0 && $epass != epass($_SESSION[conf::app.'_customer_id']))
		die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert(\"رمز بلیت الکترونیکی اشتباه است\");window.location = 'ticket_check.php?adl=$adl&chd=$chd&inf=$inf&selected_parvaz=$selected_parvaz&ticket_type=$ticket_type&r='+Math.random();</script></body></html>");
        $tmp = explode(",",$selected_parvaz);
	foreach($tmp as $parvaz_id)
        {
        	$tmp_parvaz = new parvaz_det_class((int)$parvaz_id);
                $selectedParvaz[] = $tmp_parvaz;
        }
	$customer = new customer_class((int)$_SESSION[conf::app."_customer_id"]);
	$customer_typ = (int)$_SESSION[conf::app."_customer_typ"];
	$tedad = $adl + $chd;
	$jam_ghimat = 0;
	$tedad_ok = TRUE;
        foreach($tmp as $parvaz_id)
        {
	        $tmp_parvaz = new parvaz_det_class((int)$parvaz_id);

                if(flightZarfiat($tmp_parvaz) < $tedad)
        	        $tedad_ok = FALSE;

/*		if($tmp_parvaz->getZarfiat($customer->getId())<$tedad)
			$tedad_ok = FALSE;
*/
                $jam_ghimat += ($tedad * $tmp_parvaz->ghimat);
                $jam_ghimat += ($inf * $tmp_parvaz->ghimat)/10;
        }



	$paravaz_tedad  = ((count($tmp)>0)?TRUE:FALSE);
	$domasire_ok = TRUE;
	if(count($tmp) == 1 && (int)$tmp[0] <=0)
		$paravaz_tedad = FALSE;
	foreach($selectedParvaz as $tmp)
	{
		$bar = bargashtHast($selectedParvaz,$tmp);
		$domasire_ok = $bar && $domasire_ok;
	}
	$customer_etebar_ok = TRUE;
	$customer_tedad_ok = TRUE;
	$customer_shomare_ok = TRUE;
	if($customer_typ != 2 && $customer->max_amount < $jam_ghimat )
		$customer_etebar_ok = FALSE;
	if($customer->max_ticket+1-$customer->min_ticket < $tedad)
		$customer_tedad_ok = FALSE;
//---------Flight Selctetion Problems------
	$msg = "";	
	if( !(isset($_REQUEST['mod']) && $_REQUEST['mod']=='save') && !$tedad_ok )           
		$msg = "Zarfiate parvaz kam ast";
	if(!$paravaz_tedad)
		$msg .= " parvaz entekhab nashode";
	if(!$domasire_ok && $customer_typ != 2)
		$msg .= " paravz domasire dorost entekhab nashode";
//-----------------------------------------
//---------Customer Problems---------------
	if(!$customer_etebar_ok && $kharid_typ=='etebari')
		$msg .= " اعتبار مشتری کافی نیست";
	if(!$customer_tedad_ok)
		$msg .= " سقف تعداد خرید مشتری کافی نیست";
	if(!$customer_shomare_ok)
		$msg .= " تعداد شماره تیکت مشتری کافی نیست";
	$out = "";
	$adults = "";
	$childs = "";
	$infants = "";
	$tmp_id = array();

	if(!isset($_REQUEST["mod"]))
	{

		if($msg == "")
		{
			$adl_ghimat = 0;
			$chd_ghimat = 0;
			$inf_ghimat = 0;
			$poorsant = 0;
			$radif = '۱';
			$k = 0;
			//var_dump($_SESSION);
			foreach($selectedParvaz as $tmp)
			{
				$timeout = 5;
				if($kharid_typ=='naghdi')
					$timeout = 15;
				if(!isset($_SESSION[conf::app.'_addTmp']) || count($_SESSION[conf::app.'_addTmp'])<count($selectedParvaz) )
				{
					$alaki = ticket_class::addTmp($tmp->getId(),$tedad,$timeout);
					$tmp->setZarfiat($tedad);
					$_SESSION[conf::app.'_addTmp'][] = $alaki ;
				}
				else
					$alaki = $_SESSION[conf::app.'_addTmp'][$k]; 
				$tmp_id[] = $alaki;
				$adl_ghimat += $tmp->ghimat;
				$chd_ghimat += $tmp->ghimat;
				$inf_ghimat += ($tmp->ghimat/10);
				$poorsant += $tmp->ghimat * ($customer->getPoorsant($tmp->getId())/100);
				$k++;
			}
			$res_tmp = new reserve_tmp_class($alaki);
			$time_out = strtotime($res_tmp->tarikh .' + 5 minute ') - strtotime(date("Y-m-d H:i:s"));
 			$time_out = audit_class::secondToMinute($time_out);
			$adl_ghimat = enToPerNums(monize($adl_ghimat));
			$chd_ghimat = enToPerNums(monize($chd_ghimat));
			$inf_ghimat = enToPerNums(monize($inf_ghimat));
			$poorsant = enToPerNums(monize($poorsant));
			if ($ticket_type==1)
				$e_ticket="<th colspan='1' >شماره تیکت</th>";
			else	
				$e_ticket="<th colspan='1'></th>";
			$adults = <<<adul
				<tr class="showgrid_row_odd">
                                        <th class="showgrid_row_td_reserve_reserve">ردیف</th>
					<th colspan='2' class="showgrid_row_td_reserve_reserve">بزرگسال</th>
					<th colspan='1' class="showgrid_row_td_reserve_reserve">جنسیت</th>
					$e_ticket
					<th class="showgrid_row_td_reserve_reserve">بهای فروش</th>
                                        <th class="showgrid_row_td_reserve_reserve">کمیسیون</th>
                                        <th class="showgrid_row_td_reserve_reserve">بهای خالص</th>
				</tr>
adul;
			for($i = 0;$i < $adl;$i++)
			{
				$khal = enToPerNums(monize(perToEnNums(umonize($adl_ghimat))-perToEnNums(umonize($poorsant))));
				if($ticket_type == 1)
				{
					$adults .= <<<tmp0
				<tr class="showgrid_row_even" >
					<td class="showgrid_row_td_reserve" >$radif</td>
					<td>نام‌و‌نام‌خانوادگی</td>
					<td style="width:auto;"><input type='text' name='adl_lname_$i' id='adl_lname_$i' class='inp' style="width:400px;"/></td>
					<td><select class='inp' name='adl_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
					<td class="showgrid_row_td_reserve"><input type='text' name='adl_shomare_$i' id='adl_shomare_$i' class='inp'  /></td>
					<td class="showgrid_row_td_reserve" readonly="readonly">$adl_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
				</tr>
tmp0;
				}
				else
				{
	                                $adults .= <<<tmp0
                                <tr class="showgrid_row_even" >
                                        <td class="showgrid_row_td_reserve" >$radif</td>
					<td>نام‌و‌نام‌خانوادگی</td>
                                        <td colspan="1" style="width:auto;"><input type='text' name='adl_lname_$i' id='adl_lname_$i' class='inp' style="width:400px;"/></td>
					<td><select class='inp' name='adl_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
<!--
	
                                        <td class="showgrid_row_td_reserve" >شماره تماس:</td>
                                        <td class="showgrid_row_td_reserve" ><input type='text' name='adl_tel_$i' id='adl_tel$i' class='inp'  /></td>
-->
					$e_ticket
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$adl_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
                                </tr>
tmp0;
	                                $radif = enToPerNums(perToEnNums($radif)+1);
				}
			}
			if($chd >0)
			{
				$childs = <<<chil
				<tr class="showgrid_row_odd" >
					<th colspan='8' class="showgrid_row_td_reserve" >کودک</th>
				</tr>
chil;
				for($i = 0;$i < $chd;$i++)
				{
					$khal = enToPerNums(monize(perToEnNums(umonize($chd_ghimat))-perToEnNums(umonize($poorsant))));
					if($ticket_type == 1)
					{
						$childs .= <<<tmp1
				<tr class="showgrid_row_even">
					<td class="showgrid_row_td_reserve" >$radif</td>
					<td >نام و نام‌خانوادگی:</td>
					<td  colspan='1'  class="showgrid_row_td_reserve" style='width:auto;text-align:right;' ><input type='text' name='chd_lname_$i' id='chd_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='chd_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
                                        <td class="showgrid_row_td_reserve" ><input type='text' name='chd_shomare_$i' id='chd_shomare_$i' class='inp'  /></td>
					<td class="showgrid_row_td_reserve" readonly="readonly">$chd_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
				</tr>
tmp1;
					}
					else
					{
                                                $childs .= <<<tmp1
                                <tr class="showgrid_row_even">
                                        <td class="showgrid_row_td_reserve" >$radif</td>
                                        <td>نام و نام‌خانوادگی:</td>
					<td  colspan='1'  class="showgrid_row_td_reserve" style='width:auto;text-align:right;' ><input type='text' name='chd_lname_$i' id='chd_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='chd_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
					$e_ticket
					<td class="showgrid_row_td_reserve" readonly="readonly">$chd_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
                                </tr>
tmp1;

					}
	                                $radif = enToPerNums(perToEnNums($radif)+1);
				}
			}
			if($inf > 0)
			{
				$infants = <<<infa
				<tr class="showgrid_row_odd" >
					<th colspan='8' class="showgrid_row_td_reserve_reserve" >نوزاد</th>
				</tr>
infa;
				for($i = 0;$i < $inf;$i++)
				{
					$khal = enToPerNums(monize(perToEnNums(umonize($inf_ghimat))-perToEnNums(umonize($poorsant))));
                                        if($ticket_type == 1)
                                        {
						$infants .= <<<tmp2
				<tr class="showgrid_row_even">
					<td class="showgrid_row_td_reserve" >$radif</td>
					<td >نام و نام‌خانوادگی:</td>
					<td  colspan='1'  class="showgrid_row_td_reserve" style='width:autp;text-align:right;' ><input type='text' name='inf_lname_$i' id='inf_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='inf_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
                                        <td class="showgrid_row_td_reserve" ><input type='text' name='inf_shomare_$i' id='inf_shomare_$i' class='inp'  /></td>
					<td class="showgrid_row_td_reserve" readonly="readonly">$inf_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
				</tr>
tmp2;
					}
					else
					{
                                                $infants .= <<<tmp2
                                <tr class="showgrid_row_even">
                                        <td class="showgrid_row_td_reserve" >$radif</td>
                                        <td>نام و نام‌خانوادگی:</td>
                                        <td  colspan='1'  class="showgrid_row_td_reserve" style='width:auto;text-align:right;' ><input type='text' name='inf_lname_$i' id='inf_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='inf_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
					$e_ticket
					<td class="showgrid_row_td_reserve" readonly="readonly">$inf_ghimat</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$poorsant</td>
                                        <td class="showgrid_row_td_reserve" readonly="readonly">$khal</td>
                                </tr>
tmp2;

					}
	                                $radif = enToPerNums(perToEnNums($radif)+1);
				}

			}
			$out = <<<OOUT
	<span style="color:firebrick;font-size:15px;" >
		دقت فرمایید رزرو موقت شما تنها ۵ دقیقه معتبر است
	</span>
		<input id="tim" style="color:#000000;width:70px;font-size:25px;" readonly="readonly" value="$time_out" />
		<script>
			var t = setTimeout("dec();",1000);
			function dec()
			{
				var tim = document.getElementById('tim');
				var noe = tim.value;
				var tmp = noe.split(':');
				var m = parseInt(tmp[0],10);
				var s = parseInt(tmp[1],10);
				if(s > 0)
				{
					s--;
					var t = setTimeout("dec();",1000);
				}
				else if(m > 0)
				{
					s = 59;
					m--;
					if(m == 1)
						tim.style.color = "firebrick";
					var t = setTimeout("dec();",1000);
				}
				else if(m==0 && s==0)
				{
					alert('TIME OUT!');
					window.location = 'index.php';
				}
				tim.value = m+":"+s;
			}
		</script>
	        <table style="border-style:solid;border-width:1px;border-color:Black;width:80%" border='1'>
$adults
$childs
$infants
			<tr class="showgrid_row_even">
				<td class="showgrid_row_td_reserve" colspan="2" >شماره تماس :</td>
                                <td class="showgrid_row_td_reserve" colspan="6" ><input type='text' name='adl_tel_0' id='adl_tel0' class='inp'  style="width:99%;" /></td>
			</tr>
			<tr class="showgrid_row_odd">
				<td colspan = "8" class="showgrid_row_td_reserve" >
				<br/>
				<img src="../img/btn8.png" style="cursor:pointer;" onclick="sendTickets();">
	                        <img src="../img/btn1.png" style="cursor:pointer;" onclick="rejectTickets();">

			</tr>		
		</table>

OOUT;
		}
	}
	else if($_REQUEST["mod"] == "save" && $msg == "")
	{
		$empty_tickets = 0;
		$tmp_id = explode(",",$_REQUEST["tmp_id"]);
		mysql_class::ex_sql("select `id` from `reserve_tmp` where `id` = ".$tmp_id[0],$qqq);
		if(!($rrr=mysql_fetch_array($qqq)))
			die('<script>window.location = "index.php";</script>');
                $sanad_record_id = 200;
		mysql_class::ex_sql("select MAX(`sanad_record_id`) as `sss` from `customer_daryaft`",$q);
		if($r = mysql_fetch_array($q))
		{
			$sanad_record_id = (((int)$r["sss"]>199)?(int)$r["sss"]:199);
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
			$ticket = new ticket_class;
			for($i = 0;$i < $adl;$i++)
			{
				//$ticket->fname = $_REQUEST["adl_fname_$i"];
				$ticket->lname = $_REQUEST["adl_lname_$i"];
				$ticket->tel = ((isset($_REQUEST["adl_tel_$i"]))?$_REQUEST["adl_tel_$i"]:'');
				$ticket->adult = 0;						
				$ticket->parvaz_det_id = $parvaz->getId();
				$ticket->mablagh = $parvaz->ghimat*$zarib;
				$ticket->poorsant = $customer->getPoorsant($parvaz->getId());
				$ticket->customer_id = $customer->getId();
				$ticket->user_id = (int)$_SESSION[conf::app."_user_id"];
				$ticket->typ = $ticket_type;
				$ticket->gender = $_REQUEST["adl_gender_$i"];
				if($ticket->lname != "")
					$empty_tickets++;
				$ticket->en = 1;
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
                                else if($ticket_type == 0)
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
			$ticket = new ticket_class;
			for($i = 0;$i < $chd;$i++)
                        {
                                //$ticket->fname = $_REQUEST["chd_fname_$i"];
                                $ticket->lname = $_REQUEST["chd_lname_$i"];
                                $ticket->tel = "";
                                $ticket->adult = 1;
                                $ticket->parvaz_det_id = $parvaz->getId();
                                $ticket->mablagh = $parvaz->ghimat*$zarib;
                                $ticket->poorsant = $customer->getPoorsant($parvaz->getId());
                                $ticket->customer_id = $customer->getId();
                                $ticket->user_id = (int)$_SESSION[conf::app."_user_id"];
                                $ticket->typ = $ticket_type;
				$ticket->gender = $_REQUEST["chd_gender_$i"];
                                if($ticket->lname != "")
					$empty_tickets++;
                                $ticket->en = 1;
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
			$ticket = new ticket_class;
                        for($i = 0;$i < $inf;$i++)
                        {
                                //$ticket->fname = $_REQUEST["inf_fname_$i"];
                                $ticket->lname = $_REQUEST["inf_lname_$i"];
                                $ticket->tel = "";
                                $ticket->adult = 2;
                                $ticket->parvaz_det_id = $parvaz->getId();
                                $ticket->mablagh = $parvaz->ghimat*$zarib;
                                $ticket->poorsant = $customer->getPoorsant($parvaz->getId());
                                $ticket->customer_id = $customer->getId();
                                $ticket->user_id = (int)$_SESSION[conf::app."_user_id"];
                                $ticket->typ = $ticket_type;
				$ticket->gender = $_REQUEST["inf_gender_$i"];
                                $ticket->en = 1;
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
                                        $ghimat_kharid += $parvaz->mablagh_kharid;
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
                                        $ghimat_kharid += $parvaz->mablagh_kharid;
				} 
                        }
			if($kharid_typ=='etebari')
				mysql_class::ex_sqlx("delete from `reserve_tmp` where `id` = ".$tmp_id[$index]);
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
				$rahgiri = $pardakht->getBarcode();
				$pay_code = pay_class::pay($pardakht_id,$jam_ghimat1);
				$tmpo = explode(',',$pay_code);
				if(count($tmpo)==2 && $tmpo[0]==0 || TRUE)
					$redirect = "<script language=\"javascript\">alert(\"کد رهگیری خود را یادداشت نمایید \\n $rahgiri \\n سپس به بانک هدایت می شوید\");postRefId('".$tmpo[1]."');</script>";
				else
				{
					die('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head><body><center>در پردازش مشکلی پیش آمده است مجدد تلاش نمایید در صورت پرداخت وجه مبلغی از حساب شما کم نشده است <br/><a href="index.php" >بازگشت</a></center></body></html>');
				}
			}
			else
				die("<html><body><script language=\"javascript\">alert(\"Err. Try again please\"); window.location='index.php'; </script></body></html>");
		}
		else if($kharid_typ=='etebari')
		{
			$customer->buyTicket($sanad_record_id,$jam_ghimat1);
			if($parvaz->is_shenavar)
				parvaz_det_class::sanad_shenavar_kharid($parvaz,$adl+$chd,$sanad_record_id,$user_id);
			if($ok)
				echo "<script>window.location = 'finalticket.php?ticket_type=$ticket_type&sanad_record_id=$sanad_record_id&r='+Math.random();</script>";
			else
				echo  "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('ثبت ناموفق');window.location = 'index.php';</script></body></html>";
		}
	}
	else if($_REQUEST["mod"] == "reject")
	{
		$tmp_id = explode(",",$_REQUEST["tmp_id"]);
               	$alaki = ticket_class::removeTmp($tmp_id);
		foreach($selectedParvaz as $tmp)
                {
	                $tmp->resetZarfiat($tedad);
		}
		die("<html><body><script language=\"javascript\"> window.location='index.php'; </script></body></html>");
	
	}
	$grid = new jshowGrid_new("parvaz_det","grid1");
	$grid->index_width = '30px';
        $grid->width = '820px';
	$grid->whereClause = "`id`='$selected_parvaz'";	
	$grid->columnHeaders[1] = null;
        $grid->columnHeaders[0]="شماره";
        $grid->columnFunctions[0] ="loadShomare";
        $grid->columnHeaders[2]="تاریخ";
        $grid->columnFunctions[2] = "hamed_pdate";
        $grid->columnHeaders[3]="ساعت";
        $grid->columnFunctions[3] = "saat";
        $grid->columnHeaders[4]=null;
        $grid->columnHeaders[5]="قیمت بلیط"."(ریال)";
        $grid->columnFunctions[5] = "monize";
        $grid->columnHeaders[6] = null;
        $grid->columnHeaders[7] = null;
        $grid->columnHeaders[8] = null;
        $grid->columnHeaders[9] = null;
        $grid->columnHeaders[10] = null;
        $grid->columnHeaders[11] = null;
        $grid->columnHeaders[12] = null;
        $grid->columnHeaders[13] = null;
	$grid->columnHeaders[14] = null;
	$grid->addFeild("id");
        $grid->columnHeaders[15] = "کمسیون(ریال)";
        $grid->columnFunctions[15] = "poorsant";
        $grid->addFeild("id",2);
        $grid->columnHeaders[2]="هواپیمایی";
        $grid->columnFunctions[2] ="loadSherkat";
        $grid->addFeild("id",2);
        $grid->columnHeaders[2]="مقصد";
        $grid->columnFunctions[2] ="loadMaghsad";
        $grid->addFeild("id",2);
        $grid->columnHeaders[2]="مبدا";
        $grid->columnFunctions[2] ="loadMabda";		
	$grid->canAdd = FALSE;
        $grid->canDelete = FALSE;
        $grid->canEdit = FALSE;	
	$grid->intial();
        $grid->executeQuery();
        $outgrid = $grid->getGrid();
	
//-----------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php echo lang_fa_class::title; ?></title>
    <link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
	<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link type="text/css" href="../css/style.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" media="all" href="../css/skins/aqua/theme.css" title="Aqua" />
	<style type="text/css">
		.calendar {
			direction: rtl;
		}
	
		#flat_calendar_1, #flat_calendar_2{
			width: 200px;
		}
		.example {
			padding: 10px;
		}
	
		.display_area {
			background-color: #FFFF88
		}
	</style>
	<script type="text/javascript" src="../js/tavanir.js"></script>
	<script type="text/javascript" src="../js/jalali.js"></script>
	<script type="text/javascript" src="../js/calendar.js"></script>
	<script type="text/javascript" src="../js/calendar-setup.js"></script>
	<script type="text/javascript" src="../js/lang/calendar-fa.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
	<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script> 
	<script language="javascript">
		function sendTickets()
		{
			var inputs = document.getElementsByTagName('input');
			var ok = true;
			var telfound = false;
			var tmp = Array();
			for(var i = 0;i < inputs.length;i++)
			{
				tmp = String(inputs[i].name).split('_');
				if(tmp[1] && tmp[1] == 'lname' && inputs[i].value == '')
					ok = false;
				if(tmp[1] && tmp[1] == 'tel' && inputs[i].value != '')
					telfound = true;
			}
			if(ok && telfound)
			{
				document.getElementById('mod').value = 'save';
				document.getElementById('blit_data').submit();
			}
			else
				alert('نام خانوادگی و حداقل یک تلفن باید وارد شود');
		}
		function rejectTickets()
		{
			document.getElementById('mod').value = "reject";
			document.getElementById('blit_data').submit();
		}
                function postRefId (refIdValue)
                {
	                var form = document.createElement("form");
        	        form.setAttribute("method", "POST");
                	form.setAttribute("action", "<?php echo conf::mellat_payPage; ?>");         
	                form.setAttribute("target", "_self");
        	        var hiddenField = document.createElement("input");              
                	hiddenField.setAttribute("name", "RefId");
	                hiddenField.setAttribute("value", refIdValue);
        	        form.appendChild(hiddenField);
                	document.body.appendChild(form);         
	                form.submit();
        	        document.body.removeChild(form);
                }
	</script>
</head>
<body>
	<br/>
	<br/>
	<div align="center" >
		<?php
			if($redirect!='')
				echo $redirect;	
			else
				echo $outgrid;
		?> 
		<form id="blit_data" method="POST" >
		<?php
			if ($msg != "")
			{
				echo "<script>alert('$msg');window.location='index.php';</script>";
			}
			else
			{
				echo $out;
			}
		?>
			<input type="hidden" name="adl" value="<?php echo $adl; ?>" />
		        <input type="hidden" name="chd" value="<?php echo $chd; ?>" />
		        <input type="hidden" name="inf" value="<?php echo $inf; ?>" />
		        <input type="hidden" name="ticket_type" value="<?php echo $ticket_type; ?>" />
		        <input type="hidden" name="selected_parvaz" value="<?php echo $selected_parvaz; ?>" />
			<input type="hidden" name="tmp_id" value = "<?php echo implode(",",$tmp_id); ?>" />
			<input type="hidden" name="epass" value = "<?php echo $epass; ?>" />
			<input type="hidden" name="kharid_typ" value="<?php echo $kharid_typ; ?>">
			<input type="hidden" id="mod" name="mod" value="" />
		</form>
	</div>
</body>
</html>
