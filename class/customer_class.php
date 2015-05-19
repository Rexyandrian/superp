<?php
	class customer_class
	{
		private $id = -1;
		public $name = "";
		public $typ = -1;
		public $max_amount = 0;
		public $ticket_numbers = array();
		public $poor_id = -1;
		public $acc_id = -1;
		public $min_ticket = 0;
		public $max_ticket = 0;
		public $cod = '';
		public $can_sms = FALSE;
		public $protected = FALSE;
		public function __construct($id = -1)
		{
			$id = (int)$id;
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `customers` where `id` = '$id'",$q);
			if(isset($q[0]))
			{
				$r = $q[0];
				$this->id = $id;
				$this->name = $r["name"];
				$this->typ = (int)$r["typ"];
				$this->max_amount = (int)$r["max_amount"];
				try
				{
					if($r["ticket_numbers"]!="")
						$this->ticket_numbers = unserialize($r["ticket_numbers"]);
				}
				catch(Exception $e)
				{
					$this->ticket_numbers = array();
				}
				$this->poor_id = (int)$r["poor_id"];
				$this->acc_id = (int)$r["acc_id"];
				$this->min_ticket = (int)$r["min_ticket"];
				$this->max_ticket = (int)$r["max_ticket"];
				$this->cod = $r["cod"];
				$this->can_sms = (((int)$r['can_sms']==1)?TRUE:FALSE);
				$this->protected = (((int)$r['protected']==1)?TRUE:FALSE);
			}
		}
                public function incTicketNums($count = 1000)
                {
                        $newStart = 1000;
			$mysql = new mysql_class;
                        $mysql->ex_sql("select max(`max_ticket`) as `mticket` from `customers` where `en` = 1",$q);
                        if(isset($q[0]))
                                $newStart = (int)$q[0]['mticket'];
                        $newStart++;
                        $newEnd = $newStart+$count-1;
                        $mysql->ex_sqlx("update `customers` set `min_ticket` = $newStart , `max_ticket` = $newEnd where `id` = ".$this->id);
                }
                public function loadField($id,$field)
                {
                        $out = FALSE;
                        if((int)$id > 0 && is_array($field) && count($field) > 0)
                        {
                                $field_txt = '';
				$mysql = new mysql_class;
                                for($i = 0;$i < count($field);$i++)
                                        $field_txt .= '`'.$field[$i].'`'.(($i < count($field)-1)?',':'');
                                $mysql->ex_sql("select $field_txt from `customers` where `id` = $id",$q);
                                if(isset($q[0]))
                                {
					$r = $q[0];
                                        $this->id=$id;
                                        for($i = 0;$i < count($field);$i++)
                                        {
                                                $this->{$field[$i]} = $r[$field[$i]];
                                                $out[$field[$i]] = $r[$field[$i]];
                                        }
                                }
                        }
                        return($out);
                }
		public function getId()
		{
			return($this->id);
		}
		public function setPoorsant($parvaz_det,$poorsant,$user_id=-1)
		{
			$conf = new conf;
			if($user_id <=0 && isset($_SESSION[$conf->app."_user_id"]))
                                $user_id = (int)$_SESSION[$conf->app."_user_id"];
			$parvaz_det = (int)$parvaz_det;
			$poorsant = (int)$poorsant;
			$mysql = new mysql_class;
			$mysql->ex_sql("select `id` from `customer_parvaz` where `customer_id` = '".$this->id."' and `parvaz_det_id` = '$parvaz_det'",$q);
			if(isset($q[0]))
				$mysql->ex_sqlx("update `customer_parvaz` set `p_user_id` = '$user_id', `poorsant`='$poorsant' where `id` = '".(int)$q[0]["id"]."'");
			else
				$mysql->ex_sqlx("insert into `customer_parvaz` (`customer_id`,`parvaz_det_id`,`poorsant`,`p_user_id`) values ('".$this->id."','$parvaz_det','$poorsant','$user_id')"); 
		}
		public function getPoorsant($parvaz_det)
		{
			$out = 0;
			$parvaz_det = (int)$parvaz_det;
			$parvaz = new parvaz_det_class($parvaz_det);
			$out = $parvaz->poor_def;
			$mysql = new mysql_class;
			$mysql->ex_sql("select `poorsant` from `customer_parvaz` where `customer_id` = '".$this->id."' and `parvaz_det_id` = '$parvaz_det'",$q);
			if(isset($q[0]))
			{
				$r= $q[0];
				if((int)$r["poorsant"] > -1)
					$out = (int)$r["poorsant"];
			}
			return($out);
		}
		public function setZakhire($parvaz_det,$zakhire,$user_id=-1)
		{
			$conf = new conf;
			$parvaz_det = (int)$parvaz_det;
			$zakhire = (int)$zakhire;
			if($user_id <=0 && isset($_SESSION[$conf->app."_user_id"]))
				$user_id = (int)$_SESSION[$conf->app."_user_id"];
			$mysql = new mysql_class;
                        $mysql->ex_sql("select `id` from `customer_parvaz` where `customer_id` = '".$this->id."' and `parvaz_det_id` = '$parvaz_det'",$q);
                        if(isset($q[0]))
                                $mysql->ex_sqlx("update `customer_parvaz` set `zakhire`='$zakhire' where `id` = '".(int)$q[0]["id"]."'");
                        else
			{
				$mysql->ex_sqlx("INSERT INTO `new_reserve`.`log` (`id`, `toz`, `user_id`, `host`, `page_address`, `tarikh`, `typ`) VALUES (NULL, 'ذخیره سازی انجام شد', '', '', '', CURRENT_TIMESTAMP, '-1')");
                                $mysql->ex_sqlx("insert into `customer_parvaz` (`customer_id`,`parvaz_det_id`,`zakhire`,`poorsant`) values ('".$this->id."','$parvaz_det','$zakhire',-1)");
			}

		}
		public function getZakhire($parvaz_det)
		{
			$out = 0;
			$mysql = new mysql_class;
                        $mysql->ex_sql("select `zakhire` from `customer_parvaz` where `customer_id` = '".$this->id."' and `parvaz_det_id` = '$parvaz_det'",$q);
                        if(isset($q[0]))
                                $out = (int)$q[0]["zakhire"];
                        return($out);
		}
		public function ticketNumberExists($ticket_number)
		{
			$out = -1;
			$ticket_number = (int)$ticket_number;
			foreach($this->ticket_numbers as $i => $tmp)
				if($ticket_number == $this->ticket_numbers[$i])
					$out = $i;
			return($out);
		}
		public function addTicketNumber($startNumber,$stopNumber=-1)
		{
			$coun = 1;
			$mysql = new mysql_class;
			if($startNumber > 0 && $startNumber <= $stopNumber)
			{
				for($i = $startNumber;$i < $stopNumber;$i++)
				{
					if($this->ticketNumberExists($i)==-1)
					{
						$this->ticket_numbers[] = $i;
						$coun++;
					}
				}
			}
			else if($startNumber > 0 && $stopNumber == -1)
			{
				if($this->ticketNumberExists($startNumber)==-1)
					$this->ticket_numbers[] = $startNumber;
			}
			$mysql->ex_sqlx("update `customers` set `ticket_numbers` = '".serialize($this->ticket_numbers)."' where `id` = '".$this->id."'");

		}
		public function deleteTicketNumber($ticketNumber)
		{
			$mysql = new mysql_class;
			if($this->ticketNumberExists($ticketNumber)!=-1)
				unset($this->ticket_numbers[$this->ticketNumberExists($ticketNumber)]);
			$mysql->ex_sqlx("update `customers` set  `ticket_numbers` = '".serialize($this->ticket_numbers)."' where `id` = '".$this->id."'");
		}
		public function decTicketCount($co = 1)
		{
			$out = $this->min_ticket;
			$co = (int)$co;
			$mysql = new mysql_class;
			if($this->min_ticket+$co <= $this->max_ticket)
				$mysql->ex_sqlx("update `customers` set `min_ticket`=`min_ticket`+$co where `id` = '".$this->id."'");
			$this->min_ticket+=$co;
			return($out);
		}
		public function decTicketAmount($am = 0)
		{
			$am = (int)$am;
			$mysql = new mysql_class;
			$mysql->ex_sqlx("update `customers` set `max_amount`=`max_amount`-$am where `id` = '".$this->id."'");
		}
		public function daryaft($amount,$user_id,$tozihat,$typ,$tarikh = '')
		{
			$conf = new conf;
			$amount = (int)$amount;
			$user_id = (int)$user_id;
        	        $sanad_record_id = 200;
			$mysql = new mysql_class;
                	$mysql->ex_sql("select MAX(`sanad_record_id`) as `sss` from `customer_daryaft`",$q);
        	        if(isset($q[0])) 
	                {
				$r = $q[0];
                	        $sanad_record_id = (((int)$r["sss"]>199)?(int)$r["sss"]:199);
        	                $sanad_record_id ++;
	                }
                        $arg["toz"]="ثبت دریافتی به مبلغ 
			$amounti
										 جهت مشتری به نام
			 ".$this->name.' ,id='.$this->id;
                        $arg["user_id"]=$_SESSION[$conf->app."_user_id"];
                        $arg["host"]=$_SERVER["REMOTE_ADDR"];
                        $arg["page_address"]=$_SERVER["SCRIPT_NAME"];
                        $arg["typ"]=9;
                        log_class::add($arg);
			$mysql->ex_sqlx("update `customers` set `max_amount`=`max_amount`+$amount where `id` = '".$this->id."'");
			if($tarikh == '')
				$mysql->ex_sqlx("insert into `customer_daryaft` (`customer_id`,`user_id`,`mablagh`,`tozihat`,`typ`,`sanad_record_id`) values ('".$this->id."','$user_id','$amount','$tozihat','$typ','$sanad_record_id')");
			else
				$mysql->ex_sqlx("insert into `customer_daryaft` (`customer_id`,`user_id`,`mablagh`,`tozihat`,`tarikh`,`typ`,`sanad_record_id`) values ('".$this->id."','$user_id','$amount','$tozihat','$tarikh','$typ','$sanad_record_id')");
		}
		public function buyTicket($sanad_record_id,$amount,$etebari = TRUE)
		{
			$conf = new conf;
			$mysql = new mysql_class;
			$user_id = isset($_SESSION[$conf->app."_user_id"])?(int)$_SESSION[$conf->app."_user_id"]:-1;
			$mysql->ex_sqlx("insert into `customer_daryaft` (`customer_id`,`user_id`,`mablagh`,`sanad_record_id`,`typ`,`tozihat`) values ('".$this->id."','$user_id','$amount','$sanad_record_id,',-1,'$sanad_record_id')");
			if($etebari)
				$this->decTicketAmount($amount);
		}
		public function esterdad($ticket_id,$esterdad_darsad=0)
		{
			$conf = new conf;
			$user_id = $_SESSION[$conf->app.'_user_id'];
			$customer_id = $_SESSION[$conf->app.'_customer_id'];
			$sanad_record_id = 199;
			$mysql = new mysql_class;
			$mysql->ex_sql('select max(`sanad_record_id`) as `sanad` from `customer_daryaft` ',$qq);
			if(isset($qq[0]))
				$sanad_record_id = (int)$qq[0]['sanad'];
			$sanad_record_id++;
			$ticket = new ticket_class((int)$ticket_id);
			$mablagh =( $ticket->mablagh * (1-$ticket->poorsant/100) ) * (1-$esterdad_darsad/100) ;
                        $arg["toz"]="استرداد بلیت شماره ".$ticket->shomare;
                        $arg["user_id"]=$_SESSION[$conf->app."_user_id"];
                        $arg["host"]=$_SERVER["REMOTE_ADDR"];
                        $arg["page_address"]=$_SERVER["SCRIPT_NAME"];
                        $arg["typ"]=7;
                        log_class::add($arg);
			$ticket->esterdad();//-------بلیط باطل شد
			$mysql->ex_sqlx("insert into `customer_daryaft`  (`customer_id`, `user_id`, `mablagh`,`tozihat`, `sanad_record_id`, `typ`, `sanad_typ`) values ('$customer_id','$user_id','$mablagh','$ticket_id','$sanad_record_id','1','-1') ");
			if($ticket->adult<2)
			{
				$parvaz = new parvaz_det_class($ticket->parvaz_det_id);
				$parvaz->resetZarfiat(1);
			}
		}
		public function deleteTicket($ticket_id)
		{
			$conf = new conf;
                        $ticket = new ticket_class((int)$ticket_id);
			$sanad_record_id = $ticket->sanad_record_id;
			$parvaz = new parvaz_det_class($ticket->parvaz_det_id);
			$today = date("Y-m-d H:i:s");
			$out = FALSE;
			if($today < $parvaz->tarikh." ".$parvaz->saat)
			{
				$mysql = new mysql_class;
				$mablagh = $ticket->mablagh * (1 - $ticket->poorsant/100);
				$arg["toz"]="حذف کامل بلیت شماره ".$ticket->shomare;
				$arg["toz"] .='از پرواز شماره '.$parvaz->shomare.' تاریخ '.(audit_class::hamed_pdate($parvaz->tarikh));
	                        $arg["user_id"]=$_SESSION[$conf->app."_user_id"];
        	                $arg["host"]=$_SERVER["REMOTE_ADDR"];
                	        $arg["page_address"]=$_SERVER["SCRIPT_NAME"];
                        	$arg["typ"]=8;
				log_class::add($arg);
				$mysql->ex_sql("select `id`,`mablagh` from `customer_daryaft` where `sanad_record_id` = $sanad_record_id",$q);
				if(isset($q[0]))
					if((int)$q[0]["mablagh"] == $mablagh)
						$mysql->ex_sqlx("delete from `customer_daryaft` where `id` = ".(int)$q[0]["id"]);
				$mysql->ex_sqlx("update `customer_daryaft` set `mablagh`=`mablagh`-$mablagh where `sanad_record_id` = $sanad_record_id");
				$mysql->ex_sqlx("delete from `ticket` where `id` = ".$ticket->getId());
                        	if($ticket->adult<2)
        	                        $parvaz->resetZarfiat(1);
				$out = TRUE;
			}
			return($out);
		}
		public function pardakht($sanad_record_id,$custumer_id,$mablagh,$tozihat,$user_id)
		{
			$tarikh = date("Y-m-d H:i:s");
			$mysql = new mysql_class;
			if($mablagh!=0)
				$mysql->ex_sqlx("insert into `customer_daryaft` (`customer_id`, `user_id`, `mablagh`, `tarikh`, `tozihat`, `sanad_record_id`, `typ`) values ('$custumer_id','$user_id','$mablagh','$tarikh','$tozihat','$sanad_record_id','1') ");
		}
	}
?>
