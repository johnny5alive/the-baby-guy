<?php

class AW_Aheadmetrics_SyncController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();        
        $session = Mage::getSingleton('core/session');
        if (1 != $session->getData('aheadAnalyticsAuthorized')) {
            $this->norouteAction();
        }
    }

    private function _sendSerialize($data)
    {
        $sAdapter = new Zend_Serializer_Adapter_PhpSerialize();
        $serializer = Zend_Serializer::factory($sAdapter);
        $serialized = $serializer->serialize($data);
        $this->getResponse()->setHeader('Content-type', 'text/plain');
	    $this->getResponse()->clearBody();
        $this->getResponse()->setBody($serialized);
		$this->getResponse()->sendResponse();

    }

    public function getEntityStatusAction()
    {
        $entity = $this->getRequest()->getPost('entity');

        $model = Mage::getModel($entity);
        $collection = $model->getCollection();
        $this->_sendSerialize(
            array(
                'message' => $collection->count()
            )
        );
    }

    public function getEntityDataAction()
    {
        /** @var AW_Aheadmetrics_Helper_Fieldsmap $fieldsmapHelper */
        $fieldsmapHelper = Mage::helper('awaheadmetrics/fieldsmap');
        $entity = trim($this->getRequest()->getPost('entity'));
        $from = (int)$this->getRequest()->getPost('from');
        $limit = (int)$this->getRequest()->getPost('limit');

        $model = Mage::getModel($entity);
        if (!$model) {
            return;
        }
        /** @var Mage_Eav_Model_Entity_Collection_Abstract|Mage_Core_Model_Mysql4_Collection_Abstract $collection */
        $collection = $model->getCollection();
        $entityMap = $fieldsmapHelper->getEntityMap($entity);

        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        foreach ($entityMap as $field) {
            if ($fieldsmapHelper->columnIsAttribute($entity, $field)) {
                $collection->addAttributeToSelect($field);
            } else {
                $collection->getSelect()->columns($field);
            }
        }

        if (in_array($entity, array('review/review'))) {
            $collection->addStoreData();

            $collection->getSelect()->order(array('main_table.' . $model->getIdFieldName() . ' ASC'));
        } else {
            $collection->getSelect()->order(array($model->getIdFieldName() . ' ASC'));
        }

        $collection->getSelect()->limit($limit, $from);

        $sync_data = array();
        foreach ($collection as $item) {
            $item_data['op'] = 'sync';
            $item_data['entity'] = $entity;
            $item_data['data'] = $item->getData();
            $item_data['id_field'] = $item->getIdFieldName();
            $sync_data[] = base64_encode(serialize($item_data));
        }

        $response = array();
        $response['data'] = $sync_data;
        $this->_sendSerialize($response);
    }

    public function clearSyncDataAction()
    {
        $last_id = (int)$this->getRequest()->getPost('from');
        $write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $table = Mage::getSingleton('core/resource')->getTableName('awaheadmetrics/sync');
        $write->query("Delete From `$table` Where id <= " . (int)$last_id);
    }
}
