<?php
        function havapeima($user,$pass)
        {
		$out = 'auth_error';
		if(user_class::is_authonticated($pass,$user))
		{
			$tmp_arr = array();	
			$my = new mysql_class;
			$my->ex_sql("select * from havapeima order by name",$q);
			foreach($q as $r)
				$tmp_arr[]=array('id'=>$r['id'],'name'=>$r['name']);	
			$out=xml_class::export($tmp_arr);
		}
                return($out);
        }
	function havapeima_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#havapeima',
		  	        'rpc',
			        'encoded',
			        'havapeima  . . .'
			)
		);
	}
?>
