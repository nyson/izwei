1
/**
 * Creates an imageBlock within a monad
 * 
 * @param id of the image to view
 */

function viewImage(imageId){
	var imageBlock = $(document.createElement("div"));
	var imageImage = $(document.createElement("img"));
	var imageWidth = $(document.createElement("input"));

	monad(imageBlock);

	imageWidth.attr({
		"type":"hidden",
		"value":0,
		"id":"imageZoomWidth"
	});
	
	imageBlock.attr({
		"id":"imageZoomBox"
	});
	
	imageImage.attr({
		"id":"imageZoomView",
		"src":"./design/loading.gif"
	});
	
	imageImage.click(function() {
			setHash('');
			closeMonad();
		});
	imageBlock.append(imageImage);
	imageBlock.append(imageWidth);
	
	$.ajax({
		data: {
			"do": "getImage",
			"image": imageId
		},
		dataType: "json",		
		success: function (image) {
			setHash(imageId);
			var width= (image['width'] < window.innerWidth 
					? image['width'] : (window.innerWidth * 0.9)) + "px";
			$("#imageZoomView").attr({
				"src":"./images/" + image['file']
			});
			
			$("#imageZoomWidth").val(image['width']);
			
			$("#imageZoomView").css({
				"width":width,
				"maxWidth": image['width']
			});
			
			$("#imageZoomBox").css({
				"width":width,
				"maxHeight": window.innerHeight * 1,
				"display": "block"
			});
			
			// binds a rezize of the window to a resize of the imagebox
			$(window).resize(function () {
				if($("#monad").css('display') == "block")
					var width= ($("#imageZoomWidth").val() < window.innerWidth 
							? $("#imageZoomWidth").val() 
							: (window.innerWidth * 0.9)) 
						+ "px";
					
					$("#imageZoomBox").css({
						"maxHeight": window.innerHeight * 1 + "px",
						"width": width
					});
					
					$("#imageZoomView").css({"width":width});
			});
			
			
		},
		error: function () {
			alert("Could not retrieve image!");
			closeMonad();
		}
	});
	

}
