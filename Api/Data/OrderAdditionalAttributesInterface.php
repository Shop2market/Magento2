<?php
namespace Adcurve\Adcurve\Api\Data;

interface OrderAdditionalAttributesInterface
{
    const SHIPPING_PRICE = 'adcurveShippingPrice';
    const ORDER_SOURCE = 'adcurveOrderSource';
	const ORDER_ID = 'adcurveOrderId';
    /**
     * Get AdcurveShippingPrice
     * @return string|null
     */
    public function getAdcurveShippingPrice();

    /**
     * Set AdcurveShippingPrice
     * @param string $adcurveShippingPrice
     * @return Adcurve\Adcurve\Api\Data\OrderAdditionalAttributesInterface
     */
    public function setAdcurveShippingPrice($adcurveShippingPrice);
	
	/**
     * Get AdcurveOrderSource
     * @return string|null
     */
    public function getAdcurveOrderSource();

    /**
     * Set AdcurveOrderSource
     * @param string $adcurveOrderSource
     * @return Adcurve\Adcurve\Api\Data\OrderAdditionalAttributesInterface
     */
    public function setAdcurveOrderSource($adcurveOrderSource);
	
	
	/**
     * Get AdcurveOrderId
     * @return string|null
     */
    public function getAdcurveOrderId();

    /**
     * Set AdcurveOrderId
     * @param string $adcurveOrderId
     * @return Adcurve\Adcurve\Api\Data\OrderAdditionalAttributesInterface
     */
    public function setAdcurveOrderId($adcurveOrderId);
	
}
