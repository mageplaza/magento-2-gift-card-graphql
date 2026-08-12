<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_GiftCardGraphQl
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\GiftCardGraphQl\Model\Resolver;

use DateTime;
use DateTimeZone;
use Exception;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mageplaza\GiftCard\Api\GiftCardManagementInterface;
use Mageplaza\GiftCard\Helper\Data as HelperData;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Source\DeliveryMethods;

/**
 * Class PreviewEmail
 * @package Mageplaza\GiftCardGraphQl\Model\Resolver
 */
class PreviewEmail implements ResolverInterface
{
    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @var GiftCardManagementInterface
     */
    protected $giftCardManagement;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * PreviewEmail constructor.
     *
     * @param GiftCardFactory $giftCardFactory
     * @param HelperData $helperData
     * @param GiftCardManagementInterface $giftCardManagement
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        GiftCardFactory $giftCardFactory,
        HelperData $helperData,
        GiftCardManagementInterface $giftCardManagement,
        TimezoneInterface $timezone
    ) {
        $this->giftCardFactory    = $giftCardFactory;
        $this->helperData         = $helperData;
        $this->giftCardManagement = $giftCardManagement;
        $this->timezone           = $timezone;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $productData = $args['input'];

        if ($productData['balance'] <= 0) {
            throw new GraphQlInputException(__('Balance must be greater than 0'));
        }

        if (!in_array((int) $productData['status'], range(1, 6), true)) {
            throw new GraphQlInputException(__('Status must be an integer between 1 and 6'));
        }

        if (!($productData['giftcode_pattern'])) {
            throw new GraphQlInputException(__('Gift code pattern must be a non-empty string'));
        }

        if (!in_array((int) $productData['delivery_method'], range(1, 4), true)) {
            throw new GraphQlInputException(__('Delivery method must be an integer between 1 and 4'));
        }

        if (isset($productData['expire_after']) && (int) $productData['expire_after'] <= 0) {
            throw new GraphQlInputException(__('Expire after must be a number greater than 0 if provided'));
        }

        $giftCard = $this->giftCardFactory->create()->addData($productData);
        if (isset($productData['giftcode_pattern'])) {
            $giftCard->setCode($productData['giftcode_pattern']);
        }
        $productData['expire_after'] = $productData['expire_after'] ?? 30;
        $productData['timezone']     = $productData['timezone'] ?? $this->timezone->getConfigTimezone();
        $timezone                    = new DateTimeZone($productData['timezone']);
        $expiredAt                   = (new DateTime(
            '+' . $productData['expire_after'] . ' day',
            $timezone
        ))->format('Y-m-d');
        $giftCard->setExpiredAt($expiredAt);

        $deliveryMethod = (int) $giftCard->getDeliveryMethod();
        $result         = '';
        $params         = [];

        switch ($deliveryMethod) {
            case DeliveryMethods::METHOD_PRINT:
                $params['is_print'] = true;
                // no break
            case DeliveryMethods::METHOD_EMAIL:
                $templateFields = $giftCard->getTemplateFields()
                    ? HelperData::jsonDecode($giftCard->getTemplateFields())
                    : [];

                if (isset($params['is_print'])) {
                    $params['print_true'] = '1';
                } else {
                    $params['print_false'] = '1';
                }

                $params = array_merge([
                    'sender'          => $templateFields['sender'] ?? '',
                    'recipient'       => $templateFields['recipient'] ?? '',
                    'message'         => $templateFields['message'] ?? '',
                    'balanceFormated' => $this->helperData->convertPrice(
                        $giftCard->getBalance(),
                        true,
                        false
                    ),
                    'status_label'    => $giftCard->getStatusLabel(),
                    'expired_date'    => $giftCard->getExpiredAt()
                        ? $this->helperData->formatDate($giftCard->getExpiredAt())
                        : null,
                    'giftcard'        => $giftCard
                ], $params);

                $fileUrl      = $this->giftCardManagement->getPreviewGiftCardPdfUrl($giftCard);
                $emailContent = $this->helperData->getEmailHelper()->getEmailTemplate(
                    $this->helperData->getEmailConfig('template'),
                    $params
                );
                $emailHtml    = htmlspecialchars($emailContent->processTemplate());
                $result       = $this->giftCardManagement->getPreviewEmailHtml($emailHtml, $fileUrl);
                break;

            case DeliveryMethods::METHOD_SMS:
                $result = $this->helperData->getSmsHelper()->generateMessageContent($giftCard, '');
                break;

            case DeliveryMethods::METHOD_POST:
                $fileUrl = $this->giftCardManagement->getPreviewGiftCardPdfUrl($giftCard);
                $result  = $fileUrl;
                break;
        }

        return $result;
    }
}
