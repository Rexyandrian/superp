<?php
	class slide_class
	{
		$conf = new conf;
		public $index = 0;
		public $data = array();
		public function __construct($app = $conf->app)
		{
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `admin` where `app` = '$app'",$q);
			while($q as $r)
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
