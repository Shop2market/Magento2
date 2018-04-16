<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Integration;

class Activate extends \Magento\Backend\App\Action
{
	protected $resultPageFactory;
	protected $integrationService;
	protected $_oauthService;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Integration\Model\IntegrationService $integrationService,
        \Magento\Integration\Api\OauthServiceInterface $oauthService
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->integrationService = $integrationService;
		$this->_oauthService = $oauthService;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$integration = $this->integrationService->findByName('AdcurveIntegration');
		try {
			$this->_oauthService->createAccessToken($integration->getConsumerId());
        	$integration->setStatus(\Magento\Integration\Model\Integration::STATUS_ACTIVE)->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*');
            return;
        } catch (\Exception $e) {
            $this->_logger->critical($e);
            $this->messageManager->addError(__('Internal error. Check exception log for details.'));
            $this->_redirect('*/*');
            return;
        }
		
		if (!$integration || !$integration->getStatus()) {
			$this->messageManager->addError(__('Integration could not activate correctly, please try again. If the problem persists, please contact developer support to help with this issue.'));
			$this->_redirect('*/*');
			return;
		}
		
		$this->messageManager->addSuccess(__('Adcurve Magento Integration was successfully established.'));
        $resultRedirect = $this->resultRedirectFactory->create();
		return $resultRedirect->setPath('adcurve_adcurve/connection/index');
    }
}
