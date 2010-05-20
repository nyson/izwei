$(document).ready(function() {
	$.ajaxSetup ({
		url: "./ajax.php",
		method: "GET"
	})
	
	// let's make a dialog!
	$("#dialog").dialog({
		autoOpen: false,
		buttons: {
			"Tag!" : function(){$(this).dialog("close")},
			"Cancel" : function(){$(this).dialog("close")},
		},
		title : "Tag",
		modal: true,
		dialogClass: 'dialog'
	});
	
})

/**
 * creates a tagdialog to tag image imageId 
 * @param imageId id of image to 
 */
function tagDialog(imageId) {
	$("#dialog").dialog("open");
	$.ajax({
		data: {"do": "getTags", "image":imageId},
		dataType: "json",
		success: function(image) { 
			$("#dialog > .content").html();
			$("#dialog > .content").html(formatImage(image));
			
		},
		error: function() {alert("AIDS")}
	});
	
};

function formatImage(image) {
	var hello = $(document.createElement('span')); 
	for (i in image) {
		hello.add("a")
			.attr({
				"href": "javascript:showTag("+image[i]['id']+");",
				"title": image[i]['description']
			})
			.html(image[i]['tag']);
	}
	
	return hello;
}

function showTag(tag) {
	alert("dummy, " + tag);
}
