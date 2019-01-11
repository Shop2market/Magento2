<?php
namespace Adcurve\Adcurve\Model;
use Adcurve\Adcurve\Api\Data\OrderAdditionalAttributesInterface;

class OrderAdditionalAttributes extends \Magento\Framework\Model\AbstractModel implements OrderAdditionalAttributesInterface
{

	/**
     * Get AdcurveShippingPrice
     * @return string|null
     */
    public function getAdcurveShippingPrice()
	{
        return $this->getData(self::SHIPPING_PRICE);
    }

    /**
     * Set AdcurveShippingPrice
     * @param string $adcurveShippingPrice
     * @return Adcurve\Adcurve\Api\Data\OrderAdditionalAttributesInterface
     */
    public function setAdcurveShippingPrice($adcurveShippingPrice)
	{
        return $this->setData(self::SHIPPING_PRICE, $adcurveShippingPrice);
    }
	
	/**
     * Get AdcurveOrderSource
     * @return string|null
     */
    public function getAdcurveOrderSource()
	{
        return $this->getData(self::ORDER_SOURCE);
    }

    /**
     * Set AdcurveOrderSource
     * @param string $adcurveOrderSource
     * @return Adcurve\Adcurve\Api\Data\OrderAdditionalAttributesInterface
     */
    public function setAdcurveOrderSource($adcurveOrderSource)
	{
        return $this->setData(self::ORDER_SOURCE, $adcurveShippingPrice);
    }
	
	
	/**
     * Get AdcurveOrderId
     * @return string|null
     */
    public function getAdcurveOrderId()
	{
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Set AdcurveOrderId
     * @param string $adcurveOrderId
     * @return Adcurve\Adcurve\Api\Data\OrderAdditionalAttributesInterface
     */
    public function setAdcurveOrderId($adcurveOrderId)
	{
        return $this->setData(self::ORDER_ID, $adcurveOrderId);
    }
    
}
