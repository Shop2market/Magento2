<?php
namespace Adcurve\Adcurve\Api\Data;

interface CartProductInformationInterface
{
    const PRODUCT_ID = 'product_id';
    const SKU = 'sku';
	const QTY = 'qty';

	/**
     * Gets the product_id.
     *
     * @api
     * @return int
     */
    public function getProductId();
 
    /**
     * Sets the product_id.
     *
     * @api
     * @param int $product_id
     * @return void
     */
    public function setProductId($product_id);
	
	
	
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
     * @return int
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
