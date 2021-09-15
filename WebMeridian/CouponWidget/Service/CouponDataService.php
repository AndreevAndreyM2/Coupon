<?php

namespace WebMeridian\CouponWidget\Service;

use Exception;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Api\Data\CouponInterface;
use Psr\Log\LoggerInterface;

class CouponDataService
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var RuleRepositoryInterface
     */
    protected $ruleRepository;

    /**
     * @var CouponInterface
     */
    protected $coupon;

    /**
     * @var CouponGenerateService
     */
    protected $couponGenerate;

    /**
     * @param CouponInterface $coupon
     * @param CouponGenerateService $couponGenerate
     * @param RuleRepositoryInterface $ruleRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        CouponInterface         $coupon,
        CouponGenerateService   $couponGenerate,
        RuleRepositoryInterface $ruleRepository,
        LoggerInterface         $logger
    )
    {
        $this->logger = $logger;
        $this->ruleRepository = $ruleRepository;
        $this->coupon = $coupon;
        $this->couponGenerate = $couponGenerate;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function createCoupon(): array
    {
        return $this->couponGenerate->createCartRules();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCouponCode(): string
    {
        $couponCodesData = $this->createCoupon();

        return implode($couponCodesData);
    }

    /**
     * @param $couponCode
     * @return RuleInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws InputException
     */
    public function getRule($couponCode): RuleInterface
    {
        $ruleId = $this->coupon->loadByCode($couponCode)->getRuleId();

        try {
            $rule = $this->ruleRepository->getById($ruleId);
        } catch (NoSuchEntityException | LocalizedException $e) {
            $this->logger->critical($e->getMessage());
        }

        $this->ruleRepository->save($rule);

        return $rule;
    }

    /**
     * @param $couponCode
     * @return int
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCouponDaysAvailable($couponCode): int
    {
        $rule = $this->getRule($couponCode);

        $toDate = date_create($rule->getToDate());
        $fromDate = date_create($rule->getFromDate());
        $dateDiff = date_diff($toDate, $fromDate);

        return $dateDiff->d;
    }

    /**
     * @param $couponCode
     * @return int
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCouponUsed($couponCode): int
    {
        $rule = $this->getRule($couponCode);

        return $rule->getTimesUsed();
    }

}
