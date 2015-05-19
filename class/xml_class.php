<?php
	class xml_class
	{
		function import($inp)
		{
			return(var_export($inp,TRUE));
		}
		public function export1($array)
		{
			$RSS_PHP = new rss_php; 
			$RSS_PHP->loadArray($array); #the second param is an optional root node name
			return($RSS_PHP->getXML()); 
		}
		public function export_small($array, $level=1) {
		    $xml = '';
		    foreach ($array as $key=>$value) {
			$key = strtolower($key);
			if (is_object($value)) {$value=get_object_vars($value);}// convert object to array
			
			if (is_array($value)) {
			    $multi_tags = false;
			    foreach($value as $key2=>$value2) {
			     if (is_object($value2)) {$value2=get_object_vars($value2);} // convert object to array
				if (is_array($value2)) {
				    $xml .= str_repeat("\t",$level)."<$key>\n";
				    $xml .= array_to_xml($value2, $level+1);
				    $xml .= str_repeat("\t",$level)."</$key>\n";
				    $multi_tags = true;
				} else {
				    if (trim($value2)!='') {
					if (htmlspecialchars($value2)!=$value2) {
					    $xml .= str_repeat("\t",$level).
						    "<$key2><![CDATA[$value2]]>". // changed $key to $key2... didn't work otherwise.
						    "</$key2>\n";
					} else {
					    $xml .= str_repeat("\t",$level).
						    "<$key2>$value2</$key2>\n"; // changed $key to $key2
					}
				    }
				    $multi_tags = true;
				}
			    }
			    if (!$multi_tags and count($value)>0) {
				$xml .= str_repeat("\t",$level)."<$key>\n";
				$xml .= array_to_xml($value, $level+1);
				$xml .= str_repeat("\t",$level)."</$key>\n";
			    }
		      
			 } else {
			    if (trim($value)!='') {
				if (htmlspecialchars($value)!=$value) {
				    $xml .= str_repeat("\t",$level)."<$key>".
					    "<![CDATA[$value]]></$key>\n";
				} else {
				    $xml .= str_repeat("\t",$level).
					    "<$key>$value</$key>\n";
				}
			    }
			}
		    }
		    return $xml;
		}
		public function export($arr)
		{
			$out = '<root>'."\n";
			foreach($arr as $key=>$val)
			{
				$out.='<row>'."\n". xml_class::export_small(array($key=>$val)).'</row>'."\n";
			}
			$out .='</root>'."\n";
			return($out);
		}
	}
?>
