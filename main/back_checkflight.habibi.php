<?php
	session_start();
	include_once ("../kernel.php");
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
		if($parvaz->j_id >0)
		{
			$out = FALSE;
			foreach($selectedParvaz as $tmp)
			{				
				if($tmp->mabda_id == $parvaz->maghsad_id && $parvaz->mabda_id = $tmp->maghsad_id)
					$out = TRUE;
			}
		}
		return($out);
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
	$msg = " ";
	$out = "";
	$grid = new jshowGrid_new("parvaz_det","grid1");
	if(!isset($_SESSION[conf::app."_user_id"]) || !isset($_REQUEST["adl"]))
		die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">window.location = 'login.php';</script></body></html>");
	if((int)$_REQUEST["adl"] <= 0)
		die("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('حداقل یک بزرگسال باید انتخاب شود');window.location='index.php';</script></body></html>");
	$customer_typ = (int)$_SESSION[conf::app.'_customer_typ'];
        $adl = abs((int)$_REQUEST["adl"]);
        $chd = abs((int)$_REQUEST["chd"]);
        $inf = abs((int)$_REQUEST["inf"]);
        $ticket_type = (int)$_REQUEST["ticket_type"];
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
		$domasire_ok = bargashtHast($selectedParvaz,$tmp) and $domasire_ok;
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
	if(!$customer_etebar_ok)
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
			foreach($selectedParvaz as $tmp)
			{
				$alaki = ticket_class::addTmp($tmp->getId(),$tedad);
				$tmp->setZarfiat($tedad);
				$tmp_id[] = $alaki;
				$adl_ghimat += $tmp->ghimat;
				$chd_ghimat += $tmp->ghimat;
				$inf_ghimat += ($tmp->ghimat/10);
				$poorsant += $tmp->ghimat * ($customer->getPoorsant($tmp->getId())/100);
			}
			$adl_ghimat = enToPerNums(monize($adl_ghimat));
			$chd_ghimat = enToPerNums(monize($chd_ghimat));
			$inf_ghimat = enToPerNums(monize($inf_ghimat));
			$poorsant = enToPerNums(monize($poorsant));
			$adults = <<<adul
				<tr class="showgrid_row_odd">
                                        <th class="showgrid_row_td_reserve_reserve">ردیف</th>
					<th colspan='3'	class="showgrid_row_td_reserve_reserve">بزرگسال</th>
					<th colspan='2' >شماره تیکت</th>
					<th class="showgrid_row_td_reserve_reserve">بهای فروش</th>
                                        <th class="showgrid_row_td_reserve_reserve">کمیسین</th>
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
					<td><select class='inp' style='display:none;' name='adl_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
					<td class="showgrid_row_td_reserve" ><input type='text' name='adl_shomare_$i' id='adl_shomare_$i' class='inp'  /></td>
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
                                        <td colspan="2" style="width:auto;"><input type='text' name='adl_lname_$i' id='adl_lname_$i' class='inp' style="width:400px;"/></td>
					<td><select class='inp' name='adl_gender_$i'  style='display:none;' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
<!--

	
                                        <td class="showgrid_row_td_reserve" >شماره تماس:</td>
                                        <td class="showgrid_row_td_reserve" ><input type='text' name='adl_tel_$i' id='adl_tel$i' class='inp'  /></td>
-->
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
				<tr class="showgrid_row_odd">
					<td class="showgrid_row_td_reserve" >$radif</td>
					<td >نام و نام‌خانوادگی:</td>
					<td  colspan='2'  class="showgrid_row_td_reserve" style='width:auto;text-align:right;' ><input type='text' name='chd_lname_$i' id='chd_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' style='display:none;' name='chd_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
					<td class="showgrid_row_td_reserve" >شماره تیکت</td>
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
                                <tr class="showgrid_row_odd">
                                        <td class="showgrid_row_td_reserve" >$radif</td>
                                        <td>نام و نام‌خانوادگی:</td>
					<td  colspan='2'  class="showgrid_row_td_reserve" style='width:auto;text-align:right;' ><input type='text' name='chd_lname_$i' id='chd_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' style='display:none;' name='chd_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
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
				<tr class="showgrid_row_even" >
					<th colspan='8' class="showgrid_row_td_reserve_reserve" >نوزاد</th>
				</tr>
