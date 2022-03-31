<?php

namespace Adcurve\Adcurve\Api;

interface CartManagementInterface
{
    /**
     * Updates the specified cart with the specified products.
     *
     * @api
     * @param int $cartId
     * @param \Adcurve\Adcurve\Api\CartProductInformationInterface[] $products
     * @return boolean
     */
    public function updateCart($cartId, $products = null);
}
