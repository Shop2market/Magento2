<?php
namespace Adcurve\Adcurve\Block\Tag;

class CartTag extends \Adcurve\Adcurve\Block\Tag\AbstractTag
{
	protected $cart;
	
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Adcurve\Adcurve\Helper\Config $configHelper,
		\Adcurve\Adcurve\Helper\Tag $tagHelper,
		\Magento\Checkout\Model\Cart $cart,
		array $data = []
	){
    	$this->cart = $cart;
		
		parent::__construct($context, $configHelper, $tagHelper, $data);
	}
	
    /**
     * Return all visible items of current cart
     *
     * @return mixed
     */
    public function getItems()
    {
    	// Notice: getAllItems could be used to get both configurable and invisible simple product variants.
        return $this->cart->getQuote()->getAllVisibleItems();
    }
}