<?php
/**
 * Template to Process Login Request.
 *
 * @since 1.0.0
 */

if ( ! isset( $_GET['astoundify_simple_social_login'] ) ) {
	return false;
}

do_action( 'astoundify_simple_social_login_process_' . $_GET['astoundify_simple_social_login'] );


/* ====================================================== */

/**
 * @link https://stackoverflow.com/questions/32029116
 *
 * Session is required for FB API to work. So, start if not yet initiated.
 */
if( ! session_id() ) {
	session_start();
}

/**
 * API Config
 */
$config = array(
	'app_id'                => astoundify_simple_social_login_facebook_get_app_id(),
	'app_secret'            => astoundify_simple_social_login_facebook_get_app_secret(),
	'default_graph_version' => 'v2.8',
);

// Start: Open Sesame!
$fb = new Facebook\Facebook( $config );

/**
 * See "woocommerce-social-login\lib\hybridauth\hybridauth\Hybrid\Providers\Facebook.php"
 */
$helper = $fb->getRedirectLoginHelper();

/**
 * @link https://stackoverflow.com/questions/32029116
 * Needed for : Facebook SDK returned an error: Cross-site request forgery validation failed. Required param "state" missing from persistent data.
 */
$_SESSION['FBRLH_state'] = $_GET['state'];

/**
 * Get Access Token.
 */
try {

	$access_token = $helper->getAccessToken();

} catch( Facebook\Exceptions\FacebookResponseException $e ) {

	// When Graph returns an error.
	echo 'Graph returned an error: ' . $e->getMessage();
	exit;

} catch(Facebook\Exceptions\FacebookSDKException $e) {

	// When validation fails or other local issues.
	echo 'Facebook SDK returned an error: ' . $e->getMessage();
	exit;

}

/**
 * Fail To Get Access Token. Bail. Set Error Page.
 */
if ( ! isset( $access_token ) ) {
	if ( $helper->getError() ) {
		header('HTTP/1.0 401 Unauthorized');
		echo "Error: " . $helper->getError() . "\n";
		echo "Error Code: " . $helper->getErrorCode() . "\n";
		echo "Error Reason: " . $helper->getErrorReason() . "\n";
		echo "Error Description: " . $helper->getErrorDescription() . "\n";
	} else {
		header('HTTP/1.0 400 Bad Request');
		echo 'Bad request';
	}
	exit;
}


/**
 * THE ACCESS TOKEN.
 */
echo '<h3>Access Token</h3>';
echo "<pre>";
var_dump( $access_token->getValue() );
echo "</pre>";

// The OAuth 2.0 client handler helps us manage access tokens.
$oauth2_client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$token_metadata = $oauth2_client->debugToken( $access_token );

/**
 * TOKEN META DATA
 */
echo '<h3>Token Metadata</h3>';
echo "<pre>";
var_dump( $token_metadata );
echo "</pre>";

// Validation (these will throw FacebookSDKException's when they fail).
$token_metadata->validateAppId( astoundify_simple_social_login_facebook_get_app_id() );

// If you know the user ID this access token belongs to, you can validate it here
//$token_metadata->validateUserId('123');
$token_metadata->validateExpiration();

// Access token is short-lived.
if ( ! $access_token->isLongLived() ) {

	// Exchanges a short-lived access token for a long-lived one.
	try {
		$access_token = $oauth2_client->getLongLivedaccess_token( $access_token );
	} catch ( Facebook\Exceptions\FacebookSDKException $e ) {
		echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
		exit;
	}

}

/**
 * LONG LIVED ACCESS TOKEN
 */
echo '<h3>Long-lived</h3>';
echo "<pre>";
var_dump( $access_token->getValue() );
echo "</pre>";


//$_SESSION['fb_access_token'] = (string) $access_token;

// Set the token to FB App.
$fb->setDefaultAccessToken( $access_token->getValue() ) ;

/**
 * GET AND DISPLAY BASIC USER DATA.
 */
echo "<h3>Your info:</h3>";

try {

	$profile_request = $fb->get('/me?fields=name,first_name,last_name,email');
	$profile = $profile_request->getGraphUser();

	$id = $profile->getProperty('id');
	$full_name = $profile->getProperty('name');
	$first_name = $profile->getProperty('first_name');
	$last_name = $profile->getProperty('last_name');
	$email = $profile->getProperty('email');

} catch( Facebook\Exceptions\FacebookResponseException $e ) {
	echo 'Graph returned an error: ' . $e->getMessage();
}
/**
 * @see "woocommerce-social-login\includes\hybridauth\class-sv-hybrid-providers-facebook.php"
 *
 * $this->user->profile->profileURL = (array_key_exists('link', $data)) ? $data['link'] : "";
 * $this->user->profile->webSiteURL = (array_key_exists('website', $data)) ? $data['website'] : "";
 * $this->user->profile->description = (array_key_exists('about', $data)) ? $data['about'] : "";
 *
 * Photo/Avatar:
 * $this->user->profile->photoURL = !empty($this->user->profile->identifier) ? "https://graph.facebook.com/" . $this->user->profile->identifier . "/picture?width=150&height=150" : '';
 */
?>
<ul>
	<li>FB ID: <?php echo ( isset( $id ) ? $id : 'Error' ) ?></li>
	<li>Full Name: <?php echo ( isset( $full_name ) ? $full_name : 'Error' ) ?></li>
	<li>First Name: <?php echo ( isset( $first_name ) ? $first_name : 'Error' ) ?></li>
	<li>Last Name: <?php echo ( isset( $last_name ) ? $last_name : 'Error' ) ?></li>
	<li>Email: <?php echo ( isset( $email ) ? $email : 'Error' ) ?></li>
</ul>


<p>END OF FILE.</p>
















