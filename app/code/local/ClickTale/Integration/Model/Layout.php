<?php
/**
 * ClickTale - Magento Integration Module
 *
 * LICENSE
 *
 * This source file is subject to the ClickTale(R) Integration Module License that is bundled
 * with this package in the file LICENSE_CLICKTALE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.clicktale.com/Integration/0.2/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@clicktale.com so we can send you a copy immediately.
 *
 */
?>
<?php
require_once Mage::getRoot().DS.'..'.DS.'lib'.DS.'ClickTale'.DS.'ClickTaleInit.php';
require_once Mage::getRoot().DS.'..'.DS.'lib'.DS.'ClickTale'.DS.'ClickTale.inc.php';

class ClickTale_Integration_Model_Layout extends Mage_Core_Model_Layout
{
  public function getOutput() {
  	$passthrough = false;
	
	$request = Mage::app()->getRequest();
	$response = Mage::app()->getResponse();
	
	// ignore XHR requests
	$passthrough = $passthrough || $request->isXmlHttpRequest();
	
	// ignore admin pages
	$passthrough = $passthrough || strtolower($request->getModuleName()) == 'admin';
	
	// process only specific content types
	if(!$passthrough) {
		$passthrough = true;
		foreach($response->getHeaders() as $header) {
			$matches = array();
			
			if($header['name'] == "Content-Type" &&
				preg_match("=^(?:text/html|application/xhtml+xml)=", $header['value'])) {
					$passthrough = false;
					break;
			}
		}
	}
		
  	$out = parent::getOutput();
	if($passthrough) {
		return $out;
	}
	
	$settings = ClickTale_Settings::Instance();
	if (empty($settings->CacheFetchingUrl))
	{
		$base = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$settings->CacheFetchingUrl = "{$base}lib/ClickTale/ClickTaleCache.php?t=%CacheToken%";
	}
	
	return ClickTale_ProcessOutput($out);
  }
}
