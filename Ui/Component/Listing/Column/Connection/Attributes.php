<?php
namespace Adcurve\Adcurve\Ui\Component\Listing\Column\Connection;

use Magento\Framework\Data\OptionSourceInterface;

class Attributes implements OptionSourceInterface
{
	protected $attributeCollection;
    protected $options;
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct(
		\Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $attributeCollection
	){
		$this->attributeCollection = $attributeCollection;
	}
	
    /**
     * Get all Adcurve country options
     *
     * @return array
     */
    public function toOptionArray()
    {
		if ($this->options === null) {
			
			$this->attributeCollection
				->addFieldToFilter('frontend_label', array('neq' => ''))
	            ->addFieldToFilter('is_visible', array('eq' => '1'))
	            ->getItems();
			
			// @TODO: Continue here with attribute collection getter for product attributes
			
			$this->options = [
				['value' => '', 'label' => __('Please select')],
		        ['value' => 'AT', 'label' => __('Austria')],
		        ['value' => 'BE', 'label' => __('Belgium')],
		        ['value' => 'DK', 'label' => __('Denmark')],
		        ['value' => 'FI', 'label' => __('Finland')],
		        ['value' => 'FR', 'label' => __('France')],
		        ['value' => 'DE', 'label' => __('Germany')],
		        ['value' => 'IT', 'label' => __('Italy')],
		        ['value' => 'LU', 'label' => __('Luxembourg')],
		        ['value' => 'MT', 'label' => __('Malta')],
		        ['value' => 'NL', 'label' => __('Netherlands')],
		        ['value' => 'NO', 'label' => __('Norway')],
		        ['value' => 'PL', 'label' => __('Poland')],
		        ['value' => 'PT', 'label' => __('Portugal')],
		        ['value' => 'ES', 'label' => __('Spain')],
		        ['value' => 'SE', 'label' => __('Sweden')],
		        ['value' => 'CH', 'label' => __('Switzerland')],
		        ['value' => 'GB', 'label' => __('United Kingdom')]
	        ];
        }
		
        return $this->options;
    }
}