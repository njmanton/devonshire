<?php 
	$this->set('title_for_layout', 'Player: ' . $selecteduser['User']['username']);
	$wins = 0;
	$odds_only = ($selecteduser['User']['games'] == 2);
	$now = new DateTime();
?>

<section class="small-12 columns">
	<header>
		<h2><?php echo strtoupper($selecteduser['User']['username']); ?></h2>
	</header>

	<ul class="tabs" data-tab>
		<?php if (($selecteduser['User']['games'] & 1) != 0): ?>
			<li class="tab-title"><a href="#tab1">Goalmine</a></li>
		<?php endif; ?>
		<?php if (($selecteduser['User']['games'] & 2) != 0): ?>
		<li class="tab-title"><a href="#tab2">Tipping</a></li>
		<?php endif; ?>
	</ul>
	
	<div class="tabs-content">
		<div class="content <?php if (!$odds_only) echo 'active'; ?>" id="tab1">
			<?php
				$points = [];
				$wpoints = [];
				$wid = [];

				foreach ($selecteduser['Standing'] as $l) {
					// create the arrays for the goalmine highchart
					$points[] = ($l['position'] == 1) ? 0 : $l['points'];
					$wpoints[] = ($l['position'] == 1) ? $l['points'] : 0;
					$wid[] = $l['week_id'];
					$wins += ($l['position'] == 1);
				}
			?>
			<div id="stars">
				<?php echo str_repeat('<span data-icon="&#x61;" aria-hidden="true"></span>', $wins); ?>
			</div>
			<div id="goalmine-graph" style="width: 900px; margin: 10px auto;"></div>
		</div>

		<div class="content <?php if ($odds_only) echo 'active'; ?>" id="tab2">

			<div id="tip-chart" style="width: 90%; max-width: 800px; margin: 10px auto;"></div>
			<?php
			$matchtotals = [];
			$matchcolsmin = [];
			$matchcolsplus = [];
			$matchrolling = [];
			$weektotals = [];
			$prev = 0;
			$fix = [];

			foreach ($bets as $k=>$b) {
				$weektotals[$k] = 0;
				foreach ($b as $m) {
					if (isset($m['outcome'])) {
						$matchcolsplus[] = ($m['outcome'] >= 0) ? $m['outcome'] : 0;
						$matchcolsmin[] = ($m['outcome'] < 0) ? $m['outcome'] : 0;
						$weektotals[$k] += $m['outcome'];
						$matchrolling[] = $m['outcome'] + $prev;
						$prev += $m['outcome'];
						$fix[] = $m['fixture'];
					}
				}
			}
			krsort($bets);

			?>

			<dl class="accordion" data-accordion>
			<?php foreach ($bets as $k=>$week): ?>
				<dd class="accordion-navigation">
					<a href="#collapse<?=$k; ?>"><strong>Week <?=$k; ?>: Total <?=aus($weektotals[$k]); ?></strong></a>
					<div id="collapse<?=$k; ?>" class="content">
						<table class="table">
							<thead>
								<tr>
									<th>Date</th>
									<th>Match</th>
									<th>Bet</th>
									<th>Result</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($week as $k=>$m): ?>
								<?php $exp = new DateTime($m['date']); $exp->add(new DateInterval('PT11H45M')); ?>
								<?php if ($now > $exp): ?>
								<tr>
									<td><?php echo date('jS M', strtotime($m['date'])); ?></td>
									<td><a href="/matches/view/<?php echo $k; ?>"><?php echo $m['fixture']; ?></a></td>
									<td>$
									<?php
										echo $m['amount'];
										switch ($m['bet']) {
											case '1': echo ' (H)'; break;
											case '2': echo ' (A)'; break;
											case 'X': echo ' (D)'; break;
										}
									?>
									</td>
									<td><?php echo (isset($m['outcome'])) ? aus($m['outcome']) : '-'; ?></td>
								</tr>
							<?php else: ?>
								<tr><td colspan="4">Bets hidden until deadline</td></tr>
							<?php endif; ?>
							<?php endforeach; ?>	
							</tbody>
						</table>
					</div>
				</dd>
			<?php endforeach; ?>
			</dl>			

		</div>

	</div>

</section>

<script src="/js/highchart-user.js"></script>
<script>

	$('dd').eq(0).addClass('active').find('div').eq(0).addClass('active'); // open the first accordion

	// set up js arrays from model data
	var wid = [<?php echo implode(',', $wid); ?>];
	var points = [<?php echo implode(',', $points); ?>];
	var wpoints = [<?php echo implode(',', $wpoints); ?>];
	var matchcolsplus = [<?php echo implode(',', $matchcolsplus); ?>];
	var matchcolsmin = [<?php echo implode(',', $matchcolsmin); ?>];
	var matchrolling =  [<?php echo implode(',', $matchrolling); ?>];
	var fix = ['<?php echo implode("', '", $fix); ?>'];
	hc_usercharts(wid, points, wpoints, matchcolsplus, matchcolsmin, matchrolling, fix);

</script>
