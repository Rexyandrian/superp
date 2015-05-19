<?php
	class parvaz_det_tmp_class
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
		public function __construct($id = -1)
		{
			$id = (int)$id;
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `parvaz_det_tmp` where `id` ='$id'",$q);
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
				$this->sites_id = (int)$q[0]["sites_id"];
			}
		}
		public function disable_parvaz_det($sites_id)
		{
			$tarikh = date("Y-m-d");
			$saat = date("H:i:s");
			$my = new mysql_class;
			$my->ex_sqlx("update parvaz_det set en=3 where en=1 and ((tarikh>'$tarikh') or (tarikh='$tarikh' and saat>='$saat')) and id in (select parvaz_det_id from parvaz_det_sites where sites_id=$sites_id) ");
		}
		public function add($inp_all,$sites_id,$add_ghimat)
		{
			$my = new mysql_class;
			$tmp = '';
			parvaz_det_tmp_class::disable_parvaz_det($sites_id);
			$okDets = array();
			foreach($inp_all as $inp)
			{
				$parvaz_id = $inp['parvaz'];
				$tarikh = $inp['date'];
				$saat = $inp['saat'];
				$zarfiat = (int)$inp['tedad'];
				
				$ghimat = $inp['ghimat']+$add_ghimat;
				$my->ex_sql("select id,ghimat,zarfiat,en from parvaz_det where parvaz_id=$parvaz_id and DATE(tarikh) = '$tarikh'",$q);
				//$my->ex_sql("select id,ghimat,zarfiat,en from parvaz_det where parvaz_id=$parvaz_id and tarikh='$tarikh' and saat='$saat' ",$q);
				if(isset($q[0]))
				{
					if((int)$q[0]['en']!=0)
					{
						$r = $q[0];
						if((int)$r['ghimat']>$ghimat || ((int)$r['ghimat']==$ghimat and $zarfiat!=(int)$r['zarfiat']) || (int)$r['en']==3)
						{
							$my->ex_sqlx("delete from parvaz_det where id=".$r['id']);
							$my->ex_sqlx("insert into parvaz_det (id,parvaz_id,tarikh,saat,zarfiat,ghimat,typ,toz,en) values (".$r['id'].",'$parvaz_id','$tarikh','$saat','$zarfiat','$ghimat','0','',".((int)$q[0]['en']==3?1:$q[0]['en']).")");
							$my->ex_sqlx("update parvaz_det_sites set sites_id=$sites_id where parvaz_det_id =".$r['id'] );
						}
					}
					$okDets[] = (int)$r['id'];
				}
				else
				{
					$ln = $my->ex_sqlx("insert into parvaz_det (parvaz_id,tarikh,saat,zarfiat,ghimat,typ) values ('$parvaz_id','$tarikh','$saat','$zarfiat','$ghimat','0')",FALSE);
					$id_inp =(int) $my->insert_id($ln);
					$my->close($ln);
					if($id_inp>0)
					{
						$okDets[] = (int)$id_inp;
						$my->ex_sqlx("insert into parvaz_det_sites (parvaz_det_id,sites_id) values ($id_inp,$sites_id) ");
					}
				}
			}
			$my->ex_sqlx("update parvaz_det set en=3 where id in (select parvaz_det_id from parvaz_det_sites where sites_id = $sites_id) ".((count($okDets)>0)?'and not id in ('.implode(',',$okDets).')':''));
		}
	}
?>
