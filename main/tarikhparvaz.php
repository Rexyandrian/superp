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
	function hamed_pdateBack1($inp)
        {
                $out = FALSE;
                $tmp = explode("/",$inp);
                if (count($tmp)==3)
                {
                        $y=(int)$tmp[2];
                        $m=(int)$tmp[1];
                        $d=(int)$tmp[0];
                        if ($d>$y)
                        {
                                $tmp=$y;
                                $y=$d;
                                $d=$tmp;
                        }
                        if ($y<1000)
                        {
                                $y=$y+1300;
                        }
                        $inp="$y/$m/$d";
                        $out = audit_class::hamed_jalalitomiladi(audit_class::perToEn($inp));
                }
			return $out;
        }
	function hamed_pdate($str)
	{
		$out=jdate('Y/n/j',strtotime($str));
                return enToPerNums($out);
	}
	$saztarikh = ((isset($_REQUEST["saztarikh"]))?hamed_pdateBack($_REQUEST["saztarikh"]):date("Y-m-d"));
	$statarikh = ((isset($_REQUEST["statarikh"]))?hamed_pdateBack($_REQUEST["statarikh"]):date("Y-m-d"));
	$parvaz_id = ((isset($_REQUEST["parvaz_id"]))?(int)$_REQUEST["parvaz_id"]:-1);
	if(isset($_REQUEST["saztarikh"]))
	{
		$parvaz = new parvaz_class($parvaz_id);
		$days = explode(',',$_REQUEST["days"]);
		if($parvaz_id > 0 )
		{
			$parvaz->fromDateToDate($saztarikh,$statarikh,$days);	
			die('true');
		}
		else
			die('false');
	}
?>
<script>
	$(document).ready(function(){
                $.each($(".dateValue"),function(id,field){
	                if(field.id)
        		        Calendar.setup({
		                inputField     :    field.id,
		                button:    field.id,
		                ifFormat       :    "%Y/%m/%d",
		                dateType           :    "jalali",
		                weekNumbers    : false
                		});
		});
		$("#sabt").click(function(){
			var saztarikh = $("#saztarikh").val();
			var statarikh = $("#statarikh").val();
			var days='';
			var parvaz_id =  $("#parvaz_id").val();
			$("#sabt").prop('disabled',true);
			$("#par_msg").removeClass();
			$("#par_msg").html('<img src="../img/status_fb.gif" >');
			$.each($(".day_week"),function(id,field){
				if($(field).prop("checked"))
					days+=((days!='')?',':'')+$(field).val();
			});
			$.get("tarikhparvaz.php?parvaz_id="+parvaz_id+"&saztarikh="+saztarikh+"&statarikh="+statarikh+"&days="+days+"&rr="+Math.random()+'&',function(result){
				result=trim(result);
				if(result=="true")
				{
					$("#par_msg").html('ثبت با موفقیت انجام شد');
					$("#par_msg").addClass('msg');
					$("#par_msg").fadeIn(1500);
				}	
				else if(result=="false")
				{
					$("#par_msg").html('خطا در ثبت پرواز ، دوباره تلاش نمایید');
					$("#par_msg").addClass('notice');
					$("#par_msg").fadeIn(1500);
				}
				$("#sabt").prop('disabled',false);
			});
			
		});
	});
	function checkAll()
	{
		$(".day_week").prop('checked',!$(".day_week").prop('checked'));
	}
</script>
<div align="center">
	<br/>
	<br/>
	<form id="tarikh" method="post">
		<table>
			<tr>
				<td>
					از تاریخ :
				</td>
				<td>
					<input class="dateValue" style="direction:ltr;" type="text" id="saztarikh" name="saztarikh" value="<?php echo hamed_pdate($saztarikh); ?>"/>
				</td>
				<td>
					تا تاریخ :
				</td>
				<td>
					<input class="dateValue" style="direction:ltr;" type="text" id="statarikh" name="statarikh" value="<?php echo hamed_pdate($statarikh); ?>"/>
				</td>
			</tr>
			<tr>
                                <td colspan="4">
					<table>
						<tr>
							<td>
								شنبه‌ها
								<input class="day_week" type="checkbox" id="shanbe" name="days[]" value="6" />
							</td>
                                                        <td>
                                                                یکشنبه‌ها
                                                                <input class="day_week" type="checkbox" id="yekshanbe" name="days[]" value="7" />
                                                        </td>
                                                        <td>
                                                                دوشنبه‌ها
                                                                <input class="day_week" type="checkbox" id="doshanbe" name="days[]" value="1" />
                                                        </td>
                                                        <td>
                                                                سه‌شنبه‌ها
                                                                <input class="day_week" type="checkbox" id="seshanbe" name="days[]" value="2" />
                                                        </td>
                                                        <td>
                                                                چهارشنبه‌ها
                                                                <input class="day_week" type="checkbox" id="chaharshanbe" name="days[]" value="3" />
                                                        </td>
                                                        <td>
                                                                پنج‌شنبه‌ها
                                                                <input class="day_week" type="checkbox" id="panjshanbe" name="days[]" value="4" />
                                                        </td>
                                                        <td>
                                                                جمعه‌ها
                                                                <input class="day_week" type="checkbox" id="jome" name="days[]" value="5" />
                                                        </td>
						</tr>
					</table>
				</td> 
			</tr>
			<tr>
				<td colspan="2">
					<input type='button' value='انتخاب همه' class='inp' onclick="checkAll();">		
				</td>
				<td colspan="2" align='left' >
					<input type="button" value="ثبت" id="sabt" />
					<input type="hidden" id="parvaz_id" name="parvaz_id" value="<?php echo $parvaz_id; ?>" />
				</td>
			</tr>
		</table>
	</form>
	<div id="par_msg" >
	</div>
</div>
	
