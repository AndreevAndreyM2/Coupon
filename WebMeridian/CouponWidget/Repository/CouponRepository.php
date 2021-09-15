<?php

namespace WebMeridian\CouponWidget\Repository;

use WebMeridian\CouponWidget\Api\Data\CouponInterface;
use WebMeridian\CouponWidget\Api\Repository\CouponRepositoryInterface;
use WebMeridian\CouponWidget\Model\ResourceModel\CouponEmail as ResourceModel;
use WebMeridian\CouponWidget\Model\ResourceModel\CouponEmail\CollectionFactory;
use WebMeridian\CouponWidget\Model\CouponEmailFactory as ModelFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CouponRepository implements CouponRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    private $resource;

    /**
     * @var ModelFactory
     */
    private $modelFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $processor;

    /**
     * @var SearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * @param ResourceModel $resource
     * @param ModelFactory $modeFactory
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        ResourceModel                 $resource,
        ModelFactory                  $modeFactory,
        CollectionFactory             $collectionFactory,
        CollectionProcessorInterface  $collectionProcessor,
        SearchResultsInterfaceFactory $searchResultFactory
    )
    {
        $this->resource = $resource;
        $this->modelFactory = $modeFactory;
        $this->collectionFactory = $collectionFactory;
        $this->processor = $collectionProcessor;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @param int $id
     * @return CouponInterface
     * @throws NoSuchEntityException
     */
    public function getById($id): CouponInterface
    {
        $coupon = $this->modelFactory->create();

        $this->resource->load($coupon, $id);

        if (empty($coupon->getId())) {
            throw new NoSuchEntityException(__("Coupon data %1 not found", $id));
        }

        return $coupon;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): SearchResultsInterface
    {
        $collection = $this->collectionFactory->create();

        $this->processor->process($searchCriteria, $collection);

        $searchResult = $this->searchResultFactory->create();

        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setItems($collection->getItems());

        return $searchResult;
    }

    /**
     * @param CouponInterface $coupon
     * @return CouponInterface
     * @throws CouldNotSaveException
     */
    public function save(CouponInterface $coupon): CouponInterface
    {
        try {
            $this->resource->save($coupon);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__("Coupon data could not save"));
        }

        return $coupon;
    }

    /**
     * @param CouponInterface $coupon
     * @return CouponRepositoryInterface
     * @throws CouldNotDeleteException
     */
    public function delete(CouponInterface $coupon): CouponRepositoryInterface
    {
        try {
            $this->resource->delete($coupon);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException("Coupon not delete");
        }

        return $this;
    }

    /**
     * @param int $id
     * @return CouponRepositoryInterface
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id): CouponRepositoryInterface
    {
        $coupon = $this->getById($id);
        $this->delete($coupon);

        return $this;
    }
}
