	<section id="new-match-box" class="reveal-modal f32 row" data-reveal>
		<form id="new-match-form" action="/matches/add" method="post">
			<div class="modal-header">
				<a type="button" class="close-reveal-modal" aria-hidden="true">&times;</a>
				<h3>Add/Edit match</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="small-7 columns">
						<fieldset>
							<input type="checkbox" name="gm-toggle" id="gm-toggle" /><label for="gm-toggle">Goalmine</label>
							<input type="checkbox" name="odd-toggle" id="odd-toggle" /><label for="odd-toggle">Tipping</label>
							
							<span id="game-help"></span>
							<label for="date">Date</label>
							<input type="text" name="date" class="input-medium" placeholder="dd/mm/yyyy" />
							<input type="hidden" name="week" value="<?=$week; ?>">
							<input type="hidden" name="matchid" value="" />
							<input type="hidden" name="game" value="" />
						</fieldset>
						<fieldset>
							<label class="control-label" for="teama">Home Team</label>
							<input type="text" id="ta" name="teama" placeholder="Home Team" />
							<input type="hidden" name="teama_id" />
							<label class="control-label" for="teamb">Away Team</label>
							<input type="text" id="tb" name="teamb" placeholder="Away Team" />
							<input type="hidden" name="teamb_id" />
							<label class="control-label" for="comp">Competition</label>
							<input type="text" id="compdd" name="comp" data-country="" placeholder="Competition" />
							<input type="hidden" name="comp_id" />
						</fieldset>
					</div>
					<div class="small-5 columns">
						<fieldset>
							<label for="odds1">Odds 1</label>
							<input type="text" class="input-mini" name="odds1" disabled />
							<label for="oddsX">Odds X</label>
							<input type="text" class="input-mini" name="oddsX" disabled />
							<label for="odds2">Odds 2</label>
							<input type="text" class="input-mini" name="odds2" disabled />
							<label for="gotw">GotW</label>
							<input type="checkbox" id="gotw" name="gotw" disabled />
						</fieldset>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="button tiny" data-dismiss="modal" aria-hidden="true" onclick="javascript: document.getElementById('new-match-form').reset(); return false">Close</button>
				<?php echo $this->Js->submit('Save', ['update' => '#new-match-form', 'class' => 'button tiny', 'div' => false]); ?>
			</div>
		</form>
	</section>