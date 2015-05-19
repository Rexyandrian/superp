<?php
class sms_class{
	public function sendSms($msg,$numbers,$user_id,$sanad_record_id=0)
	{
		$user=conf::sms_user;
		$pass=conf::sms_pass;
		$shomare = conf::sms_shomare;
		if(!is_array($numbers))
			$out = 'شماره وارد شده بصورت آرایه نیست';
		else
		{
			$deliver = array();
			for($i=0;$i<count($numbers);$i++)
			{
				if(!sms_class::isMobile($numbers[$i]))
					$deliver[]=array($numbers[$i]=>'-1');
				else if(sms_class::getMaxCount()>0)
				{
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "http://www.payam-resan.com/APISend.aspx");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt ($ch, CURLOPT_POST, 1);
					$post="Username=$user&Password=$pass&From=$shomare&To=".$numbers[$i]."&Text=$msg";
					curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
					$estore = curl_exec ($ch);
					curl_close ($ch);
					sms_class::decreaseSms($numbers[$i],$user_id,$sanad_record_id,$estore);
					$deliver[]=array($numbers[$i]=>$estore);
				}
				//"Http://www.payam-resan.com/APISend.aspx?Username=$user&Password=$pass&From=30007546&To=".$number[$i]."&Text=$msg"
			}
			$out = $deliver;
		}
		return $out;	
	}
	public function isMobile($number)
	{
		$out = FALSE;
/*
		$tmp = str_split($number);
		$tmp_int = (int)$number;
		$tmp_int_str =  str_split("$tmp_int");
		if($tmp[0]==0 && $tmp[1]!=0 && count($tmp)==11 && $tmp_int>0 && count($tmp_int_str)==10 )
			$out = TRUE;
*/
		return $out;
	}
	public function getMaxCount()
	{
		$out = 0;
		mysql_class::ex_sql("select sum(`maxi`-`cont`) as `realmax` from `sms_charge`",$q);
		if($r = mysql_fetch_array($q))
		{
			if((int)$r['realmax']>0)
				$out = (int)$r['realmax'];
		}
		return $out;
	}
	public function decreaseSms($number,$user_id,$sanad_record_id,$status)
	{
		mysql_class::ex_sql("select `id` from `sms_charge` where `cont`<`maxi` order by `id` limit 1 ",$q);
		if($r=mysql_fetch_array($q))
			mysql_class::ex_sqlx('update `sms_charge` set `cont`=`cont`+1 where `id`='.$r['id']);
		//---------------------------------------------------------
		mysql_class::ex_sqlx("insert into `sms` (`number`,`user_id`,`sanad_record_id`,`sent`) values ('$number','$user_id','$sanad_record_id','$status')");
	}
}
?>
