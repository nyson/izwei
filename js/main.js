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
		if(event.keyCode == '13')
			$("#sumbitImage").click();
			event.preventDefault();
	});
	
	// let's make a dialog!
	$("#dialog").dialog({autoOpen: false});
});

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
