<?php


namespace Adcurve\Adcurve\Api\Data;

interface UpdateSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Update list.
     * @return \Adcurve\Adcurve\Api\Data\UpdateInterface[]
     */
    
    public function getItems();

    /**
     * Set status list.
     * @param \Adcurve\Adcurve\Api\Data\UpdateInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
