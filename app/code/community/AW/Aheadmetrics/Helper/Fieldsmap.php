<?php

class AW_Aheadmetrics_Helper_Fieldsmap
{
    const CONFIG_NODE = 'awaheadmetrics/sync/fieldsmap';

    protected $_fieldMap = null;
    protected $_attributesMap = null;
    protected $_syncMap = null;
    protected $_mapEntityTable = null;

    protected function _loadMap($update = false)
    {
        if ((!$this->_syncMap || $update) && ($syncMap = @unserialize(Mage::getStoreConfig(self::CONFIG_NODE)))) {
            $this->_syncMap = $syncMap;
            $this->_fieldMap = $this->_syncMap['fieldmap'];
            $this->_mapEntityTable = isset($this->_syncMap['relations']) ? $this->_syncMap['relations'] : $this->_getDefaultMapEntityTable();
            $this->_attributesMap = $this->_syncMap['attributes'];
        } else {
            $this->_mapEntityTable = $this->_getDefaultMapEntityTable();

            $this->_fieldMap = array(
                'sales_flat_order' => array(
                    'entity_id', 'created_at', 'state', 'store_id', 'customer_email', 'status', 'base_grand_total',
                    'base_total_invoiced', 'base_total_refunded', 'base_subtotal', 'base_tax_amount',
                    'base_shipping_amount', 'base_tax_amount', 'base_discount_amount', 'base_shipping_amount',
                    'base_grand_total', 'base_total_invoiced', 'base_total_refunded', /*'customer_group_code',*/
                    'customer_email', 'increment_id', 'customer_group_id', 'coupon_code'
                ),
                'sales_flat_order_address' => array(
                    'entity_id', 'customer_id', 'parent_id', 'address_type', 'postcode', 'country_id', 'region',
                    'city', 'postcode'
                ),
                'sales_flat_order_item' => array(
                    'item_id', 'order_id', 'parent_item_id', 'qty_ordered', 'base_row_total', 'sku', 'product_id',
                    'product_type', 'qty_invoiced', 'qty_refunded', 'base_discount_invoiced', 'base_amount_refunded',
                    'created_at', 'store_id', 'name', 'qty_shipped', 'row_invoiced', 'qty_shipped', 'price',
                    'tax_amount', 'discount_amount', 'row_total', 'row_total_incl_tax', 'tax_invoiced', 'tax_refunded',
                    'quote_item_id', 'base_price', 'base_original_price', 'base_tax_amount', 'base_discount_amount',
                    'base_tax_invoiced', 'base_row_invoiced', 'base_row_total_incl_tax'
                ),
                'wishlist' => array(
                    'wishlist_id', 'customer_id'
                ),
                'wishlist_item' => array(
                    'wishlist_item_id', 'store_id', 'added_at', 'description', 'wishlist_id', 'product_id'
                ),
                'customer' => array(
                    'entity_id', 'group_id', 'email'
                ),
                'customer_group' => array(
                    'customer_group_id', 'customer_group_code'
                ),
                'core_store' => array(
                    'store_id', 'website_id', 'group_id', 'name'
                ),
                'core_store_group' => array(
                    'group_id', 'website_id', 'name'
                ),
                'core_website' => array(
                    'website_id', 'name'
                ),
                'core_config_data' => array(
                    'config_id', 'scope', 'scope_id', 'value', 'path'
                ),
                'catalog_product' => array(
                    'entity_id', 'sku', 'name'
                ),
                'review' => array(
                    'review_id', 'created_at', 'status_id'
                ),
            );
            $this->_attributesMap = array(
                'catalog/product' => array('name')
            );
            $this->_syncMap['fieldmap'] = $this->_fieldMap;
            $this->_syncMap['tablemap'] = $this->_mapEntityTable;
            $this->_syncMap['attributes'] = $this->_attributesMap;
        }

        // For new Magento version
        if (Mage::helper('awaheadmetrics')->checkExtensionVersion('Mage_Sales', '1.6.0.5')) {
            $this->_fieldMap['sales_flat_order_item'][] = 'base_tax_refunded';
            $this->_fieldMap['sales_flat_order_item'][] = 'base_discount_refunded';
        }
    }

    public function getMap()
    {
        $this->_loadMap();
        return $this->_fieldMap;
    }

    public function update($map = array())
    {
        $configModel = Mage::getSingleton('core/config');
        $config = $configModel->saveConfig(self::CONFIG_NODE, serialize($map));
        $this->_loadMap(true);
        return $config;
    }

    public function mapEntityToTable($entity)
    {
        $this->_loadMap();
        return isset($this->_mapEntityTable[$entity]) ? $this->_mapEntityTable[$entity] : null;
    }

    public function getEntityMap($entity)
    {
        $this->_loadMap();
        $mapKey = $this->mapEntityToTable($entity);
        if (array_key_exists($mapKey, $map = $this->getMap())) {
            return $map[$mapKey];
        }
    }

    public function columnIsAttribute($entity, $column)
    {
        $this->_loadMap();
        return (isset($this->_attributesMap[$entity]) && in_array($column, $this->_attributesMap[$entity]));
    }

    protected function _getDefaultMapEntityTable()
    {
        return array(
            'sales/order' => 'sales_flat_order',
            'sales/order_address' => 'sales_flat_order_address',
            'sales/order_item' => 'sales_flat_order_item',
            'wishlist/wishlist' => 'wishlist',
            'wishlist/item' => 'wishlist_item',
            'customer/customer' => 'customer',
            'customer/group' => 'customer_group',
            'core/store' => 'core_store',
            'core/store_group' => 'core_store_group',
            'core/website' => 'core_website',
            'core/config_data' => 'core_config_data',
            'catalog/product' => 'catalog_product',
            'review/review' => 'review'
        );
    }
}
