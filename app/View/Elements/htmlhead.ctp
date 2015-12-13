<head>
	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<?php echo $this->Html->charset(); ?>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $title_for_layout; ?></title>
	<?php
		echo $this->Html->css('master'); // @includes all other stylesheets
		echo $this->Html->script('vendor/modernizr-latest'); // should load in head for html5shim & stop FOUC
	?>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.js"></script>
	<script>window.jQuery || document.write ('<script src="/js/vendor/jquery-1.8.2.min.js">\x3C/script>')</script>
	<script src="/js/vendor/highcharts.js"></script>
	<script src="/js/vendor/highcharts-more.js"></script>
	<script>
		!function ($) {
			$(function(){
				// Fix for dropdowns on mobile devices
				$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { 
					e.stopPropagation(); 
			});
				$(document).on('click','.dropdown-menu a',function(){
					document.location = $(this).attr('href');
				});
			})
		}(window.jQuery)
	</script>	
</head>