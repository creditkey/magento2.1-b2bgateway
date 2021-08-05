<?php
namespace CreditKey\B2BGateway\Helper;

use \Psr\Log\LoggerInterface;

/**
 * Data Helper
 */
class Data
{
    /**
     * Logger
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Construct
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Return a collection of \CreditKey\Models\CartItem objects from a Quote or Order object
     * @return \CreditKey\Models\CartItem[]
     */
    public function buildCartContents($holder)
    {
        $cartContents = [];
        foreach ($holder->getAllVisibleItems() as $item) {
            $productId = (int)$item->getItemId();
            $name = $item->getName();
            $price = (float)$item->getPrice();
            $sku = $item->getSku();
            $quantity = (int)$item->getQty();

            array_push(
                $cartContents,
                new \CreditKey\Models\CartItem($productId, $name, $price, $sku, $quantity, null, null)
            );
        }

        return $cartContents;
    }

    /**
     * Return a \CreditKey\Models\Address from a Magento Address object
     * @return \CreditKey\Models\Address
     */
    public function buildAddress($magentoAddress)
    {
        $street = $magentoAddress->getStreet();
        $address1 = null;
        $address2 = null;
        
        if (count($street) >= 1) {
            $address1 = $street[0];
        }
        if (count($street) >= 2) {
            $address2 = $street[1];
        }

        return new \CreditKey\Models\Address(
            $magentoAddress->getFirstname(),
            $magentoAddress->getLastname(),
            $magentoAddress->getCompany(),
            $magentoAddress->getEmail(),
            $address1,
            $address2,
            $magentoAddress->getCity(),
            $magentoAddress->getRegionCode(),
            $magentoAddress->getPostcode(),
            $magentoAddress->getTelephone()
        );
    }

    /**
     * Return a \CreditKey\Models\Charges objects from a Quote or Order object
     * @return \CreditKey\Models\Charges
     */
    public function buildCharges($holder)
    {
        $grandTotal = (float)$holder->getGrandTotal();
        return $this->buildChargesWithUpdatedGrandTotal($holder, $grandTotal);
    }

    /**
     * Return a \CreditKey\Models\Charges objects from a Quote or Order object, but with an updated grand total amount.
     * @return \CreditKey\Models\Charges
     */
    public function buildChargesWithUpdatedGrandTotal($holder, $updatedGrandTotal)
    {
        $total = (float)$holder->getSubtotal();

        $shippingAmount = $holder->getShippingAmount() == null
          ? (float)0
          : (float)$holder->getShippingAmount();

        if ($shippingAmount == 0) {
            $shippingAmount = $holder->getShippingAddress() == null
              ? (float)0
              : (float)$holder->getShippingAddress()->getShippingAmount();
        }

        $tax = $holder->getBillingAddress() == null
          ? (float)0
          : (float)$holder->getBillingAddress()->getTaxAmount();

        if ($tax == 0) {
            $tax = $holder->getShippingAddress() == null
              ? (float)0
              : (float)$holder->getShippingAddress()->getTaxAmount();
        }

        if ($tax == 0) {
            $tax = $holder->getTaxAmount();
        }

        $discount = $holder->getSubtotalWithDiscount() == null
          ? (float)0
          : (float)$holder->getSubtotal() - $holder->getSubtotalWithDiscount();

        if ($discount == 0) {
            $discount = $holder->getDiscountAmount() == null
              ? (float)0
              : (float)abs($holder->getDiscountAmount());
        }

        return new \CreditKey\Models\Charges($total, $shippingAmount, $tax, $discount, $updatedGrandTotal);
    }
}
