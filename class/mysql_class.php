<?php
	class mysql_class
	{
		public $host = 'localhost';
		public $db = '';
		public $user = '';
		public $pass = '';
		public $ex_db = FALSE;
		public $hotTable = array('cache','menu_table','access','access_det','grop');
		public $coldTable = array();
		public $enableCache = FALSE;
		public $oldSql = FALSE;
		public function arabicToPersian($inp)
		{
			$out = str_replace("ي","ی",$inp);
			$out = str_replace("ك","ک",$out);
			return($out);
		}
		public function __construct($tables = array())
		{
			$conf = new conf;
			//$this->enableCache = ($conf->enableCache === FALSE)?$conf->enableCache:TRUE;
			//$this->hotTable = ((isset($tables['hotTable']))?$tables['hotTable']:((isset($conf->hotTable))?$conf->hotTable:array()));
			//$this->coldTable = ((isset($tables['coldTable']))?$tables['coldTable']:((isset($conf->coldTable))?$conf->coldable:array()));
		}
		public function getArrayFirstIndex($arr)
		{
			foreach ($arr as $key => $value)
				return $key;
		}
		public function fetch_array($q)
		{
			return(mysql_fetch_array($q));
		}
		public function fetch_field($q)
		{
			return(mysql_fetch_field($q));
		}
		public function close($ln)
		{
			return(mysql_close($ln));
		}
		public function num_rows($q)
		{
			return(mysql_num_rows($q));
		}
		public function qToArr($data)
		{
			$tmpData = array();
			$keys = array();
			if($data !== FALSE)
			{
				while($r = mysql_fetch_field($data))
					$keys[] = $r->name;
				while($r = mysql_fetch_array($data))
				{
					$tmpData1 = array();
					foreach($keys as $key)
						$tmpData1[$key] = $r[$key];
					$tmpData[] = $tmpData1;
				}
			}
			return($tmpData);
		}
		public function directQuery($sql,&$q)
		{
			$sql = mysql_class::arabicToPersian($sql);
			$conf= new conf;
			$host = $conf->host;
			$db =$conf->db;
			$user = $conf->user;
			$pass = $conf->pass;
			if(isset($this) && get_class($this) == __CLASS__)
                        {
                                if(isset($this->ex_db) && $this->ex_db)
                                {
                                        $host = $this->host;
                                        $user = $this->user;
                                        $pass = $this->pass;
                                        $db = $this->db;
                                }
                        }
			$out = "ok";
			$q = NULL;
			$conn = mysql_connect($host,$user,$pass);
			if(!($conn==FALSE)){
				if(!(mysql_select_db($db,$conn)==FALSE)){
					mysql_query("SET NAMES 'utf8'");
					$tmpq = mysql_query($sql,$conn);
					mysql_close($conn);
					$q = ($this->enableCache)?$this->qToArr($tmpq):(($this->oldSql)?$tmpq:$this->qToArr($tmpq));
				}else
					$out = "Select DB Error.";
			}else
				$out = "Connect MySql Error.";		
			return($out);
		}
		public function directQueryx($sql)
                {
			$sql = mysql_class::arabicToPersian($sql);
                        $conf= new conf;
                        $host = $conf->host;
                        $db =$conf->db;
                        $user = $conf->user;
                        $pass = $conf->pass;
                        if(isset($this) && get_class($this) == __CLASS__)
                        {
                                if(isset($this->ex_db) && $this->ex_db)
                                {
                                        $host = $this->host;
                                        $user = $this->user;
                                        $pass = $this->pass;
                                        $db = $this->db;
                                }
                        }
                        $out = "ok";
                        $q = NULL;
                        $conn = mysql_connect($host,$user,$pass);
                        if(!($conn==FALSE)){
                                if(!(mysql_select_db($db,$conn)==FALSE)){
                                        mysql_query("SET NAMES 'utf8'");
                                        mysql_query($sql,$conn);
                                        mysql_close($conn);
                                }else
                                        $out = "Select DB Error.";
                        }else
                                $out = "Connect MySql Error.";
                        return($out);
                }
		public function tableFromQuery($sql)
		{
			$out = '';
			$fr = ' ';
			if(strpos(trim($sql),"from") !== FALSE)
				$fr = 'from';
			else if(strpos(trim($sql),"into") !== FALSE)
                                $fr = 'into';
			$tmp = explode($fr,strtolower(trim($sql)));
			if(count($tmp) > 1)
			{
				$tmp1 = trim($tmp[1]);
				$tmp2 = explode(' ',$tmp1);
				$out = $tmp2[0];
			}
			return(str_replace('`','',$out));
		}
		public function insert_id($ln)
		{
			return(mysql_insert_id($ln));
		}
		public function ex_sql($sql,&$q)
		{
			$sql = mysql_class::arabicToPersian($sql);
			$table = $this->tableFromQuery($sql);
			if(!in_array($table,$this->hotTable) && $this->enableCache)
			{
				$cache = new cache_class($table,str_replace("'","~",$sql));
				if($cache->id <= 0 )
				{
					$out = $this->directQuery($sql,$q);
					$qCopy = $q;
					if($out == 'ok')
						$cache->add($table,$qCopy,str_replace("'","~",$sql));
				}
				else
				{
					$out = 'ok';
					$q = $cache->data;
				}
			}
			else
				$out = $this->directQuery($sql,$q);
			$out = 'ok';
			return($out);
		}
		public function ex_sqlx($sql,$close=TRUE)
		{
			$sql = mysql_class::arabicToPersian($sql);
			$conf= new conf;
                        $host = $conf->host;
                        $db =$conf->db;
                        $user = $conf->user;
                        $pass = $conf->pass;
			if(isset($this) && get_class($this) == __CLASS__)
                        {
                                if(isset($this->ex_db) && $this->ex_db)
                                {
                                        $host = $this->host;
                                        $user = $this->user;
                                        $pass = $this->pass;
                                        $db = $this->db;
                                }
                        }
			$table = $this->tableFromQuery($sql);
			if(!in_array($table,$this->hotTable) && $this->enableCache)
			{
                                $cache = new cache_class($table,'');
				$cache->delete();
			}
			$out = "ok";
			$q = NULL;
			$conn = mysql_connect($host,$user,$pass);
			if(!($conn==FALSE)){
				if(!(mysql_select_db($db,$conn)==FALSE)){
					mysql_query("SET NAMES 'utf8'");
					mysql_query($sql,$conn);
					if($close)
						mysql_close($conn);
					else
						$out = $conn;
				}else
					$out = "Select DB Error.";
			}else
				$out = "Connect MySql Error.";
			return($out);
		}
		function loadField($table)
		{
			$out = array();
			$my = new mysql_class;
			$my->oldSql = TRUE;
			$my->ex_sql("select * from `$table` where 1=0",$q);
			while($r = mysql_fetch_field($q))
				$out[] = $r->name;
			return $out;
		}
		function accessToGroup($page_name,$group_id)
		{
			$my = new mysql_class;
			$my->ex_sqlx("insert into `access` (`page_name`,`group_id`,`is_group`) values ('$page_name','$group_id','1')");
		}
		function deleteAccessToGroup($page_name,$group_id=-1)
		{
			$my = new mysql_class;
			if((int)$group_id>0)
				$my->ex_sqlx("delete from `access`  where `page_name`='$page_name' and `group_id`='$group_id'");
			else
				$my->ex_sqlx("delete from `access`  where `page_name`='$page_name' ");
		}
	}
?>
