<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Integration;

class Activation extends \Magento\Backend\App\Action
{
	const URL_PATH_ADCURVE_INTEGRATION_ACTIVATE = 'adcurve_adcurve/integration/activate';
	
	protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Integration\Model\IntegrationService $integrationService
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->integrationService = $integrationService;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	var_dump('Activation page, to do: create block with explanation and button to activate!');
		die();
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Adcurve Integration activation'));
        return $resultPage;
    }
}
