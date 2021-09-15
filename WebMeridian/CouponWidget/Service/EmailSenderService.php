<?php

namespace WebMeridian\CouponWidget\Service;

use Magento\Framework\App\Area;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\Store;

class EmailSenderService
{
    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        TransportBuilder $transportBuilder
    )
    {
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param $message
     * @param $email
     * @throws LocalizedException
     * @throws MailException
     */
    public function sendEmail($message, $email)
    {
        $data = ['myvar' => $message];
        $postObject = new DataObject();
        $postObject->setData($data);

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('send_email_email_template')
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => Store::DEFAULT_STORE_ID])
            ->setTemplateVars(['data' => $postObject])
            ->setFromByScope(['name' => 'Robot', 'email' => 'leonid.leonidovich.96@gmail.com'])
            ->addTo($email)
            ->getTransport();
        $transport->sendMessage();
    }

}
