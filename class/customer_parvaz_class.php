<?php
	class customer_parvaz_class
	{
		public $id=-1;
		public $customer_id=-1;
		public $parvaz_det_id=-1;
		public $poorsant=-1;
		public $zakhire=-1;
		public $p_user_id=-1;
		public $z_user_id=-1;
		public $regtime=-1;
		public $deadtime=-1;
		public function __construct($id=-1)
		{
			if((int)$id > 0)
			{
				$mysql = new mysql_class;
				$mysql->ex_sql("select * from `customer_parvaz` where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->customer_id=$r['customer_id'];
					$this->parvaz_det_id=$r['parvaz_det_id'];
					$this->poorsant=$r['poorsant'];
					$this->zakhire=$r['zakhire'];
					$this->p_user_id=$r['p_user_id'];
					$this->z_user_id=$r['z_user_id'];
					$this->regtime=$r['regtime'];
					$this->deadtime=$r['deadtime'];
				}
			}
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
				$mysql->ex_sql("select $field_txt from `customer_parvaz` where `id` = $id",$q);
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
	}
?>
