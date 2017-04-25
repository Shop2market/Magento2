<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

class Index extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
	const URL_PATH_ADCURVE_INTEGRATION_ACTIVATION = 'adcurve_adcurve/integration/activation';
	
	protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Integration\Model\IntegrationService $integrationService
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->integrationService = $integrationService;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	/** @var \Magento\Integration\Model\Integration $integration */
    	$integration = $this->integrationService->findByName('AdcurveIntegration');
		if (!$integration->getStatus()) {
			$this->messageManager->addError(__('Adcurve Integration Service needs to be activated first.'));
			/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
			$resultRedirect = $this->resultRedirectFactory->create();
			return $resultRedirect->setPath(self::URL_PATH_ADCURVE_INTEGRATION_ACTIVATION);
		}
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Adcurve Connection Management'));
        return $resultPage;
    }
}
