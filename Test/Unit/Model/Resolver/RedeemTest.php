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

use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Mageplaza\GiftCard\Api\GiftCardManagementInterface;
use Mageplaza\GiftCard\Helper\Product;
use Mageplaza\GiftCardGraphQl\Model\Resolver\Redeem;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RedeemTest extends TestCase
{
    /**
     * @var Redeem
     */
    private $redeemResolver;

    /**
     * @var GetCustomer|MockObject
     */
    private $getCustomerMock;

    /**
     * @var Product|MockObject
     */
    private $productHelperMock;

    /**
     * @var GiftCardManagementInterface|MockObject
     */
    private $giftCardManagementMock;

    /**
     * @var Field|MockObject
     */
    private $fieldMock;

    /**
     * @var ResolveInfo|MockObject
     */
    private $resolveInfoMock;

    /**
     * @var ContextInterface|MockObject
     */
    private $contextMock;

    /**
     * @var CustomerInterface|MockObject
     */
    private $customerMock;

    protected function setUp(): void
    {
        $this->getCustomerMock = $this->createMock(GetCustomer::class);
        $this->productHelperMock = $this->createMock(Product::class);
        $this->giftCardManagementMock = $this->createMock(GiftCardManagementInterface::class);
        $this->fieldMock = $this->createMock(Field::class);
        $this->resolveInfoMock = $this->createMock(ResolveInfo::class);
        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->customerMock = $this->createMock(CustomerInterface::class);

        /** @phpstan-ignore-next-line */
        $this->redeemResolver = new Redeem(
            $this->getCustomerMock,
            $this->productHelperMock,
            $this->giftCardManagementMock
        );
    }

    public function testResolveWithDisabledModule()
    {
        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('The module is disabled');

        $this->redeemResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            null,
            ['code' => 'TEST123']
        );
    }

    public function testResolveWithUnauthorizedCustomer()
    {
        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $extensionAttributesMock = $this->createMock(\Magento\GraphQl\Model\Query\ContextExtensionInterface::class);
        $extensionAttributesMock->method('getIsCustomer')->willReturn(false);
        $this->contextMock->method('getExtensionAttributes')->willReturn($extensionAttributesMock);

        $this->expectException(GraphQlAuthorizationException::class);
        $this->expectExceptionMessage('The current customer isn\'t authorized.');

        $this->redeemResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            null,
            ['code' => 'TEST123']
        );
    }

    public function testResolveSuccessfully()
    {
        $code = 'TEST123';
        $customerId = 123;
        $expectedResult = ['status' => 'success', 'balance' => 100];

        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $extensionAttributesMock = $this->createMock(\Magento\GraphQl\Model\Query\ContextExtensionInterface::class);
        $extensionAttributesMock->method('getIsCustomer')->willReturn(true);
        $this->contextMock->method('getExtensionAttributes')->willReturn($extensionAttributesMock);

        $this->customerMock->method('getId')->willReturn($customerId);
        $this->getCustomerMock->expects($this->once())
            ->method('execute')
            ->with($this->contextMock)
            ->willReturn($this->customerMock);

        $this->giftCardManagementMock->expects($this->once())
            ->method('redeem')
            ->with($customerId, $code)
            ->willReturn($expectedResult);

        $result = $this->redeemResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            null,
            ['code' => $code]
        );

        $this->assertEquals($expectedResult, $result);
    }

    public function testResolveWithException()
    {
        $code = 'INVALID123';
        $customerId = 123;
        $exceptionMessage = 'Invalid gift code';

        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $extensionAttributesMock = $this->createMock(\Magento\GraphQl\Model\Query\ContextExtensionInterface::class);
        $extensionAttributesMock->method('getIsCustomer')->willReturn(true);
        $this->contextMock->method('getExtensionAttributes')->willReturn($extensionAttributesMock);

        $this->customerMock->method('getId')->willReturn($customerId);
        $this->getCustomerMock->expects($this->once())
            ->method('execute')
            ->with($this->contextMock)
            ->willReturn($this->customerMock);

        $this->giftCardManagementMock->expects($this->once())
            ->method('redeem')
            ->with($customerId, $code)
            ->willThrowException(new Exception($exceptionMessage));

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $this->redeemResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            null,
            ['code' => $code]
        );
    }

    public function testResolveWithEmptyCode()
    {
        $code = '';
        $customerId = 123;
        $expectedResult = ['status' => 'error', 'message' => 'Code is required'];

        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $extensionAttributesMock = $this->createMock(\Magento\GraphQl\Model\Query\ContextExtensionInterface::class);
        $extensionAttributesMock->method('getIsCustomer')->willReturn(true);
        $this->contextMock->method('getExtensionAttributes')->willReturn($extensionAttributesMock);

        $this->customerMock->method('getId')->willReturn($customerId);
        $this->getCustomerMock->expects($this->once())
            ->method('execute')
            ->with($this->contextMock)
            ->willReturn($this->customerMock);

        $this->giftCardManagementMock->expects($this->once())
            ->method('redeem')
            ->with($customerId, $code)
            ->willReturn($expectedResult);

        $result = $this->redeemResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            null,
            ['code' => $code]
        );

        $this->assertEquals($expectedResult, $result);
    }
}