infa;
				for($i = 0;$i < $inf;$i++)
				{
					$khal = enToPerNums(monize(perToEnNums(umonize($inf_ghimat))-perToEnNums(umonize($poorsant))));
                                        if($ticket_type == 1)
                                        {
						$infants .= <<<tmp2
				<tr class="showgrid_row_odd">
					<td class="showgrid_row_td_reserve" >$radif</td>
					<td >نام و نام‌خانوادگی:</td>
					<td  colspan='2'  class="showgrid_row_td_reserve" style='width:autp;text-align:right;' ><input type='text' name='inf_lname_$i' id='inf_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='inf_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
					<td>جنسیت</td>
					<td class="showgrid_row_td_reserve" >شماره تیکت</td>
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
                                <tr class="showgrid_row_odd">
                                        <td class="showgrid_row_td_reserve" >$radif</td>
                                        <td>نام و نام‌خانوادگی:</td>
                                        <td  colspan='2'  class="showgrid_row_td_reserve" style='width:auto;text-align:right;' ><input type='text' name='inf_lname_$i' id='inf_lname_$i' class='inp'  style="width:400px;"/></td>
					<td><select class='inp' name='inf_gender_$i' ><option value='1' >مذکر</option><option value='0' >مؤنث</option></select></td>
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
		<input id="tim" style="color:#000000;width:70px;font-size:25px;" readonly="readonly" value="5:0" />
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
				<td class="showgrid_row_td_reserve" colspan="2" >شماره تماس و اطلاعات دیگر :</td>
                                <td class="showgrid_row_td_reserve" colspan="6" ><input type='text' name='adl_tel_0' id='adl_tel0' class='inp'  style="width:99%;" /></td>
			</tr>
			<tr class="showgrid_row_odd">
				<td colspan = "8" class="showgrid_row_td_reserve" >
				<br/>
				<input type="button" value="ثبت نهایی" class="inp1" onclick="sendTickets();" />
                                <input type="button" value="انصراف" class="inp1" onclick="rejectTickets();" /></td>
			</tr>		
		</table>

OOUT;
		}
	}
	else if($_REQUEST["mod"] == "save" && $msg == "")
	{
		$empty_tickets = 0;
		$sanad_record_id = 200;
		$tmp_id = explode(",",$_REQUEST["tmp_id"]);
		mysql_class::ex_sql("select `id` from `reserve_tmp` where `id` = ".$tmp_id[0],$qqq);
		if(!($rrr=mysql_fetch_array($qqq)))
			die('<script>window.location = "index.php";</script>');
		mysql_class::ex_sql("select MAX(`sanad_record_id`) as `sss` from `customer_daryaft`",$q);
		if($r = mysql_fetch_array($q))
		{
			$sanad_record_id = (((int)$r["sss"]>199)?(int)$r["sss"]:199);
			$sanad_record_id ++;
		}
		$ticket = new ticket_class;
		$ticket->clearTickets();
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
		foreach($selectedParvaz as $parvaz)
		{
			for($i = 0;$i < $adl;$i++)
			{
				//$ticket->fname = $_REQUEST["adl_fname_$i"];
				$ticket->lname = $_REQUEST["adl_lname_$i"];
				$ticket->tel = ((isset($_REQUEST["adl_tel_$i"]))?$_REQUEST["adl_tel_$i"]:'');
				$ticket->adult = 0;						
				$ticket->parvaz_det_id = $parvaz->getId();
				$ticket->mablagh = $parvaz->ghimat;
				$ticket->poorsant = $customer->getPoorsant($parvaz->getId());
				$ticket->customer_id = $customer->getId();
				$ticket->user_id = (int)$_SESSION[conf::app."_user_id"];
				$ticket->typ = $ticket_type;
				if($ticket->lname != "")
					$empty_tickets++;
				$ticket->en = 1;
				$ticket->sanad_record_id = $sanad_record_id;				
				$j = 0;
				$shomare = -1;
				if(isset($_REQUEST["adl_shomare_$i"]))
					$shomare = (int)$_REQUEST["adl_shomare_$i"];
                                if($customer->ticketNumberExists($shomare)>-1 && $ticket_type == 1)
                                {
                                        $ticket->shomare = $shomare;
                                        $customer->deleteTicketNumber($shomare);
                                        $ok = $ok and $ticket->add($tmp_id[$index]);
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
                                        $ok = $ok and $ticket->add($tmp_id[$index]);
                                }
                                else if($ticket_type == 1 && $customer->ticketNumberExists($shomare)<=-1)
                                {
                                        $ticket->en = 0;
                                        $ok = $ok and $ticket->add($tmp_id[$index]);
                                }				
			}
			for($i = 0;$i < $chd;$i++)
                        {
                                //$ticket->fname = $_REQUEST["chd_fname_$i"];
                                $ticket->lname = $_REQUEST["chd_lname_$i"];
                                $ticket->tel = "";
                                $ticket->adult = 1;
                                $ticket->parvaz_det_id = $parvaz->getId();
                                $ticket->mablagh = $parvaz->ghimat;
                                $ticket->poorsant = $customer->getPoorsant($parvaz->getId());
                                $ticket->customer_id = $customer->getId();
                                $ticket->user_id = (int)$_SESSION[conf::app."_user_id"];
                                $ticket->typ = $ticket_type;
                                if($ticket->lname != "")
					$empty_tickets++;
                                $ticket->en = 1;
                                $ticket->sanad_record_id = $sanad_record_id;
                                $j = 0;
                                $shomare = -1;
                                if(isset($_REQUEST["chd_shomare_$i"]))
	                                $shomare = (int)$_REQUEST["chd_shomare_$i"];
                                if($customer->ticketNumberExists($shomare)>-1 && $ticket_type == 1)
                                {
                                        $ticket->shomare = $shomare;
                                        $customer->deleteTicketNumber($shomare);
                                        $ok = $ok and $ticket->add($tmp_id[$index]);
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
                                        $ok = $ok and $ticket->add($tmp_id[$index]);
                                }
				else if($ticket_type == 1 && $customer->ticketNumberExists($shomare)<=-1)
				{
					$ticket->en = 0;
					$ok = $ok and $ticket->add($tmp_id[$index]);
				}
                        }
                        for($i = 0;$i < $inf;$i++)
                        {
                                //$ticket->fname = $_REQUEST["inf_fname_$i"];
                                $ticket->lname = $_REQUEST["inf_lname_$i"];
                                $ticket->tel = "";
                                $ticket->adult = 2;
                                $ticket->parvaz_det_id = $parvaz->getId();
                                $ticket->mablagh = $parvaz->ghimat;
                                $ticket->poorsant = $customer->getPoorsant($parvaz->getId());
                                $ticket->customer_id = $customer->getId();
                                $ticket->user_id = (int)$_SESSION[conf::app."_user_id"];
                                $ticket->typ = $ticket_type;
                                $ticket->en = 1;
                                $ticket->sanad_record_id = $sanad_record_id;
                                $j = 0;
                                $shomare = -1;
                                if(isset($_REQUEST["inf_shomare_$i"]))
	                                $shomare = (int)$_REQUEST["inf_shomare_$i"];
                                if($customer->ticketNumberExists($shomare)>-1 && $ticket_type == 1)
                                {
                                        $ticket->shomare = $shomare;
                                        $customer->deleteTicketNumber($shomare);
                                        $ok = $ok and $ticket->add($tmp_id[$index]);
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
					$ok = $ok and $ticket->add($tmp_id[$index]);
				}
                                else if($ticket_type == 1 && $customer->ticketNumberExists($shomare)<=-1)
                                {
                                        $ticket->en = 0;
                                        $ok = $ok and $ticket->add($tmp_id[$index]);
                                }
                        }
			$zarib = (100 - $customer->getPoorsant($parvaz->getId()))/100;
			$jam_ghimat1 += $zarib*$tedad*$parvaz->ghimat+$zarib*$inf*$parvaz->ghimat/10;
			$index ++;
			$p_i++;
		}
		$customer->buyTicket($sanad_record_id,$jam_ghimat1);
		if($ok)
			echo "<script>window.location = 'finalticket.php?ticket_type=$ticket_type&sanad_record_id=$sanad_record_id&r='+Math.random();</script>";
		else
			echo  "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"></head><body><script language=\"javascript\">alert('ثبت ناموفق');window.location = 'index.php';</script></body></html>";
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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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
				document.getElementById('blit_data').submit();
			else
				alert('نام خانوادگی و حداقل یک تلفن باید وارد شود');
		}
		function rejectTickets()
		{
			document.getElementById('mod').value = "reject";
			document.getElementById('blit_data').submit();
		}
	</script>
</head>
<body>
	<br/>
	<br/>
	<div align="center" >
		<form id="blit_data" >
		<?php
			echo (($msg != "")?"<script>alert('$msg');window.location='index.php';</script>":$out);
		?>
			<input type="hidden" name="adl" value="<?php echo $adl; ?>" />
		        <input type="hidden" name="chd" value="<?php echo $chd; ?>" />
		        <input type="hidden" name="inf" value="<?php echo $inf; ?>" />
		        <input type="hidden" name="ticket_type" value="<?php echo $ticket_type; ?>" />
		        <input type="hidden" name="selected_parvaz" value="<?php echo $selected_parvaz; ?>" />
			<input type="hidden" name="tmp_id" value = "<?php echo implode(",",$tmp_id); ?>" />
			<input type="hidden" name="epass" value = "<?php echo $epass; ?>" />
			<input type="hidden" id="mod" name="mod" value="save" />
		</form>
	</div>
</body>
</html>
