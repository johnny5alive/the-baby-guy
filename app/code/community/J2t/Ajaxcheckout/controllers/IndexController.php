<?php

/**
 * J2T-DESIGN.
 *
 * @category   J2t
 * @package    J2t_Ajaxcheckout
 * @copyright  Copyright (c) 2003-2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    GPL
 */

class J2t_Ajaxcheckout_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();


    }

    public function cartAction()
    {
        
        if ($this->getRequest()->getParam('cart')){
            if ($this->getRequest()->getParam('cart') == "delete"){
                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    try {
                        Mage::getSingleton('checkout/cart')->removeItem($id)
                          ->save();
                    } catch (Exception $e) {
                        Mage::getSingleton('checkout/session')->addError($this->__('Cannot remove item'));
                    }
                }
            }
        }

        if ($this->getRequest()->getParam('product')) {
            $cart   = Mage::getSingleton('checkout/cart');
            $params = $this->getRequest()->getParams();
            $related = $this->getRequest()->getParam('related_product');

            $productId = (int) $this->getRequest()->getParam('product');
            

            if ($productId) {
                $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                try {
                    $cart->addProduct($product, $params);
                    if (!empty($related)) {
                        $cart->addProductsByIds(explode(',', $related));
                    }

                    $cart->save();

                    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);

                    $img = '';
                    Mage::dispatchEvent('checkout_cart_add_product_complete', array('product'=>$product, 'request'=>$this->getRequest()));
                    $img = '<img src="'.Mage::helper('catalog/image')->init($product, 'image')->resize(55,55).'" width="55" height="55" />';
                    $message = $this->__('%s was successfully added to your shopping cart.', $product->getName());
                    Mage::getSingleton('checkout/session')->addSuccess('<div class="j2tajax-checkout-img">'.$img.'</div><div class="j2tajax-checkout-txt">'.$message.'</div>');
                }
                catch (Mage_Core_Exception $e) {
                    if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
                        Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
                    } else {
                        $messages = array_unique(explode("\n", $e->getMessage()));
                        foreach ($messages as $message) {
                            Mage::getSingleton('checkout/session')->addError($message);
                        }
                    }
                }
                catch (Exception $e) {
                    Mage::getSingleton('checkout/session')->addException($e, $this->__('Can not add item to shopping cart'));
                }
                
            }
        }
        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        
        $this->renderLayout();
    }

    public function addtocartAction()
    {
        $this->indexAction();
    }



    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
    }


}