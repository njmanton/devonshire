<!-- /app/View/Users/forgot.ctp -->
<?php $this->set('title_for_layout', 'Forgotten Password'); ?>
<section>
	<header>
		<h2>Forgotten Password</h2>
		<p>If you have forgotten your password, then enter your username and password in the form below.</p>
		<p>If your details are found then your password will be reset to a temporary value and emailed to you.</p>
	</header>

	<div class="userform">
		<?php
		echo $this->Form->create('Forgot');
		echo '<fieldset>';
		echo $this->Form->input('name');
		echo $this->Form->input('email', ['type' => 'email']);
		echo '</fieldset>';
		?>
		<input type="submit" value="submit" class="button small" />
	</div>

</section>

