<?php
	$current = ($this->Session->check('current_week')) ? $this->Session->read('current_week') : $this->requestAction('/weeks/current/');
	$weeks = $this->requestAction('/weeks/unplayedWeeks/');
	$killers = $this->requestAction('/killers/currentKillers/' . $user['id']);
	$gmup = $tpup = '';
	if ($weeks) {
		foreach ($weeks as $k=>$w) {
			$lbl = ($current == $k) ? __('THIS WEEK') : 'WEEK ' . $k;
			if (!is_null($w['preds'])) {
				$cl = ($w['preds'] < 12) ? 'label alert' : 'label success';

				$gmup .= __('<li><a href="/predictions/%s">%s <span class="radius %s">%s</span></a></li>', $k, $lbl, $cl, (12 - $w['preds']));
			}
			if (!is_null($w['bets'])) {
				$cl = ($w['bets'] < 3) ? 'label alert' : 'label success';
				$tpup .= __('<li><a href="/bets/%s">%s <span class="radius %s">%s</span></a></li>', $k, $lbl, $cl, max(0, 3 - $w['bets']));
			}
		}
	}
?>

<div class="">
	<nav id="mainnav" role="navigation" class="top-bar" data-topbar>
		<?php if ($user): ?>
		<section class="top-bar-section">
			<ul class="left">
				<li class="tb-user"><a title="My results" href="/users/<?=$user['id']; ?>"><?=strtoupper($user['username']); ?></a></li>
				<li><a href="/news">NEWS</a></li>
				<li class="has-dropdown">
					<a href="#">GOALMINE</a>
					<ul class="dropdown">
						<?php echo $gmup; ?>
						<li><a href="/league/">LEAGUE TABLE</a></li>
					</ul>
				</li>
				<li class="has-dropdown">
					<a href="">TIPPING</a>
					<ul class="dropdown">
						<?php echo $tpup; ?>
						<li><a href="/weeks/ytd">LEAGUE TABLE</a></li>
					</ul>
				</li>
				<?php if (($user['games'] & 4) != 0): ?>
				<li class="has-dropdown">
					<a href="#">KILLER</a>
					<ul class="dropdown">
					<?php if (empty($killers)): ?>
						<li><a title="No Killer Games currently running" href="/killers">NO GAMES</a></li>
					<?php else: ?>
					<?php foreach ($killers as $k): ?>
						<li>
							<a href="/killer/<?=$k['K']['id']; ?>">GAME <?=$k['K']['id']; ?> <span class="life"><?=str_repeat('&#9829;', $k['E']['lives']); ?></span></a>
						</li>
					<?php endforeach; ?>
					<?php endif; ?>
					</ul>
				</li>
				<?php endif; ?>
			</ul>
			<ul class="right">
				<li class="has-dropdown">
					<a href="#">OPTIONS</a>
					<ul class="dropdown">
						<li><a href="/users/update">UPDATE DETAILS</a></li>
						<li><a href="/money/<?=$user['id']; ?>">MY BALANCE</a></li>
						<?php if ($user['admin']): ?>
						<li class="divider"></li>
						<li class="adminoption"><a href="/users/add">Add New Player</a></li>
				    <li class="adminoption"><a href="/teams/add">Add New Team</a></li>
				    <li class="adminoption"><a href="/competitions/add">Add New Competition</a></li>
				    <li class="adminoption"><a href="/ledgers/edit">Manage Transactions</a></li>
				    <li class="divider"></li>
				    <li class="adminoption"><a href="/users/send">Send bulk email</a></li>
						<?php endif; ?>
					</ul>
				</li>
				<li><a href="/users/logout">LOGOUT</a></li>
			</ul>
		</section>
		<?php else: ?>
		<section class="top-bar-section">
			<ul class="left">
				<li><a href="/users/login">LOGIN</a></li>
			</ul>
		</section>
		
		<?php endif; ?>

	</nav>
	<header role="banner">
		<div>
			<a href="/" title="home">
				<img src="/img/gm_logo.png" alt="Goalmine Logo">
				<span>GOALMINE 2015</span>
			</a>
		</div>
	</header>
</div>