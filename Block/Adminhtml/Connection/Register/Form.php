<?php
namespace Adcurve\Adcurve\Block\Adminhtml\Connection\Register;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
	protected $authSession;
	protected $countryOptions;
	protected $productAttributeOptions;
	protected $configHelper;
	protected $connectionHelper;
	
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Adcurve\Adcurve\Ui\Component\Listing\Column\Connection\Countries $countryOptions,
        \Adcurve\Adcurve\Ui\Component\Listing\Column\Connection\Attributes $productAttributeOptions,
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Adcurve\Adcurve\Helper\Connection $connectionHelper,
        array $data = []
    ) {
		$this->authSession = $authSession;
    	$this->countryOptions = $countryOptions;
		$this->productAttributeOptions = $productAttributeOptions;
		$this->configHelper = $configHelper;
		$this->connectionHelper = $connectionHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Retrieve template object
     *
     * @return \Adcurve\Adcurve\Model\Connection
     */
    public function getConnection()
    {
        return $this->_coreRegistry->registry('adcurve_adcurve_connection');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $connection = $this->getConnection();
		$currentUser = $this->authSession->getUser();
		
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => [
            	'id' 		=> 'adcurve_registration_form',
            	'method' 	=> 'post',
            	'action' 	=> $this->getData('action'),
            	'enctype' 	=> 'multipart/form-data'
    		]]
        );
		
        $companyFieldset = $form->addFieldset(
            'company_fieldset',
            ['legend' => __('Company information'), 'class' => 'fieldset-wide']
        );
		
		$companyFieldset->addField(
			'company_name',
			'text',
			[
				'name' 	=> 'company[name]',
				'label' => __('Company name'),
				'title' => __('Company name'),
				'required' => true,
				'value' => $connection->getCompanyName(),
				'readonly' => true
			]
		);
		
		$companyFieldset->addField(
			'company_address',
			'text',
			[
				'name' 	=> 'company[address]',
				'label' => __('Address'),
				'title' => __('Address'),
				'required' => true,
				'value' => $connection->getCompanyAddress(),
				'readonly' => true
			]
		);
		
		$companyFieldset->addField(
			'company_zipcode',
			'text',
			[
				'name' 	=> 'company[zipcode]',
				'label' => __('Zipcode'),
				'title' => __('Zipcode'),
				'required' => true,
				'value' => $connection->getCompanyZipcode(),
				'readonly' => true
			]
		);
		
		$companyFieldset->addField(
			'company_city',
			'text',
			[
				'name' 	=> 'company[city]',
				'label' => __('City'),
				'title' => __('City'),
				'required' => true,
				'value' => $connection->getCompanyCity(),
				'readonly' => true
			]
		);
		
		$companyFieldset->addField(
			'company_region',
			'text',
			[
				'name' 	=> 'company[region]',
				'label' => __('Region'),
				'title' => __('Region'),
				'required' => true,
				'value' => $connection->getCompanyRegion(),
				'readonly' => true
			]
		);
		
		$companyFieldset->addField(
			'company_country',
			'select',
			[
				'name' 	=> 'company[country]',
				'label' => __('Country'),
				'title' => __('Country'),
				'required' => true,
				'class' => 'select',
				'values' => $this->countryOptions->toOptionArray(),
				'value' => ($connection->getCompanyCountry()) ? $connection->getCompanyCountry() : 'NL',
				'readonly' => true
			]
		);
		
		$shopFieldset = $form->addFieldset(
            'shop_fieldset',
            ['legend' => __('Shop information'), 'class' => 'fieldset-wide']
        );
		
		$shopFieldset->addField(
			'production_mode',
			'select',
			[
				'name' 	=> 'shop[installation_type]',
				'label' => __('Installation type'),
				'title' => __('Installation type'),
				'required' => true,
				'class' => 'select',
				'values' => [
					['value' => 'live', 'label' => __('Production mode')],
					['value' => 'test', 'label' => __('Test mode')],
				],
				'value' => ($connection->getProductionMode()) ? 'live' : 'test',
				'readonly' => true
			]
		);
		
		$shopFieldset->addField(
			'store_id',
			'hidden',
			[
				'name' 	=> 'shop[store_id]',
				'label' => __('Store ID'),
				'title' => __('Store ID'),
				'required' => true,
				'value' => $connection->getStoreId(),
				'readonly' => true
			]
		);
		
		$shopFieldset->addField(
			'store_name',
			'text',
			[
				'name' 	=> 'shop[name]',
				'label' => __('Name'),
				'title' => __('Name'),
				'required' => true,
				'value' => $connection->getStoreName(),
				'readonly' => true
			]
		);
		
		$shopFieldset->addField(
			'soap_username',
			'text',
			[
				'name' => 'shop[soap_username]',
				'label' => __('API username'),
				'title' => __('API username'),
				'required' => true,
				'value' => $connection->getSoapUsername(),
				'readonly' => true
			]
		);
		
		$shopFieldset->addField(
			'integration_access_token',
			'text',
			[
				'name' => 'shop[soap_api_key]',
				'label' => __('Integration Access Token'),
				'title' => __('Integration Access Token'),
				'required' => true,
				'value' => $this->connectionHelper->getIntegrationAccessToken(),
				'readonly' => true
			]
		);
		
		
		$contactFieldset = $form->addFieldset(
            'contact_fieldset',
            ['legend' => __('Contact information'), 'class' => 'fieldset-wide']
        );
		
		$contactFieldset->addField(
			'contact_firstname',
			'text',
			[
				'name' => 'contact[first_name]',
				'label' => __('First name'),
				'title' => __('First name'),
				'required' => true,
				'value' => ($connection->getContactFirstname()) ? $connection->getContactFirstname() : $currentUser->getData('firstname'),
				'readonly' => true
			]
		);
		
		$contactFieldset->addField(
			'contact_lastname',
			'text',
			[
				'name' => 'contact[last_name]',
				'label' => __('Last name'),
				'title' => __('Last name'),
				'required' => true,
				'value' => ($connection->getContactLastname()) ? $connection->getContactLastname() : $currentUser->getData('lastname'),
				'readonly' => true
			]
		);
		
		$contactFieldset->addField(
			'contact_email',
			'text',
			[
				'name' => 'contact[email]',
				'label' => __('Email address'),
				'title' => __('Email address'),
				'required' => true,
				'value' => ($connection->getContactEmail()) ? $connection->getContactEmail() : $currentUser->getData('email'),
				'readonly' => true
			]
		);
		
		$contactFieldset->addField(
			'contact_telephone',
			'text',
			[
				'name' => 'contact[phone]',
				'label' => __('Telephone'),
				'title' => __('Telephone'),
				'required' => true,
				'value' => $connection->getContactTelephone(),
				'readonly' => true
			]
		);
		
		$hiddenFieldset = $form->addFieldset(
            'hidden_fieldset',
            ['legend' => __('Hidden information')/** TODO: Re-hide all irrelevant automatically filled fields, 'class' => 'hidden' */]
        );
		
		$hiddenFieldset->addField(
			'shop_url',
			'text',
			[
				'name' 	=> 'shop[url]',
				'label' => __('Url'),
				'title' => __('Url'),
				'required' => true,
				'value' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, $connection->getStoreId()),
				'readonly' => true
			]
		);
		
		$hiddenFieldset->addField(
			'success_url',
			'text',
			[
				'name' => 'success_url',
				'label' => __('Successful registration return url'),
				'title' => __('Successful registration return url'),
				'required' => true,
				'value' => $this->connectionHelper->getSuccessUrl($connection),
				'readonly' => true
			]
		);
		
		$hiddenFieldset->addField(
			'fail_url',
			'text',
			[
				'name' => 'fail_url',
				'label' => __('Failed registration return url'),
				'title' => __('Failed registration return url'),
				'required' => true,
				'value' => $this->connectionHelper->getFailUrl(),
				'readonly' => true
			]
		);
		
		$hiddenFieldset->addField(
			'wsdl_endpoint',
			'text',
			[
				'name' => 'wsdl_endpoint',
				'label' => __('WSDL Endpoint'),
				'title' => __('WSDL Endpoint'),
				'required' => true,
				'value' => 'http://boris.mage2.sandbox20.xpdev.nl/rest/default/schema?services=all', 
				/** 
				 * @TODO: Get correct wsdl endpoint url('s) (multiple for Magento 2)
				 * Structure is http://<magento.host>/soap/<optional_store_code>?wsdl&services=<service_name_1>,<service_name_2>
				 * This is saved in the config, but a solution first has to be discussed with the adcurve software team
				 */
				'readonly' => true
			]
		);
		
		$hiddenFieldset->addField(
			'shop_attributes',
			'multiselect',
			[
				'name' => 'shop[attributes][]',
				'label' => __('Product attributes'),
				'title' => __('Product attributes'),
				'required' => true,
				'values' => $this->productAttributeOptions->toOptionArray(),
				'value' => $this->productAttributeOptions->getAllValues(),
				'class' => 'multiselect',
				'readonly' => true
			]
		);
		
        $form->setAction($this->configHelper->getRegisterUrl($connection));
        $form->setUseContainer(true);
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
}