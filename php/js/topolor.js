function htmlEncode(value){
    if (value) {
        return jQuery('<div />').text(value).html();
    } else {
        return '';
    }
}
 
function htmlDecode(value) {
    if (value) {
        return $('<div />').html(value).text();
    } else {
        return '';
    }
}

function getNow() {
	var now;
	var d = new Date();
	var h = d.getHours();
	var m = d.getMinutes();
	if (h < 13)
		now = h;
	else
		now = h-12;
	if (m < 10)
		now += ':0' + m;
	else
		now += ':' +m;
	if (h < 12)
		now += 'AM';
	else
		now += 'PM';
	if (h < 10 || (h > 12 && h < 22))
		now = '0'+ now;
	return now;
}