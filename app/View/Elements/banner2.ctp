<?php $uw = $this->requestAction('/weeks/unplayedWeeks/'); ?>
<nav id="mainnav" class="top-bar" role="navigation" data-topbar>
	<section class="top-bar-section">
		<ul class="left" id="game-menus">
			<li><a href="/"><img title="Home" src="/img/gm_logo.svg" alt="Home" /></a></li>
			<li><a data-panel="gm" href="#">GOALMINE</a></li>
			<li><a data-panel="tp" href="#">TIPPING</a></li>
			<li><a data-panel="kl" href="#">KILLER</a></li>
		</ul>
	</section>
	<section class="top-bar-section">
		<ul class="right">
			<?php if ($user['admin']): ?>
			<li class="has-dropdown">
				<a href="#">ADMIN</a>
				<ul class="dropdown">
				  <li class="adminoption"><a href="/users/add">NEW PLAYER</a></li>
			    <li class="adminoption"><a href="/teams/add">NEW TEAM</a></li>
			    <li class="adminoption"><a href="/competitions/add">NEW COMPETITION</a></li>
			    <li class="adminoption"><a href="/ledgers/edit">MANAGE TRANSACTIONS</a></li>
			    <li class="divider"></li>
			    <li class="adminoption"><a href="/posts/add">NEW POST</a></li>
			    <li class="adminoption"><a href="/users/send">SEND BULK EMAIL</a></li>
				</ul>
			</li>
			<?php endif; ?>
			<?php if ($user): ?>
			<li><a title="My Balance" href="/money/<?=$user['id']; ?>"><?=sterling($this->requestAction('/ledgers/view/' . $user['id'])); ?></a></li>
			<li><a href="/users/update">OPTIONS</a></li>
			<li><a href="/users/logout">LOGOUT</a></li>
			<?php else: ?>
			<li><a href="/pages/about/">ABOUT</a></li>
			<li><a href="/users/login/">LOGIN</a></li>
			<?php endif; ?>
		</ul>
	</section>
</nav>
<section class="panels">
	<div class="panel gm">
		<?=$this->element('goalmine-panel', ['uw' => $uw]); ?>
	</div>
	<div class="panel tp">
		<?=$this->element('tipping-panel', ['uw' => $uw]); ?>
	</div>
	<div class="panel kl">
		<?=$this->element('killer-panel', ['uw' => $uw]); ?>
	</div>
</section>
<header role="banner">
	<div>
		<div>
			<a href="/" title="home">
				<img src="/img/gm_logo_opt.svg" alt="Goalmine Logo">
				<!-- <span>GOALMINE 2015</span> -->
			</a>
		</div>
	</div>
</header>
