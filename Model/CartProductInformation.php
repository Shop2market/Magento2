<?php
namespace Adcurve\Adcurve\Model;
 
use \Adcurve\Adcurve\Api\CartProductInformationInterface;
 
/**
 * Model that contains updated cart information.
 */
class CartProductInformation implements CartProductInformationInterface {
 
    /**
     * The sku for this cart entry.
     * @var string
     */
    protected $sku;
 
    /**
     * The quantity value for this cart entry.
     * @var int
     */
    protected $qty;
 
    /**
     * Gets the sku.
     *
     * @api
     * @return string
     */
    public function getSku() {
        return $this->sku;
    }
 
    /**
     * Sets the sku.
     *
     * @api
     * @param int $sku
     */
    public function setSku($sku) {
        $this->sku = $sku;
    }
 
    /**
     * Gets the quantity.
     *
     * @api
     * @return string
     */
    public function getQty() {
        return $this->qty;
    }
 
    /**
     * Sets the quantity.
     *
     * @api
     * @param int $qty
     * @return void
     */
    public function setQty($qty) {
        $this->qty = $qty;
    }
}