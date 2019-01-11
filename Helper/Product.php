<?php
namespace Adcurve\Adcurve\Helper;
use Adcurve\Adcurve\Model\ResourceModel\Connection\CollectionFactory as ConnectionCollectionFactory;
//use Magento\Catalog\Helper\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Module\ModuleListInterface;
use Psr\Log\LoggerInterface as Logger;
class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $productData;
	protected $productFactory;  
	protected $storeModel;
	protected $_storeManager;
	protected $updateFactory;
	protected $updateRepository;
	protected $dateTime;
	protected $_configurable;
	protected $productRepository;
	protected $_categoryResource;
	protected $_configurableproductResource;
	protected $_attributeRepository;
	protected $catalogHelper;
	protected $_attributeFactory;
	protected $searchCriteria;
    protected $filterGroup;
    protected $filterBuilder;
	protected $_taxHelper;
	protected $priceModel;
	protected $priceCurrency;
	protected $urlHelper;
	protected $_product;
	protected $_fullCategoryPathsMappingArray;
	protected $ConnectionCollectionFactory;
	protected $taxgimmickHelper;
	protected $imageHelper;
	protected $imageMediaUrl;
	
	
	protected $configHelper;
	protected $_moduleList;
	
	const MODULE_NAME = 'Adcurve_Adcurve';
	
	/**
	 * @var \Magento\Tax\Api\TaxCalculationInterface
	 */
	protected $taxCalculation;

	/**
	 * @var \Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfig;
	
	/**
     * @var Logger
     */
    protected $logger;
	
	protected $irrelevantAttributes = array(
		/** General attributes */
		'associated_product_ids',
		'category_ids',
		'configurable-matrix',
		'copy_to_stores',
		'created_at',
		'cross_sell_products',
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
        /** Downloadable product attributes */
        'downloadable_links',
        'downloadable_samples',
    );
	protected $exclude_these_attributes=array();
	
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Store\Model\Store $storeModel,
		\Adcurve\Adcurve\Model\UpdateFactory $updateFactory,
		\Adcurve\Adcurve\Model\UpdateRepository $updateRepository,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Magento\Catalog\Model\ProductRepository $productRepository,
		\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoriesCollection,
		\Magento\Catalog\Model\ResourceModel\Category $_categoryResource,
		\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $_configurableproductResource,
		\Magento\Catalog\Model\Product\Attribute\Repository $_attributeRepository,
	    \Magento\Catalog\Helper\Data $catalogHelper,
		ModuleListInterface $moduleList,
		//Product $productHelper,
		\Magento\Framework\Url $urlHelper,
		StoreManagerInterface $storeManager,
		\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
	    \Magento\Framework\Api\Search\FilterGroup $filterGroup,
	    \Magento\Framework\Api\FilterBuilder $filterBuilder,
	    \Magento\Catalog\Model\Product\Type\Price $priceModel,
	    \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
	    \Adcurve\Adcurve\Helper\Taxgimmick $taxgimmickHelper,
		\Adcurve\Adcurve\Helper\Config $configHelper,
	    \Magento\Catalog\Helper\Image $imageHelper,
		ConnectionCollectionFactory $connectionCollectionFactory,
		\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory,
		\Magento\Tax\Helper\Data $taxHelper,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Tax\Api\TaxCalculationInterface $taxCalculation,
		Configurable $configurable,
		Logger $logger
	){
		parent::__construct($context);
		
		$this->storeModel = $storeModel;
		$this->_storeManager = $storeManager;
		$this->updateFactory = $updateFactory;
		$this->updateRepository = $updateRepository;
		$this->dateTime = $dateTime;
		$this->connectionCollectionFactory = $connectionCollectionFactory;
		$this->productRepository = $productRepository;
		$this->_categoryResource = $_categoryResource;
		$this->_configurableproductResource = $_configurableproductResource;
		$this->_attributeRepository = $_attributeRepository;
		//$this->catalogHelper = $catalogHelper;
		$this->productHelper = $catalogHelper;
		$this->_attributeFactory = $attributeFactory;
		$this->_taxHelper = $taxHelper;
		$this->logger = $logger;
		$this->productFactory = $productFactory;
		
		//$this->productHelper = $productHelper;
		$this->searchCriteria = $searchCriteria;
	    $this->filterGroup = $filterGroup;
	    $this->filterBuilder = $filterBuilder;
		$this->_categoriesCollection = $categoriesCollection;     
		$this->priceModel = $priceModel;
		$this->priceCurrency = $priceCurrency;
		$this->urlHelper = $urlHelper;
		$this->taxgimmickHelper = $taxgimmickHelper;
		$this->imageHelper = $imageHelper;
		$this->_configurable=$configurable;
		$this->configHelper=$configHelper;
		$this->_moduleList = $moduleList;
		$this->scopeConfig = $scopeConfig;
		$this->taxCalculation = $taxCalculation;
		
	}
	
	public function getProductData($product = null, $storeId)
	{
		if (!$product) {
			return false;
		}
		
		if (!$product->getSku() || $product->getSku() == '') {
			return false;
		}
		
		$this->storeModel->setId($storeId);
		
		$this->_product = $this->productRepository->get($product->getSku(), false, $storeId);
		
		
		$this->productData = [];
		//$allProductAttributes = $this->_product->getData();
		
		
	   $attributeInfo = $this->_attributeFactory->getCollection();
	   $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	   foreach ($attributeInfo as $attributes)
	   {
			$attributeId = $attributes->getAttributeId();
			$attributeCode = $attributes->getAttributeCode();
			$product =$this->_product;
			$_attributeValue = $product->getData("$attributeCode");
			
			//$_attributeValue =$product->getResource()->getAttribute($attributeCode)->getFrontend()->getValue($product);
			
			if (!is_array($_attributeValue)) {
				//$this->logger->addInfo($attributeCode."::".$_attributeValue);
				$this->productData[$attributeCode]=$_attributeValue;
			}
			
			
	   }

		$this->productData['plugin_version'] = $this->getVersion();
		$this->productData['store_id'] = $storeId;
		$this->productData['entity_id'] = $this->_product->getId();
		$this->productData['simple_id'] = $this->_product->getId();
		
		
		/***********EXCLDE ATTRIBUTES*********/
		$collection = $this->connectionCollectionFactory->create()
		->addFieldToFilter('store_id', array('eq' => $storeId));
		$exclude=array();
		foreach ($collection as $update) {
			
			$exclude=json_decode($update->get_excluded_attributes());
		}

		$this->exclude_these_attributes=$exclude;
		/*******************/
		
		$this->_unsetIrrelevantAttributes();
	
		/** Get the labels of select and multiselect attributes */
		$this->_transposeSelectAttributes();
		
		/**
		 * function _addLogicAttributes()
		 * Adds attributes which need logic to retrieve
		 * attributes: [enabled, configurable_id, category_path, deeplink, currency]
		 */
		 
		 
		
		//$this->_addLogicAttributes();

		
		$this->_addImageAttributes();
		
		$this->_addPriceAttributes();
		
		$this->_addStockAttributes();
		
		$storeId=$this->productData['store_id'];
		$this->_addLogicComplexAttributes($this->_product,$storeId,false);

		ksort($this->productData);
		
		if (count($this->productData) < 1) {
            return false;
        }
		
		return $this->productData;
	}
	public function getVersion()
    {
        return $this->_moduleList
            ->getOne(self::MODULE_NAME)['setup_version'];
    }
	public function saveUpdateForAdcurveProductDelete($preparedData, $status = \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_UPDATE)
	{
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$entity_id=$preparedData["entity_id"];
		$storeId=$preparedData["store_id"];
		
		
		$product = $this->productFactory->create()->load($entity_id);
		//$product = $this->productRepository->getById($entity_id, false, $storeId);
		
		
		$helper=$this;
		
        if (!$helper->isProductValidForExport($product)) {
            // If product is not valid for export, skip 
            return $this;
        }

        try {
           
                if ($storeId == 0) {
                    false;
                }
                //Extra validation to ensure store id filter
                if (!in_array($storeId, $product->getStoreIds())) {
                    //continue;
                }

                //if (!Adcurve_Adcurve_Helper_Config::isApiConfigured($storeId)) {
                    /** If the api is not configured, don't process this storeview */
                    //continue;
               // }

			   
			  
                /*Check the type of the product*/
                if ($product->getTypeId() != "configurable" &&
                    $product->getTypeId() != "bundle" && 
                    $product->getTypeId() != "grouped") {
            
                    /* simpleId is a product id*/
                    $simpleId = $product->getId();

                    $configurableId = "";
                    $configurableIds = array();
                    $bundleId = "";
                    $bundleIds = array();
                    $groupedId = "";
                    $groupedIds = array();
                    
                    if ($product->getTypeId() == "simple" ) {
						
						
						
                        /* check if it is a simple product and child of any configurable product */
                        //$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
                        $parentIds = $this->_configurableproductResource->getParentIdsByChild($product->getId());
						
						
						// check if this already 
                        if (count($parentIds) > 0) {
                            $configurableIds = $parentIds;
                        }
                        /* check if it is a simple product and child of any bundle product */
                        //$parentIds = Mage::getResourceSingleton('bundle/selection')->getParentIdsByChild($product->getId());
                        
						//$typeInstance = $_objectManager->get('Magento\Bundle\Model\Product\Type');
						$typeInstance = $objectManager->get('Magento\Bundle\Model\ResourceModel\Selection');
						$parentIds = $typeInstance->getParentIdsByChild($product->getId());
						
						
						if (count($parentIds) > 0) {                                
                            $bundleIds = $parentIds;
                        }
                        /* check if it is a simple product and child of any grouped product */
                        //$parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
                        $typeInstance = $objectManager->get('Magento\GroupedProduct\Model\Product\Type\Grouped');
						$parentIds = $typeInstance->getParentIdsByChild($product->getId());
		
						
						if (count($parentIds) == 0) {
                            $groupedIds = $parentIds;
                        }
                    } else {
                        /* Otherwise it is just  a simple product */
                        $configurableId = $simpleId;
                        $bundleId = $simpleId;
                        $groupedId = $simpleId;
                    }

                    /* check if it this product has not any configurable product parent */
                    if ($configurableId == $simpleId && $bundleId == $simpleId && $groupedId == $simpleId) {

                        $this->productData = array('entity_id' => $product->getId(),
                                            'name' => $product->getName(),
                                            'sku' => $product->getSku(),
                                            'enabled' => 'false',
                                            'store_id' => $storeId,
                                            'simple_id' => $simpleId,
                                            'configurable_id' => $configurableId,
                                            'bundle_id' => $bundleId,
                                            'grouped_id' => $groupedId);
						
                        $this->_sendProductToUpdate($this->productData);
                    } else {   


					
                        $this->productData = array('entity_id' => $product->getId(),
                                            'name' => $product->getName(),
                                            'sku' => $product->getSku(),
                                            'enabled' => 'false',
                                            'store_id' => $storeId,
                                            'simple_id' => $simpleId,
                                            'configurable_id' => $simpleId,
                                            'bundle_id' => $simpleId,
                                            'grouped_id' => $simpleId);

                        $this->_sendProductToUpdate($this->productData);
                    }

                    if (is_array($configurableIds) && count($configurableIds) > 0) {
                        /* if there are any configurable parent products*/

                        foreach ($configurableIds as $cId) {

                            $this->productData = array('entity_id' => $product->getId(),
                                                'name' => $product->getName(),
                                                'sku' => $product->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $simpleId,
                                                'configurable_id' => $cId,
                                                'bundle_id' => $simpleId,
                                                'grouped_id' => $simpleId);
                                            
                            $this->_sendProductToUpdate($this->productData);
                        }
                    }

                    if (is_array($bundleIds) && count($bundleIds) > 0) {
                        /* if there are any bundle parent products*/
                        foreach ($bundleIds as $bId) {

                            $this->productData = array('entity_id' => $product->getId(),
                                                'name' => $product->getName(),
                                                'sku' => $product->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $simpleId,
                                                'configurable_id' => $simpleId,
                                                'bundle_id' => $bId,
                                                'grouped_id' => $simpleId);
                                                
                            $this->_sendProductToUpdate($this->productData);
                        }
                    }

                    if (is_array($groupedIds) && count($groupedIds) > 0) {
                        /* if there are any grouped parent products*/
                        foreach ($groupedIds as $gId) {

                            $this->productData = array('entity_id' => $product->getId(),
                                                'name' => $product->getName(),
                                                'sku' => $product->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $simpleId,
                                                'configurable_id' => $simpleId,
                                                'bundle_id' => $simpleId,
                                                'grouped_id' => $gId);
                                            
                            $this->_sendProductToUpdate($this->productData);
                        }
                    }
                } else if ($product->getTypeId() == "configurable") {
                     
                    /* configurable_id is a product id*/
                    $configurableId = $product->getId();
                    //$simpleIds = array();
                    $simpleIds = $helper->getProductChild($product);
                   
                    /* Get child products id and such (only ids) */
                    //$childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product->getId());
                    
                    /* check if it has any child */
                    //if (count($childIds[0]) > 0) {
                        //$simpleIds = $childIds[0];
                    //}
                    
                    /*Sending Configurable product update for deletion*/
                    /*
                    $confProductData = array('entity_id' => $product->getId(),
                                                'name' => $product->getName(),
                                                'sku' => $product->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $product->getId(),
                                                'configurable_id' => $product->getId());
                    
                    $this->_sendProductToUpdate($confProductData);
                    */
                   
                    /*Sending update of simple child products of Configurable product for deletion*/
                    if (is_array($simpleIds) && count($simpleIds) > 0) {

					 
                        foreach ($simpleIds as $sId) {
                        
						
                            /** @var Mage_Catalog_Model_Product $sp */
                            //$sp = Mage::getModel('catalog/product')->load($sId);
                            //$sp = $this->productRepository->getById($sId, false, $storeId);
                            $sp = $this->productFactory->create()->load($sId);
                            $this->productData = array('entity_id' => $sp->getId(),
                                                'name' => $sp->getName(),
                                                'sku' => $sp->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $sp->getId(),
                                                'configurable_id' => $configurableId,
                                                'bundle_id' => $sp->getId(),
                                                'grouped_id' => $sp->getId());
                           
                            $this->_sendProductToUpdate($this->productData);                    
                        }
                    }
                } else if ($product->getTypeId() == "bundle") {
                    
                    /* configurable_id is a product id*/
                    $bundleId = $product->getId();
                    //$simpleIds = array();
                    $simpleIds = $helper->getProductChild($product);
                                        
                    /*Sending update of simple child products of bundle product for deletion*/
                    if (is_array($simpleIds) && count($simpleIds) > 0) {

                        foreach ($simpleIds as $sId) {
                        
                            /** @var Mage_Catalog_Model_Product $sp */
                           // $sp = Mage::getModel('catalog/product')->load($sId);
                            //$sp = $this->productRepository->getById($sId, false, $storeId);
							$sp = $this->productFactory->create()->load($sId);
                            $this->productData = array('entity_id' => $sp->getId(),
                                                'name' => $sp->getName(),
                                                'sku' => $sp->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $sp->getId(),
                                                'configurable_id' => $sp->getId(),
                                                'bundle_id' => $bundleId,
                                                'grouped_id' => $sp->getId());
                       
                            $this->_sendProductToUpdate($this->productData);                    
                        }
                    }
                } else if ($product->getTypeId() == "grouped") {
                    
                    /* configurable_id is a product id*/
                    $groupedId = $product->getId();
                    //$simpleIds = array();
                    $simpleIds = $helper->getProductChild($product);
                                        
                    /*Sending update of simple child products of bundle product for deletion*/
                    if (is_array($simpleIds) && count($simpleIds) > 0) {

                        foreach ($simpleIds as $sId) {
                        
                            /** @var Mage_Catalog_Model_Product $sp */
                            //$sp = Mage::getModel('catalog/product')->load($sId);
                            //$sp = $this->productRepository->getById($sId, false, $storeId);
							$sp = $this->productFactory->create()->load($sId);
                            $this->productData= array('entity_id' => $sp->getId(),
                                                'name' => $sp->getName(),
                                                'sku' => $sp->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $sp->getId(),
                                                'configurable_id' => $sp->getId(),
                                                'bundle_id' => $sp->getId(),
                                                'grouped_id' => $groupedId);
                            
                            $this->_sendProductToUpdate($this->productData);                    
                        }
                    }
                }
                   
                
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
		

	}
	
	public function saveUpdateForAdcurve($preparedData, $status = \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_UPDATE)
	{
		
		return false;
		exit;
		
		
		if (!$preparedData) {
			return false;
		}
		
		if (!isset($preparedData['entity_id']) || !isset($preparedData['store_id'])) {
			return false;
		}
		
		try {
			
			$update = $this->updateFactory->create();
			$update->setProductId($preparedData['entity_id']);
			$update->setStoreId($preparedData['store_id']);
			$update->setProductData($preparedData);
			$currentDateTime = $this->dateTime->gmtDate();
			$update->setCreatedAt($currentDateTime);
			$update->setUpdatedAt($currentDateTime);
			$update->setStatus($status);

			$this->updateRepository->save($update);
			
		} catch(\Exception $e){
			$this->_logger->addError($e);
		}
		
	}
	public function saveUpdateForAdcurve_org($preparedData, $status = \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_UPDATE)
	{
		
		if (!$preparedData) {
			return false;
		}
		
		if (!isset($preparedData['entity_id']) || !isset($preparedData['store_id'])) {
			return false;
		}
		
		try {
			
			/*
			$update = $this->updateFactory->create();
			$update->setProductId($preparedData['entity_id']);
			$update->setStoreId($preparedData['store_id']);
			$update->setProductData($preparedData);
			$currentDateTime = $this->dateTime->gmtDate();
			$update->setCreatedAt($currentDateTime);
			$update->setUpdatedAt($currentDateTime);
			$update->setStatus($status);

			$this->updateRepository->save($update);
			*/
			
			$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
				/*Check the type of the product*/
                if ($product->getTypeId() != "configurable" &&
                    $product->getTypeId() != "bundle" && 
                    $product->getTypeId() != "grouped") {
            
                    /* simpleId is a product id*/
                    $simpleId = $product->getId();

                    $configurableId = "";
                    $configurableIds = array();
                    $bundleId = "";
                    $bundleIds = array();
                    $groupedId = "";
                    $groupedIds = array();
                    
                    if ($product->getTypeId() == "simple" ) {
                        /* check if it is a simple product and child of any configurable product */
                        //$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
                        $parentIds = $this->_configurableproductResource->getParentIdsByChild($product->getId());
		
						// check if this already 
                        if (count($parentIds) > 0) {
                            $configurableIds = $parentIds;
                        }
                        /* check if it is a simple product and child of any bundle product */
                        //$parentIds = Mage::getResourceSingleton('bundle/selection')->getParentIdsByChild($product->getId());
                        
						$typeInstance = $_objectManager->get('Magento\Bundle\Model\Product\Type');
						$parentIds = $typeInstance->getParentIdsByChild($product->getId());
						
						
						if (count($parentIds) > 0) {                                
                            $bundleIds = $parentIds;
                        }
                        /* check if it is a simple product and child of any grouped product */
                        //$parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
                        $typeInstance = $_objectManager->get('Magento\GroupedProduct\Model\Product\Type\Grouped');
						$parentIds = $typeInstance->getParentIdsByChild($product->getId());
		
						
						if (count($parentIds) == 0) {
                            $groupedIds = $parentIds;
                        }
                    } else {
                        /* Otherwise it is just  a simple product */
                        $configurableId = $simpleId;
                        $bundleId = $simpleId;
                        $groupedId = $simpleId;
                    }

                    /* check if it this product has not any configurable product parent */
                    if ($configurableId == $simpleId && $bundleId == $simpleId && $groupedId == $simpleId) {

                        $productData = array('entity_id' => $product->getId(),
                                            'name' => $product->getName(),
                                            'sku' => $product->getSku(),
                                            'enabled' => 'false',
                                            'store_id' => $storeId,
                                            'simple_id' => $simpleId,
                                            'configurable_id' => $configurableId,
                                            'bundle_id' => $bundleId,
                                            'grouped_id' => $groupedId);

                        $this->_sendProductToUpdate($productData);
                    } else {                        
                        $productData = array('entity_id' => $product->getId(),
                                            'name' => $product->getName(),
                                            'sku' => $product->getSku(),
                                            'enabled' => 'false',
                                            'store_id' => $storeId,
                                            'simple_id' => $simpleId,
                                            'configurable_id' => $simpleId,
                                            'bundle_id' => $simpleId,
                                            'grouped_id' => $simpleId);

                        $this->_sendProductToUpdate($productData);
                    }

                    if (is_array($configurableIds) && count($configurableIds) > 0) {
                        /* if there are any configurable parent products*/

                        foreach ($configurableIds as $cId) {

                            $productData = array('entity_id' => $product->getId(),
                                                'name' => $product->getName(),
                                                'sku' => $product->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $simpleId,
                                                'configurable_id' => $cId,
                                                'bundle_id' => $simpleId,
                                                'grouped_id' => $simpleId);
                                                
                            $this->_sendProductToUpdate($productData);
                        }
                    }

                    if (is_array($bundleIds) && count($bundleIds) > 0) {
                        /* if there are any bundle parent products*/
                        foreach ($bundleIds as $bId) {

                            $productData = array('entity_id' => $product->getId(),
                                                'name' => $product->getName(),
                                                'sku' => $product->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $simpleId,
                                                'configurable_id' => $simpleId,
                                                'bundle_id' => $bId,
                                                'grouped_id' => $simpleId);
                                                
                            $this->_sendProductToUpdate($productData);
                        }
                    }

                    if (is_array($groupedIds) && count($groupedIds) > 0) {
                        /* if there are any grouped parent products*/
                        foreach ($groupedIds as $gId) {

                            $productData = array('entity_id' => $product->getId(),
                                                'name' => $product->getName(),
                                                'sku' => $product->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $simpleId,
                                                'configurable_id' => $simpleId,
                                                'bundle_id' => $simpleId,
                                                'grouped_id' => $gId);
                                                
                            $this->_sendProductToUpdate($productData);
                        }
                    }
                } else if ($product->getTypeId() == "configurable") {
                    
                    /* configurable_id is a product id*/
                    $configurableId = $product->getId();
                    //$simpleIds = array();
                    $simpleIds = $helper->getProductChild($product);
                    
                    /* Get child products id and such (only ids) */
                    //$childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product->getId());
                    
                    /* check if it has any child */
                    if (count($childIds[0]) > 0) {
                        //$simpleIds = $childIds[0];
                    }
                    
                    /*Sending Configurable product update for deletion*/
                    /*
                    $confProductData = array('entity_id' => $product->getId(),
                                                'name' => $product->getName(),
                                                'sku' => $product->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $product->getId(),
                                                'configurable_id' => $product->getId());
                    
                    $this->_sendProductToUpdate($confProductData);
                    */
                    
                    /*Sending update of simple child products of Configurable product for deletion*/
                    if (is_array($simpleIds) && count($simpleIds) > 0) {

                        foreach ($simpleIds as $sId) {
                        
                            /** @var Mage_Catalog_Model_Product $sp */
                            //$sp = Mage::getModel('catalog/product')->load($sId);
                            $sp = $this->productRepository->getById($sId);
                            $productData = array('entity_id' => $sp->getId(),
                                                'name' => $sp->getName(),
                                                'sku' => $sp->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $sp->getId(),
                                                'configurable_id' => $configurableId,
                                                'bundle_id' => $sp->getId(),
                                                'grouped_id' => $sp->getId());
                            
                            $this->_sendProductToUpdate($productData);                    
                        }
                    }
                } else if ($product->getTypeId() == "bundle") {
                    
                    /* configurable_id is a product id*/
                    $bundleId = $product->getId();
                    //$simpleIds = array();
                    $simpleIds = $helper->getProductChild($product);
                                        
                    /*Sending update of simple child products of bundle product for deletion*/
                    if (is_array($simpleIds) && count($simpleIds) > 0) {

                        foreach ($simpleIds as $sId) {
                        
                            /** @var Mage_Catalog_Model_Product $sp */
                            //$sp = Mage::getModel('catalog/product')->load($sId);
                            $sp = $this->productRepository->getById($sId);
                            $productData = array('entity_id' => $sp->getId(),
                                                'name' => $sp->getName(),
                                                'sku' => $sp->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $sp->getId(),
                                                'configurable_id' => $sp->getId(),
                                                'bundle_id' => $bundleId,
                                                'grouped_id' => $sp->getId());
                            
                            $this->_sendProductToUpdate($productData);                    
                        }
                    }
                } else if ($product->getTypeId() == "grouped") {
                    
                    /* configurable_id is a product id*/
                    $groupedId = $product->getId();
                    //$simpleIds = array();
                    $simpleIds = $helper->getProductChild($product);
                                        
                    /*Sending update of simple child products of bundle product for deletion*/
                    if (is_array($simpleIds) && count($simpleIds) > 0) {

                        foreach ($simpleIds as $sId) {
                        
                            /** @var Mage_Catalog_Model_Product $sp */
                            //$sp = Mage::getModel('catalog/product')->load($sId);
                            $sp = $this->productRepository->getById($sId);
                            $productData = array('entity_id' => $sp->getId(),
                                                'name' => $sp->getName(),
                                                'sku' => $sp->getSku(),
                                                'enabled' => 'false',
                                                'store_id' => $storeId,
                                                'simple_id' => $sp->getId(),
                                                'configurable_id' => $sp->getId(),
                                                'bundle_id' => $sp->getId(),
                                                'grouped_id' => $groupedId);
                            
                            $this->_sendProductToUpdate($productData);                    
                        }
                    }
                }
			
			
			
			
			
			
		} catch(\Exception $e){
			$this->_logger->addError($e);
		}
		
		
	}
	
	private function _unsetIrrelevantAttributes()
	{
		foreach ($this->irrelevantAttributes as $attribute) {
			if (isset($this->productData[$attribute])) {
				unset($this->productData[$attribute]);
			}
		}
		
		foreach ($this->exclude_these_attributes as $attribute) {
			if (isset($this->productData[$attribute])) {
				unset($this->productData[$attribute]);
			}
			
		}
		
		
		/** Unset all "_cache_instance" attributetypes */
		foreach ($this->productData as $key => $value) {
			if (strpos($key, '_cache_instance') == (int)0) {
				//unset($this->productData[$key]);
			}
		}
		
		/*if (isset($this->productData['extension_attributes'])) {
			 TO DO: Complete extensions attribute logic
			var_dump(get_class($this->productData['extension_attributes']));
			var_dump(get_class_methods($this->productData['extension_attributes']));
			if (isset($extensionAttributes['_data']) && !empty($extensionAttributes['_data'])) {
				foreach ($this->productData['extension_attributes']->getData() as $key => $attribute) {
					var_dump($key);
					var_dump($attribute);
					if (isset($this->productData[$key])) {
						$this->productData[$key] = $attribute;
					}
				}
			}
			
			unset($this->productData['extension_attributes']);
		}*/
		
		
	}
	
	/**
	 * Function to get option label's instead of value id's for select and multiselect attributes
	 */
	private function _transposeSelectAttributes($implodeMultiselect = true)
	{
		foreach ($this->productData as $attributeCode => $value) {
			$attribute = $this->_product->getResource()->getAttribute($attributeCode);
			
			if (!$attribute
				|| $attribute == (bool)false
				|| !is_object($attribute)
			) {
				continue;
			}
			
			if (empty($value) || is_array($value)) {
				continue;
			}
			
			$selectAttributes = ['select', 'multiselect'];
			if (in_array($attribute->getFrontend()->getInputType(), $selectAttributes)) {
				$optionText = $attribute->getSource()->getOptionText($value);
				if ($optionText instanceof \Magento\Framework\Phrase) {
					$optionText = $optionText->getText();
				}
				
				if ($implodeMultiselect 
					&& $attribute->getFrontend()->getInputType() == 'multiselect'
					&& is_array($optionText)
				) {
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
		if ($productType == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
			&& isset($parentIds[0])
			&& $parentIds[0] != $productId
		) {
			return $parentIds[0];
		} else{
			return $productId;
		}
	}
	
	    /**
     * @param $product
     *
     * @return string
     */
    public function getCategoryPath($product)
    {
        if (is_numeric($product)) {
            //$product = Mage::getModel('catalog/product')->load($product);
			$product = $this->productRepository->getById($product);
		
        }

        if (!($product instanceof \Magento\Catalog\Model\Product\Type\AbstractType) || !$product->getId()) {
            //new \Magento\Framework\Exception\LocalizedException('$product is not of type \Magento\Catalog\Model\Product\Type\AbstractType or product could not be loaded');
        
		
		}

        $categoryIds = $product->getCategoryIds();
        
        $catIds = array();
        if (count($categoryIds)>1) {
            asort($categoryIds);
            //$catIds[0] = $categoryIds[0];
        } else {
            //$catIds = $categoryIds;
        }
        
        $pathIds = $this->_getPathIds($categoryIds);
        return $this->_buildPath($pathIds);
    }
	
	 /**
     * @param Mage_Catalog_Model_Product $product
     * @param null                       $storeId
     * @param int                        $parentId
     *
     * @return string
     */
    public function getParentProductUrl(\Magento\Catalog\Model\Product $product, $storeId = null, $parentId)
    {
        if (is_null($storeId)) {
            //$storeId = Mage::app()->getDefaultStoreView()->getId();
			$storeId = $this->_storeManager->getDefaultStoreView()->getStoreId();
        }
        
        $urlPath = "";
        
        /** @var Mage_Catalog_Helper_Product $productHelper */
        $productHelper = $this->productHelper;
        //$suffix = $productHelper->getProductUrlSuffix($storeId);
        $suffix = ".html";
		
		
		$suffixLengh = strlen($suffix);
        
        if ($product->getTypeId() =="simple"  && $product->setStoreId($storeId)->getVisibility() == \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE && $parentId > 0) {
            
            //$pProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($parentId);
            $pProduct = $this->productRepository->getById($parentId, false, $storeId);
		
            if ($pProduct->getData('url_path') == '') {
                $urlPath = $pProduct->formatUrlKey($pProduct->getData('name'));
            } else {
                $urlPath = $pProduct->getData('url_path');
                
                if ($suffixLengh > 0) {
                    $urlPath = substr($urlPath, 0, -$suffixLengh);
                }
            }
        }
        
        if ($urlPath == "") {

            if ($product->setStoreId($storeId)->getData('url_path') == '') {
                $urlPath = $product->setStoreId($storeId)->formatUrlKey($product->setStoreId($storeId)->getData('name'));
            } else {
                $urlPath = $product->setStoreId($storeId)->getData('url_path');
                
                if ($suffixLengh > 0) {
                    $urlPath = substr($urlPath, 0, -$suffixLengh);
                }
            }
        }
                
        //return Mage::getUrl($urlPath . $suffix, array('_store' => $storeId, '_nosid' => true));
		$this->urlHelper->getUrl( $urlPath . $suffix, [ '_scope' => $storeId, '_nosid' => true ]);
	
	}
	/**
     * @param $categoryIds
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    protected function _getCategoryCollection($categoryIds)
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
       
		$collection = $this->_categoriesCollection->create();
		$collection->addAttributeToSelect('name')
		->addIdFilter($categoryIds)
        ->addFieldToFilter('level', array('gt' => 1))
        ->addAttributeToSort('id', 'ASC');
		return $collection;
    }
	
	 /**
     * @param $categoryIds
     *
     * @return array
     */
    protected function _getPathIds($categoryIds)
    {
        $collection = $this->_getCategoryCollection($categoryIds);
		

        $path = array();
        foreach ($categoryIds as $categoryId) {
            /** @var Mage_Catalog_Model_Category $category */
            $category = $collection->getItemById($categoryId);
            if (null == $category) {
                continue;
            }
            if (count($category->getPathIds()) > count($path)) {
                $path = $category->getPathIds();
            }
        }

        return $path;
    }

    /**
     * @param $pathIds
     *
     * @return string
     */
    protected function _buildPath($pathIds)
    {
        $collection = $this->_getCategoryCollection($pathIds);
        $path = array();
        foreach ($pathIds as $pathId) {
            /** @var Mage_Catalog_Model_Category $category */
            $category = $collection->getItemById($pathId);
            if (null == $category) {
                continue;
            }

            $path[] = $category->getName();
        }

        $pathString = implode('>', $path);

        if (empty($pathString)) {
            $pathString = 'unknown';
        }

        return $pathString;
    }
	
	private function _addLogicComplexAttributes($product, $storeId, $productHasOrigData)
	{
		
		
		$newProductData = $this->getNewProductData($product, $storeId, $productHasOrigData);
        
		
		
		if (!$this->validateData($newProductData)) {
            /** If the new data is not valid, skip */
            return $this;
        }
		
		
		if ($product->getTypeId() =="simple") {
			
			
			
			
                // sending multiple calls to API if a simple product exist in multiple configurable, bundle or grouped products
                if ((isset($newProductData['configurable_id']) && $newProductData['configurable_id'] == $newProductData['simple_id']) && 
                    (isset($newProductData['bundle_id']) && $newProductData['bundle_id'] == $newProductData['simple_id']) &&
                    (isset($newProductData['grouped_id']) && $newProductData['grouped_id'] == $newProductData['simple_id'])) {
                    
                    $this->productData['entity_id'] = $newProductData['simple_id'];
                    $this->productData['configurable_name'] = $newProductData['name'];
                    $this->productData['bundle_name'] = $newProductData['name'];
                    $this->productData['grouped_name'] = $newProductData['name'];
                    $this->productData['configurable_category_path'] = $newProductData['category_path'];
					
					
					$this->_sendProductToUpdate($newProductData['simple_id'], $storeId, $this->productData);
                     
                } else {
                    $configurableIds = $newProductData['configurable_id'];
                    $bundleIds = $newProductData['bundle_id'];
                    $groupedIds = $newProductData['grouped_id'];
					
                    if (is_array($configurableIds) && count($configurableIds) > 0) {
                    
                        foreach ($configurableIds as $cId) {
                            //$cProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($cId);
                            $cProduct = $this->productRepository->getById($cId, false, $storeId);
		
							if (!in_array($storeId, $cProduct->getStoreIds())) {
                                /** If the product doesn't belong to this store, skip */
                                continue;
                            }
                            $this->productData['configurable_id'] = $cId;
                            $this->productData['bundle_id'] = $newProductData['simple_id'];
                            $this->productData['grouped_id'] = $newProductData['simple_id'];
                            $this->productData['configurable_name'] = $cProduct->getName();
                            $this->productData['bundle_name'] = $newProductData['name'];
                            $this->productData['grouped_name'] = $newProductData['name'];
                            $this->productData['configurable_category_path'] = $this->getCategoryPath($cProduct);
                            $this->productData['deeplink'] = $this->getParentProductUrl($product, $storeId, $cId);
                            $this->productData['entity_id'] = $newProductData['simple_id'];
							
							
							
							$this->_sendProductToUpdate($newProductData['simple_id'], $storeId, $this->productData);
                     
						
						}
                    }

                    if (is_array($bundleIds) && count($bundleIds) > 0) {
                        foreach ($bundleIds as $cId) {
                            //$cProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($cId);
							$cProduct = $this->productRepository->getById($cId, false, $storeId);
                            if (!in_array($storeId, $cProduct->getStoreIds())) {
                                /** If the product doesn't belong to this store, skip */
                                continue;
                            }
                            $this->productData['configurable_id'] = $newProductData['simple_id'];
                            $this->productData['bundle_id'] = $cId;
                            $this->productData['grouped_id'] = $newProductData['simple_id'];
                            $this->productData['configurable_name'] = $newProductData['name'];
                            $this->productData['bundle_name'] = $cProduct->getName();
                            $this->productData['grouped_name'] = $newProductData['name'];
                            //$newProductData['configurable_category_path'] = $this->getCategoryPath($cProduct);
                            $this->productData['configurable_category_path'] = $newProductData['category_path'];
                            $this->productData['deeplink'] = $this->getParentProductUrl($product, $storeId, $cId);
                            $this->productData['entity_id'] = $newProductData['simple_id'];
                     
							$this->_sendProductToUpdate($newProductData['simple_id'], $storeId, $this->productData);
                     
						}
                    }
                    
                    if (is_array($groupedIds) && count($groupedIds) > 0) {
                        foreach ($groupedIds as $cId) {
                            //$cProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($cId);
							$cProduct = $this->productRepository->getById($cId, false, $storeId);
                            if (!in_array($storeId, $cProduct->getStoreIds())) {
                                /** If the product doesn't belong to this store, skip */
                                continue;
                            }
                            $this->productData['configurable_id'] = $newProductData['simple_id'];
                            $this->productData['bundle_id'] = $newProductData['simple_id'];
                            $this->productData['grouped_id'] = $cId;
                            $this->productData['configurable_name'] = $newProductData['name'];
                            $this->productData['bundle_name'] = $newProductData['name'];
                            $this->productData['grouped_name'] = $cProduct->getName();
                            //$newProductData['configurable_category_path'] = $this->getCategoryPath($cProduct);
                            $this->productData['configurable_category_path'] = $newProductData['category_path'];
                            $this->productData['deeplink'] = $this->getParentProductUrl($product, $storeId, $cId);
                            $this->productData['entity_id'] = $newProductData['simple_id'];
						
							
							$this->_sendProductToUpdate($newProductData['simple_id'], $storeId, $this->productData);
                     
						 }
                    }
                }
            } elseif ( 
                $product->getTypeId() == "configurable" ||
                $product->getTypeId() == "bundle" ||
                $product->getTypeId() == "grouped" ) {
            
                $childIds = $this->getProductChild($product);

                if (count($childIds) > 0) {
                    $parentId = $product->getId();
                    $parentName = $product->getName();
                    $sendForDel = array();
                    foreach ($childIds as $cId) {
                    
                        //$childProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($cId);
						$childProduct = $this->productRepository->getById($cId, false, $storeId);
                        if (!in_array($storeId, $childProduct->getStoreIds())) {
                            /** If the product doesn't belong to this store, skip */
                            continue;
                        }

                        $newProductData = $this->getNewProductData($childProduct, $storeId, $productHasOrigData);
                        if (!$this->validateData($newProductData)) {
                            /** If the new data is not valid, skip */
                            continue;
                        }
                        
                        if ($product->getTypeId() == "configurable" ) {
							
							
                            /** Unset configurable_id array value and assign single to create multiple call for each configurable_id */

						    unset($this->productData['configurable_id']);
                            $this->productData['configurable_id'] = $parentId;                            

							$this->productData['bundle_id'] = $cId;
                            $this->productData['grouped_id'] = $cId;
                            $this->productData['configurable_name'] = $parentName;
							if (isset($newProductData['name']))
								$this->productData['bundle_name'] = $newProductData['name'];
							else
								$this->productData['bundle_name'] = "";
							if (isset($newProductData['name']))
								$this->productData['grouped_name'] = $newProductData['name'];
							else
								$this->productData['grouped_name'] = "";
                            $this->productData['configurable_category_path'] = $this->getCategoryPath($product);
                            /** add simple child products of configurable for delete call - duplication issue */
                            $sendForDel[] = $cId;
							
                        } else if ($product->getTypeId() == "bundle" ) {
                            /** Unset bundle_id array value and assign single to create multiple call for each bundle_id */
                            $this->productData['configurable_id'] = $cId;                            
                            unset($this->productData['bundle_id']);
                            $this->productData['bundle_id'] = $parentId;
                            $this->productData['grouped_id'] = $cId;
                            $this->productData['configurable_name'] = $newProductData['name'];
                            $this->productData['bundle_name'] = $parentName;
                            $this->productData['grouped_name'] = $newProductData['name'];
                            //$newProductData['configurable_category_path'] = $this->getCategoryPath($product);
                            $this->productData['configurable_category_path'] = $newProductData['category_path'];
                            /** add simple child products of bundle for delete call - duplication issue */
                            $sendForDel[] = $cId;
                        } else if ($product->getTypeId() == "grouped") {
                            /** Unset grouped_id array value and assign single to create multiple call for each grouped_id */
                            $this->productData['configurable_id'] = $cId;
                            $this->productData['bundle_id'] = $cId;
                            unset($this->productData['grouped_id']);
                            $this->productData['grouped_id'] = $parentId;
                            $this->productData['configurable_name'] = $newProductData['name'];
                            $this->productData['bundle_name'] = $newProductData['name'];
                            $this->productData['grouped_name'] = $parentName;
                            //$newProductData['configurable_category_path'] = $this->getCategoryPath($product);
                            $this->productData['configurable_category_path'] = $newProductData['category_path'];
                            /** do not add simple child products of grouped for delete call*/
                        }

                        $this->productData['deeplink'] = $this->getParentProductUrl($childProduct, $storeId, $parentId);
                        $this->productData['entity_id'] = $cId;
                        
						
						
						$this->_sendProductToUpdate($newProductData['simple_id'], $storeId, $this->productData);
                     
						
                    }
                 
                    if (count($sendForDel) > 0) {
						
						
                        // ROLL-BACK: issue #248 -'deleting' duplicated items
                        // $this->_sendChildProductToDelete($parentId, $storeId, $sendForDel);
                    }
                }
        }
		
	}

	private function _addImageAttributes()
	{
		$mediaGalleryImages = $this->_product->getMediaGalleryImages();
		
		if ($mediaGalleryImages->getSize() > 0) {
			$imageCount = 1;
			foreach ($mediaGalleryImages as $image) {
				$imageKey = 'image_' . $imageCount;
				$this->productData[$imageKey] = $image->getUrl();
				$imageCount++;
			}
		}
		
		// TO DO: Get all images with image helper/factory
		$this->imageMediaUrl = $this->storeModel->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
		$imageKeys = ['image', 'small_image', 'thumbnail'];
		
		foreach ($imageKeys as $imageKey) {
			if (isset($this->productData[$imageKey])
				&& $this->productData[$imageKey] != 'no_selection'
				&& $this->productData[$imageKey] != false
			) {
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
		
		if ($priceAttributes) {
			foreach ($priceAttributes as $priceAttribute) {
				if (isset($this->productData[$priceAttribute->getAttributeCode()])) {
					$this->productData[$priceAttribute->getAttributeCode()] = $this->priceCurrency->convert($this->productData[$priceAttribute->getAttributeCode()]);
				}
			}
		}
		
		if (!isset($this->productData['price'])) {
			$this->productData['price'] = '';
		}
		
		if (!isset($this->productData['special_price'])) {
			$this->productData['special_price'] = '';
		}
		
		if (!isset($this->productData['special_from_date'])) {
			$this->productData['special_from_date'] = '';
		}
		
		if (!isset($this->productData['special_to_date'])) {
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
		
		if ($priceInclTax) {
			$this->productData['price'] = number_format($priceInclTax, 4, '.', '');
		}
		
		if ($finalPriceInclTax) {
	        $this->productData['selling_price'] = number_format($finalPriceInclTax, 4, '.', '');
		}
		
		if ($finalPriceExclTax) {
	        $this->productData['selling_price_ex'] = number_format($finalPriceExclTax, 4, '.', '');
		}
	}
	
	private function _addStockAttributes()
	{
		$stockItem = $this->_product->getExtensionAttributes()->getStockItem();
		
		if ($stockItem) {
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
		if (!$this->_product) {
			return false;
		}
		
		$categoryCollection = $this->_product->getCategoryCollection()->addAttributeToSelect('path');
		$categories = array();
		foreach ($categoryCollection as $category) {
			if (!isset($this->_fullCategoryPathsMappingArray[$category->getPath()])) {
				$fullCategoryName = '';
				$categoryIds = explode('/', $category->getPath());
				for ($i=0; $i < $shiftAmount; $i++) {
					/** Shift off categories (such as Magento root, website specific or generic names) */
					array_shift($categoryIds);
				}
				if (count($categoryIds) > 0) {
					$i = 0;
					foreach ($categoryIds as $categoryId) {
						$categoryName = $this->_categoryResource->getAttributeRawValue($categoryId, 'name', $this->_product->getStoreId());
						if ($categoryName) {
							if ($i > 0) {
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
			if (isset($this->_fullCategoryPathsMappingArray[$category->getPath()])
				&& $this->_fullCategoryPathsMappingArray[$category->getPath()] != ''
			) {
				$categories[] = $this->_fullCategoryPathsMappingArray[$category->getPath()];
			}
		}
		return $categories;
	}
	/**
     * @param $product
     *
     * @return bool
     */
    public function isProductValidForExport($product)
    {
        if ($product->getTypeId() !="simple") {
           // return false;
        }

        return true;
    }
	
	/**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return array
     */
    public function getProductChild(\Magento\Catalog\Model\Product $product)
    {
        $childs = array();
        
        if ($product->getTypeId() == "configurable") {
                
            /* Get child products id(only ids) */
            //$childIds = Mage::getModel('catalog/product_type_configurable')->getChildrenIds($product->getId());
			$childIds = $this->_configurable->getChildrenIds($product->getId());
                        
            /* check if it has any child */
            if (count($childIds[0]) > 0) {
                $childs = $childIds[0];
            }
        } elseif ($product->getTypeId() == "bundle") {
                
            $collection = $product->getTypeInstance(true)
                                    ->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);
            if (count($collection) > 0) {
                foreach ($collection as $item) {
                    $childs[] = $item->getId();
                }
            }            
        } elseif ($product->getTypeId() == "grouped") {
                
            $childs = $product->getTypeInstance(true)->getAssociatedProductIds($product);
            if (count($childs) == 0) {
                $childs = array();
            }
        }

        return $childs;
    }
	/**
     * Get modified data from a product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param                            $storeId
     * @param                            $productHasOrigData
     *
     * @return mixed
     * @throws Mage_Core_Exception
     */
    public function getNewProductData(\Magento\Catalog\Model\Product $product, $storeId, $productHasOrigData)
    {
        /** Get the new and the original data */
        $data = $product->getData();
        $originalData = $product->getOrigData();
        if (is_null($originalData)) {
            $originalData = array();
        }
        /** Remove all unwanted attributes from the data */
        foreach ($this->irrelevantAttributes as $attr) {
            unset($data[$attr]);
            unset($originalData[$attr]);
        }
        /** Remove all attributes that aren't selected during registration as well */
        /** $selectedAttributes = Mage::getStoreConfig(Adcurve_Adcurve_Helper_Config::XPATH_SHOP_ATTRIBUTES, $storeId);
        $selectedAttributes = unserialize($selectedAttributes);
        $allAttributes = $this->getAllProductAttributes();
        if (is_array($selectedAttributes)) {        
            foreach ($allAttributes as $attribute) {            
                $currentAttributeCode = $attribute->getAttributecode();
                if (in_array($currentAttributeCode, $selectedAttributes)) {
                    continue;
                }
                unset($data[$currentAttributeCode]);
                unset($originalData[$currentAttributeCode]);
            }
        }
        */

        $productResource = $product->getResource();

        if ($product->isObjectNew() && !$productHasOrigData) {
            foreach ($data as $attributeKey => $value) {
                $attributeProduct = $product->getResource()->getAttribute($attributeKey);

                if ($attributeProduct) {
                    $attributeText = $attributeProduct->getSource()->getOptionText($value);

                    if (isset($attributeText) && $attributeText && strlen($attributeText) > 0) {
                        /** @noinspection PhpParamsInspection */
                        $data[$attributeKey] = $attributeText;
                    }
                }

                $attribute = $productResource->getAttribute($attributeKey);

                if ($attribute && $attribute->getFrontend()->getInputType() == 'select') {
                   $value = $attribute->getSource()->getOptionText($value);
                   $data[$attributeKey] = $value;
                }

                if ($attribute && $attribute->getFrontend()->getInputType() == 'multiselect') {
                    $value = $attribute->getSource()->getOptionText($value);

                    if (count($value) > 0) {
                        if (is_array($value)) {
                            $value = implode(",", $value);   
                        } else {
                            $value = (string)$value;
                        }
                    } else {
                        $value = "";
                    }
                }
            }
            /** If the product is new, add a few extra values */
            return $this->_modifyNewData($data, $product, $storeId);
        }
        /** @var Mage_Catalog_Model_Product $storeViewProduct */
        //$storeViewProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($product->getId());
		$storeViewProduct = $this->productRepository->getById($product->getId(), false, $storeId);
		
        foreach ($data as $key => $value) {
                $data[$key] = $storeViewProduct->getData($key);
        }
        /** @var Mage_Catalog_Model_Resource_Product $productResource */
        $productResource = $product->getResource();
        
        foreach ($data as $attributeKey => $value) {
            $attribute = $productResource->getAttribute($attributeKey);

            if ($attribute && $attribute->getFrontend()->getInputType() == 'select') {
                   $value = $attribute->getSource()->getOptionText($value);
            }

            if ($attribute && $attribute->getFrontend()->getInputType() == 'multiselect') {
                $value = $attribute->getSource()->getOptionText($value);

                if (count($value) > 0) {
                    if (is_array($value)) {
                        $value = implode(",", $value);   
                    } else {
                        $value = (string)$value;
                    }
                } else {
                    $value = "";
                }
            }

            $data[$attributeKey] = $value;
        }
        
        return $this->_modifyNewData($data, $product, $storeId);
    }
    
	/**
     * @param                            $data
     * @param Mage_Catalog_Model_Product $product
     * @param null                       $storeId
     *
     * @return mixed
     */
    protected function _modifyNewData($data, \Magento\Catalog\Model\Product $product, $storeId = null)
    {
        /** @var Adcurve_Adcurve_Helper_Config $configHelper*/
        //$configHelper = Mage::helper('adcurve_adcurve/config');
        //Exclude attributes  selected by admin 
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
        /*$toExclude = $this->configHelper->getExcludedAttributes($storeId);
        if (strlen($toExclude) > 0) {
            $attrToExclude = explode(",", $toExclude);
            foreach ($attrToExclude as $key) {
                //$data[$key] = "";
                unset($data[$key]);
            }
        }*/
		

		foreach ($this->exclude_these_attributes as $attribute) {
			if (isset($data[$attribute])) {
				unset($data[$attribute]);
			}	
		}
		

        $data['store_id']      = (int)$storeId;
        $data['deeplink']      = $this->getProductUrl($product, $storeId);
        $data['enabled']       = $this->getProductEnabled($product, $storeId);
        $data['category_path'] = $this->getCategoryPath($product);
        $data['simple_id']     = $product->getId();
        //$data['currency']      = Mage::app()->getStore($storeId)->getCurrentCurrencyCode();
		$data['currency']      = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        
        $data['image']         = $this->getProductImage($product);
        
        if (isset($data['small_image'])) {
            $data['small_image'] = ($data['small_image'] != "no_selection")? $data['small_image'] : "";
        } else {
            $data['small_image'] = "";
        }
        
        if (isset($data['thumbnail'])) {
            $data['thumbnail'] = ($data['thumbnail'] != "no_selection")? $data['thumbnail'] : "";
        } else {
            $data['thumbnail'] = "";
        }
        
        //$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
        $parentIds = $this->_configurable->getParentIdsByChild($product->getId());
        if ($product->getTypeId() == "simple" && count($parentIds) > 0) {
            $data['configurable_id'] = $parentIds;
        } else {
            $data['configurable_id'] = $data['simple_id'];
        }
        
        //$parentIds = Mage::getModel('bundle/product_type')->getParentIdsByChild($product->getId());
        
		$typeInstance = $_objectManager->get('Magento\Bundle\Model\Product\Type');
		$parentIds = $typeInstance->getParentIdsByChild($product->getId());
		
        if ($product->getTypeId() == "simple" && count($parentIds) > 0) {
            $data['bundle_id'] = $parentIds;
        } else {
            $data['bundle_id'] = $data['simple_id'];
        }

        //$parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
		$typeInstance = $_objectManager->get('Magento\GroupedProduct\Model\Product\Type\Grouped');
		$parentIds = $typeInstance->getParentIdsByChild($product->getId());
		
		
		
        if ($product->getTypeId() == "simple" && count($parentIds) > 0) {
            $data['grouped_id'] = $parentIds;
        } else {
            $data['grouped_id'] = $data['simple_id'];
        }

        $data = $this->addPriceAttributes($data, $product, $storeId);
		//_addPriceAttributes
        
        $productImages = $product->getMediaGalleryImages();

        if (!is_null($productImages) && $productImages->count()) {
            $imageCount = 1;

            foreach ($productImages as $productImage) {
                $imageKey = 'image_' . $imageCount;
                $data[$imageKey] = $productImage->getUrl();
                $imageCount++;
            }
        }
        
        /** @var Mage_CatalogInventory_Model_Stock_Item $stockItem */
        $stockItem = $product->getData('stock_item');

        if ($stockItem) {
            /** If a stockItems is set, update stock status and amount */
            $data['stock_status'] = $stockItem->getIsInStock();

            $qty =  $stockItem->getQty();
            if (is_null($qty)) {
                $qty = 0;
            }
            $data['stock_amount'] = $qty;
            $data['manage_stock'] = ($stockItem->getManageStock() == 1 )? "Yes":"No";
        }
        
        return $data;
    }
	
	 /**
     * @param Mage_Catalog_Model_Product $product
     * @param null                       $storeId
     *
     * @return string
     */
    public function getProductUrl(\Magento\Catalog\Model\Product $product, $storeId = null)
    {
		$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		
        if (is_null($storeId)) {
            //$storeId = Mage::app()->getDefaultStoreView()->getId();
			$storeId = $this->_storeManager->getStore()->getId();
        }
        
        $urlPath = "";
        
        /** @var Mage_Catalog_Helper_Product $productHelper */
        //$productHelper = Mage::helper('catalog/product');
		$productHelper=$this->productHelper;
		
        //$suffix = $productHelper->getProductUrlSuffix($storeId);
		$suffix =".html";
        $suffixLengh = strlen($suffix);
        
        //Check product visibility
        //$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
        
		$typeInstance = $_objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable');
		$parentIds =$typeInstance->getParentIdsByChild($product->getId());
		
        if (!$parentIds) {
            //$parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId());
        
			$typeInstance = $_objectManager->get('Magento\GroupedProduct\Model\Product\Type\Grouped');
			$parentIds =$typeInstance->getParentIdsByChild($product->getId());
		
		}
        
        if ($product->getTypeId() == "simple"  && $product->setStoreId($storeId)->getVisibility() == \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE && count($parentIds)>0) {
            
            //$pProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($parentIds[0]);
            $typeInstance = $_objectManager->get('\Magento\Catalog\Model\Product');
			$pProduct =$typeInstance->setStoreId($storeId)->load($parentIds[0]);
		
		
            if ($pProduct->getData('url_path') == '') {
                $urlPath = $pProduct->formatUrlKey($pProduct->getData('name'));
            } else {
                $urlPath = $pProduct->getData('url_path');
                
                if ($suffixLengh > 0) {
                    $urlPath = substr($urlPath, 0, -$suffixLengh);
                }
            }
        }
        
        if ($urlPath == ""){

            if ($product->setStoreId($storeId)->getData('url_path') == '') {
                $urlPath = $product->formatUrlKey($product->setStoreId($storeId)->getData('name'));
            } else {
                $urlPath = $product->setStoreId($storeId)->getData('url_path');
                
                if ($suffixLengh > 0) {
                    $urlPath = substr($urlPath, 0, -$suffixLengh);
                }
            }
        }
		
		//return Mage::getUrl($urlPath . $suffix, array('_store' => $storeId, '_nosid' => true));
		$return_url=$this->urlHelper->getUrl( $urlPath . $suffix, [ '_scope' => $storeId, '_nosid' => true ]);
		return $return_url;
        
    }
	/**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return bool
     */
    public function getProductEnabled(\Magento\Catalog\Model\Product $product, $storeId)
    {
        return (bool) ($product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) && in_array($storeId, $product->getStoreIds());
    }
	
	 /**
     * @param $data
     *
     * @return bool
     */
    public function validateData($data)
    {
        if (!is_array($data)) {
            return false;
        }

        unset($data['created_at']);
        unset($data['updated_at']);
        if (!count($data)) {
            return false;
        }

        return true;
    }
	
	/**
     * @param Mage_Catalog_Model_Product $product
     *
     * @return string
     */
    public function getProductImage(\Magento\Catalog\Model\Product $product)
    {
        /** @var Mage_Catalog_Helper_Image $imageHelper */
        //$imageHelper = Mage::helper('catalog/image');
		$imageHelper = $this->imageHelper;
        try {
           //$image = ($product->getData('image') != "no_selection" && $product->getData('image') != false && $product->getData('image') != "" ) ? $imageHelper->init($product, 'image')->__toString() : "";
        
			$image=$product->getData('image');
			
        } catch (Exception $e) {
             $image = "";
        }
        
        return $image;
    }
	public function addPriceAttributes($data, $product, $storeId, $cycleAllPriceAttributes = true)
    {
        if (!isset($data['special_price'])) {
            $data['special_price'] = "";
        }
        
        if (!isset($data['special_from_date'])) {
            $data['special_from_date'] = "";
        }
        
        if (!isset($data['special_to_date'])) {
            $data['special_to_date'] = "";
        }
        
        if (!isset($data['price'])) {
            $data['price'] = 0;
        }
        
        if ($cycleAllPriceAttributes) {
            $_store = $product->getStore();
            //$priceAttributes = Mage::getResourceModel('catalog/product_attribute_collection')->addFieldToFilter('frontend_input', array('eq' => 'price'));
            
			$priceAttributes = $this->_attributeFactory->getCollection()->addFieldToFilter('frontend_input', array('eq' => 'price'));
			
			if ($priceAttributes->getSize() > 0) {
                foreach ($priceAttributes as $a) {
                    if (isset($data[$a->getAttributeCode()]) ) {
                        //$data[$a->getAttributeCode()] = $_store->convertPrice($data[$a->getAttributeCode()]);
						$data[$a->getAttributeCode()] = $this->convertPrice($data[$a->getAttributeCode()], $_store);
				   }             
                }
            }
        }
        //\Magento\Catalog\Model\Product\Type\Price
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$aa = $objectManager->get('Magento\Catalog\Model\Product\Type\Price');
		
		$finalPrice = $aa->calculatePrice(
            $data['price'],
            $data['special_price'],
            $data['special_from_date'],
            $data['special_to_date']
        );
        
        /** @var Mage_Tax_Helper_Data $taxHelper */
        //$taxHelper = Mage::helper('tax');
		$taxHelper = $this->_taxHelper;

        $taxClassId = $product->getData('tax_class_id');
        //$storeObj  = Mage::getModel('core/store')->load($storeId); 
		//$storeObj = $this->_storeManager->load($storeId);
        
		$product_id=$product->getId();
		
		$taxedFinalPrice=0;
		$taxedPrice=0;
		if ($product->getTypeId() != "grouped") {
			$res=$this->getPriceInclAndExclTax($product_id);
			$incl =$res["incl"];
			$excl =$res["excl"];
			$taxedFinalPrice=$incl;
			$taxedPrice=$incl-$excl;
		}
		
		
		/*
        $taxedFinalPrice = $taxHelper->getPrice(
            $product->load('tax_class_id'),
            $finalPrice,
            true,
            null,
            null,
            null,
            $storeId
        );
        
        $taxedPrice = $taxHelper->getPrice(
            $product->load('tax_class_id'),
            $data['price'],
            true,
            null,
            null,
            null,
            $storeId
        );
        */
        /** Format the price the right way */
        $data['price'] = number_format(
            $taxedPrice,
            4,
            ".",
            ""
        );
        
        /** Price including VAT */
        $data['selling_price'] = number_format(
            $taxedFinalPrice,
            4,
            ".",
            ""
        );
        
        /** Price excluding VAT */
        $data['selling_price_ex'] = number_format(
            $finalPrice,
            4,
            ".",
            ""
        );
        
        return $data;
    }
	
	/**
	 * @param int $productId
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 * @return array
	 */
	public function getPriceInclAndExclTax(int $productId): array
	{
		$product = $this->productRepository->getById($productId);
		$taxAttribute = $product->getCustomAttribute('tax_class_id');
	 
		if ($taxAttribute = $product->getCustomAttribute('tax_class_id')) {
			// First get base price (=price excluding tax)
			$productRateId = $taxAttribute->getValue();
			$rate = $this->taxCalculation->getCalculatedRate($productRateId);
	 
			if ((int) $this->scopeConfig->getValue(
				'tax/calculation/price_includes_tax', 
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE) === 1
			) {
				// Product price in catalog is including tax.
				$priceExcludingTax = $product->getPrice() / (1 + ($rate / 100));
			} else {
				// Product price in catalog is excluding tax.
				$priceExcludingTax = $product->getPrice();
			}
	 
			$priceIncludingTax = $priceExcludingTax + ($priceExcludingTax * ($rate / 100));
	 
			return [
				'incl' => $priceIncludingTax,
				'excl' => $priceExcludingTax
			];
		}
	 
		throw new \Magento\Framework\Exception\LocalizedException(__('Tax Attribute not found'));
	}
	
	public function convertPrice($amount = 0, $store = null, $currency = null)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$priceCurrencyObject = $objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface'); //instance of PriceCurrencyInterface
		$storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); //instance of StoreManagerInterface
		if ($store == null) {
			$store = $storeManager->getStore()->getStoreId(); //get current store id if store id not get passed
		}
		$rate = $priceCurrencyObject->convert($amount, $store, $currency); //it return price according to current store from base currency
	 
		//If you want it in base currency then use:
		if ($amount>0)
		$rate = $this->priceCurrency->convert($amount, $store) / $amount;
	
		if ($rate>0)
		$amount = $amount / $rate;
		 
		return $priceCurrencyObject->round($amount);//You can round off to it or you can return it in its original form
	}
	
	/**
     * Send product data to queue for update 
     * @param array $data
     */     
    function _sendProductToUpdate($preparedData)
    {
		
			$status = \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_UPDATE;
			
			$update = $this->updateFactory->create();
			$update->setProductId($this->productData['entity_id']);
			$update->setStoreId($this->productData['store_id']);
			$update->setProductData($this->productData);
			$currentDateTime = $this->dateTime->gmtDate();
			$update->setCreatedAt($currentDateTime);
			$update->setUpdatedAt($currentDateTime);
			$update->setStatus($status);

			$this->updateRepository->save($update);		
		
    }
	
}