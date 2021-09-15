<?php

namespace WebMeridian\CouponWidget\Ui\Component\Listing\Column;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use WebMeridian\CouponWidget\Service\CouponDataService;

class IsUsed extends Column
{
    /**
     * @var CouponDataService
     */
    protected $couponDataService;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CouponDataService $couponDataService
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        CouponDataService  $couponDataService,
        array              $components = [],
        array              $data = []
    )
    {
        $this->couponDataService = $couponDataService;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                $couponCode = $items['coupon_code'];

                $items['is_used'] = __('No');
                if ($this->couponDataService->getCouponUsed($couponCode)) {
                    $items['is_used'] = __('Yes');

                }
            }
        }

        return $dataSource;
    }
}
