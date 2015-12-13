<!-- /app/View/Bets/view.ctp -->
<?php 
@$max = $places[0]['Place']['balance']; 
$this->set('title_for_layout', 'Bets: Week ' . $week['Week']['id']);
$now = new DateTime(); 
$dl = new DateTime($week['Week']['start']);
$dl->add(new DateInterval(DEADLINE_OFFSET));
?>

<section class="small-12 columns">
	<header>
		<h2>Bets - week <?=$week['Week']['id']; ?></h2>
		<ul class="tabs" data-tab>
			<li class="tab-title active"><a href="#tab1">Grid</a></li>
			<li class="tab-title"><a href="#tab2">Standings</a></li>
		</ul>
	</header>
	<div class="tabs-content">
		<div class="content active" id="tab1">
			<!--tab 1 prediction table-->
			
			<table id="grid" class="bet-grid table">
				<thead>
					<tr>
						<th class="betdate grid-header">Date</th>
						<th class="grid-header">Match</th>
						<th class="grid-header">Winner</th>
						<th class="grid-header">Return</th>
			<?php foreach($players as $k=>$p): ?>
				<th style="min-width: 130px;"><a href="/users/<?php echo $k; ?>"><?php echo $p['username']; ?></a></th>
			<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
				<?php foreach($table as $r): ?>
					<tr>
						<td class="grid-header">
							<?php echo date('D j M', strtotime($r['_match']['date'])); ?>
						</td>
						<td class="grid-header">
							<a href="/matches/<?php echo $r['_match']['id']; ?>"><?php echo $r['_match']['fixture']; ?></a>
						</td>
						<td class="grid-header">
							<?php echo $r['_match']['result']; ?>
						</td>
						<td>
							<?php echo (array_key_exists('return', $r['_match'])) ? aus($r['_match']['return']) : '-'; ?>
						</td>
						<?php foreach($players as $k=>$p): ?>
						<td class="bet-cell">
							<?php 
							if (array_key_exists($k, $r)) {
								$occlass = '';
								if (!is_null($r[$k]['res'])) {
									$occlass = ($r[$k]['res'] > 0) ? 'gbet' : 'bbet';
								}
								echo '<div class="bet">
												<span class="betamt">' . $r[$k]['bet'] . '</span>
												<span class="outcome ' . $occlass . '">' . number_format($r[$k]['res'], 2) . '</span>
											</div>';
							} else {
								echo '&nbsp;';
							}
							
							?>
						</td>
						<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php if ($now < $dl): ?>
			<a href="/bets/edit/<?=$week['Week']['id']; ?>" class="button tiny">Edit my bets</a>
			<?php endif; ?>
		</div>

		<div class="content" id="tab2">
			<div id="container" style="height: 800px; width: 60%;"></div>
		</div>
</section>

<script src="/js/vendor/jquery.dataTables.min.js"></script>
<script src="/js/vendor/dataTables.fixedColumns.js"></script>
<script>
	$(document).ready(function() {
		var dt = $('#grid').dataTable( {
			'scrollX': '100%',
			'sScrollXInner': '110%',
			'paging': false,
			'searching': false,
			'ordering': false,
			'info': false,
			'columnsDefs': [{ 'width': '90%', 'targets': 0}]
		});

		new $.fn.dataTable.FixedColumns(dt, {
			'iLeftColumns': 4
		});

		// options for highcharts
		var options = {
			colors: ['#060'],
			chart: {
				renderTo: 'container',
				type: 'bar'
			},
			title: {
				text: 'Standings'
			},
			xAxis: {
				categories: [],
				reversed: false
			},
			tooltip: {
				valueDecimals: 2,
				formatter: function() {
					return '<strong>' + this.x + '</strong><br />' + ((this.y < 0) ? ('-£' + Math.abs(this.y)) : ('£' + this.y));
				}
			},
			yAxis: {
				title: {
					text: 'Balance'
				},
				min: -100,
				labels: {
					formatter: function() {
						return (this.value < 0 ? '-' : '') + '£' + Math.abs(this.value);
					}
				}
			},
			plotOptions: {
				bar: {
					pointWidth: 20
				}
			},
			legend: {
				enabled: false
			},
			series: []
		};

		$.ajax({
			method: 'get',
			url: '/bets/bets_places/' + <?=$week['Week']['id']; ?>,
			success: function(r) {
				r = JSON.parse(r);
				options.xAxis.categories = r.names;
				options.yAxis.max = Math.max.apply(Math, r.values);
				var series = {
					data: []
				};
				$.each(r.values, function(i, v) {
					var data = {};
					if (v < 0) {
						series.data.push({
							y: v,
							color: '#f00'
						})
					} else {
						series.data.push({
							y: v
						})
					}
				})
				options.series.push(series);
				var chart = new Highcharts.Chart(options);
			}

		})

	});
</script>

