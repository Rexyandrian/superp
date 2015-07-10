var command='';
var phpFile='';
var exteraData='';
var output='';
var refreshVar;
var canRefresh = true;
var refreshTimeout = 60000;
var noAppointmet = 'جلسه‌ای موجود نیست.';
var subAmount = 'اعتبار شما از هزینه جلسات کمتر است.';
var appointmentOk = 'جلسه/ات با موفقیت رزرو شد.';
var appointmentError = 'خطا در بروزرسانی لطفا مجددا سعی نمایید.';
var selectmoshaverbuttonValue = 'انتخاب مشاور';
var selectappointmentValue = 'انتخاب جلسه';
var searchButtonValue = 'جستجو';
var addMoshaverOk = 'زمان جلسه\ات با موفقیت ثبت گردید.';
var addMoshaverError = 'خطا در ثبت جلسات.';
var jalaseTyps = '';
var startPage = 'login.php';
//	HTML Objects
var dateDiv = 'dateDiv'
var balanceDiv = 'balanceDiv';
var searchHeader = 'searchHeader';
var searchStartButton = 'startSearch';
var searchResult = 'searchResult';
var appointmentResult = 'appointmentResult';
var startSearchDiv = 'startSearchDiv';
var addMoshaverTimeDiv = 'addMoshaverTimeDiv';
var addMoshaverTime = 'addMoshaverTime';
//	end of HTML Ojects
//	Refresh Block
function getRefresh()
{
	$('#'+dateDiv).load('date.php');
	$('#'+balanceDiv).load('balance.php');
	$("#"+dateDiv).show('slow');
	$("#"+balanceDiv).show('slow');
	if(canRefresh)
                out = setTimeout("getRefresh();",refreshTimeout);
	refreshVar = out;
	return(out);
}
function startRefresh()
{
	var out;
	if(canRefresh)
		out = getRefresh();
	return(out);
}
function stopRefresh()
{
	canRefresh = false;
}
//	End of Refresh Block
//	Search Block
function fetchSearchFields()
{
	output = '';
	phpFile = 'loadSearch.php';
	command = 'search';
	$.getJSON('loadSearch.php',function(result){
		output = result;
		createSearchFields(result);
	});
}
function createSearchFields(result)
{
	if(trim(String(result)) == 'false')
		window.location = startPage;
	var ht = '<form id="searchform">';
	var indx = 0;
	$.each(result, function(i, field)
	{
		ht += i+' : <select id="search_'+indx+'" name="'+i+'" >';
		$.each(field, function(j, inField)
		{
			ht += '<option value = "'+inField+'">'+inField+'</option>';
		});
		ht += '</select>';
		indx++;
	});
	ht += 'نام و نام خانوادگی مشاور : <input type="text" id="moshaver" name="moshaver" value="" />';
	ht += '<input type="button" id="searchButton" value="'+searchButtonValue+'" />';
	ht += '</form>';
	$('#'+searchHeader).html(ht);
	$('#searchButton').click(function(){
		getSearchResult();
	});
	$('#'+searchHeader).show('slow');
}
function getSearchResult()
{
	var tmp;
	var searchFields = {};
	$.each($("select"),function(id,field){
		tmp = field.id.split('_');
		if(tmp.length == 2 && tmp[0] == 'search')
			searchFields['search_'+field.name] = field.options[field.selectedIndex].value;
	});
	searchFields['search_moshaver'] = $("#moshaver").val();
	tmp = '?'+$.param(searchFields)+'&';
	output = '';
        phpFile = 'search.php';
        command = 'dosearch';
        $.getJSON('search.php'+tmp,function(result){
                output = result;
                showSearchResult(result);
        });
}
function showSearchResult(result)
{
        if(trim(String(result)) == 'false')
                window.location = startPage;
	var ht = '<form id="moshaverselect">';
	$.each(result,function(id,name){
		ht += "<input type='checkbox' id='moshaver_" + id + "' name='moshaver_" + id + "' />" + name + "<br/>\n";
	});
	ht += "<input type='button' id='selectmoshaverbutton' value='"+selectmoshaverbuttonValue+"' /><br/>\n";
	ht += '</form>';
	$('#'+searchResult).html(ht);
	$('#selectmoshaverbutton').click(function(){
                selectMoshaver();
        });
	$('#'+searchResult).show('slow');
}
function selectMoshaver()
{
	var tmp;
        var searchFields = {};
        $.each($("input"),function(id,field){
                tmp = field.id.split('_');
                if(tmp.length == 2 && tmp[0] == 'moshaver' && field.type == 'checkbox' && field.checked)
                        searchFields[field.name] = field.name;
        });
        tmp = '?'+$.param(searchFields)+'&';
        output = '';
        phpFile = 'getMoshaverTime.php';
        command = 'getmoshavertime';
        $.getJSON('getMoshaverTime.php'+tmp,function(result){
                output = result;
                showAppointment(result);
        });
}
function getJalaseTyps()
{
	output = '';
        phpFile = 'jalase_typ.php';
        command = 'loadjalasetyps';
        $.get('jalase_typ.php',function(result){
                output = result;
                jalaseTyps = result;
        });
}
function showAppointment(result)
{
        if(trim(String(result)) == 'false')
                window.location = startPage;
	var ht = '';
	$('#'+appointmentResult).html('');
        $.each(result,function(id,name){
                ht += "<input type='checkbox' id='jalase_" + id + "' name='jalase_" + id + "' />" + name;
		ht += "<select id='jalasetyp_" + id + "' name='jalasetyp_" + id + "'>\n"+jalaseTyps+"</select>";
		ht += "<input type='text' id='jalasetoz_" + id + "' name='jalasetoz_" + id + "' />" + "<br/>\n";
        });
	if(ht!='')
	{
	        ht += "<input type='button' id='selectappointment' value='"+selectappointmentValue+"' /><br/>\n";
		$('#'+appointmentResult).html(ht);
		$('#selectappointment').click(function(){
                	confirmAppointment();
	        });
		$('#'+appointmentResult).show('slow');
	}
	else
		alert(noAppointmet);
}
//	End of Search Block
//	Appointment Block
function confirmAppointment()
{
	var tmp;
        var searchFields = {};
        $.each($("input"),function(id,field){
                tmp = field.id.split('_');
                if(tmp.length == 2 && tmp[0] == 'jalase' && field.type == 'checkbox' && field.checked)
		{
                        searchFields[field.name] = field.name;			
			searchFields[$("#jalasetyp_"+String(tmp[1])).attr('name')] = $("#jalasetyp_"+String(tmp[1])).val();
			searchFields[$("#jalasetoz_"+String(tmp[1])).attr('name')] = $("#jalasetoz_"+String(tmp[1])).val();
		}
        });
	var user_id = $("#user_id").val();
        tmp = '?'+$.param(searchFields)+'&user_id='+user_id+'&';
        output = '';
        phpFile = 'confirmAppointment.php';
        command = 'confirmApp';
        $.get('confirmAppointment.php'+tmp,function(result){
                output = result;
                confirmResult(result);
        });
}
function confirmResult(result)
{
        if(trim(String(result)) == 'false')
                window.location = startPage;
	result = trim(String(result));
	if(result=='amount')
	{
		alert(subAmount);
		$('#'+appointmentResult).hide('slow');
	}
	else if(result=='true')
	{
		alert(appointmentOk);
		$('#'+appointmentResult).hide('slow');
		$('#'+searchResult).hide('slow');
	}
	else
	{
		alert(appointmentError);
		$('#'+appointmentResult).hide('slow');
		$('#'+searchResult).hide('slow');
	}
}
//	End of Appointment Block
//	Moshaver Time Block
function addMoshaverTimeFunc()
{
	var aztarikh = $("#aztarikh").val();
	var tatarikh = $("#tatarikh").val();
	var jalase_numbers = {};
	var tmp;
	$.each($("input"),function(id , field){
		tmp = field.id.split('_');
		if(tmp.length == 2 && tmp[0] == 'number' && field.type == 'checkbox' && field.checked)
			jalase_numbers[field.id] = tmp[1];
	});
	jalase_numbers["aztarikh"] = aztarikh;
	jalase_numbers["tatarikh"] = tatarikh;
        tmp = '?'+$.param(jalase_numbers)+'&';
        output = '';
        phpFile = 'addMoshaverTime.php';
        command = 'addMoshaverTime';
        $.get('addMoshaverTime.php'+tmp,function(result){
                output = result;
                addMoshaverResult(result);
        });
}
function addMoshaverResult(result)
{
        if(trim(String(result)) == 'false')
                window.location = startPage;
        result = trim(String(result));
        if(result=='true')
                alert(addMoshaverOk);
        else
                alert(addMoshaverError);
	$("[type='checkbox']").attr("checked",false);	
}
//	End of Moshaver Time Block
function init()
{
	$("div").hide();
	$("#main1").show();
	$("#"+startSearchDiv).show('slow');
	$("#"+addMoshaverTimeDiv).show('slow');
	$('#'+searchStartButton).click(function(){
		fetchSearchFields();
	});
	$('#'+addMoshaverTime).click(function(){
                addMoshaverTimeFunc();
        });
	getJalaseTyps();
	//refreshVar = startRefresh();
}
