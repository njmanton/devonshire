<!-- /app/View/Ledgers/index.ctp -->
<?php
	$redact = 'xxxx';
	$total = 0;
	$this->set('title_for_layout', 'Ledgers');
?>
<section>
	<header>
		<h2>Goalmine Ledger</h2>
		<p>This table shows all the ledger entries for Goalmine. Only admin users can see all details.</p>
	</header>
	<table class="table">
		<thead>
			<tr>
				<th>Date</th>
				<th>User</th>
				<th>Description</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($lines as $l): ?>
			<tr>
				<td><?php echo date('jS M', strtotime($l['Ledger']['date'])); ?></td>
				<td><?php echo $user['admin'] ? $l['User']['username'] : $redact ; ?></td>
				<td><?php echo $l['Ledger']['description']; ?></td>
				<td><?php echo sterling($l['Ledger']['amount'] * -1); $total -= $l['Ledger']['amount']; ?></td>
			</tr>
		<?php endforeach; ?>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>Balance:</td>
				<td><?php echo sterling($total*-1); ?></td>
			</tr>
		</tbody>
	</table>
</section>
