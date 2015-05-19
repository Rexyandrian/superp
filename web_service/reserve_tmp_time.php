<?php
        function reserve_tmp_time($user,$enc_pass,$reserve_tmp_ids)
        {
		$out="auth_error";
		if(user_class::is_authonticated($enc_pass,$user))
			$out = reserve_tmp_class::load_reserve_tmp_times_by_ids($reserve_tmp_ids);
                return($out);
        }
	function reserve_tmp_time_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','reserve_tmp_ids'=>'xsd:int'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#reserve_tmp_time',
		  	        'rpc',
			        'encoded',
			        '. . .'
			)
		);
	}
	//var_dump(reserve_tmp_time("asd","sad","7064"));
?>
