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
	
	$("#quickSearchExecute").click(function () {
		var search = getSearchArray($("#quickSearchText").val());
		//	rAlert(search);
		displaySearchRuleset(search);
		getImages(search);
	});
	
	
	// let's make a dialog!
	$("#dialog").dialog({autoOpen: false});
});

/**
 *	Creates a monad from jquery element content
 * 
 * @param content jquery element to show in the monad
 */
function monad(content){
	$(document.body).css({'overflow':'hidden'});
	$("#monadContent").append(content);
	$("#monadContent").css({"display":"block"});
	$("#monad").css({"display":"block"});
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