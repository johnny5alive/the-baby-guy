<?php
/**
 * Catalog Price Slider
 *
 * @category   Magehouse
 * @class    Magehouse_Slider_Block_Catalog_Layer_Filter_Price
 * @author     Mrugesh Mistry <core@magentocommerce.com>
 */
class Magehouse_Slider_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price 
{
    	
	public $_currentCategory;
	public $_searchSession;
	public $_productCollection;
	public $_maxPrice;
	public $_minPrice;
	public $_currMinPrice;
	public $_currMaxPrice;
	public $_imagePath;
	
	public function __construct(){
	
		$this->_currentCategory = Mage::registry('current_category');
		$this->_searchSession = Mage::getSingleton('catalogsearch/session');
		$this->setProductCollection();
		$this->setMinPrice();
		$this->setMaxPrice();
		$this->setCurrentPrices();
		$this->_imagePath = $this->getUrl('media/magehouse/slider');
		
		parent::__construct();		
	}
	
	public function getSliderStatus(){
		if(Mage::getStoreConfig('price_slider/price_slider_settings/slider_loader_active'))
			return true;
		else
			return false;			
	}
	 
	public function getHtml(){
		if($this->getSliderStatus()){
			$text='
				<div class="price">
					<p>
						<input type="text" id="amount" readonly="readonly" style="background:none; border:none;" />
					</p>
					<div id="slider-range"></div>
					
				</div>'.$this->getSliderJs().'
			';	
			
			return $text;
		}	
	}
	
	public function prepareParams(){
		$url="";
	
		$params=$this->getRequest()->getParams();
		foreach ($params as $key=>$val)
			{
					if($key=='id'){ continue;}
					if($key=='min'){ continue;}
					if($key=='max'){ continue;}
					$url.='&'.$key.'='.$val;		
			}		
		return $url;
	}
	
	public function getCurrencySymbol(){
		return Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
	}
	
	public function getSliderJs(){
		
		$baseUrl = explode('?',Mage::helper('core/url')->getCurrentUrl());
		$baseUrl = $baseUrl[0];
		$timeout = $this->getConfig('price_slider/price_slider_conf/timeout');
		$styles = $this->prepareCustomStyles();
		if($this->_currMaxPrice > 0){$max = $this->_currMaxPrice;} else{$max = $this->_maxPrice;}
		if($this->_currMinPrice > 0){$min = $this->_currMinPrice;} else{$min = $this->_minPrice;}
		$html = '
			<script type="text/javascript">
			//<![CDATA[
				jQuery(function($) {
					$( "#slider-range" ).slider({
						range: true,
						min: '.$this->_minPrice.',
						max: '.$this->_maxPrice.',
						values: [ '.$min.', '.$max.' ],
						slide: function( event, ui ) {
							$( "#amount" ).val( "'.$this->getCurrencySymbol().'" + ui.values[ 0 ] + " - '.$this->getCurrencySymbol().'" + ui.values[ 1 ] );
						},stop: function( event, ui ) {
							var x1 = ui.values[0];
							var x2 = ui.values[1];
							$( "#amount" ).val( "'.$this->getCurrencySymbol().'"+x1+" - '.$this->getCurrencySymbol().'"+x2 );
							var url = "'.$baseUrl.'"+"?min="+x1+"&max="+x2+"'.$this->prepareParams().'";
							if(x1 != '.$min.' && x2 != '.$max.'){
								clearTimeout(timer);
								window.location= url;
							}else{
									timer = setTimeout(function(){
										window.location= url;
									}, '.$timeout.');     
								}
						}
					});
					$( "#amount" ).val( "'.$this->getCurrencySymbol().'" + $( "#slider-range" ).slider( "values", 0 ) +
						" - '.$this->getCurrencySymbol().'" + $( "#slider-range" ).slider( "values", 1 ) );
				});
			//]]>
			</script>
			
			'.$styles.'
		';	
		
		return $html;
	}
	
