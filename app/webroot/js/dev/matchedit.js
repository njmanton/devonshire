function initNewMatchBox(startDate) {

	x = 10000; // start of new ids

	$('#ta, #tb').autocomplete({
		source: '/teams/show',
		minLength: 3,
		select: function(event, ui) {
			//$(this).next().next().val(teamsid[$.inArray(ui.item.value, teams)]);
			$(this).next().next().val(ui.item.id);
		},
		change: function(event, ui) {
			$(this).val((ui.item ? ui.item.value : ''));
		}
	});

	$('[name="comp"]').autocomplete({
		source: '/competitions/show',
		minLength: 2,
		select: function(event, ui) {
			$(this).next().next().val(ui.item.id);
			if (ui.item.id) {
				$(this).addClass('inputflag ' + ui.item.country);
			} else {
				$(this).removeClass('inputflag ' + ui.item.country);
			}
		},
		change: function(event, ui) {
			$(this).val((ui.item ? ui.item.value : ''));
		}
	});

	$('[name="date"]').datepicker({
		dateFormat: 'D dd M yy',
		minDate: startDate,
		maxDate: new Date(startDate.getTime() + (60 * 60 * 24 * 6 * 1000))
	});

	$('#match-rows').on('click', '.del', function(e) {

		var result = confirm('Are you sure you want to delete this record?');
		var row = $(this).parents('tr');

		if (result) {
			$.ajax({
				type: 'POST',
				url: $(this).attr('href'),
				data: 'ajax=1',
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						row.fadeOut();
					} else {
						alert('Sorry, something seems to have gone wrong with the deletion!');
					}
				}

			});
		}
		return false;
	});

	$('#gm-toggle').on('click', function() {
		if ($(this).is(':checked')) {
			$('#gotw').removeAttr('disabled', false);
		} else {
			$('#gotw').attr('disabled', 'disabled');
		}
	});

	$('#odd-toggle').on('click', function() {
		if ($(this).is(':checked')) {
			$('[name^="odds"]').removeAttr('disabled');
			$('[name="comp_id"]').val(1);
			$('[name="comp"]').val('Premier League');
		} else {
			$('[name^="odds"]').attr('disabled', 'disabled');
		}
	});

	$("#new-match-form").bind("submit", function (event) {
		$.ajax({
			async: true, 
			data: $('#new-match-form').serialize(), 
			type: 'post', 
			url: '\/matches\/add',
			success: function() {
				document.location.reload();
			}
		});
		return false;
	});

	$('.edt').on('click', function() {
		var matchid = $(this).data('match');
		$.ajax({
			async: true,
			url: '/matches/view/' + matchid,
			success: function(response) {
				var o = JSON.parse(response);
				if (o.Match.game & 1) {  // goalmine game
					$('#gm-toggle').prop('checked', true);
					$('#gotw').removeAttr('disabled', false);
				}
				if (o.Match.game & 2) { // tipping game
					$('#odd-toggle').prop('checked', true);
					$('[name^="odds"]').removeAttr('disabled');
				}
				if (o.Match.gotw) { // gotw
					$('#gotw').prop('checked', true);
				}
				$('#ta').val(o.TeamA.name);
				$('#tb').val(o.TeamB.name);
				$('[name=comp]').val(o.Competition.name);
				$('[name=date]').val(o.Match.date);
				$('[name=game]').val(o.Match.game);
				$('[name=odds1]').val(o.Match.odds1);
				$('[name=odds2]').val(o.Match.odds2);
				$('[name=oddsX]').val(o.Match.oddsX);
				$('[name=teama_id]').val(o.Match.teama_id);
				$('[name=teamb_id]').val(o.Match.teamb_id);
				$('[name=comp_id]').val(o.Match.competition_id);
				$('[name=comp]').addClass('inputflag ' + o.Competition.country);
				$('[name=week]').val(o.Match.week_id);
				$('[name=matchid]').val(o.Match.id);
				$('#new-match-box').foundation('reveal', 'open');
			}
		});

	});

};
