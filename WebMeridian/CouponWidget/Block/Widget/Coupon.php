<?php

namespace WebMeridian\CouponWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class Coupon extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/coupon.phtml";

}
