<?php

	class session_class {
		protected $savePath;
		protected $sessionName;

		public function __construct() {
			/*
			session_set_save_handler(
			array($this, "open"),
			array($this, "close"),
			array($this, "read"),
			array($this, "write"),
			array($this, "destroy"),
			array($this, "gc")
			);
			*/
		}
		function open($savePath, $sessionName)
		{
			$this->savePath = $savePath;
			$this->sessionName = $sessionName;	
			return true;
		}

		function close()
		{
			return true;
		}
		function read($id)
		{
			$out = '';
			$msql = new mysql_class;
			//$msql->enableCache = FALSE;
			$msql->ex_sql("select `data` from `session` where `id` = '$id'",$q);
			if(isset($q[0]))
			{
				$out = $q[0]['data'];
				$msql->directQueryx("update `session` set `lastUpdate` = '".date("Y-m-d H:i:s")."' where `id` = '$id'");
			}
			else
				$msql->ex_sqlx("insert into `session` (`id`,`name`, `data`, `firstTime`, `lastUpdate`) values  ('$id','".$this->sessionName."','','".date("Y-m-d H:i:s")."','".date("Y-m-d H:is")."')",FALSE);
			return($out);
		}

		function write($id, $data)
		{
			$out = TRUE;
			$msql = new mysql_class;
			$msql->enableCache = FALSE;
                        $msql->ex_sql("select `id` from `session` where `id` = '$id'",$q);
			if(isset($q[0]))
                                $msql->ex_sqlx("update `session` set `lastUpdate` = '".date("Y-m-d H:i:s")."',`data`='$data' where `id` = '$id'");
                        else
                                $msql->ex_sqlx("insert into `session` (`id`,`name`, `data`, `firstTime`, `lastUpdate`) values  ('$id','".$this->sessionName."','$data','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."')");
			return($out);
		}

		function destroy($id)
		{
			$msql = new mysql_class;
			$msql->directQueryx("delete from `session` where `id` = '$id'");
			return true;
		}

		function gc($maxlifetime)
		{
			$deadTime = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s") . " - $maxlifetime second"));
			$msql = new mysql_class;
                        $msql->directQueryx("delete from `session` where `lastUpdate` < '$deadTime'");
			return true;
		}
		function kill()
		{
			$msql = new mysql_class;
                        $msql->directQueryx("truncate table `session`");
		}
	}
?>
