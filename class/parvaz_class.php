<?php
	class parvaz_class
	{
		public $id = -1;
		public $shomare = '-1';
		public $name = "";
		public $sherkat_id = -1;
		public $mabda_id = -1;
		public $maghsad_id = -1;
		public $havapiema_id = -1;
		public $zarfiat_def = 0;
		public $typ_def	= 0;
		public $saat_def = '';
		public $saat_kh_def = '';
		public $ghimat_def = 0;
		public $poor_def = 0;
		public $mablagh_kharid_def = 0;
		public $rang = "";
		public $customer_id_det = -1;
		public $is_shenavar = FALSE;
		public function miniTime($inp)
		{
                        $tmp = explode(':',$inp);
                        $out = $inp;
                        if(count($tmp) == 3)
                                $out = $tmp[0].':'.$tmp[1];
                        return($out);
		}
		public function add()
		{
			$out = -1;
			$my = new mysql_class;
			$my->ex_sql("select id from `parvaz` where `shomare` = '".$this->shomare."'",$q);
			if(isset($q[0]))
				$out = (int)$q[0]['id'];
			else
			{
				$ln = $my->ex_sqlx("insert into parvaz (`shomare`, `name`, `sherkat_id`, `mabda_id`, `maghsad_id`, `havapiema_id`, `ghimat_def`, `zarfiat_def`, `saat_def`, `saat_kh_def`, `poor_def`, `typ_def`, `mablagh_kharid_def`, `customer_id_det`, `rang`, `is_shenavar`, `ghimat_ticket`) values ('".$this->shomare."','".$this->name."','".$this->sherkat_id."','".$this->mabda_id."','".$this->maghsad_id."','".$this->havapiema_id."','".$this->ghimat_def."','".$this->zarfiat_def."','".$this->saat_def."','".$this->saat_kh_def."','".$this->poor_def."','".$this->typ_def."','".$this->mablagh_kharid_def."','".$this->customer_id_det."','".$this->rang."','".$this->is_shenavar."','".$this->ghimat_ticket."')",FALSE);
				$out = $my->insert_id($ln);
				$my->close($ln);
			}
			return($out);
		}
		public function __construct($id = -1)
		{
			$id = (int)$id;
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `parvaz` where `id` = '$id'",$q);
			if(isset($q[0]))
			{
				$r = $q[0];
				$this->id = $id;
				$this->shomare = $r["shomare"];
				$this->name = $r["name"];
				$this->saat_def = $this->miniTime($r["saat_def"]);
				$this->saat_kh_def = $this->miniTime($r["saat_kh_def"]);
				$this->sherkat_id = (int)$r["sherkat_id"];
				$this->mabda_id = (int)$r["mabda_id"];
				$this->maghsad_id = (int)$r["maghsad_id"];
				$this->havapiema_id = (int)$r["havapiema_id"];
				$this->zarfiat_def = (int)$r["zarfiat_def"];
				$this->typ_def = (int)$r["typ_def"];
				$this->ghimat_def = (int)$r["ghimat_def"];
				$this->poor_def = (int)$r["poor_def"];
				$this->mablgh_kharid_def = (int)$r["mablagh_kharid_def"];
				$this->rang = $r["rang"];
				$this->customer_id_det = $r["customer_id_det"];
				$this->is_shenavar = (((int)$r["is_shenavar"]==1)?TRUE:FALSE);
				$this->ghimat_ticket = (int)$r["ghimat_ticket"];
			}
		}
		public function getId()
		{
			return($this->id);
		}
		public function fromDateToDate($start_date,$stop_date,$week_day = array())
		{
			$mysql = new mysql_class;
			$days = array(1=>"دوشنبه",2=>"سه‌شنبه",3=>"چهارشنبه",4=>"پنجشنبه",5=>"جمعه",6=>"شنبه",7=>"یکشنبه");
			if( strtotime($stop_date) >= strtotime($start_date))
			{
				$tmp = $start_date;
				while(strtotime($stop_date) >= strtotime($tmp))
				{
					$ok = FALSE;
					$this_day = strtotime($tmp);
					$dayOfW = date("N",$this_day);
					for($i=0;$i<count($week_day);$i++)
					{
						if($dayOfW == $week_day[$i])
							$ok = TRUE;
					}
					if($ok)
					{
						$mysql->ex_sql("select `id` from `parvaz_det` where `parvaz_id` = '".$this->id."' and `tarikh`='$tmp' and `saat`='".$this->saat_def."' and `en`='1' ",$q);
						if(!(isset($q[0])))
						{
							$mysql->ex_sqlx("insert into `parvaz_det` (`parvaz_id`, `tarikh`, `saat`,`saat_kh`, `zarfiat`, `ghimat`, `typ`, `zakhire`, `poor_def`,`mablagh_kharid`,`customer_id`) values ('".$this->id."','$tmp','".$this->saat_def."','".$this->saat_kh_def."','".$this->zarfiat_def."','".$this->ghimat_def."','".$this->typ_def."','".$this->zarfiat_def."','".$this->poor_def."','".$this->mablgh_kharid_def."','".$this->customer_id_det."')");
							if($this->is_shenavar)
							{
								$mysql->ex_sql("SELECT `id` FROM `parvaz_det` WHERE `parvaz_id`='".$this->id."' and `tarikh`='$tmp' and `saat`='".$this->saat_def."' and `saat_kh`='".$this->saat_kh_def."' and `zarfiat`='".$this->zarfiat_def."' and `ghimat`='".$this->ghimat_def."' and `typ`='".$this->typ_def."' and `zakhire`='".$this->zarfiat_def."' and `poor_def`='".$this->poor_def."' and `mablagh_kharid`='".$this->mablgh_kharid_def."' and `customer_id`='".$this->customer_id_det."'",$qu);
								if(isset($qu))
								{
									$r = $qu[0];
									$parvaz_det = new parvaz_det_class($r['id']);
									$parvaz_det->kharidParvaz($this->zarfiat_def);
								}
							}
						}
					}
					$tmp = strtotime($tmp." + 1 day");
					$tmp = date("Y-m-d",$tmp);
				}
			}
		}
	}
?>
