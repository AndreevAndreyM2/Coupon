<?php

namespace WebMeridian\CouponWidget\Api\Repository;

use WebMeridian\CouponWidget\Api\Data\CouponInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;

interface CouponRepositoryInterface
{
    /**
     * @param int $id
     * @return CouponInterface
     */
    public function getById(int $id): CouponInterface;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface;

    /**
     * @param CouponInterface $coupon
     * @return CouponInterface
     */
    public function save(CouponInterface $coupon): CouponInterface;

    /**
     * @param CouponInterface $coupon
     * @return CouponRepositoryInterface
     */
    public function delete(CouponInterface $coupon): CouponRepositoryInterface;

    /**
     * @param int $id
     * @return CouponRepositoryInterface
     */
    public function deleteById(int $id): CouponRepositoryInterface;

}
