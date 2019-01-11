<?php
namespace Adcurve\Adcurve\Block\System\Config\Fieldset;

class SupportTab extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    const NEW_TICKET_URL_XPATH 			= 'adcurve/support/new_ticket_url';
    const INSTALLATION_MANUAL_URL_XPATH = 'adcurve/support/installation_guide_url';
    const KB_URL_XPATH 					= 'adcurve/support/adcurve_kb_url';
    const RELEASE_NOTES_URL_XPATH       = 'adcurve/support/release_notes_url';
    const PUBLISHERS_URL_XPATH 			= 'adcurve/support/publishers';
    const FEATURES_URL_XPATH   			= 'adcurve/support/features';
    const PRICING_URL_XPATH	   			= 'adcurve/support/pricing';
    const DOCUMENTATION_URL_XPATH 		= 'adcurve/support/documentation';
    const EMAIL_CONTACT 				= 'adcurve/support/email';
    const PHONE_CONTACT 				= 'adcurve/support/phone';
	
	public $configHelper;
    protected $_template = 'Adcurve_Adcurve::system/config/fieldset/supporttab.phtml';

	/**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface
     */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Adcurve\Adcurve\Helper\Config $configHelper,
		array $data = []
	){
		parent::__construct($context, $data);
		
		$this->configHelper = $configHelper;
	}

    /**
     * Returns the URL to get more information about publisher.
     *
     * @return string
     */
    public function getPublishersUrl()
    {
        return $this->_scopeConfig->getValue(self::PUBLISHERS_URL_XPATH);
    }

    /**
     * Returns the URL to get more information about features.
     *
     * @return string
     */
    public function getFeaturesUrl()
    {
        $url = $this->_scopeConfig->getValue(self::FEATURES_URL_XPATH);

        return $url;
    }

    /**
     * Returns the URL to get more information about pricing.
     *
     * @return string
     */
    public function getPricingUrl()
    {
        $url = $this->_scopeConfig->getValue(self::PRICING_URL_XPATH);

        return $url;
    }

    /**
     * Returns the URL to get more information about documentation.
     *
     * @return string
     */
    public function getDocumentationUrl()
    {
        $url = $this->_scopeConfig->getValue(self::DOCUMENTATION_URL_XPATH);

        return $url;
    }
	
    /**
     * Returns the URL to get more information about contact email.
     *
     * @return string
     */
    public function getContactEmail()
    {
        $url = $this->_scopeConfig->getValue(self::EMAIL_CONTACT);

        return $url;
    }
	
    /**
     * Returns the URL to get more information about contact phone.
     *
     * @return string
     */
    public function getContactPhone()
    {
        $url = $this->_scopeConfig->getValue(self::PHONE_CONTACT);

        return $url;
    }

    /**
     * Returns the URL to create a new Support ticket.
     *
     * @return string
     */
    public function getNewTicketUrl()
    {
        $url = $this->_scopeConfig->getValue(self::NEW_TICKET_URL_XPATH);

        return $url;
    }

    /**
     * Returns the URL to the installation manual.
     *
     * @return string
     */
    public function getInstallationManualUrl()
    {
        $url = $this->_scopeConfig->getValue(self::INSTALLATION_MANUAL_URL_XPATH);

        return $url;
    }

    /**
     * Returns the URL to the current changelog.
     *
     * @return string
     */
    public function getChangelogUrl()
    {
        $url = $this->_scopeConfig->getValue(self::RELEASE_NOTES_URL_XPATH);

        return $url;
    }

    /**
     * Returns the URL to the Knowledge Base.
     *
     * @return string
     */
    public function getKbUrl()
    {
        $url = $this->_scopeConfig->getValue(self::KB_URL_XPATH);

        return $url;
    }

    /**
     * Render html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->toHtml();
    }
}