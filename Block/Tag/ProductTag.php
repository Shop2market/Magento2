<?php

namespace Adcurve\Adcurve\Block\Tag;

class ProductTag extends \Adcurve\Adcurve\Block\Tag\AbstractTag
{
    protected $_registry;
    protected $stockRegistry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Adcurve\Adcurve\Helper\Tag $tagHelper,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->stockRegistry = $stockRegistry;

        parent::__construct($context, $configHelper, $tagHelper, $data);
    }

    /**
     * Get current product from registry
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Get product stock
     * if the product is configurable or bundled with stock 0, return empty
     * because the main stock is of its children
     *
     * @todo allow default stock level to be set from configuration
     *
     * @return float|string
     */
    public function getProductStock()
    {
        /** @var \Magento\CatalogInventory\Model\Stock\ItemArray $stockItem */
        $stockItem = $this->stockRegistry->getStockItem(
            $this->getCurrentProduct()->getId(),
            $this->getCurrentProduct()->getStore()->getWebsiteId()
        );
        if ($stockItem->getManageStock() == 0) {
            return 1;
        }

        $qty = round($stockItem->getQty());
        if ($qty < 1) {
            if ($stockItem->getBackorders() == 1) {
                return 1;
            }

            if ($stockItem->getIsInStock()) {
                return '';
            }
        }
        return $qty;
    }
}
