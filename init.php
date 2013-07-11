<?php
class oneclickpocket extends Plugin {
	private $host;

	function init($host) {
		$this->host = $host;

		$host->add_hook($host::HOOK_ARTICLE_BUTTON, $this);
		$host->add_hook($host::HOOK_PREFS_TAB, $this);
		
		$host->add_hook($host::HOOK_HOTKEY_MAP, $this);
		$host->add_hook($host::HOOK_HOTKEY_INFO, $this);
		
	}

	function about() {
		return array(0.32,
				"Add articles to Pocket with a single click",
				"fxneumann");
	}
	function save() {

		$pocket_consumer_key = db_escape_string($_POST["pocket_consumer_key"]);
		$this->host->set($this, "pocket_consumer_key", $pocket_consumer_key);
		
		$pocket_access_token = db_escape_string($_POST["pocket_access_token"]);
		$this->host->set($this, "pocket_access_token", $pocket_access_token);
				
		echo "Consumer Key set to<br/> <small>$pocket_consumer_key</small><br/>Access Token set to<br/> <small>$pocket_access_token</small>";
	}

        function api_version() {
                return 2;
        }

	function get_js() {
		return file_get_contents(dirname(__FILE__) . "/pocket.js");
	}

	function hook_article_button($line) {
		$article_id = $line["id"];

		$rv = "<img src=\"plugins/oneclickpocket/pocketgrey.png\"
			class=\"tagsPic\" id=\"ocp$article_id\" style=\"cursor : pointer\"
			onclick=\"shareArticleToPocket($article_id, this)\"
			title='".__('Save to Pocket')."'>";

		return $rv;
	}

	function getInfo() {
	    	
		//retrieve Data from the DB
		$id = db_escape_string($_REQUEST['id']);
		$result = db_query("SELECT title, link
				FROM ttrss_entries, ttrss_user_entries
				WHERE id = '$id' AND ref_id = id AND owner_uid = " .$_SESSION['uid']);
		if (db_num_rows($result) != 0) {
			$title = truncate_string(strip_tags(db_fetch_result($result, 0, 'title')),
					100, '...');
			$article_link = db_fetch_result($result, 0, 'link');
		}
		
		$consumer_key = $this->host->get($this, "pocket_consumer_key");
		$pocket_access_token = $this->host->get($this, "pocket_access_token");
		
		
		//Call Pocket API
		
		if (function_exists('curl_init')) {
 		 $postfields = array(
		 	'consumer_key' => $consumer_key,
		 	'access_token' => $pocket_access_token,
			'url'          => $article_link,
			'title'        => $title
			);
		 $cURL = curl_init();
		 curl_setopt($cURL, CURLOPT_URL, 'https://getpocket.com/v3/add');
		 curl_setopt($cURL, CURLOPT_HEADER, 1);
		 curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'));
		 curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
		 curl_setopt($cURL, CURLOPT_TIMEOUT, 5);
		 curl_setopt($cURL, CURLOPT_POST, 4);
		 curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($postfields));
		 $apicall = curl_exec($cURL);
		 curl_close($cURL);
		 
		 //Store error code in $status
		 $status = preg_match('/^X-Error: .*$/m', $apicall, $matches) ? $matches[0] : 1;
		} else {
		 $status = 'For the plugin to work you need to <strong>enable PHP extension CURL</strong>!';
		}
		//Return information on article and status
		print json_encode(array(
			"title" => $title,
			"link" => $article_link,
			"id" => $id,
			"status" => $status
			));		
	}

	function hook_prefs_tab($args) {
	    //Add preferences pane
		if ($args != "prefPrefs") return;

		print "<div dojoType=\"dijit.layout.AccordionPane\" title=\"".__("Pocket")."\">";

		print "<br/>";

        $pocket_consumer_key = $this->host->get($this, "pocket_consumer_key");
		$pocket_access_token = $this->host->get($this, "pocket_access_token");
		
		print "<form dojoType=\"dijit.form.Form\">";

		print "<script type=\"dojo/method\" event=\"onSubmit\" args=\"evt\">
			evt.preventDefault();
		if (this.validate()) {
			console.log(dojo.objectToQuery(this.getValues()));
			new Ajax.Request('backend.php', {
parameters: dojo.objectToQuery(this.getValues()),
onComplete: function(transport) {
notify_info(transport.responseText);
}
});
}
</script>";

print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"op\" value=\"pluginhandler\">";
print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"method\" value=\"save\">";
print "<input dojoType=\"dijit.form.TextBox\" style=\"display : none\" name=\"plugin\" value=\"oneclickpocket\">";
print "<table width=\"100%\" class=\"prefPrefsList\">";

if (!function_exists('curl_init')) {
 print '<tr><td colspan="3" style="color:red;font-size:large">For the plugin to work you need to <strong>enable PHP extension CURL</strong>!</td></tr>';
}

    print "<tr><td width=\"20%\">".__("Pocket Consumer Key")."</td>";
	print '<td width=\"20%\">Get a <a href="http://getpocket.com/developer/apps/new">Pocket Consumer Key</a></td>';
	print "<td class=\"prefValue\"><input dojoType=\"dijit.form.ValidationTextBox\" required=\"1\" name=\"pocket_consumer_key\" value=\"$pocket_consumer_key\"></td>";
	print "<tr><td width=\"20%\">".__("Pocket Access Token")."</td>";
	print "<td width=\"20%\"><a href=\"plugins/oneclickpocket/auth.php?consumer_key=$pocket_consumer_key\">Generate Access Token</a></td>";
	print "<td class=\"prefValue\"><input dojoType=\"dijit.form.ValidationTextBox\" required=\"1\" name=\"pocket_access_token\" value=\"$pocket_access_token\"></td></tr>";
	print "</table>";
	print "<p><button dojoType=\"dijit.form.Button\" type=\"submit\">".__("Save")."</button>";

	print "</form>";

	print "</div>"; #pane

	}
	
	function hook_hotkey_map($hotkeys) {
        // Use the new target "pock_it" to define your own 
        // hotkey to this function in other plugins.
        $hotkeys['i'] = 'pock_it';

        return $hotkeys;
    }
	
	function hook_hotkey_info($hotkeys) {
        
        $offset = 1 + array_search('open_in_new_window', array_keys($hotkeys[__('Article')]));
        $hotkeys[__('Article')] =
            array_slice($hotkeys[__('Article')], 0, $offset, true) +
            array('pock_it' => __('Save to Pocket')) +
            array_slice($hotkeys[__('Article')], $offset, NULL, true);

        return $hotkeys;
    }

}


?>
