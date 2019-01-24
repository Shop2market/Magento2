<?php
namespace Adcurve\Adcurve\Api\Data;

interface MultiItemsCartInterface
{
    /**
     * Add the specified cart item.
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $cartItem The item.
     * @return \Magento\Quote\Api\Data\CartItemInterface Item.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified item could not be saved to the cart.
     * @throws \Magento\Framework\Exception\InputException The specified item or cart is not valid.
     */
    public function multiItemsCart(\Magento\Quote\Api\Data\CartItemInterface $cartItem);
	
}