var http_request = false;
var last_id, last_type, last_cid, last_subj, last_owner;
var vote_last_id, vote_last_type, vote_last_cid, vote_last_owner, vote_last_req;
var last_req;
function makePOSTRequest(url, parameters) {
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

	http_request.onreadystatechange = alertContents;
	http_request.open('POST', url, true);
	http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_request.setRequestHeader("Content-length", parameters.length);
	http_request.setRequestHeader("Connection", "close");
	http_request.send(parameters);
}

function getAddToFavsStr(str) {
	return "<div class=\"sidebar_add_to_favs\">"+str+"</div>\
            <div class=\"addToFavs\" style=\"float:right\">&nbsp;</div>";
}
function getAddedFavStr() {
	ret ="<div class=\"sidebar_fav_course_container\">\
			<a  class=\"bodylink rm_from_favs\"\
                id=\"rm_from_favs_"+last_cid+"\"\
                href=\"javascript:rmFromFavs('"+last_cid+"',\
                                             '"+last_owner+"',\
                                             '"+last_type+"',\
                                             '"+last_subj+"' );\">\
				<div class=\"rmFromFavs sidebar_rm_from_favs\">&nbsp;</div>\
			</a>\
			<a href=\"/search?id="+last_id+"\">\
				<div class=\"sidebar_fav_course\">\
					"+last_subj+"\
				</div>\
			</a>\
			<div class=\"addToFavs sidebar_add_to_favs\">&nbsp;</div>\
			<div style=\"clear:both\"></div>\
			</div>";
	return ret;
}
function getRemoveFromFavsStr(str) {
	return "<div class=\"sidebar_remove_from_favs\">"+str+"</div>\
            <div class=\"removeFromFavs\" style=\"float:right\">&nbsp;</div>";
}
function alertContents() {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			result = http_request.responseText;
			if(last_req == 1) {
				$(last_id).innerHTML = getAddToFavsStr(result);
				$("fav_"+last_type+"s").innerHTML += getAddedFavStr();
				jQuery("#fav_courses").toggleClass("hidden", false);
				//sidebar_fav_course
			} else {
				window.location.reload(true);
			}
		} else if(false) {
			if(last_req == 1) {
				$(last_id).innerHTML = "Couldn't add to favs.";
				alert('Could not add favourite.\n\r' + 'Error code: ' + http_request.status);
			} else {
				alert('Could not remove favourite.\n\r' + 'Error code: ' + http_request.status);
			}
		}
	}
}
function addToFavs(cid, owner, type, subj) {
	id = "add_to_favs_"+cid;
	if($("logged_in").innerHTML == "n") {
		old_href = jQuery("#"+id).attr('href');
		jQuery("#"+id).fancybox({
			'scrolling'		: 'no',
			'titleShow'		: false,
			'onClosed'		: function() {
				jQuery("#login_error").hide();
			}
		});
		jQuery("#"+id).attr('href', '#login_form');
		jQuery("#"+id).click();
		jQuery("#"+id).attr('href', old_href);
		return;
	}

	last_id = id;
	last_cid = cid;
	last_owner = owner;
	last_type = type;
	last_subj = subj;
	last_req = 1;
	$(id).innerHTML = getAddToFavsStr("Adding...");

	var poststr = "cid=" + encodeURI( cid )
					+ "&owner=" + encodeURI( owner )
					+ "&type=" + encodeURI( type );
	makePOSTRequest('/favs', poststr);
}
function rmFromFavs(cid, owner, type, subj) {
	if(!window.confirm("Are you sure you want to remove this course from your favourites?")) return;
	id = "favs_"+cid;
	last_id = id;
	last_cid = cid;
	last_subj = subj;
	last_type = type;
	last_req = 2;

	var poststr = "cid=" + encodeURI( cid )
					+ "&owner=" + encodeURI( owner )
					+ "&type=" + encodeURI( type );
	makePOSTRequest('/favsrm', poststr);
	window.location.reload(true);
}
function voteUp(cid, owner, type) {
	id = "vote_up_"+cid;
	if($("logged_in").innerHTML == "n") {
		old_href = jQuery("#"+id).attr('href');
		jQuery("#"+id).fancybox({
			'scrolling'		: 'no',
			'titleShow'		: false,
			'onClosed'		: function() {
				jQuery("#login_error").hide();
			}
		});
		jQuery("#"+id).attr('href', '#login_form');
		jQuery("#"+id).click();
		jQuery("#"+id).attr('href', old_href);
		return;
	}

	vote_last_id = id;
	vote_last_cid = cid;
	vote_last_owner = owner;
	vote_last_type = type;
	last_req = 3;
	$(id).innerHTML = "Voting...";

	var poststr = "cid=" + encodeURI( cid )
					+ "&owner=" + encodeURI( owner )
					+ "&type=" + encodeURI( type );
	makePOSTRequest('/voteup', poststr);
}
function voteDown(cid, owner, type) {
	id = "vote_up_"+cid;
	if($("logged_in").innerHTML == "n") {
		old_href = jQuery("#"+id).attr('href');
		jQuery("#"+id).fancybox({
			'scrolling'		: 'no',
			'titleShow'		: false,
			'onClosed'		: function() {
				jQuery("#login_error").hide();
			}
		});
		jQuery("#"+id).attr('href', '#login_form');
		jQuery("#"+id).click();
		jQuery("#"+id).attr('href', old_href);
		return;
	}

	vote_last_id = id;
	vote_last_cid = cid;
	vote_last_owner = owner;
	vote_last_type = type;
	last_req = 3;
	$(id).innerHTML = "Voting...";

	var poststr = "cid=" + encodeURI( cid )
					+ "&owner=" + encodeURI( owner )
					+ "&type=" + encodeURI( type );
	makePOSTRequest('/votedown', poststr);
}
