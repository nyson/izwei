
/**
 * Checks what type of upload we want based on what fields are filled in
 * @return
 */
function validateUpload() {
	uploadMessage("Validating upload...");
	if($("#uploadURL").val().length == 0 
		&& $("#uploadImage").val().length == 0) { 
		uploadMessage("You have to fill out at least one of the fields!");
	}
	else if ($("#uploadImage").val() != "") {
		uploadMessage("Submitting image!");
		return true;
	}
	else {
		uploadMessage("Uploading '"+$("#uploadURL").val()+"' by URL..");
		uploadByURL($("#uploadURL").val());
		$("#uploadURL").val("");
		return false;
	}
		
	return false;
}


function uploadMessage(message) {
	$("#uploadLabel").css({"display": "block"});
	$("#uploadLabel").html(message);
}

function uploadByURL(url) {
	$.ajax({
		data: {
			"do" : "uploadByURL",
			"url" : url
		},
		success: function(result) {
			console.debug(result);
			//return;
			window.location.reload();
		} 
	});
}
