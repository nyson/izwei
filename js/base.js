/**
 * Basic javascript functions like typechecking and printing an object
 */


/**
 * transforms an array to a goody goody string for use as an ajax url
 * 
 * @param arr key-value array to return as a string
 */
function urlify(arr) {
	ret = new Array();
	for(a in arr) {
		ret.push(a + "=" + encodeURIComponent(arr[a]));
	}
	return ret.join('&');
}

/**
 * trims whitespace, tabs, newlines and nullbytes from strings
 */
function trim(str) {
	return str.replace(/^\s*|\s*$/g, '');
}

/**
 * Checks if the element elem is in array 
 * @param elem 
 * @param array 
 * @return bool true on found element else false
 */
function isInArray(elem, array) {
	for(a in array) {
		// Use ===, or else isInArray(0, ['']) == true
		if(array[a] === elem)
			return true;
	}
	return false;
}


/**
 * Gets the type of an element and returns a neat string
 * @param element the element we want to check the type on
 * @return
 */
function typeGet(element) {
	if(element == null)
		return "null";
	return element.constructor.toString().substr(9).split('(',1)[0];
}

/**
 * recursively traverses through an array or string and returns the output
 * @param input
 * @return the traversed input
 */
function rEcho(input, depth) {
	if(input == null)
		return;
	var ret = "";
	if(depth == null)
		depth = 0;
	if(typeGet(input) == "Array" || typeGet(input) == "Object"){
		for(i in input){
			for(var j = 0; j < depth; j++)
				ret += (j == 1 ? "" : "\t"); 
			
			ret +=  "'" + i + "' => ";
			if(typeGet(input[i]) == "Array")
				ret += "\n";
			ret += rEcho(input[i], depth+1);
		}
	}
	else {
		ret += input+" (" +typeGet(input)+ ")\n";
	}
	return ret;			
}

/**
 * recursively traverses through an array or string and returns the output
 * @param input the input we want to traverse from
 */
function rAlert(input) {
	alert(rEcho(input, 0));
}
