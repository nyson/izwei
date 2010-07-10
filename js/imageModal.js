1
/**
 * Creates an imageBlock within a modal dialog.
 * 
 * @param id of the image to view
 */

function viewImage(imageId){
	var imageBlock = $(document.createElement("div"));
	var imageImage = $(document.createElement("img"));
	var imageWidth = $(document.createElement("input"));

	modal(imageBlock, true);

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
			closeModal();
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
			var width;
			if(image['width'] < $(window).width()*0.9) {
				width = image['width'];
			} else {
				width = $(window).width() * 0.9;
			}
			$("#imageZoomView").attr({
				"src":"./images/" + image['file']
			});
			
			$("#imageZoomWidth").val(image['width']);
			
			$("#imageZoomView").css({
				"width": width + 'px',
				"maxWidth": image['width'] + 'px'
			});
			
			$("#imageZoomBox").css({
				"width": width + 'px',
				"maxHeight": $(window).height() + 'px',
				"display": "block"
			});
			
			// binds a rezize of the window to a resize of the imagebox
			$(window).resize(function () {
				if($("#modal").css('display') == "block")
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
			closeModal();
		}
	});
	

}
