<?php

namespace Adcurve\Adcurve\Api\Data;

interface ConnectionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Connection list.
     * @return \Adcurve\Adcurve\Api\Data\ConnectionInterface[]
     */

    public function getItems();

    /**
     * Set connection_id list.
     * @param \Adcurve\Adcurve\Api\Data\ConnectionInterface[] $items
     * @return $this
     */

    public function setItems(array $items);
}
