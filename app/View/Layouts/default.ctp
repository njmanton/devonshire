<!DOCTYPE html>
<html lang="en">
<?php echo $this->element('htmlhead'); ?>
	<body>
		<?php	echo $this->element('banner2', []); ?>
		<div class="row" role="main">
			<?php echo $this->Session->flash(); ?>
			<?php echo $this->fetch('content'); ?>
		</div>
		<?php //echo $this->element('sql_dump');
			echo $this->element('foot');
			echo $this->element('ga');
		?>

		<script src="/js/vendor/jquery-ui.min.js"></script>
		<script src="/js/devonshire.min.js"></script>
		<script src="/js/foundation/foundation.js"></script>
		<script src="/js/foundation/foundation.reveal.js"></script>
		<script src="/js/foundation/foundation.tab.js"></script>
		<script src="/js/foundation/foundation.accordion.js"></script>
		<script src="/js/foundation/foundation.alert.js"></script>
		<script src="/js/foundation/foundation.topbar.js"></script>

		<script>
			$(document).foundation(); // initialise foundation js
		</script>

	</body>

</html>
