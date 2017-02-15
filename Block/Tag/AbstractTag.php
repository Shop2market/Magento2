<?php
namespace Adcurve\Adcurve\Block\Tag;

class AbstractTag extends \Magento\Framework\View\Element\Template
{
	public $configHelper;
	public $tagHelper;
	
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Adcurve\Adcurve\Helper\Config $configHelper,
		\Adcurve\Adcurve\Helper\Tag $tagHelper,
		array $data = []
	){
		$this->configHelper = $configHelper;
		$this->tagHelper = $tagHelper;
		
		parent::__construct($context, $data);
	}
}
