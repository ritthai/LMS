var http_request = false;
var timer_done = true;
var cur_post_form;
var new_comment_id = 0;
var timeout = 60*1000; // 1 min
function makePOSTRequestComment(url, parameters) {
	http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}
	if (!http_request) {
		alert('Cannot create XMLHTTP instance');
		return false;
	}

	http_request.onreadystatechange = alertContentsComment;
	http_request.open('POST', url, true);
	http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_request.setRequestHeader("Content-length", parameters.length);
	http_request.setRequestHeader("Connection", "close");
	http_request.send(parameters);
}

function alertContentsComment() {
	if(http_request.readyState == 4) {
		if(http_request.status == 200) {
			result = http_request.responseText;
			if(result == '0') window.location = window.location;
		} else {
			timer_done = true;
		}
	}
}

function check() {
	if(timeout < 0) {
		window.location = window.location;
		return;
	}
	timeout -= 1000;
	cid = RegisteredGlobals["CourseCID"];
	var poststr = "cid="+cid;
	makePOSTRequestComment('/check_lock', poststr);
}
jQuery(document).ready(function() {
	check();
	setInterval("check()", 1000);
});

function submit_(obj, boxid) {
	cur_post_form = obj;
	timer_done = true; // workaround
	if(timer_done == false) {
		alert("You may only make one post every 5 seconds.");
		timer_done = true;
		return false;
	}
	if(obj.body.value == '') {
		alert("Can't make an empty post.");
		return false;
	}
	if(obj.owner.value == 'not logged in') {
		jQuery("#top_login_link").trigger('click');
		/*jQuery.fancybox.showActivity();
		jQuery.ajax({   
            type        : "POST",
            cache       : false,
            url         : "/user/ajaxlogin",
            data        : jQuery(obj).serializeArray(),
            success     : function(data) {
                jQuery.fancybox(data);
            }
        });*/
		return false;
	}

	cid = "comment_"+new_comment_id;
	new_comment_id++;
	$(boxid).innerHTML = $(boxid).innerHTML + "\
		<div class=\"comment\" style=\"display:none\" id=\""	+cid+	"\">\
			<div class=\"avatar\">\
				<div class=\"user_image\">\
					<img src=\""	+RegisteredGlobals["AuthenticatedUserAvatar"]+	"\" />\
				</div>\
			<div>\
			<h5>"	+obj.subject.value+	" - Posted now by "	+obj.owner.value+	"</h5>\
			<p>"	+obj.body.value+	"</p>";
	jQuery("#"+cid).slideDown('slow');

	timer_done = false;
}
