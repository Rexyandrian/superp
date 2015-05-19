<?php   
        include_once("../kernel.php");
        $SESSION = new session_class;
        register_shutdown_function('session_write_close');
        session_start();
	//$other_js = 'alert("جلسه کاری شما منقشی شده و یا شما به این صفحه دسترسی ندارد ، لطفاً مجددا وارد شوید");window.location="login.php";';
	$other_js = 'window.location="login.php";';
        if(!isset($_SESSION[$conf->app.'_user_id']))
                die($other_js);
        $se = security_class::auth((int)$_SESSION[$conf->app.'_user_id']);
        if(!$se->can_view)
                die($other_js);
	$statusImg = '../img/status_fb.gif';
	$effectIn = "slideDown";
	$effectOut = "hide";
	$exitConfirm = 'آیا مایل به خروج هستید؟';
	$startPage = 'login.php';
?>
var canRefresh = true;
var refreshTimeout = 300000;
var statusImg = '<?php echo $statusImg; ?>';
var exitConfirm = '<?php echo $exitConfirm; ?>';
var startPage = '<?php echo $startPage; ?>';
var app = '<?php echo $conf->app; ?>';
var contentDivMain = 'body';
var currentAddr = '#';
var onlineData={};
var lastRefreshDiv = 'lastRefresh';
var dialogDivId = "dialog";
var reserveDiv = 'reserve_div';
var dialogRefresh = true;
var menuClass = 'menuClass';
var menuHoverClass = 'menuHoverClass';
var adIndx = 1;
var parvaz_id = {};
function createDialog(dialogDiv)
{
        $("#"+dialogDiv).dialog({
                autoOpen : false,
                show: "slide",
                hide: "drop",
                modal: true,
                resizable: false,
                minWidth :700,
                minHeight : 600,
                position : 'center',
                closeOnEscape: true,
                beforeClose: function(event, ui) {
			if(dialogRefresh)
				refreshPage();
			dialogRefresh = true;
                        return(true);
                }
        });

}
function openDialog(addr,tit,siz,df)
{
	siz = (typeof siz !== 'undefined' && typeof siz !== 'boolean')?siz:{'minWidth':700,'minHeight':600};
	df = (typeof df === 'boolean')?df:true;
	df = (typeof siz === 'boolean')?siz:df;
	dialogRefresh = df;
        $("#"+dialogDivId).html("<img src='"+statusImg+"' alt='Loading . . .'/>");
        if($("#"+dialogDivId).dialog)
        {
                $("#"+dialogDivId).dialog("option","title",tit);
		$("#"+dialogDivId).dialog("option","minWidth",siz.minWidth);
		$("#"+dialogDivId).dialog("option","minHeight",siz.minHeight);
                $("#"+dialogDivId).dialog("open");
                $("#"+dialogDivId).load(addr);
        }
}
function closeDialog()
{
	if($("#"+dialogDivId).dialog)
		$("#"+dialogDivId).dialog("close");
}
function jToM(fi)
{
        fi = unFixNums(fi);
        var tmpTr = fi.split('/');
        var Y = parseInt(tmpTr[0],10);
        var D = parseInt(tmpTr[2],10);
        var m = parseInt(tmpTr[1],10);
        if(D>Y)
        {
                Y = parseInt(tmpTr[2]);
                D = parseInt(tmpTr[0]);
        }
        tmpTr = JTG(Y,m,D,'');
        tmpTr = tmpTr.replace(/\//g,'-');
        return tmpTr;
}
function getRefresh()
{
	refreshPage();
	if(canRefresh)
                out = setTimeout("getRefresh();",refreshTimeout);
	refreshVar = out;
	return(out);
}
function startRefresh(refreshTime)
{
	refreshTime = (typeof refreshTime !== 'undefined')?refreshTime:300000;
	var out;
	refreshTimeout = parseInt(refreshTime,10);
	canRefresh = true;
	out = getRefresh();
	return(out);
}
function stopRefresh()
{
	canRefresh = false;
}
function statusWait(id)
{
	$(id).html('<img src="'+statusImg+'" />');
}
function loadPage(addr)
{
	parvaz_id = {};
	currentAddr = addr;
	statusWait("#"+contentDivMain);
	statusWait("#"+contentDivMain);
	$("#"+contentDivMain).load(addr,function(){
		$("#"+contentDivMain).<?php echo $effectOut; ?>();
		$("#"+contentDivMain).<?php echo $effectIn; ?>('slow');
		if($("#"+reserveDiv).length > 0)
		{
			$("#"+reserveDiv).topLeft(300,10);
			$(window).resize(function() {
				if($("#"+reserveDiv).length > 0)
					$("#"+reserveDiv).topLeft(300,10);
			});
		}
		
	});
}
function refreshPage()
{
	loadPage(currentAddr);
}
function getOnlineUsers()
{
	$.getJSON("online.php",function(result){
		onlineData = result;
		var onCount  = 0;
		for(i in onlineData)
			onCount++;
		$("#onlineCount").html(onCount);
	});
}
function addExtraMenu()
{
	//$(".menuDiv").append("<button onclick=\"getOnlineUsers();\">online</button>");
}
function removeMenuClass()
{
	$(".topMenu td").removeClass(menuClass+' '+menuHoverClass);
	$.each($(".topMenu td"),function(id,value){
		if($("#"+value.id+" img").prop("id")==currentAddr)
			$(value).addClass(menuHoverClass);
	});
}
function readyMenu()
{
	$(".topMenu td").css("cursor","pointer");
	$(".topMenu td img").tooltip();
	$(".topMenu td").mouseover(function(){
		removeMenuClass();
		$(this).addClass(menuHoverClass);
	});
	$(".topMenu td").mouseout(function(){
		removeMenuClass();
		if($("#"+this.id+" img").prop("id")!=currentAddr)
			$(this).addClass(menuClass);
        });
	$(".topMenu td").click(function(){
                $(".topMenu td").removeClass(menuClass+' '+menuHoverClass);
		var addr = $("#"+this.id+" img").prop("id");
		$(".topMenu td").addClass(menuClass);
		$(this).addClass(menuHoverClass);
		if(addr == 'exit')
		{
			if(confirm(exitConfirm))
				window.location="login.php";
		}
		else
			loadPage(addr);
	});
        $(".topMenu td").removeClass(menuClass+' '+menuHoverClass);
	$(".topMenu td:first").addClass(menuHoverClass);
	loadPage($(".topMenu td:first img").prop("id"));
}
function loadHeader()
{
	$("#header").<?php echo $effectOut; ?>();
	$("#header").load("header.php",function(){
                $(this).<?php echo $effectIn; ?>('slow');
        });
}
function loadFooter()
{
	$("#foorer").<?php echo $effectOut; ?>();
        $("#footer").load("footer.php",function(){
                $(this).<?php echo $effectIn; ?>('slow');
        });
}
function locateGcom()
{
	var gcom = $("#gcom");
	$("#add").tooltip();
	gcom.hide();
	gcom.css("position","absolute");
	gcom.css("top","0px");
	gcom.css("left","20px");
	gcom.css("height","<?php echo ($conf->kharidar_link=='')?'40':'80px'; ?>");
	gcom.slideDown('slow');
}
function lastRefreshStart()
{
	if($("#lastRefresh").length == 0)
	{
		var ht = "<td id=\"lastRefresh\" align=\"center\">آخرین بروزرسانی : <div>00:00</div></td>";
		$(".topMenu td:last").after(ht);
	}
	setTimeout("addTimeRefresh();",1000);	
}
function addSecond(inp)
{
	var tmp = String(inp).split(":");
	var m,s;
	if(tmp.length == 2)
	{
		s = parseInt(tmp[1],10);
		m = parseInt(tmp[0],10);
		if(s<55)
			s++;
		else
		{
			s = 0;
			m++;
		}
	}
	else if(tmp.length == 1)
	{
		s = parseInt(tmp[0],10);
		if(s<55)
                        s++;
                else
                {
                        s = 0;
                        m=1;
                }
	}
	return(String(m)+":"+String(s))
}
function addTimeRefresh()
{
	if($("#lastRefresh").length != 0)
        {
		var tim = trim($("#lastRefresh div").html());
		$("#lastRefresh div").html(addSecond(tim));
		setTimeout("addTimeRefresh();",1000);
	}
}
function removeRefreshObj()
{
	$("#lastRefresh").remove();
}
function loadAd()
{
	if($(".ad").length > 0)
	{
		$(".ad").fadeOut('slow',function(){
			$(".ad").load("adLoader.php?indx="+adIndx+"r="+Math.random()+"&",function(){
				$(this).fadeIn('slow');
				adIndx++;
				setTimeout("loadAd();",60000);
			});
		});
	}
}
function getParvazData(pid)
{
	var outPP = {};
	var startIndx = ((isAdmin())?4:2);
	if($("#ch_"+pid).length > 0)
	{
		thisTr = $("#ch_"+pid).parent().parent().parent().prop("id");
		rowNum = $("#ch_"+pid).parent().prop("id").split("-")[3];
		ghimat = trim($("#"+thisTr+" td:lt("+String(startIndx)+"):last span").html());
		zarfiat = trim($("#"+thisTr+" td:lt("+String(startIndx+1)+"):last span").html());
		mabda = trim($("#"+thisTr+" td:lt("+String(startIndx+2)+"):last span").html());
		maghsad = trim($("#"+thisTr+" td:lt("+String(startIndx+3)+"):last span").html());
		shomare = trim($("#"+thisTr+" td:lt("+String(startIndx+4)+"):last span").html());
		hava = trim($("#"+thisTr+" td:lt("+String(startIndx+5)+"):last span").html());
		tarikh = trim($("#"+thisTr+" td:lt("+String(startIndx+6)+"):last span").html());
		khorooj = trim($("#"+thisTr+" td:lt("+String(startIndx+7)+"):last span").html());
		vorood = trim($("#"+thisTr+" td:lt("+String(startIndx+8)+"):last span").html());
		comision = trim($("#"+thisTr+" td:lt("+String(startIndx+9)+"):last span").html());
		toz = trim($("#"+thisTr+" td:lt("+String(startIndx+10)+"):last span").html());
		if(isAdmin())
		{
			var tmp = zarfiat.split('>');
			var tmp1 =tmp.length>1?tmp[1].split('<'):'';
			zarfiat = tmp1[0];
		}
		outPP = {'ghimat':ghimat,'zarfiat':zarfiat,'mabda':mabda,'maghsad':maghsad,'shomare':shomare,'hava':hava,'tarikh':tarikh,'khorooj':khorooj,'vorood':vorood,'comision':comision,'toz':toz};//,'rowNum':rowNum};
	}
	//parvaz_id[pid] = {'jids':jids,'mabda':mabda,'maghsad':maghsad};
	return(outPP);
}
function mehrdadDump(Obj)
{
	for(i in Obj)
		alert(i+"=>"+Obj[i]);
}
function loadBargasht(pid)
{
	var outLL = false;
	if($("#ch_"+pid).length > 0)
        {
		jids = $("#ch_"+pid).prop("name").split('-');
		if(jids.length > 0)
		{
			var outLL = {};
			for(i in jids)
			{
				tmpPid = parseInt(jids[i],10);
				if(tmpPid > 0 && !isNaN(tmpPid) && $("#ch_"+tmpPid).length > 0)
					outLL[tmpPid] = getParvazData(tmpPid);
			}
		}
	}
	return(outLL);
}
function deSelectParvaz(pid)
{
	$("#ch_"+pid).prop("checked",false);
	if(parvaz_id[pid])
		delete parvaz_id[pid];
}
function selectParvaz(pid,checkObj)
{
	if($("#ch_"+pid).length > 0 && $(checkObj).length > 0)
	{
		$.each($('[name = "'+$(checkObj).prop("name")+'"]'),function(id,value){
			tmppid = $(value).prop("id").split("_")[2];
			deSelectParvaz(tmppid);
		});
		if(!$("#ch_"+pid).prop("checked"))
		{
			$("#ch_"+pid).prop("checked",true);
			parvaz_id[pid] = getParvazData(pid);
		}
	}
}
function showBargasht(pid,bars)
{
	out = false;
	if($("#ch_"+pid).length > 0)
	{
		thisTr = $("#ch_"+pid).parent().parent().parent().prop("id");
		var ht = ''
		for(i in bars)
		{
			ht += "<tr id=\""+thisTr+"-sub-"+i+"\" class=\"subTr\"><td><input onclick=\"selectParvaz("+i+",this);\" type=\"radio\" name=\"bargasht_"+pid+"\" id=\"bargasht_"+pid+"_"+i+"\"/></td>";
			for(j in bars[i])
				ht += "<td class=\"subTd\">"+bars[i][j]+"</td>";
			ht += "</td>";
		}
		if(ht != '')
		{
			ht = "<tr class=\"subTr\"><td class=\"subTd\" colspan=\"12\"><b>پرواز‌های برگشت پرواز بالا(اجباری)</b></td></tr>"+ht;
			$("#"+thisTr).after(ht);
			$("#"+thisTr+"-sub").hide();
			$("#"+thisTr+"-sub").slideDown('slow');
			$(".subTd").css("color","#fff");
			out = true;
		}
	}
	return(out);
}
function parvazjids(pid)
{
	var ou = [];
	if($("#ch_"+pid).length > 0)
		if($("#ch_"+pid).prop("name") != '0')
			ou = $("#ch_"+pid).prop("name").split('-');
	return(ou);
}
function parvazHasJid(pid)
{
	var ou = false;
	if($("#ch_"+pid).length > 0)
		ou = ($("#ch_"+pid).prop("name") != '');
	return(ou);
}
function mehrdadParseInt(inp)
{
	var ou = (!isNaN(parseInt(inp,10)) && (typeof parseInt(inp,10) !== 'undefined'))?parseInt(inp,10):0;
	return(ou);
}
function checkAdult()
{
	var ou = true;
	if($("#reserve_adl").length == 0 || ($("#reserve_adl").length > 0 && $("#reserve_adl").val() == 0))
		ou = "حتماً یک نفر بزرگسال باید انتخاب شود";
	else
	{
		var tedad = mehrdadParseInt($("#reserve_adl").val()) + mehrdadParseInt($("#reserve_chd").val()) ;
		for(i in parvaz_id)
			if(parseInt(trim(parvaz_id[i]['zarfiat']),10) < tedad)
				ou = "ظرفیت پرواز ها کمتر از تعداد درخواستی است";
	}
	return(ou);
}
function continueReserve()
{
	var pids = '';
	for(i in parvaz_id)
		pids += ((pids!='')?',':'')+String(i);
	openDialog("ticket_check.php?selected_parvaz="+pids+"&adl="+$("#reserve_adl").val()+"&chd="+$("#reserve_chd").val()+"&inf="+$("#reserve_inf").val()+"&ticket_type=0&",'رزرو پرواز',{'minWidth':800,'minHeight':600},false);
}
function isAdmin()
{
	ou = $("#reserve_adl").is("input");
	return(ou);
}
function initReserve(gname,reserveArgs)
{
	if($(".sel").length > 0)
	{
		$(".sel").click(function(){
			pid = $(this).prop("id").split('_')[1];
			if($(this).prop("checked"))
			{
				parvaz_id[pid] = getParvazData(pid);
				if(!isAdmin())
				{
					$(".subTr").remove();
					var bars = loadBargasht(pid);
					if(bars !== false)
						showBargasht(pid,bars);
				}
			}
			else
			{
				if(!isAdmin())
					$(".subTr").remove();
				if(parvaz_id[pid])
					delete parvaz_id[pid];
			}
			return(true);
		});
	}
	$("#reserve").click(function(){
		plength = 0;
		pids = [];
		for(i in parvaz_id)
		{
			plength++;
			pids[pids.length] = i;
		}
		if(plength == 2)
		{
			praft = parvaz_id[pids[0]];
			pbargasht = parvaz_id[pids[1]];
			if(praft.mabda == pbargasht.maghsad && pbargasht.mabda == praft.maghsad)
			{
				rjid = parvazjids(pids[0]);
				bjid = parvazjids(pids[1]);
				rnojid = (rjid.length == 0 || (rjid.length == 1 && rjid == ''));
				bnojid = (bjid.length == 0 || (bjid.length == 1 && bjid == ''));
				f1 = (rnojid && bnojid);
				f2 = (!rnojid && bnojid && jQuery.inArray(pids[1],rjid) > -1);
				f3 = (!bnojid && rnojid && jQuery.inArray(pids[0],bjid) > -1);
				f4 = (!rnojid && !bnojid && jQuery.inArray(pids[0],bjid) > -1 && jQuery.inArray(pids[1],rjid) > -1);
				if(f1 || f2 || f3 || f4 || isAdmin())
				{
					chA = checkAdult();
					if(chA === true)
						continueReserve();
					else
						alert(chA);
				}	
				else
				{
					alert('پروازهای انتخابی دومسیره یک دیگر نیستند.');
					$(".sel").prop("checked",false);
					$(".subTr").remove();
					parvaz_id = {};
				}
			}
			else
			{
				alert('پروازهای انتخابی می بایست رفت و برگشت باشند.');
				$(".sel").prop("checked",false);
				$(".subTr").remove();
				parvaz_id = {};
			}
		}
		else if(plength == 1)
		{
			praft = parvaz_id[pids[0]];
			praftjids = parvazjids(pids[0]);
			if(!isAdmin() && ((praftjids.length > 0 && praftjids != '')  || (typeof praft.jid !== 'undefined') || (parvazHasJid(pids[0]))))
			{
				alert('این پرواز می‌بایست با یک پرواز مسیر برگشت انتخاب شود.');
				$(".sel").prop("checked",false);
                                $(".subTr").remove();
                                parvaz_id = {};
			}
			else
			{
				chA = checkAdult();
				if(chA === true)
					continueReserve();
				else
					alert(chA);
			}
		}
		else if(plength == 0)
		{
			alert('هیچ پروازی انتخاب نشده است');
		}
		else
		{
			alert('حداکثر دو پرواز بطور همزمان باید انتخاب شوند');
			$(".sel").prop("checked",false);
			parvaz_id = {};
		}
	});
}
function init()
{
        jQuery.fn.center = function () {
	        var myHeight = this.height();
        	var parentHeight = this.parent().height();
	        var myWidth = this.width();
        	var parentWidth = this.parent().width();
	        this.css("position","absolute");
	        this.css("top", Math.max(0, (parentHeight - myHeight) / 2) + "px");
	        this.css("left", Math.max(0, (parentWidth - myWidth) / 2) + "px");
        	return this;
        }
        jQuery.fn.bottomLeft = function (topOff,leftOff) {
		if(this.length > 0)
		{
			topOff = (typeof topOff !== 'undefined')?parseInt(topOff,10):10;
			leftOff = (typeof leftOff !== 'undefined')?parseInt(leftOff,10):10;
			this.css("position","fixed");
			var wh = $(window).height();
			var oh = this.height();
			var tt = String(wh-oh-topOff)+"px";
			this.css("top",tt);
			this.css("left",String(leftOff)+"px");
		}
	}
	jQuery.fn.topLeft = function (topOff,leftOff) {
		if(this.length > 0)
		{
			topOff = (typeof topOff !== 'undefined')?parseInt(topOff,10):10;
			leftOff = (typeof leftOff !== 'undefined')?parseInt(leftOff,10):10;
			this.css("position","fixed");
			var wh = $(window).height();
			var oh = this.height();
			var tt = String(topOff)+"px";
			this.css("top",tt);
			this.css("left",String(leftOff)+"px");
		}
	}
	loadHeader();
	loadFooter();
	loadAd();
	$(".menuDiv").<?php echo $effectOut; ?>();
	$(".menuDiv").load("menu.php",function(){
		$(this).<?php echo $effectIn; ?>('slow');
		readyMenu();
		addExtraMenu();
	});
	setTimeout("locateGcom();",4000);
	createDialog(dialogDivId);
	$('.log').ajaxComplete(function(e, xhr, settings) {
		var tmp =settings.url.split('?');
		var phpPage = tmp[0];
		var parameters = {};
		var tmp1,tmp2;
/*
		if((phpPage == 'home.php' || phpPage == 'home_admin.php') && phpPage == currentAddr)
			lastRefreshStart();
		else
			removeRefreshObj();
		for(var i = 1;i < tmp.length;i++)
		{
			tmp1 = tmp[i].split('&');
			for(j in tmp1)
			{
				tmp2 = tmp1[j].split('=');
				if(tmp2.length == 2)
					parameters[tmp2[0]] = tmp2[1];
			}
		}
		if(phpPage!='log.php' && phpPage!='date.php' && phpPage!='balance.php')
			$.get("log.php?phpPage="+phpPage+"&parameters="+JSON.stringify(parameters)+"&r="+Math.random()+"&",function(result){
			});
*/
		var result = xhr.responseText;
		if(result == 'error')
		{
			alert('در ارتباط شما با سرور مشکلی پیش آمده ، لطفاً مجدداً وارد شوید.');
			window.location = "login.php";
		}
	});
}
