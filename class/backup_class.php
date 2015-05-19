<?php
	class backup_class
	{
		public function backup($backupFile)
		{
			$conf = new $conf;
			$mys = new mysql_class;
			exec('mysqldump -u '.$conf->user.' '.$conf->db.' -p\''.$conf->pass.'\' > '.$backupFile);
		}	
	}
?>
