<?php
	class parvaz_det_class
	{
		private $id = -1;
		public $parvaz_id = -1;
		public $tarikh = "";
		public $saat = "";
		public $zarfiat = 0;
		public $ghimat = 0;
		public $typ = 0;
		public $zakhire = 0;
		public $j_id = -1;
		public $poor_def = 0;
		public $mabda_id = -1;
		public $maghsad_id = -1;
		public $shomare = -1;
		public $havapeima_id="";
		public $sherkat_id = -1;
		public $can_esterdad = 1;
		public $mablagh_kharid = 0;
		public $customer_id = -1;
		public $is_shenavar = FALSE;
                public function miniTime($inp)
                {
			$tmp = explode(':',$inp);
			$out = $inp;
			if(count($tmp) == 3)
	                        $out = $tmp[0].':'.$tmp[1];
			return($out);
                }
		public function __construct($id = -1)
		{
			$id = (int)$id;
			mysql_class::ex_sql("select * from `parvaz_det` where `id` ='$id'",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id = $id;
				$this->parvaz_id = (int)$r["parvaz_id"];
				$this->tarikh = $r["tarikh"];
				$this->saat = $this->miniTime($r["saat"]);
				$this->zarfiat = (int)$r["zarfiat"];
				$this->ghimat = (int)$r["ghimat"];
				$this->typ = (int)$r["typ"];
				$this->zakhire = (int)$r["zakhire"];
				$this->j_id = (int)$r["j_id"];
				$this->poor_def = (int)$r["poor_def"];
				$parvaz = new parvaz_class((int)$r["parvaz_id"]);
				$this->mablagh_kharid = (int)$r["mablagh_kharid"];
				$this->customer_id = (int)$r["customer_id"];
				$this->mabda_id = $parvaz->mabda_id;
				$this->maghsad_id = $parvaz->maghsad_id;
                                $this->shomare = $parvaz->shomare;
				$this->havapeima_id = $parvaz->havapiema_id;
				$this->sherkat_id = $parvaz->sherkat_id;
				$this->can_esterdad = (int)$r["can_esterdad"];
				$this->is_shenavar = $parvaz->is_shenavar;
			}
		}
		public function getId()
		{
			return($this->id);
		}
		public function setZarfiat($zarfiat,$customer_id = -1)
		{
			if($customer_id == -1 && isset($_SESSION[conf::app."_customer_id"]))
				$customer_id = (int)$_SESSION[conf::app."_customer_id"];
			if($customer_id >0)
			{
				$customer = new customer_class($customer_id);

				$customer_zarfiat = $this->getZarfiat($customer_id);
				$free_zarfiat = $this->getZarfiat();
				$requested_zarfiat = $zarfiat;
				$customer_zakhire = $customer_zarfiat - $free_zarfiat;
				$total_zarfiat = $this->zarfiat;
				$this->zarfiat -= $requested_zarfiat;
				if($free_zarfiat < $requested_zarfiat)
				{
					$customer_zakhire -= ($requested_zarfiat-$free_zarfiat);
				}
				mysql_class::ex_sqlx("update `parvaz_det` set `zarfiat` = '".$this->zarfiat."'  where `id` = '".$this->id."'");
				if((int)$customer_zakhire!=0)
					$customer->setZakhire($this->id,$customer_zakhire);
/*
				if($p_zarfiat >= $zarfiat)
				{
					if($zarfiat > $this->getZarfiat())
					{
						
					}
					$this->zarfiat -= $zarfiat;
					$zakhire = $zakhire - ($zarfiat - $this->zarfiat);
					mysql_class::ex_sqlx("update `parvaz_det` set `zarfiat` = '".$this->zarfiat."'  where `id` = '".$this->id."'");
					$customer->setZakhire($this->id,$zakhire);
				}
*/
			}
		}
		public function resetZarfiat($zarfiat,$customer_id = -1)
		{
                        if($customer_id == -1 && isset($_SESSION[conf::app."_customer_id"]))
                                $customer_id = (int)$_SESSION[conf::app."_customer_id"];
                        if($customer_id >0)
                        {
                                $customer = new customer_class($customer_id);

                                $customer_zarfiat = $this->getZarfiat($customer_id);
                                $free_zarfiat = $this->getZarfiat();
                                $requested_zarfiat = $zarfiat;
                                $customer_zakhire = $customer_zarfiat - $free_zarfiat;
                                $total_zarfiat = $this->zarfiat;
                                $this->zarfiat += $requested_zarfiat;
                                if($free_zarfiat < $requested_zarfiat)
                                {
                                        $customer_zakhire += ($requested_zarfiat-$free_zarfiat);
                                }
                                mysql_class::ex_sqlx("update `parvaz_det` set `zarfiat` = '".$this->zarfiat."'  where `id` = '".$this->id."'");
				//if((int)$customer_zakhire!=0)
		                        //$customer->setZakhire($this->id,$customer_zakhire);
			}
		}
		public function getZarfiat($customer_id = -1)
		{
			$customer_id = (int)$customer_id;
			$out = 0;
			$zakhire_kol = 0;
			mysql_class::ex_sql("select SUM(`zakhire`) as `zakh` from `customer_parvaz` where `parvaz_det_id` = '".$this->id."'",$q);
			if($r = mysql_fetch_array($q))
			{
				$zakhire_kol = (int)$r["zakh"];
			}
			$zakhire = 0;
			$query = "select `zakhire` from `customer_parvaz` where `parvaz_det_id` = '".$this->id."' and `customer_id` = '$customer_id'";
			$q = null;
			mysql_class::ex_sql($query,$q);
			if($r = mysql_fetch_array($q))
			{
				$zakhire = (int)$r["zakhire"];
			}
			if($customer_id == -1)
				$out = $this->zarfiat - $zakhire_kol;
			else
				$out = $this->zarfiat - $zakhire_kol + $zakhire;
			return($out);
		}
                public function check_raft_bargasht($p1,$p2)
                {
                        $out = FALSE;
                        $p1 = new parvaz_det_class((int)$p1);
                        $p2 = new parvaz_det_class((int)$p2);
                        if( $p1->mabda_id==$p2->maghsad_id )
                        {
                                $out = TRUE;
                        }
                        return $out;
                }
		public function kharidParvaz($tedad,$toz='')
		{
	                $sanad_record_id = 200;
        	        mysql_class::ex_sql("select MAX(`sanad_record_id`) as `sss` from `customer_daryaft`",$q);
                	if($r = mysql_fetch_array($q))
        	        {
	                        $sanad_record_id = (((int)$r["sss"]>199)?(int)$r["sss"]:199);
                	        $sanad_record_id ++;
	                }
			$toz = (($toz=='')?"بابت خرید $tedad بلیت از پرواز ".$this->shomare." تاریخ ".audit_class::hamed_pdate($this->tarikh)." ساعت ".$this->saat:$toz);
			$user_id = $_SESSION[conf::app.'_user_id'];
			if($this->mablagh_kharid!=0)
				mysql_class::ex_sqlx('insert into `customer_daryaft` (`customer_id`, `user_id`, `mablagh`, `tozihat`, `sanad_record_id`, `typ`,`sanad_typ`) values('.$this->customer_id.','.$user_id.','.($tedad*$this->mablagh_kharid).',\''.$toz.'\','.$sanad_record_id.',1,2)');
		}
		public function sanad_shenavar_kharid($parvaz,$tedad,$sanad_record_id,$user_id)
		{
			$toz = "بابت خرید $tedad بلیت از پرواز ".$parvaz->shomare." تاریخ ".audit_class::hamed_pdate($parvaz->tarikh)." ساعت ".$parvaz->saat.' بصورت شناور';
			if($parvaz->mablagh_kharid!=0)
				mysql_class::ex_sqlx('insert into `customer_daryaft` (`customer_id`, `user_id`, `mablagh`, `tozihat`, `sanad_record_id`, `typ`,`sanad_typ`) values('.$parvaz->customer_id.','.$user_id.','.($tedad*$parvaz->mablagh_kharid).',\''.$toz.'\','.$sanad_record_id.',1,2)');
		}
		public function loadJid($id = -1)
		{
			$id = (int)$id;
			if($id<=0)
				$id = $this->id;
			$out = null;
			mysql_class::ex_sql("select * from `parvaz_jid` where `parvaz_det_id` = $id",$q);
			while($r = mysql_fetch_array($q))
				$out[] = (int)$r['jid'];
			return($out);
		}
                public function loadField($id,$field)
                {
                        $out = FALSE;
                        if((int)$id > 0 && is_array($field) && count($field) > 0)
                        {
                                $field_txt = '';
                                for($i = 0;$i < count($field);$i++)
                                        $field_txt .= '`'.$field[$i].'`'.(($i < count($field)-1)?',':'');
                                mysql_class::ex_sql("select $field_txt from `parvaz_det` where `id` = $id",$q);
                                if($r = mysql_fetch_array($q))
                                {
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
	}
?>
