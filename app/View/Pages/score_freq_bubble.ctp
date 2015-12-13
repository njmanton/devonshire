<?php 
echo $this->Html->script('highcharts');
?>

<section>
	
	<header>
		<h2>Bubble Chart of scores in Goalmine Matches</h2>
		<p class="alert alert-box info"><strong>Work in Progress!</strong></p>
		<p>The bubble chart below shows the distribution of goals scored home and away. The size of the bubble represents the number of matches with that result.</p>
	</header>

	<div id="score-freq-chart" style="width: 90%; max-width: 800px; margin: 10px auto;"></div>

</section>

<script>
	$(document).ready(function() {

		var ajaxScores = [];
		var ajaxPreds = [];
		var point;
		var pointP;
		var chart;
		var options;

		$.ajax({
			type: 'GET',
			async: false,
			url: '/matches/scoreFreq/',
			dataType: 'json',
			success: function(response) {
				console.log(response);
				jQuery.each(response, function(i, val) {
					point = {
						y: parseInt(i.substring(0,1)),
						x: parseInt(i.substring(2)),
						marker: {
							radius: parseInt(val.score)*2
						}
					};
					pointP = {
						y: parseInt(i.substring(0,2)),
						x: parseInt(i.substring(2)),
						marker: {
							radius: parseInt(val.pred)*2
						}
					};

					ajaxScores.push(point);
					ajaxPreds.push(pointP);

				});
			}
		});

		options = {
			chart: {
				renderTo: 'score-freq-chart',
				type: 'scatter',
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
				title: {
					text: 'Away'
				},
				max: 7,
				min: 0,
				tickInterval: 1,
				gridLineWidth: 1
			},
			yAxis: {
				title: {
					text: 'Home'
				},
				max: 7,
				min: 0,
				tickInterval: 1
			},
			tooltip: {
				formatter: function() {
					return '<strong>Result: ' + this.x + '</strong><br />Value: ' + this.y + '%';
				}
			},
			series: [{
				marker: {
					symbol: 'circle',
					fillColor: 'rgba(24,90,169,0.5)',
					lineColor: 'rgba(24,90,169,0.75)',
					lineWidth: 1,
					color: 'rgba(24,90,169,1)',
					states: {
						hover: {
							enabled: false
						}
					}
				},
				name: 'scores',
				data: ajaxScores
				//pointWidth: 12
			}, {
				marker: {
					symbol: 'circle',
					fillColor: 'rgba(238,46,47,0.3)',
					lineColor: 'rgba(238,46,47,0.75)',
					lineWidth: 1,
					color: 'rgba(238,46,47,1)',
					states: {
						hover: {
							enabled: false
						}
					}
				},
				name: 'preds',
				data: ajaxPreds
			}]
		};
		cht = new Highcharts.Chart(options);

	});


</script>