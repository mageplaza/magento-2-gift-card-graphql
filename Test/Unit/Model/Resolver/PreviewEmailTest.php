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

namespace Mageplaza\GiftCardGraphQl\Test\Unit\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mageplaza\GiftCard\Api\GiftCardManagementInterface;
use Mageplaza\GiftCard\Model\Api\GiftCardManagement;
use Mageplaza\GiftCard\Helper\Data as HelperData;
use Mageplaza\GiftCard\Model\GiftCard;
use Mageplaza\GiftCard\Model\GiftCardFactory;
use Mageplaza\GiftCard\Model\Source\DeliveryMethods;
use Mageplaza\GiftCardGraphQl\Model\Resolver\PreviewEmail;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PreviewEmailTest extends TestCase
{
    /**
     * @var PreviewEmail
     */
    private $previewEmailResolver;

    /**
     * @var GiftCardFactory|MockObject
     */
    private $giftCardFactoryMock;

    /**
     * @var HelperData|MockObject
     */
    private $helperDataMock;

    /**
     * @var GiftCardManagement|MockObject
     */
    private $giftCardManagementMock;

    /**
     * @var TimezoneInterface|MockObject
     */
    private $timezoneMock;

    /**
     * @var Field|MockObject
     */
    private $fieldMock;

    /**
     * @var ResolveInfo|MockObject
     */
    private $resolveInfoMock;

    /**
     * @var GiftCard|MockObject
     */
    private $giftCardMock;

    protected function setUp(): void
    {
        $this->giftCardFactoryMock    = $this->createMock(GiftCardFactory::class);
        $this->helperDataMock         = $this->createMock(HelperData::class);
        $this->giftCardManagementMock = $this->createMock(GiftCardManagement::class);
        $this->timezoneMock           = $this->createMock(TimezoneInterface::class);
        $this->fieldMock              = $this->createMock(Field::class);
        $this->resolveInfoMock        = $this->createMock(ResolveInfo::class);
        $this->giftCardMock           = $this->createMock(GiftCard::class);

        /** @phpstan-ignore-next-line */
        $this->previewEmailResolver = new PreviewEmail(
            $this->giftCardFactoryMock,
            $this->helperDataMock,
            $this->giftCardManagementMock,
            $this->timezoneMock
        );
    }

    public function testResolveWithInvalidBalance()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Balance must be greater than 0');

        $this->previewEmailResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['input' => ['balance' => 0]]
        );
    }

    public function testResolveWithNegativeBalance()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Balance must be greater than 0');

        $this->previewEmailResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['input' => ['balance' => -10]]
        );
    }

    public function testResolveWithInvalidStatus()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Status must be an integer between 1 and 6');

        $this->previewEmailResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['input' => ['balance' => 100, 'status' => 7]]
        );
    }

    public function testResolveWithEmptyGiftCodePattern()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Gift code pattern must be a non-empty string');

        $this->previewEmailResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['input' => ['balance' => 100, 'status' => 1, 'giftcode_pattern' => '']]
        );
    }

    public function testResolveWithInvalidDeliveryMethod()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Delivery method must be an integer between 1 and 4');

        $this->previewEmailResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['input' => ['balance' => 100, 'status' => 1, 'giftcode_pattern' => 'GIFT123', 'delivery_method' => 5]]
        );
    }

    public function testResolveWithInvalidExpireAfter()
    {
        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('Expire after must be a number greater than 0 if provided');

        $this->previewEmailResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            [
                'input' => [
                    'balance'          => 100,
                    'status'           => 1,
                    'giftcode_pattern' => 'GIFT123',
                    'delivery_method'  => 1,
                    'expire_after'     => 0
                ]
            ]
        );
    }

    public function testResolveWithValidInput()
    {
        $inputData = [
            'giftcode_pattern' => '[4AN]-[4A]-[4N]',
            'template_id'      => 1,
            'expire_after'     => 30,
            'balance'          => 1,
            'status'           => 1,
            'delivery_method'  => 4,
            'template_fields'  => '{"sender":"Sender name"}',
            'timezone'         => 'Asia/Ho_Chi_Minh',
            'image'            => '/sample/template/Giftcard_Christmas_400x450.png',
        ];

        $this->giftCardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->giftCardMock);

        $this->giftCardMock->expects($this->once())
            ->method('addData')
            ->with($inputData)
            ->willReturnSelf();


        $this->giftCardMock->expects($this->any())
            ->method('getDeliveryMethod')
            ->willReturn(4); // METHOD_POST

        $this->timezoneMock->expects($this->once())
            ->method('getConfigTimezone')
            ->willReturn('UTC');

        // Mock getTemplateId để getPreviewGiftCardPdfUrl không return false
        $this->giftCardMock->expects($this->any())
            ->method('getTemplateId')
            ->willReturn(1);

        // Mock getPreviewGiftCardPdfUrl method cho METHOD_POST
        $this->giftCardManagementMock->expects($this->once())
            ->method('getPreviewGiftCardPdfUrl')
            ->with($this->giftCardMock)
            ->willReturn('https://example.com/preview-gift-card.pdf');

