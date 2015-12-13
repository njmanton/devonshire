<!-- /app/View/Ledgers/view.ctp -->
<?php 
	$tot = 0; // running total 
	$viewuser = ($lines[0]['User']['username']) ? $lines[0]['User']['username'] : 'the Goalmine pot';
	$this->set('title_for_layout', 'Ledger for ' . $viewuser);
?>
<section>
	<header>
		<h2>Transactions for <?php echo $viewuser; ?></h2>
		<p>The table shows all credits (winnings) and debits (weekly payments and withdrawals) for <?php echo $viewuser; ?>. </p>
	</header>
	<table class="table">
		<thead>
			<tr>
				<th>Date</th>
				<th>Description</th>
				<th>Amount</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
	<?php foreach ($lines as $l): ?>
			<tr>
				<td><?=date('jS M', strtotime($l['Ledger']['date'])); ?></td>
				<td><?=$l['Ledger']['description']; ?></td>
				<td><?=sterling($l['Ledger']['amount']); ?></td>
				<td><?=sterling($tot += $l['Ledger']['amount']); ?></td>
			</tr>
	<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>Current Balance:</td>
					<td><?=sterling($tot); ?></td>
				</tr>
			</tfoot>
	</table>
</section>