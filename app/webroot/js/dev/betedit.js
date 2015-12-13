function gcd(x, y) {
	while (y != 0) {
		var z = x % y;
		x = y;
		y = z;
	}
		return x;
}

function trad2dec(a, b) {
	var f = (a / b) + 1;
	return f;
}

function dec2trad(a) {
	if (a < 1) return 'NaN';

	var num = Math.floor((a - 1) * 100);
	gcd = gcd(num, 100);

	var x = num / gcd;
	var y = 100 / gcd;

	return (x.toString() + '/' + y.toString());

}

$(document).ready(function() {

	checkRows();
	getCount();

	// when a radio button is clicked, put the value of the button into the form field
	$('.odds-buttons button').on('click', function() {
		$(this).parents('td').find('input[type="hidden"]').val($(this).data('sel'));
		checkRows();
	});

	// checkRows is called whenever a bet line is changed, to validate the data
	function checkRows() {

		var	tot = 0,
				ret = 0,
				totspan = $('#totamount'),
				retspan = $('#totreturn'),
				cntspan = $('#totmatches');

		// iterate across all rows, disabling/enabling controls depending on the checkbox
		$('.betedit tbody tr').each(function(i) {
			var box = $(this).find('input[type="checkbox"]');
			if(box.is(':checked')) {
				$(this).removeClass('disablerow').find('.amt, button').removeAttr('disabled');
				var a = $(this).find('.amt').val();
				if (a > 0) {
					tot += parseInt(a);
					// need to find the selected element in the buttonset
					ret += parseInt(a) * ($(this).find('button.alert').text() - 1);
				}
			} else {
				$(this).addClass('disablerow').find('.amt, button').attr('disabled','disabled');
			}
			totspan.text(tot);
			retspan.text(ret);

		});
		// if amount or number of matches incorrect, add a warning
		if (tot != 100) {
			totspan.addClass('text-error');
		} else {
			totspan.removeClass('text-error');
		}
		var cnt = $('input:checkbox:checked').size();
		if (cnt > 5 || cnt < 3) {
			cntspan.addClass('text-error');
		} else {
			cntspan.removeClass('text-error');
		}
	};

	function getCount() {
	// gets the number of checkboxes ticked and displays it
		var match_count = $('input:checkbox:checked').size();
		var suffix = (match_count == 1) ? '' : 'es';
		$('#totmatches').text(match_count + ' match' + suffix);

	};

	$('input:checkbox').on('click', function() {
	// on selecting a row, make sure that user can't select more than 5 boxes
		$(this).prev().prev().attr('value', '1');
		var checked = $('input:checkbox:checked').size();
		if (checked > 5) {
			$(this).attr('checked',false);
			alert ('You have already selected the maximum of 5 matches');
			return false;
		} else {
			checkRows();
		}
		getCount();
	});

	$('#submitform').on('click', function() {
	// before submission, if amount or match count invalid, cancel the submit
		var total_amount = parseInt($('#totamount').text());
		var match_count = $('input:checkbox:checked').size();
		var e = 0;
		if (total_amount != 100) {
			alert('Your total amount bet must equal $100');
			e++;
		} else if (match_count < 3) {
			alert('You must bet on at least 3 matches');
			e++;
		}
		if (e > 0) return false;

		$('.betedit tbody tr').each(function(i) {
			var a = $(this).find('.amt').val(),
					o = $(this).find('button.alert').length,
					box = $(this).find('input[type=checkbox]');

			if (box.is(':checked')) {
				if (a < 20 || a > 60 || (a % 5) != 0) {
					alert ('Each amount must be between $20 and $60 in $5 intervals (row ' + (i + 1) + ')');
					e++;
				}
				if (o == 0) {
					alert ('you must select a result (row ' + (i + 1) + ')');
					e++;
				}
			}
		});
		return (e == 0);
	});

	$('.amt').change(function() {
		checkRows();
		if ($(this).val() < 20 || $(this).val() > 60 || $(this).val() % 5 != 0)  {
			$(this).addClass('text-error');
		} else {
			$(this).removeClass('text-error');
		}
	});

});