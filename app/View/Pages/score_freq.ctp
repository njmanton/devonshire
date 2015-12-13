<?php 
echo $this->Html->script('highcharts');
?>

<section>
	
	<header>
		<h2>Frequency of scores in Goalmine Matches</h2>
		<p>The chart below shows a histogram of scores from each match picked in Goalmine. Clicking a bar will show a table of all matches with that score.</p>
	</header>

	<div id="score-freq-chart" style="width: 90%; max-width: 800px; margin: 10px auto;"></div>

</section>

<script>
	$(document).ready(function() {

		var ajaxLabels = [];
		var ajaxScores = [];
		var ajaxPreds = [];
		
		var chart;
		var options;

		$.ajax({
			type: 'GET',
			async: false,
			url: '/matches/scoreFreq/',
			dataType: 'json',
			success: function(response) {
				jQuery.each(response, function(i, val) {
					ajaxScores.push(parseFloat(val.score));
					ajaxPreds.push(parseFloat(val.pred));
					ajaxLabels.push(i);
				});
			}
		});

		options = {
			chart: {
				renderTo: 'score-freq-chart',
				type: 'column',
				height: 600
			},
			colors: [
				'rgba(69,114,167,0.85)',
				'rgba(170,70,67,0.85)'
			],
			legend: {
				enabled: true
			},
			title: {
				text: null
			},
			credits: {
				enabled: false
			},
			xAxis: {
				categories: ajaxLabels,
				labels: {
					rotation: 90
				},				
				title: {
					text: 'score'
				}
			},
			yAxis: {
				labels: {
					formatter: function() {
						return Highcharts.numberFormat(this.value,1);
					}
				},
				title: {
					text: 'Proportion (%)'
				}
			},
			tooltip: {
				formatter: function() {
					return '<strong>Result: ' + this.x + '</strong><br />Value: ' + this.y + '%';
				}
			},
			plotOptions: {
				series: {
					cursor: 'pointer',
					pointPadding: 0.1,
					events: {
						click: function(event) {
							window.location.href = '/matches/byScore/' + event.point.category;
						}
					}
				}
			},
			series: [{
				name: 'scores',
				data: ajaxScores,
				pointWidth: 12
			}, {
				name: 'predictions',
				data: ajaxPreds,
				pointWidth: 12
			}]
		};
		cht = new Highcharts.Chart(options);

	});


</script>