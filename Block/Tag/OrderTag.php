<?php

namespace Adcurve\Adcurve\Block\Tag;

class OrderTag extends \Adcurve\Adcurve\Block\Tag\AbstractTag
{
    public const ORDER_TRANS_TYPE = 'Order';

    protected $checkoutSession;
    protected $onepageModel;

    protected $orderInfo;
    protected $itemsInfo;
    protected $customerInfo;
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Adcurve\Adcurve\Helper\Tag $tagHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Type\Onepage $onepageModel,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->onepageModel = $onepageModel;

        parent::__construct($context, $configHelper, $tagHelper, $data);
    }

    /**
     * Return the most recently placed order
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->checkoutSession->getLastRealOrder();
        }
        return $this->_order;
    }

    /**
     * Get all order info to tag
     *
     * @return array $orderInfo
     */
    public function getOrderInfo()
    {
        if (empty($this->orderInfo)) {
            $order = $this->_getOrder();
            $this->orderInfo = [
                'trans_id'              => $order->getIncrementId(),
                'trans_type'            => self::ORDER_TRANS_TYPE,
                'price_including_tax'   => $order->getBaseSubtotalInclTax(),
                'price_excluding_tax'   => $order->getBaseSubtotal(),
                'shipping_cost'         => $order->getBaseShippingAmount(),
                'coupon_code'           => $order->getCouponCode()
            ];
        }
        return $this->orderInfo;
    }

    /**
     * Return all order item info to tag
     *
     * @return array $itemsInfo
     */
    public function getItemsInfo()
    {
        if (empty($this->itemsInfo)) {
            $order = $this->_getOrder();

            /** @var \Magento\Sales\Model\Order\Item $item */
            foreach ($order->getAllItems() as $item) {
                $this->itemsInfo[] = [
                    'product_id'            => $item->getProductId(),
                    'product_name'          => htmlspecialchars($item->getProduct()->getName(), ENT_QUOTES),
                    'price_including_tax'   => $this->tagHelper->getProductPriceInclTax($item->getProduct()),
                    'price_excluding_tax'   => $this->tagHelper->getProductPriceExclTax($item->getProduct()),
                    'qty'                   => round($item->getQtyOrdered())
                ];
            }
        }
        return $this->itemsInfo;
    }

    /**
     * Return all customer info to tag
     *
     * @return Varien_Object
     */
    public function getCustomerInfo()
    {
        if (empty($this->customerInfo)) {
            $order = $this->_getOrder();
            $this->customerInfo = [
                'is_new'    => $this->_getIsCustomerNew(),
                'email'     => md5($order->getCustomerEmail()),
                'id'        => $order->getCustomerId(),
            ];
        }
        return $this->customerInfo;
    }

    /**
     * Return if the customer is new or not
     *
     * @return int|string
     */
    protected function _getIsCustomerNew()
    {
        switch ($this->onepageModel->getCheckoutMethod()) {
            case \Magento\Checkout\Model\Type\Onepage::METHOD_GUEST:
                $isNew = '';
                break;
            case \Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER:
                $isNew = 1;
                break;
            default:
                $isNew = 0;
                break;
        }
        return $isNew;
    }

    /**
     * Return order info in get params format
     *
     * @return string
     */
    public function getOrderUrlParams()
    {
        $orderInfo = $this->getOrderInfo();
        $result = "&trans_id="  . $orderInfo['trans_id']
            . "&trans_type="    . $orderInfo['trans_type']
            . "&amount_1="      . $orderInfo['price_including_tax']
            . "&amount_2="      . $orderInfo['price_excluding_tax']
            . "&amount_3="      . $orderInfo['shipping_cost']
            . "&coupon_code="   . $orderInfo['coupon_code'];
        return $result;
    }

    /**
     * Get items info in GET parameter format
     *
     * @return string
     */
    public function getItemsUrlParams()
    {
        $itemsInfo = $this->getItemsInfo();
        $orderInfo = $this->getOrderInfo();
        $result = '';
        foreach ($itemsInfo as $itemInfo) {
            $result .= "&oi[][trans_id]="   . $orderInfo['trans_id']
                . "&oi[][item_id]="         . $itemInfo['product_id']
                . "&oi[][item_info_1]="     . urlencode($itemInfo['product_name'])
                . "&oi[][item_amount_1]="   . $itemInfo['price_including_tax']
                . "&oi[][item_amount_2]="   . $itemInfo['price_excluding_tax']
                . "&oi[][quantity]="        . $itemInfo['qty'];
        }

        return $result;
    }

    /**
     * Get customer info in GET parameter format
     *
     * @return string
     */
    public function getCustomerParams()
    {
        $customerInfo = $this->getCustomerInfo();
        $result = "&ui[user_email]="    . urlencode($customerInfo['email'])
            . "&ui[customer_id]="       . $customerInfo['id']
            . "&ui[new_customer]="      . $customerInfo['is_new'];

        return $result;
    }
}
