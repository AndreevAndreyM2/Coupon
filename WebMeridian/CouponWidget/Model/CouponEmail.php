<?php

namespace WebMeridian\CouponWidget\Model;

use WebMeridian\CouponWidget\Api\Data\CouponInterface;
use Magento\Framework\Model\AbstractModel;
use WebMeridian\CouponWidget\Model\ResourceModel\CouponEmail as ResourceModel;

class CouponEmail extends AbstractModel implements CouponInterface
{

    public function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        return $this->_getData(self::ID);
    }

    /**
     * @param mixed $id
     * @return void
     *
     */
    public function setId($id)
    {
        $this->setData(self::ID, $id);
    }

    /**
     * @return mixed|null
     */
    public function getEmail()
    {
        return $this->_getData(self::EMAIL);
    }

    /**
     * @param $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->setData(self::EMAIL, $email);
    }

    /**
     * @return mixed|null
     */
    public function getCouponCode()
    {
        return $this->_getData(self::COUPON_CODE);
    }

    /**
     * @param $coupon_code
     * @return void
     */
    public function setCouponCode($coupon_code)
    {
        $this->setData(self::COUPON_CODE, $coupon_code);
    }

    /**
     * @return mixed|null
     */
    public function getIsUsed()
    {
        return $this->_getData(self::IS_USED);
    }

    /**
     * @param $is_used
     * @return void
     */
    public function setIsUsed($is_used)
    {
        $this->setData(self::IS_USED, $is_used);
    }

    /**
     * @return mixed|null
     */
    public function getDaysAvailable()
    {
        return $this->_getData(self::DAYS_AVAILABLE);
    }

    /**
     * @param $days_available
     * @return void
     */
    public function setDaysAvailable($days_available)
    {
        $this->setData(self::DAYS_AVAILABLE, $days_available);
    }

}
