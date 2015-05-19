<?php
	class sanad_class
	{
		public function addBlit($blit)
		{
			var_dump($blit);
		}
		public function getLastSanad_record_id()
		{
			$mysql = new mysql_class;
			$sanad_record_id = 200;
			$mysql->ex_sql("select max(`sanad_record_id`) as `sss` from `customer_daryaft`",$q);
			if(isset($q[0]))
			{
				$sanad_record_id = (((int)$q[0]["sss"]>199)?(int)$q[0]["sss"]:199);
				$sanad_record_id ++;
			}
			return 	$sanad_record_id;	
		}
	}
?>
