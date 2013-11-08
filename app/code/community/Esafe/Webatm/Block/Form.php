<?php
/**
 * Esafe Webatm Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so one can be sent to you a copy immediately.
 *
 * @category   Esafe
 * @package    Esafe_Webatm
 * @author     Jiang Sungjin
 * @author     Jin Kang
 * @copyright  Copyright (c) 2013 Skybear Co. Ltd. (http://www.myskybear.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Esafe_Webatm_Block_Form extends Mage_Payment_Block_Form
{
    protected function _toHtml()
    {
        $_code = $this->getMethodCode();
        $html = '<ul id="payment_form_' . $_code . '" class="form-list" style="display:none"><li>';
        $html .= $this->__('&#30070;&#24744;&#25552;&#20132;&#35330;&#21934;&#26178;&#65292;&#23559;&#34987;&#24341;&#23566;&#33267;&#32005;&#38525;&#31185;&#25216;&#32178;&#31449;');
        //$html .= '<li><img src="'.$this->getSkinUrl('images/cc_logos/JCB.png').'" height="40" />';
        //$html .= '&emsp;<img src="'.$this->getSkinUrl('images/cc_logos/Master.png').'" height="40" />';
        //$html .= '&emsp;<img src="'.$this->getSkinUrl('images/cc_logos/VISA.png').'" height="40" /></li>';
        $html .= '</li></ul>';
        return $html;
    }
}
