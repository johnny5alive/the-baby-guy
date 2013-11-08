
<?php

class Apptha_Sociallogin_Model_Observer extends Mage_Core_Model_Abstract {

    public function checkCaptcha($observer) {

    $formId = 'Apptha_Sociallogin'; // Identifier in config.xml
    $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
    $request = $controller->getRequest();
    if ($captchaModel->isRequired()) {
        $controller = $observer->getControllerAction();

        $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
        if (!$captchaModel->isCorrect($this->_getCaptchaString($request, $formId))) {


            if((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
                // Is ajax
                $action = $request->getActionName();
                Mage::app()->getFrontController()->getAction()->setFlag(
                        $action, Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);

                $controller->getResponse()->setHttpResponseCode(200);
                $controller->getResponse()->setHeader('Content-type', 'application/json');

                $controller->getResponse()->setBody(json_encode(
                        array(
                            "msg" => Mage::helper('module')->__('Incorrect CAPTCHA.')
                        )
                    ));

            } else {
               // Is form submit
                Mage::getSingleton('customer/session')
                    ->addError(Mage::helper('module')->__('Incorrect CAPTCHA.'));
                $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
                Mage::getSingleton('customer/session')
                    ->setCustomerFormData($controller->getRequest()->getPost());
                $controller->getResponse()->setRedirect(Mage::getUrl('*/*'));
            }
        }
    }

    return $this;
    }


}
?>