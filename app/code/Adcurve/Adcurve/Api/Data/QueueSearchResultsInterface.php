<?php
namespace Adcurve\Adcurve\Api\Data;

interface QueueSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Queue list.
     * @return \Adcurve\Adcurve\Api\Data\QueueInterface[]
     */
    public function getItems();

    /**
     * Set queue_id list.
     * @param \Adcurve\Adcurve\Api\Data\QueueInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
