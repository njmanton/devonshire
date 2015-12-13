function hc_usercharts(wid, points, wpoints, matchcolsplus, matchcolsmin, matchrolling, fix) {

	$('.nav-tabs').children(':first').addClass('active');

	var gm_chart;
	gm_chart = new Highcharts.Chart({
		chart: {
			backgroundColor: 'rgba(255,255,255,0)',
			renderTo: 'goalmine-graph',
			type: 'column'
		},
		credits: { enabled: false },
		title: {
			text: null
		},
		colors: ['#4572a7', '#d4a017'],
		xAxis: {
			lineColor: '#999',
			categories: wid,
			title: {text: 'Week'}
		},
		yAxis: {
			gridLineColor: '#ddd',
			lineColor: '#999',
			title: {text: 'Points'},
			allowDecimals: false,
			tickInterval: 2
		},
		plotOptions: {
			series: {
				stacking: 'normal',
				cursor: 'pointer',
				events: {
					click: function(event) {
						window.location.href = '/predictions/' + wid[(event.point.x)] + '';
					}
				}
			},
			column: {
				borderWidth: 0,
			}
		},
		legend: {enabled: false},

		tooltip: {
			formatter: function() {
				
				return '<strong>Week: ' +
								this.x + 
								'</strong><br />Points: ' + 
								this.y +
								'<br /><span style="font-size: 80%; font-style: italic;">(click to see week)<span>';

			}
		},
		
		series: [
			{type: 'column', name: 'points', data: points},
			{type: 'column', name: 'points2', data: wpoints}

			]
	});

	var tip_chart;
	tip_chart = new Highcharts.Chart({
		chart: {
			renderTo: 'tip-chart',
			type: 'column'
		},
		title: {
			text: null
		},
		credits: { enabled: false },
		xAxis: {
			categories: []
		},
		yAxis: {
			title: {text: 'Dollars'}
		},
		legend: {enabled: false},
		tooltip: {
			formatter: function() {
				return fix[this.x];
			}
		},
		plotOptions: {
			series: {stacking: 'normal'}
		},
		legend: {enabled: false},
		series: [
			{type: 'column', name: 'winnings', data: matchcolsplus},
			{type: 'column', name: 'losses', data: matchcolsmin},
			{type: 'line', name: 'total', data: matchrolling}
			]
	});

}