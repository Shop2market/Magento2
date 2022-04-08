<?php

namespace Adcurve\Adcurve\Model\Rest;

class UpdateRequest extends AbstractRequest
{
    /**
     * Get the product API URL
     *
     * @param null $store
     *
     * @return string
     */
    public function _getApiUrl($shopId)
    {
        return $this->configHelper->getProductApiUrl($shopId);
    }

    /**
     * Response is always empty, so no processing nor a return
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
