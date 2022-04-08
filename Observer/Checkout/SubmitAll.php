<?php

namespace Adcurve\Adcurve\Observer\Checkout;

class SubmitAll implements \Magento\Framework\Event\ObserverInterface
{
    protected $productHelper;
    protected $configHelper;
    protected $_productloader;
    protected $logger;
    protected $connectionRepository;

    public function __construct(
        \Adcurve\Adcurve\Helper\Product $productHelper,
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Adcurve\Adcurve\Model\QueueFactory $queueFactory,
        \Psr\Log\LoggerInterface $logger,
        \Adcurve\Adcurve\Model\ConnectionRepository $connectionRepository
    ) {
        $this->productHelper = $productHelper;
        $this->_productloader = $_productloader;
        $this->_storeManager = $storeManager;
        $this->queueFactory = $queueFactory;
        $this->logger = $logger;
        $this->_configHelper = $configHelper;
        $this->connectionRepository = $connectionRepository;
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
        if ($observer->getEvent()->hasOrders()) {
            $orders = $observer->getEvent()->getOrders();
        } else {
            $orders = array($observer->getEvent()->getOrder());
        }
        $stockItems = array();
        foreach ($orders as $order) {
            if ($order) {
                foreach ($order->getAllItems() as $orderItem) {
                    /** @var Mage_Sales_Model_Order_Item $orderItem */
                    if ($orderItem->getQtyOrdered() && $orderItem->getProductType() == 'simple') {
                        $productId = $orderItem->getProductId();
                        $this->_addProductsToBatch($productId);
                    }
                }
            }
        }
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
                        $this->_logger->critical($e->getMessage());
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
                $this->_logger->critical($e->getMessage());
            }
        }
    }
}