	public function prepareCustomStyles(){
		$useImage = $this->getConfig('price_slider/price_slider_conf/use_image');
		
		$handleHeight = $this->getConfig('price_slider/price_slider_conf/handle_height');
		$handleWidth = $this->getConfig('price_slider/price_slider_conf/handle_width');
		
		$sliderHeight = $this->getConfig('price_slider/price_slider_conf/slider_height');
		$sliderWidth = $this->getConfig('price_slider/price_slider_conf/slider_width');
		
		$amountStyle = $this->getConfig('price_slider/price_slider_conf/amount_style');
		
		
		if($useImage){
			$handle = $this->getConfig('price_slider/price_slider_conf/handle_image');
			$range = $this->getConfig('price_slider/price_slider_conf/range_image');
			$slider = $this->getConfig('price_slider/price_slider_conf/background_image');	
			
			if($handle){$bgHandle = 'url('.$this->_imagePath.$handle.') no-repeat';}
			if($range){$bgRange = 'url('.$this->_imagePath.$range.') no-repeat';}
			if($slider){$bgSlider = 'url('.$this->_imagePath.$slider.') no-repeat';}
		}else{	
			$bgHandle = $this->getConfig('price_slider/price_slider_conf/handle_color');
			$bgRange = $this->getConfig('price_slider/price_slider_conf/range_color');
			$bgSlider = $this->getConfig('price_slider/price_slider_conf/background_color');	
			
		}
		$html ='';
		//$html = '<style type="text/css">';	
		//	$html .= '.ui-slider .ui-slider-handle{';
		//	if($bgHandle){$html .= 'background:'.$bgHandle.';';}
		//	$html .= 'width:'.$handleWidth.'px; height:'.$handleHeight.'px; border:none;}';
		//	
		//	$html .= '.ui-slider{';
		//	if($bgSlider){$html .= 'background:'.$bgSlider.';';}
		//	$html .= ' width:'.$sliderWidth.'px; height:'.$sliderHeight.'px; border:none;}';
		//	
		//	$html .= '.ui-slider .ui-slider-range{';
		//	if($bgRange){$html .= 'background:'.$bgRange.';';}
		//	$html .= 'border:none;}';
		//	
		//	$html .= '#amount{'.$amountStyle.'}';	
		//$html .= '</style>';		
		return $html;
	}
	
	public function getConfig($key){
		return Mage::getStoreConfig($key);
	}
	
	public function setMinPrice(){
		if( (isset($_GET['q']) && !isset($_GET['min'])) || !isset($_GET['q'])){
		$this->_minPrice = $this->_productCollection
							->getFirstItem()
							->getPrice();
		$this->_searchSession->setMinPrice($this->_minPrice);					
							
		}else{
			$this->_minPrice = $this->_searchSession->getMinPrice();	
		}
	}
	
	public function setMaxPrice(){
		if( (isset($_GET['q']) && !isset($_GET['max'])) || !isset($_GET['q'])){
		$this->_maxPrice = $this->_productCollection
							->getLastItem()
							->getPrice();
		$this->_searchSession->setMaxPrice($this->_maxPrice);					
		}else{
			$this->_maxPrice = $this->_searchSession->getMaxPrice();
		}
	}
	
	public function setProductCollection(){
		
		if($this->_currentCategory){
			$this->_productCollection = $this->_currentCategory
							->getProductCollection()
							->addAttributeToSelect('*')
							->setOrder('price', 'ASC');
		}else{
			$this->_productCollection = Mage::getSingleton('catalogsearch/layer')->getProductCollection()	
							->addAttributeToSelect('*')
							->setOrder('price', 'ASC');

		}
							
	}
	
	public function setCurrentPrices(){
		
		$this->_currMinPrice = $this->getRequest()->getParam('min');
		$this->_currMaxPrice = $this->getRequest()->getParam('max'); 
	}	
}
