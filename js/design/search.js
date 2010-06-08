/**
 * 
 */



/**
 * Takes a string and returns an array of rules
 * 
 *  @param string searchString
 */
function getSearchArray(searchString) {
	var searches = searchString.split(" ");
	var result = new Array();
	var acceptedOrders = new Array('best', 'worst', 'newest', 'oldest', 'random');
	
	
	for(s in searches) {
		searches[s];
		// get our excluded tags
		if((aSearch = searches[s].split('=')).length > 1) {
			switch(aSearch[0]) {
				case 'order':
					var addOrders = aSearch[1].split(',');
						for(o in addOrders)
							if(isInArray(addOrders[o], acceptedOrders)){
								if(result['order'] == null)
									result['order'] = new Array();
								result['order'].push(addOrders[o]);
								searches[s] = "";
							}					
					break;
					
				case 'offset':
					if(result['offset'] == null)
						result['offset'] = new Array();
					result['offset'] = parseInt(aSearch[1]);
					if(result['offset'] == Number.NaN)
						result['offset'] = 0;
					searches[s] = "";
					break;
				case 'count':
					if(result['count'] == null)
						result['count'] = new Array();
					result['count'] = parseInt(aSearch[1]);
					if(result['count'] == Number.NaN)
						result['count'] = 12;
					searches[s] = "";
					break;

					
				default: 
					alert(search[0]);
				
			
			}
		}
		
		
		// add our tags
		if(searches[s] != ""){
			var tags = searches[s].split(',');
			for(t in tags){
				tags[t] = tags[t].split('=')[0];
				tags[t] = tags[t].split(',')[0];
				if(tags[t].charAt(0) == '!') {
					if(result['exclude'] == null)
						result['exclude'] = new Array();
					result['exclude'].push(trim(tags[t].substr(1))); 
				}
				else {
					if(result['include'] == null)
						result['include'] = new Array();

					result['include'].push(trim(tags[t]));
				}
			}
		}
	}

	return result;
}

function displaySearchRuleset(search){
	$("#searchRules").empty();
	var box = $(document.createElement('div'));
	box.attr({'class':'searchRuleBox'});
	
	
	if(search['order'] != null && search['order'].length > 0){
		var order = box.clone();
		order.html("order is: " + search['order'].join(', '));
		$("#searchRules").append(order);
	}
	
	if(search['offset'] != null || search['count'] != null) {
		var range = box.clone();
		range.html(
			(search['offset'] != null ? "offset = " + search['offset'] + "<br />": "") 
			+ (search['count'] != null ? "count = " + search['count'] : "")
		);
		$("#searchRules").append(range);
	}
	
	if(search['include'] != null) {
		var include = box.clone();
		include.html("only include tags: " + search['include'].join(', '));
		$("#searchRules").append(include);		
	}
	if(search['exclude'] != null) {
		var exclude = box.clone();
		exclude.html("exclude tags: " + search['exclude'].join(', '));
		$("#searchRules").append(exclude);
	}
}

function getImages(searchRules) {
	// transforms our arrays to strings
	for(i in searchRules)
		if(typeGet(searchRules[i]) == "Array")
			searchRules[i] = searchRules[i].join(',');
	searchRules['do'] = 'getImages';
	
	$.ajax({
		data: urlify(searchRules),
		dataType: 'json',
		success: function (images) {
			$("#content").empty();
			for(i in images)
				$("#content").append(makeImageBox(images[i]));
		},
		error: function (x, y, z) {
			rAlert(Array(x,y,z));
		}
	});
}


function makeImageBox (image) {
	var block = $(document.createElement('div'));
	var link = $(document.createElement('a'));
	var img = $(document.createElement('img'));
	var options = makeImageOperations(image);
	
	block.attr({'class':'imageBlock'});
	link.attr({
		'href':'javascript:viewImage('+image['id']+')',
		'title':'Click to zoom!'
	});
	img.attr({
		'id':'image' + image['id'],
		'class':'thumbnail',
		'src':'./thumbs/' + image['file'],
		'alt':image['name']
	});
	
	link.append(img);
	block.append(link);
	block.append(options);
	
	return block;
}

function makeImageOperations(image) {
	var block = $(document.createElement('div'));
	var tagAnchor = $(document.createElement('a'));
	var tagImage = $(document.createElement('img'));
	
	block.attr({'class':'imageOperations'});
	tagAnchor.attr({
		'href':'javascript:tagDialog('+image['id']+');',
		'title': 'Tag this image!'
	});
	tagImage.attr({
		'class':'tagAction',
		'src':'./design/icons/tag.png',
		'alt':'Tag this image!'
	});
	
	tagAnchor.append(tagImage);
	block.append(tagAnchor);
	
	return block;
}



