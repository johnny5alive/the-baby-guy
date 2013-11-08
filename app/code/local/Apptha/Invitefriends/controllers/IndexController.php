<?php
class Apptha_Invitefriends_IndexController extends Mage_Core_Controller_Front_Action
{
    const EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH 	= 'invitefriends/email_settings/email_template';
    
    public function indexAction()
    {
        $tokenId = $this->getRequest()->getParam('token');
        Mage::getModel('core/cookie')->set('tokenid', $tokenId);
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {  // if not logged in
            Mage::getSingleton('customer/session')->setBeforeAuthUrl(Mage::getBaseurl() . "invitefriends/index");
            //redirect to login page
            $this->_redirectUrl(Mage::helper('customer')->getLoginUrl());
        }
                $this->loadLayout();
		$this->renderLayout();
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

     protected function getStringBetween($string, $startStr, $endStr)
    {
    	$startStrIndex = strpos($string,$startStr);
    	if($startStrIndex === false) return false;
    	$startStrIndex ++;
    	$endStrIndex = strpos($string,$endStr,$startStrIndex);
    	if($endStrIndex === false) return false;
    	return substr($string,$startStrIndex,$endStrIndex-$startStrIndex);
    }

    //email transaction for invite a friend
   	public function _sendEmailTransaction($emailto, $name, $template, $data)
   	{
             $templateId = Mage::getStoreConfig($template);
		$storeId = Mage::app()->getStore()->getId();
   		$customer = $this->_getSession()->getCustomer();
		  $translate  = Mage::getSingleton('core/translate');
		  $translate->setTranslateInline(false);		  
		  
		  	$sender = array('name'=>$customer->getName(),'email'=>$customer->getEmail());
		  try{
			  Mage::getModel('core/email_template')
			      ->sendTransactional(
			      $templateId,
			      $sender,
			      $emailto,
			      $name,
			      $data,
			      $storeId);
			  $translate->setTranslateInline(true);
		  }catch(Exception $e){
		  		$this->_getSession()->addError($this->__("Email can not send !"));
		  }
   	}

    public function sendEmailAction() {
        $post = $this->getRequest()->getPost('email');
    	$post = trim($post," ,");
    	$emails = explode(',',$post);

    	$validator = new Zend_Validate_EmailAddress();
    	$error = array();
    	foreach($emails as $email){
    		$name = $email;
    		$_name = $this->getStringBetween($email,'"','"');
    		$_email = $this->getStringBetween($email,'<','>');

    		if($_email!== false && $_name !== false)
    		{
    			$email = $_email;
    			$name = $_name;
    		}else if($_email!== false && $_name === false)
    		{
    			if(strpos($email,'"')===false)
    			{
    				$email = $_email;
    				$name = $email;
    			}
    		}
    		$email = trim($email);
                $customer = $this->_getSession()->getCustomer();
	    	if(($validator->isValid($email)) && ($email != $customer->getEmail())) {
	    		// Send email to friend
				$template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
				$postObject = new Varien_Object();
				$customer = $this->_getSession()->getCustomer();
				$postObject->setSender($customer);
				$postObject->setMessage($this->getRequest()->getPost('message'));
				$postObject->setData('invitation_link',Mage::getModel('invitefriends/customer')->getRefferalLink());
				$this->_sendEmailTransaction($email, $name, $template, $postObject->getData());
			}
			else {
			   $error[] = $email;
			}
    	}
    	if(sizeof($error))
    	{
	    	$err = implode("<br>",$error);
	    	Mage::getSingleton('core/session')->addError($this->__("These emails are invalid, the invitation message will not be sent to:<br>%s",$err));
                $this->_redirect('invitefriends/index/index');
    	} else {
		$msg = "Email sent successfully";
		if(sizeof($emails) >1) $msg = "Email sent successfully";
		if(sizeof($emails) > sizeof($error))                  
                Mage::getSingleton('core/session')->addSuccess($this->__($msg));
                $this->_redirect('invitefriends/index/index');
        }
    //invite friend via email end
    }


    public function gmailinviteAction() {
        $this->loadLayout();
	$this->renderLayout();
    }

    public function mailinviteAction() {
        $email = $this->getRequest()->getParam('email');
        $template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
	$postObject = new Varien_Object();
	$customer = $this->_getSession()->getCustomer();
	$postObject->setSender($customer);
	$postObject->setMessage($this->getRequest()->getPost('message'));
	$postObject->setData('invitation_link',Mage::getModel('invitefriends/customer')->getRefferalLink());
        $this->_sendEmailTransaction($email, $name, $template, $postObject->getData());
        $this->_redirect('invitefriends/index/index');
    }

    //add invite friend list into dadabase
    public function invitefriendsAction() {
        $arrData = $this->getRequest()->getParams();
        if($arrData['fbinvite_request_to'] != '' && $arrData['fbinvite_request_from'] != ''){        
        Mage::getModel('invitefriends/customer')->saveInvitedFriends($arrData['fbinvite_request_to'], $arrData['fbinvite_request_from']);
		//$this->_redirect();
        }
    }

    public function fbinviteAction() {
         $this->loadLayout();
	$this->renderLayout();
    }

    public function updateFbuseridAction() {
        $arrData = $this->getRequest()->getParams();
        if($arrData['fbinvite_request_to'] != '' && $arrData['fbinvite_request_from'] != ''){
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $_customer = Mage::getModel('invitefriends/customer')->load($customer->getId());
        $fbfriendIds = $_customer->getFbfriendids();        
        if(!empty ($fbfriendIds)) {
            $arrIds1 = explode(",",$fbfriendIds);
            $arrIds2 = explode(",",$arrData['fbinvite_request_to']);
            $resultarray = array_merge($arrIds1,$arrIds2);
            $result = array_unique($resultarray);
            $friendIds = implode(",", $result);
        } else {
            $friendIds = $arrData['fbinvite_request_to'];
        }        
        $_customer->setFbuserid($arrData['fbinvite_request_from']);
        $_customer->setFbfriendids($friendIds);
        $_customer->save();
        }
    }

    public function updatefriendidAction() {
            $facebookId = $this->getRequest()->getParam('facebookid');
            $_customer = Mage::getModel('invitefriends/customer')->getCollection();
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $tableName = $_customer->getTable('customer');
            $sql = "SELECT customer_id FROM $tableName WHERE fbfriendids LIKE  '%$facebookId%'";
            $selectResult = $write->query($sql);
            $friend_id = $selectResult->fetch(PDO::FETCH_COLUMN);
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerModel = Mage::getModel('invitefriends/customer')->load($customer->getId());
            $customerModel->setFriendId($friend_id);
            $customerModel->setFbuserid($facebookId);
            $customerModel->save();
        
    }


    public function twitterinviteAction() {    
        $this->loadLayout();
	$this->renderLayout();       
    }

   
}