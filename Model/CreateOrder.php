<?php

namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\CreateOrderInterface;
use Magento\Checkout\Model\Cart;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;

class CreateOrder extends \Magento\Framework\Model\AbstractModel implements CreateOrderInterface
{
    protected $quoteFactory;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @var EventManager
     */

    protected $eventManager;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    protected $_session;
    protected $_registry;

    public function __construct(
        EventManager $eventManager,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Order $order,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->_product = $product;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->order = $order;
        $this->currencyFactory = $currencyFactory;
        $this->_productFactory = $productFactory;
        $this->checkoutSession = $checkoutSession;
        $this->eventManager = $eventManager;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->_session = $sessionManager;
        $this->_registry = $registry;
    }

    /**
     * Return order id.
     *
     * @api
     * @param string $quoteId.
     * @param int $storeId.
     * @param mixed $agreements.
     * @param mixed $orderAdditionalAttributes
     * @return int Order id.
     */
    public function createOrder($quoteId, $storeId = null, $agreements = null, $orderAdditionalAttributes = null)
    {
        if (!is_numeric($quoteId)) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'masked_id');
            $quoteId = $quoteIdMask->getQuoteId();
        }


        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $QuoteManagement = $objectManager->create('\Magento\Quote\Model\QuoteManagement');


        $quote = $this->quoteFactory->create()->load($quoteId);

        if ($quote->getCheckoutMethod() === "guest" || $quote->getCheckoutMethod() === null) {
            $quote->setCustomerId(null);
            $quote->setCustomerEmail($quote->getBillingAddress()->getEmail());
            $quote->setCustomerIsGuest(true);
            $quote->setCustomerGroupId(\Magento\Customer\Api\Data\GroupInterface::NOT_LOGGED_IN_ID);
        }


        if ($orderAdditionalAttributes) {
            //set Additional attributes
            $adcurveShippingPrice = $orderAdditionalAttributes["adcurveShippingPrice"];
            $quote->setAdcurveOrderSource($orderAdditionalAttributes["adcurveOrderSource"]);
            $quote->setAdcurveOrderId($orderAdditionalAttributes["adcurveOrderId"]);
            $quote->setAdcurveShippingPrice($adcurveShippingPrice);

            if ($adcurveShippingPrice > 0) {
                $sessionId = $this->_session->getSessionId();
                $this->_registry->register('adcurveShippingPriceValue' . $sessionId, $adcurveShippingPrice);

                $shippingAddress = $quote->getShippingAddress();
                $shippingAddress->setCollectShippingRates(true)
                ->collectShippingRates()
                ->setShippingMethod('adcurveshipping_adcurveshipping'); //shipping method
            }
        }

        $quote->setPaymentMethod('adcurvepayment'); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'adcurvepayment']);

        $this->eventManager->dispatch('checkout_submit_before', ['quote' => $quote]);
        $order = $QuoteManagement->submit($quote);

        if (null == $order) {
            return false;
            //throw new LocalizedException(
              //  __('An error occurred on the server. Please try to place the order again.')
           // );
        }

        $this->eventManager->dispatch('checkout_submit_all_after', ['order' => $order, 'quote' => $quote]);

        if ($adcurveShippingPrice > 0) {
            $order->setAdcurveOrderSource($orderAdditionalAttributes["adcurveOrderSource"]);
            $order->setAdcurveOrderId($orderAdditionalAttributes["adcurveOrderId"]);
            $order->setAdcurveShippingPrice($adcurveShippingPrice);
            $order->save();
        }
        return $order->getId();
        exit;
    }
}
