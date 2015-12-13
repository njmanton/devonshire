$('document').ready(function() {
	var r = $('#UserDetailsRepeat');
	var n = $('#UserDetailsNew');
	
	$('#UserDetailsCurrent, #UserDetailsNew, #UserDetailsRepeat').keyup(function() {

		if (r.val() != '') {
			if (r.val() != n.val()) {
				r.css('color', '#f33');
				$('form').addClass('error').find('#repeat_err').text(' Passwords don\'t match');
			} else {
				r.css('color', '#060');
				$('form').removeClass('error').find('#repeat_err').text('');
			}
		}	else {
			r.css('color', '#000');
		}

	});

	$('#UserDetailsUpdateForm').submit(function() {

		var err = false;
		$('#current_err, #repeat_err, #new_err').html('&nbsp;');

		if ($('#UserDetailsCurrent').val() == '' && n.val() != '')	{
			$('#current_err').html(' required to change password').addClass('error');
			err = true;
		}
		
		if (n.val() != '' && n.val().length < 6) {
			// need to add check for lower/upper case, digits etc.
			$('#new_err').addClass('error').html(' Password must contain more than five characters');
			err = true;
		}
		
		if (n.val() != r.val()) {
			$('#repeat_err').addClass('error').html(' Passwords don\'t match');
			err = true;
		}
	
		return (!err);
	});
	
});