<?php   session_start();
        include_once("../kernel.php");
        if(!isset($_SESSION[conf::app.'_user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION[conf::app.'_user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	function loadCities($smabda_id = -1)
	{
		$smabda_id = (int)$smabda_id;
		$out ="<option value=\"-1\">\nهمه\n</option>\n";
		mysql_class::ex_sql("select * from `shahr` order by `name`",$q);
		while($r = mysql_fetch_array($q))
		{
			$out .= "<option value=\"".(int)$r["id"]."\" ".(((int)$r["id"]==$smabda_id)?"selected=\"selected\"":"")." >\n";
			$out .= $r["name"]."\n";
			$out .= "</option>\n";
		}
		return($out);
	}
	$today = date("Y-m-d");
	$aftertomarrow = date("Y-m-d",strtotime($today." + 1 month"));
	$smabda_id = ((isset($_REQUEST["smabda_id"]))?$_REQUEST["smabda_id"]:-1);
	$smaghsad_id = ((isset($_REQUEST["smaghsad_id"]))?$_REQUEST["smaghsad_id"]:-1);
	$domasire = FALSE;
	$domasire = ((isset($_REQUEST["domasire"]) && $_REQUEST["domasire"])?TRUE:FALSE);
	$saztarikh = ((isset($_REQUEST["saztarikh"]))?$_REQUEST["saztarikh"]:$today);
	$statarikh = ((isset($_REQUEST["statarikh"]))?$_REQUEST["statarikh"]:$aftertomarrow);
	$whereClause = " `tarikh` >= '$saztarikh' and `tarikh` <= '$statarikh' ";
	//and  parvaz_id in (select id from `parvaz` where
	$whereClause1 = "";
	if($smabda_id > 0)
	{
		$whereClause1 .= " (`mabda_id` = '$smabda_id'";
		if($domasire)
			$whereClause1 .= " or `maghsad_id` = '$smabda_id'";
		$whereClause1 .= ")";
	}
        if($smaghsad_id > 0)
        {
                $whereClause1 .= " and (`maghsad_id` = '$smaghsad_id'";
                if($domasire)
                        $whereClause1 .= " or `mabda_id` = '$smaghsad_id'";
                $whereClause1 .= ")";
        }
	if($whereClause1 != "")
	{
		$whereClause .= "and  parvaz_id in (select id from `parvaz` where $whereClause1)";
	}
?>
<html>
	<head>

		<script language="javascript">
			function searchFlight()
			{
				document.getElementById('search_form').submit();
			}
		</script>


	</head>
	<body dir="rtl">
		<?php
			echo $whereClause;
		?>
		<form id="search_form" method="get">
			<table>
				<tr>
					<td>
						مبدأ :
					</td>
					<td>
						<select id="smabda_id" name="smabda_id" >
						<?php
							echo loadCities($smabda_id);
						?>
						</select>
					</td>
					<td>
						مقصد :
					</td>
                                        <td>
                                                <select id="smaghsad_id" name="smaghsad_id" >
                                                <?php
							echo loadCities($smaghsad_id);
                                                ?>
                                                </select>
                                        </td>
					<td>
						دومسیره :
					</td>
                                        <td>
						<input type="checkbox" id="domasire" name="domasire" <?php echo (($domasire)?"checked=\"checked\"":""); ?> />
                                        </td>
				</tr>
				<tr>
					<td>
						از تاریخ :
					</td>
                                        <td>
						<input type="text" id="saztarikh" name="saztarikh" value="<?php echo $saztarikh; ?>"/>
                                        </td>
                                        <td>
                                                تا تاریخ :
                                        </td>
                                        <td>
                                                <input type="text" id="statarikh" name="statarikh" value="<?php echo $statarikh; ?>"/>
                                        </td>
					<td colspan = "2">
						<input type="button" value="جستجو" onclick="searchFlight();" />
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>
