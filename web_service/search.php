<?php
//	include('../kernel.php');
        function search($user,$enc_pass,$st_date,$en_date,$st_city_id,$en_city_id,$load_go_return,$load_both_ways,$load_phone)
        {
		$out="auth_error";
		if(user_class::is_authonticated($enc_pass,$user))
		{
			$fieldsArray = array('parvaz_det.id','ghimat','zarfiat','mabda_id','maghsad_id',
			'shomare','havapiema_id','sherkat_id','tarikh','saat','saat_kh','j_id');
			$fields = implode(',',$fieldsArray);
			$whereClause = '';
			$leftJoin = FALSE;
			$hasOr = ((int)trim($load_both_ways)==1 || trim($load_both_ways)=='');
			if(trim($st_date) != '' && strtotime(trim($st_date))>strtotime(date("Y-m-d")))
				$whereClause .= (($whereClause=='')?'':' and ').'  date(tarikh) >= \''.date("Y-m-d",strtotime($st_date)).'\'';
			else
				$whereClause .= (($whereClause=='')?'':' and ').'  date(tarikh) >= \''.date("Y-m-d").'\'';
			if(trim($en_date) != '')
				$whereClause .= (($whereClause=='')?'':' and ').'  date(tarikh) <= \''.date("Y-m-d",strtotime($en_date)).'\'';
			if((int)trim($st_city_id)>0)
			{
				$whereClause .= (($whereClause=='')?'':' and ').(($hasOr)?'((':'').'  mabda_id = '.trim($st_city_id).' '.(($hasOr && (int)trim($en_city_id)<=0)?') or (':'');
				$leftJoin = TRUE;
			}
			if((int)trim($en_city_id)>0)
			{
                                $whereClause .= (($whereClause=='')?'':' and ').(($hasOr && (int)trim($st_city_id)<=0)?'((':'').'  maghsad_id = '.trim($en_city_id).' '.(($hasOr)?') or (':'');
				$leftJoin = TRUE;
			}
			if($hasOr)
			{
				if((int)trim($st_city_id)>0)
					$whereClause .= '  maghsad_id = '.trim($st_city_id).' '.(($hasOr && (int)trim($en_city_id)<=0)?'))':'');
				if((int)trim($en_city_id)>0)
                                        $whereClause .= (($whereClause!='' && (int)trim($st_city_id)>0)?' and ':'').'  mabda_id = '.trim($en_city_id).' '.(($hasOr)?'))':'');
			}
			$whereClause  = ' where '.$whereClause;
			$searchQuery = "select $fields from parvaz_det left join parvaz on (parvaz.id=parvaz_id)  $whereClause"; 
			$my = new mysql_class;
			$my->ex_sql($searchQuery,$q);
/*
			foreach($q as $in=>$r)
			{
				$r['jid'] = (int)trim($r['jid']);
				$q[$in] = $r;
			}
			//$out = $q;
*/
			$out = xml_class::export($q);//$searchQuery
		}
		//return($searchQuery);
                return($out);
        }
	function search_def()
	{
		return (
			array(
				array('ws_user'=>'xsd:string','ws_pass'=>'xsd:string','st_date'=>'xsd:string','en_date'=>'xsd:string',
				      'st_city_id'=>'xsd:int','en_city_id'=>'xsd:int','load_go_return'=>'xsd:int','load_both_ways'=>'xsd:int',
				      'load_phone'=>'xsd:int'),
				array('return'=>'xsd:string'),
			        'test_wsdl',
			        'test_wsdl#search',
		  	        'rpc',
			        'encoded',
			        'search open flights . . .'
			)
		);
	}
//	var_dump(search('web_service','260062','2013-01-05','2014-02-05',1,2,1,1,1));
?>
