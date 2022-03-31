<?php

namespace Adcurve\Adcurve\Api;

/**
 * Interface for tracking cart product updates.
 */
interface CartProductInformationInterface
{
    /**
     * Gets the sku.
     *
     * @api
     * @return string
     */
    public function getSku();

    /**
     * Sets the sku.
     *
     * @api
     * @param int $sku
     */
    public function setSku($sku);

    /**
     * Gets the quantity.
     *
     * @api
     * @return string
     */
    public function getQty();

    /**
     * Sets the quantity.
     *
     * @api
     * @param int $qty
     * @return void
     */
    public function setQty($qty);
}
