// searchNodes.js - DOM Nodes associated with clientside search functions

function displaySearchRuleset(search){
	$("#searchRules").empty();
	var box = $(document.createElement('div'));
	var head = $(document.createElement('h2'));
	box.attr({'class':'searchRuleBox'});
		
	// our sorting methods
	if(search['order'] != null && search['order'].length > 0){
		var oHead = head.clone();
		oHead.html("Sorting by: ");
		$("#searchRules").append(oHead);

		for(o in search['order']) {
			var order = box.clone();
			order.html(search['order'][o]);
			$("#searchRules").append(order);
		}
	}
	
	// our offset
	if(search['offset'] != null || search['count'] != null) {
		var range = head.clone();
		range.html(
			(search['offset'] != null ? "offset = " + search['offset'] + "<br />": "") 
			+ (search['count'] != null ? "count = " + search['count'] : "")
		);
		$("#searchRules").append(range);
	}
	
	// our included tags
	if(search['include'] != null && search['include'].length > 0) {
		var iHead = head.clone();
		iHead.html("Included tags");
		$("#searchRules").append(iHead);
		
		for(i in search['include']) {
			var include = box.clone();
			include.html(search['include'][i]);
			$("#searchRules").append(include);
		}
	}
	
	// our excluded tags
	if(search['exclude'] != null && search['exclude'].length > 0) {
		var eHead = head.clone();
		eHead.html("Excluded tags");
		$("#searchRules").append(eHead);
		
		for(e in search['exclude']) {
			var exclude = box.clone();
			exclude.html(search['exclude'][e]);
			$("#searchRules").append(exclude);
		}
	}}

function loadImage() {
	var load = $(document.createElement("img"));
	load.attr({
		"src":"./design/load.gif",
		"alt":"Loading...",
		"class":"ajaxLoading"
	 });
	 
	 modal();
	 return load;	 
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

