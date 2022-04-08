<?php

namespace Adcurve\Adcurve\Api;

interface CartProductAddInterface
{
    /**
    * Return bool.
    *
    * @api
    * @param string $quoteId.
    * @param mixed $productsData
    * @param int $store.
    * @return bool.
    */
    public function addProduct($quoteId, $productsData, $store = null);
}
