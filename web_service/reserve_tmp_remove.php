<?php
        function reserve_tmp_remove($user,$enc_pass,$tmp_ids)
        {
		$out="auth_error";
		if(user_class::is_authonticated($enc_pass,$user))
		{
			ticket_class::removeTmp($tmp_ids);
			$out = "OK";
		}
                return($out);
        }
	function reserve_tmp_remove_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','tmp_ids'=>'xsd:string'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#reserve_tmp_remove',
		  	        'rpc',
			        'encoded',
			        'remove temporary flights comma seperated...'
			)
		);
	}
?>
