<?php

namespace WebMeridian\CouponWidget\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as cartSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use WebMeridian\CouponWidget\Service\CookieService;

class ApplyCouponAfterAddToCart implements ObserverInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CookieService
     */
    protected $couponCookie;

    /**
     * @var cartSession
     */
    protected $cartSession;

    /**
     * @param CookieService $couponCookie
     * @param cartSession $cartSession
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        CookieService           $couponCookie,
        cartSession             $cartSession,
        CartRepositoryInterface $quoteRepository
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->couponCookie = $couponCookie;
        $this->cartSession = $cartSession;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $quote = $this->cartSession->getQuote();
        $couponCode = $this->couponCookie->get();

        if ($couponCode) {
            $quote->setCouponCode($couponCode);
            $quote->collectTotals();

            $this->quoteRepository->save($quote);
        }
    }
}
