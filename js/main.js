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
	
	// let's make a dialog!
	$("#dialog").dialog({autoOpen: false});

	// Start polling for hash changes; polling is retarded, but it's the only
	// way to do this currently.
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
	hashNav.hash = location.hash;
	var theHash = location.hash.substring(1);
	switch(theHash) {
		case '': // empty hash; close all dialogs
			closeMonad();
			break;
		default: // if we just get a number with no prefix, it's an image ID
			viewImage(theHash);
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
 *	Creates a monad from jquery element content
 * 
 * @param content jquery element to show in the monad, or a two element array
 *                where the first element is the element to show, and the
 *                second is the sub-element of the dialogue that should
 *                receive focus on opening the dialog.
 *                For example, in the tag dialog, the tag text box should
 *                have focus when the dialog opens, so we pass
 *                [dialog, textBox].
 */
function monad(content){
	if(content instanceof Array) {
		thebody = content[0];
	} else {
		thebody = content;
	}
	$(document.body).css({'overflow':'hidden'});
	$("#monadContent").append(thebody);
	$("#monadContent").css({"display":"block"});
	$("#monad").css({"display":"block"});
	if(content instanceof Array && content.length == 2) {
		content[1].focus();
	}	
}
/**
 * Closes the monad
 */
function closeMonad() {
	$(document.body).css({'overflow':'auto'});
	$("#monadContent").children().remove();
	$("#monadContent").css({"display":"none"});
	$("#monad").css({"display":"none"});
}
