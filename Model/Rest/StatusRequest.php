<?php
namespace Adcurve\Adcurve\Model\Rest;

class StatusRequest extends AbstractRequest
{
    const STATUS_SUCCESS = '1';
    const STATUS_ERROR_UNKNOWN = '2';
    const STATUS_ERROR_CONNECTION_TO_ADCURVE = '3';
    const STATUS_ERROR_RESULT_FROM_ADCURVE = '4';
	
    /**
	 * @param \Adcurve\Adcurve\Model\Connection $connection
     *
     * @return mixed
     * @throws \Magento\Framework\Validator\Exception
     */
    public function getConnectionStatus($connection)
    {
    	if (!$connection->getId()) {
    		throw new \Magento\Framework\Validator\Exception(__('A valid Adcurve Connection model is required'));
    	}
		
		$this->_setConnectionModel($connection);
		
        if(!$this->configHelper->isApiConfigured($this->_getConnectionModel())){
        	throw new \Magento\Framework\Validator\Exception(__('API not configured'));
        }
		
        $this->_prepareRequest();
        $result = $this->_sendRequest();
		
		/* @TODO: Complete connection status logic.
		// OLD function logic below for reference
		$apiStatus = $this->statusRequest->getConnectionStatus($storeId);
		
        switch ($apiStatus['status']) {
            case \Adcurve\Adcurve\Model\Rest\StatusRequest::STATUS_ERROR_CONNECTION_TO_ADCURVE:
                $statusResult['stepsCompleted'] = 1;
                $message = 'Something went wrong with the installation, please contact support';
                $statusResult['suggestion'] = __($message);
                break;
            case \Adcurve\Adcurve\Model\Rest\StatusRequest::STATUS_ERROR_RESULT_FROM_ADCURVE:
                $statusResult['stepsCompleted'] = 2;
                $msgStep2 = 'Connection with AdCurve API not established yet. Please follow this %s for more information';
                $urlRF = $this->configHelper->getApiRoleCreatedFailedUrl($storeId);
                $msgStep2D = "<a target='_blank' class='manual-url' href='" . $urlRF . "'>" . __('manual') . "</a>";
                $statusResult['suggestion'] = __($msgStep2, $msgStep2D);
                break;
            case \Adcurve\Adcurve\Model\Rest\StatusRequest::STATUS_SUCCESS:
                $statusResult['stepsCompleted'] = 3;
                if ($this->configHelper->isTestMode($storeId) == true) {
                    $msgStep3 = 'Ready for testing. Please make sure you install the live version';
                    $msgStep3 .= ' using the registration form bellow to enjoy all the functionality!';
                    $statusResult['suggestion'] = __($msgStep3);
                } else {
                    $msgStep3 = 'Connection successfully established. No further action is needed.';
                    $statusResult['suggestion'] = __($msgStep3);
                }
                break;
        }
		*/
		
		
		
        return $result;
    }
	
    /**
     * The ping API merely wants to show the connection status, and not throw an exception when something goes wrong
     *
     * @return mixed
     */
    protected function _sendRequest()
    {
        $curl = $this->_getCurl();
        $response = curl_exec($curl);
		
        return $this->_processResponse($response);
    }

	/**
     * The ping API call uses GET instead of POST
     *
     * @return array
     */
    protected function _getCurlOptions()
    {
        $apiToken = $this->_getConnectionModel()->getAdcurveToken();
		
        $this->_curlOpt[CURLOPT_HTTPHEADER][] = 'X-Api-Key: ' . $apiToken;
        $this->_curlOpt[CURLOPT_POST] = false;
		
        return $this->_curlOpt;
    }

    /**
     * Get the product API Url by Adcurve shop ID
     *
     * @param \Adcurve\Adcurve\Model\Connection $connection
     *
     * @return string $url
     */
    protected function _getApiUrl($connection)
    {
        return $this->configHelper->getStatusApiUrl($connection);
    }

    /**
     * @param $response
     *
     * @return array
     */
    protected function _processResponse($response)
    {
        $result = array(
            'status' => self::STATUS_SUCCESS,
        );
		
        $responseJson = json_decode($response);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $result['status'] = self::STATUS_ERROR_CONNECTION_TO_ADCURVE;
            $result['error'] = 'Could not establish a connection with Adcurve';
			
            return $result;
        }
		
        if (!isset($responseJson->connected) || $responseJson->connected === false) {
            $result['status'] = self::STATUS_ERROR_RESULT_FROM_ADCURVE;
            $result['error'] = 'Adcurve could not establish a connection with the storeview';
			
            return $result;
        }
		
        return $result;
    }
}