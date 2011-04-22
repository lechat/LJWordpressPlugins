<?php

echo 'var lj_comments_req;

lj_comments_req = false;

if (window.ActiveXObject) {
	try {
		lj_comments_req = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			lj_comments_req = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (e) {
			lj_comments_req = false;
		}
	}
} // if (window.ActiveXObject)
else if (window.XMLHttpRequest && !(window.ActiveXObject)) {
	try {
		lj_comments_req = new XMLHttpRequest();
	} catch (e) {
		lj_comments_req = false;
	}
} // if(window.XMLHttpRequest && !(window.ActiveXObject))


function lj_comments_ajax_get(url, need_to_handle_response) {
	if(lj_comments_req) {
		lj_comments_req.open("GET", url, true);
		if (need_to_handle_response != null) {
			lj_comments_req.onreadystatechange = lj_comments_ajax_catch_response;
		}
		lj_comments_req.send("");
	}
} // ajax_get

function lj_comments_ajax_catch_response() {
	// only if lj_comments_req shows "loaded"
	if (lj_comments_req.readyState == 4) { // only if "OK"
		if (lj_comments_req.status == 200) { // we got the response
			document.getElementById("lj_sync_progress").innerHTML = "LiveJournal comments import finished.";
		} // if (lj_comments_req.status == 200)
	} // if OK
} // ajax_catch_response

function lj_comments_call_sync(need_to_handle_response) {
	if (need_to_handle_response == null) {
		lj_comments_ajax_get("http://'.$_SERVER["HTTP_HOST"].'/wp-content/plugins/lj-comments-import-reloaded/sync_lj_comments.php");
	}
	else {
		lj_comments_ajax_get("http://'.$_SERVER["HTTP_HOST"].'/wp-content/plugins/lj-comments-import-reloaded/sync_lj_comments.php", true);
	}
}

function lj_comments_adminpage_sync() {
	document.getElementById("lj_sync_progress").innerHTML = \'<img src="/wp-content/plugins/lj-comments-import-reloaded/ajax-loader.gif" alt="loading..." />\';
	lj_comments_call_sync(true);
}';

?>
