<?php

namespace Adcurve\Adcurve\Observer\Order;

class ItemCancel implements \Magento\Framework\Event\ObserverInterface
{
    protected $productHelper;
    protected $configHelper;
    protected $_productloader;
    protected $connectionRepository;
    protected $_logger;

    public function __construct(
        \Adcurve\Adcurve\Helper\Product $productHelper,
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Adcurve\Adcurve\Model\QueueFactory $queueFactory,
        \Adcurve\Adcurve\Model\ConnectionRepository $connectionRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->productHelper = $productHelper;
        $this->_productloader = $_productloader;
        $this->_storeManager = $storeManager;
        $this->queueFactory = $queueFactory;
        $this->_configHelper = $configHelper;
        $this->connectionRepository = $connectionRepository;
        $this->_logger = $logger;
    }

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $item = $observer->getEvent()->getItem();
        $qty = $item->getQtyOrdered() - max($item->getQtyShipped(), $item->getQtyInvoiced()) - $item->getQtyCanceled();
        if ($item->getId() && ($productId = $item->getProductId()) && empty($children) && $qty) {
            $productId = $item->getProductId();
            $this->_addProductsToBatch($productId);
        }

        return $this;
    }

    /**
     * Load product from product id
     *
     * @param int $productId
     *
     *
     */
    public function getLoadProduct($productId)
    {
        return $this->_productloader->create()->load($productId);
    }

    /**
     * Add a products to batch that need to be processed for the given storeview
     *
     * @param int $productId
     *
     * @param int $storeId
     *
     */
    protected function _addProductsToBatch($productId, $storeId = 0)
    {
        $product = $this->getLoadProduct($productId);
        if (!$this->productHelper->isProductValidForExport($product)) {
            /** If product is not valid for export, skip */
            return;
        }
        if ($storeId == 0) {
            $storeIds = $product->getStoreIds();
            if (count($storeIds) == 0) {
                $storeList = $this->_storeManager->getStores();

                /** @var Mage_Core_Model_Store $storeObject */
                foreach ($storeList as $storeObject) {
                    if ($storeObject->getId() == 0) {
                        continue;
                    }

                    $storeIds[] = $storeObject->getId();
                }
            }
            if (count($storeIds) > 0) {
                foreach ($storeIds as $storeId) {
                    try {
                        if ($storeId == 0) {
                            continue;
                        }
                        if (!in_array($storeId, $product->getStoreIds())) {
                            return;
                        }
                        $connection = $this->connectionRepository->getByStoreId($storeId);
                        if (!$this->_configHelper->isApiConfigured($connection)) {
                            /** If the api is not configured, don't process this storeview */
                            continue;
                        }
                        $preparedData = $this->productHelper->getProductData($product, $storeId);
                        $this->productHelper->saveUpdateForAdcurve($preparedData);
                    } catch (\Exception $e) {
                        $this->_logger->addError($e->getMessage());
                    }
                }
            }
        } else {
            try {
                if (!in_array($storeId, $product->getStoreIds())) {
                    return;
                }
                $connection = $this->connectionRepository->getByStoreId($storeId);
                if (!$this->_configHelper->isApiConfigured($connection)) {
                    return;
                }
                $preparedData = $this->productHelper->getProductData($product, $storeId);
                $this->productHelper->saveUpdateForAdcurve($preparedData);
            } catch (\Exception $e) {
                $this->_logger->addError($e->getMessage());
            }
        }
    }
}
