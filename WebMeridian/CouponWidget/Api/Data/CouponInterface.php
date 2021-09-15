<?php

namespace WebMeridian\CouponWidget\Api\Data;

interface CouponInterface
{
    const ID = 'id';

    const EMAIL = 'email';

    const COUPON_CODE = 'coupon_code';

    const IS_USED = 'is_used';

    const DAYS_AVAILABLE = 'days_available';

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param $id
     * @return mixed
     */
    public function setId($id);

    /**
     * @return mixed
     */
    public function getEmail();

    /**
     * @param $email
     * @return mixed
     */
    public function setEmail($email);

    /**
     * @return mixed
     */
    public function getCouponCode();

    /**
     * @param $coupon_code
     * @return mixed
     */
    public function setCouponCode($coupon_code);

    /**
     * @return mixed
     */
    public function getIsUsed();

    /**
     * @param $is_used
     * @return mixed
     */
    public function setIsUsed($is_used);

    /**
     * @return mixed
     */
    public function getDaysAvailable();

    /**
     * @param $days_available
     * @return mixed
     */
    public function setDaysAvailable($days_available);

}
