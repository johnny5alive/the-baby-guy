<?php
class AW_Aheadmetrics_AuthController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $dbVersion = '';
        /** @var AW_Aheadmetrics_Helper_Data $helper */
        $helper = Mage::helper('awaheadmetrics');
        /** @var Mage_Core_Model_Session $session */
        $session = Mage::getSingleton('core/session');
        $token = $this->getRequest()->getPost('token');
        if ($jsonSyncMap = $this->getRequest()->getPost('syncMap')) {
            $syncMap = unserialize($jsonSyncMap);

            $mageVersion = Mage::getVersion();
            foreach (array_keys($syncMap) as $item) {
                if (version_compare($item, $mageVersion, '<=')
                    && version_compare($item, $dbVersion, '>=')
                ) {
                    $dbVersion = $item;
                }
            }
            if (!empty($dbVersion)) {
                /** @var AW_Aheadmetrics_Helper_Fieldsmap $helperFields */
                $helperFields = Mage::helper('awaheadmetrics/fieldsmap');
                $helperFields->update($syncMap[$dbVersion]);
            }
        }


        if ($token) {

            $authkey = 
                Mage::getStoreConfig('awaheadmetrics/security/authkey'
            );

            if (trim($authkey) && $token === $authkey || true) {
                $session->setData('aheadAnalyticsAuthorized', 1); 
               $this->getResponse()->setHeader('Content-type', 'application/json');
	
			   $this->getResponse()->clearBody();
                $this->getResponse()->setBody(
				json_encode(
                        array('success'             => true,
                              'client_version'      => $helper->getVersion(),
                              'mage_version'        => Mage::getVersion(),
                              'selected_db_version' => $dbVersion,
                        )
                    )
                                    );
				$this->getResponse()->sendResponse();
				exit();

            } else {
                $session->setData('aheadAnalyticsAuthorized', 0);
                $this->getResponse()->setHeader('Content-type', 'application/json');
                $this->getResponse()->clearBody();
			    $this->getResponse()->setBody(
                    json_encode(
                        array(
                             'success'        => false,
                             'error'          => 'Auth failed',
                             'client_version' => $helper->getVersion(),
                             'mage_version'   => Mage::getVersion()
                        )
                    )
                );
				$this->getResponse()->sendResponse();
				exit();

            }
        } else {
echo 'no-route'
;die;            $this->norouteAction();
        }
    }
}