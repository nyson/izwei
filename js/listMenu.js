// listMenu.js - handles behaviour of our list menu


 //bindBubbleKeys();


/**
 * Returns true if we're in a position where we can listen for incoming keys
 */
function isBubbleKeyListenAvalible() {
	for(var i = 0; i < $(".menuPopupContainer").length; i++)
		if($(".menuPopupContainer:eq("+i+")").css('display') != "none")
			return false;

	if($("#modal").css("display") != "none")
		return false;

	return true;
}
 

/**
 * toggles a bubble or destroys all bubbles
 * @param id null if all bubbles should be destroyed, else id is a representing string for the bubble to remove/show
 */ 
function bubble(id) {
	if(id === null) {
		clearBubbles();
		return;
	}
	
	switch(id) {
		case 'search':
			toggleBubble("#popupBubbleSearch");
			break;
			
		case 'upload':
			toggleBubble("#popupBubbleUpload");
			break;
		default: 
			clearBubbles();
			break;
	}
}

/**
 * Toggles or removes bubble with given id
 * @param id id of our buble
 */
function toggleBubble(id) {

 	if($(id).css("display") != "block"){
		clearBubbles();
		$(id).css({"display":"block"});
		$(id).find(".iWantFocus").focus();
		
		
		$(id).keydown(function(event){
			if(event.keyCode == 27)
				bubble();
		});
	}
	else {
		clearBubbles();
		$(id).css({"display":"none"});

	}


}

/**
 * Clears all visible bubbles
 */
function clearBubbles() {
	$(".menuPopupContainer").css({"display":"none"});
}

/**
 * Binds keys for our bubbles
 */
function bindBubbleKeys() {
	$(document).bind('keydown', function(event) {
		
		// we don't want anything except for these keys to be processed
		switch(event.which) {
			case 115: case 83: // s 
			case 117: case 85: // u
				break;			
			default: 
				return;
				break;
		}

		// checks if our keys are listened to
		if(isBubbleKeyListenAvalible())
			switch(event.which) {
				// s or S
				case 115: 
				case 83: 
					event.preventDefault();
					bubble("search");
					break;
				// u or U
				case 85:
				case 117:
					event.preventDefault();
					bubble("upload");		
					break;
			}	
	});
}


