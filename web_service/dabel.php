<?php
//	include('../kernel.php');
        function dabel($user,$enc_pass,$id)
        {
		$id = (int)$id;
		$out="auth_error";
		if(user_class::is_authonticated($enc_pass,$user))
		{
			$my = new mysql_class;
			$my->ex_sql("select jid from parvaz_jid where parvaz_det_id = $id",$q);
			$out = xml_class::export($q);
		}
                return($out);
        }
	function dabel_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','flight_id'=>'xsd:int'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#dabel',
		  	        'rpc',
			        'encoded',
			        'returning dabel flights of a flight'
			)
		);
	}
//	var_dump(dabel('web_service','260062',12637));
?>
