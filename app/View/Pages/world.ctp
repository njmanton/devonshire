<section>
	<div id="hcmap" style="width: 1000px; height: 500px; background: #f99;">
		
	</div>
</section>
<script src="http://code.highcharts.com/maps/modules/map.js"></script>
<script src="http://code.highcharts.com/mapdata/custom/world.js"></script>
<script>
	
	$(document).ready(function() {

		var series = [{
			'type': 'map'

		}];

		$('#hcmap').highcharts('Map', {
			title: {
				text: 'demo map'
			},
			plotOptions: {
				map: {
					mapData: Highcharts.maps['custom/world']
				}
			}
		})


	})
</script>