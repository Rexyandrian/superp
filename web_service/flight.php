<?php
//	include('../kernel.php');
        function flight($user,$pass,$flight_id)
        {
		$out='auth_error';
		if(user_class::is_authonticated($pass,$user))
		{
			$tmp_arr = array();
			$fieldsArray = array('parvaz_det.id','ghimat','zarfiat','mabda_id','maghsad_id',
				'shomare','havapiema_id','sherkat_id','tarikh','saat','saat_kh','j_id');
			$my = new mysql_class;
			$feildStr='';
			foreach($fieldsArray as $i=>$str)
				$feildStr.=($feildStr==''?'':',').$str;
			$my->ex_sql("select $feildStr from parvaz_det left join parvaz on (parvaz_det.parvaz_id=parvaz.id) where parvaz_det.id=$flight_id  order by tarikh",$q);
			//foreach($q as $r)	
			$out=xml_class::export($q);
		}
                return($out);
        }
	function flight_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','flight_id'=>'xsd:int'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#flight',
		  	        'rpc',
			        'encoded',
			        'load flight information by flight.id  . . .'
			)
		);
	}
	//var_dump(flight("asd","adf",12637));
?>
