<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

class AjaxSave extends \Magento\Backend\App\Action
{
    protected $jsonFactory;
	protected $connectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Adcurve\Adcurve\Model\ConnectionFactory $connectionFactory
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
		$this->connectionFactory = $connectionFactory;
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
		$postItems = $this->getRequest()->getParam('items', []);
		/** @var Magento\Framework\App\Request\Http */
		return $resultJson->setData([
			'success' => true,
			'messages' => implode(',', $this->getRequest()->getPostValue())
		]);
		die();
        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $id) {
                    $connection = $this->connectionFactory->create('Adcurve\Adcurve\Model\Connection')->load($id);
                    try {
                        $connection->setData(array_merge($connection->getData(), $postItems[$id]));
                        $connection->save();
                    } catch (\Exception $e) {
                        $messages[] = "[Connection ID: {$id}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }
        
		echo $this->getRequest()->getPost();
				
        return $resultJson->setData([
        	'success' => 'true',
            'messages' => $messages,
            'error' => $error
        ]);
    }
}