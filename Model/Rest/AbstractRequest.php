<?php
namespace Adcurve\Adcurve\Model\Rest;

abstract class AbstractRequest
{
	protected $configHelper;
	
	public function __construct(
		\Adcurve\Adcurve\Helper\Config $configHelper
	){
		$this->configHelper = $configHelper;
	}
	
    /**
     * @var resource
     */
    protected $_curl;

    /**
     * @var array
     */
    protected $_data;

    /**
     * @var
     */
    protected $_store = null;

    /**
     * @var array
     */
    protected $_curlOpt = array(
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        ),
    );

    /**
     * @return array
     */
    protected function _getData()
    {
        return $this->_data;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    protected function _setData($data)
    {
        $this->_data = $data;

        return $this;
    }

    /**
     * @return null|int
     */
    protected function _getStore()
    {
        return $this->_store;
    }

    /**
     * @param $store
     *
     * @return $this
     */
    protected function _setStore($store)
    {
        $this->_store = $store;

        return $this;
    }

    /**
     * @return resource
     */
    protected function _getCurl()
    {
        if (!$this->_curl) {
            $apiUrl = $this->_getApiUrl($this->_getStore());

            $this->_curl = curl_init($apiUrl);
        }

        return $this->_curl;
    }

    /**
     * @return array
     */
    protected function _getCurlOptions()
    {
        $apiToken = $this->configHelper->getApiToken($this->_getStore());

        $this->_curlOpt[CURLOPT_HTTPHEADER][]   = 'X-Api-Key: ' . $apiToken;
        $this->_curlOpt[CURLOPT_POSTFIELDS]     = json_encode($this->_getData());

        return $this->_curlOpt;
    }

    /**
     * @param $data
     *
     * @param null $store
     *
     * @return $this
     */
    public function sendData($data, $store = null)
    {
        if (!$this->configHelper->isApiConfigured($store)) {
        	throw new \Magento\Framework\Validator\Exception(__('API not configured'));
        }

        $this->_setData($data);
        $this->_setStore($store);

        $this->_prepareRequest()
            ->_sendRequest();

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

        // Check for errors
        if($response === false){
        	throw new \Magento\Framework\Validator\Exception(curl_error($this->_getCurl()));
        }

        return $this->_processResponse($response);
    }

    /**
     * Process the response
     *
     * @param $response
     *
     * @return mixed
     */
    abstract protected function _processResponse($response);

    /**
     * Get the API URL
     *
     * @param null $store
     *
     * @return mixed
     */
    abstract protected function _getApiUrl($store = null);
}