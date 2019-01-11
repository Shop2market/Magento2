<?php 
namespace Adcurve\Adcurve\Block\Adminhtml\Connection;
 
class CustomData extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'connection/customdata.phtml';
	protected $productAttributeOptions;
	private $_objectManager;
 
    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
		\Adcurve\Adcurve\Ui\Component\Listing\Column\Connection\Attributes $productAttributeOptions,
		\Magento\Framework\ObjectManagerInterface $objectmanager,
        array $data = []
    ) {
        parent::__construct($context, $data);
		$this->productAttributeOptions = $productAttributeOptions;
		$this->_objectManager = $objectmanager;
    }
	
	public function getAttributesArray()
    {
		$attributesToSkip=$this->productAttributeOptions->toOptionArray();
        return $attributesToSkip;
    }
	
	public function getAllValues(){
		
		$allValues=$this->productAttributeOptions->getAllValues();
		return $allValues;
	}
	
	public function getExcludedAttributes(){
		
		$arrExc="[]";
		$id = $this->getRequest()->getParam('connection_id');
        $connection = $this->_objectManager->create('Adcurve\Adcurve\Model\Connection');
        if ($id) {
            $connection->load($id);
			$excludedAttributes=$connection->getExcludedAttributes();
			$arrExc=$excludedAttributes;
		}
		return $arrExc;
	}
}