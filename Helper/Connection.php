<?php

namespace Adcurve\Adcurve\Helper;

class Connection extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const API_ROLE_NAME                 = 'AdCurve API Role';
    public const EMAIL_SUCCESS_REG_TEMPLATE_ID = 'adcurve_store_installation';
    public const CRON_CHECK_FILE_NAME          = 'cron_checked.log';
    public const XPATH_DESIGN_PACKAGE_NAME     = 'design/package/name';
    public const XPATH_DESIGN_THEME_DEFAULT    = 'design/theme/default';
    public const XPATH_DESIGN_HEADER_LOGO_SRC  = 'design/header/logo_src';
    protected $storeManager;
    protected $backendUrlBuilder;
    protected $configHelper;
    protected $integrationService;
    protected $oauthService;
    protected $countries;
    protected $apiRoleResources;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\UrlInterface $backendUrlBuilder,
        \Magento\Integration\Model\IntegrationService $integrationService,
        \Magento\Integration\Model\OauthService $oauthService,
        \Adcurve\Adcurve\Helper\Config $configHelper
    ) {
        parent::__construct($context);

        $this->storeManager = $storeManager;
        $this->backendUrlBuilder = $backendUrlBuilder;
        $this->configHelper = $configHelper;
        $this->integrationService = $integrationService;
        $this->_oauthService = $oauthService;
    }

    /**
     * Get Access tokens of the Adcurve Integration
     *
     * @return string $accessToken
     */
    public function getIntegrationAccessToken()
    {
        /** @var \Magento\Integration\Model\Integration $itegration */
        $integration = $this->integrationService->findByName('AdcurveIntegration');
        if (!$integration || !$integration->getStatus() || !$integration->getConsumerId()) {
            return false;
        }

        /**
         * @var \Magento\Integration\Model\Oauth\Token $accessToken
         *
         * For example contains the following data:
         * 'type' => string 'access' (length=6)
         * 'token' => string 'aaaaaa_token_example_aaaaaaa' (length=32)
         * 'secret' => string 'aaaaaa_secret_example_aaaaaa' (length=32)
         * 'verifier' => string 'aaaaa_verifier_example_aaaaa' (length=32)
         */
        $accessToken = $this->_oauthService->getAccessToken($integration->getConsumerId());
        if (!$accessToken) {
            return false;
        }
        if (!$accessToken->getData('token')) {
            return false;
        }

        /** @TODO: Verify if this public token is enough to make all the calls that are required by Adcurve */
        return $accessToken->getData('token');
    }

    /** @TODO: Fix role of Adcurve integration based on needes rights
        $magentoOnePluginRoles = array(
            '__root__',
            'cart',
            'cart/customer',
            'cart/customer/addresses',
            'cart/customer/set',
            'cart/shipping',
            'cart/shipping/list',
            'cart/shipping/method',
            'cart/payment',
            'cart/payment/list',
            'cart/payment/method',
            'cart/product',
            'cart/product/list',
            'cart/product/remove',
            'cart/product/update',
            'cart/product/add',
            'cart/license',
            'cart/order',
            'cart/info',
            'cart/totals',
            'cart/create',
            'core',
            'core/store',
            'core/store/list',
            'customer',
            'customer/info',
            'customer/create',
            'customer/update',
            'customer/address',
            'customer/address/info',
            'customer/address/update',
            'customer/address/create',
        );
    */

    /**
     * Return url to come back after a successful Adcurve registration
     *
     * @param \Adcurve\Adcurve\Model\Connection $connection
     *
     * @return string $url
     */
    public function getSuccessUrl($connection)
    {
        return $this->backendUrlBuilder->getUrl('adcurve_adcurve/connection/registration_success', ['connection_id' => $connection->getId()]);
    }

    /**
     * Return url to come back after a failed Adcurve installation
     *
     * @return string $url
     */
    public function getFailUrl()
    {
        return $this->backendUrlBuilder->getUrl('adcurve_adcurve/connection/registration_failed');
    }

    /**
     * Return url of website
     *
     * @return string
     */
    public function getShopUrl($store = null)
    {
        //@TODO: make web url website dependant
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }
    /**
    * Return url of website
    *
    * @return string
    */
    public function getWsdlEndpointUrl($store = null)
    {
        //@TODO: make web url website dependant
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . 'soap/?wsdl_list=1';
    }
}
