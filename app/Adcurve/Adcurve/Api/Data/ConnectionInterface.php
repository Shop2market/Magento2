<?php
namespace Adcurve\Adcurve\Api\Data;

interface ConnectionInterface
{
    const ENABLED 			= 'enabled';
	const STORE_ID 			= 'store_id';
	const STORE_NAME 		= 'store_name';
	const STORE_CODE 		= 'store_code';
	const PRODUCTION_MODE 	= 'production_mode';
	const ADCURVE_SHOP_ID 	= 'adcurve_shop_id';
	const ADCURVE_TOKEN 	= 'adcurve_token';
	const STATUS 			= 'status';
	const SUGGESTION 		= 'suggestion';
	const SOAP_USERNAME 	= 'soap_username';
	const SOAP_API_KEY 		= 'soap_api_key';
	const CONTACT_FIRSTNAME = 'contact_firstname';
	const CONTACT_LASTNAME 	= 'contact_lastname';
	const CONTACT_EMAIL 	= 'contact_email';
	const CONTACT_TELEPHONE = 'contact_telephone';
	const COMPANY_NAME 		= 'company_name';
	const COMPANY_ADDRESS 	= 'company_address';
	const COMPANY_ZIPCODE 	= 'company_zipcode';
	const COMPANY_CITY 		= 'company_city';
	const COMPANY_REGION 	= 'company_region';
	const COMPANY_COUNTRY 	= 'company_country';
	const UPDATED_AT 		= 'updated_at';

 	/**
     * Get connection_id
     * @return string|null
     */
    public function getConnectionId();

    /**
     * Set connection_id
     * @param string $connection_id
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setConnectionId($connectionId);

    /**
     * Get enabled
     * @return string|null
     */
    public function getEnabled();

    /**
     * Set enabled
     * @param string $enabled
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setEnabled($enabled);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $store_id
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setStoreId($store_id);

    /**
     * Get store_name
     * @return string|null
     */
    public function getStoreName();

    /**
     * Set store_name
     * @param string $store_name
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setStoreName($store_name);

    /**
     * Get store_code
     * @return string|null
     */
    public function getStoreCode();

    /**
     * Set store_code
     * @param string $store_code
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setStoreCode($store_code);

    /**
     * Get production_mode
     * @return string|null
     */
    public function getProductionMode();

    /**
     * Set production_mode
     * @param string $production_mode
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setProductionMode($production_mode);

    /**
     * Get adcurve_shop_id
     * @return string|null
     */
    public function getAdcurveShopId();

    /**
     * Set adcurve_shop_id
     * @param string $adcurve_shop_id
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setAdcurveShopId($adcurve_shop_id);

    /**
     * Get adcurve_token
     * @return string|null
     */
    public function getAdcurveToken();

    /**
     * Set adcurve_token
     * @param string $adcurve_token
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setAdcurveToken($adcurve_token);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setStatus($status);

    /**
     * Get suggestion
     * @return string|null
     */
    public function getSuggestion();

    /**
     * Set suggestion
     * @param string $suggestion
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setSuggestion($suggestion);

    /**
     * Get soap_username
     * @return string|null
     */
    public function getSoapUsername();

    /**
     * Set soap_username
     * @param string $soap_username
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setSoapUsername($soap_username);

    /**
     * Get soap_api_key
     * @return string|null
     */
    public function getSoapApiKey();

    /**
     * Set soap_api_key
     * @param string $soap_api_key
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setSoapApiKey($soap_api_key);

    /**
     * Get contact_firstname
     * @return string|null
     */
    public function getContactFirstname();

    /**
     * Set contact_firstname
     * @param string $contact_firstname
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setContactFirstname($contact_firstname);

    /**
     * Get contact_lastname
     * @return string|null
     */
    public function getContactLastname();

    /**
     * Set contact_lastname
     * @param string $contact_lastname
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setContactLastname($contact_lastname);

    /**
     * Get contact_email
     * @return string|null
     */
    public function getContactEmail();

    /**
     * Set contact_email
     * @param string $contact_email
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setContactEmail($contact_email);

    /**
     * Get contact_telephone
     * @return string|null
     */
    public function getContactTelephone();

    /**
     * Set contact_telephone
     * @param string $contact_telephone
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setContactTelephone($contact_telephone);

    /**
     * Get company_name
     * @return string|null
     */
    public function getCompanyName();

    /**
     * Set company_name
     * @param string $company_name
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setCompanyName($company_name);

    /**
     * Get company_address
     * @return string|null
     */
    public function getCompanyAddress();

    /**
     * Set company_address
     * @param string $company_address
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setCompanyAddress($company_address);

    /**
     * Get company_zipcode
     * @return string|null
     */
    public function getCompanyZipcode();

    /**
     * Set company_zipcode
     * @param string $company_zipcode
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setCompanyZipcode($company_zipcode);
	
	/**
     * Get company_city
     * @return string|null
     */
	public function getCompanyCity();

    /**
     * Set company_city
     * @param string $company_city
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setCompanyCity($company_city);

    /**
     * Get company_region
     * @return string|null
     */
    public function getCompanyRegion();

    /**
     * Set company_region
     * @param string $company_region
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setCompanyRegion($company_region);

    /**
     * Get company_country
     * @return string|null
     */
    public function getCompanyCountry();

    /**
     * Set company_country
     * @param string $company_country
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setCompanyCountry($company_country);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updated_at
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setUpdatedAt($updated_at);
}