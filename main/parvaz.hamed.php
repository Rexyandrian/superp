<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadCompany()
	{
		$out = null;
		mysql_class::ex_sql("select * from `sherkat` order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$out[$r["name"]] = (int)$r["id"];
		}
		return($out);
	}
        function loadCity()
        {
                $out = null;
                mysql_class::ex_sql("select * from `shahr` order by `name`",$q);
                while($r = mysql_fetch_array($q))
                {
                        $out[$r["name"]] = (int)$r["id"];
                }
                return($out);
        }
        function loadPlain()
        {
                $out = null;
                mysql_class::ex_sql("select * from `havapeima` order by `name`",$q);
                while($r = mysql_fetch_array($q))
                {
                        $out[$r["name"]] = (int)$r["id"];
                }
                return($out);
        }
	function tarikh($inp)
	{
		$inp = (int)$inp;
		$out ="<u><span style=\"color:firebrick;cursor:pointer;\" onclick=\"wopen('tarikhparvaz.php?parvaz_id=$inp&','',500,200);\">ادامه</span></u>";
		return($out);
	}
	function loadCustomer()
	{
		$out = '';
		mysql_class::ex_sql("select `id`,`name` from `customers` where `en` = 1 order by `name` ",$q);
		while($r = mysql_fetch_array($q))
		{
			$out[$r['name']] = $r['id'];
		}
		return $out;
	}
	function add_item()
	{
		$fields = array();
		foreach($_REQUEST as $key => $value)
		{
			if(strpos($key,"new_") === 0 && $key != "new_id" && $key != "new_en")
			{
				$fields[substr($key,4)] = $value;
			}
		}
		$fields["rang"] = "#".$fields["rang"];
		$fi = "(";
		$va = "(";
		foreach($fields as $key => $value)
		{
			$fi .= "`$key`,";
			$va .= "'$value',";
		}
		$fi = substr($fi,0,-1);
		$va = substr($va,0,-1);
		$fi .= ")";
		$va .= ")";
		$query = "insert into `parvaz` $fi values $va";
		mysql_class::ex_sqlx($query);
	}
	function edit_item($id,$feild,$value)
	{
		if($feild != "rang")
			mysql_class::ex_sqlx("update `parvaz` set `$feild` = '$value' where `id` = $id");
		else
			mysql_class::ex_sqlx("update `parvaz` set `$feild` = '#$value' where `id` = $id");
	}
	$noe["عمومی"] = 0;
        $noe["خصوصی"] = 1;
        $noe["صندوق"] = 2;
	$yesNo["خیر"] = 0;
	$yesNo["بله"] = 1;
 	$grid = new jshowGrid_new("parvaz","grid1");
	$grid->index_width = '20px';
	$grid->width = '95%';
	$grid->addFeild("id");
	$grid->columnHeaders[0] = null;
	$grid->columnHeaders[1] = "شماره پرواز";
	$grid->columnHeaders[2] = null;
	$grid->columnHeaders[3] = "شرکت";
	$grid->columnHeaders[4] = "مبدا";
	$grid->columnHeaders[5] = "مقصد";
	$grid->columnHeaders[6] = "هواپیما";
	$grid->columnHeaders[7] = "قیمت مصوب";
	$grid->columnHeaders[8] = "ظرفیت پایه";
	$grid->columnHeaders[9] = "ساعت پرواز";
	$grid->columnHeaders[10] = "ساعت ورود <br>به مقصد";
	$grid->columnHeaders[11] = "کمیسیون پایه(به درصد)";
	$grid->columnHeaders[12] = "نوع پرواز";
	$grid->columnHeaders[13] = "مبلغ خرید<br>پیشفرض";
	$grid->columnHeaders[14] = "شرکت<br/>فروشنده";
	$grid->columnHeaders[15] = "رنگ";
	$grid->columnHeaders[16] = "شناور است";
        $grid->columnHeaders[17] = "تعریف پرواز جهت رزرو";
	$grid->columnLists[3] = loadCompany();
        $grid->columnLists[4] = loadCity();
        $grid->columnLists[5] = loadCity();
        $grid->columnLists[6] = loadPlain();
	$grid->columnLists[14] = loadCustomer();
        $grid->columnLists[12] = $noe;
	$grid->columnLists[16] = $yesNo;
	$grid->columnFunctions[7] = "monize";
	$grid->columnFunctions[13] = "monize";
	$grid->columnFunctions[17] = "tarikh";
	$grid->addFunction = "add_item";
	$grid->editFunction = "edit_item";
	$grid->intial();
	$grid->executeQuery();
	$out = $grid->getGrid();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<!-- Style Includes -->
		<link type="text/css" href="../js/jquery/themes/trontastic/jquery-ui.css" rel="stylesheet" />
		<link type="text/css" href="../js/jquery/window/css/jquery.window.css" rel="stylesheet" />

		<link type="text/css" href="../css/style.css" rel="stylesheet" />

		<!-- JavaScript Includes -->
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>

		<script type="text/javascript" src="../js/jquery/jquery-ui.js"></script>
		<script type="text/javascript" src="../js/jquery/window/jquery.window.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
                <script type="text/javascript" src="../js/jscolor/jscolor.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		سامانه مدیریت آژانس مسافرتی
		</title>
	</head>
	<body>
		<div align="center">
			<br/>
			<br/>
			<?php echo $out;  ?>
			برای انتخاب رنگ برروی جعبه روبرو کلیک کنید :
			<input class="color" id="colorp"/> 
		</div>
		<script language="javascript">
			var inps = document.getElementsByTagName("input");
			var tmp;
			var tempo;
			for(var i=0;i < inps.length;i++)
			{
				tmp = inps[i].id.split("_");
				if(tmp.length==3 && tmp[2]=='rang')
				{
					tempo = document.getElementById(inps[i].id+"_back");
					tempo.style.backgroundColor = inps[i].value;
				}
			}
		</script>
	</body>
</html>
