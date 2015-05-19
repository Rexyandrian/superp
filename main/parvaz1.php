<?php   include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die('error');
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die('error');
	function loadCompany()
	{
		$out = null;
		$mysql = new mysql_class;
		$mysql->ex_sql("select * from `sherkat` order by `name`",$q);
		 foreach($q as $r)
			$out[(int)$r["id"]] = $r["name"];
		return($out);
	}
        function loadCity()
        {
                $out = null;
		$mysql = new mysql_class;
                $mysql->ex_sql("select * from `shahr` order by `name`",$q);
                foreach($q as $r)
                        $out[(int)$r["id"]] = $r["name"];
                return($out);
        }
        function loadPlain()
        {
                $out = null;
		$mysql = new mysql_class;
                $mysql->ex_sql("select * from `havapeima` order by `name`",$q);
                 foreach($q as $r)
                        $out[(int)$r["id"]] =$r["name"];
                return($out);
        }
	function tarikh($inp)
	{
		$inp = (int)$inp;
		//$out ="<u><span style=\"color:firebrick;cursor:pointer;\" onclick=\"wopen('tarikhparvaz.php?parvaz_id=$inp&','',500,200);\">ادامه</span></u>";
		$out = "<span style=\"color:firebrick;cursor:pointer;\" onclick=\"defineParvaz_det($inp);\" >ادامه</span>";
		return($out);
	}
	function loadCustomer()
	{
		$out = '';
		$mysql = new mysql_class;
		$mysql->ex_sql("select `id`,`name` from `customers` where `en` = 1 order by `name` ",$q);
		foreach($q as $r)
			$out[(int)$r['id']] =$r["name"] ;
		return $out;
	}
	function grid1_add($gname,$table,$fields,$col)
	{
		$conf = new conf;
		function fieldToId($col,$fieldName)
		{
			$out = -1;
			foreach($col as $id=>$f)
				if($f['fieldname']==$fieldName)
					$out = $id;
			return $out;
		}
		//$rang = $fields['rang'];
		//$rtmp = explode('#',$rang);
		//if($rtmp[0]!='#')
		$fields["rang"] = "#".$fields["rang"];
		$fi = "(";
                $valu="(";
                foreach ($fields as $field => $value)
                {
			$f_id = fieldToId($col,$field);
			$fn = (isset($col[$f_id]['cfunction']) && isset($col[$f_id]['cfunction'][1]))?$col[$f_id]['cfunction'][1]:'';
                        $fi.="`$field`,";
                        $valu .="'".(($fn!='')?$fn($value):$value)."',";
                }
                $fi=substr($fi,0,-1);
                $valu=substr($valu,0,-1);
                $fi.=")";
                $valu.=")";
		$query="insert into `$table` $fi values $valu";
		$mysql = new mysql_class;		
		$ln = $mysql->ex_sqlx($query,FALSE);
		$out = $mysql->insert_id($ln);
		$mysql->close($ln);
		$ret = FALSE;
		if($out>0)
			$ret = TRUE;
		return $ret;
	}
	$noe["عمومی"] = 0;
        $noe["خصوصی"] = 1;
        $noe["صندوق"] = 2;
	$yesNo["خیر"] = 0;
	$yesNo["بله"] = 1;
	$gname = 'grid1';
	$input =array($gname=>array('table'=>'parvaz','div'=>'main_div'));
	$xgrid = new xgrid($input);
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='شماره پرواز';
	$xgrid->column[$gname][2]['name'] ='';
	$xgrid->column[$gname][3]['name'] ='شرکت';
	$xgrid->column[$gname][3]['clist'] = loadCompany();
	$xgrid->column[$gname][4]['name'] ='مبدا';
	$xgrid->column[$gname][4]['clist'] = loadCity();
	$xgrid->column[$gname][5]['name'] ='مقصد';
	$xgrid->column[$gname][5]['clist'] = loadCity();
	$xgrid->column[$gname][6]['name'] ='هواپیما';
	$xgrid->column[$gname][6]['clist'] = loadPlain();
	$xgrid->column[$gname][7]['name'] ='قیمت مصوب';
	$xgrid->column[$gname][7]['cfunction'] = array('monize');
	$xgrid->column[$gname][8]['name'] ='ظرفیت پایه';
	$xgrid->column[$gname][9]['name'] ='ساعت پرواز';
	$xgrid->column[$gname][10]['name'] ='ساعت ورود';
	$xgrid->column[$gname][11]['name'] ='ک.م.س<br/>%';
	$xgrid->column[$gname][12]['name'] ='نوع';
	$xgrid->column[$gname][12]['clist'] = $noe;
	$xgrid->column[$gname][13]['name'] ='مبلغ خرید';
	$xgrid->column[$gname][13]['cfunction'] = array('monize');
	$xgrid->column[$gname][14]['name'] ='فروشنده';
	$xgrid->column[$gname][14]['clist'] = loadCustomer();
	$xgrid->column[$gname][15]['name'] ='رنگ';
	$xgrid->column[$gname][16]['name'] ='شناور است';
	$xgrid->column[$gname][16]['clist'] = $yesNo;
	$xgrid->column[$gname][] =array('name'=>'تعریف','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('tarikh'));
	$xgrid->canEdit[$gname] = TRUE;
	$xgrid->canAdd[$gname] = TRUE;
	$xgrid->canDelete[$gname] = TRUE;
	$xgrid->addFunction[$gname] = 'grid1_add';
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);
?>
		<script>
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
			$(document).ready(function(){
				$(".color").ColorPicker({
					onSubmit: function(hsb, hex, rgb, el) {
						$(el).val(hex);
						$(el).ColorPickerHide();
					},
					onBeforeShow: function () {
						$(this).ColorPickerSetColor(this.value);
					}
				})
				.bind('keyup', function(){
					$(this).ColorPickerSetColor(this.value);
				});
				var args=<?php echo $xgrid->arg; ?>;
				intialGrid(args);
			});
			function defineParvaz_det(id)
			{
				openDialog('tarikhparvaz.php?parvaz_id='+id,'تعریف پرواز',{'minWidth':500,'minHeight':200},false);
			}
		</script>
		<div id="main_div" style="overflow:auto;border:1px dotted #bbb;paddin:5px;" >
		</div>
<!--		<div align="center" style="z-index:15;height:500px;">
			<br/><br/><br/><br/><br/><br/><br/>-->
			برای انتخاب رنگ برروی جعبه روبرو کلیک کنید :
			<input class="color" id="colorp"/>
<!--		</div>-->

