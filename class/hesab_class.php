<?php
	class hesab_class
	{
		private $id = -1;
		public $name = "";
		public $number = -1;
		public $sath_id = -1;
		public $presath_id = -1;
		public function __construct($id = -1)
		{
			$id = (int)$id;
			$mysql = new mysql_class;	
			$mysql->ex_sql("select * from `sath_details` where `id`='$id'",$q);
			if(isset($q[0]))
			{
				$r = $q[0];
				$this->id = $id;
				$this->name = $r["name"];
				$this->number = (int)$r["number"];
				$this->sath_id = (int)$r["sath_id"];
				$this->presath_id = (int)$r["presath_id"];
			}
		}
		public function getId()
		{
			return($this->id);
		}
	}
?>
