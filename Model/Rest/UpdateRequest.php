<?php
namespace Adcurve\Adcurve\Model\Rest;

class UpdateRequest extends \Adcurve\Adcurve\Model\Rest\AbstractRequest
{
    /**
     * Get the product API URL
     *
     * @param null $store
     *
     * @return string
     */
    protected function _getApiUrl($store = null)
    {
        return $this->configHelper->getProductApiUrl($store);
    }
	
    /**
     * Response is always empty
     *
     * @param $response
     *
     * @return $this
     */
    protected function _processResponse($response)
    {
        return $this;
    }
}