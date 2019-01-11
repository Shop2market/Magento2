<?php
namespace Adcurve\Adcurve\Model;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Quote\Api\Data\CartItemInterface;
use Adcurve\Adcurve\Api\MultiItemsCartInterface as ApiInterface;

class MultiItemsCart extends \Magento\Quote\Model\Quote\Item\Repository implements ApiInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;
    protected $serviceProcessor;
    protected $quoteItemRepository;
    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $itemDataFactory;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Webapi\ServiceInputProcessor $serviceProcessor,
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $itemDataFactory,
        \Magento\Quote\Model\Quote\Item\Repository $quoteItemRepository,
        array $cartItemProcessors = []
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->serviceProcessor = $serviceProcessor;
        $this->quoteItemRepository =$quoteItemRepository;
        $this->itemDataFactory = $itemDataFactory;

    }
    /**
    * Returns greeting message to user
    *
    * @api
    * @param mixed $cartItems
    * @return array
    */
    public function multiItemsCart($cartItems) {
		

		foreach ($cartItems as $cartItem) {
			
            $this->cartItem->setSku($cartItem['sku']);
            $this->cartItem->setQuoteId($cartItem['quote_id']);
            $this->cartItem->setQty($cartItem['qty']);
            $this->cartItem->setProductOption($this->productOption);
            $this->quoteItemRepository->save($this->cartItem);

        }
        return $cartItems;

    }
}
