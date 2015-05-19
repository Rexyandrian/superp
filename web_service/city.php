<?php
        function city($user,$pass)
        {
		$out='auth_error';
		if(user_class::is_authonticated($pass,$user))
		{
			$tmp_arr = array();	
			$my = new mysql_class;
			$my->ex_sql("select * from shahr order by name",$q);
			$out=xml_class::export($q);
		}
                return($out);
        }
	function city_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#city',
		  	        'rpc',
			        'encoded',
			        'cities  . . .'
			)
		);
	}
	//city("sdf","adf");
?>
