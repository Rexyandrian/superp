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
		public function __construct($id = -1)
		{
			$id = (int)$id;
			mysql_class::ex_sql("select * from `parvaz_det` where `id` ='$id'",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id = $id;
				$this->parvaz_id = (int)$r["parvaz_id"];
				$this->tarikh = $r["tarikh"];
				$this->saat = $r["saat"];
				$this->zarfiat = (int)$r["zarfiat"];
				$this->ghimat = (int)$r["ghimat"];
				$this->typ = (int)$r["typ"];
				$this->zakhire = (int)$r["zakhire"];
				$this->j_id = (int)$r["j_id"];
				$this->poor_def = (int)$r["poor_def"];
				$parvaz = new parvaz_class((int)$r["parvaz_id"]);
				$this->mabda_id = $parvaz->mabda_id;
				$this->maghsad_id = $parvaz->maghsad_id;
                                $this->shomare = $parvaz->shomare;
				$this->havapeima_id = $parvaz->havapiema_id;
				$this->sherkat_id = $parvaz->sherkat_id;
				$this->can_esterdad = (int)$r["can_esterdad"];
				$this->mablagh_kharid = (int)$r["mablagh_kharid"];
				$this->customer_id = (int)$r["customer_id"];
			}
		}
		public function getId()
		{
			return($this->id);
		}
		public function setZarfiat($zarfiat,$customer_id = -1)
		{
			if($customer_id == -1 && isset($_SESSION["customer_id"]))
				$customer_id = (int)$_SESSION["customer_id"];
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
                        if($customer_id == -1 && isset($_SESSION["customer_id"]))
                                $customer_id = (int)$_SESSION["customer_id"];
                        if($customer_id >0)
                        {
/*
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
*/
				$this->zarfiat += $zarfiat;
                                mysql_class::ex_sqlx("update `parvaz_det` set `zarfiat` = '".$this->zarfiat."'  where `id` = '".$this->id."'");
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
		
	}
?>