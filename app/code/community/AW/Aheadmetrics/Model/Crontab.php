<?php
class AW_Aheadmetrics_Model_Crontab
{
    private $server = null;
    private $session = null;
    private $auth_token = null;

    private function _init()
    {
        $this->server = Mage::getConfig()->getNode(
            'default/awaheadmetrics/processing'
        )->server;

        $this->auth_token = 
            Mage::getStoreConfig('awaheadmetrics/security/authkey');
    }

    private function _sendCurlRequest($url, $data, &$response)
    {
        $ch = curl_init();
        curl_setopt_array($ch, array(
                CURLOPT_URL => $this->server . $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_SSL_VERIFYPEER => false
            )
        );

        if (Mage::helper('awaheadmetrics')->isDebugMode()) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);
    }

    private function _auth()
    {
        $response = null;
        $this->_sendCurlRequest(
            '/backwardsync/auth', array(
                'auth_key' => $this->auth_token
            ), $response
        );

        $decodedResponse = json_decode($response, true);
        if ($decodedResponse['success']) {
            $this->session = $decodedResponse['session'];
        } else {
            throw new Exception($decodedResponse['error_msg'] ? $decodedResponse['error_msg'] : $response);
        }
    }

    private function _sendSyncData()
    {
        $from = 0;
        $limit = 50;

        do {
            $sendedIds = array();
            $exist_data = false;
            $data = array();

            $model = Mage::getModel('awaheadmetrics/sync');
            $collection = $model->getCollection();

            $collection->getSelect()->order(array($model->getIdFieldName() . ' ASC'));
            $collection->getSelect()->limit($limit);

            $collection->addFieldToFilter(
                $model->getIdFieldName(), array('from' => $from)
            );

            $collection->load();

            foreach ($collection as $data_item) {
                $sendedIds[] = $data_item->getId();
                $from = $data_item->getId() + 1;
                $exist_data = true;

                $data[] = $data_item->getData('sync_data');
            }

            $response_text = null;

            $exist_data && $this->_sendCurlRequest(
                '/backwardsync/sync', http_build_query(
                    array(
                        'session' => $this->session,
                        'data' => $data
                    )
                ), $response_text
            );

            $this->_cleanSyncData($sendedIds);
        } while ($exist_data);
    }

    private function _cleanSyncData($ids)
    {
        if ($ids) {
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
            $table = Mage::getSingleton('core/resource')->getTableName('awaheadmetrics/sync');
            $write->query("Delete From `$table` Where id IN (" . implode(',', $ids) . ")");
        }
    }

    private function _finalyze()
    {
        $response = null;
        $this->_sendCurlRequest(
            '/backwardsync/finalize', array(
                'session' => $this->session
            ), $response
        );
    }

    private function sendClientVersion()
    {
        $responseText = null;
        $this->_sendCurlRequest(
            '/backwardsync/version', http_build_query(
                array(
                    'session' => $this->session,
                    'client_version' => Mage::helper('awaheadmetrics')->getVersion()
                )
            ), $responseText
        );
    }

    public function sync()
    {
        $this->_init();
        try {
            $this->_auth();
            $this->sendClientVersion();
            $this->_sendSyncData();
            $this->_finalyze();
        } catch (Exception $e) {
            Mage::log($e->getMessage());
        }
    }
}
