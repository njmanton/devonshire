<!-- /app/View/Users/add.ctp -->
<?php $this->set('title_for_layout', 'Add Player'); ?>
<section class="row userform">
	
	<header>
		<h2>Add new player</h2>
	<p>Use this form to add a new player to the competition. All fields are mandatory</p>
	</header>

	<?php	echo $this->Form->create('User', ['class' => 'stdform']); ?>
	<fieldset>
		<legend>Player Details</legend>
		<label for="UserUsername">username</label>
		<input type="text" name="data[User][username]" id="UserUsername" maxlength="50" autocomplete="off" /><div id="username_err"></div>
	
		<label for="UserEmail">email</label>
		<input type="email" name="data[User][email]" id="UserEmail" maxlength="50" autocomplete="off" />
	
		<label for="UserPassword">password</label>
		<input type="password" name="data[User][password]" id="UserPassword" maxlength="50" autocomplete="off" /><div id="new_err"></div>

		<label for="UserRepeat">password (repeat)</label>
		<input type="password" name="data[User][repeat]" id="UserRepeat" maxlength="50" autocomplete="off" /><div id="repeat_err"></div>
	</fieldset>
	<fieldset>
		<legend>Registered Games</legend>
		<label for="UserGameGoalmine">Goalmine <input id="UserGameGoalmine" name="data[User][game][goalmine]" value="1" type="checkbox" /></label>
		<label for="UserGameTipping">Tipping <input id="UserGameTipping" name="data[User][game][tipping]" value="2" type="checkbox" /></label>
		<label for="UserGameKiller">Killer <input id="UserGameKiller" name="data[User][game][killer]" value="4" type="checkbox" /></label>
	</fieldset>

	<input type="submit" value="Add" class="button tiny">

</section>
<script>
	$('#UserAddForm').on('submit', function(e) {
		console.log(e);
		//return false;
	});
	$('#UserRepeat').on('change', function() {
		if ($(this).val() !== $('UserPassword').val()) {

		}
	});
	$('#UserPassword').on('change', function() {
		if ($(this).length < 6) {

		}
	});
	$('#UserUsername').on('change', function() {
		// ajax to check usernames
	});

</script>
