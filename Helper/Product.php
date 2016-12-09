<?php
namespace Adcurve\Adcurve\Helper;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $productRepository;
	protected $_product;
	
	protected $_skipAttributes = array(
		// == General attributes ==
        'media_gallery',
        'category_ids',
        'tier_price',
		'extension_attributes',
        '_cache_instance_product_set_attributes',
        'stock_data',
        'copy_to_stores',
        'website_ids',
        'configurable-matrix',
        'associated_product_ids',
        'store_ids',
        'related_products',
        'cross_sell_products',
        'up_sell_products',
        // == Magento 1 list ==
        // 'media_gallery_images',
        // '_cache_editable_attributes',
        // 'matched_rules',
        // 'use_config_gift_message_available',
        // 'media_attributes',
        // 'can_save_configurable_attributes',
        // 'can_save_custom_options',
        // 'can_save_bundle_selections',
        // 'type_has_options',
        // 'type_has_required_options',
        // 'matched_rules',
        // 'parent_id',
        // 'is_changed_categories',
        // 'is_changed_websites',
        // //'tax_class_id',
        // //'tax_percent',
        // 'applied_rates',
        // 'stock_item',
        // 'custom_design_from_is_formated',
        // 'custom_design_to_is_formated',
        // 'news_from_date_is_formated',
        // 'news_to_date_is_formated',
        // 'special_from_date_is_formated',
        // 'special_to_date_is_formated',
        // 'page_layout',
        // 'entity_type_id',
        // 'options_container',
    );
	
	public function __construct(
		\Magento\Catalog\Model\ProductRepository $productRepository
	){
		$this->productRepository = $productRepository;
	}
	
	/**
	 * Collect fuction to prepare productdata for Adcurve updates
	 */
	public function getProductData($product = null, $storeId)
	{
		if(!$product){
			return false;
		}
		
		if(!$product->getSku() || $product->getSku() == ''){
			return false;
		}
		
		$this->_product = $this->productRepository->get($product->getSku(), false, $storeId);
		
		$data = $this->_product->getData();
		
		$data = $this->_unsetSkipAttributes($data);
		
		$data = $this->_transposeSelectAttributes($data);
		
		$data = $this->_addLogicAttributes($data);
		var_dump($data);
		die();
		$this->addSpecialAttributes($data);
		
		return $product->getData();
	}
	
	/**
	 * Function to unset all special and irrelevant attributes
	 */
	private function _unsetSkipAttributes($data)
	{
		foreach($this->_skipAttributes as $attribute){
			if(isset($data[$attribute])){
				unset($data[$attribute]);
			}
		}
		return $data;
	}
	
	/**
	 * Function to get option label's instead of value id's for select and multiselect attributes
	 */
	private function _transposeSelectAttributes($data, $implodeMultiselect = true)
	{
		foreach($data as $attributeCode => $value){
			$attribute = $_product->getResource()->getAttribute($attributeCode);
			
			if(!is_object($attribute)){
				continue;
			}
			
			if (in_array($attribute->getFrontend()->getInputType(), ['select', 'multiselect'])) {
				$optionText = $attribute->getSource()->getOptionText($value);
				
				if($implodeMultiselect 
					&& $attribute->getFrontend()->getInputType() == 'multiselect'
					&& is_array($optionText)
				){
					$optionText = implode(',', $optionText);
				}
				$data[$attributeCode] = $optionText;
			}
		}
		return $data;
	}
	
	private function _addLogicAttributes($data, $_product)
	{
		$data['store_id']		= (int)$store_id;
        $data['category_path']  = $this->getCategoryPath($product);
        $data['deeplink']       = $this->getProductUrl($product, $store_id);
		$data['simple_id']		= $product->getId();		
		$data['currency']		= Mage::app()->getStore($store_id)->getCurrentCurrencyCode();
	}
	
}