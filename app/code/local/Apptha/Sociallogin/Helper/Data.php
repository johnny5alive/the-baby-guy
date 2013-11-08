<?php

/**
 * @name         :  Apptha One Step Checkout
 * @version      :  1.0
 * @since        :  Magento 1.5
 * @author       :  Prabhu Mano
 * @copyright    :  Copyright (C) 2011 Powered by Apptha
 * @license      :  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @Creation Date:  July 26 2012
 *
 * */
?>
<?php



class Apptha_Sociallogin_Helper_Data extends Mage_Core_Helper_Abstract {
    /* function to get twitter aunthenticate url */

    public function getTwitterUrl() {
        require'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';
        //$twitter_consumer_key = Mage::getStoreConfig('sociallogin/general/tw_key');
 //$twitter_consumer_secret = Mage::getStoreConfig('sociallogin/general/tw_secret');
        
        
      $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET);
 
// Requesting authentication tokens, the parameter is the URL we will be redirected to
        $request_token = $twitteroauth->getRequestToken(Mage::getBaseUrl() . 'sociallogin/index/twitterlogin');
       
        if ($twitteroauth->http_code == 200) {
            $tw_oauth_token = Mage::getSingleton('customer/session')->setTwToken($request_token['oauth_token']);
            $tw_oauth_token_secret = Mage::getSingleton('customer/session')->setTwSecret($request_token['oauth_token_secret']);
            return $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
        }
    }

    /* function to get google aunthenticate url */

    public function getGoogleUrl() {
       
//        require_once 'sociallogin/src/Google_Client.php';
//require_once 'sociallogin/src/contrib/Google_Oauth2Service.php';
//        
//$google_client_id 		= '589721790333-k7tnbqo3e62jstcgi1deksa7afonsh1n.apps.googleusercontent.com';
//$google_client_secret 	= '_Cid9zzx3d8FlrBFlGeNeg_b';
//$google_redirect_url 	= 'http://www.iseofirm.net/groupclone/magnetotest/magento/';
//$google_developer_key 	= 'AIzaSyBXkwrLtBQYn0U3qYknc8VNC3YyKcQIjdI';
//$gClient = new Google_Client();
//$gClient->setApplicationName('login');
//$gClient->setClientId($google_client_id);
//$gClient->setClientSecret($google_client_secret);
//$gClient->setRedirectUri($google_redirect_url);
//$gClient->setDeveloperKey($google_developer_key);
//$google_oauthV2 = new Google_Oauth2Service($gClient);
//
//if (isset($_REQUEST['reset'])) 
//{
//  unset($_SESSION['token']);
//  $gClient->revokeToken();
//  header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
//}
//if (isset($_GET['code']))
//{
//    $gClient->authenticate($_GET['code']);
//    $_SESSION['token'] = $gClient->getAccessToken();
//    
//    header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
//    return;
//}
//if (isset($_SESSION['token'])) 
//{ 
//		$gClient->setAccessToken($_SESSION['token']);
//}
//if ($gClient->getAccessToken()) 
//{
//	  //Get user details if user is logged in
//	  $user 				= $google_oauthV2->userinfo->get();
//	  $user_id 				= $user['id'];
//	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
//	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
//	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
//	  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
//	  $personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
//	  $_SESSION['token'] 	= $gClient->getAccessToken();
//}
//else 
//{
//	//get google login url
//	$authUrl = $gClient->createAuthUrl();
//}


   
    

    }

    /* function to get yahoo aunthenticate url */

    public function getYahooUrl() {
       
require 'sociallogin/lib/Yahoo.inc';
// debug settings
//error_reporting(E_ALL | E_NOTICE); # do not show notices as library is php4 compatable
//ini_set('display_errors', true);
YahooLogger::setDebug(true);
YahooLogger::setDebugDestination('LOG');
// use memcache to store oauth credentials via php native sessions

// Make sure you obtain application keys before continuing by visiting:
// https://developer.yahoo.com/dashboard/createKey.html
define('OAUTH_CONSUMER_KEY', 'dj0yJmk9bURERGlLREJDWjA4JmQ9WVdrOWVUVnZXVEJyTXpZbWNHbzlOemN3TmpFM01UWXkmcz1jb25zdW1lcnNlY3JldCZ4PTZm');
define('OAUTH_CONSUMER_SECRET', '7c7349e0bca2775841a1606db25bdf1f45eebc62');
define('OAUTH_DOMAIN', 'iseofirm.net');
define('OAUTH_APP_ID', 'y5oY0k36');
$hasSession = YahooSession::hasSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);
if($hasSession == FALSE) {
  // create the callback url,
  $callback = YahooUtil::current_url()."?in_popup";
$sessionStore = new NativeSessionStore();
  // pass the credentials to get an auth url.
  // this URL will be used for the pop-up.
$auth_url = YahooSession::createAuthorizationUrl(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $callback, $sessionStore);
 if($auth_url)
 {
     echo $auth_url;
 }
 
  
}
else {
  // pass the credentials to initiate a session
  $session = YahooSession::requireSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);
  // if the in_popup flag is detected,
  // the pop-up has loaded the callback_url and we can close this window.
  // if a session is initialized, fetch the user's profile information
  if($session) {
    // Get the currently sessioned user.
    $user = $session->getSessionedUser();
    // Load the profile for the current user.
    $profile = $user->getProfile();
    
   
  
  }
}
    }



    

}