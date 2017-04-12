<?php
namespace Adcurve\Adcurve\Api\Data;

interface ConnectionInterface
{
    const ENABLED 			= 'enabled';
	const STORE_ID 			= 'store_id';
	const STORE_NAME 		= 'store_name';
	const STORE_CODE 		= 'store_code';
	const ADCURVE_SHOP_ID 	= 'adcurve_shop_id';
	const ADCURVE_TOKEN 	= 'adcurve_token';
	const IS_TESTMODE 		= 'is_testmode';
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

    public function getConnectionId();
    public function setConnectionId($connectionId);

	public function getEnabled();
	public function setEnabled($enabled);

	public function getStoreId();
	public function setStoreId($storeId);

	public function getStoreName();
	public function setStoreName($storeName);

	public function getStoreCode();
	public function setStoreCode($storeCode);

	public function getAdcurveShopId();
	public function setAdcurveShopId($adcurveShopId);

	public function getAdcurveToken();
	public function setAdcurveToken($adcurveToken);

	public function getIsTestmode();
	public function setIsTestmode($isTestmode);

	public function getStatus();
	public function setStatus($status);

	public function getSuggestion();
	public function setSuggestion($suggestion);

	public function getSoapUsername();
	public function setSoapUsername($soapUsername);

	public function getSoapApiKey();
	public function setSoapApiKey($soapApiKey);

	public function getContactFirstname();
	public function setContactFirstname($contactFirstname);

	public function getContactLastname();
	public function setContactLastname($contactLastname);

	public function getContactEmail();
	public function setContactEmail($contactEmail);

	public function getContactTelephone();
	public function setContactTelephone($contactTelephone);

	public function getCompanyName();
	public function setCompanyName($companyName);

	public function getCompanyAddress();
	public function setCompanyAddress($companyAddress);

	public function getCompanyZipcode();
	public function setCompanyZipcode($companyZipcode);

	public function getCompanyCity();
	public function setCompanyCity($companyCity);

	public function getCompanyRegion();
	public function setCompanyRegion($companyRegion);

	public function getCompanyCountry();
	public function setCompanyCountry($companyCountry);

	public function getUpdatedAt();
	public function setUpdatedAt($updatedAt);
}
