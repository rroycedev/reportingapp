function display_error(msg)
{
	$('#errormsg').html(msg);
	$('#errordiv').removeClass('alert-warning').removeClass('alert-success').addClass('alert-danger').addClass('show');
}

function display_warning(msg)
{
	$('#errormsg').html(msg);
	$('#errordiv').removeClass('alert-danger').removeClass('alert-success').addClass('alert-warning').addClass('show');
}

function display_success(msg)
{
	$('#errormsg').html(msg);
	$('#errordiv').removeClass('alert-danger').removeClass('alert-warning').addClass('alert-success').addClass('show');
}