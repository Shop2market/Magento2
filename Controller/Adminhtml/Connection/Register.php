<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

use Magento\Framework\Exception\LocalizedException;

class Register extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
	protected $_storeManager;
	protected $connectionFactory;
	
	/**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Adcurve\Adcurve\Model\ConnectionFactory $connectionFactory
    ) {
    	$this->_storeManager = $storeManager;
		$this->connectionFactory = $connectionFactory;
        parent::__construct($context, $coreRegistry);
    }
	
	/**
     * Refreshes all store connections with Adcurve
	 * 
	 * Creates new entites is stores are missing and checks connection if store exists and is enabled
     *
     * @return void
     */
    public function execute()
    {
    	/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
		
		$data = $this->getRequest()->getPostValue();
		/** @TODO Complete registration
		var_dump($data);
		die();
	 	*/
	}
}