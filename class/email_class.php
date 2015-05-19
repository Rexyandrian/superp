<?php
	class email_class
	{
		public function __construct($to,$subject,$message,$from='info@superparvaz.com')
		{
			$out = FALSE;
			//$to = "hscomp2002@gamil.com";
			//$subject = "ﻢﻫﺭﺩﺍﺩ";
			//$message = "ﻖﻫﺮﻣﺎﻨﻫ";
			if($to != '' && $subject != '' && $message != '')
			{
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
				$headers .= 'From: '. $from . "\r\n";
				$out =  mail($to,$subject ,$message,$headers);
			}
			return($out);
		}
	}
?>
