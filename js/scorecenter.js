// Delete Confirm
function confirmDelete(name) {
	return confirm('Are you sure you wish to delete this '+name+'?');
}

// Text Field Limit
function limit(element) {
    var max_chars = 3;
    if(element.value.length > max_chars) {
        element.value = element.value.substr(0, max_chars);
    }
}
	
	
// Screen Alerts	
function displayError(message) {
	document.getElementById('errors').style.display = "block";
	document.getElementById('errors').innerHTML = message;
	document.body.scrollTop = document.documentElement.scrollTop = 0;	
}
function clearError() {
	document.getElementById('errors').style.display = "none";
	document.getElementById('errors').innerHTML = "";
}
function displaySuccess(message) {
	document.getElementById('messages').style.display = "block";
	document.getElementById('messages').innerHTML = message;
	document.body.scrollTop = document.documentElement.scrollTop = 0;
	
}
function clearSuccess() {
	document.getElementById('messages').style.display = "none";
	document.getElementById('messages').innerHTML = "";
}

	// calculate event score logic (specific per event)
	function calculateScorez(name) {
				if (confirm('Score Center will attempt to calculate team ranks based on the inputed data. Current ranks will be overwritten. Ranks can still be set manually. Scores are not saved until the save button is clicked. Do you wish to continue?')) {

		}
	}