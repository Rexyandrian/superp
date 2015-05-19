<?php   include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
	$isAdmin = $se->detailAuth('all');
	$isAdmin = FALSE;
	function loadCities($smabda_id = -1)
	{
		$smabda_id = (int)$smabda_id;
		$out ="<option value=\"-1\">\nهمه\n</option>\n";
		$mysql = new mysql_class;
		$mysql->ex_sql("select * from `shahr` order by `name`",$q);
		foreach($q as $r)
		{
			$out .= "<option value=\"".(int)$r["id"]."\" ".(((int)$r["id"]==$smabda_id)?"selected=\"selected\"":"")." >\n";
			$out .= $r["name"]."\n";
			$out .= '</option>\n';
		}
		return($out);
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
	$domasire = TRUE;
	$gname = 'grid1';
	$input =array($gname=>array('table'=>'parvaz_det','div'=>'main_div'));
	$xgrid = new xgrid($input);
	$id = $xgrid->column[$gname][0];
	$parvaz_id = $xgrid->column[$gname][1];
	$tarikh = $xgrid->column[$gname][2];
	$saat = $xgrid->column[$gname][3];
	$zarfiat = $xgrid->column[$gname][4];
	$ghimat = $xgrid->column[$gname][5];
	$typ = $xgrid->column[$gname][6];
	$zakhire = $xgrid->column[$gname][7];
	$j_id = $xgrid->column[$gname][8];
	$poor_def =  $xgrid->column[$gname][9];
	$mablagh_kharid = $xgrid->column[$gname][10];
	$saat_kh = $xgrid->column[$gname][11];
	$can_esterdad = $xgrid->column[$gname][12];
	$en = $xgrid->column[$gname][13];
	$customer_id = $xgrid->column[$gname][14];
	if(!$isAdmin)
	{
		$xgrid->column[$gname][1] = $ghimat;
		$xgrid->column[$gname][1]['name'] = 'قیمت';

		$xgrid->column[$gname][2] = $id;
		$xgrid->column[$gname][2]['name'] = 'ظرفیت';	
	
		$xgrid->column[$gname][3] = array('name'=>'مبدأ','fieldname'=>'parvaz_id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array());
		$xgrid->column[$gname][4] = array('name'=>'مقصد','fieldname'=>'parvaz_id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array());
		$xgrid->column[$gname][5] = $parvaz_id;
		$xgrid->column[$gname][5]['name'] = 'شماره';
		$xgrid->column[$gname][6] = array('name'=>'هواپیما','fieldname'=>'parvaz_id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array());
		$xgrid->column[$gname][7] = $tarikh;
		$xgrid->column[$gname][7]['name'] = 'تاریخ';

		$xgrid->column[$gname][8] = $saat;
		$xgrid->column[$gname][8]['name'] = 'خروج';
		$xgrid->column[$gname][9] = $saat_kh;
		$xgrid->column[$gname][9]['name'] = 'ورود';

		$xgrid->column[$gname][10] = $id;
		$xgrid->column[$gname][10]['name'] = 'ک.م.س';
		$xgrid->column[$gname][11]['name']='';
		$xgrid->column[$gname][12]['name']='';
		$xgrid->column[$gname][13]['name']='';
		$xgrid->column[$gname][14]['name']='';
	}
	else
	{
		
	}
	/*
	$xgrid->column[$gname][0]['name'] ='';
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
	*/
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link type="text/css" href="../css/style.css" rel="stylesheet" />
		<link type="text/css" href="../css/xgrid.css" rel="stylesheet" />
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/grid.js"></script>
		<script type="text/javascript" src="../js/tavanir.js"></script>
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>
		صفحه اصلی
		</title>
	</head>
	<body>
		<script>
			$(document).ready(function(){
				var args=<?php echo $xgrid->arg; ?>;
				intialGrid(args);
			});
		</script>
		<table style="width:22cm;border-width:1px;border-style:dashed;border-collapse:collapse;border-color:#BCBCBC;">
								<br/>
								<br/>
								<br/>
								<tr  style="background-color:#EEEEEE;">
									<th>
										مبدأ :
									</th>
									
									<th>
										مقصد :
									</th>
																
									<th>
										از تاریخ :
									</th>
									
									<th>
										تا تاریخ :
									</th>
									
									<td align="right" >
										دوطرفه :
										<input type="checkbox" checked="checked" id="domasire" name="domasire" <?php echo (($domasire)?"checked=\"checked\"":""); ?> />
									</td>
								</tr>
								<tr >
									<td>
										<select id="smabda_id" name="smabda_id" class="inp" >
										<?php
											echo loadCities($smabda_id);
										?>
										</select>
									</td>
									<td>
										<select id="smaghsad_id" name="smaghsad_id" class="inp" >
										<?php
											echo loadCities($smaghsad_id);
										?>
										</select>
									</td>	
									<td>
										<input readonly="readonly" class="inp" style="direction:ltr;" type="text" id="saztarikh" name="saztarikh" value=""/>
										<img id="mehrdad_date_btn_9" src="../img/cal.png" style="vertical-align: top;" />
										<script type="text/javascript">
											Calendar.setup({
												inputField  : "saztarikh",   // id of the input field
												button      : "mehrdad_date_btn_9",   // trigger for the calendar (button ID)
											ifFormat    : "%Y/%m/%d",       // format of the input field
											showsTime   : true,
											dateType	: 'jalali',
											showOthers  : true,
											langNumbers : true,
											weekNumbers : true
											});
										</script>	
									</td>
									<td>
										<input readonly="readonly" class="inp" style="direction:ltr;" type="text" id="statarikh" name="statarikh" value=""/>
										<img id="mehrdad_date_btn_1" src="../img/cal.png" style="vertical-align: top;" />
										<script type="text/javascript">
											Calendar.setup({
												inputField  : "statarikh",   // id of the input field
												button      : "mehrdad_date_btn_1",   // trigger for the calendar (button ID)
											ifFormat    : "%Y/%m/%d",       // format of the input field
											showsTime   : true,
											dateType	: 'jalali',
											showOthers  : true,
											langNumbers : true,
											weekNumbers : true
											});
										</script>
									</td>
									<td>
										<img src="../img/search.jpg" style="cursor:pointer;" onclick="searchFlight();" > 
										<input style="display:none;" type="button" value="نمایش و بروزرسانی"  />
									</td>
								</tr>
							</table>
		<div id="main_div" style="overflow:auto;border:1px dotted #bbb;paddin:5px;" >
		</div>
	</body>
</html>
