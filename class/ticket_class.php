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
		public $tour_mablagh = 0;
		public $poorsant = 0;
		public $gender = 1;
		public $email_addr = '';
		public $sites_id = -1;
		public function __construct($id = -1)
		{
			$id = (int)$id;
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `ticket` where `id` = '$id'",$q);
			if(isset($q[0]))
			{
				$this->id = $id;
				$this->fname = $q[0]["fname"];
				$this->lname = $q[0]["lname"];
				$this->tel = $q[0]["tel"];
				$this->adult = (int)$q[0]["adult"];
				$this->sanad_record_id = (int)$q[0]["sanad_record_id"];
				$this->parvaz_det_id = (int)$q[0]["parvaz_det_id"];
				$this->customer_id = (int)$q[0]["customer_id"];
				$this->user_id = (int)$q[0]["user_id"];
				$this->shomare = (int)$q[0]["shomare"];
				$this->typ = (int)$q[0]["typ"];
				$this->en = (int)$q[0]["en"];
				$this->regtime = $q[0]["regtime"];
				$this->mablagh = (int)$q[0]["mablagh"];
				$this->tour_mablagh = (int)$q[0]["tour_mablagh"];
				$this->poorsant = (int)$q[0]["poorsant"];
				$this->gender = (int)$q[0]["gender"];
				$this->email_addr = trim($q[0]["email_addr"]);
				$this->sites_id = (int)$q[0]["sites_id"];
                                $this->code_melli = $q[0]["code_melli"];
			}
		}
		public function getId()
		{
			return($this->id);
		}
		public function add($tmp_id,&$ticket_id)
		{
			$mysql = new mysql_class;
			$conf = new conf;
			$tmp_id= (int)$tmp_id;
			$out = FALSE;
			$mysql->ex_sql("select `id` from `ticket` where `fname`='".$this->fname."' and `lname`='".$this->lname."' and `tel`='".$this->tel."' and `parvaz_det_id`='".$this->parvaz_det_id."' and `en`='".$this->en."' and `mablagh` = '".$this->mablagh."' and `poorsant` = '".$this->poorsant."' and `shomare` = '".$this->shomare."' and `gender` = ".$this->gender." and email_addr = '".$this->email_addr."' and sites_id='".$this->sites_id."' and code_melli='".$this->code_melli."'",$q);
			if(count($q)==0 && $this->lname!="")
			{
				$mablagh = $this->mablagh;
				if($this->adult == 2)
					$mablagh = (int)$mablagh /10;
				$arg["toz"]="ثبت بلیت به شماره ".$this->shomare." کد رهگیری ".$this->rahgiriToCode($this->sanad_record_id,$conf->rahgiri);
		                $arg["user_id"]=(isset($_SESSION)?$_SESSION[$conf->app."_user_id"]:-1);
		                $arg["host"]=$_SERVER["REMOTE_ADDR"];
		                $arg["page_address"]=$_SERVER["SCRIPT_NAME"];
				$arg["typ"]=3;
		                log_class::add($arg);
				$hala = date("Y-m-d H:i:s");
				$con = $mysql->ex_sqlx("insert into ticket (`fname`,`lname`,`tel`,`adult`,`sanad_record_id`,`parvaz_det_id`,`customer_id`,`user_id`,`shomare`,`typ`,`en`,`mablagh`,`poorsant`,`gender`,`regtime`,email_addr,sites_id,code_melli) values ('".$this->fname."','".$this->lname."','".$this->tel."','".$this->adult."','".$this->sanad_record_id."','".$this->parvaz_det_id."','".$this->customer_id."','".$this->user_id."','".$this->shomare."','".$this->typ."','".$this->en."','$mablagh','".$this->poorsant."',".$this->gender.",'$hala','".$this->email_addr."','".$this->sites_id."','".$this->code_melli."')",FALSE);
				$ticket_id = $mysql->insert_id($con);
				$mysql->close($con);
				$out = TRUE;
				//------------sms------------------
				/*
				$cust_sms = new customer_class($this->customer_id);
				
				if(sms_class::isMobile($this->tel) && $cust_sms->can_sms)
				{
					$sms_parvaz = new parvaz_det_class($this->parvaz_det_id);
					$sms_msg="ازخریدشمامتشکریم\nپرواز:".$sms_parvaz->shomare."\n".audit_class::hamed_pdate($sms_parvaz->tarikh)."\nرهگیری:".$this->rahgiriToCode($this->sanad_record_id,$conf->rahgiri)."\n".$cust_sms->name;
					sms_class::sendSms($sms_msg,array("$this->tel"),(int)$_SESSION[$conf->app.'_user_id'],$this->sanad_record_id);
				}*/
				//---------------------------------
				//$out =(($ok=="ok")?TRUE:FALSE);
				
			}
			return $out;
		}
		public function clearTickets($def = 5)
		{
			$mysql = new mysql_class;
			$tarikh = date("Y-m-d H:i:s");
			$mysql->ex_sql("select `zarfiat`,`parvaz_det_id`,`id` from `reserve_tmp` where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute) and not(`tedad` is null) ",$q);
			$p_det = array();
			foreach($q as $r)
			{
				$mysql->ex_sqlx("update `parvaz_det` set `zarfiat`=`zarfiat`+".((int)$r['zarfiat'])." where `id`='".$r['parvaz_det_id']."' ");
				$mysql->ex_sqlx("delete from `reserve_tmp` where `id`='".$r['id']."'");
			}
			$mysql->ex_sqlx("update  `customer_parvaz` set `zakhire` = 0 , regtime = '0000-00-00 00:00:00'  WHERE `regtime` + interval `deadtime` minute < '$tarikh' and `deadtime` > 0");
			/*
			$mysql->ex_sqlx("update `parvaz_det` set `zarfiat`=`zarfiat`+(select SUM(`zarfiat`) from `reserve_tmp`  where `parvaz_det_id` = `parvaz_det`.`id` and `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))  where `id` in (select `parvaz_det_id` from `reserve_tmp`  where (not(`tedad` is null))  and `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))");
			$mysql->ex_sqlx("update `customer_parvaz` set `zakhire`=`zakhire`+(select SUM(`zakhire`) from `reserve_tmp`  where `customer_id` = `customer_parvaz`.`customer_id` and `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))  where `parvaz_det_id` in (select `parvaz_det_id` from `reserve_tmp`  where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute)) and `customer_id` in (select `customer_id` from `reserve_tmp`  where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))");
			$mysql->ex_sqlx("delete from `reserve_tmp` where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute)");
//----------------------پاک کردن ظرفیت مهلت دار--
			$mysql->ex_sqlx("update  `customer_parvaz` set `zakhire` = 0 , regtime = '0000-00-00 00:00:00'  WHERE `regtime` + interval `deadtime` minute < '$tarikh' and `deadtime` > 0");
			*/
		}
		public function updateTmp($tmp_id,$info=null)
		{
			$mysql = new mysql_class;
			$out = FALSE;
			$tmp_id = (int)$tmp_id;
			$mysql->ex_sql("select `parvaz_det_id` from `reserve_tmp` where `id` = $tmp_id",$q);
			if(isset($q[0]))
			{
				$p = new parvaz_det_class($q[0]['parvaz_det_id']);
				$i = array("parvaz"=>$p,"info"=>$info);
				$si = serialize($i);
				$mysql->ex_sqlx("update `reserve_tmp` set `info` = '$si' where `id` = $tmp_id");
				$out = TRUE;
			}
			return($out);
		}
		public function addTmp($parvaz_det_id,$tedad,$timeout,$netlog,$rwaitlog,$customer_id = -1)
		{
			$mysql = new mysql_class;
			$conf = new conf;
			if($customer_id <=0)
				if(isset($_SESSION[$conf->app."_customer_id"]))
					$customer_id = (int)$_SESSION[$conf->app."_customer_id"];
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
                        $arg["user_id"]=isset($_SESSION)?$_SESSION[$conf->app."_user_id"]:-1;
                        $arg["host"]=$_SERVER["REMOTE_ADDR"];
                        $arg["page_address"]=$_SERVER["SCRIPT_NAME"];
			$arg["typ"]=4;
                        log_class::add($arg);
			$tarikh = date("Y-m-d H:i:s");
			$con = $mysql->ex_sqlx("insert into `reserve_tmp` (`parvaz_det_id`,`tedad`,`customer_id`,`zakhire`,`zarfiat`,`tarikh`,`timeout`,`netlog`,`rwaitlog`) values ('$parvaz_det_id','$tedad','$customer_id','$zakhire','$zarfiat','$tarikh','$timeout','$netlog','$rwaitlog') ",FALSE);
			$out =$mysql->insert_id($con);
			$mysql->close($con);
			return $out;
		}
                public function removeTmp($tmp)
		{
			$mysql = new mysql_class;
			$t = (is_array($tmp) && count($tmp)>0)?implode(',',$tmp):$tmp;
			$mysql->ex_sqlx("delete from `reserve_tmp` where `id` in ($t)");
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
			$mysql = new mysql_class;
			if($inp <= 0)
				$inp = $this->id;
        	        $out = -1;
                	$inp = (int)$inp;
	                $ticket = new ticket_class($inp);
//`id`, `fname`, `lname`, `tel`, `adult`, `sanad_record_id`, `parvaz_det_id`, `customer_id`, `user_id`, `shomare`, `typ`, `en`, `regtime`, `mablagh`, `poorsant`
        	        $mysql->ex_sql("select `id` from `ticket` where `parvaz_det_id` <> '".$ticket->parvaz_det_id."' and `shomare` = '".$ticket->shomare."' and `regtime`>='".$ticket->regtime."' and `regtime`<DATE_ADD('".$ticket->regtime."',interval 1 minute) ",$q);
                	if(isset($q[0]))
	                {
                	        $out = (int)$q[0]['id'];
	                }
        	        return($out);   
	        }
		public 	function esterdad()
		{
			$mysql = new mysql_class;
			$this->en = -1;
			$mysql->ex_sqlx("update `ticket` set `en` = -1 where `id` = ".$this->id);
		}
	}
?>
