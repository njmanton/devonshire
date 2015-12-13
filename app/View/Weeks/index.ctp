<?php $this->set('title_for_layout', 'All Weeks'); ?>
<section>
	<header>
		<h2>List of weeks</h2>
		<p>The table below lists Goalmine weeks for the 2015/16 season. Where available, you can drill into the predictions and bets for each week.</p>
	</header>

	<ul class="tabs" data-tab>
		<li class="active tab-title"><a href="#active">Active</a></li>
		<li class="tab-title"><a href="#past">Past Weeks</a></li>
	</ul>

	<div class="tabs-content">
		<div class="content active" id="active">
			<table class="table">
				<thead>
					<tr>
						<th>Week</th>
						<th>Dates</th>
						<th>Status</th>
						<th>Goalmine</th>
						<th>Tipping</th>
						<?php if ($user['admin']) { echo '<th>Manage</th>'; } ?>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($weeks as $w): ?>
				<?php if ($w[0]['status'] != 1): ?>
				<?php 
					$dt = new DateTime($w[0]['start']);
					$now = new DateTime();
					$week = $w[0]['id'];
					$status = '';
						if ($w[0]['ms'] == 0) {
							$status = 'Not Available';
						} elseif ($now < $dt) {
							$status = 'Available';
						} else {
							$status = 'In Progress';
						}
				?>
					<tr>
						<td><a href="/weeks/<?=$week; ?>"><?php echo $week; ?></a></td>
						<td><?php echo $dt->format('j M y') . ' - ' . $dt->add(new DateInterval('P6D'))->format('j M y'); ?></td>
						<td><?php echo $status; ?></td>
						<td>
							<?php if ($status != 'Not Available'): ?>
							<a class="button tiny" href="/predictions/<?=$week; ?>">View</a>
							<?php endif; ?>
						</td>
						<td>
							<?php if ($status != 'Not Available'): ?>
							<a class="button tiny success" href="/bets/<?=$week; ?>">View</a>
							<?php endif; ?>
						</td>
						<?php if ($user['admin']): ?> 
							<td><a class="button tiny" href="/matches/results/<?php echo $week; ?>">Results</a>
							<a class="button tiny" href="/matches/edit/<?=$week; ?>">Manage</a></td>
						<?php endif; ?>
					</tr>
				<?php endif; ?>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="content" id="past">
			<table class="table">
				<thead>
					<tr>
						<th>Week</th>
						<th>Dates</th>
						<th>Goalmine</th>
						<th>Tipping</th>
						<?php if ($user['admin']) { echo '<th>Manage</th>'; } ?>
					</tr>
				</thead>
				<tbody>
				<?php foreach (array_reverse($weeks) as $w): ?>
				<?php if ($w[0]['status'] == 1): ?>
					<tr>
						<td><a href="/weeks/<?=$w[0]['id']; ?>"><?=$w[0]['id']; ?></a></td>
						<td><?php $dt = new DateTime($w[0]['start']); echo $dt->format('j M y') . ' - ' . $dt->add(new DateInterval('P6D'))->format('j M y'); ?></td>
						<td><a class="button tiny" href="/predictions/<?=$w[0]['id']; ?>">View</a></td>
						<td><a class="button tiny success" href="/predictions/<?=$w[0]['id']; ?>">View</a></td>
						<?php if ($user['admin']): ?> 
							<td><a class="button tiny" href="/matches/results/<?php echo $w[0]['id']; ?>">Results</a>
							<a class="button tiny" href="/matches/edit/<?=$w[0]['id']; ?>">Manage</a></td>
						<?php endif; ?>
					</tr>
				<?php endif; ?>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>

	</div>

</section>

