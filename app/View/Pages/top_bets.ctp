<?php //$data = $this->requestAction('/bets/topBets'); ?>
<?php $data = $this->requestAction('places/twohundred'); ?> 

<section>
	<header>
		<h2>Tipping High Scores</h2>
		<p>The table below shows all weekly scores above $200 for this season of the Tipping Competition</p>
	</header>
	<table class="table">
		<thead>
			<tr>
				<th>Week</th>
				<th>Name</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($data as $d): ?>
			<tr>
				<td><a href="/bets/view/<?php echo $d['Place']['week_id'] ?>"><?php echo $d['Place']['week_id']; ?></a></td>
				<td><a href="/users/view/<?php echo $d['User']['id']; ?>"><?php echo $d['User']['username']; ?></a></td>
				<td><?php echo aus($d['Place']['balance']); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<hr />
	<p>And this table shows all returns over $200 for any single bet</p>
	<table class="table">
		<thead>
			<tr>
				<th>Week</th>
				<th>Name</th>
				<th>Match</th>
				<th>Amount</th>
				<th>Return</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><a href="/bets/view/18">18</a></td>
				<td><a href="/users/view/132">Dan</a></td>
				<td><a href="/matches/view/235">Liverpool v Aston Villa</a></td>
				<td>30</td>
				<td><?php echo aus(285); ?></td>
			</tr>
			<tr>
				<td><a href="/bets/view/12">12</a></td>
				<td><a href="/users/view/132">Dan</a></td>
				<td><a href="/matches/view/163">Tottenham v Wigan</a></td>
				<td>40</td>
				<td><?php echo aus(280); ?></td>
			</tr>
			<tr>
				<td><a href="/bets/view/18">18</a></td>
				<td><a href="/users/view/1">Nick</a></td>
				<td><a href="/matches/view/235">Liverpool v Aston Villa</a></td>
				<td>25</td>
				<td><?php echo aus(237.5); ?></td>
			</tr>
			<tr>
				<td><a href="/bets/view/18">18</a></td>
				<td><a href="/users/view/143">Martyn</a></td>
				<td><a href="/matches/view/235">Liverpool v Aston Villa</a></td>
				<td>25</td>
				<td><?php echo aus(237.5); ?></td>
			</tr>
			<tr>
				<td><a href="/bets/view/15">15</a></td>
				<td><a href="/users/view/43">JohnH</a></td>
				<td><a href="/matches/view/198">Everton v Norwich</a></td>
				<td>50</td>
				<td><?php echo aus(220); ?></td>
			</tr>
		</tbody>
	</table>
</section>