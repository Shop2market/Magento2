<?php
namespace Adcurve\Adcurve\Helper;

class Tag extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $customerSession;
	protected $taxgimmickHelper;
	
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Customer\Model\Session $customerSession,
		\Adcurve\Adcurve\Helper\Taxgimmick $taxgimmickHelper,
		array $data = []
	){
		parent::__construct($context);
		
		$this->customerSession = $customerSession;
		// \Magento\Catalog\Helper\Data contains the original function but did not calculate tax correctly
		$this->taxgimmickHelper = $taxgimmickHelper;
	}
	
    /**
     * Return customer id logged in, if is not logged in return empty
     *
     * @return string
     */
    public function getCustomerId()
    {
        if (!$this->customerSession->isLoggedIn()){
            return '';
        }
        return $this->customerSession->getCustomer()->getId();
    }

    /**
     * Return md5 of customer email logged in, if is not logged in return empty
     *
     * @return string
     */
    public function getCustomerEmailHash()
    {
        if (!$this->customerSession->isLoggedIn()){
            return '';
        }
        $email = $this->customerSession->getCustomer()->getData('email');
        return md5($email);
    }

    /**
     * Return final price including tax of $product
     *
     * @param Mage_Catalog_Model_Product $product
     *
     * @return mixed
     */
    public function getProductPriceInclTax(\Magento\Catalog\Model\Product $product)
    {
    	if ($product) {
			return $this->taxgimmickHelper->getTaxPrice($product, $product->getFinalPrice(), true);
    	}
		return false;
    }

    /**
     * Return final price excluding tax of $product
     *
     * @param $product
     *
     * @return mixed
     */
    public function getProductPriceExclTax(\Magento\Catalog\Model\Product $product)
    {
    	if ($product) {
			return $this->taxgimmickHelper->getTaxPrice($product, $product->getFinalPrice(), false);
    	}
		return false;
    }
}