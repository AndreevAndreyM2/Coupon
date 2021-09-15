<?php

namespace WebMeridian\CouponWidget\Service;

use Exception;
use Magento\Customer\Model\GroupManagement;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\SalesRule\Api\Data\RuleInterface;
use Magento\SalesRule\Model\RuleFactory;
use Magento\SalesRule\Api\Data\CouponGenerationSpecInterfaceFactory;
use Magento\SalesRule\Model\Service\CouponManagementService;
use Psr\Log\LoggerInterface;

class CouponGenerateService
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CouponGenerationSpecInterfaceFactory
     */
    protected $generationSpecFactory;

    /**
     * @var CouponManagementService
     */
    protected $couponManagementService;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @param RuleFactory $ruleFactory
     * @param CouponGenerationSpecInterfaceFactory $generationSpecFactory
     * @param CouponManagementService $couponManagementService
     * @param LoggerInterface $logger
     */
    public function __construct(
        RuleFactory                          $ruleFactory,
        CouponGenerationSpecInterfaceFactory $generationSpecFactory,
        CouponManagementService              $couponManagementService,
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->ruleFactory = $ruleFactory;
        $this->couponManagementService = $couponManagementService;
        $this->generationSpecFactory = $generationSpecFactory;
    }

    /**
     * @return string[]
     * @throws Exception
     */
    public function createCartRules()
    {
        $rule = $this->ruleFactory->create();
        $rule->setName('20% discount')
            ->setDescription('20% discount on all')
            ->setIsAdvanced(true)
            ->setStopRulesProcessing(false)
            ->setDiscountQty(0)
            ->setCustomerGroupIds(GroupManagement::NOT_LOGGED_IN_ID)
            ->setWebsiteIds([1])
            ->setUseAutoGeneration(1)
            ->setCouponType(2)
            ->setSimpleAction(RuleInterface::DISCOUNT_ACTION_BY_PERCENT)
            ->setUsesPerCoupon(1)
            ->setUsesPerCustomer(2)
            ->setDiscountAmount(20)
            ->setFromDate('2021-09-10')
            ->setToDate('2021-09-30')
            ->setIsActive(true);

        try {
            $rule->save();

            $couponId = $this->autoGenerateCouponOnSingleRules($rule);
        } catch (LocalizedException $ex) {
            $this->logger->critical($ex->getMessage());
        }
        return $couponId;
    }

    /**
     * @param $rule
     * @return string[]
     * @throws InputException
     * @throws LocalizedException
     */
    public function autoGenerateCouponOnSingleRules($rule)
    {
        $couponSpecData = [
            'rule_id' => $rule->getRuleId(),
            'qty' => 1,
            'length' => 12,
            'format' => 'alphanum',
            'prefix' => 'A',
            'suffix' => 'Z',
            'dash' => 0,
            'quantity' => 1
        ];

        $couponSpec = $this->generationSpecFactory->create(['data' => $couponSpecData]);

        return $this->couponManagementService->generate($couponSpec);
    }
}

