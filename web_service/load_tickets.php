<?php
        function load_tickets($user,$pass,$sanad_record_ids)
        {
		$tmp_arr = array();	
		$my = new mysql_class;
		$u = new user_class;
                $u->loadByUser($user);
		$customer_id = $u->customer_id;
		$my->ex_sql("select * from ticket where customer_id=$customer_id and  sanad_record_id in ($sanad_record_ids)",$q);
		foreach($q as $r)
		{
			$tick = new ticket_class;
			//$tick->id = (int)$r['id'];
			unset($tick->sanad_record_id);
			$tick->fname = $r["fname"];
			$tick->lname = $r["lname"];
			$tick->tel = $r["tel"];
			$tick->adult = (int)$r["adult"];
			$tick->rahgiri = (int)$r["sanad_record_id"];
			$tick->parvaz_det_id = (int)$r["parvaz_det_id"];
			$tick->customer_id = (int)$r["customer_id"];
			$tick->user_id = (int)$r["user_id"];
			$tick->shomare = (int)$r["shomare"];
			$tick->typ = (int)$r["typ"];
			$tick->en = (int)$r["en"];
			$tick->regtime = $r["regtime"];
			$tick->mablagh = (int)$r["mablagh"];
			$tick->tour_mablagh = (int)$r["tour_mablagh"];
			$tick->poorsant = (int)$r["poorsant"];
			$tick->gender = (int)$r["gender"];
			$tmp_arr[]=$tick;	
		}
		$out=xml_class::export($tmp_arr);
                return($out);
        }
	function load_tickets_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','xsd:rahgiriha'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#load_tickets',
		  	        'rpc',
			        'encoded',
			        'load Tickets by rahgiri code  . . .'
			)
		);
	}
	//city("sdf","adf");
?>
