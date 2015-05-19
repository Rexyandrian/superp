<?php
	class grop_class
	{
		public $id=-1;
		public $name="";
		public $en=-1;
		public function __construct($id=-1)
		{
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `grop` where `id` = $id",$q);
			if(isset($q[0]))
			{
				$r = $q[0];
				$this->id=$r['id'];
				$this->name=$r['name'];
				$this->en=$r['en'];
			}
		}
	}
?>
