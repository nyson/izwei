$(document).ready(function() {
	$.ajaxSetup ({
		url: "./ajax.php",
		method: "GET",
		cache: false
	})
	
	// let's make a dialog!
	$("#dialog").dialog({autoOpen: false});
});

$(window).resize(function () {
	//alert($("#monad").css('display'));
	if($("#monad").css('display') == "block")
		$("#imageZoomBox").css({
			"maxHeight": window.innerHeight * 0.96 + 2,
			"margin-top" : window.innerHeight * 0.01
		});
});

function monad(content){
	$(document.body).css({'overflow':'hidden'});
	$("#monadContent").append(content);
	$("#monadContent").css({"display":"block"});
	$("#monad").css({"display":"block"});
}

function closeMonad() {
	$(document.body).css({'overflow':'auto'});
	$("#monadContent").children().remove();
	$("#monadContent").css({"display":"none"});
	$("#monad").css({"display":"none"});
}