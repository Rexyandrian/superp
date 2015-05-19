function newprofile_init()
{
	$("#sabt").click(function(){
		sendForm('profileTable');
	});
	$("#cancel").click(function(){
                window.location='login.php';
        });
}
function validateForm(tid)
{
	var out = ''
	$.each($("#profileTable input"),function(id,field){
		
	});
	return(out);
}
function sendForm(tid)
{
	var email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
	var out = {};
        $.each($("#profileTable input"),function(id,field){
		out[field.name] = field.value;
        });
	if(!email_regex.test($("#email_add").val()))
	{
		alert('آدرس پست الکترونیک به درستی وارد شود.');
		return(false);
	}
	var tmp = '?'+$.param(out)+'&';
	$.get('newprofile.php'+tmp,function(result){
		if(result == 'true')
		{
			alert('کاربری شما با موفقیت ثبت گردید و حالا می توانید با اطلاعات خود به سیستم وارد شوید.');
			window.location = 'login.php';
		}
		else
			alert('خطا در ثبت ، لطفاً مجدد سعی نمایید.');
	});
	return(true);
}