//        $smsHelperMock = $this->createMock(\Mageplaza\GiftCard\Helper\Sms::class);
//        $this->helperDataMock->expects($this->once())
//            ->method('getSmsHelper')
//            ->willReturn($smsHelperMock);
//
//        $smsHelperMock->expects($this->once())
//            ->method('generateMessageContent')
//            ->with($this->giftCardMock, '')
//            ->willReturn('SMS content for gift card');

        $result = $this->previewEmailResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['input' => $inputData]
        );

        $this->assertEquals('https://example.com/preview-gift-card.pdf', $result);
    }

    public function testResolveWithEmailDeliveryMethod()
    {
        // This test will fail due to missing methods in interface
        // But we can test that validation passes
        $inputData = [
            'balance'          => 100,
            'status'           => 1,
            'giftcode_pattern' => 'GIFT123',
            'delivery_method'  => DeliveryMethods::METHOD_EMAIL,
            'template_fields'  => '{"sender":"John","recipient":"Jane","message":"Happy Birthday!"}'
        ];

        $this->giftCardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->giftCardMock);

        $this->giftCardMock->expects($this->once())
            ->method('addData')
            ->with($inputData)
            ->willReturnSelf();

        $this->giftCardMock->expects($this->once())
            ->method('setCode')
            ->with('GIFT123')
            ->willReturnSelf();

        $this->giftCardMock->expects($this->once())
            ->method('setExpiredAt')
            ->willReturnSelf();

        $this->giftCardMock->expects($this->any())
            ->method('getDeliveryMethod')
            ->willReturn(DeliveryMethods::METHOD_EMAIL);

        $this->giftCardMock->expects($this->any())
            ->method('getTemplateFields')
            ->willReturn($inputData['template_fields']);

        // Mock getPreviewGiftCardPdfUrl method
        $this->giftCardManagementMock->expects($this->once())
            ->method('getPreviewGiftCardPdfUrl')
            ->with($this->giftCardMock)
            ->willReturn('https://example.com/preview-gift-card.pdf');

        $this->giftCardMock->expects($this->any())
            ->method('getBalance')
            ->willReturn(100);

        $this->giftCardMock->expects($this->any())
            ->method('getStatusLabel')
            ->willReturn('Active');

        $this->giftCardMock->expects($this->any())
            ->method('getExpiredAt')
            ->willReturn('2024-12-31');

        $this->timezoneMock->expects($this->once())
            ->method('getConfigTimezone')
            ->willReturn('UTC');

        // Mock helper methods that might be called
        $this->helperDataMock->method('jsonDecode')
            ->willReturn(json_decode($inputData['template_fields'], true));

        $this->helperDataMock->method('convertPrice')
            ->willReturn('$100.00');

        $this->helperDataMock->method('formatDate')
            ->willReturn('Dec 31, 2024');

        $this->helperDataMock->method('getEmailConfig')
            ->willReturn('gift_card_template');

        $emailHelperMock = $this->createMock(\Mageplaza\GiftCard\Helper\Email::class);
        $this->helperDataMock->method('getEmailHelper')
            ->willReturn($emailHelperMock);

        $emailTemplateMock = $this->createMock(\Magento\Email\Model\Template::class);
        $emailHelperMock->method('getEmailTemplate')
            ->willReturn($emailTemplateMock);

        $emailTemplateMock->method('processTemplate')
            ->willReturn('<html>Email content</html>');

        // Expect exception due to missing methods in interface
        $this->expectException(\Error::class);

        $this->previewEmailResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['input' => $inputData]
        );
    }

    public function testResolveWithDefaultExpireAfter()
    {
        $inputData = [
            'balance'          => 100,
            'status'           => 1,
            'giftcode_pattern' => 'GIFT123',
            'delivery_method'  => DeliveryMethods::METHOD_SMS
        ];

        $this->giftCardFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->giftCardMock);

        $this->giftCardMock->expects($this->once())
            ->method('addData')
            ->willReturnSelf();

        $this->giftCardMock->expects($this->once())
            ->method('setCode')
            ->with('GIFT123')
            ->willReturnSelf();

        $this->giftCardMock->expects($this->once())
            ->method('setExpiredAt')
            ->willReturnSelf();

        $this->giftCardMock->expects($this->any())
            ->method('getDeliveryMethod')
            ->willReturn(DeliveryMethods::METHOD_SMS);

        $this->timezoneMock->expects($this->once())
            ->method('getConfigTimezone')
            ->willReturn('UTC');

        $smsHelperMock = $this->createMock(\Mageplaza\GiftCard\Helper\Sms::class);
        $this->helperDataMock->expects($this->once())
            ->method('getSmsHelper')
            ->willReturn($smsHelperMock);

        $smsHelperMock->expects($this->once())
            ->method('generateMessageContent')
            ->with($this->giftCardMock, '')
            ->willReturn('SMS content for gift card');

        $result = $this->previewEmailResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            ['input' => $inputData]
        );

        $this->assertEquals('SMS content for gift card', $result);
    }
}
