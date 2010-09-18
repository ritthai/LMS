function reload() {
	window.location.reload(true);
}
jQuery(document).ready(function() {
	jQuery(".all_result_content").each(function(idx) {
		jQuery(this).hide();
	});
	jQuery("a.youtube_link").each(function(idx) {
		//jQuery(this).attr('href', '#');
		jQuery(this).fancybox({ 'onStart' : function() { setTimeout('jQuery.fancybox.hideActivity()', 1000); } });
	});
	if($("logged_in").innerHTML == "n") {
		jQuery("#fav_block").hide();
	}
	/*jQuery('img[title]').qtip({
		style: {
			name: 'cream',
			tip: true
		}
	});*/
	/*jQuery('.youtube_thumb').each(function(idx) {
		id = jQuery(this).attr('title');
		title = jQuery(this).attr('alt');
		jQuery(this).onMouseOver(function() {
			$('youtube_caption_'+id).innerHTML = 
		});
	});*/
	/*jQuery(".add_to_favs").each(function(idx) {
		jQuery(this).fancybox({
			'scrolling'     : 'no',
			'titleShow'     : false,
			'onClosed'      : function() {
				jQuery("#login_error").hide();
			}
		});
	});*/
	jQuery(".loginlink").each(function() {
		jQuery(this).fancybox({
			'scrolling'     : 'no',
			'titleShow'     : false,
			'onClosed'      : function() {
				jQuery("#login_error").hide();
			}
		});
    });
	jQuery("#login_form").bind("submit", function() {
		jQuery.fancybox.showActivity();
		jQuery.ajax({
			type		: "POST",
			cache		: false,
			url			: "/user/ajaxlogin",
			data		: jQuery(this).serializeArray(),
			success: function(data) {
				//jQuery.fancybox(data);
				if(data[0] == 'Y') {
					setTimeout('reload()', 1000);
				} else {
					jQuery("#login_error").html(data);
					jQuery(".loginlink").click();
				}
			}
		});
		return false;
	});
})
// http://blog.pengoworks.com/index.cfm/2009/4/21/Fixing-jQuerys-slideDown-effect-ie-Jumpy-Animation
// this is a fix for the jQuery slide effects
function slideToggle(el, bShow){
  var $el = jQuery(el), height = jQuery(el).data("originalHeight"), visible = jQuery(el).is(":visible");
  
  // if the bShow isn't present, get the current visibility and reverse it
  if( arguments.length == 1 ) bShow = !visible;
  
  // if the current visiblilty is the same as the requested state, cancel
  if( bShow == visible ) return false;
  
  // get the original height
  if( !height ){
    // get original height
    height = $el.show().height();
    // update the height
    $el.data("originalHeight", height);
    // if the element was hidden, hide it again
    if( !visible ) $el.hide().css({height: 0});
  }

  // expand the knowledge (instead of slideDown/Up, use custom animation which applies fix)
  if( bShow ){
    $el.show().animate({height: height}, {duration: 250});
  } else {
    $el.animate({height: 0}, {duration: 250, complete:function (){
        $el.hide();
      }
    });
  }
};
var last_cid = 0;
var last_comment_cid = 0;
function closeContentPanel(cid) {
	id = "#all_result_content_"+cid;
	img = "#expand_"+cid;
	jQuery(id).slideUp();
	jQuery(img).removeClass('collapse');
	jQuery(img).addClass('expand');
}
function scroll(duration) {
	if(duration > 0) {
		window.scrollBy(0,-30);
		scrolldelay = setTimeout('scroll()',100);
		scroll(duration - 50);
	}
}
function toggleContentPanel(cid) {
	if(cid < 1000000000 && last_cid != 0 && last_cid != cid) {
		id = "#all_result_content_"+last_cid;
		img = "#expand_"+last_cid;
		jQuery(id).slideUp(400);
		//scroll(400);
		jQuery(img).removeClass('collapse');
		jQuery(img).addClass('expand');
	} else if(cid >= 1000000000 && last_comment_cid != 0 && last_comment_cid != cid) {
		id = "#all_result_content_"+last_comment_cid;
		img = "#expand_"+last_comment_cid;
		jQuery(id).slideUp(400);
		//scroll(400);
		jQuery(img).removeClass('collapse');
		jQuery(img).addClass('expand');
	}
	if(cid < 1000000000) last_cid = cid;
	else if(cid >= 1000000000) last_comment_cid = cid;
	id = "#all_result_content_"+cid;
	img = "#expand_"+cid;
	if(jQuery(id).is(":hidden")) {
		jQuery(id).slideDown(400);
		//jQuery(id).show().animate({height: height}, {duration: 250});
		//slideToggle(id, true);
		jQuery(img).removeClass('expand');
		jQuery(img).addClass('collapse');
	} else {
		jQuery(id).slideUp();
		//slideToggle(id, false);
		jQuery(img).removeClass('collapse');
		jQuery(img).addClass('expand');
	}
}
function toggleVidPanel(cid) {
	var panel_width = jQuery("#vid_panel_"+cid).width() + 5;
	if(parseInt(jQuery("#result_content_"+cid).css('marginLeft'),10) < panel_width
		&& !jQuery("#result_content_"+cid).is(':animated') )
	{ // vid panel open 
		jQuery("#result_content_"+cid).animate( { marginLeft: "+="+panel_width }, 400 );
	} else if(!jQuery("#result_content_"+cid).is(':animated')) { // vid panel closed
		jQuery("#result_content_"+cid).animate( { marginLeft: "-="+panel_width }, 400 );
	}
}
