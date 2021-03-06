<?php
	class reserve_tmp_class
	{
		public $id=-1;
		public $parvaz_det_id=-1;
		public $tarikh='0000-00-00 00:00:00';
		public $tedad=0;
		public $customer_id=-1;
		public $zakhire=0;
		public $zarfiat=0;
		public $timeout=5;
		public $info='';
		public function __construct($id=-1)
		{
                    $mysql = new mysql_class;
                    $id = (int)$id;
                    $mysql->ex_sql("select * from `reserve_tmp` where `id` = $id",$q);
                    if(isset($q[0]))
                    {
                        $r = $q[0];
                        $this->id=$r['id'];
                        $this->parvaz_det_id=$r['parvaz_det_id'];
                        $this->tarikh=$r['tarikh'];
                        $this->tedad=$r['tedad'];
                        $this->customer_id=$r['customer_id'];
                        $this->zakhire=$r['zakhire'];
                        $this->zarfiat=$r['zarfiat'];
                        $this->timeout=$r['timeout'];
                        $this->adlprice=$r['adlprice'];
                        $this->chdprice=$r['chdprice'];
                        $this->infprice=$r['infprice'];
                        $this->adltedad=$r['adltedad'];
                        $this->chdtedad=$r['chdtedad'];
                        $this->inftedad=$r['inftedad'];
                        $this->netlog=$r['netlog'];
                        $this->rwaitlog=$r['rwaitlog'];
                        if($r['info'] != '' && $r['info'] != null)
                                $this->info=unserialize($r['info']);
                        $this->parvaz_det_info=$r['parvaz_det_info'];
                    }
		}
		public function load_reserve_tmp_times_by_ids($reserve_tmp_ids)
		{
			$out = array();
			if($reserve_tmp_ids!='')
			{
				$qu='';
				$tmp = explode(',',$reserve_tmp_ids);
				foreach($tmp as $id)
					if((int)$id>0)
						$qu.=($qu==''?'':',').$id;
				if($qu!='')
				{
					$mysql = new mysql_class;
					$mysql->ex_sql("select tarikh,id from reserve_tmp where id in ($qu)",$q);
					foreach($q as $r)
					{
						$d1 =strtotime(date("Y-m-d H:i:s"));
						$d2  = strtotime($r['tarikh']);
						$out[] = array('id'=>$r['id'],'tarikh'=>($d1-$d2));	
					}
				}
			}
			return(xml_class::export($out));
		}
	}
?>
