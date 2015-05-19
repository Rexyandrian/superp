<?php   
	include_once("../kernel.php");
	$SESSION = new session_class;
	register_shutdown_function('session_write_close');
	session_start();
	if(!isset($_SESSION[$conf->app.'_user_id']))
                die($conf->access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($conf->access_deny);
	function loadCustomer($customer_id)
	{
		$mysql = new mysql_class;
		$out = '<option value=\'-1\' ></option>';
		$cust_id = (int)$_SESSION[$conf->app.'_customer_id'];
		$qu = "select `name`,`id` from `customers` where `id`='$cust_id'";
		if($_SESSION[$conf->app.'_customer_typ']==2)
			$qu = 'select `name`,`id` from `customers` order by `name`';
		$mysql->ex_sql($qu,$q);
		foreach($q as $r)
		{
			$sel = '';
			if($customer_id ==(int)$r['id']) 
				$sel = 'selected=\'selected\'';
			$out.="<option value=".$r['id']." $sel >".$r['name']."</option>\n";
		}
		if($_SESSION[$conf->app.'_customer_typ']==2) 
		{
			if($customer_id==-2)
				$sel = 'selected=\'selected\''; 
			$out .= "<option value='-2' $sel >همه</option>";
		}
		return $out;
	}
	function loadAdl($inp)
	{
		$ar=array(0=>'بزرگسال',1=>'کودک',2=>'نوزاد');
		return $ar[$inp];
	}
	function loadCity($inp)
	{
		$mysql = new mysql_class;
		$inp = (int)$inp;
		$out = "";
		$mysql->ex_sql("select `name` from `shahr` where `id` = '$inp'",$q);
		if(isset($q[0]))
			$out = $q[0]["name"];
		return($out);
	}
	function loadParvazInfo($inp)
	{
		$out = "&nbsp;";
		$inp = (int)$inp;
		$parvaz = new parvaz_det_class($inp);
		if($parvaz->getId() >0)
		{
			$out = $parvaz->shomare."( ".loadCity($parvaz->mabda_id)." -> ".loadCity($parvaz->maghsad_id)." )".jdate("j / m / Y",strtotime($parvaz->tarikh)).'<br />'.date("F d",strtotime($parvaz->tarikh)) ;
		}
		return($out);
	}
	function loadPrint($inp)
	{
		$out = (int)$inp;
		$tick = new ticket_class($out);
		if($tick->typ == 0)
			$out = "<u><span style='cursor:pointer;color:firebrick;' onclick=\"wopen('eticket.php?shomare=".$tick->shomare."&id=$out','',900,600)\" >".$tick->shomare."<br/> چاپ Eticket </span></u>";
		else
			$out = "<span 'color:firebrick;'>".$tick->shomare."</span>";
		//$out = $tick->typ;
		return $out;
	}
	function loadCustomerName()
	{
		$mysql = new mysql_class;
		$out = null;
		$mysql->ex_sql('select `name`,`id` from `customers` order by `name`',$q);
		foreach($q as $r)
		{
			$out[$r['id']] = (int)$r['name'];
		}
		return($out);
	}
	function loadMablagh($id)
	{
		$id = (int)$id;
		$tick = new ticket_class($id);
		$out = $tick->mablagh * (1 - $tick->poorsant/100 );
		return monize($out);
	}
	function hamed_pdate($str)
        {
                $out=jdate('H:i:s d / m / Y ',strtotime($str));
		$out .= "<br/>".date('F d',strtotime($str));
                return enToPerNums($out);
        }
	function rahgiri($inp)
	{
		$inp = ticket_class::rahgiriToCode((int)$inp,$conf->rahgiri);
		return $inp;
	}
	$parvaz_det_id = ((isset($_REQUEST['parvaz_det_id']))?$_REQUEST["parvaz_det_id"]:-1);
	$wer = ' en<>-1 and `parvaz_det_id`='.$parvaz_det_id;
	$gname = 'grid_ticket';
	$input =array($gname=>array('table'=>'ticket','div'=>'main_div_ticket'));
	$xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] = $wer;
	$xgrid->column[$gname][0]['name'] ='';
	$xgrid->column[$gname][1]['name'] ='';
	$xgrid->column[$gname][2]['name'] ='نام و نام خانوادگی';
	//$xgrid->column[$gname][2]['name'] ='نوع';
	//$xgrid->column[$gname][2]['clist'] = loadType();
	$xgrid->column[$gname][3]['name'] ='توضیحات';
	//$xgrid->column[$gname][3]['cfunction'] = array('monize');
	$xgrid->column[$gname][4]['name'] ='';
	$xgrid->column[$gname][5]['name'] ='کد رهگیری';
	$xgrid->column[$gname][3]['cfunction'] = array('rahgiri');
	$xgrid->column[$gname][6]['name'] ='';
	$xgrid->column[$gname][7]['name'] ='آژانس خریدار';
	$xgrid->column[$gname][7]['clist'] = loadCustomerName();
	$xgrid->column[$gname][8]['name'] ='';
	$xgrid->column[$gname][9]['name'] ='';
	$xgrid->column[$gname][10]['name'] ='';
	$xgrid->column[$gname][11]['name'] ='';
	$xgrid->column[$gname][12]['name'] = 'تاریخ صدور';
	$xgrid->column[$gname][12]['cfunction'] = array('hamed_pdate');
	$xgrid->column[$gname][13]['name'] ='';
	$xgrid->column[$gname][14]['name'] ='';
	$xgrid->column[$gname][] =array('name'=>'شماره بلیت','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadPrint'));
	$xgrid->column[$gname][] =array('name'=>'قیمت<br>فروخته شده','fieldname'=>'id','css'=>'','typ'=>'int','access'=>'a','cfunction'=>array('loadMablagh'));
	for($j=0;$j < count($xgrid->column[$gname]);$j++)
		$xgrid->column[$gname][$j]['access'] ='a';
	$xgrid->column[$gname][2]['access'] ='a';
	$xgrid->canEdit[$gname] = TRUE;
	$out =$xgrid->getOut($_REQUEST);
	if($xgrid->done)
		die($out);
?>
	<head>		
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/grid.js"></script>
		<style type="text/css">
			.calendar 
			{
				direction: rtl;
			}
	
			#flat_calendar_1, #flat_calendar_2
			{
				width: 200px;
			}
			.example {
				padding: 10px;
			}
	
			.display_area {
				background-color: #FFFF88
			}
		</style>
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
	</head>
<div align="center">
	<br/>
	<h3>منیفست پرواز:</h3><br />
	<?php 	
		echo "<b>".loadParvazInfo($parvaz_det_id)." <u><span style=\"color:blue;cursor:pointer;\" onclick=\"wopen('manifest2.php?parvaz_det_id=$parvaz_det_id','',900,500);\">چاپ منیفست</span></u></b><br /><br />"; 
		echo $out;
	?>
</div>
<div align="center" id="main_div_ticket">
</div>
