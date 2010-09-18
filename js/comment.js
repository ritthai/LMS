var http_request = false;
var timer_done = true;
var cur_post_form;
var new_comment_id = 0;
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
			//jQuery("#comment_"+(new_comment_id-1)).slideDown('slow');
			result = http_request.responseText;
			cur_post_form.body.innerHTML = "Result: "+result;
			var t=setTimeout("clearReceivedComment()", 0);
		} else {
			alert('Could not post message.\n\r' + 'Error code: ' + http_request.status);
			timer_done = true;
		}
	}
}
function clearReceivedComment() {
	cur_post_form.reset();
	cur_post_form.subject.value = "";
	cur_post_form.body.value = "";
	timer_done = true;
}

function submit_comment(obj, boxid) {
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
		//window.location = '/user/login';
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
	var poststr = "body="		+ encodeURI( obj.body.value )
				+ "&subject="	+ encodeURI( obj.subject.value )
				+ "&owner="		+ encodeURI( obj.owner.value )
				+ "&cid="		+ encodeURI( obj.cid.value );
	makePOSTRequestComment('/post', poststr);
}
