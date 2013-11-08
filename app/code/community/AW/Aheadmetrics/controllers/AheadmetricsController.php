<?php

class AW_Aheadmetrics_AheadmetricsController extends Mage_Adminhtml_Controller_Action
{
    private $server = null;
    private $session = null;
    private $auth_token = null;

    public function getReportAction()
    {

    }

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
                CURLOPT_POSTFIELDS => $data
            )
        );

        $response = curl_exec($ch);
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

        $decodet_response = json_decode($response, true);
        if ($decodet_response['success']) {
            $this->session = $decodet_response['session'];
        } else {
            throw new Exception($decodet_response['error_msg']);
        }
    }

    private function _sendSyncData()
    {
        $from = 0;
        $limit = 10;
        $exist_data = false;

        do {
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

            echo $response_text;
        } while ($exist_data);
        $this->_cleanSyncData($from);
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
}
