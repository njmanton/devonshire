<!-- /app/View/Competitions/index.ctp -->
<?php 
$this->set('title_for_layout', 'List of competitions');
$total = ['gm' => 0, 'odds' => 0];
?>

<section>
	<header>
		<h2>List of Competitions</h2>
		<p>This table shows a list of leagues and other competitions. Click on a competition to see its matches.</p>
	</header>
	
	<table class="table f32">
		<thead>
			<tr>
				<th width="300px">Competition</th>
				<th>Goalmine Matches</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($comps as $c): ?>
		<?php
			$matches = ['gm' => 0];
			foreach($c['Match'] as $m) {
				$matches['gm'] += (($m['game'] & 1) != 0);
			}
		?>	
			<tr>
				<td class="flag <?=$c['Competition']['country'] ;?>"><span style="padding-left: 30px;"><a href="/competitions/<?php echo $c['Competition']['id']; ?>"><?php echo $c['Competition']['name']; ?></a></span></td>
				<td class=""><?php echo $matches['gm']; $total['gm'] += $matches['gm']; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td>Grand Total</td>
				<td><?php echo $total['gm']; ?></td>
			</tr>
		</tfoot>
	</table>
	
</section>
