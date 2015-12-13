<?php	$this->set('title_for_layout', 'Killer game ' . $game['Killer']['id']); ?>

<section>
	<header>
		<h2><?php echo __('Killer Game %s', $game['Killer']['id']); ?></h2>
		<p></p>
	</header>
	<p><?=$game['Killer']['description']; ?></p>
	<dl class="accordion" data-accordion>
	<?php foreach ($killers as $k=>$row): // loop through each round ?>
		<dd class="accordion-navigation">
			<a href="#round<?=$k; ?>">ROUND <?=$k; ?> - WEEK <?=$row['week']; ?> (<?php $c = count($row['rows']); echo $c; echo ($c > 1) ? ' players' : ' player'; ?>)</a>
			<div class="content" id="round<?=$k; ?>">
				<table width="80%">
					<thead>
						<tr>
							<th width="15%">Players</th>
							<th width="15%">Lives</th>
							<th width="40%">Match</th>
							<th width="10%">Date</th>
							<th width="10%">Pred</th>
							<th width="10%">Score</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($row['rows'] as $k=>$r): // then loop through user in that round ?>
						<tr <?php if ($r['dead']) echo 'class="deadrow"'; ?>>
							<td><a href="/users/<?php echo $k; ?>"><?php echo $r['name']; ?></a></td>
							<td>
								<?php	if ($r['dead']): ?>
								<span class="life">&#9760;</span>
								<?php else: ?>
									<?php echo str_repeat('<span class="life">&#9829;</span>', $r['lives'] - $r['lostlife']); ?>
									<?php if ($r['lostlife']): ?>
									<span class="life lost">&#9829;</span>
									<?php endif; ?>
								<?php endif; ?>
							</td>
							<td><?=$r['label']; ?>
							<?php if (!$row['expired'] && ($k == $user['id'])): ?>
									<a class="killer-edt" data-dt="<?php echo $row['start']; ?>" data-kid="<?php echo $r['kid']; ?>" data-week="<?php echo $row['week']; ?>" data-pred="<?php echo $r['pred']; ?>" data-mid="<?php echo $r['mid']; ?>" data-reveal-id="killer-match-edit" href="#">Edit</a>
							<?php endif; ?>
							</td>
							<td><?php echo (isset($r['date'])) ? date('jS M', strtotime($r['date'])) : '&nbsp;'; ?></td>
							<td class="pred"><?php echo ($r['pred'] && ($row['expired'] || $user['id'] == $k)) ? $r['pred'] : '' ; ?></td>
							<td><?php echo ($r['score']) ? $r['score'] : '&nbsp;' ; ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</dd>
	<?php endforeach; ?>
	</dl>

</section>

<?php echo $this->element('killermatchedit'); ?>
<script>
	$(document).ready(function() {

		var kmindt = new Date("<?php echo $week; ?>");
		var killer = <?=$game['Killer']['id']; ?>;

		$('#kdatep').datepicker({
			minDate: kmindt,
			maxDate: new Date(kmindt.getTime() + (60 * 60 * 24 * 6 * 1000))
		});

		$('#kta, #ktb').autocomplete({
			source: "/teams/show?killer=<?=$game['Killer']['id']; ?>"
		});

	});
</script>


