<?php

namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\Data\ConnectionInterface;

class Connection extends \Magento\Framework\Model\AbstractModel implements ConnectionInterface
{
    public const STATUS_INITIAL = 1;
    public const STATUS_PRE_REGISTRATION = 2;
    public const STATUS_POST_REGISTRATION = 33;
    public const STATUS_ERROR_CONNECTION_TO_ADCURVE = 4;
    public const STATUS_ERROR_RESULT_FROM_ADCURVE = 5;
    public const STATUS_SUCCESS = 3;

    protected $encryptor;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Adcurve\Adcurve\Model\ResourceModel\Connection $resource,
        \Adcurve\Adcurve\Model\ResourceModel\Connection\Collection $resourceCollection,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        array $data = []
    ) {
        $this->encryptor = $encryptor;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Adcurve\Adcurve\Model\ResourceModel\Connection');
    }

    public function getConnectionId()
    {
        return $this->getData(self::CONNECTION_ID);
    }

    public function setConnectionId($connectionId)
    {
        return $this->setData(self::CONNECTION_ID, $connectionId);
    }

    public function getEnabled()
    {
        return $this->getData(self::ENABLED);
    }

    public function setEnabled($enabled)
    {
        return $this->setData(self::ENABLED, $enabled);
    }

    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    public function getStoreName()
    {
        return $this->getData(self::STORE_NAME);
    }

    public function setStoreName($storeName)
    {
        return $this->setData(self::STORE_NAME, $storeName);
    }

    public function getStoreCode()
    {
        return $this->getData(self::STORE_CODE);
    }

    public function setStoreCode($storeCode)
    {
        return $this->setData(self::STORE_CODE, $storeCode);
    }

    public function getProductionMode()
    {
        return $this->getData(self::PRODUCTION_MODE);
    }

    public function setProductionMode($productionMode)
    {
        $productionMode = 1;
        return $this->setData(self::PRODUCTION_MODE, $productionMode);
    }

    public function getIsAdcurveReady()
    {
        return $this->getData(self::PRODUCTION_MODE);
    }

    public function setIsAdcurveReady($isAdcurveReady)
    {
        return $this->setData(self::IS_ADCURVE_READY, $isAdcurveReady);
    }

    public function getAdcurveShopId()
    {
        return $this->getData(self::ADCURVE_SHOP_ID);
    }

    public function setAdcurveShopId($adcurveShopId)
    {
        return $this->setData(self::ADCURVE_SHOP_ID, $adcurveShopId);
    }

    public function getAdcurveToken()
    {
        $data = $this->getData(self::ADCURVE_TOKEN);
        return $this->encryptor->decrypt($data);
    }

    public function setAdcurveToken($adcurveToken)
    {
        $data = $this->encryptor->encrypt($adcurveToken);
        return $this->setData(self::ADCURVE_TOKEN, $data);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getSuggestion()
    {
        return $this->getData(self::SUGGESTION);
    }

    public function setSuggestion($suggestion)
    {
        return $this->setData(self::SUGGESTION, $suggestion);
    }

    public function getSoapUsername()
    {
        return $this->getData(self::SOAP_USERNAME);
    }

    public function setSoapUsername($soapUsername)
    {
        return $this->setData(self::SOAP_USERNAME, $soapUsername);
    }

    public function getSoapApiKey()
    {
        return $this->getData(self::SOAP_API_KEY);
    }

    public function setSoapApiKey($soapApiKey)
    {
        return $this->setData(self::SOAP_API_KEY, $soapApiKey);
    }

    public function getContactFirstname()
    {
        return $this->getData(self::CONTACT_FIRSTNAME);
    }

    public function setContactFirstname($contactFirstname)
    {
        return $this->setData(self::CONTACT_FIRSTNAME, $contactFirstname);
    }

    public function getContactLastname()
    {
        return $this->getData(self::CONTACT_LASTNAME);
    }

    public function setContactLastname($contactLastname)
    {
        return $this->setData(self::CONTACT_LASTNAME, $contactLastname);
    }

    public function getContactEmail()
    {
        return $this->getData(self::CONTACT_EMAIL);
    }

    public function setContactEmail($contactEmail)
    {
        return $this->setData(self::CONTACT_EMAIL, $contactEmail);
    }

    public function getContactTelephone()
    {
        return $this->getData(self::CONTACT_TELEPHONE);
    }

    public function setContactTelephone($contactTelephone)
    {
        return $this->setData(self::CONTACT_TELEPHONE, $contactTelephone);
    }

    public function getCompanyName()
    {
        return $this->getData(self::COMPANY_NAME);
    }

    public function setCompanyName($companyName)
    {
        return $this->setData(self::COMPANY_NAME, $companyName);
    }

    public function getCompanyAddress()
    {
        return $this->getData(self::COMPANY_ADDRESS);
    }

    public function setCompanyAddress($companyAddress)
    {
        return $this->setData(self::COMPANY_ADDRESS, $companyAddress);
    }

    public function getCompanyZipcode()
    {
        return $this->getData(self::COMPANY_ZIPCODE);
    }

    public function setCompanyZipcode($companyZipcode)
    {
        return $this->setData(self::COMPANY_ZIPCODE, $companyZipcode);
    }

    public function getCompanyCity()
    {
        return $this->getData(self::COMPANY_CITY);
    }

    public function setCompanyCity($companyCity)
    {
        return $this->setData(self::COMPANY_CITY, $companyCity);
    }

    public function getCompanyRegion()
    {
        return $this->getData(self::COMPANY_REGION);
    }

    public function setCompanyRegion($companyRegion)
    {
        return $this->setData(self::COMPANY_REGION, $companyRegion);
    }

    public function getCompanyCountry()
    {
        return $this->getData(self::COMPANY_COUNTRY);
    }

    public function setCompanyCountry($companyCountry)
    {
        return $this->setData(self::COMPANY_COUNTRY, $companyCountry);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
