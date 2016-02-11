// Delete Confirm
function confirmDelete(name) {
	return confirm('Are you sure you wish to delete this '+name+'?');
}

function confirmCancel() {
	return confirm('All unsaved data will be lost. Do you wish to continue?');
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

function loadPage(p, total, pages) {
		var currentPage = Number($('#selectedPage').val());
		if (p == 'first') p = 1;
		else if (p == 'previous') { if (currentPage == 1) p = currentPage; else p = currentPage - 1; }
		else if (p == 'next') { if (currentPage == pages) p = currentPage; else p = currentPage + 1; }
		else if (p == 'last') p = pages;
		
		$('#selectedPage').val(p);
		
		var start = (p-1) * 15;
		var end = ((p-1) * 15) + 14;
		var count = 0;
		while (count <= total) {
			if (document.getElementById('resultPageRow'+count) != null) {
				if (count >= start && count <= end) {
					document.getElementById('resultPageRow'+count).style.display = "";
				}
				else {
					document.getElementById('resultPageRow'+count).style.display = "none";
				}
			}
			count++;
		}	
		var navigationHtml = "Page: ";
		var pageCount = 1;
		navigationHtml += ' <a href="javascript:void(0)" onclick="loadPage(\'first\',\''+total+'\',\''+pages+'\')">|<</a> ';
		navigationHtml += ' <a href="javascript:void(0)" onclick="loadPage(\'previous\',\''+total+'\',\''+pages+'\')"><</a> ';	
		while (pageCount <= pages) {
			if (p == pageCount) {
				navigationHtml += "<b><u><span id='selectedPage'>"+pageCount+"</span></u></b> ";
				navigationHtml += "";
			} 
			else {
				navigationHtml += '<a href="javascript:void(0)" onclick="loadPage(\''+pageCount+'\',\''+total+'\',\''+pages+'\')">'+pageCount+' </a>';		
			}
				pageCount++;
		}
		navigationHtml += ' <a href="javascript:void(0)" onclick="loadPage(\'next\',\''+total+'\',\''+pages+'\')">></a> ';
		navigationHtml += ' <a href="javascript:void(0)" onclick="loadPage(\'last\',\''+total+'\',\''+pages+'\')">>|</a> ';
		
		document.getElementById('pageStartId').innerHTML = start+1;
		if (p == pages) document.getElementById('pageEndId').innerHTML = total;
		else document.getElementById('pageEndId').innerHTML = end+1;
		document.getElementById('pagesId').innerHTML = navigationHtml;
		
		document.getElementById('pageStartId2').innerHTML = start+1;
		if (p == pages) document.getElementById('pageEndId2').innerHTML = total;
		else document.getElementById('pageEndId2').innerHTML = end+1;
		document.getElementById('pagesId2').innerHTML = navigationHtml;
		
		// Set Page Index on Server
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
			if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {					
			}
		}	
        xmlhttp.open("GET","controller.php?command=updateResultPage&page="+p);
        xmlhttp.send();
}

function resetScores() {
	if (confirm('Are you sure you want to clear all data for this event? Existing data can be retrieved be clicking cancel.')) {
	var count = 0;
	while (count < 1000) {
		if  ($('#teamRawScore'+count) != null && $('#teamRawScore'+count).val() != null) {
				$('#teamRawScore'+count).val('');
				$('#teamStatus'+count).val('P');
				$('#teamScoreTier'+count).val(1);
				$('#teamTieBreak'+count).val(0);
				$('#teamScore'+count).val('');
				$('#teamPointsEarned'+count).val('');
				document.getElementById('teamRawScore'+count).style.backgroundColor = "#FFFFFF";
		}
		else break;		
		count++;
	}
	count = 0;
	while (count < 1000) {
		if  ($('#teamARawScore'+count) != null && $('#teamARawScore'+count).val() != null) {
				$('#teamARawScore'+count).val('');
				$('#teamAStatus'+count).val('P');
				$('#teamAScoreTier'+count).val(1);
				$('#teamATieBreak'+count).val(0);
				$('#teamAScore'+count).val('');
				$('#teamAPointsEarned'+count).val('');
				document.getElementById('teamARawScore'+count).style.backgroundColor = "#FFFFFF";
		}
		else break;		
		count++;
	}
	}
}

