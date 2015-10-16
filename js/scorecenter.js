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
function calculateScorez(name, division, algorithm) {
	if (confirm('Score Center will attempt to calculate team ranks based on the inputed data. Current ranks will be overwritten. Ranks can still be set manually. Scores are not saved until the save button is clicked. Do you wish to continue?')) {
		//alert(name.trim().toUpperCase());
		//alert(algorithm);
		if (algorithm != null && algorithm != '') {
			if (algorithm == 'HIGHRAW') calc('HIGHRAW');
			else if (algorithm == 'HIGHRAWTIER') calc('HIGHRAWTIER');
			else if (algorithm == 'LOWRAW') calc('LOWRAW');
			else if (algorithm == 'LOWRAWTIER') calc('LOWRAWTIER');
		}
		else {
		switch (name.trim().toUpperCase()) {
			case "AIR TRAJECTORY": calc('HIGHRAWTIER'); break;
			case "ANATOMY & PHYSIOLOGY": calc('HIGHRAW'); break;
			case "ASTRONOMY": calc('HIGHRAW'); break;
			case "BIO-PROCESS LAB": calc('HIGHRAW'); break;
			case "BOTTLE ROCKET": calc('HIGHRAWTIER'); break;
			case "BRIDGE BUILDING": calc('HIGHRAWTIER'); break;
			case "CELL BIOLOGY": calc('HIGHRAW'); break;
			case "CHEMISTRY LAB": calc('HIGHRAW'); break;
			case "CRAVE THE WAVE": calc('HIGHRAW'); break;
			case "CRIME BUSTERS": calc('HIGHRAW'); break;
			case "DISEASE DETECTIVES": calc('HIGHRAW'); break;
			case "DYNAMIC PLANET": calc('HIGHRAW'); break;
			case "ELECTRIC VEHICLE": calc('LOWRAW'); break;
			case "ELASTIC LAUNCHED GLIDER": calc('HIGHRAWTIER'); break;
			case "EXPERIMENTAL DESIGN": calc('HIGHRAWTIER'); break;
			case "FOOD SCIENCE": calc('HIGHRAW'); break;
			case "FORENSICS": calc('HIGHRAW'); break;
			case "FOSSILS": calc('HIGHRAW'); break;
			case "GAME ON": calc('HIGHRAWTIER'); break;
			case "GEOLOGIC MAPPING": calc('HIGHRAW'); break;
			case "GREEN GENERATION": calc('HIGHRAW'); break;
			case "HYDROGEOLOGY": calc('HIGHRAW'); break;
			case "INVASIVE SPECIES": calc('HIGHRAW'); break;
			case "IT'S ABOUT TIME": calc('HIGHRAW'); break;
			case "METEOROLOGY": calc('HIGHRAW'); break;
			case "MISSION POSSIBLE": calc('HIGHRAWTIER'); break;
			case "PICTURE THIS": calc('HIGHRAW'); break;
			case "PROTEIN MODELING": calc('HIGHRAW'); break;
			case "REACH FOR THE STARS": calc('HIGHRAW'); break;
			case "ROAD SCHOLAR": calc('HIGHRAW'); break;
			case "ROBOT ARM": calc('HIGHRAWTIER'); break;
			case "SCRAMBLER": calc('LOWRAW'); break;
			case "WIND POWER": calc('HIGHRAW'); break;
			case "WRIGHT STUFF": calc('HIGHRAWTIER'); break;
			case "WRITE IT, DO IT": calc('HIGHRAWTIER'); break;
			default: displayError("<strong>Error:</strong> Unable to calculate ranks for this event. Please enter ranks manually.");
		}
		}
	
	
	
	
	}
}


// calc
// 1. HIGHRAW
// 2 HIGHRAWTIER
// 3. LOWRAW
// 4. LOWRAWTIER
function calc(type) {
		var count = 0;
		var rank = 1;
		var scoreArr = [];
		while (count < 1000) {
			if  ($('#teamRawScore'+count) != null && $('#teamRawScore'+count).val() != null) {
				var record = [];
				var score = $('#teamRawScore'+count).val();
				if (score == '') score == -1;
				// [TEAM NUMBER, TEAM RANK, RAW SCORE, TIER, TIE BREAK, ####]
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
		else if (type == 'HIGHRAWTIER') scoreArr.sort(compare2);
		else if (type == 'LOWRAW') scoreArr.sort(compare3);
		else if (type == 'LOWRAWTIER') scoreArr.sort(compare4);
		
		
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

// 2. HIGHRAWTIER
function compare2(a,b) {
  if (a[3] < b[3])
    return -1;
  if (a[3] > b[3])
    return 1;
  if (a[3] == b[3]) {
  	if (a[2] > b[2]) return -1;
  	if (a[2] < b[2]) return 1;
  }
  return 0;	
}

// 3. LOWRAW
function compare3(a,b) {
  var x = a[2]; if (a[2] == '') x = 100000;
  var y = b[2]; if (b[2] == '') y = 100000;
  if (x < y)
    return -1;
  if (x > y)
    return 1;
  return 0;	
}

// 4. LOWRAWTIER
function compare4(a,b) {
  if (a[3] < b[3])
    return -1;
  if (a[3] > b[3])
    return 1;
  if (a[3] == b[3]) {  
  	var x = a[2]; if (a[2] == '') x = 100000;
  	var y = b[2]; if (b[2] == '') y = 100000;
  	if (x < y) return -1;
  	if (x > y) return 1;
  }
  return 0;	
}

