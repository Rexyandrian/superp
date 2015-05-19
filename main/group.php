<?php	
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	function loadGrooh($inp=-1)
        {
                $out=null;
		$mysql = new mysql_class;
		$inp = (int)$inp;
                $mysql->ex_sql("select `name`,`id` from `grop` order by `name`",$q);
                foreach($q as $r)
			if((int)$r["id"] != $inp)
	                        $out[$r["name"]]=(int)$r["id"];
                return $out;
        }
	function delete_item($id)
	{
		$id = (int)$id;
		$mysql = new mysql_class;
		$mysql->ex_sqlx("update `grop` set `en` = 0 where `id` = $id");
	}
	function loadGorooh($inp)
	{
		$out = '';
		foreach($inp as $text => $value)
			$out .= "<option value=\"$value\">\n$text\n</option>\n";
		return($out);
	}
	function accessCopy($inp)
	{
		$inp = (int)$inp;
		$out = "<select id=\"from_grp_$inp\" class=\"inp\" >\n".loadGorooh(loadGrooh($inp))."\n</select>\n";
		$out .= "<input type=\"button\" class=\"inp\" value=\"ارث‌بری\" onclick=\"window.open('changeAccess.php?to_grp=$inp&from_grp='+document.getElementById('from_grp_$inp').value+'&r='+Math.random()+'&');\" />";
		return($out);
	}

        function add_item()
        {
                $fields = null;
		$mysql = new mysql_class;
                foreach($_REQUEST as $key => $value)
                {
                        if(substr($key,0,4)=="new_")
                        {
                                if($key != "new_id" && $key != "new_en")
                                {
                                        $fields[substr($key,4)] =$value;
                                }
                        }
                }
                $query = '';
                $fi = "(";
                $valu="(";
                foreach ($fields as $field => $value)
                {
                        $fi.="`$field`,";
                        $valu .="'$value',";
                }
                $fi=substr($fi,0,-1);
                $valu=substr($valu,0,-1);
                $fi.=")";
                $valu.=")";
                $query="insert into `grop` $fi values $valu";
		echo $query;
                $mysql->ex_sqlx($query);
        }
	$combo["بستانکار"]=1;
	$combo["بدهکار"]=-1;
	$gname = 'grid_grop';
	$input =array($gname=>array('table'=>'grop','div'=>'main_div_grop'));
	$xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] = ' `en`=1 order by `name`';
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='نام';
	$xgrid->column[$gname][2]['name'] ='';
	$xgrid->column[$gname][] =array('name'=>'ارث بری دسترسی','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('accessCopy'));
	$xgrid->addFunction[$gname] ='add_item';
	$xgrid->deleteFunction[$gname] ='delete_item';
	$xgrid->canAdd[$gname] = TRUE;
	$xgrid->canEdit[$gname] = TRUE;
	$xgrid->canDelete[$gname] = TRUE;	
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);;
?>
	<head>		
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/grid.js"></script>
	</head>
<script type="text/javascript" >
	$(document).ready(function(){
		var args=<?php echo $xgrid->arg; ?>;
		intialGrid(args);
	});
	function changeWerc(ser)
	{
		var werc ='';
		$.each($('.'+ser),function(id,field)
		{
			werc +=((werc=='')?' where ':' and ')+" (`"+field.id+"` like '|"+trim(field.value)+"|') ";
		});

		var ggname ='<?php echo $gname; ?>';
		whereClause[ggname] = encodeURIComponent(werc);
		grid[ggname].init(gArgs[ggname]);
	}
</script>
<div align="center" id="main_div_grop">
</div>
