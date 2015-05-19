<?php
	class slide_class
	{
		public $index = 0;
		public $data = array();
		public function __construct($app = conf::app)
		{
			mysql_class::ex_sql("select * from `admin` where `app` = '$app'",$q);
			while($r = mysql_fetch_array($q))
			{
				$this->data[] = $r['content'];
			}
		}
		public function loadData($index = 0)
		{	
			$this->index = $index;
			$out = ((isset($this->data[$index]))?$this->data[$index]:'');
			return($out);
			
		}
	}
?>