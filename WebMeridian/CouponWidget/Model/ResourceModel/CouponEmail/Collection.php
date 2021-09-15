<?php

namespace WebMeridian\CouponWidget\Model\ResourceModel\CouponEmail;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'webmeridian_coupon_collection';
    protected $_eventObject = 'coupon_collection';

    protected function _construct()
    {
        $this->_init('WebMeridian\CouponWidget\Model\CouponEmail', 'WebMeridian\CouponWidget\Model\ResourceModel\CouponEMail');
    }

}
