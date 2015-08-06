<?php
	class pardakht_class
	{
		public $id=-1;
		public $sanad_record_id='-1';
		public $tarikh=-1;
		public $mablagh=-1;
		public $bank_out=-1;
		public $is_hotel = FALSE;
		public $is_tmp = TRUE;
		public function __construct($id=-1)
		{
			$id = (int)$id;
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `pardakht` where `id` = $id",$q);
			if(isset($q[0]))
			{
				$r = $q[0];
				$this->id=$r['id'];
				$this->sanad_record_id=$r['sanad_record_id'];
				$this->tarikh=$r['tarikh'];
				$this->mablagh=$r['mablagh'];
                                if($r['bank_out'] != '')
  	                              $this->bank_out=unserialize($r['bank_out']);
				$this->is_hotel = ((int)$r['is_hotel']==1)?TRUE:FALSE;
				$this->is_tmp = ((int)$r['is_tmp']==1)?TRUE:FALSE;
                        }
                }
		public function loadPaylineByBankOut($id)
		{
			$id = (int)$id;
			$mysql = new mysql_class;
                        $mysql->ex_sql("select * from `pardakht` where `bank_out` = '$id'",$q);
                        if(isset($q[0]))
                        {
                                $r = $q[0];
                                $this->id=$r['id'];
                                $this->sanad_record_id=$r['sanad_record_id'];
                                $this->tarikh=$r['tarikh'];
                                $this->mablagh=$r['mablagh'];
                                $this->bank_out=(int)$r['bank_out'];
                                $this->is_hotel = ((int)$r['is_hotel']==1)?TRUE:FALSE;
                                $this->is_tmp = ((int)$r['is_tmp']==1)?TRUE:FALSE;
                        }
		}
                public function add($sanad_record_id,$tarikh,$mablagh)
                {
                        $out = -1;
			$mysql = new mysql_class;
                        $sql = "insert into `pardakht` (`sanad_record_id`,`tarikh`,`mablagh`) values ('$sanad_record_id','$tarikh',$mablagh)";
			$conn = $mysql->ex_sqlx($sql,FALSE);
                        $out = $mysql->insert_id($conn);
			$mysql->close($conn);
                        return($out);
                }
                public function update($sanad_record_id)
                {
			$sanad_record_id = (int)$sanad_record_id;
                        $sql = "update `pardakht` set `bank_out` = '".$this->bank_out."',`sanad_record_id` = '$sanad_record_id',`is_tmp` = 0 where `id` = ".$this->id;
			$mysql = new mysql_class;
			$mysql->ex_sqlx($sql);
                }
		public function getBarcode($id = -1)
		{
			$id = (int)$id;
			if($id <= 0)
				$id = $this->id;
			$out = pow($id,2) + 10000;
			$out = dechex($out);
			return($out);
		}
		public function barcode($barcode)
		{
			$barcode = hexdec($barcode);
			$out = sqrt($barcode - 10000);
			if($out % 1 != 0)
				$out = 0;
			return($out);
		}
		public function loadBySanad_record_id($sanad_record_id)
		{
			$out = '---';
			$mysql = new mysql_class;
			$mysql->ex_sql("select bank_out from pardakht where is_tmp=0 and sanad_record_id=".$sanad_record_id,$q);
			//echo "select bank_out from pardakht where is_tmp=0 and sanad_record_id=".$sanad_record_id;
			//var_dump($q);
			if(isset($q[0]))
			{
				$bank_out = $q[0]['bank_out'];
				if($bank_out!='')
				{
					$bank_out = unserialize($bank_out);
					//var_dump($bank_out['SaleReferenceId']);
					$out = $bank_out['SaleReferenceId'];
				}
			}
			return($out);
		}
                public static function getBracodeBySanad_record_id($sanad_record_id)
                {
                    $out = '---';
                    $mysql = new mysql_class;
                    $mysql->ex_sql("select id from pardakht where is_tmp=0 and sanad_record_id=".$sanad_record_id,$q);

                    if(isset($q[0]))
                    {
                        $out = pardakht_class::getBarcode($q[0]['id']);
                    }
                    return($out);
                }        
	}
?>