<?php
	$project_path = dirname(dirname(__FILE__));
        include_once("$project_path/class/conf.php");
        include_once("$project_path/class/mysql_class.php");
	function clearTicket()
	{
			$mysql = new mysql_class;
                        $tarikh = date("Y-m-d H:i:s");
                        $mysql->ex_sqlx("update `parvaz_det` set `zarfiat`=`zarfiat`+(select SUM(`zarfiat`) from `reserve_tmp`  where `parvaz_det_id` = `parvaz_det`.`id` and `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))  where `id` in (select `parvaz_det_id` from `reserve_tmp`  where (not(`tedad` is null))  and `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))");
                        $mysql->ex_sqlx("update `customer_parvaz` set `zakhire`=`zakhire`+(select SUM(`zakhire`) from `reserve_tmp`  where `customer_id` = `customer_parvaz`.`customer_id` and `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))  where `parvaz_det_id` in (select `parvaz_det_id` from `reserve_tmp`  where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute)) and `customer_id` in (select `customer_id` from `reserve_tmp`  where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute))");
                        $mysql->ex_sqlx("delete from `reserve_tmp` where `tarikh` <= SUBDATE('$tarikh' , interval `timeout` minute)");
//----------------------پﺍک کﺭﺪﻧ ﻅﺮﻓیﺕ ﻢﻬﻠﺗ ﺩﺍﺭ--
                        $mysql->ex_sqlx("update  `customer_parvaz` set `zakhire` = 0 , regtime = '0000-00-00 00:00:00'  WHERE `regtime` + interval `deadtime` minute < '$tarikh' and `deadtime` > 0");
/*
//-----------------------پاک کردن بلیط موقت -----
			mysql_class::ex_sqlx("update `parvaz_det` set `zarfiat`=`zarfiat`+(select SUM(`zarfiat`) from `reserve_tmp`  where `parvaz_det_id` = `parvaz_det`.`id` and `tarikh` <= now() - interval `timeout` minute)  where `id` in (select `parvaz_det_id` from `reserve_tmp`  where (not(`tedad` is null))  and `tarikh` <= now() - interval `timeout` minute)");
			mysql_class::ex_sqlx("update `customer_parvaz` set `zakhire`=`zakhire`+(select SUM(`zakhire`) from `reserve_tmp`  where `customer_id` = `customer_parvaz`.`customer_id` and `tarikh` <= now() - interval `timeout` minute)  where `parvaz_det_id` in (select `parvaz_det_id` from `reserve_tmp`  where `tarikh` <= now() - interval `timeout` minute) and `customer_id` in (select `customer_id` from `reserve_tmp`  where `tarikh` <= now() - interval `timeout` minute)");
			mysql_class::ex_sqlx("delete from `reserve_tmp` where `tarikh` <= now() - interval `timeout` minute");
//----------------------پاک کردن ظرفیت مهلت دار--
			mysql_class::ex_sqlx("update  `customer_parvaz` set `zakhire` = 0 , regtime = '0000-00-00 00:00:00'  WHERE `regtime` + interval `deadtime` minute < now() and `deadtime` > 0");
*/
	}
	clearTicket();
?>