// Set Rank Scores from Pop Up Box
function pasteText(text, type) {
	var ranks = [0];
	var tokens = text.split('\n');
	for (i = 0; i < tokens.length; i++) {
	//	if (tokens[i].trim() != '') {
			if ((type=='rankBox' || type=='rawBox') && isNaN(tokens[i].trim())) {
				alert('All values must be numerical.');		
				return;
			}
			else
				ranks.push(tokens[i].trim());
	//	}
	}

	if (type == 'rankBox') {
		var teamCount = $('#primaryTeamTable tr').length;	
		if (teamCount != ranks.length) alert("Number of inputed ranks ("+(ranks.length-1)+") does not match the number of teams ("+(teamCount-1)+") on this screen.")
		else {
			$('#primaryTeamTable tr').each(function (i, row) {
				var count = 0;
				$(this).find('td').each (function() {
					if (count == 5) {
						$(this).next().find('input').val(ranks[i]);
						updatePointsEarned('teamScore', i-1,'teamPointsEarned','teamStatus');
					}
					count++;
				});
			});
		}
	}
	
	else if (type == 'rawBox') {
		var teamCount = $('#primaryTeamTable tr').length;	
		if (teamCount != ranks.length) alert("Number of inputed raw scores ("+(ranks.length-1)+") does not match the number of teams ("+(teamCount-1)+") on this screen.")
		else {
			$('#primaryTeamTable tr').each(function (i, row) {
				var count = 0;
				$(this).find('td').each (function() {
					if (count == 2) {
						$(this).next().find('input').val(ranks[i]);
						highlightRawScoreDuplication();
					}
					count++;
				});
			});
		}
	}
	else if (type == 'tierBox') {
		var teamCount = $('#primaryTeamTable tr').length;	
		if (teamCount != ranks.length) alert("Number of inputed tiers ("+(ranks.length-1)+") does not match the number of teams ("+(teamCount-1)+") on this screen.")
		else {
			$('#primaryTeamTable tr').each(function (i, row) {
				var count = 0;
				$(this).find('td').each (function() {
					if (count == 3) {
						var tier = ranks[i];
						if (tier == 'I') tier = 1; else if (tier == 'II') tier = 2; else if (tier == 'III') tier = 3; else if (tier == 'IV') tier = 4; else if (tier == 'V') tier = 5;
						$(this).next().find('select').val(tier);
						highlightRawScoreDuplication();
					}
					count++;
				});
			});
		}
	}
	
	
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
			else if (algorithm == 'HIGHRAWTIER4LOW') calc('HIGHRAWTIER4LOW');
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


function calc(type) {
	calcPrimary(type);
	calcAlternate(type);
}
// calc Primary Teams
function calcPrimary(type) {
		var count = 0;
		var rank = 1;
		var previousRawScore = -100;
		var previousTier = -100;
		var previousStatus = '';
		var previousTieBreak = -100;
		var tiesCount = 0;
		var scoreArr = [];
		while (count < 1000) {
			if  ($('#teamRawScore'+count) != null && $('#teamRawScore'+count).val() != null) {
				var record = [];
				var score = $('#teamRawScore'+count).val();
				var status = $('#teamStatus'+count).val();
				var tier = $('#teamScoreTier'+count).val();
				var tieBreak = $('#teamTieBreak'+count).val();
				if (score == '') score == -1;
				// [TEAM NUMBER, TEAM RANK, RAW SCORE, TIER, STATUS, TIE BREAK, ####]
				record.push(count); // Team Row Number
				record.push(""); // Team Rank
				record.push(Number(score)); // Raw Score
				record.push(tier); // Tier
				record.push(status); // Status
				record.push(Number(tieBreak)); // Tie Break		
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
		else if (type == 'HIGHRAWTIER4LOW') scoreArr.sort(compare5);
		
		
		// Set Ranks, ReOrder, Set Points Earned (Status of P gets ranked. N and D get 0 Rank)
		scoreArr.forEach(function(entry) {
			// if (entry[0] ==15 )
			//	alert(entry[0]+': Rank Count:' +rank+ ' Tie Count: '+tiesCount);
		
			if (previousRawScore != entry[2] || previousTier != entry[3] || previousTieBreak != entry[5] || (previousRawScore != '' && entry[2] == '')) tiesCount = 0;
			if (entry[4] == 'P') {
				if ((type == 'HIGHRAW' || type == 'LOWRAW') && previousRawScore == entry[2] && previousTieBreak == entry[5]) {
					tiesCount++;
					entry[1] = rank - tiesCount;
				}
				else if ((type == 'HIGHRAWTIER' || type == 'LOWRAWTIER' || type == 'HIGHRAWTIER4LOW') && previousRawScore == entry[2] && previousTier == entry[3] && previousTieBreak == entry[5]) {
					tiesCount++;
					entry[1] = rank - tiesCount;
				}
				else {
					entry[1] = rank;
				}
				rank++;
			}
			else {
				entry[1] = 0;
			}
			
			previousRawScore = entry[2];
			previousTier = entry[3];
			previousStatus = entry[4];
			previousTieBreak = entry[5];
		});
		scoreArr.sort(compare);
		//alert(scoreArr.toString());
		
		scoreArr.forEach(function(entry) {
			$('#teamScore'+entry[0]).val(entry[1]);
			updatePointsEarned('teamScore', entry[0], 'teamPointsEarned','teamStatus');
		});
}

// calc Alternate Teams
function calcAlternate(type) {
		var count = 0;
		var rank = 1;
		var previousRawScore = -100;
		var previousTier = -100;
		var previousStatus = '';
		var previousTieBreak = -100;
		var tiesCount = 0;
		var scoreArr = [];
		while (count < 1000) {
			if  ($('#teamARawScore'+count) != null && $('#teamARawScore'+count).val() != null) {
				var record = [];
				var score = $('#teamARawScore'+count).val();
				var status = $('#teamAStatus'+count).val();
				var tier = $('#teamAScoreTier'+count).val();
				var tieBreak = $('#teamATieBreak'+count).val();
				if (score == '') score == -1;
				// [TEAM NUMBER, TEAM RANK, RAW SCORE, TIER, STATUS, TIE BREAK, ####]
				record.push(count); // Team Row Number
				record.push(""); // Team Rank
				record.push(Number(score)); // Raw Score
				record.push(tier); // Tier
				record.push(status); // Status
				record.push(Number(tieBreak)); // Tie Break		
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
		else if (type == 'HIGHRAWTIER4LOW') scoreArr.sort(compare5);
		
		
		// Set Ranks, ReOrder, Set Points Earned (Status of P gets ranked. N and D and X get 0 Rank)
		scoreArr.forEach(function(entry) {
			// if (entry[0] ==15 )
			//	alert(entry[0]+': Rank Count:' +rank+ ' Tie Count: '+tiesCount);
		
			if (previousRawScore != entry[2] || previousTier != entry[3] || previousTieBreak != entry[5] || (previousRawScore != '' && entry[2] == '')) tiesCount = 0;
			if (entry[4] == 'P') {
				if ((type == 'HIGHRAW' || type == 'LOWRAW') && previousRawScore == entry[2] && previousTieBreak == entry[5]) {
					tiesCount++;
					entry[1] = rank - tiesCount;
				}
				else if ((type == 'HIGHRAWTIER' || type == 'LOWRAWTIER' || type == 'HIGHRAWTIER4LOW') && previousRawScore == entry[2] && previousTier == entry[3] && previousTieBreak == entry[5]) {
					tiesCount++;
					entry[1] = rank - tiesCount;
				}
				else {
					entry[1] = rank;
				}
				rank++;
			}
			else {
				entry[1] = 0;
			}
			
			previousRawScore = entry[2];
			previousTier = entry[3];
			previousStatus = entry[4];
			previousTieBreak = entry[5];
		});
		scoreArr.sort(compare);
		//alert(scoreArr.toString());
		
		scoreArr.forEach(function(entry) {
			$('#teamAScore'+entry[0]).val(entry[1]);
			updatePointsEarned('teamAScore', entry[0], 'teamAPointsEarned','teamAStatus');
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

// ALL Score Calculations. 'N' And 'D' Status is Last

// 1. HIGHRAW
function compare1(a,b) {
 // if (a[4] == 'N' || a['4'] == 'D' || a['4'] == 'X') return 1;
  if (a[2] > b[2])
    return -1;
  if (a[2] < b[2])
    return 1;
  if (a[2] == b[2]) {
	  if (a[5] < b[5])
	  	return -1;
	  if (a[5] > b[5])
	  	return 1;
  }
  return 0;	
}

// 2. HIGHRAWTIER
function compare2(a,b) {
//  if (a[4] == 'N' || a['4'] == 'D' || a['4'] == 'X') return 1;
  if (a[3] < b[3])
    return -1;
  if (a[3] > b[3])
    return 1;
  if (a[3] == b[3]) {
  	if (a[2] > b[2]) return -1;
  	if (a[2] < b[2]) return 1;
  	if (a[2] == b[2]) {
	  if (a[5] < b[5])
	  	return -1;
	  if (a[5] > b[5])
	  	return 1;
  	}
  }
  return 0;	
}

// 3. LOWRAW
function compare3(a,b) {
  //if (a[4] == 'N' || a['4'] == 'D' || a['4'] == 'X') return 1;
  var x = a[2]; if (a[2] == '') x = 100000;
  var y = b[2]; if (b[2] == '') y = 100000;
  if (x < y)
    return -1;
  if (x > y)
    return 1;
  if (x == y) {
	  if (a[5] < b[5])
	  	return -1;
	  if (a[5] > b[5])
	  	return 1;
  }
  return 0;	
}

// 4. LOWRAWTIER
function compare4(a,b) {
//  if (a[4] == 'N' || a['4'] == 'D' || a['4'] == 'X') return 1;
  if (a[3] < b[3])
    return -1;
  if (a[3] > b[3])
    return 1;
  if (a[3] == b[3]) {  
  	var x = a[2]; if (a[2] == '') x = 100000;
  	var y = b[2]; if (b[2] == '') y = 100000;
  	if (x < y) return -1;
  	if (x > y) return 1;
  	if (x == y) {
	  if (a[5] < b[5])
	  	return -1;
	  if (a[5] > b[5])
	  	return 1;
  	}
  }
  return 0;	
}

// 5. HIGHRAWTIER4LOW - 4th Tier Low Score
function compare5(a,b) {
//  if (a[4] == 'N' || a['4'] == 'D' || a['4'] == 'X') return 1;
  if (a[3] < b[3])
    return -1;
  if (a[3] > b[3])
    return 1;
  if (a[3] == b[3]) {
	if (a[3] == 4) {
		if (a[2] < b[2]) return -1;
		if (a[2] > b[2]) return 1;
		if (a[2] == b[2]) {
			if (a[5] < b[5])
				return -1;
			if (a[5] > b[5])
				return 1;
  		}
	}
	else {
		if (a[2] > b[2]) return -1;
		if (a[2] < b[2]) return 1;
		if (a[2] == b[2]) {
			if (a[5] < b[5])
				return -1;
			if (a[5] > b[5])
				return 1;
  		}
	}
  }
  return 0;	
}

