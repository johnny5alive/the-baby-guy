<?php
class AW_Aheadmetrics_Model_Observer
{
    private $entityList = array(
        'sales/order',
        'sales/order_address',
        'sales/order_item',
        'wishlist/wishlist',
        'wishlist/item',
        'customer/customer',
        'customer/group',
        'core/store',
        'core/store_group',
        'core/website',
        'core/config_data',
        'catalog/product',
        'review/review',
    );

    protected function _write($writeData)
    {
        try {
            $sync = Mage::getModel("awaheadmetrics/sync");
            $sync->setData('sync_data', serialize($writeData));
            $sync->save();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }

    public function modelSaveAfter($event)
    {
        if (!$object = $event->getObject()) {
            return;
        }
        $entity = $object->getResourceName();
        $idField = $object->getIdFieldName();

        if ((in_array($entity, $this->entityList)) && !is_null($idField) && ($data = $object->getData())) {
            $entityMap = Mage::helper('awaheadmetrics/fieldsmap')->getEntityMap($entity);
            $sendData = array();
            foreach ($data as $key => $value) {
                if (!is_object($value) && !is_array($value)
                    && (($entityMap && in_array($key, $entityMap)) || (!$entityMap))
                ) {
                    $sendData[$key] = $value;
                }
            }
            if($entity=='review/review'){
                $sendData['stores']=$object->getStores();
            }
            $write = array(
                'op' => $event->getName(),
                'entity' => $entity,
                'id_field' => $idField,
                'data' => $sendData,
            );
            $this->_write($write);
        }
    }

    public function modelDeleteAfter($event)
    {
        if (!$object = $event->getObject()) {
            return;
        }
        $entity = $object->getResourceName();
        $idField = $object->getIdFieldName();
        if ((in_array($entity, $this->entityList)) && !is_null($idField)) {
            $id = $object->getData($idField);
            $write = array(
                'op' => $event->getName(),
                'entity' => $entity,
                'id_field' => $idField,
                'data' => array($idField => $id),
            );
            $this->_write($write);
        }
    }

    public function beforeDashboard($observer)
    {
        /** @var AW_Aheadmetrics_Helper_Data $helper */
        $helper = Mage::helper('awaheadmetrics');
        /** @var AW_Aheadmetrics_Helper_Config $configHelper */
        $configHelper = Mage::helper('awaheadmetrics/config');
        if ($configHelper->getDashboardEnabled() && ($domainName = $configHelper->getDashboardDomain())) {
            $authKey = $configHelper->getSecurityAuthKey();
            /** @var Mage_Core_Model_Layout $layout */
            $layout = Mage::getSingleton('core/layout');
            /** @var Mage_Adminhtml_Block_Template $newDashboardBlock */
            $newDashboardBlock = $layout->createBlock('adminhtml/template')
                ->setTemplate('aw_aheadmetrics/dashboard.phtml')
                ->setData(array(
                    'aw_am_auth_key' => $authKey,
                    'aw_am_domain' => $helper->getDomainUrl($domainName)
                ));
            /** @var Mage_Core_Block_Text_List $contentBlock */
            $contentBlock = $layout->getBlock('content');
            $contentBlock->unsetChild('dashboard');
            $contentBlock->append($newDashboardBlock, 'dashboard');
        }
    }

    public function afterConfigSave($observer)
    {
        /** @var AW_Aheadmetrics_Helper_Config $configHelper */
        $configHelper = Mage::helper('awaheadmetrics/config');
        if ($configHelper->getDashboardEnabled()) {
            /** @var AW_Aheadmetrics_Helper_Data $helper */
            $helper = Mage::helper('awaheadmetrics');
            $authKey = $configHelper->getSecurityAuthKey();
            $domainName = $configHelper->getDashboardDomain();

            $canUseDomain = true;
            try {
                $httpClient = new Zend_Http_Client($helper->getDomainUrl($domainName) . '/external/?ak=' . $authKey);
                if ($httpClient->request()->getStatus() != 200) {
                    Mage::getSingleton('core/session')->addError($helper->__('Wrong domain name specified'));
                    $canUseDomain = false;
                }
            } catch (Exception $ex) {
                Mage::getSingleton('core/session')->addError($ex->getMessage());
                $canUseDomain = false;
            }

            if (!$canUseDomain) {
                $configHelper->setDashboardEnabled(false)
                    ->setDashboardDomain('');
            }
        }
    }
}
