<?php $this->set('title_for_layout', 'All matches'); ?>
<section>
	<header>
		<h2>All matches</h2>
		<p>click on a week heading to see all matches for that week.</p>
	</header>

	<dl class="accordion" data-accordion id="week_list">
		<?php foreach ($matches as $k=>$m): ?>
		<dd class="accordion-navigation">
			<a href="#week-<?php echo $k; ?>" data-week="<?php echo $k; ?>">Week <?php echo $k; ?></a>
		
			<div id="week-<?php echo $k; ?>" class="content">
				 
			</div>
		</dd>
		<?php endforeach; ?>
	</dl>
	
</section>

