<?php

namespace WebMeridian\CouponWidget\Controller\Index;

use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use WebMeridian\CouponWidget\Api\Repository\CouponRepositoryInterface;
use WebMeridian\CouponWidget\Service\CookieService;
use WebMeridian\CouponWidget\Service\CouponDataService;
use WebMeridian\CouponWidget\Api\Data\CouponInterfaceFactory;
use WebMeridian\CouponWidget\Service\EmailSenderService;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use WebMeridian\CouponWidget\Model\ResourceModel\CouponEmail\CollectionFactory as couponEmailCollectionFactory;

class EmailValidation implements ActionInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var CouponRepositoryInterface
     */
    protected $couponRepository;

    /**
     * @var CouponDataService
     */
    protected $couponDataManager;

    /**
     * @var CouponInterfaceFactory
     */
    protected $couponEmailModel;

    /**
     * @var couponEmailCollectionFactory
     */
    protected $couponEmailCollection;

    /**
     * @var CookieService
     */
    protected $couponCookie;

    /**
     * @var EmailSenderService
     */
    protected $emailSender;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var CollectionFactory
     */
    protected $customerCollection;

    /**
     * @param CouponInterfaceFactory $couponEmailModel
     * @param couponEmailCollectionFactory $couponEmailCollection
     * @param CookieService $couponCookie
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $customerCollection
     * @param Session $session
     * @param EmailSenderService $emailSender
     * @param CouponRepositoryInterface $couponRepository
     * @param CartRepositoryInterface $quoteRepository
     * @param CouponDataService $couponDataManager
     * @param RequestInterface $request
     */
    public function __construct(
        CouponInterfaceFactory       $couponEmailModel,
        couponEmailCollectionFactory $couponEmailCollection,
        CookieService                $couponCookie,
        Context                      $context,
        JsonFactory                  $resultJsonFactory,
        CollectionFactory            $customerCollection,
        Session                      $session,
        EmailSenderService           $emailSender,
        CouponRepositoryInterface    $couponRepository,
        CartRepositoryInterface      $quoteRepository,
        CouponDataService            $couponDataManager,
        RequestInterface             $request
    )
    {
        $this->couponEmailCollection = $couponEmailCollection;
        $this->customerCollection = $customerCollection;
        $this->couponRepository = $couponRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->session = $session;
        $this->emailSender = $emailSender;
        $this->couponCookie = $couponCookie;
        $this->couponEmailModel = $couponEmailModel;
        $this->couponDataManager = $couponDataManager;
        $this->quoteRepository = $quoteRepository;
        $this->request = $request;
    }

    /**
     * @return Json
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     * @throws Exception
     */
    public function execute(): Json
    {
        $resultJson = $this->resultJsonFactory->create();

        $email = $this->request->getParam('email');

        if ($this->validateEmail($email)) {
            $couponCode = $this->couponDataManager->getCouponCode();

            $this->setCouponData($couponCode, $email);
            $this->couponCookie->set($couponCode);
            $this->setCouponInQuoteIfNotEmpty($couponCode);
            $this->sendCouponEmail($couponCode, $email);

            return $resultJson->setData(__("Your coupon $couponCode was applied in shopping cart"));
        }

        return $resultJson->setData(__("This email has been already used!"));
    }

    /**
     * @param $couponCode
     * @param $email
     * @return $this
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setCouponData($couponCode, $email): EmailValidation
    {
        $couponModelFactory = $this->couponEmailModel->create();

        $couponModelFactory->setEmail($email);
        $couponModelFactory->setIsUsed($this->couponDataManager->getCouponUsed($couponCode));
        $couponModelFactory->setDaysAvailable($this->couponDataManager->getCouponDaysAvailable($couponCode));
        $couponModelFactory->setCouponCode($couponCode);

        $this->couponRepository->save($couponModelFactory);

        return $this;
    }

    /**
     * @param $email
     * @return bool
     */
    public function validateEmail($email): bool
    {
        $couponEmailCollection = $this->couponEmailCollection->create();
        $customerCollection = $this->customerCollection->create();

        $customerEmail = $customerCollection
            ->addFieldToFilter('email', $email);
        $customerCouponEmail = $couponEmailCollection
            ->addFieldToFilter('email', $email);

        if (!count($customerEmail)) {
            if (!count($customerCouponEmail)) {

                return true;
            }
        }

        return false;
    }

    /**
     * @param $couponCode
     * @return CartInterface|Quote
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setCouponInQuoteIfNotEmpty($couponCode)
    {
        $quote = $this->session->getQuote();

        if ($quote->getItems()) {
            $quote->setCouponCode($couponCode);
            $quote->collectTotals();
            $this->quoteRepository->save($quote);
        }

        return $quote;
    }

    /**
     * @param $couponCode
     * @param $email
     * @return $this
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendCouponEmail($couponCode, $email): EmailValidation
    {
        $message = __("Lets use a discount, your coupon code is $couponCode");
        $this->emailSender->sendEmail($message, $email);

        return $this;
    }

}

