<?php
namespace Adcurve\Adcurve\Model\Rest;

abstract class AbstractRequest
{
	protected $configHelper;
    protected $_curl;
    protected $_data;
    protected $_store = null;
	protected $_connectionModel;
    protected $_curlOpt = array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        ),
    );

	public function __construct(
		\Adcurve\Adcurve\Helper\Config $configHelper
	){
		$this->configHelper = $configHelper;
	}

    protected function _getData()
    {
        return $this->_data;
    }

    protected function _setData($data)
    {
        $this->_data = $data;

        return $this;
    }

	/**
	 * @return \Adcurve\Adcurve\Model\Connection
	 */
	protected function _getConnectionModel()
	{
		return $this->_connectionModel;
	}

	/**
     * @param $connection
     *
     * @return $this
     */
    protected function _setConnectionModel($connection)
    {
        $this->_connectionModel = $connection;

        return $this;
    }

    /**
     * @param array $data
     * @param \Adcurve\Adcurve\Model\Connection $connection
     *
     * @return $this
     */
    public function sendData($data, $connection)
    {
    	$this->_setConnectionModel($connection);
		
        if(!$this->configHelper->isApiConfigured($this->_getConnectionModel())){
        	throw new \Magento\Framework\Validator\Exception(__('API not configured'));
        }
		
        $this->_setData($data);
		
        $this->_prepareRequest();
        $this->_sendRequest();
		
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareRequest()
    {
        curl_setopt_array($this->_getCurl(), $this->_getCurlOptions());

        return $this;
    }

    /**
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _sendRequest()
    {
        $curl = $this->_getCurl();
        $response = curl_exec($curl);
		
		// Httpcode can be used to debug
		//$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
        // Check for errors
        if($response === false){
        	throw new \Magento\Framework\Validator\Exception(curl_error($this->_getCurl()));
        }

        return $this->_processResponse($response);
    }

	/**
     * @return resource
     */
    protected function _getCurl()
    {
        if (!$this->_curl) {
        	$shopId = $this->_getConnectionModel()->getAdcurveShopId();
            $apiUrl = $this->_getApiUrl($shopId);
			
            $this->_curl = curl_init($apiUrl);
        }
		
        return $this->_curl;
    }

    /**
     * @return array
     */
    protected function _getCurlOptions()
    {
        $apiToken = $this->_getConnectionModel()->getAdcurveToken();
		
        $this->_curlOpt[CURLOPT_HTTPHEADER][]   = 'X-Api-Key: ' . $apiToken;
        $this->_curlOpt[CURLOPT_POSTFIELDS]     = json_encode($this->_getData());
		
        return $this->_curlOpt;
    }

    /**
     * Process the response
     *
     * @param $response
     *
     * @return json $response
     */
    abstract protected function _processResponse($response);

    /**
     * Get the API Url based on Adcurve shop ID
     *
     * @return string $apiUrl
     */
    abstract protected function _getApiUrl($shopId);
}