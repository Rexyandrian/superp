<?php
	class ticket_class
	{
		private $id = -1;
		public $fname = "";
                public $lname = "";
		public $tel = "";
		public $adult = 0;
		public $sanad_record_id = -1;
		public $parvaz_det_id = -1;
		public $customer_id = -1;
		public $user_id = -1;
		public $shomare = -1;
		public $typ = 1;
		public $en = 0;
		public $regtime = "";
		public $mablagh = 0;
		public $poorsant = 0;
		public $gender = 1;
		public function __construct($id = -1)
		{
			$id = (int)$id;
			mysql_class::ex_sql("select * from `ticket` where `id` = '$id'",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id = $id;
				$this->fname = $r["fname"];
				$this->lname = $r["lname"];
				$this->tel = $r["tel"];
				$this->adult = (int)$r["adult"];
				$this->sanad_record_id = (int)$r["sanad_record_id"];
				$this->parvaz_det_id = (int)$r["parvaz_det_id"];
				$this->customer_id = (int)$r["customer_id"];
				$this->user_id = (int)$r["user_id"];
				$this->shomare = (int)$r["shomare"];
				$this->typ = (int)$r["typ"];
				$this->en = (int)$r["en"];
				$this->regtime = $r["regtime"];
				$this->mablagh = (int)$r["mablagh"];
				$this->poorsant = (int)$r["poorsant"];
				$this->gender = (int)$r["gender"];
			}
		}
		public function getId()
		{
			return($this->id);
		}
		public function add($tmp_id,&$ticket_id)
		{
			$tmp_id= (int)$tmp_id;
			$out = FALSE;
			mysql_class::ex_sql("select `id` from `ticket` where `fname`='".$this->fname."' and `lname`='".$this->lname."' and `tel`='".$this->tel."' and `parvaz_det_id`='".$this->parvaz_det_id."' and `en`='".$this->en."' and `mablagh` = '".$this->mablagh."' and `poorsant` = '".$this->poorsant."' and `shomare` = '".$this->shomare."' and `gender` = ".$this->gender,$q);
			if(mysql_num_rows($q)==0 && $this->lname!="")
			{
				$mablagh = $this->mablagh;
				if($this->adult == 2)
					$mablagh = (int)$mablagh /10;
				$arg["toz"]="ثبت بلیت به شماره ".$this->shomare." کد رهگیری ".$this->rahgiriToCode($this->sanad_record_id,conf::rahgiri);
		                $arg["user_id"]=$_SESSION[conf::app."_user_id"];
		                $arg["host"]=$_SERVER["REMOTE_ADDR"];
		                $arg["page_address"]=$_SERVER["SCRIPT_NAME"];
				$arg["typ"]=3;
		                log_class::add($arg);
				$con = mysql_class::ex_sqlx("insert into ticket (`fname`,`lname`,`tel`,`adult`,`sanad_record_id`,`parvaz_det_id`,`customer_id`,`user_id`,`shomare`,`typ`,`en`,`mablagh`,`poorsant`,`gender`) values ('".$this->fname."','".$this->lname."','".$this->tel."','".$this->adult."','".$this->sanad_record_id."','".$this->parvaz_det_id."','".$this->customer_id."','".$this->user_id."','".$this->shomare."','".$this->typ."','".$this->en."','$mablagh','".$this->poorsant."',".$this->gender.")",FALSE);
				$ticket_id = mysql_insert_id($con);
				mysql_close($con);
				//------------sms------------------
				$cust_sms = new customer_class($this->customer_id);
				if(sms_class::isMobile($this->tel) && $cust_sms->can_sms)
				{
					$sms_parvaz = new parvaz_det_class($this->parvaz_det_id);
					$sms_msg="ازخریدشمامتشکریم\nپرواز:".$sms_parvaz->shomare."\n".audit_class::hamed_pdate($sms_parvaz->tarikh)."\nرهگیری:".$this->rahgiriToCode($this->sanad_record_id,conf::rahgiri)."\n".$cust_sms->name;
					sms_class::sendSms($sms_msg,array("$this->tel"),(int)$_SESSION[conf::app.'_user_id'],$this->sanad_record_id);
				}
				//---------------------------------
				//$out =(($ok=="ok")?TRUE:FALSE);
				$out = TRUE;
			}
			return $out;
		}
		public function clearTickets($def = 5)
		{
			$tarikh = date("Y-m-d H:i:s");
			mysql_class::ex_sqlx("update `parvaz_det` set `zarfiat`=`zarfiat`+(select SUM(`zarfiat`) from `reserve_tmp`  where `parvaz_det_id` = `parvaz_det`.`id` and `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))  where `id` in (select `parvaz_det_id` from `reserve_tmp`  where (not(`tedad` is null))  and `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))");
			mysql_class::ex_sqlx("update `customer_parvaz` set `zakhire`=`zakhire`+(select SUM(`zakhire`) from `reserve_tmp`  where `customer_id` = `customer_parvaz`.`customer_id` and `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))  where `parvaz_det_id` in (select `parvaz_det_id` from `reserve_tmp`  where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute)) and `customer_id` in (select `customer_id` from `reserve_tmp`  where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))");
			mysql_class::ex_sqlx("delete from `reserve_tmp` where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute)");
//----------------------پاک کردن ظرفیت مهلت دار--
			mysql_class::ex_sqlx("update  `customer_parvaz` set `zakhire` = 0 , regtime = '0000-00-00 00:00:00'  WHERE `regtime` + interval `deadtime` minute < '$tarikh' and `deadtime` > 0");
/*
			$def = (int)$def;
//-----------------------پاک کردن بلیط موقت -----
			mysql_class::ex_sqlx("update `parvaz_det` set `zarfiat`=`zarfiat`+(select SUM(`zarfiat`) from `reserve_tmp`  where `parvaz_det_id` = `parvaz_det`.`id`)  where `id` in (select `parvaz_det_id` from `reserve_tmp`  where (not(`tedad` is null))  and `tarikh` <= now() - interval $def minute)");
			mysql_class::ex_sqlx("update `customer_parvaz` set `zakhire`=`zakhire`+(select SUM(`zakhire`) from `reserve_tmp`  where `customer_id` = `customer_parvaz`.`customer_id`)  where `parvaz_det_id` in (select `parvaz_det_id` from `reserve_tmp`  where `tarikh` <= now() - interval $def minute) and `customer_id` in (select `customer_id` from `reserve_tmp`  where `tarikh` <= now() - interval $def minute)");
			mysql_class::ex_sqlx("delete from `reserve_tmp` where `tarikh` <= now() - interval $def minute");
//----------------------پاک کردن ظرفیت مهلت دار--
			mysql_class::ex_sqlx("update  `customer_parvaz` set `zakhire` = 0 , regtime = '0000-00-00 00:00:00' , `deadtime` = 0 WHERE `regtime` + interval `deadtime` minute < now() and `deadtime` > 0");
*/
			
		}
		public function updateTmp($tmp_id,$info=null)
		{
			$out = FALSE;
			$tmp_id = (int)$tmp_id;
			mysql_class::ex_sql("select `parvaz_det_id` from `reserve_tmp` where `id` = $tmp_id",$q);
			if($r = mysql_fetch_array($q))
			{
				$p = new parvaz_det_class($r['parvaz_det_id']);
				$i = array("parvaz"=>$p,"info"=>$info);
				$si = serialize($i);
				mysql_class::ex_sqlx("update `reserve_tmp` set `info` = '$si' where `id` = $tmp_id");
				$out = TRUE;
			}
			return($out);
		}
		public function addTmp($parvaz_det_id,$tedad,$timeout,$customer_id = -1)
		{
			if($customer_id <=0 && isset($_SESSION[conf::app."_customer_id"]))
				$customer_id = (int)$_SESSION[conf::app."_customer_id"];
			$cust = new customer_class($customer_id);
			$out = -1;
			$parvaz_det = new parvaz_det_class((int)$parvaz_det_id);
			$free_zarfiat = $parvaz_det->getZarfiat(-1);
			$zakhire = 0;
			$zarfiat = 0;
			if($free_zarfiat<$tedad)
				$zakhire = $tedad - $free_zarfiat;
			$zarfiat = $tedad;
			$arg["toz"]="ثبت موقت برای پرواز شماره 
			".$parvaz_det->shomare.' تاریخ '.(audit_class::hamed_pdate($parvaz_det->tarikh))."
								 به تعداد
			$tedad
								 از طریق".
			$cust->name ;
                        $arg["user_id"]=$_SESSION[conf::app."_user_id"];
                        $arg["host"]=$_SERVER["REMOTE_ADDR"];
                        $arg["page_address"]=$_SERVER["SCRIPT_NAME"];
			$arg["typ"]=4;
                        log_class::add($arg);
			$tarikh = date("Y-m-d H:i:s");
			$con = mysql_class::ex_sqlx("insert into `reserve_tmp` (`parvaz_det_id`,`tedad`,`customer_id`,`zakhire`,`zarfiat`,`tarikh`,`timeout`) values ('$parvaz_det_id','$tedad','$customer_id','$zakhire','$zarfiat','$tarikh','$timeout') ",FALSE);
			$out =mysql_insert_id($con);
			mysql_close($con);
			return $out;
		}
                public function removeTmp($tmp)
		{
			for($i = 0;$i < count($tmp);$i++)
			{
				mysql_class::ex_sqlx("delete from `reserve_tmp` where `id` = '".$tmp[$i]."'");
			}	
		}
		public function rahgiriToCode($rahgiri,$salt='')
		{
			$rahgiri = (int)$rahgiri;
			$out = dechex($rahgiri);
			return($salt.$out);
		}
		public function codeToRahgiri($code,$salt='')
		{
			$out = 0;
			$code = substr($code,strlen($salt),strlen($code));
			$out = hexdec($code);
			return($out);
		}
		public function loadBargasht($inp = -1)
	        {
			if($inp <= 0)
				$inp = $this->id;
        	        $out = -1;
                	$inp = (int)$inp;
	                $ticket = new ticket_class($inp);
//`id`, `fname`, `lname`, `tel`, `adult`, `sanad_record_id`, `parvaz_det_id`, `customer_id`, `user_id`, `shomare`, `typ`, `en`, `regtime`, `mablagh`, `poorsant`
        	        mysql_class::ex_sql("select `id` from `ticket` where `parvaz_det_id` <> '".$ticket->parvaz_det_id."' and `shomare` = '".$ticket->shomare."' and `regtime`>='".$ticket->regtime."' and `regtime`<DATE_ADD('".$ticket->regtime."',interval 1 minute) ",$q);
                	if($r = mysql_fetch_array($q))
	                {
                	        $out = (int)$r['id'];
	                }
        	        return($out);   
	        }
		public 	function esterdad()
		{
			$this->en = -1;
			mysql_class::ex_sqlx("update `ticket` set `en` = -1 where `id` = ".$this->id);
		}
	}
?>
