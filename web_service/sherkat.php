<?php
        function sherkat($user,$pass)
        {
		$tmp_arr = array();	
		$my = new mysql_class;
		$my->ex_sql("select * from sherkat order by name",$q);
		foreach($q as $r)
			$tmp_arr[]=array('id'=>$r['id'],'name'=>$r['name']);	
		$out=xml_class::export($tmp_arr);
                return($out);
        }
	function sherkat_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#sherkat',
		  	        'rpc',
			        'encoded',
			        'sherkat  . . .'
			)
		);
	}
	//city("sdf","adf");
?>
