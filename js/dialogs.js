/*/////////////////////////////////////////////////////
 *  Add new tags dialog
 * 
 */

/**
 * creates a tagdialog to tag image imageId 
 * @param imageId id of image to 
 */
function tagDialog(imageId) {
	$.ajax({
		data: {"do": "getTags", "image":imageId},
		dataType: "json",
		success: function(tags) { 
			monad(tagDialogContent(tags, imageId));			
		},
		error: function() {alert("AIDS");}
	});
	
};


/**
 * Formats a list of tags and an image id to a dialog to tag an image
 * 
 * @param tags an associative array of tags to list
 * @param image the image where we add tags
 * 
 * @return a neatly formatted box to append to the document
 */
function tagDialogContent(tags, image) {
	var box = $(document.createElement('div'));
	var tagField = $(document.createElement('fieldset'));
	var textBox = $(document.createElement('input'));
	var imageId = textBox.clone();
	var okButton = textBox.clone();
	okButton.attr({'type':'button'});
	var cancelButton = okButton.clone();
	
	box.attr({'id':'tagDialog'});
	
	tagField.append("<legend>Current Tags</legend>");
	tagField.addClass("tagField");

	textBox .attr({
		"id": "tagNewTags",
		"type": "text"
	});
		
	imageId .attr({
		"id": "tagImageId",
		"type": "hidden",
		"value": image		
	});
	
	okButton.val("Submit tags!");
	okButton.click(function () {
		submitTags();
		closeMonad();		
	});
	
	cancelButton.val("Cancel");
	cancelButton.click(closeMonad);
	

	for (t in tags) {
		tag = $(document.createElement('a'));
		
		tag.html(tags[t]['tag']);
		tag.attr({
				"href": "javascript:showTag("+tags[t]['id']+");",
				"title": tags[t]['description']
			});
		tagField.append(tag);
	}

	if(tags.length > 0) {
		box.append(tagField);
		box.append("<br />");
	}
	else {
		label = $(document.createElement("label"));
		label.html("No tags yet! Be the first to tag it!");
		box.append(label);
		box.append("<br />");
	}	

	box.append(textBox);
	box.append(imageId);
	box.append($(document.createElement("br")));
	box.append(cancelButton);
	box.append(okButton);
	return box;
}
