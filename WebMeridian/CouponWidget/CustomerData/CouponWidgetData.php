<?php

namespace WebMeridian\CouponWidget\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session;
use WebMeridian\CouponWidget\Service\CookieService;

class CouponWidgetData implements SectionSourceInterface
{
    /**
     * @var CookieService
     */
    protected $couponCookie;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param CookieService $couponCookie
     * @param Session $customerSession
     */
    public function __construct(
        CookieService $couponCookie,
        Session       $customerSession
    )
    {
        $this->customerSession = $customerSession;
        $this->couponCookie = $couponCookie;
    }

    /**
     * @return bool[]
     */
    public function getSectionData(): array
    {
        $checkCustomer = !$this->customerSession->isLoggedIn();
        $checkCoupon = !$this->checkCoupon();

        return [
            'isNotLoggedIn' => $checkCustomer,
            'checkCoupon' => $checkCoupon
        ];
    }

    /**
     * @return bool
     */
    public function checkCoupon(): bool
    {
        return (bool)$this->couponCookie->get();
    }

}
