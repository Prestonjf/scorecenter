function confirmDelete(name) {
	return confirm('Are you sure you wish to delete this '+name+'?');
}

	function limit(element) {
    	var max_chars = 3;
    	if(element.value.length > max_chars) {
        	element.value = element.value.substr(0, max_chars);
    	}
	}