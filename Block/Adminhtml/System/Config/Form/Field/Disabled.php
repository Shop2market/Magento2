<?php 
namespace Adcurve\Adcurve\Block\Adminhtml\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
class Disabled extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected function _getElementHtml(AbstractElement $element) 
    {
        $element->setDisabled('disabled');
        return parent::_getElementHtml($element);
    }
	
}