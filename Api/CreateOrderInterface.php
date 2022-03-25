<?php

namespace Adcurve\Adcurve\Api;

interface CreateOrderInterface
{
    /**
    * Return order id.
    *
    * @api
    * @param string $quoteId.
    * @param int $storeId.
    * @param mixed $agreements.
    * @param mixed $orderAdditionalAttributes
    * @return int Order id.
    */
    public function createOrder($quoteId, $storeId = null, $agreements = null, $orderAdditionalAttributes = null);
}
