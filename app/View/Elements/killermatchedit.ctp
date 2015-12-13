<section id="killer-match-edit" role="dialog" aria-hidden="true" data-reveal class="reveal-modal">
	<form id="killer-match-form" action="/killers/add" method="post">
		<div class="modal-header">
			<a type="button" class="close-reveal-modal" data-dismiss="modal" aria-hidden="true">&times;</a>
			<h3>Add/Edit match</h3>
			<p>
				Add or edit your match for this round of Killer. You may choose any match from the four English leagues only. Once you have chosen a team once, you cannot choose them again in further rounds for that game. Note that the Goalmine server will <strong>not</strong> check the validity of the match entered. If for whatever reason the match does not take place, you will lose a life.
			</p>
		</div>		
		<div class="modal-body row">
			<div class="columns small-8">
				<fieldset>
					<label for="date">Date</label>
					<input type="text" name="date" id="kdatep" class="input-medium" placeholder="dd/mm/yyyy" />
					<input type="hidden" name="game" value="<?=$game['Killer']['id']; ?>">
					<input type="hidden" name="week" id="week" value="" />
					<input type="hidden" name="matchid" id="mid" value="" />
					<input type="hidden" name="kid" id="kid" value="" />
					<input type="hidden" name="uid" id="uid" value="<?=$user['id']; ?>" />
					
					<label class="control-label" for="teama">Home Team</label>
					<input type="text" id="kta" name="teama" class="controls input-large" placeholder="Home Team" />
					<input type="hidden" name="teama_id" />
					<label class="control-label" for="teamb">Away Team</label>
					<input type="text" id="ktb" name="teamb" class="controls input-large" placeholder="Away Team" />
					<input type="hidden" name="teamb_id" />
					<label class="control-label" for="pred">Score</label>
					<select name="pred" id="pred">
						<option value="1">Home Win</option>
						<option value="X">Draw</option>
						<option value="2">Away Win</option>
					</select>
				</fieldset>
			</div>
			<div role="complementary" class="used small-4 columns">
				<h4>Used teams</h4>
				<ul>
				<?php if (!empty($used)): ?>
				<?php foreach ($used as $k=>$u): ?>
					<li data-id="<?=$k; ?>"><?=$u; ?></li>
				<?php endforeach; ?>
				<?php else: ?>
					<li>None</li>
				<?php endif; ?>
				</ul>
			</div>
		</div>
		<div class="modal-footer">
			<button class="button tiny" data-dismiss="modal" aria-hidden="true" onclick="javascript: document.getElementById('killer-match-form').reset(); return false">Reset</button>
			<?php echo $this->Js->submit('Save', ['update' => '#killer-match-form', 'class' => 'button success tiny', 'div' => false]); ?>
		</div>
	</form>

</section>
