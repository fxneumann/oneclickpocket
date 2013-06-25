<!doctype html>
<html>
  <head>
    <title>Authenticate Pocket</title>
    <meta charset="utf-8" />
  </head>
  <body>

<?php

$self = (isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

function cURL($url, $post) {
	$cURL = curl_init();
	curl_setopt($cURL, CURLOPT_URL, $url);
	curl_setopt($cURL, CURLOPT_HEADER, 0);
	curl_setopt($cURL, CURLOPT_HTTPHEADER, array('Content-type: application/x-www-form-urlencoded;charset=UTF-8'));
	curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cURL, CURLOPT_TIMEOUT, 5);
	curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($cURL, CURLOPT_POST, count($post));
	curl_setopt($cURL, CURLOPT_POSTFIELDS, http_build_query($post));
	$output = curl_exec($cURL);
	curl_close($cURL);

	return $output;
}



if (isset($_GET['consumer_key'])) {
 $consumer_key = $_GET['consumer_key'];

if (isset ($_GET["token"])) {
// Numbers in comments represent the steps as described at http://getpocket.com/developer/docs/authentication
// (4) Receive the callback from Pocket
// (5) Convert a request token into a Pocket access token

$oAuthRequest = cURL(
   'https://getpocket.com/v3/oauth/authorize',
   array(
    'consumer_key' => $consumer_key, 
    'code' => $_GET["token"]
   )
 );
 
 $access_token = explode('&', $oAuthRequest);
 $access_token = $access_token[0];
 $access_token = explode('=', $access_token);
 $access_token = $access_token[1];
 
} else {
 // (2) Obtain request token
 $oAuthRequestToken = explode('=', cURL(
   'https://getpocket.com/v3/oauth/request',
   array(
  	 'consumer_key' => $consumer_key,
  	 'redirect_uri' => $self."?consumer_key=$consumer_key"
   )
 ));

 // (3) Redirect user to Pocket to continue authorization
 echo '<meta http-equiv="refresh" content="0;url=' . 'https://getpocket.com/auth/authorize?request_token=' . urlencode($oAuthRequestToken[1]) . '&redirect_uri=' . urlencode($self."?consumer_key=$consumer_key" . '&token=' . $oAuthRequestToken[1]) . '" />';
 }

echo '<h1>Authenticate Pocket</h1>';
echo '<form><table><tr>';
echo '<td><label for="consumer_key">Consumer key</label></td>';
echo '<td><input type="text" id="consumer_key" name="consumer_key" value="' . $consumer_key . '"></td></tr>';

if (isset($access_token)){
echo '<td><label for="access_token">Access token</label></td>';
echo '<td><input type="text" id="access_token" name="access_token" value="' . $access_token . '"></td></tr>';
}
echo '</table></form>';

} else {
echo '<h1>Authenticate pocket</h1>';
echo '<form><table><tr>';
echo '<td><label for="consumer_key">Consumer key</label></td>';
echo '<td><input type="text" id="consumer_key" name="consumer_key" value=""></td></tr>';
echo '</table><input type="submit" value="Absenden"></form>';
}
?>

</body>
</html>
</html>