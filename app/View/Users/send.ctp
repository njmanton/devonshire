<?php $this->set('title_for_layout', 'Send bulk email'); ?>
<section class="small-12 columns">
	<header>
		<h2>Send bulk email to users</h2>
		<p>Use these forms to send an email to groups of users at once.</p>
	</header>
	<div class="tab-pane active" id="tab1">
		<p>This email will be sent to all users registered as playing the following games</p>
		<form action="/users/send" method="post" id="GoalmineForm">
			<label class="checkbox"><input type="checkbox" value="1" name="data[game][gm]" /> Goalmine</label>
			<label class="checkbox"><input type="checkbox" value="2" name="data[game][tp]" /> Tipping</label>
			<label class="checkbox"><input type="checkbox" value="4" name="data[game][kl]" /> Killer</label>
			<label for="TippingFormTitle">Subject</label>
			<input type="text" id="GoalmineFormTitle" name="data[subject]" />
			<label for="GoalmineFormBody">Message</label>
			<textarea name="data[body]" id="GoalmineFormBody" rows="10"></textarea>
			<br />
			<input type="submit" value="Send" class="button small" />
		</form>
	</div>
</section>