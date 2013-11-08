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

require_once 'sociallogin/openid.php';
require_once 'sociallogin/src/Google_Client.php';
require_once 'sociallogin/src/contrib/Google_Oauth2Service.php';

class Apptha_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    /* Customer save action */
public function customerAction($firstname, $lastname, $email,$image_name, $provider, $provider_user_id) {
        $customer = Mage::getModel('customer/customer');
        $magentotime      = Mage::getModel('core/date')->timestamp(time());
        $now 	  = date('Y-m-d H:i:s' . ' 00:00:00',$magentotime);        
     
        /* getting customer collection */
        $collection = $customer->getCollection();
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            $collection->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId());
        }
        if ($this->_getCustomerSession()->isLoggedIn()) {
            $collection->addFieldToFilter('entity_id', array('neq' => $this->_getCustomerSession()->getCustomerId()));
        }
        if ($provider == 'Facebook') {
            $provider_db = "facebook_uid";
        } else if ($provider == 'Google') {
            $provider_db = "google_uid";
        } else if ($provider == 'Yahoo') {
            $provider_db = "yahoo_uid";
        } else if ($provider == 'Linkedin') {
            $provider_db = "linkedin_uid";
        } else if ($provider == 'Twitter') {
            $provider_db = "twitter_uid";
        }
       // $customers = Mage::getModel('customer/customer')->getCollection()->addFieldToFilter("$provider_db", "$provider_user_id")->load();
        /* If user not registered */
        foreach ($customers as $customerUid) {
            $customer_id_by_provider = $customerUid->getId();
            $customer_email_by_provider = $customerUid->getEmail();
        }
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);

        $customer_id_by_email = $customer->getId(); 

        if ($customer_id_by_email == '') {
            $standardInfo['email'] = $email;
        } else {
            $standardInfo['email'] = $email;
        }

        /* getting customer params */

        $standardInfo['first_name'] = $firstname;
        $standardInfo['last_name'] = $lastname;
        $standardInfo['provider_id'] = $provider_user_id;               
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
        ->loadByEmail($standardInfo['email']);
        
        if ($customer->getId()) {

            /* Save the provider user id */
            if ($provider_db == 'google_uid') {
                
                $customer->setGoogleUid($provider_user_id);
                $customer->save();
                
                $googleimage = $image_name; 
                $read = Mage::getSingleton('core/resource')->getConnection('core_read');
               
               $query = "SELECT * FROM " . Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar') ." where value='$provider_user_id'";
                $results = $read->fetchAll($query);                               
                foreach ($results as $res){
                        $ent_id = $res['entity_id'];                        
                }                  
                            
                
            } 
            
            
            
            
            else if ($provider_db == 'yahoo_uid') {
                $customer->setYahooUid($provider_user_id);
                $customer->save();
            } else if ($provider_db == 'facebook_uid') {
                $customer->setFacebookUid($provider_user_id);
                $customer->save();
                 //getting fb profile image name
                $fbimage = $image_name; 
                $read = Mage::getSingleton('core/resource')->getConnection('core_read');
               
               $query = "SELECT * FROM " . Mage::getSingleton('core/resource')->getTableName('customer_entity_varchar') ." where value='$provider_user_id'";
                $results = $read->fetchAll($query);                               
                foreach ($results as $res){
                        $ent_id = $res['entity_id'];                        
                }                  
                                                                
            } else if ($provider_db == 'twitter_uid') {
                $customer->setTwitterUid($provider_user_id);
                $customer->save();
            } else if ($provider_db == 'linkedin_uid') {
                $customer->setLinkedinUid($provider_user_id);
                $customer->save();
            }
            $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
            $this->_getCustomerSession()->addSuccess(
                    $this->__('Your account has been successfully connected through' . ' ' . $provider)
            );
            $link = Mage::getSingleton('customer/session')->getLink();
            if (!empty($link)) {
                $requestPath = trim($link, '/');
            }
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect($requestPath);
                return;
            } else {
                $redirect = $this->_loginPostRedirect();
                //$this->_redirectUrl($redirect);
                $this->_redirectUrl(Mage::getSingleton('core/session')->getReLink());
                return;
            }
        }
        /* Generate Random Password */
        $randomPassword = $customer->generatePassword(8);
        $customer->setId(null)
                ->setSkipConfirmationIfEmail($standardInfo['email'])
                ->setFirstname($standardInfo['first_name'])
                ->setLastname($standardInfo['last_name'])
                ->setEmail($standardInfo['email'])
                ->setPassword($randomPassword)
                ->setConfirmation($randomPassword);
        if ($provider_db == 'google_uid') {
            $customer->setGoogleUid($provider_user_id);
            $customer->setYahooUid('');
            $customer->setFacebookUid('');
            $customer->setTwitterUid('');
            $customer->setLinkedinUid('');
        } else if ($provider_db == 'yahoo_uid') {
            $customer->setGoogleUid('');
            $customer->setYahooUid($provider_user_id);
            $customer->setFacebookUid('');
            $customer->setTwitterUid('');
            $customer->setLinkedinUid('');
        } else if ($provider_db == 'facebook_uid') {                                       
            $customer->setFacebookUid($provider_user_id);           
        } else if ($provider_db == 'twitter_uid') {
            $customer->setTwitterUid($provider_user_id);
        } else if ($provider_db == 'linkedin_uid') {
            $customer->setLinkedinUid($provider_user_id);
            $customer->setTwitterUid('');
            $customer->setFacebookUid('');
            $customer->setGoogleUid('');
            $customer->setYahooUid('');
        }


        if ($this->getRequest()->getParam('is_subscribed', false)) {
            $customer->setIsSubscribed(1);
        }
        /* registration will fail if tax required, also if dob, gender aren't allowed in profile */
        $errors = array();
        $validationCustomer = $customer->validate();
        if (is_array($validationCustomer)) {
            $errors = array_merge($validationCustomer, $errors);
        }
        $validationResult = true;

        if (true === $validationResult) {
            $customer->save();

            $this->_getCustomerSession()->addSuccess(
                    $this->__('Thank you for registering with %s', Mage::app()->getStore()->getFrontendName()) .
                    '. ' .
                    $this->__('You will receive welcome email with registration info in a moment.')
            );
            //if not change password or click here forget password

            $customer->sendNewAccountEmail();

            $this->_getCustomerSession()->setCustomerAsLoggedIn($customer);
            $link = Mage::getSingleton('customer/session')->getLink();
            if (!empty($link)) {

                $requestPath = trim($link, '/');
            }
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect($requestPath);
                return;
            } else {
                $redirect = $this->_loginPostRedirect();
               // $this->_redirectUrl($redirect);
                 $this->_redirectUrl(Mage::getSingleton('core/session')->getReLink());
                return;
            }
            //else set form data and redirect to registration
        } else {
            $this->_getCustomerSession()->setCustomerFormData($customer->getData());
            $this->_getCustomerSession()->addError($this->__('User profile can\'t provide all required info, please register and then connect with Apptha Social login.'));
            if (is_array($errors)) {
                foreach ($errors as $errorMessage) {
                    $this->_getCustomerSession()->addError($errorMessage);
                }
            }
            $this->_redirect('customer/account/create');
        }
    }

    /* function to get customer session */

    private function _getCustomerSession() {
        return Mage::getSingleton('customer/session');
    }

    /* function to redirect my account dashboard page */

    protected function _loginPostRedirect() {
        $session = $this->_getCustomerSession();

        if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {

            // Set default URL to redirect customer to
            $session->setBeforeAuthUrl(Mage::helper('customer')->getAccountUrl());

            // Redirect customer to the last page visited after logging in
            if ($session->isLoggedIn()) {
                if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) {
                    if ($referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME)) {
                        $referer = Mage::helper('core')->urlDecode($referer);
                        if ($this->_isUrlInternal($referer)) {
                            $session->setBeforeAuthUrl($referer);
                        }
                    }
                } else if ($session->getAfterAuthUrl()) {
                    $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
                }
            } else {
                $session->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
            }
        } else if ($session->getBeforeAuthUrl() == Mage::helper('customer')->getLogoutUrl()) {
            $session->setBeforeAuthUrl(Mage::helper('customer')->getDashboardUrl());
        } else {
            if (!$session->getAfterAuthUrl()) {
                $session->setAfterAuthUrl($session->getBeforeAuthUrl());
            }
            if ($session->isLoggedIn()) {
                $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
            }
        }

        return $session->getBeforeAuthUrl(true);
    }

    /* function for twitter login */

    

    /* function to save twitter email */
 public function twitterloginAction() {
       // if (!class_exists('TwitterOAuth')) {
            require'sociallogin/twitter/twitteroauth.php';
            require 'sociallogin/config/twconfig.php';
       // }
        //get twitter consumer key and secret
        $tw_oauth_token = Mage::getSingleton('customer/session')->getTwToken();
        $tw_oauth_token_secret = Mage::getSingleton('customer/session')->getTwSecret();
        $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $tw_oauth_token, $tw_oauth_token_secret);
        //request the access token
        $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
        //getting twitter user details
        $user_info = $twitteroauth->get('account/verify_credentials');
        if (isset($user_info->error)) {
            Mage::getSingleton('customer/session')->addError($this->__('Twitter Login connection failed'));
            $url = Mage::helper('customer')->getAccountUrl();
            return $this->_redirectUrl($url);
        } else {

            $firstname = $user_info->name;
            $twitter_id = $user_info->id;
            $email = Mage::getSingleton('customer/session')->getTwemail();
            $lastname = $user_info->name;

            if ($email == '' || $firstname == '') {
                //error message
                Mage::getSingleton('customer/session')->addError($this->__('Twitter Login connection failed'));
                $url = Mage::helper('customer')->getAccountUrl();
                return $this->_redirectUrl($url);
            } else {
                $this->customerAction($firstname, $lastname, $email, 'dfd','Twitter', $twitter_id);
            }
        }
    }
    public function twitterpostAction() {
        $provider = '';
        $twitter_email = (string) $this->getRequest()->getPost('email_value'); 
        Mage::getSingleton('customer/session')->setTwemail($twitter_email);
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($twitter_email);
        $customer_id_by_email = $customer->getId();
        $customer = Mage::getModel('customer/customer')->load($customer_id_by_email);
        $google_uid = $customer->getGoogleUid();
        if ($google_uid != '') {
            $provider.=' Google';
        }

        $facebook_uid = $customer->getFacebookUid();
        if ($facebook_uid != '') {
            $provider.=', Facebook';
        }
        $linkedin_uid = $customer->getLinkedinUid();
        if ($linkedin_uid != '') {
            $provider.=', Linkedin';
        }
        $yahoo_uid = $customer->getYahooUid();
        if ($yahoo_uid != '') {
            $provider.=', Yahoo';
        }
        $twitter_uid = $customer->getTwitterUid();
        $provider = ltrim($provider, ',');
        
       if($customer_id_by_email == '')
        {
            echo $url = Mage::helper('sociallogin')->getTwitterUrl();
           
           
        }
        else if($provider!='')
        {
            echo $url = Mage::helper('sociallogin')->getTwitterUrl();
            //echo $this->__('This email is already associated with') . $provider;
           
    
        }
        else if(($provider=='' )&& ( $twitter_uid!=''))
        {
              echo $url = Mage::helper('sociallogin')->getTwitterUrl();
              
             
        }
        else 
        {
            echo $url = Mage::helper('sociallogin')->getTwitterUrl();
             //echo $this->__('This email is already registered!');
             
        }exit;
    }

    /* function for facebook login */

   public function fbloginAction() {
       // if(!class_exists('Facebook')){
            require 'sociallogin/facebook/facebook.php';
       // }
        require 'sociallogin/config/fbconfig.php';
        //create facebook object
        $facebook = new FacebookApptha(array(
                    'appId' => APP_ID,
                    'secret' => APP_SECRET,
                    'cookie' => false,
                ));
        //getting facebook user details
       $user = $facebook->getUser();
                
        if ($user) {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                $user_profile = $facebook->api('/me');               
                $firstname = $user_profile['first_name'];
                $email = $user_profile['email'];               
                $lastname = $user_profile['last_name'];
                $facebook_user_id = $user_profile['id'];
                
               $emailpart = explode("@", $email);
               $fbimage = $emailpart[0];   
               
                $url = "http://graph.facebook.com/$user/picture?type=large";
                                                
                $fb_image = file_get_contents($url);     
                $fb_image_name = $fbimage.'-'.$user.'.jpg';
                $fileName =Mage::getBaseDir("media") . "/catalog/customer/resz_".$fbimage.'-'.$user.'.jpg';                 
                $file = fopen($fileName, 'w+');
                fputs($file, $fb_image);
                fclose($file);
                
                $large_url = "http://graph.facebook.com/$user/picture?height=9999&width=9999";
                $fb_large_image = file_get_contents($large_url);     
               
                $largefileName =Mage::getBaseDir("media") . "/catalog/customer/".$fbimage.'-'.$user.'.jpg';                 
                $largefile = fopen($largefileName, 'w+');
                fputs($largefile, $fb_large_image);
                fclose($largefile);
                
                $thumb_url = "http://graph.facebook.com/$user/picture?height=75&width=100";
                $fb_thumb_image = file_get_contents($thumb_url);     
               
                $thumbfileName =Mage::getBaseDir("media") . "/catalog/customer/thumbs/".$fbimage.'-'.$user.'.jpg';                 
                $thumbfile = fopen($thumbfileName, 'w+');
                fputs($thumbfile, $fb_thumb_image);
                fclose($thumbfile);                                                                                                                           
                if ($email == '') {
                    //error message
                    Mage::getSingleton('customer/session')->addError($this->__('Facebook Login connection failed'));
                    $url = Mage::helper('customer')->getAccountUrl();
                    return $this->_redirectUrl($url);
                } else {
                    $this->customerAction($firstname, $lastname, $email,$fb_image_name, 'Facebook', $facebook_user_id);
                }
            } catch (FacebookApiException $e) {
                error_log($e);
                $user = null;
            }
        } 
    }

    /* function for Google login */

    public function googlepostAction() {
       
        ########## Google Settings.. Client ID, Client Secret #############
    require_once 'sociallogin/src/Google_Client.php';
require_once 'sociallogin/src/contrib/Google_Oauth2Service.php';
$google_client_id = Mage::getStoreConfig('sociallogin/general/google_id');
$google_client_secret =  Mage::getStoreConfig('sociallogin/general/google_secret');       
$google_developer_key = Mage::getStoreConfig('sociallogin/general/google_develop');       
$google_redirect_url 	= Mage::getUrl().'sociallogin/index/googlepost/';
$gClient = new Google_Client();
$gClient->setApplicationName('login');
$gClient->setClientId($google_client_id);
$gClient->setClientSecret($google_client_secret);
$gClient->setRedirectUri($google_redirect_url);
$gClient->setDeveloperKey($google_developer_key);
$google_oauthV2 = new Google_Oauth2Service($gClient);

if (isset($_REQUEST['reset'])) 
{
  unset($_SESSION['token']);
  $gClient->revokeToken();
  header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
}
if (isset($_GET['code']))
{

    $gClient->authenticate($_GET['code']);
    $_SESSION['token'] = $gClient->getAccessToken();
     
    header('Location: ' . filter_var($google_redirect_url, FILTER_SANITIZE_URL));
     $this->_redirectUrl($google_redirect_url);
    return;
}
if (isset($_SESSION['token'])) 
{ 
		$gClient->setAccessToken($_SESSION['token']);
}
if ($gClient->getAccessToken()) 
{
	  //Get user details if user is logged in
	  $user 				= $google_oauthV2->userinfo->get();
	  $user_id 				= $user['id'];
	  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	  $_SESSION['token'] 	= $gClient->getAccessToken();
          
          $googleimg_url= "https://plus.google.com/s2/photos/profile/$user_id?sz=100" ;
          
           $emailpart = explode("@", $email);
           $googleimage = $emailpart[0];   
         
           $google_image = file_get_contents($googleimg_url);     
                 $google_image_name = $googleimage.'-'.$user_id.'.jpg';
                 $fileName =Mage::getBaseDir("media") . "/catalog/customer/resz_".$googleimage.'-'.$user_id.'.jpg';                 
                $file = fopen($fileName, 'w+');
                fputs($file, $google_image);
                fclose($file);
                
                $googlelarge_url = "https://plus.google.com/s2/photos/profile/$user_id?height=9999&width=9999";
                $google_large_image = file_get_contents($googlelarge_url);     
               
                $largeimgName =Mage::getBaseDir("media") . "/catalog/customer/".$googleimage.'-'.$user_id.'.jpg';                 
                $largeimg = fopen($largeimgName, 'w+');
                fputs($largeimg, $google_large_image);
                fclose($largeimg);
                
                $googlethumb_url = "https://plus.google.com/s2/photos/profile/$user_id?height=75&width=100";
                $google_thumb_image = file_get_contents($googlethumb_url);     
               
                $thumbimgName =  Mage::getBaseDir("media") . "/catalog/customer/thumbs/".$googleimage.'-'.$user_id.'.jpg';                 
                $thumbimg = fopen($thumbimgName, 'w+');
                fputs($thumbimg, $google_thumb_image);
                fclose($thumbimg);           
          
}
else 
{
	//get google login url
	$authUrl = $gClient->createAuthUrl();
}

if(isset($authUrl)) //user is not logged in, show login button
{
    
     $this->_redirectUrl($authUrl);
	  //echo $authUrl;
} else // user logged in 
{
   /* connect to mysql */
    
  
     $firstname = $user['given_name'];
     $lastname = $user['family_name'];
    
    $email = $user['email'];
    $google_user_id = $user['id'];
    
   if ($email == '') {
                //error message
                Mage::getSingleton('customer/session')->addError($this->__('Google Login connection failed'));
                $url = Mage::helper('customer')->getAccountUrl();
                return $this->_redirectUrl($url);
            } else {
                
                $this->customerAction($firstname, $lastname, $email,$google_image_name, 'Google',$google_user_id);
            }
    
}

    }

    /* function for Yahoo login */

    public function yahoopostAction() {
        require 'sociallogin/lib/Yahoo.inc';
YahooLogger::setDebug(true);
YahooLogger::setDebugDestination('LOG');
$yahoo_client_id = Mage::getStoreConfig('sociallogin/general/yahoo_id');
$yahoo_client_secret =  Mage::getStoreConfig('sociallogin/general/yahoo_secret');       
$yahoo_developer_key = Mage::getStoreConfig('sociallogin/general/yahoo_develop');  
$yahoo_domain = Mage::getUrl();
// use memcache to store oauth credentials via php native sessions

// Make sure you obtain application keys before continuing by visiting:
// https://developer.yahoo.com/dashboard/createKey.html
define('OAUTH_CONSUMER_KEY', "$yahoo_client_id");
define('OAUTH_CONSUMER_SECRET', "$yahoo_client_secret");
define('OAUTH_DOMAIN', "$yahoo_domain");
define('OAUTH_APP_ID', "$yahoo_developer_key");
       if(array_key_exists("logout", $_GET)) {
  // if a session exists and the logout flag is detected
  // clear the session tokens and reload the page.
  YahooSession::clearSession();
  header("Location: index.php");
}
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
       $this->_redirectUrl($auth_url);
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
  
   $yahoo_user_id =  $profile->guid;
 
   
   
            
             $email = $profile->emails[0]->handle;
             $firstname = $profile->givenName;
          
             $lastname = $profile->familyName;
           

            if ($email == '') {
                //error message
                Mage::getSingleton('customer/session')->addError($this->__('Yahoo Login connection failed'));
                $url = Mage::helper('customer')->getAccountUrl();
                return $this->_redirectUrl($url);
            } else {
                $this->customerAction($firstname, $lastname, $email,$email, 'Yahoo', $yahoo_user_id);
  
  }
  }
    }
    }

  
    /* Redirect to index page if login page */

    public function loginAction() {

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        } else if (Mage::getStoreConfig('sociallogin/general/enable_sociallogin') == 1) {
            $this->_redirect();
            return;
        }
        $this->getResponse()->setHeader('Login-Required', 'true');
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }

    /* Redirect to index page if register page */

    public function createAction() {

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        } else {
             $enable_status = Mage::getStoreConfig('sociallogin/general/enable_sociallogin');
            if($enable_status == 1)
            {
            $this->_redirect();
            return;
            }
        }

        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->renderLayout();
    }

    public function _isVatValidationEnabled($store = null) {
        return Mage::helper('customer/address')->isVatValidationEnabled($store);
    }

    /* Welcome messsage for the registered Customers */

    public function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getCustomerSession()->addSuccess(
                $this->__('Thank you for registering with %s.', Mage::app()->getStore()->getFrontendName())
        );


      /*  if ($this->_isVatValidationEnabled()) {
            // Show corresponding VAT message to customer
            $configAddressType = Mage::helper('customer/address')->getTaxCalculationAddressType();
            $userPrompt = '';
            switch ($configAddressType) {
                case Mage_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you shipping address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
                    break;
                default:
                    $userPrompt = $this->__('If you are a registered VAT customer, please click <a href="%s">here</a> to enter you billing address for proper VAT calculation', Mage::getUrl('customer/address/edit'));
            }
            $this->_getCustomerSession()->addSuccess($userPrompt);
        }*/

        $customer->sendNewAccountEmail(
                $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app()->getStore()->getId()
        );

        $successUrl = Mage::getUrl('customer/account', array('_secure' => true));

        if ($this->_getCustomerSession()->getBeforeAuthUrl()) {
            $successUrl = $this->_getCustomerSession()->getBeforeAuthUrl(true);
        }
        return $successUrl;
    }

    /* customer login action if entered using default login form */

    public function customerloginpostAction() {
        $session = $this->_getCustomerSession();
        //get customer credentials 
        $login['username'] = $this->getRequest()->getPost('email_value');
        $login['password'] = $this->getRequest()->getPost('password_value');
        //customer login
        if ($session->isLoggedIn()) {
            echo 'Already loggedin';
            return;
        }
        if ($this->getRequest()->isPost()) {
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $session->login($login['username'], $login['password']);
                    if ($session->getCustomer()->getIsJustConfirmed()) {
                        echo $this->_welcomeCustomer($session->getCustomer(), true);
                    }
                } catch (Mage_Core_Exception $e) {
                    switch ($e->getCode()) {
                        case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                            $value = Mage::helper('customer')->getEmailConfirmationUrl($login['username']);
                            echo $message = Mage::helper('customer')->__('Account Not Confirmed', $value);
                            break;
                        case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                            echo $message = $this->__('Invalid Email Address or Password');
                            break;
                        default:
                            echo $message = $e->getMessage();
                    }
                    $session->setUsername($login['username']);
                } catch (Exception $e) {
                    //this may sometimes disclose the password
                }
                //after logging in redirect to the respective page    
                if ($session->getCustomer()->getId()) {
                    $link = Mage::getSingleton('customer/session')->getLink();

                    if (!empty($link)) {

                        $requestPath = trim($link, '/');
                    }
                    if ($requestPath == 'checkout/onestep') {
                        echo $requestPath;
                    } else {
                        //echo $this->_loginPostRedirect();
                        echo Mage::getSingleton('core/session')->getReLink();
                    }
                }
            }
        }
    }

    /* customer register action if entered using default register form */

    public function createPostAction() {
        $customer = Mage::getModel('customer/customer');
        $session = $this->_getCustomerSession();
        if ($session->isLoggedIn()) {
            $this->_redirect('*/*/');
            return;
        }
        $session->setEscapeMessages(true); // prevent XSS injection in user input
        if ($this->getRequest()->isPost()) {
            $errors = array();

            if (!$customer = Mage::registry('current_customer')) {
                $customer = Mage::getModel('customer/customer')->setId(null);
            }

            /* @var $customerForm Mage_Customer_Model_Form */
            $customerForm = Mage::getModel('customer/form');
            $customerForm->setFormCode('customer_account_create')
                    ->setEntity($customer);

            $customerData = $customerForm->extractData($this->getRequest());
            if ($this->getRequest()->getParam('is_subscribed', false)) {
                $customer->setIsSubscribed(1);
            }

            /**
             * Initialize customer group id
             */
            $customer->getGroupId();

            if ($this->getRequest()->getPost('create_address')) {
                /* @var $address Mage_Customer_Model_Address */
                $address = Mage::getModel('customer/address');
                /* @var $addressForm Mage_Customer_Model_Form */
                $addressForm = Mage::getModel('customer/form');
                $addressForm->setFormCode('customer_register_address')
                        ->setEntity($address);

                $addressData = $addressForm->extractData($this->getRequest(), 'address', false);
                $addressErrors = $addressForm->validateData($addressData);
                if ($addressErrors === true) {
                    $address->setId(null)
                            ->setIsDefaultBilling($this->getRequest()->getParam('default_billing', false))
                            ->setIsDefaultShipping($this->getRequest()->getParam('default_shipping', false));
                    $addressForm->compactData($addressData);
                    $customer->addAddress($address);

                    $addressErrors = $address->validate();
                    if (is_array($addressErrors)) {
                        $errors = array_merge($errors, $addressErrors);
                    }
                } else {
                    $errors = array_merge($errors, $addressErrors);
                }
            }

            try { 
                $customerErrors = $customerForm->validateData($customerData);

                if ($customerErrors !== true) { 
                    $errors = array_merge($customerErrors, $errors);
                } else { 
                    $customerForm->compactData($customerData);
                    $customer->setDate($this->getRequest()->getPost('dob'));
                    $customer->setPassword($this->getRequest()->getPost('password'));
                    $customer->setConfirmation($this->getRequest()->getPost('confirmation'));
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($customerErrors, $errors);
                    }
                }

                $validationResult = count($errors) == 0;

                if (true === $validationResult) { 
                    $customer->save();

                    Mage::dispatchEvent('customer_register_success', array('account_controller' => $this, 'customer' => $customer)
                    );

                    if ($customer->isConfirmationRequired()) { 
                        $customer->sendNewAccountEmail(
                                'confirmation', $session->getBeforeAuthUrl(), Mage::app()->getStore()->getId()
                        );
                        $session->addSuccess($this->__('Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper('customer')->getEmailConfirmationUrl($customer->getEmail())));
                        echo Mage::getUrl('/index', array('_secure' => true));
                        return;
                    } else { 
                        $session->setCustomerAsLoggedIn($customer);                              
                        echo $url = $this->_welcomeCustomer($customer);                   
                        return;
                    }
                } else { 
                    $session->setCustomerFormData($this->getRequest()->getPost());
                    if (is_array($errors)) {
                        foreach ($errors as $errorMessage) {
                            $session->$errorMessage;
                        }
                        echo $errorMessage;
                        return;
                    } else {
                        $session->addError($this->__('Invalid customer data'));
                    }
                }
            } catch (Mage_Core_Exception $e) { 
                $session->setCustomerFormData($this->getRequest()->getPost());
                if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    echo $message = $this->__('Already exists');
                    $session->setEscapeMessages(false);
                    return;
                } else {
                    echo $message = $e->getMessage();
                    return;
                }
                $session->addError($message);
            } catch (Exception $e) {

                $session->setCustomerFormData($this->getRequest()->getPost())
                        ->addException($e, $this->__('Cannot save the customer.'));
            }
        }

        echo Mage::getUrl('*/index', array('_secure' => true));
    }

    /* ForgetPassword action */

    public function forgotPasswordPostAction() {
        $email = (string) $this->getRequest()->getPost('email_value');
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
        if ($customer->getId()) {
            try {
                $newResetPasswordLinkToken = Mage::helper('customer')->generateResetPasswordLinkToken();
                $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                $customer->sendPasswordResetConfirmationEmail();
            } catch (Exception $exception) {
                $this->_getCustomerSession()->addError($exception->getMessage());
                return;
            }
        }
        echo 'sent';
        return;
    }

}