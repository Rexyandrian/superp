<?php
	$aaaa = FALSE;
	$GLOBALS['aaaa'] = $aaaa;
	if($aaaa)
		include('../kernel.php');
        function reserve($user,$enc_pass,$tmp_ids,$fnames,$lnames,$isInfs,$genders,$tell)
        {
		$aaaa = $GLOBALS['aaaa'];
		function flightZarfiat($parvaz)
		{
			$out = $parvaz->getZarfiat();
			return($out);
		}
		$out="auth_error";
		if(user_class::is_authonticated($enc_pass,$user))
		{
			$u = new user_class;
			$u->loadByUser($user);
			$customer = new customer_class($u->customer_id);
			$ticket_ids = array();
			$t = new ticket_class;
			$gender = explode(',',$genders);
			$fname = explode(',',$fnames);
			$lname = explode(',',$lnames);
			$isInf = explode(',',$isInfs);
			$tmp_id = explode(',',$tmp_ids);
			$tedad = 0;
			$jam_ghimat = 0;
			foreach($isInf as $inf)
				if($inf!=2)
					$tedad++;
			$shomare_last = array();
			$shomare_last_index = 0;
			$sanads = array();
			$qqq = null;
			$mysql = new mysql_class;
			$selectedParvaz = array();
			$mysql->ex_sql("select `id` ,parvaz_det_id from `reserve_tmp` where `id` in (".$tmp_ids.")",$qqq);
			foreach($qqq as $r)
			{
				$selectedParvaz[] = new  parvaz_det_class($r['parvaz_det_id']);
				$tmp_parvaz = $selectedParvaz[count($selectedParvaz)-1];
				if(flightZarfiat($tmp_parvaz) < $tedad)
					$tedad_ok = FALSE;
				$jam_ghimat += ($tedad * $tmp_parvaz->ghimat);
				$jam_ghimat += ($inf * $tmp_parvaz->ghimat)/10;
			}
			$ok = FALSE;
			$error = "parvaz expired";
			$etebar_ok = ($customer->max_amount >= $jam_ghimat);
			if($etebar_ok)
			{
				$domasire_ast = ((count($selectedParvaz)) == 2 && parvaz_det_class::check_raft_bargasht($selectedParvaz[0]->getId(),$selectedParvaz[1]->getId()));
				if($aaaa)
					var_dump($domasire_ast);
				$p_i = 0;
				foreach($selectedParvaz as $tt=>$parvaz)
				{
					if($p_i == 0)
						$error = "";
					$tmp_idi = $tmp_id[$tt];
					$adl = 0;
					$chd = 0;
					$inf = 0;
					$mysql  =new mysql_class;
					$sanad_record_id = 200;
					$mysql->ex_sql("select MAX(`sanad_record_id`) as `sss` from `customer_daryaft`",$q);
					if(isset($q[0]))
					{
						$sanad_record_id = (((int)$q[0]["sss"]>199)?(int)$q[0]["sss"]:199);
						$sanad_record_id ++;
					}
					$sanads[] = $sanad_record_id;
					$ok = TRUE;
					$jam_ghimat1 = 0;
					$index = 0;
					$adl_last = array();
					$chd_last = array();
					$inf_last = array();
					$ghimat_kharid = 0;
					$ticket_type = 0;
					$zarib = (100 - $customer->getPoorsant($parvaz->getId()))/100;
					foreach($gender as $i=>$g)
					{
						switch($isInf[$i])
						{
							case 0:
								$adl++;
								break;
							case 1:
								$chd++;
								break;
							case 2:
								$inf++;
								break;
						}
						$ticket = new ticket_class;
						$ticket->fname = '';
						$ticket->lname = $fname[$i] .' ' .$lname[$i];
						$ticket->tel = $tell;
						$ticket->adult = $isInf[$i];						
						$ticket->parvaz_det_id = $parvaz->getId();
						$ticket->mablagh = $parvaz->ghimat*$zarib;
						$ticket->poorsant = $customer->getPoorsant($parvaz->getId());
						$ticket->customer_id = $customer->getId();
						$ticket->user_id = (int)$u->id;
						$ticket->typ = $ticket_type;
						$ticket->gender = $gender[$i];
						$ticket->en = 1;
						$ticket->sanad_record_id = $sanad_record_id;				
						$j = 0;
						$shomare = -1;
						if($aaaa)
                                                	var_dump($p_i);
						if(($domasire_ast && $p_i == 0) || !$domasire_ast)
						{
							$ticket->shomare = $customer->decTicketCount();
							$shomare_last[] = $ticket->shomare;
						}
						else if($domasire_ast && $p_i>0)
						{
							$ticket->shomare = $shomare_last[$shomare_last_index];
							$shomare_last_index++;
						}
						$tttt = $ticket->add($tmp_id[$index],$noth);
						if(!$tttt)
							$error .= 'ticket registereation error...('.$tmp_id[$index].')';
						$ok = $ok and $tttt;
						$ghimat_kharid += $parvaz->mablagh_kharid;
						if($aaaa)
						{
							var_dump($ticket);
							var_dump($shomare_last);
						}
					}
					$mysql->ex_sqlx("delete from `reserve_tmp` where `id` = ".$tmp_id[$index]);
					$tedad = $adl+$chd;
					$jam_ghimat1 += $zarib*$tedad*$parvaz->ghimat+$zarib*$inf*$parvaz->ghimat/10;
					$index ++;
					$p_i++;
					$customer->buyTicket($sanad_record_id,$jam_ghimat1);
					if($parvaz->is_shenavar)
						parvaz_det_class::sanad_shenavar_kharid($parvaz,$adl+$chd,$sanad_record_id,$user_id);
				}
			}
			else
			{
				$ok = FALSE;
				$error = "etebar is less";
			}
			$out = $ok?"true|".implode(',',$sanads):"false|".$error;
		}
                return($out);
        }
	function reserve_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','tmp_id'=>'xsd:int','fnames'=>'xsd:string',
					'lnames'=>'xsd:string','is_inf'=>'xsd:string','genders'=>'xsd:string','tell'=>'xsd:string'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#reserve',
		  	        'rpc',
			        'encoded',
			        'book flight permanently ,please be aware , you should enter first names ,last names , and gender codes like this:'."\n".'fnames = "ali,muhammad,zohre"; lnames = "hasani,abbasi,rezaei"; is_inf= "0,1,2"; genders = "1,1,0"; . . .'
			)
		);
	}
	if($aaaa)
		var_dump(reserve('support',encrypt_class::encrypt('31048145'),'23,24','m1,m,a','m,e,m','0,1,2','1,0,1','09155193104'));
?>
