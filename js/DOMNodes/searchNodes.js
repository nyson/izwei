// searchNodes.js - DOM Nodes associated with clientside search functions

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

function loadImage() {
	var load = $(document.createElement("img"));
	load.attr({
		"src":"./design/load.gif",
		"alt":"Loading...",
		"class":"ajaxLoading"
	 });
	 
	 monad();
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
