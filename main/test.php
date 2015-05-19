<?php
	include_once("../kernel.php");
	$m = new mysql_class;
	$ar = array();
	$m->ex_sql("select * from shahr where id in (1,75,78)",$q);
	foreach($q as $r)
		$ar[]=$r['name'];
	if($ar[0]==$ar[1])
		echo "0=1\n";
	if($ar[0]==$ar[2])
                echo "0=2\n";
	if($ar[2]==$ar[1])
                echo "2=1\n";
	//echo preg_replace('/[^\P{C}\n]+/u', '',"تست ");
	/*
	function test($str)
	{
		$out = '';
		$arr = array('a'=>'b','b'=>'c','c'=>'d','d'=>'e','e'=>'f','f'=>'g','g'=>'h','h'=>'i','i'=>'j','j'=>'k','k'=>'l','l'=>'m','m'=>'n','n'=>'o','o'=>'p','p'=>'q','q'=>'r','r'=>'s','s'=>'t','t'=>'u','u'=>'v','v'=>'w','w'=>'x','x'=>'y','y'=>'z','z'=>'a','0'=>'1','1'=>'2','2'=>'3','3'=>'4','4'=>'5','5'=>'6','6'=>'7','7'=>'8','8'=>'9','9'=>'0');
        	$rarr = array_reverse($arr,true);
		$tmp = str_split($str);
		foreach($tmp as $ch)
			$out.=isset($arr[$ch])?$arr[$ch]:$ch;
		return($out);
	}
	//var_dump(test('abcdA!'));
	//var_dump(encrypt_class::decrypt( encrypt_class::encrypt('zxczc')  ));
	$arr = array('a'=>'b','b'=>'c','c'=>'d','d'=>'e','e'=>'f','f'=>'g','g'=>'h','h'=>'i','i'=>'j','j'=>'k','k'=>'l','l'=>'m','m'=>'n','n'=>'o','o'=>'p','p'=>'q','q'=>'r','r'=>'s','s'=>'t','t'=>'u','u'=>'v','v'=>'w','w'=>'x','x'=>'y','y'=>'z','z'=>'a','0'=>'1','1'=>'2','2'=>'3','3'=>'4','4'=>'5','5'=>'6','6'=>'7','7'=>'8','8'=>'9','9'=>'0');
	$out = '<root>'."\n";
	foreach($arr as $key=>$val)
	{
		$out.='<row>'. xml_class::export(array($key=>$val)).'</row>'."\n";
	}
	$out .='</root>';
	echo $out;
	*/
?>
