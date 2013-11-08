<?php

/**
 * MGT-Commerce GmbH
 * http://www.mgt-commerce.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@mgt-commerce.com so we can send you a copy immediately.
 *
 * @category    Mgt
 * @package     Mgt_AmazingWysiwyg
 * @author      Stefan Wieczorek <stefan.wieczorek@mgt-commerce.com>
 * @copyright   Copyright (c) 2012 (http://www.mgt-commerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mgt_AmazingWysiwyg_Adminhtml_MgtamazingwysiwygController extends Mage_Adminhtml_Controller_Action
{
    public function loadAction()
    {
        $request = new Varien_Object($this->getRequest()->getParams());
        if ($request && $request->getKey()) {

            $uploader = new Mage_Core_Model_File_Uploader('file');
            $allowed = Mage::getSingleton('cms/wysiwyg_images_storage')->getAllowedExtensions('image');
            $uploader->setAllowedExtensions($allowed);
           
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $result = $uploader->save(Mage::helper('cms/wysiwyg_images')->getCurrentPath());
            
            $imageUrl = sprintf('%s/%s/%s', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA), Mage_Cms_Model_Wysiwyg_Config::IMAGE_DIRECTORY, $result['file']);

            $array = array(
              'filelink' => $imageUrl,
              'filename' => $_FILES['file']['name']
            );
            
            echo stripslashes(json_encode($array));
            exit;
        }
    }
}
