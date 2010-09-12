/**
 * script start
 */
$(document).ready(function() {
	// sets our default ajax values
	$.ajaxSetup ({
		url: "./ajax.php",
		method: "GET",
		cache: false
	});
	
	$("#quickSearchText").keypress(function (event) {
		if(event.keyCode == '13')
			$("#quickSearchExecute").click();
	});
	
	$("#quickSearchExecute").click(function () {
		var search = getSearchArray($("#quickSearchText").val());
		//	rAlert(search);
		displaySearchRuleset(search);
		getImages(search);
	});
	
	$("#uploadURL").keypress(function(event) {
		if(event.keyCode == '13') {
			$("#sumbitImage").click();
		}
	});
	
	// allows us to use our fancy search and upload windows by keyup
	bindBubbleKeys();
	
	// let's make a dialog!
	$("#dialog").dialog({autoOpen: false});

	// Start polling for hash changes; polling is retarded, but it's the only
	// way to do this currently.
	hashNav.hash = '';
	hashNav();
});

/**
 * Hash-based navigation; take an action depending on the hash we get.
 */
function hashNav() {
	// Return ASAP if the hash didn't change; this path must always be fast,
	// as we'll be running it twice every second.
	if(location.hash == hashNav.hash) {
		window.setTimeout(hashNav, 500);
		return;
	}
	// Save hash for checking next time.
	hashNav.hash = location.hash;

	// Split hash into command and data part.
	var theHash = location.hash.substring(1).split(':', 1)[0];
	var data = location.hash.substring(2+theHash.length);

	switch(theHash) {
		case '': // empty hash; close all dialogs
			closeModal();
			getImages();
			break;
		case 'search': // stored search, perform it and show the results
			getImages(decode64(data));
			break;
		default: // if we just get a number with no prefix, it's an image ID
			if(!isNaN(parseFloat(theHash))) {
				viewImage(theHash);
			}
			break;
	}
	window.setTimeout(hashNav, 500);
}

/**
 * Sets location.hash in such a fashion that hashNav does not handle the
 * change.
 *
 * @param newHash    Hash value to set.
 * @param useHandler Should this change be handled by hashNav?
 */
function setHash(newHash, useHandler) {
	if(!useHandler) {
		hashNav.hash = newHash ? '#' + newHash : '';
	}
	location.hash = '#' + newHash;
}

/**
 *	Creates a modal from jquery element content
 * 
 * @param content jquery element to show in the modal, or a two element array
 *                where the first element is the element to show, and the
 *                second is the sub-element of the dialogue that should
 *                receive focus on opening the dialog.
 *                For example, in the tag dialog, the tag text box should
 *                have focus when the dialog opens, so we pass
 *                [dialog, textBox].
 *
 * @param closeOnClickOutside Should the modal dialog close when the user
 *                            clicks outside it?
 */
function modal(content, closeOnClickOutside){
	if(content instanceof Array) {
		thebody = content[0];
	} else {
		thebody = content;
	}
	$(document.body).css({'overflow':'hidden'});
	$("#modalContent").append(thebody);
	$("#modalContent").css({"display":"block"});
	$("#modal").css({"display":"block"});
	if(closeOnClickOutside) {
		$("#modalContent").click(closeModal);
	}
	if(content instanceof Array && content.length == 2) {
		content[1].focus();
	}	
}
/**
 * Closes the modal
 */
function closeModal() {
	$(document.body).css({'overflow':'auto'});
	$("#modalContent").children().remove();
	$("#modalContent").css({"display":"none"});
	$("#modal").css({"display":"none"});
}	
