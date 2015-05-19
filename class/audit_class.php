<?php
class audit_class{
	public function isAdmin($typ){
		$out = FALSE;
		if($typ == 0){
			$out = TRUE;
		}
		return $out;
	}
	public function hamed_pdateBack($inp,$saat=TRUE)
        {
                $out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }

                return $out.($saat?" 12:00:00":'');
        }
        public function hamed_pdate($str)
        {
                $out=jdate('Y/n/j',strtotime($str));
                //$out=audit_class::enToPer($out);
                return $out;
        }

	public function enToPer($inNum){
		$outp = $inNum;
		$outp = str_replace('0', '۰', $outp);
		$outp = str_replace('1', '۱', $outp);
		$outp = str_replace('2', '۲', $outp);
		$outp = str_replace('3', '۳', $outp);
		$outp = str_replace('4', '۴', $outp);
		$outp = str_replace('5', '۵', $outp);
		$outp = str_replace('6', '۶', $outp);
		$outp = str_replace('7', '۷', $outp);
		$outp = str_replace('8', '۸', $outp);
		$outp = str_replace('9', '۹', $outp);
		return($outp);	
	}
	public function perToEn($inNum){
		$outp = $inNum;
		$outp = str_replace('۰', '0', $outp);
		$outp = str_replace('۱', '1', $outp);
		$outp = str_replace('۲', '2', $outp);
		$outp = str_replace('۳', '3', $outp);
		$outp = str_replace('۴', '4', $outp);
		$outp = str_replace('۵', '5', $outp);
		$outp = str_replace('۶', '6', $outp);
		$outp = str_replace('۷', '7', $outp);
		$outp = str_replace('۸', '8', $outp);
		$outp = str_replace('۹', '9', $outp);
		return($outp);	
        }
    public function hamed_jalalitomiladi($str)
	{
		$s=explode('/',$str);
		$out = "";
		if(count($s)==3){
			$y = (int)$s[0];
			$m = (int)$s[1];
			$d = (int)$s[2];
			if($d > $y)
			{
				$tmp = $d;
				$d = $y;
				$y = $tmp;
			}
			$y = (($y<1000)?$y+1300:$y);
			$miladi=jalali_to_jgregorian($y,$m,$d);
			$out=$miladi[0]."-".$miladi[1]."-".$miladi[2];
		}
		return $out;
		//jalali_to_gregorian()
	}    
	public function secondToMinute($inp)
	{
		$out = "0:0";
		$inp = (int)$inp;
		if($inp > 0)
		{
			$s = $inp % 60;
			$m = ($inp - $s) / 60;
			$out = "$m:$s";
		}
		return($out);
	}
}
?>
