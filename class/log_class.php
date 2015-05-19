<?php
	class log_class
	{
		public $id=-1;
		public $toz="";
		public $user_id=-1;
		public $typ = -1;
		public $host="";
		public $page_address="";
		public $tarikh="";
		public function __construct($id=-1)
		{
			$mysql = new mysql_class;
			$mysql->ex_sql("select * from `log` where `id` = $id",$q);
			if(isset($q[0]))
			{
				$r = $q[0];
				$this->id=$r['id'];
				$this->toz=$r['toz'];
				$this->user_id=$r['user_id'];
				$this->host=$r['host'];
				$this->page_address=$r['page_address'];
				$this->typ = (int)$r['typ'];
				$this->tarikh=$r['tarikh'];
			}
		}
		public function add($args = array())
		{
			$out = -1;
			if(is_array($args))
			{
				$mysql = new mysql_class;
				if(count($args) == 0 || !isset($args["toz"]))
				{
					$args["toz"] = $this->toz;
					$args["user_id"] = $this->user_id;
					$args["host"] = $this->host;
					$args["page_address"] = $this->page_address;
					$args["typ"] = $this->typ;
				}
//				echo "insert into `log` (`toz`,`user_id`,`host`,`page_address`) values ('".$args["toz"]."','".$args["user_id"]."','".$args["host"]."','".$args["page_address"]."')<br/>\n";
				$mysql->ex_sqlx("insert into `log` (`toz`,`user_id`,`host`,`page_address`,`tarikh`,`typ`) values ('".$args["toz"]."','".$args["user_id"]."','".$args["host"]."','".$args["page_address"]."','".date("Y-m-d H:i:s")."','".$args["typ"]."')"); 
				
			}
			return($out);
		}
	}
?>
