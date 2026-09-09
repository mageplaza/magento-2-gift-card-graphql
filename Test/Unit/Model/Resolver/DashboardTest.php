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

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\CustomerGraphQl\Model\Customer\GetCustomer;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Mageplaza\GiftCard\Helper\Product;
use Mageplaza\GiftCardGraphQl\Model\Resolver\Dashboard;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DashboardTest extends TestCase
{
    /**
     * @var Dashboard
     */
    private $dashboardResolver;

    /**
     * @var GetCustomer|MockObject
     */
    private $getCustomerMock;

    /**
     * @var Product|MockObject
     */
    private $productHelperMock;

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
        $this->fieldMock = $this->createMock(Field::class);
        $this->resolveInfoMock = $this->createMock(ResolveInfo::class);
        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->customerMock = $this->createMock(CustomerInterface::class);

        /** @phpstan-ignore-next-line */
        $this->dashboardResolver = new Dashboard(
            $this->getCustomerMock,
            $this->productHelperMock
        );
    }

    public function testResolveWithDisabledModule()
    {
        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->expectException(GraphQlInputException::class);
        $this->expectExceptionMessage('The module is disabled');

        $this->dashboardResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            null,
            []
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

        $this->dashboardResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            null,
            []
        );
    }

    public function testResolveSuccessfully()
    {
        $expectedResult = [
            'gift_cards' => [
                ['code' => 'GIFT123', 'balance' => 100],
                ['code' => 'GIFT456', 'balance' => 50]
            ],
            'total_balance' => 150,
            'total_cards' => 2
        ];

        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $extensionAttributesMock = $this->createMock(\Magento\GraphQl\Model\Query\ContextExtensionInterface::class);
        $extensionAttributesMock->method('getIsCustomer')->willReturn(true);
        $this->contextMock->method('getExtensionAttributes')->willReturn($extensionAttributesMock);

        $this->getCustomerMock->expects($this->once())
            ->method('execute')
            ->with($this->contextMock)
            ->willReturn($this->customerMock);

        $this->productHelperMock->expects($this->once())
            ->method('getDashboardConfig')
            ->with($this->customerMock)
            ->willReturn($expectedResult);

        $result = $this->dashboardResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            null,
            []
        );

        $this->assertEquals($expectedResult, $result);
    }

    public function testResolveWithEmptyDashboard()
    {
        $expectedResult = [
            'gift_cards' => [],
            'total_balance' => 0,
            'total_cards' => 0
        ];

        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $extensionAttributesMock = $this->createMock(\Magento\GraphQl\Model\Query\ContextExtensionInterface::class);
        $extensionAttributesMock->method('getIsCustomer')->willReturn(true);
        $this->contextMock->method('getExtensionAttributes')->willReturn($extensionAttributesMock);

        $this->getCustomerMock->expects($this->once())
            ->method('execute')
            ->with($this->contextMock)
            ->willReturn($this->customerMock);

        $this->productHelperMock->expects($this->once())
            ->method('getDashboardConfig')
            ->with($this->customerMock)
            ->willReturn($expectedResult);

        $result = $this->dashboardResolver->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->resolveInfoMock,
            null,
            []
        );

        $this->assertEquals($expectedResult, $result);
    }

    public function testResolveWithNullContext()
    {
        $this->productHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->expectException(\Error::class);

        $this->dashboardResolver->resolve(
            $this->fieldMock,
            null,
            $this->resolveInfoMock,
            null,
            []
        );
    }
}
