<?php
	class access_det_class
	{
		public $id=-1;
		public $acc_id=-1;
		public $frase="";
		public function __construct($id=-1)
		{
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `access_det` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->acc_id=$r['acc_id'];
				$this->frase=$r['frase'];
			}
		}
		public function loadByAcc($acc_id)
		{
			$out = array();
			$acc_id = (int)$acc_id;
			$mysql = new mysql_class;
			$mysql->ex_sql("select `frase` from `access_det` where `acc_id` = $acc_id",$q);
			foreach($q as $r)
				$out[] = $r['frase'];
			return($out);
		}
		public function loadByGrp($grp_id)
		{
			$accs = new access_class;
			$out = array();
			$accs = $accs->loadByGroup($grp_id);
			$mysql = new mysql_class;
			foreach($accs as $id => $page)
			{
	                        $acc_id = $id;
        	                $mysql->ex_sql("select `frase` from `access_det` where `acc_id` = $acc_id",$q);
                	        foreach($q as $r)
                        	        $out[] = array('frase'=>$r['frase'],'page'=>$page);
			}
                        return($out);
		}
	}
?>
