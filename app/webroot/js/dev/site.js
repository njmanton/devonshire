$('document').ready(function() {

	$('[name*="dirty"]').attr("value", "0");

	if ($('#PredictionViewForm').length && $('#PredictionViewForm :text').size() == 0) {
		$('#PredictionViewForm input:submit').attr('disabled', 'disabled');
	}

	$('#PredictionViewForm :text').change(function() {
		$(this).next().attr("value", "1");
	})
	.focusout(function() {
		var re = /^(\d{1,2}|[AP])-(\d{1,2}|[AP])$/i;
		if ($(this).val().match(re) || $(this).val() == '') {
			$(this).removeClass('score_val_err');
		}	else {
			$(this).addClass('score_val_err');
		}
		if ($('.score_val_err').size()) {
			$('#pred_submit').attr('disabled', 'disabled');
		} else {
			$('#pred_submit').attr('disabled', null);
		}
	});

	$('#PredictionViewForm :radio').click(function() {
		$(this).prev().prev().attr("value", "1");
	}); 

	$('#PredictionViewForm').submit(function(){
		// alow form submission if there're no enabled jokers, or at least one enabled joker is set
		if ($('input[type=radio]:enabled').length == 0 || $('input[type=radio]:enabled:checked').length > 0) {
			return true;
		} else {
			alert('You must select one fixture as your Joker');
			return false;
		}
	});

	$('#new-comp').autocomplete({
		minLength: 2,
		delay: 0,
		source: "/competitions/show",
		select: function(event, ui) {
			if (ui.item) {
				$(this).parent().addClass('error').find('#err').text('That name already exists in the database');
				return false;
			} else {
				$(this).parent().removeClass('error').find('#err').text('');
			}
		}			
	});

	// handler for games menu items
	$('#game-menus a').on('click', function() {
		var game = $(this).data('panel'),
				panel = $('div.' + game),
				button = $(this).parent();

		panel.toggle().siblings().hide();
		button.toggleClass('active').siblings().removeClass('active');
	})

	// handler for weeks/index accordion
	$('#week_list a').on('click', function() {
		var week = $(this).data('week');
		var body = $('div#week-' + week);

			$.ajax({
				type: 'GET',
				url: '/matches/week/' + week,
				dataType: 'json',
				success: function (response) {
					body.empty();
					var table = '<table class="f32 table">';
					table += '<thead><tr><th>Date</th><th style="min-width: 20em;">Competition</th><th>Fixture</th><th>Score</th>';
					table += '<tbody>';
					$(response).each(function(i, o) {
						var dt = new Date(o.Match.date).toJSON().substring(0, 10).replace('T', '');
						var score = (o.Match.score) ? o.Match.score : '-';
						table += '<tr>';
						table += '<td>' + dt + '</td>';
						table += '<td class="grid flag ' + o.Competition.country + '"><span><a href="/competitions/View/' + o.Competition.id + '">' + o.Competition.name + '</a></span></td>';
						table += '<td><a href="/matches/view/' + o.Match.id + '">' + o.TeamA.name + ' v ' + o.TeamB.name + '</a></td>';
						table += '<td>' + score + '</td>';
						table += '</tr>';
					})
					table += '</tbody></table>';
					body.append(table);
				}
			});
	});

	$('dd').eq(0).addClass('active').find('div').eq(0).addClass('active'); // open the first accordion

	$('.btn-group button').on('click', function() {
		$('#pred2').val($(this).data('sel'));
	});

	$('#kta, #ktb').autocomplete({
		minLength: 2,
		select: function(event, ui) {
			$(this).next().next().val(ui.item.id);
		},
		change: function(event, ui) {
			$(this).val((ui.item ? ui.item.value : ''));
		}
	});

	$('#kdatep').datepicker({
		dateFormat: 'D dd M',
	});

	$('.killer-edt').on('click', function() {
		var score = $(this).parents('tr').find('.pred').text();
		var matchid = $(this).data('mid');
		var pred = $(this).data('pred');
		$('#kid').val($(this).data('kid'));
		$('#week').val($(this).data('week'));
		$('#mid').val(matchid);

		if (matchid) {
			$.ajax({
				url: '/matches/' + matchid,
				success: function(response) {
					var o = JSON.parse(response);
					if (o) {
						$('#killer-match-edit #kta').val(o.TeamA.name);
						$('#killer-match-edit #ktb').val(o.TeamB.name);
						$('#killer-match-edit [name=date]').val(o.Match.date);
						$('#killer-match-edit [name=teama_id]').val(o.Match.teama_id);
						$('#killer-match-edit [name=teamb_id]').val(o.Match.teamb_id);
						$('#killer-match-edit #mid').val(o.Match.id);
						$('#killer-match-edit select option[value="' + pred + '"]').attr("selected", "selected");
						$('#killer-match-edit #new-match-box').foundation('reveal', 'open');
					}
				}
			});
		}
	});

	$("#killer-match-form").on("submit", function(event) {
		$.ajax({
			data: $('#killer-match-form').serialize(), 
			type: 'post', 
			url: '\/kentries\/add',
			success: function(response) {
				document.location.reload();
			}
		});
		return false;
	});

	// auto clear message boxes after 3s
	window.setTimeout(function() {
		$('.alert-box .close').click();
	}, 3000);

})