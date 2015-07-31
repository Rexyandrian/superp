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
                    $id = (int)$id;
                    mysql_class::ex_sql("select * from `reserve_tmp` where `id` = $id",$q);
                    if($r = mysql_fetch_array($q))
                    {
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
                    }
		}
	}
?>
