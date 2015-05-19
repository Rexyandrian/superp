<?php
	class sanad_class
	{
		public function addBlit($blit)
		{
			var_dump($blit);
		}
		public function getLastSanad_record_id()
		{
			$sanad_record_id = 200;
			mysql_class::ex_sql("select MAX(`sanad_record_id`) as `sss` from `customer_daryaft`",$q);
			if($r = mysql_fetch_array($q))
			{
				$sanad_record_id = (((int)$r["sss"]>199)?(int)$r["sss"]:199);
				$sanad_record_id ++;
			}
			return 	$sanad_record_id;	
		}
	}
?>
