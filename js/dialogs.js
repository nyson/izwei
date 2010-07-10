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
			modal(tagDialogContent(tags, imageId));			
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
 * @return an array containing two items; the first item is a neatly formatted
 *         box to append to the document, the second is the text box that
 *         needs to be focused when the dialog opens.
 */
function tagDialogContent(tags, image) {
	var box = $(document.createElement('div'));
	var tagField = $(document.createElement('fieldset'));

	// NOTE: since jQuery is fucking retarded, we can't use it to set handlers
	//       on UI elements other than passing them as text which will later
	//       be eval'd. To work around this, we ensure that we get a reference
	//       to the real text box and then bypass jQuery to set the keypress
	//       handler.
	var realTextBox = document.createElement('input');
	realTextBox.onkeypress = function(evt) {
			switch(evt.keyCode) {
				case 27: // escape; close dialog
					closeModal();
					break;
				case 13: // return; submit tags
					submitTags();
					closeModal();
					break;
			}
		}

	var textBox = $(realTextBox);
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
		closeModal();		
	});
	
	cancelButton.val("Cancel");
	cancelButton.click(closeModal);
	

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
	return [box, textBox];
}
