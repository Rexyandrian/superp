<?php
        include_once("../kernel.php");
        $SESSION = new session_class;
        register_shutdown_function('session_write_close');
        session_start();
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
        function loadCust()
        {
                $out = '<select id="customer_id" ><option value="-1" >همه</option>'."\n";
                $mysql = new mysql_class;
                $mysql->ex_sql("select `id`,`name` from `customers` order by `name` ",$q);
                foreach($q as $r)
                        $out.='<option value="'.$r['id'].'" >'.$r['name'].'</option>'."\n";
                $out .='</select>';
                return $out;
        }
        function loadCustomerName($id)
        {
                $id = (int)$id;
                $cu = new customer_class($id);
                return($cu->name);
        }
        function loadUserName($id)
        {
                $id = (int)$id;
                $cu = new user_class($id);
                return($cu->fname." ".$cu->lname);
        }
        function loadPDate($dt)
        {
                if($dt != '0000-00-00 00:00:00')
                        $out = jdate("d / m / Y",strtotime($dt));
                else
                        $out = '';
                return($out);
        }
        function loadTozihat($sanad_record_id)
        {
                $out = '----';
		$mysql = new mysql_class;
                $mysql->ex_sql("select * from `ticket` where `sanad_record_id` = $sanad_record_id ",$q);
                foreach($q as $i => $r)
                {
                        $customer_id = (int)$r["customer_id"];
                        $par = new parvaz_det_class($r['parvaz_det_id']);
			if($i == 0)
	                        $out = '<div class=".lessBlock" style="cursor:pointer;" onclick="toggleMore(this);">مسافر: <b>'.$r['fname'].' '.$r['lname'].'</b> پرواز: <b>'.enToPerNums($par->shomare).'</b> تاریخ: <b>'.loadPDate($par->tarikh).'</b></div><div class=".moreBlock" style="display:none;">';
			else
				$out .= $r['fname'].' '.$r['lname']."<br/>\n";
                }
		if($out != '----')
			$out .= "</div>";
                return($out);
        }
        function sanadJam($where='')
        {
                $out = array("sum_mablagh"=>0);
                $mysql = new mysql_class;
                $mysql->ex_sql("select sum(`mablagh`) as `sm` from `customer_daryaft` ".(($where != '')?" where $where":''),$q);
                if(isset($q[0]))
                        $out = array("sum_mablagh"=>(int)$q[0]['sm']);
                return($out);

        }
	function pateBack($pdat)
	{
		$out = audit_class::hamed_pdateBack($pdat);
		$tmp = explode(' ',$out);
		return($tmp[0]);
	}
	function moni($inp)
	{
		$inp = (int)$inp;
		$out = ($inp < 0)?'(':'';
		$out .= monize(abs($inp));
		$out .= ($inp < 0)?')':'';
		return($out);
	}
	$customer_id = (int)$_SESSION[$conf->app.'_customer_id'];
	$tday = perToEnNums(jdate("Y/m/d"));
	$tdayG = date("Y-m-d");
	$aztarikh = (isset($_REQUEST['aztarikh']))?pateBack($_REQUEST['aztarikh']):$tdayG;
	$tatarikh = (isset($_REQUEST['tatarikh']))?pateBack($_REQUEST['tatarikh']):$tdayG;
	if(isset($_REQUEST['jam']) && $_REQUEST['jam'] == 'true')
	{
		$su = sanadJam("`typ`=-1 and `sanad_typ`=0 and (date(`tarikh`) >= '$aztarikh' and date(`tarikh`) <= '$tatarikh') and `customer_id` = $customer_id ");
		die("جمع مبلغ : ".monize($su['sum_mablagh']));
	}
        $gname = "gname_customer_daryaft";
        $input =array($gname=>array('table'=>'customer_daryaft','div'=>'main_div_customer_daryaft'));
        $xgrid = new xgrid($input);
	$xgrid->whereClause[$gname] = "`typ`=-1 and `sanad_typ`=0 and (date(`tarikh`) >= '$aztarikh' and date(`tarikh`) <= '$tatarikh') and `customer_id` = $customer_id ";
	$xgrid->column[$gname][1]['cfunction'] = array('loadCustomerName');
	$xgrid->column[$gname][2]['cfunction'] = array('loadUserName');
	$xgrid->column[$gname][3]['cfunction'] = array('moni');
	$xgrid->column[$gname][4]['cfunction'] = array('loadPDate');
	$xgrid->column[$gname][5]['cfunction'] = array('loadTozihat');
	$xgrid->column[$gname][0]['name'] = '';
	$xgrid->column[$gname][1]['name'] = '';
	$xgrid->column[$gname][2]['name'] = 'کاربر';
	$xgrid->column[$gname][3]['name'] = 'مبلغ';
	$xgrid->column[$gname][4]['name'] = 'تاریخ';
	$xgrid->column[$gname][4]['sort'] = 'true';
	$xgrid->column[$gname][5]['name'] = 'توضیحات';
	$xgrid->column[$gname][6]['name'] = 'شماره سند';
	$xgrid->column[$gname][6]['sort'] = 'true';
	$xgrid->column[$gname][7]['name'] = '';
	$xgrid->column[$gname][8]['name'] = '';
        $out =$xgrid->getOut($_REQUEST);
        if($xgrid->done)
                die($out);
?>
<script type="text/javascript" >
	function toggleMore(obj)
	{
		var lessBlock = $(obj);
		if(lessBlock.length > 0)
			if(lessBlock.next().prop('class') == '.moreBlock')
				lessBlock.next().slideToggle();
	}
	function calcJam()
	{
		$("#jam").html('');
		$("#jam").load("sales_moshtari_sanad.php?customer_id="+$("#customer_id").val()+"&aztarikh="+$("#aztarikh").val()+"&tatarikh="+$("#tatarikh").val()+"&jam=true&r="+Math.random()+"&");
	}
        $(document).ready(function(){
                $("#sbit").click(function(){
			calcJam();
                        gArgs['gname_customer_daryaft']['eRequest']={'customer_id': $("#customer_id").val(),'aztarikh':$("#aztarikh").val(),'tatarikh':$("#tatarikh").val()};
                        grid['gname_customer_daryaft'].init(gArgs['gname_customer_daryaft']);
                });
                var args=<?php echo $xgrid->arg; ?>;
                intialGrid(args);
        });
</script>
	از : <input type="text" class="dateValue" id="aztarikh" name="aztarikh" value="<?php echo $tday; ?>"/>
	تا : <input type="text" class="dateValue" id="tatarikh" name="tatarikh" value="<?php echo $tday; ?>"/>
<?php
	//echo "مشتری : ".loadCust();
?>
	<button id="sbit">انتخاب</button>
<div id="main_div_customer_daryaft"></div>
<div id="jam">
<?php
	$su = sanadJam($xgrid->whereClause[$gname]); 
	echo("جمع مبلغ : ".monize($su['sum_mablagh']));
?>
</div>
