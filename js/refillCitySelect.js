<script>
	var cities = <?php echo json_encode($cities); ?>;
	//filling country list
	var countries = <?php echo json_encode($countries); ?>;

	function refillCitySelect(country_id, element_id) {		
		var sel = document.getElementById(element_id);
		
		//clearing previous cities
		var length = sel.options.length;
		for (i = length-1; i >= 0; i--) {
		  sel.options[i] = null;
		}
		
		var is_found = false;
		
		for (var i = 0; cities[i] != null && (!is_found || cities[i][2] == country_id); i++) {
			if (cities[i][2] == country_id) {	
				is_found = true;
			
				var opt = document.createElement('option');
				
				opt.innerHTML = cities[i][1];
				opt.value = cities[i][0];
				sel.appendChild(opt);
			}
		}
	}
</script>