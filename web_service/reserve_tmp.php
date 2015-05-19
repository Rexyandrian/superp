<?php
        function reserve_tmp($user,$enc_pass,$parvaz_det_ids,$tedad)
        {
		$out="auth_error";
		if(user_class::is_authonticated($enc_pass,$user))
		{
			$u = new user_class;
			$u->loadByUser($user);
			$timeout = 5;
			$parvaz_det_ids_array = explode(',',$parvaz_det_ids);
			$out_array = array();
			foreach($parvaz_det_ids_array as $parvaz_det_id)
			{
				$out_array[] = ticket_class::addTmp($parvaz_det_id,$tedad,$timeout,$u->customer_id);
				$p = new parvaz_det_class($parvaz_det_id);
				$p->setZarfiat($tedad,$u->customer_id);
			}
			$out = count($out_array)>0?implode('|',$out_array):'false';
		}
                return($out);
        }
	function reserve_tmp_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','parvaz_id'=>'xsd:int','tedad'=>'xsd:int'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#reserve_tmp',
		  	        'rpc',
			        'encoded',
			        'book flight temprerally . . .'
			)
		);
	}
?>
