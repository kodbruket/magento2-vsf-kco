<?php
namespace Kodbruket\VsfKco\Plugin;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class BeforeVsfPullCart
 * @package Kodbruket\VsfKco\Plugin
 */
class BeforeVsfPullCart
{
    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * BeforeVsfPullCart constructor.
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    )
    {
        $this->cartManagement          = $cartManagement;
        $this->quoteRepository         = $quoteRepository;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item\Repository $subject
     * @param $cartId
     * @return int
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function beforeGetList(
        \Magento\Quote\Model\Quote\Item\Repository $subject,
        $cartId
    ) {
        try {
            $this->quoteRepository->getActive($cartId);
        }catch (NoSuchEntityException $e) {
            $quote = $this->quoteRepository->get($cartId);

            if ( $quote->getCustomer() && $quote->getCustomer()->getId() ) {
                $cartId = $this->cartManagement->createEmptyCartForCustomer($quote->getCustomer()->getId());
            } else {
                $cartId = $this->cartManagement->createEmptyCart();
            }
        }
        return $cartId;
    }
}
