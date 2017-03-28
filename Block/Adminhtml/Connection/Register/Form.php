<?php
namespace Adcurve\Adcurve\Block\Adminhtml\Connection\Register;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
	protected $authSession;
	protected $countryOptions;
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
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Adcurve\Adcurve\Helper\Connection $connectionHelper,
        array $data = []
    ) {
		$this->authSession = $authSession;
    	$this->countryOptions = $countryOptions;
		$this->configHelper = $configHelper;
		$this->connectionHelper = $connectionHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Retrieve template object
     *
     * @return \Adcurve\Adcurve\Model\Connection
     */
    public function getModel()
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
        $model = $this->getModel();
		$currentUser = $this->authSession->getUser();
		
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => [
            	'id' => 'adcurve_registration_form',
            	'action' => $this->getData('action'),
            	'method' => 'post'
    		]]
        );
		
        $companyFieldset = $form->addFieldset(
            'company_fieldset',
            ['legend' => __('Company information'), 'class' => 'fieldset-wide']
        );
		
		$companyFieldset->addField(
			'company[name]',
			'text',
			[
				'name' 	=> 'company[name]',
				'label' => __('Company name'),
				'title' => __('Company name'),
				'required' => true,
				'value' => $model->getCompanyName()
			]
		);
		
		$companyFieldset->addField(
			'company[address]',
			'text',
			[
				'name' 	=> 'company[address]',
				'label' => __('Address'),
				'title' => __('Address'),
				'required' => true,
				'value' => $model->getCompanyAddress()
			]
		);
		
		$companyFieldset->addField(
			'company[zipcode]',
			'text',
			[
				'name' 	=> 'company[zipcode]',
				'label' => __('Zipcode'),
				'title' => __('Zipcode'),
				'required' => true,
				'value' => $model->getCompanyZipcode()
			]
		);
		
		$companyFieldset->addField(
			'company[city]',
			'text',
			[
				'name' 	=> 'company[city]',
				'label' => __('City'),
				'title' => __('City'),
				'required' => true,
				'value' => $model->getCompanyCity()
			]
		);
		
		$companyFieldset->addField(
			'company[region]',
			'text',
			[
				'name' 	=> 'company[region]',
				'label' => __('Region'),
				'title' => __('Region'),
				'required' => true,
				'value' => $model->getCompanyRegion()
			]
		);
		
		$companyFieldset->addField(
			'company[country]',
			'select',
			[
				'name' 	=> 'company[country]',
				'label' => __('Country'),
				'title' => __('Country'),
				'required' => true,
				'class' => 'select',
				'values' => $this->countryOptions->toOptionArray(),
				'value' => $model->getCompanyCountry()
			]
		);
		
		$shopFieldset = $form->addFieldset(
            'shop_fieldset',
            ['legend' => __('Shop information'), 'class' => 'fieldset-wide']
        );
		
		$shopFieldset->addField(
			'shop[installation_type]',
			'select',
			[
				'name' 	=> 'shop[installation_type]',
				'label' => __('Installation type'),
				'title' => __('Installation type'),
				'required' => true,
				'class' => 'select',
				'values' => [
					['value' => $this->connectionHelper->getInstallationTypeLive(), 'label' => __('Live')],
					['value' => $this->connectionHelper->getInstallationTypeTest(), 'label' => __('Test')],
				]
			]
		);
		
		$shopFieldset->addField(
			'shop[store_id]',
			'hidden',
			[
				'name' 	=> 'shop[store_id]',
				'label' => __('Store ID'),
				'title' => __('Store ID'),
				'required' => true,
				'value' => $model->getStoreId(),
				'disabled' => true
			]
		);
		
		$shopFieldset->addField(
			'shop[name]',
			'text',
			[
				'name' 	=> 'shop[name]',
				'label' => __('Name'),
				'title' => __('Name'),
				'required' => true,
				'value' => $model->getStoreName()
			]
		);
		
		$shopFieldset->addField(
			'shop[url]',
			'text',
			[
				'name' 	=> 'shop[url]',
				'label' => __('Url'),
				'title' => __('Url'),
				'required' => true,
				'value' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, $model->getStoreId()),
				'disabled' => true
			]
		);
		
		$shopFieldset->addField(
			'shop[soap_username]',
			'text',
			[
				'name' => 'shop[soap_username]',
				'label' => __('API username'),
				'title' => __('API username'),
				'required' => true,
				'value' => ''
			]
		);
		
		$shopFieldset->addField(
			'shop[soap_api_key]',
			'text',
			[
				'name' => 'shop[soap_api_key]',
				'label' => __('API key'),
				'title' => __('API key'),
				'required' => true,
				'value' => ''
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
				'name' => 'contact_firstname',
				'label' => __('First name'),
				'title' => __('First name'),
				'required' => true,
				'value' => ($model->getContactFirstname()) ? $model->getContactFirstname() : $currentUser->getData('firstname')
			]
		);
		
		$contactFieldset->addField(
			'contact_lastname',
			'text',
			[
				'name' => 'contact_lastname',
				'label' => __('Last name'),
				'title' => __('Last name'),
				'required' => true,
				'value' => ($model->getContactLastname()) ? $model->getContactLastname() : $currentUser->getData('lastname')
			]
		);
		
		$contactFieldset->addField(
			'contact_email',
			'text',
			[
				'name' => 'contact_email',
				'label' => __('Email address'),
				'title' => __('Email address'),
				'required' => true,
				'value' => ($model->getContactEmail()) ? $model->getContactEmail() : $currentUser->getData('email'),
				'validate' => 'email'
			]
		);
		
		$contactFieldset->addField(
			'contact_telephone',
			'text',
			[
				'name' => 'contact_telephone',
				'label' => __('Telephone'),
				'title' => __('Telephone'),
				'required' => true,
				'value' => $model->getContactTelephone()
			]
		);
		
		$hiddenFieldset = $form->addFieldset(
            'hidden_fieldset',
            ['legend' => __('Hidden information'), 'class' => 'hidden']
        );
		
		$hiddenFieldset->addField(
			'success_url',
			'text',
			[
				'name' => 'success_url',
				'label' => __('Checkout success url'),
				'title' => __('Checkout success url'),
				'required' => true,
				'value' => $this->connectionHelper->getSuccessUrl($model->getStoreId())
			]
		);
		
		$hiddenFieldset->addField(
			'fail_url',
			'text',
			[
				'name' => 'fail_url',
				'label' => __('Checkout fail url'),
				'title' => __('Checkout fail url'),
				'required' => true,
				'value' => $this->connectionHelper->getFailUrl()
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
				'value' => 'test' // @TODO: Get correct wsdl endpoint url
			]
		);
		
		/** TODO: Add underwater attributes input */
		
        $form->setAction($this->configHelper->getRegisterUrl());
        $form->setUseContainer(true);
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
}