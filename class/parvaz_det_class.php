<?php
	class parvaz_det_class
	{
		public $id = -1;
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
		public $tour_mablagh= 0;
//		public $toz = '';
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
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `parvaz_det` where `id` ='$id'",$q);
			if(isset($q[0]))
			{
				$this->id = $id;
				$this->parvaz_id = (int)$q[0]["parvaz_id"];
				$this->tarikh = $q[0]["tarikh"];
				$this->saat = $this->miniTime($q[0]["saat"]);
				$this->zarfiat = (int)$q[0]["zarfiat"];
				$this->ghimat = (int)$q[0]["ghimat"];
				$this->typ = (int)$q[0]["typ"];
				$this->zakhire = (int)$q[0]["zakhire"];
				$this->j_id = (int)$q[0]["j_id"];
				$this->poor_def = (int)$q[0]["poor_def"];
				$parvaz = new parvaz_class((int)$q[0]["parvaz_id"]);
				$this->mablagh_kharid = (int)$q[0]["mablagh_kharid"];
				$this->customer_id = (int)$q[0]["customer_id"];
				$this->tour_mablagh=(int)$q[0]["tour_mablagh"];
				$this->toz = $q[0]["toz"];
				$this->mabda_id = $parvaz->mabda_id;
				$this->maghsad_id = $parvaz->maghsad_id;
                                $this->shomare = $parvaz->shomare;
				$this->havapeima_id = $parvaz->havapiema_id;
				$this->sherkat_id = $parvaz->sherkat_id;
				$this->can_esterdad = (int)$q[0]["can_esterdad"];
				$this->is_shenavar = $parvaz->is_shenavar;
			}
		}
		public function getId()
		{
			return($this->id);
		}
		public function setZarfiat($zarfiat,$customer_id = -1)
		{
			$conf = new conf;
			$mysql = new mysql_class;
			if($customer_id == -1 && isset($_SESSION[$conf->app."_customer_id"]))
				$customer_id = (int)$_SESSION[$conf->app."_customer_id"];
			if($customer_id >0)
			{
				$customer = new customer_class($customer_id);

				$customer_zarfiat = $this->getZarfiat($customer_id);
				$free_zarfiat = $this->getZarfiat();
				$requested_zarfiat = $zarfiat;
				$customer_zakhire = $customer_zarfiat - $free_zarfiat;
				$total_zarfiat = $this->zarfiat;
				$this->zarfiat -= $requested_zarfiat;
				$customer_zakhire -= $requested_zarfiat;
				if($customer_zakhire < 0)
					$customer_zakhire = 0;

				$customer->setZakhire($this->id,$customer_zakhire);
				$mysql->ex_sqlx("update `parvaz_det` set `zarfiat` = '".$this->zarfiat."'  where `id` = '".$this->id."'");
/*
				if($free_zarfiat < $requested_zarfiat)
				{
					$customer_zakhire -= ($requested_zarfiat-$free_zarfiat);
				}
				$mysql->ex_sqlx("update `parvaz_det` set `zarfiat` = '".$this->zarfiat."'  where `id` = '".$this->id."'");
				if((int)$customer_zakhire!=0)
					$customer->setZakhire($this->id,$customer_zakhire);
*/
			}
		}
		public function resetZarfiat($zarfiat,$customer_id = -1)
		{
			$conf = new conf;
			$mysql = new mysql_class;
                        if($customer_id == -1 && isset($_SESSION[$conf->app."_customer_id"]))
                                $customer_id = (int)$_SESSION[$conf->app."_customer_id"];
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
                                $mysql->ex_sqlx("update `parvaz_det` set `zarfiat` = '".$this->zarfiat."'  where `id` = '".$this->id."'");
				//if((int)$customer_zakhire!=0)
		                        //$customer->setZakhire($this->id,$customer_zakhire);
			}
		}
		public function getZarfiat($customer_id = -1)
		{
			$mysql = new mysql_class;
			$customer_id = (int)$customer_id;
			$out = 0;
			$zakhire_kol = 0;
			$mysql->ex_sql("select SUM(`zakhire`) as `zakh` from `customer_parvaz` where `parvaz_det_id` = '".$this->id."'",$q);
			if(isset($q[0]))
				$zakhire_kol = (int)$q[0]["zakh"];
			$zakhire = 0;
			if($customer_id>0)
			{
				$query = "select `zakhire` from `customer_parvaz` where `parvaz_det_id` = '".$this->id."' and `customer_id` = '$customer_id'";
				$q = null;
				$mysql->ex_sql($query,$q);
				if(isset($q[0]))
					$zakhire = (int)$q[0]["zakhire"];
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
                                $out = TRUE;
                        return $out;
                }
		public function kharidParvaz($tedad,$toz='')
		{
			$conf = new conf;
			$mysql = new mysql_class;
	                $sanad_record_id = 200;
        	        $mysql->ex_sql("select MAX(`sanad_record_id`) as `sss` from `customer_daryaft`",$q);
                	if(isset($q[0]))
        	        {
	                        $sanad_record_id = (((int)$q[0]["sss"]>199)?(int)$q[0]["sss"]:199);
                	        $sanad_record_id ++;
	                }
			$toz = (($toz=='')?"بابت خرید $tedad بلیت از پرواز ".$this->shomare." تاریخ ".audit_class::hamed_pdate($this->tarikh)." ساعت ".$this->saat:$toz);
			$user_id = $_SESSION[$conf->app.'_user_id'];
			if($this->mablagh_kharid!=0)
				$mysql->ex_sqlx('insert into `customer_daryaft` (`customer_id`, `user_id`, `mablagh`, `tozihat`, `sanad_record_id`, `typ`,`sanad_typ`) values('.$this->customer_id.','.$user_id.','.($tedad*$this->mablagh_kharid).',\''.$toz.'\','.$sanad_record_id.',1,2)');
		}
		public function sanad_shenavar_kharid($parvaz,$tedad,$sanad_record_id,$user_id)
		{
			$mysql = new mysql_class;
			$toz = "بابت خرید $tedad بلیت از پرواز ".$parvaz->shomare." تاریخ ".audit_class::hamed_pdate($parvaz->tarikh)." ساعت ".$parvaz->saat.' بصورت شناور';
			if($parvaz->mablagh_kharid!=0)
				$mysql->ex_sqlx('insert into `customer_daryaft` (`customer_id`, `user_id`, `mablagh`, `tozihat`, `sanad_record_id`, `typ`,`sanad_typ`) values('.$parvaz->customer_id.','.$user_id.','.($tedad*$parvaz->mablagh_kharid).',\''.$toz.'\','.$sanad_record_id.',1,2)');
		}
		public function loadJid($id = -1)
		{
			$mysql = new mysql_class;
			$id = (int)$id;
			if($id<=0)
				$id = $this->id;
			$out = null;
			$mysql->ex_sql("select * from `parvaz_jid` where `parvaz_det_id` = $id",$q);
			foreach($q as $r)
				$out[] = (int)$r['jid'];
			return($out);
		}
                public function loadField($id,$field)
                {
			$mysql = new mysql_class;
                        $out = FALSE;
                        if((int)$id > 0 && is_array($field) && count($field) > 0)
                        {
                                $field_txt = '';
                                for($i = 0;$i < count($field);$i++)
                                        $field_txt .= '`'.$field[$i].'`'.(($i < count($field)-1)?',':'');
                                $mysql->ex_sql("select $field_txt from `parvaz_det` where `id` = $id",$q);
                                if(issety($q[0]))
                                {
                                        $this->id=$id;
                                        for($i = 0;$i < count($field);$i++)
                                        {
                                                $this->{$field[$i]} = $q[0][$field[$i]];
                                                $out[$field[$i]] = $q[0][$field[$i]];
                                        }
                                }
                        }
                        return($out);
                }
	}
?>
