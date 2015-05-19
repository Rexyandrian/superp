<?php
	class cache_class
	{
		public $id=-1;
		public $tableName='';
		public $data=null;
		public $query ='';
		public $createTime='0000-00-00 00:00:00';
		public $viewCount=0;
		public function __construct($tableName,$query='')
		{
			if($tableName !='' )
			{
				$mysql = new mysql_class;
				$mysql->directQuery("select * from `cache` where `data`<>'' and `tableName` = '$tableName'".(($query != '')?" and `query` = '$query'":''),$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id=$r['id'];
					$this->tableName=$r['tableName'];
					$data=$r['data'];
					if($data!='')
						$this->data = unserialize($data);
					$this->query=$r['query'];
					$this->createTime=$r['createTime'];
					$this->viewCount=$r['viewCount'];
					//$mysql->directQueryx("update `cache` set `viewCount` = `viewCount` + 1 where `id` = " . $this->id);
				}
			}
		}
		public function add($tableName,$data,$query)
		{
			if($tableName !='' )
			{
				$createTime = date("Y-m-d H:i:s");
				$mysql = new mysql_class;
				$thisId = -1;
				$mysql->enableCache = FALSE;
				$mysql->directQuery("select `id` from `cache` where `tableName` = '$tableName' and `query` = '$query' ",$q);
				if($r = $mysql->fetch_array($q))
					$thisId = $r['id'];
				if($thisId > 0)
					$mysql->directQueryx("update  `cache` set `data` = '".(serialize($data))."' where `id` = '$thisId'");
				else
				{
					$ln = $mysql->ex_sqlx("insert into `cache` (`tableName`,`data`,`createTime`,`query`) values('$tableName','".(serialize($data))."','$createTime','$query') ",FALSE);
					$thisId = (int)$mysql->insert_id($ln);
					$mysql->close($ln);
				}
				$this->id = $thisId;
				$this->tableName = $tableName;
				$this->data = $data;
				$this->query=$query;
				$this->createTime  = $createTime;
				$this->viewCount = 1;
			}
			return ($this->id);
		}
		public function delete()
		{
			$mysql = new mysql_class;
			$mysql->directQueryx("delete from  `cache` where `tableName` = '".$this->tableName."'");
		}
	}
?>
