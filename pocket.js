	function shareArticleToPocket(id) {
	try {
		var query = "?op=pluginhandler&plugin=oneclickpocket&method=getInfo&id=" + param_escape(id);

		var d = new Date();
	        var ts = d.getTime();

		notify_progress("Saving to Pocket …", true);
		new Ajax.Request("backend.php",	{
			parameters: query,
			onSuccess: function(transport) {
				var ti = JSON.parse(transport.responseText);
				if (ti.status=="1") {
				notify_info("Saved to Pocket:<br/><em>" + ti.title + "</em>");
				} else {
				notify_error("<strong>Error saving to Pocket!</strong><br/>("+ti.status+")");}
			} });

	} catch (e) {
		exception_error("PocketArticle", e);
	}
	}
	

