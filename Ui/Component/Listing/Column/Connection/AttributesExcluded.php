<?php

namespace Adcurve\Adcurve\Ui\Component\Listing\Column\Connection;

use Magento\Framework\Data\OptionSourceInterface;

class AttributesExcluded implements OptionSourceInterface
{
    protected $attributeCollectionFactory;
    protected $options;
    protected $allValues;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * Get all product attributes that have a frontend label
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $attributeCollection = $this->attributeCollectionFactory->create()
                ->addFieldToSelect(['attribute_code', 'frontend_label'])
                ->addFieldToFilter('frontend_label', ['neq' => '']);

            $options = [];
            foreach ($attributeCollection as $attribute) {
                $options[] = [
                    'value' => $attribute->getAttributecode(),
                    'label' => $attribute->getFrontendLabel()
                ];
            }
            $this->options = $options;
        }

        return $this->options;
    }

    public function getAllValues()
    {
        if (!$this->allValues) {
            $options = $this->toOptionArray();
            $allValues = [];
            foreach ($options as $option) {
                $allValues[] = $option['value'];
            }
            $this->allValues = $allValues;
        }
        return $this->allValues;
    }
}
