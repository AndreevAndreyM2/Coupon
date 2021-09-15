<?php

namespace WebMeridian\CouponWidget\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CouponEmail extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('coupon_email', 'id');
    }
}
