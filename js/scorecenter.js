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
function calculateScorez(name, division) {
	if (confirm('Score Center will attempt to calculate team ranks based on the inputed data. Current ranks will be overwritten. Ranks can still be set manually. Scores are not saved until the save button is clicked. Do you wish to continue?')) {
		//alert(name.trim().toUpperCase());
		switch (name.trim().toUpperCase()) {
			case "AIR TRAJECTORY":
				calc('HIGHRAW');
			break;
			case "ASTRONOMY":
				
			break;
			default:
				displayError("<strong>Error:</strong> Unable to calculate ranks for this event. Please Manually enter ranks.");
		}
	
	
	
	
	
	}
}


// calc
// 1. HIGHRAW
//
//
function calc(type) {
		var count = 0;
		var rank = 1;
		var scoreArr = [];
		while (count < 1000) {
			if  ($('#teamRawScore'+count) != null && $('#teamRawScore'+count).val() != null) {
				var record = [];
				var score = $('#teamRawScore'+count).val();
				if (score == '') score == -1;
				record.push(count); // Team Row Number
				record.push(""); // Team Rank
				record.push(Number(score)); // Raw Score
				record.push($('#teamScoreTier'+count).val()); // Tier
				//record.push($('#teamTieBreak'+count).val()); // Tie Break		
				record.push("####");			
				scoreArr.push(record);
			}
			else break;		
			count++;
		}
		// Use Correct Sort Function
		if (type == 'HIGHRAW') scoreArr.sort(compare1);
		
		
		// Set Ranks, ReOrder, Set Points Earned
		scoreArr.forEach(function(entry) {
			entry[1] = rank;
			rank++;
		});
		scoreArr.sort(compare);
		//alert(scoreArr.toString());
		
		scoreArr.forEach(function(entry) {
			$('#teamScore'+entry[0]).val(entry[1]);
			updatePointsEarned('teamScore', entry[0], 'teamPointsEarned');
		});
}

// sort row number
function compare(a,b) {
  if (a[0] < b[0])
    return -1;
  if (a[0] > b[0])
    return 1;
  return 0;	
}

// 1. HIGHRAW
function compare1(a,b) {
  if (a[2] > b[2])
    return -1;
  if (a[2] < b[2])
    return 1;
  return 0;	
}



