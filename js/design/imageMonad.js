function viewImage(imageId){
	var imageBlock = $(document.createElement("div"));
	var imageImage = $(document.createElement("img"));
	
	monad(imageBlock);
	
	imageBlock.attr({
		"id":"imageZoomBox"
	});
	
	imageImage.attr({
		"id":"imageZoomView",
		"src":"./design/loading.gif"
	});
	
	imageImage.click(closeMonad);
	imageBlock.append(imageImage);
	
	$.ajax({
		data: {
			"do": "getImage",
			"image": imageId
		},
		dataType: "json",		
		success: function (image) {
			
			$("#imageZoomView").attr({
				"src":"./images/" + image['file'],
			});
			
			
			$("#imageZoomBox").css({
				"width":image['width'] + "px",
				"maxHeight": window.innerHeight * 1,
				"display": "block"
			});
			
			
		},
		error: function () {alert("ADIDS");}
	});
}