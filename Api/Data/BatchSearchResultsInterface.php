<?php


namespace Adcurve\Adcurve\Api\Data;

interface BatchSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Batch list.
     * @return \Adcurve\Adcurve\Api\Data\BatchInterface[]
     */
    
    public function getItems();

    /**
     * Set status list.
     * @param \Adcurve\Adcurve\Api\Data\BatchInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
