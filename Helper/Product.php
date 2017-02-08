<?php
namespace Adcurve\Adcurve\Helper;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $productData;
	
	protected $storeModel;
	
	protected $logger;
	
	protected $updateFactory;
	protected $updateRepository;
	protected $dateTime;
	
	protected $productRepository;
	protected $_categoryResource;
	protected $_configurableproductResource;
	protected $_attributeRepository;
	protected $catalogHelper;
	
	protected $searchCriteria;
    protected $filterGroup;
    protected $filterBuilder;
	
	protected $priceModel;
	protected $priceCurrency;
	
	protected $_product;
	protected $_fullCategoryPathsMappingArray;
	
	protected $taxgimmickHelper;
	protected $imageHelper;
	protected $imageMediaUrl;
	
	protected $irrelevantAttributes = array(
		// == General attributes ==
		'associated_product_ids',
		'category_ids',
		'configurable-matrix',
		'copy_to_stores',
		'created_at',
		'cross_sell_products',
		//'extension_attributes',
		'gift_message_available',
		'media_gallery',
		'options_container',
		'options',
		'quantity_and_stock_status',
		'related_products',
		'stock_data',
		'store_ids',
		'tier_price',
		'up_sell_products',
		'updated_at',
		'website_ids',
        // == Downloadable product attributes ==
        'downloadable_links',
        'downloadable_samples',
    );
	
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Store\Model\Store $storeModel,
		\Adcurve\Adcurve\Model\UpdateFactory $updateFactory,
		\Adcurve\Adcurve\Model\UpdateRepository $updateRepository,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Magento\Catalog\Model\ResourceModel\Category $_categoryResource,
		\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $_configurableproductResource,
		\Magento\Catalog\Model\Product\Attribute\Repository $_attributeRepository,
	    \Magento\Catalog\Helper\Data $catalogHelper,
		\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
	    \Magento\Framework\Api\Search\FilterGroup $filterGroup,
	    \Magento\Framework\Api\FilterBuilder $filterBuilder,
	    \Magento\Catalog\Model\Product\Type\Price $priceModel,
	    \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
	    \Adcurve\Adcurve\Helper\Taxgimmick $taxgimmickHelper,
	    \Magento\Catalog\Helper\Image $imageHelper
	){
		parent::__construct($context);
		
		$this->storeModel = $storeModel;
		
		$this->logger = $logger;
		
		$this->updateFactory = $updateFactory;
		$this->updateRepository = $updateRepository;
		$this->dateTime = $dateTime;
		
		$this->productRepository = $productRepository;
		$this->_categoryResource = $_categoryResource;
		$this->_configurableproductResource = $_configurableproductResource;
		$this->_attributeRepository = $_attributeRepository;
		$this->catalogHelper = $catalogHelper;
		
		$this->searchCriteria = $searchCriteria;
	    $this->filterGroup = $filterGroup;
	    $this->filterBuilder = $filterBuilder;
		
		$this->priceModel = $priceModel;
		$this->priceCurrency = $priceCurrency;
		
		$this->taxgimmickHelper = $taxgimmickHelper;
		$this->imageHelper = $imageHelper;
	}
	
	public function getProductData($product = null, $storeId)
	{
		if(!$product){
			return false;
		}
		
		if(!$product->getSku() || $product->getSku() == ''){
			return false;
		}
		
		$this->storeModel->setId($storeId);
		
		$this->_product = $this->productRepository->get($product->getSku(), false, $storeId);
		
		$this->productData = [];
		$this->productData = $this->_product->getData();
		
		$this->productData['store_id'] = $storeId;
		$this->productData['entity_id'] = $this->_product->getId();
		$this->productData['simple_id'] = $this->_product->getId();
		
		$this->_unsetIrrelevantAttributes();
		
		// Get the labels of select and multiselect attributes
		$this->_transposeSelectAttributes();
		
		// Adds attributes which need logic to retrieve
		// attributes: [enabled, configurable_id, category_path, deeplink, currency]
		$this->_addLogicAttributes();
		
		$this->_addImageAttributes();
		
		$this->_addPriceAttributes();
		
		$this->_addStockAttributes();
		
		ksort($this->productData);
		
		if(count($this->productData) < 1){
            return false;
        }
		
		return $this->productData;
	}
	
	public function saveUpdateForAdcurve($preparedData)
	{
		if(!$preparedData){
			return false;
		}
		
		if(!isset($preparedData['entity_id']) || !isset($preparedData['store_id'])){
			return false;
		}
		
		try{
			$update = $this->updateFactory->create();
			$update->setProductId($preparedData['entity_id']);
			$update->setStoreId($preparedData['store_id']);
			$update->setProductData($preparedData);
			$currentDateTime = $this->dateTime->gmtDate();
			$update->setCreatedAt($currentDateTime);
			$update->setUpdatedAt($currentDateTime);
			$update->setStatus(\Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_UPDATE);
			$this->updateRepository->save($update);
		} catch(\Exception $e){
			$this->logger->addError($e);
		}
	}
	
	private function _unsetIrrelevantAttributes()
	{
		foreach($this->irrelevantAttributes as $attribute){
			if(isset($this->productData[$attribute])){
				unset($this->productData[$attribute]);
			}
		}
		// Unset all "_cache_instance" attributetypes
		foreach($this->productData as $key => $value){
			if(strpos($key, '_cache_instance') === (int)0){
				unset($this->productData[$key]);
			}
		}
		if(isset($this->productData['extension_attributes'])){
			
			/* TO DO: Complete extensions attribute logic
			var_dump(get_class($this->productData['extension_attributes']));
			var_dump(get_class_methods($this->productData['extension_attributes']));
			if(isset($extensionAttributes['_data']) && !empty($extensionAttributes['_data'])){
				foreach($this->productData['extension_attributes']->getData() as $key => $attribute){
					var_dump($key);
					var_dump($attribute);
					if(isset($this->productData[$key])){
						$this->productData[$key] = $attribute;
					}
				}
			}
			*/
			unset($this->productData['extension_attributes']);
		}
	}
	
	/**
	 * Function to get option label's instead of value id's for select and multiselect attributes
	 */
	private function _transposeSelectAttributes($implodeMultiselect = true)
	{
		foreach($this->productData as $attributeCode => $value){
			$attribute = $this->_product->getResource()->getAttribute($attributeCode);
			
			if(!$attribute
				|| $attribute === (bool)false
				|| !is_object($attribute)
			){
				continue;
			}
			
			if(empty($value) || is_array($value)){
				continue;
			}
			
			$selectAttributes = ['select', 'multiselect'];
			if (in_array($attribute->getFrontend()->getInputType(), $selectAttributes)) {
				$optionText = $attribute->getSource()->getOptionText($value);
				if($optionText instanceof \Magento\Framework\Phrase){
					$optionText = $optionText->getText();
				}
				
				if($implodeMultiselect 
					&& $attribute->getFrontend()->getInputType() == 'multiselect'
					&& is_array($optionText)
				){
					$optionText = implode(',', $optionText);
				}
				$this->productData[$attributeCode] = $optionText;
			}
		}
	}
	
	private function _addLogicAttributes()
	{
		$this->productData['enabled'] = (bool) ($this->_product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
			&& in_array($this->productData['store_id'], $this->_product->getStoreIds());
		
		$this->productData['configurable_id'] = $this->getConfigurableproductId($this->_product->getId(), $this->_product->getTypeId());
		
		$categoriesArray = $this->getProductCategoriesArray(2, '>');
		// TO DO: Create a way to send all the category paths, ask Adcurve for a possible format.
        $this->productData['category_path'] = end($categoriesArray);
		$this->productData['deeplink'] = $this->_product->getUrlModel()->getUrl($this->_product);
		
		$this->productData['currency'] = $this->storeModel->getCurrentCurrencyCode();
	}
	
	public function getConfigurableproductId($productId, $productType)
	{
		$parentIds = $this->_configurableproductResource->getParentIdsByChild($productId);
		if($productType == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
			&& isset($parentIds[0])
			&& $parentIds[0] != $productId
		){
			return $parentIds[0];
		} else{
			return $productId;
		}
	}
	
	private function _addImageAttributes()
	{
		$mediaGalleryImages = $this->_product->getMediaGalleryImages();
		
		if($mediaGalleryImages->getSize() > 0){
			$imageCount = 1;
			foreach($mediaGalleryImages as $image){
				$imageKey = 'image_' . $imageCount;
				$this->productData[$imageKey] = $image->getUrl();
				$imageCount++;
			}
		}
		
		// TO DO: Get all images with image helper/factory
		$this->imageMediaUrl = $this->storeModel->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
		$imageKeys = ['image', 'small_image', 'thumbnail'];
		
		foreach($imageKeys as $imageKey){
			if(isset($this->productData[$imageKey])
				&& $this->productData[$imageKey] != 'no_selection'
				&& $this->productData[$imageKey] != false
			){
				$this->productData[$imageKey] = $this->imageMediaUrl . $this->productData[$imageKey];
			}
		}
	}
	
	private function _addPriceAttributes()
	{
		$this->filterGroup->setFilters([
	        $this->filterBuilder
	            ->setField('frontend_input')
	            ->setConditionType('eq')
	            ->setValue('price')
	            ->create()
	    ]);
		
	    $this->searchCriteria->setFilterGroups([$this->filterGroup]);
		
		$priceAttributes = $this->_attributeRepository->getList($this->searchCriteria)->getItems();
		
		if($priceAttributes){
			foreach($priceAttributes as $priceAttribute){
				if(isset($this->productData[$priceAttribute->getAttributeCode()])){
					$this->productData[$priceAttribute->getAttributeCode()] = $this->priceCurrency->convert($this->productData[$priceAttribute->getAttributeCode()]);
				}
			}
		}
		
		if(!isset($this->productData['price'])){
			$this->productData['price'] = '';
		}
		
		if(!isset($this->productData['special_price'])){
			$this->productData['special_price'] = '';
		}
		
		if(!isset($this->productData['special_from_date'])){
			$this->productData['special_from_date'] = '';
		}
		
		if(!isset($this->productData['special_to_date'])){
			$this->productData['special_to_date'] = '';
		}
		
		$finalPrice = $this->priceModel->calculatePrice(
			$this->productData['price'],
	        $this->productData['special_price'],
	        $this->productData['special_from_date'],
	        $this->productData['special_to_date']
		);
		
		$finalPriceInclTax = $this->taxgimmickHelper->getTaxPrice(
	        $this->_product,
	        $finalPrice,
	        true, // incl. tax
	        null,
	        null,
	        null,
	        $this->storeModel
	    );
		
		$finalPriceExclTax = $this->taxgimmickHelper->getTaxPrice(
	        $this->_product,
	        $finalPrice,
	        false, // excl. tax
	        null,
	        null,
	        null,
	        $this->storeModel
	    );
		
		$priceInclTax = $this->taxgimmickHelper->getTaxPrice(
            $this->_product,
            $this->productData['price'],
            true, // incl. tax
            null,
            null,
            null,
            $this->storeModel
        );
		
		if($priceInclTax){
			$this->productData['price'] = number_format($priceInclTax, 4, '.', '');
		}
		
		if($finalPriceInclTax){
	        $this->productData['selling_price'] = number_format($finalPriceInclTax, 4, '.', '');
		}
		
		if($finalPriceExclTax){
	        $this->productData['selling_price_ex'] = number_format($finalPriceExclTax, 4, '.', '');
		}
	}
	
	private function _addStockAttributes()
	{
		$stockItem = $this->_product->getExtensionAttributes()->getStockItem();
		
		if($stockItem){
			$this->productData['stock_status'] = $stockItem->getIsInStock();
            $qty = $stockItem->getQty();
            if (is_null($qty)) {
                $qty = 0;
            }
            $this->productData['stock_amount'] = (int)$qty;
			$this->productData['manage_stock'] = ($stockItem->getManageStock() == 1 ) ? 'Yes' : 'No';
		}
	}
	
	public function getProductCategoriesArray($shiftAmount = 2, $delimiter = ' > ')
	{
		if(!$this->_product){
			return false;
		}
		
		$categoryCollection = $this->_product->getCategoryCollection()->addAttributeToSelect('path');
		$categories = array();
		foreach($categoryCollection as $category){
			if(!isset($this->_fullCategoryPathsMappingArray[$category->getPath()])){
				$fullCategoryName = '';
				$categoryIds = explode('/', $category->getPath());
				for ($i=0; $i < $shiftAmount; $i++) {
					array_shift($categoryIds); // Shift off categories (such as Magento root, website specific or generic names)
				}
				if(count($categoryIds) > 0){
					$i = 0;
					foreach($categoryIds as $categoryId){
						$categoryName = $this->_categoryResource->getAttributeRawValue($categoryId, 'name', $this->_product->getStoreId());
						if($categoryName){
							if($i > 0){
								$fullCategoryName .= $delimiter;
							}
							$fullCategoryName .= $categoryName;
							$categoryName = false;
							$i++;
						}
					}
					$this->_fullCategoryPathsMappingArray[$category->getPath()] = $fullCategoryName;
					unset($fullCategoryName);
				}
			}
			if(isset($this->_fullCategoryPathsMappingArray[$category->getPath()])
				&& $this->_fullCategoryPathsMappingArray[$category->getPath()] != ''
			){
				$categories[] = $this->_fullCategoryPathsMappingArray[$category->getPath()];
			}
		}
		return $categories;
	}
}