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
            	'id' 		=> 'adcurve_registration_form',
            	'action' 	=> $this->getData('action'),
            	'method' 	=> 'post',
            	'enctype' 	=> 'multipart/form-data'
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
			'shop[soap_username]',
			'text',
			[
				'name' => 'shop[soap_username]',
				'label' => __('API username'),
				'title' => __('API username'),
				'required' => true,
				'value' => $model->getSoapUsername()
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
				'value' => $model->getSoapApiKey()
			]
		);
		
		$contactFieldset = $form->addFieldset(
            'contact_fieldset',
            ['legend' => __('Contact information'), 'class' => 'fieldset-wide']
        );
		
		$contactFieldset->addField(
			'contact[first_name]',
			'text',
			[
				'name' => 'contact[first_name]',
				'label' => __('First name'),
				'title' => __('First name'),
				'required' => true,
				'value' => ($model->getContactFirstname()) ? $model->getContactFirstname() : $currentUser->getData('firstname')
			]
		);
		
		$contactFieldset->addField(
			'contact[last_name]',
			'text',
			[
				'name' => 'contact[last_name]',
				'label' => __('Last name'),
				'title' => __('Last name'),
				'required' => true,
				'value' => ($model->getContactLastname()) ? $model->getContactLastname() : $currentUser->getData('lastname')
			]
		);
		
		$contactFieldset->addField(
			'contact[email]',
			'text',
			[
				'name' => 'contact[email]',
				'label' => __('Email address'),
				'title' => __('Email address'),
				'required' => true,
				'value' => ($model->getContactEmail()) ? $model->getContactEmail() : $currentUser->getData('email'),
				'validate' => 'email'
			]
		);
		
		$contactFieldset->addField(
			'contact[phone]',
			'text',
			[
				'name' => 'contact[phone]',
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
			'shop[url]',
			'text',
			[
				'name' 	=> 'shop[url]',
				'label' => __('Url'),
				'title' => __('Url'),
				'required' => true,
				'value' => $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, $model->getStoreId()),
			]
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
				'value' => 'test' // @TODO: Get correct wsdl endpoint url('s) (multiple for Magento 2)
				// Structure is http://<magento.host>/soap/<optional_store_code>?wsdl&services=<service_name_1>,<service_name_2>
				// This is saved in the config, but a solution first has to be discussed with the adcurve software team
			]
		);
		
		$hiddenFieldset->addField(
			'attributes[]',
			'multiselect',
			[
				'name' => 'attributes[]',
				'label' => __('Product attributes'),
				'title' => __('Product attributes'),
				'required' => true,
				'values' => $this->productAttributeOptions->toOptionArray(),
				'class' => 'multiselect'
			]
		);
		
        $form->setAction($this->configHelper->getRegisterUrl());
        $form->setUseContainer(true);
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
}