function shareArticleToPocket(id, btn) {
	try {
		var query = "?op=pluginhandler&plugin=oneclickpocket&method=getInfo&id=" + encodeURIComponent(id);

		var d = new Date();
		var ts = d.getTime();

		Notify.progress("Saving to Pocket …", true);
		new Ajax.Request("backend.php",	{
			parameters: query,
			onSuccess: function(transport) {
				var ti = JSON.parse(transport.responseText);
				if (ti.status=="1") {
					Notify.info("Saved to Pocket:<br/><em>" + ti.title + "</em>");

					btn.src='plugins/oneclickpocket/pocket.png';
					btn.title='Saved to Pocket';
				} else {
					Notify.error("<strong>Error saving to Pocket!</strong><br/>("+ti.status+")");}
			} });

	} catch (e) {
		exception_error("PocketArticle", e);
	}
};


require(['dojo/_base/kernel', 'dojo/ready'], function (dojo, ready) {
	ready(function () {
		PluginHost.register(PluginHost.HOOK_INIT_COMPLETE, () => {
			App.hotkey_actions["pock_it"]  = function() {
				if (Article.getActive()) {
					postArticleToWallabag(Article.getActive());
					return;
				}
			};
		});
	});
});
