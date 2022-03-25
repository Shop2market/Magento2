<?php

namespace Adcurve\Adcurve\Observer\Catalog;

class ProductSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    protected $productHelper;
    protected $_configHelper;

    public function __construct(
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Adcurve\Adcurve\Helper\Product $productHelper
    ) {
        $this->productHelper = $productHelper;
        $this->_configHelper = $configHelper;
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
        $product = $observer->getEvent()->getProduct();

        // TO DO: add website scope support
        if ($product->getStoreId() == 0) { // Update all storeviews when global scope is edited
            foreach ($product->getStoreIds() as $storeId) {
                $connection = $this->_configHelper->getAdcurveConnectionByStore($storeId);
                if (!$this->_configHelper->isApiConfigured($connection)) {
                    /** If the api is not configured, don't process this storeview */
                    continue;
                }
                $preparedData = $this->productHelper->getProductData($product, $storeId);
                $this->productHelper->saveUpdateForAdcurve($preparedData);
            }
        } else {
            $storeId = $product->getStoreId();
            $connection = $this->_configHelper->getAdcurveConnectionByStore($storeId);
            if (!$this->_configHelper->isApiConfigured($connection)) {
                /** If the api is not configured, don't process this storeview */
                return false;
            }
            $preparedData = $this->productHelper->getProductData($product, $product->getStoreId());
            $this->productHelper->saveUpdateForAdcurve($preparedData);
        }
    }
}
