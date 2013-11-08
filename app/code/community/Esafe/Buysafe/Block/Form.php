<?php
/**
 * Esafe Buysafe Extension
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
 * @package    Esafe_Buysafe
 * @author     Jiang Sungjin
 * @author     Jin Kang
 * @copyright  Copyright (c) 2013 Skybear Co. Ltd. (http://www.myskybear.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Esafe_Buysafe_Block_Form extends Mage_Payment_Block_Form
{
    protected function _toHtml()
    {
        $_code = $this->getMethodCode();
        $html = '<ul id="payment_form_' . $_code . '" class="form-list" style="display:none"><li>';
        $html .= $this->__('當您提交訂單，將被引導至紅陽科技網站完成付款作業。目前本網站僅接受台灣銀行發行之信用卡。');
        $html .= '<li><img src="'.$this->getSkinUrl('images/cc_logos/JCB.png').'" height="40" />';
        $html .= '&emsp;<img src="'.$this->getSkinUrl('images/cc_logos/Master.png').'" height="40" />';
        $html .= '&emsp;<img src="'.$this->getSkinUrl('images/cc_logos/VISA.png').'" height="40" /></li>';
        $html .= '</li></ul>';
        return $html;
    }
}
