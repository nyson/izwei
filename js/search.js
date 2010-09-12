/**
 * 
 */

function searchHelp() {
	var str = " include(any tag): \n'tag tag2 tag3'\n "
		+ "\nexclude(any tag): \n'!tag !tag2 !tag3'\n "
		+ "\norder (mode): \nsupported modes: random, best/worst, newest/oldest, mosttags/leasttags): \n'order=newest,best'\n "
		+ "\nrange (number): \n'offset=1 count=2'\n "
		+ "\ncombine!\n 'count=20 order=random !nsfw'"
	alert(str);
}

/**
 * Takes a string and returns an array of rules
 * 
 *  @param string searchString
 */
function getSearchArray(searchString) {
	var searches = searchString.split(" ");
	var result = new Array();
	var acceptedOrders = new Array('best', 'worst', 'newest', 'oldest', 'random', 'mosttags', 'leasttags');
	var caught;
	$("#thumbnails").empty();
	$("#thumbnails").append(loadImage());
	
	
	for(s in searches) {
		caught = false;
		// get our excluded tags
		if((aSearch = searches[s].split('=')).length > 1) {
			switch(aSearch[0]) {
				// our sort order
				case 'order':	 
					var addOrders = aSearch[1].split(',');
						for(o in addOrders)
							if(isInArray(addOrders[o], acceptedOrders)){
								if(result['order'] == null)
									result['order'] = new Array();
								result['order'].push(addOrders[o]);
								caught = true;
							}					
					break;
				
				// our offset of the search
				case 'offset':
					if(result['offset'] == null)
						result['offset'] = new Array();
					result['offset'] = parseInt(aSearch[1]);
					if(result['offset'] == Number.NaN)
						result['offset'] = 0;
					caught = true;
					break;
					
				// amount of images to show
				case 'count':
					if(result['count'] == null)
						result['count'] = new Array();
					result['count'] = parseInt(aSearch[1]);
					if(result['count'] == Number.NaN)
						result['count'] = 12;
					caught = true;
					break;
			}
		}
		
		// add our tags
		if(!caught){
			var tags = searches[s].split(',');
			
			for(t in tags){
				tags[t] = tags[t].split('=')[0];
				tags[t] = tags[t].split(',')[0];
								
				if(tags[t] == '')
					break;
				
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

function getImages(searchRules) {
	var searchString;
	// if searchRules is a string, it's the exact string we want to search
	// with.
	if(typeof searchRules == 'string') {
		searchString = searchRules;
		setHash('search:' + encode64(searchString));
	} else if(searchRules == null) {
		// If searchRules is null, then we get the default set of images,
		// just like on front page load. If this is the case, we don't set
		// the hash, because the result is going to be identical to just
		// loading the front page.
		searchString = 'do=getImages';
	} else {
		// transforms our arrays to strings
		for(i in searchRules) {
			if(typeGet(searchRules[i]) == "Array") {
				searchRules[i] = searchRules[i].join(',');
			}
		}
		searchRules['do'] = 'getImages';
		searchString = urlify(searchRules);
		setHash('search:' + encode64(searchString));
	}

	$.ajax({
		data: searchString,
		dataType: 'json',
		success: function (images) {
			$("#thumbnails").empty();
			closeModal();
			for(i in images)
				$("#thumbnails").append(makeImageBox(images[i]));
		},
		error: function (x, y, z) {
			rAlert(Array(x,y,z));
		}
	});
}

