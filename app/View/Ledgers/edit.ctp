<!-- /app/View/Ledgers/edit.ctp -->
<?php $this->set('title_for_layout', 'Edit ledger'); ?>
<section>
	<header>
		<h2>Ledger entry</h2>
		<p>Select a user and amount to make a manual credit or debit entry.</p>
	</header>
	
	<?php
		echo $this->Form->create();
		echo $this->Form->input('user_id');
		echo $this->Form->input('amount');
		echo $this->Form->input('description', ['size' => 60]);
		echo $this->Form->input('date', ['type' => 'hidden', 'value' => date('Y-m-d H:i')]);
	?>
	<input type="submit" value="update" class="button tiny" />
</section>