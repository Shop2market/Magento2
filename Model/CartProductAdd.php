<?php

namespace Adcurve\Adcurve\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\Form\FormKey;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Adcurve\Adcurve\Api\CartProductAddInterface as ApiInterface;

class CartProductAdd implements ApiInterface
{
    //class CartProductAdd extends \Magento\Quote\Model\Quote\Item\Repository implements ApiInterface {


    protected $formKey;
    protected $product;
    protected $_logger;


    /**
     * Quote / Cart Repository
     * @var CartRepositoryInterface $quoteRepository
     */
    protected $quoteRepository;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Model\Quote\Item\Repository $quoteItemRepository,
        FormKey $formKey,
        Product $product,
        \Magento\Quote\Api\Data\CartItemInterface $cartItem,
        \Psr\Log\LoggerInterface $logger,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->formKey = $formKey;
        $this->product = $product;
        $this->_logger = $logger;
        $this->cartItem = $cartItem;
        $this->quoteItemRepository = $quoteItemRepository;
        $this->productRepository = $productRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    /**
     * Updates the specified cart with the specified products.
     *
     * @api
     * @param string $quoteId.
     * @param mixed $productsData
     * @param int $store.
     * @return bool.
     */
    public function addProduct($quoteId, $productsData = null, $store = null)
    {
        if (!is_numeric($quoteId)) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'masked_id');
            $quoteId = $quoteIdMask->getQuoteId();
        }


        $quote = $this->quoteRepository->getActive($quoteId);

        if ($quote->hasItems()) {
            //$quote->removeAllItems();
        }

        if (!empty($productsData)) {
            foreach ($productsData as $product) {
                $sku = $product["sku"];
                $qty = $product["qty"];

                $add_product = $this->productRepository->get($sku);
                $productType = $add_product->getTypeId();

                if (!empty($product["product_option"])) {
                    $add_product->addCustomOption('additional_options', serialize($product["product_option"]));
                }

                $quote->addProduct($add_product, $qty);
            }
        }

        $this->quoteRepository->save($quote->collectTotals());



        return true;
    }
}
