<?php
namespace Adcurve\Adcurve\Model\Rest;

class StatusRequest extends AbstractRequest
{
    const STATUS_SUCCESS = '1';
    const STATUS_ERROR_UNKNOWN = '2';
    const STATUS_ERROR_CONNECTION_TO_ADCURVE = '3';
    const STATUS_ERROR_RESULT_FROM_ADCURVE = '4';
	
	/**
	 * @var \Adcurve\Adcurve\Helper\Config
	 */
	protected $configHelper;
	
	public function __construct(
		\Adcurve\Adcurve\Helper\Config $configHelper
	){
		$this->configHelper = $configHelper;
	}
	
    /**
     * @param null $store
     *
     * @return mixed
     * @throws \Magento\Framework\Validator\Exception
     */
    public function getConnectionStatus($store = null)
    {
        if(!$this->configHelper->isApiConfigured($store)){
        	throw new \Magento\Framework\Validator\Exception(__('API not configured'));
        }

        $this->_setStore($store);
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
        $apiToken = $this->configHelper->getApiToken($this->_getStore());
		
        $this->_curlOpt[CURLOPT_HTTPHEADER][] = 'X-Api-Key: ' . $apiToken;
        $this->_curlOpt[CURLOPT_POST] = false;
		
        return $this->_curlOpt;
    }

    /**
     * Get the product API URL
     *
     * @param null $store
     *
     * @return string
     */
    protected function _getApiUrl($store = null)
    {
        return $this->configHelper->getStatusApiUrl($store);
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
            $result['error'] = 'Adcurve could not establish a connection with the Storeview';

            return $result;
        }

        return $result;
    }
}